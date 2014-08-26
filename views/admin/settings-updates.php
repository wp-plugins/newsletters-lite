<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Check for Updates', $this -> plugin_name); ?></h2>
	
	<?php
	
	$update = $this -> vendor('update');
	$update_info = $update -> get_version_info();
	
	if (version_compare($this -> version, $update_info['version']) < 0) {
		$this -> render('update', array('update_info' => $update_info), true, 'admin'); ?>
		
		<?php $changelog = $update -> get_changelog(); ?>
		<div style="margin:10px 0; padding: 10px 20px; border:1px solid #ccc; border-radius:4px; moz-border-radius:4px; webkit-border-radius:4px;">
			<?php echo $changelog; ?>
		</div>
					
		<?php
	} else {
		?>

		<div class="error"><p><?php _e('Your version of the Newsletter plugin is up to date.', $this -> plugin_name); ?></p></div>
		
		<?php if ($raw_response = get_transient($this -> pre . 'update_info')) : ?>
			<?php if (!empty($raw_response['headers']['date'])) : ?>
				<p><?php echo sprintf(__('Last checked on <b>%s</b>', $this -> plugin_name), $raw_response['headers']['date']); ?></p>
				<p><a href="?page=<?php echo $this -> sections -> settings_updates; ?>&amp;method=check" class="button-primary"><?php _e('Check Again', $this -> plugin_name); ?></a>
				<?php echo $Html -> help(__('The plugin checks for new versions every 24 hours. If you want to check right now, click the "Check Again" button in order to do so.', $this -> plugin_name)); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php
	}
	
	?>
</div>