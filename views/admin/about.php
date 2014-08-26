<?php
/**
 * Newsletters About Dashboard
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap about-wrap">
	<h1><?php echo sprintf(__( 'Welcome to Newsletters %s', $this -> plugin_name), $this -> version); ?></h1>
	<div class="about-text">
		<?php echo sprintf(__('Thank you for installing! Newsletters %s is more powerful, reliable and versatile than before. It includes many features and improvements to make email marketing easier and more efficient for you.', $this -> plugin_name), $this -> version); ?>
	</div>
	<div class="newsletters-badge"><?php echo sprintf(__('Version %s', $this -> plugin_name), $this -> version); ?></div>
	
	<div class="changelog">
		<h3><?php _e( 'What\'s new in this release', $this -> plugin_name); ?></h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-1.jpg">
				<h4><?php _e('WordPress 3.9 Compatibility', $this -> plugin_name); ?></h4>
				<p><?php _e('This version is 100% compatible with the latest WordPress version. It will fit nicely into your WordPress dashboard and maximizes the WordPress capabilities for speed, functionality and reliability.', $this -> plugin_name); ?></p>
			</div>
			<div class="col-2">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-2.jpg">
				<h4><?php _e('Easier Editing Tools', $this -> plugin_name); ?></h4>
				<p><?php _e('Improved visual editing of themes and several other areas throughout the plugin to help you see better what the end-product will look like without having to necessarily go into code.', $this -> plugin_name); ?></p>
			</div>
			<div class="col-3 last-feature">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-3.jpg">
				<h4><?php _e('JSON API', $this -> plugin_name); ?></h4>
				<p><?php _e('The JSON API is a direct bridge and communication channel between any code or application and the Newsletter plugin itself, including it\'s database.', $this -> plugin_name); ?></p>
			</div>
		</div>
	</div>
	
	<hr>

	<div class="feature-section col two-col">
		<div class="col-1">
			<img src="<?php echo $this -> url(); ?>/images/about/feature-5.png">			
			<h4><?php _e('Spam Score', $this -> plugin_name); ?></h4>
			<p><?php _e('Prevent your emails from ending up in spam/junk folder unnecessarily. The spam score utility will show on each newsletter you create to give the newsletter a score, taking all it\'s aspects and factors in consideration.', $this -> plugin_name); ?></p>
		</div>
		<div class="col-2 last-feature">
						<img src="<?php echo $this -> url(); ?>/images/about/feature-6.png">
						<h4><?php _e('Sorry to see you go email', $this -> plugin_name); ?></h4>
			<p><?php _e('The plugin can automatically send a "Sorry to see you go..." email to a user when they unsubscribe to both confirm their subscription, express your disappointment that they are leaving and it also includes a resubscribe link to convert.', $this -> plugin_name); ?></p>
		</div>
</div>

<hr>
	
		<div class="changelog under-the-hood">
		<h3><?php _e( 'New Extensions', $this -> plugin_name); ?></h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<h4><?php _e('Digital Access Pass', $this -> plugin_name); ?></h4>
				<p><?php _e('This extension plugin allows you to capture Newsletter plugin subscribers from your 3rd party Digital Access Pass application/platform. As customers purchase products/memberships through Digital Access Pass, they will be added as subscribers.', $this -> plugin_name); ?></p>
				<p> <a href="http://tribulant.com/extensions/view/43/digital-access-pass" class="button button button-primary">Digital Access Pass</a></p>
			</div>
			<div class="col-2">
				<h4><?php _e('WooCommerce Subscribers', $this -> plugin_name); ?></h4>
				<p><?php _e('WooCommerce Subscribers is a free extension plugin to capture email subscribers from your WooCommerce plugin checkout procedure into your Newsletter plugin.', $this -> plugin_name); ?></p>
				<p> <a href="http://tribulant.com/extensions/view/42/woocommerce-subscribers" class="button button button-primary">WooCommerce Subscribers</a></p>
			</div>
			<div class="col-3 last-feature">
				<h4><?php _e('Google Analytics', $this -> plugin_name); ?></h4>
				<p><?php _e('Google Analytics helps you analyze visitor traffic and paint a complete picture of your audience and their needs, wherever they are along the path.', $this -> plugin_name); ?></p>
				<p> <a href="http://tribulant.com/extensions/view/45/google-analytics" class="button button button-primary">Google Analytics</a></p>
			</div>
		</div>
	</div>
	<div class="changelog under-the-hood">
		<h3>Under the Hood</h3>
	
		<div class="feature-section col three-col">
		<div>
		<h4><?php _e('Auto import users', $this -> plugin_name); ?></h4>
		<p><?php _e('The new auto import user function prevent unsubscribe/bounce emails and users from importing again.', $this -> plugin_name); ?></p>		
		<h4><?php _e('WordPress Object Cache API', $this -> plugin_name); ?></h4>
		<p><?php _e('peed up the plugin with the WordPress Object Cache API which is now built in to cache queries through the WordPress database object.', $this -> plugin_name); ?></p>
		</div>
		<div>
		<h4><?php _e('Edit themes directly while creating a newsletter', $this -> plugin_name); ?></h4>
		<p><?php _e('You can now edit a theme directly as you are busy creating a newsletter.', $this -> plugin_name); ?></p>
		
		<h4><?php _e('Paid subscriptions immediate payment', $this -> plugin_name); ?></h4>
		<p><?php _e('This function allow paid subscribers to pay immediately for a subscription and not being redirect and ask to confirm on email.', $this -> plugin_name); ?></p>
		</div>
		<div class="last-feature">
		<h4><?php _e('Auto save information while creating a newsletter', $this -> plugin_name); ?></h4>
		<p><?php _e('Auto save information inserted into a newsletter while busy creating it.', $this -> plugin_name); ?></p>
		
		<h4><?php _e('More Hooks', $this -> plugin_name); ?></h4>
		<p><?php _e('We\'ve added more action and filter hooks to the core of the plugin which can be used by developers to integrate their own apps or 3rd party plugins as needed. If you need specific hooks, please contact us and we\'ll add it.', $this -> plugin_name); ?></p>
		</div>
		
		</div>
		
		<hr>
		
		<div class="return-to-dashboard">
		<a href="<?php echo admin_url('admin.php'); ?>?page=newsletters"><?php _e('Go to Newsletters overview', $this -> plugin_name); ?></a>
		</div>
	
	</div>
</div>