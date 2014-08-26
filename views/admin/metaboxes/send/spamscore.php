<div id="spamscore_result">
	<p><?php _e('Click "Check Now" to test', $this -> plugin_name); ?></p>
</div>

<p style="text-align:center;"><a class="button button-secondary button-small" id="spamscorerunnerbutton" href="" onclick="spamscorerunner(); return false;"><?php _e('Check Now', $this -> plugin_name); ?></a>
<span id="spamscorerunnerloading" style="display:none;"><img src="<?php echo $this -> render_url('images/loading.gif', 'admin', false); ?>" alt="loading" /></span></p>

<script type="text/javascript" src="<?php echo $this -> render_url('js/justgage.js', 'admin', false); ?>"></script>
<script type="text/javascript" src="<?php echo $this -> render_url('js/raphael.js', 'admin', false); ?>"></script>

<script type="text/javascript">
var spamscorerequest = false;

<?php if (!empty($_POST['ishistory'])) : ?>
var history_id = "<?php echo $_POST['ishistory']; ?>";
<?php endif; ?>

function spamscorerunner() {
	jQuery('iframe#content_ifr').attr('tabindex', "2");
	var formvalues = jQuery('form#post').serialize();
	var content = jQuery("iframe#content_ifr").contents().find("body#tinymce").html();
	tinyMCE.triggerSave();
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
			//setTimeout(spamscorerunner, 60000);
			jQuery('#spamscorerunnerloading').hide();
			jQuery('#spamscorerunnerbutton').removeAttr('disabled');
		}
	});
}

jQuery(document).ready(function() {
	if (history_id != "") {
		spamscorerunner();
	}

	setTimeout(spamscorerunner, 60000);
});
</script>