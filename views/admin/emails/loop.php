<!-- Emails -->
	<div class="tablenav">
		<?php if ($_GET['page'] == $this -> sections -> history) : ?>
	    	<div class="alignleft actions">
	    		<?php $exportlink = ($_GET['page'] == $this -> sections -> history) ? '?page=' . $this-> sections -> history . '&amp;method=exportsent&amp;history_id=' . $history -> id : '?page='; ?>
	        	<a href="<?php echo $exportlink; ?>" title="<?php _e('Export sent emails to CSV', $this -> plugin_name); ?>" class="button"><img border="0" style="width:12px; height:12px;" src="<?php echo $this -> url(); ?>/images/icons/csv-16.png" alt="csv" /> <?php _e('Export to CSV', $this -> plugin_name); ?></a>
	        </div>
	    <?php endif; ?>    
    	<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
    </div>
    
    <?php
    
    $orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
	$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
	$otherorder = ($order == "desc") ? 'asc' : 'desc';
	
	$colspan = 7;
    
    ?>

	<table class="widefat">
    	<thead>
        	<tr>
        		<?php if ($_GET['page'] == $this -> sections -> history) : ?>
            		<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
            	<?php elseif ($_GET['page'] == $this -> sections -> subscribers) : ?>
            		<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('History Email', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
            	<?php endif; ?>
                <th class="column-mailinglist_id <?php echo ($orderby == "mailinglist_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=mailinglist_id&order=' . (($orderby == "mailinglist_id") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Mailing List(s)', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Sent', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-read <?php echo ($orderby == "read") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=read&order=' . (($orderby == "read") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Opened', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th><?php _e('Clicked', $this -> plugin_name); ?></th>
                <th class="column-bounced <?php echo ($orderby == "bounced") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=bounced&order=' . (($orderby == "bounced") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Bounced', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Sent Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
            </tr>
        </thead>
        <tfoot>
        	<tr>
        		<?php if ($_GET['page'] == $this -> sections -> history) : ?>
            		<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('Subscriber', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
            	<?php elseif ($_GET['page'] == $this -> sections -> subscribers) : ?>
            		<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('History Email', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
            	<?php endif; ?>
                <th class="column-mailinglist_id <?php echo ($orderby == "mailinglist_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=mailinglist_id&order=' . (($orderby == "mailinglist_id") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Mailing List(s)', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Sent', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-read <?php echo ($orderby == "read") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=read&order=' . (($orderby == "read") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Opened', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th><?php _e('Clicked', $this -> plugin_name); ?></th>
                <th class="column-bounced <?php echo ($orderby == "bounced") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=bounced&order=' . (($orderby == "bounced") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Bounced', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
                <th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc")); ?>#emailssent">
						<span><?php _e('Sent Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
            </tr>
        </tfoot>
    	<tbody>
    		<?php if (empty($emails)) : ?>
    			<tr class="no-items">
					<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No emails found', $this -> plugin_name); ?></td>
				</tr>
    		<?php else : ?>
	        	<?php $class = false; ?>
	        	<?php foreach ($emails as $email) : ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	            		<?php if ($_GET['page'] == $this -> sections -> history) : ?>
		                	<td>
		                		<?php
		                		
		                		if (!empty($email -> subscriber_id)) {
			                		$Db -> model = $Subscriber -> model;
			                		$subscriber = $Db -> find(array('id' => $email -> subscriber_id));
		                		} elseif (!empty($email -> user_id)) {
			                		$user = $this -> userdata($email -> user_id);
		                		}
		                		
		                		?>
		                        
		                        <?php if (!empty($subscriber)) : ?>
		                        	<strong><a class="row-title" href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $email -> subscriber_id; ?>"><?php echo $subscriber -> email; ?></a></strong>
		                        <?php elseif (!empty($user)) : ?>
		                        	<strong><a class="row-title" href="<?php echo get_edit_user_link($user -> ID); ?>"><?php echo $user -> user_email; ?></a></strong>
		                        <?php endif; ?>
		                    </td>
		                <?php elseif ($_GET['page'] == $this -> sections -> subscribers) : ?>
		                	<td>
		                		<?php
		                		
		                		$Db -> model = $History -> model;
		                		$history = $Db -> find(array('id' => $email -> history_id)); 
		                		
		                		?>
		                		<?php echo $Html -> link(__($history -> subject), '?page=' . $this -> sections -> history . '&amp;method=view&amp;id=' . $history -> id, array('class' => "row-title")); ?>
		                	</td>
		                <?php endif; ?>
	                    <td>
	                    	<?php if (!empty($subscriber)) : ?>
		                    	<?php if (empty($email -> mailinglists)) : ?>
		                    		<?php $Db -> model = $Mailinglist -> model; ?>
									<?php $mailinglist = $Db -> find(array('id' => $email -> mailinglist_id)); ?>
									<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $email -> mailinglist_id; ?>"><?php echo __($mailinglist -> title); ?></a>
								<?php else : ?>
									<?php
									
									$mailinglists = maybe_unserialize($email -> mailinglists);
									$m = 1;
									foreach ($mailinglists as $list_id) {
										$Db -> model = $Mailinglist -> model;
										$mailinglist = $Db -> find(array('id' => $list_id));
										echo $Html -> link(__($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list_id);
										if ($m < count($mailinglists)) { echo ', '; }
										$m++;
									}
									
									?>
								<?php endif; ?>
							<?php elseif (!empty($user)) : ?>
								<?php _e('None', $this -> plugin_name); ?>
							<?php endif; ?>
	                    </td>
	                    <td>
	                    	<span class="<?php echo $this -> pre; ?><?php echo ($email -> status == "sent") ? 'success' : 'error'; ?>"><?php echo ($email -> status == "sent") ? __('Sent', $this -> plugin_name) : __('Unsent', $this -> plugin_name); ?></span>
	                    </td>
	                    <td>
	                    	<?php echo (!empty($email -> read) && $email -> read == "Y") ? '<span style="color:green;">' . __('Yes', $this -> plugin_name) : '<span class="' . $this -> pre . 'error">' . __('No', $this -> plugin_name); ?></span>
	                    </td>
	                    <td>
	                    	<?php
	                    	
	                    	if (!empty($subscriber)) {
	                    		$clicked = $this -> Click -> count(array('history_id' => $history -> id, 'subscriber_id' => $email -> subscriber_id));
							} elseif (!empty($user)) {
								$clicked = $this -> Click -> count(array('history_id' => $history -> id, 'user_id' => $email -> user_id));
							}
							
							echo (empty($clicked)) ? '<span class="' . $this -> pre . 'error">' . __('No', $this -> plugin_name) . '</span>' : '<span class="' . $this -> pre . 'success">' . __('Yes', $this -> plugin_name) . '</span> (' . $clicked . ')'; 
	                    	
	                    	?>
	                    </td>
	                    <td>
	                    	<?php echo (!empty($email -> bounced) && $email -> bounced == "Y") ? '<span class="' . $this -> pre . 'error">' . __('Yes', $this -> plugin_name) . '</span>' : '<span style="color:green;">' . __('No', $this -> plugin_name) . '</span>'; ?>
	                    </td>
	                    <td>
	                    	<abbr title="<?php echo $email -> modified; ?>"><?php echo date_i18n("Y-m-d", strtotime($email -> modified)); ?></abbr>
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
					<?php $s = 5; ?>
					<?php while ($s <= 200) : ?>
						<option <?php echo (isset($_COOKIE[$this -> pre . 'emailsperpage']) && $_COOKIE[$this -> pre . 'emailsperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('emails', $this -> plugin_name); ?></option>
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
			document.cookie = "<?php echo $this -> pre; ?>emailsperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
		}
	}
	</script>