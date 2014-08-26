<div class="total">
	<p><?php _e('Mailing lists total:', $this -> plugin_name); ?></p>
	<p class="totalnumber"><?php echo $total_public; ?> / <?php echo $total_private; ?></p>
	<p class="totalsmall"><?php _e('public', $this -> plugin_name); ?> / <?php _e('private', $this -> plugin_name); ?></p>
	<p><a href="?page=<?php echo $this -> sections -> lists; ?>" class="button button-primary button-large"><?php _e('Manage Lists', $this -> plugin_name); ?></a></p>
</div>