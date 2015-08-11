<?php

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

?>

<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Extensions Settings', $this -> plugin_name); ?></h2>
    
    <?php $this -> render('extensions' . DS . 'navigation', false, true, 'admin'); ?>
    
	<form action="?page=<?php echo $this -> sections -> extensions_settings; ?>" method="post" id="settings-form">
		<?php wp_nonce_field($this -> sections -> extensions_settings); ?>
	
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> extensions_settings, 'side', false); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> extensions_settings, 'normal', false); ?>
                    <?php do_meta_boxes("newsletters_page_" . $this -> sections -> extensions_settings, 'advanced', false); ?>
				</div>
			</div>
		</div>
	</form>
</div>