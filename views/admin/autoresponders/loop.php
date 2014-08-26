<?php if (!empty($autoresponders)) : ?>
	<form action="?page=<?php echo $this -> sections -> autoresponders; ?>&amp;method=mass" onsubmit="if (!confirm('<?php _e('Are you sure you want to execute this action on the selected autoresponders?', $this -> plugin_name); ?>')) { return false; }" method="post">
    	<div class="tablenav">
        	<div class="alignleft actions">
            	<a href="?page=<?php echo $this -> sections -> autoresponderemails; ?>" class="button"><?php _e('Autoresponder Emails', $this -> plugin_name); ?></a>
        	</div>
        	<div class="alignleft actions">
				<select name="action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
                    <option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
                    <option value="activate"><?php _e('Activate', $this -> plugin_name); ?></option>
                    <option value="deactivate"><?php _e('Deactivate', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" class="button" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
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
                	<th class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo $this -> sections -> autoresponders; ?>', 'autoresponderslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
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
                    <th><?php _e('List(s)', $this -> plugin_name); ?></th>
                    <th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Newsletter', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-delay <?php echo ($orderby == "delay") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=delay&order=' . (($orderby == "delay") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Delay', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>">
							<span><?php _e('Status', $this -> plugin_name); ?></span>
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
                	<th class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo $this -> sections -> autoresponders; ?>', 'autoresponderslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
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
                    <th><?php _e('List(s)', $this -> plugin_name); ?></th>
                    <th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Newsletter', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-delay <?php echo ($orderby == "delay") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=delay&order=' . (($orderby == "delay") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Delay', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>">
							<span><?php _e('Status', $this -> plugin_name); ?></span>
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
            	<?php foreach ($autoresponders as $autoresponder) : ?>
					<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                    	<th class="check-column"><input type="checkbox" name="autoresponderslist[]" value="<?php echo $autoresponder -> id; ?>" id="checklist<?php echo $autoresponder -> id; ?>" /></th>
                        <td><label for="checklist<?php echo $autoresponder -> id; ?>"><?php echo $autoresponder -> id; ?></label></td>
                        <td>
                        	<strong><a class="row-title" href="?page=<?php echo $this -> sections -> autoresponders; ?>&amp;method=save&amp;id=<?php echo $autoresponder -> id; ?>" title="<?php echo $autoresponder -> title; ?>"><?php echo $autoresponder -> title; ?></a></strong>
                            <?php if (!empty($autoresponder -> pending)) : ?><small>(<?php echo $Html -> link($autoresponder -> pending, '?page=' . $this -> sections -> autoresponderemails . '&amp;id=' . $autoresponder -> id . '&amp;status=unsent'); ?> <?php _e('pending emails', $this -> plugin_name); ?>)</small><?php endif; ?>
                            <div class="row-actions">
                            	<span class="edit"><?php echo $Html -> link(__('Edit', $this -> plugin_name), '?page=' . $this -> sections -> autoresponders . '&amp;method=save&amp;id=' . $autoresponder -> id); ?> |</span>
                                <span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> autoresponders . '&amp;method=delete&amp;id=' . $autoresponder -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this autoresponder?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?> |</span>
                                <span class="view"><?php echo $Html -> link(__('Emails', $this -> plugin_name), '?page=' . $this -> sections -> autoresponderemails . '&amp;status=all&amp;id=' . $autoresponder -> id); ?></span>
                            </div>
                        </td>
                        <td>
                        	<?php if (!empty($autoresponder -> mailinglists)) : ?>
                            	<?php $m = 1; ?>
                            	<?php foreach ($autoresponder -> mailinglists as $mailinglist) : ?>
                                	<?php echo $Html -> link($mailinglist -> title, '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id); ?>
                                    <?php if ($m < count($autoresponder -> mailinglists)) : ?>
										<?php echo ', '; ?>
                                    <?php endif; ?>
                                    <?php $m++; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                            
                            <?php endif; ?>
                        </td>
                        <td>
                        	<?php $Db -> model = $History -> model; ?>
                        	<?php if ($history = $Db -> find(array('id' => $autoresponder -> history_id))) : ?>
                            	<?php echo $Html -> link(__($history -> subject), '?page=' . $this -> sections -> history . '&amp;method=view&amp;id=' . $history -> id); ?>
							<?php else : ?>
                            	<?php _e('None', $this -> plugin_name); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                        	<?php if (empty($autoresponder -> delay)) : ?>
                            	<?php _e('Immediately', $this -> plugin_name); ?>
                            <?php else : ?>
                            	<?php echo $autoresponder -> delay; ?> <?php echo $autoresponder -> delayinterval; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                        	<?php if (!empty($autoresponder -> status) && $autoresponder -> status == "inactive") : ?>
                            	<span class="<?php echo $this -> pre; ?>error"><?php _e('Inactive', $this -> plugin_name); ?></span>
                            <?php else : ?>
                            	<span class="<?php echo $this -> pre; ?>success"><?php _e('Active', $this -> plugin_name); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><abbr title="<?php echo $autoresponder -> modified; ?>"><?php echo date_i18n("Y-m-d", strtotime($autoresponder -> modified)); ?></abbr></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="tablenav">
        	<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
						<?php $p = 5; ?>
						<?php while ($p < 100) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'autorespondersperpage']) && $_COOKIE[$this -> pre . 'autorespondersperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>autorespondersperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo $this -> pre; ?>autoresponderssorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					document.cookie = "<?php echo $this -> pre; ?>autoresponders" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				</script>
			</div>
        	<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
    </form>
<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No autoresponders found, please add one.', $this -> plugin_name); ?></p>
<?php endif; ?>