<div class="wrap newsletters">
	<h2><?php _e('View Order: ' . $order -> order_number, $this -> name); ?></h2>
	
	<div class="subsubsub" style="float:none;">
		<a href="<?php echo $this -> url; ?>"><?php _e('&larr; All Orders', $this -> plugin_name); ?></a>
	</div>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<a class="button" href="?page=<?php echo $this -> sections -> orders; ?>&amp;method=save&amp;id=<?php echo $order -> id; ?>"><?php _e('Change', $this -> plugin_name); ?></a>
			<a class="button button-highlighted" onclick="if (!confirm('<?php _e('Are you sure you want to delete this order?', $this -> plugin_name); ?>')) { return false; }" href="?page=<?php echo $this -> sections -> orders; ?>&amp;method=delete&amp;id=<?php echo $order -> id; ?>"><?php _e('Delete', $this -> plugin_name); ?></a>
		</div>
	</div>
	
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Field', $this -> plugin_name); ?></th>
				<th><?php _e('Value', $this -> plugin_name); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $class = ''; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Subscriber', $this -> name); ?></th>
				<td><a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=view&amp;id=<?php echo $subscriber -> id; ?>"><?php echo $subscriber -> email; ?></a></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Mailing List', $this -> name); ?></th>
				<td><a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=view&amp;id=<?php echo $mailinglist -> id; ?>"><?php echo __($mailinglist -> title); ?></a></td>
			</tr>
			<tr class="alternate">
				<th><?php _e('Amount', $this -> name); ?></th>
				<td><?php echo $Html -> currency(); ?><?php echo number_format($order -> amount, 2, '.', ''); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Payment Method', $this -> plugin_name); ?></th>
				<td><?php echo (!empty($order -> pmethod) && $order -> pmethod == "2co") ? __('2CheckOut', $this -> plugin_name) : __('PayPal', $this -> plugin_name); ?></td>
			</tr>
			<?php if (!empty($order -> pmethod) && $order -> pmethod == "2co") : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('2CO Order Number', $this -> name); ?></th>
					<td><?php echo $order -> order_number; ?></td>
				</tr>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Created', $this -> name); ?></th>
				<td><?php echo $order -> created; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Modified', $this -> name); ?></th>
				<td><?php echo $order -> modified; ?></td>
			</tr>
		</tbody>
	</table>
</div>