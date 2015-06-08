<?php if (!empty($message)) : ?>
	<div class="alert alert-success">
		<p>
			<li><i class="fa fa-check"></i>
			<?php echo stripslashes($message); ?>
		</p>
	</div>
<?php endif; ?>