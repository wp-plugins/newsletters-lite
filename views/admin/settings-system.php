<?php

global $ID, $user_ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

?>

<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('System Configuration', $this -> plugin_name); ?></h2>
    <?php $this -> render('settings-navigation', false, true, 'admin'); ?>
	<form action="?page=<?php echo $this -> sections -> settings_system; ?>" method="post">
		<?php wp_nonce_field($this -> sections -> settings); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_system, 'side', false); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_system, 'normal', false); ?>
                    <?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_system, 'advanced', false); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function(){    
    var divOffset = jQuery("#tableofcontentsdiv").offset().top;
	
	jQuery(window).bind("scroll", function() {
	    var offset = jQuery(this).scrollTop();
	
	    if (offset >= divOffset) {
	        jQuery('#tableofcontentsdiv').addClass('fixed');
	    } else if (offset < divOffset) {
	    	jQuery('#tableofcontentsdiv').removeClass('fixed');
	    }
	});
});
</script>