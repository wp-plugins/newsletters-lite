<div class="wrap newsletters <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Sent &amp; Draft Emails', $this -> name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> send; ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<form id="posts-filter" method="post" action="?page=<?php echo $this -> sections -> history; ?>">
		<?php if (!empty($histories)) : ?>
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($histories); ?> <?php _e('sent/draft emails', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> history); ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<p class="search-box">
			<input type="text" id="post-search-input" class="search-input" name="searchterm" value="<?php echo (empty($_POST['searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : $_POST['searchterm']; ?>" />
			<input type="submit" class="button" name="search" value="<?php _e('Search History', $this -> plugin_name); ?>" />
		</p>
	</form>
	<?php $this -> render_admin('history' . DS . 'loop', array('histories' => $histories, 'paginate' => $paginate)); ?>
</div>