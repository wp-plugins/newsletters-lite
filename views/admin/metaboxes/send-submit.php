<!-- Submit box with some settings -->

<?php
	
$previewemail = (empty($_POST['previewemail'])) ? $this -> get_option('adminemail') : $_POST['previewemail'];
	
?>

<div class="submitbox" id="submitpost">
	<div id="minor-publishing">
		<div id="minor-publishing-actions">
			<div id="save-action">
				<input id="save-post" type="submit" name="draft" value="<?php _e('Save Draft', $this -> plugin_name); ?>" class="button button-highlighted" />
			</div>
			<div id="preview-action">
				<input type="button" name="previewemail_button" id="previewemail_button" class="button" value="<?php echo apply_filters('newsletters_admin_createnewsletter_sendpreview_text', __('Send Preview', $this -> plugin_name)); ?>" />
			</div>
			<br class="clear" />
		</div>
		<div id="misc-publishing-actions">
			<div class="misc-pub-section" id="previewemail_div" style="display:none;">
				<div class="form-field">
					<label for="previewemail"><?php _e('Send To:', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('Specify the email address(es) to send this newsletter as a preview to. For multiple emails, separate them with a comma (,)', $this -> plugin_name)); ?>
					<input type="text" name="previewemail" value="<?php echo esc_attr(stripslashes($previewemail)); ?>" id="previewemail" />
				</div>
				<p>
					<input class="button button-primary" type="submit" name="preview" value="<?php _e('Send', $this -> plugin_name); ?>" />
					<a href="" id="cancelpreviewemail"><?php _e('Cancel', $this -> plugin_name); ?></a>
				</p>
			</div>
			<div class="misc-pub-section sendfromwrapper">
				<input type="hidden" id="from" name="from" value="<?php echo (!empty($_POST['from'])) ? $_POST['from'] : ''; ?>" />
				<input type="hidden" id="fromname" name="fromname" value="<?php echo (!empty($_POST['fromname'])) ? $_POST['fromname'] : ''; ?>" />
			
				<span id="sendfrom">
					<?php /*<span id="sendfrom-image"></span>*/ ?>
					<i class="fa fa-user fa-fw pull-left"></i> <?php _e('Send from', $this -> plugin_name); ?>
					<span id="sendfrom-edit"><a id="sendfrom-edit-link" href="" onclick="jQuery('#sendfromdiv').show('slow'); jQuery('#smtpfromname').focus(); jQuery(this).hide(); return false;"><?php _e('Edit', $this -> plugin_name); ?></a></span>
					<?php echo $Html -> help(__('Change the From Name and From Address that this email is being sent from as it will appear in the email/webmail clients of the recipients.', $this -> plugin_name)); ?>
				</span>
				<span id="sendfrom-value">
					<abbr title="<?php echo (empty($_POST['from'])) ? $this -> get_option('smtpfrom') : $_POST['from']; ?>"><?php echo (empty($_POST['fromname'])) ? $this -> get_option('smtpfromname') : $_POST['fromname']; ?></abbr>
				</span>
				
				<div id="sendfromdiv" class="hide-if-js" style="display:none;">
					<div class="form-field">
						<label for="smtpfromname"><?php _e('From Name:', $this -> plugin_name); ?></label>
						<input type="text" name="smtpfromname" value="<?php echo (empty($_POST['fromname'])) ? $this -> get_option('smtpfromname') : $_POST['fromname']; ?>" id="smtpfromname" />
					</div>
					<div class="form-field">
						<label for="smtpfrom"><?php _e('From Email:', $this -> plugin_name); ?></label>
						<input type="text" name="smtpfrom" value="<?php echo (empty($_POST['from'])) ? $this -> get_option('smtpfrom') : $_POST['from']; ?>" id="smtpfrom" />
					</div>
					<p>
						<input type="button" name="changesendfrom" id="changesendfrom" class="button button-secondary" value="<?php _e('Ok', $this -> plugin_name); ?>" />
						<a href="" id="cancelsendfrom"><?php _e('Cancel', $this -> plugin_name); ?></a>
					</p>
				</div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#cancelpreviewemail').click(function() { jQuery('#previewemail_div').hide('slow'); return false; });
					jQuery('#previewemail_button').click(function() { jQuery('#previewemail_div').show('slow'); });
					
					jQuery('#changesendfrom').click(function() { changesendfrom(); });
					jQuery('#cancelsendfrom').click(function() { jQuery('#sendfromdiv').hide('slow'); jQuery('#sendfrom-edit-link').show(); return false; });			
					
					jQuery('#sendfromdiv input').keypress(function(e) {
						var code = (e.keyCode ? e.keyCode : e.which);
						if (code == 13) {
							changesendfrom();
							return false;
						}
					});
				});
				
				function changesendfrom() {
					var fromname = jQuery('#smtpfromname').val();
					var fromemail = jQuery('#smtpfrom').val();
					jQuery('#from').val(fromemail);
					jQuery('#fromname').val(fromname);
					jQuery('#sendfrom-value').html('<abbr title="' + fromemail + '">' + fromname + '</abbr>');
					jQuery('#sendfromdiv').hide('slow');
					jQuery('#sendfrom-edit-link').show();
				}
				</script>
			</div>
			
			<?php if (apply_filters('newsletters_admin_createnewsletter_recurringsettings', true)) : ?>
				<div class="misc-pub-section">
					<input type="hidden" name="sendrecurring" id="sendrecurringfield" value="<?php echo (!empty($_POST['sendrecurring']) && $_POST['sendrecurring'] == "Y") ? 'Y' : ''; ?>" />
				
					<span id="sendrecurring">
						<?php /*<span id="sendrecurring-image"></span>*/ ?>
						<i class="fa fa-retweet fa-fw pull-left"></i>
						<?php if (!empty($_POST['sendrecurring']) && $_POST['sendrecurring'] == "Y") : ?>
							<span id="sendrecurring-value">
								<?php echo sprintf(__('Send every %s %s', $this -> plugin_name), $_POST['sendrecurringvalue'], $_POST['sendrecurringinterval'], $_POST['sendrecurringlimit']); ?>
								<?php if (!empty($_POST['sendrecurringlimit'])) : ?><?php echo sprintf(__(', %s times', $this -> plugin_name), $_POST['sendrecurringlimit']); ?><?php endif; ?>
							</span>
						<?php else : ?>
							<span id="sendrecurring-value"><?php _e('Send Once', $this -> plugin_name); ?></span>
						<?php endif; ?>
						<span id="sendrecurring-edit"><a id="sendrecurring-edit-link" href="" onclick="jQuery('#sendrecurringdiv').show('slow'); jQuery(this).hide(); return false;"><?php _e('Edit', $this -> plugin_name); ?></a></span>
						<?php echo $Html -> help(__('This newsletter can be automatically repeated at a specified interval, starting on a specific date and the repeat can also be limited. Click "Edit" to configure this as a recurring newsletter. To cancel, empty all the fields and click "Ok".', $this -> plugin_name)); ?>
					</span>
					
					<div id="sendrecurringdiv" class="hide-if-js" style="display:none;">
						<div class="form-field">
							<?php _e('Every', $this -> plugin_name); ?>
							<input type="text" name="sendrecurringvalue" value="<?php echo esc_attr(stripslashes($_POST['sendrecurringvalue'])); ?>" id="sendrecurringvalue" class="widefat" style="width:45px;" /> 
							<select name="sendrecurringinterval" id="sendrecurringinterval">
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "minutes") ? 'selected="selected"' : ''; ?> value="minutes"><?php _e('Minutes', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "hours") ? 'selected="selected"' : ''; ?> value="hours"><?php _e('Hours', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "days") ? 'selected="selected"' : ''; ?> value="days"><?php _e('Days', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "weeks") ? 'selected="selected"' : ''; ?> value="weeks"><?php _e('Weeks', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "months") ? 'selected="selected"' : ''; ?> value="months"><?php _e('Months', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "years") ? 'selected="selected"' : ''; ?> value="years"><?php _e('Years', $this -> plugin_name); ?></option>
							</select>
							<?php echo $Html -> help(__('Choose the interval at which this newsletter should be sent again. All data of the newsletter such as the list(s), content, template, etc. will be reused as configured.', $this -> plugin_name)); ?>
						</div>
						<div class="form-field">
							<?php _e('Starting', $this -> plugin_name); ?>
							<?php $sendrecurringdate = (empty($_POST['sendrecurringdate'])) ? $Html -> gen_date("Y-m-d H:i:s", time()) : $_POST['sendrecurringdate']; ?>
							<input type="text" name="sendrecurringdate" value="<?php echo $sendrecurringdate; ?>" id="sendrecurringdate" class="widefat" style="width:140px;" />
							<?php echo $Html -> help(__('Specify a starting date and time for the first recurring instance to run. In the format YYYY-MM-DD HH:MM:SS', $this -> plugin_name)); ?>
						</div>
						<div class="form-field">
							<?php echo sprintf(__('Repeat %s times', $this -> plugin_name), '<input type="text" name="sendrecurringlimit" value="' . esc_attr(stripslashes($_POST['sendrecurringlimit'])) . '" id="sendrecurringlimit" class="widefat" style="width:45px;" />'); ?>
							<?php echo $Html -> help(__('How many times should this newsletter be sent? Leave this field empty for unlimited/inifinite, else specify a number.', $this -> plugin_name)); ?>
						</div>
						<p>
							<input type="button" name="changesendrecurring" id="changesendrecurring" class="button button-secondary" value="<?php _e('Ok', $this -> plugin_name); ?>" />
							<a href="" id="cancelsendrecurring"><?php _e('Cancel', $this -> plugin_name); ?></a>
						</p>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {					
						jQuery('#changesendrecurring').click(function() { changesendrecurring(); });
						jQuery('#cancelsendrecurring').click(function() { jQuery('#sendrecurringdiv').hide('slow'); jQuery('#sendrecurring-edit-link').show(); return false; });			
						
						jQuery('#sendrecurringdiv input').keypress(function(e) {
							var code = (e.keyCode ? e.keyCode : e.which);
							if (code == 13) {
								changesendrecurring();
								return false;
							}
						});
					});
					
					function changesendrecurring() {
						var sendrecurringvalue = jQuery('#sendrecurringvalue').val();
						var sendrecurringinterval = jQuery('#sendrecurringinterval').val();
						var sendrecurringlimit = jQuery('#sendrecurringlimit').val();
						
						if (sendrecurringvalue != "" && sendrecurringinterval != "") {
							sendrecurringhtml = 'Send ';
							sendrecurringhtml += ' every ' + sendrecurringvalue + ' ' + sendrecurringinterval + '';
							if (sendrecurringlimit != "") { sendrecurringhtml += ' , ' + sendrecurringlimit + ' times'; }
							jQuery('#sendrecurring-value').html(sendrecurringhtml);
							jQuery('#sendrecurringfield').val('Y');
						} else {
							jQuery('#sendrecurringfield').val('');
						}
						
						jQuery('#sendrecurringdiv').hide('slow');
						jQuery('#sendrecurring-edit-link').show();
					}
					</script>
				</div>
			<?php endif; ?>
				
    		<div class="misc-pub-section curtime misc-pub-section-last">
    			<?php if ($this -> get_option('sendingprogress') == "Y") : ?>
            		<i class="fa fa-clock-o fa-fw"></i> <span id="timestamp"><?php _e('Send immediately', $this -> plugin_name); ?></span>
            	<?php else : ?>
            		<i class="fa fa-clock-o fa-fw"></i> <span id="timestamp"><?php _e('Queue immediately', $this -> plugin_name); ?></span>
            	<?php endif; ?>
            	
            	<style type="text/css">
	            #timestamp:before {
		            content: "" !important;
	            }
	            </style>
            	
            	<a href="" onclick="jQuery('#timestampdiv').show('slow'); jQuery(this).hide(); return false;" class="edit-timestamp hide-if-no-js" style="display:inline;"><?php _e('Edit', $this -> plugin_name); ?></a>
            	
            	<?php echo $Html -> help(__('You can choose to send this newsletter immediately or you can click the "Edit" link to change the date/time to a future date/time at which it will be sent.', $this -> plugin_name)); ?>
            	
				<div id="timestampdiv" class="hide-if-js" style="display:none;">
					<p class="howto"><?php echo sprintf(__('Current time is %s', $this -> plugin_name), '<strong>' . $Html -> gen_date("Y-m-d H:i:s", time()) . '</strong>'); ?></p>
					<?php touch_time(0, 0, 0, 0); ?>
					<?php $senddate = (empty($_POST['senddate']) || strtotime($_POST['senddate']) <= time()) ? $Html -> gen_date("Y-m-d H:i:s", time()) : $_POST['senddate']; ?>
					<input type="hidden" name="sendtype" id="sendtype" value="<?php echo ($this -> get_option('sendingprogress') == "Y") ? 'send' : 'queue'; ?>" />
					<input type="hidden" name="senddate" id="senddate" value="<?php echo $senddate; ?>" />
					<input type="hidden" name="scheduled" id="scheduled" value="N" />
				</div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {				
					alwaysqueue = <?php if ($this -> get_option('sendingprogress') == "N") : ?>true<?php else : ?>false<?php endif; ?>;
					jQuery('.save-timestamp').click(update_timestamp);
					
					jQuery('.cancel-timestamp').click(function() { 
						jQuery('#timestampdiv').hide('slow'); 
						jQuery('.edit-timestamp').show(); 
					});
					
					<?php if (!empty($senddate) && strtotime($senddate) > time()) : ?>
						jQuery('#scheduled').val("Y");
						jQuery('#aa').val('<?php echo $Html -> gen_date("Y", strtotime($senddate)); ?>');
						jQuery('#mm').val('<?php echo $Html -> gen_date("m", strtotime($senddate)); ?>');
						jQuery('#jj').val('<?php echo $Html -> gen_date("d", strtotime($senddate)); ?>');
						jQuery('#hh').val('<?php echo $Html -> gen_date("H", strtotime($senddate)); ?>');
						jQuery('#mn').val('<?php echo $Html -> gen_date("i", strtotime($senddate)); ?>');
						update_timestamp();
					<?php endif; ?>
				});
				
				function update_timestamp() { 
					var date = new Date();
					var year = date.getFullYear();
					var month = ("0" + (date.getMonth() + 1)).slice(-2);
					var day = ("0" + date.getDate()).slice(-2);
					var hours = ("0" + date.getHours()).slice(-2);
					var minutes = ("0" + date.getMinutes()).slice(-2);
					var today = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
					
					var year = jQuery('#aa').val();
					var month = (jQuery('#mm').val() - 1);
					var day = jQuery('#jj').val();
					var hours = jQuery('#hh').val();
					var minutes = jQuery('#mn').val();						
					var senddate = new Date(year, month, day, hours, minutes, date.getSeconds());
					
					if (senddate.getTime() > date.getTime()) {
						jQuery('#timestamp').html('<?php _e('Schedule for:', $this -> plugin_name); ?> <strong>' + year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes + '</strong>');
						jQuery('#sendbutton').attr("name", "queue").attr("value", "<?php echo addslashes(__('Schedule Newsletter', $this -> plugin_name)); ?>");
						jQuery('#sendtype').attr("value", "schedule");
						jQuery('#scheduled').attr("value", "Y");
					} else if (alwaysqueue == true) {
						jQuery('#timestamp').html('<?php _e('Queue immediately', $this -> plugin_name); ?>');
						jQuery('#sendbutton').attr("name", "queue").attr("value", "<?php echo addslashes(__('Queue Newsletter', $this -> plugin_name)); ?>");
						jQuery('#sendtype').attr("value", "queue");
						jQuery('#scheduled').attr("value", "N");
					} else {
						jQuery('#timestamp').html('<?php _e('Send immediately', $this -> plugin_name); ?>');
						jQuery('#sendbutton').attr("name", "send").attr("value", "<?php echo addslashes(__('Send Newsletter', $this -> plugin_name)); ?>");
						jQuery('#sendtype').attr("value", "send");
						jQuery('#scheduled').attr("value", "N");
					}
					
					jQuery('#senddate').attr("value", year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes);
					jQuery('#timestampdiv').hide('slow');
					jQuery('.edit-timestamp').show();
				}
				</script>
            </div>
        </div>
		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">
		<?php if (!empty($_POST['ishistory'])) : ?>
			<div id="delete-action">
				<a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=delete&amp;id=<?php echo $_POST['ishistory']; ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this newsletter from your history?', $this -> plugin_name); ?>')) { return false; }" title="<?php _e('Remove this newsletter from your history', $this -> plugin_name); ?>" class="submitdelete deletion"><?php _e('Delete Email', $this -> plugin_name); ?></a>
				<?php echo $Html -> help(__('Since this is a saved sent/draft email, you can click this "Delete Email" link to permanently delete it from your history. Please note that this is undoable.', $this -> plugin_name)); ?>
			</div>
		<?php endif; ?>
		<div id="publishing-action">
			<?php $sendbutton = ($this -> get_option('sendingprogress') == "N") ? __('Queue Newsletter', $this -> plugin_name) : __('Send Newsletter', $this -> plugin_name); ?>
			<input class="button button-primary button-large" type="submit" name="send" id="sendbutton" disabled="disabled" value="<?php echo $sendbutton; ?>" />
		</div>
		<br class="clear" />
		<?php
			
		$sendingprogress = $this -> get_option('sendingprogress');
		
		?>
		<div class="publishing-action-inside">
			<label><input <?php echo (!empty($sendingprogress) && $sendingprogress == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="sendingprogress" value="1" id="sendingprogress" /> <?php _e('Use progress bar to queue/send', $this -> plugin_name); ?></label>
		</div>
	</div>
</div>