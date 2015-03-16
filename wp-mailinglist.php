<?php

/*
Plugin Name: Newsletters
Plugin URI: http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin
Version: 4.4.5
Description: This newsletter software allows users to subscribe to mutliple mailing lists on your WordPress website. Send newsletters manually or from posts, manage newsletter templates, view a complete history with tracking, import/export subscribers, accept paid subscriptions and much more.
Author: Tribulant Software
Author URI: http://tribulant.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-mailinglist
Domain Path: /languages
*/

if (!defined('DS')) { define("DS", DIRECTORY_SEPARATOR); }
if (!defined('WP_MEMORY_LIMIT')) { define('WP_MEMORY_LIMIT', "1024M"); }
if (!defined('W3TC_DYNAMIC_SECURITY')) { define('W3TC_DYNAMIC_SECURITY', md5(rand(0,999))); }
if (!defined('NEWSLETTERS_NAME')) { define('NEWSLETTERS_NAME', basename(dirname(__FILE__))); }

//include the wpMailPlugin class file
require_once(dirname(__FILE__) . DS . 'includes' . DS . 'checkinit.php');
require_once(dirname(__FILE__) . DS . 'includes' . DS . 'constants.php');
require_once(dirname(__FILE__) . DS . 'wp-mailinglist-plugin.php');

if (!class_exists('wpMail')) {
	class wpMail extends wpMailPlugin {
		var $url;
		var $plugin_file;
		
		function tinymce() {
			if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
	
			// Add TinyMCE buttons when using rich editor
			if (get_user_option('rich_editing') == 'true' &&
				$this -> get_option('tinymcebtn') == "Y") {
				add_filter('mce_buttons', array($this, 'mcebutton'));
				add_filter('mce_buttons_3', array($this, 'mcebutton3'));
				add_filter('mce_external_plugins', array($this, 'mceplugin'));
				add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'));
			}
		}
		
		function mcebutton($buttons) {	
			array_push($buttons, "Newsletters");		
			return $buttons;
		}
		
		function mcebutton3($buttons = array()) {
			//Viper's Video Quicktags compatibility
			if (!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> send || $_GET['page'] == $this -> sections -> templates_save)) {
				if (!empty($buttons)) {
					foreach ($buttons as $bkey => $bval) {
						if (preg_match("/\v\v\q(.*)?/si", $bval, $match)) {
							unset($buttons[$bkey]);
						}
					}
				}
			}
			
			return $buttons;
		}
	
		function mceplugin($plugins = array()) {
			if (version_compare(get_bloginfo('version'), "3.8") >= 0) {
				$url = $this -> url() . '/js/tinymce/editor_plugin.js';
			} else {
				$url = $this -> url() . '/js/tinymce/editor_plugin_old.js';
			}
			
			$plugins['Newsletters'] = $url;
			
			//Viper's Video Quicktags compatibility
			if (!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> send || $_GET['page'] == $this -> sections -> templates_save)) {
				if (isset($plugins['vipersvideoquicktags'])) {
					unset($plugins['vipersvideoquicktags']);
				}
			}
			
			return $plugins;
		}	
		
		function tiny_mce_before_init($init_array = array()) {
			global $wpdb, $Db, $post, $Template, $Mailinglist;
		
			$init_array['content_css'] .= "," . $this -> url() . '/css/editor-style.css';	

			$snippets = array();
			$templatesquery = "SELECT * FROM " . $wpdb -> prefix . $Template -> table . " ORDER BY title ASC";
			$templates = $wpdb -> get_results($templatesquery);
			
			foreach ($templates as $template) {
				$snippets[] = array('text' => __($template -> title), 'value' => $template -> id);
			}
			
			$snippets = json_encode($snippets);
			$init_array['newsletters_snippet_list'] = $snippets;
			
			$mailinglists = array();
			$Db -> model = $Mailinglist -> model;
			if ($lists = $Db -> find_all(false, false, array('title', "ASC"))) {
				foreach ($lists as $list) {
					$mailinglists[] = array('text' => $list -> id . ' - ' . __($list -> title), 'value' => $list -> id);
				}
			}
			$mailinglists = json_encode($mailinglists);
			$init_array['newsletters_mailinglists_list'] = $mailinglists;
			
			$post_id = $post -> ID;
			$init_array['newsletters_post_id'] = $post_id;
			
			$init_array['newsletters_language_do'] = $this -> language_do();
			$init_array['newsletters_languages'] = false;
			if ($this -> language_do()) {
				$newsletters_languages = array();
				$languages = $this -> language_getlanguages();
				foreach ($languages as $language) {
					$newsletters_languages[] = array('text' => $this -> language_name($language), 'value' => $language);
				}
				$newsletters_languages = json_encode($newsletters_languages);
				$init_array['newsletters_languages'] = $newsletters_languages;
			}
			
			$categories_args = array('hide_empty' => 0, 'show_count' => 1);
			if ($categories = get_categories($categories_args)) {
				$newsletters_categories = array();
				$newsletters_categories[]= array('text' => __('- Select -', $this -> plugin_name), 'value' => false);
				foreach ($categories as $category) {
					$newsletters_categories[] = array('text' => __($category -> name), 'value' => $category -> cat_ID);
				}
				$newsletters_categories = json_encode($newsletters_categories);
				$init_array['newsletters_post_categories'] = $newsletters_categories;
			}
			
			$init_array['newsletters_loading_image'] = $this -> url() . '/images/loading.gif';
			
			if ($post_types = $this -> get_custom_post_types()) {
				$newsletters_post_types = array();
				$newsletters_post_types[] = array('text' => __('- Select -', $this -> plugin_name), 'value' => false);
				foreach ($post_types as $ptype_key => $ptype) {
					$newsletters_post_types[] = array('text' => $ptype -> labels -> name, 'value' => $ptype_key);
				}
				$newsletters_post_types = json_encode($newsletters_post_types);
				$init_array['newsletters_post_types'] = $newsletters_post_types;
			}			
					
			return $init_array;
		}
	
		function my_change_mce_settings($init_array = array()) {
		    $init_array['disk_cache'] = false; // disable caching
		    $init_array['compress'] = false; // disable gzip compression
		    $init_array['old_cache_max'] = 3; // keep 3 different TinyMCE configurations cached (when switching between several configurations regularly)
		    
		    return $init_array;
		}
	
		function mceupdate($ver) {
			$ver += 3;
		  	return $ver;
		}
		
		function phpmailer_init($phpmailer = null) {	
			global $phpmailer, $fromwpml;	
					
			if (!empty($fromwpml) && $fromwpml == true) {
				global $orig_message, $wpml_message, $wpml_textmessage, $wpmlhistory_id;				
				global $wpdb, $History;
				
				if (!empty($wpmlhistory_id)) { 
					$query = "SELECT `from`, `fromname`, `text` FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $wpmlhistory_id . "'";
					$his = $wpdb -> get_row($query);
					$history = stripslashes_deep($his); 
				}
				
				if ($this -> get_option('multimime') == "Y") {
					if (!empty($wpml_textmessage)) {
						if (!empty($history -> text)) {
							$altbody = $history -> text;
		    			} else {
			    			require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
							$htmlToText = new Html2Text($wpml_textmessage, 255);
		    				$altbody = $htmlToText -> convert();
		    			}
		    			
						$phpmailer -> AltBody = $altbody;
					}
				}
				
				$smtpfrom = (empty($history -> from)) ? $this -> get_option('smtpfrom') : $history -> from;
				$smtpfromname = (empty($history -> fromname)) ? $this -> get_option('smtpfromname') : $history -> fromname;
				
				$phpmailer -> Body = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', stripslashes($phpmailer -> Body), $phpmailer, $wpmlhistory_id));
				$phpmailer -> Sender = $this -> get_option('bounceemail');
				$phpmailer -> From = $smtpfrom;
				$phpmailer -> CharSet = get_bloginfo('charset');
				$phpmailer -> Encoding = $this -> get_option('emailencoding');
				$phpmailer -> WordWrap = 0;
				$phpmailer -> Priority = $this -> get_option('mailpriority');
				$phpmailer -> MessageID = $this -> phpmailer_messageid();
				$phpmailer -> SMTPKeepAlive = true;
				
				global $Subscriber, $newsletters_presend, $newsletters_emailraw;				
				if (!empty($newsletters_presend) && $newsletters_presend == true) {	
					$subscriber_id = $Subscriber -> admin_subscriber_id();
			    	$subscriber = $Subscriber -> get($subscriber_id, false);
					$phpmailer -> PreSend();			
					$header = $phpmailer -> CreateHeader();
					$header .= "To: " . $subscriber -> email . "\r\n";
					$header .= "Subject: " . $phpmailer -> Subject . "\r\n";
					$body = $phpmailer -> CreateBody();	
					$emailraw = $header . $body;
					$newsletters_emailraw = $emailraw;				
					
					$phpmailer = new fakemailer();
				}
			}
			
			return $phpmailer;
		}
		
		//update existing subscriber's email
		function profile_update($user_id = null) {
			global $wpdb, $Db, $Subscriber;
			
			if (!empty($user_id)) {			
				if ($newuserdata = $this -> userdata($user_id)) {
					$Db -> model = $Subscriber -> model;
					
					if ($subscriber = $Db -> find(array('user_id' => $user_id))) {
						$Db -> model = $Subscriber -> model;
						$Db -> save_field('email', $newuserdata -> user_email, array('id' => $subscriber -> id));
					}
				}
			}
			
			return true;
		}
		
		function register_form() {
			if ($this -> get_option('registercheckbox') == "Y") :
				?>
				
				<p class="newsletter">
	            	<label><input tabindex="21" <?php echo $check = ($this -> get_option('checkboxon') == "Y" || $_POST[$this -> pre . 'subscribe'] == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $this -> pre; ?>subscribe" value="Y" /> <?php echo __($this -> get_option('registerformlabel')); ?></label>
	            </p>
				
				<?php
			endif;
		}
		
		function comment_form($post_id = null) {		
			if ($this -> get_option('commentformcheckbox') == "Y") {
				?>
	            
	            <p class="newsletter">
	            	<label><input style="width:auto;" <?php echo ($this -> get_option('commentformautocheck') == "Y") ? 'checked="checked"' : ''; ?> id="newsletter<?php echo $post_id; ?>" type="checkbox" name="newsletter" value="1" /> <?php echo __($this -> get_option('commentformlabel')); ?></label>
	            </p>
	            
	            <?php	
			}
		}
		
		function comment_post($comment_id = null, $comment = null) {	
		
			if ($status = wp_get_comment_status($comment_id)) {		
				if ($status == false || $status == "spam") {
					return;
				}
			}
			
			if ($this -> get_option('commentformcheckbox') == "Y") {
				if (!empty($_POST['newsletter']) && $_POST['newsletter'] == 1) {
					if (!empty($comment_id)) {
						if ($comment = get_comment($comment_id)) {
							global $Mailinglist, $Subscriber, $SubscribersList;
							
							$data = array(
								'email' 			=> 	$comment -> comment_author_email,
								'mailinglists'		=>	array($this -> get_option('commentformlist')),
								'fromregistration'	=>	false,
								'justsubscribe'		=>	true,
								'active'			=>	(($this -> get_option('requireactivate') == "Y") ? "N" : "Y"),
							);
				
							if ($Subscriber -> save($data, true)) {
								$subscriber = $Subscriber -> get($Subscriber -> insertid, false);
								$this -> subscription_confirm($subscriber);									
								$this -> admin_subscription_notification($subscriber);
							}	
						}
					}
				}
			}
		}
		
		function ratereview_hook($days = 7) {		
			$this -> update_option('showmessage_ratereview', $days);
			$this -> delete_option('hidemessage_ratereview');
			
			return true;
		}
		
		function optimize_hook() {		
			global $wpdb;		
			$this -> check_tables();
					
			if (!empty($this -> tablenames)) {
				foreach ($this -> tablenames as $table) {
					$query = "OPTIMIZE TABLE `" . $table . "`";
					$wpdb -> query($query);
				}
			}
			
			return true;
		}
		
		function emailarchive_hook() {
			$emailarchive = $this -> get_option('emailarchive');
			if (!empty($emailarchive)) {		
				global $wpdb, $Html, $Email;
				$emailarchive_olderthan = $this -> get_option('emailarchive_olderthan');
				$interval = (empty($emailarchive_olderthan)) ? 90 : $emailarchive_olderthan;
				$condition = " WHERE DATE_SUB(NOW(), INTERVAL " . $interval . " DAY) > created";
				
				$outfile_file = 'emailarchive.txt';
				$outfile_path = $Html -> uploads_path() . DS . $this -> plugin_name . DS  . 'export' . DS;
				$outfile_full = $outfile_path . $outfile_file;
				
				$fh = fopen($outfile_full, "w");
				fclose($fh);
				@chmod($outfile_full, 0777);
				
				if (file_exists($outfile_full) && is_writable($outfile_full)) {
					$query = "SELECT * FROM " . $wpdb -> prefix . $Email -> table . $condition;
					$command = 'mysql -h ' . DB_HOST . ' -u ' . DB_USER . ' -p' . DB_PASSWORD . ' ' . DB_NAME . ' -N -B -e "' . $query . '" | sed "s/\t/,/g" >> ' . $outfile_full;
					$exec = exec($command, $output);
					
					$query = "DELETE FROM " . $wpdb -> prefix . $Email -> table . $condition;
					$wpdb -> query($query);
				}
			}
			
			return true;
		}
		
		function admin_notices() {
			global $Html;
		
			if (is_admin()) {
				$this -> check_uploaddir();
				$this -> get_managementpost();
				
				//Open the menu accordingly
				if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
					$Html -> wp_has_current_submenu($_GET['page']);
				}
			
				if (!$this -> ci_serial_valid() && (empty($_GET['page']) || $_GET['page'] != $this -> sections -> submitserial)) {
					$showmessage_ratereview = $this -> get_option('showmessage_ratereview');
					$hidemessage_ratereview = $this -> get_option('hidemessage_ratereview');
					
					if (!empty($showmessage_ratereview) && empty($hidemessage_ratereview)) {
						$rate_url = "https://wordpress.org/support/view/plugin-reviews/newsletters-lite?rate=5#postform";
						$works_url = "http://wordpress.org/plugins/newsletters-lite/?compatibility[version]=" . get_bloginfo("version") . "&compatibility[topic_version]=" . $this -> version . "&compatibility[compatible]=1";
						$message = sprintf(__('You have been using %s for %s days or more. Please consider %s it and say it %s on %s', $this -> plugin_name), '<a href="https://wordpress.org/plugins/newsletters-lite/" target="_blank">' . __('Tribulant Newsletters', $this -> plugin_name) . '</a>', $showmessage_ratereview, '<a href="' . $rate_url . '" target="_blank" class="button">' . __('Rating', $this -> plugin_name) . '</a>', '<a href="' . $works_url . '" target="_blank" class="button">' . __('Works', $this -> plugin_name) . '</a>', '<a href="http://wordpress.org/plugins/newsletters-lite/" target="_blank">WordPress.org</a>');
						$message .= ' <a style="text-decoration:none;" href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=ratereview') . '" class="newsletters-icon-delete-regular"></a>';
						$this -> render_message($message);
					}
				
					$hidemessage_submitserial = $this -> get_option('hidemessage_submitserial');
				
					if (empty($hidemessage_submitserial)) {
						$message = sprintf(__('To activate Newsletters PRO, please submit a serial key, else %s', $this -> plugin_name), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=submitserial') . '">' . __('continue using Newsletters LITE', $this -> plugin_name) . '</a>');
						$message .= ' <a class="button button-primary" id="' . $this -> pre . 'submitseriallink" href="' . admin_url('admin.php') . '?page=' . $this -> sections -> submitserial . '">' . __('Submit Serial Key', $this -> plugin_name) . '</a>';
						$message .= ' <a class="button button-secondary" href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">' . __('Upgrade to PRO', $this -> plugin_name) . '</a>';
						$message .= ' <a style="text-decoration:none;" href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=submitserial') . '" class="newsletters-icon-delete-regular"></a>';
						$this -> render_message($message);
						
						?>
			            
			            <script type="text/javascript">
						jQuery(document).ready(function(e) {
			                jQuery('#<?php echo $this -> pre; ?>submitseriallink').click(function() {					
								jQuery.colorbox({href:ajaxurl + "?action=<?php echo $this -> pre; ?>serialkey"});
								return false;
							});
			            });
						</script>
			            
			            <?php
			        }
				}
				
				global $queue_count;
				if (!empty($queue_count)) {
					if ($this -> get_option('scheduling') == "N") {
						$this -> render_message(__('There are ' . $queue_count . ' emails in the queue, please turn on email scheduling under Newsletters > Configuration to process them!', $this -> plugin_name));
					}
				}
				
				if (!empty($_GET[$this -> pre . 'updated'])) {
					$this -> render_message(stripslashes(urldecode($_GET[$this -> pre . 'message'])));
				}
				
				if (!empty($_GET[$this -> pre . 'error'])) {
					$this -> render_error(stripslashes(urldecode($_GET[$this -> pre . 'message'])));
				}
				
				if (!empty($_GET['newsletters_exportlink'])) {
					$message = sprintf(__('Your export is ready. %s', $this -> plugin_name), '<a href="' . $Html -> retainquery('wpmlmethod=exportdownload&file=' . $_GET['newsletters_exportlink'], $this -> url) . '">' . __('Download', $this -> plugin_name) . '</a>');
					$this -> render_message($message);
				}
				
				if (current_user_can('edit_plugins')) {
					$folder = $Html -> uploads_path();
					if (file_exists($folder)) {
						if (is_writable($folder)) {
							//all good
						} else {
							$this -> render_error(sprintf(__('Folder named "%s" is not writable', $this -> plugin_name), $folder));
						}
					} else {
						$this -> render_error(sprintf(__('Folder named "%s" does not exist', $this -> plugin_name), $folder));
					}
				}
				
				if (current_user_can('newsletters_queue')) {
					/* Inside the plugin sections only */
					if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
						global $wpdb, $Queue;
						if ($queueerrorcount = $wpdb -> get_var("SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Queue -> table . "` WHERE `error` IS NOT NULL AND `error` <> ''")) {
							$this -> render_error(sprintf(__('There are %s failed emails in the <a href="%s">queue</a> awaiting review.', $this -> plugin_name), $queueerrorcount, admin_url('admin.php?page=' . $this -> sections -> queue . '&orderby=error&order=asc')));
						}
					}
				}
				
				if (current_user_can('edit_plugins') && $this -> has_update() && (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates))) {
					$hideupdate = $this -> get_option('hideupdate');
					if (empty($hideupdate) || (!empty($hideupdate) && version_compare($this -> version, $hideupdate, '>'))) {
						$update = $this -> vendor('update');
						$update_info = $update -> get_version_info(true);
						$this -> render('update', array('update_info' => $update_info), true, 'admin');	
					}
				}
				
				flush();
			}
		}
		
		function feed_newsletters() {
			$this -> debugging(false);
			global $Db, $History;
			header("Content-Type: application/xml");
			$data = '<?xml version="1.0" encoding="UTF-8"?>';		
			$Db -> model = $History -> model;
			$emails = $Db -> find_all(array('sent' => "> 0"), false, array('modified', "DESC"));
			$data .= $this -> render('feed-newsletters', array('emails' => $emails), false, 'default');
			echo $data;
		}
		
		function end_session() {
			session_destroy();
		}
		
		function init() {	
			if (!empty($_REQUEST['newsletters_obstart'])) {
				ob_start();
			}
		
			global $Db, $Email, $Html, $History, $Mailinglist, $wpmlOrder, $Subscriber, $SubscribersList;			
			//$this -> init_textdomain();
			
			if (!session_id() && !headers_sent()) {
				session_start();
			}
		
			$wpmlmethod = (empty($_POST[$this -> pre . 'method'])) ? null : $_POST[$this -> pre . 'method'];
			$method = (empty($_GET[$this -> pre . 'method'])) ? $wpmlmethod : $_GET[$this -> pre . 'method'];
			
			if (!empty($_GET[$this -> pre . 'link'])) {
				if ($link = $this -> Link -> find(array('hash' => $_GET[$this -> pre . 'link']))) {
				
					$email_conditions = array('history_id' => $_GET['history_id']);
					if (!empty($_GET['subscriber_id'])) { $email_conditions['subscriber_id'] = $_GET['subscriber_id']; }
					if (!empty($_GET['user_id'])) { $email_conditions['user_id'] = $_GET['user_id']; }
				
					$Db -> model = $Email -> model;
					$Db -> save_field('read', "Y", $email_conditions);
					$Db -> model = $Email -> model;
					$Db -> save_field('status', "sent", $email_conditions);
				
					$click_data = array(
						'link_id'			=>	$link -> id,
						'history_id'		=>	$_GET['history_id'],
						'user_id'			=>	$_GET['user_id'],
						'subscriber_id'		=>	$_GET['subscriber_id'],
					);
					
					$link -> link = html_entity_decode($link -> link);
					
					if ($this -> Click -> save($click_data, true)) {
						header("Location: " . $link -> link);
						exit();
					}
				}
			}
			
			if (!empty($_GET['newsletters_method'])) {
				switch ($_GET['newsletters_method']) {
					case 'management_login'				:
						global $Subscriber, $Auth, $newsletters_errors;
						
						$newsletters_errors = array();
					
						if (!empty($_POST)) {
							if (!empty($_POST['email'])) {
								if ($Subscriber -> email_validate($_POST['email'])) {
									$Db -> model = $Subscriber -> model;
									
									if ($subscriber = $Db -> find(array('email' => $_POST['email']))) {
										if ($subscriberauth = $this -> gen_auth($subscriber -> id)) {
											$Auth -> set_emailcookie($_POST['email']);
											
											$Db -> model = $Subscriber -> model;
											$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
											
											$subject = __($this -> get_option('managementloginsubject'));
											$message = $this -> render_email('management-login', array('email' => $_POST['email'], 'subscriberauth' => $subscriberauth), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false);
											
											if ($this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false)) {
												$newsletters_errors[] = __('Authentication email has been sent, please check your inbox.', $this -> plugin_name);
											} else {
												$newsletters_errors[] = __('Authentication email could not be sent.', $this -> plugin_name);	
											}
										} else {
											$newsletters_errors[] = __('Authentication string could not be created.', $this -> plugin_name);	
										}
									} else {
										$newsletters_errors[] = __('Subscriber with that email address cannot be found, please try a different email address.', $this -> plugin_name);	
									}
								} else {
									$newsletters_errors[] = __('Please fill in a valid email address.', $this -> plugin_name);	
								}
							} else {
								$newsletters_errors[] = $emailfield -> error; 
							}
						} else {
							$newsletters_errors[] = __('No data was posted.', $this -> plugin_name);	
						}
						break;
					case 'delete_transient'				:
						if (!empty($_GET['transient'])) {
							delete_transient($_GET['transient']);
							$this -> redirect($this -> referer);
						}
						break;
					case 'hidemessage'					:
						if (!empty($_GET['message'])) {
							switch ($_GET['message']) {
								case 'submitserial'				:
									$this -> update_option('hidemessage_submitserial', true);
									break;
								case 'ratereview'				:
									$this -> update_option('hidemessage_ratereview', true);
									break;
							}
						}
						
						$this -> redirect($this -> referer);
						break;
					case 'hideupdate'					:
						if (!empty($_GET['version'])) {
							$this -> update_option('hideupdate', $_GET['version']);
							$this -> redirect($this -> referer);
						}
						break;
				}
			}
		
			if (!empty($method)) {
				switch ($method) {
					case 'exportdownload'					:		
						if (current_user_can('newsletters_welcome')) {			
							if (!empty($_GET['file'])) {
								$filename = urldecode($_GET['file']);
								$filepath = $Html -> uploads_path() . '/' . $this -> plugin_name . '/export/';
								$filefull = $filepath . $filename;
							
								if (file_exists($filefull)) {
									if(ini_get('zlib.output_compression')) { 
										ini_set('zlib.output_compression', 'Off'); 
									}	
									
									$contenttype = (function_exists('mime_content_type')) ? mime_content_type($filefull) : "text/csv";								
									header("Pragma: public");
									header("Expires: 0");
									header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
									header("Cache-Control: public", false);
									header("Content-Description: File Transfer");
									header("Content-Type: text/csv");
									header("Accept-Ranges: bytes");
									header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
									header("Content-Transfer-Encoding: binary");
									header("Content-Length: " . filesize($filefull));
									
									if ($fh = fopen($filefull, 'rb')){
										while (!feof($fh) && connection_status() == 0) {
											@set_time_limit(0);
											print(fread($fh, (1024 * 8)));
											flush();
										}
										
										fclose($fh);
										exit();
										die();
									}
								} else {
									$error = __('Export file could not be created', $this -> plugin_name);
								}
							} else {
								$error = __('No export file was specified', $this -> plugin_name);
							}
						} else {
							$error = __('You do not have permission to access exports', $this -> plugin_name);
						}
						
						if (!empty($error)) {
							wp_die($error);
						}
						break;
					case 'ajaxupload'						:					
						if (!empty($_GET['file'])) {
							$uploaddir = wp_upload_dir();
							$filename = urldecode($_GET['file']);
							$filepath = $uploaddir['basedir'] . DS . $this -> plugin_name . DS . 'uploadify' . DS;
							$filefull = $filepath . $filename;
						
							if (file_exists($filefull)) {
								if(ini_get('zlib.output_compression')) { 
									ini_set('zlib.output_compression', 'Off'); 
								}	
								
								$contenttype = (function_exists('mime_content_type')) ? mime_content_type($filefull) : "application/force-download";								
								header("Pragma: public");
								header("Expires: 0");
								header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
								header("Cache-Control: public", false);
								header("Content-Description: File Transfer");
								header("Content-Type: " . $contenttype);
								header("Accept-Ranges: bytes");
								header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
								header("Content-Transfer-Encoding: binary");
								header("Content-Length: " . filesize($filefull));
								
								if ($fh = fopen($filefull, 'rb')){
									while (!feof($fh) && connection_status() == 0) {
										@set_time_limit(0);
										print(fread($fh, (1024 * 8)));
										flush();
									}
									
									fclose($fh);
									exit();
								}
							}
						}
						break;
					case 'docron'			:
						if (!empty($_GET['auth'])) {
							if ($this -> get_option('servercronstring') == $_GET['auth']) {
								$this -> cron_hook();	
							} else {
								_e('Authentication string does not match.', $this -> plugin_name);	
							}
						} else {
							_e('No authentication string was specified.', $this -> plugin_name);	
						}
							
						exit();
						break;
					case 'defaultthemes'	:
						if (current_user_can('edit_plugins') || is_super_admin()) {
							$this -> initialize_default_themes();
							echo __('Stock templates have been added', $this -> plugin_name);
						} else {
							echo __('Please login as administrator for stock templates to be loaded', $this -> plugin_name);	
						}
						
						exit();
						break;
					case 'themebyname'		:
						if (!empty($_GET['name'])) {
							ob_start();
							include $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS . $_GET['name'] . DS . 'index.html';
							$content = ob_get_clean();				
							echo $content;
							exit();
						}
						break;
					case 'themepreview'		:				
						if (!empty($_GET['id'])) {
							global $Db, $Theme;
							$Db -> model = $Theme -> model;
							$subject = __('Newsletter Template Preview', $this -> plugin_name);
							$history_id = "123";
							
							if ($theme = $Db -> find(array('id' => $_GET['id']))) {
								header('Content-Type: text/html');
								echo do_shortcode(stripslashes($theme -> content));
							}
						}
					
						exit();
						break;
					case 'preview'			:
						$this -> render_email('preview', false, true, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('sending'));
						exit();
						break;
					case 'track'			:	
						global $Html;
									
						$Db -> model = $Email -> model;
						$Db -> save_field('read', "Y", array('eunique' => $_GET['id']));
						$Db -> save_field('status', "sent", array('eunique' => $_GET['id']));
						
						$tracking = $this -> get_option('tracking');
						$tracking_image = $this -> get_option('tracking_image');
						$tracking_image_file = $this -> get_option('tracking_image_file');
						
						//if (!empty($tracking) && $tracking == "Y") {
							if (!empty($tracking_image) && $tracking_image == "custom") {
								$tracking_image_full = $Html -> uploads_path() . DS . $this -> plugin_name . DS . $tracking_image_file;
								$imginfo = getimagesize($tracking_image_full);
								header("Content-type: " . $imginfo['mime']);
								readfile($tracking_image_full);		
							} else {
								header("Content-Type: image/jpeg");
								$image = imagecreate(1, 1);
								imagejpeg($image);
								imagedestroy($image);
							}
						//}
						
						exit();
						
						break;
					case 'offsite'			:	
						global $Html, $Subscriber, $Mailinglist;			
						
						$atts['list'] = $_GET['list'];
						$number = 'embed' . rand(999, 9999);
						$widget_id = 'newsletters-' . $number;
						$instance = $this -> widget_instance($number, $atts);
						$instance['ajax'] = "N";
						$instance['offsite'] = true;
						$success = false;
						
						$defaults = array(
							'list' 				=> 	$list_id, 
							'id' 				=> 	false,
							'lists'				=>	false,
							'ajax'				=>	$instance['ajax'],
							'button'			=>	$instance['button'],
							'captcha'			=>	$instance['captcha'],
							'acknowledgement'	=>	$instance['acknowledgement'],
						);
						
						$r = shortcode_atts($defaults, $atts);
						extract($r);
					
						?>
						
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
								<title><?php echo urldecode($_GET['title']); ?></title>
	                            
	                            <?php
	                            
								wp_enqueue_script('jquery');
								wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/' . $this -> plugin_name . '.js', array('jquery'), '1.0', false);	
								wp_enqueue_script('jquery-ui-tabs');
								wp_enqueue_script('jquery-ui-button', plugins_url() . '/' . $this -> plugin_name . '/js/jquery-ui-button.js', array('jquery'));
								wp_enqueue_script('jquery-watermark', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.watermark.js', array('jquery'), '1.0', true);
								wp_enqueue_style($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/views/' . $this -> get_option('theme_folder') . '/css/style.css', false, $this -> version, "all");
								
								?>
	                            
	                            <?php wp_head(); ?>
							</head>
							<body style="background:none;">
								<div id="<?php echo $widget_id; ?>" class="<?php echo $this -> pre; ?> widget_newsletters">
								<div id="<?php echo $widget_id; ?>-wrapper">
									<?php
														
									if (!empty($_POST)) {															
										$subscriber -> list_id = __($instance['list']);
										$subscriber -> email = $_POST['email'];
										
										if ($Subscriber -> optin($_POST)) {
											$success = true;
											
											if ($this -> get_option('subscriberedirect') == "Y") {
												$subscriberedirecturl = $this -> get_option('subscriberedirecturl');
												
												if ($subscribelist = $Mailinglist -> get($subscriber -> list_id)) {
													if (!empty($subscribelist -> subredirect)) {
														$subscriberedirecturl = $subscribelist -> subredirect;
													}
												}
												
												$this -> redirect($subscriberedirecturl, false, false, true);
											}
										}
									}
									
									if ($success == true) {
										echo '<p class="newsletters-acknowledgement">' . __($instance['acknowledgement']) . '</p>';
										
										if (empty($_GET['iframe'])) { 
											echo '<p><a href="" class="button" onclick="window.close();">' . __('Close this window', $this -> plugin_name) . '</a></p>'; 
											
											?>
											
											<script type="text/javascript">
				                            jQuery(document).ready(function() {
					                            if (jQuery.isFunction(jQuery.fn.button)) {
					                            	jQuery('.widget_newsletters .button').button();
					                            }
				                            });
				                            </script>
											
											<?php
										}
									} else {
										$iframe = (!empty($_GET['iframe'])) ? '&iframe=1' : '';
										$action = $Html -> retainquery($this -> pre . 'method=offsite' . $iframe . '&list=' . __($instance['list']), home_url());
										$errors = $Subscriber -> errors;
										$this -> render('widget', array('action' => $action, 'errors' => $errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), true, 'default');
									}
									
									?>
								</div>
								</div>
	                            
	                            <?php wp_footer(); ?>
							</body>
						</html>
						
						<?php
						
						exit();
						break;
					case 'optin'			:
						global $Subscriber, $Html, $Mailinglist;
						
						if (!empty($_POST)) {						
							if ($subscriber_id = $Subscriber -> optin($_POST)) {
								if ($paidlist_id = $Mailinglist -> has_paid_list($_POST['list_id'])) {
									$subscriber = $Subscriber -> get($subscriber_id, false);
									$paidlist = $Mailinglist -> get($paidlist_id, false);
									$this -> paidsubscription_form($subscriber, $paidlist, true);
								}
							
								if ($this -> get_option('subscriberedirect') == "Y") {							
									$subscriberedirecturl = $this -> get_option('subscriberedirecturl');
					
									if (!empty($_POST['list_id']) && (!is_array($_POST['list_id']) || count($_POST['list_id']) == 1)) {
										if ($subscribelist = $Mailinglist -> get($_POST['list_id'][0])) {
											if (!empty($subscribelist -> subredirect)) {
												$subscriberedirecturl = $subscribelist -> subredirect;
											}
										}
									}
									
									$this -> redirect($subscriberedirecturl, false, false, true);
								} else {
									$url = $Html -> retainquery($this -> pre . 'method=optin&success=1', $_SERVER['REQUEST_URI']);
									$this -> redirect($url);
								}
							}
						}
						break;
					case 'unsubscribe'		:
						global $Html;
					
						$querystring = 'method=unsubscribe&' . $this -> pre . 'subscriber_id=' . $_GET[$this -> pre . 'subscriber_id'] . '&' . $this -> pre . 'subscriber_email=' . $_GET[$this -> pre . 'subscriber_email'] . '&' . $this -> pre . 'mailinglist_id=' . $_GET[$this -> pre . 'mailinglist_id'] . '&authkey=' . $_GET['authkey'];
						$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
						$this -> redirect($url);
						exit();
						break;
					case 'manage'			:
						//redirect to the new management section
						$this -> redirect($this -> get_managementpost(true));
						
						exit();
						die();
						break;				
					case 'activate'			:
						global $wpdb, $Auth, $Mailinglist, $Html, $Db, $History, $HistoriesAttachment, $Email, $Subscriber, $Autoresponderemail, $Autoresponder, $AutorespondersList;
					
						if (!empty($_GET[$this -> pre . 'subscriber_email']) && !empty($_GET[$this -> pre . 'subscriber_id']) && !empty($_GET[$this -> pre . 'mailinglist_id'])) {
							$subscriber_id = $_GET[$this -> pre . 'subscriber_id'];
							$mailinglists = @explode(",", $_GET[$this -> pre . 'mailinglist_id']);
							
							$mailinglistsstring = $_GET[$this -> pre . 'mailinglist_id'];
							$subscriber = $Subscriber -> get($subscriber_id, false);
							$Auth -> set_emailcookie($subscriber -> email, "+30 days");
							
							if (empty($subscriber -> cookieauth)) {
								$subscriberauth = $Auth -> gen_subscriberauth();
								$Db -> model = $Subscriber -> model;
								$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
							} else {
								$subscriberauth = $subscriber -> cookieauth;
							}
							
							$Auth -> set_cookie($subscriberauth, "+30 days", true);
							$paidlists = false;
							
							foreach ($mailinglists as $list_id) {
								if ($mailinglist = $Mailinglist -> get($list_id, false)) {
									if ($mailinglist -> paid == "N" || empty($mailinglist -> paid)) {
										if ($SubscribersList -> save_field('active', "Y", array('subscriber_id' => $subscriber_id, 'list_id' => $list_id))) {										
											$msgtype = "success";
											$message = __('Subscription has been activated', $this -> plugin_name);
											$subscriber = $Subscriber -> get($subscriber_id, false);
											$subscriber -> mailinglist_id = $mailinglist -> id;
											$Db -> model = $Subscriber -> model;
											$Db -> save_field('ip_address', $_SERVER['REMOTE_ADDR'], array('id' => $subscriber -> id));
											$this -> autoresponders_send($subscriber, $mailinglist);
											do_action($this -> pre . '_subscriber_activated', $subscriber);
										}
									} else {
										$paidlists[] = $list_id;
									}
								}
							}
						} else {
							$msgtype = "error";
							$message = __('Subscription is invalid', $this -> plugin_name);
						}
						
						if (!empty($mailinglists) && count($mailinglists) == 1 && !empty($mailinglist -> redirect)) {
							$activateredirecturl = $mailinglist -> redirect;
						} else {
							if ($this -> get_option('customactivateredirect') == "Y") {
								$activateredirecturl = $this -> get_option('activateredirecturl');
							} else {
								$activateredirecturl = $Html -> retainquery('updated=1&success=' . __('Thank you for confirming your subscription.', $this -> plugin_name), $this -> get_managementpost(true));
							}
						}
						
						//If there are paid lists... we need to provide a payment form.
						if (!empty($paidlists)) {
							if ($this -> get_option('activationemails') == "single") {
								$message = sprintf(__('Thank you for confirming your subscription.<br/><br/>Since you subscribed to %s paid list, please click the "Pay Now" button below to make a payment and your subscription will then be active.', $this -> plugin_name), count($paidlists));
								$this -> redirect($this -> get_managementpost(true), "success", $message);
							} else {
								$subscriber = $Subscriber -> get($subscriber_id, false);
								$mailinglist = $Mailinglist -> get($paidlists[0], false);
								$this -> paidsubscription_form($subscriber, $mailinglist, true, "_self");
								exit();
							}
						} else {
							$this -> redirect($activateredirecturl, $msgtype, $message);
						}
						break;
					case 'paypal'			:										
						global $Html;
						$req = 'cmd=_notify-validate';
						
						foreach ($_POST as $pkey => $pval) {
							$pval = urlencode(stripslashes($pval));
							$req .= "&" . $pkey . "=" . $pval . "";
						}
						
						$paypalsandbox = $this -> get_option('paypalsandbox');
					
						$custom = unserialize(urldecode($_POST['custom']));						
						$ppurl = ($paypalsandbox == "Y") ? 'www.sandbox.paypal.com' : 'www.paypal.com';
						//$ppport = ($this -> get_option('paypalsandbox') == "Y") ? 443 : 443;
						$ppport = 443;
						$header = '';
						$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
						$header .= "Host: " . $ppurl . "\r\n";
						$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
						$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
						//$fhost = ($this -> get_option('paypalsandbox') == "Y") ? 'ssl://' . $ppurl : $ppurl;						
						$fhost = 'tls://' . $ppurl;
						$item_name = $_POST['item_name'];
						$item_number = $_POST['item_number'];
						$payment_status = $_POST['payment_status'];
						$payment_amount = $_POST['mc_gross'];
						$payment_currency = $_POST['mc_currency'];
						$txn_id = $_POST['txn_id'];
						$txn_type = $_POST['txn_type'];
						$receiver_email = $_POST['receiver_email'];
						$payer_email = $_POST['payer_email'];	
						
						$verified = false;
						
						$message = sprintf(__('Received IPN call - %s', $this -> plugin_name), $req);
						
						if (function_exists('curl_init')) {							
							$ch = curl_init('https://' . $ppurl . '/cgi-bin/webscr');
							curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
							curl_setopt($ch, CURLOPT_POST, 1);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
							curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
							curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));							
							$fullresult = curl_exec($ch);
							
							if (curl_errno($ch)) {
								$this -> log_error(sprintf(__('PayPal IPN: Curl error - %s', $this -> plugin_name), curl_error($ch)));
							}
							
							if (strcmp($fullresult, "VERIFIED") == 0) {	
								$verified = true;	
							}
							
							curl_close($ch);
						} else {
							$fp = fsockopen($fhost, $ppport, $errno, $errstr, 30);
							
							if (!$fp) {
								$message = __('An HTTP error has occurred. PayPal cannot be contacted.', $this -> plugin_name);
								$this -> log_error(sprintf(__('PayPal IPN: %s - %s', $this -> plugin_name), $errno, $errstr));
							} else {
								fputs($fp, $header . $req);
								
								while (!feof($fp)) {
									$res = fgets($fp, 1024);									
									
									if (strcmp($res, "VERIFIED") == 0) {
										$verified = true;
									}
								}
								
								fclose($fp);
							}
						}	
						
						$doupdate = false;
						if (!empty($verified) && $verified == true) {			
							switch ($payment_status) {
								case 'Failed'				:
									$message = __('The payment has failed. Please try again', $this -> plugin_name);
									break;
								case 'Denied'				:
									$message = __('The payment has been denied. This payment could already be pending', $this -> plugin_name);
									break;
								default						:
									if (!empty($custom['subscriber_id']) && !empty($custom['mailinglist_id'])) {
										switch ($txn_type) {
											case 'subscr_payment'		:
												if ($payment_status == "Pending") {
													$message = __('Thank you for your PayPal subscription. Your payment is currently pending. Please wait for the merchant to accept it', $this -> plugin_name);	
												} elseif ($payment_status == "Completed") {
													$doupdate = true;
												}
												break;
											case 'subscr_cancel'		:
												$mailinglists = @explode(",", $_GET['mailinglist_id']);
												foreach ($mailinglists as $list_id) {
													$sl_conditions = array('subscriber_id' => $custom['subscriber_id'], 'list_id' => $list_id);
													$SubscribersList -> save_field('active', "N", $sl_conditions);
												}
												$message = __('PayPal subscription has been cancelled', $this -> plugin_name);
												break;
											default						:												
												if ($payment_status == "Completed" || ($_POST['test_ipn'] && $payment_status == "Pending")) {													
													$doupdate = true;
												}
												break;
										}
									} else {
										$message = __('Subscriber or list ID empty', $this -> plugin_name);
									}
									break;
							}
						} else {
							//why on earth?
							$message = __('PayPal has marked the transaction as invalid', $this -> plugin_name);
						}		
							
						//everything is fine, lets continue
						if ($doupdate == true) {
							$subscriber = $Subscriber -> get($custom['subscriber_id'], false);
							$list_id = $custom['mailinglist_id'];
							$mailinglist = $Mailinglist -> get($list_id, false);
							$subscriber -> mailinglist_id = $mailinglist -> id;

							if ($payment_amount == $mailinglist -> price) {								
								$sl_conditions = array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id);
								
								$subscriberslist = $SubscribersList -> find($sl_conditions);
								$SubscribersList -> save_field('active', "Y", $sl_conditions);
								$SubscribersList -> save_field('paid', "Y", $sl_conditions);
								$SubscribersList -> save_field('paid_date', $Html -> gen_date(), $sl_conditions);
								$SubscribersList -> save_field('paid_sent', "0", $sl_conditions);
								$SubscribersList -> save_field('modified', $Html -> gen_date(), $sl_conditions);
								
								if ($this -> get_option('paypalsubscriptions') == "Y") {
									$SubscribersList -> save_field('ppsubscription', "Y", $sl_conditions);
								}
								
								$this -> autoresponders_send($subscriber, $mailinglist);
								
								$orderdata = array(
									'list_id'				=>	$list_id,
									'subscriber_id'			=>	$custom['subscriber_id'],
									'completed'				=>	'Y',
									'amount'				=>	$mailinglist -> price,
									'product_id'			=>	$subscriberslist -> rel_id,
									'order_number'			=>	$subscriberslist -> rel_id,
									'reference'				=>	$txn_id,
									'pmethod'				=>	'pp',
								);
								
								if ($wpmlOrder -> save($orderdata, true)) {									
									//success
								}
								
								$message = __('Payment received and subscription activated', $this -> plugin_name);
								
								if ($this -> get_option('adminordernotify') == "Y") {
									$subscriber -> mailinglists = array($list_id);
									$to = new stdClass();
									$to -> email = $this -> get_option('adminemail');
									$subject = $this -> et_subject('order', $subscriber);
									$fullbody = $this -> et_message('order', $subscriber);
									$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
									$this -> execute_mail($to, false, $subject, $message, $attachment = false, $history_id = false, false, false);
								}
							}
						} else {
							//Send a message to the administrator?
							$this -> log_error(sprintf(__('PayPal IPN: %s', $this -> plugin_name), $message));
						}
						
						if (!empty($message)) {						
							?>
							
							<script type="text/javascript">
							alert('<?php echo $message; ?>');
							window.location = '<?php echo $this -> get_managementpost(true); ?>';
							</script>
							
							<?php
							
							exit();
						}
						break;
					case 'twocheckout'		:							
						if (!empty($_POST['order_number'])) {
							if ($_POST['credit_card_processed'] == "Y") {
								$vendorid = $this -> get_option('tcovendorid');
								$secret = $this -> get_option('tcosecret');
								$total = $_POST['total'];
							
								if ($_POST['demo'] == "Y" && $this -> get_option('tcodemo') == "Y") {
									$ordernumber = 1;
								} else {
									$ordernumber = $_POST['order_number'];
								}
								
								$mykey = $secret . $vendorid . $ordernumber . $total;
								$mykey = strtoupper(md5($mykey));
								
								if ($mykey === $_POST['key']) {								
									$subscriberid = $_POST['subscriber_id'];
									$subscriber = $Subscriber -> get($subscriberid, false);
									$mailinglist_id = $_POST['mailinglist_id'];
									$mailinglist = $Mailinglist -> get($mailinglist_id, false);
									$subscriber -> mailinglist_id = $mailinglist -> id;
									$this -> autoresponders_send($subscriber, $mailinglist);										
									$sl_conditions = array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id);
									$SubscribersList -> save_field('active', "Y", $sl_conditions);
									$SubscribersList -> save_field('paid', "Y", $sl_conditions);
									$SubscribersList -> save_field('paid_date', $this -> gen_date(), $sl_conditions);
									$SubscribersList -> save_field('paid_sent', "0", $sl_conditions);
									
									$orderdata = array(
										'list_id'				=>	$mailinglist_id,
										'subscriber_id'			=>	$subscriberid,
										'completed'				=>	'Y',
										'amount'				=>	$_POST['total'],
										'product_id'			=>	1,
										'order_number'			=>	$_POST['order_number'],
										'reference'				=>	$ordernumber,
										'pmethod'				=>	'2co',
									);
									
									if ($wpmlOrder -> save($orderdata, true)) {
										//success
									}	
									
									if ($this -> get_option('adminordernotify') == "Y") {
										$to -> email = $this -> get_option('adminemail');
										$subject = $this -> et_subject('order', $subscriber);
										$fullbody = $this -> et_message('order', $subscriber);
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
										$this -> execute_mail($to, false, $subject, $message, $attachment = false, $history_id = false, false, false);
									}
									
									$msgtype = 'success';
									$message = __('Payment received and subscription activated', $this -> plugin_name);
								} else {
									$msgtype = 'error';
									$message = __('Hash encryption failed! Please contact us', $this -> plugin_name);
								}
							} else {
								$msgtype = 'error';
								$message = __('Credit card could not be processed, please try again.', $this -> plugin_name);
							}
						}
						
						?>
	
						<?php if (!empty($message)) : ?>	
							<?php $this -> redirect($this -> get_managementpost(true), $msgtype, $message); ?>
						<?php endif; ?>
						
						<?php
						break;
					case 'bounce'			:					
						switch ($_GET['type']) {
							case 'sns'			:																						
								$json = json_decode(file_get_contents("php://input"));
								if (!empty($json)) {
									$json_message = json_decode($json -> Message);
									
									if ($json -> Type == "SubscriptionConfirmation") {				
										$subscribe_url = $json -> SubscribeURL;
										
										$this -> log_error(sprintf(__('Amazon SNS subscription confirm: %s', $this -> plugin_name), $subscribe_url));
															
										if (function_exists('curl_init')) {
											if ($curl_handle = curl_init($subscribe_url)) {
												curl_setopt($curl_handle, CURLOPT_URL, $subscribe_url);
												curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
												curl_exec($curl_handle);
												curl_close($curl_handle);
											}
										} else {											
											$raw_response = wp_remote_request($subscribe_url);	
										}
									} elseif ($json -> Type == "Notification") {									
										if ($json_message -> notificationType == "Bounce") {											
											if (!empty($json_message -> bounce -> bouncedRecipients)) {
												foreach ($json_message -> bounce -> bouncedRecipients as $recipient) {
													if ($recipient -> action == "failed") {
														$this -> log_error(sprintf(__('Amazon SNS bounce: %s', $this -> plugin_name), $recipient -> emailaddress));
														$result = $this -> bounce($recipient -> emailAddress, "sns");
													}
												}
											}
										} elseif ($json_message -> notificationType == "Complaint") {											
											if (!empty($json_message -> complaint -> complainedRecipients)) {
												foreach ($json_message -> complaint -> complainedRecipients as $recipient) {
													$this -> log_error(sprintf(__('Amazon SNS complaint: %s', $this -> plugin_name), $recipient -> emailaddress));
													$result = $this -> bounce($recipient -> emailAddress, "sns");
												}
											}
										}
									}
								}								
								break;
							default				:
								$this -> bounce($_GET['em']);
								break;
						}																		
						break;
					case 'newsletter'		:
						global $Db, $History, $Subscriber;
						header('Content-type: text/html; charset=utf-8');
					
						if (!empty($_GET['id'])) {
							$Db -> model = $History -> model;						
							if ($email = $Db -> find(array('id' => $_GET['id']))) {								
								$Db -> model = $Subscriber -> model;
								$subscriber = $Subscriber -> get($_GET['subscriber_id'], false);
								
								if (true || !empty($subscriber) || !empty($_GET['fromfeed']) || !empty($_GET['history'])) {																
									$subscriber -> mailinglist_id = $_GET['mailinglist_id'];
									$authkey = $_GET['authkey'];
									
									if (true || $authkey == $subscriber -> authkey || !empty($_GET['fromfeed']) || !empty($_GET['history'])) {
										$message = $this -> render('newsletter', array('email' => $email, 'subscriber' => $subscriber), false, 'default');
										$content = $this -> render_email('send', array('print' => $_GET['print'], 'message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $_GET['id']), false, true, true, $email -> theme_id);
										$output = "";										
										ob_start();										
										$thecontent = do_shortcode(stripslashes($content));
										echo apply_filters('wpml_online_newsletter', $thecontent, $subscriber);				
										$output = ob_get_clean();
										echo $this -> process_set_variables($subscriber, $user, $output, $email -> id);
										exit();										
									} else {
										$message = __('Authentication failed, please try again.', $this -> plugin_name);
									}
								} else {
									$message = __('Subscriber cannot be read.', $this -> plugin_name);
								}
							} else {
								$message = __('Newsletter cannot be read', $this -> plugin_name);
							}
						} else {
							$message = __('No newsletter was specified', $this -> plugin_name);
						}
						
						if (!empty($message)) {						
							?>
							
							<script type="text/javascript">
							alert('<?php echo $message; ?>');
							</script>
							
							<?php
						}
						break;
				}
			}
		}
		
		function wp_head() {
			$this -> render('head');
			
			global $wpmljavascript;
			if (!empty($wpmljavascript)) {
				echo $wpmljavascript;
			}
		}
		
		function wp_footer() {		
			$this -> render('footer');
		}
		
		function delete_user($user_id = null) {	
			global $Db, $Subscriber;
		
			if (!empty($user_id)) {
				$Db -> model = $Subscriber -> model;
				if ($subscriber = $Db -> find(array('user_id' => $user_id))) {
					if (!empty($subscriber)) {
						$Subscriber -> delete($subscriber -> id);
					}
				}
				
				$this -> Click -> delete_all(array('user_id' => $user_id));
			}
		}
		
		function user_register($user_id = null) {
			global $Db, $Mailinglist, $Subscriber, $SubscribersList;
		
			if (!empty($user_id)) {		
				if ($userdata = $this -> userdata($user_id)) {						
					if (!empty($_POST[$this -> pre . 'subscribe']) && $_POST[$this -> pre . 'subscribe'] == "Y") {
						$autosubscribelist = $this -> get_option('autosubscribelist');
						if (!empty($autosubscribelist)) {									
							$data = array(
								'email' 			=> 	$userdata -> user_email,
								'registered'		=>	'Y',
								'username'			=>	$userdata -> user_login,
								'mailinglists'		=>	$autosubscribelist,
								'fromregistration'	=>	true,
								'justsubscribe'		=>	true,
								'user_id'			=>	$user_id,
								'active'			=>	(($this -> get_option('requireactivate') == "Y") ? "N" : "Y"),
							);
				
							if ($Subscriber -> save($data, true)) {
								$subscriber = $Subscriber -> get($Subscriber -> insertid, false);
								$this -> subscription_confirm($subscriber);									
								$this -> admin_subscription_notification($subscriber);
							}
						}
					}
				}
			}
			
			return true;
		}
		
		function dashboard_setup() {
			if (current_user_can('newsletters_welcome')) {
				wp_add_dashboard_widget($this -> plugin_name, __('Newsletters Overview', $this -> plugin_name), array($this, 'dashboard_widget'));					
				global $wp_meta_boxes;
				$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
				$example_widget_backup = array($this -> plugin_name => $normal_dashboard[$this -> plugin_name]);
				unset($normal_dashboard[$this -> plugin_name]);
				$sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);
				$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
			}
		}
		
		function dashboard_widget() {
			$this -> render('dashboard', false, true, 'admin');
		}
		
		function do_meta_boxes($type = 'post') {
			global $Metabox, $Html;
			
			$post_types = $this -> get_custom_post_types();
			
			if (!empty($type)) {
				if ($type == "post" || $type == "page" || (!empty($post_types) && array_key_exists($type, $post_types))) {		
					if (current_user_can('newsletters_send') && $this -> get_option('sendasnewsletterbox') == "Y") {
						add_meta_box($this -> pre . 'div', __('Send as Newsletter', $this -> plugin_name) . $Html -> help(__('Use this box to send a post, page or custom post type as a newsletter to your subscribers. You can choose the list(s) to send to, the template to use, etc. All emails sent this way are queued and you can find them under Newsletters > Email Queue after the post, page or custom post type has been saved.', $this -> plugin_name)), array($Metabox, 'write_advanced'), $type, 'normal', 'high');							
					}
				}
			}
			
			return;
		}
		
		function activateaction_hook() {			
			global $wpdb, $SubscribersList, $Subscriber, $Db;
			$this -> activateaction_scheduling();
			$activateaction = $this -> get_option('activateaction');
			
			if (!empty($activateaction)) {			
				switch ($activateaction) {
					case 'remind'				:
						$activatereminder = $this -> get_option('activatereminder');
						if (!empty($activatereminder)) {
							$query = "SELECT * FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `active` = 'N' AND `reminded` = '0' AND `created` <= DATE_SUB(CURDATE(), INTERVAL " . $activatereminder . " DAY)";
							
							if ($subscriptions = $wpdb -> get_results($query)) {
								foreach ($subscriptions as $subscription) {
									$subscriber = $Subscriber -> get($subscription -> subscriber_id);
									$_POST['list_id'] = array($subscription -> list_id);
									$subscriber -> mailinglists = array($subscription -> list_id);
									$this -> subscription_confirm($subscriber);

									$Db -> model = $SubscribersList -> model;
									$Db -> save_field('reminded', "1", array('rel_id' => $subscription -> rel_id));
								}
							}
						}
						break;
					case 'delete'				:
						$activatedelete = $this -> get_option('activatedelete');
						if (!empty($activatedelete)) {
							$query = "SELECT * FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `active` = 'N' AND `created` <= DATE_SUB(CURDATE(), INTERVAL " . $activatedelete . " DAY)";							
							if ($subscriptions = $wpdb -> get_results($query)) {							
								foreach ($subscriptions as $subscription) {
									$Db -> model = $SubscribersList -> model;
									$Db -> delete_all(array('rel_id' => $subscription -> rel_id));
								}
							}
						}
						break;
					case 'none'					:
					default						:
						//do nothing...
						break;
				}
			}
		}
		
		function latestposts_hook($id = null, $preview = false) {			
			global $wpdb, $post, $Db, $Latestpost, $Template, $Html, $History, $Mailinglist, $Queue, $Subscriber, $SubscribersList;
			
			if (!empty($id) && $latestpostssubscription = $this -> Latestpostssubscription -> find(array('id' => $id))) {				
				$post_criteria = $this -> get_latestposts($latestpostssubscription);
				
				if (!empty($latestpostssubscription -> groupbycategory) && $latestpostssubscription -> groupbycategory == "Y") {
					$categories_args = array(
						'type'						=>	'post',
						'child_of'					=>	false,
						'parent'					=>	false,
						'orderby'					=>	"name",
						'order'						=>	"asc",
						'hide_empty'				=>	true,
						'hierarchical'				=>	true,
						'exclude'					=>	false,
						'include'					=>	$post_criteria['category'],
						
					);
					
					$categories_args = apply_filters('newsletters_latest_posts_categories_args', $categories_args);
					
					if ($categories = get_categories($categories_args)) {
						global $shortcode_categories;
						$c = 0;
					
						foreach ($categories as $category) {
							
							$post_criteria['category'] = $category -> cat_ID;
							$posts = get_posts($post_criteria);							
							
							if (!empty($posts)) {
								$shortcode_categories[$c]['category'] = $category;
								$shortcode_categories[$c]['posts'] = $posts;
							}
							
							$c++;
						}
					}
				}
					
				if (!empty($shortcode_categories) || $posts = get_posts($post_criteria)) {					
					if (!empty($posts) || !empty($shortcode_categories)) {
						if ($this -> language_do()) {							
							foreach ($posts as $pkey => $post) {
								$posts[$pkey] = $this -> language_use($latestpostssubscription -> language, $post, false);	
							}
						}
						
						$subject = $latestpostssubscription -> subject;
						global $shortcode_posts;
						$shortcode_posts = $posts;
						$content = $this -> et_message('latestposts', false, $latestpostssubscription -> language);
						$attachment = false;
						$post_id = false;
						
						$history_data = array(
							'subject'			=>	$subject,
							'message'			=>	$content,
							'theme_id'			=>	$latestpostssubscription -> theme_id,
							'mailinglists'		=>	$latestpostssubscription -> lists,
							'attachment'		=>	"N",
							'attachmentfile'	=>	false,
						);
						
						if (!empty($latestpostssubscription -> history_id)) {
							$history_data['id'] = $latestpostssubscription -> history_id;
						}
							
						$Db -> model = $History -> model;
						$history_data['sent'] = 1;
						$History -> save($history_data, false);
						$history_id = $History -> insertid;						
						//$this -> update_option('latestposts_historyid', $history_id);
						
						$this -> Latestpostssubscription -> save_field('history_id', $history_id, array('id' => $latestpostssubscription -> id));
						
						if (!empty($preview) && $preview == true) {							
							$subscriber_id = $Subscriber -> admin_subscriber_id();
							$subscriber = $Subscriber -> get($subscriber_id);
							$subscriber -> mailinglists = $email -> mailinglists;
							$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history_id . date_i18n("YmdH", time()));
							$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $latestpostssubscription -> theme_id);
							
							$output = "";
							ob_start();
							echo do_shortcode(stripslashes($message));
							$output = ob_get_clean();
							ob_start();
							echo $this -> process_set_variables($subscriber, $user, $output, $history_id);
							$output = ob_get_clean();
							
							return $output;
						} else {							
							$sentmailscount = 0; //number of emails sent
							$q_queries = array();
							
							if ($mailinglists = maybe_unserialize($latestpostssubscription -> lists)) {
								$mailinglistscondition = "(";
								$m = 1;
								
								foreach ($mailinglists as $mailinglist_id) {
									$mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";
									if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
									$m++;	
								}
								
								$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
								. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
								. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
								. $wpdb -> prefix . $SubscribersList -> table . " ON "
								. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id WHERE "
								. $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
								
								$subscribers = $wpdb -> get_results($query);
								
								if (!empty($subscribers)) {
									foreach ($subscribers as $subscriber) {
										$this -> remove_server_limits();
										$subscriber -> mailinglist_id = $mailinglists[0];
													
										if ($this -> get_option('scheduling') == "Y") {
											$q_queries[] = $Queue -> save($subscriber, false, $subject, $content, $attachment, $post_id, $history_id, true, $latestpostssubscription -> theme_id);
											$sentmailscount++;
										} else {
											$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history_id . date_i18n("YmdH", time()));
											$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $latestpostssubscription -> theme_id);
											$this -> execute_mail($subscriber, false, $subject, $message, $attachment, $history_id, $eunique);
											$sentmailscount++;
										}
									}
									
									if (!empty($q_queries)) {									
										foreach ($q_queries as $q_query) {
											if (!empty($q_query)) {
												$wpdb -> query($q_query);
											}
										}
									}	
								}
							}
							
							if (!empty($sentmailscount)) {															
								if (!empty($shortcode_categories)) {									
									foreach ($shortcode_categories as $shortcode_category) {
										if (!empty($shortcode_category['posts'])) {
											foreach ($shortcode_category['posts'] as $post) {
												$Db -> model = $Latestpost -> model;
												$Db -> save(array('post_id' => $post -> ID, 'lps_id' => $latestpostssubscription -> id), true);
											}
										}
									}	
								} else {									
									foreach ($shortcode_posts as $post) {
										$Db -> model = $Latestpost -> model;
										$Db -> save(array('post_id' => $post -> ID, 'lps_id' => $latestpostssubscription -> id), true);
									}
								}
							}
						}
					}
				} else {
					echo __('No posts with the specified criteria could be found. Are there new posts available to be sent?', $this -> plugin_name);
				}
			} else {
				echo __('No latest posts subscription was specified', $this -> plugin_name);
			}
			
			echo $sentmailscount . ' ' . __('emails were sent/queued.', $this -> plugin_name);
			
			return false;
		}
		
		function importusers_hook() {			
			global $wpdb, $Db, $Mailinglist, $Subscriber, $Unsubscribe, $Bounce, $Field;
			$Db -> model = $Mailinglist -> model;
			$importcount = 0;
			$importuserslists = $this -> get_option('importuserslists');
			
			if (!empty($importuserslists)) {			
				$userslistquery = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE `user_id` != '0'";
				$userslist = $wpdb -> get_var($userslistquery);
						
				$users_arguments = array(
					'blog_id'				=>	$GLOBALS['blog_id'],
					'exclude'				=>	$userslist,
					'fields'				=>	array('ID', 'user_email', 'user_login'),
				);
			
				if ($users = get_users($users_arguments)) {
					$importusersrequireactivate = $this -> get_option('importusersrequireactivate');
					
					foreach ($users as $user) {					
						if (!user_can($user -> ID, 'pending')) {
						
							// check unsubscribe
							$Db -> model = $Unsubscribe -> model;
							if (!$Db -> find(array('email' => $user -> user_email))) {
								// check bounce
								$Db -> bounce = $Bounce -> model;
								if (!$Db -> find(array('email' => $user -> user_email))) {
									$Db -> model = $Subscriber -> model;
									$user_role = $this -> user_role($user -> ID);
									
									if (!empty($importuserslists[$user_role])) {
										$subscriber = array(
											'id'				=>	false,
											'email'				=>	$user -> user_email,
											'mailinglists'		=>	$importuserslists[$user_role],
											'registered'		=>	"Y",
											'username'			=>	$user -> user_login,
											'fromregistration'	=>	true,
											'justsubscribe'		=>	true,
											'active'			=>	((empty($importusersrequireactivate) || $importusersrequireactivate == "Y") ? "N" : "Y"),
											'user_id'			=>	$user -> ID,
										);
										
										$fieldsquery = "SELECT `id`, `slug` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list'";
										$fields = $wpdb -> get_results($fieldsquery);
										
										if (!empty($fields)) {
											$importusersfields = $this -> get_option('importusersfields');
											$importusersfieldspre = $this -> get_option('importusersfieldspre');
											
											foreach ($fields as $field) {
												if (!empty($importusersfieldspre[$field -> id]) && $usermeta = get_user_meta($user -> ID, $importusersfieldspre[$field -> id], true)) {
													$subscriber[$field -> slug] = $usermeta;
												} elseif (!empty($importusersfields[$field -> id]) && $usermeta = get_user_meta($user -> ID, $importusersfields[$field -> id], true)) {
													$subscriber[$field -> slug] = $usermeta;
												}
											}
										}					
										
										if ($Subscriber -> save($subscriber, true)) {
											$importcount++;	
										}	
									}		
								}
							}
						}
					}
				}
			}
			
			echo $importcount . ' ' . __('users were imported as subscribers.', $this -> plugin_name);
		}
		
		function captchacleanup_hook() {			
			if ($this -> is_plugin_active('captcha')) {
				if (class_exists('ReallySimpleCaptcha')) {
					if ($captcha = new ReallySimpleCaptcha()) {
						$captcha -> cleanup(60);
					}
				}
			}
		}
		
		function autoresponders_hook() {				
			//update scheduling
			$this -> autoresponder_scheduling();
			$addedtoqueue = 0;
			
			/* Do the Autoresponders */
			global $wpdb, $Db, $Queue, $Html, $History, $HistoriesAttachment, $Subscriber, $SubscribersList, $Autoresponder, $Autoresponderemail;
			
			$query = "SELECT ae.id, sl.list_id, sl.subscriber_id, 
			ae.autoresponder_id FROM " . $wpdb -> prefix . $Autoresponderemail -> table . " ae LEFT JOIN 
			" . $wpdb -> prefix . $SubscribersList -> table . " sl ON ae.subscriber_id = sl.subscriber_id AND ae.list_id = sl.list_id 
			WHERE sl.active = 'Y' AND ae.status = 'unsent' 
			AND ae.senddate <= '" . date_i18n("Y-m-d H:i:s", time()) . "';";
			
			$autoresponderemails = $wpdb -> get_results($query);
			
			if (!empty($autoresponderemails)) {					
				foreach ($autoresponderemails as $ae) {				
					/* The History Email */
					$query = "SELECT " . $wpdb -> prefix . $History -> table . ".id, " 
					. $wpdb -> prefix . $History -> table . ".subject, " 
					. $wpdb -> prefix . $History -> table . ".message, " 
					. $wpdb -> prefix . $History -> table . ".theme_id FROM " 
					. $wpdb -> prefix . $History -> table . " LEFT JOIN " 
					. $wpdb -> prefix . $Autoresponder -> table . " ON " . $wpdb -> prefix . $History -> table . ".id = " . $wpdb -> prefix . $Autoresponder -> table . ".history_id WHERE " 
					. $wpdb -> prefix . $Autoresponder -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";
					
					$history = $wpdb -> get_row($query);					
					$history -> attachments = array();
					
					/* Attachments */
					$attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";
					
					if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
						foreach ($attachments as $attachment) {
							$history -> attachments[] = array(
								'id'					=>	$attachment -> id,
								'title'					=>	$attachment -> title,
								'filename'				=>	$attachment -> filename,
							);	
						}
					}
					
					/* The Subscriber */
					$Db -> model = $Subscriber -> model;
					$subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
					$subscriber -> mailinglist_id = $ae -> list_id;
					
					/* The Message */
					$eunique = $Html -> eunique($subscriber, $history -> id);
					
					/* Send the email */
					$Db -> model = $Email -> model;
					$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
					
					if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique)) {								
						$Db -> model = $Autoresponderemail -> model;
						$Db -> save_field('status', "sent", array('id' => $ae -> id));
						$addedtoqueue++;
					}
				}
			}
			
			echo $addedtoqueue . ' ' . __('autoresponder emails have been sent out.', $this -> plugin_name);
		}
	
	    function pop_hook() {			
			//update scheduling
	        $this -> pop_scheduling();
	
	        if ($this -> get_option('bouncemethod') == "pop") {
	            $this -> bounce(false, "pop");
	        }
	
	        return;
	    }
		
		function cron_hook() {			
			do_action('newsletters_cron_fired');
			
			if ($transient = get_transient('newsletters_cron')) {
				echo '<p>' . __('No emails sent out. Cron is already running, please wait a while', $this -> plugin_name) . '</p>';
				echo '<p><a class="button" href="' . admin_url('admin.php?page=' . $this -> sections -> queue . '&newsletters_method=delete_transient&transient=newsletters_cron') . '">' . __('Reset Transient', $this -> plugin_name) . '</a></p>';
			} else {
				$schedulecrontype = $this -> get_option('schedulecrontype');
				if (empty($schedulecrontype) || $schedulecrontype == "wp") {
					$schedules = $this -> cron_schedules($schedules);
					$interval = $this -> get_option('scheduleinterval');
					$expiration = ($schedules[$interval]['interval']);
				} else {
					$expiration = 150;
				}					
				set_transient('newsletters_cron', true, ($expiration));
			
				global $wpdb, $Db, $Email, $History, $Subscriber, $SubscribersList, $Queue;
				$emailssent = 0;
			
				//update scheduling
				$this -> scheduling();
				
				$innodbquery = "SHOW TABLE STATUS WHERE name = '" . $wpdb -> prefix . $Queue -> table . "'";
				if ($table = $wpdb -> get_row($innodbquery)) {
					if (!empty($table) && $table -> Engine != "InnoDB") {
						$tablequery = "ALTER TABLE `" . $wpdb -> prefix . $Queue -> table . "` ENGINE = 'InnoDB'";
						$wpdb -> query($tablequery);
					}
				}
			
				//ensure that scheduling has been turned on
				if ($this -> get_option('scheduling') == "Y") {
					$emailsperinterval = $this -> get_option('emailsperinterval');
					$wpdb -> query("START TRANSACTION");
					$queuesendorder = $this -> get_option('queuesendorder');
					$queuesendorderby = $this -> get_option('queuesendorderby');
					$emailsquery = "SELECT * FROM `" . $wpdb -> prefix . $Queue -> table . "` WHERE `senddate` < '" . date_i18n("Y-m-d H:i:s", time()) . "' ORDER BY `error` ASC, `" . $queuesendorderby . "` " . $queuesendorder . " LIMIT " . $emailsperinterval . " FOR UPDATE";
					
					//retrieve all the queue emails for this execution
					if ($emails = $wpdb -> get_results($emailsquery)) {					
						if ($this -> get_option('schedulenotify') == "Y" && $this -> get_option('scheduling') == "Y") {
							$subscriber_id = $Subscriber -> admin_subscriber_id($_POST['mailinglists']);
							$subscriber = $Subscriber -> get($subscriber_id, false);
							$subject = $this -> et_subject('schedule', $subscriber);
							$fullbody = $this -> et_message('schedule', $subscriber);	
							$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
							$this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false);
						}
						
						$subscriber = false;
						$user = false;
						$userids = array();
						$useremails = array();
						$subscriberids = array();
						$subscriberemails = array();
						$emailssent = 0;
					
						foreach ($emails as $email) {
							$this -> remove_server_limits();
							
							$historyquery = "SELECT `post_id` FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $email -> history_id . "'";
							$history_post_id = $wpdb -> get_var($historyquery);
							
							if (!empty($history_post_id)) {
								if ($getpost = get_post($history_post_id)) {
									global $post;
									$post = $getpost;
								}
							}
							
							if (!empty($email -> subscriber_id)) {
								if ($subscriber = $Subscriber -> get($email -> subscriber_id, false)) {
									$subscriber -> mailinglist_id = $email -> mailinglist_id;
									$subscriber -> mailinglists = maybe_unserialize($email -> mailinglists);
									$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH", time()));
									
									$checkemailquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Email -> table . "` WHERE `eunique` = '" . $eunique . "' AND `history_id` = '" . $email -> history_id . "'";							
									if (!$wpdb -> get_var($checkemailquery)) {
										if (empty($subscriberids) || (!empty($subscriberids) && !in_array($subscriber -> id, $subscriberids))) {
											$subscriberids[] = $subscriber -> id;
											
											if ((empty($subscriberemails[$email -> history_id])) || (!empty($subscriberemails[$email -> history_id]) && !in_array($subscriber -> email, $subscriberemails[$email -> history_id]))) {						
												$subscriberemails[$email -> history_id][] = $subscriber -> email;
												
												$Db -> model = $Email -> model;
												$message = $this -> render_email('send', array('message' => $email -> message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $email -> history_id, 'post_id' => $email -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $email -> theme_id);
												
												if ($this -> execute_mail($subscriber, false, $email -> subject, $message, $email -> attachments, $email -> history_id, $eunique)) {								
													$Queue -> delete($email -> id);
													$emailssent++;
												} else {
													global $mailerrors;
													$Db -> model = $Queue -> model;
													$Db -> save_field('error', esc_sql(trim(strip_tags($mailerrors))), array('id' => $email -> id));
												}
											}
										}
									} else {
										$Queue -> delete($email -> id);
									}
								} else {
									$Queue -> delete($email -> id);
								}
							} elseif (!empty($email -> user_id)) {
								if ($user = $this -> userdata($email -> user_id)) {
									$eunique = md5($email -> user_id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH", time()));
									if ((empty($userids[$email -> history_id]) || (!empty($userids[$email -> history_id]) && !in_array($user -> ID, $userids[$email -> history_id])))) {
										$userids[$email -> history_id][] = $user -> ID;
										$message = $this -> render_email('send', array('message' => $email -> message, 'subject' => $email -> subject, 'subscriber' => false, 'user' => $user, 'history_id' => $email -> history_id, 'post_id' => $email -> post_id), false, 'html', true, $email -> theme_id);
										
										if ($this -> execute_mail(false, $user, $email -> subject, $message, $email -> attachments, $email -> history_id, $eunique)) {
											$Queue -> delete($email -> id);
											$emailssent++;
										} else {
											global $mailerrors;
											$Db -> model = $Queue -> model;
											$Db -> save_field('error', esc_sql(trim(strip_tags($mailerrors))), array('id' => $email -> id));
										}
									}
								} else {
									$Queue -> delete($email -> id);
								}
							}
							
							$subscriber = array();
						}
					}
					
					$wpdb -> query("COMMIT");
				}
				
				$History -> queue_scheduled();
				$History -> queue_recurring();
				$this -> autoresponders_hook();
					
				//update the "lastcron" setting
				$this -> update_option('lastcron', time());
				echo '<br/>' . $emailssent . " " . __('queued emails have been sent out', $this -> plugin_name);
				
				delete_transient('newsletters_cron');
			}
			
			sleep(3);
		}
		
		function the_editor($html = null) {
			/* Check multilingual Support */
			if (is_admin()) {
				if ($this -> language_do()) {
					if ($this -> is_plugin_screen('send')) {		
						remove_filter('the_editor', 'qtrans_modifyRichEditor');	
						remove_action('wp_tiny_mce_init', 'qtrans_TinyMCE_init');
					}
				}
			}
			
			return $html;
		}
		
		function cron_schedules($schedules = array()) {
			$schedules['2minutes']		= array('interval' => 120, 'display' => __('Every 2 Minutes', $this -> plugin_name));
			$schedules['5minutes']		= array('interval' => 300, 'display' => __('Every 5 Minutes', $this -> plugin_name));
	       	$schedules['10minutes']		= array('interval' => 600, 'display' => __('Every 10 Minutes', $this -> plugin_name));
	       	$schedules['20minutes'] 	= array('interval' => 1200, 'display' => __('Every 20 Minutes', $this -> plugin_name));
	       	$schedules['30minutes'] 	= array('interval' => 1800, 'display' => __('Every 30 Minutes', $this -> plugin_name));
	       	$schedules['40minutes'] 	= array('interval' => 2400, 'display' => __('Every 40 Minutes', $this -> plugin_name));
	       	$schedules['50minutes'] 	= array('interval' => 3000, 'display' => __('Every 50 minutes', $this -> plugin_name));
			$schedules['weekly']		= array('interval' => 604800, 'display' => __('Once Weekly', $this -> plugin_name));
			$schedules['monthly']		= array('interval' => 2664000, 'display' => __('Once Monthly', $this -> plugin_name));
	    	
	    	return $schedules;
		}
		
		function screen_settings($current, $screen) {					
			if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> subscribers) {
			
				if (!empty($_POST['screenoptions'])) {
					if (!empty($_POST['fields']) && is_array($_POST['fields'])) {
						$this -> update_option('screenoptions_subscribers_fields', $_POST['fields']);	
					} else { delete_option($this -> pre . 'screenoptions_subscribers_fields'); }
					
					if (!empty($_POST['custom']) && is_array($_POST['custom'])) {
						$this -> update_option('screenoptions_subscribers_custom', $_POST['custom']);
					} else { delete_option($this -> pre . 'screenoptions_subscribers_custom'); }
				}
			
				global $Db, $Field;
				$Db -> model = $Field -> model;
				$conditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
				$fields = $Db -> find_all($conditions, false, array('order', "ASC"));
			
				$current .= $this -> render('subscribers' . DS . 'screen-options', array('fields' => $fields), false, 'admin');
			}
			
			return $current;
		}
		
		function plugin_action_links($actions = null, $plugin_file = null, $plugin_data = null, $context = null) {
			$this_plugin = plugin_basename(__FILE__);
			
			if (!empty($plugin_file) && $plugin_file == $this_plugin) {
				$actions[] = '<a href="" onclick="jQuery.colorbox({href:ajaxurl + \'?action=' . $this -> pre . 'serialkey\'}); return false;" id="' . $this -> pre . 'submitseriallink" title="' . __('Serial Key', $this -> plugin_name) . '">' . __('Serial Key', $this -> plugin_name) . '</a>';	
				$actions[] = '<a href="' . admin_url('admin.php?page=' . $this -> sections -> settings) . '">' . __('Settings', $this -> plugin_name) . '</a>';
			}
			
			return $actions;
		}
		
		function init_textdomain() {
			$locale = get_locale();
			
			if (!empty($locale)) { 
				if ($locale == "ja" || $locale == "ja_JP") { setlocale(LC_ALL, "ja_JP.UTF8"); }
			} else { 
				setlocale(LC_ALL, apply_filters('newsletters_setlocale', $locale)); 
			}
				
			if (function_exists('load_plugin_textdomain')) {			
				$mofile = $this -> plugin_name . '-' . $locale . '.mo';
				$mofullfull = WP_PLUGIN_DIR . DS . 'wp-mailinglist-languages' . DS . $mofile;
				$mofull = 'wp-mailinglist-languages' . DS;
				$language_external = $this -> get_option('language_external');
			
				if (!empty($language_external) && file_exists($mofullfull)) {				
					load_plugin_textdomain($this -> plugin_name, false, $mofull);
				} else {
					$mofull = dirname(plugin_basename(__FILE__)) . DS . 'languages' . DS;
					load_plugin_textdomain($this -> plugin_name, false, $mofull);
				}
			}	
		}
		
		function save_post($post_id = null, $post = null) {	
			global $wpdb, $post, $Db, $Html, $Shortcode, $Post, $Mailinglist, $History, $Queue, $Subscriber, $SubscribersList;
			
			// Don't do anything if it's a revision
			if (wp_is_post_revision($post_id)) {
				return;
			}
			
			// Get the $post by ID
			$post = get_post($post_id);
			$post_status = $post -> post_status;
		
			if (!empty($post_id) && !empty($post)) {
				switch ($post_status) {
					/* Future scheduled post */
					case 'future'					:											
						if (!empty($_POST[$this -> pre . 'mailinglists'])) {	
							update_post_meta($post_id, 'newsletters_scheduled', true);				
							update_post_meta($post_id, $this -> pre . 'mailinglists', $_POST[$this -> pre . 'mailinglists']);
							if (!empty($_POST[$this -> pre . 'theme_id'])) { update_post_meta($post_id, $this -> pre . 'theme_id', $_POST[$this -> pre . 'theme_id']); }
							if (!empty($_POST[$this -> pre . 'qtranslate_language'])) { update_post_meta($post_id, $this -> pre . 'qtranslate_language', $_POST[$this -> pre . 'qtranslate_language']); }
							if (!empty($_POST[$this -> pre . 'sendonpublishef'])) { update_post_meta($post_id, $this -> pre . 'sendonpublishef', $_POST[$this -> pre . 'sendonpublishef']); }
						}
						break;
					/* Post being published */
					case 'publish'					:
						global $shortcode_post, $shortcode_post_language, $wpml_target;
						$shortcode_post = $post;
						$shortcode_post_language = $_POST['wpmlqtranslate_language'];
						add_filter('excerpt_length', array($Shortcode, 'excerpt_length'));
						add_filter('excerpt_more', array($Shortcode, 'excerpt_more'));
					
						/* Is this post being published immediately? */
						if (!empty($_POST[$this -> pre . 'mailinglists'])) {
							$mailinglists = $_POST[$this -> pre . 'mailinglists'];
							$theme_id = $_POST[$this -> pre . 'theme_id'];
							$qtranslate_language = $_POST[$this -> pre . 'qtranslate_language'];
							$sendonpublishef = $_POST[$this -> pre . 'sendonpublishef'];
						} else {
							/* This looks like a future scheduled post coming through… */
							$mailinglists = get_post_meta($post_id, $this -> pre . 'mailinglists', true);
							$theme_id = get_post_meta($post_id, $this -> pre . 'theme_id', true);
							$qtranslate_language = get_post_meta($post_id, $this -> pre . 'qtranslate_language', true);
							$sendonpublishef = get_post_meta($post_id, $this -> pre . 'sendonpublishef', true);
							
							delete_post_meta($post_id, 'newsletters_scheduled');
							delete_post_meta($post_id, $this -> pre . 'mailinglists');
							delete_post_meta($post_id, $this -> pre . 'theme_id');
							delete_post_meta($post_id, $this -> pre . 'qtranslate_language');
							delete_post_meta($post_id, $this -> pre . 'sendonpublishef');
						}
				
						if (!empty($mailinglists)) {		
							if (!empty($post_id)) {			
								if ($post = get_post($post_id)) {
									//prepare global post data
									setup_postdata($post);
									
									/* multilingual stuff */		
									if ($this -> language_do()) {
										if ($languages = $this -> language_getlanguages()) {
											if (!empty($qtranslate_language)) {
												$titles = $this -> language_split($post -> post_title);
												$contents = $this -> language_split($post -> post_content);
												
												if (!empty($titles[$qtranslate_language])) { $post -> post_title = $titles[$qtranslate_language]; }
												if (!empty($contents[$qtranslate_language])) { $post -> post_content = $contents[$qtranslate_language]; }
											}
										}
									}
														
									//Full post or Excerpt?
									if (!empty($sendonpublishef) && $sendonpublishef == "fp") {
										$post_content = $post -> post_content;
									} elseif (empty($sendonpublishef) || $sendonpublishef == "ep") {
										$post_content = "";
									
										if (function_exists('has_post_thumbnail')) {
											if (has_post_thumbnail($post -> ID)) {
												$post_content .= '<a href="' . get_permalink($post -> ID) . '">' . get_the_post_thumbnail($post -> ID, "thumbnail", array('align' => "left", 'hspace' => "15", 'class' => $this -> pre . "post_thumbnail")) . '</a>';	
											}
										}
										
										$post_content .= get_the_excerpt();
										//$post_content .= ' [<a href="' . get_permalink($post -> ID) . '" title="' . $post -> post_title . '">' . __('read more', $this -> plugin_name) . '</a>]';
										$post_content .= '<hr style="clear:both; display:block; height:1px; width:100%;" />';
									}
									
									if ($this -> get_option('sendonpublishunsubscribe') == "Y") {
										$post_content .= "\n\n";
										$post_content .= "[wpmlunsubscribe]";
									}
									
									if ($this -> get_option('subscriptions') == "Y") {
										$SubscribersList -> check_expirations();
									}
									
									$subject = $post -> post_title;
									
									if (!empty($mailinglists)) {
										//save the History record
										$history_data = array(
											'subject'			=>	$subject,
											'message'			=>	$post_content,
											'theme_id'			=>	$theme_id,
											'mailinglists'		=>	serialize($mailinglists),
											'attachment'		=>	"N",
											'sent'				=>	1,
											'post_id'			=>	$post_id,
											'attachmentfile'	=>	false
										);
										
										$History -> save($history_data, false);
										$history_id = $History -> insertid;										
										$subscriberids = array();
										$subscriberemails = array();
										
										if (!empty($mailinglists)) {									
											//update scheduling
											$this -> scheduling(true);
											$mailinglistscondition = "(";
											$m = 1;
											
											foreach ($mailinglists as $mailinglist_id) {
												$mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";
												if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
												$m++;	
											}
											
											$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
											. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
											. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
											. $wpdb -> prefix . $SubscribersList -> table . " ON "
											. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id WHERE "
											. $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
											
											$q_queries = array();
											$attachment = false;
											
											if ($subscribers = $wpdb -> get_results($query)) {
												$q_queries = array();
													
												foreach ($subscribers as $subscriber) {
													$this -> remove_server_limits();
													$subscriber -> mailinglist_id = $_POST[$this -> pre . 'mailinglists'][0];								
													$subject = $post -> post_title;
													$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history_id . date_i18n("YmdH", time()));
												
													if ($this -> get_option('scheduling') == "Y") {
														$q_queries[] = $Queue -> save($subscriber, false, $subject, $post_content, $attachment, $post_id, $history_id, true, $theme_id);
													} else {
														$message = $this -> render_email('send', array('message' => $post_content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $theme_id);
														$this -> execute_mail($subscriber, false, $subject, $message, $attachment, $history_id, $eunique);
													}
												}
												
												if (!empty($q_queries)) {													
													foreach ($q_queries as $q_query) {
														if (!empty($q_query)) {
															$wpdb -> query($q_query);	
														}
													}
												}
											}
										}
										
										if (!$Post -> get_by_post_id($post -> ID)) {
											$post_data = array('post_id' => $post -> ID, 'sent' => "Y");
											$Post -> save($post_data, false);
										}
									}
								}
							}
						}
						break;
				}
			}
		}
		
		function delete_post($post_id = null) {
			global $Db, $Post, $Latestpost, $History;
		
			if (!empty($post_id)) {
				$Db -> model = $Post -> model;
				$Db -> delete_all(array('post_id' => $post_id));
				
				$Db -> model = $Latestpost -> model;
				$Db -> delete_all(array('post_id' => $post_id));
				
				$Db -> model = $History -> model;
				$Db -> save_field('post_id', "0", array('post_id' => $post_id));
			}
		}
		
		function admin() {	
			if (!$this -> ci_serial_valid()) {
				//$this -> redirect('?page=' . $this -> sections -> submitserial);
			}
		
			switch ($_GET['method']) {
				case 'wizard_install'			:
					$this -> render('wizard' . DS . 'install', false, true, 'admin');
					break;
				default							:
					$this -> admin_index();
					break;
			}
		}
		
		function admin_menu() {
			global $Queue, $queue_count;
			$queue_count = ($Queue -> count()) ? $Queue -> count() : '';
			$queue_count_icon = ' <span class="update-plugins count-1"><span class="update-count" id="newsletters-menu-queue-count">' . $queue_count . '</span></span>';
			$update_icon = ($this -> has_update()) ? ' <span class="update-plugins count-1"><span class="update-count">1</span></span>' : '';
			
			//$this -> check_roles();
		
			add_menu_page(__('Newsletters', $this -> plugin_name), __('Newsletters', $this -> plugin_name) . $update_icon, 'newsletters_welcome', $this -> sections -> welcome, array($this, 'admin'), false, "26.11");
			
			if (false && !$this -> ci_serial_valid()) {
				$this -> menus['newsletters-submitserial'] = add_submenu_page($this -> sections -> welcome, __('Submit Serial', $this -> plugin_name), __('Submit Serial', $this -> plugin_name), 'newsletters_welcome', $this -> sections -> submitserial, array($this, 'admin_submitserial'));
			} else {
				$this -> menus['newsletters'] = add_submenu_page($this -> sections -> welcome, __('Overview', $this -> plugin_name), __('Overview', $this -> plugin_name), 'newsletters_welcome', $this -> sections -> welcome, array($this, 'admin'));
				$this -> menus['newsletters-settings'] = add_submenu_page($this -> sections -> welcome, __('General Configuration', $this -> plugin_name), __('Configuration', $this -> plugin_name), 'newsletters_settings', $this -> sections -> settings, array($this, 'admin_config'));
				$this -> menus['newsletters-settings-subscribers'] = add_submenu_page("newsletters_page_" . $this -> sections -> settings, __('Subscribers Configuration', $this -> plugin_name), __('Subscribers', $this -> plugin_name), 'newsletters_settings_subscribers', $this -> sections -> settings_subscribers, array($this, 'admin_settings_subscribers'));
				$this -> menus['newsletters-settings-templates'] = add_submenu_page("newsletters_page_" . $this -> sections -> settings, __('System Emails Configuration', $this -> plugin_name), __('System Emails', $this -> plugin_name), 'newsletters_settings_templates', $this -> sections -> settings_templates, array($this, 'admin_settings_templates'));
				$this -> menus['newsletters-settings-system'] = add_submenu_page("newsletters_page_" . $this -> sections -> settings, __('System Configuration', $this -> plugin_name), __('System', $this -> plugin_name), 'newsletters_settings_system', $this -> sections -> settings_system, array($this, 'admin_settings_system'));
				$this -> menus['newsletters-settings-tasks'] = add_submenu_page("newsletters_page_" . $this -> sections -> settings, __('Scheduled Tasks', $this -> plugin_name), __('Scheduled Tasks', $this -> plugin_name), 'newsletters_settings_tasks', $this -> sections -> settings_tasks, array($this, 'admin_settings_tasks'));
				$this -> menus['newsletters-settings-api'] = add_submenu_page("newsletters_page_" . $this -> sections -> settings, __('API', $this -> plugin_name), __('API', $this -> plugin_name), 'newsletters_settings_api', $this -> sections -> settings_api, array($this, 'admin_settings_api'));
				//$this -> menus['newsletters-forms'] = add_submenu_page($this -> sections -> welcome, __('Subscribe Forms', $this -> plugin_name), __('Subscribe Forms', $this -> plugin_name), 'newsletters_forms', $this -> sections -> forms, array($this, 'admin_forms'));
				$this -> menus['newsletters-send'] = add_submenu_page($this -> sections -> welcome, __('Create Newsletter', $this -> plugin_name), __('Create Newsletter', $this -> plugin_name), 'newsletters_send', $this -> sections -> send, array($this, 'admin_send'));
				$this -> menus['newsletters-history'] = add_submenu_page($this -> sections -> welcome, __('Sent &amp; Draft Emails', $this -> plugin_name), __('Sent &amp; Draft Emails', $this -> plugin_name), 'newsletters_history', $this -> sections -> history, array($this, 'admin_history'));
				
				if ($this -> get_option('clicktrack') == "Y") {
					$this -> menus['newsletters-links'] = add_submenu_page($this -> sections -> welcome, __('Links &amp; Clicks', $this -> plugin_name), __('Links &amp; Clicks', $this -> plugin_name), 'newsletters_links', $this -> sections -> links, array($this, 'admin_links'));
					$this -> menus['newsletters-links-clicks'] = add_submenu_page($this -> menus['newsletters-links'], __('Clicks', $this -> plugin_name), __('Clicks', $this -> plugin_name), 'newsletters_clicks', $this -> sections -> clicks, array($this, 'admin_clicks'));
				}
				
				$this -> menus['newsletters-autoresponders'] = add_submenu_page($this -> sections -> welcome, __('Autoresponders', $this -> plugin_name), __('Autoresponders', $this -> plugin_name), 'newsletters_autoresponders', $this -> sections -> autoresponders, array($this, 'admin_autoresponders'));
				$this -> menus['newsletters-autoresponderemails'] = add_submenu_page("newsletters_page_" . $this -> sections -> autoresponders, __('Autoresponder Emails', $this -> plugin_name), __('Autoresponder Emails', $this -> plugin_name), 'newsletters_autoresponderemails', $this -> sections -> autoresponderemails, array($this, 'admin_autoresponderemails'));
				$this -> menus['newsletters-lists'] = add_submenu_page($this -> sections -> welcome, __('Mailing Lists', $this -> plugin_name), __('Mailing Lists', $this -> plugin_name), 'newsletters_lists', $this -> sections -> lists, array($this, 'admin_mailinglists'));
				$this -> menus['newsletters-groups'] = add_submenu_page($this -> sections -> welcome, __('Groups', $this -> plugin_name), __('Groups', $this -> plugin_name), 'newsletters_groups', $this -> sections -> groups, array($this, 'admin_groups'));
				$this -> menus['newsletters-subscribers'] = add_submenu_page($this -> sections -> welcome, __('Subscribers', $this -> plugin_name), __('Subscribers', $this -> plugin_name), 'newsletters_subscribers', $this -> sections -> subscribers, array($this, 'admin_subscribers'));
				$this -> menus['newsletters-fields'] = add_submenu_page($this -> sections -> welcome, __('Custom Fields', $this -> plugin_name), __('Custom Fields', $this -> plugin_name), 'newsletters_fields', $this -> sections -> fields, array($this, 'admin_fields'));
				$this -> menus['newsletters-import'] = add_submenu_page($this -> sections -> welcome, __('Import/Export Subscribers', $this -> plugin_name), __('Import/Export', $this -> plugin_name), 'newsletters_importexport', $this -> sections -> importexport, array($this, 'admin_importexport'));
				$this -> menus['newsletters-themes'] = add_submenu_page($this -> sections -> welcome, __('Templates', $this -> plugin_name), __('Templates', $this -> plugin_name), 'newsletters_themes', $this -> sections -> themes, array($this, 'admin_themes')); 
				$this -> menus['newsletters-templates'] = add_submenu_page($this -> sections -> welcome, __('Email Snippets', $this -> plugin_name), __('Email Snippets', $this -> plugin_name), 'newsletters_templates', $this -> sections -> templates, array($this, 'admin_templates'));
				$this -> menus['newsletters-templates-save'] = add_submenu_page($this -> menus['newsletters-templates'], __('Save an Email Snippet', $this -> plugin_name), __('Save an Email Snippet', $this -> plugin_name), 'newsletters_templates_save', $this -> sections -> templates_save, array($this, 'admin_templates'));
				$this -> menus['newsletters-queue'] = add_submenu_page($this -> sections -> welcome, __('Email Queue', $this -> plugin_name), __('Email Queue', $this -> plugin_name) . ((!empty($queue_count)) ? $queue_count_icon : ''), 'newsletters_queue', $this -> sections -> queue, array($this, 'admin_mailqueue'));
				$this -> menus['newsletters-orders'] = add_submenu_page($this -> sections -> welcome, __('Subscription Orders', $this -> plugin_name), __('Subscription Orders', $this -> plugin_name), 'newsletters_orders', $this -> sections -> orders, array($this, 'admin_orders'));
				$this -> menus['newsletters-extensions'] = add_submenu_page($this -> sections -> welcome, __('Extensions', $this -> plugin_name), __('Extensions', $this -> plugin_name), 'newsletters_extensions', $this -> sections -> extensions, array($this, 'admin_extensions'));
				$this -> menus['newsletters-settings-updates'] = add_submenu_page($this -> sections -> welcome, __('Updates', $this -> plugin_name), __('Updates', $this -> plugin_name) . $update_icon, 'newsletters_settings_updates', $this -> sections -> settings_updates, array($this, 'admin_settings_updates'));
				$this -> menus['newsletters-extensions-settings'] = add_submenu_page($this -> menus['newsletters-extensions'], __('Extensions Settings', $this -> plugin_name), __('Extensions Settings', $this -> plugin_name), 'newsletters_extensions_settings', $this -> sections -> extensions_settings, array($this, 'admin_extensions_settings'));
				
				if (WPML_SHOW_SUPPORT) {
					$this -> menus['newsletters-support'] = add_submenu_page($this -> sections -> welcome, __('Support &amp; Help', $this -> plugin_name), __('Support &amp; Help', $this -> plugin_name), 'newsletters_support', $this -> sections -> support, array($this, 'admin_help'));
				}
			}
			
			if (!$this -> ci_serial_valid()) {
				$this -> menus['newsletters-submitserial'] = add_submenu_page($this -> sections -> welcome, __('Submit Serial', $this -> plugin_name), __('Submit Serial', $this -> plugin_name), 'newsletters_welcome', $this -> sections -> submitserial, array($this, 'admin_submitserial'));
			}
			
			do_action('newsletters_admin_menu', $this -> menus);
			
			add_action('admin_head-' . $this -> menus['newsletters'], array($this, 'admin_head_welcome'));
			add_action('admin_head-' . $this -> menus['newsletters-send'], array($this, 'admin_head_send'));
			add_action('admin_head-' . $this -> menus['newsletters-templates-save'], array($this, 'admin_head_templates_save'));
			add_action('admin_head-' . $this -> menus['newsletters-settings'], array($this, 'admin_head_settings'));
			add_action('admin_head-' . $this -> menus['newsletters-settings-system'], array($this, 'admin_head_settings_system'));
			add_action('admin_head-' . $this -> menus['newsletters-settings-templates'], array($this, 'admin_head_settings_templates'));
			add_action('admin_head-' . $this -> menus['newsletters-settings-subscribers'], array($this, 'admin_head_settings_subscribers'));
			add_action('admin_head-' . $this -> menus['newsletters-extensions-settings'], array($this, 'admin_head_settings_extensions_settings'));
		}
		
		function add_dashboard() {
			add_dashboard_page(sprintf('Newsletters %s', $this -> version), sprintf('Newsletters %s', $this -> version), 'read', 'newsletters-about', array($this, 'newsletters_about'));
			remove_submenu_page('index.php', 'newsletters-about');
		}
		
		function newsletters_about() {
			$this -> render('about', false, true, 'admin');
		}
		
		function admin_head() {	
			$this -> render('head', false, true, 'admin');
		}
		
		function admin_footer() {		
			//do nothing...
		}
		
		function admin_head_welcome() {
			global $Metabox, $Html, $post;
			
			add_meta_box('quicksearchdiv', __('Quick Search', $this -> plugin_name) . $Html -> help(__('Quick search', $this -> plugin_name)), array($Metabox, 'welcome_quicksearch'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('subscribersdiv', __('Total Subscribers', $this -> plugin_name) . $Html -> help(__('This is the total number of subscribers in the database. In other words, email addresses. Each subscriber could have multiple subscriptions to different lists or no subscriptions at all for that matter.', $this -> plugin_name)), array($Metabox, 'welcome_subscribers'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('listsdiv', __('Total Mailing Lists', $this -> plugin_name) . $Html -> help(__('The total mailing lists that you have in use. Each list can have a purpose of its own, make use of lists to organize and power your subscribers.', $this -> plugin_name)), array($Metabox, 'welcome_lists'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('emailsdiv', __('Total Emails', $this -> plugin_name) . $Html -> help(__('The total number of emails sent to date since the plugin was installed until now.', $this -> plugin_name)), array($Metabox, 'welcome_emails'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('bouncesdiv', __('Bounced Emails', $this -> plugin_name) . $Html -> help(__('The total number of bounces to date.', $this -> plugin_name)), array($Metabox, 'welcome_bounces'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('unsubscribesdiv', __('Unsubscribes', $this -> plugin_name) . $Html -> help(__('Total unsubscribes to date.', $this -> plugin_name)), array($Metabox, 'welcome_unsubscribes'), "newsletters_page_" . $this -> sections -> welcome, 'side', 'core');
			add_meta_box('statsdiv', __('Statistics Overview', $this -> plugin_name) . $Html -> help(__('This chart shows an overview of subscribers, emails sent, unsubscribes, bounces, etc in a visual manner.', $this -> plugin_name)), array($Metabox, 'welcome_stats'), "newsletters_page_" . $this -> sections -> welcome, 'normal', 'core');
			add_meta_box('historydiv', __('Recent Emails', $this -> plugin_name) . $Html -> help(__('This is a quick overview of your 5 latest newsletters.', $this -> plugin_name)), array($Metabox, 'welcome_history'), "newsletters_page_" . $this -> sections -> welcome, 'normal', 'core');
			
			do_action($this -> pre . '_metaboxes_overview', "newsletters_page_" . $this -> sections -> welcome, "normal", $post);
			
			do_action('do_meta_boxes', "newsletters_page" . $this -> sections -> welcome, 'side');
			do_action('do_meta_boxes', "newsletters_page" . $this -> sections -> welcome, 'normal');
			do_action('do_meta_boxes', "newsletters_page" . $this -> sections -> welcome, 'advanced');
		}
		
		function admin_head_send() {
			global $Metabox, $Html;
		
			$createspamscore = $this -> get_option('createspamscore');
			if (!empty($createspamscore) && $createspamscore == "Y") {
				add_meta_box('spamscorediv', __('Spam Score', $this -> plugin_name), array($Metabox, 'send_spamscore'), "newsletters_page_" . $this -> sections -> send, 'side', 'core');
			}
				
			add_meta_box('mailinglistsdiv', __('Subscribers', $this -> plugin_name) . $Html ->  help(__('Tick/check the group(s) or list(s) that you want to send/queue this newsletter to. The newsletter will only be sent to active subscriptions in the chosen list(s).', $this -> plugin_name)), array($Metabox, 'send_mailinglists'), "newsletters_page_" . $this -> sections -> send, 'side', 'core');
			add_meta_box('insertdiv', __('Insert into Newsletter', $this -> plugin_name) . $Html -> help(__('Use this box to insert various things into your newsletter such as posts, snippets, custom fields and post thumbnails.', $this -> plugin_name)), array($Metabox, 'send_insert'), "newsletters_page_" . $this -> sections -> send, 'side', 'core');
			add_meta_box('themesdiv', __('Template', $this -> plugin_name) . $Html -> help(__('Choose the template that you want to use for this newsletter. The content filled into the TinyMCE editor to the left will be inserted into the template where it has the [wpmlcontent] tag inside it.', $this -> plugin_name)), array($Metabox, 'send_theme'), "newsletters_page_" . $this -> sections -> send, 'side', 'core');
			add_meta_box('submitdiv', __('Send Newsletter', $this -> plugin_name), array($Metabox, 'send_submit'), "newsletters_page_" . $this -> sections -> send, 'side', 'core');
			
			$multimime = $this -> get_option('multimime');
			if (!empty($multimime) && $multimime == "Y") {
				add_meta_box('multimimediv', __('TEXT Version', $this -> plugin_name) . $Html -> help(__('Specify the TEXT version of multipart emails which will be seen by users who prefer text or have HTML turned off.', $this -> plugin_name)), array($Metabox, 'send_multimime'), "newsletters_page_" . $this -> sections -> send, 'normal', 'core');
			}
			
			$createpreview = $this -> get_option('createpreview');
			if (!empty($createpreview) && $createpreview == "Y") {
				add_meta_box('previewdiv', __('Live Preview', $this -> plugin_name) . $Html -> help(__('The preview section below shows a preview of what the newsletter will look like with the template, content and other elements. It updates automatically every few seconds or you can click the "Update Preview" button to manually update it. Please note that this is a browser preview and some email/webmail clients render emails differently than browsers.', $this -> plugin_name)), array($Metabox, 'send_preview'), "newsletters_page_" . $this -> sections -> send, 'normal', 'core');
			}
			
			if (apply_filters('newsletters_admin_createnewsletter_variables_show', true)) { add_meta_box('setvariablesdiv', __('Variables &amp; Custom Fields', $this -> plugin_name) . $Html -> help(__('These are shortcodes which can be used inside of the newsletter template or content where needed and as many of them as needed. The shortcodes will be replaced with their respective values for each subscriber individually. You can use this to personalize your newsletters to your subscribers easily.', $this -> plugin_name)), array($Metabox, 'send_setvariables'), "newsletters_page_" . $this -> sections -> send, 'normal', 'core'); }
			if (apply_filters('newsletters_admin_createnewsletter_emailattachments_show', true)) { add_meta_box('attachmentdiv', __('Email Attachment', $this -> plugin_name) . $Html -> help(__('Attach files to your newsletter. It is possible to attach multiple files of any filetype and size to newsletters which will be sent to the subscribers. Try to keep attachments small to prevent emails from becoming too large.', $this -> plugin_name)), array($Metabox, 'send_attachment'), "newsletters_page_" . $this -> sections -> send, 'normal', 'core'); }
			if (apply_filters('newsletters_admin_createnewsletter_publishpost_show', true)) { add_meta_box('publishdiv', __('Publish as Post', $this -> plugin_name) . $Html -> help(__('When you queue/send this newsletter you can publish it as a post on your website. Configure these settings to publish this newsletter as a post according to your needs.', $this -> plugin_name)), array($Metabox, 'send_publish'), "newsletters_page_" . $this -> sections -> send, 'normal', 'core'); }
			
			do_action('newsletters_admin_createnewsletter_metaboxes', "newsletters_page_" . $this -> sections -> send);
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> send, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> send, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> send, 'advanced');
		}
		
		function admin_head_templates_save() {
			global $Metabox;
		
			add_meta_box('submitdiv', __('Save Snippet', $this -> plugin_name), array($Metabox, 'templates_submit'), "admin_page_" . $this -> sections -> templates_save, 'side', 'core');
			
			do_action('do_meta_boxes', "admin_page_" . $this -> sections -> templates_save, 'side');
			do_action('do_meta_boxes', "admin_page_" . $this -> sections -> templates_save, 'normal');
			do_action('do_meta_boxes', "admin_page_" . $this -> sections -> templates_save, 'advanced');
		}
		
		function admin_head_settings() {
			global $Metabox, $Html;
			
			add_meta_box('submitdiv', __('Configuration Settings', $this -> plugin_name), array($Metabox, 'settings_submit'), "newsletters_page_" . $this -> sections -> settings, 'side', 'core');
			add_meta_box('tableofcontentsdiv', __('Quick Links', $this -> plugin_name), array($Metabox, 'settings_tableofcontents'), "newsletters_page_" . $this -> sections -> settings, 'high', 'core');
			add_meta_box('generaldiv', __('General Mail Settings', $this -> plugin_name) . $Html -> help(__('These are general settings related to the sending of emails such as your email server. You can also turn on/off other features here such as read tracking, click tracking and more.', $this -> plugin_name)), array($Metabox, 'settings_general'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('sendingdiv', __('Sending Settings', $this -> plugin_name), array($Metabox, 'settings_sending'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('optindiv', __('Default Subscription Form Settings', $this -> plugin_name) . $Html -> help(__('Global subscribe form settings for hardcoded and shortcode (post/page) subscribe forms.', $this -> plugin_name)), array($Metabox, 'settings_optin'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('subscriptionsdiv', __('Paid Subscriptions', $this -> plugin_name), array($Metabox, 'settings_subscriptions'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('ppdiv', __('PayPal Configuration', $this -> plugin_name) . $Html -> help(__('If you are using PayPal as your payment method for paid subscriptions you can configure it here.', $this -> plugin_name)), array($Metabox, 'settings_pp'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('tcdiv', __('2CheckOut Configuration', $this -> plugin_name) . $Html -> help(__('Configure 2CheckOut (2CO) here if you are using it as your payment method for paid subscriptions.', $this -> plugin_name)), array($Metabox, 'settings_tc'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('publishingdiv', __('Posts Configuration', $this -> plugin_name) . $Html -> help(__('These are settings related to posts in general. For publishing newsletters as posts and also inserting posts into newsletters.', $this -> plugin_name)), array($Metabox, 'settings_publishing'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('schedulingdiv', __('Email Scheduling', $this -> plugin_name) . $Html -> help(__('The purpose of email scheduling is to allow you to send thousands of emails in a load distributed way. Please take note that you cannot expect your server/hosting to send hundreds/thousands of emails all simultaneously so this is where email scheduling helps you.', $this -> plugin_name)), array($Metabox, 'settings_scheduling'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('bouncediv', __('Bounce Configuration', $this -> plugin_name), array($Metabox, 'settings_bounce'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('emailsdiv', __('History &amp; Emails Configuration', $this -> plugin_name), array($Metabox, 'settings_emails'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('latestposts', __('Latest Posts Subscriptions', $this -> plugin_name), array($Metabox, 'settings_latestposts'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			add_meta_box('customcss', __('Theme, Scripts &amp; Custom CSS', $this -> plugin_name), array($Metabox, 'settings_customcss'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			
			if ($this -> language_ready()) {
				add_meta_box('languagediv', __('Language Configuration', $this -> plugin_name), array($Metabox, 'settings_language'), "newsletters_page_" . $this -> sections -> settings, 'normal', 'core');
			}
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings, 'advanced');
		}
		
		function admin_head_settings_templates() {
			global $Metabox, $Html;
			$page = "newsletters_page_" . $this -> sections -> settings_templates;
		
			add_meta_box('submitdiv', __('Configuration Settings', $this -> plugin_name), array($Metabox, 'settings_submit'), "newsletters_page_" . $this -> sections -> settings_templates, 'side', 'core');
			add_meta_box('tableofcontentsdiv', __('Quick Links', $this -> plugin_name), array($Metabox, 'settings_templates_tableofcontents'), "newsletters_page_" . $this -> sections -> settings_templates, 'high', 'core');
			add_meta_box('postsdiv', __('Posts', $this -> plugin_name) . $Html -> help(__('The posts template used when using the [wpmlpost...] or [wpmlposts...] shorcodes in your newsletters.', $this -> plugin_name)), array($Metabox, 'settings_templates_posts'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('latestpostsdiv', __('Latest Posts', $this -> plugin_name) . $Html -> help(__('The posts template used for the "Latest Posts Subscriptions" feature which automatically sends out new posts.', $this -> plugin_name)), array($Metabox, 'settings_templates_latestposts'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('confirmdiv', __('Confirmation Email', $this -> plugin_name) . $Html -> help(__('Email message sent to new subscribers to confirm their subscription.', $this -> plugin_name)), array($Metabox, 'settings_templates_confirm'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('bouncediv', __('Bounce Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the administrator when an email to a subscriber bounces.', $this -> plugin_name)), array($Metabox, 'settings_templates_bounce'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('unsubscribediv', __('Unsubscribe Admin Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the administrator when a subscriber unsubscribes.', $this -> plugin_name)), array($Metabox, 'settings_templates_unsubscribe'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('unsubscribeuserdiv', __('Unsubscribe User Email', $this -> plugin_name) . $Html -> help(__('Email message to the subscriber to confirm their unsubscribe.', $this -> plugin_name)), array($Metabox, 'settings_templates_unsubscribeuser'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('expirediv', __('Expiration Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the subscriber when a paid subscription expires.', $this -> plugin_name)), array($Metabox, 'settings_templates_expire'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('orderdiv', __('Paid Subscription Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the administrator for a new paid subscription order payment.', $this -> plugin_name)), array($Metabox, 'settings_templates_order'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('schedulediv', __('Cron Schedule Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the administrator when the email cron fires.', $this -> plugin_name)), array($Metabox, 'settings_templates_schedule'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			add_meta_box('subscribediv', __('New Subscription Email', $this -> plugin_name) . $Html -> help(__('Email message sent to the administrator when a new user subscribes.', $this -> plugin_name)), array($Metabox, 'settings_templates_subscribe'), "newsletters_page_" . $this -> sections -> settings_templates, 'normal', 'core');
			
			do_action('newsletters_admin_settingstemplates_metaboxes', $page);
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_templates, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_templates, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_templates, 'advanced');
		}
		
		function admin_head_settings_subscribers() {
			global $Html, $Metabox;
			
			add_meta_box('submitdiv', __('Configuration Settings', $this -> plugin_name), array($Metabox, 'settings_submit'), "newsletters_page_" . $this -> sections -> settings_subscribers, 'side', 'core');
			add_meta_box('tableofcontentsdiv', __('Quick Links', $this -> plugin_name), array($Metabox, 'settings_subscribers_tableofcontents'), "newsletters_page_" . $this -> sections -> settings_subscribers, 'high', 'core');
			add_meta_box('managementdiv', __('Subscriber Management Section', $this -> plugin_name) . $Html -> help(__('This section lets you control the way the subscriber management section behaves. It is the "Manage Subscriptions" page which is provided to subscribers where they unsubscribe, manage current subscriptions, update their profile, etc.', $this -> plugin_name)), array($Metabox, 'settings_management'), "newsletters_page_" . $this -> sections -> settings_subscribers, 'normal', 'core');
			add_meta_box('subscribersdiv', __('Subscription Behaviour', $this -> plugin_name) . $Html -> help(__('Control the way the plugin behaves when someone subscribes to your site. Certain things can happen upon subscription based on these settings.', $this -> plugin_name)), array($Metabox, 'settings_subscribers'), "newsletters_page_" . $this -> sections -> settings_subscribers, 'normal', 'core');
			add_meta_box('unsubscribediv', __('Unsubscribe Behaviour', $this -> plugin_name) . $Html -> help(__('Control the unsubscribe procedure. Certain things can happen when a subscriber unsubscribes from your site based on these settings.', $this -> plugin_name)), array($Metabox, 'settings_unsubscribe'), "newsletters_page_" . $this -> sections -> settings_subscribers, 'normal', 'core');
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'advanced');
		}
		
		function admin_head_settings_extensions_settings() {
			global $Metabox;
			
			add_meta_box('submitdiv', __('Extensions Settings', $this -> plugin_name), array($Metabox, 'extensions_settings_submit'), "newsletters_page_" . $this -> sections -> extensions_settings, 'side', 'core');
			do_action($this -> pre . '_metaboxes_extensions_settings', "newsletters_page_" . $this -> sections -> extensions_settings);
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> extensions_settings, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> extensions_settings, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> extensions_settings, 'advanced');
		}
		
		function admin_head_settings_system() {
			global $Metabox, $Html;
		
			add_meta_box('submitdiv', __('Configuration Settings', $this -> plugin_name), array($Metabox, 'settings_submit'), "newsletters_page_" . $this -> sections -> settings_system, 'side', 'core');
			add_meta_box('tableofcontentsdiv', __('Quick Links', $this -> plugin_name), array($Metabox, 'settings_system_tableofcontents'), "newsletters_page_" . $this -> sections -> settings_system, 'high', 'core');
			add_meta_box('captchadiv', __('Captcha Settings', $this -> plugin_name) . $Html -> help(__('Use these settings for the captcha security image used in the subscribe forms.', $this -> plugin_name)), array($Metabox, 'settings_system_captcha'), "newsletters_page_" . $this -> sections -> settings_system, 'normal', 'core');
			add_meta_box('wprelateddiv', __('WordPress Related', $this -> plugin_name) . $Html -> help(__('These are settings related to WordPress directly and how the plugin interacts with it.', $this -> plugin_name)), array($Metabox, 'settings_wprelated'), "newsletters_page_" . $this -> sections -> settings_system, 'normal', 'core');
			add_meta_box('permissionsdiv', __('Permissions', $this -> plugin_name), array($Metabox, 'settings_permissions'), "newsletters_page_" . $this -> sections -> settings_system, 'normal', 'core');
			add_meta_box('autoimportusersdiv', __('Auto Import Users', $this -> plugin_name) . $Html -> help(__('Use these settings to configure the way that WordPress users are automatically imported as subscribers into the system.', $this -> plugin_name)), array($Metabox, 'settings_importusers'), "newsletters_page_" . $this -> sections -> settings_system, 'normal', 'core');
			add_meta_box('commentform', __('WordPress Comment- and Registration Form', $this -> plugin_name) . $Html -> help(__('Put a subscribe checkbox on your WordPress registration and/or comment forms to capture subscribers.', $this -> plugin_name)), array($Metabox, 'settings_commentform'), "newsletters_page_" . $this -> sections -> settings_system, 'normal', 'core');
			
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'side');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'normal');
			do_action('do_meta_boxes', "newsletters_page_" . $this -> sections -> settings_subscribers, 'advanced');
		}
		
		function hardcoded($list_id = "select", $lists = null, $atts = array()) {	
			global $Html, $Subscriber;
				
			if (is_feed()) return;
			$atts['list'] = $list_id;
			$number = 'embed' . rand(999, 9999);
			$widget_id = 'newsletters-' . $number;
			$instance = $this -> widget_instance($number, $atts);
			
			$defaults = array(
				'list' 				=> 	$list_id, 
				'id' 				=> 	false,
				'lists'				=>	false,
				'ajax'				=>	$instance['ajax'],
				'button'			=>	$instance['button'],
				'captcha'			=>	$instance['captcha'],
				'acknowledgement'	=>	$instance['acknowledgement'],
			);
			
			$r = shortcode_atts($defaults, $atts);
			extract($r);
			
			$action = ($this -> language_do()) ? $this -> language_converturl($_SERVER['REQUEST_URI'], $instance['language']) : $_SERVER['REQUEST_URI'];
			$action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;
			$errors = $Subscriber -> errors;
			
			$output = "";
			$output .= '<div id="' . $widget_id . '" class="' . $this -> pre . ' widget_newsletters">';
			$output .= '<div id="' . $widget_id . '-wrapper">';
			$output .= $this -> render('widget', array('action' => $action, 'errors' => $errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), false, 'default');
			$output .= '</div>';
			$output .= '</div>';
			
			echo $output;
		}
		
		function widget_register() {			
			register_widget('Newsletters_Widget');
		}
		
		function admin_submitserial() {
			$success = false;
		
			if (!empty($_POST)) {
				if (empty($_REQUEST['serial'])) { $errors[] = __('Please fill in a serial key.', $this -> plugin_name); }
				else { 
					$this -> update_option('serialkey', $_REQUEST['serial']);	//update the DB option
					
					if (!$this -> ci_serial_valid()) { $errors[] = __('Serial key is invalid, please try again.', $this -> plugin_name); }
					else {
						delete_transient($this -> pre . 'update_info');
						$success = true;
						$this -> redirect('?page=' . $this -> sections -> welcome); 
					}
				}
			}
			
			$this -> render('settings-submitserial', array('success' => $success, 'errors' => $errors), true, 'admin');
		}
		
		function admin_index() {	
			$this -> render_admin('index', false, true, 'admin');
		}
		
		function admin_forms() {
			
			switch ($_GET['method']) {
				default 								:
					$this -> render('forms' . DS . 'index', false, true, 'admin');
					break;
			}
		}
		
		function admin_send() {
			global $wpdb, $Unsubscribe, $Db, $Template, $Html, $History, $HistoriesAttachment, $Mailinglist, $Queue, $Subscriber, $Field, $SubscribersList;
			$user_id = get_current_user_id();
			$post_id = false;
			$this -> remove_server_limits();
			$sentmailscount = 0;
			
			/* Themes */
			$Db -> model = $Theme -> model;
			$themes = $Db -> find_all(false, false, array('title', "ASC"));
			
			// Do the post publishing
			if (!empty($_POST['post_id'])) {
				$post_id = $_POST['post_id'];
			} elseif (!empty($_GET['id'])) {
				$history_id = $_GET['id'];				
				$Db -> model = $History -> model;
				if ($history_post_id = $Db -> field('post_id', array('id' => $history_id))) {
					$post_id = $history_post_id;
				}
			}
			
			if (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y") {									
				$status = (!empty($_POST['post_status'])) ? $_POST['post_status'] : 'draft';
				$slug = (!empty($_POST['post_slug'])) ? $_POST['post_slug'] : $Html -> sanitize($_POST['subject'], '-');
			
				$post = array(
					'ID'					=>	$post_id,
					'post_title'			=>	$_POST['subject'],
					'post_content'			=>	$this -> strip_set_variables($_POST['content']),
					'post_status'			=>	$status,
					'post_name'				=>	$slug,
					'post_category'			=>	$_POST['cat'],
					'post_type'				=>	((empty($_POST['newsletters_post_type'])) ? 'post' : $_POST['newsletters_post_type']),
					'post_author'			=>	(empty($_POST['post_author'])) ? $user_id : $_POST['post_author'],
				);
				
				stripslashes_deep($post);
				
				$currstatus = $this -> get_option('sendonpublish');
				$this -> update_option('sendonpublish', 'N');
				$_POST['sendtolist'] = "N";
				$post_id = wp_insert_post($post);				
				$this -> update_option('sendonpublish', $currstatus);
			}
			
			switch ($_GET['method']) {			
				case 'template'		:
					$mailinglists = $Mailinglist -> get_all('*', true);
					$templates = $Template -> get_all();
				
					if ($template = $Template -> get($_GET['id'])) {
						$this -> render_message(__('Email template has been loaded into the subject field and editor below.', $this -> plugin_name));
						$_POST = array('subject' => $template -> title, 'theme_id' => $template -> theme_id, 'inctemplate' => $template -> id, 'content' => $template -> content);
						$this -> render_admin('send', array('mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates));
					} else {
						$message = __('Email template could not be loaded, please try again.', $this -> plugin_name);
						$this -> redirect($this -> referer, "error", $message);	
					}
					break;				
				case 'history'		:
					$mailinglists = $Mailinglist -> get_all('*', true);
					$templates = $Template -> get_all();
				
					if ($history = $History -> get($_GET['id'])) {								
						$_POST = array(
							'ishistory'			=>	$history -> id,
							'from'				=>	$history -> from,
							'fromname'			=>	$history -> fromname,
							'subject'			=>	$history -> subject,
							'content'			=>	$history -> message,
							'groups'			=>	$history -> groups,
							'roles'				=>	maybe_unserialize($history -> roles),
							'mailinglists'		=>	$history -> mailinglists,
							'theme_id'			=>	$history -> theme_id,
							'post_id'			=>	$post_id,
							'condquery'			=>	maybe_unserialize($history -> condquery),
							'conditions'		=>	maybe_unserialize($history -> conditions),
							'conditionsscope'	=>	$history -> conditionsscope,
							'daterange'			=>	$history -> daterange,
							'daterangefrom'		=>	$history -> daterangefrom,
							'daterangeto'		=>	$history -> daterangeto,
							'fields'			=>	maybe_unserialize($history -> conditions),
							'attachments'		=>	$history -> attachments,
							'senddate'			=>	$history -> senddate,
							'customtexton'		=>	((!empty($history -> text)) ? true : false),
							'customtext'		=>	$history -> text,
						);
						
						if (!empty($_POST['condquery']) && !empty($_POST['conditions']) && !empty($_POST['conditionsscope'])) {
							$_POST['dofieldsconditions'] = 1;
						}
						
						if (!empty($history -> recurring) && $history -> recurring == "Y") {
							$_POST['sendrecurring'] = "Y";
							$_POST['sendrecurringvalue'] = $history -> recurringvalue;
							$_POST['sendrecurringinterval'] = $history -> recurringinterval;
							$_POST['sendrecurringdate'] = $history -> recurringdate;
							$_POST['sendrecurringlimit'] = $history -> recurringlimit;
							$_POST['sendrecurringsent'] = $history -> recurringsent;
						}
						
						if (!empty($post_id)) {
							if ($post = get_post($post_id)) {
								$_POST['cat'] = wp_get_post_categories($post_id);
								$_POST['post_status'] = $post -> post_status;
								$_POST['newsletters_post_type'] = $post -> post_type;
								$_POST['post_slug'] = $post -> post_name;
							}
						}
						
						$this -> render_admin('send', array('mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates), true, 'admin');
					} else {
						$message = __('Sent/draft email could not be loaded, please try again.', $this -> plugin_name);
						$this -> redirect('?page=' . $this -> sections -> history, "error", $message);	
					}
					break;				
				default				:								
					if (!empty($_POST)) {
						if (!empty($_POST['groups'])) {
							global $Db, $wpmlGroup, $Mailinglist;
							
							foreach ($_POST['groups'] as $group_id) {
								$Db -> model = $Mailinglist -> model;
								
								if ($mailinglists = $Db -> find_all(array('group_id' => $group_id), array('id'))) {
									foreach ($mailinglists as $mailinglist) {
										$_POST['mailinglists'][] = $mailinglist -> id;
									}
								}
							}
						}
						
						$mailinglists = false;
						$mailinglist = false;
						
						global $errors;
						$errors = array();
												
						if (empty($_POST['subject'])) { $errors['subject'] = __('Please fill in an email subject', $this -> plugin_name); }
						if (empty($_POST['content'])) { $errors['content'] = __('Please fill in a newsletter message', $this -> plugin_name); }
						
						if (empty($_POST['preview']) && empty($_POST['draft'])) {
							if ((empty($_POST['mailinglists']) || !is_array($_POST['mailinglists'])) && empty($_POST['roles'])) { 
								$errors['mailinglists'] = __('Please select mailing list(s)', $this -> plugin_name); 
							}
						}
						
						if (!empty($_POST['sendattachment']) && $_POST['sendattachment'] == "1") {						
							$newattachments = array();
							
							if (!empty($_POST['ishistory'])) {
								$Db -> model = $History -> model;
								if ($history = $Db -> find(array('id' => $_POST['ishistory']))) {
									$newattachments = $history -> attachments;
								}
							}
							
							if (!empty($_FILES['attachments']['name'])) {								
								foreach ($_FILES['attachments']['name'] as $fkey => $attachmentfile) {
									if ($_FILES['attachments']['error'][$fkey] == 0) {
										$name = $this -> stripext($_FILES['attachments']['name'][$fkey], 'name');
										$ext = $this -> stripext($_FILES['attachments']['name'][$fkey]);
										$attfile = $name . substr(time(), 0, 4) . '.' . $ext;
										$attpath = $Html -> uploads_path();
										$attsub = $Html -> uploads_subdir();
										$attfull = $attpath . $attsub . "/" . $attfile;
										
										if (move_uploaded_file($_FILES['attachments']['tmp_name'][$fkey], $attfull)) {
											$newattachments[] = array(
												'title'					=>	$_FILES['attachments']['name'][$fkey],
												'filename'				=>	str_replace("\\", "/", $attfull),
												'subdir'				=>	$attsub,
											);	
										}
									}
								}
							}
						}
						
						$_POST['subject'] = stripslashes($_POST['subject']);
						$_POST['content'] = stripslashes($_POST['content']);
						
						//unset the fields if the "dofieldsconditions" was unchecked
						if (empty($_POST['dofieldsconditions'])) {
							unset($_POST['fields']);
						}
	
						if (empty($errors)) {					
							if (empty($_POST['preview']) && empty($_POST['draft'])) {																		
								if (!empty($_POST)) {															
									if (!empty($errors)) {
										if ($this -> get_option('scheduling') == "Y") {
											$this -> render_error(__('Newsletter could not be scheduled/qeueued', $this -> plugin_name));
										} else {
											$this -> render_error(__('Newsletter could not be sent', $this -> plugin_name));
										}
									} else {										
										$history_data = array(
											'from'				=>	$_POST['from'],
											'fromname'			=>	$_POST['fromname'],
											'subject'			=>	$_POST['subject'],
											'message'			=>	$_POST['content'],
											'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? strip_tags($_POST['customtext']) : false),
											'theme_id'			=>	$_POST['theme_id'],
											'condquery'			=>	serialize($_POST['condquery']),
											'conditions'		=>	serialize($_POST['fields']),
											'conditionsscope'	=>	$_POST['fieldsconditionsscope'],
											'daterange'			=>	$_POST['daterange'],
											'daterangefrom'		=>	$_POST['daterangefrom'],
											'daterangeto'		=>	$_POST['daterangeto'],
											'mailinglists'		=>	serialize($_POST['mailinglists']),
											'groups'			=>	serialize($_POST['groups']),
											'roles'				=>	serialize($_POST['roles']),
											'post_id'			=>	$post_id,
											'newattachments'	=>	$newattachments,
											'senddate'			=>	$_POST['senddate'],
											'scheduled'			=>	$_POST['scheduled'],
										);
										
										//is this a recurring newsletter?
										if (!empty($_POST['sendrecurring'])) {
											if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
												$history_data['recurring'] = "Y";
												$history_data['recurringvalue'] = $_POST['sendrecurringvalue'];
												$history_data['recurringinterval'] = $_POST['sendrecurringinterval'];
												$history_data['recurringsent'] = $_POST['recurringsent'];
												
												if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
													$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
												} else {
													$history_data['recurringdate'] = $_POST['sendrecurringdate'];
												}
												
												$history_data['recurringlimit'] = $_POST['sendrecurringlimit'];
											}
										}
										
										//is this an existing newsletter?
										if (!empty($_POST['ishistory'])) {										
											$history_data['id'] = $_POST['ishistory'];
											
											$Db -> model = 'History';
											if ($history_curr = $Db -> find(array('id' => $history_data['id']))) {
												$history_data['sent'] = ($history_curr -> sent + 1);
											} else {
												$history_data['sent'] = 1;
											}
										} else {
											$history_data['sent'] = 1;
										}
										
										$History -> save($history_data, false);
										$history_id = $History -> insertid;
										if (!empty($_POST['contentarea'])) {
											foreach ($_POST['contentarea'] as $number => $content) {
												$content_data = array(
													'number'			=>	$number,
													'history_id'		=>	$history_id,
													'content'			=>	$content,
												);
											
												$this -> Content -> save($content_data, true);
											}
										}
									
										if (empty($_POST['sendtype']) || $_POST['sendtype'] == "queue" || $_POST['sendtype'] == "send") {
											if ($this -> get_option('subscriptions') == "Y") {
												$SubscribersList -> check_expirations();
											}
											
											$subscriberids = array();
											$subscriberemails = array();
											
											if (!empty($_POST['mailinglists']) || !empty($_POST['roles'])) {
												//$this -> scheduling(true);
												$mailinglistscondition = false;
												if (!empty($_POST['mailinglists'])) {
													$mailinglistscondition = "(";
													$m = 1;
													
													foreach ($_POST['mailinglists'] as $mailinglist_id) {
														$mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";
														if ($m < count($_POST['mailinglists'])) { $mailinglistscondition .= " OR "; }
														$m++;	
													}
													
													/* Fields Conditions */
													if (!empty($_POST['dofieldsconditions'])) {
														$fieldsquery = "";
														$scopeall = (empty($_POST['fieldsconditionsscope']) || $_POST['fieldsconditionsscope'] == "all") ? true : false;
														
														if (!empty($_POST['fields'])) {
															$fieldsquery .= " AND (";
															$f = 1;
														
															foreach ($_POST['fields'] as $field_slug => $field_value) {
																if (!empty($field_value)) {
																	$Db -> model = $Field -> model;
																	$customfield = $Db -> find(array('slug' => $field_slug), array('id', 'slug', 'type'));
																	$condition = $_POST['condquery'][$field_slug];
																	
																	switch ($condition) {
																		case 'smaller'				:
																			$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " < '" . $field_value . "'";
																			break;
																		case 'larger'				:
																			$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " > '" . $field_value . "'";
																			break;
																		case 'contains'				:
																			$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " LIKE '%" . $field_value . "%'";
																			break;
																		case 'equals'				:
																		default						:
																			$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $field_slug . " = '" . $field_value . "'";
																			break;
																	}
																	
																	if ($f < count($_POST['fields'])) {
																		$fieldsquery .= ($scopeall) ? " AND" : " OR";
																	}
																}
																
																$f++;
															}
															
															$fieldsquery .= ")";
															$fieldsquery = str_replace(" AND)", "", $fieldsquery);
															$fieldsquery = str_replace(" OR)", "", $fieldsquery);
															$fieldsquery .= ")";
															$fieldsquery = str_replace("))", ")", $fieldsquery);
														}
													}
													
													if (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") {
														if (!empty($_POST['daterangefrom']) && !empty($_POST['daterangeto'])) {
															$daterangefrom = date_i18n("Y-m-d", strtotime($_POST['daterangefrom']));
															$daterangeto = date_i18n("Y-m-d", strtotime($_POST['daterangeto']));
															$fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
														}
													}
												}
												
												/* Attachments */
												$Db -> model = $History -> model;
												$history = $Db -> find(array('id' => $history_id));
												
												$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
												. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
												. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
												. $wpdb -> prefix . $SubscribersList -> table . " ON "
												. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id 
												LEFT JOIN " . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = 
												" . $wpdb -> prefix . $Mailinglist -> table . ".id";
												
												if (!empty($mailinglistscondition)) {
													$query .= " WHERE " . $mailinglistscondition . ")";
												}
												
												$query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y' 
												AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval 
												OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')"
												. str_replace(" AND ()", "", $fieldsquery);
												
												$sentmailscount = 0;
												$sendingprogress_option = $this -> get_option('sendingprogress');
												$sendingprogress = (!empty($_POST['sendingprogress'])) ? "Y" : "N";
												$datasets = array();
												$q_queries = array();
												$d = 0;
												
												if (!empty($_POST['roles'])) {
													$users = array();
													$exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
													$exclude_users = $wpdb -> get_var($exclude_users_query);
													
													foreach ($_POST['roles'] as $role_key) {
														$users_arguments = array(
															'blog_id'				=>	$GLOBALS['blog_id'],
															'role'					=>	$role_key,
															'exclude'				=>	$exclude_users,
															'fields'				=>	array('ID', 'user_email', 'user_login'),
														);
														
														$role_users = get_users($users_arguments);
														$users = array_merge($users, $role_users);
													}
													
													if (!empty($users)) {
														foreach ($users as $user) {
															$this -> remove_server_limits();
															
															if ($sendingprogress == "N") {
																$q_queries[] = $Queue -> save(
																	false,
																	$user, 
																	$_POST['subject'], 
																	$_POST['content'], 
																	$newattachments, 
																	$post_id, 
																	$history_id, 
																	true, 
																	$_POST['theme_id'], 
																	$_POST['senddate']
																);
															} else {
																$dataset = array(
																	'id'				=>	false,
																	'user_id'			=>	$user -> ID,
																	'email'				=>	$user -> user_email,
																	'mailinglist_id'	=>	false,
																	'mailinglists'		=>	false,
																	'format'			=> 	'html',
																);
															
																$datasets[$d] = $dataset;
															}
															
															$d++;
														}
													}
												}
												
												if (!empty($_POST['mailinglists'])) {
													$subscribers = $wpdb -> get_results($query);
													
													if (!empty($subscribers)) {													
														foreach ($subscribers as $subscriber) {
															$this -> remove_server_limits();											
															$subscriber -> mailinglist_id = $_POST['mailinglists'][0];										
															$subscriber -> mailinglists = $_POST['mailinglists'];
															
															if ($sendingprogress == "N") {
																$q_queries[] = $Queue -> save(
																	$subscriber,
																	false, 
																	$_POST['subject'], 
																	$_POST['content'], 
																	$newattachments, 
																	$post_id, 
																	$history_id, 
																	true, 
																	$_POST['theme_id'], 
																	$_POST['senddate']
																);
															} else {
																$dataset = array(
																	'id'				=>	$subscriber -> id,
																	'email'				=>	$subscriber -> email,
																	'mailinglist_id'	=>	$subscriber -> mailinglist_id,
																	'mailinglists'		=>	$subscriber -> mailinglists,
																	'format'			=> 	(empty($subscriber -> format) ? 'html' : $subscriber -> format),
																);
															
																$datasets[$d] = $dataset;
															}
															
															$d++;
														}
													}
												}
												
												if (!empty($q_queries)) {												
													foreach ($q_queries as $q_query) {
														if (!empty($q_query)) {
															$wpdb -> query($q_query);
														}
													}
												}
												
												if ($sendingprogress == "Y") {						
													$this -> render('send-post', array('subscribers' => $datasets, 'subject' => $_POST['subject'], 'content' => $_POST['content'], 'attachments' => $newattachments, 'post_id' => $post_id, 'history_id' => $history_id, 'theme_id' => $_POST['theme_id']), true, 'admin');
													$dontrendersend = true;
												} else {
													do_action($this -> pre . '_admin_emailsqueued', (count($subscribers) + count($users)));
												
													$message = (count($subscribers) + count($users)) . ' ' . __('emails have been queued.', $this -> plugin_name);
													$this -> redirect('?page=' . $this -> sections -> queue, 'message', $message);
												}
											} else {
												$message = __('No mailing lists or roles have been selected', $this -> plugin_name);
												$this -> render_error($message);
											}
										} else {
											$message = sprintf(__('Newsletter has been scheduled for %s', $this -> plugin_name), $_POST['senddate']);
											$this -> redirect('?page=' . $this -> sections -> history, 'message', $message);
										}
										
										if (!empty($_POST['inctemplate'])) {
											$Template -> inc_sent($_POST['inctemplate']);
										}
									}
								}
							/* Save email as draft */
							} elseif (!empty($_POST['draft'])) {																									
								$history_data = array(
									'from'				=>	$_POST['from'],
									'fromname'			=>	$_POST['fromname'],
									'subject'			=>	$_POST['subject'],
									'message'			=>	$_POST['content'],
									'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? strip_tags($_POST['customtext']) : false),
									'theme_id'			=>	$_POST['theme_id'],
									'condquery'			=>	serialize($_POST['condquery']),
									'conditions'		=>	serialize($_POST['fields']),
									'conditionsscope'	=>	$_POST['fieldsconditionsscope'],
									'daterange'			=>	$_POST['daterange'],
									'daterangefrom'		=>	$_POST['daterangefrom'],
									'daterangeto'		=>	$_POST['daterangeto'],
									'post_id'			=>	$post_id,
									'mailinglists'		=>	serialize($_POST['mailinglists']),
									'groups'			=>	serialize($_POST['groups']),
									'roles'				=>	serialize($_POST['roles']),
									'newattachments'	=>	$newattachments,
									'recurring'			=>	"N",
									'senddate'			=>	$_POST['senddate'],
									'scheduled'			=>	$_POST['scheduled'],
								);
								
								if (!empty($_POST['ishistory'])) {										
									$history_data['id'] = $_POST['ishistory'];
									
									$Db -> model = $History -> model;
									if ($history_curr = $Db -> find(array('id' => $history_data['id']), array('id', 'sent', 'recurringdate'))) {
										$history_data['sent'] = $history_curr -> sent;
									}
								}
								
								if (!empty($_POST['sendrecurring'])) {
									if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
										$history_data['recurring'] = "Y";
										$history_data['recurringvalue'] = $_POST['sendrecurringvalue'];
										$history_data['recurringinterval'] = $_POST['sendrecurringinterval'];
										$history_data['recurringsent'] = $_POST['recurringsent'];
										
										if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
											$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
										} else {
											$history_data['recurringdate'] = $_POST['sendrecurringdate'];
										}
										
										$history_data['recurringlimit'] = $_POST['sendrecurringlimit'];
									}
								}
								
								if ($History -> save($history_data, false)) {
									if (!empty($_POST['contentarea'])) {
										$history_id = $History -> insertid;
										
										foreach ($_POST['contentarea'] as $number => $content) {
											$content_data = array(
												'number'			=>	$number,
												'history_id'		=>	$history_id,
												'content'			=>	$content,
											);
										
											$this -> Content -> save($content_data, true);
										}
									}
																
									$message = __('Draft has been successfully saved. It has been saved to your email history.', $this -> plugin_name);
									$this -> redirect('?page=' . $this -> sections -> send . '&method=history&id=' . $History -> insertid, 'message', $message);
								} else {
									$message = __('Draft could not be saved. Please fill in all required fields', $this -> plugin_name);
									$this -> render_error($message);
								}
							/* Send a preview email */
							} else {											
								$history_data = array(
									'from'				=>	$_POST['from'],
									'fromname'			=>	$_POST['fromname'],
									'subject'			=>	$_POST['subject'],
									'message'			=>	$_POST['content'],
									'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? strip_tags($_POST['customtext']) : false),
									'theme_id'			=>	$_POST['theme_id'],
									'condquery'			=>	serialize($_POST['condquery']),
									'conditions'		=>	serialize($_POST['fields']),
									'conditionsscope'	=>	$_POST['fieldsconditionsscope'],
									'daterange'			=>	$_POST['daterange'],
									'daterangefrom'		=>	$_POST['daterangefrom'],
									'daterangeto'		=>	$_POST['daterangeto'],
									'mailinglists'		=>	serialize($_POST['mailinglists']),
									'groups'			=>	serialize($_POST['groups']),
									'roles'				=>	serialize($_POST['roles']),
									'post_id'			=>	$post_id,
									'newattachments'	=>	$newattachments,
									'recurring'			=>	"N",
									'senddate'			=>	$_POST['senddate'],
									'scheduled'			=>	$_POST['scheduled'],
								);
								
								if (!empty($_POST['ishistory'])) {										
									$history_data['id'] = $_POST['ishistory'];
									
									$Db -> model = $History -> model;
									if ($history_curr = $Db -> find(array('id' => $history_data['id']))) {
										$history_data['sent'] = $history_curr -> sent;
									}
								}
								
								if (!empty($_POST['sendrecurring'])) {
									if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
										$history_data['recurring'] = "Y";
										$history_data['recurringvalue'] = $_POST['sendrecurringvalue'];
										$history_data['recurringinterval'] = $_POST['sendrecurringinterval'];
										$history_data['recurringsent'] = $_POST['recurringsent'];
										
										if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
											$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
										} else {
											$history_data['recurringdate'] = $_POST['sendrecurringdate'];
										}
										
										$history_data['recurringlimit'] = $_POST['sendrecurringlimit'];
									}
								}
								
								$History -> save($history_data, false);
								$history_id = $History -> insertid;
								if (!empty($_POST['contentarea'])) {
									foreach ($_POST['contentarea'] as $number => $content) {
										$content_data = array(
											'number'			=>	$number,
											'history_id'		=>	$history_id,
											'content'			=>	$content,
										);
									
										$this -> Content -> save($content_data, true);
									}
								}
								$Db -> model = $History -> model;
								$history = $Db -> find(array('id' => $history_id));
							
								$subscriber_id = $Subscriber -> admin_subscriber_id();
								if (!empty($_POST['previewemail'])) {
									$emails = explode(",", $_POST['previewemail']);
									
									foreach ($emails as $email) {
										if (!$subscriber_id = $Subscriber -> email_exists($email)) {
											$subscriber_data = array('email' => $email);										
											$Subscriber -> save($subscriber_data, false);
											$subscriber_id = $Subscriber -> insertid;
										}	
										
										$subscriber = $Subscriber -> get($subscriber_id, false);
										$subject = $_POST['subject'];
										$message = $this -> render_email('send', array('message' => $_POST['content'], 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, $_POST['theme_id']);
										
										if (!$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, false, false)) {
											global $mailerrors;
											$this -> render_error(sprintf(__('Preview cannot be sent to %s, %s.', $this -> plugin_name), $subscriber -> email, $mailerrors));
										} else {
											//$this -> render_message(sprintf(__('Preview has been sent to %s', $this -> plugin_name), ' <strong>' . $subscriber -> email . '</strong>'));
											$message = sprintf(__('Preview has been sent to %s', $this -> plugin_name), ' <strong>' . $subscriber -> email . '</strong>');
											$this -> redirect('?page=' . $this -> sections -> send . '&method=history&id=' . $History -> insertid, 'message', $message);
										}
									}
								}
								
								$_POST = array(
									'ishistory'			=>	$history -> id,
									'from'				=>	$history -> from,
									'fromname'			=>	$history -> fromname,
									'subject'			=>	$history -> subject,
									'content'			=>	$history -> message,
									'groups'			=>	$history -> groups,
									'roles'				=>	maybe_unserialize($history -> roles),
									'mailinglists'		=>	$history -> mailinglists,
									'theme_id'			=>	$history -> theme_id,
									'condquery'			=>	maybe_unserialize($history -> condquery),
									'conditions'		=>	maybe_unserialize($history -> conditions),
									'conditionsscope'	=>	$history -> conditionsscope,
									'daterange'			=>	$history -> daterage,
									'daterangefrom'		=>	$history -> daterangefrom,
									'daterangeto'		=>	$history -> daterangeto,
									'fields'			=>	maybe_unserialize($history -> conditions),
									'attachments'		=>	$history -> attachments,
									'customtexton'		=>	((!empty($history -> text)) ? true : false),
									'customtext'		=>	$history -> text,
								);
							}
						} else {
							if (!empty($_POST['preview'])) {
								$message = __('Preview could not be sent', $this -> plugin_name);
							} else {
								if (!empty($_POST['sendtype']) && $_POST['sendtype'] == "queue") {
									$message = __('Newsletter could not be scheduled/queued', $this -> plugin_name);
								} else {
									$message = __('Newsletter could not be sent', $this -> plugin_name);
								}
							}
							
							$this -> render_error($message);
						}
					}
					
					if (empty($dontrendersend) || $dontrendersend == false) {
						$mailinglists = $Mailinglist -> get_all('*', true);
						$templates = $Template -> get_all();
					
						$this -> render('send', array('mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates, 'errors' => $errors), true, 'admin');
					}
					break;
				}
		}
		
		function admin_autoresponders() {
			global $wpdb, $Db, $Autoresponder, $AutorespondersList, $Autoresponderemail;
			$Db -> model = $Autoresponder -> model;
			
			switch ($_GET['method']) {
				case 'save'					:
					if (!empty($_POST)) {
						if ($Db -> save($_POST)) {
							$message = __('Autoresponder has been saved.', $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&method=save&id=' . $Autoresponder -> insertid . '&continueediting=1'), 'message', $message);
							} else {
								$this -> redirect("?page=" . $this -> sections -> autoresponders, 'message', $message);
							}
						} else {
							$this -> render_error(__('Autoresponder could not be saved, please try again.', $this -> plugin_name));	
							$this -> render('autoresponders' . DS . 'save', false, true, 'admin');
						}
					} else {
						if (!empty($_GET['id'])) {
							$Db -> model = $Autoresponder -> model;
							$autoresponder = $Db -> find(array('id' => $_GET['id']));	
						}
						
						$this -> render('autoresponders' . DS . 'save', array('autoresponder' => $autoresponder), true, 'admin');
					}
					break;
				case 'delete'				:
					if (!empty($_GET['id'])) {
						if ($Db -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Autoresponder has been deleted.', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Autoresponder cannot be deleted, please try again.', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No autoresponder was specified.', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'mass'					:
					if (!empty($_POST['autoresponderslist'])) {
						if (!empty($_POST['action'])) {
							$autoresponders = $_POST['autoresponderslist'];
							
							switch ($_POST['action']) {
								case 'delete'				:
									foreach ($autoresponders as $autoresponder_id) {
										//remove the autoresponder
										$Db -> model = $Autoresponder -> model;
										$Db -> delete($autoresponder_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected autoresponders and scheduled messages were removed.', $this -> plugin_name);
									break;
								case 'activate'				:
									foreach ($autoresponders as $autoresponder_id) {
										$Db -> model = $Autoresponder -> model;
										$Db -> save_field('status', "active", array('id' => $autoresponder_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected autoresponders have been activated and messages will be scheduled for new subscriptions.', $this -> plugin_name);
									break;
								case 'deactivate'			:
									foreach ($autoresponders as $autoresponder_id) {
										$Db -> model = $Autoresponder -> model;
										$Db -> save_field('status', "inactive", array('id' => $autoresponder_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected autoresponders have been deactivated and no more messages will be scheduled for them.', $this -> plugin_name);
									break;	
							}
						} else {
							$msg_type = 'error';
							$message = __('No action was specified.', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No autoresponders were selected.', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> autoresponders, $msg_type, $message);
					break;
				case 'autoresponderscheduling'			:
					if (!empty($_POST['autoresponderscheduling'])) {
						$this -> update_option('autoresponderscheduling', $_POST['autoresponderscheduling']);
						$this -> autoresponder_scheduling();
						
						$msg_type = 'message';
						$message = __('Autoresponders schedule interval has been updated.', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No schedule interval was chosen.', $this -> plugin_name);
					}
					
					$this -> redirect('?page=' . $this -> sections -> autoresponders, $msg_type, $message);
					break;
				default						:
				
					$dojoin = false;
					$conditions_and = array();
					$autoresponders_table = $wpdb -> prefix . $Autoresponder -> table;
					$autoresponderslist_table = $wpdb -> prefix . $AutorespondersList -> table;
				
					$perpage = (isset($_COOKIE[$this -> pre . 'autorespondersperpage'])) ? $_COOKIE[$this -> pre . 'autorespondersperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$sections = $this -> sections -> autoresponders;
					
					if (!empty($_GET['filter'])) {
						$sections .= '&filter=1';
						
						if (!empty($_GET['list'])) {
							switch ($_GET['list']) {
								case 'all'					:
									$dojoin = false;
									break;
								case 'none'					:
									$dojoin = false;
									$conditions_and[$autoresponders_table . '.id'] = "NOT IN (SELECT autoresponder_id FROM " . $autoresponderslist_table . ")";
									break;
								default						:
									$dojoin = true;
									$conditions_and[$autoresponderslist_table . '.list_id'] = $_GET['list'];
									break;
							}
						}
						
						if (!empty($_GET['status'])) {
							switch ($_GET['status']) {
								case 'active'				:
									$conditions_and[$autoresponders_table . '.status'] = 'active';
									break;
								case 'inactive'				:
									$conditions_and[$autoresponders_table . '.status'] = 'inactive';
									break;
								default 					:
									//do nothing, all statuses
									break;
							}
						}
					}
					
					$data = array();
					if (!empty($_GET['showall'])) {
						$Db -> model = $Autoresponder -> model;
						$autoresponders = $Db -> find_all(false, "*", $order);
						$data[$Autoresponder -> model] = $autoresponders;
						$data['Paginate'] = false;
					} else {
						if ($dojoin) {
							$data = $this -> paginate($AutorespondersList -> model, false, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$autoresponders = $data[$AutorespondersList -> model];
						} else {
							$data = $this -> paginate($Autoresponder -> model, false, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$autoresponders = $data[$Autoresponder -> model];	
						}
					}
					
					$this -> render_message(__('Please note that autoresponder emails are only sent to Active subscriptions. Once a subscription is Active, the autoresponder email will queue.', $this -> plugin_name));
					$this -> render('autoresponders' . DS . 'index', array('autoresponders' => $autoresponders, 'paginate' => $data['Paginate']), true, 'admin');
					break;	
			}
		}
		
		function admin_autoresponderemails() {
			global $wpdb, $Db, $Autoresponder, $Autoresponderemail,
			$History, $Subscriber, $Queue, $SubscribersList, $Html;
			
			switch ($_GET['method']) {
				case 'send'					:
					if (!empty($_GET['id'])) {
						$query = "SELECT " . $wpdb -> prefix . $Autoresponderemail -> table . ".id, " 
						. $wpdb -> prefix . $SubscribersList -> table . ".list_id, "
						. $wpdb -> prefix . $Autoresponderemail -> table . ".subscriber_id, "
						. $wpdb -> prefix . $Autoresponderemail -> table . ".autoresponder_id FROM " . $wpdb -> prefix . $Autoresponderemail -> table . " LEFT JOIN " 
						. $wpdb -> prefix . $SubscribersList -> table . " ON " . $wpdb -> prefix . $Autoresponderemail -> table . ".subscriber_id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id 
						WHERE " . $wpdb -> prefix . $Autoresponderemail -> table . ".id = '" . $_GET['id'] . "' LIMIT 1";
						
						$ae = $wpdb -> get_row($query);
						
						if (!empty($ae)) {						
							$query = "SELECT " . $wpdb -> prefix . $History -> table . ".id, " 
							. $wpdb -> prefix . $History -> table . ".subject, " 
							. $wpdb -> prefix . $History -> table . ".message, " 
							. $wpdb -> prefix . $History -> table . ".theme_id FROM " 
							. $wpdb -> prefix . $History -> table . " LEFT JOIN " 
							. $wpdb -> prefix . $Autoresponder -> table . " ON " . $wpdb -> prefix . $History -> table . ".id = " . $wpdb -> prefix . $Autoresponder -> table . ".history_id WHERE " 
							. $wpdb -> prefix . $Autoresponder -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";
							
							$history = $wpdb -> get_row($query);
						
					
							/* The Subscriber */
							$Db -> model = $Subscriber -> model;
							$subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
							$subscriber -> mailinglist_id = $ae -> list_id;
							
							/* The Message */
							$eunique = $Html -> eunique($subscriber, $history -> id);
							
							/* Send the email */
							$Db -> model = $Email -> model;
							$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
							
							if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique)) {								
								$Db -> model = $Autoresponderemail -> model;
								$Db -> save_field('status', "sent", array('id' => $ae -> id));
								$addedtoqueue++;
								$msg_type = 'message';
								$message = __('Autoresponder email has been sent.', $this -> plugin_name);
							} else {
								$msg_type = 'error';
								$message = __('Autoresponder email could not be sent, please check your email settings.', $this -> plugin_name);
							}
						} else {
							$msg_type = 'error';
							$message = __('Autoresponder email cannot be read.', $this -> plugin_name);	
						}
					} else {
						$msg_type = 'error';
						$message = __('No autoresponder email was specified.', $this -> plugin_name);	
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'delete'				:
					if (!empty($_GET['id'])) {
						$Db -> model = $Autoresponderemail -> model;
						
						if ($Db -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Autoresponder email has been removed.', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Autoresponder email cannot be deleted.', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No autoresponder email has been specified.', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'mass'					:
					if (!empty($_POST['autoresponderemailslist'])) {
						if (!empty($_POST['action'])) {
							$autoresponderemails = $_POST['autoresponderemailslist'];
							
							switch ($_POST['action']) {
								case 'delete'				:
									foreach ($autoresponderemails as $ae_id) {
										//remove the autoresponder
										$Db -> model = $Autoresponderemail -> model;
										$Db -> delete($ae_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected autoresponder emails were removed.', $this -> plugin_name);
									break;
								case 'send'				:
									foreach ($autoresponderemails as $ae_id) {
										$query = "SELECT " . $wpdb -> prefix . $Autoresponderemail -> table . ".id, " 
										. $wpdb -> prefix . $SubscribersList -> table . ".list_id, "
										. $wpdb -> prefix . $Autoresponderemail -> table . ".subscriber_id, "
										. $wpdb -> prefix . $Autoresponderemail -> table . ".autoresponder_id FROM " . $wpdb -> prefix . $Autoresponderemail -> table . " LEFT JOIN " 
										. $wpdb -> prefix . $SubscribersList -> table . " ON " . $wpdb -> prefix . $Autoresponderemail -> table . ".subscriber_id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id 
										WHERE " . $wpdb -> prefix . $Autoresponderemail -> table . ".id = '" . $ae_id . "' LIMIT 1";
										
										if ($ae = $wpdb -> get_row($query)) {						
											$query = "SELECT " . $wpdb -> prefix . $History -> table . ".id, " 
											. $wpdb -> prefix . $History -> table . ".subject, " 
											. $wpdb -> prefix . $History -> table . ".message, " 
											. $wpdb -> prefix . $History -> table . ".theme_id FROM " 
											. $wpdb -> prefix . $History -> table . " LEFT JOIN " 
											. $wpdb -> prefix . $Autoresponder -> table . " ON " . $wpdb -> prefix . $History -> table . ".id = " . $wpdb -> prefix . $Autoresponder -> table . ".history_id WHERE " 
											. $wpdb -> prefix . $Autoresponder -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";
											
											$history = $wpdb -> get_row($query);
									
											/* The Subscriber */
											$Db -> model = $Subscriber -> model;
											$subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
											$subscriber -> mailinglist_id = $ae -> list_id;
											
											/* The Message */
											$eunique = $Html -> eunique($subscriber, $history -> id);
											
											/* Send the email */
											$Db -> model = $Email -> model;
											$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
											
											if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique)) {								
												$Db -> model = $Autoresponderemail -> model;
												$Db -> save_field('status', "sent", array('id' => $ae -> id));
												$addedtoqueue++;
											}
										}
									}
									
									$msg_type = 'message';
									$message = __('Selected autoresponder emails were sent.', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No action was specified.', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No autoresponders were selected.', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> autoresponderemails, $msg_type, $message);
					break;
				default						:
					$perpage = (isset($_COOKIE[$this -> pre . 'autoresponderemailsperpage'])) ? $_COOKIE[$this -> pre . 'autoresponderemailsperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
					
					if (!empty($_GET['status'])) {
						$_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] = $_GET['status'];
					}
					
					if (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'])) {
						switch($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) {
							case 'all'		:
								//do nothing...
								break;
							case 'sent'		:
								$conditions['status'] = "sent";
								break;
							case 'unsent'	:
							default			:
								$conditions['status'] = "unsent";
								break;	
						}
					}
					
					if (!empty($_GET['id'])) {
						$_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] = $_GET['id'];
					}
					
					if (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'])) {
						if (empty($conditions['status'])) {
							$conditions['autoresponder_id'] = $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'];
						} else {
							$conditions['status'] .= "' AND autoresponder_id = '" . $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] . "";
						}
					}
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					if (!empty($_GET['showall'])) {
						$Db -> model = $Autoresponderemail -> model;
						$autoresponderemails = $Db -> find_all($conditions, "*", $order, false, true);
						$data[$Autoresponderemail -> model] = $autoresponderemails;
						$data['Paginate'] = false;	
					} else {
						$data = $this -> paginate($Autoresponderemail -> model, false, $this -> sections -> autoresponderemails, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_message(__('Please note that autoresponder emails are only sent to Active subscriptions. Once a subscription is Active, the autoresponder email will queue.', $this -> plugin_name));
					$this -> render('autoresponderemails' . DS . 'index', array('autoresponderemails' => $data[$Autoresponderemail -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;	
			}
		}
		
		function admin_mailinglists() {
			global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList;
			$Db -> model = $Mailinglist -> model;
		
			switch ($_GET['method']) {
				case 'save'			:
					if (!empty($_POST)) {
						if ($Mailinglist -> save($_POST)) {
							$message = __('Mailing list has been saved', $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> lists . '&method=save&id=' . $Mailinglist -> insertid . '&continueediting=1'), 'message', $message);
							} else {
								$this -> redirect($this -> url, 'message', $message);
							}
						} else {
							$this -> render_error(__('Mailing list could not be saved', $this -> plugin_name));
							$mailinglist = $this -> init_class('wpmlMailinglist', $_POST);
							$this -> render_admin('mailinglists' . DS . 'save', array('mailinglist' => $mailinglist, 'errors' => $this -> Mailinglist -> errors));
						}
					} else {
						if (!empty($_GET['id'])) {
							$mailinglist = $Mailinglist -> get($_GET['id']);
						}
						
						if (!empty($_GET['group_id'])) { $Mailinglist -> data -> group_id = $_GET['group_id']; }
						$this -> render_admin('mailinglists' . DS . 'save', array('mailinglist' => $mailinglist));
					}
					break;
				case 'view'			:
					if (!empty($_GET['id'])) {
						if ($mailinglist = $Mailinglist -> get($_GET['id'])) {
							$perpage = (!empty($_COOKIE[$this -> pre . 'subscribersperpage'])) ? $_COOKIE[$this -> pre . 'subscribersperpage'] : 15;						
							$sub = $this -> sections -> lists . '&amp;method=view&amp;id=' . $_GET['id'];
							$subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
							$conditions = array($subscriberslists_table . '.list_id' => $_GET['id']);
							$searchterm = false;
							$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
							$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
							$order = array($orderfield, $orderdirection);
							$data = $this -> paginate($SubscribersList -> model, false, $sub, $conditions, $searchterm, $perpage, $order);
							$subscribers = $data[$SubscribersList -> model];
							
							$this -> render_admin('mailinglists' . DS . 'view', array('mailinglist' => $mailinglist, 'subscribers' => $subscribers, 'paginate' => $data['Paginate']));
						} else {
							$this -> render_error(__('Mailing list could not be read', $this -> plugin_name));
						}
					} else {
						$this -> render_error(__('No mailing list was specified', $this -> plugin_name));
					}
					break;
				case 'delete'		:
					if (!empty($_GET['id'])) {
						if ($Mailinglist -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Mailing list has been removed', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Mailing list cannot be removed', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No mailing list was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break 1;
				case 'mass'			:
					if (!empty($_POST['mailinglistslist'])) {
						if (!empty($_POST['action'])) {
							$lists = $_POST['mailinglistslist'];
							
							switch ($_POST['action']) {
								case 'singleopt'		:
									foreach ($lists as $list_id) {
										$Db -> model = $Mailinglist -> model;
										$Db -> save_field('doubleopt', "N", array('id' => $list_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected lists set as single opt-in', $this -> plugin_name);
									break;
								case 'doubleopt'		:
									foreach ($lists as $list_id) {
										$Db -> model = $Mailinglist -> model;
										$Db -> save_field('doubleopt', "Y", array('id' => $list_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected lists set as doublt opt-in', $this -> plugin_name);
									break;
								case 'merge'			:
									global $Db, $Mailinglist, $SubscribersList, $FieldsList, $HistoriesList;
								
									if (!empty($_POST['list_title'])) {
										if (count($lists) > 1) {
											$list_data = array(
												'title'					=>	$_POST['list_title'],
												'privatelist'			=>	"N",
												'paid'					=> 	"N",
											);
											
											if ($Mailinglist -> save($list_data)) {
												$new_list_id = $Mailinglist -> insertid;
												
												foreach ($lists as $list_id) {
													$Db -> model = $SubscribersList -> model;
													$Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));	
													$Db -> model = $FieldsList -> model;
													$Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));
													$Db -> model = $HistoriesList -> model;
													$Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));
													$Mailinglist -> delete($list_id);
												}
												
												$msg_type = 'message';
												$message = __('Selected lists have been merged', $this -> plugin_name);
											} else {
												$msg_type = 'error';
												$message = __('Merge list could not be created', $this -> plugin_name);
											}
										} else {
											$msg_type = 'error';
											$message = __('Select more than one list in order to merge', $this -> plugin_name);
										}
									} else {
										$msg_type = 'error';
										$message = __('Fill in a list title for the new list', $this -> plugin_name);
									}
								
									break;
								case 'setgroup'			:
									if (!empty($_POST['setgroup_id'])) {
										foreach ($lists as $list_id) {
											$Mailinglist -> save_field('group_id', $_POST['setgroup_id'], $list_id);	
										}
										
										$msg_type = "message";
										$message = __('Selected mailing lists assigned to the chosen group.', $this -> plugin_name);
									} else {
										$msg_type = "message";
										$message = __('No group was selected.', $this -> plugin_name);
									}
									break;
								case 'delete'			:
									$Mailinglist -> delete_array($lists);
									$msg_type = "message";
									$message = __('Selected mailing lists have been removed', $this -> plugin_name);
									break;
								case 'private'			:
									foreach ($lists as $id) {
										$Mailinglist -> save_field('privatelist', 'Y', $id);
									}
									
									$msg_type = "message";
									$message = __('Selected mailing lists have been set as private', $this -> plugin_name);
									break;
								case 'notprivate'		:
									foreach ($lists as $id) {
										$Mailinglist -> save_field('privatelist', 'N', $id);
									}
									
									$msg_type = "message";
									$message = __('Selected mailing lists have been set as not private', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = "error";
							$message = __('No action was selected', $this -> plugin_name);
						}
					} else {
						$msg_type = "error";
						$message = __('No mailing lists were selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'offsite'			:
					if (!empty($_GET['listid'])) {
						$this -> update_option('offsitelist', $_GET['listid']);
					} else {
						$msg_type = 'error';
						$message = __('No mailing list was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url . '&method=offsitewizard&listid=' . $_GET['listid'], $msg_type, $message);
					break;
				case 'offsitewizard'	:
					global $Html, $FieldsList;
				
					$code = false;
					$listid = (!empty($_GET['listid'])) ? $_GET['listid'] : $_POST['list'];
					
					if (!empty($_POST)) {
						$opts = array('title', 'formtype', 'list', 'width', 'height', 'button', 'stylesheet', 'fields');
					
						foreach ($_POST as $pkey => $pval) {					
							if (in_array($pkey, $opts)) {						
								if (!empty($pval)) {
									$this -> update_option('offsite' . $pkey, stripslashes($pval));
								}
							}
						}
															
						if (empty($listid)) {
							$this -> render_error(__('Please select mailing list(s)', $this -> plugin_name));
						}
					} else {
						$this -> render_message(__('Offsite code is specifically used for non-WordPress and other remote websites, not for the current one.', $this -> plugin_name));	
					}
	
					if (!empty($listid)) {				
						$options = $this -> get_option('offsite');
						
						$options['title'] = (empty($_POST['title'])) ? get_option('blogname') : $_POST['title'];
						$options['list'] = $listid;
						$options['button'] = (empty($_POST['button'])) ? __('Subscribe', $this -> plugin_name) : $_POST['button'];
						$options['ajax'] = "Y";
						$options['stylesheet'] = (empty($_POST['stylesheet'])) ? "Y" : $_POST['stylesheet'];
						$wpoptinid = time();
						$options['wpoptinid'] = $wpoptinid;
						
						$fields = false;
						if (!empty($_POST['formtype']) && $_POST['formtype'] == "popup") {
							if (empty($_POST['fields']) || (!empty($_POST['fields']) && $_POST['fields'] == "Y")) {
								$fields = $FieldsList -> fields_by_list($listid);
							}
						} elseif ($_POST['formtype'] == "html") {
							if (empty($_POST['html_fields']) || (!empty($_POST['html_fields']) && $_POST['html_fields'] == "Y")) {
								$fields = $FieldsList -> fields_by_list($listid);
							}
						}
						
						ob_start();
						switch ($_POST['formtype']) {
							case 'iframe'				:
								$this -> render('offsite-iframe', array('options' => $options, 'fields' => $fields), true, 'admin');
								break;
							case 'html'					:
								$this -> render('offsite-html', array('options' => $options, 'fields' => $fields), true, 'admin');
								break;
							case 'popup'				:
							default						:
								$this -> render('offsite-form', array('options' => $options, 'fields' => $fields), true, 'admin');
								break;
						}
							
						$code = ob_get_clean();
						
						$offsiteurl = home_url('?' . $this -> pre . 'method=offsite&list=' . $listid);
					}
					
					$this -> render_admin('offsite-wizard', array('code' => $code, 'offsiteurl' => $offsiteurl, 'listid' => $listid));
					break;
				default				:				
					$perpage = (isset($_COOKIE[$this -> pre . 'listsperpage'])) ? $_COOKIE[$this -> pre . 'listsperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$conditions = apply_filters($this -> pre . '_admin_mailinglists_conditions', $conditions);
					
					$data = array();
					if (!empty($_GET['showall'])) {
						$Db -> model = $Mailinglist -> model;
						$lists = $Db -> find_all(false, "*", $order);
						$data[$Mailinglist -> model] = $lists;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($Mailinglist -> model, null, $this -> sections -> lists, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_admin('mailinglists' . DS . 'index', array('mailinglists' => $data[$Mailinglist -> model], 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		function admin_groups() {
			global $wpdb, $Db, $wpmlGroup, $Mailinglist;
			$Db -> model = $wpmlGroup -> model;
			
			switch ($_GET['method']) {
				case 'save'						:
					$Db -> model = $wpmlGroup -> model;
					
					if (!empty($_POST)) {
						if ($Db -> save($_POST)) {
							$message = __('Group has been saved.', $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> groups . '&method=save&id=' . $wpmlGroup -> insertid . '&continueediting=1'), 'message', $message);
							} else {
								$this -> redirect($this -> url, 'message', $message);
							}
						} else {
							$this -> render_error(__('Group could not be saved.', $this -> plugin_name));
							$group = $this -> init_class($wpmlGroup -> model, $_POST);
							$this -> render_admin('groups' . DS . 'save', array('group' => $group, 'errors' => $wpmlGroup -> errors));
						}
					} else {
						if (!empty($_GET['id'])) {
							$group = $Db -> find(array('id' => $_GET['id']));
						}
						
						$this -> render_admin('groups' . DS . 'save', array('group' => $group), true, 'admin');
					}
					break;
				case 'view'						:
					if (!empty($_GET['id'])) {
						if ($group = $Db -> find(array('id' => $_GET['id']))) {
							$perpage = (!empty($_COOKIE[$this -> pre . 'listsperpage'])) ? $_COOKIE[$this -> pre . 'listsperpage'] : 15;
							$data = $Mailinglist -> get_all_paginated(array('group_id' => $_GET['id']), false, $this -> sections -> groups . '&amp;method=view&amp;id=' . $_GET['id'], $perpage);
							$this -> render_admin('groups' . DS . 'view', array('group' => $group, 'mailinglists' => $data['Mailinglist'], 'paginate' => $data['Pagination']));
						} else {
							$this -> render_error(__('Group could not be read', $this -> plugin_name));
						}
					} else {
						$this -> render_error(__('No group was specified', $this -> plugin_name));
					}
					break;
				case 'delete'					:
					if (!empty($_GET['id'])) {
						if ($Db -> delete($_GET['id'])) {
							$Db -> model = $Mailinglist -> model;
							$Db -> save_field('group_id', "0", array('group_id' => $_GET['id']));
							
							$msg_type = 'message';
							$message = __('Group has been removed', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Group cannot be removed', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No group was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'mass'						:
					if (!empty($_POST['groupslist'])) {
						if (!empty($_POST['action'])) {
							$groups = $_POST['groupslist'];
							
							switch ($_POST['action']) {
								case 'delete'			:
									
									foreach ($groups as $group_id) {
										$Db -> model = $wpmlGroup -> model;
										$Db -> delete($group_id);	
									}
									
									$msg_type = "message";
									$message = __('Selected groups have been removed.', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = "error";
							$message = __('No action was selected', $this -> plugin_name);
						}
					} else {
						$msg_type = "error";
						$message = __('No groups were selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default							:
					$perpage = (isset($_COOKIE[$this -> pre . 'groupsperpage'])) ? $_COOKIE[$this -> pre . 'groupsperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$data = array();
					if (!empty($_GET['showall'])) {
						$Db -> model = $wpmlGroup -> model;
						$groups = $Db -> find_all(false, "*", $order);
						$data[$wpmlGroup -> model] = $groups;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($wpmlGroup -> model, null, $this -> sections -> groups, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render('groups' . DS . 'index', array('groups' => $data[$wpmlGroup -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;	
			}
		}
		
		function admin_subscribers() {
			global $wpdb, $Html, $Db, $wpmlOrder, $Email, $Field, $Subscriber, $Unsubscribe, $Bounce, $SubscribersList, $Mailinglist;
			$Db -> model = $Subscriber -> model;
				
			switch ($_GET['method']) {
				case 'save'			:
					if (!empty($_POST)) {				
						if (!empty($_POST['Subscriber']['id'])) {
							$SubscribersList -> delete_all(array('subscriber_id' => $_POST['Subscriber']['id']));
						}
						
						$Db -> model = $Field -> model;
						$conditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
						if ($fields = $Db -> find_all($conditions)) {
							foreach ($fields as $field) {
								if (!empty($_POST[$field -> slug])) {
									if (is_array($_POST[$field -> slug])) {
										foreach ($_POST[$field -> slug] as $fkey => $fval) {
											$_POST['Subscriber'][$field -> slug][$fkey] = $fval;
										}
									} else {
										$_POST['Subscriber'][$field -> slug] = $_POST[$field -> slug];
									}
								}
							}
						}
							
						if ($Subscriber -> save($_POST)) {
							$message = __("Subscriber has been saved", $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=save&id=' . $Subscriber -> insertid . '&continueediting=1'), 'message', $message);
							} else {
								$this -> redirect($this -> url, 'message', $message);
							}
						} else {
							$Subscriber -> get($_POST['subscriber_id']);
							$this -> render_error(__('Subscriber could not be saved', $this -> plugin_name));
							$this -> render_admin('subscribers' . DS . 'save', array('subscriber' => $this -> init_class('wpmlSubscriber', $this -> Subscriber -> data), 'errors' => $this -> Subscriber -> error));
						}
					} else {
						$Subscriber -> get($_GET['id']);
						if (!empty($_GET['mailinglist_id'])) { $Subscriber -> data -> mailinglists[] = $_GET['mailinglist_id']; }
						$this -> render_admin('subscribers' . DS . 'save');
					}
					break;
				case 'view'			:
					if (!empty($_GET['id'])) {
						if ($subscriber = $Subscriber -> get($_GET['id'])) {
							$Db -> model = $wpmlOrder -> model;
							$orders = $Db -> find_all(array('subscriber_id' => $subscriber -> id));
							$conditions['subscriber_id'] = $subscriber -> id;
							$order = array($wpdb -> prefix . $Email -> table . ".modified", "DESC");
							$data = $this -> paginate($Email -> model, false, $this -> sections -> subscribers . '&method=view&id=' . $subscriber -> id, $conditions, false, 15, $order);							
							$this -> render_admin('subscribers' . DS . 'view', array('subscriber' => $subscriber, 'orders' => $orders, 'emails' => $data[$Email -> model], 'paginate' => $data['Paginate']));
						} else {						
							$message = __('Subscriber cannot be read', $this -> plugin_name);
							$this -> redirect($this -> url, 'error', $message);
						}
					} else {
						$message = __('No subscriber was specified', $this -> plugin_name);
						$this -> redirect($this -> url, 'error', $message);
					}
					break;
				case 'delete'		:
					if (!empty($_GET['id'])) {
						$Db -> model = $Subscriber -> model;
					
						if ($Db -> delete($_GET['id'])) {
							$message_type = 'message';
							$message = __('Subscriber has been removed', $this -> plugin_name);
						} else {
							$message_type = 'error';
							$message = __('Subscriber cannot be removed', $this -> plugin_name);
						}
					} else {
						$message_type = 'error';
						$message = __('No subscriber was specified', $this -> plugin_name);
					}
					
					$this -> redirect('?page=' . $this -> sections -> subscribers, $message_type, $message);
					break;
				case 'mass'			:					
					if (!empty($_POST['subscriberslist'])) {
						if (!empty($_POST['action'])) {
							$subscribers = $_POST['subscriberslist'];
							
							switch ($_POST['action']) {
								case 'assignlists'		:
									if (!empty($_POST['lists'])) {
										foreach ($subscribers as $subscriber_id) {
											foreach ($_POST['lists'] as $list_id) {
												$Db -> model = $Mailinglist -> model;
												
												if ($mailinglist = $Db -> find(array('id' => $list_id))) {
													$sl = array('subscriber_id' => $subscriber_id, 'list_id' => $list_id, 'active' => "Y");
													
													if ($mailinglist -> paid == "Y") {
														$sl['paid'] = "Y";
													}
													
													$SubscribersList -> save($sl, true);
												}
											}
										}
										
										$msg_type = 'message';
										$message = __('Selected mailing lists have been appended to the subscribers', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('No mailing lists were selected', $this -> plugin_name);
									}
									break;
								case 'setlists'			:
									if (!empty($_POST['lists'])) {
										foreach ($subscribers as $subscriber_id) {
											$SubscribersList -> delete_all(array('subscriber_id' => $subscriber_id));
											
											foreach ($_POST['lists'] as $list_id) {
												$Db -> model = $Mailinglist -> model;
												
												if ($mailinglist = $Db -> find(array('id' => $list_id))) {
													$sl = array('subscriber_id' => $subscriber_id, 'list_id' => $list_id, 'active' => "Y");
													
													if ($mailinglist -> paid == "Y") {
														$sl['paid'] = "Y";
													}
													
													$SubscribersList -> save($sl, true);
												}
											}
										}
										
										$msg_type = 'message';
										$message = __('Selected mailing lists have been assigned to the subscribers', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('No mailing lists were selected', $this -> plugin_name);
									}
									break;
								case 'delete'			:							
									foreach ($subscribers as $subscriber_id) {
										$Db -> model = $Subscriber -> model;
										$Db -> delete($subscriber_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected subscribers have been removed', $this -> plugin_name);
									break;
								case 'mandatory'		:
								case 'notmandatory'		:
									$mandatory = ($_POST['action'] == "mandatory") ? "Y" : "N";
									foreach ($subscribers as $subscriber_id) {
										$Db -> model = $Subscriber -> model;
										$Db -> save_field('mandatory', $mandatory, array('id' => $subscriber_id));
									}
									
									$msg_type = 'message';
									$message = __('Mandatory status has been changed', $this -> plugin_name);
									break;
								case 'active'			:
									if (!empty($subscribers)) {
										foreach ($subscribers as $subscriber_id) {
											$Db -> model = $SubscribersList -> model;
											$Db -> save_field('active', "Y", array('subscriber_id' => $subscriber_id));
										}
									}
									
									$msg_type = 'message';
									$message = __('Selected subscribers set as active', $this -> plugin_name);
									break;
								case 'inactive'			:
									foreach ($subscribers as $subscriber_id) {
										$Db -> model = $SubscribersList -> model;
										$Db -> save_field('active', "N", array('subscriber_id' => $subscriber_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected subscribers deactivated', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No action was selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No subscribers were selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
	            case 'check-bounced'    :
	                $bounce_results = $this -> bounce(false, "pop");
	                $bounce_message = "";
	                $bounce_message .= $bounce_results[0] . " ";
	                $bounce_message .= __('subscribers', $this -> plugin_name) . ' ';
	                $bounce_message .= __('and', $this -> plugin_name) . ' ';
	                $bounce_message .= $bounce_results[1] . " ";
	                $bounce_message .= __('bounced emails were removed.', $this -> plugin_name);
	                $this -> redirect("?page=" . $this -> sections -> subscribers, 'message', $bounce_message);
	                break;
	            case 'check-expired'	:
	            	global $SubscribersList;
	            	$updated = $SubscribersList -> check_expirations();
	            	$message = sprintf(__('%s subscriptions have been deactivated due to expiration or max emails sent.', $this -> plugin_name), $updated);
	            	$this -> redirect("?page=" . $this -> sections -> subscribers, 'message', $message);
	            	break;
	            case 'unsubscribes'				:
	            	$unsubscribes_table = $wpdb -> prefix . $Unsubscribe -> table;
	            	$sections = $this -> sections -> subscribers . '&method=unsubscribes';
	            	$conditions = false;
	            	$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
	            	$perpage = (isset($_COOKIE[$this -> pre . 'unsubscribesperpage'])) ? $_COOKIE[$this -> pre . 'unsubscribesperpage'] : 15;
	            	
	            	$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$conditions = (!empty($searchterm)) ? array($unsubscribes_table . '.email' => "LIKE '%" . $searchterm . "%'") : false;
					
					if (!empty($_GET['history_id'])) {
						$conditions[$unsubscribes_table . '.history_id'] = $_GET['history_id'];
					}
	            	
	            	$conditions_and = false;
	            	
	            	if (!empty($_GET['showall'])) {
	            		$Db -> model = $Unsubscribe -> model;
						$unsubscribes = $Db -> find_all(false, "*", $order);
						$data[$Unsubscribe -> model] = $unsubscribes;
						$data['Paginate'] = false;
	            	} else {
		            	$data = $this -> paginate($Unsubscribe -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
						$unsubscribes = $data[$Unsubscribe -> model];
					}
	            	
	            	$this -> render('subscribers' . DS . 'unsubscribes', array('unsubscribes' => $unsubscribes, 'paginate' => $data['Paginate']), true, 'admin');
	            	break;
	            case 'unsubscribedelete'		:
	            	if (!empty($_GET['id'])) {
		            	$Db -> model = $Unsubscribe -> model;
		            	
		            	if ($Db -> delete($_GET['id'])) {
		            		$msgtype = 'message';
		            		$message = __('Unsubscribe has been deleted', $this -> plugin_name);
		            	} else {
			            	$msgtype = 'error';
			            	$message = __('Unsubscribe could not be deleted', $this -> plugin_name);
		            	}
	            	} else {
		            	$msgtype = 'error';
		            	$message = __('No unsubscribe was specified', $this -> plugin_name);
	            	}
	            	
	            	$this -> redirect($this -> referer, $msgtype, $message);
	            	break;
	            case 'unsubscribemass'			:
	            	if (!empty($_POST['action'])) {
	            		$action = $_POST['action'];
	            		$unsubscribes = $_POST['unsubscribes'];
	            		
	            		if (!empty($unsubscribes)) {
		            		switch ($action) {
			            		case 'delete'				:
			            			foreach ($unsubscribes as $unsubscribe_id) {
				            			$Db -> model = $Unsubscribe -> model;
				            			$Db -> delete($unsubscribe_id);
			            			}
			            			
			            			$msgtype = 'message';
			            			$message = __('Selected unsubscribes deleted', $this -> plugin_name);
			            			break;
			            		case 'deletesubscribers'	:
			            			foreach ($unsubscribes as $unsubscribe_id) {
				            			$Db -> model = $Unsubscribe -> model;
				            			$subscriber_id = $Db -> field('subscriber_id', array('id' => $unsubscribe_id));
				            			
				            			if (!empty($subscriber_id)) {
					            			$Db -> model = $Subscriber -> model;
					            			$Db -> delete($subscriber_id);
				            			}
			            			}
			            			
			            			$msgtype = 'message';
			            			$message = __('Subscribers of the selected unsubscribes have been deleted', $this -> plugin_name);
			            			break;
			            		case 'deleteusers'			:
			            			foreach ($unsubscribes as $unsubscribe_id) {
				            			$Db -> model = $Unsubscribe -> model;
				            			$user_id = $Db -> field('user_id', array('id' => $unsubscribe_id));
				            			
				            			if (!empty($user_id)) {
					            			wp_delete_user($user_id);
				            			}
			            			}
			            			
			            			$msgtype = 'message';
			            			$message = __('Users of the selected unsubscribes have been deleted', $this -> plugin_name);
			            			break;
		            		}
	            		} else {
		            		$msgtype = 'error';
		            		$message = __('No unsubscribes were selected', $this -> plugin_name);
	            		}
	            	} else {
		            	$msgtype = 'error';
		            	$message = __('No action was specified', $this -> plugin_name);
	            	}
	            	
	            	$this -> redirect($this -> referer, $msgtype, $message);
	            	break;
	            case 'deleteuser'				:
	            	if (!empty($_GET['user_id'])) {
	            		if (wp_delete_user($_GET['user_id'])) {
		            		$msgtype = 'message';
		            		$message = __('User has been deleted', $this -> plugin_name);
	            		} else {
		            		$msgtype = 'error';
		            		$message = __('User could not be deleted', $this -> plugin_name);
	            		}
	            	} else {
		            	$msgtype = 'error';
		            	$message = __('No user was specified', $this -> plugin_name);
	            	}
	            	
	            	$this -> redirect($this -> referer, $msgtype, $message);
	            	break;
	            case 'bounces'					:
	            
	            	break;
				default			:
					$oldperpage = 15;
				
					// screen options changes?
					if (!empty($_POST['screenoptions'])) {
						if (!empty($_POST['fields']) && is_array($_POST['fields'])) {
							$this -> update_option('screenoptions_subscribers_fields', $_POST['fields']);	
						} else { delete_option($this -> pre . 'screenoptions_subscribers_fields'); }
						
						if (!empty($_POST['custom']) && is_array($_POST['custom'])) {
							$this -> update_option('screenoptions_subscribers_custom', $_POST['custom']);
						} else { delete_option($this -> pre . 'screenoptions_subscribers_custom'); }
						
						if (!empty($_POST['perpage'])) {
							$oldperpage = $_POST['perpage'];
						}
					}
				
					$perpage = (isset($_COOKIE[$this -> pre . 'subscribersperpage'])) ? $_COOKIE[$this -> pre . 'subscribersperpage'] : $oldperpage;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$searchurl = $Html -> retainquery($this -> pre . 'page=1&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
						$this -> redirect($searchurl);
					} elseif (isset($_POST['searchterm'])) {
						$this -> redirect($Html -> retainquery($this -> pre . 'page=1&' . $this -> pre . 'searchterm='));
					}
					
					$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
					$subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
					
					$conditions = (!empty($searchterm)) ? array($subscribers_table . '.email' => "LIKE '%" . $searchterm . "%'") : false;
					
					if (!empty($searchterm)) {
						$Db -> model = $Field -> model;
						$fieldsconditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
						if ($fields = $Db -> find_all($fieldsconditions)) {
							if (empty($conditions) || !is_array($conditions)) { $conditions = array(); }						
							foreach ($fields as $field) {
								$conditions[$subscribers_table . "." . $field -> slug] = "LIKE '%" . $searchterm . "%'";
							}
						}
					}
					
					$dojoin = false;
					$sections = $this -> sections -> subscribers;
					$conditions_and = array();
					
					$newsletters_filter_subscribers = (!empty($_GET['filter']) || (!empty($_COOKIE['newsletters_filter_subscribers']))) ? true : false;
					
					if (!empty($newsletters_filter_subscribers)) {
						$sections .= '&filter=1';
						
						//** list filter
						$newsletters_filter_subscribers_list = (!empty($_GET['list'])) ? $_GET['list'] : false;
						$newsletters_filter_subscribers_list = (!empty($_COOKIE['newsletters_filter_subscribers_list'])) ? $_COOKIE['newsletters_filter_subscribers_list'] : $newsletters_filter_subscribers_list;
					
						if (!empty($newsletters_filter_subscribers_list)) {
							switch ($newsletters_filter_subscribers_list) {
								case 'all'				:
									$dojoin = false;
									break;
								case 'none'				:
									$dojoin = false;
									$conditions_and[$subscribers_table . '.id'] = "NOT IN (SELECT subscriber_id FROM " . $subscriberslists_table . ")";
									break;
								default					:
									$dojoin = true;
									$conditions_and[$subscriberslists_table . '.list_id'] = $newsletters_filter_subscribers_list;	
									break;
							}
							
							$sections .= '&list=' . $newsletters_filter_subscribers_list;
						}
						
						//** status filter (active/inactive)
						
						$newsletters_filter_subscribers_status = (!empty($_COOKIE['newsletters_filter_subscribers_status'])) ? $_COOKIE['newsletters_filter_subscribers_status'] : false;
						$newsletters_filter_subscribers_status = (!empty($_GET['status'])) ? $_GET['status'] : $newsletters_filter_subscribers_status;
						
						if (!empty($newsletters_filter_subscribers_status)) {
							if ($newsletters_filter_subscribers_status != "all") {
								$status = ($newsletters_filter_subscribers_status == "active") ? "Y" : "N";
								$conditions_and[$subscriberslists_table . '.active'] = $status;
								$dojoin = true;
							}
							
							$sections .= '&status=' . $newsletters_filter_subscribers_status;
						}
						
						//** registered filter
							
						$newsletters_filter_subscribers_registered = (empty($_GET['registered'])) ? $_COOKIE['newsletters_filter_subscribers_registered'] : $_GET['registered'];
						
						if (!empty($newsletters_filter_subscribers_registered) && $newsletters_filter_subscribers_registered != "all") {
							$conditions_and[$subscribers_table . '.registered'] = $newsletters_filter_subscribers_registered;
							
							$sections .= '&registered=' . $newsletters_filter_subscribers_registered;
						}
					}
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$data = array();
					$Subscriber -> recursive = true;
					
					if (!empty($_GET['showall'])) {
						$Db -> model = $Subscriber -> model;
						$subscribers = $Db -> find_all(false, "*", $order);
						$data[$Subscriber -> model] = $subscribers;
						$data['Paginate'] = false;
					} else {
						if ($dojoin) {							
							$data = $this -> paginate($SubscribersList -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$subscribers = $data[$SubscribersList -> model];
						} else {
							$data = $this -> paginate($Subscriber -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$subscribers = $data[$Subscriber -> model];
						}
					}
					
					$this -> render_admin('subscribers' . DS . 'index', array('subscribers' => $subscribers, 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		function admin_importexport() {
			global $wpdb, $Db, $Html, $Queue, $Field, $Subscriber, $Unsubscribe, $Bounce, $SubscribersList, $Mailinglist;
		
			if (!empty($_POST)) {
				$this -> remove_server_limits();
				
				switch ($_GET['method']) {
					case 'import'			:												
						if (empty($_FILES['file']['name'])) { $error['file'] = __('No file selected for uploading', $this -> plugin_name); }
						elseif (!is_uploaded_file($_FILES['file']['tmp_name'])) { $error['file'] = __('File could not be uploaded', $this -> plugin_name); }
						if (empty($_POST['importlists'])) { $error['mailinglists'] = __('No mailing list selected', $this -> plugin_name); }
						if (empty($_POST['filetype'])) { $error['filetype'] = __('No file type has been selected', $this -> plugin_name); }
						
						if (empty($error)) {
							$numberimported = 0;
							$datasets = array();
							
							if ($_POST['filetype'] == "mac") {
								$structure = array('email' => 4);
								
								if (!empty($_POST['macfields']['fname'])) { $structure[$_POST['macfields']['fname']] = 0; }
								if (!empty($_POST['macfields']['lname'])) { $structure[$_POST['macfields']['lname']] = 1; }
								if (!empty($_POST['macfields']['phone'])) { $structure[$_POST['macfields']['phone']] = 6; }
															
								include_once($this -> plugin_base . DS . 'vendors' . DS . 'class.vcard.php');
								$conv = new vcard_convert();
								$conv -> fromFile($_FILES['file']['tmp_name']);
								$data = $conv -> toCSV(',', false, false, null);
								
								$fileName = time() . '.csv';
								$filePath = $Html -> uploads_path() . '/';
								$fileFull = $filePath . $fileName;
								$fh = fopen($fileFull, "w");
								
								if ($fh) {
									fwrite($fh, $data);
									fclose($fh);
								} else {
									$error[] = sprintf(__('Csv file could not be created! Check "%s" permissions!', $this -> plugin_name), $filePath);
								}
							} elseif ($_POST['filetype'] == "csv") {
								$fileFull = $_FILES['file']['tmp_name'];
							
								foreach ($_POST['fields'] as $key => $val) {
									$structure[$key] = (!empty($_POST['fields'][$key]) && $_POST['fields'][$key] == "Y") ? ($_POST[$key . 'column'] - 1) : false;
								}
							}
							
							if ($fh = fopen($fileFull, "r")) {
								$delimiter = (empty($_POST['delimiter'])) ? "," : $_POST['delimiter'];
								$d = 0;
								$i_queries = array();
								$import_progress = (empty($_POST['import_progress']) || $_POST['import_progress'] == "N") ? false : true;
								$import_preventbu = (empty($_POST['import_preventbu'])) ? false : true;
								
								$afterlists = array();							
								if (!empty($_POST['importlists'])) {								
									foreach ($_POST['importlists'] as $importlist_id) {
										$query = "SELECT `id`, `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $importlist_id . "'";
										
										$query_hash = md5($query);
										if ($ob_mailinglist = $this -> get_cache($query_hash)) {
											$mailinglist = $ob_mailinglist;
										} else {
											$mailinglist = $wpdb -> get_row($query);
											$this -> set_cache($query_hash, $mailinglist);
										}
									
										if (!empty($mailinglist)) {
											$paid = (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? "N" : "Y";
											$afterlists[] = array('id' => $importlist_id, 'paid' => $paid, 'active' => "Y");
										}
									}
								}
								
								while (($row = fgetcsv($fh, "1000", $delimiter)) !== false) {
									$this -> remove_server_limits();
								
									if (!empty($row)) {
										$addlists = array();	//additional lists specified in the CSV
										$thisafterlists = $afterlists;
										$mailinglists = $_POST['importlists'];
										$email = $row[$structure['email']];
										
										if ($_POST['filetype'] == "mac") {
											foreach ($structure as $skey => $sval) {
												if ($skey != "email") {
													$_POST['fields'][$skey] = $sval;
													$_POST[$skey . 'column'] = ($sval + 1);
												}
											}
										}
										
										$Db -> model = $Unsubscribe -> model;
										if ($import_progress == true || empty($import_preventbu) || ($import_preventbu == true && !$Db -> find(array('email' => $email)))) {
											$Db -> model = $Bounce -> model;
											if ($import_progress == true || empty($import_preventbu) || ($import_preventbu == true && !$Db -> find(array('email' => $email)))) {
												if (!empty($email) && $Subscriber -> email_validate($email)) {
													if ($user = $Subscriber -> get_user_by_email($email)) {
														$registered = "Y";
														$user_id = $user -> ID;
													} else {
														$registered = "N";
														$user_id = 0;
													}
													
													$current_id = $Subscriber -> email_exists($email);
													
													/* mailing lists column */
													if (!empty($_POST['fields']['mailinglists']) && 
														$_POST['fields']['mailinglists'] == "Y" && 
														!empty($_POST['mailinglistscolumn'])) {
														
														$caddlists = $row[($_POST['mailinglistscolumn'] - 1)];
														if (($addlistsarr = explode(",", $caddlists)) !== false) {																					
															foreach ($addlistsarr as $addlisttitle) {
																$newaddlisttitle = trim($addlisttitle);
																$addlists[] = $newaddlisttitle;
																
																$checkquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `title` = '" . $newaddlisttitle . "'";
																
																if ($mailinglist_id = $wpdb -> get_var($checkquery)) {
																	//do nothing, this list exists	
																} else {
																	//we'll create the mailinglist
																	if (!empty($_POST['autocreatemailinglists']) && $_POST['autocreatemailinglists'] == "Y") {
																		$mailinglistdata = array(
																			'title'					=>	$newaddlisttitle,
																			'privatelist'			=>	"N",
																			'group_id'				=>	0,
																			'paid'					=>	"N",
																		);
																		
																		if ($Mailinglist -> save($mailinglistdata, true)) {
																			$mailinglist_id = $Mailinglist -> insertid;
																		}
																	}
																}
																
																if (!empty($mailinglist_id)) {
																	if (empty($mailinglists) || (!empty($mailinglists) && !in_array($mailinglist_id, $mailinglists))) {
																		$mailinglists[] = $mailinglist_id;
																		
																		$query = "SELECT `id`, `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "'";
																		
																		$query_hash = md5($query);
																		if ($ob_mailinglist = $this -> get_cache($query_hash)) {
																			$mailinglist = $ob_mailinglist;
																		} else {
																			$mailinglist = $wpdb -> get_row($query);
																			$this -> set_cache($query_hash, $mailinglist);
																		}
																		
																		if (!empty($mailinglist)) {
																			$paid = (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? "N" : "Y";
																			$thisafterlists[] = array('id' => $mailinglist_id, 'paid' => $paid, 'active' => "Y");
																		}
																	}
																}
															}
														}
													}
													
													$datasets[$d] = array(
														'id'					=>	((empty($current_id)) ? false : $current_id),
														'email'					=>	$email,
														'active'				=>	((empty($_POST['activation']) || (!empty($_POST['activation']) && $_POST['activation'] == "Y")) ? "N" : "Y"),
														'registered'			=>	$registered,
														'user_id'				=>	$user_id,
														'mailinglists'			=>	$mailinglists,
														'afterlists'			=>	$thisafterlists,
													);
													
													if (!empty($_POST['fields'])) {										
														foreach ($_POST['fields'] as $field => $value) {
															if (empty($datasets[$d][$field])) {
																$datasets[$d][$field] = ($row[($_POST[$field . 'column'] - 1)]);
															}
														}
													}
													
													if (!empty($_POST['activation']) && $_POST['activation'] == "Y") {
														$confirmation_subject = __(stripslashes((empty($_POST['confirmation_subject'])) ? $this -> get_option('etsubject_confirm') : $_POST['confirmation_subject']));
														$confirmation_email = __(stripslashes((empty($_POST['confirmation_email'])) ? $this -> get_option('etmessage_confirm') : $_POST['confirmation_email']));
													}
													
													if (empty($import_progress) || $import_progress == false) {
														$datasets[$d]['justsubscribe'] = true;
														$datasets[$d]['fromregistration'] = true;
														$datasets[$d]['username'] = $email;
														
														if ($Subscriber -> save($datasets[$d], false, false)) {
															$Db -> model = $Subscriber -> model;
															$subscriber = $Db -> find(array('id' => $Subscriber -> insertid));
														
															if (!empty($_POST['activation']) && $_POST['activation'] == "Y") {
																foreach ($datasets[$d]['mailinglists'] as $list_id) {
																	$subscriber -> mailinglist_id = $list_id;
																	
																	$Queue -> save(
																		$subscriber, 
																		false,
																		$confirmation_subject, 
																		$confirmation_email, 
																		false, 
																		false, 
																		false, 
																		false, 
																		$this -> default_theme_id('system'), 
																		false
																	);
																}
															}
														
															$numberimported++;	
														}
													}
													
													$d++;
												}		
											}
										}
									}
								}
								
								if (empty($import_progress) || $import_progress == false) {									
									if (!empty($numberimported)) {
										$this -> render_message($numberimported . ' ' . __('subscribers successfully imported', $this -> plugin_name));
									} else {
										$this -> render_message(__('No subscribers were imported', $this -> plugin_name));
									}
										
									$this -> render_admin('import-export', array('mailinglists' => $mailinglists));
								} else {
									$this -> render('import-post', array('subscribers' => $datasets, 'confirmation_subject' => $confirmation_subject, 'confirmation_email' => $confirmation_email), true, 'admin');
								}
							} else {
								/* CSV could not be read */
								
							}
						} else {
							$this -> render_error(__('Subscribers could not be imported', $this -> plugin_name));
							$this -> render_admin('import-export', array('mailinglists' => $mailinglists, 'importerrors' => $error));
						}
						
						break;		
					case 'export'				:
						global $wpdb, $Html, $Subscriber, $SubscribersList, $Field, $wpmlCountry;
						$errors = false;
						
						if (empty($_POST['export_lists']) || !is_array($_POST['export_lists'])) { $errors[] = __('Please select export list(s)', $this -> plugin_name); }
						if (empty($_POST['export_filetype'])) { $errors[] = __('Please select an export filetype', $this -> plugin_name); }
						
						$exportfilename = 'subscribers-' . date_i18n("Ymd", time()) . '.csv';
						$exportfilepath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
						$exportfilefull = $exportfilepath . $exportfilename;
						if (!$fh = fopen($exportfilefull, "w")) { $errors[] = sprintf(__('Export file could not be created, please check permissions on <b>%s</b> to make sure it is writable.', $this -> plugin_name), $Html -> uploads_path() . "/" . $this -> plugin_name . "/export/"); }
						else { fclose($fh); }
						
						@chmod($exportfilefull, 0777);
						
						if (empty($errors)) {
							$query = "";
							$query .= "SELECT *, COUNT(" . $wpdb -> prefix . $Subscriber -> table . ".email) FROM `" . $wpdb -> prefix . $Subscriber -> table . "`";
							$query .= " LEFT JOIN `" . $wpdb -> prefix . $SubscribersList -> table . "` ON";
							$query .= " " . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id";
							
							if (!empty($_POST['export_lists'])) {
								$query .= " WHERE (";
								$e = 1;
								
								foreach ($_POST['export_lists'] as $list_id) {
									$query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $list_id . "'";
									if ($e < count($_POST['export_lists'])) { $query .= " OR "; }
									$e++;
								}
								
								$query .= ")";
							}
							
							if (!empty($_POST['export_status']) && $_POST['export_status'] != "all") {
								$active = ($_POST['export_status'] == "active") ? "Y" : "N";
								$query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = '" . $active . "'";
							}
							
							$query .= " GROUP BY " . $wpdb -> prefix . $Subscriber -> table . ".email"; 
							$subscribers = $wpdb -> get_results($query);
							
							$datasets = array();
							if (!empty($subscribers)) {
								$d = 0;
								
								$fieldsquery = "SELECT * FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
								
								$query_hash = md5($fieldsquery);
								if ($ob_fields = $this -> get_cache($query_hash)) {
									$fields = $ob_fields;
								} else {
									$fields = $wpdb -> get_results($fieldsquery);
									$this -> set_cache($query_hash, $fields);
								}
								
								$delimiter = (!empty($_POST['export_delimiter'])) ? $_POST['export_delimiter'] : ",";
								
								$headings = array();
								$headings[] = __('ID', $this -> plugin_name);
								$headings[] = __('Email Address', $this -> plugin_name);
								
								if (!empty($fields)) {
									foreach ($fields as $field) {
										$headings[] = __($field -> title);
									}
								}
								
								$headings[] = __('IP Address', $this -> plugin_name);
								$headings[] = __('Created', $this -> plugin_name); 
								$headings[] = __('Modified', $this -> plugin_name);
								
								$data = "";
								$data .= '"' . implode('"' . $delimiter . '"', $headings) . '"' . "\r\n";
							
								foreach ($subscribers as $subscriber) {
									$datasets[$d] = array(
										'id'					=>	$subscriber -> id,
										'email'					=>	$subscriber -> email,
									);
									
									if (!empty($fields)) {
										foreach ($fields as $field) {
											if (!empty($field -> fieldoptions)) {
												$fieldoptions_unserialize = unserialize($field -> fieldoptions);
												if (!empty($fieldoptions_unserialize) && is_array($fieldoptions_unserialize)) {
													$fieldoptions = array_map('__', $fieldoptions_unserialize);	
												}
											}
											
											switch ($_POST['export_purpose']) {
												case 'other'				:											
													switch ($field -> type) {
														case 'select'				:
														case 'radio'				:
															$datasets[$d][$field -> slug] = $fieldoptions[$subscriber -> {$field -> slug}];
															break;
														case 'checkbox'				:
															$checkboxes = array();
															$supoptions = maybe_unserialize($subscriber -> {$field -> slug});
															if (!empty($supoptions) && is_array($supoptions)) {
																foreach ($supoptions as $subopt) {
																	$checkboxes[] = $fieldoptions[$subopt];
																}
															}
														
															$datasets[$d][$field -> slug] = (!empty($checkboxes) && is_array($checkboxes)) ? implode(",", $checkboxes) : '';
															break;
														case 'pre_country'			:
															$query = "SELECT `value` FROM " . $wpdb -> prefix . $wpmlCountry -> table . " WHERE `id` = '" . $subscriber -> {$field -> slug} . "'";
															$country = $wpdb -> get_var($query);
															
															$datasets[$d][$field -> slug] = (!empty($country)) ? $country : '';
															break;
														case 'pre_date'				:
															//$date = maybe_unserialize($subscriber -> {$field -> slug});
															//$datasets[$d][$field -> slug] = (!empty($date) && is_array($date) && (!empty($date['y']) || !empty($date['m']) || !empty($date['d']))) ? $date['y'] . '-' . $date['m'] . '-' . $date['d'] : '';
															
															if (is_serialized($subscriber -> {$field -> slug})) {
																$date = maybe_unserialize($subscriber -> {$field -> slug});
																echo $date['y'] . '-' . $date['m'] . '-' . $date['d'];
															} else {
																echo date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug}));
															}
															break;
														case 'pre_gender'			:
															$datasets[$d][$field -> slug] = $Html -> gender($subscriber -> {$field -> slug});
															break;
														default						:
															$datasets[$d][$field -> slug] = $subscriber -> {$field -> slug};
															break;
													}
													break;
												case 'newsletters'			:
												default						:
													switch ($field -> type) {
														case 'select'				:
														case 'radio'				:
															$datasets[$d][$field -> slug] = $fieldoptions[$subscriber -> {$field -> slug}];
															break;
														default						:
															$datasets[$d][$field -> slug] = $subscriber -> {$field -> slug};
															break;
													}
													break;
											}
										}
									}
									
									$datasets[$d]['ip_address'] = $subscriber -> ip_address;
									$datasets[$d]['created'] = $subscriber -> created;
									$datasets[$d]['modified'] = $subscriber -> modified;
									
									$data .= '"' . implode('"' . $delimiter . '"', $datasets[$d]) . '"' . "\r\n";
									
									$d++;
								}
							}
							
							if (!empty($_POST['export_progress']) && $_POST['export_progress'] == "Y") {
								$this -> render('export-post', array('subscribers' => $datasets, 'headings' => $headings, 'exportfile' => $exportfilename), true, 'admin');
							} else {
								$fh = fopen($exportfilefull, "w");
								fwrite($fh, $data);
								fclose($fh);
								@chmod($exportfilefull, 0777);
								
								//$message = $d . ' ' . sprintf(__('subscribers have been exported. %s', $this -> plugin_name), '<a href="' . $Html -> uploads_url() . '/' . $this -> plugin_name . '/export/' . $exportfilename . '">' . __('Download CSV', $this -> plugin_name) . '</a>');
								//$this -> render_message($message);
								$this -> render('import-export', array('exportfile' => $exportfilename), true, 'admin');
							}
						} else {
							$this -> render('import-export', array('exporterrors' => $errors), true, 'admin');
						}
						break;
				}
			} else {
				$this -> render_admin('import-export', array('mailinglists' => $mailinglists));
			}
		}
		
		function admin_themes() {
			global $wpdb, $Db, $Theme;
			$Db -> model = $Theme -> model;
			$method = $_GET['method'];
			
			if ($this -> is_php_module('mod_security')) {
				$error = __('Please note that Apache mod_security is turned on. Saving a template may not be allowed due to the raw HTML. Please ask your hosting provider.', $this -> plugin_name);
				$this -> render_error($error);	
			}
			
			switch ($method) {
				case 'save'			:
					if (!empty($_POST)) {
						if ($Db -> save($_POST)) {
							$message = __('Template has been saved', $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> themes . '&method=save&id=' . $Theme -> insertid . '&continueediting=1'), 'message', $message);	
							} else {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> themes, 'message', $message));
							}
						} else {
							$this -> render_error(__('Template could not be saved', $this -> plugin_name));
							$this -> render('themes' . DS . 'save', false, true, 'admin');
						}
					} else {
						$Db -> find(array('id' => $_GET['id']));
						$Theme -> data -> paste = $Theme -> data -> content;
						$this -> render_admin('themes' . DS . 'save');
					}
					break;
				case 'delete'		:
					if (!empty($_GET['id'])) {
						if ($Db -> delete($_GET['id'])) {
							$msgtype = 'message';
							$message = __('Template has been removed', $this -> plugin_name);
						} else {
							$msgtype = 'error';
							$message = __('Template could not be removed', $this -> plugin_name);
						}
					} else {
						$msgtype = 'error';
						$message = __('No template was specified', $this -> plugin_name);
					}
					
					$this -> redirect('?page=' . $this -> sections -> themes, $msgtype, $message);
					break;
				case 'remove_default'					:
					if (!empty($_GET['id'])) {
						$Db -> model = $Theme -> model;
						$Db -> save_field('def', "N", array('id' => $_GET['id']));
						
						$msg_type = 'message';
						$message = __('Selected template removed as sending default', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No template was specified', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
					break;
				case 'remove_defaultsystem'				:
					if (!empty($_GET['id'])) {
						$Db -> model = $Theme -> model;
						$Db -> save_field('defsystem', "N", array('id' => $_GET['id']));
						
						$msg_type = 'message';
						$message = __('Selected template removed as system default', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No template was specified', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
					break;
				case 'default'		:
					if (!empty($_GET['id'])) {
						$Db -> model = $Theme -> model;
						$Db -> save_field('def', "N");
						
						$Db -> model = $Theme -> model;
						$Db -> save_field('def', "Y", array('id' => $_GET['id']));
						
						$msg_type = 'message';
						$message = __('Selected template has been set as the sending default', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No template was specified', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
					break;
				case 'defaultsystem'	:
					if (!empty($_GET['id'])) {
						$Db -> model = $Theme -> model;
						$Db -> save_field('defsystem', "N");
						
						$Db -> model = $Theme -> model;
						$Db -> save_field('defsystem', "Y", array('id' => $_GET['id']));
						
						$msg_type = 'message';
						$message = __('Selected template has been set as the system default', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No template was specified', $this -> plugin_name);
					}
					
					$this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
					break;
				case 'mass'			:
					if (!empty($_POST['action'])) {
						$themes = $_POST['themeslist'];
						
						if (!empty($themes)) {
							switch ($_POST['action']) {
								case 'delete'				:
									foreach ($themes as $theme_id) {
										$Db -> model = $Theme -> model;
										$Db -> delete($theme_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected templates have been removed', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No templates were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default				:
					$perpage = (empty($_COOKIE[$this -> pre . 'themesperpage'])) ? 15 : $_COOKIE[$this -> pre . 'themesperpage'];
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					if (!empty($_GET['showall'])) {
						$Db -> model = $Theme -> model;
						$themes = $Db -> find_all(false, "*", $order);
						$data[$Theme -> model] = $themes;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($Theme -> model, null, $this -> sections -> themes, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_admin('themes' . DS . 'index', array('themes' => $data[$Theme -> model], 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		function admin_templates() {
			global $wpdb, $Db, $Template;
			$Db -> model = $Template -> model;
			
			//get the page method
			$method = (empty($_GET['method'])) ? preg_replace("/newsletters\-templates\-/si", "", $_GET['page']) : $_GET['method'];
			
			switch ($method) {
				case 'save'			:
					$this -> render_message(__('Email Snippets are meant for content only, use the Themes for newsletter layouts.', $this -> plugin_name));
				
					if (!empty($_POST)) {
						if ($Template -> save($_POST)) {
							$message = __('Snippet has been saved', $this -> plugin_name);
							$this -> redirect('?page=' . $this -> sections -> templates, 'message', $message);
						} else {
							$this -> render_error(__('Snippet could not be saved', $this -> plugin_name));
							$this -> render('templates' . DS . 'save', false, true, 'admin');
						}
					} else {
						$Template -> get($_GET['id']);
						$_POST['content'] = $Template -> data[$Template -> model] -> content;
						$this -> render_admin('templates' . DS . 'save');
					}
					break;
				case 'delete'					:
					if (!empty($_GET['id'])) {
						if ($Template -> delete($_GET['id'])) {
							$message = __('Snippet has been removed', $this -> plugin_name);
							$this -> redirect($this -> url, 'message', $message);
						}
					} else {
						$message = __('No snippet was specified', $this -> plugin_name);
						$this -> redirect($this -> url, 'error', $message);
					}
					break;
				case 'view'						:
					if (!empty($_GET['id'])) {
						if ($template = $Db -> find(array('id' => $_GET['id']))) {
							$this -> render_admin('templates' . DS . 'view', array('template' => $template));
						} else {
							$message = __('Snippet cannot be read', $this -> plugin_name);
							$this -> redirect($this -> url, 'error', $message);
						}
					} else {
						$message = __('No snippet was specified', $this -> plugin_name);
						$this -> redirect($this -> url, 'error', $message);
					}
					break;
				case 'mass'						:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['templateslist'])) {
							if ($Template -> delete_array($_POST['templateslist'])) {
								$msg_type = 'message';
								$message = __('Selected snippets removed', $this -> plugin_name);
							} else {
								$msg_type = 'error';
								$message = __('Snippets could not be removed', $this -> plugin_name);
							}
						} else {
							$msg_type = 'error';
							$message = __('No snippets were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default 						:
					$perpage = (empty($_COOKIE[$this -> pre . 'templatesperpage'])) ? 15 : $_COOKIE[$this -> pre . 'templatesperpage'];
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
					
					/*$ofield = (isset($_COOKIE[$this -> pre . 'templatessorting'])) ? $_COOKIE[$this -> pre . 'templatessorting'] : "modified";
					$odir = (isset($_COOKIE[$this -> pre . 'templates' . $ofield . 'dir'])) ? $_COOKIE[$this -> pre . 'templates' . $ofield . 'dir'] : "DESC";
					$order = array($ofield, $odir);*/
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					if (!empty($_GET['showall'])) {
						$Db -> model = $Template -> model;
						$templates = $Db -> find_all(false, "*", $order);
						$data[$Template -> model] = $templates;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($Template -> model, null, $this -> sections -> templates, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_admin('templates' . DS . 'index', array('templates' => $data[$Template -> model], 'paginate' => $data['Paginate']));	
					break;
			}
		}
		
		function admin_mailqueue() {
			global $wpdb, $Db, $Queue, $Email, $Subscriber;
			$Db -> model = $Queue -> model;
			
			if ($this -> get_option('scheduling') == "N") {
				$this -> render_error(__('Email scheduling is turned off', $this -> plugin_name));
			}
			
			switch ($_GET['method']) {
				case 'clear'				:
					$Queue -> truncate();
					$message = __('The queue has been truncated', $this -> plugin_name);
					$this -> redirect($this -> url, 'message', $message);
					break;
				case 'mass'					:			
					if (!empty($_POST['action'])) {
						if (!empty($_POST['Queue']['checklist'])) {
							$emails = $_POST['Queue']['checklist'];
						
							switch ($_POST['action']) {
								case 'send'					:
									$subscriberemails = array();
									$emailssent = 0;
								
									foreach ($emails as $email_id) {									
										$this -> remove_server_limits();							
										$Db -> model = $Queue -> model;
									
										if ($email = $Db -> find(array('id' => $email_id))) {											
											$subscriber = $Subscriber -> get($email -> subscriber_id, false);
											$subscriber -> mailinglist_id = $email -> mailinglist_id;
											$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH", time()));
											
											if ((empty($subscriberemails[$email -> history_id])) || (!empty($subscriberemails[$email -> history_id]) && !in_array($subscriber -> email, $subscriberemails[$email -> history_id]))) {						
												$subscriberemails[$email -> history_id][] = $subscriber -> email;
												$message = $this -> render_email('send', array('message' => $email -> message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $email -> history_id, 'post_id' => $email -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $email -> theme_id);
												
												if ($this -> execute_mail($subscriber, false, $email -> subject, $message, $email -> attachments, $email -> history_id, $eunique)) {
													$Queue -> delete($email -> id);
													$emailssent++;	
												}
											}
										}
									}
									
									$msg_type = 'message';
									$message = $emailssent . ' ' . __('queue email(s) have been sent out', $this -> plugin_name);
									
									break;
								case 'delete'				:
									if ($Queue -> delete_array($emails)) {
										$msg_type = 'message';
										$message = count($emails) . ' ' . __('queue email(s) removed', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('Queue emails cannot be removed', $this -> plugin_name);
									}
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No queue emails were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'delete'				:
					if (!empty($_GET['id'])) {
						if ($Queue -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Queued email has been deleted', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Queued email could not be deleted', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No queued email was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'send'					:
					if (!empty($_GET['id'])) {
						if ($email = $Db -> find(array('id' => $_GET['id']))) {	
							if (empty($email -> subscriber_id)) {
								$user = $this -> userdata($email -> user_id);
								$eunique = md5($email -> user_id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH", time()));
								$message = $this -> render_email('send', array('message' => $email -> message, 'subject' => $email -> subject, 'subscriber' => false, 'user' => $user, 'history_id' => $email -> history_id, 'post_id' => $email -> post_id), false, 'html', true, $email -> theme_id);
								$result = $this -> execute_mail(false, $user, $email -> subject, $message, $email -> attachments, $email -> history_id, $eunique);
							} else {																
								$subscriber = $Subscriber -> get($email -> subscriber_id, false);
								$subscriber -> mailinglist_id = $email -> mailinglist_id;
								$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH", time()));
								$message = $this -> render_email('send', array('message' => $email -> message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $email -> history_id, 'post_id' => $email -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $email -> theme_id);
								$result = $this -> execute_mail($subscriber, false, $email -> subject, $message, $email -> attachments, $email -> history_id, $eunique);
							}
							
							if ($result == true) {
								$Queue -> delete($email -> id);
								$emailssent++;	
								$msg_type = 'message';
								$message = __('Queued email has been sent', $this -> plugin_name);
							} else {
								global $mailerrors;
								$Db -> model = $Queue -> model;
								$Db -> save_field('error', esc_sql(trim(strip_tags($mailerrors))), array('id' => $email -> id));
								$msg_type = 'error';
								$message = sprintf(__('Email could not be sent: %s', $this -> plugin_name), esc_sql(trim(strip_tags($mailerrors))));
							}
						} else {
							$msg_type = 'error';
							$message = __('Queued email cannot be read', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No queued email was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default						:
					$conditions = false;
					$sections = $this -> sections -> queue;
					$queue_table = $wpdb -> prefix . $Queue -> table;
				
					$perpage = (isset($_COOKIE[$this -> pre . 'queuesperpage'])) ? $_COOKIE[$this -> pre . 'queuesperpage'] : 15;				
					$ofield = (isset($_COOKIE[$this -> pre . 'queuessorting'])) ? $_COOKIE[$this -> pre . 'queuessorting'] : "modified";
					$odir = (isset($_COOKIE[$this -> pre . 'queues' . $ofield . 'dir'])) ? $_COOKIE[$this -> pre . 'queues' . $ofield . 'dir'] : "DESC";
					$order = array($ofield, $odir);
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					if (!empty($_GET['filter'])) {
						$sections .= '&filter=1';
						
						if (!empty($_GET['history_id'])) {
							$conditions[$queue_table . '.history_id'] = $_GET['history_id'];
						}
						
						if (!empty($_GET['theme_id'])) {
							$conditions[$queue_table . '.theme_id'] = $_GET['theme_id'];
						}
					}
					
					if (!empty($_GET['showall'])) {						
						$emailsquery = "SELECT * FROM " . $wpdb -> prefix . $Queue -> table . "";
						$emails = $wpdb -> get_results($emailsquery);
						
						$data[$Queue -> model] = $emails;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($Queue -> model, null, $sections, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_admin('queues' . DS . 'index', array('queues' => $data[$Queue -> model], 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		function admin_history() {
			global $wpdb, $Db, $Html, $History, $HistoriesList, $Email, $Subscriber, $SubscribersList, $wpmlClick;
			$Db -> model = $History -> model;
			
			$emails_table = $wpdb -> prefix . $Email -> table;
			$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
			$histories_table = $wpdb -> prefix . $History -> table;
			$clicks_table = $wpdb -> prefix . $wpmlClick -> table;
		
			switch ($_GET['method']) {
				case 'view'				:
					if (!empty($_GET['id'])) {
						if ($history = $History -> get($_GET['id'])) {							
							$sections = $this -> sections -> history . '&method=view&id=' . $history -> id;
							
							$conditions = array($wpdb -> prefix . $Email -> table . '.history_id' => $_GET['id']);
							$perpage = (isset($_COOKIE[$this -> pre . 'emailsperpage'])) ? $_COOKIE[$this -> pre . 'emailsperpage'] : 20;
							
							$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
							$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
							
							switch ($orderfield) {
								case 'clicked'						:
									$orderfield = "clicked";
									break;
								case 'subscriber_id'				:
									$orderfield = $subscribers_table . ".email";
									break;
								default 							:
									$orderfield = $emails_table . "." . $orderfield;
									break;
							}
							
							$order = array($orderfield, $orderdirection);
							
							$conditions_and = array();
							$dojoin = false;
							
							if (!empty($_GET['filter'])) {
								$sections .= '&filter=1';
								
								// status
								if (!empty($_GET['status'])) {
									switch ($_GET['status']) {
										case 'all'				:
											$dojoin = false;
											break;
										case 'sent'				:
											$dojoin = false;
											$conditions_and[$emails_table . '.status'] = "sent";
											break;
										case 'unsent'			:
											$dojoin = false;
											$conditions_and[$emails_table . '.status'] = "unsent";
											break;
									}
								}
								
								// read
								if (!empty($_GET['read'])) {
									switch ($_GET['read']) {
										case 'Y'			:
											$dojoin = false;
											$conditions_and[$emails_table . '.read'] = "Y";
											break;
										case 'N'			:
											$dojoin = false;
											$conditions_and[$emails_table . '.read'] = "N";
											break;
										case 'all'			:
										default 			:
											$dojoin = false;
											break;
									}
								}
								
								// clicked
								if (!empty($_GET['clicked'])) {
									switch ($_GET['clicked']) {
										case 'Y'			:
											$conditions_and['clicked'] = "Y";
											break;
										case 'N'			:
											$conditions_and['clicked'] = "N";
											break;
										case 'all'			:
										default 			:
											//do nothing...
											break;
									}
								}
								
								if (!empty($_GET['bounced'])) {
									switch ($_GET['bounced']) {
										case 'Y'			:
											$conditions_and['bounced'] = "Y";
											$conditions_and[$emails_table . '.bounced'] = "Y";
											break;
										case 'N'			:
											$conditions_and['bounced'] = "N";
											$conditions_and[$emails_table . '.bounced'] = "N";
											break;
										case 'all'			:
										default 			:
											//do nothing...
											break;
									}
								}
							}
							
							//$data = $Email -> get_all_paginated($conditions, false, $this -> sections -> history . '&amp;method=view&amp;id=' . $_GET['id'], $perpage, $order, '#emailssent');
							$data = $this -> paginate($Email -> model, $emails_table . ".*", $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$this -> render_admin('history' . DS . 'view', array('history' => $history, 'emails' => $data[$Email -> model], 'paginate' => $data['Paginate']));
						} else {
							$message = __('History email cannot be read', $this -> plugin_name);
							$this -> redirect($this -> url, 'error', $message);
						}
					} else {
						$message = __('No history email was specified', $this -> plugin_name);
						$this -> redirect($this -> url, 'error', $message);
					}
					break;
				case 'delete'			:
					if (!empty($_GET['id'])) {
						if ($History -> delete($_GET['id'])) {
							$message = __('History email has been removed', $this -> plugin_name);
						} else {
							$message = __('History email could not be removed', $this -> plugin_name);
						}
					} else {
						$message = __('No history email was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, 'message', $message);
					break;
				case 'duplicate'		:
					if (!empty($_GET['id'])) {
						$query = "SHOW TABLE STATUS LIKE '" . $wpdb -> prefix . $History -> table . "'";
						$tablestatus = $wpdb -> get_row($query);
						$nextid = $tablestatus -> Auto_increment;					
						$query = "CREATE TEMPORARY TABLE `historytmp` SELECT * FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $_GET['id'] . "'";
						$wpdb -> query($query);
						$query = "UPDATE `historytmp` SET `id` = '" . $nextid . "', `post_id` = '0', `sent` = '0', `created` = '" . $Html -> gen_date() . "', `modified` = '" . $Html -> gen_date() . "' WHERE `id` = '" . $_GET['id'] . "'";
						$wpdb -> query($query);
						$query = "INSERT INTO `" . $wpdb -> prefix . $History -> table . "` SELECT * FROM `historytmp` WHERE `id` = '" . $nextid . "'";
						$wpdb -> query($query);
						$query = "DROP TEMPORARY TABLE `historytmp`;";
						$wpdb -> query($query);
						
						$msgtype = 'message';
						$message = __('History email has been duplicated', $this -> plugin_name);
					} else {
						$msgtype = 'error';
						$message = __('No history email was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msgtype, $message);
					break;
				case 'emails-mass'		:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['emails'])) {
							switch ($_POST['action']) {
								case 'subscribers_delete'		:
									foreach ($_POST['emails'] as $email_id) {
										$Db -> model = $Email -> model;
										if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
											if (!empty($email -> subscriber_id)) {
												$Db -> model = $Subscriber -> model;
												$Db -> delete($email -> subscriber_id);
											}
										}
									}
									
									$msg_type = 'message';
									$message = __('Selected subscribers deleted', $this -> plugin_name);
									break;
								case 'subscribers_addlists'		:
									if (!empty($_POST['lists'])) {
										foreach ($_POST['emails'] as $email_id) {
											$Db -> model = $Email -> model;
											if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
												foreach ($_POST['lists'] as $list_id) {													
													$sl_data = array(
														'subscriber_id'			=>	$email -> subscriber_id,
														'list_id'				=>	$list_id,
														'active'				=>	"Y",
													);	
													
													$SubscribersList -> save($sl_data, true);
												}
											}
										}
										
										$msg_type = 'message';
										$message = __('Selected lists added to subscribers', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('No lists were selected', $this -> plugin_name);
									}
									break;
								case 'subscribers_setlists'		:
									if (!empty($_POST['lists'])) {
										foreach ($_POST['emails'] as $email_id) {
											$Db -> model = $Email -> model;
											if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
												if (!empty($email -> subscriber_id)) {
													$SubscribersList -> delete_all(array('subscriber_id' => $email -> subscriber_id));
													
													foreach ($_POST['lists'] as $list_id) {
														$sl_data = array(
															'subscriber_id'					=>	$email -> subscriber_id,
															'list_id'						=>	$list_id,
															'active'						=>	"Y",
														);
														
														$SubscribersList -> save($sl_data, true);
													}
												}
											}
										}
										
										$msg_type = 'message';
										$message = __('Selected lists set to subscribers', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('No lists were selected', $this -> plugin_name);
									}
									break;
								case 'subscribers_dellists'		:
									if (!empty($_POST['lists'])) {
										foreach ($_POST['emails'] as $email_id) {
											$Db -> model = $Email -> model;
											if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
												if (!empty($email -> subscriber_id)) {
													foreach ($_POST['lists'] as $list_id) {
														$SubscribersList -> delete_all(array('subscriber_id' => $email -> subscriber_id, 'list_id' => $list_id));
													}
												}
											}
										}
										
										$msg_type = 'error';
										$message = __('Selected lists removed from subscriber', $this -> plugin_name);
									} else {
										$msg_type = 'error';
										$message = __('No lists were selected', $this -> plugin_name);
									}
									break;
								case 'delete'					:
									foreach ($_POST['emails'] as $email_id) {
										$Db -> model = $Email -> model;
										$Db -> delete($email_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected emails have been deleted', $this -> plugin_name);
									break;
								case 'export'					:
									$history_id = $_POST['id'];
									$email_ids = implode(",", $_POST['emails']);
									$emailsquery = "SELECT * FROM " . $wpdb -> prefix . $Email -> table . " WHERE id IN (" . $email_ids . ")";
									
									if ($emails = $wpdb -> get_results($emailsquery)) {										
										/* CSV Headings */
										$data = "";
										$data .= '"' . __('Email Address', $this -> plugin_name) . '",';
										$data .= '"' . __('Mailing List', $this -> plugin_name) . '",';
										$data .= '"' . __('Sent/Unsent', $this -> plugin_name) . '",';
										$data .= '"' . __('Read/Opened', $this -> plugin_name) . '",';
										$data .= '"' . __('Sent Date', $this -> plugin_name) . '",';
										$data .= "\r\n";
										
										foreach ($emails as $email) {
											$this -> remove_server_limits();
											
											if (!empty($email -> subscriber_id)) {
												$Db -> model = $Subscriber -> model;
												$subscriber = $Db -> find(array('id' => $email -> subscriber_id));
												/* Subscriber */
												$Db -> model = $Subscriber -> model;
					                        	$subscriber = $Db -> find(array('id' => $email -> subscriber_id));
												$data .= '"' . $subscriber -> email . '",';
												
												/* Mailing List */
												$Db -> model = $Mailinglist -> model;
					                        	$mailinglist = $Db -> find(array('id' => $email -> mailinglist_id));
												$data .= '"' . __($mailinglist -> title) . '",';
											} elseif (!empty($email -> user_id)) {
												$user = $this -> userdata($email -> user_id);
												$data .= '"' . $user -> user_email . '",';
												$data .= '"' . '' . '",';
											}
											
											/* Read/Opened Status */
											$data .= '"' . $email -> status . '",';
											$data .= '"' . ((!empty($email -> read) && $email -> read == "Y") ? __('Yes', $this -> plugin_name) : __('No', $this -> plugin_name)) . '",';
											$data .= '"' . (date_i18n("Y-m-d H:i:s", strtotime($email -> modified))) . '",';
											$data .= "\r\n";	
										}
										
										if (!empty($data)) {
											$exportfile = 'history' . $history_id . '-emails-' . date_i18n("Ymd", time()) . '.csv';
											$exportpath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
											$exportfull = $exportpath . $exportfile;
											
											if ($fh = fopen($exportfull, "w")) {
												fwrite($fh, $data);
												fclose($fh);
												@chmod($exportfull, 0777);
												
												$exportfileabs = $Html -> uploads_url() . '/' . $exportfile;	
												$msg_type = 'message';
												//$message = __('CSV has been exported with filename "' . $exportfile . '".', $this -> plugin_name) . ' <a href="' . $exportfileabs . '" title="' . __('Download the CSV', $this -> plugin_name) . '">' . __('Download the CSV', $this -> plugin_name) . '</a>';
												$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history_id . '&newsletters_exportlink=' . $exportfile));
											} else {
												$msg_type = 'error';
												$message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', $this -> plugin_name), $exportpath);	
											}
										} else {
											$msg_type = 'error';
											$message = __('CSV data could not be formulated, no emails maybe? Please try again', $this -> plugin_name);
										}
									} else {
										$msg_type = 'error';
										$message = __('No history/draft emails are available to export!', $this -> plugin_name);
									}
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No emails were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'mass'				:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['historylist'])) {
							$histories = $_POST['historylist'];
							
							switch ($_POST['action']) {
								case 'delete'				:
									foreach ($histories as $history_id) {
										$Db -> model = $History -> model;
										$Db -> delete($history_id);
									}
									
									$msg_type = 'message';
									$message = count($histories) . ' ' . __('history record(s) have been removed', $this -> plugin_name);
									break;
								case 'export'				:
									global $Db, $Html, $Email, $History, $Mailinglist, $Theme;
									$Db -> model = $History -> model;
									
									if ($emails = $Db -> find_all(false, false, array('modified', "DESC"))) {
										$data = "";
										$data .= '"' . __('Id', $this -> plugin_name) . '",';
										$data .= '"' . __('Subject', $this -> plugin_name) . '",';
										$data .= '"' . __('Lists', $this -> plugin_name) . '",';
										$data .= '"' . __('Template', $this -> plugin_name) . '",';
										$data .= '"' . __('Author', $this -> plugin_name) . '",';
										$data .= '"' . __('Read %', $this -> plugin_name) . '",';
										$data .= '"' . __('Emails Sent', $this -> plugin_name) . '",';
										$data .= '"' . __('Emails Read', $this -> plugin_name) . '",';
										$data .= '"' . __('Created', $this -> plugin_name) . '",';
										$data .= '"' . __('Modified', $this -> plugin_name) . '",';
										$data .= "\r\n";
										
										foreach ($emails as $email) {
											$this -> remove_server_limits();			//remove the server resource limits
											
											$data .= '"' . $email -> id . '",';
											$data .= '"' . $email -> subject . '",';						
											
											/* Mailing lists */
											if (!empty($email -> mailinglists)) {
												$data .= '"';
												$m = 1;
												
												foreach ($email -> mailinglists as $mailinglist_id) {
													$mailinglist = $Mailinglist -> get($mailinglist_id);	
													$data .= __($mailinglist -> title);
													
													if ($m < count($email -> mailinglists)) {
														$data .= ', ';
													}
													
													$m++;
												}
												
												$data .= '",';
											} else { 
												$data .= '"",';
											}
											
											/* Theme */
											if (!empty($email -> theme_id)) {
												$Db -> model = $Theme -> model;
												
												if ($theme = $Db -> find(array('id' => $email -> theme_id))) {
													$data .= '"' . $theme -> title . '",';
												} else {
													$data .= '"",';	
												}
											} else {
												$data .= '"",';	
											}
											
											/* Author */
											if (!empty($email -> user_id)) {
												if ($user = get_userdata($email -> user_id)) {
													$data .= '"' . $user -> display_name . '",';
												} else {
													$data .= '"",';	
												}
											} else {
												$data .= '"",';	
											}
											
											/* read % */
											$Db -> model = $Email -> model;
											$etotal = $Db -> count(array('history_id' => $email -> id));
											$eread = $Db -> count(array('history_id' => $email -> id, 'read' => "Y"));
											$eperc = (!empty($etotal)) ? (($eread / $etotal) * 100) : 0;
											$data .= '"' . number_format($eperc, 2, '.', '') . '% ' . __('read', $this -> plugin_name) . '",';
											
											$data .= '"' . $etotal . '",'; 					// emails sent
											$data .= '"' . $eread . '",';					// emails read
											$data .= '"' . $email -> created . '",';		// created date
											$data .= '"' . $email -> modified . '",';		// modified date
											
											$data .= "\r\n";
										}
										
										if (!empty($data)) {
											$filename = "history-" . date_i18n("Ymd", time()) . ".csv";
											$filepath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
											$filefull = $filepath . $filename;
											
											if ($fh = fopen($filefull, "w")) {
												fwrite($fh, $data);
												fclose($fh);
												
												//$fileabs = $Html -> uploads_url() . '/' . $filename;	
												//$message = __('CSV has been exported with filename "' . $filename . '".', $this -> plugin_name) . ' <a href="' . $fileabs . '" title="' . __('Download the CSV', $this -> plugin_name) . '">' . __('Download the CSV', $this -> plugin_name) . '</a>';
												//$message = __('Sent and draft emails have been exported and your download will begin shortly', $this -> plugin_name); 
												$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&newsletters_exportlink=' . $filename));
											} else {
												$message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', $this -> plugin_name), $filepath);
												$this -> redirect($this -> url, "error", $message);	
											}
										} else {
											$message = __('CSV data could not be formulated, no emails maybe? Please try again', $this -> plugin_name);
											$this -> redirect($this -> url, "error", $message);
										}
									} else {
										$message = __('No history/draft emails are available to export!', $this -> plugin_name);
										$this -> redirect($this -> url, "error", $message);
									}
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No history emails were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'clear'			:
					if ($History -> truncate()) {
						$msg_type = 'message';
						$message = __('History list has been purged', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('History items cannot be removed', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'removeattachment'	:
					global $Db, $HistoriesAttachment;
					
					if (!empty($_GET['id'])) {
						$Db -> model = $HistoriesAttachment -> model;
						
						if ($attachment = $Db -> find(array('id' => $_GET['id']))) {
							if (!empty($attachment -> filename) && file_exists($attachment -> filename)) {
								@unlink($attachment -> filename);	
							}
							
							$Db -> model = $HistoriesAttachment -> model;
							$Db -> delete($attachment -> id);
							
							$msg_type = 'message';
							$message = __('Attachment file has been removed.', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Attachment could not be read.', $this -> plugin_name);	
						}
					} else {
						$msg_type = 'error';
						$message = __('No attachment was specified.', $this -> plugin_name);	
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'exportsent'		:
					global $wpdb, $Html, $Db, $Subscriber, $Mailinglist, $History, $Email;
				
					if (!empty($_GET['history_id'])) {
						$Db -> model = $Email -> model;
						
						if ($emails = $Db -> find_all(array('history_id' => $_GET['history_id']), false, array('modified', "DESC"))) {
							/* CSV Headings */
							$data = "";
							$data .= '"' . __('Email Address', $this -> plugin_name) . '",';
							$data .= '"' . __('Mailing List', $this -> plugin_name) . '",';
							$data .= '"' . __('Read/Opened', $this -> plugin_name) . '",';
							$data .= '"' . __('Sent Date', $this -> plugin_name) . '",';
							$data .= "\r\n";
							
							foreach ($emails as $email) {
								$this -> remove_server_limits();
								
								if (!empty($email -> subscriber_id)) {
									$Db -> model = $Subscriber -> model;
									$subscriber = $Db -> find(array('id' => $email -> subscriber_id));
									/* Subscriber */
									$Db -> model = $Subscriber -> model;
		                        	$subscriber = $Db -> find(array('id' => $email -> subscriber_id));
									$data .= '"' . $subscriber -> email . '",';
									
									/* Mailing List */
									$Db -> model = $Mailinglist -> model;
		                        	$mailinglist = $Db -> find(array('id' => $email -> mailinglist_id));
									$data .= '"' . __($mailinglist -> title) . '",';
								} elseif (!empty($email -> user_id)) {
									$user = $this -> userdata($email -> user_id);
									$data .= '"' . $user -> user_email . '",';
									$data .= '"' . '' . '",';
								}
								
								/* Read/Opened Status */
								$data .= '"' . ((!empty($email -> read) && $email -> read == "Y") ? __('Yes', $this -> plugin_name) : __('No', $this -> plugin_name)) . '",';
								$data .= '"' . (date_i18n("Y-m-d H:i:s", strtotime($email -> modified))) . '",';
								$data .= "\r\n";	
							}
							
							if (!empty($data)) {
								$exportfile = 'history' . $_GET['history_id'] . '-emails-' . date_i18n("Ymd", time()) . '.csv';
								$exportpath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
								$exportfull = $exportpath . $exportfile;
								
								if ($fh = fopen($exportfull, "w")) {
									fwrite($fh, $data);
									fclose($fh);
									@chmod($exportfull, 0777);
									
									$exportfileabs = $Html -> uploads_url() . '/' . $exportfile;	
									$msg_type = 'message';
									//$message = __('CSV has been exported with filename "' . $exportfile . '".', $this -> plugin_name) . ' <a href="' . $exportfileabs . '" title="' . __('Download the CSV', $this -> plugin_name) . '">' . __('Download the CSV', $this -> plugin_name) . '</a>';
									$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $_GET['history_id'] . '&newsletters_exportlink=' . $exportfile));
								} else {
									$msg_type = 'error';
									$message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', $this -> plugin_name), $exportpath);	
								}
							} else {
								$msg_type = 'error';
								$message = __('CSV data could not be formulated, no emails maybe? Please try again', $this -> plugin_name);
							}
						} else {
							$msg_type = 'error';
							$message = __('No history/draft emails are available to export!', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No history email was specified, please try again.', $this -> plugin_name);	
					}
					
					$this -> redirect("?page=" . $this -> sections -> history . "&method=view&id=" . $_GET['history_id'], $msg_type, $message);
					break;
				default					:
					$sections = $this -> sections -> history;
					$history_table = $wpdb -> prefix . $History -> table;
					$historieslist_table = $wpdb -> prefix . $HistoriesList -> table;
					$conditions_and = array();
					$perpage = (isset($_COOKIE[$this -> pre . 'historiesperpage'])) ? $_COOKIE[$this -> pre . 'historiesperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('subject' => "LIKE '%" . $searchterm . "%'") : false;		
					
					$ofield = (isset($_COOKIE[$this -> pre . 'historysorting'])) ? $_COOKIE[$this -> pre . 'historysorting'] : "modified";
					$odir = (isset($_COOKIE[$this -> pre . 'history' . $ofield . 'dir'])) ? $_COOKIE[$this -> pre . 'history' . $ofield . 'dir'] : "DESC";
					$order = array($ofield, $odir);
					
					$orderfield = (empty($_GET['orderby'])) ? 'created' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					
					$dojoin = false;
					
					if (!empty($_GET['filter'])) {
						$sections .= '&filter=1';
						
						if (!empty($_GET['list'])) {
							switch ($_GET['list']) {
								case 'all'				:
									$dojoin = false;
									break;
								case 'none'				:
									$dojoin = false;
									$conditions_and[$history_table . '.id'] = "NOT IN (SELECT history_id FROM " . $historieslist_table . ")";
									break;
								default 				:
									$dojoin = true;
									$conditions_and[$historieslist_table . '.list_id'] = $_GET['list'];
									break;
							}
						}
						
						if (!empty($_GET['sent'])) {
							switch ($_GET['sent']) {
								case 'all'				:
								
									break;
								case 'draft'			:
									$conditions_and[$history_table . '.sent'] = '0';
									break;
								case 'sent'				:
									$conditions_and[$history_table . '.sent'] = 'LE 1';
									break;
							}
						}
						
						if (!empty($_GET['theme_id'])) {
							if ($_GET['theme_id'] != "all") {
								$conditions_and[$history_table . '.theme_id'] = $_GET['theme_id'];
							}					
						}
					}
					
					$conditions = apply_filters($this -> pre . '_admin_history_conditions', $conditions);
						
					if (!empty($_GET['showall'])) {
						$Db -> model = $History -> model;
						$histories = $Db -> find_all(false, "*", $order);
						$data[$History -> model] = $histories;
						$data['Paginate'] = false;
					} else {	
						if ($dojoin) {
							$data = $this -> paginate($HistoriesList -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$histories = $data[$HistoriesList -> model];	
						} else {
							$data = $this -> paginate($History -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$histories = $data[$History -> model];
						}
					}
					
					$this -> render_admin('history' . DS . 'index', array('histories' => $histories, 'paginate' => $data['Paginate']));			
					break;
			}
		}
		
		function admin_links() {
			switch ($_GET['method']) {
				case 'delete'					:
					if (!empty($_GET['id'])) {
						if ($this -> Link -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Link has been deleted', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Link could not be deleted', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No link was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'mass'						:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['links'])) {
							$links = $_POST['links'];
							
							switch ($_POST['action']) {
								case 'delete'				:
									foreach ($links as $link_id) {
										$this -> Link -> delete($link_id);
									}
									
									$msg_type = 'message';
									$message = __('Selected links have been deleted', $this -> plugin_name);
									break;
								case 'reset'				:
									foreach ($links as $link_id) {
										$this -> Click -> delete_all(array('link_id' => $link_id));
									}
									
									$msg_type = 'message';
									$message = __('Selected links have been reset', $this -> plugin_name);
									break;
							}
						} else {
							$msg_type = 'error';
							$message = __('No links were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				default							:
					$perpage = (isset($_COOKIE[$this -> pre . 'linksperpage'])) ? $_COOKIE[$this -> pre . 'linksperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('link' => "LIKE '%" . $searchterm . "%'") : false;					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					$sub = $this -> sections -> links;
						
					if (!empty($_GET['showall'])) {
						$links = $this -> Link -> find_all(false, "*", $order);
						$data[$this -> Link -> model] = $links;
						$data['Paginate'] = false;
					} else {	
						$data = $this -> paginate($this -> Link -> model, "*", $sub, $conditions, $searchterm, $perpage, $order);
					}
					$this -> render('links' . DS . 'index', array('links' => $data[$this -> Link -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;
			}
		}
		
		function admin_clicks() {
			switch ($_GET['method']) {
				case 'delete'					:
					if (!empty($_GET['id'])) {
						if ($this -> Click -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Click has been deleted', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Click could not be deleted', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No click was specified', $this -> plugin_name);
					}
					
					$this -> redirect('?page=' . $this -> sections -> clicks, $msg_type, $message);
					break;
				case 'mass'						:
					if (!empty($_POST['action'])) {
						$action = $_POST['action'];
						$clicks = $_POST['clicks'];
						
						if (!empty($clicks)) {
							foreach ($clicks as $click_id) {
								$this -> Click -> delete($click_id);
							}
							
							$msg_type = 'message';
							$message = __('Selected clicks have been deleted', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('No clicks were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No action was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				default							:
					$perpage = (isset($_COOKIE[$this -> pre . 'linksperpage'])) ? $_COOKIE[$this -> pre . 'linksperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('link' => "LIKE '%" . $searchterm . "%'") : false;					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					$sub = $this -> sections -> links;
					
					$conditions_and = array();
					
					if (!empty($_GET['subscriber_id'])) {
						$conditions_and['subscriber_id'] = $_GET['subscriber_id'];
					}
					
					if (!empty($_GET['link_id'])) {
						$conditions_and['link_id'] = $_GET['link_id'];
					}
					
					if (!empty($_GET['history_id'])) {
						$conditions_and['history_id'] = $_GET['history_id'];
					}
					
					if (!empty($_GET['showall'])) {
						$clicks = $this -> Click -> find_all(false, "*", $order);
						$data[$this -> Click -> model] = $clicks;
						$data['Paginate'] = false;
					} else {
						$data = $this -> paginate($this -> Click -> model, "*", $sub, $conditions, $searchterm, $perpage, $order, $conditions_and);
						$clicks = $data[$this -> Click -> model];
					}
					
					$this -> render('clicks' . DS . 'index', array('clicks' => $clicks, 'paginate' => $data['Paginate']), true, 'admin');
					break;
			}
		}
		
		function admin_orders() {
			global $wpdb, $Db, $wpmlOrder, $Subscriber, $Mailinglist;
			$Db -> model = $wpmlOrder -> model;
			
			switch ($_GET['method']) {
				case 'view'			:
					if (!empty($_GET['id'])) {
						if ($order = $wpmlOrder -> get($_GET['id'])) {
							$subscriber = $Subscriber -> get($order -> subscriber_id, false);
							$mailinglist = $Mailinglist -> get($order -> list_id, false);
							$this -> render_admin('orders' . DS . 'view', array('order' => $order, 'subscriber' => $subscriber, 'mailinglist' => $mailinglist));
						} else {
							$this -> render_error(__('Order could not be retrieved', $this -> plugin_name));
						}
					} else {
						$this -> render_error(__('No order ID was specified', $this -> plugin_name));
					}
					break;
				case 'save'			:
					if (!empty($_POST)) {
						$_POST['completed'] = "Y";
					
						if ($wpmlOrder -> save($_POST, true)) {
							$message = __('Order has been saved', $this -> plugin_name);
						
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> orders . '&method=save&id=' . $wpmlOrder -> insertid . '&continueediting=1'), 'message', $message);
							} else {
								$this -> render_message($message);
								$data = $wpmlOrder -> get_all_paginated();
								$this -> render_admin('orders' . DS . 'index', array('orders' => $data[$wpmlOrder -> model], 'paginate' => $data['Pagination']));
							}
						} else {
							$this -> render_error(__('Order could not be saved', $this -> plugin_name));
							$this -> render_admin('orders' . DS . 'save', array('order' => new wpmlOrder($_POST), 'errors' => $wpmlOrder -> errors));
						}
					} else {
						if (!empty($_GET['id'])) {
							if ($order = $wpmlOrder -> get($_GET['id'])) {
								$this -> render_admin('orders' . DS . 'save', array('order' => $order));
							} else {
								$this -> render_error(__('Order could not be read', $this -> plugin_name));
							}
						} else {
							$this -> render_error(__('No order ID was specified', $this -> plugin_name));
						}
					}
					break;
				case 'delete'		:
					if (!empty($_GET['id'])) {
						if ($wpmlOrder -> delete($_GET['id'])) {
							$this -> render_message(__('Order successfully removed', $this -> plugin_name));
						} else {
							$this -> render_error(__('Order could not be removed', $this -> plugin_name));
						}
					} else {
						$this -> render_error(__('No order ID was specified', $this -> plugin_name));
					}
					
					$data = $wpmlOrder -> get_all_paginated();
					$this -> render_admin('orders' . DS . 'index', array('orders' => $data[$wpmlOrder -> model], 'paginate' => $data['Pagination']));
					break;
				case 'mass'			:
					if (!empty($_POST)) {
						if (!empty($_POST['orderslist'])) {
							if (!empty($_POST['action'])) {
								$orders = $_POST['orderslist'];
								
								switch ($_POST['action']) {
									case 'delete'		:
										foreach ($orders as $order_id) {
											$wpmlOrder -> delete($order_id);
										}
										
										$msg_type = 'message';
										$message = __('Selected orders succesfully removed', $this -> plugin_name);
										break;
								}
							} else {
								$msg_type = 'error';
								$message = __('No action was selected', $this -> plugin_name);
							}
						} else {
							$msg_type = 'error';
							$message = __('No orders were selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No data was posted', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default				:
					$perpage = (isset($_COOKIE[$this -> pre . 'ordersperpage'])) ? $_COOKIE[$this -> pre . 'ordersperpage'] : 15;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? $_GET[$this -> pre . 'searchterm'] : false;
					$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : $searchterm;
					
					if (!empty($_POST['searchterm'])) {
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}
					
					$conditions = (!empty($searchterm)) ? array('subscriber_id' => "LIKE '%" . $searchterm . "%'") : false;		
					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
						
					if (!empty($_GET['showall'])) {
						$Db -> model = $wpmlOrder -> model;
						$orders = $Db -> find_all(false, "*", $order);
						$data[$wpmlOrder -> model] = $orders;
						$data['Paginate'] = false;
					} else {	
						$data = $this -> paginate($wpmlOrder -> model, null, $this -> sections -> orders, $conditions, $searchterm, $perpage, $order);
					}
					
					$this -> render_admin('orders' . DS . 'index', array('orders' => $data[$wpmlOrder -> model], 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		function admin_fields() {
			global $wpdb, $Db, $Field, $FieldsList;
			
			switch ($_GET['method']) {
				case 'save'				:
					if (!empty($_POST)) {
						if ($Field -> save($_POST)) {					
							$message = __('Custom field has been saved', $this -> plugin_name);
							
							if (!empty($_POST['continueediting'])) {
								$this -> redirect(admin_url('admin.php?page=' . $this -> sections -> fields . '&method=save&id=' . $Field -> insertid . '&continueediting=1'), 'message', $message);	
							} else {
								$this -> redirect('?page=' . $this -> sections -> fields, 'message', $message);
							}
						} else {
							$this -> render_error(__('Custom field could not be saved', $this -> plugin_name));
							$this -> render_admin('fields' . DS . 'save');
						}
					} else {
						$Field -> get($_GET['id']);
						
						if ($Field -> data['Field'] -> slug == "email" || $Field -> data['Field'] -> slug == "list") {
							$this -> render_message(__('This is a fixed field and can be edited but not deleted.', $this -> plugin_name));
						}
						
						$this -> render_admin('fields' . DS . 'save');
					}
					break;
				case 'delete'			:
					if (!empty($_GET['id'])) {
						$fieldquery = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE id = '" . $_GET['id'] . "'";
						if ($field = $wpdb -> get_row($fieldquery)) {
							if ($field -> slug != "email" && $field -> slug != "list") {
								if ($Field -> delete($_GET['id'])) {
									$message_type = 'message';
									$message = __('Field has been removed', $this -> plugin_name);
								} else {
									$message_type = 'error';
									$message = __('Field cannot be removed', $this -> plugin_name);
								}	
							} else {
								$message_type = 'error';
								$message = __('This field may not be deleted.', $this -> plugin_name);
							}
						} else {
							$message_type = 'error';
							$message = __('Field cannot be read.', $this -> plugin_name);
						}
					} else {
						$message_type = 'error';
						$message = __('No field was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $message_type, $message);
					break;
				case 'mass'				:					
					if (!empty($_POST['fieldslist'])) {					
						if (!empty($_POST['action'])) {						
							$fields = $_POST['fieldslist'];
							
							switch ($_POST['action']) {
								case 'delete'		:
									$Field -> delete_array($fields);
									$message = __('Selected custom fields removed', $this -> plugin_name);
									break;
								case 'required'		:
									foreach ($fields as $field_id) {
										$Db -> model = $Field -> model;
										$Db -> save_field('required', "Y", array('id' => $field_id));
									}
									
									$message = __('Selected custom fields have been set as required', $this -> plugin_name);
									break;
								case 'notrequired'	:							
									foreach ($fields as $field_id) {
										$fieldquery = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE id = '" . $field_id . "'";
										if ($field = $wpdb -> get_row($fieldquery)) {
											if ($field -> slug != "email") {
												$Db -> model = $Field -> model;
												$Db -> save_field('required', "N", array('id' => $field_id));
											}
										}
									}
									
									$message = __('Selected custom fields have been set as NOT required', $this -> plugin_name);
									break;
							}
							
							$msg_type = 'message';
						} else {
							$msg_type = 'error';
							$message = __('No action was selected', $this -> plugin_name);
						}
					} else {
						$msg_type = 'error';
						$message = __('No custom fields were selected', $this -> plugin_name);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				case 'order'			:
					$Db -> model = $Field -> model;
					$fields = $Db -> find_all(false, false, array('order', "ASC"));				
					$this -> render_admin('fields' . DS . 'order', array('fields' => $fields));
					break;
				default					:					
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
								
					$data = array();
					if (!empty($_GET['showall'])) {
						$Db -> model = $Field -> model;
						$data[$Field -> model] = $Db -> find_all($conditions, "*", $order);
						$data['Paginate'] = false;
					} else {
						$perpage = (!empty($_COOKIE[$this -> pre . 'fieldsperpage'])) ? $_COOKIE[$this -> pre . 'fieldsperpage'] : 15;
						$searchterm = (empty($_GET[$this -> pre . 'searchterm'])) ? '' : $_GET[$this -> pre . 'searchterm'];
						$searchterm = (empty($_POST['searchterm'])) ? $searchterm : $_POST['searchterm'];			
						
						if (!empty($_POST['searchterm'])) {
							$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
						}
						
						if (!empty($searchterm)) {
							$conditions[] = "`title` LIKE '%" . $searchterm . "%' OR `slug` LIKE '%" . $searchterm . "%'";	
						}
						
						$data = $this -> paginate($Field -> model, null, $this -> sections -> fields, $conditions, $searchterm, $perpage, $order);
					}
						
					$this -> render_admin('fields' . DS . 'index', array('fields' => $data[$Field -> model], 'paginate' => $data['Paginate']));
					break;
			}
		}
		
		/**
		 * Administration configuration area
		 * Outputs the config form and receives posted option keys and values
		 *
		 **/
		function admin_config() {
			global $wpdb, $Html, $Db, $Subscriber, $Latestpost, $wpmlCountry, $Mailinglist;
			
			do_action('newsletters_admin_settings');
			
			if (!empty($_GET['reset']) && $_GET['reset'] == 1) {
				$this -> update_options();
				$this -> redirect($this -> url);
			}
	
			switch ($_GET['method']) {
				case 'managementpost'	:
					$this -> get_managementpost(false, true);
					$msg_type = 'message';
					$message = __('Manage subscriptions post/page has been created', $this -> plugin_name);
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'checkdb'			:
					$this -> check_roles();
					$this -> check_tables();
					
					if (!empty($this -> tablenames)) {
						foreach ($this -> tablenames as $table) {
							$query = "OPTIMIZE TABLE `" . $table . "`";
							$wpdb -> query($query);
						}
					}
					
					$msg_type = 'message';
					$message = __('All database tables have been checked and optimized.', $this -> plugin_name);
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'clearlpshistory'	:
					if (!empty($_GET['id'])) {
						$clearquery = "DELETE FROM " . $wpdb -> prefix . $Latestpost -> table . " WHERE `lps_id` = '" . $_GET['id'] . "'";	
					} else {
						$clearquery = "TRUNCATE TABLE " . $wpdb -> prefix . $Latestpost -> table . "";
					}
					
					if ($wpdb -> query($clearquery)) {
						$msg_type = 'message';
						$message = __('Latest Posts Subscription history has been cleared.', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('Latest Posts Subscription history could not be cleared, please try again.', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'reset'			:
					$query = "TRUNCATE TABLE `" . $wpdb -> prefix . "" . $wpmlCountry -> table . "`";
					$wpdb -> query($query);
				
					$query = "DELETE FROM `" . $wpdb -> prefix . "options` WHERE `option_name` LIKE '" . $this -> pre . "%';";
				
					if ($wpdb -> query($query)) {				
						$msg_type = 'message';
						$message = __('All configuration settings have been reset', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('Configuration settings cannot be reset', $this -> plugin_name);
					}
					
					$this -> redirect($Html -> retainquery('reset=1', $this -> url), $msg_type, $message);
					break;
				default					:							
					//make sure that data has been posted
					if (!empty($_POST)) {					
						//unset values that are not required
						unset($_POST['save']);
						delete_option('tridebugging');
						$this -> delete_option('inlinestyles');
						$this -> delete_option('themeintextversion');
						$this -> delete_option('emailarchive');
						$this -> delete_option('excerpt_settings');
						
						if (!empty($_FILES)) {
							foreach ($_FILES as $fkey => $fval) {
								switch ($fkey) {
									case 'tracking_image_file'			:
										$tracking_image_file = $this -> get_option('tracking_image_file');
									
										if (!empty($_POST['tracking']) && $_POST['tracking'] == "Y" && !empty($_POST['tracking_image']) && $_POST['tracking_image'] == "custom") {
											if (!empty($_FILES['tracking_image_file']['name'])) {
												$tracking_image_file = $_FILES['tracking_image_file']['name'];
												$tracking_image_path = $Html -> uploads_path() . DS . $this -> plugin_name . DS;
												$tracking_image_full = $tracking_image_path . $tracking_image_file;
											
												if (move_uploaded_file($_FILES['tracking_image_file']['tmp_name'], $tracking_image_full)) {
													$this -> update_option('tracking_image_file', $tracking_image_file);
												} else {
													$this -> render_error(__('Tracking image file could not be moved from /tmp', $this -> plugin_name));
												}
											} else {
												if (empty($tracking_image_file)) {
													$this -> render_error(__('No image was specified', $this -> plugin_name));
												}
											}
										}
										break;
								}
							}
						}
						
						foreach ($_POST as $key => $val) {				
							$this -> update_option($key, $val);
							
							switch ($key) {
								case 'debugging'			:
									if (!empty($val)) {
										update_option('tridebugging', 1);
									}
									break;
								case 'embed'				:
									if ($this -> language_do()) {
										if (!empty($val) && is_array($val)) {
											foreach ($val as $vkey => $vval) {
												$val[$vkey] = $this -> language_join($vval);
											}
										}
									}
									
									$this -> update_option('embed', $val);
									break;
								case 'excerpt_more'			:
									if ($this -> language_do()) {
										$this -> update_option($key, $this -> language_join($val));
									} else {
										$this -> update_option($key, $val);	
									}
									break;
								case 'customcsscode'		:
									if (!empty($_POST['customcss']) && $_POST['customcss'] == "Y") {
										$this -> update_option('customcss', "Y");
										$this -> update_option('customcsscode', $_POST['customcsscode']);
									} else {
										$this -> update_option('customcss', "N");	
									}
									break;
								case 'emailarchive'			:
									if (!empty($val)) {
										$this -> emailarchive_scheduling();
									}
									break;
							}
						}
						
						//update scheduling
						$this -> scheduling();
	                    $this -> pop_scheduling();
	                    $this -> optimize_scheduling();
	                    
	                    if (!empty($_POST['latestposts_updateinterval']) && $_POST['latestposts_updateinterval'] == "Y") {
	                    	$this -> latestposts_scheduling();
	                    }
	
						$this -> render_message(__('Configuration settings successfully updated', $this -> plugin_name));
					}
					
					$mailinglists = $Mailinglist -> get_all('*', true);
					$this -> render_admin('settings', array('mailinglists' => $mailinglists));
					break;
			}
		}
		
		function admin_settings_subscribers() {
			if (!empty($_POST)) {
				$this -> delete_option('unsubscribe_usernotification');
				delete_option('tridebugging');
			
				foreach ($_POST as $key => $val) {				
					switch ($key) {
						// Actions for multilingual strings
						case 'managelinktext'				:
						case 'managementpost'				:
						case 'managementloginsubject'		:
						case 'subscriberexistsmessage'		:
						case 'onlinelinktext'				:
						case 'printlinktext'				:
						case 'activationlinktext'			:
						case 'unsubscribetext'				:
						case 'unsubscribealltext'			:
						case 'resubscribetext'				:
							if ($this -> language_do()) {
								$this -> update_option($key, $this -> language_join($val));
							} else {
								$this -> update_option($key, $val);	
							}
							break;
						case 'debugging'			:
							if (!empty($val)) {
								update_option('tridebugging', 1);
							}
							break;
						case 'activateaction'				:
							$this -> update_option($key, $val);
							$this -> activateaction_scheduling();
							break;
						default								:
							$this -> update_option($key, $val);	
							break;
					}
				}
				
				$this -> render_message(__('Subscribers configuration settings have been saved.', $this -> plugin_name));
			}
			
			$this -> render('settings-subscribers', false, true, 'admin');
		}
		
		function admin_settings_templates() {
		
			if (!empty($_POST)) {
				delete_option('tridebugging');
			
				foreach ($_POST as $key => $val) {				
					if ($this -> language_do()) {
						$this -> update_option($key, $this -> language_join($val));
					} else {
						$this -> update_option($key, $val);
					}
					
					if (!empty($key) && $key == "debugging") {
						update_option('tridebugging', 1);
					}
				}
				
				$this -> render_message(__('Email template configuration settings have been saved.', $this -> plugin_name));
			}
		
			$this -> render('settings-templates', false, true, 'admin');
		}
		
		function admin_settings_system() {
			if (!empty($_POST)) {
				delete_option('tridebugging');
				$this -> delete_option('language_external');
			
				foreach ($_POST as $key => $val) {				
					$this -> update_option($key, $val);
					
					switch ($key) {
						case 'debugging'			:
							if (!empty($val)) {
								update_option('tridebugging', 1);
							}
							break;
						case 'commentformlabel'		:
						case 'registerformlabel'	:
							if ($this -> language_do()) {
								$this -> update_option($key, $this -> language_join($val));
							}
							break;
						case 'captchainterval'		:
							$this -> captchacleanup_scheduling();
							break;
						case 'permissions'		:					
							global $wp_roles;
							$role_names = $wp_roles -> get_names();
						
							if (!empty($_POST['permissions'])) {
								$permissions = $_POST['permissions'];
								
								foreach ($this -> sections as $section_key => $section_menu) {
									foreach ($role_names as $role_key => $role_name) {
										$wp_roles -> remove_cap($role_key, 'newsletters_' . $section_key);
									}
									
									if (!empty($permissions[$section_key])) {
										foreach ($permissions[$section_key] as $role) {
											$wp_roles -> add_cap($role, 'newsletters_' . $section_key);
										}
									} else {
										/* No roles were selected for this capability, at least add 'administrator' */
										$wp_roles -> add_cap('administrator', 'newsletters_' . $section_key);
										$permissions[$section_key][] = 'administrator';
									}
								}
								
								foreach ($this -> blocks as $block) {
									if (!empty($permissions[$block])) {
										foreach ($permissions[$block] as $role) {
											$wp_roles -> add_cap($role, $block);
										}
									} else {
										$wp_roles -> add_cap('administrator', $block);
										$permissions[$block][] = 'administrator';
									}
								}
							}
							
							$this -> update_option('permissions', $permissions);
							break;
						case 'importusers'		:								
							$this -> importusers_scheduling();
							break;
					}
				}
				
				$this -> render_message(__('System configuration settings have been saved.', $this -> plugin_name));
			}
			
			$this -> render('settings-system', false, true, 'admin');
		}
		
		function admin_settings_tasks() {
			
			switch ($_GET['method']) {
				case 'runschedule'		:
					if (!empty($_GET['hook'])) {	
						$arg = (empty($_GET['id'])) ? false : $_GET['id'];
											
						if (preg_match("/(newsletters)/si", $_GET['hook'])) {
							$hook = $_GET['hook'];
						} else {
							$hook = $this -> pre . '_' . $_GET['hook'];
						}
					
						do_action($hook, $arg);	
											
						$msg_type = 'message';
						$message = __('Task has been executed successfully!', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No task was specified, please try again.', $this -> plugin_name);	
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'reschedule'		:
					if (!empty($_GET['hook'])) {						
						switch ($_GET['hook']) {
							case 'newsletters_optimizehook'					:
								$this -> optimize_scheduling();
								break;
							case 'cronhook'			:
								$this -> scheduling();
								break;
							case 'pophook'			:
								$this -> pop_scheduling();
								break;
							case 'latestposts'		:
								$this -> latestposts_scheduling();
								break;
							case 'autoresponders'	:
								$this -> autoresponder_scheduling();
								break;
							case 'captchacleanup'	:
								$this -> captchacleanup_scheduling();
								break;
							case 'importusers'		:
								$this -> importusers_scheduling();
								break;
						}
						
						$msg_type = 'message';
						$message = __('Task has been rescheduled successfully!', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No task was specified, please try again.', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'clearschedule'	:
					if (!empty($_GET['hook'])) {
						if (preg_match("/(newsletters)/si", $_GET['hook'])) {
							$hook = $_GET['hook'];
						} else {
							$hook = $this -> pre . '_' . $_GET['hook'];
						}
						
						wp_clear_scheduled_hook($hook);
						
						$msg_type = 'message';
						$message = __('Task has been unscheduled, remember to reschedule as needed.', $this -> plugin_name);
					} else {
						$msg_type = 'error';
						$message = __('No task was specified, please try again.', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				default					:
					$this -> render('settings-cronschedules', false, true, 'admin');	
					break;
			}
		}
		
		function admin_settings_api() {
			
			$this -> render('settings' . DS . 'api', false, true, 'admin');
		}
		
		function admin_settings_updates() {
			switch ($_GET['method']) {
				case 'check'				:
					delete_transient($this -> pre . 'update_info');
					$this -> redirect($this -> referer);
					break;
			}
			
			$this -> render('settings-updates', false, true, 'admin');
		}
		
		/* Plugin Extensions Section */
		function admin_extensions() {
			switch ($_GET['method']) {
				case 'activate'				:
					activate_plugin(plugin_basename($_GET['plugin']));
					$this -> redirect($this -> url, 'message', __('Extension has been activated.', $this -> plugin_name));
					break;
				case 'deactivate'			:
					deactivate_plugins(array(plugin_basename($_GET['plugin'])));
					$this -> redirect($this -> url, 'error', __('Extension has been deactivated.', $this -> plugin_name));
					break;
				default						:
					$this -> render('extensions' . DS . 'index', false, true, 'admin');
					break;
			}
		}
		
		function admin_extensions_settings() {	
			$method = (!empty($_GET['method'])) ? $_GET['method'] : false;
		
			switch ($method) {
				default						:
					if (!empty($_POST)) {
						foreach ($_POST as $pkey => $pval) {
							$this -> update_option($pkey, $pval);
						}
					
						do_action($this -> pre . '_extensions_settings_saved', $_POST);
						$this -> render_message(__('Extensions settings have been saved.', $this -> plugin_name));
					}
				
					$this -> render('extensions' . DS . 'settings', false, true, 'admin');
					break;
			}
		}
		
		function admin_help() {
			$this -> render_admin('help');
		}
		
		function update_plugin_complete_actions($upgrade_actions = null, $plugin = null) {
			$this_plugin = plugin_basename(__FILE__);
			
			if (!empty($plugin) && $plugin == $this_plugin) {
				$this -> add_option('activation_redirect', true);
			}
			
			return $upgrade_actions;
		}
		
		function activation_hook() {
			$this -> ci_initialization();
			$this -> add_option('activation_redirect', true);
			//wp_redirect(admin_url('index.php') . "?page=newsletters-about");
		}
		
		function custom_redirect() {
			$activation_redirect = $this -> get_option('activation_redirect');
			
			if (is_admin() && !empty($activation_redirect)) {
				$this -> delete_option('activation_redirect');
				wp_redirect(admin_url('index.php') . "?page=newsletters-about");
			}
		}
		
		function wpMail($data = array()) {
			$url = explode("&", $_SERVER['REQUEST_URI']);
			$this -> fullurl = $_SERVER['REQUEST_URI'];
			$this -> url = $url[0];
			
			if (!empty($_SERVER['HTTP_REFERER'])) {
				$this -> referer = $_SERVER['HTTP_REFERER'];
			}
			
			$this -> plugin_file = plugin_basename(__FILE__);	
			$base = basename(dirname(__FILE__));
			$this -> register_plugin($base, __FILE__);		
			$url = explode("&", $_SERVER['REQUEST_URI']);
			$this -> url = $url[0];
		}
	}
}

/* Include the necessary class files */
require_once(dirname(__FILE__) . DS . 'models' . DS . 'mailinglist.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'subscriber.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'bounce.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'unsubscribe.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'latestpost.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'history.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'histories_list.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'histories_attachment.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'email.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'queue.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'theme.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'template.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'post.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'order.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'field.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'fields_list.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'subscribers_list.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'country.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'autoresponder.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'autoresponders_list.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'autoresponderemail.php');
require_once(dirname(__FILE__) . DS . 'models' . DS . 'group.php');
require_once(dirname(__FILE__) . DS . 'vendors' . DS . 'class.pagination.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'db.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'html.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'form.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'metabox.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'shortcode.php');
require_once(dirname(__FILE__) . DS . 'helpers' . DS . 'auth.php');

//initialize the wpMail class 
$wpMail = new wpMail();
require_once(dirname(__FILE__) . DS . 'wp-mailinglist-api.php');
require_once(dirname(__FILE__) . DS . 'wp-mailinglist-functions.php');
require_once(dirname(__FILE__) . DS . 'wp-mailinglist-widget.php');
register_activation_hook(plugin_basename(__FILE__), array($wpMail, 'activation_hook'));
//add_filter('update_plugin_complete_actions', array($wpMail, 'update_plugin_complete_actions'), 10, 2);
register_activation_hook(plugin_basename(__FILE__), array($wpMail, 'update_options'));

?>