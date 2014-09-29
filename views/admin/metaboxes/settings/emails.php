<!-- Emails Settings -->

<?php

$emailarchive = $this -> get_option('emailarchive');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="emailarchive"><?php _e('Email Archiving', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo (!empty($emailarchive)) ? 'checked="checked"' : ''; ?> type="checkbox" name="emailarchive" value="1" id="emailarchive" /> <?php _e('Enable archiving of sent emails', $this -> plugin_name); ?></label>
			</td>
		</tr>
	</tbody>
</table>