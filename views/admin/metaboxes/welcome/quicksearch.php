<div>
	<form action="<?php echo admin_url('admin.php'); ?>?page=<?php echo $this -> sections -> subscribers; ?>" method="post">
		<p>
			<label>
				<?php _e('Subscriber:', $this -> plugin_name); ?><br/>
				<input type="text" name="searchterm" value="" id="newsletters_quicksearch_input" />
			</label>
		</p>
		<p class="submit">
			<input type="submit" name="search" value="<?php _e('Search Now', $this -> plugin_name); ?>" id="newsletters_quicksearch_submit" class="button button-primary" /> 
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#newsletters_quicksearch_input').Watermark('<?php echo __('Subscriber...', $this -> plugin_name); ?>');
});
</script>