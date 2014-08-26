<label for="tableofcontents"><?php _e('Go to section', $this -> plugin_name); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); }">
	<option value=""><?php _e('Choose section...', $this -> plugin_name); ?></option>
	<option value="generaldiv"><?php _e('General Mail Settings', $this -> plugin_name); ?></option>
	<option value="sendingdiv"><?php _e('Sending Settings', $this -> plugin_name); ?></option>
	<option value="optindiv"><?php _e('Default Subscription Form Settings', $this -> plugin_name); ?></option>
	<option value="subscriptionsdiv"><?php _e('Paid Subscriptions', $this -> plugin_name); ?></option>
	<option value="ppdiv"><?php _e('PayPal Configuration', $this -> plugin_name); ?></option>
	<option value="tcdiv"><?php _e('2CheckOut Configuration', $this -> plugin_name); ?></option>
	<option value="publishingdiv"><?php _e('Posts Configuration', $this -> plugin_name); ?></option>
	<option value="schedulingdiv"><?php _e('Email Scheduling', $this -> plugin_name); ?></option>
	<option value="bouncediv"><?php _e('Bounce Configuration', $this -> plugin_name); ?></option>
	<option value="latestposts"><?php _e('Latest Posts Subscription', $this -> plugin_name); ?></option>
	<option value="customcss"><?php _e('Theme, Scripts &amp; Custom CSS', $this -> plugin_name); ?></option>
</select>