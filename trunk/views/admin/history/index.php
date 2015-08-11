<div class="wrap newsletters <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Sent &amp; Draft Emails', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> send; ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
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
			<input type="text" id="post-search-input" class="search-input" name="searchterm" value="<?php echo (empty($_POST['searchterm'])) ? ((empty($_GET[$this -> pre . 'searchterm'])) ? '' : $_GET[$this -> pre . 'searchterm']) : $_POST['searchterm']; ?>" />
			<input type="submit" class="button" name="search" value="<?php _e('Search History', $this -> plugin_name); ?>" />
		</p>
	</form>
	<br class="clear" />
	<form id="posts-filter" action="?page=<?php echo $this -> sections -> history; ?>" method="get">
    	<input type="hidden" name="page" value="<?php echo $this -> sections -> history; ?>" />
    	
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
    		<select name="sent">
	    		<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Status', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "draft") ? 'selected="selected"' : ''; ?> value="draft"><?php _e('Draft', $this -> plugin_name); ?></option>
    			<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "sent") ? 'selected="selected"' : ''; ?> value="sent"><?php _e('Sent', $this -> plugin_name); ?></option>
    		</select>
    		<select name="theme_id">
    			<option <?php echo (!empty($_GET['theme_id']) && $_GET['theme_id'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Templates', $this -> plugin_name); ?></option>
    			<?php if ($themes = $Theme -> select()) : ?>
    				<?php foreach ($themes as $theme_id => $theme_title) : ?>
    					<option <?php echo (!empty($_GET['theme_id']) && $_GET['theme_id'] == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_id; ?>"><?php echo $theme_title; ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
    		</select>
    		<input type="submit" name="filter" value="<?php _e('Filter', $this -> plugin_name); ?>" class="button button-primary" />
    	</div>
    </form>
    <br class="clear" />
	<?php $this -> render_admin('history' . DS . 'loop', array('histories' => $histories, 'paginate' => $paginate)); ?>
</div>