<?php

//directory separator constant
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
if (!defined('WP_MEMORY_LIMIT')) { define('WP_MEMORY_LIMIT', "256M"); }

$root = __FILE__;
for ($i = 0; $i < 4; $i++) $root = dirname($root);
for ($i = 0; $i < 3; $i++) $rootup = dirname($root);

if (file_exists($root . DS . 'wp-config.php')) {
	require_once($root . DS . 'wp-config.php');
} else {
	require_once($rootup . DS . 'wp-config.php');
}

require_once(ABSPATH . '/wp-load.php');
include_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'admin.php');

class wpMailAjax extends wpMailPlugin {

	var $safecommands = array(
		'subscribe',
		'optinembed',
		'offsite_form',
		'get_list_fields',
	);

	function wpMailAjax($cmd = null, $id = null) {
		$errors = array();
		
		if (!empty($cmd)) {
			global $user_ID;
			$user = $this -> userdata($user_ID);
			$permissions = $this -> get_option('permissions');
			$loadforrole = false;
			
			if ($cmd == "posts_by_category") {
				if (current_user_can('edit_posts') || is_super_admin()) {
					$loadforrole = true;	
				}
			}
			
			if (in_array($cmd, $this -> safecommands) || current_user_can('edit_plugins') || $loadforrole == true) {
				$this -> url = site_url() . '/wp-admin/admin.php?page=newsletters';
				$this -> register_plugin('wp-mailinglist', __FILE__);
			
				if (method_exists($this, $cmd)) {
					$this -> {$cmd}($id);
				} else {
					$errors[] = __('Class method does not exist', $this -> name);
				}
			} else {
				$errors[] = __('You are not allowed to access this page', $this -> plugin_name);
			}
		} else {
			$errors[] = __('No method was specified', $this -> plugin_name);
		}
		
		return $errors;
	}
	
	function posts_by_category() {
		$posts_by_category = "";
		
		$arguments = array(
			'numberposts'			=>	"-1",
			'orderby'				=>	'post_title',
			'order'					=>	"ASC",
			'post_type'				=>	"post",
			'post_status'			=>	"publish",
		);
		
		if (!empty($_REQUEST['cat_id']) && $_REQUEST['cat_id'] > 0) {
			$arguments['category'] = $_REQUEST['cat_id'];	
		}
		
		if (!empty($_REQUEST['post_type'])) {
			$arguments['post_type'] = $_REQUEST['post_type'];
		}
		
		if ($posts = get_posts($arguments)) {								
			foreach ($posts as $post) {
				if ($this -> is_plugin_active('qtranslate')) {
					$posts_by_category .= '<option value="' . $post -> ID . '">' . qtrans_use($_REQUEST['language'], $post -> post_title, false) . '</option>';
				} else {
					$posts_by_category .= '<option value="' . $post -> ID . '">' . $post -> post_title . '</option>';
				}
			}
		}
		
		echo $posts_by_category;
	}
	
	function init_textdomain() {		
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain($this -> plugin_name, $this -> plugin_name . DS . 'languages', dirname(plugin_basename(__FILE__)) . DS . 'languages');
		}	
	}
	
	function phpmailer_init($phpmailer) {
		global $orig_message;
		if ($this -> get_option('multimime') == "Y") {
			if (!empty($orig_message)) {
				$phpmailer -> AltBody = strip_tags($orig_message);	
			}
		}
		
		$phpmailer -> Sender = $this -> get_option('bounceemail');
		$phpmailer -> From = $this -> get_option('smtpfrom');
		
		return $phpmailer;
	}
	
	function subscribe($id = null) {
		global $Subscriber;
	
		$args = $_POST['args'];
		if (!empty($args)) {
			foreach ($args as $akey => $aval) {
				$args[$akey] = urldecode($aval);
			}
		}
		
		$wpoptinid = (!empty($_GET['divid'])) ? $_GET['divid'] : $_POST['number'];
		
		if (empty($_POST['embed'])) {
			$options = $this -> get_option('widget');
			
			if (!empty($_POST['list_type']) && ($_POST['list_type'] == "select" || $_POST['list_type'] == "checkboxes")) {
				if (!empty($_POST['list_string']) && ($lists = @explode(",", $_POST['list_string'])) !== false) {
					$options[$wpoptinid]['listsstring'] = $_POST['list_string'];
					$options[$wpoptinid]['lists'] = $lists;
				}
			}	
		} else {
			$options = $this -> get_option('embed');
			
			if (!empty($_POST['list_type'])) {
				switch ($_POST['list_type']) {
					case 'specific'		:
						$options['list'] = $_POST['list_id'][0];
						break;
					case 'language'		:
						$options['list'] = $_POST['list_id'][0];			
						break;
					case 'select'		:
						$options['list'] = 'select';
						if (!empty($_POST['list_string']) && ($lists = @explode(",", $_POST['list_string'])) !== false) { $options['lists'] = $lists; $options['listsstring'] = $_POST['list_string']; }
						break;
					case 'multiple'		:
					case 'checkboxes'	:
						$options['list'] = 'checkboxes';
						if (!empty($_POST['list_string']) && ($lists = @explode(",", $_POST['list_string'])) !== false) { $options['lists'] = $lists; $options['listsstring'] = $_POST['list_string']; }
						break;
				}
			}
			
			$options['list_type'] = $_POST['list_type'];
			$options['embed'] = true;
			$options['offsite'] = false;
			
			$new = array();
			$new[$wpoptinid] = $options;
			$options = $new;
		}
		
		$embed = $this -> get_option('embed');		
		$newoptions = wp_parse_args($options[$wpoptinid], $embed);
		
		//is qTranslate installed and active?
		if ($this -> is_plugin_active('qtranslate')) {
			global $q_config;
			$qtranslateactive = true;
			
			$language = $q_config['language'];
			if (!empty($_POST['language'])) { $language = $_POST['language']; }
			
			foreach ($newoptions as $okey => $oval) {
				if (!empty($newoptions[$okey]) && is_array($newoptions[$okey]) && $okey != "lists") {
					$newoptions[$okey] = $newoptions[$okey][$language];	
				}
			}
			
			$newoptions['language'] = $language;
		}

		if ($Subscriber -> optin($_POST)) {		
			echo '<p class="' . $this -> pre . 'acknowledgement">' . $newoptions['acknowledgement'] . '</p>';
			
			if ($this -> get_option('subscriberedirect') == "Y") {
				$this -> redirect($this -> get_option('subscriberedirecturl'), false, false, true);
			} else {
				if (!empty($newoptions['subscribeagain']) && $newoptions['subscribeagain'] == "Y") {
					if (empty($newoptions['ajax']) || $newoptions['ajax'] == "Y") {
						echo '<p><ul><li><a href="" onclick="wpml_subscribe(\'' . $wpoptinid . '\',\'optinform' . $wpoptinid . '\'); return false;" title="' . __('Submit another subscription', $this -> plugin_name) . '">' . __('Subscribe Again', $this -> plugin_name) . '</a></li></ul></p>';
						echo '<div style="display:none;">' . $this -> render('widget-front', array('wpoptinid' => $wpoptinid, 'args' => $args, 'subscriber' => $this -> init_class($Subscriber -> model, $Subscriber -> data), 'errors' => $Subscriber -> errors, 'options' => $newoptions), false, 'default') . '</div>';
					} else {
						echo '<p><ul><li><a href="' . home_url() . '/?' . $this -> pre . 'method=optin&amp;' . $this -> pre . 'formid=' . $wpoptinid . '" title="' . __('Submit another subscription', $this -> plugin_name) . '">' . __('Subscribe Again', $this -> plugin_name) . '</a></li></ul></p>';
					}
				}
			}
		} else {			
			$this -> render('widget-front', array('isajax' => true, 'wpoptinid' => $wpoptinid, 'args' => $args, 'subscriber' => $this -> init_class($Subscriber -> model, $Subscriber -> data), 'errors' => $Subscriber -> errors, 'options' => $newoptions));
		}
	}
	
	function template_iframe($id = null) {
		global $Db, $Template;
		$Db -> model = $Template -> model;
		$template = $Db -> find(array('id' => $id));
		$this -> render_admin('templates' . DS . 'iframe', array('template' => $template));
	}
	
	function history_iframe($id = null) {
		global $Db, $History;
		$Db -> model = $History -> model;
		$email = $Db -> find(array('id' => $id));		
		$message = $this -> render('newsletter', array('email' => $email, 'subscriber' => $subscriber), false, 'default');
		$content = $this -> render_email('send', array('message' => $message, 'subject' => $email -> subject), false, true, true, $email -> theme_id);
		$output = "";
		ob_start();
		echo do_shortcode(stripslashes($content));
		$output = ob_get_clean();
		echo $this -> process_set_variables($subscriber, $user, $output, $email -> id);
	}
	
	function optinembed($id = null) {	
		$options = $this -> get_option('widget');
		$wpoptinid = (empty($_GET['divid'])) ? $this -> gen_optin_id() : $_GET['divid'];
		
		if ($this -> Subscriber -> optin($_POST)) {
			$data .= '<p class="' . $this -> pre . 'acknowledgement">' . $options['acknowledgement'] . '</p>';
			echo $data;
		} else {			
			if (!empty($_POST['list_type'])) {
				$options['list'] = $_POST['list_type'];
			}
			
			ob_start();
			$this -> render('widget-front', array('wpoptinid' => $wpoptinid, 'subscriber' => new Subscriber($_POST), 'errors' => $this -> Subscriber -> error, 'options' => $options));
			echo ob_get_clean();
		}
		
		return true;
	}
	
	function offsite_form($id = null) {	
		if (!empty($id)) {		
			if ($list = $this -> Mailinglist -> get($id)) {
				$options = unserialize(get_option('wpml_widget'));
				$options['list'] = $list -> id;
				$wpoptinid = $this -> gen_optin_id();
				$html = $this -> gen_optin_embed($options, $wpoptinid, null, null, false);
				$html = htmlentities($html);
				$this -> render_admin('mailinglists' . DS . 'offsite', array('html' => $html, 'options' => $options, 'wpoptinid' => $wpoptinid));
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function fields($id = null) {
		$data = $this -> Field -> get_all_paginated();
		$this -> render_admin('fields' . DS . 'index', array('fields' => $data['Field'], 'paginate' => $data['Pagination']));
	}
	
	function get_list_fields($list_id = null, $render = true) {
		global $wpdb, $Db, $Field, $FieldsList;
		
		if (!empty($_GET['tabi'])) {
			global $wpmltabindex;
			$wpmltabindex = $_GET['tabi'];
		}
		
		if (!empty($_GET['wpoptinid'])) {
			global $wpoptinid;
			$wpoptinid = $_GET['wpoptinid'];
		}
				
		if (empty($_POST['list_type']) || $_POST['list_type'] != "checkboxes") {
			$_POST['list_id'] = (empty($_POST['list_id'])) ? $list_id : $_POST['list_id'];
		}
				
		if (!is_array($_POST['list_id'])) {
			$_POST['list_id'] = array($_POST['list_id']);
		}
		
		$fields_done = array();
		
		$emailfieldquery = "SELECT `id` FROM " . $wpdb -> prefix . $Field -> table . " WHERE `slug` = 'email'";
		
		$query_hash = md5($emailfieldquery);
		if ($oc_emailfield_id = wp_cache_get($query_hash, 'newsletters')) {
			$emailfield_id = $oc_emailfield_id;
		} else {
			$emailfield_id = $wpdb -> get_var($emailfieldquery);
			wp_cache_set($query_hash, $emailfield_id, 'newsletters', 0);
		}
			
		$fields_done[] = $emailfield_id;
	
		if (!empty($_POST['list_id']) && !empty($_POST['list_id'][0])) {
			foreach ($_POST['list_id'] as $list_id) {
				if ($fields = $FieldsList -> fields_by_list($list_id)) {					
					foreach ($fields as $field) {
						if (!in_array($field -> id, $fields_done)) {
							$fields_done[] = $field -> id;
						}
					}
				}
			}
		}

		if (!empty($fields_done)) {
			$Db -> model = $Field -> model;
			$fields_return = array();
						
			if ($fields = $Db -> find_all(false, false, array('order', "ASC"))) {
				foreach ($fields as $field) {
					if (in_array($field -> id, $fields_done)) {
						$managementpost_id = $this -> get_managementpost();
						
						if ($field -> slug != "email" || ($field -> slug == "email" && !is_admin() && !is_single($managementpost_id) && !is_page($managementpost_id))) {
							//should the fields be rendered?
							if (!empty($render) && $render == true) {
								$this -> render_field($field -> id, true, $wpoptinid);
							} else {
								$fields_return[] = $field;	
							}
						}
					}
				}
				
				if ($render == false) {
					return $fields_return;	
				}
			}
		}

		return true;		
	}
	
	function fields_order() {
		global $Db, $Field, $FieldsList;
	
		if (!empty($_REQUEST)) {				
			if (!empty($_REQUEST['fields'])) {
				foreach ($_REQUEST['fields'] as $order => $field_id) {
					$Db -> model = $Field -> model;
					$Db -> save_field('order', $order, array('id' => $field_id));
					
					$Db -> model = $FieldsList -> model;
					$Db -> save_field('order', $order, array('field_id' => $field_id));
				}
				
				_e('Custom fields order has been successfully saved', $this -> name);
				return true;
			}
		}
		
		return false;
	}
}

$cmd = $_GET['cmd'];
$id = $_GET['id'];

//initialize the wpMailAjax class.
$wpMailAjax = new wpMailAjax($cmd, $id);

?>