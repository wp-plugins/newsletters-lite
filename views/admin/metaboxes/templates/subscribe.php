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
			<th><label for="etsubject_subscribe"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabssubscribe">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabsubscribe<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> language_split($this -> get_option('etsubject_subscribe')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabsubscribe<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_subscribe[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($texts[$language])); ?>" id="etsubject_subscribe_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabssubscribe').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_subscribe" value="<?php echo esc_attr(stripslashes($this -> get_option('etsubject_subscribe'))); ?>" id="etsubject_subscribe" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_subscribe"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabssubscribemessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabsubscribemessage<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> language_split($this -> get_option('etmessage_subscribe')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabsubscribemessage<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	true,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_subscribe[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
									);
									
									wp_editor(stripslashes($texts[$language]), 'etmessage_subscribe_' . $language, $settings); 
									
									?>
				            		<?php /*<textarea name="etmessage_subscribe[<?php echo $language; ?>]" id="etmessage_subscribe_<?php echo $language; ?>" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($texts[$language])); ?></textarea>*/ ?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabssubscribemessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	true,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_subscribe',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_subscribe')), 'etmessage_subscribe', $settings); 
					
					?>
					<?php /*wp_editor(stripslashes($this -> get_option('etmessage_subscribe')), 'etmessage_subscribe');*/ ?>
					<?php /*<textarea name="etmessage_subscribe" id="etmessage_subscribe" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($this -> get_option('etmessage_subscribe'))); ?></textarea>*/ ?>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>