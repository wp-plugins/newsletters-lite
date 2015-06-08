<?php

$paidsubscriptions = $this -> get_option('subscriptions');

?>

<?php /*<?php if (!empty($subscribers)) : ?>*/ ?>
	<form action="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=mass" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected subscribers?', $this -> plugin_name); ?>')) { return false; };" method="post" id="subscribersform" name="subscribersform">
		<div class="tablenav">
			<div class="alignleft">
                <?php if (!empty($paidsubscriptions) && $paidsubscriptions == "Y") : ?>
                	<a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=check-expired" class="button"><?php _e('Check Expired', $this -> plugin_name); ?></a>
                <?php endif; ?>
                <a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes'); ?>"><?php _e('Unsubscribes', $this -> plugin_name); ?></a>
				<a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces'); ?>"><?php _e('Bounces', $this -> plugin_name); ?></a>
				<select class="widefat" style="width:auto;" name="action" onchange="action_change(this.value);">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete', $this -> plugin_name); ?></option>
					<optgroup label="<?php _e('Mandatory Status', $this -> plugin_name); ?>">
						<option value="mandatory"><?php _e('Set as Mandatory', $this -> plugin_name); ?></option>
						<option value="notmandatory"><?php _e('Set as not Mandatory', $this -> plugin_name);?></option>
					</optgroup>
					<optgroup label="<?php _e('Status', $this -> plugin_name); ?>">
						<option value="active"><?php _e('Activate', $this -> plugin_name); ?></option>
						<option value="inactive"><?php _e('Deactivate', $this -> plugin_name); ?></option>
					</optgroup>
					<optgroup  label="<?php _e('Mailing Lists', $this -> plugin_name); ?>">
						<option value="assignlists"><?php _e('Add Lists (appends)...', $this -> plugin_name); ?></option>
						<option value="setlists"><?php _e('Set Lists (overwrites)...', $this -> plugin_name); ?></option>
					</optgroup>
				</select>
				<input type="submit" name="execute" class="button" value="<?php _e('Apply', $this -> plugin_name); ?>" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
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
        
        <?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$cols = array(
			'id'					=>	__('ID', $this -> plugin_name),
			'image'					=>	__('Image', $this -> plugin_name),
			'email'					=>	__('Email Address', $this -> plugin_name),
			'registered'			=>	__('Registered', $this -> plugin_name),
			'mandatory'				=>	__('Mandatory', $this -> plugin_name),
			'lists'					=>	__('List(s)', $this -> plugin_name),
			'bouncecount'			=>	__('Bounces', $this -> plugin_name),
		);
		
		$screen_custom = $this -> get_option('screenoptions_subscribers_custom');
		
		if ($screen_fields = $this -> get_option('screenoptions_subscribers_fields')) {
			global $Db, $Field;
			$columns = array();
			
			foreach ($screen_fields as $field_id) {
				$Db -> model = $Field -> model;
				$field = $Db -> find(array('id' => $field_id));
				$columns[$field -> slug] = $field;
				$cols[$field -> slug] = __($field -> title);
			}
		}
		
		$cols['created'] = __('Date', $this -> plugin_name);
		
		$cols = apply_filters('newsletters_admin_subscribers_table_columns', $cols);
		$colspan = count($cols);
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></th>
					<?php
						
					ob_start();
						
					if (!empty($cols)) {
						foreach ($cols as $column_name => $column_value) {
							switch ($column_name) {
								case 'lists'					:
								case 'image'					:
									$style = "";
									if ($column_name == "lists") {
										$style = "width:20%;";
									} elseif ($column_name == "image") {
										$style = "width:4em;";
									}
								
									?>
									
									<th style="<?php echo $style; ?>" class="column-<?php echo $column_name; ?>"><?php echo $column_value; ?></th>
									
									<?php
									break;
								case 'mandatory'				:
								case 'gravatars'				:
									if (empty($screen_custom) || (!empty($screen_custom) && !in_array($column_name, $screen_custom))) {
										break;
									}
								default							:
									?>
									
									<th class="column-<?php echo $column_name; ?> <?php echo ($orderby == $column_name) ? 'sorted ' . $order : 'sortable desc'; ?>">
										<a href="<?php echo $Html -> retainquery('orderby=' . $column_name . '&order=' . (($orderby == $column_name) ? $otherorder : "asc")); ?>">
											<span><?php echo $column_value; ?></span>
											<span class="sorting-indicator"></span>
										</a>
									</th>
									
									<?php
									break;
							}
						}
					}	
					
					$cols_output = ob_get_clean();
					echo $cols_output;
						
					?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<?php echo $cols_output; ?>
				</tr>
			</tfoot>
			<tbody>
				<?php if (!empty($subscribers)) : ?>
					<?php $class = ''; ?>
					<?php foreach ($subscribers as $subscriber) : ?>
						<?php $updatediv = (empty($update)) ? 'subscribers' : $update; ?>
						<tr id="subscriberrow<?php echo $subscriber -> id; ?>" class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" id="checklist<?php echo $subscriber -> id; ?>" name="subscriberslist[]" value="<?php echo $subscriber -> id; ?>" /></th>
							<?php
								
							foreach ($cols as $column_name => $column_value) {
								switch ($column_name) {
									case 'id'								:
										?><td><label for="checklist<?php echo $subscriber -> id; ?>"><?php echo $subscriber -> id; ?></label></td><?php
										break;
									case 'image'							:
										?>
										<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
											<td>
												<label for="checklist<?php echo $subscriber -> id; ?>"><?php echo $Html -> get_gravatar($subscriber -> email); ?></label>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'email'							:
										?>
										<td>
											<strong><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $subscriber -> id; ?>" title="<?php _e('View the details of this subscriber', $this -> plugin_name); ?>" class="row-title"><?php echo $subscriber -> email; ?></a></strong>
											<div class="row-actions">
												<span class="edit"><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save&amp;id=<?php echo $subscriber -> id; ?>"><?php _e('Edit', $this -> plugin_name); ?></a> |</span>
												<span class="delete"><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=delete&amp;id=<?php echo $subscriber -> id; ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this subscriber?', $this -> plugin_name); ?>')) { return false; }" class="submitdelete"><?php _e('Delete', $this -> plugin_name); ?></a> |</span>
												<span class="view"><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $subscriber -> id; ?>"><?php _e('View', $this -> plugin_name); ?></a></span>
											</div>
										</td>
										<?php
										break;
									case 'registered'						:
										?>
										<?php if (apply_filters($this -> pre . '_admin_subscribers_registeredcolumn', true)) : ?>
											<td><label for="checklist<?php echo $subscriber -> id; ?>"><?php echo (empty($subscriber -> registered) || $subscriber -> registered == "N") ? '<span class="newsletters_error">' . __('No', $this -> plugin_name) : '<span class="newsletters_success">' . __('Yes', $this -> plugin_name); ?></span></label></td>
										<?php endif; ?>
										<?php
										break;
									case 'mandatory'						:
										?>
										<?php if (!empty($screen_custom) && in_array('mandatory', $screen_custom)) : ?>
											<td>
												<label for="checklist<?php echo $subscriber -> id; ?>">
													<?php if (!empty($subscriber -> mandatory) && $subscriber -> mandatory == "Y") : ?>
														<span class="newsletters_error"><?php _e('Yes', $this -> plugin_name); ?></span>
													<?php else : ?>
														<span class="newsletters_success"><?php _e('No', $this -> plugin_name); ?></span>
													<?php endif; ?>
												</label>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'lists'							:
										?>
										<td>
											<?php if (!empty($subscriber -> Mailinglist)) : ?>
												<?php $m = 1; ?>
												<?php foreach ($subscriber -> Mailinglist as $list) : ?>
													<?php echo $Html -> link(__($list -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list -> id); ?> (<?php echo ($SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $list -> id)) == "Y") ? '<span class="newsletters_success">' . __('active', $this -> plugin_name) : '<span class="newsletters_error">' . __('inactive', $this -> plugin_name); ?></span>)
													<?php if ($m < count($subscriber -> Mailinglist)) : ?>
														<?php echo ', '; ?>
													<?php endif; ?>
													<?php $m++; ?>
												<?php endforeach; ?>
											<?php else : ?>
												<?php _e('none', $this -> plugin_name); ?>
											<?php endif; ?>
										</td>
										<?php
										break;
									case 'bouncecount'						:
										?>
										<td><?php echo $subscriber -> bouncecount; ?></td>
										<?php
										break;
									case 'created'							:
										?>
										<td><label for="checklist<?php echo $subscriber -> id; ?>"><abbr title="<?php echo $subscriber -> modified; ?>"><?php echo $Html -> gen_date(false, strtotime($subscriber -> modified)); ?></abbr></label></td>
										<?php
										break;
									default									:
										?>
										<td>
			                        	<?php if (!empty($subscriber -> {$column_name}) && !empty($columns[$column_name])) : ?>
			                        		<?php $column = $columns[$column_name]; ?>
			                        		<?php $newfieldoptions = $column -> newfieldoptions; ?>
											<?php if ($column -> type == "radio" || $column -> type == "select") : ?>
			                                    <?php echo __($newfieldoptions[$subscriber -> {$column -> slug}]); ?>
			                                <?php elseif ($column -> type == "checkbox") : ?>
			                                    <?php $supoptions = maybe_unserialize($subscriber -> {$column -> slug}); ?>
			                                    <?php if (!empty($supoptions) && is_array($supoptions)) : ?>
			                                        <?php foreach ($supoptions as $supopt) : ?>
			                                            &raquo;&nbsp;<?php echo __($newfieldoptions[$supopt]); ?><br/>
			                                        <?php endforeach; ?>
			                                    <?php else : ?>
			                                        <?php _e('none', $this -> plugin_name); ?>
			                                    <?php endif; ?>
			                                <?php elseif ($column -> type == "file") : ?>
			                                	<?php echo $Html -> file_custom_field($subscriber -> {$column -> slug}); ?>
			                                <?php elseif ($column -> type == "pre_country") : ?>
			                                    <?php $Db -> model = $wpmlCountry -> model; ?>
			                                    <?php echo $Db -> field('value', array('id' => $subscriber -> {$column -> slug})); ?>
			                                <?php elseif ($column -> type == "pre_date") : ?>
			                                	<?php if (is_serialized($subscriber -> {$column -> slug})) : ?>
				                                    <?php $date = maybe_unserialize($subscriber -> {$column -> slug}); ?>
				                                    <?php if (!empty($date) && is_array($date)) : ?>
				                                        <?php echo $date['y']; ?>-<?php echo $date['m']; ?>-<?php echo $date['d']; ?>
				                                    <?php endif; ?>
				                                <?php else : ?>
				                                	<?php echo $Html -> gen_date(false, strtotime($subscriber -> {$column -> slug})); ?>
				                                <?php endif; ?>
			                                <?php elseif ($column -> type == "pre_gender") : ?>
			                                	<?php echo (!empty($subscriber -> {$column -> slug}) && $subscriber -> {$column -> slug} == "male") ? __('Male', $this -> plugin_name) : __('Female', $this -> plugin_name); ?>
			                                <?php else : ?>
			                                    <?php echo $subscriber -> {$column -> slug}; ?>
			                                <?php endif; ?>
			                            <?php else : ?>
			                            	<?php do_action('newsletters_admin_subscribers_table_column_output', $column_name, $subscriber); ?>
			                            <?php endif; ?>
										</td>
										<?php
										break;
								}
							}	
								
							?>										
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No subscribers were found', $this -> plugin_name); ?></td>
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
							<option <?php echo (isset($_COOKIE[$this -> pre . 'subscribersperpage']) && $_COOKIE[$this -> pre . 'subscribersperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('subscribers', $this -> plugin_name); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'subscribersperpage'])) : ?>
							<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'subscribersperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'subscribersperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo $this -> pre; ?>subscribersperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo $this -> pre; ?>subscriberssorting=" + field + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			document.cookie = "<?php echo $this -> pre; ?>subscribers" + field + "dir=" + dir + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
		}
		
		function action_change(action) {
			jQuery('#listsdiv').hide();
		
			if (action != "") {
				if (action == "assignlists" || action == "setlists") {
					jQuery('#listsdiv').show();
				}
			}
		}
		</script>
<?php /*<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No subscribers were found', $this -> plugin_name); ?></p>
<?php endif; ?>*/ ?>