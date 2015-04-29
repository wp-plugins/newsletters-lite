<!-- Extensions Navigation -->

<?php /*<h2 class="nav-tab-wrapper">
	<a class="nav-tab <?php echo ($_GET['page'] == $this -> sections -> extensions) ? 'nav-tab-active' : ''; ?>" href="?page=<?php echo $this -> sections -> extensions; ?>"><?php _e('Manage', $this -> plugin_name); ?></a>
	<a class="nav-tab <?php echo ($_GET['page'] == $this -> sections -> extensions_settings) ? 'nav-tab-active' : ''; ?>" href="?page=<?php echo $this -> sections -> extensions_settings; ?>"><?php _e('Settings', $this -> plugin_name); ?></a>
</h2>*/ ?>

<div class="wp-filter">
	<ul class="filter-links">
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> extensions) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> extensions; ?>"><?php _e('Manage', $this -> plugin_name); ?></a></li>
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> extensions_settings) ? 'current' : ''; ?>" href="?page=<?php echo $this -> sections -> extensions_settings; ?>"><?php _e('Settings', $this -> plugin_name); ?></a></li>
	</ul>
	
	<?php if (!empty($section)) : ?>
		<div class="search-form" id="tableofcontentsdiv">
			<div class="inside">
				<input type="text" class="<?php echo $section; ?>-search" placeholder="<?php _e('Search', $this -> plugin_name); ?>" />
			</div>
		</div>
	<?php endif; ?>
</div>