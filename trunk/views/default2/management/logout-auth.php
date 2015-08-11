<div class="newsletters newsletters-management-logout">
	<div class="alert alert-success">
		<i class="fa fa-check"></i>
		<?php _e('You have now logged out of your subscriber profile management.', $this -> plugin_name); ?><br/>
		<?php _e('If you wish to go back, please click the link below.', $this -> plugin_name); ?>
	</div>
	
	<p><a class="newsletters_button btn btn-primary" href="<?php echo $this -> get_managementpost(true); ?>"><?php _e('Manage Subscriptions', $this -> plugin_name); ?></a></p>
</div>