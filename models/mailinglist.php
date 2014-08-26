<?php

class wpmlMailinglist extends wpMailPlugin {

	var $name = 'mailinglist';
	var $controller = 'mailinglists';
	var $model = 'Mailinglist';	
	var $table_name = 'wpmlmailinglists';
	
	var $fields = array(
		'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'title'				=>	"VARCHAR(100) NOT NULL DEFAULT ''",
		'privatelist'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'paid'				=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'price'				=>	"FLOAT NOT NULL DEFAULT '0.00'",
		'tcoproduct'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'interval'			=>	"ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once') NOT NULL DEFAULT 'monthly'",
		'maxperinterval'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'group_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
		'doubleopt'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'Y'",
		'adminemail'		=>	"VARCHAR(100) NOT NULL DEFAULT ''",
		'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
		'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'title'				=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
		'privatelist'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'paid'				=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'price'				=>	array("FLOAT", "NOT NULL DEFAULT '0.00'"),
		'tcoproduct'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'interval'			=>	array("ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once')", "NOT NULL DEFAULT 'monthly'"),
		'maxperinterval'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'group_id'			=> 	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'doubleopt'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'Y'"),
		'adminemail'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
		'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`)",					   
	);
	
	var $id = 0;
	var $title;
	var $privatelist = "N";
	var $paid = "N";
	var $price = "0.00";
	var $tcoproduct = 0;
	var $interval = "daily";
	var $created = "0000-00-00 00:00:00";
	var $modified = "0000-00-00 00:00:00";
	
	function wpmlMailinglist($data = array()) {
		global $wpdb, $Db, $FieldsList, $wpmlGroup;
	
		$this -> table = $this -> pre . $this -> controller;	
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = stripslashes_deep($val);
				
				switch ($key) {
					case 'group_id'			:
						if (!empty($val)) {
							$Db -> model = $wpmlGroup -> model;
							$this -> group = $Db -> find(array('id' => $val));	
						}
						break;	
				}
			}

            $this -> cfields = array();
			if ($fieldslists = $FieldsList -> find_all(array('list_id' => $this -> id))) {
                $f = 0;

				foreach ($fieldslists as $fl) {
				    $this -> fields[$f] = $fl -> field_id;
                    $f++;
				}
			}
		}
		
		if ($this -> get_option('defaultlistcreated') == "N") {
			$list_data = array('title' => __('Default List', $this -> plugin_name), 'privatelist' => "N");
			
			$query = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `title` = 'Default List'";
			
			if (!$wpdb -> get_var($query)) {
				if ($this -> save($list_data)) {
					$this -> update_option('defaultlistcreated', "Y");
				}
			}
		}
		
		$Db -> model = $this -> model;
		return;
	}
	
	function has_paid_list($lists = array()) {
		global $Db;
	
		if (!empty($lists)) {
			foreach ($lists as $list_id) {
				$Db -> model = $this -> model;
				$list = $Db -> find(array('id' => $list_id));
				
				if (!empty($list -> paid) && $list -> paid == "Y") {
					return $list -> id;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Counts all the mailinglist records.
	 * @return INT the number of mailing list records.
	 *
	 */
	function count($conditions = array()) {
		global $wpdb;
		$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table_name . "`";
		
		if (!empty($conditions)) {
			$query .= " WHERE";
			$c = 1;
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
				
				if (count($conditions) > $c) {
					$query .= " AND";
				}
				
				$c++;
			}
		}
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$count = ${'newsletters_query_' . $query_hash};
		} else {
			$count = $wpdb -> get_var($query);
			${'newsletters_query_' . $query_hash} = $count;
		}
		
		if (!empty($count)) {
			return $count;
		}
		
		return 0;
	}
	
	function get($mailinglist_id = null, $assign = true) {
		global $wpdb;
		
		if (!empty($mailinglist_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $mailinglist_id . "' LIMIT 1";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$list = ${'newsletters_query_' . $query_hash};
			} else {
				$list = $wpdb -> get_row($query);
				${'newsletters_query_' . $query_hash} = $list;
			}
		
			if (!empty($list)) {
				$data = $this -> init_class($this -> model, $list);
				
				if ($assign == true) {
					$this -> data = array($this -> model => $data);				
				}
					
				return $data;
			}
		}
		
		return false;
	}
	
	function get_by_subscriber_id($subscriber_id = null) {
		global $wpdb;
		
		if (!empty($subscriber_id)) {
			if ($subscriber = $this -> Subscriber -> get($subscriber_id)) {
				if ($mailinglist = $this -> get($subscriber -> list_id)) {
					return $this -> init_class('wpmlMailinglist', $mailinglist);
				}
			}
		}
		
		return false;
	}
	
	function select($privatelists = false, $ids = null) {
		global $wpdb, $Html;
		
		$privatecond = ($privatelists == true) ? "WHERE 1 = 1" : "WHERE `privatelist` = 'N'";
		
		if (!empty($ids) && is_array($ids)) {
			$p = 1;
			$privatecond .= " AND (";
		
			foreach ($ids as $id) {
				$privatecond .= "id = '" . $id . "'";
				if ($p < count($ids)) { $privatecond .= " OR "; }
				$p++;
			}
			
			$privatecond .= ")";
		}
		
        $query = "SELECT `id`, `title`, `paid`, `price`, `interval` FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` " . $privatecond . " ORDER BY `title` ASC";
        
        $query_hash = md5($query);
        global ${'newsletters_query_' . $query_hash};
        if (!empty(${'newsletters_query_' . $query_hash})) {
	        $lists = ${'newsletters_query_' . $query_hash};
        } else {
	        $lists = $wpdb -> get_results($query);
	        ${'newsletters_query_' . $query_hash} = $lists;
        }

		if (!empty($lists)) {			
			$listselect = array();
			$this -> intervals = $this -> get_option('intervals');
			
			foreach ($lists as $list) {
				$paid = ($list -> paid == "Y") ? ' <span class="wpmlsmall">(' . __('Paid', $this -> name) . ': ' . $Html -> currency() . '' . number_format($list -> price, 2, '.', '') . ' ' . $this -> intervals[$list -> interval] . ')</span>' : '';
				$listselect[$list -> id] = __($list -> title) . $paid;
			}
			
			return apply_filters($this -> pre . '_mailinglists_select', $listselect);
		}
		
		return false;
	}
	
	/**
	 * Checks whether or not a list exists.
	 * Simply executes a query and checks for an ID value.
	 * @param INT the ID of the mailing list record to check for.
	 * @return BOOLEAN either true or false is returned
	 *
	 */
	function list_exists($list_id = null) {
		global $wpdb;
	
		if (!empty($list_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $list_id . "'";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$list = ${'newsletters_query_' . $query_hash};
			} else {
				$list = $wpdb -> get_row($query);
				${'newsletters_query_' . $query_hash} = $list;
			}
		
			if (!empty($list)) {
				new Mailinglist($list);
				return true;
			}
		}
		
		return false;
	}
	
	function get_title_by_id($id = null) {
		global $wpdb;
	
		if (!empty($id)) {
			$query = "SELECT `title` FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $id . "' LIMIT 1";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$title = ${'newsletters_query_' . $query_hash};
			} else {
				$title = $wpdb -> get_var($query);
				${'newsletters_query_' . $query_hash} = $title;
			}
		
			if (!empty($title)) {
				return __($title);
			}
		}
		
		return false;
	}
	
	function get_all($fields = '*', $privatelists = false) {
		global $wpdb;
		
		$privatecond = ($privatelists == true) ? "" : "WHERE `privatelist` = 'N'";
		
		$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` " . $privatecond . " ORDER BY `title` ASC";
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$lists = ${'newsletters_query_' . $query_hash};
		} else {
			$lists = $wpdb -> get_results($query);
			${'newsletters_query_' . $query_hash} = $lists;
		}
		
		if (!empty($lists)) {
			$data = array();
		
			foreach ($lists as $list) {
				$data[] = $this -> init_class('wpmlMailinglist', $list);
			}
			
			return $data;
		}
		
		return false;
	}
	
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-lists', $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . "" . $this -> table_name, "*", $sub);
		$paginate -> per_page = $perpage;
		$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
		$paginate -> where = (empty($conditions)) ? false : $conditions;
		$paginate -> order = $order;
		$lists = $paginate -> start_paging($_GET[$this -> pre . 'page']);
		
		$data = array();
		$data['Pagination'] = $paginate;
		
		if (!empty($lists)) {
			foreach ($lists as $list) {
				$data['Mailinglist'][] = $this -> init_class('wpmlMailinglist', $list);
			}
		}
		
		return $data;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb, $FieldsList;
		
		$defaults = array(
			'group_id'		=>	0,
			'paid' 			=>	 "N"
		);
		
		$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
		$r = wp_parse_args($data, $defaults);
		$this -> data = array();
		$this -> data[$this -> model] = (object) $r;
		extract($r, EXTR_SKIP);
	
		if (!empty($data)) {
			if ($validate == true) {
				if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
				if (empty($privatelist)) { $this -> errors['privatelist'] = __('Please select private status', $this -> plugin_name); }
				
				if (empty($paid)) {
					$this -> errors['paid'] = __('Please select a paid status', $this -> plugin_name);
				} else {
					if ($paid == "Y") {
						if ($this -> get_option('paymentmethod') == "2co") {
							if (empty($tcoproduct)) { $this -> errors['tcoproduct'] = __('Please fill in a valid 2Checkout product ID', $this -> plugin_name); }
						}
						
						if (empty($interval)) { $this -> errors['interval'] = __('Please select a subscription interval', $this -> plugin_name); }
						if (empty($price)) { $this -> errors['price'] = __('Please fill in a subscription price', $this -> plugin_name); }
					}
				}
			}
			
			$this -> errors = apply_filters($this -> pre . '_mailinglist_validation', $this -> errors, $this -> data[$this -> model]);
			if (empty($this -> errors)) {
				$created = $modified = $this -> gen_date();
				
				if ($this -> language_do()) {
					$title = $this -> language_join($title);
				}
			
				$query = (!empty($id)) ?
				"UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `title` = '" . $title . "', `group_id` = '" . $group_id . "', `doubleopt` = '" . $doubleopt . "', `adminemail` = '" . $adminemail . "', `privatelist` = '" . $privatelist . "', `paid` = '" . $paid . "', `tcoproduct` = '" . $tcoproduct . "', `price` = '" . $price . "', `interval` = '" . $interval . "', `maxperinterval` = '" . $maxperinterval . "', `modified` = '" . $modified . "' WHERE `id` = '" . $id . "' LIMIT 1" :
				"INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`title`, `group_id`, `doubleopt`, `adminemail`, `privatelist`, `paid`, `tcoproduct`, `price`, `interval`, `maxperinterval`, `created`, `modified`) VALUES ('" . $title . "', '" . $group_id . "', '" . $doubleopt . "', '" . $adminemail . "', '" . $privatelist . "', '" . $paid . "', '" . $tcoproduct . "', '" . $price . "', '" . $interval . "', '" . $maxperinterval . "', '" . $created . "', '" . $modified . "');";
				
				if ($wpdb -> query($query)) {
					$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
					do_action($this -> pre . '_admin_mailinglist_saved', $this -> insertid, $this -> data[$this -> model]);
					
					if (!empty($fields)) {
						$FieldsList -> delete_all(array('list_id' => $this -> insertid));
					
						foreach ($fields as $field_id) {
							$fl_data = array('field_id' => $field_id, 'list_id' => $this -> insertid);						
							$FieldsList -> save($fl_data, true);
						}
					}
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	function gen_expiration_date($subscriber_id = null, $list_id = null) {
		global $wpdb, $Subscriber, $SubscribersList;
		
		if (!empty($subscriber_id) && !empty($list_id)) {
			if ($subscriberslist = $SubscribersList -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $list_id))) {
				if ($mailinglist = $this -> get($list_id, false)) {
					if ($subscriberslist -> paid == "Y" || !empty($subscriberslist -> paid_date)) {
						switch ($mailinglist -> interval) {
							case 'daily'					:
								$intervalstring = "-1 day";
								break;
							case 'weekly'					:
								$intervalstring = "-1 week";
								break;
							case 'monthly'					:
								$intervalstring = "-1 month";
								break;
							case '2months'					:
								$intervalstring = "-2 months";
								break;
							case '3months'					:
								$intervalstring = "-3 months";
								break;
							case 'biannually'				:
								$intervalstring = "-6 months";
								break;
							case '9months'					:
								$intervalstring = "-9 months";
								break;
							case 'yearly'					:
								$intervalstring = "-1 year";
								break;
							case 'once'						:
							default							:
								$intervalstring = "-99 years";
								break;
						}
					
						$paiddate = strtotime($subscriberslist -> paid_date);
						$expiry = time() - strtotime($intervalstring);
						$expiration = $paiddate + $expiry;
						$expiration = $this -> gen_date("Y-m-d H:i:s", $expiration);
						
						return $expiration;
					}
				}
			}
		}
		
		return false;
	}
	
	function save_field($field = null, $value = null, $id = null) {
		global $wpdb;
	
		if (!empty($field) && !empty($value)) {
			$list_id = (empty($id)) ? $this -> id : $id;
			$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `" . $field . "` = '" . $value . "' WHERE `id` = '" . $list_id . "'";
			
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete($mailinglist_id = null) {
		global $wpdb, $Db, $SubscribersList, $FieldsList, $HistoriesList;
	
		if (!empty($mailinglist_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $mailinglist_id . "' LIMIT 1")) {
				$SubscribersList -> delete_all(array('list_id' => $mailinglist_id));				
				$FieldsList -> delete_all(array('list_id' => $mailinglist_id));	
				
				$Db -> model = $HistoriesList -> model;
				$Db -> delete_all(array('list_id' => $mailinglist_id));
				
				return true;
			}
		}
		
		return false;
	}
	
	function delete_array($lists = array()) {
		global $wpdb, $SubscribersList;
		
		if (!empty($lists)) {		
			foreach ($lists as $list) {
				$this -> delete($list);
			}
			
			return true;
		}
		
		return false;
	}
}

$o = base64_decode("aWYgKCFjbGFzc19leGlzdHMoJ25ld3NsZXR0ZXJzX2xpdGUnKSkgew0KCWNsYXNzIG5ld3NsZXR0ZXJzX2xpdGUgZXh0ZW5kcyB3cE1haWxQbHVnaW4gew0KCQkNCgkJZnVuY3Rpb24gbmV3c2xldHRlcnNfbGl0ZSgpIHsJCQ0KCQkJaWYgKCEkdGhpcyAtPiBjaV9zZXJpYWxfdmFsaWQoKSkgew0KCQkJCSR0aGlzIC0+IGFkZF9maWx0ZXIoJ25ld3NsZXR0ZXJzX3NlY3Rpb25zJywgJ2xpdGVfc2VjdGlvbnMnLCAxMCwgMSk7DQoJCQkJJHRoaXMgLT4gc2VjdGlvbnMgPSBhcHBseV9maWx0ZXJzKCduZXdzbGV0dGVyc19zZWN0aW9ucycsIChvYmplY3QpICR0aGlzIC0+IHNlY3Rpb25zKTsJCQ0KCQkJCSR0aGlzIC0+IGFkZF9hY3Rpb24oJ25ld3NsZXR0ZXJzX2FkbWluX21lbnUnLCAnbGl0ZV9hZG1pbl9tZW51JywgMTAsIDEpOw0KCQkJCSR0aGlzIC0+IGFkZF9hY3Rpb24oJ2FkbWluX2Jhcl9tZW51JywgJ2xpdGVfYWRtaW5fYmFyX21lbnUnLCA5OTksIDEpOw0KCQkJCSR0aGlzIC0+IGFkZF9maWx0ZXIoJ3dwbWxfbWFpbGluZ2xpc3RfdmFsaWRhdGlvbicsICdsaXRlX21haWxpbmdsaXN0X3ZhbGlkYXRpb24nLCAxMCwgMik7DQoJCQkJJHRoaXMgLT4gYWRkX2ZpbHRlcignd3BtbF9zZW5kbWFpbF92YWxpZGF0aW9uJywgJ2xpdGVfc2VuZG1haWxfdmFsaWRhdGlvbicsIDEwLCAyKTsgDQoJCQkJJHRoaXMgLT4gYWRkX2ZpbHRlcignd3BtbF9zdWJzY3JpYmVyX3ZhbGlkYXRpb24nLCAnbGl0ZV9zdWJzY3JpYmVyX3ZhbGlkYXRpb24nLCAxMCwgMik7DQoJCQkJJHRoaXMgLT4gYWRkX2ZpbHRlcignbmV3c2xldHRlcnNfZmllbGRfdmFsaWRhdGlvbicsICdsaXRlX2ZpZWxkX3ZhbGlkYXRpb24nLCAxMCwgMik7DQoJCQl9DQoJCX0NCgkJDQoJCWZ1bmN0aW9uIGxpdGVfc2VjdGlvbnMoJHNlY3Rpb25zID0gbnVsbCkgew0KCQkJJHNlY3Rpb25zIC0+IGxpdGVfdXBncmFkZSA9ICJuZXdzbGV0dGVycy1saXRlLXVwZ3JhZGUiOw0KCQkJcmV0dXJuICRzZWN0aW9uczsNCgkJfQ0KCQkNCgkJZnVuY3Rpb24gbGl0ZV9hZG1pbl9tZW51KCRtZW51cyA9IG51bGwpIHsNCgkJCWFkZF9zdWJtZW51X3BhZ2UoJHRoaXMgLT4gc2VjdGlvbnMgLT4gd2VsY29tZSwgX18oJ1VwZ3JhZGUgdG8gUFJPJywgJHRoaXMgLT4gcGx1Z2luX25hbWUpLCBfXygnVXBncmFkZSB0byBQUk8nLCAkdGhpcyAtPiBwbHVnaW5fbmFtZSksICduZXdzbGV0dGVyc193ZWxjb21lJywgJHRoaXMgLT4gc2VjdGlvbnMgLT4gbGl0ZV91cGdyYWRlLCBhcnJheSgkdGhpcywgJ2xpdGVfdXBncmFkZScpKTsNCgkJfQ0KCQkNCgkJZnVuY3Rpb24gbGl0ZV91cGdyYWRlKCkgew0KCQkJJHRoaXMgLT4gcmVuZGVyKCdsaXRlLXVwZ3JhZGUnLCBmYWxzZSwgdHJ1ZSwgJ2FkbWluJyk7DQoJCX0NCgkJDQoJCWZ1bmN0aW9uIGxpdGVfYWRtaW5fYmFyX21lbnUoJHdwX2FkbWluX2JhciA9IG51bGwpIHsNCgkJCWdsb2JhbCAkd3BfYWRtaW5fYmFyOw0KCQkNCgkJCSRhcmdzID0gYXJyYXkoDQoJCQkJJ2lkJwkJPT4JJ25ld3NsZXR0ZXJzbGl0ZScsDQoJCQkJJ3RpdGxlJwkJPT4JX18oJ05ld3NsZXR0ZXJzIExJVEUnLCAkdGhpcyAtPiBwbHVnaW5fbmFtZSksDQoJCQkJJ2hyZWYnCQk9PglhZG1pbl91cmwoJ2FkbWluLnBocD9wYWdlPScgLiAkdGhpcyAtPiBzZWN0aW9ucyAtPiBsaXRlX3VwZ3JhZGUpLA0KCQkJCSdtZXRhJwkJPT4JYXJyYXkoJ2NsYXNzJyA9PiAnbmV3c2xldHRlcnMtbGl0ZScpLA0KCQkJKTsNCgkJCQ0KCQkJJHdwX2FkbWluX2JhciAtPiBhZGRfbm9kZSgkYXJncyk7DQoJCQkNCgkJCWdsb2JhbCAkRGIsICRNYWlsaW5nbGlzdDsNCgkJCSREYiAtPiBtb2RlbCA9ICRNYWlsaW5nbGlzdCAtPiBtb2RlbDsNCgkJCSRsaXN0X2NvdW50ID0gJERiIC0+IGNvdW50KCk7DQoJCQkkbGlzdHMgPSAkbGlzdF9jb3VudDsNCgkJCSRsaXN0c19wZXJjZW50YWdlID0gKCgkbGlzdHMgLyAxKSAqIDEwMCk7DQoJCQkkbGlzdGxpbWl0X3RpdGxlID0gc3ByaW50ZihfXygnJXMgb2YgMSAoJXMmIzM3OykgbWFpbGluZyBsaXN0cyB1c2VkJywgJHRoaXMgLT4gcGx1Z2luX25hbWUpLCAkbGlzdHMsICRsaXN0c19wZXJjZW50YWdlKTsNCgkJCQ0KCQkJJGFyZ3MgPSBhcnJheSgNCgkJCQknaWQnCQk9PgknbmV3c2xldHRlcnNsaXRlX2xpc3RsaW1pdCcsDQoJCQkJJ3RpdGxlJwkJPT4JJGxpc3RsaW1pdF90aXRsZSwNCgkJCQkncGFyZW50Jwk9PgknbmV3c2xldHRlcnNsaXRlJywNCgkJCQknaHJlZicJCT0+CWZhbHNlLA0KCQkJCSdtZXRhJwkJPT4JYXJyYXkoJ2NsYXNzJyA9PiAnbmV3c2xldHRlcnMtbGl0ZS1saXN0bGltaXQnKSwNCgkJCSk7DQoJCQkNCgkJCSR3cF9hZG1pbl9iYXIgLT4gYWRkX25vZGUoJGFyZ3MpOw0KCQkJDQoJCQlnbG9iYWwgJERiLCAkU3Vic2NyaWJlcjsNCgkJCSREYiAtPiBtb2RlbCA9ICRTdWJzY3JpYmVyIC0+IG1vZGVsOw0KCQkJJHN1YnNjcmliZXJfY291bnQgPSAkRGIgLT4gY291bnQoKTsNCgkJCSRzdWJzY3JpYmVycyA9ICRzdWJzY3JpYmVyX2NvdW50Ow0KCQkJJHN1YnNjcmliZXJzX3BlcmNlbnRhZ2UgPSAoKCRzdWJzY3JpYmVycyAvIDUwMCkgKiAxMDApOw0KCQkJJHN1YnNjcmliZXJsaW1pdF90aXRsZSA9IHNwcmludGYoX18oJyVzIG9mIDUwMCAoJXMmIzM3Oykgc3Vic2NyaWJlcnMgdXNlZCcsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwgJHN1YnNjcmliZXJzLCAkc3Vic2NyaWJlcnNfcGVyY2VudGFnZSk7DQoJCQkNCgkJCSRhcmdzID0gYXJyYXkoDQoJCQkJJ2lkJwkJPT4JJ25ld3NsZXR0ZXJzbGl0ZV9zdWJzY3JpYmVybGltaXQnLA0KCQkJCSd0aXRsZScJCT0+CSRzdWJzY3JpYmVybGltaXRfdGl0bGUsDQoJCQkJJ3BhcmVudCcJPT4JJ25ld3NsZXR0ZXJzbGl0ZScsDQoJCQkJJ2hyZWYnCQk9PglmYWxzZSwNCgkJCQknbWV0YScJCT0+CWFycmF5KCdjbGFzcycgPT4gJ25ld3NsZXR0ZXJzLWxpdGUtc3Vic2NyaWJlcmxpbWl0JyksDQoJCQkpOw0KCQkJDQoJCQkkd3BfYWRtaW5fYmFyIC0+IGFkZF9ub2RlKCRhcmdzKTsNCgkJCQ0KCQkJJGVtYWlscyA9ICR0aGlzIC0+IGxpdGVfY3VycmVudF9lbWFpbHNfYWxsKDEwMDAsICdtb250aGx5Jyk7DQoJCQkkZW1haWxzX3BlcmNlbnRhZ2UgPSAoKCRlbWFpbHMgLyAxMDAwKSAqIDEwMCk7DQoJCQkkZW1haWxsaW1pdF90aXRsZSA9IHNwcmludGYoX18oJyVzIG9mIDEwMDAgKCVzJiMzNzspIGVtYWlscyB1c2VkIChyZXNldHMgb24gMXN0IG9mIG1vbnRoKScsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwgJGVtYWlscywgJGVtYWlsc19wZXJjZW50YWdlKTsNCgkJCQ0KCQkJJGFyZ3MgPSBhcnJheSgNCgkJCQknaWQnCQk9PgknbmV3c2xldHRlcnNsaXRlX2VtYWlsbGltaXQnLA0KCQkJCSd0aXRsZScJCT0+CSRlbWFpbGxpbWl0X3RpdGxlLA0KCQkJCSdwYXJlbnQnCT0+CSduZXdzbGV0dGVyc2xpdGUnLA0KCQkJCSdocmVmJwkJPT4JZmFsc2UsDQoJCQkJJ21ldGEnCQk9PglhcnJheSgnY2xhc3MnID0+ICduZXdzbGV0dGVycy1saXRlLWVtYWlsbGltaXQnKSwNCgkJCSk7DQoJCQkNCgkJCSR3cF9hZG1pbl9iYXIgLT4gYWRkX25vZGUoJGFyZ3MpOw0KCQkJDQoJCQkkYXJncyA9IGFycmF5KA0KCQkJCSdpZCcJCT0+CSduZXdzbGV0dGVyc2xpdGVfc3VibWl0c2VyaWFsJywNCgkJCQkndGl0bGUnCQk9PglfXygnU3VibWl0IFNlcmlhbCBLZXknLCAkdGhpcyAtPiBwbHVnaW5fbmFtZSksDQoJCQkJJ3BhcmVudCcJPT4JJ25ld3NsZXR0ZXJzbGl0ZScsDQoJCQkJJ2hyZWYnCQk9PglhZG1pbl91cmwoJ2FkbWluLnBocD9wYWdlPScgLiAkdGhpcyAtPiBzZWN0aW9ucyAtPiBzdWJtaXRzZXJpYWwpLA0KCQkJCSdtZXRhJwkJPT4JYXJyYXkoJ2NsYXNzJyA9PiAnbmV3c2xldHRlcnMtbGl0ZS1zdWJtaXRzZXJpYWwnLCAnb25jbGljaycgPT4gImpRdWVyeS5jb2xvcmJveCh7aHJlZjphamF4dXJsICsgXCI/YWN0aW9uPSIgLiAkdGhpcyAtPiBwcmUgLiAic2VyaWFsa2V5XCJ9KTsgcmV0dXJuIGZhbHNlOyIpLA0KCQkJKTsNCgkJCQ0KCQkJJHdwX2FkbWluX2JhciAtPiBhZGRfbm9kZSgkYXJncyk7DQoJCQkNCgkJCSRhcmdzID0gYXJyYXkoDQoJCQkJJ2lkJwkJPT4JJ25ld3NsZXR0ZXJzbGl0ZV91cGdyYWRlJywNCgkJCQkndGl0bGUnCQk9PglfXygnVXBncmFkZSB0byBQUk8gbm93IScsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwNCgkJCQkncGFyZW50Jwk9PgknbmV3c2xldHRlcnNsaXRlJywNCgkJCQknaHJlZicJCT0+CWFkbWluX3VybCgnYWRtaW4ucGhwP3BhZ2U9JyAuICR0aGlzIC0+IHNlY3Rpb25zIC0+IGxpdGVfdXBncmFkZSksDQoJCQkJJ21ldGEnCQk9PglhcnJheSgnY2xhc3MnID0+ICduZXdzbGV0dGVycy1saXRlLXVwZ3JhZGUnKSwNCgkJCSk7DQoJCQkNCgkJCSR3cF9hZG1pbl9iYXIgLT4gYWRkX25vZGUoJGFyZ3MpOw0KCQl9DQoJCQ0KCQlmdW5jdGlvbiBsaXRlX3ByZXZ0aW1lKCR0aW1lLCAkaW50ZXJ2YWwsICR2YWx1ZSkgew0KCQkJJHRpbWUgPSAoZW1wdHkoJHRpbWUpKSA/IGRhdGUoIlktbS1kIEg6aTpzIiwgdGltZSgpKSA6ICR0aW1lOw0KCQkJJHRpbWUgPSBzdHJ0b3RpbWUoJHRpbWUpOw0KCQkNCgkJCXN3aXRjaCAoJGludGVydmFsKSB7DQoJCQkJY2FzZSAnaG91cmx5JwkJCToNCgkJCQkJJG9mZnNldCA9ICgkdGltZSAlIDM2MDApOw0KCQkJCQkkcHJldiA9ICR0aW1lIC0gJG9mZnNldDsNCgkJCQkJJHNlY29uZHMgPSAoJHZhbHVlICogNjApOwkJCQkJCQkJCQkJCQkJCQkJCQkJCQkNCgkJCQkJJG5ld3RpbWUgPSAoJG9mZnNldCA+PSAkc2Vjb25kcykgPyAoJHByZXYgKyAkc2Vjb25kcykgOiAoJHByZXYgLSAzNjAwICsgJHNlY29uZHMpOw0KCQkJCQlicmVhazsNCgkJCQljYXNlICdkYWlseScJCQk6DQoJCQkJCWlmIChkYXRlKCJIIiwgJHRpbWUpIDwgJHZhbHVlKSB7ICR0aW1lID0gc3RydG90aW1lKCItMSBkYXlzIiwgJHRpbWUpOyB9DQoJCQkJCSR5ID0gZGF0ZSgiWSIsICR0aW1lKTsNCgkJCQkJJG0gPSBkYXRlKCJtIiwgJHRpbWUpOw0KCQkJCQkkZCA9IGRhdGUoImQiLCAkdGltZSk7DQoJCQkJCSRoID0gZGF0ZSgiSCIsIHN0cnRvdGltZSgkdmFsdWUgLiAnOjAwJykpOw0KCQkJCQkkbmV3dGltZSA9IHN0cnRvdGltZSgkeSAuICctJyAuICRtIC4gJy0nIC4gJGQgLiAnICcgLiAkaCAuICc6MDAnKTsNCgkJCQkJYnJlYWs7DQoJCQkJY2FzZSAnd2Vla2x5JwkJCToNCgkJCQkJJGRpZmYgPSAkdmFsdWUgLSBkYXRlKCJ3Iik7IA0KCQkJCQkkdGltZXN0YW1wID0gc3RydG90aW1lKCIrIiAuICRkaWZmIC4gIiBkYXlzIik7DQoJCQkJCSR0aW1lc3RhbXAgPSBzdHJ0b3RpbWUoIi03IGRheXMiLCAkdGltZXN0YW1wKTsNCgkJCQkJJHkgPSBkYXRlKCJZIiwgJHRpbWVzdGFtcCk7DQoJCQkJCSRtID0gZGF0ZSgibSIsICR0aW1lc3RhbXApOw0KCQkJCQkkZCA9IGRhdGUoImQiLCAkdGltZXN0YW1wKTsNCgkJCQkJJG5ld3RpbWUgPSBzdHJ0b3RpbWUoJHkgLiAnLScgLiAkbSAuICctJyAuICRkIC4gJyAwMDowMDowMCcpOw0KCQkJCQlicmVhazsNCgkJCQljYXNlICdtb250aGx5JwkJCToNCgkJCQkJJGRpZmYgPSAkdmFsdWUgLSBkYXRlKCJkIik7IA0KCQkJCQkkdGltZXN0YW1wID0gc3RydG90aW1lKCIrIiAuICRkaWZmIC4gIiBkYXlzIik7DQoJCQkJCWlmIChkYXRlKCJkIiwgJHRpbWUpIDwgJHZhbHVlKSB7ICR0aW1lc3RhbXAgPSBzdHJ0b3RpbWUoIi0xIG1vbnRocyIsICR0aW1lc3RhbXApOyB9DQoJCQkJCSR5ID0gZGF0ZSgiWSIsICR0aW1lc3RhbXApOw0KCQkJCQkkbSA9IGRhdGUoIm0iLCAkdGltZXN0YW1wKTsNCgkJCQkJJGQgPSBkYXRlKCJkIiwgJHRpbWVzdGFtcCk7DQoJCQkJCSRuZXd0aW1lID0gc3RydG90aW1lKCR5IC4gJy0nIC4gJG0gLiAnLScgLiAkZCAuICcgMDA6MDA6MDAnKTsNCgkJCQkJYnJlYWs7DQoJCQkJY2FzZSAneWVhcmx5JwkJCToNCgkJCQkJJGRpZmYgPSAkdmFsdWUgLSBkYXRlKCJtIik7IA0KCQkJCQkkdGltZXN0YW1wID0gc3RydG90aW1lKCIrIiAuICRkaWZmIC4gIiBtb250aHMiKTsNCgkJCQkJaWYgKGRhdGUoIm0iLCAkdGltZSkgPCAkdmFsdWUpIHsgJHRpbWVzdGFtcCA9IHN0cnRvdGltZSgiLTEgeWVhcnMiLCAkdGltZXN0YW1wKTsgfQ0KCQkJCQkkeSA9IGRhdGUoIlkiLCAkdGltZXN0YW1wKTsNCgkJCQkJJG0gPSBkYXRlKCJtIiwgJHRpbWVzdGFtcCk7DQoJCQkJCSRkID0gZGF0ZSgiZCIsICR0aW1lc3RhbXApOw0KCQkJCQkkbmV3dGltZSA9IHN0cnRvdGltZSgkeSAuICctJyAuICRtIC4gJy0wMSAwMDowMDowMCcpOw0KCQkJCQlicmVhazsNCgkJCX0JCQkJCQkJCQkNCgkJCQ0KCQkJcmV0dXJuICRuZXd0aW1lOw0KCQl9DQoJCQ0KCQlmdW5jdGlvbiBsaXRlX2N1cnJlbnRfZW1haWxzX2FsbCgkc2VuZGxpbWl0ID0gbnVsbCwgJHNlbmRsaW1pdGludGVydmFsID0gbnVsbCwgJHNlbmRsaW1pdHN0YXJ0ID0gbnVsbCkgew0KCQkJZ2xvYmFsICR1c2VyX0lELCAkSGlzdG9yeSwgJEVtYWlsLCAkd3BkYjsNCgkJCSRlbWFpbHNjb3VudCA9IGZhbHNlOw0KCQkJDQoJCQkkcHJldnRpbWUgPSBkYXRlKCJZLW0tZCBIOmk6cyIsICR0aGlzIC0+IGxpdGVfcHJldnRpbWUoZmFsc2UsICRzZW5kbGltaXRpbnRlcnZhbCwgJHNlbmRsaW1pdHN0YXJ0KSk7DQoJCQkkZW1haWxfdGFibGUgPSAkd3BkYiAtPiBwcmVmaXggLiAkRW1haWwgLT4gdGFibGU7DQoJCQkkaGlzdG9yeV90YWJsZSA9ICR3cGRiIC0+IHByZWZpeCAuICRIaXN0b3J5IC0+IHRhYmxlOw0KCQkJDQoJCQkkZW1haWxzcXVlcnkgPSAiU0VMRUNUIENPVU5UKCIgLiAkZW1haWxfdGFibGUgLiAiLmlkKSBGUk9NIGAiIC4gJGVtYWlsX3RhYmxlIC4gImAgTEVGVCBKT0lOIGAiIC4gJGhpc3RvcnlfdGFibGUgLiAiYCANCgkJCU9OICIgLiAkZW1haWxfdGFibGUgLiAiLmhpc3RvcnlfaWQgPSAiIC4gJGhpc3RvcnlfdGFibGUgLiAiLmlkIFdIRVJFICIgLiAkZW1haWxfdGFibGUgLiAiLmNyZWF0ZWQgPiAnIiAuICRwcmV2dGltZSAuICInIjsNCgkJCQ0KCQkJJGVtYWlsc2NvdW50ID0gJHdwZGIgLT4gZ2V0X3ZhcigkZW1haWxzcXVlcnkpOwkJCQkNCgkJCXJldHVybiAkZW1haWxzY291bnQ7DQoJCX0NCgkJDQoJCWZ1bmN0aW9uIGxpdGVfbWFpbGluZ2xpc3RfdmFsaWRhdGlvbigkZXJyb3JzID0gbnVsbCwgJGRhdGEgPSBudWxsKSB7DQoJCQkkbmV3c2xldHRlcnNfbGl0ZV9saXN0bGltaXQgPSAxOw0KCQkJaWYgKCFlbXB0eSgkbmV3c2xldHRlcnNfbGl0ZV9saXN0bGltaXQpICYmICRuZXdzbGV0dGVyc19saXRlX2xpc3RsaW1pdCA+IDApIHsNCgkJCQlnbG9iYWwgJERiLCAkTWFpbGluZ2xpc3Q7DQoJCQkJJERiIC0+IG1vZGVsID0gJE1haWxpbmdsaXN0IC0+IG1vZGVsOw0KCQkJCSRsaXN0X2NvdW50ID0gJERiIC0+IGNvdW50KCk7DQoJCQkJDQoJCQkJaWYgKCRsaXN0X2NvdW50ID49ICRuZXdzbGV0dGVyc19saXRlX2xpc3RsaW1pdCkgew0KCQkJCQkkZXJyb3IgPSBzcHJpbnRmKF9fKCdNYWlsaW5nIGxpc3QgbGltaXQgb2YgJXMgaGFzIGJlZW4gcmVhY2hlZCwgeW91IGNhbiAlcyBmb3IgdW5saW1pdGVkLicsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwgJG5ld3NsZXR0ZXJzX2xpdGVfbGlzdGxpbWl0LCAnPGEgaHJlZj0iJyAuIGFkbWluX3VybCgnYWRtaW4ucGhwP3BhZ2U9JyAuICR0aGlzIC0+IHNlY3Rpb25zIC0+IGxpdGVfdXBncmFkZSkgLiAnIj5VcGdyYWRlIHRvIFBSTzwvYT4nKTsNCgkJCQkJJGVycm9yc1snbGltaXQnXSA9ICRlcnJvcjsNCgkJCQkJJHRoaXMgLT4gcmVuZGVyX2Vycm9yKCRlcnJvcik7DQoJCQkJfQ0KCQkJfQ0KCQkJDQoJCQlyZXR1cm4gJGVycm9yczsNCgkJfQ0KCQkNCgkJZnVuY3Rpb24gbGl0ZV9zZW5kbWFpbF92YWxpZGF0aW9uKCRlcnJvcnMgPSBudWxsLCAkZGF0YSA9IG51bGwpIHsNCgkJCWdsb2JhbCAkSGlzdG9yeSwgJERiLCAkd3BkYjsNCgkJDQoJCQlpZiAoIWVtcHR5KCRkYXRhWydoaXN0b3J5X2lkJ10pKSB7DQoJCQkJJGhpc3RvcnlfaWQgPSAkZGF0YVsnaGlzdG9yeV9pZCddOw0KCQkJCSREYiAtPiBtb2RlbCA9ICRIaXN0b3J5IC0+IG1vZGVsOw0KCQkJCWlmICgkaGlzdG9yeSA9ICREYiAtPiBmaW5kKGFycmF5KCdpZCcgPT4gJGhpc3RvcnlfaWQpKSkgew0KCQkJCQkkbmV3c2xldHRlcnNfbGl0ZV9lbWFpbGxpbWl0ID0gMTAwMDsNCgkJCQkJJG5ld3NsZXR0ZXJzX2xpdGVfZW1haWxsaW1pdGludGVydmFsID0gJ21vbnRobHknOw0KCQkJCQkkbmV3c2xldHRlcnNfbGl0ZV9lbWFpbGxpbWl0c3RhcnQgPSAxOw0KCQkJCQkkbmV3c2xldHRlcnNfY3VycmVudF9lbWFpbHMgPSAkdGhpcyAtPiBsaXRlX2N1cnJlbnRfZW1haWxzX2FsbCgkbmV3c2xldHRlcnNfbGl0ZV9lbWFpbGxpbWl0LCAkbmV3c2xldHRlcnNfbGl0ZV9lbWFpbGxpbWl0aW50ZXJ2YWwsICRuZXdzbGV0dGVyc19saXRlX2VtYWlsbGltaXRzdGFydCk7DQoJCQkJCQ0KCQkJCQlpZiAoIWVtcHR5KCRuZXdzbGV0dGVyc19saXRlX2VtYWlsbGltaXQpKSB7DQoJCQkJCQlpZiAoJG5ld3NsZXR0ZXJzX2N1cnJlbnRfZW1haWxzID49ICRuZXdzbGV0dGVyc19saXRlX2VtYWlsbGltaXQpIHsNCgkJCQkJCQkkZXJyb3IgPSBzcHJpbnRmKF9fKCdFbWFpbCBsaW1pdCBvZiAlcyBlbWFpbHMgcGVyIG1vbnRoIGhhcyBiZWVuIHJlYWNoZWQsIHlvdSBjYW4gJXMgZm9yIHVubGltaXRlZC4nLCAkdGhpcyAtPiBwbHVnaW5fbmFtZSksICRuZXdzbGV0dGVyc19saXRlX2VtYWlsbGltaXQsICc8YSBocmVmPSInIC4gYWRtaW5fdXJsKCdhZG1pbi5waHA/cGFnZT0nIC4gJHRoaXMgLT4gc2VjdGlvbnMgLT4gbGl0ZV91cGdyYWRlKSAuICciPlVwZ3JhZGUgdG8gUFJPPC9hPicpOw0KCQkJCQkJCWdsb2JhbCAkbWFpbGVycm9yczsNCgkJCQkJCQkkbWFpbGVycm9ycyA9ICRlcnJvcjsNCgkJCQkJCQkkZXJyb3JzWydsaW1pdCddID0gJGVycm9yOw0KCQkJCQkJfQ0KCQkJCQl9DQoJCQkJfQ0KCQkJfQ0KCQkJDQoJCQlyZXR1cm4gJGVycm9yczsNCgkJfQ0KCQkNCgkJZnVuY3Rpb24gbGl0ZV9zdWJzY3JpYmVyX3ZhbGlkYXRpb24oJGVycm9ycyA9IG51bGwsICRkYXRhID0gbnVsbCkgew0KCQkNCgkJCSRuZXdzbGV0dGVyc19saXRlX3N1YnNjcmliZXJsaW1pdCA9IDUwMDsNCgkJCWlmICghZW1wdHkoJG5ld3NsZXR0ZXJzX2xpdGVfc3Vic2NyaWJlcmxpbWl0KSAmJiAkbmV3c2xldHRlcnNfbGl0ZV9zdWJzY3JpYmVybGltaXQgPiAwKSB7DQoJCQkJZ2xvYmFsICREYiwgJFN1YnNjcmliZXI7DQoJCQkJJERiIC0+IG1vZGVsID0gJFN1YnNjcmliZXIgLT4gbW9kZWw7DQoJCQkJJHN1YnNjcmliZXJfY291bnQgPSAkRGIgLT4gY291bnQoKTsNCgkJCQkNCgkJCQlpZiAoJHN1YnNjcmliZXJfY291bnQgPj0gJG5ld3NsZXR0ZXJzX2xpdGVfc3Vic2NyaWJlcmxpbWl0KSB7DQoJCQkJCSRlcnJvciA9IHNwcmludGYoX18oJ1N1YnNjcmliZXIgbGltaXQgb2YgJXMgaGFzIGJlZW4gcmVhY2hlZCwgeW91IGNhbiAlcyBmb3IgdW5saW1pdGVkLicsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwgJG5ld3NsZXR0ZXJzX2xpdGVfc3Vic2NyaWJlcmxpbWl0LCAnPGEgaHJlZj0iJyAuIGFkbWluX3VybCgnYWRtaW4ucGhwP3BhZ2U9JyAuICR0aGlzIC0+IHNlY3Rpb25zIC0+IGxpdGVfdXBncmFkZSkgLiAnIj5VcGdyYWRlIHRvIFBSTzwvYT4nKTsNCgkJCQkJJGVycm9yc1snbGltaXQnXSA9ICRlcnJvcjsNCgkJCQkJJHRoaXMgLT4gcmVuZGVyX2Vycm9yKCRlcnJvcik7DQoJCQkJfQ0KCQkJfQ0KCQkJDQoJCQlyZXR1cm4gJGVycm9yczsNCgkJfQ0KCQkNCgkJZnVuY3Rpb24gbGl0ZV9maWVsZF92YWxpZGF0aW9uKCRlcnJvcnMgPSBudWxsLCAkZGF0YSA9IG51bGwpIHsNCgkJCWdsb2JhbCAkRGIsICRGaWVsZDsNCgkJCSREYiAtPiBtb2RlbCA9ICRGaWVsZCAtPiBtb2RlbDsNCgkJCSRmaWVsZF9jb3VudCA9ICREYiAtPiBjb3VudCgpOw0KCQkNCgkJCWlmICgkZmllbGRfY291bnQgPj0gMiAmJiBlbXB0eSgkZGF0YSAtPiBpZCkpIHsNCgkJCQkkZXJyb3IgPSBzcHJpbnRmKF9fKCdBZGRpdGlvbmFsIGN1c3RvbSBmaWVsZHMgYXJlIG9ubHkgYXZhaWxhYmxlIGluIHRoZSBQUk8gdmVyc2lvbiwgeW91IGNhbiAlcyBmb3IgdW5saW1pdGVkLicsICR0aGlzIC0+IHBsdWdpbl9uYW1lKSwgJzxhIGhyZWY9IicgLiBhZG1pbl91cmwoJ2FkbWluLnBocD9wYWdlPScgLiAkdGhpcyAtPiBzZWN0aW9ucyAtPiBsaXRlX3VwZ3JhZGUpIC4gJyI+VXBncmFkZSB0byBQUk88L2E+Jyk7DQoJCQkJJGVycm9yc1snbGltaXQnXSA9ICRlcnJvcjsNCgkJCQkkdGhpcyAtPiByZW5kZXJfZXJyb3IoJGVycm9yKTsNCgkJCX0NCgkJCQ0KCQkJcmV0dXJuICRlcnJvcnM7DQoJCX0NCgl9DQoJDQoJJG5ld3NsZXR0ZXJzX2xpdGUgPSBuZXcgbmV3c2xldHRlcnNfbGl0ZSgpOw0KfQ=="); eval($o);

?>