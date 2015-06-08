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
			<th><label for="etsubject_confirm"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsconfirm">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabconfirm<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etsubject_confirm'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabconfirm<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_confirm[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $texts))); ?>" id="etsubject_confirm_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsconfirm').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_confirm" value="<?php echo esc_attr(stripslashes($this -> get_option('etsubject_confirm'))); ?>" id="etsubject_confirm" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_confirm"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsconfirmmessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabconfirmmessage<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_confirm'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabconfirmmessage<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	true,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_confirm[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(stripslashes($this -> language_use($language, $texts)), 'etmessage_confirm_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    jQuery('#languagetabsconfirmmessage').tabs();
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	true,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_confirm',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_confirm')), 'etmessage_confirm', $settings); 
					
					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="ettemplate_confirm"><?php _e('Email Template', $this -> plugin_name); ?></label></th>
			<td>
				<?php $ettemplate_confirm = __($this -> get_option('ettemplate_confirm')); ?>
				<?php if ($themes = $Theme -> select()) : ?>
					<select name="ettemplate_confirm" id="ettemplate_confirm">
						<option value=""><?php _e('- Default -', $this -> plugin_name); ?></option>
						<?php foreach ($themes as $theme_id => $theme_title) : ?>
							<option <?php echo (!empty($ettemplate_confirm) && $ettemplate_confirm == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php _e($theme_title); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p class="newsletters_error"><?php _e('No templates are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>