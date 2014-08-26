<?php $deleteonbounce = $this -> get_option('deleteonbounce'); ?>
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="deleteonbounce_Y"><?php echo __('Subscriber Delete on Bounce', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('When an email has bounced to a subscriber the number of times specified in the "Bounce Count" setting below, the subscriber will be permanently deleted from the database.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="jQuery('#deleteonbounce_div').show();" <?php echo ($deleteonbounce == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="deleteonbounce" value="Y" id="deleteonbounce_Y" /> <?php echo __('Yes', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#deleteonbounce_div').hide();" <?php echo ($deleteonbounce == "N") ? 'checked="checked"' : ''; ?> type="radio" name="deleteonbounce" value="N" id="deleteonbounce_N" /> <?php echo __('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should a subscriber be deleted when an email to a subscriber bounces?', $this -> plugin_name); ?></span>
			</td>
		</tr>
    </tbody>
</table>

<div id="deleteonbounce_div" style="display:<?php echo ($this -> get_option('deleteonbounce') == "Y") ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="bouncecount"><?php _e('Bounce Count', $this -> plugin_name); ?></label>
                <?php echo $Html -> help(__('The number of emails to bounce to a subscriber before it is deleted. Use a number 1 (immediate delete) or higher.', $this -> plugin_name)); ?></th>
                <td>
                    <input type="text" class="widefat" style="width:45px;" name="bouncecount" value="<?php echo esc_attr(stripslashes($this -> get_option('bouncecount'))); ?>" id="bouncecount" />
                    <span class="howto"><?php _e('How many times should an email bounce to a subscriber before deletion?', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
    <tbody>
		<?php $adminemailonbounce = $this -> get_option('adminemailonbounce'); ?>
		<tr>
			<th><?php echo __('Admin Notify on Bounce', $this -> plugin_name); ?></th>
			<td>
				<label><input <?php echo ($adminemailonbounce == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonbounce" value="Y" />&nbsp;<?php echo __('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo ($adminemailonbounce == "N") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonbounce" value="N" />&nbsp;<?php echo __('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should the admin be notified when an email to a subscriber has bounced?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="bounceemail"><?php echo __('Bounce Receival Email', $this -> plugin_name); ?></label></th>
			<td>
				<input class="widefat" type="text" size="25" id="bounceemail" name="bounceemail" value="<?php echo $this -> get_option('bounceemail'); ?>" />
				<span class="howto"><?php _e('Email address to receive bounce notifications on. The Return-Path header on all emails is set to this value.', $this -> plugin_name); ?></span>
			</td>
		</tr>
        <tr>
            <th><label for="bouncemethod_pop"><?php _e('Bounce Handling Method', $this -> plugin_name); ?></label></th>
            <td>
                <label><input onclick="jQuery('#bouncemethod_pop_div').hide(); jQuery('#bouncemethod_sns_div').hide(); jQuery('#bouncemehtod_cgi_div').show();" <?php echo ($this -> get_option('bouncemethod') == "cgi") ? 'checked="checked"' : ''; ?> type="radio" name="bouncemethod" value="cgi" id="bouncemethod_cgi" /> <?php _e('Email Piping (CGI)', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#bouncemethod_pop_div').show(); jQuery('#bouncemethod_sns_div').hide(); jQuery('#bouncemehtod_cgi_div').hide();" <?php echo ($this -> get_option('bouncemethod') == "pop") ? 'checked="checked"' : ''; ?> type="radio" name="bouncemethod" value="pop" id="bouncemethod_pop" /> <?php _e('POP3 Email Fetch', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#bouncemethod_sns_div').show(); jQuery('#bouncemethod_pop_div').hide(); jQuery('#bouncemehtod_cgi_div').hide();" <?php echo ($this -> get_option('bouncemethod') == "sns") ? 'checked="checked"' : ''; ?> type="radio" name="bouncemethod" value="sns" id="bouncemethod_sns" /> <?php _e('Amazon SNS', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('Method to use to record bounced emails to subscribers.', $this -> plugin_name); ?></span>
            </td>
        </tr>
	</tbody>
</table>

<div id="bouncemethod_cgi_div" style="display:;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="<?php echo $this -> pre; ?>servertype"><?php _e('Server Type', $this -> plugin_name); ?></label></th>
				<td>
					<?php $servertypes = array('cpanel' => 'cPanel (or other)', 'plesk' => 'Plesk'); ?>
					<select class="widefat" style="width:auto;" id="<?php echo $this -> pre; ?>servertype" name="servertype">
						<?php foreach ($servertypes as $skey => $sval) : ?>
							<option <?php echo ($this -> get_option('servertype') == $skey) ? 'selected="selected"' : ''; ?> value="<?php echo $skey; ?>"><?php echo $sval; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="bouncemethod_pop_div" style="display:<?php echo ($this -> get_option('bouncemethod') == "pop") ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="bouncepop_interval"><?php _e('Check Interval', $this -> plugin_name); ?></label></th>
                <td>
                    <?php $popintervals = wp_get_schedules(); ?>
                    <select class="widefat" style="width:auto;" name="bouncepop_interval" id="bouncepop_interval">
                        <option value="0"><?php _e('- Select -', $this -> plugin_name); ?></option>
                        <?php foreach ($popintervals as $key => $val) : ?>
                            <option <?php echo ($this -> get_option('bouncepop_interval') == $key) ? 'selected="selected"' : ''; ?> value="<?php echo $key; ?>"><?php echo $val['display']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="howto"><?php _e('How often should the mailbox be checked for bounced emails.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="bouncepop_host"><?php _e('POP Host', $this -> plugin_name); ?></label></th>
                <td>
                    <input class="widefat" type="text" name="bouncepop_host" value="<?php echo esc_attr(stripslashes($this -> get_option('bouncepop_host'))); ?>" id="bouncepop_host" />
                    <span class="howto"><?php _e('You can prefix with ssl:// or tls:// if you have OpenSSL support installed on PHP, else leave it out.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="bouncepop_user"><?php _e('POP User/Email', $this -> plugin_name); ?></label></th>
                <td>
                    <input class="widefat" autocomplete="off" type="text" name="bouncepop_user" value="<?php echo esc_attr(stripslashes($this -> get_option('bouncepop_user'))); ?>" id="bouncepop_user" />
                    <span class="howto"><?php _e('POP/incoming email username', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="bouncepop_pass"><?php _e('POP Password', $this -> plugin_name); ?></label></th>
                <td>
                    <input class="widefat" autocomplete="off" type="password" name="bouncepop_pass" value="<?php echo esc_attr(stripslashes($this -> get_option('bouncepop_pass'))); ?>" id="bouncepop_pass" />
                    <span class="howto"><?php _e('POP/incoming email password', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="bouncepop_port"><?php _e('POP Port', $this -> plugin_name); ?></label></th>
                <td>
                    <input class="widefat" style="width:45px;" type="text" name="bouncepop_port" value="<?php echo esc_attr(stripslashes($this -> get_option('bouncepop_port'))); ?>" id="bouncepop_port" />
                    <span class="howto"><?php _e('POP/incoming port number to connect to', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
            	<th>&nbsp;</th>
            	<td>
            		<a id="testbouncesettings" class="button-primary" onclick="testbouncesettings(); return false;" href="?page=<?php echo $this -> sections -> settings; ?>" title="<?php _e('Test POP3 bounce settings', $this -> plugin_name); ?>"><?php _e('Test POP3 Settings', $this -> plugin_name); ?></a>
            		<span id="testbouncesettingsloading" style="display:none;"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" border="0" style="border:none;" /></span>
            	</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="bouncemethod_sns_div" style="display:<?php echo ($this -> get_option('bouncemethod') == "sns") ? 'block' : 'none'; ?>;">
	<p><?php echo sprintf(__('Note that Amazon SNS is only available when you are sending emails through Amazon SES. Please see our documentation for setting up Amazon SES with SNS. Your Amazon SNS topic subscription endpoint URL is %s.', $this -> plugin_name), '<code>' . home_url('/') . '?' . $this -> pre . 'method=bounce&type=sns</code>'); ?></p>
</div>

<script type="text/javascript">
function testbouncesettings() {
	var pop_host = jQuery('#bouncepop_host').val();
	var pop_user = jQuery('#bouncepop_user').val();
	var pop_pass = jQuery('#bouncepop_pass').val();
	var pop_port = jQuery('#bouncepop_port').val();
	var formvalues = {host:pop_host, user:pop_user, pass:pop_pass, port:pop_port};
	jQuery('#testbouncesettingsloading').show();
	jQuery('#testbouncesettings').attr('disabled', "disabled");
	
	jQuery.post(wpmlajaxurl + '?action=<?php echo $this -> pre; ?>testbouncesettings', formvalues, function(response) {
		jQuery.colorbox({html:response});
		jQuery('#testbouncesettingsloading').hide();
		jQuery('#testbouncesettings').removeAttr('disabled');
	});
}
</script>