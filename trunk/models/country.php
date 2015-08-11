<?php

if (!class_exists('wpmlCountry')) {
class wpmlCountry extends wpMailPlugin {

	var $model = 'wpmlCountry';
	var $controller = 'countries';
	var $table = '';
	
	var $fields = array(
		'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'value'				=>	"VARCHAR(150) NOT NULL DEFAULT ''",
		'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
		'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'value'				=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
		'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`)",					   
	);
	
	var $indexes = array('value');
	
	function wpmlCountry($data = array()) {
		global $wpdb, $Db;
	
		$this -> table = $this -> pre . $this -> controller;
		
		if (is_admin()) {		
			$query = "SELECT `id` FROM `" . $wpdb -> prefix . "" . $this -> table . "` LIMIT 1";
			
			if ($this -> get_option('countriesinserted') == "N" || !$wpdb -> get_var($query)) {
				global $wpmlsql;
				
				$this -> tables[$this -> pre . $this -> controller] = $this -> fields;
				$this -> check_table($this -> pre . $this -> controller);
				
				include($this -> plugin_base() . DS . 'vendors' . DS . 'sql.countries.php');
				$wpdb -> query($countriesquery);
				
				$this -> update_option('countriesinserted', "Y");
			}
		}
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
				$this -> {$dkey} = $dval;
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	function select() {
		global $Db;
		
		$select = array();
		
		$Db -> model = $this -> model;
		if ($countries = $Db -> find_all(false, false, array('value', "ASC"))) {		
			foreach ($countries as $country) {
				$select[$country -> id] = $country -> value;
			}
		}
		
		return $select;
	}
}
}

?>