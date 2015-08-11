<div class="wrap newsletters">
	<h2><?php _e('Manage Orders', $this -> plugin_name); ?></h2>
	<?php if (!empty($orders)) : ?>
		<form id="posts-filter" action="?page=<?php echo $this -> sections -> orders; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($orders); ?> <?php _e('subscription orders', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> pre . 'orders'); ?></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Orders', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render('orders' . DS . 'loop', array('orders' => $orders, 'paginate' => $paginate), true, 'admin'); ?>
</div>