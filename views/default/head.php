<?php $embed = $this -> get_option('embed'); ?>

<script type="text/javascript">
var wpmlAjax = '<?php echo rtrim($this -> url(), '/'); ?>/<?php echo $this -> plugin_name; ?>-ajax.php';
var wpmlUrl = '<?php echo $this -> url(); ?>';
var wpmlScroll = "<?php echo ($embed['scroll'] == "Y") ? 'Y' : 'N'; ?>";

<?php if ($this -> is_plugin_active('qtranslate')) : ?>
	var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?lang=<?php echo qtrans_getLanguage(); ?>&';
<?php else : ?>
	var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?';
<?php endif; ?>

jQuery(document).ready(function() { jQuery('.<?php echo $this -> pre; ?>button').button(); });
</script>