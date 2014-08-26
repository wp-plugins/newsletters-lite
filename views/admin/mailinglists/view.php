<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('View List:', $this -> plugin_name); ?> <?php echo __($mailinglist -> title); ?></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; All Mailing Lists', $this -> plugin_name), $this -> url, array('title' => __('Manage All Mailing Lists', $this -> plugin_name))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft">
			<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=offsite&amp;listid=<?php echo $mailinglist -> id; ?>" title="<?php _e('Generate offsite code for this list', $this -> plugin_name); ?>" class="button"><?php _e('Offsite Form', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=save&amp;id=<?php echo $mailinglist -> id; ?>" title="<?php _e('Change the details of this mailing list', $this -> plugin_name); ?>" class="button"><?php _e('Change', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=delete&amp;id=<?php echo $mailinglist -> id; ?>" title="<?php _e('Remove this mailing list permanently', $this -> plugin_name); ?>" class="button button-highlighted" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this mailing list?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete', $this -> plugin_name); ?></a>
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
				<td><?php echo __($mailinglist -> title); ?></td>
			</tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php _e('Group', $this -> plugin_name); ?></th>
                <td>
                	<?php if (!empty($mailinglist -> group_id) && !empty($mailinglist -> group)) : ?>
                    	<?php echo $Html -> link($mailinglist -> group -> title, '?page=' . $this -> sections -> groups . '&amp;method=view&amp;id=' . $mailinglist -> group_id); ?>
                    <?php else : ?>
                    	<?php _e('none', $this -> plugin_name); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($mailinglist -> adminemail)) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            		<th><?php _e('Admin Email', $this -> plugin_name); ?></th>
            		<td><?php echo $mailinglist -> adminemail; ?></td>
            	</tr>
            <?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Subscribers', $this -> plugin_name); ?></th>
				<td><?php echo $SubscribersList -> count(array('list_id' => $mailinglist -> id)); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Private List', $this -> plugin_name); ?></th>
				<td><?php echo (empty($mailinglist -> privatelist) || $mailinglist -> privatelist == "N") ? __('No', $this -> plugin_name) : __('Yes', $this -> plugin_name); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Paid List', $this -> plugin_name); ?></th>
				<td><?php echo (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? __('No', $this -> plugin_name) : __('Yes', $this -> plugin_name); ?></td>
			</tr>
			<?php if ($mailinglist -> paid == "Y") : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Price', $this -> plugin_name); ?></th>
					<td><?php echo $Html -> currency(); ?><?php echo $mailinglist -> price; ?></td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Paid Interval', $this -> plugin_name); ?></th>
					<td>
						<?php
						
						$intervals = array(
							'daily'			=>	__('Daily', $this -> plugin_name),
							'weekly'		=>	__('Weekly', $this -> plugin_name),
							'monthly'		=>	__('Monthly', $this -> plugin_name),
							'2months'		=>	__('Every Two Months', $this -> plugin_name),
							'3months'		=>	__('Every Three Months', $this -> plugin_name),
							'biannually'	=>	__('Twice Yearly (Six Months)', $this -> plugin_name),
							'9months'		=>	__('Every Nine Months', $this -> plugin_name),
							'yearly'		=>	__('Yearly', $this -> plugin_name),
							'once'			=>	__('Once Off', $this -> plugin_name),
						);
						
						?>
						<?php echo $intervals[$mailinglist -> interval]; ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Created', $this -> plugin_name); ?></th>
				<td><?php echo $mailinglist -> created; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Modified', $this -> plugin_name); ?></th>
				<td><?php echo $mailinglist -> modified; ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="subscribers"><?php _e('Subscribers', $this -> plugin_name); ?> <?php echo $Html -> link(__('Add New', $this -> plugin_name), '?page=' . $this -> sections -> subscribers . '&amp;method=save&amp;mailinglist_id=' . $mailinglist -> id, array('class' => "add-new-h2")); ?></h3>	
	<?php $this -> render_admin('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate)); ?>
</div>