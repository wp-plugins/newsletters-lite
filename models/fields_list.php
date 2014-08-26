<?php

class wpmlFieldsList extends wpMailPlugin {

	var $field_id = '';
	var $list_id = '';

	var $name = 'wpmlFieldsList';
	var $model = 'FieldsList';
	var $controller = 'fieldslists';
	var $table_name = 'wpmlfieldslists';
	var $errors = array();
	var $data = array();

	var $table_fields = array(
		'field_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'list_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'special'			=>	"TEXT NOT NULL",
		'order'				=>	"INT(11) NOT NULL DEFAULT '0'",
		'key'				=>	"KEY `field_id` (`field_id`, `list_id`)",
	);
	
	var $tv_fields = array(
		'field_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'list_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'special'			=>	array("TEXT", "NOT NULL"),
		'order'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'key'				=>	"KEY `field_id` (`field_id`, `list_id`)",);

	function wpmlFieldsList($data = array()) {
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = $val;
			}
		}
	}
	
	function find_all($conditions = array(), $fields = false, $order = array('order', "ASC"), $limit = false) {
		global $wpdb;
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "`";
		
		if (!empty($conditions) && is_array($conditions)) {
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
		
		$order = (empty($order)) ? array('order', "ASC") : $order;
		list($ofield, $odir) = $order;
		$query .= " ORDER BY `" . $ofield . "` " . $odir . "";
		$query .= (empty($limit)) ? '' : " LIMIT " . $limit . "";
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$fieldslists = ${'newsletters_query_' . $query_hash};
		} else {
			$fieldslists = $wpdb -> get_results($query);
			${'newsletters_query_' . $query_hash} = $fieldslists;
		}
		
		if (!empty($fieldslists)) {
			$data = array();
			
			foreach ($fieldslists as $fl) {
				$data[] = $this -> init_class($this -> model, $fl);
			}
			
			return $data;
		}
		
		return false;
	}
	
	function count_by_list($list_id = null) {
		global $wpdb, $Mailinglist;
		
		if (!empty($list_id)) {		
			if ($list = $Mailinglist -> get($list_id)) {
				$query = "SELECT COUNT(*) FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `list_id` = '" . $list_id . "'";
				
				$query_hash = md5($query);
				global ${'newsletters_query_' . $query_hash};
				if (!empty(${'newsletters_query_' . $query_hash})) {
					return ${'newsletters_query_' . $query_hash};
				}
			
				if ($count = $wpdb -> get_var($query)) {
					${'newsletters_query_' . $query_hash} = $count;
					return $count;
				}
			}
		}
		
		return 0;
	}
	
	function save_field($fieldname = null, $value = null, $field_id = null) {
		global $wpdb;
	
		if (!empty($fieldname)) {
			if ($value != "") {
				if (!empty($field_id)) {					
					if ($field = $this -> get_by_field($field_id)) {
						$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `" . $fieldname . "` = '" . $value . "' WHERE `field_id` = '" . $field_id . "'";
						
						if ($wpdb -> query($query)) {
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb;
		
		if (!empty($data)) {
			if ($validate == true) {
				if (empty($data['field_id'])) { $this -> errors[] = __('No field was specified', $this -> name); }
				if (empty($data['list_id']) && $data['list_id'] != "0") { $this -> errors[] = __('No list was specified', $this -> name); }
			} else {
				$this -> errors = false;
			}
		
			if (empty($this -> errors)) {
				$query = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`field_id`, `list_id`, `special`) VALUES ('" . $data['field_id'] . "', '" . $data['list_id'] . "', '" . $data['special'] . "');";
			
				if ($wpdb -> query($query)) {					
					$this -> insert_id = $wpdb -> insert_id;					
					return true;
				}
			}
		}
		
		return false;
	}
	
	function checkedlists_by_field($field_id = null) {
		global $wpdb, $Field;
		$field_id = (empty($field_id)) ? $Field -> data[$Field -> model] -> id : $field_id;
		
		if (!empty($field_id)) {		
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `field_id` = '" . $field_id . "'";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$fieldslists = ${'newsletters_query_' . $query_hash};
			} else {
				$fieldslists = $wpdb -> get_results($query);
				${'newsletters_query_' . $query_hash} = $fieldslists;
			}
		
			if (!empty($fieldslists)) {
				$data = array();
			
				foreach ($fieldslists as $fl) {
					$data[] = $fl -> list_id;
				}
				
				return $data;
			}
		}
		
		return false;
	}
	
	function fields_by_list($list_id = null, $order_by = "order", $order_dir = "ASC", $showemail = true, $showlist = true) {
		global $wpdb, $Field;
		
		$list_id = (empty($list_id)) ? 'all' : $list_id;
		
		if (!empty($list_id) && is_array($list_id)) {
			$listidquery = "(";
			$l = 1;
		
			foreach ($list_id as $likey => $lival) {
				$listidquery .= $wpdb -> prefix . $this -> table . ".list_id = '" . $lival . "'";
				
				if ($l < count($list_id)) {
					$listidquery .= " OR ";
				}
				
				$l++;
			}
			
			$listidquery .= ") OR";
		} else {
			if (is_numeric($list_id)) {
				$listidquery = $wpdb -> prefix . $this -> table . ".list_id = '" . $list_id . "' OR";
			}
		}
		
		$wherelistid = ($list_id == "all") ? " WHERE " : " WHERE " . $listidquery . "";
		
		if (!empty($showemail) && $showemail == true) {
			$wherelistid .= " " . $wpdb -> prefix . $this -> table . ".special = 'email' OR " . $wpdb -> prefix . $Field -> table . ".slug = 'email' OR";
		}
		
		if (!empty($showlist) && $showlist == true) {
			$wherelistid .= " " . $wpdb -> prefix . $this -> table . ".special = 'list' OR " . $wpdb -> prefix . $Field -> table . ".slug = 'list' OR";
		}
		
		$wherelistid .= " " . $wpdb -> prefix . $Field -> table . ".display = 'always'";
		$query = "SELECT * FROM " . $wpdb -> prefix . $this -> table_name . " LEFT JOIN " . $wpdb -> prefix . $Field -> table . " ON " . $wpdb -> prefix . $Field -> table . ".id = " . $wpdb -> prefix . $this -> table_name . ".field_id " . $wherelistid . " ORDER BY " . $wpdb -> prefix . $Field -> table . ".order ASC";
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			return ${'newsletters_query_' . $query_hash};
		}
	
		if ($fieldslists = $wpdb -> get_results($query)) {
			$data = array();
			
			if (!empty($fieldslists)) {
				$addedfields = array();
				
				if (empty($showemail) || $showemail == false) {
					$emailfield = $Field -> email_field();
					$addedfields[] = $emailfield -> slug;
				}
				
				if (empty($showlist) || $showlist == false) {
					$listfield = $Field -> list_field();
					$addedfields[] = $listfield -> slug;
				}
			
				foreach ($fieldslists as $fl) {
					if ($field = $Field -> get($fl -> field_id)) {						
						if (!in_array($field -> slug, $addedfields)) {							
							$data[] = $this -> init_class($Field -> model, $field);
							$addedfields[] = $field -> slug;
						}
					}
				}
			}
			
			${'newsletters_query_' . $query_hash} = $data;
			return $data;
		}
		
		return false;
	}
	
	function get_by_field($field_id = null) {
		global $wpdb;
		
		if (!empty($field_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `field_id` = '" . $field_id . "'";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$fieldslists = ${'newsletters_query_' . $query_hash};
			} else {
				$fieldslists = $wpdb -> get_results($query);
				${'newsletters_query_' . $query_hash} = $fieldslists;
			}
		
			if (!empty($fieldslists)) {
				$data = array();
				
				foreach ($fieldslists as $fl) {
					$data[] = $this -> init_class('wpmlFieldsList', $fl);
				}
				
				return $data;
			}
		}
		
		return false;
	}
	
	function delete_all($conditions = array()) {
		global $wpdb;
		
		if (!empty($conditions)) {
			$c = 1;
			$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE";
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
				
				if ($c < count($conditions)) {
					$query .= " AND";
				}
			}
			
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete_by_list($list_id = null) {
		global $wpdb, $Mailinglist;
		
		if (!empty($list_id)) {
			if ($list = $Mailinglist -> get($list_id, false)) {
				$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `list_id` = '" . $list_id . "'";
			
				if ($wpdb -> query($query)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function delete_by_field($field_id = null) {
		global $wpdb;
		
		if (!empty($field_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `field_id` = '" . $field_id . "'")) {
				return true;
			}
		}
		
		return false;
	}
}

?>