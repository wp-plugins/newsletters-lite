<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('View Group:', $this -> plugin_name); ?> <?php echo $group -> title; ?></h2>	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; All Groups', $this -> plugin_name), $this -> url, array('title' => __('Manage All Groups', $this -> plugin_name))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft">
			<a href="?page=<?php echo $this -> sections -> groups; ?>&amp;method=save&amp;id=<?php echo $group -> id; ?>" title="<?php _e('Change the details of this group.', $this -> plugin_name); ?>" class="button"><?php _e('Change', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> groups; ?>&amp;method=delete&amp;id=<?php echo $group -> id; ?>" title="<?php _e('Remove this group permanently', $this -> plugin_name); ?>" class="button button-highlighted" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this group?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete', $this -> plugin_name); ?></a>
		</div>
	</div>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Title', $this -> plugin_name); ?></th>
				<td><?php echo $group -> title; ?></td>
			</tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php _e('Lists', $this -> plugin_name); ?></th>
                <td>
                	<?php echo $Html -> link($Mailinglist -> count(array('group_id' => $group -> id)), '#mailinglists'); ?>
                </td>
            </tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Created', $this -> plugin_name); ?></th>
				<td><?php echo $group -> created; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Modified', $this -> plugin_name); ?></th>
				<td><?php echo $group -> modified; ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="mailinglists"><?php _e('Mailing Lists', $this -> plugin_name); ?> <?php echo $Html -> link(__('Add New', $this -> plugin_name), '?page=' . $this -> sections -> lists . '&amp;method=save&amp;group_id=' . $group -> id, array('class' => "button add-new-h2")); ?></h3>
	<?php $this -> render_admin('mailinglists' . DS . 'loop', array('mailinglists' => $mailinglists, 'paginate' => $paginate)); ?>
</div>