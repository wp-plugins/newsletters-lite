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
						$tracking = (!empty($etotal)) ? ($eread / $etotal) * 100 : 0;
						$ebounced = $wpdb -> get_var("SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
						$eunsubscribed = $wpdb -> get_var("SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
						$clicks = $this -> Click -> count(array('history_id' => $history -> id));
							
						$data = array(
							array(
								'value'		=>	 number_format($tracking, 0, '.', ''),
								'color'		=>	"#46BFBD",
								'highlight'	=>	"#5AD3D1",
								'label'		=>	"Read",
							),
							array(
								'value'		=>	number_format((100 - $tracking), 0, '.', ''),
								'color'		=>	"#949FB1",
								'highlight'	=>	"#A8B3C5",
								'label'		=>	"Unread",
							),
							array(
								'value'		=>	number_format($ebouncedperc, 0, '.', ''),
								'color'		=>	"#F7464A",
								'highlight'	=>	"#FF5A5E",
								'label'		=>	"Bounced",
							),
							array(
								'value'		=>	number_format($eunsubscribeperc, 0, '.', ''),
								'color'		=>	"#FDB45C",
								'highlight'	=>	"#FFC870",
								'label'		=>	"Unsubscribed",
							)
						);
							
						$Html -> pie_chart('email-chart-' . $history -> id, array('width' => 150, 'height' => 150), $data, $options); 
						
						?>
						<h2>
							<p class="submit">
								<a class="button button-secondary" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>"><i class="fa fa-eye"></i> <?php _e('View', $this -> plugin_name); ?></a>
								<a class="button button-primary" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> send . '&amp;method=history&amp;id=' . $history -> id); ?>"><i class="fa fa-paper-plane"></i> <?php _e('Send/Edit', $this -> plugin_name); ?></a>
							</p>
							<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>"><?php _e($history -> subject); ?></a>
							<p class="howto">
								<?php echo sprintf(__('Created on %s by %s', $this -> plugin_name), $Html -> gen_date("M j, Y", strtotime($history -> created)), ((!empty($history -> user_id)) ? get_the_author_meta('display_name', $history -> user_id) : __('unknown', $this -> plugin_name))); ?>
							</p>
						</h2>
						<p><?php echo $Html -> truncate(strip_tags(do_shortcode($this -> strip_set_variables(__($history -> message)))), 500); ?></p>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<p class="textright">
		<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history); ?>"><?php _e('View All Newsletters', $this -> plugin_name); ?></a>
		<?php _e('or', $this -> plugin_name); ?> <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> send); ?>"><?php _e('create a new one', $this -> plugin_name); ?></a>
	</p>
<?php else : ?>
	<p>
		<?php _e('Sent emails and saved drafts will be displayed here as soon as you create them.', $this -> plugin_name); ?>
	</p>
<?php endif; ?>