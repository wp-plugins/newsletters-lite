<!-- History View -->

<?php

$preview_src = admin_url('admin-ajax.php') . '?action=' . $this -> pre . 'history_iframe&id=' . $history -> id . '&rand=' . rand(1,999);

?>

<div class="wrap newsletters <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Sent/Draft:', $this -> plugin_name); ?> <?php echo $history -> subject; ?> <a href="?page=<?php echo $this -> sections -> history; ?>&method=view&id=<?php echo $history -> id; ?>" class="add-new-h2"><?php _e('Refresh', $this -> plugin_name); ?></a></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; All Sent &amp; Drafts', $this -> plugin_name), $this -> url); ?></div>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<a href="?page=<?php echo $this -> sections -> send; ?>&amp;method=history&amp;id=<?php echo $history -> id; ?>" title="<?php _e('Send this history email again or edit the draft', $this -> plugin_name); ?>" class="button button-primary"><?php _e('Send/Edit', $this -> plugin_name); ?></a>
			<a onclick="jQuery.colorbox({href:'<?php echo $preview_src; ?>'}); return false;" href="#" class="button"><?php _e('Preview', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=delete&amp;id=<?php echo $history -> id; ?>" title="<?php _e('Remove this history email permanently', $this -> plugin_name); ?>" class="button button-highlighted" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this history email?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete', $this -> plugin_name); ?></a>
		</div>
	</div>
	<?php $class = ''; ?>
	<div class="postbox" style="padding:10px;">
	<table class="widefat queuetable">
		<tbody>
			<?php if (!empty($history -> from) || !empty($history -> fromname)) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('From', $this -> plugin_name); ?></th>
					<td>
						<?php echo (empty($history -> fromname)) ? $this -> get_option('smtpfromname') : $history -> fromname; ?>; <?php echo (empty($history -> from)) ? $this -> get_option('smtpfrom') : $history -> from; ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Email Subject', $this -> plugin_name); ?></th>
				<td><?php echo __($history -> subject); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Mailing List(s)', $this -> plugin_name); ?></th>
				<td>
					<?php if (!empty($history -> mailinglists)) : ?>
						<?php $mailinglists = $history -> mailinglists; ?>
						<?php $m = 1; ?>
						<?php if (is_array($mailinglists) || is_object($mailinglists)) : ?>
							<?php foreach ($mailinglists as $mailinglist_id) : ?>
								<?php $mailinglist = $Mailinglist -> get($mailinglist_id, false); ?>
								<?php echo $Html -> link(__($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id); ?><?php echo ($m < count($mailinglists)) ? ', ' : ''; ?>
								<?php $m++; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php _e('Theme', $this -> plugin_name); ?></th>
                <td>
                	<?php $Db -> model = $Theme -> model; ?>
                    <?php if (!empty($history -> theme_id) && $theme = $Db -> find(array('id' => $history -> theme_id))) : ?>
                    	<a href="" onclick="jQuery.colorbox({href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" title="<?php _e('Theme Preview:', $this -> plugin_name); ?> <?php echo $theme -> title; ?>"><?php echo $theme -> title; ?></a>
                    	<a href="" onclick="jQuery.colorbox({title:'<?php echo __($theme -> title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_preview"></a>
                    	<a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Theme: %s', $this -> plugin_name), __($theme -> title)); ?>', href:wpmlajaxurl + '?action=newsletters_themeedit&amp;id=<?php echo $theme -> id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_edit"></a>
                    <?php else : ?>
                    	<?php _e('None', $this -> plugin_name); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php _e('Author', $this -> plugin_name); ?></th>
            	<td>
            		<?php if (!empty($history -> user_id)) : ?>
            			<?php $user = $this -> userdata($history -> user_id); ?>
            			<a href="<?php echo get_edit_user_link($user -> ID); ?>"><?php echo $user -> display_name; ?></a>
            		<?php else : ?>
            			<?php _e('None', $this -> plugin_name); ?></td>
            		<?php endif; ?>
            	</td>
            </tr>
            <?php $Db -> model = $Queue -> model; ?>
            <?php if ($queue_count = $Db -> count(array('history_id' => $history -> id))) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            		<th><?php _e('Queued', $this -> plugin_name); ?></th>
            		<td>
            			<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> queue . '&filter=1&history_id=' . $history -> id); ?>"><?php echo sprintf(__('%s emails in the queue', $this -> plugin_name), $queue_count); ?></a>
            		</td>
            	</tr>
            <?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Tracking', $this -> plugin_name); ?></th>
				<td>
					<?php global $wpdb; $Db -> model = $Email -> model; ?>
					<?php $etotal = $Db -> count(array('history_id' => $history -> id)); ?>
					<?php $eread = $Db -> count(array('read' => "Y", 'history_id' => $history -> id)); ?>
					<?php 
					
					$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'";
					
					$query_hash = md5($query);
					global ${'newsletters_query_' . $query_hash};
					if (!empty(${'newsletters_query_' . $query_hash})) {
						$ebounced = ${'newsletters_query_' . $query_hash};
					} else {
						$ebounced = $wpdb -> get_var($query);
						${'newsletters_query_' . $query_hash} = $ebounced;
					}
					
					?>
					<?php $ebouncedperc = (!empty($etotal)) ? number_format((($ebounced/$etotal) * 100), 2, '.', '') : 0; ?>
					<?php 
					
					$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'";
					
					$query_hash = md5($query);
					global ${'newsletters_query_' . $query_hash};
					if (!empty(${'newsletters_query_' . $query_hash})) {
						$eunsubscribed = ${'newsletters_query_' . $query_hash};
					} else {
						$eunsubscribed = $wpdb -> get_var($query);
						${'newsletters_query_' . $query_hash} = $eunsubscribed;
					}
					
					$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
					$clicks = $this -> Click -> count(array('history_id' => $history -> id));
					
					?>
					<?php 
					
					echo sprintf(__('%s opened %s, %s%s unsubscribes%s %s, %s bounces %s and %s%s clicks%s out of %s emails sent out', $this -> plugin_name), 
					'<strong>' . $eread . '</strong>', 
					'(' . ((!empty($etotal)) ? number_format((($eread/$etotal) * 100), 2, '.', '') : 0) . '&#37;)', 
					'<a href="' . admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes&history_id=' . $history -> id) . '">',
					'<strong>' . $eunsubscribed . '</strong>', 
					'</a>',
					'(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', 
					'<strong>' . (empty($ebounced) ? 0 : $ebounced) . '</strong>', 
					'(' . $ebouncedperc . '&#37;)', 
					'<a href="?page=' . $this -> sections -> clicks . '&amp;history_id=' . $history -> id . '">', 
					'<strong>' . $clicks . '</strong>', 
					'</a>', 
					'<strong>' . $etotal . '</strong>'); 
					
					?>
				</td>
			</tr>
            <?php if (!empty($history -> attachments)) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                	<th><?php _e('Attachments', $this -> plugin_name); ?></th>
                    <td>
                    	<ul style="padding:0; margin:0;">
							<?php foreach ($history -> attachments as $attachment) : ?>
                            	<li class="<?php echo $this -> pre; ?>attachment">
                                	<?php echo $Html -> attachment_link($attachment, false); ?>
                                    <a class="button button-primary newsletters_attachment_remove" href="?page=<?php echo $this -> sections -> history; ?>&amp;method=removeattachment&amp;id=<?php echo $attachment['id']; ?>" onclick="if (!confirm('<?php _e('Are you sure you want to remove this attachment?', $this -> plugin_name); ?>')) { return false; }"></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
            <?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Created', $this -> plugin_name); ?></th>
				<td><?php echo $history -> created; ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php _e('Modified', $this -> plugin_name); ?></th>
				<td><?php echo $history -> modified; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
    
    <!-- Individual Emails -->
    <h3 id="emailssent"><?php _e('Emails Sent', $this -> plugin_name); ?></h3>
    <?php $this -> render('emails' . DS . 'loop', array('history' => $history, 'emails' => $emails, 'paginate' => $paginate), true, 'admin'); ?>
    
    <!-- History Preview -->
    <h3><?php _e('Preview', $this -> plugin_name); ?></h3>
	<?php $multimime = $this -> get_option('multimime'); ?>
	<?php if (!empty($history -> text) && $multimime == "Y") : ?>  
		<h4><?php _e('TEXT Version', $this -> plugin_name); ?></h4>  
	    <div class="scroll-list">
	    	<?php echo nl2br($history -> text); ?>
	    </div>
	    
	    <h4><?php _e('HTML Version', $this -> plugin_name); ?></h4>
	<?php endif; ?>
    
	<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo $preview_src; ?>" id="historypreview<?php echo $history -> id; ?>"></iframe>
    
	<div class="tablenav">
	
	</div>
</div>