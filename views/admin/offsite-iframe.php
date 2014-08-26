<iframe frameborder="0" border="0" style="border:none;" class="autoHeight" src="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=offsite&iframe=1&list=<?php echo $options['list']; ?>">
	<p><?php _e('Form loading, please wait...', $this -> plugin_name); ?></p>
</iframe>
<script type="text/javascript" src="<?php echo $this -> url(); ?>/js/jquery.autoheight.js"></script>