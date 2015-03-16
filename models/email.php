<?php

class wpmlEmail extends wpMailPlugin {

	var $name = 'wpmlEmail';
	var $model = 'Email';
	var $controller = 'emails';
	var $table = '';
	var $data = array();
	var $errors = array();
	
	var $fields = array(
		'id'					=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'eunique'				=>	"VARCHAR(32) NOT NULL DEFAULT ''",
		'subscriber_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'user_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
		'mailinglist_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'mailinglists'			=>	"TEXT NOT NULL",
		'history_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'read'					=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'device'				=>	"VARCHAR(100) NOT NULL DEFAULT ''",
		'status'				=>	"ENUM('sent','unsent') NOT NULL DEFAULT 'unsent'",
		'messageid'				=>	"TEXT NOT NULL",
		'bounced'				=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'created'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'					=>	"PRIMARY KEY (`id`), INDEX(`eunique`), INDEX(`subscriber_id`), INDEX(`user_id`), INDEX(`history_id`), INDEX(`read`), INDEX(`status`)",
	);
	
	var $tv_fields = array(
		'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'eunique'				=>	array("VARCHAR(32)", "NOT NULL DEFAULT ''"),
		'subscriber_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'user_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'mailinglist_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'mailinglists'			=>	array("TEXT", "NOT NULL"),
		'history_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'read'					=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'device'				=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
		'status'				=>	array("ENUM('sent','unsent')", "NOT NULL DEFAULT 'unsent'"),
		'messageid'				=>	array("TEXT", "NOT NULL"),
		'bounced'				=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'					=>	"PRIMARY KEY (`id`), INDEX(`eunique`), INDEX(`subscriber_id`), INDEX(`user_id`), INDEX(`history_id`), INDEX(`read`), INDEX(`status`)",					   
	);
	
	var $indexes = array('eunique', 'subscriber_id', 'user_id', 'history_id', 'read', 'status');

	function wpmlEmail($data = array()) {
		global $Db;
		
		$this -> table = $this -> pre . $this -> controller;	
	
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
				$this -> {$dkey} = stripslashes_deep($dval);
			}
		}
		
		$Db -> model = $this -> model;
		return true;
	}
	
	function defaults() {
		global $Html;
		
		$defaults = array(
			'subscriber_id'		=>	0,
			'mailinglist_id'	=>	0,
			'eunique'			=>	"",
			'history_id'		=>	0,
			'status'			=>	"unsent",
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
			if (empty($subscriber_id) && empty($user_id)) { $this -> errors['subscriber_id'] = __('No subscriber was specified', $this -> plugin_name); }
			//if (empty($mailinglist_id)) { $this -> errors['mailinglist_id'] = __('No mailing list was specified', $this -> plugin_name); }
			if (empty($eunique)) { $this -> errors['eunique'] = __('No unique ID was specified', $this -> plugin_name); }
			if (empty($history_id)) { $this -> errors['history_id'] = __('No history item was specified', $this -> plugin_name); }
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		return $this -> errors;
	}
	
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-history', $perpage = 15, $order = array('modified', "DESC"), $after = null) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . $this -> table, "*", $sub, $sub);
		$paginate -> per_page = $perpage;
		$paginate -> where = $conditions;
		$paginate -> order = $order;
		$paginate -> after = $after;
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
}

?>