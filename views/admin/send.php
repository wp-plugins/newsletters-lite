<script type="text/javascript">
var contentarea = 1;
</script>

<?php

global $ID, $post, $post_ID, $wp_meta_boxes, $errors;

$imagespost = $this -> get_option('imagespost');
$p_id = (empty($_POST['p_id'])) ? $imagespost : $_POST['p_id'];
$ID = $p_id;
$post_ID = $p_id;

wp_enqueue_media(array('post' => $p_id));

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

?>

<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> send; ?> newsletters">
	<?php if (!empty($_GET['id'])) : ?>
		<h2><?php _e('Edit Newsletter', $this -> plugin_name); ?> <a href="?page=<?php echo $this -> sections -> send; ?>" class="add-new-h2"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php else : ?>
		<h2><?php _e('Create Newsletter', $this -> plugin_name); ?></h2>
	<?php endif; ?>
	<form action="?page=<?php echo $this -> sections -> send; ?>" method="post" id="post" name="post" enctype="multipart/form-data">
		<?php wp_nonce_field($this -> sections -> send); ?>
		<input type="hidden" name="group" value="all" />
		<input type="hidden" id="ishistory" name="ishistory" value="<?php echo $_POST['ishistory']; ?>" />
		<input type="hidden" id="p_id" name="p_id" value="<?php echo $_POST['p_id']; ?>" />
		<input type="hidden" name="inctemplate" value="<?php echo $_POST['inctemplate']; ?>" />
		<input type="hidden" name="recurringsent" value="<?php echo esc_attr(stripslashes($_POST['sendrecurringsent'])); ?>" />
		<input type="hidden" name="post_id" value="<?php echo esc_attr(stripslashes($_POST['post_id'])); ?>" />
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" for="title"></label>
							<input onclick="jQuery('iframe#content_ifr').attr('tabindex', '2');" tabindex="1" id="title" autocomplete="off" type="text" placeholder="<?php echo esc_attr(stripslashes(__('Enter email subject here', $this -> plugin_name))); ?>" name="subject" value="<?php echo esc_attr(stripslashes($_POST['subject'])); ?>" />
						</div>
						<?php if (!empty($errors['subject'])) : ?>
							<p class="<?php echo $this -> pre; ?>error"><?php echo $errors['subject']; ?></p>
						<?php endif; ?>
						<div class="inside">
						<div id="edit-slug-box" class="hide-if-no-js" style="display:<?php echo (!empty($_POST['ishistory'])) ? 'block' : 'none'; ?>;">
							<?php $newsletter_url = $Html -> retainquery($this -> pre . 'method=newsletter&id=' . $_POST['ishistory'], home_url()); ?>
							<strong><?php _e('Permalink:', $this -> plugin_name); ?></strong>
							<span id="sample-permalink" tabindex="-1"><?php echo $newsletter_url; ?></span>
							<span id="view-post-btn"><a href="<?php echo $newsletter_url; ?>" target="_blank" class="button button-small"><?php _e('View Newsletter', $this -> plugin_name); ?></a></span>
							<input id="shortlink" type="hidden" value="<?php echo $newsletter_url; ?>">
							<a href="#" class="button button-small" onclick="prompt('URL:', jQuery('#shortlink').val()); return false;"><?php _e('Get Link', $this -> plugin_name); ?></a></div>
						</div>
					</div>
					<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section" style="position:relative;">
						<!-- The Editor -->
						
						<?php
						
						$setup = "";
						ob_start();
						
						echo "function (ed) {
							/*ed.onChange.add(function (ed, l) {
								jQuery('#previewiframe').contents().find('html div.newsletters_content').html(l.content);
							});*/
							
							ed.on('change', function(e) {
								jQuery('#previewiframe').contents().find('html div.newsletters_content').html(ed.getContent());
							});
						
							//ed.onKeyDown.add(function (ed, evt) {
							ed.on('keydown', function(e) {
				            	//var content = jQuery('iframe#content_ifr').contents().find('body#tinymce').html();
				            	var content = ed.getContent();
				            	jQuery('#previewiframe').contents().find('html div.newsletters_content').html(content);
				            	
								var val = jQuery.trim(content),  
								words = val.replace(/\s+/gi, ' ').split(' ').length,
								chars = val.length;
								if(!chars)words=0;
								
								jQuery('#word-count').html(words + ' " . __('words and', $this -> plugin_name) . " ' + chars + ' " . __('characters', $this -> plugin_name) . "');
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
										<span id="autosave" style="display:none;">
											
										</span>
									</td>
								</tr>
							</tbody>
						</table>
						
						<?php if (!empty($errors['content'])) : ?>
							<p class="<?php echo $this -> pre; ?>error"><?php echo $errors['content']; ?></p>
						<?php endif; ?>
						
						<p>
							<a href="" onclick="addcontentarea(); return false;" id="addcontentarea_button" class="button button-secondary"><?php _e('Add Content Area', $this -> plugin_name); ?></a>
							<span id="contentarea_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
						</p>
						<div id="contentareas">
							<!-- Content Areas Go Here -->
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
var warnMessage = "<?php echo addslashes(__('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', $this -> plugin_name)); ?>";

function deletecontentarea(number, history_id) {
	if (history_id != "") {
		var data = {number:number, history_id:history_id};
		jQuery.post(wpmlajaxurl + '?action=newsletters_deletecontentarea', data, function(response) {
			//all good, the request was successful
			
		});
	} else {
		tinyMCE.execCommand("mceRemoveEditor", false, 'contentarea' + number);
		contentarea--;
	}
	
	jQuery('#contentareabox' + number).remove();
}

function addcontentarea() {	
	jQuery('#addcontentarea_button').disabled = true;
	jQuery('#contentarea_loading').show();
	jQuery.post(wpmlajaxurl + '?action=newsletters_load_new_editor', {contentarea:contentarea}, function(response) {
		jQuery('#contentareas').append(response);
		jQuery('#addcontentarea_button').disabled = false;
		
		if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
			jQuery('#contentarea_loading').hide();
			quicktags({id:'contentarea' + contentarea});
			tinyMCE.execCommand("mceAddEditor", false, 'contentarea' + contentarea);	
			wpml_scroll('#contentareabox' + contentarea);		
			contentarea++;
		}
	});
}

jQuery(document).ready(function() {
	var media = wp.media;

	if ( media ) {

		media.view.MediaFrame.Select.prototype.initialize = function() {

			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection: [],
				library: { 
							uploadedTo: media.view.settings.post.id, 
							orderby: 'menuOrder', 
							order: 'ASC' 
				},
				multiple: false,
				state: 'library'
			});

			this.createSelection();
			this.createStates();
			this.bindHandlers();
		};
		
		media.controller.FeaturedImage.prototype.initialize = function() {

			var library, comparator;

			if ( ! this.get('library') ) {
				this.set( 'library', media.query( { 
												type: 'image', 
												uploadedTo: media.view.settings.post.id, 
												orderby: 'menuOrder', 
												order: 'ASC' 
											} ) );
			}

			media.controller.Library.prototype.initialize.apply( this, arguments );

			library    = this.get('library');
			comparator = library.comparator;

			library.comparator = function( a, b ) {
				var aInQuery = !! this.mirroring.get( a.cid ),
					bInQuery = !! this.mirroring.get( b.cid );

				if ( ! aInQuery && bInQuery ) {
					return -1;
				} else if ( aInQuery && ! bInQuery ) {
					return 1;
				} else {
					return comparator.apply( this, arguments );
				}
			};

			library.observe( this.get('selection') );
		};
		
	}
	
	_wpMediaViewsL10n.insertIntoPost = "<?php _e('Insert into Newsletter', $this -> plugin_name); ?>";
	_wpMediaViewsL10n.uploadedToThisPost = "<?php _e('Uploaded to this Newsletter', $this -> plugin_name); ?>";
	
	jQuery('iframe#content_ifr').attr('tabindex', "2");

    jQuery('input:not(:button,:submit),textarea,select').change(function() {
	    setTimeout(function() {
	    	<?php $createpreview = $this -> get_option('createpreview'); ?>
	    	<?php if (!empty($createpreview) && $createpreview == "Y") : ?>
	    		previewrunner();
	    	<?php endif; ?>
	    	<?php $createspamscore = $this -> get_option('createspamscore'); ?>
	    	<?php if (!empty($createspamscore) && $createspamscore == "Y") : ?>
	    		//spamscorerunner();
	    	<?php endif; ?>
	    }, 3000);
    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    jQuery('input:submit').click(function(e) {
        warnMessage = null;
    });
});
</script>