<div class="wrap <?php echo $this -> pre; ?> <?php echo $this -> sections -> queue; ?>">
	<h2><?php _e('Email Queue', $this -> plugin_name); ?> <a href="?page=<?php echo $this -> sections -> queue; ?>" class="add-new-h2"><?php _e('Refresh', $this -> plugin_name); ?></a></h2>
	<h3><?php _e('Schedule Details', $this -> plugin_name); ?></h3>
	
	<div class="tablenav">
		<div class="alignleft action">
			<a class="button button-secondary" href="?page=<?php echo $this -> sections -> settings; ?>#schedulingdiv"><?php _e('Configure Email Scheduling', $this -> plugin_name); ?></a>
		</div>
	</div>
	
	<div class="postbox" style="padding:10px;">
		<table class="widefat queuetable">
			<tbody>
				<tr class="alternate">
					<th><?php _e('Total emails in queue', $this -> plugin_name); ?></th>
					<td><?php echo $Queue -> count(); ?> <?php _e('emails', $this -> plugin_name); ?></td>
				</tr>
				<tr>
					<th><?php _e('Current Date &amp; Time', $this -> plugin_name); ?></th>
					<td><?php echo $this -> gen_date(); ?></td>
				</tr>
				<tr class="alternate">
					<th>
						<?php _e('Next schedule event', $this -> plugin_name); ?>
						<?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&method=runschedule&hook=cronhook', array('class' => "button button-secondary button-small", 'onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now?', $this -> plugin_name) . "')) { return false; }")); ?>
					</th>
					<td>
	                	<?php if ($this -> get_option('scheduling') == "Y") : ?>
	                    	<?php if ($this -> get_option('schedulecrontype') == "wp") : ?>
								<?php $nextschedule = wp_next_scheduled('wpml_cronhook'); ?>
	                            <?php if (!empty($nextschedule)) : ?>	                                
	                                <div id="countdown"></div>
	                            
		                            <script type="text/javascript">
									jQuery(document).ready(function() {
										jQuery('#countdown').countdown({
											format:'HMS',
											until:'+<?php echo (wp_next_scheduled('wpml_cronhook') - time()); ?>',
											onExpiry:function() { window.location = "<?php echo '?page=' . $this -> sections -> settings . '&method=runschedule&hook=cronhook'; ?>"; },
										});
									});
									</script>								
	                            <?php else : ?>
	                                <?php echo __('No interval has been set yet. Please do that in the configuration.', $this -> plugin_name); ?>
	                            <?php endif; ?>
	                        <?php else : ?>
	                        	<?php _e('Server cron is being used.', $this -> plugin_name); ?>
	                        <?php endif; ?>
	                    <?php else : ?>
	                    	<?php _e('N/A', $this -> plugin_name); ?>
	                    <?php endif; ?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Schedule Interval', $this -> plugin_name); ?></th>
					<td>
	                	<?php if ($this -> get_option('schedulecrontype') == "wp") : ?>
							<?php $intervals = wp_get_schedules(); ?>
	                        <?php echo $intervals[$this -> get_option('scheduleinterval')]['display']; ?>
	                    <?php else : ?>
	                    	<?php _e('Server cron is being used.', $this -> plugin_name); ?>
	                    <?php endif; ?>
	                </td>
				</tr>
				<tr class="alternate">
					<th><?php _e('Emails per schedule interval', $this -> plugin_name); ?></th>
					<td><?php echo $this -> get_option('emailsperinterval'); ?> <?php _e('emails', $this -> plugin_name); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<h3 id="current-queue-emails"><?php _e('Current Queue Emails', $this -> plugin_name); ?></h3>
	<?php if (!empty($queues)) : ?>
		<form id="posts-filter" method="post" action="?page=<?php echo $this -> sections -> queue; ?>">
			<?php if (!empty($queues)) : ?>
				<ul class="subsubsub">
					<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($queues); ?> <?php _e('queued emails', $this -> plugin_name); ?> |</li>
					<?php if (empty($_GET['showall'])) : ?>
						<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?> |</li>
					<?php else : ?>
						<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> queue); ?> |</li>
					<?php endif; ?>
					<?php if ((isset($_COOKIE[$this -> pre . 'queuessorting']) && $_COOKIE[$this -> pre . 'queuessorting'] == "modified") || (!isset($_COOKIE[$this -> pre . 'queuessubjectdir']) || $_COOKIE[$this -> pre . 'queuessubjectdir'] == "DESC")) : ?>
						<li><?php echo $Html -> link(__('A to Z', $this -> plugin_name), '#void', array('onclick' => "change_sorting('subject', 'ASC');")); ?> |</li>
					<?php else : ?>
						<li><?php echo $Html -> link(__('Z to A', $this -> plugin_name), '#void', array('onclick' => "change_sorting('subject', 'DESC');")); ?> |</li>
					<?php endif; ?>
					<?php if ((isset($_COOKIE[$this -> pre . 'queuessorting']) && $_COOKIE[$this -> pre . 'queuessorting'] == "subject") || (!isset($_COOKIE[$this -> pre . 'queuesmodifieddir']) || $_COOKIE[$this -> pre . 'queuesmodifieddir'] == "ASC")) : ?>
						<li><?php echo $Html -> link(__('New to Old', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'DESC');")); ?></li>
					<?php else : ?>
						<li><?php echo $Html -> link(__('Old to New', $this -> plugin_name), '#void', array('onclick' => "change_sorting('modified', 'ASC');")); ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</form>
	<?php endif; ?>
	<?php $this -> render('queues' . DS . 'loop', array('queues' => $queues, 'paginate' => $paginate), true, 'admin'); ?>
</div>