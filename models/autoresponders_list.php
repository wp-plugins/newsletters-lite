<?php

class wpmlAutorespondersList extends wpMailPlugin {
	
	var $model = "AutorespondersList";
	var $controller = "autoresponderslists";
	var $table;
	
	var $fields = array(
		'rel_id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'autoresponder_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'list_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
		'created'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'					=>	"PRIMARY KEY (`rel_id`)",
	);
	
	var $tv_fields = array(
		'rel_id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'autoresponder_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'list_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'					=>	"PRIMARY KEY (`rel_id`)",				
	);
	
	function wpmlAutorespondersList($data = array()) {
		global $Db, $Autoresponder, $Mailinglist;
		
		$this -> table = $this -> pre . $this -> controller;	
		
		if (!empty($data)) {		
			foreach ($data as $dkey => $dval) {				
				$this -> {$dkey} = $dval;
			}
		}
		
		$Db -> model = $this -> model;
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
			if (empty($autoresponder_id)) { $this -> errors['autoresponder_id'] = __('No autoresponder was specified.', $this -> plugin_name); }
			if (empty($list_id)) { $this -> errors['list_id'] = __('No mailing list was specified.', $this -> plugin_name); }
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		return $this -> errors;
	}
}

?>