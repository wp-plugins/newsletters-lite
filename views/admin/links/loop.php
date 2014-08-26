
	<form action="?page=<?php echo $this -> sections -> links; ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you want to apply this action to the selected links?', $this -> plugin_name); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<a href="?page=<?php echo $this -> sections -> clicks; ?>" class="newsletters-icon-link button"> <?php _e('Clicks', $this -> plugin_name); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					<option value="reset"><?php _e('Reset', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="apply" class="button" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 4;
		
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></th>
					<th class="column-link <?php echo ($orderby == "link") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=link&order=' . (($orderby == "link") ? $otherorder : "asc")); ?>">
							<span><?php _e('Link', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php _e('Clicks', $this -> plugin_name); ?></th>
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
					<th class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></th>
					<th class="column-link <?php echo ($orderby == "link") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=link&order=' . (($orderby == "link") ? $otherorder : "asc")); ?>">
							<span><?php _e('Link', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php _e('Clicks', $this -> plugin_name); ?></th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($links)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No links were found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = false; ?>
					<?php foreach ($links as $link) : ?>
						<tr class="<?php $class = (empty($class)) ? 'alternate' : false; ?>">
							<th class="check-column"><input type="checkbox" name="links[]" value="<?php echo $link -> id; ?>" id="links_<?php echo $link -> id; ?>" /></th>
							<td>
								<a href="" class="row-title"><?php echo $Html -> link($link -> link, $link -> link, array('target' => "_blank")); ?></a>
								<div class="row-actions">
									<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> links . '&amp;method=delete&amp;id=' . $link -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this link?', $this -> plugin_name) . "')) { return false; }", 'class' => "delete")); ?> |</span>
									<span class="view"><?php echo $Html -> link(__('Open Link', $this -> plugin_name), $link -> link, array('target' => "_blank")); ?></span>
								</div>
							</td>
							<td>
								<?php echo $Html -> link($this -> Click -> count(array('link_id' => $link -> id)), '?page=' . $this -> sections -> clicks . '&amp;link_id=' . $link -> id); ?>
							</td>
							<td>
								<abbr title="<?php echo $link -> created; ?>"><?php echo $Html -> gen_date("Y-m-d", strtotime($link -> created)); ?></abbr>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
						<?php $p = 5; ?>
						<?php while ($p < 100) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'linksperpage']) && $_COOKIE[$this -> pre . 'linksperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>linksperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				</script>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
	</form>