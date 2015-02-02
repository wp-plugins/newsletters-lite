<?php

if (!class_exists('wpmlLatestpostssubscription')) {
	class wpmlLatestpostssubscription extends wpmlDbHelper {
		var $model = 'Latestpostssubscription';
		var $controller = 'latestpostssubscriptions';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'history_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'subject'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'number'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'language'				=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'takefrom'				=>	array("VARCHAR(100)", "NOT NULL DEFAULT 'categories'"),
			'posttypes'				=>	array("TEXT", "NOT NULL"),
			'categories'			=>	array("TEXT", "NOT NULL"),
			'groupbycategory'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'exclude'				=>	array("TEXT", "NOT NULL"),
			'order'					=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'orderby'				=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'olderthan'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'lists'					=>	array("TEXT", "NOT NULL"),
			'startdate'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'interval'				=>	array("VARCHAR(50)", "NOT NULL DEFAULT ''"),
			'theme_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`)",
		);
		
		var $indexes = array();
		
		function wpmlLatestpostssubscription($data = null) {
			$this -> table = $this -> pre . $this -> controller;
			
			foreach ($this -> tv_fields as $field => $attributes) {
				if (is_array($attributes)) {
					$this -> fields[$field] = implode(" ", $attributes);
				} else {
					$this -> fields[$field] = $attributes;
				}
			}
			
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {
					$this -> {$dkey} = stripslashes_deep($dval);
				}
				
				if (!wp_get_schedule('newsletters_latestposts', array($this -> id))) {
					$this -> latestposts_scheduling($this -> interval, $this -> startdate, array($this -> id));
				}
			}
			
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
			global $Html;
			$this -> errors = array();
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {							
				if (empty($subject)) { $this -> errors['subject'] = __('Please fill in a subject', $this -> plugin_name); }
				if (empty($takefrom)) { $this -> errors['takefrom'] = __('Specify where posts will be taken from', $this -> plugin_name); }
				if (empty($lists)) { $this -> errors['lists'] = __('Choose at least one list', $this -> plugin_name); }
			} else {
				$this -> errors[] = __('No data was provided', $this -> plugin_name);
			}
			
			return $this -> errors;
		}
	}
}

?>