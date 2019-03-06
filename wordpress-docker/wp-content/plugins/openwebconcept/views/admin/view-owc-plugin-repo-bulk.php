<div class="wrap">
	<div class="section">
		<div class="grid-12">
			<h1><?php _e('Run action', 'owc-plugin'); ?></h1>
			<p><?php echo sprintf(__('Plugins are being %s...', 'owc-plugin'), __($nice_action, 'owc-plugin')); ?></p>
			<?php
				if( isset($_POST['packages']) ) { ?>

					<div class="owc-plugin-repo_action--init_wrap">
						<?php foreach ($_POST['packages'] as $package) {
							echo '<input type="hidden" name="packages[]" class="packages" value="' . $package . '">';
						} ?>
						<input type="hidden" name="action" class="action" value="<?php echo $action; ?>">
					</div>
					<div class="owc-plugin-repo_action--console_wrap">
					</div>
					<div class="owc-plugin-repo_action--message_wrap" style="display: none;">
						<?php _e('Action successfully finished.', 'owc-plugin'); ?><br>
						<?php echo '<a href="' . $return_url . '">' . __('Return to the repositories', 'owc-plugin') . '</a>'; ?>
					</div>

				<?php } else {
					echo '<p>' . __('No packages selected to install', 'owc-plugin') . '</p>';
				}
			?>
		</div>
	</div>
</div>