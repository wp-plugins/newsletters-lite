<?php if (!empty($errors) && is_array($errors)) : ?>
	<ul class="<?php echo $this -> pre; ?>error">
		<?php foreach ($errors as $err) : ?>
			<li><?php echo stripslashes($err); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>