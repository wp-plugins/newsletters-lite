<?php

class wpMailAPI extends wpMail {

	var $api_methods = array(
		'subscriber_add',
		'subscriber_delete',
	);
	
	var $api_method;
	
	function wpMailAPI() {
		return;
	}
	
	function api_init() {		
		global $wpdb, $Db, $Subscriber;
		$api_key = $this -> get_option('api_key');
		$data = json_decode(file_get_contents('php://input'), false);
		
		if (!empty($data)) {
			if (!empty($data -> api_key) && $data -> api_key == $api_key) {
				if (!empty($data -> api_method) && in_array($data -> api_method, $this -> api_methods)) {
					$this -> api_method = $data -> api_method;
				
					switch ($data -> api_method) {
						case 'subscriber_add'			:
							$subscriber_data = $data -> api_data;						
							if ($subscriber_id = $Subscriber -> optin((array) $subscriber_data, false)) {
								$result = array('id' => $subscriber_id);
								$this -> api_success($result);
							} else {
								$error = (object) $Subscriber -> errors;
								$this -> api_error($error);	
							}
							break;
						case 'subscriber_delete'		:
							$Db -> model = $Subscriber -> model;
							if ($Db -> delete($data -> api_data -> id)) {
								$result = sprintf(__('Subscriber %s has been deleted', $this -> plugin_name), $data -> api_data -> id);
								$this -> api_success($result);
							} else {
								$error = __('Subscriber could not be deleted', $this -> plugin_name);
								$this -> api_error($error);
							}
							break;
					}
				} else {
					$error = sprintf(__('%s is not a valid API method', $this -> plugin_name), $data -> api_method);
					$this -> api_error($error);
				}
			} else {
				$error = __('API key is invalid, please check', $this -> plugin_name);
				$this -> api_error($error);
			}
		} else {
			$error = __('No data was posted to the API, check the code', $this -> plugin_name);
			$this -> api_error($error);
		}
		
		exit();
		die();
	}
	
	function api_output($data = null) {
		header("Content-Type: application/json");
		$data['method'] = $this -> api_method;
		echo json_encode($data);
	}
	
	function api_success($result = null) {
		$data = array(
			'success'			=>	true,
			'result'			=>	$result,
		);
		
		$this -> api_output($data);
	}
	
	function api_error($error = null) {
		$data = array(
			'success'			=>	false,
			'errormessage'		=>	$error,
		);
		
		$this -> api_output($data);
	}
	
	function api_newkey() {
		
		$key = strtoupper(md5(time()));
		$this -> update_option('api_key', $key);
		echo $key;
		
		exit();
		die();
	}
}

$wpMailAPI = new wpMailAPI();
add_action('wp_ajax_newsletters_api', array($wpMailAPI, 'api_init'));
add_action('wp_ajax_nopriv_newsletters_api', array($wpMailAPI, 'api_init'));

?>