<!-- Import/Export -->

<?php $lists = $Mailinglist -> select(true); ?>

<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Import/Export Subscribers', $this -> plugin_name); ?></h2>
	
	<div id="import-export-tabs">
		<ul>
			<li><a href="#import-tab"><i class="fa fa-upload"></i> <?php _e('Import', $this -> plugin_name); ?></a></li>
			<li><a href="#export-tab"><i class="fa fa-download"></i> <?php _e('Export', $this -> plugin_name); ?></a></li>
		</ul>
		<div id="import-tab">
			<h3><?php _e('Import', $this -> plugin_name); ?></h3>
    
			<form action="?page=<?php echo $this -> sections -> importexport; ?>&amp;method=import" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="import" />
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="importfile"><?php _e('File', $this -> plugin_name); ?></label></th>
							<td>
								<input class="widefat" style="width:auto;" type="file" id="importfile" name="file" />
								<span class="howto"><?php _e('CSV/vCard file', $this -> plugin_name); ?></span>
		                        <?php if (!empty($importerrors['file'])) : ?><div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle"></i> <?php echo $importerrors['file']; ?></p></div><?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><label for="importlistid"><?php _e('Mailing List(s)', $this -> plugin_name); ?></label></th>
							<td>
								<?php if (!empty($lists)) : ?>
									<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Select all', $this -> plugin_name); ?></label><br/>
									<div id="newsletters-mailinglists-checkboxes" class="scroll-list">
										<?php foreach ($lists as $id => $title) : ?>
											<?php $Db -> model = $SubscribersList -> model; ?>
											<label><input <?php echo (!empty($_POST['importlists']) && in_array($id, $_POST['importlists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="importlists[]" value="<?php echo $id; ?>" id="checklist<?php echo $id; ?>" /> <?php echo $title; ?> (<?php echo $Db -> count(array('list_id' => $id)); ?> <?php _e('subscribers', $this -> plugin_name); ?>)</label><br/>
										<?php endforeach; ?>
									</div>
								<?php else : ?>
									<p class="newsletters_error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
								<?php endif; ?>
		                        
		                        <?php if (!empty($importerrors['mailinglists'])) : ?><div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle"></i> <?php echo $importerrors['mailinglists']; ?></p></div><?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php _e('File Type', $this -> plugin_name); ?></th>
							<td>
								<label><input onclick="jQuery('#csvdiv').show(); jQuery('#macdiv').hide();" <?php echo (!empty($_POST['filetype']) && $_POST['filetype'] == "csv") ? 'checked="checked"' : ''; ?> type="radio" name="filetype" value="csv" /> <?php _e('CSV Spreadsheet', $this -> plugin_name); ?></label><br/>
								<label><input onclick="jQuery('#csvdiv').hide(); jQuery('#macdiv').show();" <?php echo (!empty($_POST['filetype']) && $_POST['filetype'] == "mac") ? 'checked="checked"' : ''; ?> type="radio" name="filetype" value="mac" /> <?php _e('Mac OS Address Book (vCard file)', $this -> plugin_name); ?></label>
		                        <?php if (!empty($importerrors['filetype'])) : ?><div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle"></i> <?php echo $importerrors['filetype']; ?></p></div><?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div id="csvdiv" style="display:<?php echo (!empty($_POST['filetype']) && $_POST['filetype'] == "csv") ? 'block' : 'none'; ?>;">		
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="delimiter"><?php _e('Delimiter', $this -> plugin_name); ?></label></th>
								<td>
									<input class="widefat" style="width:45px;" type="text" name="delimiter" value="<?php echo (empty($_POST['delimiter'])) ? ',' : esc_attr(stripslashes($_POST['delimiter'])); ?>" id="delimiter" />
									<span class="howto"><?php _e('Operator delimiting field values. Open your CSV in a text editor to confirm with which operator field values are delimited. The default is comma (,).', $this -> plugin_name); ?></span>
			                        <?php if (!empty($importerrors['delimiter'])) : ?><div class="alert alert-danger"><p><i class="fa fa-exclamation-triangle"></i> <?php echo $importerrors['delimiter']; ?></p></div><?php endif; ?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Fields to Import', $this -> plugin_name); ?></th>
								<td>
									<p class="howto">
										<?php _e('Tick/check the fields which are available in your CSV file that you are uploading.', $this -> plugin_name); ?><br/>
										<?php _e('Fill in only column numbers (no letters), where "1" is the first column in your CSV file.', $this -> plugin_name); ?>
									</p>
								
									<div class="scroll-list" style="max-height:300px;">
										<table class="form-table">
											<tbody>
												<tr>
													<th style="padding:15px 0;"><label><input <?php echo $fieldemailcheck = ($_POST['fields']['mailinglists'] == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailinglistscolumn').toggle();" type="checkbox" name="fields[mailinglists]" value="Y" /> <?php _e('Mailing List(s)', $this -> plugin_name); ?></label></th>
													<td>
														<span id="mailinglistscolumn" style="display:<?php echo $fieldemaildisplay = ($_POST['fields']['mailinglists'] == "Y") ? 'block' : 'none'; ?>;">
															<b><?php _e('Column Number:', $this -> plugin_name); ?></b> <input type="text" class="widefat" style="width:45px;" name="mailinglistscolumn" value="<?php echo $_POST['mailinglistscolumn']; ?>" />
															<br/><label><input <?php echo (!empty($_POST['autocreatemailinglists']) && $_POST['autocreatemailinglists'] == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="autocreatemailinglists" value="Y" id="autocreatemailinglists" /> <?php _e('Auto create these lists by title if they do not exist', $this -> plugin_name); ?></label>
															<span class="howto"><?php _e('Comma (,) separated mailing list names/titles to add subscribers to in addition to the list(s) ticked/checked above.', $this -> plugin_name); ?></span>
														</span>
													</td>
												</tr>
												<!-- Email Address -->
												<tr>
													<th style="padding:15px 0;"><label><input <?php echo $fieldemailcheck = ($_POST['fields']['email'] == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#emailcolumn').toggle();" type="checkbox" name="fields[email]" value="Y" /> <?php _e('Email Address', $this -> plugin_name); ?></label></th>
													<td>
														<span id="emailcolumn" style="display:<?php echo $fieldemaildisplay = ($_POST['fields']['email'] == "Y") ? 'block' : 'none'; ?>;">
															<b><?php _e('Column Number:', $this -> plugin_name); ?></b> <input type="text" class="widefat" style="width:45px;" name="emailcolumn" value="<?php echo $_POST['emailcolumn']; ?>" />
														</span>
													</td>
												</tr>
												<!-- IP Address -->
												<tr>
													<th style="padding:15px 0;"><label><input <?php echo $fieldip_addresscheck = ($_POST['fields']['ip_address'] == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#ip_addresscolumn').toggle();" type="checkbox" name="fields[ip_address]" value="Y" /> <?php _e('IP Address', $this -> plugin_name); ?></label></th>
													<td>
														<span id="ip_addresscolumn" style="display:<?php echo $fieldip_addressdisplay = ($_POST['fields']['ip_address'] == "Y") ? 'block' : 'none'; ?>;">
															<b><?php _e('Column Number:', $this -> plugin_name); ?></b> <input type="text" class="widefat" style="width:45px;" name="ip_addresscolumn" value="<?php echo $_POST['ip_addresscolumn']; ?>" />
														</span>
													</td>
												</tr>
												<!-- Date -->
												<tr>
													<th style="padding:15px 0;"><label><input <?php echo $fieldcreatedcheck = ($_POST['fields']['created'] == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#createdcolumn').toggle();" type="checkbox" name="fields[created]" value="Y" /> <?php _e('Date', $this -> plugin_name); ?></label></th>
													<td>
														<span id="createdcolumn" style="display:<?php echo $fieldcreateddisplay = ($_POST['fields']['created'] == "Y") ? 'block' : 'none'; ?>;">
															<b><?php _e('Column Number:', $this -> plugin_name); ?></b> <input type="text" class="widefat" style="width:45px;" name="createdcolumn" value="<?php echo $_POST['createdcolumn']; ?>" />
														</span>
													</td>
												</tr>
												<?php $fields = $Field -> get_all('*'); ?>
												<?php if (!empty($fields)) : ?>
													<?php foreach ($fields as $field) : ?>
														<tr>
															<th style="padding:15px 0;"><label><input onclick="jQuery('#<?php echo $field -> slug; ?>div').toggle();" <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="fields[<?php echo $field -> slug; ?>]" value="Y" /> <?php echo __($field -> title); ?></label></th>
															<td>
																<span id="<?php echo $field -> slug; ?>div" style="display:<?php echo $display = (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == "Y") ? 'block' : 'none'; ?>;">
																	<b><?php _e('Column Number:', $this -> plugin_name); ?></b> <input type="text" class="widefat" style="width:45px;" name="<?php echo $field -> slug; ?>column" value="<?php echo $_POST[$field -> slug . 'column']; ?>" />
																</span>
															</td>
														</tr>
													<?php endforeach; ?>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div id="macdiv" style="display:<?php echo (!empty($_POST['filetype']) && $_POST['filetype'] == "mac") ? 'block' : 'none'; ?>;">
					<?php 
					
					global $wpdb;
					$fieldsquery = "SELECT `id`, `title`, `slug` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
					
					$query_hash = md5($fieldsquery);
					if ($ob_fields = $this -> get_cache($query_hash)) {
						$fields = $ob_fields;
					} else {
						$fields = $wpdb -> get_results($fieldsquery);
						$this -> set_cache($query_hash, $fields);
					}
					
					?>
				
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="macfields_fname"><?php _e('First Name', $this -> plugin_name); ?></label></th>
								<td>
									<?php if (!empty($fields)) : ?>
										<select name="macfields[fname]" id="macfields_fname">
											<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
											<?php foreach ($fields as $field) : ?>
												<option value="<?php echo $field -> slug; ?>"><?php _e($field -> title); ?></option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
									<span class="howto"><?php _e('Choose the custom field for the first name value from the vCard to go into.', $this -> plugin_name); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="macfields_lname"><?php _e('Last Name', $this -> plugin_name); ?></label></th>
								<td>
									<?php if (!empty($fields)) : ?>
										<select name="macfields[lname]" id="macfields_lname">
											<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
											<?php foreach ($fields as $field) : ?>
												<option value="<?php echo $field -> slug; ?>"><?php _e($field -> title); ?></option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
									<span class="howto"><?php _e('Choose the custom field for the last name value from the vCard to go into.', $this -> plugin_name); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="macfields_phone"><?php _e('Phone Number', $this -> plugin_name); ?></label></th>
								<td>
									<?php if (!empty($fields)) : ?>
										<select name="macfields[phone]" id="macfields_phone">
											<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
											<?php foreach ($fields as $field) : ?>
												<option value="<?php echo $field -> slug; ?>"><?php _e($field -> title); ?></option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
									<span class="howto"><?php _e('Choose the custom field for the phone number value from the vCard to go into.', $this -> plugin_name); ?></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="activation_Y"><?php _e('Require Activation?', $this -> plugin_name); ?></label></th>
							<td>
								<label><input onclick="jQuery('#activation_div').show();" <?php echo (!empty($_POST['activation']) && $_POST['activation'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="activation" value="Y" id="activation_Y" /> <?php _e('Yes, require activation', $this -> plugin_name); ?></label>
								<label><input onclick="jQuery('#activation_div').hide();" <?php echo (empty($_POST['activation']) || (!empty($_POST['activation']) && $_POST['activation'] == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="activation" value="N" id="activation_N" /> <?php _e('No, activate immediately', $this -> plugin_name); ?></label>
								<span class="howto"><?php _e('Would you like to send an activation/confirmation email to each subscriber to activate their subscription?', $this -> plugin_name); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div id="activation_div" style="display:<?php echo (!empty($_POST['activation']) && $_POST['activation'] == "Y") ? 'block' : 'none'; ?>;">			
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="confirmation_email"><?php _e('Confirmation Email', $this -> plugin_name); ?></label></th>
								<td>
									<p class="howto">
										<?php _e('An activation/confirmation email will be sent to each new subscriber imported. You may modify the email template below which will be sent out.', $this -> plugin_name); ?>
									</p>
									<?php $confirmation_subject = (empty($_POST['confirmation_subject'])) ? $this -> get_option('etsubject_confirm') : $_POST['confirmation_subject']; ?>
									<div id="titlediv">
		                        		<div id="titlewrap">
											<input type="text" name="confirmation_subject" value="<?php echo esc_attr(stripslashes(__($confirmation_subject))); ?>" id="title" class="widefat" />
		                        		</div>
									</div>
									<?php $confirmation_email = (empty($_POST['confirmation_email'])) ? __($this -> et_message('confirm')) : __($_POST['confirmation_email']); ?>							
									<!-- The Editor -->
									<?php if (version_compare(get_bloginfo('version'), "3.3") >= 0) : ?>
										<?php wp_editor(wpautop(stripslashes($confirmation_email)), 'content', array('tabindex' => 2, 'textarea_name' => "confirmation_email", 'textarea_rows' => "10")); ?>
									<?php else : ?>
										<?php the_editor(stripslashes($confirmation_email), 'confirmation_email', 'title', true, 2); ?>
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="import_overwrite"><?php _e('Update/Overwrite Subscribers', $this -> plugin_name); ?></label></th>
							<td>
								<label><input <?php echo (!empty($_POST['import_overwrite'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="import_overwrite" value="1" id="import_overwrite" /> <?php _e('Yes, update/overwrite existing subscribers with import data', $this -> plugin_name); ?></label>
								<span class="howto"><?php _e('Turning this on will take longer and could overwrite custom field values of subscribers.', $this -> plugin_name); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="import_preventbu"><?php _e('Bounces/Unsubscribes', $this -> plugin_name); ?></label>
							<?php echo $Html -> help(__('You can drastically reduce the load and processing of importing by disabling this.', $this -> plugin_name)); ?></th>
							<td>
								<label><input onclick="if (jQuery(this).is(':checked')) { if (!confirm('<?php _e('Enabling this will drastically increase processing time and queries to do all the checks.', $this -> plugin_name); ?>')) { return false; } }" type="checkbox" name="import_preventbu" value="1" id="import_preventbu" /> <?php _e('Prevent previous bounces/unsubscribes from being imported again', $this -> plugin_name); ?></label>
								<span class="howto"><?php _e('By ticking this, the system will check each subscriber and if they bounced/unsubscribed, they will not be imported.', $this -> plugin_name); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="import_progress_N"><?php _e('Show Progress', $this -> plugin_name); ?></label></th>
							<td>
								<label><input <?php echo (!empty($_POST['import_progress']) && $_POST['import_progress'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="import_progress" value="Y" id="import_progress_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
								<label><input <?php echo (empty($_POST['import_progress']) || (!empty($_POST['import_progress']) && $_POST['import_progress'] == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="import_progress" value="N" id="import_progress_N" /> <?php _e('No', $this -> plugin_name); ?></label>
								<span class="howto"><?php _e('Show Ajax progress as the import is done?', $this -> plugin_name); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<?php echo $Form -> submit(__('Import Subscribers', $this -> plugin_name)); ?>
				</p>
			</form>
		</div>
		<div id="export-tab">
			<h3 id="export"><?php _e('Export', $this -> plugin_name); ?></h3>	
	
			<?php $this -> render('error', array('errors' => $exporterrors), true, 'admin'); ?>
							
			<?php if (!empty($exportfile)) : ?>
				<div class="updated fade"><p><i class="fa fa-check"></i> <?php _e('Subscribers have been exported.', $this -> plugin_name); ?> <a href="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=exportdownload&file=<?php echo urlencode($exportfile); ?>" title="<?php _e('Download the subscribers CSV document to your computer', $this -> plugin_name); ?>"><?php _e('Download CSV', $this -> plugin_name); ?></a></p></div>
			<?php endif; ?>		
			<form action="?page=<?php echo $this -> sections -> importexport; ?>&amp;method=export#export" method="post" enctype="multipart/form-data" id="export-form">
				<input type="hidden" name="export_filetype" value="csv" />
			
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="exportlist"><?php _e('Mailing List(s)', $this -> plugin_name); ?></label></th>
							<td>
								<?php if (!empty($lists)) : ?>
									<div><label class="selectit" style="font-weight:bold;"><input type="checkbox" id="mailinglistsselectall" name="mailinglistsselectall" value="1" onclick="jqCheckAll(this, 'export-form', 'export_lists');" /> <?php _e('Select All Lists', $this -> plugin_name); ?></label></div>
									<div class="scroll-list">
										<?php foreach ($lists as $list_id => $list_title) : ?>
											<?php $Db -> model = $SubscribersList -> model; ?>
											<div><label><input <?php echo (!empty($_POST['export_lists']) && in_array($list_id, $_POST['export_lists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="export_lists[]" value="<?php echo $list_id; ?>" id="export_lists_<?php echo $list_id; ?>" /> <?php echo $list_title; ?> (<?php echo $Db -> count(array('list_id' => $list_id)); ?> <?php _e('subscribers', $this -> plugin_name); ?>)</label></div>
										<?php endforeach; ?>
									</div>
								<?php else : ?>
									<p class="newsletters_error"><?php _e('No mailing lists are available.', $this -> plugin_name); ?></p>
								<?php endif; ?>
								<span class="howto"><?php _e('Choose the mailing list(s) to export', $this -> plugin_name); ?></span>
							</td>
						</tr>
		                <tr>
		                    <th><label for="export_status_all"><?php _e('Export Status', $this -> plugin_name); ?></label></th>
		                    <td>
		                        <label><input <?php echo (empty($_POST['export_status']) || (!empty($_POST['export_status']) && $_POST['export_status'] == "all")) ? 'checked="checked"' : ''; ?> type="radio" name="export_status" value="all" id="export_status_all" /> <?php _e('All Subscriptions', $this -> plugin_name); ?></label><br/>
		                        <label><input <?php echo (!empty($_POST['export_status']) && $_POST['export_status'] == "active") ? 'checked="checked"' : ''; ?> type="radio" name="export_status" value="active" id="export_status_active" /> <?php _e('Active Subscriptions Only', $this -> plugin_name); ?></label><br/>
		                        <label><input <?php echo (!empty($_POST['export_status']) && $_POST['export_status'] == "inactive") ? 'checked="checked"' : ''; ?> type="radio" name="export_status" value="inactive" id="export_status_inactive" /> <?php _e('Inactive Subscriptions Only', $this -> plugin_name); ?></label>
		                    </td>
		                </tr>
		                <tr>
		                	<th><label for="export_purpose_newsletters"><?php _e('Export Purpose', $this -> plugin_name); ?></label></th>
		                	<td>
		                		<label><input <?php echo (empty($_POST['export_purpose']) || (!empty($_POST['export_purpose']) && $_POST['export_purpose'] == "newsletters")) ? 'checked="checked"' : ''; ?> type="radio" name="export_purpose" value="newsletters" id="export_purpose_newsletters" /> <?php _e('Newsletter plugin', $this -> plugin_name); ?></label>
		                		<label><input <?php echo (!empty($_POST['export_purpose']) && $_POST['export_purpose'] == "other") ? 'checked="checked"' : ''; ?> type="radio" name="export_purpose" value="other" id="export_purpose_other" /> <?php _e('3rd Party Software', $this -> plugin_name); ?></label>
		                		<span class="howto"><?php _e('Choose the purpose of this export.', $this -> plugin_name); ?></span>
		                	</td>
		                </tr>
		                <tr>
		                	<th><label for="export_delimiter"><?php _e('Delimiter', $this -> plugin_name); ?></label></th>
		                	<td>
		                		<input type="text" class="widefat" style="width:45px;" name="export_delimiter" value="<?php echo (!empty($_POST['export_delimiter'])) ? esc_attr(stripslashes($_POST['export_delimiter'])) : ';'; ?>" id="export_delimiter" />
		                		<span class="howto"><?php _e('Choose the delimiter to delimit columns with. The default is semi-colon (;)', $this -> plugin_name); ?></span>
		                	</td>
		                </tr>
		                <tr>
		                	<th><label for="export_progress_N"><?php _e('Show Progress', $this -> plugin_name); ?></label></th>
		                	<td>
		                		<label><input <?php echo (!empty($_POST['export_progress']) && $_POST['export_progress'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="export_progress" value="Y" id="export_progress_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
		                		<label><input <?php echo (empty($_POST['export_progress']) || (!empty($_POST['export_progress']) && $_POST['export_progress'] == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="export_progress" value="N" id="export_progress_N" /> <?php _e('No', $this -> plugin_name); ?></label>
		                		<span class="howto"><?php _e('Show Ajax progress as the export is done?', $this -> plugin_name); ?></span>
		                	</td>
		                </tr>
					</tbody>
				</table>
		
				<p class="submit">
					<?php echo $Form -> submit(__('Export Subscribers', $this -> plugin_name)); ?>
				</p>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php $active = (!empty($_GET['method']) && $_GET['method'] == "export") ? 1 : 0; ?>
	if (jQuery.isFunction(jQuery.fn.tabs)) {
		jQuery('#import-export-tabs').tabs({active:<?php echo $active; ?>});
	}
});
</script>