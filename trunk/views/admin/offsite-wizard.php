<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Offsite Wizard', $this -> plugin_name); ?></h2>
	<form action="?page=<?php echo $this -> sections -> lists; ?>&amp;method=offsitewizard#code" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="formtype_popup"><?php _e('Form Type', $this -> plugin_name); ?></label></th>
					<td>
						<label><input onclick="jQuery('#formtype_popup_div').show(); jQuery('#formtype_html_div').hide();" <?php echo (empty($_POST['formtype']) || $_POST['formtype'] == "popup") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="popup" id="formtype_popup" /> <?php _e('Popup', $this -> plugin_name); ?></label>
						<label><input onclick="jQuery('#formtype_popup_div').hide(); jQuery('#formtype_html_div').hide();" <?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "iframe") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="iframe" id="formtype_iframe" /> <?php _e('iFrame', $this -> plugin_name); ?></label>
						<label><input onclick="jQuery('#formtype_popup_div').hide(); jQuery('#formtype_html_div').show();" <?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "html") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="html" id="formtype_html" /> <?php _e('HTML', $this -> plugin_name); ?></label>
						<span class="howto"><?php _e('Should this offsite form open as a popup upon submission or just use an iFrame to load in itself?', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="<?php echo $this -> pre; ?>list"><?php _e('Mailing List', $this -> plugin_name); ?></label></th>
					<td>
						<?php $lists = $Mailinglist -> select($privatelists = true); ?>
						<select class="widefat" style="width:auto;" id="<?php echo $this -> pre; ?>list" name="list">
							<option value="">- <?php _e('Select List', $this -> plugin_name); ?> -</option>
							<?php if (!empty($lists) && is_array($lists)) : ?>
								<?php foreach ($lists as $id => $title) : ?>
									<option <?php echo (!empty($listid) && $listid == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $title; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<span class="howto"><?php _e('Choose the mailing list to subscribe users to.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div id="formtype_html_div" style="display:<?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "html") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="html_fields_Y"><?php _e('Show Custom Fields', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['html_fields']) || (!empty($_POST['html_fields']) && $_POST['html_fields'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="html_fields" value="Y" id="html_fields_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
							<label><input <?php echo (!empty($_POST['html_fields']) && $_POST['html_fields'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="html_fields" value="N" id="html_fields_N" /> <?php _e('No', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Should custom fields be generated in this HTML code?', $this -> plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div id="formtype_popup_div" style="display:<?php echo (empty($_POST['formtype']) || $_POST['formtype'] == "popup") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="fieldsY"><?php _e('Show Custom Fields', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['fields']) || (!empty($_POST['fields']) && $_POST['fields'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="fields" value="Y" id="fieldsY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
							<label><input <?php echo (!empty($_POST['fields']) && $_POST['fields'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="fields" value="N" id="fieldsN" /> <?php _e('No', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Should custom fields be generated in this HTML code?', $this -> plugin_name); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $this -> pre; ?>title"><?php _e('PopUp Window Title', $this -> plugin_name); ?></label></th>
						<td>
							<input type="text" class="widefat" style="width:auto;" id="<?php echo $this -> pre; ?>title" name="title" value="<?php echo $this -> get_option('offsitetitle'); ?>" />
							<span class="howto"><?php _e('Title for the popup window in the browser.', $this -> plugin_name); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $this -> pre; ?>width"><?php _e('PopUp Dimensions', $this -> plugin_name); ?></label></th>
						<td>
							<input type="text" id="<?php echo $this -> pre; ?>width" name="width" value="<?php echo $this -> get_option('offsitewidth'); ?>" class="widefat" style="width:45px;" />
							<?php _e('by', $this -> plugin_name); ?>
							<input type="text" id="<?php echo $this -> pre; ?>height" name="height" value="<?php echo $this -> get_option('offsiteheight'); ?>" class="widefat" style="width:45px;" />
							<?php _e('pixels', $this -> plugin_name); ?>
							<span class="howto"><?php _e('Width and height of the popup window.', $this -> plugin_name); ?>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $this -> pre; ?>button"><?php _e('Button Text', $this -> plugin_name); ?></label></th>
						<td>
							<input class="widefat" style="width:auto;" type="text" name="button" value="<?php echo $this -> get_option('offsitebutton'); ?>" id="<?php echo $this -> pre; ?>button" style="width:97%;" />
							<span class="howto"><?php _e('Name/caption to display on the submit button.', $this -> plugin_name); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="stylesheetY"><?php _e('Include Stylesheet', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['stylesheet']) || (!empty($_POST['stylesheet']) && $_POST['stylesheet'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="stylesheet" value="Y" id="stylesheetY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
							<label><input <?php echo (!empty($_POST['stylesheet']) && $_POST['stylesheet'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="stylesheet" value="N" id="stylesheetN" /> <?php _e('No', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Include a stylesheet with styles for the subscribe form?', $this -> plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<p class="submit">
			<input type="submit" name="generate" value="<?php _e('Generate Offsite Code', $this -> plugin_name); ?>" class="button-primary" />
		</p>
	</form>
	
	<?php if (!empty($offsiteurl)) : ?>
		<h3><?php _e('Offsite URL', $this -> plugin_name); ?></h3>
		<p class="howto"><?php _e('Direct URL for accessing this offsite form.', $this -> plugin_name); ?></p>
		<p><code><?php echo $offsiteurl; ?></code></p>
	<?php endif; ?>
	
	<?php if (!empty($code)) : ?>
		<label>
			<h3 id="code"><label for="<?php echo $this -> pre; ?>code"><?php _e('Offsite Code', $this -> plugin_name); ?></label></h3>
			<p class="howto"><?php _e('HTML and Javascript code to accept subscriptions on external websites into this one.', $this -> plugin_name); ?></p>
			<textarea wrap="off" name="code" rows="15" cols="100%" class="widefat" id="<?php echo $this -> pre; ?>code" onclick="this.select();"><?php echo htmlentities(trim($code), false, get_bloginfo('charset')); ?></textarea>
			<span class="howto"><?php _e('Copy and paste the code into any webpage.', $this -> plugin_name); ?></span>
		</label>
	<?php endif; ?>
</div>