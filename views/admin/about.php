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
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
			<div class="col-2">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-2.jpg">
				<h4><?php _e('Easier Editing Tools', $this -> plugin_name); ?></h4>
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
			<div class="col-3 last-feature">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-3.jpg">
				<h4><?php _e('JSON API', $this -> plugin_name); ?></h4>
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
		</div>
		<div class="feature-section col three-col">
			<div class="col-1">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-1.jpg">
				<h4><?php _e('WordPress 3.9 Compatibility', $this -> plugin_name); ?></h4>
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
			<div class="col-2">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-2.jpg">
				<h4><?php _e('Easier Editing Tools', $this -> plugin_name); ?></h4>
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
			<div class="col-3 last-feature">
				<img src="<?php echo $this -> url(); ?>/images/about/feature-3.jpg">
				<h4><?php _e('JSON API', $this -> plugin_name); ?></h4>
				<p>The updated visual editor has improved speed, accessibility, and mobile support. You can paste into the visual editor from your word processor without wasting time to clean up messy styling. (Yeah, we’re talking about you, Microsoft Word.)</p>
			</div>
		</div>
	</div>
	<div class="changelog under-the-hood">
		<h3>Under the Hood</h3>
	
		<div class="feature-section col three-col">
		<div>
		<h4>Semantic Captions and Galleries</h4>
		<p>Theme developers have new options for images and galleries that use intelligent HTML5 markup.</p>
		
		<h4>Inline Code Documentation</h4>
		<p>Every action and filter hook in WordPress is now documented, along with expanded documentation for the media manager and customizer APIs.</p>
		</div>
		<div>
		<h4>External Libraries</h4>
		<p>Updated libraries: TinyMCE&nbsp;4, jQuery&nbsp;1.11, Backbone&nbsp;1.1, Underscore&nbsp;1.6, Plupload&nbsp;2, MediaElement&nbsp;2.14, Masonry&nbsp;3.</p>
		
		<h4>Improved Database Layer</h4>
		<p>Database connections are now more fault-resistant and have improved compatibility with PHP 5.5 and MySQL 5.6.</p>
		</div>
		<div class="last-feature">
		<h4>New Utility Functions</h4>
		<p>Identify a hook in progress with <code>doing_action()</code> and <code>doing_filter()</code>, and manipulate custom image sizes with <code>has_image_size()</code> and <code>remove_image_size()</code>.</p>
		<p>Plugins and themes registering custom image sizes can now register suggested cropping points. For example, prevent heads from being cropped out of photos with a top-center crop.</p>
		</div>
		</div>
		
		<hr>
		
		<div class="return-to-dashboard">
		<a href="http://plugin.wp-mailinglist.wpplugins.biz/wp-admin/">Go to Dashboard → Home</a>
		</div>
	
	</div>
</div>