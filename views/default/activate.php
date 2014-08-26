<div align="center">
	<h1 id="logo"><img alt="WPMailingList" src="<?php echo $this -> url(); ?>/images/wpmailinglist.jpg" /></h1>
	
	<h2><?php _e('Activation Confirmation', $this -> plugin_name); ?></h2>
	<br class="clear" />
	
	<?php _e('Thank you for activating your subscription', $this -> plugin_name); ?><br/>
	<?php _e('Go back to', $this -> plugin_name); ?> <a href="<?php echo get_option('home'); ?>" title="<?php echo get_option('blogname'); ?>"><?php echo get_option('blogname'); ?></a>
</div>