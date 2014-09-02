<?php

class wpmlMetaboxHelper extends wpMailPlugin {
	
	var $name = 'Metabox';
	
	public function __construct() {
		if (!empty($this -> models)) {
			foreach ($this -> models as $model) {
				$classname = $this -> pre . $model;
				$this -> {$model} = new $classname;
			}
		}
	}
	
	public function __call($method, $args) {		
		if (!empty($this -> models)) {
			foreach ($this -> models as $model) {
				$this -> {$model} -> $method($args[0]);
			}
		}
	}
	
	function wpmlMetaboxHelper() {
		return true;
	}
	
	function write_advanced() {
		$this -> render('metaboxes' . DS . 'write-advanced', false, true, 'admin');
	}
	
	function welcome_stats() {
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'stats', false, true, 'admin');
	}
	
	function welcome_history() {
		global $wpdb, $Db, $History;
		$Db -> model = $History -> model;
		$histories = $Db -> find_all(false, false, array('modified', "DESC"), 5);
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'history', array('histories' => $histories), true, 'admin');
	}
	
	function welcome_quicksearch() {
		
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'quicksearch', false, true, 'admin');
	}
	
	function welcome_subscribers() {
		global $wpdb, $Subscriber;
		$subscribersquery = "SELECT COUNT(id) FROM " . $wpdb -> prefix . $Subscriber -> table . "";
		
		$query_hash = md5($subscribersquery);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$subscriberstotal = ${'newsletters_query_' . $query_hash};
		} else {
			$subscriberstotal = $wpdb -> get_var($subscribersquery);
			${'newsletters_query_' . $query_hash} = $subscriberstotal;
		}
		
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'subscribers', array('total' => $subscriberstotal), true, 'admin');
	}
	
	function welcome_lists() {
		global $wpdb, $Mailinglist;
		$publicquery = "SELECT COUNT(id) FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE `privatelist` = 'N'";
		
		$query_hash = md5($publicquery);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$total_public = ${'newsletters_query_' . $query_hash};
		} else {
			$total_public = $wpdb -> get_var($publicquery);
			${'newsletters_query_' . $query_hash} = $total_public;
		}
		
		$privatequery = "SELECT COUNT(id) FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE `privatelist` = 'Y'";
		
		$query_hash = md5($privatequery);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$total_private = ${'newsletters_query_' . $query_hash};
		} else {
			$total_private = $wpdb -> get_var($privatequery);
			${'newsletters_query_' . $query_hash} = $total_private;
		}
		
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'lists', array('total_public' => $total_public, 'total_private' => $total_private), true, 'admin');
	}
	
	function welcome_emails() {
		global $wpdb, $Email;
		$emailsquery = "SELECT COUNT(id) FROM " . $wpdb -> prefix . $Email -> table . "";
		
		$query_hash = md5($emailsquery);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$emailstotal = ${'newsletters_query_' . $query_hash};
		} else {
			$emailstotal = $wpdb -> get_var($emailsquery);
			${'newsletters_query_' . $query_hash} = $emailstotal;
		}
		
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'emails', array('total' => $emailstotal), true, 'admin');
	}
	
	function welcome_bounces() {
		global $Bounce;
		$total = $Bounce -> alltotal();
		$this -> render('metaboxes' . DS . 'welcome' . DS . 'bounces', array('total' => $total), true, 'admin');
	}
	
	function send_spamscore() {
		$this -> render('metaboxes' . DS . 'send' . DS . 'spamscore', false, true, 'admin');
	}
	
	function send_mailinglists() {
		$this -> render('metaboxes' . DS . 'send-mailinglists', false, true, 'admin');
	}
	
	function send_theme() {
		$this -> render('metaboxes' . DS . 'send-theme', false, true, 'admin');	
	}
	
	function send_insert() {
		$this -> render('metaboxes' . DS . 'send-insert', false, true, 'admin');
	}
	
	function send_submit() {
		$this -> render('metaboxes' . DS . 'send-submit', false, true, 'admin');
	}
	
	function send_otheractions() {
		$this -> render('metaboxes' . DS . 'send-otheractions', false, true, 'admin');
	}
	
	function send_multimime() {
		$this -> render('metaboxes' . DS . 'send-multimime', false, true, 'admin');
	}
	
	function send_preview() {
		$this -> render('metaboxes' . DS . 'send-preview', false, true, 'admin');
	}
	
	function send_setvariables() {
		$this -> render('metaboxes' . DS . 'send-setvariables', false, true, 'admin');
	}
	
	function send_attachment() {
		$this -> render('metaboxes' . DS . 'send-attachment', false, true, 'admin');
	}
	
	function send_publish() {
		$this -> render('metaboxes' . DS . 'send-publish', false, true, 'admin');
	}
	
	function templates_submit() {
		$this -> render('metaboxes' . DS . 'templates-submit', false, true, 'admin');
	}
	
	function settings_language() {
		$this -> render('metaboxes' . DS . 'settings-language', false, true, 'admin');
	}
	
	function settings_submit() {
		$this -> render('metaboxes' . DS . 'settings-submit', false, true, 'admin');
	}
	
	function settings_tableofcontents() {
		$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents', false, true, 'admin');
	}
	
	function settings_subscribers_tableofcontents() {
		$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-subscribers', false, true, 'admin');
	}
	
	function settings_templates_tableofcontents() {
		$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-templates', false, true, 'admin');
	}
	
	function settings_system_tableofcontents() {
		$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-system', false, true, 'admin');
	}
	
	function settings_sections() {
		$this -> render('metaboxes' . DS . 'settings-sections', false, true, 'admin');
	}
	
	function settings_wprelated() {
		$this -> render('metaboxes' . DS . 'settings-wprelated', false, true, 'admin');
	}
	
	function settings_permissions() {
		$this -> render('metaboxes' . DS . 'system' . DS . 'permissions', false, true, 'admin');
	}
	
	function settings_importusers() {
		$this -> render('metaboxes' . DS . 'settings-importusers', false, true, 'admin');
	}
	
	function settings_commentform() {
		$this -> render('metaboxes' . DS . 'settings-comments', false, true, 'admin');	
	}
	
	function settings_system_captcha() {
		$this -> render('metaboxes' . DS . 'system' . DS . 'captcha', false, true, 'admin');
	}
	
	function settings_general() {
		$this -> render('metaboxes' . DS . 'settings-general', false, true, 'admin');
	}
	
	/* Sending Settings */
	function settings_sending() {
		$this -> render('metaboxes' . DS . 'settings-sending', false, true, 'admin');	
	}
	
	/* Subscriber management section */
	function settings_management() {
		$this -> render('metaboxes' . DS . 'settings-management', false, true, 'admin');	
	}
	
	function settings_optin() {
		$this -> render('metaboxes' . DS . 'settings-optin', false, true, 'admin');
	}
	
	function settings_subscriptions() {
		$this -> render('metaboxes' . DS . 'settings-subscriptions', false, true, 'admin');
	}
	
	function settings_pp() {
		$this -> render('metaboxes' . DS . 'settings-pp', false, true, 'admin');
	}
	
	function settings_tc() {
		$this -> render('metaboxes' . DS . 'settings-tc', false, true, 'admin');
	}
	
	function settings_subscribers() {
		$this -> render('metaboxes' . DS . 'settings-subscribers', false, true, 'admin');
	}
	
	function settings_unsubscribe() {
		$this -> render('metaboxes' . DS . 'settings-unsubscribe', false, true, 'admin');
	}
	
	function settings_publishing() {
		$this -> render('metaboxes' . DS . 'settings-publishing', false, true, 'admin');
	}
	
	function settings_scheduling() {
		$this -> render('metaboxes' . DS . 'settings-scheduling', false, true, 'admin');
	}
	
	function settings_bounce() {
		$this -> render('metaboxes' . DS . 'settings-bounce', false, true, 'admin');
	}
	
	function settings_latestposts() {
		$this -> render('metaboxes' . DS . 'settings-latestposts', false, true, 'admin');	
	}
	
	function settings_customcss() {
		$this -> render('metaboxes' . DS . 'settings-customcss', false, true, 'admin');	
	}
	
	function settings_templates_posts() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'posts', false, true, 'admin');
	}
	
	function settings_templates_latestposts() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'latestposts', false, true, 'admin');
	}
	
	function settings_templates_confirm() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'confirm', false, true, 'admin');
	}
	
	function settings_templates_bounce() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'bounce', false, true, 'admin');
	}
	
	function settings_templates_unsubscribe() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'unsubscribe', false, true, 'admin');
	}
	
	function settings_templates_unsubscribeuser() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'unsubscribeuser', false, true, 'admin');
	}
	
	function settings_templates_expire() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'expire', false, true, 'admin');
	}
	
	function settings_templates_order() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'order', false, true, 'admin');
	}
	
	function settings_templates_schedule() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'schedule', false, true, 'admin');
	}
	
	function settings_templates_subscribe() {
		$this -> render('metaboxes' . DS . 'templates' . DS . 'subscribe', false, true, 'admin');
	}
	
	function extensions_settings_submit() {
		$this -> render('metaboxes' . DS . 'extensions' . DS . 'submit', false, true, 'admin');
	}
}

?>