<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<?php if (empty($_POST['sendtype']) || $_POST['sendtype'] == "send") : ?>
		<h2 id="pageheading"><?php _e('Sending Newsletter', $this -> plugin_name); ?></h2>
	<?php else : ?>
		<h2 id="pageheading"><?php _e('Queuing Newsletter', $this -> plugin_name); ?></h2>
	<?php endif; ?>
	
	<?php if (!empty($subscribers)) : ?>
		<?php if (!empty($_POST['sendtype']) && $_POST['sendtype'] == "queue") : ?>
			<p>
				<?php _e('This newsletter will be scheduled for:', $this -> plugin_name); ?> <strong><?php echo $_POST['senddate']; ?></strong>
			</p>
		<?php else : ?>
			<p>
				<?php _e('This newsletter will be sent immediately, as fast as possible.', $this -> plugin_name); ?><br/>
				<?php _e('Please consult your hosting provider to find out if you have hourly/daily/weekly/monthly email sending limits.', $this -> plugin_name); ?><br/>
				<?php _e('In case there are limits, rather tick the "Queue this newsletter instead" checkbox below to queue and throttle accordingly.', $this -> plugin_name); ?><br/>
				<?php _e('Check Newsletters > Configuration > Email Scheduling for queue/schedule/throttling settings.', $this -> plugin_name); ?>
			</p>
		<?php endif; ?>
	
		<p>
			<span id="sendajaxcount"><strong><span id="sendajaxcountinside" class="newsletters_success">0</span></strong></span> <span id="sendajaxfailedcount">(<strong><span id="sendajaxfailedcountinside" class="newsletters_error">0</span></strong> failed)</span> <?php _e('out of', $this -> plugin_name); ?> <strong><?php echo count($subscribers); ?></strong>
			<?php if (empty($_POST['sendtype']) || $_POST['sendtype'] == "send") : ?>
				<span id="havebeenqueued"><?php _e('emails have been sent out.', $this -> plugin_name); ?></span>
			<?php else : ?>
				<span id="havebeenqueued"><?php _e('emails have been queued.', $this -> plugin_name); ?></span>
			<?php endif; ?>
		</p>
		
		<div id="sendprogressbar"></div>
		
		<style type="text/css">
		.ui-progressbar-value {
			background-image: url('<?php echo $this -> url(); ?>/images/pbar-ani.gif') !important;
		}
		</style>
		
		<p class="submit">
			<a id="cancelbutton" href="" onclick="cancelsending(); return false;" disabled="disabled" style="display:none;" class="button button-secondary"><?php _e('Stop', $this -> plugin_name); ?></a>
			<?php if (empty($_POST['sendtype']) || $_POST['sendtype'] == "send") : ?>
				<a id="startsending" href="" onclick="startsending(); return false;" disabled="disabled" class="button button-primary"><?php _e('Reading data, please wait', $this -> plugin_name); ?></a>
				<span id="queuecheckboxspan"><label><input onclick="queuecheckbox();" type="checkbox" name="queuecheckbox" value="1" id="queuecheckbox" /> <?php _e('Queue this newsletter instead.', $this -> plugin_name); ?></label></span>
			<?php else : ?>
				<a id="startsending" href="" onclick="startsending(); return false;" disabled="disabled" class="button button-primary"><?php _e('Reading data, please wait', $this -> plugin_name); ?></a>
			<?php endif; ?>
		</p>
		
		<h3 id="successfullheader" style="display:none;"><?php if (empty($_POST['sendtype']) || $_POST['sendtype'] == "send") { _e('Successfully Sent', $this -> plugin_name); } else { _e('Successfully Queued', $this -> plugin_name); }; ?></h3>
		<div id="sendajaxsuccessrecords" class="scroll-list" style="display:none;"><!-- successful records --></div>
		
		<h3 id="failedheader" style="display:none;"><?php if (empty($_POST['sendtype']) || $_POST['sendtype'] == "send") { _e('Failed Sending', $this -> plugin_name); } else { _e('Failed Queuing', $this -> plugin_name); }; ?></h3>
		<div id="sendajaxfailedrecords" class="scroll-list" style="display:none;"><!-- failed records --></div>
		
		<h3><?php _e('Email Preview', $this -> plugin_name); ?></h3>
		
		<?php if (!empty($attachments)) : ?>
			<h4><?php _e('Attachments', $this -> plugin_name); ?></h4>
            <div id="currentattachments">
               <ul style="margin:0; padding:0;"> 
                    <?php foreach ($attachments as $attachment) : ?>
                    	<li class="<?php echo $this -> pre; ?>attachment">
                        	<?php echo $Html -> attachment_link($attachment, false); ?>
                        </li>    
                    <?php endforeach; ?>
               </ul>
            </div>
        <?php endif; ?>
		
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo admin_url('admin-ajax.php'); ?>?action=<?php echo $this -> pre; ?>history_iframe&id=<?php echo $history_id; ?>&rand=<?php echo rand(1,999); ?>" id="historypreview<?php echo $history_id; ?>"></iframe>
	
		<div id="sendajaxbox" style="display:none;"><?php
			
		$i = 1;
		foreach ($subscribers as $subscriber) {
			echo maybe_serialize($subscriber);
			if ($i < count($subscribers)) { echo '<|>'; }
			$i++;
		}
		
		?></div>
		
		<script type="text/javascript">		
		jQuery(document).ready(function() {
			warnMessage = "<?php _e('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name); ?>";

			window.onbeforeunload = function () {
			    if (warnMessage != false) { return warnMessage; }
			}
		
			requestArray = new Array();
			sendtype = "<?php echo $_POST['sendtype']; ?>";
			
			settexts();
			
			jQuery('#startsending').removeAttr('disabled').text(startsendingtext);
			cancensend = "N";
		});
		
		function settexts() {		
			if (sendtype == "send") { 
				startsendingnumber = 1000;
				startsendingtext = "<?php echo addslashes(__('Start Sending', $this -> plugin_name)); ?>"; 
				sendingnowtext = "<?php echo addslashes(__('Sending Now', $this -> plugin_name)); ?>";
				resumesendingtext = "<?php echo addslashes(__('Resume Sending', $this -> plugin_name)); ?>";
				jQuery('#successfullheader').text('<?php echo addslashes(__('Successfully Sent', $this -> plugin_name)); ?>');
				jQuery('#failedheader').text('<?php echo addslashes(__('Failed Sending', $this -> plugin_name)); ?>');
			} else { 
				startsendingnumber = 2000;
				startsendingtext = "<?php echo addslashes(__('Start Queuing', $this -> plugin_name)); ?>"; 
				sendingnowtext = "<?php echo addslashes(__('Queuing Now', $this -> plugin_name)); ?>";
				resumesendingtext = "<?php echo addslashes(__('Resume Queuing', $this -> plugin_name)); ?>";
				jQuery('#successfullheader').text('<?php echo addslashes(__('Successfully Queued', $this -> plugin_name)); ?>');
				jQuery('#failedheader').text('<?php echo addslashes(__('Failed Queuing', $this -> plugin_name)); ?>');
			}
		}
		
		function queuecheckbox() {
			if (jQuery('#queuecheckbox').attr('checked')) {
				sendtype = "queue";
				settexts();
				jQuery('#pageheading').text('<?php echo addslashes(__('Queuing Newsletter', $this -> plugin_name)); ?>');
				jQuery('#startsending').text(startsendingtext);
				jQuery('#havebeenqueued').html('<?php echo addslashes(__('emails have been queued.', $this -> plugin_name)); ?>');
			} else {
				sendtype = "send";
				settexts();
				jQuery('#pageheading').text('<?php echo addslashes(__('Sending Newsletter', $this -> plugin_name)); ?>');
				jQuery('#startsending').text(startsendingtext);
				jQuery('#havebeenqueued').html('<?php echo addslashes(__('emails have been sent out.', $this -> plugin_name)); ?>');
			}
		}
		
		function cancelsending() {
			cancelsend = "Y";
			jQuery('#cancelbutton').attr("value", "<?php echo addslashes(__('Cancelled', $this -> plugin_name)); ?>").attr('disabled', 'disabled');
			jQuery('#startsending').removeAttr('disabled').attr('onclick', 'resumesending(); return false;').text(resumesendingtext);
			
			for (var r = 0; r < requestArray.length; r++) {
				requestArray[r].abort();
			}
			
			requestArray = new Array();
		}
		
		function resumesending() {
			cancelsend = "N";
			jQuery('#startsending').attr('disabled', 'disabled').text(sendingnowtext);
			jQuery('#cancelbutton').removeAttr('disabled');
			
			var newsendingnumber = (startsendingnumber - completed);
			requests = (completed - 1);
		
			for (i = 0; i < newsendingnumber; i++) {			
				if (i < subscribercount) {
					if (sendtype == "send") {
						executemail(subscribers[(completed + i)]);
					} else {
						queuemail(subscribers[(completed + i)]);
					}
				}
			}
		}
		
		function startsending() {
			jQuery('#queuecheckboxspan').hide();
			jQuery('#startsending').attr('disabled', 'disabled');
			jQuery('#cancelbutton').removeAttr('disabled').show();
			jQuery('#startsending').text(sendingnowtext);
			cancelsend = "N";
			text = jQuery('#sendajaxbox').text();
			subscribercount = '<?php echo count($subscribers); ?>';
			subscribers = text.split('<|>');
			requests = 0;
			completed = 0;
			sent = 0;
			failed = 0;
			
			if (subscribercount < startsendingnumber) {
				startsendingnumber = subscribercount;
			}
			
			jQuery('#sendprogressbar').progressbar({value:0});
			
			for (i = 0; i < startsendingnumber; i++) {			
				if (i < subscribercount) {
					if (sendtype == "send") {
						executemail(subscribers[i]);
					} else {
						queuemail(subscribers[i]);
					}
				}
			}
		}
		
		function executemail(subscriber) {			
			if (cancelsend == "Y" || completed >= subscribercount) {
				return;
			}
			
			requests++;
			
			requestArray.push(jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>executemail', {
				subscriber:subscriber,
				attachments:'<?php echo maybe_serialize($attachments); ?>',
				history_id:'<?php echo $history_id; ?>',
				post_id:'<?php echo $post_id; ?>',
				theme_id:'<?php echo $theme_id; ?>'
			}, function(response) {
				var data = response.split('<|>');
				var success = data[0];
				var email = data[1];
				var message = data[2];
				
				if (success == "Y") {
					sent++;
					if ((sent + failed) <= subscribercount) { 
						jQuery('#sendajaxcountinside').text(sent); 
						jQuery('#sendajaxsuccessrecords').prepend(email + '<br/>').fadeIn().prev().fadeIn();
					}
				} else {
					failed++;
					if ((sent + failed) <= subscribercount) { 
						jQuery('#sendajaxfailedcountinside').text(failed); 
						jQuery('#sendajaxfailedrecords').prepend('<b>' + email + '</b> - ' + message + '<br/>').fadeIn().prev().fadeIn();
					}
				}
				
				completed++;
				var value = (completed * 100) / subscribercount;
				jQuery("#sendprogressbar").progressbar("value", value);
			}).success(function() { 			
				if (completed == subscribercount) {
					finished();
				} else {
					if (requests < subscribercount) {
						executemail(subscribers[(requests + 1)]);
					}
				}
			}));
		}
		
		function finished() {
			jQuery('#cancelbutton').hide();
			warnMessage = false;
			
			if (sendtype == "send") {
				jQuery('#startsending').text('<?php echo addslashes(__('Continue to History', $this -> plugin_name)); ?>').removeAttr('disabled').removeAttr('onclick').attr("href", "?page=<?php echo $this -> sections -> history; ?>&method=view&id=<?php echo $history_id; ?>");
			} else {
				jQuery('#startsending').text('<?php echo addslashes(__('Continue to Queue', $this -> plugin_name)); ?>').removeAttr('disabled').removeAttr('onclick').attr("href", "?page=<?php echo $this -> sections -> queue; ?>");
			}
				
			jQuery('#sendprogressbar').progressbar("option", "disabled", true);
			cancelsend = "Y";
		}
		
		function queuemail(subscriber) {		
			if (cancelsend == "Y" || completed >= subscribercount) {
				return;
			}
			
			requests++;
			
			requestArray.push(jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>queuemail', {
				subscriber:subscriber,
				attachments:'<?php echo maybe_serialize($attachments); ?>',
				history_id:'<?php echo $history_id; ?>',
				post_id:'<?php echo $post_id; ?>',
				theme_id:'<?php echo $theme_id; ?>',
				senddate:'<?php echo $_POST['senddate']; ?>'
			}, function(response) {
				var data = response.split('<|>');
				var success = data[0];
				var email = data[1];
				var message = data[2];
				
				if (success == "Y") {
					sent++;
					if ((sent + failed) <= subscribercount) { 
						jQuery('#sendajaxcountinside').text(sent); 
						jQuery('#sendajaxsuccessrecords').prepend(email + '<br/>').fadeIn().prev().fadeIn();
					}
				} else {
					failed++;
					if ((sent + failed) <= subscribercount) { 
						jQuery('#sendajaxfailedcountinside').text(failed); 
						jQuery('#sendajaxfailedrecords').prepend(email + ' - ' + message + '<br/>').fadeIn().prev().fadeIn();
					}
				}
				
				completed++;
				var value = (completed * 100) / subscribercount;
				jQuery("#sendprogressbar").progressbar("value", value);
			}).success(function() { 			
				if (completed == subscribercount) {
					finished();
				} else {
					if (requests < subscribercount) {
						queuemail(subscribers[(requests + 1)]);
					}
				}
			}));
		}
		</script>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No subscribers are available, please try again.', $this -> plugin_name); ?></p>
		<p>
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><?php _e('&laquo; Back', $this -> plugin_name); ?></a>
		</p>
	<?php endif; ?>
</div>