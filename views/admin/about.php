<?php
/**
 * Newsletters About Dashboard v4.5
 */

?>

<div class="wrap newsletters about-wrap">
	<h1><?php echo sprintf( 'Welcome to Tribulant Newsletters %s', $this -> version); ?></h1>
	<div class="about-text">
		<?php echo sprintf('Thank you for installing! Tribulant Newsletters %s is more powerful, reliable and versatile than before. It includes many features and improvements to make email marketing easier and more efficient for you.', $this -> version); ?>
	</div>
	<div class="newsletters-badge">
		<div>
			<i class="fa fa-envelope fa-fw" style="font-size: 72px !important; color: white;"></i>
		</div>
		<?php echo sprintf('Version %s', $this -> version); ?>
	</div>
	
	<div class="changelog">
		<h3><?php echo  'What\'s new in this release'; ?></h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-1.png">
				<h4><?php echo 'WordPress 4.0 Compatibility'; ?></h4>
				<p><?php echo 'This version is 100% compatible with the latest WordPress version. It will fit nicely into your WordPress dashboard and maximizes the WordPress capabilities for speed, functionality and reliability.'; ?></p>
			</div>
			<div class="col-2">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-2.png">
				<h4><?php echo 'Redesigned Interface'; ?></h4>
				<p><?php echo 'Both the front-end and the admin dashboard interfaces have been redesigned with new buttons, icons, beautiful charts and amazing illustrations of data.'; ?></p>
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
			<img src="<?php echo $this -> url(); ?>/images/about/feature-4.png">			
			<h4>Bounces Section</h4> 
			<p>The bounces section will show you bounces as they happen with a status code, reason, newsletter bounced on and much more.</p>
		</div>
		<div class="col-2 last-feature">
			<img src="<?php echo $this -> url(); ?>/images/about/feature-5.png">
			<h4>Media Files Per Newsletter</h4>
			<p>Media files are now linked to and separated by each newsletter individually so that you can easily find your files and images when access a newsletter again.</p>
		</div>
	</div>

	<hr>
	
	<div class="changelog under-the-hood">
		<h3>New Extension Plugins</h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<h4>Digital Access Pass</h4>
				<a href="http://tribulant.com/extensions/view/43/digital-access-pass"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/digital-access-pass.png'); ?>" alt="digital-access-pass" /></a>
				<p><?php echo 'This extension plugin allows you to capture Newsletter plugin subscribers from your 3rd party Digital Access Pass application/platform. As customers purchase products/memberships through Digital Access Pass, they will be added as subscribers.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/43/digital-access-pass" class="button button button-primary">Digital Access Pass</a></p>
			</div>
			<div class="col-2">
				<h4>WooCommerce Subscribers</h4>
				<a href="http://tribulant.com/extensions/view/42/woocommerce-subscribers"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/woocommerce-subscribers.png'); ?>" alt="woocommerce-subscribers" /></a>
				<p><?php echo 'WooCommerce Subscribers is a free extension plugin to capture email subscribers from your WooCommerce plugin checkout procedure into your Newsletter plugin.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/42/woocommerce-subscribers" class="button button button-primary">WooCommerce Subscribers</a></p>
			</div>
			<div class="col-3 last-feature">
				<h4>Google Analytics</h4>
				<a href="http://tribulant.com/extensions/view/46/google-analytics"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/google-analytics.png'); ?>" alt="google-analytics" /></a>
				<p><?php echo 'Google Analytics helps you analyze visitor traffic and paint a complete picture of your audience and their needs, wherever they are along the path.'; ?></p>
				<p> <a href="http://tribulant.com/extensions/view/46/google-analytics" class="button button button-primary">Google Analytics</a></p>
			</div>
		</div>
	</div>
	
	<div class="changelog under-the-hood">
		<h3>New Newsletter Templates</h3>
		<div class="feature-section col three-col">
			<div class="col-1">
				<h4>Magazine</h4>
				<a href="http://tribulant.com/emailthemes/view/3/magazine-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/news-theme-magazine.jpg'); ?>" alt="magazine" /></a>
				<p>The ideal newsletter template for content rich websites. Display content in multiple content areas with a sidebar as well. Fully responsive with media queries and fluid design.</p>
				<p><a href="http://tribulant.com/emailthemes/view/3/magazine-newsletter-template" class="button button button-primary">Magazine</a></p>
			</div>
			<div class="col-2">
				<h4>Simple Business</h4>
				<a href="http://tribulant.com/emailthemes/view/1/simple-business-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/news-theme-simple-business.jpg'); ?>" alt="simple-business" /></a>
				<p>The perfect newsletter theme for your business, whether you want to promote your products and pages or simply update your clients. Fully responsive, fluid and media query versions available.</p>
				<p><a href="http://tribulant.com/emailthemes/view/1/simple-business-newsletter-template" class="button button button-primary">Simple Business</a></p>
			</div>
			<div class="col-3 last-feature">
				<h4>Easy Shop</h4>
				<a href="http://tribulant.com/emailthemes/view/2/easy-shop-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo $this -> render_url('images/about/news-theme-easy-shop.jpg'); ?>" alt="easy-shop" /></a>
				<p>Market and showcase your products in a beautifully elegant eCommerce newsletter template. This professional email theme is fully responsive and created for shop owners.</p>
				<p><a href="http://tribulant.com/emailthemes/view/2/easy-shop-newsletter-template" class="button button button-primary">Easy Shop</a></p>
			</div>
		</div>
	</div>
	
	
	<div class="changelog under-the-hood">
		<h3>Under the Hood</h3>
	
		<div class="feature-section col three-col">
			<div>
				<h4>New Default Template for System Emails</h4>
				<p>System emails are now beautifully styled by default without any template configured on them.</p>		
				
				<h4>Number of Columns in Dashboard</h4>
				<p>The old, missing screen layout number of columns feature is back on the WordPress dashboard, enjoy!</p>
			</div>
			<div>
				<h4>New ColorBox Theme</h4>
				<p>We have redesigned the ColorBox integration to make it simple, flat and appealing.</p>	
				
				<h4>New Icons Throughout</h4>
				<p>A completely new icon set has been implemented throughout the plugin, not dashicons anymore.</p>
			</div>
			<div class="last-feature">
				<h4>Line/Bar Charts</h4>
				<p>Choose whether you prefer line or bar charts and the plugin will remember your selection in all sections.</p>
				
				<h4>Template per System Email</h4>
				<p>You can now set a template per system email individually instead of just one, global template for all system emails.</p>
			</div>
		</div>
		
		<hr>
		
		<div class="return-to-dashboard">
			<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> welcome); ?>">Go to Newsletters Overview <i class="fa fa-arrow-right"></i></a>
		</div>
	
	</div>
</div>