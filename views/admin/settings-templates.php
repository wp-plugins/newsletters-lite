<?php

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

?>

<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('System Emails Configuration', $this -> plugin_name); ?></h2>
    <p>
    	<?php _e('System emails are messages sent as notifications to users/admins on events.', $this -> plugin_name); ?><br/>
    	<?php _e('You can configure each email template individually according to your needs.', $this -> plugin_name); ?><br/>
    	<?php _e('You may use any of the', $this -> plugin_name); ?> <a class="button button-secondary" title="<?php _e('Shortcodes/Variables', $this -> plugin_name); ?>" href="" onclick="jQuery.colorbox({title:'<?php _e('Shortcodes/Variables', $this -> plugin_name); ?>', maxHeight:'80%', maxWidth:'80%', href:'<?php echo admin_url('admin-ajax.php'); ?>?action=<?php echo $this -> pre; ?>setvariables'}); return false;"> <?php _e('shortcodes/variables', $this -> plugin_name); ?></a> <?php _e('inside the subjects/messages of system emails.', $this -> plugin_name); ?><br/>
    	<?php _e('Each template is inserted where the <code>[wpmlcontent]</code> tag is in the default template chosen under Newsletters > Templates.', $this -> plugin_name); ?>
    </p>
	<form action="?page=<?php echo $this -> sections -> settings_templates; ?>" method="post">
		<?php $this -> render('settings-navigation', array('tableofcontents' => "tableofcontents-templates"), true, 'admin'); ?>
		<?php wp_nonce_field($this -> sections -> settings); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_templates, 'side', false); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_templates, 'high', false); ?>
					<?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_templates, 'normal', false); ?>
                    <?php do_meta_boxes("newsletters_page_" . $this -> sections -> settings_templates, 'advanced', false); ?>
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