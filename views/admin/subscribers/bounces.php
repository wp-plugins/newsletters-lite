<div class="wrap newsletters">
	<h2>
		<?php _e('Manage Bounces', $this -> plugin_name); ?>
		<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&amp;method=bounces'); ?>" class="add-new-h2"><?php _e('Refresh', $this -> plugin_name); ?></a>
	</h2>
	
	<div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; Back to Subscribers', $this -> plugin_name), "?page=" . $this -> sections -> subscribers); ?></div> 
	
	<form id="posts-filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    	<?php if (!empty($bounces)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($bounces); ?> <?php _e('bounces', $this -> plugin_name); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $Html -> retainquery('showall=1')); ?></li>
                <?php else : ?>
                    <li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), "?page=" . $this -> sections -> subscribers . '&method=bounces'); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $_GET[$this -> pre . 'searchterm']; ?>" />
			<input type="submit" class="button" value="<?php _e('Search Bounces', $this -> plugin_name); ?>" />
		</p>
	</form>
	<br class="clear" />
	
	<form id="posts-filter" action="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=bounces" method="get">
    	<input type="hidden" name="page" value="<?php echo $this -> sections -> subscribers; ?>" />
    	<input type="hidden" name="method" value="bounces" />
    	<input type="hidden" name="order" value="<?php echo $_GET['order']; ?>" />
    	<input type="hidden" name="orderby" value="<?php echo $_GET['orderby']; ?>" />
    	
    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
    		<input type="hidden" name="<?php echo $this -> pre; ?>searchterm" value="<?php echo esc_attr(stripslashes($_GET[$this -> pre . 'searchterm'])); ?>" />
    	<?php endif; ?>
    	
    	<div class="alignleft actions widefat">
    		<?php _e('Filters:', $this -> plugin_name); ?>
	        
	        <select name="history_id[]" id="historiesautocomplete" style="min-width:300px; width:auto;" multiple="multiple">
		        <?php if (!empty($_GET['history_id'])) : ?>
		        	<?php $historiesarray = (is_array($_GET['history_id'])) ? $_GET['history_id'] : array($_GET['history_id']); ?>
		        	<?php foreach ($historiesarray as $history_id) : ?>
		        		<?php
			        		
			        	$Db -> model = $History -> model;
			        	$history_subject = $Db -> field('subject', array('id' => $history_id));	
			        		
			        	?>
		        		<option value="<?php echo $history_id; ?>" selected="selected"><?php _e($history_subject); ?></option>
		        	<?php endforeach; ?>
		        <?php endif; ?>
	        </select>
	        
	        <input type="submit" name="filter" value="<?php _e('Filter', $this -> plugin_name); ?>" class="button button-primary" />
	        
	        <script type="text/javascript">			        	        
		    jQuery(document).ready(function() {
			    jQuery('#historiesautocomplete').select2({
				  placeholder: '<?php _e('Search newsletters', $this -> plugin_name); ?>',
				  ajax: {
				        url: newsletters_ajaxurl + "?action=newsletters_autocomplete_histories",
				        dataType: 'json',
				        data: function (params) {
					      return {
					        q: params.term, // search term
					        page: params.page
					      };
					    },
					    processResults: function (data, page) {
					      return {
					        results: data
					      };
					    },
				    },
				  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				  minimumInputLength: 1,
				  templateResult: formatResult,
				  templateSelection: formatSelection,
			    }).next().css('width', "auto").css('min-width', "300px");
		    });
			
			function formatResult(data) {
		        return data.text;
		    };
		
		    function formatSelection(data) {
		        return data.text;
		    };
		    
		    function filter_value(filtername, filtervalue) {	    			
		        if (filtername != "") {
		            document.cookie = "<?php echo $this -> pre; ?>filter_" + filtername + "=" + filtervalue + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
		        }
		    }
			</script>    		
    	</div>
    </form>
    <br class="clear" />
	
	<form action="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bouncemass'); ?>" method="post" onsubmit="if (!confirm('<?php _e('Are you sure you wish to execute this action on the selected bounces?', $this -> plugin_name); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<?php if ($this -> get_option('bouncemethod') == "pop") : ?>
                    <a href="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=check-bounced" class="button" onclick="if (!confirm('<?php _e('Are you sure you wish to check your POP3 mailbox for bounced emails?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Check for Bounces', $this -> plugin_name); ?></a>
                <?php endif; ?>
				<select name="action" id="newsletters-bounce-action">
					<option value=""><?php _e('- Bulk Actions -', $this -> plugin_name); ?></option>
					<option value="delete"><?php _e('Delete Bounces', $this -> plugin_name); ?></option>
					<option value="deletesubscribers"><?php _e('Delete Subscribers', $this -> plugin_name); ?></option>
				</select>
				<input type="submit" name="execute" value="<?php _e('Apply', $this -> plugin_name); ?>" class="button-secondary" />
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' : $_GET['orderby'];
		$order = (empty($_GET['order'])) ? 'desc' : strtolower($_GET['order']);
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 6;
		
		?>
	
		<table class="widefat">
			<thead>
				<th class="check-column"><input type="checkbox" name="bouncescheckall" value="1" /></th>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc")); ?>">
						<span><?php _e('Email Address', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-count <?php echo ($orderby == "count") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=count&order=' . (($orderby == "count") ? $otherorder : "asc")); ?>">
						<span><?php _e('Count', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>">
						<span><?php _e('Status', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('History Email', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
						<span><?php _e('Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</thead>
			<tfoot>
				<th class="check-column"><input type="checkbox" name="bouncescheckall" value="1" /></th>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc")); ?>">
						<span><?php _e('Email Address', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-count <?php echo ($orderby == "count") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=count&order=' . (($orderby == "count") ? $otherorder : "asc")); ?>">
						<span><?php _e('Count', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc")); ?>">
						<span><?php _e('Status', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc")); ?>">
						<span><?php _e('History Email', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . $order : 'sortable desc'; ?>">
					<a href="<?php echo $Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc")); ?>">
						<span><?php _e('Date', $this -> plugin_name); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tfoot>
			<tbody>
				<?php if (!empty($bounces)) : ?>
					<?php $class = false; ?>
					<?php foreach ($bounces as $bounce) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" name="bounces[]" value="<?php echo $bounce -> id; ?>" /></th>
							<td>
								<?php $Db -> model = $Subscriber -> model; ?>
								<?php if ($subscriber = $Db -> find(array('email' => $bounce -> email))) : ?>
									<a class="row-title" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=view&id=' . $subscriber -> id); ?>"><?php echo $bounce -> email; ?></a>
								<?php else : ?>
									<?php echo $bounce -> email; ?>
								<?php endif; ?>
								
								<div class="row-actions">
									<span class="delete"><a class="submitdelete" href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bouncedelete&id=' . $bounce -> id); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this bounce?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete Bounce', $this -> plugin_name); ?></a></span>
									<?php if (!empty($subscriber)) : ?>
										<span class="delete">| <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=delete&id=' . $subscriber -> id); ?>" onclick="if (!confirm('<?php _e('Are you sure you want to delete this subscriber?', $this -> plugin_name); ?>')) { return false; }" class="submitdelete"><?php _e('Delete Subscriber', $this -> plugin_name); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
							<td>
								<?php echo $bounce -> count; ?>
							</td>
							<td>
								<?php if (!empty($bounce -> status)) : ?>
									<?php echo esc_attr(stripslashes($bounce -> status)); ?>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($bounce -> history_id)) : ?>
									<a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $bounce -> history_id); ?>"><?php echo __($bounce -> history -> subject); ?></a>
								<?php else : ?>
									<?php _e('None', $this -> plugin_name); ?>
								<?php endif; ?>
							</td>
							<td>
								<abbr title="<?php echo $bounce -> created; ?>"><?php echo $Html -> gen_date(false, strtotime($bounce -> created)); ?></abbr>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo $colspan; ?>"><?php _e('No bounces were found', $this -> plugin_name); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php _e('- Per Page -', $this -> plugin_name); ?></option>
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'bouncesperpage']) && $_COOKIE[$this -> pre . 'bouncesperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo $s; ?>"><?php echo $s; ?> <?php _e('bounces', $this -> plugin_name); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'bouncesperpage'])) : ?>
							<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'bouncesperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'bouncesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render_admin('pagination', array('paginate' => $paginate)); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo $this -> pre; ?>bouncesperpage=" + perpage + "; expires=<?php echo $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", $_SERVER['REQUEST_URI']); ?>";
			}
		}
		</script>
	</form>
</div>