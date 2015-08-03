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
			<th><label for="etsubject_expire"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsexpire">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabexpire<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etsubject_expire'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabexpire<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_expire[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $texts))); ?>" id="etsubject_expire_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					   		jQuery('#languagetabsexpire').tabs();
					   	}
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_expire" value="<?php echo esc_attr(stripslashes($this -> get_option('etsubject_expire'))); ?>" id="etsubject_expire" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_expire"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsexpiremessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabexpiremessage<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_expire'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabexpiremessage<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	true,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_expire[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(wpautop(stripslashes($this -> language_use($language, $texts))), 'etmessage_expire_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsexpiremessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	true,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_expire',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_expire')), 'etmessage_expire', $settings); 
					
					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="ettemplate_expire"><?php _e('Email Template', $this -> plugin_name); ?></label></th>
			<td>
				<?php $ettemplate_expire = __($this -> get_option('ettemplate_expire')); ?>
				<?php if ($themes = $Theme -> select()) : ?>
					<select name="ettemplate_expire" id="ettemplate_expire">
						<option value=""><?php _e('- Default -', $this -> plugin_name); ?></option>
						<?php foreach ($themes as $theme_id => $theme_title) : ?>
							<option <?php echo (!empty($ettemplate_expire) && $ettemplate_expire == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php _e($theme_title); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p class="newsletters_error"><?php _e('No templates are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>