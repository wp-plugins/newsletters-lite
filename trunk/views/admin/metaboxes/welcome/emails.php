<div class="total">
	<p><?php _e('Emails sent to date:', $this -> plugin_name); ?></p>
	<p class="totalnumber"><?php echo $total; ?></p>
	<p><a href="?page=<?php echo $this -> sections -> history; ?>" class="button button-primary button-large"><?php _e('Manage History Emails', $this -> plugin_name); ?></a></p>
</div>