<div class="wrap newsletters <?php echo $this -> pre; ?>">
    <h2><?php _e('Manage Autoresponders', $this -> plugin_name); ?> <a class="add-new-h2" href="?page=<?php echo $this -> sections -> autoresponders; ?>&amp;method=save" title="<?php _e('Add a new autoresponder', $this -> plugin_name); ?>"><?php _e('Add New', $this -> plugin_name); ?></a></h2>
    
    <form method="post" action="?page=<?php echo $this -> sections -> autoresponders; ?>&amp;method=autoresponderscheduling">
    	<label for="autoresponderscheduling"><?php _e('Schedule Interval:', $this -> plugin_name); ?></label>
        <?php $scheduleinterval = $this -> get_option('autoresponderscheduling'); ?>
    	<select name="autoresponderscheduling" id="autoresponderscheduling">
        	<?php $schedules = wp_get_schedules(); ?>
			<?php if (!empty($schedules)) : ?>
                <?php foreach ($schedules as $key => $val) : ?>
                <?php $sel = ($scheduleinterval == $key) ? 'selected="selected"' : ''; ?>
                <option <?php echo $sel; ?> value="<?php echo $key ?>"><?php echo $val['display']; ?> (<?php echo $val['interval'] ?> <?php _e('seconds', $this -> plugin_name); ?>)</option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <input class="button" type="submit" value="<?php _e('Save Interval', $this -> plugin_name); ?>" name="submit" />
        <?php echo $Html -> help(__('You can set the interval at which the plugin will check for autoresponder emails which need to be loaded and sent to the subscribers. This schedule runs using the WordPress cron jobs and can be monitored under Newsletters > Configuration > Scheduled Tasks as well.', $this -> plugin_name)); ?>
    </form>
    
    <?php if (!empty($autoresponders)) : ?>
		<form id="posts-filter" action="<?php echo $this -> url; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($autoresponders); ?> <?php _e('autoresponders', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> sections -> autoresponders); ?></li>
				<?php endif; ?>
			</ul>
			<p class="search-box">
				<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
				<input type="submit" class="button" value="<?php _e('Search Autoresponders', $this -> plugin_name); ?>" />
			</p>
		</form>
	<?php endif; ?>
    <?php $this -> render('autoresponders' . DS . 'loop', array('autoresponders' => $autoresponders, 'paginate' => $paginate), true, 'admin'); ?>
</div>