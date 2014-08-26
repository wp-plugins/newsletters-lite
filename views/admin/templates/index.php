<div class="wrap">
	<h2><?php _e('Manage Snippets', $this -> plugin_name); ?> <a class="button add-new-h2" href="?page=<?php echo $this -> sections -> templates_save; ?>" title="<?php _e('Create a new newsletter template', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (!empty($templates)) : ?>
		<form id="posts-filter" method="post" action="?page=<?php echo $this -> sections -> templates; ?>">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($templates); ?> <?php _e('email snippets', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> pre . "templates"); ?></li>
				<?php endif; ?>
				<?php /*
				<?php if ((isset($_COOKIE[$this -> pre . 'templatessorting']) && $_COOKIE[$this -> pre . 'templatessorting'] == "modified") || (!isset($_COOKIE[$this -> pre . 'templatestitledir']) || $_COOKIE[$this -> pre . 'templatestitledir'] == "DESC")) : ?>
					<li><?php echo $Html -> link(__('A to Z', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'ASC');")); ?> |</li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Z to A', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'DESC');")); ?> |</li>
				<?php endif; ?>
				<?php if ((isset($_COOKIE[$this -> pre . 'templatessorting']) && $_COOKIE[$this -> pre . 'templatessorting'] == "title") || (!isset($_COOKIE[$this -> pre . 'templatesmodifieddir']) || $_COOKIE[$this -> pre . 'templatesmodifieddir'] == "ASC")) : ?>
					<li><?php echo $Html -> link(__('New to Old', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'DESC');")); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Old to New', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'ASC');")); ?></li>
				<?php endif; ?>
				*/ ?>
			</ul>
			<p class="search-box">
				<input type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" id="post-search-input" class="search-input" />
				<input class="button-secondary" type="submit" name="" value="<?php _e('Search Snippets', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('templates' . DS . 'loop', array('templates' => $templates, 'paginate' => $paginate)); ?>
</div>