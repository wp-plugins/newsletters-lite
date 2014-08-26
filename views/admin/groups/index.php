<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Groups', $this -> plugin_name); ?> <a class="button add-new-h2" href="<?php echo $this -> url; ?>&amp;method=save" title="<?php _e('Create a new group', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (!empty($mailinglists)) : ?>
		<form id="posts-filter" action="?page=<?php echo $this -> sections -> groups; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($groups); ?> <?php _e('groups', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> sections -> groups); ?></li>
				<?php endif; ?>
				<?php /*
				<?php if ((isset($_COOKIE[$this -> pre . 'groupssorting']) && $_COOKIE[$this -> pre . 'groupssorting'] == "modified") || (!isset($_COOKIE[$this -> pre . 'groupstitledir']) || $_COOKIE[$this -> pre . 'groupstitledir'] == "DESC")) : ?>
					<li><?php echo $Html -> link(__('A to Z', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'ASC');")); ?> |</li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Z to A', $this -> plugin_name), '#void', array('onclick' => "change_sorting('title', 'DESC');")); ?> |</li>
				<?php endif; ?>
				<?php if ((isset($_COOKIE[$this -> pre . 'groupssorting']) && $_COOKIE[$this -> pre . 'groupssorting'] == "title") || (!isset($_COOKIE[$this -> pre . 'groupsmodifieddir']) || $_COOKIE[$this -> pre . 'groupsmodifieddir'] == "ASC")) : ?>
					<li><?php echo $Html -> link(__('New to Old', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'DESC');")); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Old to New', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'ASC');")); ?></li>
				<?php endif; ?>
				*/ ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Groups', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<?php $this -> render_admin('groups' . DS . 'loop', array('groups' => $groups, 'paginate' => $paginate)); ?>
</div>