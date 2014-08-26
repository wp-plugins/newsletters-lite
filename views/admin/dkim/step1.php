<div class="wrap" style="width:450px;">
	<h2><?php _e('DKIM Wizard', $this -> plugin_name); ?></h2>
	
	<p class="howto"><?php echo sprintf(__('Setting up DKIM for the domain %s using the selector %s.', $this -> plugin_name), '<strong>' . $domain . '</strong>', '<strong>' . $selector . '</strong>'); ?></p>
	
	<h3><?php _e('Step 1: Save the private key', $this -> plugin_name); ?></h3>
	
	<textarea onmouseup="jQuery(this).unbind('mouseup'); return false;" onfocus="jQuery(this).select();" style="white-space:nowrap;" class="code" rows="13" cols="60"><?php echo stripslashes($private); ?></textarea>
	
	<p>
		<?php _e('The private key above has been filled into the DKIM Private Key box for you.', $this -> plugin_name); ?>
	</p>

	<form action="" onsubmit="do_private_key(); jQuery('#dkimbutton').attr('disabled', 'disabled'); jQuery('#dkimloading').show(); dkimwizard(jQuery(this).serialize()); return false;">	
		<input type="hidden" name="domain" value="<?php echo stripslashes($domain); ?>" />
		<input type="hidden" name="selector" value="<?php echo stripslashes($selector); ?>" />
		<input type="hidden" name="public" value="<?php echo stripslashes($public); ?>" />
		<input type="hidden" name="private" value="<?php echo stripslashes($private); ?>" />
		<input type="hidden" name="goto" value="step2" />
		
		<p class="submit">
			<input onclick="jQuery.colorbox.close();" class="button button-secondary" type="button" name="close" value="<?php _e('Close', $this -> plugin_name); ?>" />
			<input id="dkimbutton" class="button button-primary" type="submit" name="continue" value="<?php _e('Great, next step &raquo;', $this -> plugin_name); ?>" />
			<span id="dkimloading" style="display:none;"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" border="0" style="border:none;" /></span>
		</p>
	</form>
</div>

<script type="text/javascript">
function do_private_key() {
	jQuery('#dkim_private_div').show();
	jQuery('#dkim_private').val(<?php echo json_encode($private); ?>);
}
</script>