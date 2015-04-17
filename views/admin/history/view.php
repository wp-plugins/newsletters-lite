<!-- History View -->

<?php

$preview_src = admin_url('admin-ajax.php') . '?action=' . $this -> pre . 'history_iframe&id=' . $history -> id . '&rand=' . rand(1,999);

$user_chart = $this -> get_user_option(false, 'chart');
$chart = (empty($user_chart)) ? "bar" : $user_chart; 

$type = (empty($_GET['type'])) ? 'days' : $_GET['type'];
$fromdate = (empty($_GET['from'])) ? $Html -> gen_date("Y-m-d", strtotime("-13 days")) : $_GET['from'];
$todate = (empty($_GET['to'])) ? $Html -> gen_date("Y-m-d", time()) : $_GET['to'];

?>

<div class="wrap newsletters <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Sent/Draft:', $this -> plugin_name); ?> <?php echo $history -> subject; ?> <a href="?page=<?php echo $this -> sections -> history; ?>&method=view&id=<?php echo $history -> id; ?>" class="add-new-h2"><?php _e('Refresh', $this -> plugin_name); ?></a></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; All Sent &amp; Drafts', $this -> plugin_name), $this -> url); ?></div>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<a href="?page=<?php echo $this -> sections -> send; ?>&amp;method=history&amp;id=<?php echo $history -> id; ?>" class="button button-primary"><i class="fa fa-paper-plane"></i> <?php _e('Send/Edit', $this -> plugin_name); ?></a>
			<a onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo $preview_src; ?>'}); return false;" href="#" class="button"><i class="fa fa-eye"></i> <?php _e('Preview', $this -> plugin_name); ?></a>
			<a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=delete&amp;id=<?php echo $history -> id; ?>" class="button button-highlighted" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this history email?', $this -> plugin_name); ?>')) { return false; }"><i class="fa fa-trash"></i> <?php _e('Delete', $this -> plugin_name); ?></a>
			<?php /*echo $Html -> link(__('Duplicate', $this -> plugin_name), '?page=' . $this -> sections -> history . '&amp;method=duplicate&amp;id=' . $history -> id, array('class' => "button"));*/ ?>
			<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&amp;method=duplicate&amp;id=' . $history -> id); ?>" class="button"><i class="fa fa-clipboard"></i> <?php _e('Duplicate', $this -> plugin_name); ?></a>
		</div>
	</div>
	
	<div class="postbox" style="padding:10px;">
		<p>
			<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=bar'); ?>" class="button <?php echo (empty($chart) || $chart == "bar") ? 'active' : ''; ?>"><i class="fa fa-bar-chart"></i></a>
			<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=line'); ?>" class="button <?php echo (!empty($chart) && $chart == "line") ? 'active' : ''; ?>"><i class="fa fa-line-chart"></i></a>
			<?php echo $Html -> help(__('Switch between bar and line charts.', $this -> plugin_name)); ?>
		</p>
		
		<div id="chart-legend" class="newsletters-chart-legend"></div>
		<canvas id="canvas" style="width:100%; height:300px;"></canvas>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {	
			var ajaxdata = {type:'<?php echo $type; ?>', chart:'<?php echo $chart; ?>', from:'<?php echo $fromdate; ?>', to:'<?php echo $to; ?>', history_id:'<?php echo $history -> id; ?>'};
			
			jQuery.getJSON(newsletters_ajaxurl + '?action=wpmlwelcomestats', ajaxdata, function(json) {
				var barChartData = json;
				var ctx = document.getElementById("canvas").getContext("2d");
				
				<?php if (empty($chart) || $chart == "bar") : ?>
					var barChart = new Chart(ctx).Bar(barChartData, {
						barShowStroke: false,
						responsive: true,
						multiTooltipTemplate: "\<\%\= datasetLabel \%\>: \<\%\= value \%\>",
						legendTemplate: "<ul class=\"\<\%\=name.toLowerCase()\%\>-legend\">\<\% for (var i=0; i<datasets.length; i++){\%\><li><span style=\"background-color:<\%\=datasets[i].fillColor\%\>\"></span>\<\% if(datasets[i].label){ \%\><\%\=datasets[i].label\%\>\<\%}\%\></li>\<\%}\%\></ul><br class=\"clear\" />"
					});
				<?php else : ?>
					var barChart = new Chart(ctx).Line(barChartData, {
						responsive: true,
						bezierCurve: false,
						datasetFill: false,
						multiTooltipTemplate: "\<\%\= datasetLabel \%\>: \<\%\= value \%\>",
						legendTemplate: "<ul class=\"\<\%\=name.toLowerCase()\%\>-legend\">\<\% for (var i=0; i<datasets.length; i++){\%\><li><span style=\"background-color:<\%\=datasets[i].fillColor\%\>\"></span>\<\% if(datasets[i].label){ \%\><\%\=datasets[i].label\%\>\<\%}\%\></li>\<\%}\%\></ul><br class=\"clear\" />"
					});
				<?php endif; ?>
				
				var legend = barChart.generateLegend();
				jQuery('#chart-legend').html(legend);
			});
		});
		</script>
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
						<?php else : ?>
							<?php _e('None', $this -> plugin_name); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Roles', $this -> plugin_name); ?></th>
					<td>
						<?php if (!empty($history -> roles)) : ?>
							<?php 
								
							global $wp_roles;
							$roles = maybe_unserialize($history -> roles); 
							$role_names = $wp_roles -> get_names();
							$roles_output = array();
							
							if (!empty($roles) && is_array($roles)) {
								foreach ($roles as $role) {
									$roles_output[] = '<a href="' . admin_url('users.php?role=' . $role) . '">' . __($role_names[$role]) . '</a>';
								}
								
								$roles_output = implode(", ", $roles_output);
							}
							
							echo $roles_output;
							
							?>
						<?php else : ?>
							<?php _e('None', $this -> plugin_name); ?>
						<?php endif; ?>
					</td>
				</tr>
	            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	            	<th><?php _e('Template', $this -> plugin_name); ?></th>
	                <td>
	                	<?php $Db -> model = $Theme -> model; ?>
	                    <?php if (!empty($history -> theme_id) && $theme = $Db -> find(array('id' => $history -> theme_id))) : ?>
	                    	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" title="<?php _e('Template Preview:', $this -> plugin_name); ?> <?php echo $theme -> title; ?>"><?php echo $theme -> title; ?></a>
	                    	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo __($theme -> title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a>
	                    	<a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', $this -> plugin_name), __($theme -> title)); ?>', href:wpmlajaxurl + '?action=newsletters_themeedit&amp;id=<?php echo $theme -> id; ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a>
	                    <?php else : ?>
	                    	<?php _e('None', $this -> plugin_name); ?>
	                    <?php endif; ?>
	                </td>
	            </tr>
	            <?php if (!empty($history -> post_id)) : ?>
	            	<?php $post = get_post($history -> post_id); ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            	<th><?php _e('Post', $this -> plugin_name); ?>
		            	<?php echo $Html -> help(__('If a post/page was published from this newsletter, it will be linked/associated and shown here.', $this -> plugin_name)); ?></th>
		            	<td>
			            	<a href="<?php echo get_permalink($history -> post_id); ?>" target="_blank"><?php echo __($post -> post_title); ?></a>
			            	<a class="" href="<?php echo get_delete_post_link($post -> ID); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this post?', $this -> plugin_name); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
			            	<a class="" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=unlinkpost&id=' . $_POST['ishistory']); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to unlink this post from this newsletter?', $this -> plugin_name); ?>')) { return false; }"><i class="fa fa-chain-broken"></i></a>
		            	</td>
	            	</tr>
	            <?php endif; ?>
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
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            <th><?php _e('Recurring', $this -> plugin_name); ?></th>
		            <td>
			            <?php if (!empty($history -> recurring) && $history -> recurring == "Y") : ?>
                    		<?php _e('Yes', $this -> plugin_name); ?>
                    		<?php $helpstring = sprintf(__('Send every %s %s', $this -> plugin_name), $history -> recurringvalue, $history -> recurringinterval); ?>
                    		<?php if (!empty($history -> recurringlimit)) : ?><?php $helpstring .= sprintf(__(' and repeat %s times', $this -> plugin_name), $history -> recurringlimit); ?><?php endif; ?>
                    		<?php $helpstring .= sprintf(__(' starting %s and has been sent %s times already'), $history -> recurringdate, $history -> recurringsent); ?>
                    		(<?php echo $helpstring; ?>)
                    	<?php else : ?>
                    		<?php _e('No', $this -> plugin_name); ?>
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
	            <?php $Db -> model = $Autoresponder -> model; ?>
	            <?php if ($autoresponders = $Db -> find_all(array('history_id' => $history -> id))) : ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            	<th><?php _e('Autoresponders', $this -> plugin_name); ?>
		            	<?php echo $Html -> help(__('Autoresponders linked to this newsletter', $this -> plugin_name)); ?></th>
		            	<td>
			            	<ul>
				            	<?php foreach ($autoresponders as $autoresponder) : ?>
				            		<li><a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&amp;method=save&amp;id=' . $autoresponder -> id); ?>"><?php _e($autoresponder -> title); ?></a></li>
				            	<?php endforeach; ?>
			            	</ul>
		            	</td>
	            	</tr>
	            <?php endif; ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Tracking', $this -> plugin_name); ?></th>
					<td>
						<?php 
							
						global $wpdb; $Db -> model = $Email -> model;
						$etotal = $Db -> count(array('history_id' => $history -> id));
						$eread = $Db -> count(array('read' => "Y", 'history_id' => $history -> id));
						$tracking = (($eread/$etotal) * 100);
						
						$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'";
						
						$query_hash = md5($query);
						if ($ob_ebounced = $this -> get_cache($query_hash)) {
							$ebounced = $ob_ebounced;
						} else {
							$ebounced = $wpdb -> get_var($query);
							$this -> set_cache($query_hash, $ebounced);
						}
						
						$ebouncedperc = (!empty($etotal)) ? number_format((($ebounced/$etotal) * 100), 2, '.', '') : 0;
						
						$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'";
						
						$query_hash = md5($query);
						if ($ob_eunsubscribed = $this -> get_cache($query_hash)) {
							$eunsubscribed = $ob_eunsubscribed;
						} else {
							$eunsubscribed = $wpdb -> get_var($query);
							$this -> set_cache($query_hash, $eunsubscribed);
						}
						
						$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
						$clicks = $this -> Click -> count(array('history_id' => $history -> id));
						
						?>
						<?php 
						
						echo sprintf(__('%s read %s, %s%s unsubscribes%s %s, %s%s bounces%s %s and %s%s clicks%s out of %s emails sent out', $this -> plugin_name), 
						'<strong>' . $eread . '</strong>', 
						'(' . ((!empty($etotal)) ? number_format($tracking, 2, '.', '') : 0) . '&#37;)', 
						'<a href="' . admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes&history_id=' . $history -> id) . '">',
						'<strong>' . $eunsubscribed . '</strong>', 
						'</a>',
						'(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', 
						'<a href="' . admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces&history_id=' . $history -> id) . '">',
						'<strong>' . (empty($ebounced) ? 0 : $ebounced) . '</strong>', 
						'</a>',
						'(' . $ebouncedperc . '&#37;)', 
						'<a href="?page=' . $this -> sections -> clicks . '&amp;history_id=' . $history -> id . '">', 
						'<strong>' . $clicks . '</strong>', 
						'</a>', 
						'<strong>' . $etotal . '</strong>'); 
						
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
	                                    <a class="button button-primary" href="?page=<?php echo $this -> sections -> history; ?>&amp;method=removeattachment&amp;id=<?php echo $attachment['id']; ?>" onclick="if (!confirm('<?php _e('Are you sure you want to remove this attachment?', $this -> plugin_name); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
	                                </li>
	                            <?php endforeach; ?>
	                        </ul>
	                    </td>
	                </tr>
	            <?php endif; ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Created', $this -> plugin_name); ?></th>
					<td><abbr title="<?php echo $history -> created; ?>"><?php echo $Html -> gen_date(false, strtotime($history -> created)); ?></abbr></td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php _e('Modified', $this -> plugin_name); ?></th>
					<td><abbr title="<?php echo $history -> modified; ?>"><?php echo $Html -> gen_date(false, strtotime($history -> modified)); ?></abbr></td>
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
    <div class="postbox" style="padding:10px;">
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo $preview_src; ?>" id="historypreview<?php echo $history -> id; ?>"></iframe>
    </div>
    
	<div class="tablenav">
	
	</div>
</div>