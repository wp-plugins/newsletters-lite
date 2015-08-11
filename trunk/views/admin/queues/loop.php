<!-- Email Queue Loop -->
<?php
	
$queue_status = $this -> get_option('queue_status');
	
?>

	<form action="?page=<?php echo $this -> sections -> queue; ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action?', $this -> plugin_name); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<?php if (empty($queue_status) || $queue_status == "unpause") : ?>
					<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('pause'); return false;" class="button"><i class="fa fa-pause"></i> <?php _e('Pause', $this -> plugin_name); ?></a>
				<?php else : ?>
					<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('unpause'); return false;" class="button"><i class="fa fa-play"></i> <?php _e('Unpause', $this -> plugin_name); ?></a>
				<?php endif; ?>
				<a href="?page=<?php echo $this -> sections -> queue; ?>&amp;method=clear" title="<?php _e('Clear the email queue', $this -> plugin_name); ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to purge the email queue?', $this -> plugin_name); ?>')) { return false; }" class="button"><i class="fa fa-trash"></i> <?php _e('Clear Queue', $this -> plugin_name); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action" class="widefat" style="width:auto;">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					<option value="send"><?php _e('Send Now', $this -> plugin_name); ?></option>
				</select>				
				<input type="submit" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" class="button action" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<script type="text/javascript">
		function newsletters_pause_queue(status) {
			jQuery.ajax({
				url: newsletters_ajaxurl + '?action=newsletters_pause_queue',
				data: {status:status},
				cache: false,
				type: "POST",
				success: function(response) {					
					if (response == false) {
						alert('<?php _e('Queue could not be paused, please try again', $this -> plugin_name); ?>');
					} else {
						if (status == "unpause") {
							var pause_queue_html = '<i class="fa fa-pause"></i> <?php echo __('Pause', $this -> plugin_name); ?>';
							var pause_queue_action = 'pause';
						} else {
							var pause_queue_html = '<i class="fa fa-play"></i> <?php echo __('Unpause', $this -> plugin_name); ?>';
							var pause_queue_action = 'unpause';
						}
						
						jQuery('#newsletters_pause_queue_button').html(pause_queue_html).attr('onclick', "newsletters_pause_queue('" + pause_queue_action + "'); return false;");		
					}
				}
			});
		}
			
		jQuery(document).ready(function() {
			
		});
		</script>
		
		<?php
		
		$screen_custom = $this -> get_option('screenoptions_subscribers_custom');
		$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 9;
		
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
					<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
						<th><?php _e('Image', $this -> plugin_name); ?></th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('History Email', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-theme_id <?php echo ($orderby == "theme_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Template', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php _e('Attachments', $this -> plugin_name); ?></th>
					<th class="column-error <?php echo ($orderby == "error") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=error&order=' . (($orderby == "error") ? $otherorder : "asc")); ?>">
							<span><?php _e('Error', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Date', $this -> plugin_name); ?></span>
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
					<th class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc")); ?>">
							<span><?php _e('ID', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
						<th><?php _e('Image', $this -> plugin_name); ?></th>
					<?php endif; ?>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('History Email', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-theme_id <?php echo ($orderby == "theme_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc")); ?>">
							<span><?php _e('Template', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php _e('Attachments', $this -> plugin_name); ?></th>
					<th class="column-error <?php echo ($orderby == "error") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=error&order=' . (($orderby == "error") ? $otherorder : "asc")); ?>">
							<span><?php _e('Error', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc")); ?>">
							<span><?php _e('Send Date', $this -> plugin_name); ?></span>
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
				<?php if (empty($queues)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No queued emails found', $this -> plugin_name); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($queues as $queue) : ?>
						<?php 
						
						if (!empty($queue -> subscriber_id)) {
							$subscriber = $Subscriber -> get($queue -> subscriber_id); 
						} elseif (!empty($queue -> user_id)) {
							$user = $this -> userdata($queue -> user_id);
						}
						
						?>
						<?php $class = ($class == "alternate") ? '' : 'alternate'; ?>
						<tr id="queuerow<?php echo $queue -> id; ?>" class="<?php echo $class; ?>">
							<th class="check-column"><input type="checkbox" id="checklist<?php echo $queue -> id; ?>" name="Queue[checklist][]" value="<?php echo $queue -> id; ?>" /></th>
							<td><label for="checklist<?php echo $queue -> id; ?>"><?php echo $queue -> id; ?></label></td>
							<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
								<td>
									<?php if (!empty($subscriber)) : ?>
										<label for="checklist<?php echo $queue -> id; ?>"><?php echo $Html -> get_gravatar($subscriber -> email); ?></label>
									<?php elseif (!empty($user)) : ?>
										<label for="checklist<?php echo $queue -> id; ?>"><?php echo $Html -> get_gravatar($user -> user_email); ?></label>
									<?php endif; ?>
								</td>
							<?php endif; ?>
							<td>
								<?php if (!empty($subscriber)) : ?>
									<a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $subscriber -> id; ?>" class="row-title" title="<?php _e('View this subscriber', $this -> plugin_name); ?>"><?php echo $subscriber -> email; ?></a>
								<?php elseif (!empty($user)) : ?>
									<a href="<?php echo get_edit_user_link($user -> ID); ?>" class="row-title"><?php echo $user -> display_name; ?></a>
									<br/><small><?php echo $user -> user_email; ?></small>
								<?php endif; ?>
								<div class="row-actions">
									<span class="delete"><a onclick="if (!confirm('<?php _e('Are you sure you want to delete this queued email?', $this -> plugin_name); ?>')) { return false; }" class="submitdelete" href="?page=<?php echo $this -> sections -> queue; ?>&amp;method=delete&amp;id=<?php echo $queue -> id; ?>"><?php _e('Delete', $this -> plugin_name); ?></a> |</span>
									<span class="edit"><a href="?page=<?php echo $this -> sections -> queue; ?>&amp;method=send&amp;id=<?php echo $queue -> id; ?>"><?php _e('Send Now', $this -> plugin_name); ?></a></span>
								</div>
							</td>
							<td><label for="checklist<?php echo $queue -> id; ?>"><?php echo $Html -> link(__($queue -> subject), "?page=" . $this -> sections -> history . "&amp;method=view&amp;id=" . $queue -> history_id, array('title' => $queue -> subject)); ?></label></td>
		                    <td>
		                    	<?php $Db -> model = $Theme -> model; ?>
		                    	<?php if (!empty($queue -> theme_id) && $theme = $Db -> find(array('id' => $queue -> theme_id))) : ?>
		                        	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" title="<?php _e('Template Preview:', $this -> plugin_name); ?> <?php echo $theme -> title; ?>"><?php echo $theme -> title; ?></a>
		                        <?php else : ?>
		                        	<?php _e('None', $this -> plugin_name); ?>
		                        <?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php if (!empty($queue -> attachments)) : ?>
		                        	<?php $queue -> attachments = maybe_unserialize($queue -> attachments); ?>
		                        	<ul style="padding:0; margin:0;">
		                            	<?php foreach ($queue -> attachments as $attachment) : ?>
		                                	<li class="<?php echo $this -> pre; ?>attachment">
		                                    	<?php echo $Html -> attachment_link($attachment); ?>
		                                        
		                                    </li>
		                                <?php endforeach; ?>
		                            </ul>
		                        <?php else : ?>
		                        	<?php _e('None', $this -> plugin_name); ?>
		                        <?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php if (!empty($queue -> error)) : ?>
		                    		<span class="wpmlerror"><?php _e('Yes', $this -> plugin_name); ?></span>
		                    		<?php echo $Html -> help($queue -> error); ?>
		                    	<?php else : ?>
		                    		<span class="wpmlsuccess"><?php _e('No', $this -> plugin_name); ?></span>
		                    	<?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php echo $queue -> senddate; ?>
		                    </td>
							<td><label for="checklist<?php echo $queue -> id; ?>"><abbr title="<?php echo $queue -> modified; ?>"><?php echo $Html -> gen_date(false, strtotime($queue -> modified)); ?></abbr></label></td>
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
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'queuesperpage']) && $_COOKIE[$this -> pre . 'queuesperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('emails', $this -> plugin_name); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'queuesperpage'])) : ?>
							<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'queuesperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'queuesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo $this -> pre; ?>queuesperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo $this -> pre; ?>queuessorting=" + field + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			document.cookie = "<?php echo $this -> pre; ?>queues" + field + "dir=" + dir + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
		}
		</script>