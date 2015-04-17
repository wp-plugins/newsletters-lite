<div class="newsletters <?php echo $this -> pre; ?> widget_newsletters">
	<form action="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=offsite&title=<?php echo urlencode($options['title']); ?>&list=<?php echo $options['list']; ?>" onsubmit="wpmloffsite(this);" method="post">
		<input type="hidden" name="list_id[]" value="<?php echo $options['list']; ?>" />
	
		<?php if (!empty($fields)) : ?>
			<?php foreach ($fields as $field) : ?>
				<?php $this -> render_field($field -> id, true, $options['wpoptinid'], true, false, false, true); ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php $this -> render_field($Field -> email_field_id(), true, $options['wpoptinid'], true, false, false, true); ?>
		<?php endif; ?>
		<div>
			<input class="button ui-button" type="submit" name="subscribe" value="<?php echo $options['button']; ?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
function wpmloffsite(form) {
	window.open('', 'formpopup', 'resizable=0,scrollbars=1,width=<?php echo $this -> get_option('offsitewidth'); ?>,height=<?php echo $this -> get_option('offsiteheight'); ?>,status=0,toolbar=0');
	form.target = 'formpopup';
}
</script>

<?php if (!empty($options['stylesheet']) && $options['stylesheet'] == "Y") : ?>
	<style type="text/css">
	@import url('<?php echo $this -> url(); ?>/views/<?php echo $this -> get_option('theme_folder'); ?>/css/style.css');
	</style>
<?php endif; ?>