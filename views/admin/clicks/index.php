<!-- Clicks -->
<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Clicks', $this -> plugin_name); ?></h2>
	<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    	<?php if (!empty($clicks)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($clicks); ?> <?php _e('clicks', $this -> plugin_name); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
                <?php else : ?>
                    <li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> clicks); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
			<input type="submit" class="button" value="<?php _e('Search Clicks', $this -> plugin_name); ?>" />
		</p>
	</form>
	<br class="clear" />
	<?php $this -> render('clicks' . DS . 'loop', array('clicks' => $clicks, 'paginate' => $paginate), true, 'admin'); ?>
</div>