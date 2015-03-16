<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>tcovendorid"><?php _e('Vendor ID/Account Number', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Your 2CO (2CheckOut) vendor ID/account number as provided to you by 2CO when you registered an account with them.', $this -> plugin_name)); ?></th>
			<td>
				<input class="widefat" type="text" id="<?php echo $this -> pre; ?>tcovendorid" name="tcovendorid" value="<?php echo esc_attr(stripslashes($this -> get_option('tcovendorid'))); ?>" />
				<span class="howto"><?php _e('Your 2CO vendor ID/account number provided by 2CO.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>tcosecret"><?php _e('Vendor Secret', $this -> plugin_name) ?></label>
			<?php echo $Html -> help(__('You can find and change the vendor secret in your 2CO account under Account > Site Management. This vendor secret is used for a hashing algorithm to ensure transactions are not tampered with.', $this -> plugin_name)); ?></th>
			<td>
				<input class="widefat" type="text" id="<?php echo $this -> pre; ?>tcosecret" name="tcosecret" value="<?php echo esc_attr(stripslashes($this -> get_option('tcosecret'))); ?>" />
				<span class="howto"><?php _e('Used for hash encryption check', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>tcodemo"><?php _e('Demo Mode', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Use 2CO demo mode for testing purposes in order to process transactions without charging the card. This setting will only work if demo mode is set to Parameter of On in your 2CO account under Account. You can use the testing card 4111111111111111 for an approved response.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('tcodemo') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tcodemo" value="Y" /> <?php _e('Yes'); ?></label>
				<label><input id="<?php echo $this -> pre; ?>tcodemo" <?php echo ($this -> get_option('tcodemo') == "N" || !$this -> get_option('tcodemo')) ? 'checked="checked"' : ''; ?> type="radio" name="tcodemo" value="N" /> <?php _e('No'); ?></label>
				<span class="howto"><?php _e('For testing purposes. No charges are made', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>