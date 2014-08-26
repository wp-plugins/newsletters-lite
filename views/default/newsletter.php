<?php if (!empty($email)) : ?>
	<?php echo wpautop($this -> process_set_variables($subscriber, $user, __($email -> message), $email -> id)); ?>
<?php endif; ?>