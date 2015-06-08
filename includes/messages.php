<?php
	
$messages = array();

$messages[1] = __('Preview has been sent to %s', $this -> plugin_name);
$messages[2] = __('Preview cannot be sent to %s, %s.', $this -> plugin_name);
$messages[3] = __('%s is an invalid email address', $this -> plugin_name);
$messages[4] = __('Draft has been successfully saved. It has been saved to your email history.', $this -> plugin_name);
$messages[5] = __('Draft could not be saved. Please fill in all required fields', $this -> plugin_name);
$messages[6] = __('Configuration settings successfully updated', $this -> plugin_name);

$messages = apply_filters('newsletters_messageserrors', $messages);	
	
?>