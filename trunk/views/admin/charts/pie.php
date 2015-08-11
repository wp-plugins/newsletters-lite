<div class="newsletters-pie-chart-holder">
	<canvas id="<?php echo $id; ?>" width="<?php echo $attributes['width']; ?>" height="<?php echo $attributes['width']; ?>"></canvas>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	var data = <?php echo json_encode($data); ?>;
	var options = <?php echo json_encode($options); ?>;
	var ctx = document.getElementById('<?php echo $id; ?>').getContext("2d");
	var chart = new Chart(ctx).Doughnut(data,options);
});
</script>