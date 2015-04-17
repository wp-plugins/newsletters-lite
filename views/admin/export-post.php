<!-- Export Ajax Post -->

<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Export Subscribers', $this -> plugin_name); ?></h2>
	
	<?php if (!empty($subscribers)) : ?>
		<p><?php echo sprintf(__('You are about to export <b>%d</b> subscribers from <b>%d</b> mailing lists with <b>%s</b> status.', $this -> plugin_name), count($subscribers), count($_POST['export_lists']), $_POST['export_status']); ?></p>
		<p class="newsletters_exportajaxcount"><span id="exportajaxcount"><strong><span id="exportajaxcountinside" class="newsletters_success">0</span></strong></span> <span id="exportajaxfailedcount">(<strong><span id="exportajaxfailedcountinside" class="newsletters_error">0</span></strong> failed)</span> <?php _e('out of', $this -> plugin_name); ?> <strong><?php echo count($subscribers); ?></strong> <?php _e('subscribers have been exported.', $this -> plugin_name); ?></p>
		
		<div id="exportprogressbar"></div>
		
		<p class="submit">
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><i class="fa fa-arrow-left"></i> <?php _e('Back', $this -> plugin_name); ?></a>
			<a href="" onclick="cancelexporting(); return false;" id="cancelexporting" disabled="disabled" style="display:none;" class="button-secondary"><i class="fa fa-pause"></i> <?php _e('Stop', $this -> plugin_name); ?></a>
			<a href="" onclick="startexporting(); return false;" id="startexporting" disabled="disabled" class="button-primary"><i class="fa fa-refresh fa-spin"></i> <?php _e('Reading data, please wait', $this -> plugin_name); ?></a>
			<span id="exportmore" style="display:none;"><a href="?page=<?php echo $this -> sections -> importexport; ?>#export" id="" class="button-secondary"><?php _e('Export More', $this -> plugin_name); ?></a></span>
		</p>
		
		<h3 style="display:none;"><?php _e('Subscribers Exported', $this -> plugin_name); ?></h3>
		<div id="exportajaxsuccessrecords" class="scroll-list" style="display:none;"><!-- successful records --></div>
		
		<?php /*<div id="exportajaxbox" style="display:none;"><?php
		
		$i = 1;		
		foreach ($subscribers as $subscriber) {
			echo json_encode($subscriber);
			if ($i <= count($subscribers)) { echo '<|>'; }
			$i++;
		}
		
		?></div>*/ ?>
		
		<script type="text/javascript">
		var allsubscribers = [];
			
		jQuery(document).ready(function() {	
			<?php if (!empty($subscribers)) : ?>
				<?php foreach ($subscribers as $subscriber) : ?>
					allsubscribers.push(<?php echo json_encode(stripslashes_deep($subscriber)); ?>);
				<?php endforeach; ?>
			<?php endif; ?>
				
			requestArray = new Array();
			cancelexport = "N";
			exportingnumber = 2000;
			jQuery('#startexporting').removeAttr('disabled').html('<i class="fa fa-play"></i> <?php echo addslashes(__("Start Exporting", $this -> plugin_name)); ?>');
		});
		
		function cancelexporting() {
			cancelexport = "Y";
			jQuery('#cancelexporting').attr('disabled', 'disabled');
			jQuery('#startexporting').removeAttr('disabled').attr('onclick', 'resumeexporting(); return false;').html('<i class="fa fa-play"></i> <?php echo addslashes(__('Resume Exporting', $this -> plugin_name)); ?>');
			
			for (var r = 0; r < requestArray.length; r++) {
				requestArray[r].abort();
			}
		}
		
		function resumeexporting() {
			cancelexport = "N";
			jQuery('#startexporting').attr('disabled', 'disabled').html('<i class="fa fa-play"></i> <?php echo addslashes(__('Exporting Now', $this -> plugin_name)); ?>');
			jQuery('#cancelexporting').removeAttr('disabled');
			
			var newexportingnumber = (exportingnumber - completed);
			requests = (completed - 1);
		
			for (i = 0; i < newexportingnumber; i++) {			
				if (i < subscribercount) {				
					exportsubscriber(subscribers[(completed + i)]);
				}
			}
		}
		
		function startexporting() {
			jQuery('#cancelexporting').removeAttr('disabled').show();
			jQuery('#startexporting').attr('disabled', 'disabled').html('<i class="fa fa-play"></i> <?php echo addslashes(__('Exporting Now', $this -> plugin_name)); ?>');
		
			//text = jQuery('#exportajaxbox').text();
			//subscribercount = '<?php echo count($subscribers); ?>';
			subscribercount = allsubscribers.length;
			//subscribers = text.split('<|>');
			subscribers = allsubscribers;
			completed = 0;
			cancelexport = "N";
			requests = 0;
			exported = 0;
			failed = 0;
			
			headings = <?php echo json_encode($headings); ?>;
			
			if (subscribercount < exportingnumber) {
				exportingnumber = subscribercount;
			}
			
			jQuery('#exportprogressbar').progressbar({value:0});
			
			for (i = 0; i < exportingnumber; i++) {
				if (i < subscribercount) {
					exportsubscriber(subscribers[i]);
				}
			}
		}
		
		function exportsubscriber(subscriber) {
			if (requests >= subscribercount || cancelexport == "Y") { return; }
			requests++;
			
			requestArray.push(jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>exportsubscribers', delimiter:'<?php echo $delimiter; ?>', subscriber:subscriber, headings:headings, exportfile:'<?php echo $exportfile; ?>'}, function(response) {
				completed++;
				jQuery('#exportajaxcountinside').text(completed);
				jQuery('#exportajaxsuccessrecords').prepend('<div class="ui-state-highlight ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-check"></i> ' + response + '</div>').fadeIn().prev().fadeIn();
				var value = (completed * 100) / subscribercount;
				jQuery("#exportprogressbar").progressbar("value", value);
			}).success(function() {
				if (completed == subscribercount) {
					jQuery('#cancelexporting').hide();
					warnMessage = null;
					jQuery('#startexporting').html('<?php echo addslashes(__('Download CSV', $this -> plugin_name)); ?> <i class="fa fa-arrow-right"></i>').removeAttr('disabled').removeAttr('onclick').attr("href", "<?php echo $Html -> retainquery('wpmlmethod=exportdownload&file=' . urlencode($exportfile), home_url()); ?>");
					jQuery('#exportmore').show();
				} else {
					exportsubscriber(subscribers[(requests + 1)]); 
				}
			}));
		}
		</script>
		
		<script type="text/javascript">
		var warnMessage = "<?php _e('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name); ?>";
		
		jQuery(document).ready(function() {
		    window.onbeforeunload = function () {
		        if (warnMessage != null) return warnMessage;
		    }
		});
		</script>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No subscribers are available for export, please try again.', $this -> plugin_name); ?></p>
		<p>
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><?php _e('&laquo; Back', $this -> plugin_name); ?></a>
		</p>
	<?php endif; ?>
</div>