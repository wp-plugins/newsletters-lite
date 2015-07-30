<div class="wrap" style="width:450px;">
	<h2><?php _e('DKIM Wizard', $this -> plugin_name); ?></h2>
	
	<h3><?php _e('Step 2: Configure your DNS', $this -> plugin_name); ?></h3>
	
	<p>
		<?php echo sprintf(__('Create the DNS entry below on the hosting of the domain above for %s in TXT. For help, see %s or ask your domain/hosting provider.', $this -> plugin_name), '<strong>' . $selector . '._domainkey.' . $domain . '</strong>', '<a href="http://support.google.com/a/bin/answer.py?hl=en&answer=183895" target="_blank">these instructions</a>'); ?>
	</p>
	
	<?php
	
	$public = trim(preg_replace('/\s+/', '', str_replace("-----BEGIN PUBLIC KEY-----", "", str_replace("-----END PUBLIC KEY-----", "", $public))));
	$dns = "k=rsa; p=" . $public;
	
	?>
	
	<textarea onmouseup="jQuery(this).unbind('mouseup'); return false;" onfocus="jQuery(this).select();" style="white-space:nowrap;" class="code" rows="2" cols="60"><?php echo esc_attr(stripslashes($dns)); ?></textarea>
	
	<p>
		<?php echo sprintf(__('Once you have added the DNS entry, %s to check the status of the DNS to ensure it is working.', $this -> plugin_name), '<a href="http://www.dnswatch.info/dns/dnslookup?la=en&host=' . $selector . '._domainkey.' . $domain . '&type=TXT&submit=Resolve" target="_blank">' . __('click here', $this -> plugin_name) . '</a>'); ?>
	</p>
	
	<form action="" onsubmit="jQuery('#dkimbutton').prop('disabled', true); jQuery('#dkimloading').show(); dkimwizard(jQuery(this).serialize()); return false;" id="dkimform2">
		<input type="hidden" name="domain" value="<?php echo stripslashes($domain); ?>" />
		<input type="hidden" name="selector" value="<?php echo stripslashes($selector); ?>" />
		<input type="hidden" name="public" value="<?php echo stripslashes($public); ?>" />
		<input type="hidden" name="private" value="<?php echo stripslashes($private); ?>" />
		<input type="hidden" id="goto" name="goto" value="step3" />
	
		<p class="submit">
			<input onclick="jQuery.colorbox.close();" type="button" class="button button-secondary" name="close" value="<?php _e('Close', $this -> plugin_name); ?>" />
			<input onclick="jQuery('#goto').val('step1'); jQuery('#dkimform2').submit();" type="button" class="button button-secondary" name="back" value="<?php _e('&laquo; Back', $this -> plugin_name); ?>" />
			<input id="dkimbutton" type="submit" class="button button-primary" name="continue" value="<?php _e('All done, next step &raquo;', $this -> plugin_name); ?>" />
			<span id="dkimloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
		</p>
	</form>
</div>