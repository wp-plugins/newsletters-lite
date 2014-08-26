<div class="wrap">
	<h2><?php _e('Manage Lists', $this -> plugin_name); ?> <a class="button add-new-h2" href="<?php echo $this -> url; ?>&amp;method=save" title="<?php _e('Create a new mailing list', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (!empty($mailinglists)) : ?>
		<form id="posts-filter" action="?page=<?php echo $this -> sections -> lists; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($mailinglists); ?> <?php _e('mailing lists', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> sections -> lists); ?></li>
				<?php endif; ?>
				<?php /*
				<?php if ((isset($_COOKIE[$this -> pre . 'listssorting']) && $_COOKIE[$this -> pre . 'listssorting'] == "modified") || (!isset($_COOKIE[$this -> pre . 'liststitledir']) || $_COOKIE[$this -> pre . 'liststitledir'] == "DESC")) : ?>
					<li><?php echo $Html -> link(__('A to Z', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'ASC');")); ?> |</li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Z to A', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'DESC');")); ?> |</li>
				<?php endif; ?>
				<?php if ((isset($_COOKIE[$this -> pre . 'listssorting']) && $_COOKIE[$this -> pre . 'listssorting'] == "title") || (!isset($_COOKIE[$this -> pre . 'listsmodifieddir']) || $_COOKIE[$this -> pre . 'listsmodifieddir'] == "ASC")) : ?>
					<li><?php echo $Html -> link(__('New to Old', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'DESC');")); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Old to New', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'ASC');")); ?></li>
				<?php endif; ?>
				*/ ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Lists', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('mailinglists' . DS . 'loop', array('mailinglists' => $mailinglists, 'paginate' => $paginate)); ?>
</div>