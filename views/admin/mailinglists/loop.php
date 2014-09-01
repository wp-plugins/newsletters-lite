
	<form onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected mailing lists?', $this -> plugin_name); ?>')) { return false; }" action="?page=<?php echo $this -> sections -> lists; ?>&amp;method=mass" method="post">
		<div class="tablenav">
			<div class="alignleft">
				<select name="action" class="alignleft" style="width:auto;" onchange="change_action(this.value); return false;">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete Selected', $this -> plugin_name); ?></option>
					<optgroup label="<?php _e('Private', $this -> plugin_name); ?>">
						<option value="private"><?php _e('Set as private', $this -> plugin_name); ?></option>
						<option value="notprivate"><?php _e('Set as NOT private', $this -> plugin_name); ?></option>
					</optgroup>
					<optgroup label="<?php _e('Opt-In', $this -> plugin_name); ?>">
						<option value="singleopt"><?php _e('Set as single opt-in', $this -> plugin_name); ?></option>
						<option value="doubleopt"><?php _e('Set as double opt-in', $this -> plugin_name); ?></option>
					</optgroup>
                    <option value="setgroup"><?php _e('Set Group', $this -> plugin_name); ?></option>
				</select>
                
                <span id="setgroupactiondiv" style="display:none;">
                	<?php if ($groupsselect = $wpmlGroup -> select()) : ?>
                		<label>
	                		<?php _e('Group:', $this -> plugin_name); ?>
	                    	<select name="setgroup_id" id="setgroup_id" class="action">
	                        	<?php foreach ($groupsselect as $group_id => $group_title) : ?>
	                            	<option value="<?php echo $group_id; ?>"><?php echo $group_title; ?></option>
	                            <?php endforeach; ?>
	                        </select>
                		</label>
                    <?php else : ?>
                    	<?php _e('No groups are available.', $this -> plugin_name); ?>
                    <?php endif; ?>
                </span>
                
                <input type="submit" class="button" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
        
        <script type="text/javascript">
		function change_action(action) {		
			jQuery('span[id$="actiondiv"]').hide();
			jQuery('#' + action + 'actiondiv').show();	
		}
		</script>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 0;
		
		?>
        
		<table class="widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></th>
					<?php $colspan++; ?>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc")); ?>">
							<span><?php _e('Title', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th><?php _e('Fields', $this -> plugin_name); ?></th>
					<?php $colspan++; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
	                    <th class="column-group_id <?php echo ($orderby == "group_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=group_id&order=' . (($orderby == "group_id") ? $otherorder : "asc")); ?>">
								<span><?php _e('Group', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-privatelist <?php echo ($orderby == "privatelist") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=privatelist&order=' . (($orderby == "privatelist") ? $otherorder : "asc")); ?>">
								<span><?php _e('Private', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-paid <?php echo ($orderby == "paid") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=paid&order=' . (($orderby == "paid") ? $otherorder : "asc")); ?>">
								<span><?php _e('Paid', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th><?php _e('Subscriptions', $this -> plugin_name); ?></th>
					<?php $colspan++; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th><?php _e('Shortcode', $this -> plugin_name); ?></th>
						<?php $colspan++; ?>
					<?php endif; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></th>
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
					<th><?php _e('Fields', $this -> plugin_name); ?></th>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
	                    <th class="column-group_id <?php echo ($orderby == "group_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=group_id&order=' . (($orderby == "group_id") ? $otherorder : "asc")); ?>">
								<span><?php _e('Group', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-privatelist <?php echo ($orderby == "privatelist") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=privatelist&order=' . (($orderby == "privatelist") ? $otherorder : "asc")); ?>">
								<span><?php _e('Private', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-paid <?php echo ($orderby == "paid") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=paid&order=' . (($orderby == "paid") ? $otherorder : "asc")); ?>">
								<span><?php _e('Paid', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<th><?php _e('Subscriptions', $this -> plugin_name); ?></th>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th><?php _e('Shortcode', $this -> plugin_name); ?></th>
					<?php endif; ?>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($mailinglists)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No mailing lists were found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($mailinglists as $list) : ?>
					<?php $class = ($class == 'alternate') ? '' : 'alternate'; ?>
						<tr class="<?php echo $class; ?>" id="listrow<?php echo $list -> id; ?>">
							<th class="check-column"><input id="checklist<?php echo $list -> id; ?>" type="checkbox" name="mailinglistslist[]" value="<?php echo $list -> id; ?>" /></th>
							<td><label for="checklist<?php echo $list -> id; ?>"><?php echo $list -> id; ?></label></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $list -> id; ?>" title="<?php _e('View the details of this mailing list', $this -> plugin_name); ?>"><?php echo __($list -> title); ?></a></strong>
								<?php if (!empty($list -> adminemail)) : ?>
									<br/><small>(<?php _e('Admin Email:', $this -> plugin_name); ?> <strong><?php echo $list -> adminemail; ?>)</strong></small>
								<?php endif; ?>
								<?php if (!empty($list -> doubleopt) && $list -> doubleopt == "N") : ?>
									<br/><small>(<?php _e('Single Opt-In', $this -> plugin_name); ?>)</small>
								<?php endif; ?>
								<div class="row-actions">
									<span class="edit"><?php echo $Html -> link(__('Edit', $this -> plugin_name), '?page=' . $this -> sections -> lists . '&amp;method=save&amp;id=' . $list -> id); ?> |</span>
									<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> lists . '&amp;method=delete&amp;id=' . $list -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this mailing list?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?> |</span>
									<span class="view"><?php echo $Html -> link(__('View', $this -> plugin_name), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list -> id); ?> |</span>
									<span class="edit"><?php echo $Html -> link(__('Offsite', $this -> plugin_name), '?page=' . $this -> sections -> lists . '&amp;method=offsite&amp;listid=' . $list -> id); ?> |</span>
									<span class="edit"><?php echo $Html -> link(__('Add Subscriber', $this -> plugin_name), '?page=' . $this -> sections -> subscribers . '&amp;method=save&amp;mailinglist_id=' . $list -> id); ?></span>
								</div>
							</td>
							<td><label for="checklist<?php echo $list -> id; ?>"><?php echo $FieldsList -> count_by_list($list -> id); ?></label></td>
							<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
			                    <td>
			                    	<?php $Db -> model = $wpmlGroup -> model; ?>
			                    	<?php if (!empty($list -> group_id)) : ?>
			                        	<?php echo $Html -> link(__($Db -> field('title', array('id' => $list -> group_id))), '?page=' . $this -> sections -> groups . '&amp;method=view&amp;id=' . $list -> group_id); ?>
			                        <?php else : ?>
			                        	<?php _e('none', $this -> plugin_name); ?>
			                        <?php endif; ?>
			                    </td>
								<td><label for="checklist<?php echo $list -> id; ?>"><span style="color:<?php echo (empty($list -> privatelist) || $list -> privatelist == "N") ? 'red;">' . __('No', $this -> plugin_name) : 'green;">' . __('Yes', $this -> plugin_name); ?></span></label></td>
								<td>
									<label for="checklist<?php echo $list -> id; ?>"><span style="color:<?php echo (empty($list -> paid) || $list -> paid == "N") ? 'red;">' . __('No', $this -> plugin_name) : 'green;">' . __('Yes', $this -> plugin_name); ?></span></label>
									<?php if (!empty($list -> paid) && $list -> paid == "Y") : ?>
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
										<small>(<?php echo $intervals[$list -> interval]; ?>)</small>
									<?php endif; ?>	
								</td>
							<?php endif; ?>
							<td><label for="checklist<?php echo $list -> id; ?>"><b><?php echo $SubscribersList -> count(array('list_id' => $list -> id)); ?></b> (<?php echo $SubscribersList -> count(array('list_id' => $list -> id, 'active' => "Y")); ?> <?php _e('active', $this -> plugin_name); ?>)</label></td>
							<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
								<td><code>[newsletters_subscribe list="<?php echo $list -> id; ?>"]</code></td>
							<?php endif; ?>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'listsperpage']) && $_COOKIE[$this -> pre . 'listsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>listsperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo $this -> pre; ?>listssorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					document.cookie = "<?php echo $this -> pre; ?>lists" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				</script>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
	</form>