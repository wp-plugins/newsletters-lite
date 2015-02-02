<?php

class wpmlSubscriber extends wpMailPlugin {

	var $model = 'Subscriber';
	var $controller = 'subscribers';
	var $table = '';
	
	var $id;
	var $email;
	var $registered = "N";
	var $user_id = 0;
	var $emailssent = 0;
    var $bouncecount = 0;
	var $created = '0000-00-00 00:00:00';
	var $modified = '0000-00-00 00:00:00';
	
	var $insertid = '';
	var $recursive = true;

	var $error = array();
	
	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'email'			=>	"VARCHAR(255) NOT NULL DEFAULT ''",
		'registered'	=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'ip_address'	=>	"VARCHAR(20) NOT NULL DEFAULT ''",
		'user_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'emailssent'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'format'		=>	"ENUM('html','text') NOT NULL DEFAULT 'html'",
		'cookieauth'	=>	"TEXT NOT NULL",
		'authkey'		=>	"VARCHAR(32) NOT NULL DEFAULT ''",
		'authinprog'	=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
        'bouncecount'   =>  "INT(1) NOT NULL DEFAULT '0'",
        'mandatory'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`email`), INDEX(`user_id`)",
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'email'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
		'registered'	=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'ip_address'	=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
		'user_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'emailssent'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'format'		=>	array("ENUM('html','text')", "NOT NULL DEFAULT 'html'"),
		'cookieauth'	=> 	array("TEXT", "NOT NULL"),
		'authkey'		=>	array("VARCHAR(32)", "NOT NULL DEFAULT ''"),
		'authinprog'	=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
        'bouncecount'   =>  array("INT(1)", "NOT NULL DEFAULT '0'"),
        'mandatory'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`email`), INDEX(`user_id`)",					   
	);
	
	var $indexes = array('email', 'user_id');
	
	var $name = 'wpmlSubscriber';
	
	function wpmlSubscriber($data = array()) {
		global $Db;
	
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			global $wpdb, $Db, $SubscribersList, $Mailinglist;
		
			foreach ($data as $key => $val) {
				$this -> {$key} = stripslashes_deep($val);
			
				if (!empty($data -> recursive) && $data -> recursive == true) {									
					switch ($key) {
						case 'id'		:
							$Db -> model = $SubscribersList -> model;
							if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $val))) {
								foreach ($subscriberslists as $sl) {
									$listquery = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $sl -> list_id . "' LIMIT 1";
									
									$query_hash = md5($listquery);
									if ($ob_list = $this -> get_cache($query_hash)) {
										$list = $ob_list;
									} else {
										$list = $wpdb -> get_row($listquery);
										$this -> set_cache($query_hash, $list);
									}
									
									$this -> Mailinglist[] = $list;
									$this -> subscriptions[] = $sl;
								}
							}
							break;
						case 'format'	:
							$this -> format = (empty($val)) ? 'html' : $val;
							break;
					}
					
					if (!empty($val)) {
						if (!in_array($key, $this -> table_fields)) {
							$val = maybe_unserialize($val);					
							$_POST[$key] = $val;
						}
					}
				}
			}
		}
		
		$Db -> model = $this -> model;
	}
	
	function admin_subscriber_id($mailinglists = array()) {
		$adminemail = $this -> get_option('adminemail');
							
		/* Check if the administrator email already exists */
		if (!$subscriber_id = $this -> email_exists($adminemail)) {
			$subscriberdata = array(
				'email'					=>	$adminemail,
				'mailinglists'			=>	$mailinglists,
				'registered'			=>	"N",
				'active'				=>	"Y",
			);
			
			$this -> save($subscriberdata, false);
			$subscriber_id = $this -> insertid;
		}
		
		return $subscriber_id;
	}
	
	function mailinglists($subscriber_id = null, $includeonly = null, $exclude = null, $active = "Y") {
		global $wpdb, $SubscribersList;
		$mailinglists = false;
	
		if (!empty($subscriber_id)) {
			$query = "SELECT `list_id` FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `subscriber_id` = '" . $subscriber_id . "'";
			if (!empty($active)) { $query .= " AND `active` = '" . $active . "'"; }
			
			$query_hash = md5($query);
			if ($ob_mailinglists = $this -> get_cache($query_hash)) {
				return $ob_mailinglists;
			}
			
			$listsarray = $wpdb -> get_results($query);
			$mailinglists = array();
			
			if (!empty($listsarray)) {			
				foreach ($listsarray as $larr) {
					if (empty($includeonly) || (!empty($includeonly) && $includeonly[0] == "all") || (!empty($includeonly) && in_array($larr -> list_id, $includeonly))) {
						if (empty($mailinglists) || (!empty($mailinglists) && !in_array($larr -> list_id, $mailinglists))) {
							if (empty($exclude) || (!empty($exclude) && !in_array($larr -> list_id, $exclude))) {
								$mailinglists[] = $larr -> list_id;
							}
						}
					}
				}
			}
		}
		
		$this -> set_cache($query_hash, $mailinglists);
		return $mailinglists;
	}
	
	function inc_sent($subscriber_id = null) {
		global $wpdb;
		
		if (!empty($subscriber_id)) {
			$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `emailssent` = `emailssent` + 1 WHERE `id` = '" . $subscriber_id . "'";
			
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Counts all subscriber records.
	 * Can take conditions to apply to the query.
	 * @param ARRAY An array of possible field => value conditions
	 * @return INT The number of subscribers for the given conditions
	 *
	 **/
	function count($condition = array()) {
		global $wpdb;
		
		$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $this -> table . "`";
		
		if (!empty($condition)) {
			$query .= " WHERE";
			foreach ($condition as $key => $val) {
				$query .= " `" . $key . "` = '" . $val . "'";
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

	/**
	 * Counts the subscribers of a specific mailinglist
	 * @param INT The ID of the list to use as a condition
	 * @return INT The number of subscribers in the specified mailing list.
	 *
	 **/
	function count_by_list($list = null) {
		global $wpdb;
	
		if (!empty($list)) {
			$where = ($list == "all") ? '' : " WHERE `list_id` = '" . $list . "'";
			
			$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table . "`" . $where . "";
			
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
		}
		
		return 0;
	}
	
	/**
	 * Counts subscribers for a specific day
	 * @param STR The date to use for counting subscribers
	 * @return INT The number of subscribers for the given day
	 *
	 **/
	function count_by_date($date = null) {
		global $wpdb;
		
		if (!empty($date)) {
			$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE DATE_FORMAT(`created`, '%Y-%m-%d') = '" . $date . "'";
			
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
		}
		
		return 0;
	}
	
	function check_registration($email = null) {
		global $wpdb;
	
		if (!empty($email)) {			
			if ($user_id = email_exists($email)) {
				return $user_id;
			}
		}
		
		return false;
	}
	
	function get($subscriber_id = null, $assign = true) {
		global $wpdb, $SubscribersList;
		
		if (!empty($subscriber_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $subscriber_id . "' LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_subscriber = $this -> get_cache($query_hash)) {
				return $ob_subscriber;
			}
		
			if ($subscriber = $wpdb -> get_row($query)) {			
				$subscriber = $this -> init_class($this -> model, $subscriber);				
				$subscriber -> mailinglists = $this -> mailinglists($subscriber_id);
				
				if ($assign === true) {
					if ($subscriber -> registered == "Y") {
						$user = get_userdata($subscriber -> user_id);						
						$subscriber -> username = $user -> user_login;
					}
					
					$subscriber -> recursive = true;
					$this -> data = (array) $this -> data;
					$this -> data[$this -> model] = $this -> init_class($this -> model, $subscriber); 
				}

				$this -> set_cache($query_hash, $subscriber);
				return $subscriber;
			}
		}
		
		return false;
	}
	
	function get_by_list($list = null) {
		global $wpdb;
		
		if (!empty($list)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . $list . "'";
			
			$query_hash = md5($query);
			if ($ob_subscribers = $this -> get_cache($query_hash)) {
				return $ob_subscribers;
			}
		
			if ($subscribers = $wpdb -> get_results($query)) {
				if (!empty($subscribers)) {
					$data = array();
				
					foreach ($subscribers as $subscriber) {
						$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
					}
					
					$this -> set_cache($query_hash, $data);
					return $data;
				}
			}
		}
		
		return false;
	}
	
	function select() {
		global $wpdb, $Subscriber;
		$select = array();
		
		if ($subscribers = $Subscriber -> get_all()) {
			if (!empty($subscribers)) {
				foreach ($subscribers as $subscriber) {
					$select[$subscriber -> id] = $subscriber -> id . ' - ' . $subscriber -> email;
				}
				
				return $select;
			}
		}
		
		return false;
	}
	
	function get_all() {
		global $wpdb;
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` ORDER BY `email` ASC";
		
		$query_hash = md5($query);
		if ($ob_subscribers = $this -> get_cache($query_hash)) {
			return $ob_subscribers;
		}
		
		if ($subscribers = $wpdb -> get_results($query)) {
			if (!empty($subscribers)) {
				$data = array();
				
				foreach ($subscribers as $subscriber) {
					$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
				}
				
				$this -> set_cache($query_hash, $data);
				return $data;
			}
		}
		
		return false;
	}
	
	function get_send_subscribers($group = 'all', $lists = null) {
		global $wpdb;
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE";
				
		if (!empty($lists)) {
			if (is_array($lists)) {
				$this -> Mailinglist = $this -> init_class('wpmlMailinglist');
				$m = 1;
			
				foreach ($lists as $list_id) {
					$mailinglist = $this -> Mailinglist -> get($list_id);
					$activepaid = ($mailinglist -> paid == "Y") ? "`paid` = 'Y'" : "`active` = 'Y'";
					$query .= " (`list_id` = '" . $list_id . "' AND " . $activepaid . ")";
					
					if ($m < count($lists)) {
						$query .= " OR";
					}
				}
				
				$m++;
			}
		}
		
		if ($subscribers = $wpdb ->	get_results($query)) {		
			if (!empty($subscribers)) {
				$data = array();

				if (!empty($subscribers)) {			
					foreach ($subscribers as $subscriber) {
						$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
					}
					
					return $data;
				}
			}
		}
		
		return false;
	}
	
	function email_exists($email = null, $list_id = null) {
		global $wpdb;
	
		if (!empty($email)) {
			$query = "SELECT `id` FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . esc_sql($email) . "'";
			
			if (!empty($list_id)) {
				$query .= " AND `list_id` = '" . $list_id . "'";
			}
		
			if ($subscriber = $wpdb -> get_row($query)) {			
				return $subscriber -> id;
			}
		}
		
		return false;
	}
	
	function email_validate($email = null) {
		if (is_email(strtolower(trim($email)))) {
			return true;
		}
		
		return false;
	}
	
	function search($data = array()) {
		global $wpdb;
		
		if (!empty($data)) {
			if (empty($data['searchterm'])) { $this -> errors[] = __('Please fill in a searchterm', $this -> plugin_name); }
			if (empty($data['searchtype'])) { $this -> errors[] = __('Please select a search type', $this -> plugin_name); }
			
			if (empty($this -> errors)) {
				if ($data['searchtype'] == "listtitle") {
					$listsquery = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> Mailinglist -> table_name . "` WHERE `title` LIKE '%" . strtolower($data['searchterm']) . "%'";
					$lists = $wpdb -> query($listsquery);
					
					if (!empty($lists)) {
						$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . $lists[0] -> id . "'";
						
						for ($l = 1; $l < count($lists); $l++) {
							$query .= " OR `list_id` = '" . $lists[$l] -> id . "'";
						}
					} else {
						$this -> errors[] = __('No mailing lists matched your title', $this -> plugin_name);
						return false;
					}
				} else {
					$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `" . $data['searchtype'] . "` LIKE '%" . $data['searchterm'] . "%'";
					$subscribers = $wpdb -> get_results($query);
					
					if (!empty($subscribers)) {
						$data = array();
					
						foreach ($subscribers as $subscriber) {
							$data[] = $this -> init_class($this -> plugin_name, $subscriber);
						}
						
						return $data;
					}
				}
			}
		}
		
		return false;
	}
	
	function optin($data = array(), $validate = true, $checkexists = true, $confirm = true) {
		//global Wordpress variables
		global $wpdb, $Db, $Auth, $SubscribersList, $Field, $Mailinglist;
		$this -> errors = array();
		$number = $_REQUEST['uninumber'];
		$emailfield = $Field -> email_field();
		$postedlists = $data['mailinglists'];
	
		//ensure that the data is not empty
		if (!empty($data)) {
			$data['list_id'] = array_filter($data['list_id']);			
			$options = $this -> get_option('widget');
			
			if (!empty($data['list_id']) && is_array($data['list_id'])) {
				foreach ($data['list_id'] as $list_id) {
					$data['mailinglists'][] = $list_id;
				}
			}
			
			// The email address should always be validated, we don't want broken addresses
			if (empty($data['email'])) { $this -> errors['email'] = __($emailfield -> errormessage); }
			elseif (!$this -> email_validate($data['email'])) { $this -> errors['email'] = __($emailfield -> errormessage); }
			
			// Should everything be validated?
			if ($validate == true) {				
				$Field -> validate_optin($data);					
				if (!empty($Field -> errors)) {
					$this -> errors = array_merge($this -> errors, $Field -> errors);
				}

				//if ($data['captcha_prefix'] != "" || (!empty($_GET[$this -> pre . 'method']) && $_GET[$this -> pre . 'method'] == "offsite")) {
				if (!empty($data['captcha_prefix']) || !empty($data['recaptcha_challenge_field'])) {
					$cap = 'Y';
				} else {
					$cap = 'N';
				}
				
				if ($captcha_type = $this -> use_captcha($cap)) {						
					if ($captcha_type == "rsc") {						
						$captcha = new ReallySimpleCaptcha();										
						if (empty($data['captcha_code'])) { $this -> errors['captcha_code'] = __('Please fill in the code in the image.', $this -> plugin_name); }
						elseif (!$captcha -> check($data['captcha_prefix'], $data['captcha_code'])) { $this -> errors['captcha_code'] = __('Your code does not match the code in the image.', $this -> plugin_name); }
						$captcha -> remove($data['captcha_prefix']);
					} elseif ($captcha_type == "recaptcha") {					
						require_once($this -> plugin_base() . DS . 'vendors' . DS . 'recaptcha' . DS . 'recaptchalib.php');
						$privatekey = $this -> get_option('recaptcha_privatekey');
						$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
						
						if (empty($resp)) { $this -> errors['captcha_code'] = __('No response was received from reCAPTCHA, please try again.', $this -> plugin_name); }
						elseif (!$resp -> is_valid) { $this -> errors['captcha_code'] = __('Your code does not match the code in the image.', $this -> plugin_name); }
					}
				}
				
				//Honeypot spam prevention
				if (!empty($data['newslettername'])) {
					$this -> errors['newslettername'] = __('Validation error occurred, this looks like spam', $this -> plugin_name);
				}
			}
			
			if (empty($this -> errors)) {				
				if ($data['id'] = $this -> email_exists($data['email'])) {
					$lists = $this -> mailinglists($data['id'], $data['mailinglists']);
				
					if (!empty($checkexists) && $checkexists == true && !empty($lists)) {					
						$this -> render('error', array('errors' => array('email' => __($this -> get_option('subscriberexistsmessage')))), true, 'default');					
						
						if ($this -> get_option('subscriberexistsredirect') == "management") {
							$redirecturl = get_permalink($this -> get_option('managementpost'));
						} elseif ($this -> get_option('subscriberexistsredirect') == "custom") {
							$redirecturl = $this -> get_option('subscriberexistsredirecturl');	
						} else {
							//do nothing...	
						}
						
						$this -> redirect($redirecturl, false, false, true);
						exit(); die();
					} else {
						if ($subscriberslists = $SubscribersList -> find_all(array('subscriber_id' => $data['id']))) {
							foreach ($subscriberslists as $sl) {
								if ((empty($data['mailinglists'])) || (!empty($data['mailinglists']) && !in_array($sl -> list_id, $data['mailinglists']))) {
									$data['mailinglists'][] = $sl -> list_id;
								}
							}
						}
					}
				}
				
				// All lists?
				if ($data['mailinglists'] == "all" || $data['mailinglists'][0] == "all") {
					$data['mailinglists'] = array();
				
					$Db -> model = $Mailinglist -> model;
					if ($lists = $Db -> find_all()) {
						foreach ($lists as $list) {
							$data['mailinglists'][] = $list -> id;
						}
					}
					
					$data['list_id'] = $data['mailinglists'];
				}
				
				// is an "active" parameter already passed through?
				if (empty($data['active'])) {
					$data['active'] = ($this -> get_option('requireactivate') == "Y") ? 'N' : 'Y';
				}
				
				if ($userid = $this -> check_registration($data['email'])) {
					$data['registered'] = "Y";
					$data['user_id'] = $userid;
				} else {
					$data['registered'] = "N";
					$data['user_id'] = 0;
				}
						
				if ($this -> save($data, false, false)) {					
					$subscriber = $this -> get($this -> insertid, false);
					$subscriberauth = $Auth -> gen_subscriberauth();
					
					//$Db -> model = $this -> model;
					//$Db -> save_field('authkey', $subscriberauth, array('id' => $subscriber -> id));
					$subscriberauth = $this -> gen_auth($subscriber -> id);
					
					if (!is_admin()) {
						$Auth -> set_emailcookie($subscriber -> email);
					}
					
					/* Management Auth */
					if (empty($data['cookieauth'])) {							
						$Db -> model = $this -> model;
						$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
					}
					
					if ($confirm) { $this -> subscription_confirm($subscriber); }
					$subscriber -> mailinglists = $data['list_id'];
					$this -> admin_subscription_notification($subscriber);					
					return $subscriber -> id;
				}
			} else {
				$_POST[$this -> pre . 'errors'] = $this -> errors;
			}
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		$this -> data = $data;
		return false;
	}
	
	function save($data = array(), $validate = true, $return_query = false) {
		global $wpdb, $Html, $Db, $wpmlCountry, $Field, $FieldsList, $Mailinglist, $SubscribersList, 
		$Bounce, $Unsubscribe;
		
		$this -> errors = false;
		
		$defaults = array(
			'ip_address'		=>	$_SERVER['REMOTE_ADDR'],
			'cookieauth'		=>	"",
			'emailssent'		=>	0,
			'format'			=>	"html",
			'authkey'			=>	"",
			'authinprog'		=>	"N",
			'registered' 		=> 	"N", 
			'username'			=>	"",
			'password' 			=> 	substr(md5(uniqid(microtime())), 0, 6), 
			'active' 			=>	"N",
            'bouncecount'       =>  0,
			'user_id'			=>	0,
			'created' 			=> 	$Html -> gen_date(), 
			'modified' 			=> 	$Html -> gen_date()
		);
		
		$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
		$r = wp_parse_args($data, $defaults);
		$this -> data = array($this -> model => (object) $r);
		extract($r, EXTR_SKIP);
		$emailfield = $Field -> email_field();
		
		if (!empty($id)) {
			$subscriber = $this -> get($id, false);
			if ($subscriber -> registered == "Y") {
				if (!empty($subscriber -> user_id)) {
					$user = get_userdata($subscriber -> user_id);
				}
			}
		}
		
		if ($validate == true) {
			//was the email address left empty?
			if (empty($email)) { $this -> errors['email'] = __($emailfield -> errormessage); }
			//does a subscriber with this email address already exist?
			elseif ($curr_id = $this -> email_exists($email)) { 
				if ($email != $subscriber -> email) {
					$id = $curr_id;
					$this -> id = $curr_id;
					$this -> data[$this -> model] -> id = $curr_id;			
					
					$cur_lists = $this -> mailinglists($curr_id);
					$sel_lists = $mailinglists;
					$new_lists = array_merge($cur_lists, $sel_lists);
							
					$this -> data[$this -> model] -> mailinglists = $new_lists;
					
					if (empty($justsubscribe)) {
						$_POST['subscriber_id'] = $curr_id;
						$this -> errors['email'] = __('Email exists, therefore the appropriate lists have been checked below. Please submit again', $this -> plugin_name);
					}
				}
			} elseif (!$this -> email_validate($email)) { $this -> errors['email'] = __('Please fill in a valid email address', $this -> plugin_name); }
			
			if (!is_admin() && empty($mailinglists)) { $this -> errors['mailinglists'] = __('Please select mailing list(s)', $this -> plugin_name); }
			
			if (empty($registered)) { $this -> errors['registered'] = __('Please select a registered status', $this -> plugin_name); }
			elseif ($registered == "Y") {
				if (!$userid = $this -> check_registration($data['email'])) {
					if (empty($username)) { $this -> errors['username'] = __('Please fill in a username', $this -> plugin_name); }
					else {								
						if (empty($fromregistration)) {					
							if (!empty($user)) {					
								if (username_exists($username) && $username !== $user -> user_login) {
									$this -> errors['username'] = __('Username is already in use', $this -> plugin_name);
								} else {
									if ($username !== $user -> user_login) {
										if (!empty($email)) {										
											if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
												$wpuser = new WP_User($user_id);
												$wpuser -> set_role("subscriber");
												wp_new_user_notification($user_id, $password);
											}
										} else {
											$this -> errors['username'] = __('Email required for registration', $this -> plugin_name);
										}
									} else {
										$user_id = $user -> ID;
									}
								}
							} else {											
								if (username_exists($username)) {
									$this -> errors['username'] = __('Username is already in use', $this -> plugin_name);
								} else {													
									if (!empty($username) && !empty($email)) {									
										if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
											$wpuser = new WP_User($user_id);
											$wpuser -> set_role("subscriber");
											wp_new_user_notification($user_id, $password);
										}
									} else {
										$this -> errors['username'] = __('Username and email address required for registration', $this -> plugin_name);
									}
								}
							}
						}
					}
				} else {
					$userdata = $this -> userdata($userid);
					$data['username'] = $username = $userdata -> data -> user_login;
					$data['registered'] = $registered = "Y";
				}
			}
			
			if (empty($active)) { $this -> errors['active'] = __('Please select an active status', $this -> plugin_name); }
		} else {
			if (empty($email)) { $this -> errors['email'] = __($emailfield -> errormessage); }
		}
		
		$this -> errors = apply_filters($this -> pre . '_subscriber_validation', $this -> errors, $this -> data[$this -> model]);
		
		if (empty($this -> errors)) {
			$email = $data['email'] = trim(strtolower($data['email']));
			
			if ($userid = $this -> check_registration($data['email'])) {
				$data['registered'] = $registered = "Y";
				$data['user_id'] = $user_id = $userid;
			}
				
			if (!empty($id)) {
				$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET";
				unset($this -> table_fields['key']);
				unset($this -> table_fields['created']);
				//unset($this -> table_fields['email']);
				$c = 1;
						
				/* Custom Fields */	
				$usedfields = array();
				$fieldsquery = "SELECT `id`, `type`, `slug`, `fieldoptions` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list'";
				
				$query_hash = md5($fieldsquery);
				if ($ob_fields = $this -> get_cache($query_hash)) {
					$fields = $ob_fields;
				} else {
					$fields = $wpdb -> get_results($fieldsquery);
					$this -> set_cache($query_hash, $fields);
				}
				
				if (!empty($fields)) {				
					foreach ($fields as $field) {
						if ((empty($usedfields)) || (!empty($usedfields) && !in_array($field -> slug, $usedfields))) {						
							if (!empty($data[$field -> slug])) {							
								if (!empty($field -> type) && ($field -> type == "radio" || $field -> type == "select")) {
									$fieldoptions = maybe_unserialize($field -> fieldoptions);
									$fieldoptions = array_map('__', $fieldoptions);
									
									if (array_key_exists($data[$field -> slug], $fieldoptions)) {
										//do nothing, it is okay?
									} elseif ($key = array_search($data[$field -> slug], $fieldoptions)) {
										$data[$field -> slug] = $key;
									} else {
									
									}
								} elseif ($field -> type == "checkbox") {
									$fieldoptions = maybe_unserialize($field -> fieldoptions);
									$fieldoptions = array_map('__', $fieldoptions);
									
									if (!empty($data[$field -> slug])) {
										$array = $data[$field -> slug];
										
										$newarray = array();
										foreach ($array as $akey => $aval) {										
											if (!empty($aval) || $aval == "0") {																																
												if (array_key_exists($aval, $fieldoptions)) {												
													$newarray[] = $aval;
												} elseif ($key = array_search(trim(str_replace("\n", "", $aval)), $fieldoptions)) {												
													$newarray[] = $key;
												}
											}
										}
										
										$data[$field -> slug] = maybe_serialize($newarray);
									}
								} elseif ($field -> type == "pre_gender") {
									if (!empty($data[$field -> slug])) {
										$data[$field -> slug] = strtolower($data[$field -> slug]);
									}
								} elseif (!empty($field -> type) && $field -> type == "pre_country") {
									if (!is_numeric($data[$field -> slug])) {
										$countryquery = "SELECT `id` FROM `" . $wpdb -> prefix . $wpmlCountry -> table . "` WHERE `value` = '" . esc_sql($data[$field -> slug]) . "'";
										if ($country_id = $wpdb -> get_var($countryquery)) {										
											$data[$field -> slug] = $country_id;
										}
									}
								} else {
									if (is_array($data[$field -> slug])) {
										$data[$field -> slug] = serialize($data[$field -> slug]);
									}
								}
							
								$query .= " `" . $field -> slug . "` = '" . (esc_sql($data[$field -> slug])) . "', ";
							}
						
							$usedfields[] = $field -> slug;
						}
					}
				}
				
				foreach (array_keys($this -> table_fields) as $field) {
					if (!empty(${$field})) {
						$query .= " `" . $field . "` = '" . esc_sql(${$field}) . "'";
						
						if ($c < count($this -> table_fields)) {
							$query .= ", ";
						}
					}
					
					$c++;
				}
				
				$query .= " WHERE `id` = '" . $id . "';";
				$this -> table_fields['created'] = true;
			} else { 
				$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table . "` (";
				$query2 = "";
				
				unset($this -> table_fields['id']);
				unset($this -> table_fields['key']);
				//unset($this -> table_fields['email']);
				$c = 1;
				
				/* Custom Fields */
				$usedfields = array();				
				$fieldsquery = "SELECT `id`, `type`, `slug`, `fieldoptions` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list'";
				
				$query_hash = md5($fieldsquery);
				if ($ob_fields = $this -> get_cache($query_hash)) {
					$fields = $ob_fields;
				} else {
					$fields = $wpdb -> get_results($fieldsquery);
					$this -> set_cache($query_hash, $fields);
				}
				
				if (!empty($fields)) {
					foreach ($fields as $field) {
						if (empty($usedfields) || (!empty($usedfields) && !in_array($field -> slug, $usedfields))) {
							if (!empty($data[$field -> slug])) {								
								if (!empty($field -> type) && ($field -> type == "radio" || $field -> type == "select")) {
									$fieldoptions = maybe_unserialize($field -> fieldoptions);
									$fieldoptions = array_map('__', $fieldoptions);
									
									if (array_key_exists($data[$field -> slug], $fieldoptions)) {
										//do nothing, it is okay?
									} elseif ($key = array_search($data[$field -> slug], $fieldoptions)) {
										$data[$field -> slug] = $key;
									} else {
										
									}
								} elseif ($field -> type == "checkbox") {
									$fieldoptions = maybe_unserialize($field -> fieldoptions);
									$fieldoptions = array_map('__', $fieldoptions);
									
									if (!empty($data[$field -> slug])) {
										$array = $data[$field -> slug];
										
										$newarray = array();
										foreach ($array as $akey => $aval) {										
											if (!empty($aval) || $aval == "0") {																																
												if (array_key_exists($aval, $fieldoptions)) {												
													$newarray[] = $aval;
												} elseif ($key = array_search(trim(str_replace("\n", "", $aval)), $fieldoptions)) {												
													$newarray[] = $key;
												}
											}
										}
										
										$data[$field -> slug] = maybe_serialize($newarray);
									}
								} elseif ($field -> type == "pre_gender") {
									if (!empty($data[$field -> slug])) {
										$data[$field -> slug] = strtolower($data[$field -> slug]);
									}
								} elseif (!empty($field -> type) && $field -> type == "pre_country") {
									if (!is_numeric($data[$field -> slug])) {
										$countryquery = "SELECT `id` FROM `" . $wpdb -> prefix . $wpmlCountry -> table . "` WHERE `value` = '" . esc_sql($data[$field -> slug]) . "'";
										if ($country_id = $wpdb -> get_var($countryquery)) {										
											$data[$field -> slug] = $country_id;
										}
									}
								} else {
									if (is_array($data[$field -> slug])) {
										$data[$field -> slug] = serialize($data[$field -> slug]);
									}
								}
								
								$query1 .= "`" . $field -> slug . "`, ";
								$query2 .= "'" . esc_sql($data[$field -> slug]) . "', ";
							}
						
							$usedfields[] = $field -> slug;
						}
					}
				}
				
				foreach (array_keys($this -> table_fields) as $field) {
					$query1 .= "`" . $field . "`";
					$query2 .= "'" . esc_sql(${$field}) . "'";
					
					if ($c < count($this -> table_fields)) {
						$query1 .= ", ";
						$query2 .= ", ";
					}
					
					$c++;
				}
				
				$query1 .= ") VALUES (";
				$query = $query1 . $query2 . ");";
			}
			
			if (empty($return_query) || $return_query == false) {				
				if ($wpdb -> query($query)) {
					$this -> insertid = $subscriber_id = (empty($id)) ? $wpdb -> insert_id : $id;
					
					$unsubscribe_delete_query = "DELETE FROM " . $wpdb -> prefix . $Unsubscribe -> table . " WHERE `email` = '" . $data['email'] . "'";
					$wpdb -> query($unsubscribe_delete_query);
					$bounce_delete_query = "DELETE FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE `email` = '" . $data['email'] . "'";
					$wpdb -> query($bounce_delete_query);
					
					/* Mailing list associations */
					if (!empty($mailinglists)) {
						$oldactive = $active;
														
						foreach ($mailinglists as $key => $list_id) {					
							$mailinglist = $Mailinglist -> get($list_id);
							$paid = ($mailinglist -> paid == "Y") ? 'Y' : 'N';
							if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") { $active = "N"; }
							$sl_data = array('SubscribersList' => array('subscriber_id' => $this -> insertid, 'list_id' => $list_id, 'active' => $active, 'paid' => $paid));
							$SubscribersList -> save($sl_data);
							
							$active = $oldactive;
						}
					}
					
					/* Subscriber register? */
					if ($this -> get_option('subscriberegister') == "Y") {
						$username = $email;
						$password = wp_generate_password(12);
						
						if ($user_id = username_exists($username)) {
							//do nothing, we have the user ID
						} elseif ($user_id = email_exists($email)) {
							//do nothing, we have the user ID
						} else {
							if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
								$wpuser = new WP_User($user_id);
								$wpuser -> set_role("subscriber");
								wp_new_user_notification($user_id, $password);
							}
						}
						
						$subscriberquery = "UPDATE `" . $wpdb -> prefix . $this -> table . "` SET `registered` = 'Y', `user_id` = '" . $user_id . "' WHERE `id` = '" . $subscriber_id . "'";
						$wpdb -> query($subscriberquery);
					}
					
					if (empty($_POST['preventautoresponders'])) {
						if (!empty($mailinglists)) {
							foreach ($mailinglists as $mkey => $mval) {
								$subscriber = $this -> get($subscriber_id, false);
								$this -> gen_auth($subscriber -> id);
								$mailinglist = $Mailinglist -> get($mval, false);
								$this -> autoresponders_send($subscriber, $mailinglist);
							}
						}
					}
					
					do_action($this -> pre . '_subscriber_saved', $this -> insertid, $data);
					
					return true;
				}
			} else {
				return $query;	
			}
		}
		
		return false;
	}
	
	/**
	 * Saves the value of the single field in the "subscribers" table.
	 * @param STR The name of the field to save to.
	 * @param STR The value to write to the field mentioned above
	 * @param INT The ID of the record to update.
	 * @return BOOL Returns true if the procedure was successful
	 *
	 **/
	function save_field($field = null, $value = null, $id = null) {
		global $wpdb;
		
		$subscriber_id = (empty($id)) ? $this -> id : $id;
	
		if (!empty($field) && !empty($value)) {
			if ($wpdb -> query("UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `" . $field . "` = '" . $value . "' WHERE `id` = '" . $subscriber_id . "'")) {
				return true;
			}
		}
		
		return false;
	}
	
	function find($conditions = array(), $fields = false) {
		global $wpdb;
		
		if (!empty($fields)) {
			$f = 1;
			$newfields = "";
		
			foreach ($fields as $field) {
				$newfields .= " `" . $field . "`";
				
				if ($f < count($fields)) {
					$newfields .= ", ";
				}
				
				$f++;
			}
		} else {
			$newfields = "*";
		}
		
		$query = "SELECT " . $newfields . " FROM `" . $wpdb -> prefix . "" . $this -> table . "`";
		
		if (!empty($conditions)) {
			$c = 1;
			$query .= " WHERE";
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " `" . $ckey . "` = '" . $cval . "'";
			
				if ($c < count($conditions)) {
					$query .= " AND";
				}
				
				$c++;
			}
		}
		
		if ($subscriber = $wpdb -> get_row($query)) {
			if (!empty($subscriber)) {
				$subscriber = $this -> init_class($this -> model, $subscriber);
				
				return $subscriber;
			}
		}
		
		return false;
	}
	
	function get_by_email($email = null) {
		global $wpdb;
		
		if (!empty($email)) {
			if ($subscribers = $wpdb -> get_results("SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . $email . "'")) {
				if (!empty($subscribers)) {
					$data = array();
					
					foreach ($subscribers as $subscriber) {
						$data[] = $this -> init_class($this -> model, $subscriber);
					}
					
					return $data;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Fetches a Wordpress user by email address
	 * @param STRING. The email to execute the query with.
	 * @return BOOLEAN/OBJ
	 *
	 */
	function get_user_by_email($email = null) {
		if ($user_id = $this -> check_registration($email)) {
			if ($userdata = $this -> userdata($user_id)) {
				return $userdata;
			}
		}
		
		return false;
	}

	
	/**
	 * Activates a subscriber by ID and email address
	 * Simply updates the `active` field and sets it to "Y"
	 * @param INT. The ID of the subscriber
	 * @param STRING. The email address of the subscriber
	 * @return BOOLEAN
	 *
	 */
	function activate($id = null, $email = null) {
		global $wpdb;
		
		if (!empty($id) && !empty($email)) {
			if ($subscriber = $this -> get($id)) {
				if ($subscriber -> active == "N") {
					if ($wpdb -> query("UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `active` = 'Y' WHERE `id` = '" . $id . "' AND `email` = '" . $email . "' LIMIT 1")) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes a single subscriber record from the database
	 * @param INT. The ID of the subscriber
	 * @return BOOLEAN
	 *
	 */
	function delete($subscriber_id = null) {
		global $wpdb, $Autoresponderemail, $wpmlOrder, $Queue, $SubscribersList;
		
		if (!empty($subscriber_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $subscriber_id . "' LIMIT 1")) {
				$wpmlOrder -> delete_all(array('subscriber_id' => $subscriber_id));
				$SubscribersList -> delete_all(array('subscriber_id' => $subscriber_id));
				$Queue -> delete_all(array('subscriber_id' => $subscriber_id));
				
				$wpdb -> query("DELETE FROM " . $wpdb -> prefix . $Autoresponderemail -> table . " WHERE `subscriber_id` = '" . $subscriber_id . "'");
				return true;
			}
		}
		
		return false;
	}
	
	function delete_by_list($list_id = null) {
		global $wpdb;
	
		if (!empty($list_id)) {
			$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . $list_id . "'";
			
			if ($wpdb -> query($query)) {
				return true;
			}
		}
		
		return false;
	}
	
	function delete_by_email($email = null) {
		global $wpdb;
		
		if (!empty($email)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . $email . "'")) {
				return true;
			}
		}
		
		return false;
	}
	
	function unsubscribe($id = null, $email = null) {
		global $wpdb;
		
		if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $id . "' AND `email` = '" . $email . "' LIMIT 1")) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Removes several subscriber records by an array of IDs
	 * @param ARRAY. An array of subscriber IDs
	 * @return BOOLEAN.
	 *
	 */
	function delete_array($subscribers = array()) {
		global $wpdb;
		
		if (!empty($subscribers)) {
			foreach ($subscribers as $subscriber) {
				$this -> delete($subscriber);
			}
			
			return true;
		}
		
		return false;
	}
}

include_once(dirname(__FILE__) . DS . 'newsletter.php');

?>