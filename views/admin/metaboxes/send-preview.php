<?php $src = (!empty($_POST['ishistory'])) ? admin_url('admin-ajax.php') . '?action=' . $this -> pre . 'history_iframe&id=' . $_POST['ishistory'] : false; ?>

<p>
	<a href="" id="previewrunnerbutton" onclick="previewrunner(); return false;" class="button button-secondary"><?php _e('Update Preview', $this -> plugin_name); ?></a>
	<span id="previewrunnerloading" style="display:none;"><img src="<?php echo $this -> url(); ?>/images/loading.gif" /></span>
</p>

<iframe width="100%" height="300" frameborder="0" scrolling="auto" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0; border:1px #CCCCCC solid;" src="<?php echo $src; ?>" id="previewiframe">
	<?php _e('Nothing to show yet, please add a subject, content and choose at least one mailing list.', $this -> plugin_name); ?>
</iframe>

<script type="text/javascript">
var previewrequest = false;

<?php if (!empty($_POST['ishistory'])) : ?>
var history_id = "<?php echo $_POST['ishistory']; ?>";
<?php endif; ?>

function previewrunner() {
	jQuery('iframe#content_ifr').attr('tabindex', "2");
	var formvalues = jQuery('form#post').serialize();
	var content = jQuery("iframe#content_ifr").contents().find("body#tinymce").html();
	tinyMCE.triggerSave();
	if (previewrequest) { previewrequest.abort(); }
	jQuery('#previewrunnerbutton').attr('disabled', "disabled");
	jQuery('#previewrunnerloading').show();
	
	previewrequest = jQuery.ajax({
		data: formvalues,
		dataType: 'xml',
		url: wpmlajaxurl + '?action=wpmlpreviewrunner',
		type: "POST",
		success: function(response) {
			history_id = jQuery("history_id", response).text();
			previewcontent = jQuery("previewcontent", response).text();
			if (history_id != "") { jQuery('#ishistory').val(history_id); }
		},
		complete: function(response) {		
			setTimeout(previewrunner, 30000);
			if (typeof previewcontent != 'undefined') { jQuery('#previewiframe').contents().find('html').html(previewcontent); }
			jQuery('#previewrunnerbutton').removeAttr('disabled');
			jQuery('#previewrunnerloading').hide();
			
			var iframeheight = jQuery("#previewiframe").contents().find("html").outerHeight();
			jQuery("#previewiframe").height(iframeheight).css({height: iframeheight}).attr("height", iframeheight);
		}
	});
}

jQuery(document).ready(function() {
	setTimeout(previewrunner, 60000);
});
</script>