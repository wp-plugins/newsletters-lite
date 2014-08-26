<div class="wrap newsletters">
	<h2><?php _e('Save Subscription Order', $this -> plugin_name); ?></h2>

	<form action="?page=<?php echo $this -> sections -> orders; ?>&amp;method=save&amp;id=<?php echo $order -> id; ?>" method="post">
		<input type="hidden" name="id" value="<?php echo $order -> id; ?>" />
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmlOrder.subscriber_id"><?php _e('Subscriber ID', $this -> plugin_name); ?></label></th>
					<td>
						<input type="text" name="subscriber_id" value="<?php echo $order -> subscriber_id; ?>" id="subscriber_id" class="widefat" style="width:65px;" />
					</td>
				</tr>
				<tr>
					<th><label for="list_id"><?php _e('Mailing List', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
							<select name="list_id" id="list_id">
								<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
								<?php foreach ($mailinglists as $list_id => $list_title) : ?>
									<option <?php echo (!empty($order -> list_id) && $order -> list_id == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo $list_id; ?>"><?php echo __($list_title); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<span class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available.', $this -> plugin_name); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Amount', $this -> plugin_name); ?></th>
					<td><?php echo $Html -> currency(); ?><input size="5" type="text" name="amount" value="<?php echo $order -> amount; ?>" /></td>
				</tr>
				<tr>
					<th><?php _e('Order Number', $this -> plugin_name); ?></th>
					<td><input type="text" size="15" name="order_number" value="<?php echo $order -> order_number; ?>" /></td>
				</tr>
				<tr>
					<th><?php _e('Product ID', $this -> plugin_name); ?></th>
					<td><input type="text" size="10" name="product_id" value="<?php echo $order -> product_id; ?>" /></td>
				</tr>
				<tr>
					<th><label for="pmethod"><?php _e('Payment Method', $this -> plugin_name); ?></label></th>
					<td>
						<?php $pmethods = array('pp' => __('PayPal', $this -> plugin_name), '2co' => __('2CheckOut', $this -> plugin_name)); ?>
						<select name="pmethod" id="pmethod">
							<option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
							<?php foreach ($pmethods as $pkey => $pval) : ?>
								<option <?php echo (!empty($order -> pmethod) && $order -> pmethod == $pkey) ? 'selected="selected"' : ''; ?> value="<?php echo $pkey; ?>"><?php echo $pval; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input type="submit" name="save_order" value="<?php _e('Save Order', $this -> plugin_name); ?>" class="button-primary" />
		</p>
	</form>
</div>