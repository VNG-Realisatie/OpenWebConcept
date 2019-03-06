<?php
/**
 * Converts WordPress-flavored markup from standard readme.txt files
 * to Github-flavored markup for a README.md file
 * @author Benjamin J. Balter -- http://ben.balter.com
 * @license MIT
 * @version 1.0
 */

class OwcPluginReadmeConverter
{
    /**
     * @param string $readme plugin readme.txt content
     * @param string $pluginSlug explicitly set the plugin slug, NULL for autodetect
     * @return string
     */
    public static function convert($readme, $pluginSlug = null) {

        $result = $tab_array = $tmp_array = array();

        // convert line endings from DOS to Unix
        $readme = str_replace("\r\n", "\n", $readme);

        $readme = preg_replace('|^=([^=]+)=*?\s*?\n|im', PHP_EOL . '###$1' . PHP_EOL, $readme);
        $readme = preg_replace('|^===([^=]+)=*?\s*?\n|im', PHP_EOL . '#$1' . PHP_EOL, $readme);

        //parse contributors, donate link, etc.
        $labels = array(
            'Contributors',
            'Donate link',
            'Tags',
            'Requires at least',
            'Tested up to',
            'Requires PHP',
            'Stable tag',
            'License',
            'License URI',
            'WC requires at least',
            'WC tested up to',
        );
        foreach ($labels as $label) {
            $readme = preg_replace("|^($label): (.+)$|im", '<strong>$1:</strong> $2  ', $readme);
        }

        if ($pluginSlug !== null) {
            $plugin = $pluginSlug;
        } else {
            //guess plugin slug from plugin name
            preg_match('|^#(.*?)$|im', $readme, $matches);
            $plugin = str_replace(' ', '-', strtolower(trim($matches[1])));
        }

        //process screenshots, if any
        if (preg_match('|## Screenshots (.*?)## [a-z]+ |ism', $readme, $matches)) {
            //parse screenshot list into array
            preg_match_all('|^[0-9]+\. (.*)$|im', $matches[1], $screenshots, PREG_SET_ORDER);

            //replace list item with markdown image syntax, hotlinking to plugin repo
            $i = 1;
            foreach ($screenshots as $screenshot) {
                $screenshot_url = self::findScreenshot($i, $plugin);
                if ($screenshot_url) {
                    $readme = str_replace($screenshot[0], "### {$i}. {$screenshot[1]}\n![{$screenshot[1]}](" . $screenshot_url . ")\n", $readme);
                } else {
                    $readme = str_replace($screenshot[0], "### {$i}. {$screenshot[1]}\n[missing image]\n", $readme);
                }
                $i++;
            }

        }

        $readme     = ltrim($readme);
        $tab_array  = self::getTabArray($readme);

        return $tab_array;
    }

    public static function getTabArray( $readme ) {

        $tab_array = $tmp_array = array();
        
        // Get all tabs
        preg_match_all('|^==([^=]+)=*?\s*?\n|im', $readme, $tab_items);

        $last_tab = 'info';

        if( isset($tab_items[1]) ) {
            
            $i      = 1;
            $len_i    = count($tab_items[0]);
            
            foreach( $tab_items[0] as $key => $value) {

                if( empty($tmp_array) ) {
                    $tmp_array = array($readme);
                }

                $readme_tmp     = array_splice($tmp_array, -1);

                if( isset($readme_tmp[0]) ) {

                    
                    $tmp_array = explode($value, $readme_tmp[0]);

                    if( isset($tmp_array[0]) ) {
                        $tab_array[$last_tab] = $tmp_array[0];
                    }

                    $last_tab = sanitize_title($tab_items[1][$key]);

                    if( $i == $len_i && isset($tmp_array[1]) ) {
                        $tab_array[$last_tab] = $tmp_array[1];
                    }

                }

                $i++;
            }

        }

        return $tab_array;

    }

    /**
     * Finds the correct screenshot file with the given number and plugin slug.
     *
     * As per the WordPress plugin repo, file extensions may be any
     * of: (png|jpg|jpeg|gif).  We look in the /assets directory first,
     * then in the base directory.
     *
     * @param   int $number Screenshot number to look for
     * @param   string $plugin_slug
     * @return  string|false   Valid screenshot URL or false if none found
     * @uses    url_validate
     * @link    http://wordpress.org/plugins/about/readme.txt
     */
    private static function findScreenshot($number, $plugin_slug)
    {
        $extensions = array('png', 'jpg', 'jpeg', 'gif');

        // this seems to now be the correct URL, not s.wordpress.org/plugins
        $base_url   = 'https://s.w.org/plugins/' . $plugin_slug . '/';
        $assets_url = 'https://ps.w.org/' . $plugin_slug . '/assets/';

        /* check assets for all extensions first, because if there's a
           gif in the assets directory and a jpg in the base directory,
           the one in the assets directory needs to win.
        */
        foreach (array($assets_url, $base_url) as $prefix_url) {
            foreach ($extensions as $ext) {
                $url = $prefix_url . 'screenshot-' . $number . '.' . $ext;
                if (self::validateUrl($url)) {
                    return $url;
                }
            }
        }

        return false;
    }

    /**
     * Test whether a file exists at the given URL.
     *
     * To do this as quickly as possible, we use fsockopen to just
     * get the HTTP headers and see if the response is "200 OK".
     * This is better than fopen (which would download the entire file)
     * and cURL (which might not be installed on all systems).
     *
     * @param    string $link URL to validate
     * @return   boolean
     * @link http://www.php.net/manual/en/function.fsockopen.php#39948
     */
    private static function validateUrl($link)
    {
        $url_parts = @parse_url($link);

        if (empty($url_parts['host'])) {
            return false;
        }
        $host = $url_parts['host'];

        if (!empty($url_parts['path'])) {
            $documentpath = $url_parts['path'];
        } else {
            $documentpath = '/';
        }

        if (!empty($url_parts['query'])) {
            $documentpath .= '?' . $url_parts['query'];
        }

        if (!empty($url_parts['port'])) {
            $port = $url_parts['port'];
        } else {
            $port = '80';
        }

        $socket = @fsockopen($host, $port, $errno, $errstr, 30);

        if (!$socket) {
            return false;
        } else {
            fwrite($socket, "HEAD " . $documentpath . " HTTP/1.0\r\nHost: $host\r\n\r\n");
            $http_response = fgets($socket, 22);

            if (preg_match('/200 OK/', $http_response, $regs)) {
                fclose($socket);
                return true;
            } else {
                return false;
            }
        }
    }
}
