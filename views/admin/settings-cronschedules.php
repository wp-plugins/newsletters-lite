<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Scheduled Tasks', $this -> plugin_name); ?> <?php echo $Html -> link(__('Refresh', $this -> plugin_name), '?page=' . $this -> sections -> settings_tasks, array('class' => "add-new-h2")); ?></h2>   
	
	<?php $this -> render('settings-navigation', false, true, 'admin'); ?>
    
    <p>
		<?php _e('These are scheduled tasks which are automatically run using the WordPress cron.', $this -> plugin_name); ?><br/>
        <?php _e('The current server time is:', $this -> plugin_name); ?> <strong><?php echo date_i18n("Y-m-d H:i:s", time()); ?></strong>
    </p>
    
    <table class="widefat">
    	<thead>
        	<tr>
            	<th><?php _e('Schedule Task', $this -> plugin_name); ?></th>
                <th><?php _e('Next Scheduled Run', $this -> plugin_name); ?></th>
            </tr>
        </thead>
        <tfoot>
        	<tr>
            	<th><?php _e('Schedule Task', $this -> plugin_name); ?></th>
                <th><?php _e('Next Scheduled Run', $this -> plugin_name); ?></th>
            </tr>
        </tfoot>
    	<tbody>
        	<!-- Email Scheduling = "wpml_cronhook" -->
        	<tr>
            	<th>
                	<?php $queuecount = $Queue -> count(); ?>
					<a class="row-title" href="?page=<?php echo $this -> sections -> queue; ?>"><?php _e('Email Queue', $this -> plugin_name); ?></a> <?php if (!empty($queuecount)) : ?><small>(<?php echo $queuecount; ?> <?php _e('emails in the queue', $this -> plugin_name); ?>)</small><?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>                
                	<?php if ($this -> get_option('schedulecrontype') == "wp") : ?>
						<?php echo $Html -> next_scheduled('cronhook'); ?>
					<?php else : ?>
						<?php _e('Server cron is currently being used so it is dependent on your server.', $this -> plugin_name); ?>
					<?php endif; ?>
                </td>
            </tr>
            <!-- POP3 Scheduling = "wpml_pophook" -->
            <tr class="alternate">
            	<th>
					<a class="row-title" href="?page=<?php echo $this -> sections -> settings; ?>#bouncediv"><?php _e('POP/IMAP Bounce Check', $this -> plugin_name); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>
                	<?php if ($this -> get_option('bouncemethod') == "pop") : ?>
                		<?php echo $Html -> next_scheduled('pophook'); ?>
                    <?php else : ?>
                    	<?php _e('POP/IMAP bounce handling is turned OFF.', $this -> plugin_name); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <!-- Latest Posts = "wpml_latestposts" -->
            <tr>
            	<th>
					<a class="row-title" href="?page=<?php echo $this -> sections -> settings; ?>#latestposts"><?php _e('Latest Posts Subscription', $this -> plugin_name); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=latestposts', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=latestposts', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=latestposts', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>
                	<?php if ($this -> get_option('latestposts') == "Y") : ?>
                		<?php echo $Html -> next_scheduled('latestposts'); ?>
                    <?php else : ?>
                    	<?php _e('Latest posts subscription is turned OFF.', $this -> plugin_name); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <!-- Autoresponder emails = "wpml_autoresponders" -->
            <tr class="alternate">
            	<th>
                	<?php
					
					$Db -> model = $Autoresponderemail -> model;
					$autoresponderemailcount = $Db -> count(array('status' => "unsent"));
					
					?>
                
					<a class="row-title" href="?page=<?php echo $this -> sections -> autoresponderemails; ?>"><?php _e('Autoresponder Emails', $this -> plugin_name); ?></a>
                    <?php if (!empty($autoresponderemailcount)) : ?><small>(<?php echo $autoresponderemailcount; ?> <?php _e('future autoresponder emails waiting', $this -> plugin_name); ?>)</small><?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo $Html -> next_scheduled('autoresponders'); ?>
                </td>
            </tr>
            <!-- Import WordPress Users -->
            <tr>
            	<th>
                	<a class="row-title" href="?page=<?php echo $this -> sections -> settings; ?>#wprelateddiv"><?php _e('Auto Import WordPress Users', $this -> plugin_name); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo $Html -> next_scheduled('importusers'); ?>
                </td>
            </tr>
            <?php $activateaction = $this -> get_option('activateaction'); ?>
            <?php if (!empty($activateaction) && $activateaction != "none") : ?>
	            <!-- Confirmation/Activation Reminders/Deletion -->
	            <tr>
	            	<th>
	                	<a class="row-title" href="?page=<?php echo $this -> sections -> settings_subscribers; ?>#subscribersdiv"><?php _e('Inactive Subscriptions Action', $this -> plugin_name); ?></a>
	                    <div class="row-actions">
	                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
	                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
	                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
	                    </div>
	                </th>
	                <td>
	                	<?php echo $Html -> next_scheduled('activateaction'); ?>
	                </td>
	            </tr>
	        <?php endif; ?>
        </tbody>
        <?php if ($this -> is_plugin_active('captcha')) : ?>
        	<tr>
        		<th>                
					<?php _e('Really Simple Captcha cleanup', $this -> plugin_name); ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo $Html -> link(__('Run Now', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=runschedule&amp;hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="edit"><?php echo $Html -> link(__('Reschedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=reschedule&amp;hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', $this -> plugin_name) . "')) { return false; }")); ?> |</span>
                        <span class="delete"><?php echo $Html -> link(__('Stop Schedule', $this -> plugin_name), '?page=' . $this -> sections -> settings . '&amp;method=clearschedule&amp;hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo $Html -> next_scheduled('captchacleanup'); ?>
                </td>
        	</tr>
        <?php endif; ?>
        <?php do_action('wpml_cronschedules'); ?>
    </table>
</div>