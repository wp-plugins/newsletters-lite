<?php if (!empty($checkoutdata)) : ?>
	<?php if ($this -> get_option('paypalsandbox') == "Y") : ?>
		<form id="<?php echo $formid; ?>" action="<?php echo $this -> get_option('paypalsandurl'); ?>" method="post" target="<?php echo $target; ?>">
	<?php else : ?>
		<form id="<?php echo $formid; ?>" action="<?php echo $this -> get_option('paypalliveurl'); ?>" method="post" target="<?php echo $target; ?>">
	<?php endif; ?>	
		<?php foreach ($checkoutdata as $ckey => $cval) : ?>
			<input type="hidden" name="<?php echo $ckey; ?>" value="<?php echo $cval; ?>" />
		<?php endforeach; ?>
		<input type="submit" class="<?php echo $this -> pre; ?>button ui-button-success paybutton" name="checkout" value="<?php _e('Pay Now', $this -> plugin_name); ?>" />
	</form>
	
	<?php if ($autosubmit) : ?>
		<script type="text/javascript">
		document.getElementById('<?php echo $formid; ?>').submit();
		</script>
	<?php endif; ?>
<?php endif; ?>