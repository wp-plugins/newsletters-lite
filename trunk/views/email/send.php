<?php echo $message; ?>

<?php if (!empty($print)) : ?>
<script type="text/javascript">
window.onload = function() {
	window.print();
}
</script>
<?php endif; ?>