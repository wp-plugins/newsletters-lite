<!-- Custom CSS, Theme and Scripts -->

<table class="form-table">
	<tbody>
		<tr>
        	<th><label for="theme_folder"><?php _e('Select Theme Folder', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($themefolders = $this -> get_themefolders()) : ?>
                	<select onchange="newsletters_change_themefolder(this.value);" name="theme_folder" id="theme_folder">
                    	<?php foreach ($themefolders as $themefolder) : ?>
                        	<option <?php echo ($this -> get_option('theme_folder') == $themefolder) ? 'selected="selected"' : ''; ?> name="<?php echo $themefolder; ?>"><?php echo $themefolder; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="change_themefolder_loading" style="display:none;"><i class="fa fa-spin fa-fw fa-refresh"></i></span>
                    <span class="howto"><?php echo sprintf(__('Select the folder inside "%s" to render template files from. eg. "default"', $this -> plugin_name), $this -> plugin_name . '/views/'); ?></span>
                <?php else : ?>
                	<p class="newsletters_error"><?php _e('No theme folders could be found inside the "' . $this -> plugin_name . '/views/" folder.', $this -> plugin_name); ?>
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
	</tbody>
</table>

<script type="text/javascript">
	var newsletters_change_themefolder = function(themefolder) {
		if (typeof themefolder != 'undefined') {
			jQuery('#change_themefolder_loading').show();
			jQuery('#defaultscriptsstyles').slideUp();
			
			jQuery.ajax({
				url: newsletters_ajaxurl + '?action=newsletters_change_themefolder',
				method: "POST",
				data: {themefolder:themefolder}
			}).done(function(response) {
				jQuery('#defaultscriptsstyles').html(response).slideDown();
			}).fail(function(jqXHR, textStatus, errorThrown) {
				alert('<?php _e('Ajax call failed: ' + errorThrown, $this -> plugin_name); ?>');
			}).always(function() {
				jQuery('#change_themefolder_loading').hide();
			});
		}
	}
</script>

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