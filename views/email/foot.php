	<?php if ($this -> get_option('tracking') == "Y") : ?>
		<img style="display:none;" src="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=track&id=<?php echo $eunique; ?>" alt="<?php _e('tracking', $this -> plugin_name); ?>" />
	<?php endif; ?>
	
	</body>
</html>