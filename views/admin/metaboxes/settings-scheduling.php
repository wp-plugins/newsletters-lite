<?php $scheduling = $this -> get_option('scheduling'); ?>
<input type="hidden" name="scheduling" value="Y" />

<table class="form-table">
	<tbody>
    	<tr>
			<th><label for="<?php echo $this -> pre; ?>emailsperinterval"><?php _e('Emails per Interval', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Specify the number of emails to send per interval. Rather keep the interval short and this number lower to prevent the procedure which sends out the emails to timeout due to too many emails at once.', $this -> plugin_name)); ?></th>
			<td>
				<input class="widefat" style="width:45px;" type="text" value="<?php echo $this -> get_option('emailsperinterval'); ?>" id="<?php echo $this -> pre; ?>emailsperinterval" name="emailsperinterval" />
				<span class="howto"><?php _e('Recommended below 100', $this -> plugin_name); ?></span>
			</td>
		</tr>
        <tr>
        	<th><label for="schedulecrontype_server"><?php _e('Cron/Schedule Type', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#schedulecrontype_wp_div').show(); jQuery('#schedulecrontype_server_div').hide();" <?php echo ($this -> get_option('schedulecrontype') == "wp") ? 'checked="checked"' : ''; ?> type="radio" name="schedulecrontype" value="wp" id="schedulecrontype_wp" /> <?php _e('WordPress Cron', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#schedulecrontype_wp_div').hide(); jQuery('#schedulecrontype_server_div').show();" <?php echo ($this -> get_option('schedulecrontype') == "server") ? 'checked="checked"' : ''; ?> type="radio" name="schedulecrontype" value="server" id="schedulecrontype_server" /> <?php _e('Server Cron Job (recommended)', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('It is recommended that you use the server cron job as it is more reliable and accurate compared to the WordPress cron.', $this -> plugin_name); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div id="schedulecrontype_wp_div" style="display:<?php echo ($this -> get_option('schedulecrontype') == "wp") ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="<?php echo $this -> pre; ?>scheduleinterval"><?php _e('Schedule Interval', $this -> plugin_name); ?></label></th>
                <td>
                    <?php $scheduleinterval = $this -> get_option('scheduleinterval'); ?>
                    <select class="widefat" style="width:auto;" id="<?php echo $this -> pre; ?>scheduleinterval" name="scheduleinterval">
                    <option value=""><?php _e('- Select Interval -', $this -> plugin_name); ?></option>
                    <?php $schedules = wp_get_schedules(); ?>
                    <?php if (!empty($schedules)) : ?>
                        <?php foreach ($schedules as $key => $val) : ?>
                        <?php $sel = ($scheduleinterval == $key) ? 'selected="selected"' : ''; ?>
                        <option <?php echo $sel; ?> value="<?php echo $key ?>"><?php echo $val['display']; ?> (<?php echo $val['interval'] ?> <?php _e('seconds', $this -> plugin_name); ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </select>
                    
                    <span class="howto"><?php _e('Keep the schedule interval as low as possible for frequent executions.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php

$servercronstring = "";
if (!$servercronstring = $this -> get_option('servercronstring')) {
	$servercronstring = substr(md5(rand(1,999)), 0, 12);
}

$commandurl = home_url() . '/?' . $this -> pre . 'method=docron&amp;auth=' . $servercronstring;
$command = '<code>wget -O /dev/null "' . $commandurl . '" > /dev/null 2>&1</code>';

?>

<input type="hidden" name="servercronstring" value="<?php echo esc_attr(stripslashes($servercronstring)); ?>" />

<div id="schedulecrontype_server_div" style="display:<?php echo ($this -> get_option('schedulecrontype') == "server") ? 'block' : 'none'; ?>;">
	<p>
		<?php echo sprintf(__('You have to create a cron job on your server to execute every 5 minutes with the following command %s', $this -> plugin_name), $command); ?>
	</p>
	<p>
		<?php _e('Please see the documentation for instructions and check with your hosting provider that the WGET command is fully supported on your hosting.', $this -> plugin_name); ?>
	</p>
	<p>
		<?php echo sprintf(__('If you cannot create a cron job or your hosting does not support WGET, you can use %s with the URL %s', $this -> plugin_name), '<a href="https://tribulant.com/partners/easycron/" target="_blank">EasyCron</a>', '<code>' . $commandurl . '</code>'); ?>
	</p>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><?php _e('Admin Notify on Execution', $this -> plugin_name); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('schedulenotify') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="schedulenotify" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('schedulenotify') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="schedulenotify" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
			</td>
		</tr>
	</tbody>
</table>