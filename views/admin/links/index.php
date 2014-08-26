<!-- Links -->
<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Links &amp; Clicks', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('links' . DS . 'loop', array('links' => $links, 'paginate' => $paginate), true, 'admin'); ?>
</div>