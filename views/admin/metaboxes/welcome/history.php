<?php if (!empty($histories)) : ?>
	<table class="widefat">
		<tbody>
			<?php $class = false; ?>
			<?php foreach ($histories as $history) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<td>
						<?php
			
						global $wpdb;
						$Db -> model = $Email -> model;
						$etotal = $Db -> count(array('history_id' => $history -> id));
						$eread = $Db -> count(array('history_id' => $history -> id, 'read' => "Y"));
						$tracking = (!empty($etotal)) ? ($eread/$etotal) * 100 : 0;
						$ebounced = $wpdb -> get_var("SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
						$eunsubscribed = $wpdb -> get_var("SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
						$clicks = $this -> Click -> count(array('history_id' => $history -> id));
						
						?>
						<h4>
							<a href="?page=<?php echo $this -> sections -> history; ?>&method=view&id=<?php echo $history -> id; ?>"><?php echo $history -> subject; ?></a>
							<?php if (empty($history -> sent)) : ?>
								<small><?php _e('(Draft)', $this -> plugin_name); ?></small>
							<?php endif; ?>
							<small class="alignright"><?php echo sprintf(__('%s&#37; <span class="wpmlsuccess">opened</span> / %s&#37; <span class="wpmlpending">unsub</span> / %s&#37; <span class="wpmlerror">bounced</span> / %s <span class="wpmlneutral">clicks</span>', $this -> plugin_name), number_format($tracking, 2, '.', ''), number_format($eunsubscribeperc, 2, '.', ''), number_format($ebouncedperc, 2, '.', ''), $clicks); ?></small>
						</h4>
						<em><abbr title="<?php echo $history -> created; ?>"><?php echo date_i18n("M j, Y", strtotime($history -> created)); ?></abbr></em>
						<p><?php echo $Html -> truncate(strip_tags(do_shortcode($this -> strip_set_variables($history -> message))), 300); ?></p>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="textright">
		<a href="?page=<?php echo $this -> sections -> history; ?>" class="button button-secondary button-large"><?php _e('View All Emails', $this -> plugin_name); ?></a>
	</p>
<?php else : ?>
	<p>
		<?php _e('Sent emails and saved drafts will be displayed here as soon as you create them.', $this -> plugin_name); ?>
	</p>
<?php endif; ?>