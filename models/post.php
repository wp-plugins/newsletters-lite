<?php

////////////////////////////////////////////////////
//
// Tribulant Software
// Wordpress Mailing List Plugin
//
// Wordpress plugin for managing mailing lists,
// subscribers, templates, history, paid subscribers,
// and much more.
//
// Copyright (C) 2007 - 2008 Tribulant Software
//
// License: Commercial (see : http://tribulant.com/policies/distribution/)
// File : /models/post.php
// Modified : 2008-01-11
//
////////////////////////////////////////////////////

class wpmlPost extends wpMailPlugin {

	var $model = 'Post';
	var $controller = "posts";
	var $table_name = 'wpmlposts';
	
	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'post_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'sent'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'post_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'sent'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`)"					   
	);
	
	function wpmlPost($data = array()) {
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = $val;
			}
		}
		
		return true;
	}
	
	function get_by_post_id($postid = null) {
		global $wpdb;
	
		if (!empty($postid)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `post_id` = '" . $postid . "' LIMIT 1";
			$objectcache = $this -> get_option('objectcache');
			$query_hash = md5($query);
			if (!empty($objectcache) && $oc_post = wp_cache_get($query_hash, 'newsletters')) {
				$post = $oc_post;
			} else {
				$post = $wpdb -> get_row($query);
				if (!empty($objectcache)) {
					wp_cache_set($query_hash, $post, 'newsletters', 0);
				}
			}
		
			if (!empty($post)) {
				return true;
			}
		}
		
		return false;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb;
		
		if (!empty($data)) {
			if ($validate == true) {
				if (empty($data['post_id'])) { $this -> errors[] = __('No post was specified', $this -> plugin_name); }
				if (empty($data['sent'])) { $this -> errors[] = __('No sent status was specified', $this -> plugin_name); }
			}
		
			if (empty($this -> errors)) {
				$nowdate = $this -> gen_date();
			
				if (!empty($data['id'])) {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `post_id` = '" . $data['post_id'] . "', `sent` = '" . $data['sent'] . "', `modified` = '" . $nowdate . "'";
				} else {
					$query = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`post_id`, `sent`, `created`, `modified`) VALUES ('" . $data['post_id'] . "', '" . $data['sent'] . "', '" . $nowdate . "', '" . $nowdate . "');";
				}
				
				if ($wpdb -> query($query)) {
					$this -> insertid = (empty($data['id'])) ? $wpdb -> insert_id : $data['id'];
					return true;
				}
			}
		}
		
		return false;
	}
}

?>