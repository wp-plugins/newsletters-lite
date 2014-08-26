
	<form action="?page=<?php echo $this -> sections -> autoresponderemails; ?>&amp;method=mass" onsubmit="if (!confirm('<?php _e('Are you sure you want to execute this action on the selected autoresponder emails?', $this -> plugin_name); ?>')) { return false; }" method="post">
    	<div class="tablenav">
        	<div class="alignleft actions">
				<select name="action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
                    <option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
                    <option value="send"><?php _e('Send Selected', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" class="button" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
        
        <?php
        
        $orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		$colspan = 0;
        
        ?>
    
    	<table class="widefat">
        	<thead>
            	<tr>
                	<th class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo $this -> sections -> autoresponderemails; ?>', 'autoresponderemailslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
                	<?php $colspan++; ?>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-autoresponder_id <?php echo ($orderby == "autoresponder_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=autoresponder_id&order=' . (($orderby == "autoresponder_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Autoresponder', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>">
							<span><?php _e('Status', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                </tr>
            </thead>
            <tfoot>
            	<tr>
                	<th class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo $this -> sections -> autoresponderemails; ?>', 'autoresponderemailslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-autoresponder_id <?php echo ($orderby == "autoresponder_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=autoresponder_id&order=' . (($orderby == "autoresponder_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Autoresponder', $this -> plugin_name); ?></span>
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
                    <th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                </tr>
            </tfoot>
        	<tbody>
        		<?php if (empty($autoresponderemails)) : ?>
        			<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No autoresponder emails found', $this -> plugin_name); ?></td>
					</tr>
        		<?php else : ?>
	            	<?php foreach ($autoresponderemails as $aemail) : ?>
	            		<?php
	            		
	            		if (!empty($aemail -> subscriber_id) && !empty($aemail -> list_id)) {
		            		global $wpdb;
		            		$query = "SELECT `active` FROM " . $wpdb -> prefix . $SubscribersList -> table . " WHERE `subscriber_id` = '" . $aemail -> subscriber_id . "' AND `list_id` = '" . $aemail -> list_id . "' LIMIT 1";
		            		
		            		$query_hash = md5($query);
		            		global ${'newsletters_query_' . $query_hash};
		            		if (!empty(${'newsletters_query_' . $query_hash})) {
			            		$active = ${'newsletters_query_' . $query_hash};
		            		} else {
			            		$active = $wpdb -> get_var($query);
			            		${'newsletters_query_' . $query_hash} = $active;
		            		}
		            		
		            		$aemail -> active = $active;
	            		}
	            		
	            		?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	                    	<th class="check-column"><input type="checkbox" name="autoresponderemailslist[]" value="<?php echo $aemail -> id; ?>" id="checklist<?php echo $aemail -> id; ?>" /></th>
	                        <td><label for="checklist<?php echo $aemail -> id; ?>"><?php echo $aemail -> id; ?></label></td>
	                        <td>
	                        	<?php $difference = $Html -> time_difference($aemail -> senddate, date_i18n("Y-m-d H:i:s"), $aemail -> autoresponder -> delayinterval); ?>
	                            <?php if ($difference >= 0) { $daysstring = __('This autoresponder email is due in ' . $difference . ' ' . $aemail -> autoresponder -> delayinterval . ' only.', $this -> plugin_name); } else { $daysstring = ""; }; ?>
	                        	<strong><?php echo $Html -> link($aemail -> subscriber -> email, '?page=' . $this -> sections -> subscribers . '&amp;method=view&amp;id=' . $aemail -> subscriber_id, array('class' => "row-title")); ?></strong>
	                        	<?php if (!empty($aemail -> active)) : ?>(<span class="<?php echo $this -> pre; ?><?php echo ($aemail -> active == "Y") ? 'success' : 'error'; ?>"><?php echo ($aemail -> active == "Y") ? __('active', $this -> plugin_name) : __('inactive', $this -> plugin_name); ?></span>)<?php endif; ?>
	                            <div class="row-actions">
	                            	<?php $sendtext = ($aemail -> status == "unsent") ? __('Send Now', $this -> plugin_name) : __('Send Again', $this -> plugin_name); ?>
	                            	<span class="edit"><?php echo $Html -> link($sendtext, '?page=' . $this -> sections -> autoresponderemails . '&amp;method=send&amp;id=' . $aemail -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to send this autoresponder email now?', $this -> plugin_name) . " " . $daysstring . "')) { return false; }")); ?> |</span>
	                                <span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> autoresponderemails . '&amp;method=delete&amp;id=' . $aemail -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this autoresponder email?', $this -> plugin_name) . "')) { return false; }")); ?></span>
	                            </div>
	                        </td>
	                        <td>
	                        	<?php echo $Html -> link($aemail -> autoresponder -> title, '?page=' . $this -> sections -> autoresponders . '&amp;method=save&amp;id=' . $aemail -> autoresponder_id); ?>
	                        </td>
	                        <td>
	                        	<?php if ($aemail -> status == "sent") : ?>
	                            	<span class="<?php echo $this -> pre; ?>success"><?php _e('Sent', $this -> plugin_name); ?></span>
	                            <?php else : ?>
	                            	<span class="<?php echo $this -> pre; ?>error"><?php _e('Unsent', $this -> plugin_name); ?></span>
	                            <?php endif; ?>
	                        </td>
	                        <td>
	                        	<abbr title="<?php echo $aemail -> created; ?>"><?php echo date_i18n("Y-m-d", strtotime($aemail -> created)); ?></abbr>
	                        </td>
	                        <td>
	                        	<abbr title="<?php echo $aemail -> senddate; ?>"><?php echo date_i18n("Y-m-d", strtotime($aemail -> senddate)); ?></abbr>
	                            <?php $difference = $Html -> time_difference($aemail -> senddate, date_i18n("Y-m-d H:i:s"), $aemail -> autoresponder -> delayinterval); ?>
	                            <?php if ($difference >= 0) : ?>(<?php echo $difference; ?> <?php echo $aemail -> autoresponder -> delayinterval; ?>+)<?php endif; ?>
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
						<?php while ($p < 200) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsperpage']) && $_COOKIE[$this -> pre . 'autoresponderemailsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>autoresponderemailsperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo $this -> pre; ?>autoresponderemailssorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					document.cookie = "<?php echo $this -> pre; ?>autoresponderemails" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				</script>
			</div>
        	<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
    </form>