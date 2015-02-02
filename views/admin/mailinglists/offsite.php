<div class="wrap newsletters">
	<h2><?php _e('Offsite HTML Code', $this -> plugin_name); ?></h2>
	<div>
		<p><?php _e('Put the code below in the <code><HEAD></HEAD></code> section of your site', $this -> plugin_name); ?></p>
		<textarea onclick="this.select();" rows="5" cols="45"><?php echo 
			htmlentities('<script type="text/javascript"> var wpmlAjax = "' . $this -> url() . '/' . $this -> plugin_name . '-ajax.php"; </script>
			<script type="text/javascript" src="' . $this -> url() . '/js/wp-mailinglist.js"></script>
			<script type="text/javascript" src="' . get_option('siteurl') . '/wp-includes/js/scriptaculous/prototype.js"></script>
			<script type="text/javascript" src="' . get_option('siteurl') . '/wp-includes/js/scriptaculous/scriptaculous.js?load=effects"></script>'); 
		?></textarea>
		
		<p><?php _e('Use the code below to create an opt-in form on any website', $this -> plugin_name); ?><br/>
		<?php _e('Place the code into the HTML of your site', $this -> plugin_name); ?></p>
		<textarea onclick="this.select();" rows="5" cols="45"><?php echo $html; ?></textarea>
	</div>
</div>