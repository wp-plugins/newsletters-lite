<div>
	<form action="<?php echo admin_url('admin.php'); ?>?page=<?php echo $this -> sections -> subscribers; ?>" method="post">
		<p>
			<label>
				<?php _e('Subscriber:', $this -> plugin_name); ?><br/>
				<input placeholder="<?php echo esc_attr(stripslashes(__('Subscriber...', $this -> plugin_name))); ?>" type="text" name="searchterm" value="" id="newsletters_quicksearch_input" />
			</label>
		</p>
		<p class="submit">
			<input type="submit" name="search" value="<?php _e('Search Now', $this -> plugin_name); ?>" id="newsletters_quicksearch_submit" class="button button-primary" /> 
		</p>
	</form>
</div>