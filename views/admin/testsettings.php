<div class="wrap newsletters">
	<script type="text/javascript">
	var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	
	<div style="width:400px;">
	    <h2><?php _e('Test Email Settings', $this -> plugin_name); ?></h2>
	    
	    <p><?php _e('This function will test your current email settings and provide an explanatory error message if email sending fails.', $this -> plugin_name); ?>
	     <?php _e('Please type an email address below to send a test email to.', $this -> plugin_name); ?></p>
	    
	    <div class="newsletters_error"><?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?></div>
	    
	    <form id="testsettingsform" onsubmit="wpml_testsettings(this); return false;" action="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=testsettings" method="post">    
	        <p>
	            <label for="testemail"><?php _e('Email Address:', $this -> plugin_name); ?></label><br/>
	            <input tabindex="1" class="widefat" type="text" style="width:400px;" name="testemail" id="testemail" value="<?php echo esc_attr(stripslashes($_POST['testemail'])); ?>" />
	        </p>
	        
	        <p>
	        	<?php $_POST['subject'] = (empty($_POST['subject'])) ? __('Test Email', $this -> plugin_name) : $_POST['subject']; ?>
	        	<label for="subject"><?php _e('Subject:', $this -> plugin_name); ?></label><br/>
	        	<input tabindex="2" class="widefat" style="width:400px;" type="text" name="subject" id="subject" value="<?php echo esc_attr(stripslashes($_POST['subject'])); ?>" />
	        </p>
	        
	        <p>
	        	<label for="message"><?php _e('Message:', $this -> plugin_name); ?></label>
	        	<?php $_POST['message'] = (empty($_POST['message'])) ? __('This is a test email sent from the Newsletter plugin.', $this -> plugin_name) : $_POST['message']; ?>
	        	<textarea name="message" id="message" rows="5" class="widefat" style="width:400px;" cols="100%"><?php echo esc_attr(stripslashes($_POST['message'])); ?></textarea>
	        </p>
	        
	        <p>
	        	<label><input <?php echo (!empty($_POST['testattachment'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="testattachment" value="1" /> <?php _e('Include a test attachment', $this -> plugin_name); ?></label>
	        </p>
	        
	        <p>
	        	<input class="button-secondary" onclick="jQuery.colorbox.close();" type="button" name="close" value="<?php _e('Close', $this -> plugin_name); ?>" />
	            <input id="testsettingsbutton" class="button-primary" type="submit" name="submit" value="<?php _e('Send Test Email', $this -> plugin_name); ?>" />
	            <span style="display:none;" id="wpml_testsettings_loading"><span class="newsletters_loading"></span></span>
	        </p>
	    </form>
	    
	    <script type="text/javascript">    
		function wpml_testsettings(form) {
			var formvalues = jQuery('#testsettingsform').serialize();
			jQuery('#wpml_testsettings_loading').show();
			jQuery('#testsettingsbutton').attr('disabled', "disabled");
			
			jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>testsettings', formvalues, function(response) {
				jQuery('#testsettingswrapper').html(response);
				jQuery('#wpml_testsettings_loading').hide();
				jQuery('#testsettingsbutton').removeAttr('disabled');
				jQuery.colorbox.resize();
			});
		}
		</script>
	</div>
</div>