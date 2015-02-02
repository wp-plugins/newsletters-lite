<!-- Comments and Registration Form Settings -->
<table class="form-table">
	<tbody>
    	<tr>
        	<th><label for="commentformcheckbox_Y"><?php _e('Comment Form Checkbox', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#commentformcheckbox_div').show();" <?php echo ($this -> get_option('commentformcheckbox') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="commentformcheckbox" value="Y" id="commentformcheckbox_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#commentformcheckbox_div').hide();" <?php echo ($this -> get_option('commentformcheckbox') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="commentformcheckbox" value="N" id="commentformcheckbox_N" /> <?php _e('No', $this -> plugin_name); ?></label>
            	<span class="howto"><?php _e('Turn this on (Yes) to display a checkbox on the WordPress comment form for commentors to subscribe to a mailing list.', $this -> plugin_name); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div id="commentformcheckbox_div" style="display:<?php echo ($this -> get_option('commentformcheckbox') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="commentformlist"><?php _e('Subscribe List', $this -> plugin_name); ?></label></th>
                <td>
                	<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
                    	<select name="commentformlist" id="commentformlist">
                        	<?php foreach ($mailinglists as $id => $name) : ?>
                            	<option <?php echo ($this -> get_option('commentformlist') == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <span class="howto"><?php _e('Choose the list to which commentors will be subscribed when the checkbox is checked.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        	<tr>
            	<th><label for="commentformlabel"><?php _e('Comments Checkbox Label', $this -> plugin_name); ?></label></th>
                <td>
                	<?php if ($this -> language_do()) : ?>
						<?php
						
						$el = $this -> language_getlanguages();
						$commentformlabel = $this -> language_split($this -> get_option('commentformlabel'));
						
						?>
						<?php if (!empty($el)) : ?>					
							<div id="commentformlabeltabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($el as $language) : ?>
					                 	<li><a href="#commentformlabeltab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($el as $language) : ?>
					            	<div id="commentformlabeltab<?php echo $tabnumber; ?>">
					            		<input class="widefat" type="text" name="commentformlabel[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($commentformlabel[$language])); ?>" id="commentformlabel<?php echo $language; ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#commentformlabeltabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
                		<input class="widefat" type="text" name="commentformlabel" value="<?php echo esc_attr(stripslashes($this -> get_option('commentformlabel'))); ?>" id="commentformlabel" />
                	<?php endif; ?>
                	<span class="howto"><?php _e('Type a label/caption to display for the checkbox which your commentors will see.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
            	<th><label for="commentformautocheck_N"><?php _e('Auto Check Checkbox', $this -> plugin_name); ?></label></th>
                <td>
                	<label><input <?php echo ($this -> get_option('commentformautocheck') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="commentformautocheck" value="Y" id="commentformautocheck_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input <?php echo ($this -> get_option('commentformautocheck') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="commentformautocheck" value="N" id="commentformautocheck_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                    <span class="howto"><?php _e('automatically check the checkbox on the comment form.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
	<tbody>
    	<tr>
			<th><?php _e('Registration Checkbox', $this -> plugin_name); ?></th>
			<td>
				<?php $registercheckbox = $this -> get_option('registercheckbox'); ?>
				<label><input onclick="jQuery('#registercheckboxdiv').show();" <?php echo $check = ($registercheckbox == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="registercheckbox" value="Y" />&nbsp;<?php _e('Yes'); ?></label>
				<label><input onclick="jQuery('#registercheckboxdiv').hide();" <?php echo $check = ($registercheckbox == "N") ? 'checked="checked"' : ''; ?> type="radio" name="registercheckbox" value="N" />&nbsp;<?php _e('No'); ?></label>
				
				<?php $users_can_register = get_option('users_can_register'); ?>
				<?php if (empty($users_can_register) || $users_can_register == 0) : ?>
					<div class="<?php echo $this -> pre; ?>error"><?php _e('WordPress registration is currently deactivated', $this -> plugin_name); ?></div>
				<?php endif; ?>
                
                <span class="howto"><?php _e('Turn this on to place a subscribe checkbox on the registration form.', $this -> plugin_name); ?></span>
			</td>
		</tr>
    </tbody>
</table>

<div id="registercheckboxdiv" style="display:<?php echo $display = ($registercheckbox == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
        	<tr>
            	<th><label for="registerformlabel"><?php _e('Registration Checkbox Label', $this -> plugin_name); ?></label></th>
                <td>
                	<?php if ($this -> language_do()) : ?>
						<?php
						
						$el = $this -> language_getlanguages();
						$registerformlabel = $this -> language_split($this -> get_option('registerformlabel'));
						
						?>
						<?php if (!empty($el)) : ?>					
							<div id="registerformlabeltabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($el as $language) : ?>
					                 	<li><a href="#registerformlabeltab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($el as $language) : ?>
					            	<div id="registerformlabeltab<?php echo $tabnumber; ?>">
					            		<input class="widefat" type="text" name="registerformlabel[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($registerformlabel[$language])); ?>" id="registerformlabel<?php echo $language; ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#registerformlabeltabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
                		<input class="widefat" type="text" name="registerformlabel" value="<?php echo esc_attr(stripslashes($this -> get_option('registerformlabel'))); ?>" id="registerformlabel" />
                	<?php endif; ?>
                    <span class="howto"><?php _e('Label/caption text next to the checkbox on the registration form.', $this -> plugin_name); ?></span>
                </td>
            </tr>
			<tr>
				<th><?php _e('Auto check to subscribe'); ?></th>
				<td>
					<?php $checkboxon = $this -> get_option('checkboxon'); ?>
					<label><input <?php echo $check = ($checkboxon == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="checkboxon" value="Y" />&nbsp;<?php _e('Yes'); ?></label>
					<label><input <?php echo $check = ($checkboxon == "N") ? 'checked="checked"' : ''; ?> type="radio" name="checkboxon" value="N" />&nbsp;<?php _e('No'); ?></label>
					<span class="howto"><?php _e('Should the subscribe checkbox be ticked/checked by default or not?', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>autosubscribelist"><?php _e('Registration List(s)', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('New users will be subscribed to the chosen list(s) upon successful registration. The subscribe will only happen if the subscribe checkbox was ticked/checked by the user accordingly.', $this -> plugin_name)); ?></th>
				<td>
					<?php $autosubscribelist = $this -> get_option('autosubscribelist'); ?>
					<?php
					
					if (!empty($autosubscribelist) && is_numeric($autosubscribelist) && !is_array($autosubscribelist)) {
						$autosubscribelist = array($autosubscribelist);
					}
					
					?>
					<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
						<div class="scroll-list">
							<?php foreach ($mailinglists as $list_id => $list_title) : ?>
								<label><input <?php echo (!empty($autosubscribelist) && in_array($list_id, $autosubscribelist)) ? 'checked="checked"' : ''; ?> type="checkbox" name="autosubscribelist[]" value="<?php echo $list_id; ?>" id="autosubscribelist_<?php echo $list_id; ?>" /> <?php _e($list_title); ?></label><br/>
							<?php endforeach; ?>
						</div>
						<span class="howto"><?php _e('To which list(s) should new users be subscribed upon registration?', $this -> plugin_name); ?></span>
					<?php else : ?>
						<span class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>