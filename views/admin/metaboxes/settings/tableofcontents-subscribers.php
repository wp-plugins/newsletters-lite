<label for="tableofcontents"><?php _e('Go to section', $this -> plugin_name); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); }">
	<option value=""><?php _e('Choose section...', $this -> plugin_name); ?></option>
	<option value="managementdiv"><?php _e('Subscriber Management Section', $this -> plugin_name); ?></option>
	<option value="subscribersdiv"><?php _e('Subscription Behaviour', $this -> plugin_name); ?></option>
	<option value="unsubscribediv"><?php _e('Unsubscribe Behaviour', $this -> plugin_name); ?></option>
</select>