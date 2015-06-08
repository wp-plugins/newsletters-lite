<!-- Custom CSS, Theme and Scripts -->

<table class="form-table">
	<tbody>
		<tr>
        	<th><label for="theme_folder"><?php _e('Select Theme Folder', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($themefolders = $this -> get_themefolders()) : ?>
                	<select onchange="jQuery('#settings-form').submit();" name="theme_folder" id="theme_folder">
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
        
	        <tr>
	        	<th><?php _e('Child Theme Folder', $this -> plugin_name); ?></th>
	        	<td>
		        	<?php if ($this -> has_child_theme_folder()) : ?>
	        			<p><?php echo sprintf(__('Yes, there is a %s folder inside your theme folder %s', $this -> plugin_name), '<code>newsletters</code>', '<code>' . basename(get_stylesheet_directory()) . '</code>'); ?></p>
	        		<?php else : ?>
	        			<p><?php echo sprintf(__('No child theme folder. See the %s to use this.', $this -> plugin_name), '<a href="http://tribulant.com/docs/wordpress-mailing-list-plugin/7890" target="_blank">' . __('documentation', $this -> plugin_name) . '</a>'); ?></p>
	        		<?php endif; ?>
	        	</td>
	        </tr>
        <?php /*<tr>
        	<th><label for="theme_usestyle_Y"><?php _e('Use Theme Style File?', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input <?php echo ($this -> get_option('theme_usestyle') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="theme_usestyle" value="Y" id="theme_usestyle_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                <label><input <?php echo ($this -> get_option('theme_usestyle') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="theme_usestyle" value="N" id="theme_usestyle_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                <span class="howto"><?php echo sprintf(__('Setting this to "Yes" will load the %s file inside the theme folder.', $this -> plugin_name), '<code>css/style.css</code>'); ?></span>
            </td>
        </tr>*/ ?>
	</tbody>
</table>

<!-- Default Scripts & Styles -->
<div id="defaultscriptsstyles">
	<?php $this -> render('settings' . DS . 'defaultscriptsstyles', false, true, 'admin'); ?>
</div>

<table class="form-table">
	<tbody>
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
	<textarea name="customcsscode" id="customcsscode" rows="12" class="widefat"><?php echo htmlentities($this -> get_option('customcsscode'), false, get_bloginfo('charset')); ?></textarea>
</div>