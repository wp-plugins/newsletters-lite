<!-- Emails -->
	<?php if ($_GET['page'] == $this -> sections -> history) : ?>
		<ul class="subsubsub">
			<li><?php echo sprintf(__('%s emails', $this -> plugin_name), $paginate -> allcount); ?></li>
		</ul>
		<br class="clear" />
	
		<form id="posts-filter" action="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>" method="get">
	    	<input type="hidden" name="page" value="<?php echo $this -> sections -> history; ?>" />
	    	<input type="hidden" name="method" value="view" />
	    	<input type="hidden" name="id" value="<?php echo $history -> id; ?>" />
	    	
	    	<?php if (!empty($_GET['order']) && !empty($_GET['orderby'])) : ?>
	    		<input type="hidden" name="order" value="<?php echo $_GET['order']; ?>" />
	    		<input type="hidden" name="orderby" value="<?php echo $_GET['orderby']; ?>" />
	    	<?php endif; ?>
	    	
	    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
	    		<input type="hidden" name="<?php echo $this -> pre; ?>searchterm" value="<?php echo esc_attr(stripslashes($_GET[$this -> pre . 'searchterm'])); ?>" />
	    	<?php endif; ?>
	    	
	    	<div class="alignleft actions">
	    		<?php _e('Filters:', $this -> plugin_name); ?>
	    		<select name="status">
		    		<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All sent/unsent', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "sent") ? 'selected="selected"' : ''; ?> value="sent"><?php _e('Sent emails', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "unsent") ? 'selected="selected"' : ''; ?> value="unsent"><?php _e('Unsent emails', $this -> plugin_name); ?></option>
	    		</select>
	    		<select name="read">
		    		<option <?php echo (!empty($_GET['read']) && $_GET['read'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All read/unread', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['read']) && $_GET['read'] == "Y") ? 'selected="selected"' : ''; ?> value="Y"><?php _e('Read', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['read']) && $_GET['read'] == "N") ? 'selected="selected"' : ''; ?> value="N"><?php _e('Unread', $this -> plugin_name); ?></option>
	    		</select>
	    		<select name="clicked">
		    		<option <?php echo (!empty($_GET['clicked']) && $_GET['clicked'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All clicked/unclicked', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['clicked']) && $_GET['clicked'] == "Y") ? 'selected="selected"' : ''; ?> value="Y"><?php _e('Clicked', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['clicked']) && $_GET['clicked'] == "N") ? 'selected="selected"' : ''; ?> value="N"><?php _e('Unclicked', $this -> plugin_name); ?></option>
	    		</select>
	    		<select name="bounced">
		    		<option <?php echo (!empty($_GET['bounced']) && $_GET['bounced'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All bounced/unbounced', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['bounced']) && $_GET['bounced'] == "Y") ? 'selected="selected"' : ''; ?> value="Y"><?php _e('Bounced', $this -> plugin_name); ?></option>
		    		<option <?php echo (!empty($_GET['bounced']) && $_GET['bounced'] == "N") ? 'selected="selected"' : ''; ?> value="N"><?php _e('Unbounced', $this -> plugin_name); ?></option>
	    		</select>
	    		<input type="submit" name="filter" value="<?php _e('Filter', $this -> plugin_name); ?>" class="button button-primary" />
	    	</div>
	    </form>
	    <br class="clear" />
	    
	    <form onsubmit="if (!confirm('<?php _e('Are you sure you want to apply this action?', $this -> plugin_name); ?>')) { return false; }" action="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=emails-mass'); ?>" method="post" id="newsletters-emails-form">
		    <input type="hidden" name="id" value="<?php echo $history -> id; ?>" />
		    
		    <div class="tablenav">
				<?php if ($_GET['page'] == $this -> sections -> history) : ?>
			    	<div class="alignleft actions">
			    		<?php $exportlink = ($_GET['page'] == $this -> sections -> history) ? '?page=' . $this-> sections -> history . '&amp;method=exportsent&amp;history_id=' . $history -> id : '?page='; ?>
			        	<a onclick="jQuery('#newsletters-emails-action').val('export'); jQuery('#newsletters-emails-form').removeAttr('onsubmit').submit(); return false;" href="" title="<?php _e('Export sent emails to CSV', $this -> plugin_name); ?>" class="newsletters-icon-download button"> <?php _e('Export', $this -> plugin_name); ?></a>
			        </div>
			        <div class="alignleft actions">
				        <select name="action" id="newsletters-emails-action" onchange="emails_change_action(this.value);">
					        <option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					        <optgroup label="<?php _e('Emails', $this -> plugin_name); ?>">
					        	<option value="export"><?php _e('Export', $this -> plugin_name); ?></option>
					        	<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					        </optgroup>
					        <optgroup label="<?php _e('Subscribers', $this -> plugin_name); ?>">
					        	<option value="subscribers_delete"><?php _e('Delete Subscribers', $this -> plugin_name); ?></option>
					        	<option value="subscribers_addlists"><?php _e('Add Lists (appends)...', $this -> plugin_name); ?></option>
					        	<option value="subscribers_setlists"><?php _e('Set Lists (overwrites)...', $this -> plugin_name); ?></option>
					        	<option value="subscribers_dellists"><?php _e('Remove Lists...', $this -> plugin_name); ?></option>
					        </optgroup>
				        </select>
				        <input type="submit" name="apply" value="<?php _e('Apply', $this -> plugin_name); ?>" class="button" />
			        </div>
			    <?php endif; ?>    
		    	<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		    </div>
		    
		    <script type="text/javascript">
			function emails_change_action(action) {
				jQuery('#listsdiv').hide();
				
				if (action == "subscribers_addlists" || action == "subscribers_setlists" || action == "subscribers_dellists") {
					jQuery('#listsdiv').show();
				}
			}
			</script>
		    
		    <div id="listsdiv" style="display:none;">
				<?php if ($lists = $Mailinglist -> select(true)) : ?>
					<p>
						<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="1" id="checkboxall" onclick="jqCheckAll(this, false, 'lists');" /> <?php _e('Select all', $this -> plugin_name); ?></label><br/>
						<?php foreach ($lists as $lid => $lval) : ?>
							<label><input type="checkbox" name="lists[]" value="<?php echo $lid; ?>" /> <?php echo $lval; ?> (<?php echo $SubscribersList -> count(array('list_id' => $lid)); ?> <?php _e('subscribers', $this -> plugin_name); ?>)</label><br/>
						<?php endforeach; ?>
					</p>
				<?php else : ?>
					<p class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</div>
	<?php endif; ?>
	<!-- endif history section only -->
	    
	    <?php
	    
	    $orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 8;
	    
	    ?>
	
		<table class="widefat">
	    	<thead>
	        	<tr>
		        	<th class="check-column">
			        	<input type="checkbox" name="checkboxall" value="1" id="checkboxall" />
		        	</th>
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
					<th class="column-clicked <?php echo ($orderby == "clicked") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=clicked&order=' . (($orderby == "clicked") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('Clicked', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
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
		        	<th class="check-column">
			        	<input type="checkbox" name="checkboxall" value="1" id="checkboxall" />
		        	</th>
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
					<th class="column-clicked <?php echo ($orderby == "clicked") ? 'sorted ' . $order : 'sortable desc'; ?>">
						<a href="<?php echo $Html -> retainquery('orderby=clicked&order=' . (($orderby == "clicked") ? $otherorder : "asc")); ?>#emailssent">
							<span><?php _e('Clicked', $this -> plugin_name); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
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
			            	<th class="check-column">
					        	<input type="checkbox" name="emails[]" value="<?php echo $email -> id; ?>" id="emails_<?php echo $email -> id; ?>" />
				        	</th>
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
		                    	<?php if (!empty($email -> mailinglists)) : ?>
		                    		<?php
										
									$mailinglists = maybe_unserialize($email -> mailinglists);
									if (is_array($mailinglists)) {
										$m = 1;
										foreach ($mailinglists as $list_id) {								
											$Db -> model = $Mailinglist -> model;
											$mailinglist = $Db -> find(array('id' => $list_id));
											echo $Html -> link(__($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list_id);
											if ($m < count($mailinglists)) { echo ', '; }
											$m++;
										}
									}
									
									?>
		                    	<?php elseif (!empty($email -> mailinglist_id)) : ?>
		                    		<?php $Db -> model = $Mailinglist -> model; ?>
									<?php $mailinglist = $Db -> find(array('id' => $email -> mailinglist_id)); ?>
									<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $email -> mailinglist_id; ?>"><?php echo __($mailinglist -> title); ?></a>
		                    	<?php else : ?>
		                    		<?php _e('None', $this -> plugin_name); ?>
		                    	<?php endif; ?>
		                    </td>
		                    <td>
		                    	<span class="<?php echo $this -> pre; ?><?php echo ($email -> status == "sent") ? 'success' : 'error'; ?>"><?php echo ($email -> status == "sent") ? __('Sent', $this -> plugin_name) : __('Unsent', $this -> plugin_name); ?></span>
		                    </td>
		                    <td>
		                    	<?php echo (!empty($email -> read) && $email -> read == "Y") ? '<span class="newsletters_success">' . __('Yes', $this -> plugin_name) : '<span class="newsletters_error">' . __('No', $this -> plugin_name); ?></span>
		                    </td>
		                    <td>
		                    	<?php
		                    	
		                    	if (!empty($email -> subscriber_id)) {
		                    		$clicked = $this -> Click -> count(array('history_id' => $email -> history_id, 'subscriber_id' => $email -> subscriber_id));
								} elseif (!empty($user)) {
									$clicked = $this -> Click -> count(array('history_id' => $email -> history_id, 'user_id' => $email -> user_id));
								}
								
								echo (empty($clicked)) ? '<span class="' . $this -> pre . 'error">' . __('No', $this -> plugin_name) . '</span>' : '<span class="' . $this -> pre . 'success">' . __('Yes', $this -> plugin_name) . '</span> (<a href="?page=' . $this -> sections -> clicks . '&amp;history_id=' . $email -> history_id . '&amp;subscriber_id=' . $email -> subscriber_id . '">' . $clicked . '</a>)'; 
		                    	
		                    	?>
		                    </td>
		                    <td>
		                    	<?php echo (!empty($email -> bounced) && $email -> bounced == "Y") ? '<span class="' . $this -> pre . 'error">' . __('Yes', $this -> plugin_name) . '</span>' : '<span class="newsletters_success">' . __('No', $this -> plugin_name) . '</span>'; ?>
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
	<?php if ($_GET['page'] == $this -> sections -> history) : ?>
		</form>
	<?php endif; ?>