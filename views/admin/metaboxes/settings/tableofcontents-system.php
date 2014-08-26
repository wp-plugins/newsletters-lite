<label for="tableofcontents"><?php _e('Go to section', $this -> plugin_name); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); window.location.hash = '#' + this.value; }">
	<option value=""><?php _e('Choose section...', $this -> plugin_name); ?></option>
	<option value="captchadiv"><?php _e('Captcha Settings', $this -> plugin_name); ?></option>
	<option value="wprelateddiv"><?php _e('WordPress Related', $this -> plugin_name); ?></option>
	<option value="autoimportusersdiv"><?php _e('Auto Import Users', $this -> plugin_name); ?></option>
	<option value="commentform"><?php _e('WordPress Comment- and Registration Form', $this -> plugin_name); ?></option>
</select>

<p class="savebutton">
	<input type="submit" class="button button-primary button-large" value="<?php _e('Save Settings', $this -> plugin_name); ?>" name="save" />
</p>