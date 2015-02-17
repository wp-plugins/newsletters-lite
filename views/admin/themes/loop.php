
	<form action="?page=<?php echo $this -> sections -> themes; ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected templates?', $this -> plugin_name); ?>')) { return false; }" id="themesform">
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" class="alignleft widefat" style="width:auto;">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" class="button-secondary action alignleft" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
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
					<th class="check-column"><input type="checkbox" id="checkboxall" name="" value="" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc")); ?>">
							<span><?php _e('Title', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" id="checkboxall" name="" value="" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc")); ?>">
							<span><?php _e('Title', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($themes)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No templates were found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($themes as $theme) : ?>
					<?php $class = ($class == "alternate") ? '' : 'alternate'; ?>
						<tr class="<?php echo $class; ?>" id="templaterow<?php echo $theme -> id; ?>">
							<th class="check-column"><input id="checklist<?php echo $theme -> id; ?>" type="checkbox" name="themeslist[]" value="<?php echo $theme -> id; ?>" /></th>
							<td><label for="checklist<?php echo $theme -> id; ?>"><?php echo $theme -> id; ?></label></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo $this -> sections -> themes; ?>&amp;method=save&amp;id=<?php echo $theme -> id; ?>"><?php echo $theme -> title; ?></a></strong>
								<?php echo (!empty($theme -> defsystem) && $theme -> defsystem == "Y") ? ' <small>(' . __('System Default', $this -> plugin_name) . ' <a onclick="if (!confirm(\'' . __('Are you sure you want to remove this template as the system default?', $this -> plugin_name) . '\')) { return false; }" class="newsletters-icon-delete-regular" href="' . admin_url('admin.php?page=' . $this -> sections -> themes . '&method=remove_defaultsystem&id=' . $theme -> id) . '"></a>)</small> ' . $Html -> help(__('This template is used for system emails such as confirmation, unsubscribe, authentication and other system notifications.', $this -> plugin_name)) : ''; ?>
								<?php echo (!empty($theme -> def) && $theme -> def == "Y") ? ' <small>(' . __('Send Default', $this -> plugin_name) . ' <a onclick="if (!confirm(\'' . __('Are you sure you want to remove this template as the sending default?', $this -> plugin_name) . '\')) { return false; }" class="newsletters-icon-delete-regular" href="' . admin_url('admin.php?page=' . $this -> sections -> themes . '&method=remove_default&id=' . $theme -> id) . '"></a>)</small> ' . $Html -> help(__('This template is used for sending and will be pre-selected under Newsletters > Create Newsletter for example.', $this -> plugin_name)) : ''; ?>
								<div class="row-actions">
									<span class="edit"><?php echo $Html -> link(__('Edit', $this -> plugin_name), '?page=' . $this -> sections -> themes . '&amp;method=save&amp;id=' . $theme -> id); ?> |</span>
		                            <?php if (empty($theme -> def) || $theme -> def == "N") : ?><span class="edit"><?php echo $Html -> link(__('Set as Send Default', $this -> plugin_name), '?page=' . $this -> sections -> themes . '&amp;method=default&amp;id=' . $theme -> id); ?> |</span><?php endif; ?>
		                            <?php if (empty($theme -> defsystem) || $theme -> defsystem == "N") : ?><span class="edit"><?php echo $Html -> link(__('Set as System Default', $this -> plugin_name), '?page=' . $this -> sections -> themes . '&method=defaultsystem&id=' . $theme -> id); ?> |</span><?php endif; ?>
									<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> themes . '&amp;method=delete&amp;id=' . $theme -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this template?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?> |</span>
									<span class="view"><?php echo $Html -> link(__('Preview', $this -> plugin_name), "", array('onclick' => "jQuery(this).colorbox({iframe:true, width:'80%', height:'80%', href:'" . home_url() . '/?' . $this -> pre . 'method=themepreview&id=' . $theme -> id . "'}); return false;", 'title' => __('Template Preview: ', $this -> plugin_name) . $theme -> title)); ?></span>
								</div>
							</td>
							<td><label for="checklist<?php echo $theme -> id; ?>"><abbr title="<?php echo $theme -> modified; ?>"><?php echo date("Y-m-d", strtotime($theme -> modified)); ?></abbr></label></td>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'themesperpage']) && $_COOKIE[$this -> pre . 'themesperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					document.cookie = "<?php echo $this -> pre; ?>themesperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo $this -> pre; ?>themessorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					document.cookie = "<?php echo $this -> pre; ?>themes" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				</script>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
	</form>