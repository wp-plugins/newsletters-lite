<!-- Captcha Settings -->

<?php

$captcha_type = $this -> get_option('captcha_type');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="captcha_type_none"><?php _e('Captcha Type', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('Choose the type of captcha security image you want to use or select "None" for no captcha.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="jQuery('#recaptcha_div').hide(); jQuery('#rsc_div').hide();" <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="none" id="captcha_type_none" /> <?php _e('None', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#recaptcha_div').show(); jQuery('#rsc_div').hide();" <?php echo (!empty($captcha_type) && $captcha_type == "recaptcha") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="recaptcha" id="captcha_type_recaptcha" /> <?php _e('reCAPTCHA', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#recaptcha_div').hide(); jQuery('#rsc_div').show();" <?php echo (!empty($captcha_type) && $captcha_type == "rsc") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="rsc" id="captcha_type_rsc" <?php echo (!$this -> is_plugin_active('captcha')) ? 'disabled="disabled"' : ''; ?> /> <?php _e('Really Simple Captcha', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Choose the type of captcha you want to use as a security image on subscribe forms or turn off by choosing "None"', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<!-- reCAPTCHA Settings -->
<?php

$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
$recaptcha_privatekey = $this -> get_option('recaptcha_privatekey');
$recaptcha_theme = $this -> get_option('recaptcha_theme');
$recaptcha_customcss = $this -> get_option('recaptcha_customcss');

?>

<div id="recaptcha_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "recaptcha") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th>&nbsp;</th>
				<td>
					<p><?php echo sprintf(__('In order to use reCAPTCHA, the public and private keys below are required.<br/>Go to the reCAPTCHA sign up and %screate a set of keys%s for this domain.', $this -> plugin_name), '<a href="https://www.google.com/recaptcha/admin/create" onclick="jQuery.colorbox({iframe:true, fastIframe:false, width:\'80%\', height:\'80%\', href:\'http://www.google.com/recaptcha/admin/create\'}); return false;">', '</a>'); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_publickey"><?php _e('Public Key', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="recaptcha_publickey" value="<?php echo esc_attr(stripslashes($recaptcha_publickey)); ?>" id="recaptcha_publickey" />
					<span class="howto"><?php _e('Public key provided by reCAPTCHA upon signing up.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_privatekey"><?php _e('Private Key', $this -> plugin_name); ?></label></th>
				<td>
					<input type="text" class="widefat" name="recaptcha_privatekey" value="<?php echo esc_attr(stripslashes($recaptcha_privatekey)); ?>" id="recaptcha_privatekey" /> 
					<span class="howto"><?php _e('Private key provided by reCAPTCHA upon signing up.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_theme"><?php _e('Theme', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Choose the reCAPTCHA theme to show to your users. Some premade themes by reCAPTCHA are available or you can use the Custom theme and style it according to your needs.', $this -> plugin_name)); ?></th>
				<td>
					<?php $themes = array('red' => "Red", 'white' => "White", 'blackglass' => "Black Glass", 'clean' => "Clean", 'custom' => "Custom"); ?>
					<select onchange="if (this.value == 'custom') { jQuery('#recaptcha_theme_custom_div').show(); } else { jQuery('#recaptcha_theme_custom_div').hide(); }" name="recaptcha_theme" id="recaptcha_theme">
						<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
						<?php foreach ($themes as $theme_key => $theme_value) : ?>
							<option <?php echo (!empty($recaptcha_theme) && $recaptcha_theme == $theme_key) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_key; ?>"><?php echo $theme_value; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="howto"><?php _e('Pick the reCAPTCHA theme that you want to use.', $this -> plugin_name); ?></span>
					
					<div id="recaptcha_theme_custom_div" style="display:<?php echo (!empty($recaptcha_theme) && $recaptcha_theme == "custom") ? 'block' : 'none'; ?>;">
						<h4><?php _e('Custom CSS', $this -> plugin_name); ?></h4>
						<textarea class="widefat" rows="8" cols="100%" name="recaptcha_customcss"><?php echo htmlentities(trim($recaptcha_customcss)); ?></textarea>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php if ($this -> is_plugin_active('captcha')) : ?>
	<div id="rsc_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "rsc") ? 'block' : 'none'; ?>;">
		<!-- Really Simple Captcha Settings -->
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="captcha_bg"><?php _e('Background Color', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('The background color of the captcha image in hex code eg. #FFFFFF', $this -> plugin_name)); ?></th>
					<td>				
						<div class="wp-picker-container">
							<a tabindex="0" id="captcha_bg_button" class="wp-color-result" style="background-color:<?php echo $this -> get_option('captcha_bg'); ?>; ?>;" title="<?php _e('Select Color', $this -> plugin_name); ?>"></a>
							<span class="wp-picker-input-wrap">
								<input type="text" name="captcha_bg" id="captcha_bg" value="<?php echo $this -> get_option('captcha_bg'); ?>" class="wp-color-picker" style="display:none;" />
							</span>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#captcha_bg').iris({
								hide: true,
								change: function(event, ui) {
									jQuery('#captcha_bg_button').css('background-color', ui.color.toString());
								}
							});
							
							jQuery('#captcha_bg').click(function(event) {
								event.stopPropagation();
							});
						
							jQuery('#captcha_bg_button').click(function(event) {							
								jQuery(this).attr('title', "<?php _e('Current Color', $this -> plugin_name); ?>");
								jQuery('#captcha_bg').iris('toggle').toggle();								
								event.stopPropagation();
							});
							
							jQuery('html').click(function() {
								jQuery('#captcha_bg').iris('hide').hide();
								jQuery('#captcha_bg_button').attr('title', "<?php _e('Select Color', $this -> plugin_name); ?>");
							});
						});
						</script>
						
						<span class="howto"><?php _e('Set the background color of the captcha image', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_fg"><?php _e('Text Color', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('The foreground/text color of the text on the captcha image.', $this -> plugin_name)); ?></th>
					<td>
						<div class="wp-picker-container">
							<a tabindex="0" id="captcha_fg_button" class="wp-color-result" style="background-color:<?php echo $this -> get_option('captcha_fg'); ?>; ?>;" title="<?php _e('Select Color', $this -> plugin_name); ?>"></a>
							<span class="wp-picker-input-wrap">
								<input type="text" name="captcha_fg" id="captcha_fg" value="<?php echo $this -> get_option('captcha_fg'); ?>" class="wp-color-picker" style="display:none;" />
							</span>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#captcha_fg').iris({
								hide: true,
								change: function(event, ui) {
									jQuery('#captcha_fg_button').css('background-color', ui.color.toString());
								}
							});
							
							jQuery('#captcha_fg').click(function(event) {
								event.stopPropagation();
							});
						
							jQuery('#captcha_fg_button').click(function(event) {							
								jQuery(this).attr('title', "<?php _e('Current Color', $this -> plugin_name); ?>");
								jQuery('#captcha_fg').iris('toggle').toggle();								
								event.stopPropagation();
							});
							
							jQuery('html').click(function() {
								jQuery('#captcha_fg').iris('hide').hide();
								jQuery('#captcha_fg_button').attr('title', "<?php _e('Select Color', $this -> plugin_name); ?>");
							});
						});
						</script>
						<span class="howto"><?php _e('Set the foreground/text color of the captcha image', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_size_w"><?php _e('Image Size', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('Choose the size of the captcha image as it will display to your users. Fill in the width and the height of the image in pixels (px). The default is 72 by 24px which is optimal.', $this -> plugin_name)); ?></th>
					<td>
						<?php $captcha_size = $this -> get_option('captcha_size'); ?>
						<input type="text" class="widefat" style="width:45px;" name="captcha_size[w]" value="<?php echo $captcha_size['w']; ?>" id="captcha_size_w" /> <?php _e('by', $this -> plugin_name); ?>
						<input type="text" class="widefat" style="width:45px;" name="captcha_size[h]" value="<?php echo $captcha_size['h']; ?>" id="captcha_size_h" /> <?php _e('px', $this -> plugin_name); ?>
						<span class="howto"><?php _e('Choose your preferred size for the captcha image.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_chars"><?php _e('Number of Characters', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('You can increase the number of characters to show in the captcha image to increase the security. Too many characters will make it difficult for your users though. The default is 4.', $this -> plugin_name)); ?></th>
					<td>
						<input type="text" name="captcha_chars" value="<?php echo $this -> get_option('captcha_chars'); ?>" id="captcha_chars" class="widefat" style="width:45px;" /> <?php _e('characters', $this -> plugin_name); ?>
						<span class="howto"><?php _e('The number of characters to show in the captcha image.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_font"><?php _e('Font Size', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('A larger font will make the characters easier to read for your users. The default is 14 pixels.', $this -> plugin_name)); ?></th>
					<td>
						<input type="text" name="captcha_font" value="<?php echo $this -> get_option('captcha_font'); ?>" id="captcha_font" class="widefat" style="width:45px;" /> <?php _e('px', $this -> plugin_name); ?>
						<span class="howto"><?php _e('Choose the font size of the characters on the captcha image.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captchainterval"><?php _e('Cleanup Interval', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('To keep your server clean from old, unused captcha images a schedule will run at the interval specified to clean up old images. Set this to hourly or less as a recommended setting.', $this -> plugin_name)); ?></th>
					<td>
						<?php $captchainterval = $this -> get_option('captchainterval'); ?>
		                <select class="widefat" style="width:auto;" id="captchainterval" name="captchainterval">
			                <option value=""><?php _e('- Select Interval -', $this -> plugin_name); ?></option>
			                <?php $schedules = wp_get_schedules(); ?>
			                <?php if (!empty($schedules)) : ?>
			                    <?php foreach ($schedules as $key => $val) : ?>
			                    <?php $sel = ($captchainterval == $key) ? 'selected="selected"' : ''; ?>
			                    <option <?php echo $sel; ?> value="<?php echo $key ?>"><?php echo $val['display']; ?> (<?php echo $val['interval'] ?> <?php _e('seconds', $this -> plugin_name); ?>)</option>
			                    <?php endforeach; ?>
			                <?php endif; ?>
		                </select>
						<span class="howto"><?php _e('The interval at which old captcha images will be removed from the server.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php endif; ?>