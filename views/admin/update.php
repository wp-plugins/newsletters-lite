<?php if ($this -> ci_serial_valid()) : ?>
	<?php if (!empty($update_info) && $update_info['is_valid_key'] == "1") : ?>
		<?php
		
		$upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($this -> plugin_file), 'upgrade-plugin_' . $this -> plugin_file);
		
		?>
		
		<div class="update-nag newsletters-update-nag-wrapper">
			<span class="newsletters-update-nag"></span> <?php echo sprintf(__('Newsletters plugin %s is available.', $this -> plugin_name), $update_info['version']); ?><br/>
			<?php _e('You can update automatically or download to install manually.', $this -> plugin_name); ?>
			<br/><br/>
			<a href="<?php echo $upgrade_url; ?>" title="" class="button-primary"><?php _e('Update Automatically', $this -> plugin_name); ?></a>
			<a target="_blank" href="<?php echo $update_info['url']; ?>" title="" class="button-secondary"><?php _e('Download', $this -> plugin_name); ?></a>
			<a style="color:black; text-decoration:none;" href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $this -> sections -> settings_updates; ?>&amp;method=check" class="button button-secondary"><?php _e('Check Again', $this -> plugin_name); ?></a>
			<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
				<a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_updates); ?>"><?php _e('Changelog', $this -> plugin_name); ?></a>
				<a href="?newsletters_method=hideupdate&version=<?php echo $update_info['version']; ?>" class="newsletters-icon-delete"></a>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="update-nag newsletters-update-nag-wrapper">
			<span class="newsletters-update-nag"></span> <?php echo sprintf(__('Newsletters plugin %s is available.', $this -> plugin_name), $update_info['version']); ?><br/>
			<?php _e('Unfortunately your download has expired, please renew to gain access.', $this -> plugin_name); ?>
			<br/><br/>
			<a style="color:white; text-decoration:none;" href="<?php echo $update_info['url']; ?>" target="_blank" title="" class="button button-primary"><?php _e('Renew Now', $this -> plugin_name); ?></a>
			<a style="color:black; text-decoration:none;" href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $this -> sections -> settings_updates; ?>&amp;method=check" class="button button-secondary"><?php _e('Check Again', $this -> plugin_name); ?></a>
			<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
				<a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_updates); ?>"><?php _e('Changelog', $this -> plugin_name); ?></a>
				<a href="?newsletters_method=hideupdate&version=<?php echo $update_info['version']; ?>" class="newsletters-icon-delete"></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php else : ?>
	<div class="update-nag newsletters-update-nag-wrapper">
		<span class="newsletters-update-nag"></span> <?php echo sprintf(__('Newsletters plugin %s is available.', $this -> plugin_name), $update_info['version']); ?><br/>
		<?php _e('You can update automatically or download to install manually.', $this -> plugin_name); ?>
		<br/><br/>
		<a href="<?php echo $upgrade_url; ?>" title="" class="button-primary"><?php _e('Update Automatically', $this -> plugin_name); ?></a>
		<a target="_blank" href="<?php echo $update_info['url']; ?>" title="" class="button-secondary"><?php _e('Download', $this -> plugin_name); ?></a>
		<a style="color:black; text-decoration:none;" href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $this -> sections -> settings_updates; ?>&amp;method=check" class="button button-secondary"><?php _e('Check Again', $this -> plugin_name); ?></a>
		<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
			<a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_updates); ?>"><?php _e('Changelog', $this -> plugin_name); ?></a>
			<a href="?newsletters_method=hideupdate&version=<?php echo $update_info['version']; ?>" class="newsletters-icon-delete"></a>
		<?php endif; ?>
	</div>
<?php endif; ?>