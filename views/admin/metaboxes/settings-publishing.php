<!-- Posts Configuration -->

<?php
	
$excerpt_settings = $this -> get_option('excerpt_settings');	
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="excerpt_settings"><?php _e('Custom Excerpt Settings', $this -> plugin_name); ?></label>
			<?php echo $Html -> help(__('By turning this on, you can specify your own excerpt length and more text. If you leave it off, the default excerpt length and more text defined by the system, a theme or the plugin will be used.', $this -> plugin_name)); ?></th>
			<td>
				<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#excerpt_settings_div').show(); } else { jQuery('#excerpt_settings_div').hide(); }" <?php echo (!empty($excerpt_settings)) ? 'checked="checked"' : ''; ?> type="checkbox" name="excerpt_settings" value="1" id="excerpt_settings" /> <?php _e('Yes, use custom excerpt length and more text', $this -> plugin_name); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div id="excerpt_settings_div" style="display:<?php echo (!empty($excerpt_settings)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="excerpt_length"><?php _e('Excerpt Length', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('This is the length of the excerpt of posts when inserted into a newsletter. It will be the effective length when you are using the <code>[newsletters_post_excerpt]</code> shortcode for example. The length is in words, not characters.', $this -> plugin_name)); ?></th>
				<td>
					<input type="text" name="excerpt_length" value="<?php echo esc_attr(stripslashes($this -> get_option('excerpt_length'))); ?>" id="excerpt_length" class="widefat" style="width:65px;" /> <?php _e('words', $this -> plugin_name); ?>
					<span class="howto"><?php _e('Length of the excerpt in words.', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="excerpt_more"><?php _e('Excerpt More Text', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Set the text of the "read more" link which is placed at the end of an excerpt. This link is only shown if the length of the content is more then the excerpt length specified above.', $this -> plugin_name)); ?></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<?php
						
						$el = $this -> language_getlanguages();
						$excerpt_more = $this -> language_split($this -> get_option('excerpt_more'));
						
						?>
						<?php if (!empty($el)) : ?>					
							<div id="excerptmoretabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($el as $language) : ?>
					                 	<li><a href="#excerptmoretab<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($el as $language) : ?>
					            	<div id="excerptmoretab<?php echo $tabnumber; ?>">
					            		<input class="widefat" type="text" name="excerpt_more[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($excerpt_more[$language])); ?>" id="excerpt_more_<?php echo $language; ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#excerptmoretabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
						<input type="text" name="excerpt_more" value="<?php echo esc_attr(stripslashes($this -> get_option('excerpt_more'))); ?>" id="excerpt_more" class="widefat" />
					<?php endif; ?>
					<span class="howto"><?php _e('Text to use for the "read more" link of excerpts.', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<p class="howto"><?php _e('When writing a WordPress post, you will see a panel named "Send to Mailing List" which allows you to send a post as a newsletter', $this -> plugin_name); ?></p>

<table class="form-table">
	<tbody>
		<tr>
			<th><?php _e('Full Post or Excerpt', $this -> plugin_name); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('sendonpublishef') == "fp") ? 'checked="checked"' : ''; ?> type="radio" name="sendonpublishef" value="fp" />&nbsp;<?php _e('Full Post', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('sendonpublishef') == "ep") ? 'checked="checked"' : '';; ?> type="radio" name="sendonpublishef" value="ep" />&nbsp;<?php _e('Excerpt of Post', $this -> plugin_name); ?></label>
                
                <span class="howto"><?php _e('Excerpt will be the content before <code>&#60;!--more--&#62;</code>. if it is not available, an excerpt will be automatically generated.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="form-table">
	<tbody>
		<tr>
			<th><?php _e('Include Unsubscribe Link', $this -> plugin_name); ?></th>
			<td>
				<?php $unsubscribe = $this -> get_option('sendonpublishunsubscribe'); ?>
				<?php $unsubscribeCheck1 = ($unsubscribe == "Y") ? 'checked="checked"' : ''; ?>
				<?php $unsubscribeCheck2 = ($unsubscribe == "N") ? 'checked="checked"' : ''; ?>
				<label><input <?php echo $unsubscribeCheck1; ?> type="radio" name="sendonpublishunsubscribe" value="Y" />&nbsp;<?php _e('Yes'); ?></label>
				<label><input <?php echo $unsubscribeCheck2; ?> type="radio" name="sendonpublishunsubscribe" value="N" />&nbsp;<?php _e('No'); ?></label>
			</td>
		</tr>
	</tbody>
</table>