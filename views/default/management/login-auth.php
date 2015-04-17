<div class="newsletters newsletters-management-loginauth">
	<p>
		<?php _e('Thank you for authenticating your email address.', $this -> plugin_name); ?><br/>
		<?php _e('If you do not get redirected in a second, click the link below.', $this -> plugin_name); ?>
	</p>
	
	<p><a class="newsletters_button" href="<?php echo $this -> get_managementpost(true); ?>"><?php _e('Manage Subscriptions', $this -> plugin_name); ?></a></p>
	
	<script type="text/javascript">jQuery(document).ready(function() { window.location = "<?php echo $Html -> retainquery('subscriberauth=' . $subscriberauth, $this -> get_managementpost(true)); ?>"; });</script>
</div>