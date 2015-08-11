<div class="newsletters <?php echo $this -> pre; ?>">
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<div class="alert alert-success">
		<i class="fa fa-check"></i>
		<?php _e('You have resubscribed!', $this -> plugin_name); ?><br/>
		<?php _e('We are happy that you still want to receive our emails.', $this -> plugin_name); ?>
	</div>
	
	<p><a class="newsletters_button btn btn-primary" href="<?php echo $this -> get_managementpost(true); ?>"><?php _e('Manage Subscriptions', $this -> plugin_name); ?></a></p>
</div>