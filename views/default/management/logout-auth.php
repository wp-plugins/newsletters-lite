<p>
	<?php _e('You have now logged out of your subscriber profile management.', $this -> plugin_name); ?><br/>
	<?php _e('If you wish to go back, please click the link below.', $this -> plugin_name); ?>
</p>

<p><a href="<?php echo get_permalink($this -> get_option('managementpost')); ?>"><?php _e('Manage Subscriptions', $this -> plugin_name); ?></a></p>