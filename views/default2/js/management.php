<script type="text/javascript">
var unsubscribe_comments = "";
	
jQuery(document).ready(function() {	
	var hash = window.location.hash;
	var thash = hash.substring(hash.lastIndexOf('#'), hash.length);
	
	if (thash != "") {
		jQuery('#managementtabs').find('a[href*='+ thash + ']').trigger('click').closest('li').addClass('active');
	} else {
		//setTimeout(function() { window.scrollTo(0, 0); }, 1);
	}
});

function wpmlmanagement_savefields(form) {
	jQuery('#savefieldsbutton').prop('disabled', true);
	var formdata = jQuery('#subscribersavefieldsform').serialize();	
	jQuery('#savefieldsloading').show();
	
	jQuery('div.newsletters-field-error', form).slideUp();
	jQuery(form).find('.<?php echo $this -> pre; ?>fielderror').removeClass('<?php echo $this -> pre; ?>fielderror');
	
	jQuery.post(wpmlajaxurl + "action=managementsavefields", formdata, function(response) {
		jQuery('#savefields').html(response);
		jQuery('#savefieldsbutton').prop('disabled', false);
		wpml_scroll('#managementtabs');
	});
}

function wpmlmanagement_activate(subscriber_id, mailinglist_id, activate) {	
	if (activate == "Y") {
		jQuery('#activatelink' + mailinglist_id).html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php _e('Activating...', $this -> plugin_name); ?>');	
	} else {
		jQuery('tr#currentsubscription' + mailinglist_id).fadeOut(1000, function() { jQuery(this).remove(); });
		jQuery('#activatelink' + mailinglist_id).html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php _e('Removing...', $this -> plugin_name); ?>');
	}

	jQuery.post(wpmlajaxurl + "action=managementactivate", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id, 'activate':activate, 'comments':unsubscribe_comments}, function(response) {
		jQuery('#currentsubscriptions').html(response);
		wpmlmanagement_reloadsubscriptions("new", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
		wpml_scroll('#managementtabs');
	});
}

function wpmlmanagement_subscribe(subscriber_id, mailinglist_id) {
	jQuery('.subscribebutton').prop('disabled', true);
	jQuery('#subscribenowlink' + mailinglist_id).html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php _e('Subscribing...', $this -> plugin_name); ?>');
	
	jQuery.post(wpmlajaxurl + "action=managementsubscribe", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id}, function(response) {
		wpmlmanagement_reloadsubscriptions("current", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
		jQuery('#newsubscriptions').html(response);
		jQuery('.subscribebutton').prop('disabled', false);
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