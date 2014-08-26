<div class="wrap newsletters">
	<h2><?php _e('Autoresponder Emails', $this -> plugin_name); ?></h2>
    
    <div style="float:none;" class="subsubsub"><?php echo $Html -> link(__('&larr; Back to Autoresponders', $this -> plugin_name), "?page=" . $this -> sections -> autoresponders); ?></div> 
    
    <?php if (true || !empty($autoresponderemails)) : ?>
		<form id="posts-filter" action="<?php echo $this -> url; ?>" method="post">
        	<select name="filter_status" onchange="changefilter('status', this.value);">
                <option <?php echo (!isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) || $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "" || empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) || (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "all")) ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Autoresponder Emails', $this -> plugin_name); ?></option>
                <option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "unsent") ? 'selected="selected"' : ''; ?> value="unsent"><?php _e('Unsent Autoresponder Emails', $this -> plugin_name); ?></option>
                <option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "sent") ? 'selected="selected"' : ''; ?> value="sent"><?php _e('Sent Autoresponder Emails', $this -> plugin_name); ?></option>
            </select>
            <select name="filter_autoresponder_id" onchange="changefilter('autoresponder_id', this.value);">
            	<option value=""><?php _e('All Autoresponders', $this -> plugin_name); ?></option>
                <?php if ($autoresponders = $Autoresponder -> select()) : ?>
                	<?php foreach ($autoresponders as $akey => $aval) : ?>
                    	<option <?php echo (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] == $akey) ? 'selected="selected"' : ''; ?> value="<?php echo $akey; ?>"><?php echo $aval; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <a href="?page=<?php echo $this -> sections -> autoresponderemails; ?>" class="button"><?php _e('Filter', $this -> plugin_name); ?></a>
            <?php echo $Html -> help(__('Filter the autoresponder emails below by status (sent/unsent) and by specific autoresponder to find the email(s) that you might be looking for.', $this -> plugin_name)); ?>            
            <br class="clear" />
        
        	<?php if (!empty($autoresponderemails)) : ?>
			<ul class="subsubsub">
				<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($autoresponderemails); ?> <?php _e('autoresponder emails', $this -> plugin_name); ?> |</li>
				<?php if (empty($_GET['showall'])) : ?>
					<li><?php echo $Html -> link(__('Show All', $this -> plugin_name), $this -> url . '&amp;showall=1'); ?></li>
				<?php else : ?>
					<li><?php echo $Html -> link(__('Show Paging', $this -> plugin_name), '?page=' . $this -> sections -> autoresponderemails); ?></li>
				<?php endif; ?>
			</ul>
            <?php endif; ?>            
            <script type="text/javascript">
			function changefilter(field, value) {				
				if (value != "") {
					document.cookie = "<?php echo $this -> pre; ?>autoresponderemailsfilter_" + field + "=" + value + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
				} else {
					document.cookie = "<?php echo $this -> pre; ?>autoresponderemailsfilter_" + field + "=" + value + "; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("-30 days")); ?> UTC; path=/";
				}
			}
						
			jQuery(document).ready(function() {
				<?php if (!empty($_GET['id'])) : ?>
					changefilter('autoresponder_id', '<?php echo $_GET['id']; ?>');
				<?php endif; ?>
				<?php if (!empty($_GET['status'])) : ?>
					changefilter('status', '<?php echo $_GET['status']; ?>');
				<?php endif; ?>
			});
			</script>
		</form>
	<?php endif; ?>
    
    <?php $this -> render('autoresponderemails' . DS . 'loop', array('autoresponderemails' => $autoresponderemails, 'paginate' => $paginate), true, 'admin'); ?>
</div>