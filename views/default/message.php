<?php if (!empty($message)) : ?>
	<div class="ui-state-highlight ui-corner-all">
		<p>
			<li><i class="fa fa-check"></i>
			<?php echo stripslashes($message); ?>
		</p>
	</div>
<?php endif; ?>