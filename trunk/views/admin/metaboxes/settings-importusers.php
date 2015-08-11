<!-- Auto Import WordPress Users Settings -->

<?php

global $wp_roles;
$mailinglists = $Mailinglist -> select(true); 
$importuserslists = $this -> get_option('importuserslists');

?>

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

	<h2><?php _e('Roles/Lists', $this -> plugin_name); ?></h2>
	<?php if (!empty($wp_roles -> role_names) && !empty($mailinglists)) : ?>
		<div class="scroll-list" style="max-height:400px;">
			<table class="form-table">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
							<th style="font-weight:bold; text-align:center; white-space:nowrap;">
								<label><input onclick="jqCheckAll(this, '<?php echo $this -> sections -> settings_system; ?>', 'importuserslists[<?php echo $role_key; ?>]');" type="checkbox" name="checkall<?php echo $role_key; ?>" value="1" id="checkall<?php echo $role_key; ?>" />
								<?php _e($role_name); ?></label>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($mailinglists as $mailinglist_id => $mailinglist_title) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'arow' : ''; ?>">
							<th style="white-space:nowrap; text-align:right;"><?php _e($mailinglist_title); ?></th>
							<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
								<td style="text-align:center;">
									<input <?php echo (!empty($importuserslists[$role_key]) && in_array($mailinglist_id, $importuserslists[$role_key])) ? 'checked="checked"' : ''; ?> type="checkbox" name="importuserslists[<?php echo $role_key; ?>][]" value="<?php echo $mailinglist_id; ?>" id="importuserslists_<?php echo $role_key; ?>_<?php echo $mailinglist_id; ?>" />
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php else : ?>
		<p class="newsletters_error"><?php _e('No roles/lists are available', $this -> plugin_name); ?></p>
	<?php endif; ?>

	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="importusersrequireactivate_N"><?php _e('Require Activation', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('importusersrequireactivate') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="importusersrequireactivate" value="Y" id="importusersrequireactivate_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('importusersrequireactivate') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="importusersrequireactivate" value="N" id="importusersrequireactivate_N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Should imported users be required to activate/confirm their subscription via email?', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for=""><?php _e('Custom Fields', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Users custom fields mapping used for importing users as subscribers as well as when sending newsletters to user roles.', $this -> plugin_name)); ?></th>
			<td>
				<div class="scroll-list">
					<?php if ($fields = $Field -> select()) : ?>
						<?php 
							
						$importusersfields = $this -> get_option('importusersfields');
						$importusersfieldspre = $this -> get_option('importusersfieldspre');
						
						?>
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
														<option <?php echo (!empty($importusersfieldspre[$field_id]) && $importusersfieldspre[$field_id] == $usermeta_field_name) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr(stripslashes($usermeta_field_name)); ?>"><?php echo __($usermeta_field); ?></option>
													<?php endforeach; ?>
												</select>
											<?php endif; ?>
											<?php _e('or meta key:', $this -> plugin_name); ?>
											<input type="text" name="importusersfields[<?php echo $field_id; ?>]" value="<?php echo esc_attr(stripslashes($importusersfields[$field_id])); ?>" id="importusersfields_<?php echo $field_id; ?>" />
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<div class="newsletters_error"><?php _e('No custom fields are available.', $this -> plugin_name); ?></div>
					<?php endif; ?>
				</div>
				<span class="howto"><?php _e('Map user meta by selection or custom key to import into custom fields.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>