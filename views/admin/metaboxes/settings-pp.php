<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>paypalemail"><?php _e('PayPal Email Address', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Your registered PayPal email that will receive the payments.', $this -> plugin_name)); ?></th>
			<td>
				<input type="text" class="widefat" name="paypalemail" value="<?php echo $this -> get_option('paypalemail'); ?>" id="<?php echo $this -> pre; ?>paypalemail" />
				<span class="howto"><?php _e('Your registered PayPal email that will receive the payments.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="paypalsubscriptions_N"><?php _e('PayPal Subscriptions', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Turning this On will result in an automatic, recurring payment by your subscribers through PayPal.<br/><br/>Turning this Off will result in a once-off payment through PayPal by your subscribers each time.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('paypalsubscriptions') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="paypalsubscriptions" value="Y" id="paypalsubscriptions_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('paypalsubscriptions') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="paypalsubscriptions" value="N" id="paypalsubscriptions_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Use PayPal Subscriptions for automatic, recurring payments.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>paypalsandbox"><?php _e('PayPal Sandbox', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Turn this On to use the PayPal Sandbox environment for testing purposes. Make sure you use a valid PayPal Sandbox Seller account for the PayPal Email Address setting above. For the Sandobx, you need to have port 443 over SSL protocol enabled on your hosting.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('paypalsandbox') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="paypalsandbox" value="Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input id="<?php echo $this -> pre; ?>paypalsandbox" <?php echo ($this -> get_option('paypalsandbox') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="paypalsandbox" value="N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('The PayPal Sandbox environment is for testing purposes.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>