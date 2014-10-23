<?php $embed = $this -> get_option('embed'); ?>

<script type="text/javascript">
var wpmlAjax = '<?php echo rtrim($this -> url(), '/'); ?>/<?php echo $this -> plugin_name; ?>-ajax.php';
var wpmlUrl = '<?php echo $this -> url(); ?>';
var wpmlScroll = "<?php echo ($embed['scroll'] == "Y") ? 'Y' : 'N'; ?>";

<?php if ($this -> language_do()) : ?>
	var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?lang=<?php echo $this -> language_current(); ?>&';
<?php else : ?>
	var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?';
<?php endif; ?>

jQuery(document).ready(function() { jQuery('.<?php echo $this -> pre; ?>button').button(); });
</script>

<style type="text/css">
<?php if (get_option('wpmlcustomcss') == "Y") : ?>
	<?php echo stripslashes(get_option('wpmlcustomcsscode')); ?>
<?php endif; ?>
</style>

<!--[if lt IE 10]>
<style type="text/css">
.newsletters_loading {
	width: 16px;
	height: 16px;
	background: transparent url('<?php echo $this-> render_url('images/loading.gif', 'default', false); ?>') no-repeat;
	display: inline-block;
}
</style>
<![endif]-->