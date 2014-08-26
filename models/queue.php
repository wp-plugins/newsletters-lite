<?php

class wpmlQueue extends wpMailPlugin {

	var $id;
	var $subscriber_id;	
	var $subject;	
	var $message;	
	var $attachment;	
	var $attachmentfile;
	var $created;
	var $error = array();	
	
	var $model = 'Queue';
	var $controller = 'queue';
	var $table_name = 'wpmlqueue';
	
	var $table_fields = array(
		'id' 				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'post_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'user_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'subscriber_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'mailinglist_id'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'mailinglists'		=>	"TEXT NOT NULL",
		'history_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'theme_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'subject' 			=>	"VARCHAR(250) NOT NULL DEFAULT ''",
		'slug'				=>	"VARCHAR(250) NOT NULL DEFAULT ''",
		'message' 			=>	"LONGTEXT NOT NULL",
		'attachments'		=>	"TEXT NOT NULL",
		'error'				=>	"TEXT NOT NULL",
		'senddate'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'created' 			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
		'id' 				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'post_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'user_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'subscriber_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'mailinglist_id'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'mailinglists'		=>	array("TEXT", "NOT NULL"),
		'history_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'theme_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'subject' 			=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
		'slug'				=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
		'message' 			=>	array("LONGTEXT", "NOT NULL"),
		'attachments'		=>	array("TEXT", "NOT NULL"),
		'error'				=>	array("TEXT", "NOT NULL"),
		'senddate'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'created' 			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`)",					   
	);

	function wpmlQueue($data = array()) {
		global $Subscriber, $Db, $HistoriesAttachment;
		$this -> table = $this -> pre . $this -> controller;
		
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = stripslashes_deep($val);
				
				switch ($key) {
					case 'subscriber'		:
						$subscriber = $Subscriber -> get($val);
						$this -> {$key} = $subscriber;
						break;
				}
			}
			
			$this -> attachments = false;
			$Db -> model = $HistoriesAttachment -> model;
			if ($attachments = $Db -> find_all(array('history_id' => $this -> history_id))) {
				foreach ($attachments as $attachment) {
					$this -> attachments[] = array(
						'id'				=>	$attachment -> id,
						'title'				=>	$attachment -> title,
						'filename'			=>	$attachment -> filename,
					);	
				}
			}
		}
	}
	
	function count($conditions = array()) {
		global $wpdb;
		
		$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table_name . "`";
		
		if (!empty($conditions)) {
			$c = 1;
			$query .= " WHERE";
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
				
				if ($c < count($conditions)) {
					$query .= " AND";
				}
			}
		}
		
		if ($count = $wpdb -> get_var($query)) {
			return $count;
		}
		
		return 0;
	}
	
	function get_all_limited($limit = 100) {
		global $wpdb;
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . "mailqueue` LIMIT " . $limit . "";
		
		if ($emails = $wpdb -> get_results($query)) {
			if (!empty($emails)) {
				$data = array();
			
				foreach ($emails as $email) {
					$data[] = $this -> init_class('wpmlQueue', $email);
				}
				
				return $data;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-queue', $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . $this -> table_name, "*", $sub, $sub);
		$paginate -> per_page = $perpage;
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
	
	function find_all($conditions = array(), $fields = '', $order = array('created', "DESC"), $limit = false) {
		global $wpdb;
				
		$fields = (empty($fields)) ? '*' : $fields;
		$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $this -> table_name . "`";
		
		if (!empty($conditions)) {
			$c = 1;
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
				
				if ($c < count($conditions)) {
					$query .= " AND";
				}
				
				$c++;
			}
		}
		
		if (!empty($order)) {
			list($field, $direction) = $order;
			$query .= " ORDER BY `" . $field . "` " . $direction . "";
		}
		
		if (!empty($limit)) { $query .= " LIMIT " . $limit . ""; }
		
		if ($emails = $wpdb -> get_results($query)) {
			if (!empty($emails)) {
				$data = array();
				
				foreach ($emails as $email) {
					$data[] = $this -> init_class($this -> model, $email);
				}
				
				return $data;
			}
		}
		
		return false;
	}
	
	function save($subscriber = null, $user = null, $subject = null, $message = null, $attachments = array(), $post_id = 0, $history_id = 0, $return_query = false, $theme_id = 0, $senddate = null) {
		global $wpdb, $Html;
		$this -> errors = array();
		
		if (empty($senddate) || strtotime($senddate) < time()) {
			$this -> set_timezone();
			$senddate = date_i18n("Y-m-d H:i:s", time());
		}
		
		if (empty($subscriber) && empty($user)) { $this -> errors[] = __('No subscriber specified', $this -> plugin_name); }		
		if (empty($subject)) { $this -> errors[] = __('No subject specified', $this -> plugin_name); }
		else { $slug = $Html -> sanitize($subject); }		
		if (empty($message)) { $this -> errors[] = __('No message specified', $this -> plugin_name); }
		
		$this -> errors = apply_filters($this -> pre . '_queue_validation', $this -> errors, $subscriber, $subject, $message, $attachments, $post_id, $history_id, $return_query, $theme_id, $senddate);
		
		if (empty($this -> errors)) {
			$exists = false;
			
			if (!empty($subscriber)) {
				$user_id = 0;
				$subscriber_id = $subscriber -> id;
				$existsquery = "SELECT `id` FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `subscriber_id` = '" . $subscriber -> id . "' AND `slug` = '" . $slug . "' LIMIT 1";
			} elseif (!empty($user)) {
				$subscriber_id = 0;
				$user_id = $user -> ID;
				$existsquery = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `user_id` = '" . $user -> ID . "' AND `slug` = '" . $slug . "' LIMIT 1";
			}
				
			if ($wpdb -> get_var($existsquery)) {
				$exists = true;
			}
		
			if ($exists == false) {
				$subject = addslashes($subject);
				$message = addslashes($message);
				$nowdate = $Html -> gen_date();
				
				$query = "INSERT INTO `" . $wpdb -> prefix . $this -> table_name . "` (
					`post_id`, 
					`history_id`, 
					`theme_id`,
					`user_id`,
					`subscriber_id`, 
					`mailinglist_id`, 
					`mailinglists`,
					`subject`, 
					`slug`, 
					`message`, 
					`attachments`,
					`senddate`,
					`created`, 
					`modified`) VALUES (
					'" . (empty($post_id) ? '0' : $post_id) . "', 
					'" . $history_id . "', 
					'" . $theme_id . "', 
					'" . $user_id . "',
					'" . $subscriber_id . "', 
					'" . $subscriber -> mailinglist_id . "', 
					'" . maybe_serialize($subscriber -> mailinglists) . "',
					'" . $subject . "', 
					'" . $slug . "', 
					'" . $message . "', 
					'" . (!empty($attachments) ? maybe_serialize($attachments) : '') . "',
					'" . $senddate . "', 
					'" . $nowdate . "', 
					'" . $nowdate . "');";
			
				if (empty($return_query) || !$return_query) {
					if ($wpdb -> query($query)) {
						$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
						return true;
					} else {
						$this -> errors[] = __('Query failed, email could not be queued.', $this -> plugin_name);
					}
				} else {
					return $query;	
				}
			} else {
				$this -> errors[] = __('Email with this subscriber and history email is already in the queue.', $this -> plugin_name);
			}
		}
		
		return false;
	}
	
	function delete_all($conditions = array()) {
		global $wpdb;
		
		if (!empty($conditions)) {
			$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE";
			$c = 1;
			
			foreach ($conditions as $ckey => $cval) {
				if ($cval != "") {
					$query .= " `" . $ckey . "` = '" . $cval . "'";
					
					if ($c < count($conditions)) {
						$query .= " AND";
					}
				}
				
				$c++;
			}
			
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete($email_id = null) {
		global $wpdb;
		
		if (!empty($email_id)) {
			$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $email_id . "' LIMIT 1";
		
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete_array($data = array()) {
		global $wpdb;
		
		if (!empty($data)) {
			foreach ($data as $email) {
				$wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $email . "' LIMIT 1");
			}
			
			return true;
		}
		
		return false;
	}
	
	function truncate() {
		//global Wordpress variables
		global $wpdb;
		
		//execute a TRUNCATE query
		if ($wpdb -> query("TRUNCATE TABLE `" . $wpdb -> prefix . "" . $this -> table_name . "`")) {
			return true;
		}
		
		return false;
	}
}

?>