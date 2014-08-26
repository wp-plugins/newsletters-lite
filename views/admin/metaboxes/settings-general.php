<table class="form-table">
	<tbody>
		<tr>
			<th><label for="rssfeedN"><?php _e('Newsletters RSS Feed', $this -> plugin_name); ?></label> <?php echo $Html -> help(__('A simple RSS feed of your newsletters which your users can subscribe to.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('rssfeed') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="rssfeed" value="Y" id="rssfeedY" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('rssfeed') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="rssfeed" value="N" id="rssfeedN" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Turn On to show an RSS feed of newsletters at', $this -> plugin_name); ?> <?php echo $Html -> link(home_url() . '/?feed=newsletters', home_url() . '/?feed=newsletters', array('style' => "font-weight:bold;")); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>adminemail"><?php _e('Administrator Email', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('This email address is used for general notification purposes throughout the plugin. You may use multiple, comma separated email addresses for multiple administrators eg. email1@domain.com,email2@domain.com,email3@domain.com,etc.', $this -> plugin_name)); ?></th>
			<td>
				<input type="text" class="widefat" id="<?php echo $this -> pre; ?>adminemail" name="adminemail" value="<?php echo $this -> get_option('adminemail'); ?>" />
				<span class="howto"><?php _e('Email address of the administrator for notification purposes.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="smtpfrom"><?php _e('From Address', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('This email address is used for the "From:" header in all outgoing emails and it will appear in the recipient email/webmail client as the sender from which the email was sent.', $this -> plugin_name)); ?></th>
			<td>
            	<input onkeyup="jQuery('#updatereturnpath_div').show(); if (jQuery('#updatereturnpath').attr('checked')) { jQuery('#bounceemail').val(jQuery(this).val()); }" class="widefat" type="text" id="smtpfrom" name="smtpfrom" value="<?php echo $this -> get_option('smtpfrom'); ?>" />
            	
            	<div id="updatereturnpath_div" style="display:none;">
            		<label><input onclick="jQuery('#bounceemail').val(jQuery('#smtpfrom').val());" type="checkbox" name="updatereturnpath" value="1" id="updatereturnpath" /> <?php _e('Update "Bounce Receival Email" setting with this value as well?', $this -> plugin_name); ?></label>
            		<?php echo $Html -> help(__('Many email servers requires the "Bounce Receival Email" (Return-Path) header value to be the same as the "From Address" (From) header value else it may not send out emails. If your emails are not going out, try making the "Bounce Receival Email" (Return-Path) and "From Address" (From) exactly the same using this checkbox.', $this -> plugin_name)); ?>
            	</div>
                
                <span class="howto"><?php _e('This is the From email address that your subscribers will see.', $this -> plugin_name); ?></span>
            </td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>smtpfromname"><?php _e('From Name', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Use your business name, website name or even your own name which will appear to the recipient in their email/webmail client so that they immediately know from whom the email was sent.', $this -> plugin_name)); ?></th>
			<td>
            	<input class="widefat" type="text" id="<?php echo $this -> pre; ?>smtpfromname" name="smtpfromname" value="<?php echo $this -> get_option('smtpfromname'); ?>" />
                <span class="howto"><?php _e('This is the name that will be displayed in the From field to your subscribers.', $this -> plugin_name); ?></span>
            </td>
		</tr>
		<tr>
			<th><label for="trackingY"><?php _e('Read Tracking', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Turn this setting on to enable the remote read tracking then you can use the shortcode [wpmltrack] inside your newsletter theme or content. The [wpmltrack] shortcode creates a 1x1 pixels invisible image in the email which calls back to your site to let your software know that the email was opened/read by the recipient.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('tracking') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tracking" value="Y" id="trackingY" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('tracking') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="tracking" value="N" id="trackingN" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Invisible tracking inside newsletters to tell you how many emails were (not) read', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="clicktrack_Y"><?php _e('Click Tracking', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('The click tracking will convert your links to unique shortlinks automatically. When the links are clicked inside newsletters, the link, email and subscriber is tracked to create statistics.', $this -> plugin_name)); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('clicktrack') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="clicktrack" value="Y" id="clicktrack_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('clicktrack') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="clicktrack" value="N" id="clicktrack_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should links inside newsletters be tracked as they are clicked?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>mailtype"><?php _e('Mail Type', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Choose your preferred way of sending emails. If you are not sure, leave it on "Local Server" setting to send through your own server. Advanced users can use an "SMTP Server" if needed.', $this -> plugin_name)); ?></th>
			<td>
				<?php $mailtype = $this -> get_option('mailtype'); ?>
				<label><input <?php echo ($mailtype == "smtp") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailtypediv').show();" type="radio" name="mailtype" value="smtp" />&nbsp;<?php _e('SMTP Server', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Use this for any remote or local SMTP server or popular email and relay services such as Gmail, AuthSMTP, AmazonSES, SendGrid, etc.', $this -> plugin_name)); ?>
				<label><input id="<?php echo $this -> pre; ?>mailtype" <?php echo ($mailtype == "mail") ? 'checked="checked"' : ''; ?> onclick="jQuery('#mailtypediv').hide();" type="radio" name="mailtype" value="mail" />&nbsp;<?php _e('Local Server (recommended)', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Local server uses WordPress wp_mail() which by default uses your local email exchange on this hosting. This is the recommended option as it should work without any additional setup.', $this -> plugin_name)); ?>
                <span class="howto"><?php _e('The method of sending out emails globally.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="mailtypediv" style="display:<?php echo $mailtypedisplay = ($mailtype == "smtp" || $mailtype == "gmail") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th>
					<label for="<?php echo $this -> pre; ?>smtphost"><?php _e('SMTP Host Name', $this -> plugin_name); ?></label>
				</th>
				<td>
                	<input class="widefat" type="text" id="<?php echo $this -> pre; ?>smtphost" name="smtphost" value="<?php echo $this -> get_option('smtphost'); ?>" />
                    <span class="howto"><?php _e('SMTP host name eg. "localhost". For Gmail, use "smtp.gmail.com".', $this -> plugin_name); ?></span>
                </td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>smtpport"><?php _e('SMTP Port', $this -> plugin_name); ?></label></th>
				<td>
                	<input class="widefat" style="width:65px;" type="text" name="smtpport" value="<?php echo $this -> get_option('smtpport'); ?>" id="<?php echo $this -> pre; ?>smtpport" />
                    <span class="howto"><?php _e('This is the SMTP port number to connect to. This is usually port 25.', $this -> plugin_name); ?></span>
                </td>
			</tr>
			<tr>
				<th><label for="smtpsecure_N"><?php _e('SMTP Protocol', $this -> plugin_name); ?></label></th>
				<td>
					<?php $smtpsecure = $this -> get_option('smtpsecure'); ?>
					<label><input <?php echo ($smtpsecure == "ssl") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="ssl" id="smtpsecure_ssl" /> <?php _e('SSL', $this -> plugin_name); ?></label>
					<label><input <?php echo ($smtpsecure == "tls") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="tls" id="smtpsecure_tls" /> <?php _e('TLS', $this -> plugin_name); ?></label>
					<label><input <?php echo (empty($smtpsecure) || $smtpsecure == "N") ? 'checked="checked"' : ''; ?> type="radio" name="smtpsecure" value="N" id="smtpsecure_N" /> <?php _e('None (recommended)', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Set the connection protocol prefix.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>smtpauth"><?php _e('SMTP Authentication', $this -> plugin_name); ?></label></th>
				<td>
					<?php $smtpauth = $this -> get_option('smtpauth'); ?>
					<label><input id="<?php echo $this -> pre; ?>smtpauth" onclick="jQuery('#smtpauthdiv').show();" <?php echo $authCheck1 = ($smtpauth == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="smtpauth" value="Y" />&nbsp;<?php _e('On', $this -> plugin_name); ?></label>
					<label><input onclick="jQuery('#smtpauthdiv').hide();" <?php echo $authCheck2 = ($smtpauth == "N") ? 'checked="checked"' : ''; ?> type="radio" name="smtpauth" value="N" />&nbsp;<?php _e('Off', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('Turn On if your SMTP server requires a username and password.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php $authdisplay = ($smtpauth == "Y") ? 'block' : 'none'; ?>
	<div id="smtpauthdiv" style="display:<?php echo $authdisplay; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="<?php echo $this -> pre; ?>smtpuser"><?php _e('SMTP Username', $this -> plugin_name); ?></label></th>
					<td><input autocomplete="off" class="widefat" type="text" id="<?php echo $this -> pre; ?>smtpuser" name="smtpuser" value="<?php echo $this -> get_option('smtpuser'); ?>" /></td>
				</tr>
				<tr>
					<th><label for="<?php echo $this -> pre; ?>smtppass"><?php _e('SMTP Password', $this -> plugin_name); ?></label></th>
					<td><input autocomplete="off" class="widefat" type="text" id="<?php echo $this -> pre; ?>smtppass" name="smtppass" value="<?php echo $this -> get_option('smtppass'); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="dkim_N"><?php _e('DKIM Signing', $this -> plugin_name); ?></label> <?php echo $Html -> help(__('DKIM (DomainKeys Identified Mail) is a way to digitally sign messages and verify that the messages were sent by a particular domain. It works like a wax seal on an envelope, preventing messages from being tampered with.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="if (!confirm('<?php _e('The DKIM signature only works if you are using an SMTP server. If you want to use your local email server (WP Mail), please enable DKIM on the server itself and do not turn this on. The wizard will now start.', $this -> plugin_name); ?>')) { return false; } dkimwizard({domain:jQuery('#dkim_domain').val(), selector:jQuery('#dkim_selector').val()}); jQuery('#dkim_div').show(); jQuery('#dkim_wizard_div').show();" <?php echo ($this -> get_option('dkim') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="dkim" value="Y" id="dkim_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#dkim_div').hide(); jQuery('#dkim_wizard_div').hide();" <?php echo ($this -> get_option('dkim') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="dkim" value="N" id="dkim_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Turn on/off the DKIM signing of your outgoing emails. Only use this with SMTP server.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="dkim_div" style="display:<?php echo ($this -> get_option('dkim') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="dkim_domain"><?php _e('DKIM Domain', $this -> plugin_name); ?></label></th>
				<td>					
					<input type="text" name="dkim_domain" class="widefat" value="<?php echo esc_attr(stripslashes($this -> get_option('dkim_domain'))); ?>" id="dkim_domain" />
					<span class="howto"><?php _e('Use the domain name that you are sending from, the one inside the From Address value.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="dkim_selector"><?php _e('DKIM Selector', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" name="dkim_selector" class="widefat" value="<?php echo esc_attr(stripslashes($this -> get_option('dkim_selector'))); ?>" id="dkim_selector" />
					<span class="howto"><?php _e('Any string with letters only. Use "newsletters" by default', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php $private = $this -> get_option('dkim_private'); ?>
	<div id="dkim_private_div" style="display:<?php echo (!empty($private)) ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="dkim_private"><?php _e('DKIM Private Key', $this -> plugin_name); ?></label></th>
					<td>
						<textarea id="dkim_private" name="dkim_private" rows="4" cols="100%" class="widefat"><?php echo stripslashes($private); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th>&nbsp;</th>
			<td>
				<a id="testsettings" class="button button-primary" onclick="testsettings(); return false;" href="?page=<?php echo $this -> sections -> settings; ?>" title="<?php _e('Test your email settings', $this -> plugin_name); ?>"><?php _e('Test Email Settings &raquo;', $this -> plugin_name); ?></a>
				
				<span id="dkim_wizard_div" style="display:<?php echo ($this -> get_option('dkim') == "Y") ? 'inline-block' : 'none'; ?>;">
					<a id="dkimwizard" href="" onclick="dkimwizard({domain:jQuery('#dkim_domain').val(), selector:jQuery('#dkim_selector').val()}); return false;" class="button button-primary"><?php _e('Run DKIM Wizard &raquo;', $this -> plugin_name); ?></a>
				</span>
				
				<span id="testsettingsloading" style="display:none;"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" border="0" style="border:none;" /></span>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
function testsettings() {
	jQuery('#testsettingsloading').show();
	jQuery('#testsettings').attr('disabled', "disabled");
	var formvalues = jQuery('#settings-form').serialize();
	
	jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>testsettings&init=1', formvalues, function(response) {
		jQuery.colorbox({html:response});
		jQuery('#testsettingsloading').hide();
		jQuery('#testsettings').removeAttr('disabled');
	});
}

function dkimwizard(formvalues) {
	jQuery('#testsettingsloading').show();
	jQuery('#dkimwizard').attr('disabled', 'disabled');
	
	jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>dkimwizard', formvalues, function(response) {
		jQuery.colorbox({html:response});
		jQuery('#testsettingsloading').hide();
		jQuery('#dkimwizard').removeAttr('disabled');
	});
}
</script>