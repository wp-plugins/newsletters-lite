<?php

class wpmlDbHelper extends wpMailPlugin {

	var $name = "Db";	
	var $model = '';
	var $errors = array();
	
	function wpmlDbHelper() {
		return true;
	}
	
	function getLock($lockName) {
		global $wpdb;
	    $query = "SELECT GET_LOCK('" . $lockName . "', 0)";
	    $result = $wpdb -> get_var($query);
	    return $result;
	}
	
	function releaseLock($lockName) {
		global $wpdb;
	    $query = "SELECT RELEASE_LOCK('" . $lockName . "')";
	    $wpdb -> query($query);
	}

	
	function save($data = array(), $validate = true) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};

			if (!empty($data)) {
				$defaults = array();
				
				if (method_exists($object, 'defaults')) {
					$defaults = $object -> defaults();
				}
			
				$data = (empty($data[$object -> model])) ? $data : $data[$object -> model];
				$r = wp_parse_args($data, $defaults);
				$object -> data = (object) $r;
				extract($r, EXTR_SKIP);
			
				if ($validate == true) {
					if (method_exists($object, 'validate')) {
						$object -> validate($data);
					}
				}
				
				if ($object -> model == "Autoresponder") {
					$object -> data -> nnewsletter['content'] = $_POST['content'];	
				}
				
				if (empty($object -> errors)) {
					switch ($object -> model) {
						case 'Theme'			:
							$object -> data -> content = mysql_real_escape_string($object -> data -> content);
							break;
					}
					
					$query = (empty($id)) ? $this -> iquery($object -> model) : $this -> uquery($object -> model);
					
					if ($wpdb -> query($query)) {
						$object -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
						$oldmodel = $object -> model;
						
						switch ($object -> model) {
							case 'Theme'					:
								$themeoptions = array(
									'pronews_address',
									'pronews_facebook',
									'pronews_twitter',
									'pronews_rss',
									'themailer_address',
									'themailer_facebook',
									'themailer_twitter',
									'themailer_rss',
								);
								
								foreach ($_POST as $pkey => $pval) {
									if (!empty($pkey) && in_array($pkey, $themeoptions)) {
										$this -> update_option($pkey, $_POST[$pkey]);
									}
								}
								break;
							case 'Autoresponder'			:															
								global $Html, $Autoresponder, $AutorespondersList, $History,
								$Subscriber, $SubscribersList, $Autoresponderemail;
							
								/* Create the History email if needed */
								if (!empty($object -> data -> newsletter) && $object -> data -> newsletter == "new") {									
									$history_data = array(
										'subject'			=>	$object -> data -> nnewsletter['subject'],
										'message'			=>	$object -> data -> nnewsletter['content'],
										'theme_id'			=>	$object -> data -> nnewsletter['theme_id'],
										'mailinglists'		=>	$object -> data -> lists,
									);
									
									if ($History -> save($history_data, true)) {
										$history_id = $History -> insertid;
										
										$this -> model = $Autoresponder -> model;
										$this -> save_field('history_id', $history_id, array('id' => ${$oldmodel} -> insertid));
									}
								}
								
								/* Do the Autoresponder > List associations */
								if (!empty(${$oldmodel} -> insertid)) {
									$this -> model = $AutorespondersList -> model;								
									$this -> delete_all(array('autoresponder_id' => ${$oldmodel} -> insertid));
									$listsquery = "";
									$l = 1;
									
									foreach (${$oldmodel} -> data -> lists as $list_id) {
										$listsquery .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $list_id . "'";
										if (count(${$oldmodel} -> data -> lists) > $l) { $listsquery .= " OR "; }
										
										$autoresponderslist_data = array(
											'autoresponder_id'	=>	${$oldmodel} -> insertid,
											'list_id'			=>	$list_id,
										);
										
										$this -> model = $AutorespondersList -> model;
										$this -> save($autoresponderslist_data, true);	
										$l++;
									}
								}
								
								if (${$oldmodel} -> data -> applyexisting == "Y" && ${$oldmodel} -> data -> status == "active") {
									$senddate = date_i18n("Y-m-d H:i:s", strtotime("+ " . ${$oldmodel} -> data -> delay . " " . ${$oldmodel} -> data -> delayinterval));
									
									$query1 = "SELECT DISTINCT " 
									. $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id as sid, "
									. $wpdb -> prefix . $AutorespondersList -> table . ".list_id, "
									. $wpdb -> prefix . $AutorespondersList -> table . ".autoresponder_id, "
									. "'" . $Html -> gen_date() . "', '" . $Html -> gen_date() . "', '" . $senddate . "' FROM " 
									. $wpdb -> prefix . $SubscribersList -> table . " LEFT JOIN "
									. $wpdb -> prefix . $AutorespondersList -> table . " ON ("
									. $wpdb -> prefix . $SubscribersList -> table . ".list_id = "
									. $wpdb -> prefix . $AutorespondersList -> table . ".list_id)"
									. " WHERE (" . $listsquery . ") AND "
									. $wpdb -> prefix . $AutorespondersList -> table . ".autoresponder_id = '" . ${$oldmodel} -> insertid . "'";
									
									$query2 = "INSERT INTO " . $wpdb -> prefix . $Autoresponderemail -> table 
									. " (subscriber_id, list_id, autoresponder_id, created, modified, senddate) (" . $query1 . ")";
									
									$wpdb -> query($query2);
								}
								break;	
						}
						
						$this -> model = $oldmodel;
						return true;
					}
				} else {
					$this -> model = $oldmodel;	
				}
			}
		}
		
		return false;
	}
	
	function save_field($field = null, $value = null, $conditions = array()) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};

			if (!empty($field)) {			
				$query = "UPDATE `" . $wpdb -> prefix . "" . $object -> table . "` SET `" . $field . "` = '" . $value . "'";
				
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
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function iquery($model = null) {	
		if (!empty($model)) {
			global $wpdb, ${$model};
			
			$object = (!is_object(${$model})) ? $this : ${$model};
			
			if (!empty($object -> data)) {
				$data = $object -> data;
			
				if (empty($data -> id)) {
					if (!empty($object -> fields)) {
						$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $object -> table . "` (";
						$query2 = "";
						$c = 1;
						
						unset($object -> fields['key']);
						unset($object -> fields['id']);
						
						foreach (array_keys($object -> fields) as $field) {
							if (!empty($data -> {$field})) {
								$query1 .= "`" . $field . "`";
								$query2 .= "'" . $data -> {$field} . "'";
								
								if ($c < count($object -> fields)) {
									$query1 .= ", ";
									$query2 .= ", ";
								}
							}
							
							$c++;
						}
						
						$query1 .= ") VALUES (";
						$query = $query1 . $query2 . ");";
						
						return $query;
					}
				} else {
					$query = $this -> uquery($model);
					return $query;
				}
			}
		}
		
		return false;
	}
	
	function uquery($model = null) {
		global $wpdb, ${$model};
		
		$object = (!is_object(${$model})) ? $this : ${$model};
		
		if (!empty($model)) {
			$data = $object -> data;
			
			if (!empty($data -> id)) {
				if (!empty($object -> fields)) {					
					$query = "UPDATE `" . $wpdb -> prefix . "" . $object -> table . "` SET ";
					
					$c = 1;
					unset($object -> fields['key']);
					
					foreach (array_keys($object -> fields) as $field) {						
						if (!empty($data -> {$field}) || $data -> {$field} == "0") {
							$query .= "`" . $field . "` = '" . $data -> {$field} . "'";
					
							if ($c < count($object -> fields)) {
								$query .= ", ";
							}	
						}
						
						$c++;
					}
					
					$query .= " WHERE `id` = '" . $data -> id . "';";
					return $query;
				}
			} else {
				$query = $this -> iquery($model);
				return $query;
			}
		}
		
		return false;
	}
	
	function field($field = null, $conditions = array()) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			if (!empty($conditions) && is_array($conditions)) {
				$query = "SELECT `" . $field . "` FROM `" . $wpdb -> prefix . "" . ${$this -> model} -> table . "` WHERE";
				$c = 1;
				
				foreach ($conditions as $ckey => $cval) {
					$query .= " `" . $ckey . "` = '" . $cval . "'";
					
					if ($c < count($conditions)) {
						$query .= " AND";
					}
					
					$c++;
				}
				
				$query_hash = md5($query);
				global ${'newsletters_query_' . $query_hash};
				if (!empty(${'newsletters_query_' . $query_hash})) {
					return ${'newsletters_query_' . $query_hash};
				}
				
				if ($value = $wpdb -> get_var($query)) {
					${'newsletters_query_' . $query_hash} = $value;
					return $value;
				}
			}
		}
		
		return false;
	}
	
	function delete($record_id = null) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};
		
			if (!empty($record_id)) {
				$query = "DELETE FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE `id` = '" . $record_id . "' LIMIT 1";
				
				if ($wpdb -> query($query)) {
					switch ($this -> model) {
						case 'Link'					:
							global $wpmlClick;
							$wpmlClick -> delete_all(array('link_id' => $record_id));
							break;
						case 'Subscriber'			:
							//global variables
							global $Autoresponderemail, $AutorespondersList, $Email, $wpmlOrder, $Queue, $SubscribersList;
						
							//remove all Orders
							$this -> model = $wpmlOrder -> model;
							$this -> delete_all(array('subscriber_id' => $record_id));
							
							//remove all List associations
							$this -> model = $SubscribersList -> model;
							$this -> delete_all(array('subscriber_id' => $record_id));
							
							//remove all queued emails
							$this -> model = $Queue -> model;
							$this -> delete_all(array('subscriber_id' => $record_id));

                            //remove all emails
                            $this -> model = $Email -> model;
                            $this -> delete_all(array('subscriber_id' => $record_id));
                            
                            //remove all autoresponder emails
                            $this -> model = $Autoresponderemail -> model;
                            $this -> delete_all(array('subscriber_id' => $record_id));
							return true;
							break;
						case 'Mailinglist'			:
							global $AutorespondersList;
						
							$this -> model = 'HistoriesList';
							$this -> delete_all(array('list_id' => $record_id));
							
							/* Remove the Autoresponder/List associations */
							$this -> model = $AutorespondersList -> model;
							$this -> delete_all(array('list_id' => $record_id));
							
							$this -> model = $Autoresponderemail -> model;
							$this -> delete_all(array('list_id' => $record_id));
							break;
						case 'History'				:
							global $Queue, $Email, $Autoresponder;
						
							$this -> model = 'HistoriesList';
							$this -> delete_all(array('history_id' => $record_id));
							
							/* Remove autoresponders associated */
							$this -> model = $Autoresponder -> model;
							$this -> delete_all(array('history_id' => $record_id));
							
							///remove all queued emails
							$this -> model = $Queue -> model;
							$this -> delete_all(array('history_id' => $record_id));

                            //remove all emails
                            $this -> model = $Email -> model;
                            $this -> delete_all(array('history_id' => $record_id));
							break;
						case 'Autoresponder'		:
							global $AutorespondersList, $Autoresponderemail;
							$oldmodel = $this -> model;
							
							//remove the AutorespondersList associations
							$this -> model = $AutorespondersList -> model;
							$this -> delete_all(array('autoresponder_id' => $record_id));
							
							//remove the Autoresponderemail records
							$this -> model = $Autoresponderemail -> model;
							$this -> delete_all(array('autoresponder_id' => $record_id));
							
							$this -> model = $oldmodel;
							break;
					}
				
					return true;
				}
			}
		}
		
		return false;
	}
	
	function delete_all($conditions = array()) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};
			
			if (!empty($conditions) && is_array($conditions)) {
				$query = "DELETE FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE";
				$c = 1;
				
				foreach ($conditions as $ckey => $cval) {
					$query .= " `" . $ckey . "` = '" . $cval . "'";
					
					if ($c < count($conditions)) {
						$query .= " AND";
					}
					
					$c++;
				}
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function count($conditions = array()) {
		$count = 0;
	
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};
			
			$query = "SELECT COUNT(*) FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
			
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
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				return ${'newsletters_query_' . $query_hash};
			}
			
			if (!empty($query)) {
				$count = $wpdb -> get_var($query);
				${'newsletters_query_' . $query_hash} = $count;
			}
		}
		
		return $count;
	}
	
	function find($conditions = array(), $fields = false, $order = array('modified', "DESC"), $assign = true, $recursive = true, $cache = true) {
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};
			
			if (empty($object -> table)) {
				return false;
			}
			
			$fields = (empty($fields)) ? "*" : implode(", ", $fields);
			$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
			
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
			
			$order = (empty($order)) ? array('modified', "DESC") : $order;
			list($ofield, $odir) = $order;
			$query .= " ORDER BY `" . $ofield . "` " . $odir . "";
			$query .= " LIMIT 1";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if ($cache == true && !empty(${'newsletters_query_' . $query_hash})) {
				return ${'newsletters_query_' . $query_hash};
			}
			
			if ($record = $wpdb -> get_row($query)) {
				if (!empty($record)) {				
					$record -> recursive = ((!empty($recursive) && $recursive == true) ? 1 : 0);
					$data = $this -> init_class($object -> model, $record);
					
					if ($assign == true) {
						$object -> data = $data;
					}
					
					${'newsletters_query_' . $query_hash} = $data;
					return $data;
				}
			}
		}
		
		return false;
	}
	
	function find_all($conditions = array(), $fields = false, $order = array('modified', "DESC"), $limit = false, $recursive = false) {	
		if (!empty($this -> model)) {
			global $wpdb, ${$this -> model};
			
			$object = (!is_object(${$this -> model})) ? $this : ${$this -> model};
			$fields = (empty($fields) || !is_array($fields)) ? "*" : implode(", ", $fields);
			$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
			
			if (!empty($conditions) && is_array($conditions)) {
				$query .= " WHERE";
				$c = 1;
				
				foreach ($conditions as $ckey => $cval) {				
					if (preg_match("/[>]\s?(.*)?/si", $cval, $cmatches)) {
						if (!empty($cmatches[1]) || $cmatches[1] == "0") {					
							$query .= " `" . $ckey . "` > " . $cmatches[1] . "";
						}
					} elseif (preg_match("/[<]\s?(.*)?/si", $cval, $cmatches)) {
						if (!empty($cmatches[1]) || $cmatches[1] == "0") {
							$query .= " `" . $ckey . "` < " . $cmatches[1] . "";	
						}
					} else {				
						$query .= " " . $ckey . " = " . $cval . "";
					}
					
					if ($c < count($conditions)) {
						$query .= " AND";
					}
					
					$c++;
				}
			}
			
			$order = (empty($order)) ? array('modified', "DESC") : $order;
			list($ofield, $odir) = $order;
			$query .= " ORDER BY `" . $ofield . "` " . $odir . "";
			$query .= (empty($limit)) ? '' : " LIMIT " . $limit . "";
			
			$query_hash = md5($query);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				return ${'newsletters_query_' . $query_hash};
			}
			
			if ($records = $wpdb -> get_results($query)) {
				if (!empty($records)) {
					$data = array();
					
					foreach ($records as $record) {
						if ((!empty($recursive) && $recursive == true) || $object -> recursive == true) { $record -> recursive = true; }
						$data[] = $this -> init_class($object -> model, $record);
					}
					
					${'newsletters_query_' . $query_hash} = $data;
					return $data;
				}
			}
		}
		
		return false;
	}
}

?>