<?php

class wpmlpaginate extends wpMailPlugin {
	
	/**
	 * DB table name to paginate on
	 *
	 */
	var $table = '';
	var $url_page = "";
	
	/**
	 * Fields for SELECT query
	 * Only these fields will be fetched.
	 * Use asterix for all available fields
	 *
	 */
	var $fields = '*';
	
	/**
	 * Current page
	 *
	 */
	var $page = 1;
	
	/**
	 * Records to show per page
	 *
	 */
	var $per_page = 10;
	
	var $order = array('modified', "DESC");
	
	/**
	 * WHERE conditions
	 * This should be an array
	 *
	 */
	var $where = '';
	
	var $plugin_url = '';
	var $sub = '';
	var $parent = '';
	
	var $allRecords = array();
	
	var $pagination = '';
	
	function wpmlpaginate($table = null, $fields = "*", $sub = null, $parent = null) {	
		$this -> sub = $sub;
		$this -> parentd = $parent;
	
		if (!empty($table)) {
			$this -> table = $table;
		}
		
		if (!empty($fields)) {
			$this -> fields = $fields;
		}
	}
	
	function start_paging($page = null) {
		global $wpdb, $Html, $Subscriber, $wpmlClick, $Bounce, $Email, $SubscribersList, $History, $HistoriesList, $Autoresponder, $AutorespondersList;
	
		$page = (empty($page)) ? 1 : $page;
		
		$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
		$emails_table = $wpdb -> prefix . $Email -> table;
		$clicks_table = $wpdb -> prefix . $wpmlClick -> table;
		$bounces_table = $wpdb -> prefix . $Bounce -> table;
	
		if (!empty($page)) {
			$this -> page = $page;
		}
		
			$query = "SELECT " . $this -> fields . " FROM `" . $this -> table . "`";
			$countquery = "SELECT COUNT(*) FROM `" . $this -> table . "`";
		
		switch ($this -> model) {
			case 'Email'						:
				$query = "SELECT DISTINCT " . $this -> table . ".* FROM `" . $this -> table . "`";
				$countquery = "SELECT COUNT(DISTINCT " . $this -> table . ".id) FROM `" . $this -> table . "`";
			
				$query .= " LEFT JOIN " . $subscribers_table . " ON " . $emails_table . ".subscriber_id = " . $subscribers_table . ".id";
				$countquery .= " LEFT JOIN " . $subscribers_table . " ON " . $emails_table . ".subscriber_id = " . $subscribers_table . ".id";
				
				if (!empty($this -> order[0]) && $this -> order[0] == "clicked") {
					$this -> order[0] = $clicks_table . ".subscriber_id";
					$query .= " LEFT JOIN " . $clicks_table . " ON " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id";
					$countquery .= " LEFT JOIN " . $clicks_table . " ON " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id";
				}
				break;
			case 'Subscriber'					:
			
				break;
			case 'SubscribersList'				:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $Subscriber -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . $Subscriber -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $Subscriber -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . $Subscriber -> table . ".id";	
				break;
			case 'HistoriesList'				:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $History -> table . " ON " . $wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . $History -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $History -> table . " ON " . $wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . $History -> table . ".id";	
				break;
			case 'AutorespondersList'			:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $Autoresponder -> table . " ON " . $wpdb -> prefix . $AutorespondersList -> table . ".autoresponder_id = " . $wpdb -> prefix . $Autoresponder -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $Autoresponder -> table . " ON " . $wpdb -> prefix . $AutorespondersList -> table . ".autoresponder_id = " . $wpdb -> prefix . $Autoresponder -> table . ".id";	
				break;
		}
		
		$didwhere = false;
		
		if (!empty($this -> where_and['clicked'])) {
			$didwhere = true;
			$clicked = $this -> where_and['clicked'];
			unset($this -> where_and['clicked']);
			$click_cond = ($clicked == "Y") ? "IN" : "NOT IN";
			
			$query .= " WHERE " . $emails_table . ".subscriber_id " . $click_cond . " 
			(SELECT " . $clicks_table . ".subscriber_id FROM " . $clicks_table . "";
			
			$countquery .= " WHERE " . $emails_table . ".subscriber_id " . $click_cond . " 
			(SELECT " . $clicks_table . ".subscriber_id FROM " . $clicks_table . "";
			
			if (!empty($this -> where[$emails_table . '.history_id'])) {
				$query .= " WHERE " . $clicks_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
				$countquery .= " WHERE " . $clicks_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
			}
			
			$query .= ")";
			$countquery .= ")";
		}
		
		if (!empty($this -> where_and['bounced'])) {
			$bounced = $this -> where_and['bounced'];
			unset($this -> where_and['bounced']);
			$bounce_cond = ($bounced == "Y") ? "IN" : "NOT IN";
			
			if (!$didwhere) {
				$query .= " WHERE";
				$countquery .= " WHERE";
			} else {
				$query .= " AND";
				$countquery .= " AND";
			}
			
			$query .= " " . $emails_table . ".subscriber_id " . $bounce_cond . " 
			(SELECT " . $subscribers_table . ".id FROM " . $subscribers_table . " LEFT JOIN " . 
			$bounces_table . " ON " . $subscribers_table . ".email = " . $bounces_table . ".email";
			
			$countquery .= " " . $emails_table . ".subscriber_id " . $bounce_cond . " 
			(SELECT " . $subscribers_table . ".id FROM " . $subscribers_table . " LEFT JOIN " . 
			$bounces_table . " ON " . $subscribers_table . ".email = " . $bounces_table . ".email";
			
			if (!empty($this -> where[$emails_table . '.history_id'])) {
				$query .= " WHERE " . $bounces_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
				$countquery .= " WHERE " . $bounces_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
			}
			
			$query .= ")";
			$countquery .= ")";
			$didwhere = true;
		}
		
		if (!empty($this -> where)) {
			if (!$didwhere) {
				$didwhere = true;
				$query .= " WHERE (";
				$countquery .= " WHERE (";
			} else {
				$query .= " AND (";
				$countquery .= " AND (";
			}
				
			$c = 1;
			
			foreach ($this -> where as $key => $val) {
				if (preg_match("/(LIKE)/si", $val)) {
					$query .= " " . $key . " " . $val . "";	
					$countquery .= " " . $key . " " . $val . "";
				} elseif (preg_match("/(NOT IN)/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val . "";
				} else {
					$query .= " " . $key . " = '" . $val . "'";
					$countquery .= " " . $key . " = '" . $val . "'";
				}
				
				if ($c < count($this -> where)) {
					$query .= " OR";
					$countquery .= " OR";
				}
				
				$c++;
			}
			
			$query .= ")";
			$countquery .= ")";
		}
		
		if (!empty($this -> where_and)) {
			if (!$didwhere) {
				$query .= " WHERE";
				$countquery .= " WHERE";
			} else {
				$query .= " AND";
				$countquery .= " AND";
			}
		
			$a = 1;
		
			foreach ($this -> where_and as $key => $val) {
				if (preg_match("/(NOT IN)/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val . "";
				} else {
					$query .= " " . $key . " = '" . $val . "'";
					$countquery .= " " . $key . " = '" . $val . "'";
				}
				
				if ($a < count($this -> where_and)) {
					$query .= " AND";
					$countquery .= " AND";
				}
				
				$a++;
			}
		}
		
		$r = 1;
		
		if ($this -> page > 1) {
			$begRecord = (($this -> page * $this -> per_page) - ($this -> per_page));
		} else {
			$begRecord = 0;
		}
		
		switch ($this -> model) {
			//case 'Email'					:
			case 'SubscribersList'			:
				$query .= " GROUP BY " . $this -> table . ".subscriber_id";
				break;
		}
			
		$endRecord = $begRecord + $this -> per_page;
		list($ofield, $odir) = $this -> order;
		switch ($this -> model) {
			case 'Email'					:
				if ($ofield == $clicks_table . ".subscriber_id" && $odir == "DESC") {
					$query .= " ORDER BY IF (" . $ofield . " != '' OR " . $ofield . " IS NOT NULL,1,0), " . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> per_page . ";";
				} else {
					$query .= " ORDER BY IF (" . $ofield . " = '' OR " . $ofield . " IS NULL,1,0), " . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> per_page . ";";
				}
				
				if ($ofield == $clicks_table . ".subscriber_id") {
					$ofield = "clicked";
				} elseif ($ofield == $subscribers_table . ".email") {
					$ofield = "subscriber_id";
				}
				break;
			case 'SubscribersList'			:
				$query .= " ORDER BY IF (" . $subscribers_table . "." . $ofield . " = '' OR " . $subscribers_table . "." . $ofield . " IS NULL,1,0), " . $subscribers_table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> per_page . ";";	
				break;
			default							:
				$query .= " ORDER BY IF (" . $this -> table . "." . $ofield . " = '' OR " . $this -> table . "." . $ofield . " IS NULL,1,0), " . $this -> table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> per_page . ";";	
				break;
		}
		
		$records = $wpdb -> get_results($query);			
		$records_count = count($records);
		$this -> allcount = $allRecordsCount = $count = $wpdb -> get_var($countquery);	
			
		$totalpagescount = ceil($this -> allcount / $this -> per_page);
		
		if (empty($this -> url_page)) {
			$this -> url_page = $this -> sub;	
		}
		
		if (($ofields = explode(".", $ofield)) !== false) {
			if (count($ofields) > 1) {
				$ofield = $ofields[1];
			} else {
				$ofield = $ofields[0];
			}
		}
		
		if (count($records) < $allRecordsCount) {			
			$p = 1;
			$k = 1;
			$n = $this -> page;
			$search = (empty($this -> searchterm)) ? '' : '&' . $this -> pre . 'searchterm=' . urlencode($this -> searchterm);
			$orderby = (empty($ofield)) ? '' : '&orderby=' . $ofield;
			$order = (empty($odir)) ? '' : '&order=' . strtolower($odir);
			$this -> pagination .= '<span class="displaying-num">' . sprintf(__('%s items', $this -> plugin_name), $this -> per_page) . '</span>';
			$this -> pagination .= '<span class="pagination-links">';
			$this -> pagination .= '<a href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=1' . $search . $orderby . $order . $this -> after . '" class="first-page' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '">&laquo;</a>';
			$this -> pagination .= '<a class="prev-page' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '" href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . ($this -> page - 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Previous Page', $this -> plugin_name) . '">&#8249;</a>';
			$this -> pagination .= '<span class="paging-input">';
			$this -> pagination .= '<input class="newsletters-paged-input current-page" type="text" name="paged" id="paged-input" value="' . $this -> page . '" size="1"> ';
			$this -> pagination .= __('of', $this -> plugin_name); 
			$this -> pagination .= ' <span class="total-pages">' . $totalpagescount . '</span>';
			$this -> pagination .= '</span>';
			$this -> pagination .= '<a class="next-page' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '" href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . ($this -> page + 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Next Page', $this -> plugin_name) . '">&#8250;</a>';
			$this -> pagination .= '<a href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . $totalpagescount . $search . $orderby . $order . $this -> after . '" class="last-page' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '">&raquo;</a>';
			$this -> pagination .= '</span>';
			
			ob_start();
			
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.newsletters-paged-input').keypress(function(e) {
					code = (e.keyCode ? e.keyCode : e.which);
		            if (code == 13) {
		            	window.location = '?page=<?php echo $this -> url_page; ?>&<?php echo $this -> pre; ?>page=' + jQuery(this).val() + '<?php echo $search . $orderby . $order . $this -> after; ?>';
		            	e.preventDefault();
		            }
				});
			});
			</script>
			
			<?php
			
			$script = ob_get_clean();
			$this -> pagination .= $script;
		}
		
		return $records;
	}
}

?>