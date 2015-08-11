<!-- Links -->
<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Links &amp; Clicks', $this -> plugin_name); ?></h2>
	<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    	<?php if (!empty($links)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($links); ?> <?php _e('links', $this -> plugin_name); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
                <?php else : ?>
                    <li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> links); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
			<input type="submit" class="button" value="<?php _e('Search Links', $this -> plugin_name); ?>" />
		</p>
	</form>
	<br class="clear" />
	<?php $this -> render('links' . DS . 'loop', array('links' => $links, 'paginate' => $paginate), true, 'admin'); ?>
</div>