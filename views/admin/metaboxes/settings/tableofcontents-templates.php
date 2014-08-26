<label for="tableofcontents"><?php _e('Go to section', $this -> plugin_name); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); }">
	<option value=""><?php _e('Choose section...', $this -> plugin_name); ?></option>
	<option value="postsdiv"><?php _e('Posts', $this -> plugin_name); ?></option>
	<option value="latestpostsdiv"><?php _e('Latest Posts', $this -> plugin_name); ?></option>
	<option value="confirmdiv"><?php _e('Confirmation Email', $this -> plugin_name); ?></option>
	<option value="bouncediv"><?php _e('Bounce Email', $this -> plugin_name); ?></option>
	<option value="unsubscribediv"><?php _e('Unsubscribe Admin Email', $this -> plugin_name); ?></option>
	<option value="unsubscribeuserdiv"><?php _e('Unsubscribe User Email', $this -> plugin_name); ?></option>
	<option value="expirediv"><?php _e('Expiration Email', $this -> plugin_name); ?></option>
	<option value="orderdiv"><?php _e('Paid Subscription Email', $this -> plugin_name); ?></option>
	<option value="schedulediv"><?php _e('Cron Schedule Email', $this -> plugin_name); ?></option>
	<option value="subscribediv"><?php _e('New Subscription Email', $this -> plugin_name); ?></option>
</select>