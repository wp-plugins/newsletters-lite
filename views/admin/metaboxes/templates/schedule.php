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
			<th><label for="etsubject_schedule"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsschedule">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabschedule<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etsubject_schedule'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabschedule<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_schedule[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($this -> language_use($language, $texts))); ?>" id="etsubject_schedule_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsschedule').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_schedule" value="<?php echo esc_attr(stripslashes($this -> get_option('etsubject_schedule'))); ?>" id="etsubject_schedule" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_schedule"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsschedulemessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabschedulemessage<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_schedule'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabschedulemessage<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	true,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_schedule[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(stripslashes($this -> language_use($language, $texts)), 'etmessage_schedule_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsschedulemessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	true,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_schedule',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_schedule')), 'etmessage_schedule', $settings); 
					
					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="ettemplate_schedule"><?php _e('Email Template', $this -> plugin_name); ?></label></th>
			<td>
				<?php $ettemplate_schedule = __($this -> get_option('ettemplate_schedule')); ?>
				<?php if ($themes = $Theme -> select()) : ?>
					<select name="ettemplate_schedule" id="ettemplate_schedule">
						<option value=""><?php _e('- Default -', $this -> plugin_name); ?></option>
						<?php foreach ($themes as $theme_id => $theme_title) : ?>
							<option <?php echo (!empty($ettemplate_schedule) && $ettemplate_schedule == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php _e($theme_title); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p class="newsletters_error"><?php _e('No templates are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>