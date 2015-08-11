<!-- Settings Navigation -->

<div class="wp-filter">
	<ul class="filter-links">
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings; ?>"><?php _e('General', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings_subscribers) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings_subscribers; ?>"><?php _e('Subscribers', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings_templates) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings_templates; ?>"><?php _e('System Emails', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings_system) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings_system; ?>"><?php _e('System', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings_tasks) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings_tasks; ?>"><?php _e('Scheduled Tasks', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> settings_api) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> settings_api; ?>"><?php _e('API', $this -> plugin_name); ?></a></li>
	</ul>
	
	<?php if (!empty($tableofcontents)) : ?>
		<div class="search-form" id="tableofcontentsdiv">
			<div class="inside">
				<?php $this -> render('metaboxes' . DS . 'settings' . DS . $tableofcontents, false, true, 'admin'); ?>
			</div>
		</div>
	<?php endif; ?>
</div>