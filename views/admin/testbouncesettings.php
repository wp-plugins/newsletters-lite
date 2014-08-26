<div style="width:400px;">
	<h3><?php _e('Test POP3 Settings', $this -> plugin_name); ?></h3>
	
	<?php if ($success == true) : ?>
		<p>
			<?php _e('Congratulations, your POP3 settings are working!', $this -> plugin_name); ?><br/>
			<?php _e('Remember to save your configuration settings.', $this -> plugin_name); ?>
		</p>
		
		<?php if (!empty($message)) : ?>
			<p class="<?php echo $this -> pre; ?>success"><?php echo $message; ?></p>
		<?php endif; ?>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('Unfortunately a POP3 error occurred:', $this -> plugin_name); ?> <?php echo stripslashes($error); ?></p>
	<?php endif; ?>
	
	<p>
		<input class="button-secondary" onclick="jQuery.colorbox.close();" type="button" name="close" value="<?php _e('Close', $this -> plugin_name); ?>" />
	</p>
</div>