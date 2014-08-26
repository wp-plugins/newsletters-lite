<?php

global $ID, $user_ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

?>

<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> welcome; ?> newsletters">
	<h2><?php echo sprintf(__('Newsletters %s', $this -> plugin_name), $this -> get_option('version')); ?></h2>    
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<?php do_action('submitpage_box'); ?>
				<?php do_meta_boxes("newsletters_page_" . $this -> sections -> welcome, 'side', false); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes("newsletters_page_" . $this -> sections -> welcome, 'normal', false); ?>
                <?php do_meta_boxes("newsletters_page_" . $this -> sections -> welcome, 'advanced', false); ?>
			</div>
		</div>
	</div>
</div>