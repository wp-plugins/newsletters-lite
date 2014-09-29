<!-- Save an Autoresponder -->

<?php

global $ID, $post_ID, $post;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

$alwayssend = $Autoresponder -> data -> alwayssend;

?>

<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Save an Autoresponder', $this -> plugin_name); ?></h2>
    
    <form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> autoresponders; ?>&amp;method=save" method="post">
    	<?php echo $Form -> hidden('Autoresponder[id]'); ?>
    
    	<table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Autoresponder.title"><?php _e('Name', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The name/title of your autoresponder for internal identification purposes. Your subscribers will not see this.', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php echo $Form -> text('Autoresponder[title]'); ?>
                    	<span class="howto"><?php _e('Fill in a name/title for this autoresponder for identification purposes.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
                <tr>
                	<th><label for="selectall"><?php _e('Mailing List(s)', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('Choose the mailing list(s) to attach to this autoresponder. When a subscriber subscribes to any of the chosen list(s) and the subscription is active, this autoresponder will be sent to the subscriber.', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
                        	<div><label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Select all', $this -> plugin_name); ?></label></div>                        
                        	<!-- loop of mailing lists -->
                        	<div class="scroll-list">
                            	<?php foreach ($mailinglists as $list_id => $list_title) : ?>
                                	<div><label><input <?php echo (!empty($Autoresponder -> data -> lists) && in_array($list_id, $Autoresponder -> data -> lists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="Autoresponder[lists][]" value="<?php echo $list_id; ?>" id="checklist<?php echo $list_id; ?>" /> <?php echo $list_title; ?></label></div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                        	<span class="error"><?php _e('No mailinglists found, please add.', $this -> plugin_name); ?></span>
                        <?php endif; ?>
                        <?php echo $Html -> field_error('Autoresponder[lists]'); ?>
                    	<span class="howto"><?php _e('Subscriptions to these list(s) will be subscribed to this autoresponder.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
                <tr>
                	<th><label for="Autoresponder_applyexisting_N"><?php _e('Apply to Existing Subscribers?', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('If you choose to apply this autoresponder to existing subscribers, it will be sent to all existing subscribers in the database subscribed to the specified mailing list(s). By default, the autoresponder only sends to new subscribers if this option is set to "No".', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php $applyexisting = $Html -> field_value('Autoresponder[applyexisting]'); ?>
                    	<label><input <?php echo ($applyexisting == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[applyexisting]" value="Y" id="Autoresponder_applyexisting_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                        <label><input <?php echo (empty($applyexisting) || $applyexisting == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[applyexisting]" value="N" id="Autoresponder_applyexisting_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                        
                        <?php if ($applyexisting == "Y") : ?><div class="<?php echo $this -> pre; ?>error"><?php _e('Autoresponder has already been applied to the existing subscribers before.', $this -> plugin_name); ?><br/>
                        <?php _e('This autoresponder will not be queued to the same subscribers as before again, only new ones.'); ?></div><?php endif; ?>
                    	<span class="howto">
							<?php _e('Should this autoresponder be applied to existing subscribers of the list(s) above?', $this -> plugin_name); ?><br/>
                            <?php _e('The send delay will be applied from the current date/time.', $this -> plugin_name); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                	<th><label for="Autoresponder_alwayssend_N"><?php _e('Always Send?', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('You may want an autoresponder to always send/queue when a subscriber subscribes, even if they are already subscribed and if they have already received this autoresponder email. Set this to Yes to always send and to No to ensure that each subscriber gets and autoresponder email only once.', $this -> plugin_name)); ?></th>
                	<td>
                		<label><input <?php echo (!empty($alwayssend) && $alwayssend == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[alwayssend]" value="Y" id="Autoresponder_alwayssend_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                		<label><input <?php echo ((empty($alwayssend)) || (!empty($alwayssend) && $alwayssend == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[alwayssend]" value="N" id="Autoresponder_alwayssend_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                		<span class="howto"><?php _e('Should this autoresponder always be sent to a subscriber, disregarding if it has been sent before?', $this -> plugin_name); ?></span>
                	</td>
                </tr>
                <tr>
                	<th><label for="Autoresponder.newsletter.exi"><?php _e('Newsletter', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The email which will be used for the autoresponder can be either an existing sent/draft email from the Newsletters > Sent &amp; Draft Emails section or you can choose to create a new email below.', $this -> plugin_name)); ?></th>
                    <td>                    
                    	<label><input onclick="jQuery('#newsletterdiv_exi').show(); jQuery('#newsletterdiv_new').hide();" <?php echo (empty($Autoresponder -> data -> newsletter) || (!empty($Autoresponder -> data -> newsletter) && $Autoresponder -> data -> newsletter == "exi")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[newsletter]" value="exi" id="Autoresponder.newsletter.exi" /> <?php _e('Choose Newsletter', $this -> plugin_name); ?></label>
                        <label><input onclick="jQuery('#newsletterdiv_exi').hide(); jQuery('#newsletterdiv_new').show();" <?php echo (!empty($Autoresponder -> data -> newsletter) && $Autoresponder -> data -> newsletter == "new") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[newsletter]" value="new" id="Autoresponder.newsletter.new" /> <?php _e('Create Newsletter', $this -> plugin_name); ?></label>
                        <?php echo $Html -> field_error('Autoresponder[newsletter]'); ?>
                        <span class="howto"><?php _e('You can choose an existing newsletter or create one now.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="newsletterdiv_exi" style="display:<?php echo (empty($Autoresponder -> data -> newsletter) || (!empty($Autoresponder -> data -> newsletter) && $Autoresponder -> data -> newsletter == "exi")) ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for="Autoresponder.history_id"><?php _e('Sent/Draft Newsletter', $this -> plugin_name); ?></label>
                    	<?php echo $Html -> help(__('Choose the existing sent/draft email to use from the Newsletters > History/Draft Emails section as is.', $this -> plugin_name)); ?></th>
                        <td>
                        	<?php if ($histories = $History -> select()) : ?>
                            	<select name="Autoresponder[history_id]" id="Autoresponder.history_id">
                                	<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
                                    <?php foreach ($histories as $h_id => $h_subject) : ?>
                                    	<option <?php echo (!empty($Autoresponder -> data -> history_id) && $Autoresponder -> data -> history_id == $h_id) ? 'selected="selected"' : ''; ?> value="<?php echo $h_id; ?>"><?php echo __($h_subject); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                            	<span class="error"><?php _e('No sent/draft emails found, please add.', $this -> plugin_name); ?></span>
                            <?php endif; ?>
                            <?php echo $Html -> field_error('Autoresponder[history_id]'); ?>
                        	<span class="howto"><?php _e('Choose an existing history/draft newsletter to use as the message for this autoresponder.', $this -> plugin_name); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="newsletterdiv_new" style="display:<?php echo (!empty($Autoresponder -> data -> newsletter) && $Autoresponder -> data -> newsletter == "new") ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for="Autoresponder_nnewsletter_subject"><?php _e('Newsletter Subject', $this -> plugin_name); ?></label>
                    	<?php echo $Html -> help(__('Fill in the email subject of the new email which will be sent for this autoresponder. This is the subject which will show up in the email/webmail client of your subscribers.', $this -> plugin_name)); ?></th>
                        <td>
                        	<div id="titlediv">
                        		<div id="titlewrap">
                        			<input class="widefat" type="text" id="title" name="Autoresponder[nnewsletter][subject]" value="<?php echo esc_attr(stripslashes($Autoresponder -> data -> nnewsletter['subject'])); ?>" id="" />
                        		</div>
                        	</div>
                            <?php echo $Html -> field_error('Autoresponder[nnewsletter_subject]'); ?>
                        	<span class="howto"><?php _e('Subject of the newsletter.', $this -> plugin_name); ?></span>
                        </td>
                    </tr>
                	<tr>
                    	<th><label for=""><?php _e('Newsletter Content', $this -> plugin_name); ?></label>
                    	<?php echo $Html -> help(__('The message/content of the newsletter which will be sent to the subscribers. It is used in conjunction with the theme chosen below and this message/content is placed into the theme where the [wpmlcontent] shortcode was specified.', $this -> plugin_name)); ?></th>
                        <td>
                        	<div id="poststuff">
                                <div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section">                                    
                                    <!-- The Editor -->
									<?php if (version_compare(get_bloginfo('version'), "3.3") >= 0) : ?>
										<?php wp_editor(stripslashes($Autoresponder -> data -> nnewsletter['content']), 'content', array('tabindex' => 2)); ?>
									<?php else : ?>
										<?php the_editor(stripslashes($Autoresponder -> data -> nnewsletter['content']), 'content', 'title', true, 2); ?>
									<?php endif; ?>
                                    
                                    <table id="post-status-info" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td id="wp-word-count">
                                                    <?php _e('Word Count', $this -> plugin_name); ?>:
                                                    <span id="word-count">0</span>
                                                </td>
                                                <td class="autosave-info">
                                                    <span id="autosave" style="display:none;"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <?php echo $Html -> field_error('Autoresponder[nnewsletter_content]'); ?>
                                </div> 
                            </div> 
                        	<span class="howto"><?php _e('Content of the newsletter.', $this -> plugin_name); ?></span>
                            <?php $this -> render('setvariables', false, true, 'admin'); ?>
                        </td>
                    </tr>
                    <tr>
                    	<th><label for="Autoresponder_nnewsletter_theme_id_0"><?php _e('Newsletter Theme', $this -> plugin_name); ?></label>
                    	<?php echo $Html -> help(__('Choose the theme to use for the email that will be sent to the subscribers for this autoresponder. The content above will be put into this theme where the [wpmlcontent] shortcode was specified.', $this -> plugin_name)); ?></th>
                        <td>
                        	<?php if ($themes = $Theme -> select()) : ?>
                            	<div><label><input <?php echo (empty($Autoresponder -> data -> nnewsletter['theme_id'])) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[nnewsletter][theme_id]" id="Autoresponder_nnewsletter_theme_id_0" value="0"> <?php _e('NONE', $this -> plugin_name); ?></label></div>
                            	<div class="scroll-list">
	                            	<?php foreach ($themes as $theme_id => $theme_title) : ?>
	                                	<div><label><input <?php echo (!empty($Autoresponder -> data -> nnewsletter['theme_id']) && $Autoresponder -> data -> nnewsletter['theme_id'] == $theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[nnewsletter][theme_id]" value="<?php echo $theme_id; ?>" id="Autoresponder.nnewsletter.theme_id.<?php echo $theme_id; ?>" /> <?php echo $theme_title; ?></label></div>
	                                <?php endforeach; ?>
	                            </div>
                            <?php else : ?>
                            	<span class="error"><?php _e('No themes found, please add one.', $this -> plugin_name); ?></span>
                            <?php endif; ?>
                            <?php echo $Html -> field_error('Autorseponder[nnewsletter_theme]'); ?>
                            <span class="howto"><?php _e('Choose the theme to use for this new newsletter.', $this -> plugin_name); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Autoresponder.delay"><?php _e('Send Delay', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The send delay is measured in days. How many days after the subscriber has subscribed do you want this autoresponder message to send to the subscriber? You can specify 0 (zero) to have the autoresponder send to the subscriber immediately upon activation.', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php echo $Form -> text('Autoresponder[delay]', array('width' => "45px")); ?>
                    	<?php /*<?php _e('days after subscribing.', $this -> plugin_name); ?>*/ ?>
                    	<?php $delayintervals = array('minutes' => __('Minutes', $this -> plugin_name), 'hours' => __('Hours', $this -> plugin_name), 'days' => __('Days', $this -> plugin_name), 'weeks' => __('Weeks', $this -> plugin_name), 'years' => __('Years', $this -> plugin_name)); ?>
                    	<?php echo $Form -> select('Autoresponder[delayinterval]', $delayintervals); ?>
                    	<?php _e('after subscribing and confirming', $this -> plugin_name); ?>
                    	<span class="howto"><?php _e('Delay before sending this message. Set to 0 to send immediately upon subscribe/confirm.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
                <tr>
                	<th><label for="Autoresponder.status.active"><?php _e('Status', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The status of this autoresponder will determine if it is effective or not. If it is Active, it will be effective and this autoresponder will be sent to subscribers accordingly. If it is Inactive, it will be ignored and will not be used and no messages will be sent from this autoresponder.', $this -> plugin_name)); ?></th>
                    <td>
                    	<label><input <?php echo (empty($Autoresponder -> data -> status) || (!empty($Autoresponder -> data -> status) && $Autoresponder -> data -> status == "active")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[status]" value="active" id="Autoresponder.status.active" /> <?php _e('Active', $this -> plugin_name); ?></label>
                        <label><input <?php echo (!empty($Autoresponder -> data -> status) && $Autoresponder -> data -> status == "inactive") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[status]" value="inactive" id="Autoresponder.status.inactive" /> <?php _e('Inactive', $this -> plugin_name); ?></label>
                    	<span class="howto"><?php _e('Deactivating this autoresponder will prevent it from sending out any messages to subscribers.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    
    	<p class="submit">
        	<?php echo $Form -> submit(__('Save Autoresponder', $this -> plugin_name)); ?>
        	<div class="checkout_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php _e('Continue editing', $this -> plugin_name); ?></label>
			</div>
        </p>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="Autoresponder[title]"]').Watermark('<?php echo addslashes(__('Enter autoresponder title here', $this -> plugin_name)); ?>');
	jQuery('#Autoresponder_nnewsletter_subject').Watermark('<?php echo addslashes(__('Enter email subject here', $this -> plugin_name)); ?>');
});
</script>