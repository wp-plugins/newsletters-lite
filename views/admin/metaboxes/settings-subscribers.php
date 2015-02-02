<table class="form-table">
	<tbody>
		<tr>
			<th><label for="subscriberedirect_N"><?php _e('Redirect On Success Subscribe', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('This redirect takes effect on the actual subscribe form when a user subscribes. You can turn this setting on to redirect a subscriber to a specific place upon successful subscribe.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="jQuery('#subscriberedirecturl_div').show();" <?php echo ($this -> get_option('subscriberedirect') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberedirect" value="Y" id="subscriberedirect_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#subscriberedirecturl_div').hide();" <?php echo ($this -> get_option('subscriberedirect') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberedirect" value="N" id="subscriberedirect_N" /> <?php _e('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should a subscriber be redirected after successfully subscribing?', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="subscriberedirecturl_div" style="display:<?php echo ($this -> get_option('subscriberedirect') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="subscriberedirecturl"><?php _e('Redirect URL', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" name="subscriberedirecturl" id="subscriberedirecturl" class="widefat" value="<?php echo $this -> get_option('subscriberedirecturl'); ?>" />
					<span class="howto"><?php _e('Absolute URL to redirect to after successfully subscribing.', $this -> plugin_name); ?></span>	
				</td>
			</tr>
		</tbody>
	</table>
</div>
		
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>generalredirect"><?php _e('General Redirect URL', $this -> plugin_name); ?></label></th>
			<td>
				<input type="text" class="widefat" id="<?php echo $this -> pre; ?>generalredirect" name="generalredirect" value="<?php echo $this -> get_option('generalredirect'); ?>" />
				<span class="howto"><?php _e('Redirect upon unsubscription, activation, etc...', $this -> plugin_name); ?></span>
			</td>
		</tr>
        <tr>
        	<th><label for="subscriberexistsredirect_management"><?php _e('Subscriber Exists Redirect', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').hide();" <?php echo ($this -> get_option('subscriberexistsredirect') == "management") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="management" id="subscriberexistsredirect_management" /> <?php _e('Management Section', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').show();" <?php echo ($this -> get_option('subscriberexistsredirect') == "custom") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="custom" id="subscriberexistsredirect_custom" /> <?php _e('Custom URL', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').hide();" <?php echo ($this -> get_option('subscriberexistsredirect') == "nothing") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="nothing" id="subscriberexistsredirect_nothing" /> <?php _e('Do Nothing', $this -> plugin_name); ?></label>
            	<span class="howto"><?php _e('What to do when a user subscribes with an existing email address?', $this -> plugin_name); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div id="subscriberexistsredirectcustomdiv" style="display:<?php echo ($this -> get_option('subscriberexistsredirect') == "custom") ? 'block' : 'none'; ?>;">
	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="subscriberexistsredirecturl"><?php _e('Custom Redirect URL', $this -> plugin_name); ?></label></th>
                <td>
                	<input type="text" class="widefat" name="subscriberexistsredirecturl" value="<?php echo esc_attr(stripslashes($this -> get_option('subscriberexistsredirecturl'))); ?>" id="subscriberexistsredirecturl" />
                	<span class="howto"><?php _e('URL/Link to redirect an existing subscriber to.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
	<tbody>
    	<tr>
        	<th><label for="subscriberexistsmessage"><?php _e('Subscriber Exists Message', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($this -> language_do()) : ?>
            		<?php 
					
					$el = $this -> language_getlanguages(); 
					$subscriberexistsmessage = $this -> language_split($this -> get_option('subscriberexistsmessage'));
					
					?>
					<div id="subscriberexistsmessagetabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#subscriberexistsmessagetab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="subscriberexistsmessagetab<?php echo $tabnumber; ?>">
			            		<input type="text" name="subscriberexistsmessage[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($subscriberexistsmessage[$language])); ?>" id="subscriberexistsmessage_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#subscriberexistsmessagetabs').tabs();
						}
					});
					</script>
            	<?php else : ?>
            		<input type="text" class="widefat" name="subscriberexistsmessage" value="<?php echo esc_attr(stripslashes($this -> get_option('subscriberexistsmessage'))); ?>" id="subscriberexistsmessage" />
            	<?php endif; ?>
            	<span class="howto"><?php _e('Message to show to a user when they already exist for the specified list(s).', $this -> plugin_name); ?></span>
            </td>
        </tr>
	</tbody>
</table>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>onlinelinktext"><?php _e('Online Newsletter Link Text', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$el = $this -> language_getlanguages(); 
					$onlinelinktext = $this -> language_split($this -> get_option('onlinelinktext'));
					
					?>
					<div id="onlinelinktexttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($el as $language) : ?>
			                 	<li><a href="#onlinelinktexttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($el as $language) : ?>
			            	<div id="onlinelinktexttab<?php echo $tabnumber; ?>">
			            		<input type="text" name="onlinelinktext[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($onlinelinktext[$language])); ?>" id="onlinelinktext_<?php echo $language; ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#onlinelinktexttabs').tabs();
						}
					});
					</script>
				<?php else : ?>
					<input class="widefat" type="text" id="<?php echo $this -> pre; ?>onlinelinktext" name="onlinelinktext" value="<?php echo $this -> get_option('onlinelinktext'); ?>" />
				<?php endif; ?>
				<span class="howto"><?php _e('Displays email in browser. generated by <code>[' . $this -> pre . 'online]</code> in content', $this -> plugin_name); ?></span>
			</td>
		</tr>	
		<tr>
			<th><?php _e('Admin Notification on Subscription', $this -> plugin_name); ?></th>
			<td>
				<?php $adminemailonsubscription = $this -> get_option('adminemailonsubscription'); ?>
				<label><input <?php echo $check1 = ($adminemailonsubscription == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonsubscription" value="Y" /> <?php _e('Yes'); ?></label>
				<label><input <?php echo $check2 = ($adminemailonsubscription == "N") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonsubscription" value="N" /> <?php _e('No'); ?></label>
			</td>
		</tr>
		<?php $requireactivate = $this -> get_option('requireactivate'); ?>
		<tr>
			<th><?php _e('Require Confirmation?', $this -> plugin_name); ?></th>
			<td>
				<label><input onclick="jQuery('#requireactivatediv').show();" <?php echo $check1 = ($requireactivate == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="requireactivate" value="Y" />&nbsp;<?php _e('Yes, confirm email address', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#requireactivatediv').hide();" <?php echo $check2 = ($requireactivate == "N") ? 'checked="checked"' : ''; ?> type="radio" name="requireactivate" value="N" />&nbsp;<?php _e('No, immediately activate', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Is double opt-in action required by subscribers to activate subscriptions?', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="requireactivatediv" style="display:<?php echo $requireactivatedisplay = ($requireactivate == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="activationemails_single"><?php _e('Confirmation Emails', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('activationemails') == "single") ? 'checked="checked"' : ''; ?> type="radio" name="activationemails" value="single" id="activationemails_single" /> <?php _e('Single Email', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('activationemails') == "multiple") ? 'checked="checked"' : ''; ?> type="radio" name="activationemails" value="multiple" id="activationemails_multiple" /> <?php _e('Multiple Emails (One for each list)', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Should a single email or multiple emails (one for each list) be sent for confirmation when subscribing to multiple lists.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>activationlinktext"><?php _e('Activation Link Text', $this -> plugin_name); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<?php 
					
						$el = $this -> language_getlanguages(); 
						$activationlinktext = $this -> language_split($this -> get_option('activationlinktext'));
						
						?>
						<div id="activationlinktexttabs">
							<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#activationlinktexttab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="activationlinktexttab<?php echo $tabnumber; ?>">
				            		<input type="text" name="activationlinktext[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($activationlinktext[$language])); ?>" id="activationlinktext_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#activationlinktexttabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<input class="widefat" type="text" id="<?php echo $this -> pre; ?>activationlinktext" name="activationlinktext" value="<?php echo $this -> get_option('activationlinktext'); ?>" />
					<?php endif; ?>
					<span class="howto"><?php _e('Displays an activation link generated by <code>[' . $this -> pre . 'activate]</code> in content', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="customactivateredirect_N"><?php _e('Confirm Redirect', $this -> plugin_name); ?></label></th>
				<td>
					<label><input onclick="jQuery('#customactivateredirect_div').show();" <?php echo ($this -> get_option('customactivateredirect') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="customactivateredirect" value="Y" id="customactivateredirect_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#customactivateredirect_div').hide();" <?php echo ($this -> get_option('customactivateredirect') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="customactivateredirect" value="N" id="customactivateredirect_N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Defaults to the subscriber management section. This URL can be configured per mailing list as well.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="customactivateredirect_div" style="display:<?php echo ($this -> get_option('customactivateredirect') == "Y") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="activateredirecturl"><?php _e('Confirm Redirect URL', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" class="widefat" name="activateredirecturl" value="<?php echo esc_attr(stripslashes($this -> get_option('activateredirecturl'))); ?>" id="activateredirecturl" />
						<span class="howto"><?php _e('Link/URL to which subscribers will be redirected upon activation.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="activateaction_none"><?php _e('Inactive Subscriptions', $this -> plugin_name); ?></label></th>
				<td>
					<?php $activateaction = $this -> get_option('activateaction'); ?>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide();" <?php echo (empty($activateaction) || $activateaction == "none") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="none" id="activateaction_none" /> <?php _e('Do Nothing', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide(); jQuery('#activateaction_' + this.value + '_div').show();" <?php echo (!empty($activateaction) && $activateaction == "remind") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="remind" id="activateaction_remind" /> <?php _e('Send Reminder', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide(); jQuery('#activateaction_' + this.value + '_div').show();" <?php echo (!empty($activateaction) && $activateaction == "delete") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="delete" id="activateaction_delete" /> <?php _e('Delete', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('How should inactive subscriptions be handled?', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<!-- Activate delete settings -->
	<div id="activateaction_delete_div" style="display:<?php echo (!empty($activateaction) && $activateaction == "delete") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for=""><?php _e('Delete Delay', $this -> plugin_name); ?></label></th>
					<td>
						<?php $activatedelete = $this -> get_option('activatedelete'); ?>
						<?php echo sprintf(__('Delete inactive subscriptions %s days after subscribing.', $this -> plugin_name), '<input type="text" class="widefat" style="width:45px;" name="activatedelete" value="' . esc_attr(stripslashes($activatedelete)) . '" id="activatedelete" />'); ?>
						<span class="howto"><?php _e('After how many days should an inactive subscription to a list be deleted?', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<!-- Activate reminder settings -->
	<div id="activateaction_remind_div" style="display:<?php echo (!empty($activateaction) && $activateaction == "remind") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="activatereminder"><?php _e('Confirmation Reminder', $this -> plugin_name); ?></label></th>
					<td>
						<?php $activatereminder = $this -> get_option('activatereminder'); ?>
						<?php echo sprintf(__('Send an activate reminder to inactive subscriptions %s days after subscribing', $this -> plugin_name), '<input type="text" class="widefat" style="width:45px;" name="activatereminder" value="' . esc_attr(stripslashes($activatereminder)) . '" id="activatereminder" />'); ?>
						<span class="howto"><?php _e('Send a confirmation reminder to a subscriber X days after subscribing.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>