<div class="wrap">
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
				<?php /*
				<?php if ((isset($_COOKIE[$this -> pre . 'orderssorting']) && $_COOKIE[$this -> pre . 'orderssorting'] == "modified") || (!isset($_COOKIE[$this -> pre . 'orderssubscribersdir']) || $_COOKIE[$this -> pre . 'orderssubscribersdir'] == "DESC")) : ?>
					<li><?php echo $Html -> link(__('A to Z', $this -> plugin_name), '#void', array('onclick' => "change_sorting('subscriber_id', 'ASC');")); ?> |</li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Z to A', $this -> plugin_name), '#void', array('onclick' => "change_sorting('subscriber_id', 'DESC');")); ?> |</li>
				<?php endif; ?>
				<?php if ((isset($_COOKIE[$this -> pre . 'orderssorting']) && $_COOKIE[$this -> pre . 'orderssorting'] == "subscriber_id") || (!isset($_COOKIE[$this -> pre . 'ordersmodifieddir']) || $_COOKIE[$this -> pre . 'ordersmodifieddir'] == "ASC")) : ?>
					<li><?php echo $Html -> link(__('New to Old', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'DESC');")); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Old to New', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'ASC');")); ?></li>
				<?php endif; ?>
				*/ ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Orders', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render('orders/loop', array('orders' => $orders, 'paginate' => $paginate), true, 'admin'); ?>
</div>