<div class="total">
	<p><?php _e('Bounced emails to date:', $this -> plugin_name); ?></p>
	<p class="totalnumber"><?php echo $total; ?></p>
	<p><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=bounces" class="button button-primary button-large"><?php _e('Manage Subscribers', $this -> plugin_name); ?></a></p>
</div>