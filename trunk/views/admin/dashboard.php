<div class="newsletters-dashboard-widget">
	<?php
		
	$user_chart = $this -> get_user_option(false, 'chart');
	$chart = (empty($user_chart)) ? "bar" : $user_chart;
	
	$from = $Html -> gen_date("Y-m-d", strtotime("-6 days"));
	$to = $Html -> gen_date("Y-m-d", time());
	
	?>
	
	<p>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=bar'); ?>" class="button <?php echo (empty($chart) || $chart == "bar") ? 'active' : ''; ?>"><i class="fa fa-bar-chart"></i></a>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=line'); ?>" class="button <?php echo (!empty($chart) && $chart == "line") ? 'active' : ''; ?>"><i class="fa fa-line-chart"></i></a>
		<?php echo $Html -> help(__('Switch between bar and line charts.', $this -> plugin_name)); ?>
	</p>
	
	<div>
		<div id="chart-legend" class="newsletters-chart-legend"></div>
		<canvas id="canvas" style="width:100%; height:200px;"></canvas>
	</div>
	<br class="clear" />
	
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var ajaxdata = {chart:'<?php echo $chart; ?>', from:'<?php echo $from; ?>', to:'<?php echo $to; ?>'};
		
		jQuery.getJSON(newsletters_ajaxurl + '?action=wpmlwelcomestats', ajaxdata, function(json) {
			var barChartData = json;
			var ctx = document.getElementById("canvas").getContext("2d");
			
			<?php if (empty($chart) || $chart == "bar") : ?>
				var barChart = new Chart(ctx).Bar(barChartData, {
					responsive: true,
					barShowStroke: false,
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
		<?php $options = false; ?>
		<?php $Html -> pie_chart('overview-chart', array('width' => 200), $data, $options); ?>
	</div>
	
	<br class="clear" />
</div>