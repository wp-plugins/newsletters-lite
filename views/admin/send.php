<?php

global $ID, $user_ID, $post, $post_ID, $wp_meta_boxes, $errors;

$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

?>

<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> send; ?>">
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

jQuery(document).ready(function() {
	jQuery('iframe#content_ifr').attr('tabindex', "2");

    jQuery('input:not(:button,:submit),textarea,select').change(function() {
    	previewrunner();
    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    jQuery('input:submit').click(function(e) {
        warnMessage = null;
    });
});
</script>