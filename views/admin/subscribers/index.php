<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Subscribers', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save" title="<?php _e('Create a new subscriber', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (true || !empty($subscribers)) : ?>
		<form id="posts-filter" action="<?php echo $this -> url; ?>" method="post">
        	<?php if (!empty($subscribers)) : ?>
                <ul class="subsubsub">
                    <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($subscribers); ?> <?php _e('subscribers', $this -> plugin_name); ?> |</li>
                    <?php if (empty($_GET['showall'])) : ?>
                        <li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
                    <?php else : ?>
                        <li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> subscribers); ?></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Subscribers', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
	<br class="clear" />
	<?php /*<p class="howto"><?php _e('You can click "Screen Options" in the top, right-hand corner to display custom columns in this table.', $this -> plugin_name); ?></p>*/ ?>
	<?php $this -> render_admin('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate)); ?>
</div>