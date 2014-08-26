<?php

if ($this -> is_plugin_active('qtranslate')) {
	global $q_config;
	$el = qtrans_getSortedLanguages();
}

?>

<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Custom Field', $this -> plugin_name); ?></h2>
	<?php $this -> render_admin('error', array('errors' => $errors)); ?>
	<?php $slug = $Html -> field_value('Field[slug]'); ?>
    
	<form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> fields; ?>&amp;method=save" method="post" id="Field.saveform">
		<?php echo $Form -> hidden('Field[id]'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Field_title"><?php _e('Title', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> is_plugin_active('qtranslate')) : ?>
							<div id="tabs_title">
								<ul>
									<?php $tabs_title = 1; ?>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_title_<?php echo $tabs_title; ?>"><img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo esc_attr($language); ?>" /></a>
										</li>
										<?php $tabs_title++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_title = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_title_<?php echo $tabs_title; ?>">
										<input <?php echo ($tabs_title == 1) ? 'onkeyup="wpml_titletoslug(this.value);"' : ''; ?> type="text" class="widefat" name="Field[title][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[title]', $language))); ?>" id="Field_title_<?php echo $language; ?>" />
									</div>
									<?php $tabs_title++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<input onkeyup="wpml_titletoslug(this.value);" type="text" class="widefat" name="Field[title]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[title]'))); ?>" id="Field_title" />
						<?php endif; ?>
                        <span class="howto"><?php _e('Title/name for this custom field as it will be displayed on subscribe forms.', $this -> plugin_name); ?></span>
                        <?php echo $Html -> field_error('Field[title]'); ?>
                    </td>
				</tr>
				<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
	                <tr>
	                	<th><label for="Field_slug"><?php _e('Slug/Nicename', $this -> plugin_name); ?></label></th>
	                    <td>
	                    	<input id="Field_slug" type="text" class="widefat" name="Field[slug]" value="<?php echo $Html -> field_value('Field[slug]'); ?>" />
	                        <?php $fieldslugerror = $Html -> field_error('Field[slug]'); ?>
	                        <?php if (!empty($fieldslugerror)) : ?>
	                        	<div class="<?php echo $this -> pre; ?>"><?php echo $fieldslugerror; ?></div>
	                        <?php endif; ?>
	                        <span class="howto"><?php _e('Only use letters, lowercase, no spaces or other characters, please.', $this -> plugin_name); ?></span>
	                    </td>
	                </tr>
	            <?php else : ?>
	            	<input type="hidden" name="Field[slug]" value="<?php echo esc_attr(stripslashes($slug)); ?>" />
	            <?php endif; ?>
				<tr>
					<th><label for="Field.caption"><?php _e('Caption/Description', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> is_plugin_active('qtranslate')) : ?>
							<div id="tabs_caption">
								<?php $tabs_caption = 1; ?>
								<ul>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_caption_<?php echo $tabs_caption; ?>">
												<img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" />
											</a>
										</li>	
										<?php $tabs_caption++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_caption = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_caption_<?php echo $tabs_caption; ?>">
										<input type="text" class="widefat" name="Field[caption][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[caption]', $language))); ?>" id="Field_caption_<?php echo $language; ?>" />
									</div>
									<?php $tabs_caption++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php echo $Form -> text('Field[caption]'); ?>
						<?php endif; ?>
						<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Give this field a descriptive caption/notation for subscribers to see.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="Field.watermark"><?php _e('Watermark', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> is_plugin_active('qtranslate')) : ?>
							<div id="tabs_watermark">
								<ul>
									<?php $tabs_watermark = 1; ?>
									<?php foreach ($el as $language) : ?>
										<li>
											<a href="#tabs_watermark_<?php echo $tabs_watermark; ?>">
												<img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" />
											</a>
										</li>
										<?php $tabs_watermark++; ?>
									<?php endforeach; ?>
								</ul>
								
								<?php $tabs_watermark = 1; ?>
								<?php foreach ($el as $language) : ?>
									<div id="tabs_watermark_<?php echo $tabs_watermark; ?>">
										<input type="text" class="widefat" name="Field[watermark][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[watermark]', $language))); ?>" id="Field_watermark_<?php echo $language; ?>" />
									</div>
									<?php $tabs_watermark++; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php echo $Form -> text('Field[watermark]'); ?>
						<?php endif; ?>
						<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Watermark to show inside the input field which will disappear when the field is clicked on.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
					<tr>
						<th><label for="Field.requiredNo"><?php _e('Required', $this -> plugin_name); ?></label></th>
						<td>
							<?php $buttons = array('Y' => __('Yes', $this -> plugin_name), 'N' => __('No', $this -> plugin_name)); ?>
							<?php echo $Form -> radio('Field[required]', $buttons, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#errormessagediv').show(); } else { jQuery('#errormessagediv').hide(); }")); ?>
						</td>
					</tr>
				<?php else : ?>
					<input type="hidden" name="Field[required]" value="Y" />
				<?php endif; ?>
			</tbody>
		</table>
		
		<div id="errormessagediv" style="display:<?php echo ($Html -> field_value('Field[required]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field.errormessage"><?php _e('Error Message', $this -> plugin_name); ?></label></th>
						<td>
							<?php if ($this -> is_plugin_active('qtranslate')) : ?>
								<div id="tabs_errormessage">
									<ul>
										<?php $tabs_errormessage = 1; ?>
										<?php foreach ($el as $language) : ?>
											<li>
												<a href="#tabs_errormessage_<?php echo $tabs_errormessage; ?>">
													<img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" />
												</a>
											</li>
											<?php $tabs_errormessage++; ?>
										<?php endforeach; ?>
									</ul>
									
									<?php $tabs_errormessage = 1; ?>
									<?php foreach ($el as $language) : ?>
										<div id="tabs_errormessage_<?php echo $tabs_errormessage; ?>">
											<input type="text" class="widefat" name="Field[errormessage][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[errormessage]', $language))); ?>" id="Field_errormessage_<?php echo $tabs_errormessage; ?>" />
										</div>
										<?php $tabs_errormessage++; ?>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<?php echo $Form -> text('Field[errormessage]'); ?>
							<?php endif; ?>
							<div class="howto"><?php _e('Error message which will be displayed when the field is left empty', $this -> plugin_name); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field.type"><?php _e('Field Type', $this -> plugin_name); ?></label></th>
						<td>
							<?php 
							
							$this -> init_fieldtypes();
							$types = $this -> get_option('fieldtypes'); 
							unset($types['special']);
							
							?>
							<?php echo $Form -> select('Field[type]', $types, array('onchange' => "if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox') { jQuery('#typediv').show(); } else { if (this.value == 'file') { jQuery('#filediv').show(); } else { jQuery('#filediv').hide(); } jQuery('#typediv').hide(); }")); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php elseif ($slug == "list") : ?>
			<input type="hidden" name="Field[type]" value="special" />
		<?php else : ?>	
			<input type="hidden" name="Field[type]" value="text" />
		<?php endif; ?>
		
		<div id="filediv" style="display:<?php echo ($Html -> field_value('Field[type]') == "file") ? 'block' : 'none'; ?>;">		
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field_fileext"><?php _e('Alowed File Types', $this -> plugin_name); ?></label></th>
						<td>
							<input type="text" name="Field[fileext]" value="<?php echo esc_attr(stripslashes($Html -> field_value('Field[filetypes]'))); ?>" id="Field_fileext" class="widefat" />
							<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php echo sprintf(__('A comma separated (no spaces) list of allowed file extensions eg. %s', $this -> plugin_name), '".jpg,.png,.csv,.zip,.html"'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="Field_sizelimit"><?php _e('File Size Limit', $this -> plugin_name); ?></label></th>
						<td>
							<?php
							
							$filesizelimit = $Html -> field_value('Field[filesizelimit]');
							$sizelimit = preg_replace("/[^0-9.]/si", "", $filesizelimit);
							$sizetype = preg_replace("/[0-9]*/si", "", $filesizelimit);
							
							?>
							<input type="text" name="Field[sizelimit]" value="<?php echo esc_attr(stripslashes($sizelimit)); ?>" id="Field_sizelimit" class="widefat" style="width:45px;" />
							<select name="Field[sizetype]" id="Field_sizetype">
								<option <?php echo (!empty($sizetype) && $sizetype == "B") ? 'selected="selected"' : ''; ?> value="B"><?php _e('Bytes (B)', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($sizetype) && $sizetype == "KB") ? 'selected="selected"' : ''; ?> value="KB"><?php _e('Kilobytes (KB)', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($sizetype) && $sizetype == "MB") ? 'selected="selected"' : ''; ?> value="MB"><?php _e('Megabytes (MB)', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($sizetype) && $sizetype == "GB") ? 'selected="selected"' : ''; ?> value="GB"><?php _e('Gigabytes (GB)', $this -> plugin_name); ?></option>
							</select>
							<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Specify the maximum size that files are allowed to be when uploading.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="typediv" style="display:<?php echo ($Html -> field_value('Field[type]') == "checkbox" || $Html -> field_value('Field[type]') == "radio" || $Html -> field_value('Field[type]') == "select") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr class="form-field" style="display:block;">
						<th><label for="Field.fieldoptions"><?php _e('Field Options', $this -> plugin_name); ?></label></th>
						<td>
							<?php if ($this -> is_plugin_active('qtranslate')) : ?>							
								<div id="tabs_fieldoptions">
									<ul>
										<?php $tabs_fieldoptions = 1; ?>
										<?php foreach ($el as $language) : ?>
											<li>
												<a href="#tabs_fieldoptions_<?php echo $tabs_fieldoptions; ?>">
													<img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" />
												</a>
											</li>
											<?php $tabs_fieldoptions++; ?>
										<?php endforeach; ?>
									</ul>
									
									<?php $tabs_fieldoptions = 1; ?>
									<?php foreach ($el as $language) : ?>
										<div id="tabs_fieldoptions_<?php echo $tabs_fieldoptions; ?>">
											<textarea style="width:100%;" width="100%" cols="100%" rows="5" class="widefat" name="Field[fieldoptions][<?php echo $language; ?>]" id="Field_fieldoptions_<?php echo $tabs_fieldoptions; ?>"><?php echo stripslashes($Html -> field_value('Field[fieldoptions]', $language)); ?></textarea>
										</div>
										<?php $tabs_fieldoptions++; ?>
									<?php endforeach; ?>
								</div>
								<span class="howto"><?php _e('Place each option on a new line and add the same number of options for each language.', $this -> plugin_name); ?></span>
							<?php else : ?>
								<?php $Field -> data[$Field -> model] -> fieldoptions = @implode("\n", unserialize($Html -> field_value('Field[fieldoptions]'))); ?>
								<?php echo $Form -> textarea('Field[fieldoptions]'); ?>
								<div class="howto"><?php _e('Place each option on a newline', $this -> plugin_name); ?></div>
	                            <?php if ($Html -> field_value('Field[id]') != "") : ?>
	                            	<div class="howto"><?php _e('Removing options or changing the order will affect existing subscribers. Rather just add options below the current ones.', $this -> plugin_name); ?></div>
	                            <?php endif; ?>
	                        <?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (empty($slug) || ($slug != 'email' && $slug != "list")) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Field_display_always"><?php _e('Display', $this -> plugin_name); ?></label></th>
						<td>
							<label><input onclick="jQuery('#displaydiv').hide();" <?php echo ($Html -> field_value('Field[display]') == "always") ? 'checked="checked"' : ''; ?> type="radio" name="Field[display]" value="always" id="Field_display_always" /> <?php _e('Always show', $this -> plugin_name); ?></label>
							<label><input onclick="jQuery('#displaydiv').show();" <?php echo ($Html -> field_value('Field[display]') == "specific") ? 'checked="checked"' : ''; ?> type="radio" name="Field[display]" value="specific" id="Field_display_specific" /> <?php _e('Specific list(s)', $this -> plugin_name); ?></label>
							<span class="howto"><?php _e('Should this field always show or only for specific mailing list(s)?', $this -> plugin_name); ?></span>
							<?php echo $Html -> field_error('Field[display]'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		
			<div id="displaydiv" style="display:<?php echo ($Html -> field_value('Field[display]') == "specific") ? 'block' : 'none'; ?>;">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="checkboxall"><?php _e('Mailing Lists', $this -> plugin_name); ?></label></th>
							<td>
								<?php $Field -> data = (array) $Field -> data; ?>
								<?php $lists = $Mailinglist -> select(true); ?>
								<?php if (!empty($lists)) : ?>
									<label style="font-weight:bold;"><input type="checkbox" id="mailinglistsselectall" name="mailinglistsselectall" value="1" onclick="jqCheckAll(this, 'Field.saveform', 'Field[mailinglists]');" /> <?php _e('Select All', $this -> plugin_name); ?></label><br/>
									<div class="scroll-list">
										<?php foreach ($lists as $id => $title) : ?>
											<?php $checkedlistsbyfield = $FieldsList -> checkedlists_by_field(); ?>
											<?php $fieldslist = (empty($Field -> data[$Field -> model] -> id)) ? $_POST[$Field -> model]['mailinglists'] : $checkedlistsbyfield; ?>
											<label><input <?php echo (!empty($fieldslist) && is_array($fieldslist) && in_array($id, $fieldslist)) ? 'checked="checked"' : ''; ?> type="checkbox" id="checklist<?php echo $id; ?>" name="Field[mailinglists][]" value="<?php echo $id; ?>" /> <?php echo $title; ?></label><br/>
										<?php endforeach; ?>
									</div>
									<?php echo $Html -> field_error('Field[mailinglists]'); ?>
								<?php else : ?>
									<p class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists were found', $this -> name); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<input type="hidden" name="Field[display]" value="always" />
		<?php endif; ?>
		
		<p class="submit">
			<?php echo $Form -> submit(__('Save Custom Field', $this -> plugin_name)); ?>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	/* Tabs */
	<?php if ($this -> is_plugin_active('qtranslate')) : ?>
		jQuery('#tabs_title').tabs();
		jQuery('#tabs_caption').tabs();
		jQuery('#tabs_watermark').tabs();
		jQuery('#tabs_errormessage').tabs();
		jQuery('#tabs_fieldoptions').tabs();
	<?php else : ?>
		/* Watermarks */
		jQuery('#Field_title').Watermark('<?php echo addslashes(__('Enter field title/name here', $this -> plugin_name)); ?>');
		jQuery('#Field_slug').Watermark('<?php echo addslashes(__('Enter field slug for database and shortcodes use here', $this -> plugin_name)); ?>');
		jQuery('[name="Field[caption]"]').Watermark('<?php echo addslashes(__('Enter a caption/description to show below the field here', $this -> plugin_name)); ?>');
		jQuery('[name="Field[watermark]"]').Watermark('<?php echo addslashes(__('Enter watermark text here', $this -> plugin_name)); ?>');
	<?php endif; ?>
});
</script>