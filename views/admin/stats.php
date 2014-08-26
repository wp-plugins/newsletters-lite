<div class="wrap">
	<h2><?php _e('Subscriber Stats', $this -> plugin_name); ?></h2>
	<p><?php _e('Below are your subscriber statistics for the past week', $this -> plugin_name); ?></p>
	<br class="clear" />
	
	<ul style="float:left; margin:0; padding:0; list-style:none;">
		<?php foreach ($days as $day) : ?>
		<li style="padding:5px; height:20px;"><?php echo __($this -> gen_date("D d M", $day['timestamp']), $this -> plugin_name); ?></li>
		<?php endforeach; ?>
	</ul>
	
	<?php if (!empty($days)) : ?>
		<ul style="margin:0; padding:0; list-style:none; float:left;">
			<?php foreach ($days as $day) : ?>
			<?php $width = (empty($day['subscribers'])) ? 10 : ($day['subscribers'] * (500 / $high)); ?>
			<li style="padding:5px; background:#83B4D8; height:20px; width:<?php echo $width; ?>px;"><?php echo $day['subscribers']; ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No days were found', $this -> plugin_name); ?></p>
	<?php endif; ?>
</div>