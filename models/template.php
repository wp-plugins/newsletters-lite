<?php

class wpmlTemplate extends wpMailPlugin {

	var $name = 'wpmlTemplate';
	var $model = 'Template';
	var $controller = 'templates';
	
	var $id = '';
	var $title = '';
	var $content = '';
	var $sent = 0;
	var $created = '0000-00-00 00:00:00';
	var $modified = '0000-00-00 00:00:00';

	var $table_name = 'wpmltemplates';
	
	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'title'			=>	"VARCHAR(150) NOT NULL DEFAULT ''",
		'content'		=>	"LONGTEXT NOT NULL",
		'theme_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'sent'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'title'			=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
		'content'		=>	array("LONGTEXT", "NOT NULL"),
		'theme_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'sent'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'created'		=>	array("DATETIME NOT", "NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME NOT", "NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`)"					   
	);
	
	function wpmlTemplate($data = array()) {
		global $Db;
	
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = $val;
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	/**
	 * Incrments the 'sent' field of a specific template by ID.
	 * @param INT. The ID of the template
	 * @return BOOLEAN.
	 *
	 */
	function inc_sent($template_id = null) {
		global $wpdb;
	
		if (!empty($template_id)) {
			$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `sent` = `sent` + 1 WHERE `id` = '" . $template_id . "' LIMIT 1";
		
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function select() {
		if ($templates = $this -> get_all()) {
			$select = array();
			
			foreach ($templates as $template) {
				$select[$template -> id] = $template -> title;
			}
			
			return apply_filters('newsletters_snippets_select', $select);
		}
		
		return false;
	}
	
	function get($template_id = null, $assign = true) {
		global $wpdb;
		
		if (!empty($template_id)) {
			if ($template = $wpdb -> get_row("SELECT * FROM `" . $wpdb -> prefix . $this -> table_name . "` WHERE `id` = '" . $template_id . "'")) {
				$template = $this -> init_class($this -> model, $template);
				
				if ($assign == true) {
					$this -> data[$this -> model] = $template;
				}
				
				return $template;
			}
		}
		
		return false;
	}
	
	function get_all() {
		global $wpdb;
		
		if ($templates = $wpdb -> get_results("SELECT * FROM `" . $wpdb -> prefix . $this -> table_name . "` ORDER BY `title` ASC")) {
			if (!empty($templates)) {
				$data = array();
			
				foreach ($templates as $template) {
					$data[] = $this -> init_class('wpmlTemplate', $template);
				}
				
				return $data;
			}
		}
		
		return false;
	}
	
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-templates', $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		$this -> sections = (object) $this -> sections;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . "" . $this -> table_name, "*", $this -> controller, $this -> controller);
		$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
		$paginate -> where = (empty($conditions)) ? false : $conditions;		
		$paginate -> per_page = $perpage;
		$paginate -> order = $order;
		$paginate -> url_page = $this -> sections -> templates;
		$templates = $paginate -> start_paging($_GET[$this -> pre . 'page']);
		
		$data = array();
		$data['Pagination'] = $paginate;
		
		if (!empty($templates)) {
			foreach ($templates as $template) {
				$data['Template'][] = $this -> init_class('wpmlTemplate', $template);
			}
		}
		
		return $data;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb;
		
		$defaults = array('created' => $this -> gen_date(), 'content' => $_POST['content'], 'modified' => $this -> gen_date());
		$r = wp_parse_args($data[$this -> model], $defaults);
		$this -> data[$this -> model] = $this -> array_to_object($r);
		extract($r, EXTR_SKIP);
		
		if ($validate == true) {
			if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
			if (empty($content)) { $this -> errors['content'] = __('Please fill in the content', $this -> plugin_name); }
		}
		
		if (empty($this -> errors)) {
			if (!empty($id)) {
				$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET";
				$c = 1;
				unset($this -> table_fields['key']);
				
				foreach (array_keys($this -> table_fields) as $field) {
					$query .= "`" . $field . "` = '" . ${$field} . "'";
					
					if ($c < count($this -> table_fields)) {
						$query .= ", ";
					}
					
					$c++;
				}
				
				$query .= " WHERE `id` = '" . $id . "';";
			} else {
				$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (";
				$query2 = "";
				unset($this -> table_fields['key']);
				unset($this -> table_fields['id']);
				$c = 1;
				
				foreach (array_keys($this -> table_fields) as $field) {
					$query1 .= "`" . $field . "`";
					$query2 .= "'" . ${$field} . "'";
					
					if ($c < count($this -> table_fields)) {
						$query1 .= ", ";
						$query2 .= ", ";
					}
				
					$c++;
				}
				
				$query1 .= ") VALUES (";
				$query = $query1 . $query2 . ");";
			}
			
			if ($wpdb -> query($query)) {
				$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
				return true;
			}
		}
		
		return false;
	}
	
	function delete($template_id = null) {
		global $wpdb;
		
		if (!empty($template_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $template_id . "' LIMIT 1")) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete_array($data = array()) {
		global $wpdb;
		
		if (!empty($data)) {
			foreach ($data as $template_id) {
				$this -> delete($template_id);
			}
			
			return true;
		}
		
		return false;
	}
}

?>