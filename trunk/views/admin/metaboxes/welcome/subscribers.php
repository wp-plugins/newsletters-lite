<div class="total">
	<p><?php _e('Subscribers total to date:', $this -> plugin_name); ?></p>
	<p class="totalnumber"><?php echo $total; ?></p>
	<p>
		<a href="?page=<?php echo $this -> sections -> subscribers; ?>" class="button button-primary button-large"><?php _e('Manage Subscribers', $this -> plugin_name); ?></a>
	</p>
	<p>
		<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes'); ?>" class="button button-secondary"><?php _e('Unsubscribes', $this -> plugin_name); ?></a>
		<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces'); ?>" class="button button-secondary"><?php _e('Bounces', $this -> plugin_name); ?></a>
	</p>
</div>