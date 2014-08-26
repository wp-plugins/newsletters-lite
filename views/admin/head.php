<script type="text/javascript">
var wpmlAjax = '<?php echo $this -> url(); ?>/<?php echo $this -> plugin_name; ?>-ajax.php';
var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
var wpmlUrl = '<?php echo $this -> url(); ?>';

<?php if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) : ?>
	jQuery(document).ready(function() {
		jQuery(".wpmlhelp a").tooltip();
	});
<?php endif; ?>
</script>