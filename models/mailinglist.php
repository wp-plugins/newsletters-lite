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

if (!class_exists('newsletters_lite')) {
	class newsletters_lite extends wpMailPlugin {
		
		function newsletters_lite() {		
			if (!$this -> ci_serial_valid()) {
				$this -> add_filter('newsletters_sections', 'lite_sections', 10, 1);
				$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);		
				$this -> add_action('newsletters_admin_menu', 'lite_admin_menu', 10, 1);
				$this -> add_action('admin_bar_menu', 'lite_admin_bar_menu', 999, 1);
				$this -> add_filter('wpml_mailinglist_validation', 'lite_mailinglist_validation', 10, 2);
				$this -> add_filter('wpml_sendmail_validation', 'lite_sendmail_validation', 10, 2); 
				$this -> add_filter('wpml_subscriber_validation', 'lite_subscriber_validation', 10, 2);
			}
		}
		
		function lite_sections($sections = null) {
			$sections -> lite_upgrade = "newsletters-lite-upgrade";
			return $sections;
		}
		
		function lite_admin_menu($menus = null) {
			add_submenu_page($this -> sections -> welcome, __('Upgrade to PRO', $this -> plugin_name), __('Upgrade to PRO', $this -> plugin_name), 'newsletters_welcome', $this -> sections -> lite_upgrade, array($this, 'lite_upgrade'));
		}
		
		function lite_upgrade() {
			$this -> render('lite-upgrade', false, true, 'admin');
		}
		
		function lite_admin_bar_menu($wp_admin_bar = null) {
			global $wp_admin_bar;
		
			$args = array(
				'id'		=>	'newsletterslite',
				'title'		=>	__('Newsletters LITE', $this -> plugin_name),
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'newsletters-lite'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			global $Db, $Mailinglist;
			$Db -> model = $Mailinglist -> model;
			$list_count = $Db -> count();
			$lists = $list_count;
			$lists_percentage = (($lists / 1) * 100);
			$listlimit_title = sprintf(__('%s of 1 (%s&#37;) mailing lists used', $this -> plugin_name), $lists, $lists_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_listlimit',
				'title'		=>	$listlimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-listlimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			global $Db, $Subscriber;
			$Db -> model = $Subscriber -> model;
			$subscriber_count = $Db -> count();
			$subscribers = $subscriber_count;
			$subscribers_percentage = (($subscribers / 500) * 100);
			$subscriberlimit_title = sprintf(__('%s of 500 (%s&#37;) subscribers used', $this -> plugin_name), $subscribers, $subscribers_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_subscriberlimit',
				'title'		=>	$subscriberlimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-subscriberlimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$emails = $this -> lite_current_emails_all(1000, 'monthly');
			$emails_percentage = (($emails / 1000) * 100);
			$emaillimit_title = sprintf(__('%s of 1000 (%s&#37;) emails used (resets on 1st of month)', $this -> plugin_name), $emails, $emails_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_emaillimit',
				'title'		=>	$emaillimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-emaillimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'newsletterslite_submitserial',
				'title'		=>	__('Submit Serial Key', $this -> plugin_name),
				'parent'	=>	'newsletterslite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> submitserial),
				'meta'		=>	array('class' => 'newsletters-lite-submitserial', 'onclick' => "jQuery.colorbox({href:ajaxurl + \"?action=" . $this -> pre . "serialkey\"}); return false;"),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'newsletterslite_upgrade',
				'title'		=>	__('Upgrade to PRO now!', $this -> plugin_name),
				'parent'	=>	'newsletterslite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'newsletters-lite-upgrade'),
			);
			
			$wp_admin_bar -> add_node($args);
		}
		
		function lite_prevtime($time, $interval, $value) {
			$time = (empty($time)) ? date("Y-m-d H:i:s", time()) : $time;
			$time = strtotime($time);
		
			switch ($interval) {
				case 'hourly'			:
					$offset = ($time % 3600);
					$prev = $time - $offset;
					$seconds = ($value * 60);																						
					$newtime = ($offset >= $seconds) ? ($prev + $seconds) : ($prev - 3600 + $seconds);
					break;
				case 'daily'			:
					if (date("H", $time) < $value) { $time = strtotime("-1 days", $time); }
					$y = date("Y", $time);
					$m = date("m", $time);
					$d = date("d", $time);
					$h = date("H", strtotime($value . ':00'));
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' ' . $h . ':00');
					break;
				case 'weekly'			:
					$diff = $value - date("w"); 
					$timestamp = strtotime("+" . $diff . " days");
					$timestamp = strtotime("-7 days", $timestamp);
					$y = date("Y", $timestamp);
					$m = date("m", $timestamp);
					$d = date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' 00:00:00');
					break;
				case 'monthly'			:
					$diff = $value - date("d"); 
					$timestamp = strtotime("+" . $diff . " days");
					if (date("d", $time) < $value) { $timestamp = strtotime("-1 months", $timestamp); }
					$y = date("Y", $timestamp);
					$m = date("m", $timestamp);
					$d = date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' 00:00:00');
					break;
				case 'yearly'			:
					$diff = $value - date("m"); 
					$timestamp = strtotime("+" . $diff . " months");
					if (date("m", $time) < $value) { $timestamp = strtotime("-1 years", $timestamp); }
					$y = date("Y", $timestamp);
					$m = date("m", $timestamp);
					$d = date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-01 00:00:00');
					break;
			}									
			
			return $newtime;
		}
		
		function lite_current_emails_all($sendlimit = null, $sendlimitinterval = null, $sendlimitstart = null) {
			global $user_ID, $History, $Email, $wpdb;
			$emailscount = false;
			
			$prevtime = date("Y-m-d H:i:s", $this -> lite_prevtime(false, $sendlimitinterval, $sendlimitstart));
			$email_table = $wpdb -> prefix . $Email -> table;
			$history_table = $wpdb -> prefix . $History -> table;
			
			$emailsquery = "SELECT COUNT(" . $email_table . ".id) FROM `" . $email_table . "` LEFT JOIN `" . $history_table . "` 
			ON " . $email_table . ".history_id = " . $history_table . ".id WHERE " . $email_table . ".created > '" . $prevtime . "'";
			
			$emailscount = $wpdb -> get_var($emailsquery);				
			return $emailscount;
		}
		
		function lite_mailinglist_validation($errors = null, $data = null) {
			$newsletters_lite_listlimit = 1;
			if (!empty($newsletters_lite_listlimit) && $newsletters_lite_listlimit > 0) {
				global $Db, $Mailinglist;
				$Db -> model = $Mailinglist -> model;
				$list_count = $Db -> count();
				
				if ($list_count >= $newsletters_lite_listlimit) {
					$error = sprintf(__('Mailing list limit of %s has been reached, you can %s for unlimited.', $this -> plugin_name), $newsletters_lite_listlimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					$this -> render_error($error);
				}
			}
			
			return $errors;
		}
		
		function lite_sendmail_validation($errors = null, $data = null) {
			global $History, $Db, $wpdb;
		
			if (!empty($data['history_id'])) {
				$history_id = $data['history_id'];
				$Db -> model = $History -> model;
				if ($history = $Db -> find(array('id' => $history_id))) {
					$newsletters_lite_emaillimit = 1000;
					$newsletters_lite_emaillimitinterval = 'monthly';
					$newsletters_lite_emaillimitstart = 1;
					$newsletters_current_emails = $this -> lite_current_emails_all($newsletters_lite_emaillimit, $newsletters_lite_emaillimitinterval, $newsletters_lite_emaillimitstart);
					
					if (!empty($newsletters_lite_emaillimit)) {
						if ($newsletters_current_emails >= $newsletters_lite_emaillimit) {
							$error = sprintf(__('Email limit of %s emails per month has been reached, you can %s for unlimited.', $this -> plugin_name), $newsletters_lite_emaillimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
							global $mailerrors;
							$mailerrors = $error;
							$errors['limit'] = $error;
						}
					}
				}
			}
			
			return $errors;
		}
		
		function lite_subscriber_validation($errors = null, $data = null) {
		
			$newsletters_lite_subscriberlimit = 500;
			if (!empty($newsletters_lite_subscriberlimit) && $newsletters_lite_subscriberlimit > 0) {
				global $Db, $Subscriber;
				$Db -> model = $Subscriber -> model;
				$subscriber_count = $Db -> count();
				
				if ($subscriber_count >= $newsletters_lite_subscriberlimit) {
					$error = sprintf(__('Subscriber limit of %s has been reached, you can %s for unlimited.', $this -> plugin_name), $newsletters_lite_subscriberlimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					$this -> render_error($error);
				}
			}
			
			return $errors;
		}
	}
	
	$newsletters_lite = new newsletters_lite();
}

?>