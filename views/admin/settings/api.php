<!-- API -->

<?php

$api_endpoint = admin_url('admin-ajax.php') . '?action=newsletters_api';
$api_key = $this -> get_option('api_key');

?>

<div class="wrap newsletters">
	<h2><?php _e('JSON API', $this -> plugin_name); ?></h2>
	
	<?php $this -> render('settings-navigation', false, true, 'admin'); ?>
	
	<p><?php _e('Use the JSON API to perform certain functions via API calls.', $this -> plugin_name); ?><br/>
	<?php _e('It can be from a remote server or from a 3rd party application, plugin, theme, etc.', $this -> plugin_name); ?></p>
	
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
					<span id="api_key_loading" style="display:none;"><span class="newsletters_loading"></span></span>
					<span class="howto"><?php _e('Unique key to use for authentication with the API', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3><?php _e('Making an API Call', $this -> plugin_name); ?></h3>
	<p><?php _e('Below is an example of making a JSON API call', $this -> plugin_name); ?></p>
	
	<pre class="brush:php; toolbar:false; gutter:false;">$url = '<?php echo $api_endpoint; ?>';
$data = array(
	'api_method' 		=> 	'subscriber_add', 
	'api_key' 			=> 	'<?php echo $api_key; ?>',
	'api_data' 			=> 	array(
		'email'				=> "email@domain.com",
		'list_id'			=>	array(1,2,3),
	)
);                                                   

$data_string = json_encode($data);                                                                                 
 
$ch = curl_init($url);                                                                      
curl_setopt($ch, CURLOPT_POST, true);                                                                 
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data_string))                                                                       
);
 
$result = json_decode(curl_exec($ch));
curl_close($ch);</pre>
</div>

	<h3><?php _e('API Methods', $this -> plugin_name); ?></h3>
	
	<h4>subscriber_add</h4>
	<pre class="brush:php; toolbar:false; gutter:false;">$data = array(
	'api_method' 		=> 	'subscriber_add', 
	'api_key' 			=> 	'<?php echo $api_key; ?>',
	'api_data' 			=> 	array(
		'email'				=> "email@domain.com",
		'list_id'			=>	array(1,2,3),
	)
); 

// Success: {"success":"true","result":{"id":"123"},"method":"subscriber_add"}
// Error: {"success":"false","errormessage":"Subscriber cannot be added"}</pre>
	
	<h4>subscriber_delete</h4>
	<pre class="brush:php; toolbar:false; gutter:false;">$data = array(
	'api_method' 		=> 	'subscriber_delete', 
	'api_key' 			=> 	'<?php echo $api_key; ?>',
	'api_data' 			=> 	array(
		'id'				=>	'123',
	)
); 

// Success: {"success":"true","result":{"0":"Subscriber 123 has been deleted"},"method":"subscriber_delete"}
// Error: {"success":"false","errormessage":"Subscriber cannot be deleted"}</pre>

<script type="text/javascript">
jQuery(document).ready(function() {
	SyntaxHighlighter.all();
});

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