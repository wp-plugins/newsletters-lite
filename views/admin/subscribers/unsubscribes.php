<div class="wrap newsletters">
	<h2><?php _e('Manage Unsubscribes', $this -> plugin_name); ?></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; Back to Subscribers', $this -> plugin_name), "?page=" . $this -> sections -> subscribers); ?></div> 
	
	<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    	<?php if (!empty($unsubscribes)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($unsubscribes); ?> <?php _e('unsubscribes', $this -> plugin_name); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
                <?php else : ?>
                    <li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> subscribers . '&method=unsubscribes'); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
			<input type="submit" class="button" value="<?php _e('Search Unsubscribes', $this -> plugin_name); ?>" />
		</p>
	</form>
	
	<form action="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribemass'); ?>" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected unsubscribes?', $this -> plugin_name); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" id="newsletters-unsubscribe-action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					<option value="deletesubscribers"><?php _e('Delete Subscribers', $this -> plugin_name); ?></option>
					<option value="deleteusers"><?php _e('Delete Users', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" class="button-secondary" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 7;
		
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
				<th class="column-comments <?php echo ($orderby == "comments") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=comments&order=' . (($orderby == "comments") ? $otherorder : "asc")); ?>">
						<span><?php _e('Comments', $this -> plugin_name); ?></span>
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
				<th class="column-comments <?php echo ($orderby == "comments") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=comments&order=' . (($orderby == "comments") ? $otherorder : "asc")); ?>">
						<span><?php _e('Comments', $this -> plugin_name); ?></span>
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
								
								<div class="row-actions">
									<span class="delete"><a class="submitdelete" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribedelete&id=' . $unsubscribe -> id); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this unsubscribe?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete Unsubscribe', $this -> plugin_name); ?></a></span>
									<?php if (!empty($subscriber)) : ?>
										<span class="delete">| <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=delete&id=' . $subscriber -> id); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this subscriber?', $this -> plugin_name); ?>')) { return false; }" class="submitdelete"><?php _e('Delete Subscriber', $this -> plugin_name); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
							<td>
								<?php if (!empty($unsubscribe -> user_id)) : ?>
									<a href="<?php echo get_edit_user_link($unsubscribe -> userdata -> ID); ?>"><?php echo $unsubscribe -> userdata -> display_name; ?></a>
									<div class="row-actions">
										<span class="delete"><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=deleteuser&user_id=' . $unsubscribe -> user_id); ?>" class="submitdelete" onclick="if (!confirm('<?php _e('Are you sure you want to delete this user?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete User', $this -> plugin_name); ?></a></span>
									</div>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($unsubscribe -> comments)) : ?>
									<?php echo esc_attr(stripslashes($unsubscribe -> comments)); ?>
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
		
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'unsubscribesperpage']) && $_COOKIE[$this -> pre . 'unsubscribesperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('unsubscribes', $this -> plugin_name); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo $this -> pre; ?>unsubscribesperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
		}
		</script>
	</form>
</div>