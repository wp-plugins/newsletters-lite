<div class="wrap newsletters">
	<h2><?php _e('Manage Unsubscribes', $this -> plugin_name); ?></h2>
	
	<form action="" method="">
		<div class="tablenav">
		
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 6;
		
		?>
	
		<table class="widefat">
			<thead>
				<th class="check-column"><input type="checkbox" name="unsubscribescheckall" value="1" /></th>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc")); ?>">
						<span><?php _e('Email Address', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-user_id <?php echo ($orderby == "user_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=user_id&order=' . (($orderby == "user_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('User', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-mailinglist_id <?php echo ($orderby == "mailinglist_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=mailinglist_id&order=' . (($orderby == "mailinglist_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('Mailing List', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('History Email', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
						<span><?php _e('Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</thead>
			<tfoot>
				<th class="check-column"><input type="checkbox" name="unsubscribescheckall" value="1" /></th>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc")); ?>">
						<span><?php _e('Email Address', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-user_id <?php echo ($orderby == "user_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=user_id&order=' . (($orderby == "user_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('User', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-mailinglist_id <?php echo ($orderby == "mailinglist_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=mailinglist_id&order=' . (($orderby == "mailinglist_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('Mailing List', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('History Email', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
						<span><?php _e('Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tfoot>
			<tbody>
				<?php if (!empty($unsubscribes)) : ?>
					<?php $class = false; ?>
					<?php foreach ($unsubscribes as $unsubscribe) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" name="unsubscribes[]" value="<?php echo $unsubscribe -> id; ?>" /></th>
							<td>
								<?php $Db -> model = $Subscriber -> model; ?>
								<?php if ($subscriber = $Db -> find(array('email' => $unsubscribe -> email))) : ?>
									<a class="row-title" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=view&id=' . $subscriber -> id); ?>"><?php echo $unsubscribe -> email; ?></a>
								<?php else : ?>
									<?php echo $unsubscribe -> email; ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($unsubscribe -> user_id)) : ?>
									<a href="<?php echo get_edit_user_link($unsubscribe -> userdata -> ID); ?>"><?php echo $unsubscribe -> userdata -> display_name; ?></a>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($unsubscribe -> mailinglist_id)) : ?>
									<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> lists . '&method=view&id=' . $unsubscribe -> mailinglist_id); ?>"><?php echo __($unsubscribe -> mailinglist -> title); ?></a>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($unsubscribe -> history_id)) : ?>
									<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $unsubscribe -> history_id); ?>"><?php echo __($unsubscribe -> history -> subject); ?></a>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<abbr title="<?php echo $unsubscribe -> created; ?>"><?php echo date_i18n("Y-m-d", strtotime($unsubscribe -> created)); ?></abbr>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No unsubscribes were found', $this -> plugin_name); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>