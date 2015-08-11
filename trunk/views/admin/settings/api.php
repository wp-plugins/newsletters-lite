<!-- API -->

<?php

$api_endpoint = admin_url('admin-ajax.php') . '?action=newsletters_api';
$api_key = $this -> get_option('api_key');

?>

<div class="wrap newsletters">
	<h2><?php _e('JSON API', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('settings-navigation', false, true, 'admin'); ?>
	
	<p><?php _e('Use the JSON API to perform certain functions via API calls.', $this -> plugin_name); ?><br/>
	<?php _e('It can be from a remote server or from a 3rd party application, plugin, template, etc.', $this -> plugin_name); ?></p>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for=""><?php _e('API Endpoint', $this -> plugin_name); ?></label></th>
				<td>
					<code><?php echo $api_endpoint; ?></code>
					<span class="howto"><?php _e('The URL to submit API calls to', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for=""><?php _e('API Key', $this -> plugin_name); ?></label></th>
				<td>
					<code><span id="api_key"><?php echo $api_key; ?></span></code>
					<a class="button button-secondary button-small" onclick="if (confirm('<?php _e('Are you sure you want to generate a new key? The previous key will stop working.', $this -> plugin_name); ?>')) { newsletters_api_newkey(); } return false;"><?php _e('Generate New Key', $this -> plugin_name); ?></a>
					<span id="api_key_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
					<span class="howto"><?php _e('Unique key to use for authentication with the API', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3><?php _e('Making an API Call', $this -> plugin_name); ?></h3>
	<p><?php _e('Below is an example of making a JSON API call', $this -> plugin_name); ?></p>
	
	<script src="https://gist.github.com/tribulant/846400b10de1897de805.js"></script>

	<h3><?php _e('API Methods', $this -> plugin_name); ?></h3>
	
	<h4>subscriber_add</h4>
	<script src="https://gist.github.com/tribulant/aa76b890e48e2da1ece8.js"></script>
	
	<h4>subscriber_delete</h4>
	<script src="https://gist.github.com/tribulant/3452bd0769dbd7c1a0db.js"></script>
	
	<h4>send_email</h4>
	<script src="https://gist.github.com/tribulant/a82a8f1487e1afcf01eb.js"></script>
</div>

<script type="text/javascript">
function newsletters_api_newkey() {
	jQuery('#api_key_loading').show();

	jQuery.ajax({
		url: wpmlajaxurl + '?action=newsletters_api_newkey',
		success: function(response) {
			jQuery('#api_key_loading').hide();
			jQuery('#api_key').html(response);
		}
	});
}
</script>