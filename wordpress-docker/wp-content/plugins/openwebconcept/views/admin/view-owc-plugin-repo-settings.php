<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url( 'tools.php?page=owc_plugin_repository'); ?>"><?php _e('Repository', 'owc-plugin'); ?></a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url( 'tools.php?page=owc_plugin_repository&tab=settings'); ?>"><?php _e('Settings', 'owc-plugin'); ?></a>
	</h2>
	<div id="owc_plugin-repo--messages_wrap"></div>
	<div class="section">
		<div class="grid-12">
			<?php echo ($alert_message ? $alert_message : ''); ?>
			<h1><?php _e('Private repositories', 'owc-plugin'); ?></h1>
			<p><?php _e('To get access to private plugins and themes, you can request access from the product owner. Make sure you have an account for the Git repository.', 'owc-plugin'); ?></p>
			<form method="post" action="" novalidate="novalidate">
				<h2>Bitbucket<?php echo owc_plugin_get_icon_bitbucket(); ?></h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="bitbucket_key"><?php _e('Callback URL', 'owc-plugin'); ?></label>
							</th>
							<td>
								<input class="regular-text" value="<?php echo owc_get_callback_url_bitbucket(); ?>" disabled="disabled">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="bitbucket_key"><?php _e('Key', 'owc-plugin'); ?></label>
							</th>
							<td>
								<input name="bitbucket_key" type="password" id="bitbucket_key" value="<?php echo owc_plugin_get_option('bitbucket_key', true); ?>" class="regular-text" data-cip-id="bitbucket_key">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="bitbucket_secret"><?php _e('Secret', 'owc-plugin'); ?></label>
							</th>
							<td>
								<input name="bitbucket_secret" type="password" id="bitbucket_secret" value="<?php echo owc_plugin_get_option('bitbucket_secret', true); ?>" class="regular-text" data-cip-id="bitbucket_secret">
								<?php if( !owc_is_authenticated_bitbucket() ) { ?>
									<br>
									WordPress heeft geen rechten. <a href="<?php echo owc_get_authentication_url_bitbucket(); ?>">Authenticeer site</a>.
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th></th>
							<td></td>
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<input type="hidden" name="owc_action" value="save">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Wijzigingen opslaan">
				</p>

			</form>

		</div>
	</div>
</div>