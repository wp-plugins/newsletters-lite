<!-- WordPress Related Settings -->

<?php

$locale = get_locale();
$mofile = $this -> plugin_name . '-' . $locale . '.mo';
$mofull = 'wp-mailinglist-languages' . DS;
$mofullf = $mofull . $mofile;
$mofullfull = WP_PLUGIN_DIR . DS . $mofull . $mofile;
$language_external = $this -> get_option('language_external');

$language_file = $mofile;
if (!empty($language_external)) {
	$language_folder = $mofull;
	$language_path = $mofull . $mofile;
	$language_full = $mofullfull;
} else {
	$language_folder = $this -> plugin_name . DS . 'languages' . DS;
	$language_path = $this -> plugin_name . DS . 'languages' . DS . $mofile;
	$language_full = $this -> plugin_base() . DS . 'languages' . DS . $mofile;
}

$wpmailconf = $this -> get_option('wpmailconf');
$wpmailconf_template = $this -> get_option('wpmailconf_template');
$custompostslug = $this -> get_option('custompostslug');

?>

<h2><?php _e('WordPress Emails', $this -> plugin_name); ?></h2>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="wpmailconf"><?php _e('Style WordPress Emails', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#wpmailconf_div').show(); } else { jQuery('#wpmailconf_div').hide(); }" <?php echo (!empty($wpmailconf)) ? 'checked="checked"' : ''; ?> type="checkbox" name="wpmailconf" value="1" id="wpmailconf" /> <?php _e('Yes, apply a template to outgoing emails', $this -> plugin_name); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div id="wpmailconf_div" style="display:<?php echo (!empty($wpmailconf)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="wpmailconf_template"><?php _e('Template', $this -> plugin_name); ?></label></th>
				<td>
					<?php if ($themes = $Theme -> select()) : ?>
						<select name="wpmailconf_template" id="wpmailconf_template">
							<option value=""><?php _e('- None -', $this -> plugin_name); ?></option>
							<?php foreach ($themes as $theme_id => $theme_title) : ?>
								<option <?php echo (!empty($wpmailconf_template) && $wpmailconf_template == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php echo __($theme_title); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<p class="newsletters_error"><?php _e('No templates are available', $this -> plugin_name); ?></p>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<h2><?php _e('Other WordPress Settings', $this -> plugin_name); ?></h2>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="tinymcebtnY"><?php _e('TinyMCE Editor Button', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('tinymcebtn') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebtn" value="Y" id="tinymcebtnY" /> <?php _e('Show', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('tinymcebtn') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebtn" value="N" id="tinymcebtnN" /> <?php _e('Hide', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Would you like to show or hide the plugin button in the TinyMCE editor?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="language_external"><?php _e('Load External Language', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(sprintf(__('When turning this on, ensure that the following file exists: %s . Get language files at %s', $this -> plugin_name), 'wp-content/plugins/' . $language_path, '<a href="https://github.com/tribulant/wp-mailinglist-languages" target="_blank">' . __('wp-mailinglist-languages Github', $this -> plugin_name) . '</a>')); ?></th>
			<td>
				<label><input <?php echo (!empty($language_external) && $language_external == 1) ? 'checked="checked"' : ''; ?> type="checkbox" name="language_external" value="1" id="language_external" /> <?php _e('Yes, load external language file', $this -> plugin_name); ?></label>
				(<a href="https://github.com/tribulant/wp-mailinglist-languages" target="_blank"><?php _e('language files', $this -> plugin_name); ?></a>)
				<span class="howto"><?php echo sprintf(__('Place the %s file inside %s with the correct file name', $this -> plugin_name), '<code>' . $language_file . '</code>', '<code>wp-content/plugins/' . $language_folder . '</code>'); ?></span>
			</td>
		</tr>
		<tr>
			<th><?php _e('Current Language', $this -> plugin_name); ?></th>
			<td>
				<?php if (file_exists($language_full)) : ?>
					<code><?php echo $language_path; ?></code>
				<?php else : ?>
					<?php echo sprintf(__('No language file loaded, please ensure that %s exists.', $this -> plugin_name), '<code>' . $language_path . '</code>'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="sendasnewsletterbox_Y"><?php _e('"Send as Newsletter" box on posts/pages', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('sendasnewsletterbox') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="sendasnewsletterbox" value="Y" id="sendasnewsletterbox_Y" /> <?php _e('Show', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('sendasnewsletterbox') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="sendasnewsletterbox" value="N" id="sendasnewsletterbox_N" /> <?php _e('Hide', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Should the "Send as Newsletter" box show on post/page editing screens?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="subscriberegister_N"><?php _e('Register New Subscribers as Users', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('subscriberegister') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberegister" value="Y" id="subscriberegister_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('subscriberegister') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberegister" value="N" id="subscriberegister_N" /> <?php _e('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Would you like to register all new subscribers as users?', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="custompostslug"><?php _e('Custom Post Type Slug', $this -> plugin_name); ?></label></th>
			<td>
				<input type="text" name="custompostslug" value="<?php echo esc_attr(stripslashes($custompostslug)); ?>" id="custompostslug" />
				<span class="howto"><?php _e('The slug for the newsletter custom post type used internally', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>