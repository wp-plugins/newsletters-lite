<div class="wrap newsletters">
	<h2><?php _e('Submit Serial Key', $this -> plugin_name); ?></h2>
	
	<p>
		<?php _e('Please submit a serial key in the form below.', $this -> plugin_name); ?><br/>
		<?php echo sprintf(__('You can obtain the serial key from your %s.', $this -> plugin_name), '<a href="http://tribulant.com/downloads/" target="_blank">' . __('downloads section', $this -> plugin_name) . '</a>'); ?><br/>
	</p>
	
	<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
	
	<form action="?page=<?php echo $this -> sections -> submitserial; ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="serial"><?php _e('Serial Key', $this -> plugin_name); ?></label></th>
					<td>
						<input style="width:320px;" class="widefat" type="text" name="serial" value="<?php echo esc_attr(stripslashes($_POST['serial'])); ?>" id="serial" />
					</td>
				</tr>
			</tbody>
		</table>
	
		<p class="submit">
			<input type="submit" class="button button-primary" name="submit" value="<?php _e('Submit Serial Key', $this -> plugin_name); ?>" />
		</p>
	</form>
</div>