<script type="text/javascript">
var wpmlAjax = '<?php echo $this -> url(); ?>/<?php echo $this -> plugin_name; ?>-ajax.php';
var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
var newsletters_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
var wpmlUrl = '<?php echo $this -> url(); ?>';

<?php if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) : ?>
	jQuery(document).ready(function() {
		if (jQuery.isFunction(jQuery.fn.tooltip)) {
			jQuery(".wpmlhelp a").tooltip({
				tooltipClass: 'newsletters-ui-tooltip',
				content: function () {
		            return jQuery(this).prop('title');
		        },
		        show: null, 
		        close: function (event, ui) {
		            ui.tooltip.hover(
		            function () {
		                jQuery(this).stop(true).fadeTo(400, 1);
		            },    
		            function () {
		                jQuery(this).fadeOut("400", function () {
		                    jQuery(this).remove();
		                })
		            });
		        }
			});
		}
	});
<?php endif; ?>
</script>

<!--[if lt IE 10]>
<style type="text/css">
.newsletters_loading {
	width: 16px;
	height: 16px;
	background: transparent url('<?php echo $this-> render_url('images/loading.gif', 'admin', false); ?>') no-repeat;
	display: inline-block;
}
</style>
<![endif]-->