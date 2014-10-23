<?php
/**
 * Newsletters About Dashboard v4.3.6
 */

?>

<div class="wrap newsletters about-wrap">
	<h1><?php echo sprintf( 'Welcome to Tribulant Newsletters %s', $this -> version); ?></h1>
	<div class="about-text">
		<?php echo sprintf('Thank you for installing! Tribulant Newsletters %s is more powerful, reliable and versatile than before. It includes many features and improvements to make email marketing easier and more efficient for you.', $this -> version); ?>
	</div>
	<div class="newsletters-badge"><?php echo sprintf('Version %s', $this -> version); ?></div>
	
	<div class="changelog">
		<h3><?php echo  'What\'s new in this release'; ?></h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-1.png">
				<h4><?php echo 'WordPress 4.0 Compatibility'; ?></h4>
				<p><?php echo 'This version is 100% compatible with the latest WordPress version. It will fit nicely into your WordPress dashboard and maximizes the WordPress capabilities for speed, functionality and reliability.'; ?></p>
			</div>
			<div class="col-2">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-4.png">
				<h4><?php echo 'WPML integration'; ?></h4>
				<p><?php echo 'This version of the Newsletter plugin is fully integrated with WPML. It now supports internationalization and multilanguage through WPML.'; ?></p>
			</div>
			<div class="col-3 last-feature">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-3.jpg">
				<h4><?php echo 'JSON API'; ?></h4>
				<p><?php echo 'The JSON API is a direct bridge and communication channel between any code or application and the Newsletter plugin itself, including it\'s database.'; ?></p>
			</div>
		</div>
	</div>
	
	<hr>

	<div class="feature-section col two-col">
		<div class="col-1">
			<img src="<?php echo $this -> url(); ?>/images/about/feature-7.png">			
			<h4><?php echo 'Clicks section in admin'; ?></h4> 
			<p><?php echo 'A new clicks section in admin to view and manage all clicks on links. You can view all links and clicks per individual subscriber and all clicks per link.'; ?></p>
		</div>
		<div class="col-2 last-feature">
			<img src="<?php echo $this -> url(); ?>/images/about/feature-8.png">
			<h4><?php echo 'Filter Subscribers'; ?></h4>
			<p><?php echo 'You can now filter subscribers per mailinglist, status and registered status. After filtering your subscribers, you can activate or deactivate them, assign them to another mailinglist or delete them.'; ?></p>
		</div>
	</div>

	<hr>
	
	<div class="changelog under-the-hood">
		<h3><?php echo  'New Extensions'; ?></h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<h4><?php echo 'Digital Access Pass'; ?></h4>
				<p><?php echo 'This extension plugin allows you to capture Newsletter plugin subscribers from your 3rd party Digital Access Pass application/platform. As customers purchase products/memberships through Digital Access Pass, they will be added as subscribers.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/43/digital-access-pass" class="button button button-primary">Digital Access Pass</a></p>
			</div>
			<div class="col-2">
				<h4><?php echo 'WooCommerce Subscribers'; ?></h4>
				<p><?php echo 'WooCommerce Subscribers is a free extension plugin to capture email subscribers from your WooCommerce plugin checkout procedure into your Newsletter plugin.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/42/woocommerce-subscribers" class="button button button-primary">WooCommerce Subscribers</a></p>
			</div>
			<div class="col-3 last-feature">
				<h4><?php echo 'Google Analytics'; ?></h4>
				<p><?php echo 'Google Analytics helps you analyze visitor traffic and paint a complete picture of your audience and their needs, wherever they are along the path.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/46/google-analytics" class="button button button-primary">Google Analytics</a></p>
			</div>
		</div>
	</div>
	<div class="changelog under-the-hood">
		<h3>Under the Hood</h3>
	
		<div class="feature-section col three-col">
		<div>
		<h4><?php echo 'Auto import users'; ?></h4>
		<p><?php echo 'The new auto import user function prevent unsubscribe/bounce emails and users from importing again.'; ?></p>		
		<h4><?php echo 'WordPress Object Cache API'; ?></h4>
		<p><?php echo 'peed up the plugin with the WordPress Object Cache API which is now built in to cache queries through the WordPress database object.'; ?></p>
		</div>
		<div>
		<h4><?php echo 'Edit themes directly while creating a newsletter'; ?></h4>
		<p><?php echo 'You can now edit a theme directly as you are busy creating a newsletter.'; ?></p>
		
		<h4><?php echo 'Paid subscriptions immediate payment'; ?></h4>
		<p><?php echo 'This function allow paid subscribers to pay immediately for a subscription and not being redirect and ask to confirm on email.'; ?></p>
		</div>
		<div class="last-feature">
		<h4><?php echo 'Auto save information while creating a newsletter'; ?></h4>
		<p><?php echo 'Auto save information inserted into a newsletter while busy creating it.'; ?></p>
		
		<h4><?php echo 'More Hooks'; ?></h4>
		<p><?php echo 'We\'ve added more action and filter hooks to the core of the plugin which can be used by developers to integrate their own apps or 3rd party plugins as needed. If you need specific hooks, please contact us and we\'ll add it.'; ?></p>
		</div>
		
		</div>
		
		<hr>
		
		<div class="return-to-dashboard">
		<a href="<?php echo admin_url('admin.php'); ?>?page=newsletters"><?php echo 'Go to Newsletters overview'; ?></a>
		</div>
	
	</div>
</div>