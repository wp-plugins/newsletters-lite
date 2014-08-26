<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Manage Subscribers', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save" title="<?php _e('Create a new subscriber', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
	<?php if (true || !empty($subscribers)) : ?>
		<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
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
    <form id="posts-filter" action="?page=<?php echo $this -> sections -> subscribers; ?>" method="get">
    	<input type="hidden" name="page" value="<?php echo $this -> sections -> subscribers; ?>" />
    	
    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
    		<input type="hidden" name="<?php echo $this -> pre; ?>searchterm" value="<?php echo esc_attr(stripslashes($_GET[$this -> pre . 'searchterm'])); ?>" />
    	<?php endif; ?>
    	
    	<div class="alignleft actions">
    		<?php _e('Filters:', $this -> plugin_name); ?>
    		<select name="list">
    			<option <?php echo (!empty($_GET['list']) && $_GET['list'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Mailing Lists', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['list']) && $_GET['list'] == "none") ? 'selected="selected"' : ''; ?> value="none"><?php _e('No Mailing Lists', $this -> plugin_name); ?></option>
    			<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
    				<?php foreach ($mailinglists as $list_id => $list_title) : ?>
    					<option <?php echo (!empty($_GET['list']) && $_GET['list'] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo $list_id; ?>"><?php echo __($list_title); ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
    		</select>
    		<select name="status">
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Status', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "active") ? 'selected="selected"' : ''; ?> value="active"><?php _e('Active Subscriptions', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "inactive") ? 'selected="selected"' : ''; ?> value="inactive"><?php _e('Inactive Subscriptions', $this -> plugin_name); ?></option>
    		</select>
    		<select name="registered">
    			<option <?php echo (!empty($_GET['registered']) && $_GET['registered'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Subscribers', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['registered']) && $_GET['registered'] == "Y") ? 'selected="selected"' : ''; ?> value="Y"><?php _e('Registered Users', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['registered']) && $_GET['registered'] == "N") ? 'selected="selected"' : ''; ?> value="N"><?php _e('Not Registered', $this -> plugin_name); ?></option>
    		</select>
    		<input type="submit" name="filter" value="<?php _e('Filter', $this -> plugin_name); ?>" class="button button-primary" />
    	</div>
    </form>
    <br class="clear" />
	<?php $this -> render_admin('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate)); ?>
</div>