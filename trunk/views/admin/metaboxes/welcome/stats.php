<!-- Overview Chart -->

<?php

$user_chart_type = $this -> get_user_option(false, 'chart_type');
$chart_type = (empty($user_chart_type)) ? 'days' : $user_chart_type;

$fromdate = (empty($_GET['from'])) ? $Html -> gen_date("Y-m-d", strtotime("-13 days")) : $_GET['from'];
$todate = (empty($_GET['to'])) ? $Html -> gen_date("Y-m-d", time()) : $_GET['to'];

$user_chart = $this -> get_user_option(false, 'chart');
$chart = (empty($user_chart)) ? 'bar' : $user_chart;

?>

<div class="alignleft actions">
	<p>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=years'); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "years") ? 'active' : ''; ?>"><?php _e('Years', $this -> plugin_name); ?></a>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=months'); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "months") ? 'active' : ''; ?>"><?php _e('Months', $this -> plugin_name); ?></a>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=days'); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "days") ? 'active' : ''; ?>"><?php _e('Days', $this -> plugin_name); ?></a>
		<?php echo $Html -> help(__('Display the chart with stats below by days, months or years according to your needs. The default is days.', $this -> plugin_name)); ?>
		
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=bar'); ?>" class="button <?php echo (empty($chart) || $chart == "bar") ? 'active' : ''; ?>"><i class="fa fa-bar-chart"></i></a>
		<a href="<?php echo $Html -> retainquery('newsletters_method=set_user_option&option=chart&value=line'); ?>" class="button <?php echo (!empty($chart) && $chart == "line") ? 'active' : ''; ?>"><i class="fa fa-line-chart"></i></a>
		<?php echo $Html -> help(__('Switch between bar and line charts.', $this -> plugin_name)); ?>
	</p>
</div>
<div class="alignright actions">
	<p>
		<form action="" method="get">
			<input type="hidden" name="chart" value="<?php echo $chart; ?>" />
			<input type="hidden" name="chart_type" value="<?php echo $chart_type; ?>" />
			<input type="hidden" name="page" value="<?php echo $this -> sections -> welcome; ?>" />
			<input style="width:100px;" type="text" name="from" value="<?php echo $fromdate; ?>" id="fromdate" />
			<?php _e('to', $this -> plugin_name); ?>
			<input style="width:100px;" type="text" name="to" value="<?php echo $todate; ?>" id="todate" />
			<input class="button button-primary" type="submit" name="changedate" value="<?php _e('Change', $this -> plugin_name); ?>" />
			<?php echo $Html -> help(__('By default, the chart will show stats for the last 30 days, including today. Use the two date inputs to choose a from and to date to create a range.', $this -> plugin_name)); ?>
		</form>
	</p>
</div>

<div id="chart-legend" class="newsletters-chart-legend"></div>
<canvas id="canvas" style="width:100%; height:300px;"></canvas>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#fromdate').datepicker({showButtonPanel:true, numberOfMonths:2, changeMonth:true, changeYear:true, defaultDate:"<?php echo $fromdate; ?>", dateFormat:"yy-mm-dd"});
	jQuery('#todate').datepicker({showButtonPanel:true, numberOfMonths:2, changeMonth:true, changeYear:true, defaultDate:"<?php echo $todate; ?>", dateFormat:"yy-mm-dd"});
	
	var ajaxdata = {type:'<?php echo $chart_type; ?>', chart:'<?php echo $chart; ?>', from:'<?php echo $fromdate; ?>', to:'<?php echo $todate; ?>'};
	
	jQuery.getJSON(newsletters_ajaxurl + '?action=wpmlwelcomestats', ajaxdata, function(json) {
		helpers = Chart.helpers;
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
	});
});
</script>