<?php if (!empty($errors) && is_array($errors)) : ?>
	<div class="alert alert-danger">
		<ul class="newsletters_nolist">
			<?php foreach ($errors as $err) : ?>
				<li><i class="fa fa-exclamation-triangle"></i> <?php echo stripslashes($err); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>