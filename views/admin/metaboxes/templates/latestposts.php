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
			<th><label for="etmessage_latestposts"><?php _e('Email Message', $this -> plugin_name); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($el) && is_array($el)) : ?>
				    	<div id="languagetabslatestposts">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($el as $language) : ?>
				                 	<li><a href="#languagetablatestposts<?php echo $tabnumber; ?>"><?php echo $this -> language_flag($language); ?></a></li>   
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_latestposts'); ?>
				            <?php foreach ($el as $language) : ?>
				            	<div id="languagetablatestposts<?php echo $tabnumber; ?>">
					            	<?php 
					
									$settings = array(
										//'wpautop'			=>	false,
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_latestposts[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	true,
									);
									
									wp_editor(wpautop(stripslashes($this -> language_use($language, $texts))), 'etmessage_latestposts_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabslatestposts').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						//'wpautop'			=>	false,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_latestposts',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	true,
					);
					
					wp_editor(stripslashes($this -> get_option('etmessage_latestposts')), 'etmessage_latestposts', $settings); 
					
					?>
				<?php endif; ?>
				
				<div class="howto">
					<strong><?php _e('Shortcode Information', $this -> plugin_name); ?></strong><br/>
					<code>[newsletters_post_loop]...[/newsletters_post_loop]</code> <?php _e('The posts loop. Use the codes below inside.', $this -> plugin_name); ?><br/>
					<code>[newsletters_category_heading]</code> <?php _e('Category heading and link if using group by category.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_id]</code> <?php _e('The ID of the post.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_author]</code> <?php _e('The display name of the author.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_title]</code> <?php _e('The title of the post.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_link]</code> <?php _e('The URL of the post.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_date_wrapper]</code> <?php _e('A wrapper for the date, simply to work with the "showdate" parameter in the shortcode.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_date format="F jS, Y"]</code> <?php _e('The date of the post with an optional "format" parameter.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_thumbnail size="thumbnail"]</code> <?php _e('The thumbnail (if any) of the post with an optional "size" parameter.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_excerpt]</code> <?php _e('The excerpt of the post taken from the content.', $this -> plugin_name); ?><br/>
					<code>[newsletters_post_content]</code> <?php _e('The full content of the post as published.', $this -> plugin_name); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>