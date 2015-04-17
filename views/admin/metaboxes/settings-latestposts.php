<!-- Latest Posts Subscriptions Settings -->

<?php
	
$latestpostssubscriptions = $this -> Latestpostssubscription -> find_all();	
	
?>

<table class="widefat" id="latestposts_table">
	<thead>
		<tr>
			<th><?php _e('Subject', $this -> plugin_name); ?></th>
			<th><?php _e('Interval', $this -> plugin_name); ?></th>
			<th><?php _e('Lists', $this -> plugin_name); ?></th>
			<th><?php _e('Posts', $this -> plugin_name); ?></th>
			<th><?php _e('Actions', $this -> plugin_name); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($latestpostssubscriptions as $latestpostssubscription) : ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>" id="latestposts_row_<?php echo $latestpostssubscription -> id; ?>">
				<td>
					<span class="row-title"><?php echo $latestpostssubscription -> subject; ?></span>
					<div class="row-actions">
						<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&amp;method=runschedule&amp;hook=newsletters_latestposts&id=' . $latestpostssubscription -> id); ?>"><?php _e('Run Now', $this -> plugin_name); ?></a>
					</div>
				</td>
				<td>
					<?php if (!empty($latestpostssubscription -> interval)) : ?>
						<?php echo $Html -> next_scheduled('newsletters_latestposts', array($latestpostssubscription -> id)); ?>
					<?php else : ?>
						<span class="newsletters_error"><?php _e('None', $this -> plugin_name); ?></span>
					<?php endif; ?>
				</td>
				<td>
					<?php if (!empty($latestpostssubscription -> lists)) : ?>
						<?php $lists = maybe_unserialize($latestpostssubscription -> lists); ?>
						<?php $l = 1; ?>
						<?php foreach ($lists as $list_id) : ?>
							<?php $list = $Mailinglist -> get($list_id); ?>
							<?php echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lists . '&method=view&id=' . $list_id) . '">' . __($list -> title) . '</a>'; ?>
							<?php echo ($l < count($lists)) ? ', ' : ''; ?>
							<?php $l++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<span class="newsletters_error"><?php _e('None', $this -> plugin_name); ?></span>
					<?php endif; ?>
				</td>
				<td>
					<?php 
					
					$posts_used = $this -> get_latestposts_used($latestpostssubscription);	
					
					?>
					
					<a href="" onclick="jQuery.colorbox({href:wpmlajaxurl + '?action=newsletters_lpsposts&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;"><?php echo sprintf(__('%s posts used/sent', $this -> plugin_name), $posts_used); ?></a>
					<a onclick="if (!confirm('<?php _e('Are you sure you want to clear the posts history for this latest posts subscription?', $this -> plugin_name); ?>')) { return false; }" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> settings . '&amp;method=clearlpshistory&id=' . $latestpostssubscription -> id); ?>" class=""><i class="fa fa-trash"></i></a>
				</td>
				<td>
					<a href="" onclick="jQuery(this).colorbox({iframe:true, width:'80%', height:'80%', href:wpmlajaxurl + '?action=wpmllatestposts_preview&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;" class="button"><i class="fa fa-eye fa-fw"></i></a>
					<a href="" onclick="jQuery.colorbox({href:wpmlajaxurl + '?action=newsletters_latestposts_save&id=<?php echo $latestpostssubscription -> id; ?>'}); return false;" class="button editrow"><i class="fa fa-pencil fa-fw"></i></a>
					<a href="" onclick="if (confirm('<?php _e('Are you sure you want to delete this latest posts subscription?', $this -> plugin_name); ?>')) { latestposts_del_row('<?php echo $latestpostssubscription -> id; ?>'); } return false;" class="button delrow"><i class="fa fa-trash"></i></a>	
					<span id="latestposts_loading_<?php echo $latestpostssubscription -> id; ?>" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<p>
	<a href="" class="button latestposts-addrow"><i class="fa fa-plus"></i> <?php _e('Add Another', $this -> plugin_name); ?></a>
</p>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.latestposts-addrow').live('click', function() {
		latestposts_add_row();
		return false;
	});
});

function latestposts_add_row() {	
	jQuery.colorbox({href:wpmlajaxurl + '?action=newsletters_latestposts_save'});
}

function latestposts_del_row(id) {
	jQuery('#latestposts_loading_' + id).show();
	jQuery.post(wpmlajaxurl + '?action=newsletters_latestposts_delete&id=' + id, false, function(response) {
		jQuery('#latestposts_row_' + id).remove();
	});
}
</script>