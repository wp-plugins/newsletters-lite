<div class="wrap" style="width:450px;">
	<h2><?php _e('DKIM Wizard', $this -> plugin_name); ?></h2>
	
	<h3><?php _e('Step 3: Verify the DKIM setup', $this -> plugin_name); ?></h3>
	
	<p>
		<?php _e('All done! Save the settings then send an email using the Test Email Settings utility to the email address below and you will receive the results on your From Address in a few minutes.', $this -> plugin_name); ?>
	</p>
	
	<textarea onmouseup="jQuery(this).unbind('mouseup'); return false;" onfocus="jQuery(this).select();" style="white-space:nowrap;" class="code" rows="2" cols="60"><?php echo esc_attr(stripslashes("check-auth@verifier.port25.com")); ?></textarea>

	<span style="display:none;">	
		<form action="" onsubmit="jQuery('#dkimbutton').attr('disabled', 'disabled'); jQuery('#dkimloading').show(); dkimwizard(jQuery(this).serialize()); return false;" id="dkimform3">
			<input type="hidden" name="domain" value="<?php echo stripslashes($domain); ?>" />
			<input type="hidden" name="selector" value="<?php echo stripslashes($selector); ?>" />
			<input type="hidden" name="public" value="<?php echo stripslashes($public); ?>" />
			<input type="hidden" name="private" value="<?php echo stripslashes($private); ?>" />
			<input type="hidden" name="goto" value="step2" />
			<input type="submit" name="continue" value="<?php _e('Continue', $this -> plugin_name); ?>" />
		</form>
	</span>
		
	<p class="submit">
		<input onclick="jQuery.colorbox.close();" type="button" class="button button-secondary" name="close" value="<?php _e('Close', $this -> plugin_name); ?>" />
		<input onclick="jQuery('#goto').val('step2'); jQuery('#dkimform3').submit();" type="button" class="button button-secondary" name="back" value="<?php _e('&laquo; Back', $this -> plugin_name); ?>" />
		<input id="dkimbutton" onclick="jQuery('#settings-form').submit();" type="button" class="button button-primary" name="continue" value="<?php _e('Finished, save the settings &raquo;', $this -> plugin_name); ?>" />
		<span id="dkimloading" style="display:none;"><span class="newsletters_loading"></span></span>
	</p>
</div>