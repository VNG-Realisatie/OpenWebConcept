<?php

namespace Deployer;

require 'recipe/common.php';
require 'recipe/slack.php';

// Project name
set('application', 'Open Melding');

set('timezone', 'Europe/Amsterdam');

// Project repository
set('repository', 'git@bitbucket.org:openwebconcept/open-melding-backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('keep_releases', 3);

set('wpcli_command', 'wp');

// Shared files/dirs between deploys
set('shared_dirs', [
    'logs',
    'provision',
    'htdocs/wp-content/uploads',
    'storage',
]);
set('shared_files', [
    '.env',
    '.env.example',
    //	'htdocs/wp-config.php',
]);

// Writable dirs by web server
set('writable_dirs', [
    'logs',
    'htdocs/wp-content/uploads'
]);
set('allow_anonymous_stats', false);

/**
 * Send slack a notification
 */
set('slack_webhook', 'https://hooks.slack.com/services/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');

// Hosts
host('174.138.15.68')
    ->stage('production')
    ->user('root')
    ->set('deploy_path', '/var/www/api')
    ->identityFile('~/.ssh/digimelden')
    ->set('http_user', 'www-data')
    ->set('composer_options',
        'install --no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction --ignore-platform-reqs')
    ->set('writable_mode', 'chmod')
    ->set('writable_chmod_mode', '0775')
    ->set('writable_use_sudo', false)
    ->set('ssh_multiplexing', true);

// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

//before('deploy', 'slack:notify');
//after('success', 'slack:notify:success');
//after('deploy:failed', 'slack:notify:failure');