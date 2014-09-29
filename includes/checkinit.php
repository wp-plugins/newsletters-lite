<?php

if (!class_exists('wpMailCheckinit')) {
	class wpMailCheckinit {
	
		function wpMailCheckinit() {
			return true;	
		}
		
		function ci_initialize() {				
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			
			if (!is_plugin_active(plugin_basename($this -> plugin_name . DS . 'wp-mailinglist.php'))) {			
				return;
			}
			
			add_action('wp_ajax_wpmlserialkey', array($this, 'ajax_serialkey'));
		
			if (true || !is_admin() || (is_admin() && $this -> ci_serial_valid())) {
				$this -> ci_initialization();
			} else {			
				$this -> add_action('admin_print_styles', 'ci_print_styles', 10, 1);
				$this -> add_action('admin_print_scripts', 'ci_print_scripts', 10, 1);
				$this -> add_action('admin_notices');
				$this -> add_action('init', 'init', 10, 1);
				$this -> add_action('admin_menu', 'admin_menu');
			}
			
			return false;
		}
		
		function ci_initialization() {								
			/* RSS Feeds */
			if ($this -> get_option('rssfeed') == "Y" && !is_admin()) { 
				global $wp_rewrite;
				if (!$wp_rewrite) $wp_rewrite = new WP_Rewrite();
				add_feed('newsletters', array($this, 'feed_newsletters'));	
			}
			
			$this -> add_filter('media_send_to_editor', 'media_insert', 20, 3);
			$this -> add_filter('attachment_fields_to_save', 'attachment_fields_to_save', null, 2);
			$this -> add_filter('attachment_fields_to_edit', 'attachment_fields_to_edit', null, 2);
			
			//Action hooks
			$this -> add_action('register_form');
			$this -> add_action('admin_menu');
			$this -> add_action('admin_menu', 'add_dashboard', 10, 1);
			$this -> add_action('admin_head');
			$this -> add_action('widgets_init', 'widget_register', 10, 1);
			$this -> add_action('wp_head', 'wp_head', 15, 1);
			$this -> add_action('wp_footer');
			$this -> add_action('admin_footer');
			$this -> add_action('delete_user', 'delete_user', 10, 1);
			$this -> add_action('user_register', 'user_register', 10, 1);
			$this -> add_action('save_post', 'save_post', 10, 2);
			$this -> add_action('delete_post', 'delete_post', 10, 1);
			$this -> add_action('init', 'init', 11, 1);
			$this -> add_action('wp_login', 'end_session', 10, 1);
			$this -> add_action('wp_logout', 'end_session', 10, 1);
			$this -> add_action('init', 'init_textdomain', 10, 1);
			$this -> add_action('plugins_loaded', "plugins_loaded", 2, 1);
			
			/* Schedules */
			$this -> add_action('newsletters_ratereviewhook', 'ratereview_hook', 10, 1);
			$this -> add_action('newsletters_optimizehook', 'optimize_hook', 10, 1);
			$this -> add_action($this -> pre . '_cronhook', 'cron_hook', 10, 1);
	        $this -> add_action($this -> pre . '_pophook', 'pop_hook', 10, 1);
			$this -> add_action($this -> pre . '_latestposts', 'latestposts_hook', 10, 1);
			$this -> add_action($this -> pre . '_activateaction', 'activateaction_hook', 10, 1);
			$this -> add_action($this -> pre . '_autoresponders', 'autoresponders_hook', 10, 1);
			$this -> add_action($this -> pre . '_captchacleanup', 'captchacleanup_hook', 10, 1);
			$this -> add_action($this -> pre . '_importusers', 'importusers_hook', 10, 1);
			$this -> add_action('do_meta_boxes', 'do_meta_boxes', 10, 1);
			$this -> add_action('admin_notices');
			$this -> add_action('admin_init', 'tinymce');
			$this -> add_action('admin_init', 'custom_redirect', 1, 1);
			$this -> add_action('phpmailer_init', 'phpmailer_init', 999, 1);
			$this -> add_action('profile_update');
			$this -> add_action('comment_form');
			$this -> add_action('wp_insert_comment', 'comment_post', 10, 2);
			$this -> add_action('wp_print_styles', 'print_styles');
			$this -> add_action('admin_print_styles', 'print_styles');
			$this -> add_action('wp_print_scripts', 'print_scripts');
			$this -> add_action('admin_print_scripts', 'print_scripts');
			$this -> add_action('wp_dashboard_setup', 'dashboard_setup');
			
			//Filter hooks
			$this -> add_filter('cron_schedules');
			$this -> add_filter('screen_settings', 'screen_settings', 15, 2);
			$this -> add_filter('plugin_action_links', 'plugin_action_links', 10, 4);
			$this -> add_filter('the_editor', 'the_editor', 1, 1);
	        $this -> add_filter('tiny_mce_before_init', 'override_mce_options', 10, 1);
	        
		    $this -> add_action('after_plugin_row_' . $this -> plugin_name . '/wp-mailinglist.php', 'after_plugin_row', 10, 2);
			
			if ($this -> ci_serial_valid()) {	
				$this -> add_action('install_plugins_pre_plugin-information', 'display_changelog', 10, 1);
				$this -> add_filter('transient_update_plugins', 'check_update', 10, 1);
		        $this -> add_filter('site_transient_update_plugins', 'check_update', 10, 1);
		    }
	        
	        if ($this -> language_do()) {
	        	add_filter('gettext', array($this, 'language_useordefault'), 0);
	        }
			
			//WordPress Shortcodes
			global $Shortcode;
			add_shortcode($this -> pre . 'management', array($this, 'sc_management'));
			add_shortcode($this -> pre . 'subscribe', array($Shortcode, 'subscribe'));
			add_shortcode($this -> pre . 'template', array($Shortcode, 'template'));
			add_shortcode($this -> pre . 'snippet', array($Shortcode, 'template'));
			add_shortcode($this -> pre . 'history', array($Shortcode, 'history'));
			add_shortcode($this -> pre . 'meta', array($Shortcode, 'meta'));
			add_shortcode($this -> pre . 'date', array($Shortcode, 'datestring'));
			add_shortcode($this -> pre . 'post', array($Shortcode, 'posts_single'));
			add_shortcode($this -> pre . 'posts', array($Shortcode, 'posts_multiple'));
			add_shortcode($this -> pre . 'post_thumbnail', array($Shortcode, 'post_thumbnail'));	
			add_shortcode($this -> pre . 'post_permalink', array($Shortcode, 'post_permalink'));
			add_shortcode($this -> pre . 'subscriberscount', array($Shortcode, 'subscriberscount'));
			
			add_shortcode('newsletters_management', array($this, 'sc_management'));
			add_shortcode('newsletters_subscribe', array($Shortcode, 'subscribe'));
			add_shortcode('newsletters_template', array($Shortcode, 'template'));
			add_shortcode('newsletters_snippet', array($Shortcode, 'template'));
			add_shortcode('newsletters_history', array($Shortcode, 'history'));
			add_shortcode('newsletters_meta', array($Shortcode, 'meta'));
			add_shortcode('newsletters_date', array($Shortcode, 'datestring'));
			add_shortcode('newsletters_post', array($Shortcode, 'posts_single'));
			add_shortcode('newsletters_posts', array($Shortcode, 'posts_multiple'));
			add_shortcode('newsletters_post_thumbnail', array($Shortcode, 'post_thumbnail'));	
			add_shortcode('newsletters_post_permalink', array($Shortcode, 'post_permalink'));
			add_shortcode('newsletters_subscriberscount', array($Shortcode, 'subscriberscount'));
			
			add_shortcode($this -> pre . 'themailer_address', array($Shortcode, 'themailer_address'));
			add_shortcode($this -> pre . 'themailer_facebookurl', array($Shortcode, 'themailer_facebookurl'));
			add_shortcode($this -> pre . 'themailer_twitterurl', array($Shortcode, 'themailer_twitterurl'));
			add_shortcode($this -> pre . 'themailer_rssurl', array($Shortcode, 'themailer_rssurl'));
			add_shortcode($this -> pre . 'pronews_address', array($Shortcode, 'pronews_address'));
			add_shortcode($this -> pre . 'pronews_facebookurl', array($Shortcode, 'pronews_facebookurl'));
			add_shortcode($this -> pre . 'pronews_twitterurl', array($Shortcode, 'pronews_twitterurl'));
			add_shortcode($this -> pre . 'pronews_rssurl', array($Shortcode, 'pronews_rssurl'));
			add_shortcode($this -> pre . 'lagoon_address', array($Shortcode, 'lagoon_address'));
			add_shortcode($this -> pre . 'lagoon_facebookurl', array($Shortcode, 'lagoon_facebookurl'));
			add_shortcode($this -> pre . 'lagoon_twitterurl', array($Shortcode, 'lagoon_twitterurl'));
			add_shortcode($this -> pre . 'lagoon_rssurl', array($Shortcode, 'lagoon_rssurl'));
			
			/* Post Shortcodes */
			$post_shortcodes = array('post_loop', 'category_heading', 'post_id', 'post_author', 'post_title', 'post_link', 'post_date_wrapper', 'post_date', 'post_thumbnail', 'post_excerpt', 'post_content');
			foreach ($post_shortcodes as $post_shortcode) {
				add_shortcode($post_shortcode, array($Shortcode, 'shortcode_posts'));
				add_shortcode('newsletters_' . $post_shortcode, array($Shortcode, 'shortcode_posts'));
			}
			
			/* Ajax */
			if (is_admin()) {
				add_action('wp_ajax_newsletters_mailinglist_save', array($this, 'ajax_mailinglist_save'));
				add_action('wp_ajax_newsletters_tinymce_snippet', array($this, 'ajax_tinymce_snippet'));
				add_action('wp_ajax_newsletters_tinymce_dialog', array($this, 'ajax_tinymce_dialog'));
				add_action('wp_ajax_newsletters_order_fields', array($this, 'ajax_order_fields'));
				add_action('wp_ajax_newsletters_themeedit', array($this, 'ajax_themeedit'));
				add_action('wp_ajax_newsletters_addcontentarea', array($this, 'ajax_addcontentarea'));
				add_action('wp_ajax_newsletters_deletecontentarea', array($this, 'ajax_deletecontentarea'));
				add_action('wp_ajax_subscribercount', array($this, 'ajax_subscribercount'));
				add_action('wp_ajax_subscribercountdisplay', array($this, 'ajax_subscribercountdisplay'));
				add_action('wp_ajax_wpmltestsettings', array($this, 'ajax_testsettings'));
				add_action('wp_ajax_wpmldkimwizard', array($this, 'ajax_dkimwizard'));
				add_action('wp_ajax_wpmltestbouncesettings', array($this, 'ajax_testbouncesettings'));
				add_action('wp_ajax_wpmlhistory_iframe', array($this, 'ajax_historyiframe'));
				
				$createpreview = $this -> get_option('createpreview');
				if (!empty($createpreview) && $createpreview == "Y") {
					add_action('wp_ajax_wpmlpreviewrunner', array($this, 'ajax_previewrunner'));
				}
				
				add_action('wp_ajax_newsletters_spamscorerunner', array($this, 'ajax_spamscorerunner'));
				add_action('wp_ajax_newsletters_gauge', array($this, 'ajax_gauge'));
				add_action('wp_ajax_wpmllatestposts_preview', array($this, 'ajax_latestposts_preview'));
				add_action('wp_ajax_newsletters_lpsposts', array($this, 'ajax_lps_posts'));
				add_action('wp_ajax_newsletters_delete_lps_post', array($this, 'ajax_delete_lps_post'));
				add_action('wp_ajax_wpmlwelcomestats', array($this, 'ajax_welcomestats'));
				add_action('wp_ajax_wpmlexecutemail', array($this, 'ajax_executemail'));
				add_action('wp_ajax_wpmlqueuemail', array($this, 'ajax_queuemail'));
				add_action('wp_ajax_wpmlsetvariables', array($this, 'ajax_setvariables'));
				add_action('wp_ajax_wpmlgetposts', array($this, 'ajax_getposts'));
				add_action('wp_ajax_newsletters_api_newkey', array($this, 'api_newkey'));
			}		
			
			add_action('wp_ajax_newsletters_api', array($this, 'api_init'));
			add_action('wp_ajax_nopriv_newsletters_api', array($this, 'api_init'));
			add_action('wp_ajax_wpmlsubscribe', array($this, 'ajax_subscribe'));
			add_action('wp_ajax_nopriv_wpmlsubscribe', array($this, 'ajax_subscribe'));
			add_action('wp_ajax_managementactivate', array($this, 'ajax_managementactivate'));
			add_action('wp_ajax_nopriv_managementactivate', array($this, 'ajax_managementactivate'));
			add_action('wp_ajax_managementsubscribe', array($this, 'ajax_managementsubscribe'));
			add_action('wp_ajax_nopriv_managementsubscribe', array($this, 'ajax_managementsubscribe'));
			add_action('wp_ajax_managementcurrentsubscriptions', array($this, 'ajax_managementcurrentsubscriptions'));
			add_action('wp_ajax_nopriv_managementcurrentsubscriptions', array($this, 'ajax_managementcurrentsubscriptions'));
			add_action('wp_ajax_managementnewsubscriptions', array($this, 'ajax_managementnewsubscriptions'));
			add_action('wp_ajax_nopriv_managementnewsubscriptions', array($this, 'ajax_managementnewsubscriptions'));
			add_action('wp_ajax_managementsavefields', array($this, 'ajax_managementsavefields'));
			add_action('wp_ajax_nopriv_managementsavefields', array($this, 'ajax_managementsavefields'));
			add_action('wp_ajax_managementcustomfields', array($this, 'ajax_managementcustomfields'));
			add_action('wp_ajax_nopriv_managementcustomfields', array($this, 'ajax_managementcustomfields'));
			add_action('wp_ajax_wpmlimportsubscribers', array($this, 'ajax_importsubscribers'));
			add_action('wp_ajax_wpmlexportsubscribers', array($this, 'ajax_exportsubscribers'));
			add_action('wp_ajax_wpmlgetlistfields', array($this, 'ajax_getlistfields'));
			add_action('wp_ajax_nopriv_wpmlgetlistfields', array($this, 'ajax_getlistfields'));
			
			add_action('wp_ajax_newsletters_posts_by_category', array($this, 'ajax_posts_by_category'));
			add_action('wp_ajax_newsletters_template_iframe', array($this, 'ajax_template_iframe'));
			add_action('wp_ajax_newsletters_uploadify', array($this, 'ajax_uploadify'));
			add_action('wp_ajax_nopriv_newsletters_uploadify', array($this, 'ajax_uploadify'));
			
			$this -> updating_plugin();
			
			return true;
		}
		
		function ci_get_serial() {
			if ($serial = $this -> get_option('serialkey')) {
				return $serial;
			}
			
			return false;
		}
		
		function ci_serial_valid() {
			$host = $_SERVER['HTTP_HOST'];
			$result = false;
			
			if (preg_match("/^(www\.)(.*)/si", $host, $matches)) {
				$wwwhost = $host;
				$nonwwwhost = preg_replace("/^(www\.)?/si", "", $wwwhost);
			} else {
				$nonwwwhost = $host;
				$wwwhost = "www." . $host;	
			}
			
			if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "localhost:" . $_SERVER['SERVER_PORT']) {
				$result = true;	
			} else {
				if ($serial = $this -> ci_get_serial()) {			
					if ($serial == strtoupper(md5($_SERVER['HTTP_HOST'] . "wpml" . "mymasesoetkoekiesisfokkenlekker"))) {
						$result = true;
					} elseif (strtoupper(md5($wwwhost . "wpml" . "mymasesoetkoekiesisfokkenlekker")) == $serial || 
								strtoupper(md5($nonwwwhost . "wpml" . "mymasesoetkoekiesisfokkenlekker")) == $serial) {
						$result = true;
					}
				}
			}
			
			$result = apply_filters($this -> pre . '_serialkey_validation', $result);
			return $result;
		}
	}
}

?>