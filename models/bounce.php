<?php

class wpmlBounce extends wpMailPlugin {

	var $model = 'Bounce';
	var $controller = 'bounces';
	var $table;
	
	var $fields = array(
		'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'email'				=>	"VARCHAR(250) NOT NULL DEFAULT ''",
		'count'				=>	"INT(11) NOT NULL DEFAULT '0'",
		'history_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`), INDEX(`email`)",
	);
	
	var $tv_fields = array(
		'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'email'				=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
		'count'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'history_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`), INDEX(`email`)",					   
	);
	
	function wpmlBounce($data = array()) {
		global $wpdb, $Db;
		$this -> table = $this -> pre . $this -> controller;
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {				
				$this -> {$dkey} = stripslashes_deep($dval);
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	function defaults() {
		global $Html;
		
		$defaults = array(
			'count'				=>	0,
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
			if (empty($email)) { $this -> errors['email'] = __('No email was specified.', $this -> plugin_name); }
			else {
				
			}
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		return $this -> errors;
	}
	
	function alltotal() {
		global $wpdb;
		$total = 0;
		
		$alltotalquery = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $this -> table . "`";
		
		$query_hash = md5($alltotalquery);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$alltotal = ${'newsletters_query_' . $query_hash};
		} else {
			$alltotal = $wpdb -> get_var($alltotalquery);
			${'newsletters_query_' . $query_hash} = $alltotal;
		}
		
		if (!empty($alltotal)) {
			$total = $alltotal;
		}
		
		return $total;
	}
	
	function save($data = null, $validate = true) {
		global $wpdb;
		$wpdb -> query("ALTER TABLE `" . $wpdb -> prefix . $this -> table . "` DROP INDEX `email`");
	
		if (!empty($data)) {
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$defaults = $this -> defaults();
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if ($validate == true) {
				$this -> validate($data);
			}
			
			if (empty($this -> errors)) {
				$bouncequery = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `email` = '" . $email . "' AND `history_id` = '" . $history_id . "'";
				
				if ($bounce = $wpdb -> get_row($bouncequery)) {
					$query = "UPDATE `" . $wpdb -> prefix . $this -> table . "` "
					. " SET `count` = '" . ((int) $bounce -> count + 1) . "', `modified` = '" . $modified . "' WHERE `id` = '" . $bounce -> id . "' LIMIT 1";
				} else {
					$query = "INSERT INTO `" . $wpdb -> prefix . $this -> table . "` "
					. " (`id`, `email`, `count`, `history_id`, `created`, `modified`) "
					. " VALUES ('', '" . $email . "', '1', '" . $history_id . "', '" . $created . "', '" . $modified . "')";
				}
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		return false;
	}
}

?>