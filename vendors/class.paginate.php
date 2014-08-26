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
	
	function wpmlpaginate($table = null, $fields = null, $sub = null, $parent = null) {	
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
		global $wpdb;
	
		$page = (empty($page)) ? 1 : $page;
	
		if (!empty($page)) {
			$this -> page = $page;
		}
		
		$query = "SELECT " . $this -> fields . " FROM `" . $this -> table . "`";
		$countquery = "SELECT COUNT(*) FROM `" . $this -> table . "`";
		
		if (!empty($this -> where)) {
			$query .= " WHERE";
			$countquery .= " WHERE";
			$c = 1;
			
			foreach ($this -> where as $key => $val) {
				if (preg_match("/(LIKE)/si", $val)) {
					$query .= " `" . $key . "` " . $val . "";	
					$countquery .= " `" . $key . "` " . $val . "";
				} else {
					$query .= " `" . $key . "` = '" . $val . "'";
					$countquery .= " `" . $key . "` = '" . $val . "'";
				}
				
				if ($c < count($this -> where)) {
					$query .= " OR";
					$countquery .= " OR";
				}
				
				$c++;
			}
		}
		
		$r = 1;
		
		if ($this -> page > 1) {
			$begRecord = (($this -> page * $this -> per_page) - ($this -> per_page));
		} else {
			$begRecord = 0;
		}
			
		$endRecord = $begRecord + $this -> per_page;
		list($ofield, $odir) = $this -> order;
		$query .= " ORDER BY IF (`" . $ofield . "` = '' OR `" . $ofield . "` IS NULL,1,0), `" . $ofield . "` " . $odir . " LIMIT " . $begRecord . " , " . $this -> per_page . ";";
		$records = $wpdb -> get_results($query);	
		$records_count = count($records);
		$this -> allcount = $allRecordsCount = $count = $wpdb -> get_var($countquery);		
		$totalpagescount = ceil($this -> allcount / $this -> per_page);
		
		if (empty($this -> url_page)) {
			$this -> url_page = $this -> sub;	
		}
		
		if (count($records) < $allRecordsCount) {			
			$p = 1;
			$k = 1;
			$n = $this -> page;
			$search = (empty($this -> searchterm)) ? '' : '&' . $this -> pre . 'searchterm=' . urlencode($this -> searchterm);
			$orderby = (empty($ofield)) ? '' : '&orderby=' . $ofield;
			$order = (empty($odir)) ? '' : '&order=' . strtolower($odir);
			//$this -> pagination .= '<span class="displaying-num">' . sprintf(__('%s items', $this -> plugin_name), ($begRecord + 1), ($begRecord + count($records)), $allRecordsCount) . '</span>';
			$this -> pagination .= '<span class="displaying-num">' . sprintf(__('%s items', $this -> plugin_name), $allRecordsCount) . '</span>';
		
			$this -> pagination .= '<span class="pagination-links">';
			$this -> pagination .= '<a href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=1' . $search . $orderby . $order . $this -> after . '" class="first-page' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '">&laquo;</a>';
		
			//if ($this -> page > 1) {
				$this -> pagination .= '<a class="prev-page' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '" href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . ($this -> page - 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Previous Page', $this -> plugin_name) . '">&#8249;</a>';
			//}
			
			$this -> pagination .= '<span class="paging-input">';
			$this -> pagination .= '<input class="current-page" type="text" name="paged" id="paged-input" value="' . $this -> page . '" size="1"> ';
			$this -> pagination .= __('of', $this -> plugin_name); 
			$this -> pagination .= ' <span class="total-pages">' . $totalpagescount . '</span>';
			$this -> pagination .= '</span>';
			
			/*while ($p <= $allRecordsCount) {			
				if ($k >= ($this -> page - 5) && $k <= ($this -> page + 5)) {
					if ($k != $this -> page) {
						//$this -> pagination .= '<a class="page-numbers" href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . ($k) . $search . $orderby . $order . $this -> after . '" title="' . __('Page', $this -> plugin_name) . ' ' . $k . '">' . $k . '</a>';
					} else {
						//$this -> pagination .= '<span class="page-numbers current">' . $k . '</span>';
					}
				}
				
				$p = $p + $this -> per_page;
				$k++;
			}*/
			
			//if ((count($records) + $begRecord) < $allRecordsCount) {
				$this -> pagination .= '<a class="next-page' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '" href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . ($this -> page + 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Next Page', $this -> plugin_name) . '">&#8250;</a>';
			//}
			
			$this -> pagination .= '<a href="?page=' . $this -> url_page . '&amp;' . $this -> pre . 'page=' . $totalpagescount . $search . $orderby . $order . $this -> after . '" class="last-page' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '">&raquo;</a>';
			$this -> pagination .= '</span>';
			
			ob_start();
			
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#paged-input').keypress(function(e) {
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