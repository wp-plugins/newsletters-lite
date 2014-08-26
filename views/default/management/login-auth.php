<p>
	<?php _e('Thank you for authenticating your email address.', $this -> plugin_name); ?><br/>
	<?php _e('If you do not get redirected in a second, click the link below.', $this -> plugin_name); ?>
</p>

<p><a href="<?php echo get_permalink($this -> get_option('managementpost')); ?>"><?php _e('Manage Subscriptions', $this -> plugin_name); ?></a></p>

<script type="text/javascript">jQuery(document).ready(function() { window.location = "<?php echo get_permalink($this -> get_option('managementpost')); ?>"; });</script>