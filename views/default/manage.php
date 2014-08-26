<div align="center">
	<h1 id="logo"><a href="<?php echo home_url(); ?>" title="<?php echo get_bloginfo('name'); ?>"><img style="border:none;" alt="newsletter" src="<?php echo $this -> url(); ?>/images/wp-mailinglist.jpg" /></a></h1>
	
	<h3><?php _e('Your Subscriptions', $this -> plugin_name); ?></h3>
	<?php if (!empty($subscriptions)) : ?>
		<form onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action?', $this -> plugin_name); ?>')) { return false; }" action="<?php echo $this -> url; ?>&amp;email=<?php echo $subscriber -> email; ?>" method="post">
			<table class="<?php echo $this -> pre; ?>">
				<thead>
					<th>&nbsp;</th>
					<th><?php _e('Mailing List', $this -> plugin_name); ?></th>
					<th><?php _e('Active', $this -> plugin_name); ?></th>
					<th><?php _e('Date', $this -> plugin_name); ?></th>
				</thead>
				<tbody>
					<?php $class = ''; ?>
					<?php foreach ($subscriptions as $subscription) : ?>
						<?php if ($subscription -> mailinglist -> privatelist != "Y") : ?>
							<tr class="<?php echo $class = (empty($class) || $class == "erow") ? 'arow' : 'erow'; ?>">
								<th><input type="checkbox" name="Mailinglist[checklist][]" value="<?php echo $subscription -> list_id; ?>" id="checklist<?php echo $subscription -> list_id; ?>" /></th>
								<td><label for="checklist<?php echo $subscription -> list_id; ?>"><?php echo $subscription -> mailinglist -> title; ?></label></td>
								<td><label for="checklist<?php echo $subscription -> list_id; ?>"><?php echo ($subscription -> active == "Y") ? __('Yes', $this -> plugin_name) : __('No', $this -> plugin_name); ?></label></td>
								<td><label for="checklist<?php echo $subscription -> list_id; ?>"><abbr title="<?php echo $subscription -> created; ?>"><?php echo date_i18n("Y-m-d", strtotime($subscription -> created)); ?></abbr></label></td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<p>
				<strong><?php _e('With Selected', $this -> plugin_name); ?> :</strong>
				<select name="action">
					<option value="">- <?php _e('Select', $this -> plugin_name); ?> -</option>
					<option value="unsubscribe"><?php _e('Unsubscribe', $this -> plugin_name); ?></option>
					<option value="activate"><?php _e('Activate', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="execute" value="<?php _e('Execute', $this -> plugin_name); ?>" class="button" />
			</p>
		</form>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No subscriptions are available', $this -> plugin_name); ?></p>
	<?php endif; ?>
	
	<p><?php echo $Html -> link(__('&larr; ', $this -> plugin_name) . get_option('blogname'), get_option('home'), array('title' => get_option('blogname'))); ?></p>
</div>