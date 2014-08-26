<?php

global $ID, $post_ID;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

if ($this -> is_plugin_active('qtranslate')) {
	global $q_config;
	$el = qtrans_getSortedLanguages();
}

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="etsubject_schedule"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> is_plugin_active('qtranslate')) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsschedule">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabschedule<?php echo $tabnumber; ?>"><img src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = qtrans_split($this -> get_option('etsubject_schedule')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabschedule<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_schedule[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($texts[$language])); ?>" id="etsubject_schedule_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    jQuery('#languagetabsschedule').tabs();
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
				<?php if ($this -> is_plugin_active('qtranslate')) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsschedulemessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabschedulemessage<?php echo $tabnumber; ?>"><img src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = qtrans_split($this -> get_option('etmessage_schedule')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabschedulemessage<?php echo $tabnumber; ?>">
				            		<textarea name="etmessage_schedule[<?php echo $language; ?>]" id="etmessage_schedule_<?php echo $language; ?>" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($texts[$language])); ?></textarea>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    jQuery('#languagetabsschedulemessage').tabs();
				    });
				    </script>
				<?php else : ?>
					<?php wp_editor(stripslashes($this -> get_option('etmessage_schedule')), 'etmessage_schedule'); ?>
					<?php /*<textarea name="etmessage_schedule" id="etmessage_schedule" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($this -> get_option('etmessage_schedule'))); ?></textarea>*/ ?>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>