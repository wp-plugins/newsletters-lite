<?php

class wpmlAutoresponder extends wpMailPlugin {

	var $model = 'Autoresponder';
	var $controller = 'autoresponders';
	var $table = '';
	
	var $fields = array(
		'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'title'				=>	"VARCHAR(250) NOT NULL DEFAULT ''",
		'history_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'status'			=>	"ENUM('active','inactive') NOT NULL DEFAULT 'active'",
		'delay'				=>	"INT(11) NOT NULL DEFAULT '0'",
		'delayinterval'		=>	"VARCHAR(50) NOT NULL DEFAULT 'days'",
		'applyexisting'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
		'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'title'				=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
		'history_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'status'			=>	array("ENUM('active','inactive')", "NOT NULL DEFAULT 'active'"),
		'delay'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'delayinterval'		=>	array("VARCHAR(50)", "NOT NULL DEFAULT 'days'"),
		'applyexisting'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`)",					   
	);
	
	function wpmlAutoresponder($data = array()) {
		global $wpdb, $AutorespondersList, $Mailinglist, $Db;
	
		$this -> table = $this -> pre . $this -> controller;
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {				
				$this -> {$dkey} = stripslashes_deep($dval);
			}
			
			if (!empty($data -> recursive) && $data -> recursive == true) {
				/* Mailing List Associations */
				$this -> mailinglists = array();
				$Db -> model = $AutorespondersList -> model;
				if ($autoresponderslists = $Db -> find_all(array('autoresponder_id' => $this -> id))) {				
					foreach ($autoresponderslists as $autoresponderslist) {
						$Db -> model = $Mailinglist -> model;
						$this -> lists[] = $autoresponderslist -> list_id;
						$this -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
					}
				}
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	function defaults() {
		global $Html;
		
		$defaults = array(
			'history_id'		=>	1,
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
			if (empty($title)) { $this -> errors['title'] = __('Please fill in a title.', $this -> plugin_name); }
			if (empty($lists) || !is_array($lists)) { $this -> errors['lists'] = __('Please select mailing list(s).', $this -> plugin_name); }
			
			if (empty($newsletter)) {
				$this -> errors['newsletter'] = __('Please choose a newsletter type.', $this -> plugin_name);	
			} else {
				if ($newsletter == "new") {
					if (empty($nnewsletter['subject'])) { $this -> errors['nnewsletter_subject'] = __('Please fill in a subject.', $this -> plugin_name); }	
					if (empty($_POST['content'])) { $this -> errors['nnewsletter_content'] = __('Please fill in content for this newsletter.', $this -> plugin_name); }
				} else {
					if (empty($history_id)) { $this -> errors['history_id'] = __('Please select a history email.', $this -> plugin_name); }
				}
			}
			
			if (empty($delay) && $delay != "0") { $this -> errors['delay'] = __('Please fill in a send delay.', $this -> plugin_name); }
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		return $this -> errors;
	}
	
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-autoresponders', $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . $this -> table, "*", $sub, $sub);
		$paginate -> per_page = $perpage;
		$paginate -> where = $conditions;
		$paginate -> order = $order;
		$queues = $paginate -> start_paging($_GET[$this -> pre . 'page']);
		
		$data = array();
		$data['Pagination'] = $paginate;
		
		if (!empty($queues)) {
			foreach ($queues as $queue) {
				$data[$this -> model][] = $this -> init_class($this -> model, $queue);
			}
		}
		
		return $data;
	}
	
	function select() {
		global $Db;		
		$select = array();		
		$Db -> model = $this -> model;
		
		if ($autoresponders = $Db -> find_all(false, array('id', 'title'), array('title', "ASC"))) {		
			foreach ($autoresponders as $autoresponder) {
				$select[$autoresponder -> id] = $autoresponder -> title;
			}
		}
		
		return $select;
	}
}

?>