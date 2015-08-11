<!-- General System Settings -->

<?php
	
$csvdelimiter = $this -> get_option('csvdelimiter');	
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="csvdelimiter"><?php _e('Global CSV Delimiter', $this -> plugin_name); ?></label></th>
			<td>
				<input style="width:45px;" type="text" name="csvdelimiter" value="<?php echo esc_attr(stripslashes($csvdelimiter)); ?>" id="csvdelimiter" />
				<span class="howto"><?php _e('The global CSV delimiter to use for exports and imports. The default is comma (,)', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>