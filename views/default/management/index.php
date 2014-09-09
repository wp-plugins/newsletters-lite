<?php $subscribedlists = array(); ?>
<?php

if (!empty($subscriber -> subscriptions)) {
	foreach ($subscriber -> subscriptions as $subscription) {
		$subscribedlists[] = $subscription -> mailinglist -> id; 	
	}
	
	$_POST['list_id'] = $subscribedlists;
}

?>

<?php if (is_user_logged_in()) : ?>
	<p><?php _e('You are logged in and the subscriber email below is linked to your user account.', $this -> plugin_name); ?></p>
<?php endif; ?>

<p class="managementemail">
	<?php _e('Your email address is:', $this -> plugin_name); ?> <strong><?php echo stripslashes($subscriber -> email); ?></strong> 
    <span class="managementlogout"><a onclick="if (!confirm('<?php _e('Are you sure you wish to logout?', $this -> plugin_name); ?>')) { return false; }" href="<?php echo $Html -> retainquery('method=logout', get_permalink($this -> get_managementpost())); ?>"><?php _e('Logout', $this -> plugin_name); ?></a></span>
</p>

<?php if (!empty($_REQUEST['updated'])) : ?>
	<?php if (!empty($_REQUEST['success'])) : ?>
		<p class="<?php echo $this -> pre; ?>success"><?php echo stripslashes($_REQUEST['success']); ?></p>
	<?php endif; ?>
	<?php if (!empty($_REQUEST['error'])) : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php echo stripslashes($_REQUEST['error']); ?></p>
	<?php endif; ?>
<?php endif; ?>

<div class="<?php echo $this -> pre; ?>">
	<div id="managementtabs">
		<ul>
	    	<li><a href="#managementtabs1"><?php _e('Current', $this -> plugin_name); ?></a></li>
	        <?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?><li><a href="#managementtabs2"><?php _e('Subscribe', $this -> plugin_name); ?></a></li><?php endif; ?>
	        <?php if ($this -> get_option('managementcustomfields') == "Y") : ?><li><a href="#managementtabs3"><?php _e('Profile', $this -> plugin_name); ?></a></li><?php endif; ?>
	    </ul>
	    
	    <div id="managementtabs1">
	    	<div id="currentsubscriptions">
				<?php $this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default'); ?>
	        </div>
	        
	        <br class="clear" />
	    </div>
	    
	   	<?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?>    
	        <div id="managementtabs2">
				<?php $otherlists = array(); ?>
	            <?php if ($mailinglists = $Mailinglist -> select(false)) : ?>
	                <?php foreach ($mailinglists as $list_id => $list_title) : ?>
	                    <?php if (empty($subscribedlists) || (!empty($subscribedlists) && !in_array($list_id, $subscribedlists))) : ?>
	                        <?php $otherlists[] = $list_id; ?>
	                    <?php endif; ?>
	                <?php endforeach; ?>
	            <?php endif; ?>
	            
	            <?php if (true || !empty($otherlists)) : ?>
	                <div id="newsubscriptions">
	                    <?php $this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'otherlists' => $otherlists), true, 'default'); ?>
	                </div>
	            <?php endif; ?>
	            <br class="clear" />
	        </div>
        <?php endif; ?>    
	    
	    <?php if ($this -> get_option('managementcustomfields') == "Y") : ?>
	        <div id="managementtabs3">
				<?php
	            
	            $fields = $FieldsList -> fields_by_list($_POST['list_id'], "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
	            
	            ?>
	            <div id="savefields" class="<?php echo $this -> pre; ?>widget widget_newsletters <?php echo $this -> pre; ?>">
	                <?php $this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default'); ?>
	            </div>
	            
	            <br class="clear" />
	        </div>
	    <?php endif; ?>
	</div>
	
	<br class="clear" />
</div>

<script type="text/javascript">
jQuery(document).ready(function() { 
	if (jQuery.isFunction(jQuery.fn.cookie)) {
		var managementtabscookieid = jQuery.cookie('managementtabscookie') || 0;
	}
		
	if (jQuery.isFunction(jQuery.fn.tabs)) {
		jQuery('#managementtabs').tabs({
			active: managementtabscookieid,
			activate: function(event, ui) {
				if (jQuery.isFunction(jQuery.fn.cookie)) {
					jQuery.cookie('managementtabscookie', ui.newTab.index(), {expires: 365, path: '/'});
				}
			}
		}); 
	}
});

function wpmlmanagement_savefields() {
	jQuery('#savefields').button('option', "disabled", true);
	var formdata = jQuery('#subscribersavefieldsform').serialize();	
	jQuery('#savefieldsloading').show();
	
	jQuery.post(wpmlajaxurl + "action=managementsavefields", formdata, function(response) {
		jQuery('#savefields').html(response);
		jQuery('#savefields').button('option', "disabled", false);
		wpml_scroll('#managementtabs');
	});
}

function wpmlmanagement_activate(subscriber_id, mailinglist_id, activate) {	
	if (activate == "Y") {
		jQuery('#activatelink' + mailinglist_id).html('<img src="<?php echo $this -> url(); ?>/views/default/img/loading.gif" /> <?php _e('Activating...', $this -> plugin_name); ?>');	
	} else {
		jQuery('tr#currentsubscription' + mailinglist_id).fadeOut(1000, function() { jQuery(this).remove(); });
		jQuery('#activatelink' + mailinglist_id).html('<img src="<?php echo $this -> url(); ?>/views/default/img/loading.gif" /> <?php _e('Removing...', $this -> plugin_name); ?>');
	}

	jQuery.post(wpmlajaxurl + "action=managementactivate", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id, 'activate':activate}, function(response) {
		jQuery('#currentsubscriptions').html(response);
		wpmlmanagement_reloadsubscriptions("new", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
		wpml_scroll('#managementtabs');
	});
}

function wpmlmanagement_subscribe(subscriber_id, mailinglist_id) {
	jQuery('.subscribebutton').button('option', "disabled", true);
	jQuery('#subscribenowlink' + mailinglist_id).html('<img src="<?php echo $this -> url(); ?>/views/default/img/loading.gif" /> <?php _e('Subscribing...', $this -> plugin_name); ?>');
	
	jQuery.post(wpmlajaxurl + "action=managementsubscribe", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id}, function(response) {
		wpmlmanagement_reloadsubscriptions("current", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
		jQuery('#newsubscriptions').html(response);
		jQuery('.subscribebutton').button('option', "disabled", false);
		wpml_scroll('#managementtabs');
	});
}

function wpmlmanagement_reloadsubscriptions(divs, subscriber_id) {
	if (divs == "both" || divs == "current") {		
		jQuery.post(wpmlajaxurl + "action=managementcurrentsubscriptions", {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#currentsubscriptions').html(response);
		});
	}
	
	if (divs == "both" || divs == "new") {		
		jQuery.post(wpmlajaxurl + "action=managementnewsubscriptions", {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#newsubscriptions').html(response);
		});
	}
	
	if (divs == "both" || divs == "customfields") {
		jQuery.post(wpmlajaxurl + 'action=managementcustomfields', {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#savefields').html(response);
		});	
	}
}
</script>