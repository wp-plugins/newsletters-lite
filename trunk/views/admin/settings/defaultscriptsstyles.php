<?php include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php'); ?>

<?php if (!empty($successmessage)) : ?>
	<?php $this -> render('message', array('message' => $successmessage), true, 'admin'); ?>
<?php endif; ?>

<h4><?php _e('Load Default Styles', $this -> plugin_name); ?></h4>
<p class="howto"><?php _e('Turn On/Off the loading of default styles in the plugin.', $this -> plugin_name); ?></p>

<?php if (!empty($defaultstyles)) : ?>
	<?php
		
	$loadstyles = $this -> get_option('loadstyles');
	$loadstyles_handles = $this -> get_option('loadstyles_handles');	
	$loadstyles_pages = $this -> get_option('loadstyles_pages');
		
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="checkboxall" value="1" /></th>
				<th><?php _e('Style', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('The name of the style to load. Tick/check the checkboxes to enable and load styles. Do not disable loading of styles unless you specifically need or want to.', $this -> plugin_name)); ?></th>
				<th><?php _e('Handle', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Handle/slug to register the style with. Can be changed if a conflict occurs.', $this -> plugin_name)); ?></th>
				<th><?php _e('Post/Page IDs', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Comma separated post/page IDs to only load the style on, eg. 1,54,3,71', $this -> plugin_name)); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $class = false; ?>
			<?php foreach ($defaultstyles as $handle => $style) : ?>
				<?php
				
				$custom_handle = (empty($loadstyles_handles[$handle])) ? $handle : $loadstyles_handles[$handle];
				$custom_pages = (empty($loadstyles_pages[$handle])) ? false : $loadstyles_pages[$handle];
					
				?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th class="check-column">
						<input <?php echo (!empty($loadstyles) && in_array($handle, $loadstyles)) ? 'checked="checked"' : ''; ?> type="checkbox" name="loadstyles[]" value="<?php echo esc_attr(stripslashes($handle)); ?>" id="loadstyles_<?php echo $handle; ?>" />
					</td>
					<td>
						<label class="row-title" for="loadstyles_<?php echo $handle; ?>"><?php echo $style['name']; ?></label>
						<small>(<?php echo sprintf(__('Version %s', $this -> plugin_name), ((empty($style['version'])) ? __('N/A', $this -> plugin_name) : $style['version'])); ?>)</small>
					</td>
					<td>
						<input class="widefat" type="text" name="loadstyles_handles[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_handle)); ?>" id="loadstyles_handles_<?php echo $handle; ?>" />
					</td>
					<td>
						<input placeholder="<?php echo esc_attr(stripslashes(__('All posts/pages', $this -> plugin_name))); ?>" class="widefat" type="text" name="loadstyles_pages[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_pages)); ?>" id="loadstyles_pages_<?php echo $handle; ?>" />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<h4><?php _e('Load Default Scripts', $this -> plugin_name); ?></h4>
<p class="howto"><?php _e('Turn On/Off the loading of default scripts in the plugin.', $this -> plugin_name); ?></p>

<?php if (!empty($defaultscripts)) : ?>
	<?php
		
	$loadscripts = $this -> get_option('loadscripts');
	$loadscripts_handles = $this -> get_option('loadscripts_handles');	
	$loadscripts_pages = $this -> get_option('loadscripts_pages');
		
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="checkboxall" value="1" /></th>
				<th><?php _e('Script', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('The name of the script to load. Tick/check the checkboxes to enable and load scripts. Do not disable loading of scripts unless you specifically need or want to.', $this -> plugin_name)); ?></th>
				<th><?php _e('Handle', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Handle/slug to register the script with. Can be changed if a conflict occurs.', $this -> plugin_name)); ?></th>
				<th><?php _e('Post/Page IDs', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Comma separated post/page IDs to only load the script on, eg. 1,54,3,71', $this -> plugin_name)); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $class = false; ?>
			<?php foreach ($defaultscripts as $handle => $script) : ?>
				<?php
				
				$custom_handle = (empty($loadscripts_handles[$handle])) ? $handle : $loadscripts_handles[$handle];
				$custom_pages = (empty($loadscripts_pages[$handle])) ? false : $loadscripts_pages[$handle];
					
				?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th class="check-column">
						<input <?php echo (!empty($loadscripts) && in_array($handle, $loadscripts)) ? 'checked="checked"' : ''; ?> type="checkbox" name="loadscripts[]" value="<?php echo esc_attr(stripslashes($handle)); ?>" id="loadscripts_<?php echo $handle; ?>" />
					</td>
					<td>
						<label class="row-title" for="loadscripts_<?php echo $handle; ?>"><?php echo $script['name']; ?></label>
						<small>(<?php echo sprintf(__('Version %s', $this -> plugin_name), ((empty($script['version'])) ? __('N/A', $this -> plugin_name) : $script['version'])); ?>)</small>
					</td>
					<td>
						<input class="widefat" type="text" name="loadscripts_handles[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_handle)); ?>" id="loadscripts_handles_<?php echo $handle; ?>" />
					</td>
					<td>
						<input placeholder="<?php echo esc_attr(stripslashes(__('All posts/pages', $this -> plugin_name))); ?>" class="widefat" type="text" name="loadscripts_pages[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_pages)); ?>" id="loadscripts_pages_<?php echo $handle; ?>" />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>