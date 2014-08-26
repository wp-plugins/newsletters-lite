<div class="<?php echo $this -> pre; ?> widget_newsletters">
	<form action="<?php echo home_url('?' . $this -> pre . 'method=offsite&list=' . $options['list']); ?>" method="post">
		<input type="hidden" name="list_id[]" value="<?php echo $options['list']; ?>" />
		<?php $this -> render_field($Field -> email_field_id(), true, $options['wpoptinid'], true, false); ?>
		<div>
			<input class="button ui-button" type="submit" name="subscribe" value="<?php echo $options['button']; ?>" />
		</div>
	</form>
</div>