<?php

if (!class_exists('wpmlLink')) {
	class wpmlLink extends wpmlDbHelper {
	
		var $model = 'Link';
		var $controller = 'links';
		var $errors = array();
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'link'					=>	array("TEXT", "NOT NULL"),
			'hash'					=>	array("TEXT", "NOT NULL"),
			'used'					=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`)"
		);
		
		function wpmlLink($data = null) {
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
				if (empty($link)) { $this -> errors['link'] = __('Please specify a link', $this -> plugin_name); }
			} else {
				$this -> errors[] = __('No data was provided', $this -> plugin_name);
			}
			
			return $this -> errors;
		}
	}
}

?>