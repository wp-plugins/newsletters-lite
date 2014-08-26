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
			<th><label for="etsubject_confirm"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> is_plugin_active('qtranslate')) : ?>				    
				    <?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsconfirm">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabconfirm<?php echo $tabnumber; ?>"><img src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = qtrans_split($this -> get_option('etsubject_confirm')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabconfirm<?php echo $tabnumber; ?>">
				            		<input type="text" name="etsubject_confirm[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($texts[$language])); ?>" id="etsubject_confirm_<?php echo $language; ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    jQuery('#languagetabsconfirm').tabs();
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
				<?php if ($this -> is_plugin_active('qtranslate')) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabsconfirmmessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetabconfirmmessage<?php echo $tabnumber; ?>"><img src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = qtrans_split($this -> get_option('etmessage_confirm')); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetabconfirmmessage<?php echo $tabnumber; ?>">
				            		<textarea name="etmessage_confirm[<?php echo $language; ?>]" id="etmessage_confirm_<?php echo $language; ?>" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($texts[$language])); ?></textarea>
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
					<?php wp_editor(stripslashes($this -> get_option('etmessage_confirm')), 'etmessage_confirm'); ?>
					<?php /*<textarea name="etmessage_confirm" id="etmessage_confirm" class="widefat" cols="100%" rows="10"><?php echo esc_attr(stripslashes($this -> get_option('etmessage_confirm'))); ?></textarea>*/ ?>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>