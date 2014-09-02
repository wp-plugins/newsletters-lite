<!-- WordPress Related Settings -->

<?php

$locale = get_locale();
$mofile = $this -> plugin_name . '-' . $locale . '.mo';
$mofull = 'wp-mailinglist-languages' . DS;
$language_external = $this -> get_option('language_external');

?>

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
			<?php echo $Html -> help(sprintf(__('When turning this on, ensure that the following file exists: %s . Get language files at %s', $this -> plugin_name), 'wp-content/plugins/' . $mofull, '<a href="https://github.com/tribulant/wp-mailinglist-languages" target="_blank">' . __('wp-mailinglist-languages Github', $this -> plugin_name) . '</a>')); ?></th>
			<td>
				<label><input <?php echo (!empty($language_external) && $language_external == 1) ? 'checked="checked"' : ''; ?> type="checkbox" name="language_external" value="1" id="language_external" /> <?php _e('Yes, load external language file', $this -> plugin_name); ?></label>
				(<a href="https://github.com/tribulant/wp-mailinglist-languages" target="_blank"><?php _e('language files', $this -> plugin_name); ?></a>)
				<span class="howto"><?php _e('Place the .mo file inside wp-content/plugins/wp-mailinglist-languages/ with the correct file name', $this -> plugin_name); ?></span>
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
	</tbody>
</table>