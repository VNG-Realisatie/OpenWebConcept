<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url( 'tools.php?page=owc_plugin_repository'); ?>"><?php _e('Repository', 'owc-plugin'); ?></a>
		<a class="nav-tab" href="<?php echo admin_url( 'tools.php?page=owc_plugin_repository&tab=settings'); ?>"><?php _e('Settings', 'owc-plugin'); ?></a>
	</h2>
	<div id="owc_plugin-repo--messages_wrap"></div>
	<div class="section">
		<div class="grid-12">
			<div class="tablenav top">
				<div class="alignleft actions bulkactions">
					<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Bulk action select', 'owc-plugin'); ?></label>
					<select name="action" class="bulk-action-selector">
						<option value=""><?php _e('Actions', 'owc-plugin'); ?></option>
						<option value="install-selected"><?php _e('Install', 'owc-plugin'); ?></option>
						<option value="activate-selected"><?php _e('Activate', 'owc-plugin'); ?></option>
						<option value="deactivate-selected"><?php _e('Deactivate', 'owc-plugin'); ?></option>
						<!-- <option value="update-selected"><?php _e('Update', 'owc-plugin'); ?></option> -->
					</select>
					<input type="button" class="button do-owc-plugin-repo_action action" value="<?php _e('Apply', 'owc-plugin'); ?>">
				</div>
				<br class="clear">
			</div>
			<div class="owc_plugin__repro--wrap">
				<div class="owc_plugin__repro--side">
					<div class="owc_plugin__repro--search_wrap">
						<input type="text" id="owc_plugin-filter_repo--list" placeholder="<?php _e('Search...', 'owc-plugin'); ?>">
					</div>
					<form action="<?php echo admin_url( 'tools.php?page=owc_plugin_repository'); ?>" id="owc-plugin-repo_action--form" method="POST">
						
						<?php include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-list.php'; ?>
						
						<input type="hidden" id="owc-plugin-repo_action--input" name="action" value="">

					</form>
				</div>
				<div class="owc_plugin__repro--content_cell">
					<div class="owc_plugin__repro--content">
						
					</div>
					<div class="owc_plugin__repro--loading-wrap">
						<div><img src="<?php echo OWC_PLUGIN_URL; ?>/assets/images/ajax-loading.gif"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>