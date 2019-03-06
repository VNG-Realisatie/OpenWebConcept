<ul class="owc_plugin__repro--list">
	<?php foreach ($repo_array['Packages'] as $group_key => $group_data) { ?>
		<li class="owc_plugin__repro--has_sub open">
			<a href="#">
				<span>
					<input type="checkbox">
					<?php echo (isset($group_data['title']) ? $group_data['title'] : 'Groep'); ?> <small>- (<?php echo count($group_data['packages']); ?>)</small>
				</span>
				<i class="owc_plugin__icon--arrow"></i>
			</a>
			<ul>
				<?php foreach($group_data['packages'] as $package) { ?>
					<?php if( isset($package['@attributes']['name']) ) { ?>
						<li class="owc_plugin__item <?php echo owc_get_repo_status_class($package); ?>">
							<a href="#">
								<input type="checkbox" name="packages[]" value="<?php echo sanitize_title($package['slug']); ?>">
								<?php echo (isset($package['@attributes']['name']) ? $package['@attributes']['name'] : 'Pakket'); ?>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		</li>
	<?php } ?>
</ul>