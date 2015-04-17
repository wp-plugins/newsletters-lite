<div class="newsletters <?php echo $this -> pre; ?>unsubscribe <?php echo $this -> pre; ?>">
	<?php global $wpdb, $Mailinglist; ?>	
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<?php if ($dorender) : ?>
		<?php if (!empty($success) && $success == true) : ?>
			<h2><?php _e('Unsubscribe Successful', $this -> plugin_name); ?></h2>
			<p>
				<?php _e('You have successfully unsubscribed from the selected list(s).', $this -> plugin_name); ?><br/>
				<?php _e('You will no longer receive correspondence on the list(s) unsubscribed from.', $this -> plugin_name); ?>
			</p>
			
			<p>
				<?php echo sprintf(__('Was this a mistake? If it was, you can %s', $this -> plugin_name), $this -> gen_resubscribe_link($subscriber)); ?>
			</p>
			
			<?php if (empty($deleted) && $deleted == false) : ?>
				<ul>
					<li><?php _e('Go back to', $this -> plugin_name); ?> <a href="<?php echo home_url(); ?>" title="<?php echo esc_attr(stripslashes(get_bloginfo('name'))); ?>"><?php echo get_bloginfo('name'); ?></a></li>
					<li><?php echo $Html -> link(__('Manage Subscriptions', $this -> plugin_name), $Html -> retainquery('email=' . $subscriber -> email, $this -> get_managementpost(true))); ?></li>
				</ul>
			<?php endif; ?>
		<?php elseif (!empty($data)) : ?>
			<h2><?php _e('Unsubscribe Confirmation', $this -> plugin_name); ?></h2>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
				<?php foreach ($data as $gkey => $gval) : ?>
					<input type="hidden" name="<?php echo $gkey; ?>" value="<?php echo $gval; ?>" />
				<?php endforeach; ?>
				
				<p><?php _e('Tick/check the list(s) below from which you want to unsubscribe.', $this -> plugin_name); ?></p>
				
				<table>
					<tbody>
						<tr>
							<td><strong><?php _e('Email Address:', $this -> plugin_name); ?></strong></td>
							<td><?php echo $subscriber -> email; ?></td>
						</tr>
						<tr>
							<td><strong><?php _e('You are unsubscribing from:', $this -> plugin_name); ?></strong></td>
							<td>
								<?php if (!empty($subscriber -> mailinglists)) : ?>
									<?php if ($listsselect = $Mailinglist -> select(true, $subscriber -> mailinglists)) : ?>
										<?php foreach ($listsselect as $list_id => $list_title) : ?>
											<label class="<?php echo $this -> pre; ?>checkboxlabel <?php echo $this -> pre; ?>">
												<input checked="checked" class="<?php echo $this -> pre; ?>checkbox" <?php echo (!empty($data['unsubscribelists']) && in_array($list_id, $data['unsubscribelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="unsubscribelists[]" value="<?php echo esc_attr(stripslashes($list_id)); ?>" id="lists_<?php echo $list_id; ?>" /> <?php echo stripslashes($list_title); ?>
											</label>
										<?php endforeach; ?>
									<?php else : ?>
										<?php _e('No mailing lists', $this -> plugin_name); ?>
									<?php endif; ?>
								<?php else : ?>
									<?php _e('No mailing lists', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
						</tr>
						
						<?php 
								
						$otherlists_ids = $Subscriber -> mailinglists($subscriber -> id, false, $subscriber -> mailinglists); 
						$otherlists = (!empty($otherlists_ids)) ? $Mailinglist -> select(true, $otherlists_ids) : false;
						
						if (!empty($otherlists)) :
						
						?>
						
						<tr>
							<td><strong><?php _e('Other subscribed list(s):', $this -> plugin_name); ?></strong></td>
							<td>
								<?php if (!empty($otherlists)) : ?>
									<?php foreach ($otherlists as $otherlist_id => $otherlist_title) : ?>
										<label class="<?php echo $this -> pre; ?>checkboxlabel <?php echo $this -> pre; ?>">
											<input class="<?php echo $this -> pre; ?>checkbox" <?php echo (!empty($data['unsubscribelists']) && in_array($otherlist_id, $data['unsubscribelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="unsubscribelists[]" value="<?php echo esc_attr(stripslashes($otherlist_id)); ?>" id="lists_<?php echo $otherlist_id; ?>" /> <?php echo stripslashes($otherlist_title); ?>
										</label>
									<?php endforeach; ?>
								<?php else : ?>
									<?php _e('No other subscriptions.', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
						</tr>
						
						<?php endif; ?>
					</tbody>
				</table>
				
				<?php if ($this -> get_option('unsubscribecomments') == "Y") : ?>
					<h3><?php _e('Comments', $this -> plugin_name); ?> <?php _e('(optional)', $this -> plugin_name); ?></h3>
					<p>
						<textarea name="<?php echo $this -> pre; ?>comments" style="width:97%;" rows="5" class="widefat"><?php echo stripslashes(htmlentities(strip_tags($data[$this -> pre . 'comments']))); ?></textarea>
					</p>
				<?php endif; ?>
				
				<p class="submit">
					<input type="submit" name="confirm" value="<?php _e('Confirm Unsubscribe', $this -> plugin_name); ?>" class="<?php echo $this -> pre; ?>button" />
				</p>
			</form>
			
			<p><?php echo $Html -> link(__('&larr; Manage Subscriptions', $this -> plugin_name), $Html -> retainquery('email=' . $subscriber -> email, $this -> get_managementpost(true))); ?></p>
		<?php else : ?>
			<?php foreach ($errors as $err) : ?>
				&raquo; <?php echo $err; ?><br/>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>