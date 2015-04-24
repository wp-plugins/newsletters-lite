<!-- Custom CSS, Theme and Scripts -->

<table class="form-table">
	<tbody>
		<tr>
        	<th><label for="theme_folder"><?php _e('Select Theme Folder', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($themefolders = $this -> get_themefolders()) : ?>
                	<select name="theme_folder" id="theme_folder">
                    	<?php foreach ($themefolders as $themefolder) : ?>
                        	<option <?php echo ($this -> get_option('theme_folder') == $themefolder) ? 'selected="selected"' : ''; ?> name="<?php echo $themefolder; ?>"><?php echo $themefolder; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="howto"><?php echo sprintf(__('Select the folder inside "%s" to render template files from. eg. "default"', $this -> plugin_name), $this -> plugin_name . '/views/'); ?></span>
                <?php else : ?>
                	<p class="<?php echo $this -> pre; ?>error"><?php _e('No theme folders could be found inside the "' . $this -> plugin_name . '/views/" folder.', $this -> plugin_name); ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php if ($this -> has_child_theme_folder()) : ?>
	        <tr>
	        	<th><?php _e('Child Theme Folder', $this -> plugin_name); ?></th>
	        	<td>
	        		<?php echo sprintf(__('Yes, there is a %s folder inside your theme folder %s', $this -> plugin_name), '<code>newsletters</code>', '<code>' . basename(get_stylesheet_directory()) . '</code>'); ?>
	        	</td>
	        </tr>
	    <?php endif; ?>
        <tr>
        	<th><label for="theme_usestyle_Y"><?php _e('Use Theme Style File?', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input <?php echo ($this -> get_option('theme_usestyle') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="theme_usestyle" value="Y" id="theme_usestyle_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                <label><input <?php echo ($this -> get_option('theme_usestyle') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="theme_usestyle" value="N" id="theme_usestyle_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('Setting this to "Yes" will load the "css/style.css" file inside the theme folder.', $this -> plugin_name); ?></span>
            </td>
        </tr>
    	<tr>
			<th><label for="customcssN"><?php _e('Use Custom CSS', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('customcss') == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#customcssdiv').show();" type="radio" name="customcss" value="Y" id="customcssY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('customcss') == "N") ? 'checked="checked"' : ''; ?> onclick="jQuery('#customcssdiv').hide();" type="radio" name="customcss" value="N" id="customcssN" /> <?php _e('No', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('Load any additional CSS into the site as needed.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="customcssdiv" style="display:<?php echo ($this -> get_option('customcss') == "Y") ? 'block' : 'none'; ?>;">
	<textarea name="customcsscode" id="customcsscode" rows="12" class="widefat"><?php echo htmlentities($this -> get_option('customcsscode')); ?></textarea>
</div>

<h4><?php _e('Load Default Scripts', $this -> plugin_name); ?></h4>

<p class="howto"><?php _e('Turn On/Off the loading of default scripts in the plugin.', $this -> plugin_name); ?></p>

<?php include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php'); ?>
<?php if (!empty($defaultscripts)) : ?>
	<?php
		
	$loadscripts = $this -> get_option('loadscripts');
	$loadscripts_handles = $this -> get_option('loadscripts_handles');	
	$loadscripts_pages = $this -> get_option('loadscripts_pages');
		
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="checkboxall" value="1" /></th>
				<th><?php _e('Script', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('The name of the script to load. Tick/check the checkboxes to enable and load scripts. Do not disable loading of scripts unless you specifically need or want to.', $this -> plugin_name)); ?></th>
				<th><?php _e('Handle', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Handle/slug to register the script with. Can be changed if a conflict occurs.', $this -> plugin_name)); ?></th>
				<th><?php _e('Post/Page IDs', $this -> plugin_name); ?>
				<?php echo $Html -> help(__('Comma separated post/page IDs to only load the script on, eg. 1,54,3,71', $this -> plugin_name)); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $class = false; ?>
			<?php foreach ($defaultscripts as $handle => $script) : ?>
				<?php
				
				$custom_handle = (empty($loadscripts_handles[$handle])) ? $handle : $loadscripts_handles[$handle];
				$custom_pages = (empty($loadscripts_pages[$handle])) ? false : $loadscripts_pages[$handle];
					
				?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th class="check-column">
						<input <?php echo (!empty($loadscripts) && in_array($handle, $loadscripts)) ? 'checked="checked"' : ''; ?> type="checkbox" name="loadscripts[]" value="<?php echo esc_attr(stripslashes($handle)); ?>" id="loadscripts_<?php echo $handle; ?>" />
					</td>
					<td>
						<label class="row-title" for="loadscripts_<?php echo $handle; ?>"><?php echo $script['name']; ?></label>
					</td>
					<td>
						<input class="widefat" type="text" name="loadscripts_handles[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_handle)); ?>" id="loadscripts_handles_<?php echo $handle; ?>" />
					</td>
					<td>
						<input class="widefat" type="text" name="loadscripts_pages[<?php echo $handle; ?>]" value="<?php echo esc_attr(stripslashes($custom_pages)); ?>" id="loadscripts_pages_<?php echo $handle; ?>" />
					</td>
				</tr>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#loadscripts_pages_<?php echo $handle; ?>').Watermark('<?php echo __('All posts/pages', $this -> plugin_name); ?>');
				});
				</script>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php /*
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="loadscript_jqueryuitabs_Y"><?php _e('jQuery UI Tabs', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('loadscript_jqueryuitabs') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuitabs" value="Y" id="loadscript_jqueryuitabs_Y" /> <?php _e('Yes, load this script', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('loadscript_jqueryuitabs') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuitabs" value="N" id="loadscript_jqueryuitabs_N" /> <?php _e('No, I have it loaded already', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Load the jQuery UI Tabs script for the subscriber management section.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="loadscript_jqueryuibutton_Y"><?php _e('jQuery UI Button', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('loadscript_jqueryuibutton') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuibutton" value="Y" id="loadscript_jqueryuibutton_Y" /> <?php _e('Yes, load this script', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('loadscript_jqueryuibutton') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuibutton" value="N" id="loadscript_jqueryuibutton_N" /> <?php _e('No, I have it loaded already', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('Load the jQuery UI Button script for all the buttons.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="loadscript_jqueryuiwatermark_Y"><?php _e('jQuery UI Watermark', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="jQuery('#loadscript_jqueryuiwatermark_div').show();" <?php echo ($this -> get_option('loadscript_jqueryuiwatermark') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuiwatermark" value="Y" id="loadscript_jqueryuiwatermark_Y" /> <?php _e('Yes, load this script', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#loadscript_jqueryuiwatermark_div').hide();" <?php echo ($this -> get_option('loadscript_jqueryuiwatermark') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuiwatermark" value="N" id="loadscript_jqueryuiwatermark_N" /> <?php _e('No, I have it loaded already', $this -> plugin_name); ?></label>
				<div id="loadscript_jqueryuiwatermark_div" style="display:<?php echo ($this -> get_option('loadscript_jqueryuiwatermark') == "Y") ? 'block' : 'none'; ?>;"><label><strong><?php _e('Handle:', $this -> plugin_name); ?></strong> <input type="text" name="loadscript_jqueryuiwatermark_handle" value="<?php echo esc_attr(stripslashes($this -> get_option('loadscript_jqueryuiwatermark_handle'))); ?>" id="loadscript_jqueryuiwatermark_handle" class="widefat" style="width:150px;" /></label></div>
				<span class="howto"><?php _e('Load the jQuery UI Watermark script for field watermarks.', $this -> plugin_name); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="loadscript_jqueryuploadify_Y"><?php _e('jQuery Uploadify', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="jQuery('#loadscript_jqueryuploadify_div').show();" <?php echo ($this -> get_option('loadscript_jqueryuploadify') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuploadify" value="Y" id="loadscript_jqueryuploadify_Y" /> <?php _e('Yes, load this script', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#loadscript_jqueryuploadify_div').hide();" <?php echo ($this -> get_option('loadscript_jqueryuploadify') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="loadscript_jqueryuploadify" value="N" id="loadscript_jqueryuploadify_N" /> <?php _e('No, I have it loaded already', $this -> plugin_name); ?></label>
				<div id="loadscript_jqueryuploadify_div" style="display:<?php echo ($this -> get_option('loadscript_jqueryuploadify') == "Y") ? 'block' : 'none'; ?>;"><label><strong><?php _e('Handle:', $this -> plugin_name); ?></strong> <input type="text" name="loadscript_jqueryuploadify_handle" value="<?php echo esc_attr(stripslashes($this -> get_option('loadscript_jqueryuploadify_handle'))); ?>" id="loadscript_jqueryuploadify_handle" class="widefat" style="width:150px;" /></label></div>
				<span class="howto"><?php _e('Load the Uploadify script for file upload custom fields.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>*/ ?>