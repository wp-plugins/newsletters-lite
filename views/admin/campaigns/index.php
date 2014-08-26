<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Campaigns', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> campaigns; ?>&amp;method=save"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	
	<?php $this -> render('campaigns' . DS . 'loop', array('campaigns' => $campaigns, 'paginate' => $paginate), true, 'admin'); ?>
</div>