<div class="newsletters-dashboard-widget">
	<div>
		<?php
		
		$from = date("Y-m-d", strtotime("-6 days"));
		$to = date("Y-m-d", time());
		
		?>
		<div id="chart-legend" class="newsletters-chart-legend"></div>
		<canvas id="canvas" style="width:100%; height:200px;"></canvas>
	</div>
	<br class="clear" />
	
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var ajaxdata = {from:'<?php echo $from; ?>', to:'<?php echo $to; ?>'};
		
		jQuery.getJSON(newsletters_ajaxurl + '?action=wpmlwelcomestats', ajaxdata, function(json) {
			var barChartData = json;
			var ctx = document.getElementById("canvas").getContext("2d");
			var barChart = new Chart(ctx).Bar(barChartData, {
				barShowStroke: false,
				multiTooltipTemplate: "<%= datasetLabel %>: <%= value %>",
				legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul><br class=\"clear\" />"
			});
			var legend = barChart.generateLegend();
			jQuery('#chart-legend').html(legend);
		})
	});
	</script>
	
	<?php
	
	$Db -> model = $History -> model;
	$histories = $Db -> find_all(false, false, array('modified', "DESC"), 5);
	
	?>
	
	<div class="newsletters-dashboard-widget-column">
		<h4><?php _e('Recent Newsletters', $this -> plugin_name); ?></h4>
		<?php if (!empty($histories)) : ?>
			<?php /*<table>
				<tbody>
					<?php foreach ($histories as $history) : ?>
						<tr>
							<td class="first b b-ad">
								<?php echo __($history -> subject); ?>
							</td>
							<td class="t ad" style="text-align:right; width:100px;">
								<a class="button button-small" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>"><?php _e('View', $this -> plugin_name); ?></a>
								<a class="button button-small" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> send . '&amp;method=history&amp;id=' . $history -> id); ?>"><?php _e('Edit', $this -> plugin_name); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>*/ ?>
			<ul>
				<?php foreach ($histories as $history) : ?>
					<li>
						<a class="welcome-icon dashicons-edit" style="float:left; padding:0; width:20px;" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> send . '&method=history&id=' . $history -> id); ?>"></a>
						<a class="welcome-icon dashicons-visibility" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id); ?>"><?php _e($history -> subject); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			
			<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history); ?>"><?php _e('View All Newsletters', $this -> plugin_name); ?></a>
			<p><?php _e('or', $this -> plugin_name); ?> <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> send); ?>"><?php _e('create a new one', $this -> plugin_name); ?></a></p>
		<?php else : ?>
			<p><?php echo sprintf(__('No emails are available yet, please %s.', $this -> plugin_name), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> send) . '">' . __('create one', $this -> plugin_name) . '</a>'); ?></p>
		<?php endif; ?>
	</div>
	
	<?php
	
	global $wpdb;
	$Db -> model = $Email -> model;
	$emails = $Db -> count();
	$read = $Db -> count(array('read' => "Y"));
	$tracking = (($read / $emails) * 100);
	$Db -> model = $Subscriber -> model;
	$total = $Db -> count();
	$Db -> model = $SubscribersList -> model;
	$active = $Db -> count(array('active' => "Y"));
	$Db -> model = $Unsubscribe -> model;
	$unsubscribes = $Db -> count();
	$eunsubscribeperc = (($unsubscribes / $emails) * 100);
	$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "`";
	$bounces = $wpdb -> get_var($query);
	$bounces = (empty($bounces)) ? 0 : $bounces;
	$ebouncedperc = (($bounces / $emails) * 100);
	
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
	
	?>
	
	<div class="newsletters-dashboard-widget-column">
		<h4><?php _e('Overview', $this -> plugin_name); ?></h4>
		<?php $Html -> pie_chart('overview-chart', array('width' => 200), $data, $options); ?>
	</div>
	
	<?php /*
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
						<a class="waiting" href="admin.php?page=<?php echo $this -> sections -> subscribers; ?>&method=unsubscribes"><?php _e('Unsubscribes', $this -> plugin_name); ?></a>
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
		
		<p>
			<a class="button" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> welcome); ?>"><?php _e('See full overview', $this -> plugin_name); ?></a>
		</p>
	</div>
	*/ ?>
	
	<br class="clear" />
</div>