<?php

$validation_rules = array(
	'notempty'				=>	array(
		'title'					=>	__('Not Empty', $this -> plugin_name),
		'regex'					=>	"",
	),
	'alphanumeric'			=>	array(
		'title'					=>	__('Alpha-Numeric', $this -> plugin_name),
		'regex'					=>	"/^[a-zA-Z0-9]*$/",
	),
	'alphabetic'			=>	array(
		'title'					=>	__('Alphabetic', $this -> plugin_name),
		'regex'					=>	"/^[a-zA-Z]*$/",
	),
	'numeric'				=>	array(
		'title'					=>	__('Numeric', $this -> plugin_name),
		'regex'					=>	"/^[0-9]*$/",
	),
	'email'					=>	array(
		'title'					=>	__('Email', $this -> plugin_name),
		'regex'					=>	"/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/",
	),
	'ipaddress'				=>	array(
		'title'					=>	__('IP Address', $this -> plugin_name),
		'regex'					=>	"/^((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))*$/",
	),
	'urls'					=>	array(
		'title'					=>	__('URLs', $this -> plugin_name),
		'regex'					=>	"/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/",
	),
);

$validation_rules = apply_filters('newsletters_validation_rules', $validation_rules);
$validation_rules['custom'] = array('title' => __('CUSTOM REGEX', $this -> plugin_name));

?>