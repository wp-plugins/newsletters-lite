<?php

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap newsletters about-wrap">
	<h1><?php echo sprintf(__('Upgrade to Newsletters PRO %s', $this -> plugin_name), $this -> version); ?></h1>
	<div class="about-text">
		<?php echo sprintf(__('Thank you for installing the Newsletter plugin. You are using the Newsletters LITE plugin which contains all of the powerful features of the PRO plugin but with some limits. You can upgrade to Newsletters PRO by submitting a serial key. If you do not have a serial key, you can buy one now.', $this -> plugin_name), $this -> version); ?>
	</div>
	<div class="newsletters-badge"><?php echo sprintf(__('Version %s', $this -> plugin_name), $this -> version); ?></div>
	
	<div class="changelog">
		<h3><?php _e('Upgrade to Newsletters PRO', $this -> plugin_name); ?></h3>
		<p><?php _e('Click the button below to upgrade to Newsletters PRO', $this -> plugin_name); ?></p>
		<p>
			<?php $plugin_link = "http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin"; ?>
			<a class="button button-primary button-large" href="<?php echo $plugin_link; ?>" onclick="jQuery.colorbox({iframe:true, width:'90%', height:'90%', href:'<?php echo $plugin_link; ?>'}); return false;"><?php _e('Upgrade to PRO', $this -> plugin_name); ?></a>
		</p>
	</div>
</div>