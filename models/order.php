<?php

class wpmlOrder extends wpMailPlugin {
	
	var $name = 'wpmlorder';
	var $controller = 'orders';
	var $model = 'wpmlOrder';
	var $table = '';
	var $table_name = 'wpmlorders';

	/**
	 * The ID of the order record
	 *
	 **/
	var $id = '';
	
	/**
	 * The ID of the subscriber whom submitted the order
	 *
	 **/
	var $subscriber_id = '';
	
	/**
	 * Total amount for the order
	 *
	 **/
	var $amount = 0;
	
	/**
	 * Indicates paid status.
	 * "Y" represents that the order has been paid
	 *
	 **/
	var $completed = 'N';
	var $product_id = 0;
	var $order_number = 0;
	var $created = '0000-00-00 00:00:00';
	var $modified = '0000-00-00 00:00:00';
	var $errors = array();
	var $data = array();
	var $insert_id = 0;
	
	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'subscriber_id'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'list_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'completed'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'amount'		=>	"FLOAT NOT NULL DEFAULT '0.00'",
		'product_id'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'order_number'	=>	"INT(11) NOT NULL DEFAULT '0'",
		'pmethod'		=>	"ENUM('pp','2co') NOT NULL DEFAULT 'pp'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`subscriber_id`), INDEX(`list_id`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'subscriber_id'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'list_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'completed'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'amount'		=>	array("FLOAT", "NOT NULL DEFAULT '0.00'"),
		'product_id'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'order_number'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'pmethod'		=>	array("ENUM('pp','2co')", "NOT NULL DEFAULT 'pp'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`subscriber_id`), INDEX(`list_id`)"					   
	);
	
	var $indexes = array('subscriber_id', 'list_id');

	function wpmlOrder($data = array()) {
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = $val;
			}
		}
	}
	
	/**
	 * Retrieves a single Order record with an ID condition
	 * @param INT The ID of the Order record to get.
	 * @return OBJ An Order object with the values of the order.
	 *
	 **/
	function get($order_id = null) {
		global $wpdb;
		
		//make sure an order ID is available
		if (!empty($order_id)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "wpmlorders` WHERE `id` = '" . $order_id . "' LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_order = $this -> get_cache($query_hash)) {
				return $ob_order;
			}
		
			if ($order = $wpdb -> get_row($query)) {
				$order = $this -> init_class($this -> model, $order);
				$this -> set_cache($query_hash, $order);
				return $order;
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve all orders in a paginated fashion
	 * @param $conditions ARRAY conditions passed on to the pagination class.
	 * @return $data ARRAY an array of order objects retrieved from the database
	 *
	 **/
	function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-orders', $perpage = 15, $order = array('modified', "DESC")) {
		global $wpdb;
		
		$paginate = new wpMailPaginate($wpdb -> prefix . $this -> table_name, '*', $sub, $sub);
		$paginate -> where = (empty($conditions)) ? false : $conditions;
		$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
		$paginate -> per_page = $perpage;
		$paginate -> order = $order;
		$orders = $paginate -> start_paging($_GET['wpmlpage']);
		
		$data = array();
		$data['Pagination'] = $paginate;
		
		if (!empty($orders)) {
			foreach ($orders as $order) {
				$data[$this -> model][] = $this -> init_class($this -> model, $order);
			}
		}
		
		return $data;
	}
	
	function save($data = array(), $validate = true) {
		global $wpdb;
		
		if (!empty($data)) {
			if ($validate == true) {
				if (empty($data['subscriber_id'])) { $this -> errors[] = __('No subscriber specified for this order', $this -> plugin_name); }
				if (empty($data['list_id'])) { $this -> errors[] = __('No mailing list was specified', $this -> plugin_name); }
				if (empty($data['completed'])) { $this -> errors[] = __('Please specify completed status for this order', $this -> plugin_name); }
				if (empty($data['amount'])) { $this -> errors[] = __('Please specify an amount for this order', $this -> plugin_name); }
				if (empty($data['product_id'])) { $this -> errors[] = __('Please specify a product ID for this order', $this -> plugin_name); }
				if (empty($data['order_number'])) { $this -> errors[] = __('Please specify an order number for this order', $this -> plugin_name); }
			} else {
				$this -> errors = false;
			}
			
			if (empty($this -> errors)) {
				$nowdate = $this -> gen_date();
			
				if (empty($data['id'])) {
					$query = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`subscriber_id`, `list_id`, `completed`, `amount`, `product_id`, `order_number`, `pmethod`, `created`, `modified`)
					VALUES ('" . $data['subscriber_id'] . "', '" . $data['list_id'] . "', '" . $data['completed'] . "', '" . $data['amount'] . "', '" . $data['product_id'] . "', '" . $data['order_number'] . "', '" . $data['pmethod'] . "', '" . $nowdate . "', '" . $nowdate . "');";
				} else {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `subscriber_id` = '" . $data['subscriber_id'] . "', `list_id` = '" . $data['list_id'] . "', `completed` = '" . $data['completed'] . "', `amount` = '" . $data['amount'] . "', `product_id` = '" . $data['product_id'] . "', `order_number` = '" . $data['order_number'] . "', `pmethod` = '" . $data['pmethod'] . "', `modified` = '" . $nowdate . "' WHERE `id` = '" . $data['id'] . "';";
				}
				
				if ($wpdb -> query($query)) {
					$this -> insertid = (empty($data['id'])) ? $wpdb -> insert_id : $data['id'];					
					return true;
				}
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
	
	/**
	 * Removes a single Order record with an ID condition
	 * @param INT The ID of the order record.
	 * @return BOOLEAN Either returns true or false
	 *
	 **/
	function delete($order_id = null) {
		global $wpdb, $Subscriber, $SubscribersList;
		
		if (!empty($order_id) && $order = $this -> get($order_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . $order_id . "'")) {
				$SubscribersList -> delete_all(array('subscriber_id' => $order -> subscriber_id, 'list_id' => $order -> list_id));
				return true;
			} else {
				$this -> errors[] = __('Order could not be removed', $this -> plugin_name);
				return false;
			}
		} else {
			$this -> errors[] = __('No order ID was specified', $this -> plugin_name);
			return false;
		}
	}
	
	function delete_by_subscriber($subscriber_id = null) {
		global $wpdb;
		
		if (!empty($subscriber_id)) {
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "wpmlorders` WHERE `subscriber_id` = '" . $subscriber_id . "'")) {
				return true;
			} else {
				$this -> errors[] = __('No order records were removed', $this -> plugin_name);
				return false;
			}
		} else {
			$this -> errors[] = __('No subscriber ID was specified for deleting orders', $this -> plugin_name);
			return false;
		}
	}
}

?>