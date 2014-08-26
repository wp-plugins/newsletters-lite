<div class="wrap">
	<div style="width:600px;">
		<h2><?php _e('Posts Logged', $this -> plugin_name); ?></h2>
		
		<?php if (!empty($posts)) : ?>
			<div class="tablenav">
			
			</div>
			<table id="posts_table" class="widefat">
				<thead>
					<tr>
						<th><?php _e('ID', $this -> plugin_name); ?></th>
						<th><?php _e('Post', $this -> plugin_name); ?></th>
						<th><?php _e('Date', $this -> plugin_name); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th><?php _e('ID', $this -> plugin_name); ?></th>
						<th><?php _e('Post', $this -> plugin_name); ?></th>
						<th><?php _e('Date', $this -> plugin_name); ?></th>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($posts as $p) : ?>
						<?php
						
						global $post;
						if ($post = get_post($p -> post_id)) {
							setup_postdata($post);
							
							?>
							<tr id="post_row_<?php echo $p -> id; ?>">
								<td>
									<?php the_ID(); ?>
								</td>
								<td>
									<?php the_title(); ?>
									<div class="row-actions">
										<span class="delete"><a href="" onclick="if (confirm('<?php echo __('Are you sure you want to delete this logged post? You are not actually deleting the post itself, just the fact that it was sent already.', $this -> plugin_name); ?>')) { delete_lps_post('<?php echo $p -> id; ?>'); } return false;"><?php _e('Delete', $this -> plugin_name); ?></a></span>
									</div>
								</td>
								<td>
									<abbr title="<?php echo $p -> created; ?>"><?php echo date("Y-m-d", strtotime($p -> created)); ?></abbr>
								</td>
							</tr>
						<?php } ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p class="newsletters_error"><?php _e('No posts have been logged  yet.', $this -> plugin_name); ?></p>
		<?php endif; ?>
		
		<p class="submit">
			<input type="button" class="button button-secondary button-large" onclick="jQuery.colorbox.close();" value="<?php _e('Close This', $this -> plugin_name); ?>" />
		</p>
	</div>
</div>

<script type="text/javascript">
function delete_lps_post(id) {
	jQuery.post(wpmlajaxurl + '?action=newsletters_delete_lps_post', {id:id}, function(response) {
		jQuery('table#posts_table tr#post_row_' + id).fadeOut('slow');
	});
}
</script>