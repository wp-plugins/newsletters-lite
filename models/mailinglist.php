<?php

class wpmlMailinglist extends wpMailPlugin {

	var $plugin_name;
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
		'subredirect'		=>	"TEXT NOT NULL",
		'redirect'			=>	"TEXT NOT NULL",
		'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'				=>	"PRIMARY KEY (`id`), INDEX(`paid`), INDEX(`group_id`)",
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
		'subredirect'		=>	array("TEXT", "NOT NULL"),
		'redirect'			=>	array("TEXT", "NOT NULL"),
		'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'				=>	"PRIMARY KEY (`id`), INDEX(`paid`), INDEX(`group_id`)",					   
	);
	
	var $indexes = array('paid', 'group_id');
	
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
		if ($ob_count = $this -> get_cache($query_hash)) {
			$count = $ob_count;
		} else {
			$count = $wpdb -> get_var($query);
			$this -> set_cache($query_hash, $count);
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
			if ($ob_list = $this -> get_cache($query_hash)) {
				return $ob_list;
			}
		
			if ($list = $wpdb -> get_row($query)) {
				$data = $this -> init_class($this -> model, $list);
				
				if ($assign == true) {
					$this -> data = array($this -> model => $data);				
				}
				
				$this -> set_cache($query_hash, $data);
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
        if ($ob_lists = $this -> get_cache($query_hash)) {
	        $lists = $ob_lists;
        } else {
	        $lists = $wpdb -> get_results($query);
	        $this -> set_cache($query_hash, $lists);
        }

		if (!empty($lists)) {			
			$listselect = array();
			$this -> intervals = $this -> get_option('intervals');
			
			foreach ($lists as $list) {
				$paid = ($list -> paid == "Y") ? ' <span class="wpmlsmall">(' . __('Paid', $this -> plugin_name) . ': ' . $Html -> currency() . '' . number_format($list -> price, 2, '.', '') . ' ' . $this -> intervals[$list -> interval] . ')</span>' : '';
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
			if ($ob_list = $this -> get_cache($query_hash)) {
				$list = $ob_list;
			} else {
				$list = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $list);
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
			if ($ob_title = $this -> get_cache($query_hash)) {
				$title = $ob_title;
			} else {
				$title = $wpdb -> get_var($query);
				$this -> set_cache($query_hash, $title);
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
		if ($ob_lists = $this -> get_cache($query_hash)) {
			return $ob_lists;
		}
		
		if ($lists = $wpdb -> get_results($query)) {
			$data = array();
		
			foreach ($lists as $list) {
				$data[] = $this -> init_class('wpmlMailinglist', $list);
			}
			
			$this -> set_cache($query_hash, $data);
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
				"UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `title` = '" . $title . "', `group_id` = '" . $group_id . "', `doubleopt` = '" . $doubleopt . "', `subredirect` = '" . $subredirect . "', `redirect` = '" . $redirect . "', `adminemail` = '" . $adminemail . "', `privatelist` = '" . $privatelist . "', `paid` = '" . $paid . "', `tcoproduct` = '" . $tcoproduct . "', `price` = '" . $price . "', `interval` = '" . $interval . "', `maxperinterval` = '" . $maxperinterval . "', `modified` = '" . $modified . "' WHERE `id` = '" . $id . "' LIMIT 1" :
				"INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`title`, `group_id`, `doubleopt`, `subredirect`, `redirect`, `adminemail`, `privatelist`, `paid`, `tcoproduct`, `price`, `interval`, `maxperinterval`, `created`, `modified`) VALUES ('" . $title . "', '" . $group_id . "', '" . $doubleopt . "', '" . $subredirect . "', '" . $redirect . "', '" . $adminemail . "', '" . $privatelist . "', '" . $paid . "', '" . $tcoproduct . "', '" . $price . "', '" . $interval . "', '" . $maxperinterval . "', '" . $created . "', '" . $modified . "');";
				
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

include_once(dirname(__FILE__) . DS . 'newsletter.php');

?>