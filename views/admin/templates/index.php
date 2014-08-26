<div class="wrap newsletters">
	<h2><?php _e('Manage Snippets', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> templates_save; ?>" title="<?php _e('Create a new newsletter template', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (!empty($templates)) : ?>
		<form id="posts-filter" method="post" action="?page=<?php echo $this -> sections -> templates; ?>">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($templates); ?> <?php _e('email snippets', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> pre . "templates"); ?></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<input type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" id="post-search-input" class="search-input" />
				<input class="button-secondary" type="submit" name="" value="<?php _e('Search Snippets', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('templates' . DS . 'loop', array('templates' => $templates, 'paginate' => $paginate)); ?>
</div>