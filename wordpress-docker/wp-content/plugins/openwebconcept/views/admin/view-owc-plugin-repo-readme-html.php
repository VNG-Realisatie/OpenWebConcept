<?php
	$tab_head_i 	= $tab_body_i = 0;
	$banner_class 	= ($banner ? 'with-banner' : 'without-banner');
	$parsedown 		= new Parsedown();
?>

<?php if( $banner ) { ?>
	<style type="text/css">
		#plugin-information-title.with-banner { background-image: url( <?php echo $banner; ?> ); }
		@media only screen and ( -webkit-min-device-pixel-ratio: 1.5 ) {
			#plugin-information-title.with-banner {	background-image: url( <?php echo $banner; ?> ); }
		}
	</style>
<?php } ?>

<div id="plugin-information-scrollable">
	<div id="plugin-information-title" class="<?php echo $banner_class; ?>">
		<div class="vignette"></div>
		<h2><?php echo $title; ?></h2>
	</div>
	<div id="plugin-information-tabs" class="<?php echo $banner_class; ?>">
		<?php foreach ($readme_tabs as $tab_key => $tab_content) { ?>
			<a name="<?php echo $tab_key; ?>" href="#section-<?php echo $tab_key; ?>" class="owc-plugin_info--tab<?php echo ($tab_head_i == 0 ? ' current' : ''); ?>"><?php echo owc_plugin_translate( str_replace('-', ' ', $tab_key), true ); ?></a>
		<?php $tab_head_i++; } ?>
	</div>
	<div class="owc-plugin_section--wrap <?php echo $banner_class; ?>">
		<?php
			if( empty($readme_tabs) ) {
				
				if( $readme !== '' ) {
					echo owc_plugin_get_admin_notice(__("Readme file is not valid", 'owc-plugin'), 'warning');
				} else if( $private ) {
					echo owc_plugin_get_admin_notice(__("Readme file couldn't be loaded, maybe you don't have read access for this repo?", 'owc-plugin'), 'warning');
				} else {
					echo owc_plugin_get_admin_notice(__("Readme file couldn't be loaded", 'owc-plugin'), 'error');
				}

			} else {
				
				foreach ($readme_tabs as $tab_key => $tab_content) { ?>
					<div id="section-<?php echo $tab_key; ?>" class="owc-plugin_section" style="display: <?php echo ($tab_body_i == 0 ? 'block' : 'none'); ?>">
						<?php
							if( $tab_body_i == 0 && $private ) {
								echo owc_plugin_get_admin_notice(__('This is a private repo', 'owc-plugin'), 'warning');
							}
							echo $parsedown->text($tab_content); 
						?>
					</div>
				<?php $tab_body_i++; }
			}
		?>
	</div>
</div>
<div id="plugin-information-footer">
	<?php echo owc_get_repo_button_html($package); ?>
	<span class="spinner"></span>
</div>