<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Importing Subscribers', $this -> plugin_name); ?></h2>
	
	<?php if (!empty($subscribers)) : ?>		
		<p class="newsletters_importajaxcount"><span id="importajaxcount"><strong><span id="importajaxcountinside" class="newsletters_success">0</span></strong></span> <span id="importajaxfailedcount">(<strong><span id="importajaxfailedcountinside" class="newsletters_error">0</span></strong> failed)</span> <?php _e('out of', $this -> plugin_name); ?> <strong><?php echo count($subscribers); ?></strong> <?php _e('subscribers have been imported.', $this -> plugin_name); ?></p>
		
		<div id="importprogressbar"></div>
		
		<p class="submit">
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><i class="fa fa-arrow-left"></i> <?php _e('Back', $this -> plugin_name); ?></a>
			<a href="" onclick="cancelimporting(); return false;" id="cancelimporting" disabled="disabled" style="display:none;" class="button-secondary"><i class="fa fa-pause"></i> <?php _e('Pause', $this -> plugin_name); ?></a>
			<a href="" onclick="startimporting(); return false;" id="startimporting" disabled="disabled" class="button-primary"><i class="fa fa-refresh fa-spin"></i> <?php _e('Reading data, please wait', $this -> plugin_name); ?></a>
			<span id="import_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
			<span id="importmore" style="display:none;"><a href="?page=<?php echo $this -> sections -> importexport; ?>" id="" class="button-secondary"><?php _e('Import More', $this -> plugin_name); ?></a></span>
		</p>
		
		<div id="confirmation_subject" style="display:none;"><?php echo $confirmation_subject; ?></div>
		<div id="confirmation_email" style="display:none;"><?php echo $confirmation_email; ?></div>
		
		<h3 style="display:none;"><?php _e('Subscribers Imported', $this -> plugin_name); ?></h3>
		<div id="importajaxsuccessrecords" class="scroll-list" style="display:none;"><!-- successful records --></div>
		
		<h3 style="display:none;"><?php _e('Failed Subscribers', $this -> plugin_name); ?></h3>
		<div id="importajaxfailedrecords" class="scroll-list" style="display:none;"><!-- failed records --></div>
		
		<div id="importajaxresponse"><!-- response here --></div>
		
		<script type="text/javascript">
		var allsubscribers = [];
			
		jQuery(document).ready(function() {		
			<?php if (!empty($subscribers)) : ?>
				<?php foreach ($subscribers as $subscriber) : ?>
					allsubscribers.push(<?php echo json_encode($subscriber); ?>);
				<?php endforeach; ?>
			<?php endif; ?>
			
			requestArray = new Array();
			cancelimport = "N";
			importingnumber = 1000;
			confirmation_subject = jQuery('#confirmation_subject').html();
			confirmation_email = jQuery('#confirmation_email').html(); 
			import_preventbu = "<?php echo (!empty($_POST['import_preventbu'])) ? 'Y' : 'N'; ?>";
			import_overwrite = "<?php echo (!empty($_POST['import_overwrite'])) ? 'Y' : 'N'; ?>";
			jQuery('#startimporting').prop('disabled', false).html('<i class="fa fa-play"></i> <?php echo addslashes(__("Start Importing", $this -> plugin_name)); ?>');
		});
		
		function cancelimporting() {
			cancelimport = "Y";
			jQuery('#cancelimporting').prop('disabled', true);
			jQuery('#startimporting').prop('disabled', false).attr('onclick', 'resumeimporting(); return false;').html('<i class="fa fa-play"></i> <?php echo addslashes(__('Resume Importing', $this -> plugin_name)); ?>');
			jQuery('#import_loading').hide();
			
			for (var r = 0; r < requestArray.length; r++) {
				requestArray[r].abort();
			}
		}
		
		function startimporting() {
			jQuery('#cancelimporting').prop('disabled', false).show();
			jQuery('#startimporting').prop('disabled', true).html('<i class="fa fa-play"></i> <?php echo addslashes(__('Importing Now', $this -> plugin_name)); ?>');
			jQuery('#import_loading').show();

			subscribercount = allsubscribers.length;
			subscribers = allsubscribers;
			completed = 0;
			cancelimport = "N";
			requests = 0;
			imported = 0;
			failed = 0;
			
			if (subscribercount < importingnumber) {
				importingnumber = subscribercount;
			}
			
			jQuery('#importprogressbar').progressbar({value:0});
			
			for (i = 0; i < importingnumber; i++) {
				if (i < subscribercount) {
					importsubscriber(subscribers[i]);
				}
			}
		}
		
		function resumeimporting() {
			cancelimport = "N";
			jQuery('#startimporting').prop('disabled', true).html('<i class="fa fa-play"></i> <?php echo addslashes(__('Importing Now', $this -> plugin_name)); ?>');
			jQuery('#cancelimporting').prop('disabled', false);
			jQuery('#import_loading').show();
			
			var newimportingnumber = (importingnumber - completed);
			requests = (completed - 1);
		
			for (i = 0; i < newimportingnumber; i++) {			
				if (i < subscribercount) {				
					importsubscriber(subscribers[(completed + i)]);
				}
			}
		}
		
		function importsubscriber(subscriber) {
			if (requests >= subscribercount || cancelimport == "Y") { return; }
		
			requests++;
		
			requestArray.push(jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>importsubscribers', {subscriber:subscriber, import_preventbu:import_preventbu, import_overwrite:import_overwrite, confirmation_subject:confirmation_subject, confirmation_email:confirmation_email}, function(response) {					
				var data = response.split('<|>');
				var success = data[0];
				var email = data[1];
				var message = data[2];
											
				if (success == "Y") {
					imported++;
					
					if ((imported + failed) <= subscribercount) {
						jQuery('#importajaxcountinside').text(imported);
						jQuery('#importajaxsuccessrecords').prepend('<div class="ui-state-highlight ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-check"></i> ' + email + '</p></div>').fadeIn().prev().fadeIn();
					}
				} else {
					failed++;
					
					if ((imported + failed) <= subscribercount) {
						jQuery('#importajaxfailedcountinside').text(failed);
						jQuery('#importajaxfailedrecords').prepend('<div class="alert alert-danger" style="margin-bottom:3px;"><p><i class="fa fa-exclamation-triangle"></i> ' + email + ' - ' + message + '</p></div>').fadeIn().prev().fadeIn();
					}
				}
				
				completed++;
				var value = (completed * 100) / subscribercount;
				jQuery("#importprogressbar").progressbar("value", value);
			}).success(function() { 			
				if (completed == subscribercount) {
					jQuery('#cancelimporting').hide();
					warnMessage = null;
					jQuery('#startimporting').html('<?php echo addslashes(__('Continue to Subscribers', $this -> plugin_name)); ?> <i class="fa fa-arrow-right"></i>').prop('disabled', false).removeAttr('onclick').attr("href", "?page=<?php echo $this -> sections -> subscribers; ?>");
					jQuery('#importmore').show();
					jQuery('#import_loading').hide();
				} else {
					importsubscriber(subscribers[(requests + 1)]); 
				}
			}));
		}
		
		var warnMessage = "<?php _e('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name); ?>";

		jQuery(document).ready(function() {
		    window.onbeforeunload = function () {
		        if (warnMessage != null) return warnMessage;
		    }
		});
		</script>
	<?php else : ?>
		<p class="newsletters_error"><?php _e('No subscribers are available for import, please try again.', $this -> plugin_name); ?></p>
		<p>
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><?php _e('&laquo; Back', $this -> plugin_name); ?></a>
		</p>
	<?php endif; ?>
</div>