<?php if (!empty($orders)) : ?>
	<form action="?page=<?php echo $this -> sections -> orders; ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action?', $this -> plugin_name); ?>')) { return false; }" id="ordersform">
		<div class="tablenav">
			<div class="alignleft">
				<select name="action" style="width:auto;" class="widefat">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" class="button-secondary" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-list_id <?php echo ($orderby == "list_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=list_id&order=' . (($orderby == "list_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Mailing List', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-amount <?php echo ($orderby == "amount") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=amount&order=' . (($orderby == "amount") ? $otherorder : "asc")); ?>">
							<span><?php _e('Amount', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-pmethod <?php echo ($orderby == "pmethod") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=pmethod&order=' . (($orderby == "pmethod") ? $otherorder : "asc")); ?>">
							<span><?php _e('Payment Method', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-list_id <?php echo ($orderby == "list_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=list_id&order=' . (($orderby == "list_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Mailing List', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-amount <?php echo ($orderby == "amount") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=amount&order=' . (($orderby == "amount") ? $otherorder : "asc")); ?>">
							<span><?php _e('Amount', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-pmethod <?php echo ($orderby == "pmethod") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=pmethod&order=' . (($orderby == "pmethod") ? $otherorder : "asc")); ?>">
							<span><?php _e('Payment Method', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php $class = ''; ?>
				<?php foreach ($orders as $order) : ?>
				<?php $subscriber = $Subscriber -> get($order -> subscriber_id, false); ?>
				<?php $mailinglist = $Mailinglist -> get($order -> list_id, false); ?>
				<tr id="orderrow<?php echo $order -> id; ?>" class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th class="check-column"><input type="checkbox" name="orderslist[]" value="<?php echo $order -> id; ?>" id="checklist<?php echo $order -> id; ?>" /></th>
					<td><label for="checklist<?php echo $order -> id; ?>"><?php echo $order -> id; ?></label></td>
					<td>
						<strong><a class="row-title" href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $mailinglist -> id; ?>" title="<?php _e('View the details of this mailinglist', $this -> plugin_name); ?>"><?php echo $mailinglist -> title; ?></a></strong>
						<div class="row-actions">
							<span class="edit"><?php echo $Html -> link(__('Edit', $this -> plugin_name), '?page=' . $this -> sections -> orders . '&amp;method=save&amp;id=' . $order -> id); ?> |</span>
							<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> orders . '&amp;method=delete&amp;id=' . $order -> id, array('class' => "submitdelete", 'onclick' => "if (!confirm('" . __('Are you sure you want to delete this order? Linked subscription will be removed as well.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
							<span class="view"><?php echo $Html -> link(__('View Order', $this -> plugin_name), '?page=' . $this -> sections -> orders . '&amp;method=view&amp;id=' . $order -> id); ?></span>
						</div>
					</td>
					<td><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $subscriber -> id; ?>" title="<?php _e('View the details of this subscriber', $this -> plugin_name); ?>"><?php echo $subscriber -> email; ?></a></td>
					<td><label for="checklist<?php echo $order -> id; ?>"><strong><?php echo $Html -> currency(); ?><?php echo number_format($order -> amount, 2, '.', ''); ?></strong></label></td>
					<td><label for="checklist<?php echo $order -> id; ?>"><?php echo (!empty($order -> pmethod) && $order -> pmethod == "2co") ? '2CheckOut' : 'PayPal'; ?></label></td>
					<td><label for="checklist<?php echo $order -> id; ?>"><abbr title="<?php echo $order -> modified; ?>"><?php echo date_i18n("Y-m-d", strtotime($order -> modified)); ?></abbr></label></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'ordersperpage']) && $_COOKIE[$this -> pre . 'ordersperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('orders', $this -> plugin_name); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>	
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo $this -> pre; ?>ordersperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo $this -> pre; ?>orderssorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			document.cookie = "<?php echo $this -> pre; ?>orders" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
		}
		</script>
	</form>
<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No subscription orders were found', $this -> plugin_name); ?></p>
<?php endif; ?>