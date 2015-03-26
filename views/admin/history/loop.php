
	<form action="?page=<?php echo $this -> sections -> history; ?>&amp;method=mass" id="newsletters-history-form" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected history emails?', $this -> plugin_name); ?>')) { return false; }" method="post">
		<div class="tablenav">
			<div class="alignleft actions">
				<?php $rssfeed = $this -> get_option('rssfeed'); ?>
				<?php if (!empty($rssfeed) && $rssfeed == "Y" && apply_filters($this -> pre . '_admin_history_rsslink', true)) : ?>
					<a href="<?php echo home_url(); ?>/?feed=newsletters" title="<?php _e('RSS feed for all newsletter history', $this -> plugin_name); ?>" class="newsletters-icon-rss button"> <?php _e('RSS', $this -> plugin_name); ?></a>
				<?php endif; ?>
				<?php if (apply_filters($this -> pre . '_admin_history_exportlink', true)) : ?>
                	<a onclick="jQuery('#newsletters-history-action').val('export'); jQuery('#newsletters-history-form').removeAttr('onsubmit').submit(); return false;" href="" class="newsletters-icon-download button"> <?php _e('Export', $this -> plugin_name); ?></a>
				<?php endif; ?>
				<a href="<?php echo $this -> url; ?>&amp;method=clear" title="<?php _e('Clear all email history', $this -> plugin_name); ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to clear the email history?', $this -> plugin_name); ?>')) { return false; }" class="button newsletters_delete_link"> <?php _e('Clear', $this -> plugin_name); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action" id="newsletters-history-action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					<option value="export"><?php _e('Export', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" class="button-secondary" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
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
					<th class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></th>
					<?php $colspan++; ?>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-subject <?php echo ($orderby == "subject") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subject&order=' . (($orderby == "subject") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subject', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-mailinglists"><?php _e('List(s)', $this -> plugin_name); ?></th>
					<?php $colspan++; ?>
                    <th class="column-theme <?php echo ($orderby == "theme_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Template', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-stats" nowrap="nowrap"><?php _e('Open / Unsub / Bounce / Clicks', $this -> plugin_name); ?></th>
					<?php $colspan++; ?>
					<th class="column-sent <?php echo ($orderby == "sent") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=sent&order=' . (($orderby == "sent") ? $otherorder : "asc")); ?>">
							<span><?php _e('Status', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-recurring <?php echo ($orderby == "recurring") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=recurring&order=' . (($orderby == "recurring") ? $otherorder : "asc")); ?>">
							<span><?php _e('Recurring', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-post_id <?php echo ($orderby == "post_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=post_id&order=' . (($orderby == "post_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Post', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<?php if (apply_filters($this -> pre . '_admin_history_authorcolumn', true)) : ?>
	                    <th class="column-author <?php echo ($orderby == "user_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
							<a href="<?php echo $Html -> retainquery('orderby=user_id&order=' . (($orderby == "user_id") ? $otherorder : "asc")); ?>">
								<span><?php _e('Author', $this -> plugin_name); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-attachments"><?php _e('Attachments', $this -> plugin_name); ?></th>
					<?php $colspan++; ?>
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
					<th class="column-subject <?php echo ($orderby == "subject") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subject&order=' . (($orderby == "subject") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subject', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php _e('List(s)', $this -> plugin_name); ?></th>
                    <th class="column-theme <?php echo ($orderby == "theme_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Template', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-stats" nowrap="nowrap"><?php _e('Open / Unsub / Bounce / Clicks', $this -> plugin_name); ?></th>
					<th class="column-sent <?php echo ($orderby == "sent") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=sent&order=' . (($orderby == "sent") ? $otherorder : "asc")); ?>">
							<span><?php _e('Status', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-recurring <?php echo ($orderby == "recurring") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=recurring&order=' . (($orderby == "recurring") ? $otherorder : "asc")); ?>">
							<span><?php _e('Recurring', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-post_id <?php echo ($orderby == "post_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=post_id&order=' . (($orderby == "post_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Post', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (apply_filters($this -> pre . '_admin_history_authorcolumn', true)) : ?>
                    <th class="column-author <?php echo ($orderby == "user_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=user_id&order=' . (($orderby == "user_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Author', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php endif; ?>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>">
							<span><?php _e('Date', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-attachments"><?php _e('Attachments', $this -> plugin_name); ?></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($histories)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No history emails were found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = ''; ?>
					<?php foreach ($histories as $email) : ?>
					<tr id="historyrow<?php echo $email -> id; ?>" class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
						<th class="check-column"><input id="checklist<?php echo $email -> id; ?>" type="checkbox" name="historylist[]" value="<?php echo $email -> id; ?>" /></th>
						<td><label for="checklist<?php echo $email -> id; ?>"><?php echo $email -> id; ?></label></td>
						<td>
							<strong><a class="row-title" href="?page=<?php echo $this -> sections -> history; ?>&amp;method=view&amp;id=<?php echo $email -> id; ?>" title="<?php _e('View this email', $this -> plugin_name); ?>"><?php echo __($email -> subject); ?></a></strong>
							<div class="row-actions">
								<span class="edit"><?php echo $Html -> link(__('Send/Edit', $this -> plugin_name), '?page=' . $this -> sections -> send . '&amp;method=history&amp;id=' . $email -> id); ?> |</span>
								<span class="delete"><?php echo $Html -> link(__('Delete', $this -> plugin_name), '?page=' . $this -> sections -> history . '&amp;method=delete&amp;id=' . $email -> id, array('class' => "submitdelete", 'onclick' => "if (!confirm('" . __('Are you sure you want to delete this email?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
								<span class="edit"><?php echo $Html -> link(__('Duplicate', $this -> plugin_name), '?page=' . $this -> sections -> history . '&amp;method=duplicate&amp;id=' . $email -> id); ?> |</span>
								<span class="view"><?php echo $Html -> link(__('View', $this -> plugin_name), '?page=' . $this -> sections -> history . '&amp;method=view&amp;id=' . $email -> id); ?></span>
							</div>
						</td>
						<td>
							<?php if (!empty($email -> mailinglists)) : ?>
								<?php $m = 1; ?>
								<?php $mailinglists = maybe_unserialize($email -> mailinglists); ?>
								<?php if (!empty($mailinglists) && (is_array($mailinglists) || is_object($mailinglists))) : ?>
									<?php foreach ($mailinglists as $mailinglist_id) : ?>
										<?php $mailinglist = $Mailinglist -> get($mailinglist_id, false); ?>
										<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $mailinglist_id; ?>" title="<?php echo __($mailinglist -> title); ?>"><?php echo __($mailinglist -> title); ?></a><?php echo ($m < count($mailinglists)) ? ', ' : ''; ?>
										<?php $m++; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php else : ?>
								<?php _e('none', $this -> plugin_name); ?>
							<?php endif; ?>
						</td>
	                    <td>
	                    	<?php $Db -> model = $Theme -> model; ?>
	                        <?php if (!empty($email -> theme_id) && $theme = $Db -> find(array('id' => $email -> theme_id))) : ?>
	                        	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" title="<?php _e('Template Preview:', $this -> plugin_name); ?> <?php echo $theme -> title; ?>"><?php echo $theme -> title; ?></a>
	                        <?php else : ?>
	                        	<?php _e('None', $this -> plugin_name); ?>
	                        <?php endif; ?>
	                    </td>
						<td nowrap="nowrap">
							<?php $Db -> model = $Email -> model; ?>
							<?php $etotal = $Db -> count(array('history_id' => $email -> id)); ?>
							<?php $eread = $Db -> count(array('history_id' => $email -> id, 'read' => "Y")); ?>
							<?php 
							
							global $wpdb;
							$tracking = (!empty($etotal)) ? ($eread/$etotal) * 100 : 0;
							
							$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $email -> id . "'";
							
							$query_hash = md5($query);
							if ($ob_ebounced = $this -> get_cache($query_hash)) {
								$ebounced = $ob_ebounced;
							} else {
								$ebounced = $wpdb -> get_var($query);
								$this -> set_cache($query_hash, $ebounced);
							}
							
							$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
							
							$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $email -> id . "'";
							
							$query_hash = md5($query);
							if ($ob_eunsubscribed = $this -> get_cache($query_hash)) {
								$eunsubscribed = $ob_eunsubscribed;
							} else {
								$eunsubscribed = $wpdb -> get_var($query);
								$this -> set_cache($query_hash, $eunsubscribed);
							}
							
							$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
							$clicks = $this -> Click -> count(array('history_id' => $email -> id));
							
							?>
							<a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=view&amp;id=<?php echo $email -> id; ?>"><?php echo sprintf("%s&#37; / %s&#37; / %s&#37; / %s", number_format($tracking, 2, '.', ''), number_format($eunsubscribeperc, 2, '.', ''), number_format($ebouncedperc, 2, '.', ''), $clicks); ?></a>
							<?php echo $Html -> help(sprintf(__('%s opened %s, %s unsubscribes %s, %s bounces %s and %s clicks out of %s emails sent out', $this -> plugin_name), '<strong>' . $eread . '</strong>', '(' . ((!empty($etotal)) ? number_format((($eread/$etotal) * 100), 2, '.', '') : 0) . '&#37;)', '<strong>' . $eunsubscribed . '</strong>', '(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', '<strong>' . $ebounced . '</strong>', '(' . number_format($ebouncedperc, 2, '.', '') . '&#37;)', '<strong>' . $clicks . '</strong>', '<strong>' . $etotal . '</strong>')); ?>
							
							<?php
							
							$data = array(
								array(
									'value'		=>	 number_format($tracking, 0, '.', ''),
									'color'		=>	"#46BFBD",
									'highlight'	=>	"#5AD3D1",
									'label'		=>	"Read",
								),
								array(
									'value'		=>	number_format((100 - $tracking), 0, '.', ''),
									'color'		=>	"#949FB1",
									'highlight'	=>	"#A8B3C5",
									'label'		=>	"Unread",
								),
								array(
									'value'		=>	number_format($ebouncedperc, 0, '.', ''),
									'color'		=>	"#F7464A",
									'highlight'	=>	"#FF5A5E",
									'label'		=>	"Bounced",
								),
								array(
									'value'		=>	number_format($eunsubscribeperc, 0, '.', ''),
									'color'		=>	"#FDB45C",
									'highlight'	=>	"#FFC870",
									'label'		=>	"Unsubscribed",
								)
							);
								
							?>
							
							<?php $Html -> pie_chart('email-chart-' . $email -> id, array('width' => 100), $data, $options); ?>
						</td>
						<td>
							<?php if ($email -> scheduled == "Y") : ?>
								<span class="wpmlpending"><?php _e('Scheduled', $this -> plugin_name); ?></span>
								<small>(<abbr title="<?php echo $email -> senddate; ?>"><?php echo date_i18n("Y-m-d", strtotime($email -> senddate)); ?></abbr>)</small>
							<?php elseif ($email -> sent <= 0) : ?>
								<span class="wpmlerror"><?php _e('Draft', $this -> plugin_name); ?></span>
							<?php else : ?>
								<span class="wpmlsuccess"><?php _e('Sent', $this -> plugin_name); ?></span>
								<small>(<?php echo sprintf(__('%s times', $this -> plugin_name), $email -> sent); ?>)</small>
							<?php endif; ?>
	                    </td>
	                    <td>
	                    	<?php if (!empty($email -> recurring) && $email -> recurring == "Y") : ?>
	                    		<?php _e('Yes', $this -> plugin_name); ?>
	                    		<?php $helpstring = sprintf(__('Send every %s %s', $this -> plugin_name), $email -> recurringvalue, $email -> recurringinterval); ?>
	                    		<?php if (!empty($email -> recurringlimit)) : ?><?php $helpstring .= sprintf(__(' and repeat %s times', $this -> plugin_name), $email -> recurringlimit); ?><?php endif; ?>
	                    		<?php $helpstring .= sprintf(__(' starting %s and has been sent %s times already'), $email -> recurringdate, $email -> recurringsent); ?>
	                    		<?php echo $Html -> help($helpstring); ?>
	                    	<?php else : ?>
	                    		<?php _e('No', $this -> plugin_name); ?>
	                    	<?php endif; ?>
	                    </td>
	                    <td>
	                    	<?php if (!empty($email -> post_id)) : ?>
	                    		<?php 
	                    		
	                    		$post = get_post($email -> post_id);
	                    		edit_post_link(__($post -> post_title), null, null, $email -> post_id);
	                    		
	                    		?>
	                    	<?php else : ?>
	                    		<?php _e('None', $this -> plugin_name); ?>
	                    	<?php endif; ?>
	                    </td>
	                    <?php if (apply_filters($this -> pre . '_admin_history_authorcolumn', true)) : ?>
	                    <td>
	                    	<?php if ($user = get_userdata($email -> user_id)) : ?>
	                        	<?php echo $Html -> link($user -> display_name, get_edit_user_link($user -> ID)); ?>
	                        <?php else : ?>
	                        	<?php _e('None', $this -> plugin_name); ?>
	                        <?php endif; ?>
	                    </td>
	                    <?php endif; ?>
						<td><label for="checklist<?php echo $email -> id; ?>"><abbr title="<?php echo $email -> modified; ?>"><?php echo date_i18n("Y-m-d", strtotime($email -> modified)); ?></abbr></label></td>
	                    <td>
	                    	<?php if (!empty($email -> attachments)) : ?>
	                        	<ul style="padding:0; margin:0;">
	                            	<?php foreach ($email -> attachments as $attachment) : ?>
	                                	<li class="<?php echo $this -> pre; ?>attachment"><?php echo $Html -> attachment_link($attachment, false); ?></li>
	                                <?php endforeach; ?>
	                            </ul>
	                        <?php else : ?>
	                        	<?php _e('None', $this -> plugin_name); ?>
	                        <?php endif; ?>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'historiesperpage']) && $_COOKIE[$this -> pre . 'historiesperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo $p; ?>"><?php echo $p; ?> <?php _e('per page', $this -> plugin_name); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo $this -> pre; ?>historiesperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo $this -> pre; ?>historysorting=" + field + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					document.cookie = "<?php echo $this -> pre; ?>history" + field + "dir=" + dir + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
				}
				</script>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
	</form>