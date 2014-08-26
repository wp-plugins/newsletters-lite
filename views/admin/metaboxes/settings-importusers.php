<table class="form-table">
	<tbody>
		<tr>
			<th><label for="importusers_N"><?php _e('Auto Import WordPress Users', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="jQuery('#importusersdiv').show();" <?php echo ($this -> get_option('importusers') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="importusers" value="Y" id="importusers_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#importusersdiv').hide();" <?php echo ($this -> get_option('importusers') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="importusers" value="N" id="importusers_N" /> <?php _e('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('With this turned on, the WordPress user import will run once hourly to check for new users.', $this -> plugin_name); ?></span>
			</td>
		</tr>
    </tbody>
</table>

<div id="importusersdiv" style="display:<?php echo ($this -> get_option('importusers') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="importuserslist"><?php _e('Users Import List(s)', $this -> plugin_name); ?></label></th>
				<td>					
					<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
						<?php $importuserslists = $this -> get_option('importuserslists'); ?>
						<div class="scroll-list">
							<?php foreach ($mailinglists as $list_id => $list_title) : ?>
								<div><label><input <?php echo (!empty($importuserslists) && in_array($list_id, $importuserslists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="importuserslists[]" value="<?php echo $list_id; ?>" id="importuserslists_<?php echo $list_id; ?>" /> <?php echo __($list_title); ?></label></div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></div>
					<?php endif; ?>
					<span class="howto"><?php _e('Mailing list(s) to import users into as subscribers.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="importusersrequireactivate_N"><?php _e('Require Activation', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('importusersrequireactivate') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="importusersrequireactivate" value="Y" id="importusersrequireactivate_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('importusersrequireactivate') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="importusersrequireactivate" value="N" id="importusersrequireactivate_N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Should imported users be required to activate/confirm their subscription via email?', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for=""><?php _e('Import Custom Fields', $this -> plugin_name); ?></label></th>
				<td>
					<?php if ($fields = $Field -> select()) : ?>
						<?php $importusersfields = $this -> get_option('importusersfields'); ?>
						<?php $importusersfieldspre = $this -> get_option('importusersfieldspre'); ?>
						<table>
							<tbody>
								<?php foreach ($fields as $field_id => $field_title) : ?>
									<tr>
										<th><label for="importusersfields_<?php echo $field_id; ?>"><?php _e($field_title); ?></label></th>
										<td>
											<?php _e('Select:', $this -> plugin_name); ?>
											<?php if ($usermeta_fields = $Html -> wordpress_usermeta_fields()) : ?>
												<select name="importusersfieldspre[<?php echo $field_id; ?>]">
													<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
													<?php foreach ($usermeta_fields as $usermeta_field_name => $usermeta_field) : ?>
														<option <?php echo (!empty($importusersfieldspre[$field_id]) && $importusersfieldspre[$field_id] == $usermeta_field_name) ? 'selected="selected"' : ''; ?> value="<?php echo $usermeta_field_name; ?>"><?php echo __($usermeta_field); ?></option>
													<?php endforeach; ?>
												</select>
											<?php endif; ?>
											<?php _e('or meta key:', $this -> plugin_name); ?>
											<input type="text" name="importusersfields[<?php echo $field_id; ?>]" value="<?php echo esc_attr($importusersfields[$field_id]); ?>" id="importusersfields_<?php echo $field_id; ?>" />
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<div class="<?php echo $this -> pre; ?>error"><?php _e('No custom fields are available.', $this -> plugin_name); ?></div>
					<?php endif; ?>
					<span class="howto"><?php _e('Map user meta by selection or custom key to import into custom fields.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>