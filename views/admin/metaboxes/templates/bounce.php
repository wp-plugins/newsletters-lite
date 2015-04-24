<?php

global $ID, $post_ID;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

if ($this -> language_do()) {
	$el = $this -> language_getlanguages();
}

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="etsubject_bounce"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsbounce">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabbounce<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etsubject_bounce'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabbounce<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_bounce[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $texts))); ?>" id="etsubject_bounce_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsbounce').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_bounce" value="<?php echo esc_attr(stripslashes($this -> get_option('etsubject_bounce'))); ?>" id="etsubject_bounce" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_bounce"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsbouncemessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabbouncemessage<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_bounce'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabbouncemessage<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	true,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_bounce[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(stripslashes($this -> language_use($language, $texts)), 'etmessage_bounce_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsbouncemessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	true,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_bounce',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_bounce')), 'etmessage_bounce', $settings); 
					
					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="ettemplate_bounce"><?php _e('Email Template', $this -> plugin_name); ?></label></th>
			<td>
				<?php $ettemplate_bounce = __($this -> get_option('ettemplate_bounce')); ?>
				<?php if ($themes = $Theme -> select()) : ?>
					<select name="ettemplate_bounce" id="ettemplate_bounce">
						<option value=""><?php _e('- None -', $this -> plugin_name); ?></option>
						<?php foreach ($themes as $theme_id => $theme_title) : ?>
							<option <?php echo (!empty($ettemplate_bounce) && $ettemplate_bounce == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php _e($theme_title); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p class="newsletters_error"><?php _e('No templates are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>