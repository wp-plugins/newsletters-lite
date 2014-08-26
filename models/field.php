<?php

class wpmlField extends wpMailPlugin {

	var $id;
	var $title;
	var $slug;
	var $type = 'text';
	var $options;
	var $required = 'Y';
	var $default;
	var $created = '0000-00-00 00:00:00';
	var $modified = '0000-00-00 00:00:00';

	var $insertid;
	var $name = 'wpmlfield';
	var $model = 'Field';
	var $controller = 'fields';	
	var $error = array();
	var $data = array();

	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'title'			=>	"VARCHAR(100) NOT NULL DEFAULT ''",
		'caption'		=>	"TEXT NOT NULL",
		'watermark'		=>	"TEXT NOT NULL",
		'slug'			=>	"VARCHAR(100) NOT NULL DEFAULT ''",
		'type'			=>	"VARCHAR(255) NOT NULL DEFAULT 'text'",
		'fieldoptions'	=>	"TEXT NOT NULL",
		'filetypes'		=>	"TEXT NOT NULL",
		'filesizelimit'	=>	"TEXT NOT NULL",
		'required'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'Y'",
		'errormessage'	=>	"TEXT NOT NULL",
		'invalidmessage'	=> "TEXT NOT NULL",
		'display'		=>	"ENUM('always','specific') NOT NULL DEFAULT 'specific'",
		'validation'	=>	"TEXT NOT NULL",
		'regex'			=>	"TEXT NOT NULL",
		'order'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'title'			=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
		'caption'		=>	array("TEXT", "NOT NULL"),
		'watermark'		=>	array("TEXT", "NOT NULL"),
		'slug'			=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
		'type'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT 'text'"),
		'fieldoptions'	=>	array("TEXT", "NOT NULL"),
		'filetypes'		=>	array("TEXT", "NOT NULL"),
		'filesizelimit'	=>	array("TEXT", "NOT NULL"),
		'required'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'Y'"),
		'errormessage'	=>	array("TEXT", "NOT NULL"),
		'invalidmessage'	=>	array("TEXT", "NOT NULL"),
		'display'		=>	array("ENUM('always','specific')", "NOT NULL DEFAULT 'specific'"),
		'validation'	=>	array("TEXT", "NOT NULL"),
		'regex'			=>	array("TEXT", "NOT NULL"),
		'order'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`)"
	);

	function wpmlField($data = array()) {
		global $wpdb, $Db, $FieldsList;
		
		$this -> table = $this -> pre . $this -> controller;	
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = stripslashes_deep($val);
			}
		}
		
		if (is_admin()) {
			if (!$emailfield = $this -> email_field()) {
				$this -> init_fieldtypes();
			
				$emailfielddata = array(
					$this -> model =>	array(
						'title' 		=> 	__('Email Address', $this -> plugin_name),
						'slug'			=> 	"email",
						'type'			=>	"text",
						'required'		=>	"Y",
						'errormessage'	=>	__('Please fill in your email address', $this -> plugin_name),
						'display'		=>	"always",
						'order'			=>	"0",
					),
				);
				
				$this -> save($emailfielddata);
				$emailfield_id = $this -> insertid;
			} else {
				$emailfield_id = $emailfield -> id;	
			}
			
			$efieldslistquery = "SELECT * FROM " . $wpdb -> prefix . $FieldsList -> table . " WHERE `special` = 'email'";
			
			$query_hash = md5($efieldslistquery);
			$oc_efieldslist = wp_cache_get($query_hash, 'newsletters');
			
			if (empty($oc_efieldslist) && !$efieldslist = $wpdb -> get_row($efieldslistquery)) {
				$efieldslistdata = array(
					'field_id'				=>	$emailfield_id,
					'list_id'				=>	"0",
					'special'				=>	"email",
				);	
				
				$FieldsList -> save($efieldslistdata);
			}
			
			if (!$listfield = $this -> list_field()) {
				$this -> init_fieldtypes();
			
				$listfielddata = array(
					$this -> model 		=>	array(
						'title'				=>	__('Mailing List', $this -> plugin_name),
						'slug'				=>	"list",
						'type'				=>	"special",
						'required'			=>	"Y",
						'errormessage'		=>	__('Please select a list', $this -> plugin_name),
						'display'			=>	"always",
						'order'				=>	"1",
					)
				);
				
				$this -> save($listfielddata);
				$listfield_id = $this -> insertid;
			} else {
				$listfield_id = $listfield -> id;
			}
			
			$lfieldslistquery = "SELECT * FROM " . $wpdb -> prefix . $FieldsList -> table . " WHERE `special` = 'list'";
			$query_hash = md5($lfieldslistquery);
			$oc_lfieldslist = wp_cache_get($query_hash, 'newsletters');
			
			if (empty($oc_efieldslist) && !$lfieldslist = $wpdb -> get_row($lfieldslistquery)) {
				$lfieldslistdata = array(
					'field_id'				=>	$listfield_id,
					'list_id'				=>	"0",
					'special'				=>	"list",
				);	
				
				$FieldsList -> save($lfieldslistdata);
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	function email_field() {
		global $wpdb;
		
		$emailfieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE slug = 'email'";
		
		$query_hash = md5($emailfieldquery);
		if ($emailfield = wp_cache_get($query_hash, 'newsletters')) {
			return $emailfield;
		}
		
		if ($emailfield = $wpdb -> get_row($emailfieldquery)) {
			$emailfield -> error = $emailfield -> errormessage;
			wp_cache_set($query_hash, $emailfield, 'newsletters', 0);
			return $emailfield;
		}
		
		return false;
	}
	
	function email_field_id() {
		if ($emailfield = $this -> email_field()) {
			return $emailfield -> id;
		}
		
		return false;
	}
	
	function list_field() {
		global $wpdb;
		$listfieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE slug = 'list'";
		
		$query_hash = md5($listfieldquery);
		if ($listfield = wp_cache_get($query_hash, 'newsletters')) {
			return $listfield;
		}
		
		if ($listfield = $wpdb -> get_row($listfieldquery)) {
			$listfield -> error = $listfield -> errormessage;
			wp_cache_set($query_hash, $listfield, 'newsletters', 0);
			return $listfield;
		}
		
		return false;
	}
	
	function list_field_id() {
		if ($listfield = $this -> list_field()) {
			return $listfield -> id;
		}
		
		return false;
	}
	
	function find($conditions = array()) {
		global $wpdb;
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . "`";
		
		if (!empty($conditions)) {
			$query .= " WHERE";
			$c = 1;
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
				
				if ($c < count($conditions)) {
					$query .= " AND";
				}
			
				$c++;
			}
		}
		
		$query .= " LIMIT 1";
		
		$query_hash = md5($query);
		if ($data = wp_cache_get($query_hash, 'newsletters')) {
			return $data;
		}
		
		if ($field = $wpdb -> get_row($query)) {
			if (!empty($field)) {
				$data = $this -> init_class('wpmlField', $field);
				wp_cache_set($query_hash, $data, 'newsletters', 0);
				return $data;
			}
		}
		
		return false;
	}
	
	function select($conditions = false) {
		global $Db, $wpdb;		
		$select = array();
		
		$Db -> model = $this -> model;
		if ($fields = $Db -> find_all($conditions, false, array('order', "ASC"))) {
			if (!empty($fields)) {
				foreach ($fields as $field) {
					if ($field -> slug != "email" && $field -> slug != "list") {
						$select[$field -> id] = $field -> title;
					}
				}
			}
		}
		
		return $select;
	}
	
	function save_field($fieldname = null, $value = null, $field_id = null) {
		global $wpdb;
	
		if (!empty($fieldname)) {
			if ($value != "") {
				if (!empty($field_id)) {					
					if ($field = $this -> get($field_id)) {
						$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `" . $fieldname . "` = '" . $value . "' WHERE `id` = '" . $field_id . "' LIMIT 1";
						
						if ($wpdb -> query($query)) {
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	function titleslug_exists($title = null) {
		global $Db, $Html;
		$Db -> model = $this -> model;
		
		if (!empty($title)) {
			$slug = $Html -> sanitize($title, "_");
			
			if ($Db -> find(array('slug' => $slug), array('id'), false, false, false)) {
				return true;
			}
		}
	
		return false;	
	}
	
	function slug_exists($slug) {
		global $Db, $Html;
		$Db -> model = $this -> model;
		
		if (!empty($slug)) {			
			if ($Db -> find(array('slug' => $slug), array('id'), false, false, false)) {
				return true;
			}
		}
	
		return false;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb, $Db, $Html, $Subscriber, $FieldsList;
		
		$defaults = array(
			'fieldoptions'			=>	false,
			'order'					=>	0,
			'created'				=>	$Html -> gen_date(),
			'modified'				=>	$Html -> gen_date(),
		);
		
		$this -> data[$this -> model] = (object) $data[$this -> model];
		
		if ($this -> is_plugin_active('qtranslate')) {
			global $q_config;
		
			if (function_exists('qtrans_join')) {
				$this -> data[$this -> model] -> title = qtrans_join($this -> data[$this -> model] -> title);
				$this -> data[$this -> model] -> caption = qtrans_join($this -> data[$this -> model] -> caption);
				$this -> data[$this -> model] -> watermark = qtrans_join($this -> data[$this -> model] -> watermark);
				$this -> data[$this -> model] -> errormessage = qtrans_join($this -> data[$this -> model] -> errormessage);
				
				$languages = array();
				$fieldoptions = array();
				$newfieldoptions = array();
				
				if (!empty($this -> data[$this -> model] -> fieldoptions)) {
					foreach ($this -> data[$this -> model] -> fieldoptions as $fokey => $fo) {
						$languages[] = $fokey;
						$fieldoptions[] = explode("\r\n", $fo);
					}
					
					for ($j = 0; $j < count($fieldoptions); $j++) {
						for ($i = 0; $i < count($fieldoptions[$j]); $i++) {
							$newfieldoptions[$i][$languages[$j]] = $fieldoptions[$j][$i];
						}
					}
					
					foreach ($newfieldoptions as $newfieldoption_key => $newfieldoption) {
						$newfieldoptions[$newfieldoption_key] = qtrans_join($newfieldoption);
					}
					
					$newfieldoptions = @implode("\r\n", $newfieldoptions);
					$this -> data[$this -> model] -> fieldoptions = $newfieldoptions;
					$fieldoptions = $newfieldoptions;
				}
			}
		}
		
		$data = $this -> data;
		$r = wp_parse_args($data[$this -> model], $defaults);
		extract($r, EXTR_SKIP);
	
		if (!empty($data)) {			
			if ($validate == true) {
				if (!empty($id)) {
					$Db -> model = $this -> model;
					$oldfield = $Db -> find(array('id' => $id), false, false, false);	
					if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
					elseif ($oldfield -> slug != "email") {
						if ($Html -> sanitize($title, '_') == "email") { $this -> errors['title'] = __('You cannot create an email custom field.', $this -> plugin_name); }
					}
				} else {
					if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
					elseif ($Html -> sanitize($title, '_') == "email") { $this -> errors['title'] = __('You cannot create an email custom field.', $this -> plugin_name); }
					elseif (strlen($Html -> sanitize($title, '_')) > 64) { $this -> errors['title'] = __('This title is too long, please keep below 64 characters.', $this -> plugin_name); }
				}
				
				if (empty($slug)) { $this -> errors['slug'] = __('Please fill in a slug/nicename for this custom field.', $this -> plugin_name); }
				elseif (empty($id) && empty($oldfield) && $this -> slug_exists($slug)) { $this -> errors['slug'] = __('A custom field with this slug already exists, please choose a different one.', $this -> plugin_name); }
				
				if (empty($required)) { $this -> errors['required'] = __('Please choose a required status', $this -> plugin_name); }
				else {
					if ($required == "Y") {
						if (empty($errormessage)) { $this -> errors['errormessage'] = __('Please fill in an error message', $this -> plugin_name); }
					}
				}
				
				if (empty($display)) { $this -> errors['display'] = __('Please choose the display for this field.', $this -> plugin_name); }
				
				if (empty($type)) { $this -> errors['type'] = __('Please choose a field type', $this -> plugin_name); }
				else {				
					if ($type == "select" || $type == "radio" || $type == "checkbox") {
						if (empty($fieldoptions)) {
							$this -> errors['fieldoptions'] = __('Please fill in some options', $this -> plugin_name);
						} else {
							$fieldoptions = explode("\r\n", $fieldoptions);
							$newoptions = array();
							
							if (!empty($fieldoptions)) {
								$n = 1;
												
								foreach ($fieldoptions as $option) {
									$option = trim($option);
									
									if (!empty($option)) {
										$newoptions[$n] = $option;
										$n++;
									}
								}
							}
							
							if (!empty($newoptions)) {
								$fieldoptions = serialize($newoptions);
							} else {
								$fieldoptions = '';
								$this -> errors['fieldoptions'] = __('Please fill in some options', $this -> plugin_name);
							}
							
							$this -> data[$this -> model] -> fieldoptions = $fieldoptions;
						}
					} elseif ($type == "file") {
						$filesizelimit = false;
						$filetypes = false;
					
						/* Allowed file types/extensions */
						if (!empty($fileext)) {
							$filetypes = $fileext;
						}
						
						/* File size limit */
						if (!empty($sizelimit) && !empty($sizetype)) {
							$filesizelimit = $sizelimit . $sizetype;
						}
					}
				}	
			}
			
			if (empty($this -> errors)) {
				$created = $modified = $this -> gen_date();
				
				if (empty($slug)) {
					$slug = $Html -> sanitize($title, '_');
				}
			
				if (!empty($id)) {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `title` = '" . $title . "', `slug` = '" . $slug . "', `display` = '" . $display . "', `required` = '" . $required . "', `errormessage` = '" . $errormessage . "', `validation` = '" . $validation . "', `regex` = '" . $regex . "', `type` = '" . $type . "', `filetypes` = '" . $filetypes . "', `filesizelimit` = '" . $filesizelimit . "', `fieldoptions` = '" . $fieldoptions . "', `modified` = '" . $modified . "', `caption` = '" . $caption . "', `watermark` = '" . $watermark . "' WHERE `id` = '" . $id . "' LIMIT 1;";
					$field_old = $this -> get($id);
				} else {
					$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table . "` (";
					$query2 = "";
					$c = 1;
					
					$oldkeyattr = $this -> table_fields['key'];
					unset($this -> table_fields['key']);
					$oldidattr = $this -> table_fields['id'];
					unset($this -> table_fields['id']);
					
					foreach (array_keys($this -> table_fields) as $field) {
						$query1 .= "`" . $field . "`";
						$query2 .= "'" . ${$field} . "'";
						
						if ($c < count($this -> table_fields)) {
							$query1 .= ", ";
							$query2 .= ", ";
						}
						
						$c++;
					}
					
					$this -> table_fields['id'] = $oldidattr;
					$this -> table_fields['key'] = $oldkeyattr;
					
					$query1 .= ") VALUES (";
					$query = $query1 . $query2 . ");";
				}

				if ($wpdb -> query($query)) {
					$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;

					if (!empty($id)) {
						$FieldsList -> delete_all(array('field_id' => $this -> insertid));
						$this -> change_field($Subscriber -> table, $field_old -> slug, $slug);
					} else {
						$this -> insert_id = $data['id'] = $wpdb -> insert_id;						
						$this -> add_field($Subscriber -> table, $slug);
					}
					
					if ($display == "always") {
						$Db -> model = $FieldsList -> model;
						$Db -> delete_all(array('field_id' => $this -> insertid));
						
						$fl_data = array('field_id' => $this -> insertid, 'list_id' => "0");
						$FieldsList -> save($fl_data, false); 
					} else {
						if (!empty($mailinglists)) {					
							foreach ($mailinglists as $mailinglist_id) {
								$fl_data = array('field_id' => $this -> insertid, 'list_id' => $mailinglist_id);
								$FieldsList -> save($fl_data, false);
							}
						} else {
							$fl_data = array('field_id' => $this -> insertid, 'list_id' => "0");
							$FieldsList -> save($fl_data, false);
						}
					}
					
					do_action($this -> pre . '_wpml_field_saved', $this -> insertid, $data);
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	function validate_optin($data = array(), $type = 'subscribe') {	
		global $wpdb, $FieldsList;
		include $this -> plugin_base() . DS . 'includes' . DS . 'variables.php';
			
		if ($fields = $FieldsList -> fields_by_list($data['list_id'], "order", "ASC", true, true)) {									
			if (!empty($fields)) {				
				foreach ($fields as $field) {														
					if ($field -> required == "Y") {							
						if (empty($field -> errormessage)) {
							$field -> errormessage = __('Please fill in ', $this -> plugin_name) . $field -> title;
						}
						
						switch ($field -> type) {
							case 'pre_date'			:								
								if (empty($data[$field -> slug]['y']) || empty($data[$field -> slug]['m']) || empty($data[$field -> slug]['d'])) {
									$this -> errors[$field -> slug] = __($field -> errormessage);
								}
							case 'special'			:
								switch ($field -> slug) {
									case 'list'				:
										if (empty($data['list_id']) && $type == "subscribe") {
											$this -> errors['list_id'] = __($field -> errormessage);
										}
										break;
								}
								break;
							default					:							
								if (empty($field -> validation) || $field -> validation == "notempty") {
									if (empty($data[$field -> slug])) {
										$this -> errors[$field -> slug] = __($field -> errormessage);
									}
								} else {
									if (!empty($field -> validation)) {									
										$regex = ($field -> validation == "custom") ? $field -> regex : $validation_rules[$field -> validation]['regex'];										
										if (!preg_match($regex, $data[$field -> slug])) {
											$this -> errors[$field -> slug] = __($field -> errormessage);
										}
									}
								}
								break;
						}
					}
				}
			}
		}
		
		return $this -> errors;
	}
	
	function delete($field_id = null) {
		global $wpdb, $Db, $Subscriber, $FieldsList;
		
		if (!empty($field_id)) {		
			$oldmodel = $Db -> model;
			$Db -> model = $this -> model;
			
			if ($field = $Db -> find(array('id' => $field_id))) {			
				if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $field_id . "' LIMIT 1")) {					
					$this -> delete_field($Subscriber -> table, $field -> slug);					
					$FieldsList -> delete_all(array('field_id' => $field_id));
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Removes multiple fields by ID.
	 * @param $array ARRAY An array of field record IDs
	 * @return BOOLEAN Either true or false based on the outcome
	 *
	 **/
	function delete_array($array = array()) {
		global $wpdb;
			
		if (!empty($array)) {
			foreach ($array as $field_id) {
				$fieldquery = "SELECT * FROM " . $wpdb -> prefix . $this -> table . " WHERE id = '" . $field_id . "'";
				if ($field = $wpdb -> get_row($fieldquery)) {
					if ($field -> slug != "email") {
						$this -> delete($field_id);
					}
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	function get($field_id = null) {
		global $wpdb;
	
		if (!empty($field_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `id` = '" . $field_id . "' LIMIT 1";
			$query_hash = md5($query);
			if ($data = wp_cache_get($query_hash, 'newsletters')) {
				return $data;
			}
		
			if ($field = $wpdb -> get_row($query)) {
				$this -> data = (array) $this -> data;
				$this -> data[$this -> model] = $this -> init_class($this -> model, $field);
				wp_cache_set($query_hash, $this -> data[$this -> model], 'newsletters', 0);
				return $this -> data[$this -> model];
			}
		}
		
		return false;
	}
	
	function get_all($fields = array()) {
		global $wpdb;
		
		$fields = (empty($fields)) ? "*" : $fields;
		
		if ($fields != "*") {
			if (is_array($fields)) {
				$selectfields = "";
				$i = 1;
				
				foreach ($fields as $field) {
					$selectfields .= "`" . $field . "`";
					
					if ($i < count($fields)) {
						$selectfields .= ", ";
					}
					
					$i++;
				}
			} else {
				$selectfields = "*";
			}
		} else {
			$selectfields = "*";
		}
		
		$query = "SELECT " . $selectfields . " FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
		
		$query_hash = md5($query);
		if ($data = wp_cache_get($query_hash, 'newsletters')) {
			return $data;
		}
		
		if ($fields = $wpdb -> get_results($query)) {
			if (!empty($fields)) {
				$data = array();
			
				foreach ($fields as $field) {
					$data[] = $this -> init_class($this -> model, $field);
				}
				
				wp_cache_set($query_hash, $data, 'newsletters', 0);
				return $data;
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve all fields in a paginated fashion
	 * @param $conditions ARRAY conditions passed on to the pagination class.
	 * @return $data ARRAY an array of order objects retrieved from the database
	 *
	 **/
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = "newsletters-fields", $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . "" . $this -> table, '*', $sub, $sub);
		$paginate -> where = (empty($conditions)) ? false : $conditions;
		$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
		$paginate -> per_page = $perpage;
		$paginate -> order = $order;
		$fields = $paginate -> start_paging($_GET[$this -> pre . 'page']);
		
		$data = array();
		$data['Pagination'] = $paginate;
		
		if (!empty($fields)) {
			foreach ($fields as $field) {
				$data[$this -> model][] = $this -> init_class($this -> model, $field);
			}
		}
		
		return $data;
	}
}

?>