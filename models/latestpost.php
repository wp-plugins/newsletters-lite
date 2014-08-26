<?php

class wpmlLatestpost extends wpMailPlugin {
	
	var $model = 'Latestpost';
	var $controller = 'latestposts';
	var $table_name = 'wpmllatestposts';
	
	var $fields = array(
		'id'					=> 	"INT(11) NOT NULL AUTO_INCREMENT",
		'post_id'				=>	"INT(11) NOT NULL UNIQUE",
		'created'				=> 	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'					=>	"PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
		'id'					=> 	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'post_id'				=>	array("INT(11)", "NOT NULL UNIQUE"),
		'created'				=> 	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'					=>	"PRIMARY KEY (`id`)",					   
	);
	
	function wpmlLatestpost($data = array()) {
		global $Db;
		
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
				$this -> {$dkey} = $dval;	
			}
		}
		
		$Db -> model = $this -> model;
		return;
	}
	
	function defaults() {
		global $Html;
		
		$defaults = array(
			'created'			=>	$Html -> gen_date(),
			'modified'			=>	$Html -> gen_date(),
		);
		
		return $defaults;
	}
	
	function validate($data = array()) {
		$this -> errors = array();
		
		$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
		$r = wp_parse_args($data, $defaults);
		extract($r, EXTR_SKIP);
		
		if (!empty($data)) {
			if (empty($post_id)) { $this -> errors['post_id'] = __('No post ID was specified', $this -> plugin_name); }
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);	
		}
		
		return $this -> errors;
	}
}

?>