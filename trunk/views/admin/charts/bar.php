<div id="<?php echo $id; ?>-legend" class="newsletters-chart-legend"></div>

<div class="newsletters-bar-chart-holder">
	<canvas id="<?php echo $id; ?>" style="width:<?php echo $attributes['width']; ?>; height:<?php echo $attributes['height']; ?>"></canvas>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	var data = <?php echo json_encode($data); ?>;
	var options = <?php echo json_encode($options); ?>;
	var ctx = document.getElementById('<?php echo $id; ?>').getContext("2d");
	
	var barChart = new Chart(ctx).Bar(data, options);
	var legend = barChart.generateLegend();
	jQuery('#<?php echo $id; ?>-legend').html(legend);
});
</script>