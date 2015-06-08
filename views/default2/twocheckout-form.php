<?php if (!empty($checkoutdata)) : ?>
	<form action="https://www.2checkout.com/2co/buyer/purchase" method="post" id="<?php echo $formid; ?>" target="<?php echo $target; ?>">
		<?php foreach ($checkoutdata as $key => $val) : ?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
		<?php endforeach; ?>
		<input type="submit" class="<?php echo $this -> pre; ?>button btn btn-success paybutton" value="<?php _e('Pay Now', $this -> plugin_name); ?>" name="checkout" />
	</form>
	
	<?php if ($autosubmit) : ?>
		<script type="text/javascript">
		document.getElementById('<?php echo $formid; ?>').submit();
		</script>
	<?php endif; ?>
<?php endif; ?>