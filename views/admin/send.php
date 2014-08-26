<script type="text/javascript">
var contentarea = 1;
</script>

<?php

global $ID, $user_ID, $post, $post_ID, $wp_meta_boxes, $errors;

$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

?>

<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> send; ?> newsletters">
	<h2><?php _e('Create Newsletter', $this -> plugin_name); ?></h2>
	<form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> send; ?>" method="post" id="post" name="post" enctype="multipart/form-data">
		<?php wp_nonce_field($this -> sections -> send); ?>
		<input type="hidden" name="group" value="all" />
		<input type="hidden" id="ishistory" name="ishistory" value="<?php echo $_POST['ishistory']; ?>" />
		<input type="hidden" name="inctemplate" value="<?php echo $_POST['inctemplate']; ?>" />
		<input type="hidden" name="recurringsent" value="<?php echo esc_attr(stripslashes($_POST['sendrecurringsent'])); ?>" />
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" for="title"></label>
							<input onclick="jQuery('iframe#content_ifr').attr('tabindex', '2');" tabindex="1" id="title" autocomplete="off" type="text" name="subject" value="<?php echo esc_attr(stripslashes($_POST['subject'])); ?>" />
						</div>
						<?php if (!empty($errors['subject'])) : ?>
							<p class="<?php echo $this -> pre; ?>error"><?php echo $errors['subject']; ?></p>
						<?php endif; ?>
					</div>
					<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section" style="position:relative;">
						<!-- The Editor -->
						
						<?php
						
						$setup = "";
						ob_start();
						
						echo "function (ed) {
							ed.onChange.add(function (ed, l) {
								jQuery('#previewiframe').contents().find('html div.newsletters_content').html(l.content);
							});
						
							ed.onKeyDown.add(function (ed, evt) {
				            	var content = jQuery('iframe#content_ifr').contents().find('body#tinymce').html();
				            	jQuery('#previewiframe').contents().find('html div.newsletters_content').html(content);
				            });
						}";
						
						$setup = ob_get_clean();
						
						$tinymce = array('setup' => $setup);
						
						?>
						
						<?php if (version_compare(get_bloginfo('version'), "3.3") >= 0) : ?>
							<?php wp_editor(stripslashes($_POST['content']), 'content', array('tabindex' => "2", 'tinymce' => $tinymce)); ?>
						<?php else : ?>
							<?php the_editor(stripslashes($_POST['content']), 'content', 'title', true, 2); ?>
						<?php endif; ?>
						
						<table id="post-status-info" cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
									<td id="wp-word-count">
										<?php _e('Word Count:', $this -> plugin_name); ?>
										<span id="word-count">0</span>
									</td>
									<td class="autosave-info">
										<span id="autosave" style="display:none;"></span>
									</td>
								</tr>
							</tbody>
						</table>
						
						<?php if (!empty($errors['content'])) : ?>
							<p class="<?php echo $this -> pre; ?>error"><?php echo $errors['content']; ?></p>
						<?php endif; ?>
						
						<p>
							<a href="" onclick="addcontentarea(); return false;" class="button button-secondary"><?php _e('Add Content Area', $this -> plugin_name); ?></a>
							<span id="contentarea_loading" style="display:none;"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" /></span>
						</p>
						<div id="contentareas">
							<?php

							if (!empty($_POST['ishistory'])) {
								$history_id = $_POST['ishistory'];
								if ($contentareas = $this -> Content -> find_all(array('history_id' => $history_id), false, array('number', "ASC"))) {
									foreach ($contentareas as $contentarea) {
										?>
										
										<div class="postbox" id="contentareabox<?php echo $contentarea -> number; ?>">
											<div class="handlediv" title="Click to toggle"><br></div>
											<h3 class="hndle"><span><?php echo sprintf(__('Content Area %s', $this -> plugin_name), $contentarea -> number); ?></span></h3>
											<div class="inside">
												<?php
												
												$settings = array(
													'wpautop'			=>	false,
													'media_buttons'		=>	true,
													'textarea_name'		=>	'contentarea[' . $contentarea -> number . ']',
													'textarea_rows'		=>	10,
													'quicktags'			=>	true,
												);
												
												wp_editor(stripslashes($contentarea -> content), 'contentarea' . $contentarea -> number, $settings); 
												
												?>
												<table id="post-status-info" cellpadding="0" cellspacing="0">
													<tbody>
														<tr>
															<td id="wp-word-count">
																<span id="word-count"><?php echo sprintf(__('Use shortcode %s to display this content', $this -> plugin_name), '<code>[newsletters_content id="' . $contentarea -> number . '"]</code>'); ?></span>
															</td>
															<td class="autosave-info">
																<span id="autosave" style="display:none;"></span>
															</td>
														</tr>
													</tbody>
												</table>
												
												<p>
													<a href="" onclick="if (confirm('<?php echo __('Are you sure you want to remove this content area?', $this -> plugin_name); ?>')) { deletecontentarea('<?php echo $contentarea -> number; ?>', '<?php echo $contentarea -> history_id; ?>'); } return false;" class="button button-secondary"><?php _e('Delete', $this -> plugin_name); ?></a>
												</p>
											</div>
										</div>
										<script type="text/javascript">
										contentarea++;
										</script>
										
										<?php
									}
								}
							}
							
							?>
						</div>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> send, 'side', $post); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> send, 'normal', $post); ?>
                    <?php do_meta_boxes("newsletters_page_" . $this -> sections -> send, 'advanced', $post); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#title').Watermark('<?php echo addslashes(__('Enter email subject here', $this -> plugin_name)); ?>');
	jQuery('#daterangefrom').Watermark('eg. <?php echo date_i18n("Y-m-d", strtotime("-1 month")); ?>');
	jQuery('#daterangeto').Watermark('eg. <?php echo date_i18n("Y-m-d", time()); ?>');
});

var warnMessage = "<?php echo addslashes(__('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name)); ?>";

function deletecontentarea(number, history_id) {
	if (history_id != "") {
		var data = {number:number, history_id:history_id};
		jQuery.post(wpmlajaxurl + '?action=newsletters_deletecontentarea', data, function(response) {
			//all good, the request was successful
		});
	} else {
		tinyMCE.execCommand("mceRemoveControl", false, 'contentarea' + number);
		contentarea--;
	}
	
	jQuery('#contentareabox' + number).remove();
}

function addcontentarea() {
	/*var data = {contentarea:contentarea};
	jQuery('#contentarea_loading').show();
	jQuery.post(wpmlajaxurl + '?action=newsletters_addcontentarea', data, function(response) {
		jQuery('#contentarea_loading').hide();
		jQuery('#contentareas').append(response);
		tinyMCE.execCommand('mceAddControl', false, 'contentarea' + contentarea);
	});*/
	
	var contentarea_html = '';
	contentarea_html += '<div class="postbox" id="contentareabox' + contentarea + '">';
	contentarea_html += '<div class="handlediv" title="Click to toggle"><br></div>';
	contentarea_html += '<h3 class="hndle"><span><?php echo __('Content Area', $this -> plugin_name); ?> ' + contentarea + '</span></h3>';
	contentarea_html += '<div class="inside">';
	contentarea_html += '<textarea id="contentarea' + contentarea + '" name="contentarea[' + contentarea + ']"></textarea>';
	contentarea_html += '<table id="post-status-info" cellpadding="0" cellspacing="0">';
	contentarea_html += '<tbody>';
	contentarea_html += '<tr>';
	contentarea_html += '<td id="wp-word-count">';
	contentarea_html += '<span id="word-count"><code>[newsletters_content id="' + contentarea + '"]</code></span>';
	contentarea_html += '</td>';
	contentarea_html += '<td class="autosave-info">';
	contentarea_html += '<span id="autosave" style="display:none;"></span>';
	contentarea_html += '</td>';
	contentarea_html += '</tr>';
	contentarea_html += '</tbody>';
	contentarea_html += '</table>';
	contentarea_html += '<p><a href="" onclick="if (confirm(\'<?php echo __('Are you sure you want to remove this content area?', $this -> plugin_name); ?>\')) { deletecontentarea(' + contentarea + ', \'\'); } return false;" class="button button-secondary"><?php _e('Delete', $this -> plugin_name); ?></a></p>';
	contentarea_html += '</div>';
	contentarea_html += '</div>';
	
	jQuery('#contentareas').append(contentarea_html);
	wpml_scroll('#contentareabox' + contentarea);
	
	if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
		tinyMCE.execCommand("mceAddControl", false, 'contentarea' + contentarea);
	}
		
	contentarea++;
}

jQuery(document).ready(function() {
	jQuery('iframe#content_ifr').attr('tabindex', "2");

    jQuery('input:not(:button,:submit),textarea,select').change(function() {
    	previewrunner();
    	spamscorerunner();
    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    jQuery('input:submit').click(function(e) {
        warnMessage = null;
    });
});
</script>