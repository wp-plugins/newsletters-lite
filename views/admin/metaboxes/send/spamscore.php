<div id="spamscore_result">
	<?php if (!empty($_POST['spamscore'])) : ?>
		<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo admin_url('admin-ajax.php'); ?>?action=newsletters_gauge&value=<?php echo $_POST['spamscore']; ?>"></iframe>
	<?php else : ?>
		<p style="text-align:center;"><?php _e('Click "Check Now" to test', $this -> plugin_name); ?></p>
	<?php endif; ?>
</div>

<p style="text-align:center;"><a class="button button-secondary button-small" id="spamscorerunnerbutton" href="" onclick="spamscorerunner(); return false;"><?php _e('Check Now', $this -> plugin_name); ?></a>
<span id="spamscorerunnerloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span></p>

<?php /*<script type="text/javascript" src="<?php echo $this -> render_url('js/justgage.js', 'admin', false); ?>"></script>
<script type="text/javascript" src="<?php echo $this -> render_url('js/raphael.js', 'admin', false); ?>"></script>*/ ?>

<script type="text/javascript">
var spamscorerequest = false;

<?php $history_id = (empty($_POST['ishistory'])) ? $_GET['id'] : $_POST['ishistory']; ?>

<?php if (!empty($history_id)) : ?>
var history_id = "<?php echo $history_id; ?>";
<?php else : ?>
var history_id = false;
<?php endif; ?>

function spamscorerunner() {
	jQuery('iframe#content_ifr').attr('tabindex', "2");
	var formvalues = jQuery('form#post').serialize();
	var content = jQuery("iframe#content_ifr").contents().find("body#tinymce").html();
	
	if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
		tinyMCE.triggerSave();
	}
	
	if (spamscorerequest) { spamscorerequest.abort(); }
	jQuery('#spamscorerunnerbutton').attr('disabled', "disabled");
	jQuery('#spamscorerunnerloading').show();
	
	spamscorerequest = jQuery.ajax({
		data: formvalues,
		dataType: 'xml',
		url: wpmlajaxurl + '?action=newsletters_spamscorerunner',
		type: "POST",
		success: function(response) {
			succ = jQuery("success", response).text();
			report = jQuery("report", response).text();
			score = jQuery("score", response).text();
			output = jQuery("output", response).text();
			jQuery('#spamscore_result').html(output);
		},
		complete: function(response) {
			jQuery('#spamscorerunnerloading').hide();
			jQuery('#spamscorerunnerbutton').removeAttr('disabled');
		},
		cache: true
	});
}

jQuery(document).ready(function() {
	setTimeout(function() {
		if (history_id != false) {
			spamscorerunner();
		}
	
		setInterval(spamscorerunner, 60000);
	}, 3000);
});
</script>