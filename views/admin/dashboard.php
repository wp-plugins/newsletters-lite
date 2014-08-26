<div>
	<?php
	
	include_once $this -> plugin_base() . DS . 'vendors' . DS . 'ofc' . DS . 'open_flash_chart_object.php';
	open_flash_chart_object("100%", "180", admin_url('admin-ajax.php') . '?action=wpmlwelcomestats', false, $this -> url());
	
	?>
</div>
<br class="clear" />

<?php

$Db -> model = $History -> model;
$histories = $Db -> find_all(false, false, array('modified', "DESC"), 4);

?>

<div class="table table_content">
	<p class="sub"><?php _e('Latest', $this -> plugin_name); ?></p>
	<?php if (!empty($histories)) : ?>
		<table>
			<tbody>
				<?php foreach ($histories as $history) : ?>
					<tr>
						<td class="first b b-ad">
							<?php echo __($history -> subject); ?>
						</td>
						<td class="t ad">
							<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>"><?php _e('View', $this -> plugin_name); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p><?php echo sprintf(__('No emails are available yet, please %s.', $this -> plugin_name), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> send) . '">' . __('create one', $this -> plugin_name) . '</a>'); ?></p>
	<?php endif; ?>
</div>

<?php

global $wpdb;
$Db -> model = $Subscriber -> model;
$total = $Db -> count();
$Db -> model = $SubscribersList -> model;
$active = $Db -> count(array('active' => "Y"));
$Db -> model = $Unsubscribe -> model;
$unsubscribes = $Db -> count();
$bounces = $wpdb -> get_var("SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "`");

?>

<div class="table table_discussion">
	<p class="sub"><?php _e('Overview', $this -> plugin_name); ?></p> 
	<table> 
		<tbody>
			<tr class="first">
				<td class="b b-comments">
					<a href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><span class="total-count"><?php echo $total; ?></span></a>
				</td>
				<td class="last t comments">
					<a href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><?php _e('Subscribers', $this -> plugin_name); ?></a>
				</td>
			</tr>
			<tr>
				<td class="b b_approved">
					<a href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><span class="approved-count"><?php echo $active; ?></span></a>
				</td>
				<td class="last t">
					<a class="approved" href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><?php _e('Subscriptions', $this -> plugin_name); ?></a>
				</td>
			</tr>
			<tr>
				<td class="b b-waiting">
					<a href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><span class="pending-count"><?php echo $unsubscribes; ?></span></a>
				</td>
				<td class="last t">
					<a class="waiting" href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><?php _e('Unsubscribes', $this -> plugin_name); ?></a>
				</td>
			</tr>
			<tr>
				<td class="b b-spam">
					<a href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><span class="spam-count"><?php echo $bounces; ?></span></a>
				</td>
				<td class="last t">
					<a class="spam" href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>"><?php _e('Bounces', $this -> plugin_name); ?></a>
				</td>
			</tr> 
		</tbody>
	</table>
</div>

<br class="clear" />