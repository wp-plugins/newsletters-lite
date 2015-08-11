<div class="newsletters <?php echo $this -> pre; ?>unsubscribe <?php echo $this -> pre; ?>">
	<?php global $wpdb, $Mailinglist; ?>	
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<?php if (!empty($success) && $success == true) : ?>
		<h2><?php _e('Unsubscribe Successful', $this -> plugin_name); ?></h2>
		<p>
			<?php _e('You have successfully unsubscribed.', $this -> plugin_name); ?><br/>
			<?php _e('You will no longer receive correspondence.', $this -> plugin_name); ?>
		</p>
		
		<?php if (empty($deleted) && $deleted == false) : ?>
			<ul>
				<li><?php _e('Go back to', $this -> plugin_name); ?> <a href="<?php echo home_url(); ?>" title="<?php echo esc_attr(stripslashes(get_bloginfo('name'))); ?>"><?php echo get_bloginfo('name'); ?></a></li>
			</ul>
		<?php endif; ?>
	<?php elseif (!empty($data)) : ?>
		<h2><?php _e('Unsubscribe Confirmation', $this -> plugin_name); ?></h2>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<?php foreach ($data as $gkey => $gval) : ?>
				<input type="hidden" name="<?php echo $gkey; ?>" value="<?php echo $gval; ?>" />
			<?php endforeach; ?>
			
			<p><?php _e('Please confirm that you want to unsubscribe.', $this -> plugin_name); ?></p>
			
			<table>
				<tbody>
					<tr>
						<td><strong><?php _e('Email Address:', $this -> plugin_name); ?></strong></td>
						<td><?php echo $user -> user_email; ?></td>
					</tr>
				</tbody>
			</table>
			
			<?php if ($this -> get_option('unsubscribecomments') == "Y") : ?>
				<h3><?php _e('Comments', $this -> plugin_name); ?> <?php _e('(optional)', $this -> plugin_name); ?></h3>
				<p>
					<textarea name="<?php echo $this -> pre; ?>comments" style="width:97%;" rows="5" class="widefat"><?php echo stripslashes(htmlentities(strip_tags($data[$this -> pre . 'comments']), false, get_bloginfo('charset'))); ?></textarea>
				</p>
			<?php endif; ?>
			
			<p class="submit">
				<input type="submit" name="confirm" value="<?php _e('Confirm Unsubscribe', $this -> plugin_name); ?>" class="<?php echo $this -> pre; ?>button" />
			</p>
		</form>
	<?php else : ?>
		<?php foreach ($errors as $err) : ?>
			&raquo; <?php echo $err; ?><br/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>