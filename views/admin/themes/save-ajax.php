<div style="width:800px;" class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Save a Theme', $this -> plugin_name); ?></h2>
    
    <p>
    	<?php _e('This is a full HTML theme and should contain at least <code>[wpmlcontent]</code> somewhere.', $this -> plugin_name); ?><br/>
        <?php _e('Upload your images, stylesheets and other elements via FTP or the media uploader in WordPress.', $this -> plugin_name); ?><br/>
        <?php _e('Please ensure that all links, images and other references use full, absolute URLs.', $this -> plugin_name); ?>
    </p>
    
    <?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
    
    <?php if ($success) : ?>
    	<p class="newsletters_success"><?php _e('Theme has been saved', $this -> plugin_name); ?></p>
    	
    	<script type="text/javascript">
    	jQuery(document).ready(function() {
	    	jQuery.colorbox.close();
	    	<?php $createpreview = $this -> get_option('createpreview'); ?>
	    	<?php if (!empty($createpreview) && $createpreview == "Y") : ?>
	    		previewrunner();
	    	<?php endif; ?>
    	});
    	</script>
    <?php endif; ?>
    
    <form onsubmit="jQuery.Watermark.HideAll(); newsletters_save_theme(this); return false;" action="?page=<?php echo $this -> sections -> themes; ?>&amp;method=save" method="post" enctype="multipart/form-data">
    	<?php echo $Form -> hidden('Theme[id]'); ?>
    	<?php echo $Form -> hidden('Theme[name]'); ?>
    
    	<table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Theme.title"><?php _e('Title', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('The title of this newsletter theme for internal usage.', $this -> plugin_name)); ?></th>
                    <td>
                    	<?php echo $Form -> text('Theme[title]'); ?>
                    </td>
                </tr>
                <tr>
                	<th><label for="Theme.type_upload"><?php _e('Theme Type', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('Choose how you want to save this newsletter theme. You can either paste HTML code or upload a .html file.', $this -> plugin_name)); ?></th>
                    <td>
                    	<label><input <?php echo ($Html -> field_value('Theme[type]') == "upload" || $Html -> field_value('Theme[type]') == "") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_upload').show(); jQuery('#typediv_paste').hide();" type="radio" name="Theme[type]" value="upload" id="Theme.type_upload" /> <?php _e('Upload an HTML File', $this -> plugin_name); ?></label>
                        <label><input <?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_paste').show(); jQuery('#typediv_upload').hide();" type="radio" name="Theme[type]" value="paste" id="Theme.type_paste" /> <?php _e('HTML Code', $this -> plugin_name); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="typediv_upload" style="display:<?php echo ($Html -> field_value('Theme[type]') == "" || $Html -> field_value('Theme[type]') == "upload") ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for=""><?php _e('Choose HTML File', $this -> plugin_name); ?></label></th>
                        <td>
                        	<input class="widefat" type="file" name="upload" value="" />
                            <?php if (!empty($Theme -> errors['upload'])) : ?>
                            	<div class="<?php echo $this -> pre; ?>error"><?php echo $Theme -> errors['upload']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="typediv_paste" style="display:<?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'block' : 'none'; ?>;">
        	<textarea name="Theme[paste]" class="widefat" id="Theme_paste" rows="10" cols="100%"><?php echo esc_attr(stripslashes($Theme -> data -> paste)); ?></textarea>
        	
        	<script type="text/javascript">
        	jQuery(document).ready(function() {
            	jQuery('textarea#Theme_paste').ckeditor({
                	fullPage: true,
					allowedContent: true,
					height: 500,
					entities: false
            	});
        	});
        	</script>
        </div>
        
        <?php $theme_name = $Html -> field_value('Theme[name]'); ?>
        <?php if (!empty($theme_name)) : ?>
        	<?php if ($theme_name == "themailer") : ?>
        		<h3><?php _e('The Mailer Settings', $this -> plugin_name); ?></h3>
        		<table class="form-table">
        			<tbody>
        				<tr>
        					<th><label for="themailer_address"><?php _e('Address', $this -> plugin_name); ?></label></th>
        					<td>
        						<textarea name="themailer_address" rows="4" class="widefat" id="themailer_address"><?php echo esc_attr(stripslashes($this -> get_option('themailer_address'))); ?></textarea>
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_facebook"><?php _e('Facebook URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_facebook" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_facebook'))); ?>" id="themailer_facebook" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_twitter"><?php _e('Twitter URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_twitter" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_twitter'))); ?>" id="themailer_twitter" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="themailer_rss"><?php _e('RSS URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="themailer_rss" value="<?php echo esc_attr(stripslashes($this -> get_option('themailer_rss'))); ?>" id="themailer_rss" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        			</tbody>
        		</table>
        	<?php elseif ($theme_name == "pronews") : ?>
        		<h3><?php _e('Pro News Settings', $this -> plugin_name); ?></h3>
        		<table class="form-table">
        			<tbody>
        				<tr>
        					<th><label for="pronews_address"><?php _e('Address', $this -> plugin_name); ?></label></th>
        					<td>
        						<textarea name="pronews_address" rows="4" class="widefat" id="pronews_address"><?php echo esc_attr(stripslashes($this -> get_option('pronews_address'))); ?></textarea>
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_facebook"><?php _e('Facebook URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_facebook" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_facebook'))); ?>" id="pronews_facebook" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_twitter"><?php _e('Twitter URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_twitter" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_twitter'))); ?>" id="pronews_twitter" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        				<tr>
        					<th><label for="pronews_rss"><?php _e('RSS URL', $this -> plugin_name); ?></label></th>
        					<td>
        						<input type="text" name="pronews_rss" value="<?php echo esc_attr(stripslashes($this -> get_option('pronews_rss'))); ?>" id="pronews_rss" class="widefat" />
        						<span class="howto"></span>
        					</td>
        				</tr>
        			</tbody>
        		</table>
        	<?php endif; ?>
        <?php endif; ?>
        
        <table class="form-table">
        	<tbody>
        		<tr>
        			<th><label for="Theme_inlinestyles_N"><?php _e('Inline Styles', $this -> plugin_name); ?></label>
        			<?php echo $Html -> help(__('Set this setting to "Yes" to automatically convert all CSS rules into inline, style attributes in the HTML elements. If you use this setting, be sure to create a backup of your original HTML for easier editing later on.', $this -> plugin_name)); ?></th>
        			<td>
        				<label><input onclick="if (!confirm('<?php echo __('Please ensure that you create a local copy/backup of your newsletter theme HTML for editing in the future.', $this -> plugin_name); ?>')) { return false; }" type="radio" name="Theme[inlinestyles]" value="Y" id="Theme_inlinestyles_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
        				<label><input type="radio" checked="checked" name="Theme[inlinestyles]" value="N" id="Theme_inlinestyles_N" /> <?php _e('No', $this -> plugin_name); ?></label>
        				<span class="howto"><?php _e('Convert CSS rules into inline, style attributes on elements.', $this -> plugin_name); ?></span>
        			</td>
        		</tr>
        		<tr>
        			<th><label for="Theme_acolor"><?php _e('Shortcode Link Color', $this -> plugin_name); ?></label>
        			<?php echo $Html -> help(__('Set the color of the links generated from the plugin shortcodes dynamically.', $this -> plugin_name)); ?></th>
        			<td>
        				<fieldset>
        					<legend class="screen-reader-text"><span>Background Color</span></legend>
        					<div class="wp-picker-container">
        						<a tabindex="0" id="acolorbutton" class="wp-color-result" style="background-color:<?php echo $Html -> field_value('Theme[acolor]'); ?>;" title="Select Color"></a>
        						<span class="wp-picker-input-wrap">
        							<input type="text" name="Theme[acolor]" id="Theme_acolor" value="<?php echo $Html -> field_value('Theme[acolor]'); ?>" class="wp-color-picker" style="display: none;" />
        						</span>
        					</div>
						</fieldset>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#Theme_acolor').iris({
								hide: true,
								change: function(event, ui) {
									jQuery('#acolorbutton').css('background-color', ui.color.toString());
								}
							});
							
							jQuery('#Theme_acolor').click(function(event) {
								event.stopPropagation();
							});
						
							jQuery('#acolorbutton').click(function(event) {							
								jQuery(this).attr('title', "Current Color");
								jQuery('#Theme_acolor').iris('toggle').toggle();								
								event.stopPropagation();
							});
							
							jQuery('html').click(function() {
								jQuery('#Theme_acolor').iris('hide').hide();
								jQuery('#acolorbutton').attr('title', "Select Color");
							});
						});
						</script>
						<span class="howto"><?php echo sprintf(__('Control the color of the links generated by shortcodes such as %s, %s, %s, etc.', $this -> plugin_name), '[wpmlonline]', '[wpmlactivate]', '[wpmlunsubscribe]'); ?></span>
        			</td>
        		</tr>
        	</tbody>
        </table>
        
        <p class="submit">
        	<input class="button-primary" type="submit" name="save" value="<?php _e('Save Theme', $this -> plugin_name); ?>" />
        	<span id="newsletters_themeedit_loader" style="display:none;"><span class="newsletters_loading"></span></span>
        </p>
    </form>
</div>

<script type="text/javascript">
function newsletters_save_theme(form) {
	var formvalues = jQuery(form).serialize();
	jQuery('#newsletters_themeedit_loader').show();
	
	jQuery.post(wpmlajaxurl + '?action=newsletters_themeedit&id=<?php echo $_GET['id']; ?>', formvalues, function(response) {
		jQuery('#cboxLoadedContent').html(response);
		jQuery.colorbox.resize();
	});
}

jQuery(document).ready(function() {
	jQuery('[name="Theme[title]"]').Watermark('<?php echo addslashes(__('Enter theme title here', $this -> plugin_name)); ?>');
});
</script>