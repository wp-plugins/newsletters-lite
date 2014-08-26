<?php

if (!class_exists('wpMailPlugin')) {
	class wpMailPlugin extends wpMailCheckinit {
	
		var $plugin_name = 'wp-mailinglist';
		var $name = 'wp-mailinglist';
		var $plugin_base;
		var $pre = 'wpml';	
		var $version = '4.1.2';
		var $debugging = false;			//set to "true" to turn on debugging
		var $debug_level = 2; 			//set to 1 for only database errors and var dump; 2 for PHP errors as well
		var $post_errors = array();
		var $intervals = array();
		var $menus = array();
		
		var $sections = array(
			'welcome'					=> 	"newsletters",
			'submitserial'				=>	"newsletters-submitserial",
			'send'						=>	"newsletters-create",
			'autoresponders'			=>	"newsletters-autoresponders",
			'autoresponderemails'		=>	"newsletters-autoresponderemails",
			'lists'						=>	"newsletters-lists",
			'groups'					=>	"newsletters-groups",
			'subscribers'				=>	"newsletters-subscribers",
			'fields'					=>	"newsletters-fields",
			'importexport'				=>	"newsletters-importexport",
			'themes'					=>	"newsletters-themes",
			'templates'					=>	"newsletters-templates",
			'templates_save'			=>	"newsletters-templates-save",
			'queue'						=>	"newsletters-queue",
			'history'					=>	"newsletters-history",
			'links'						=>	"newsletters-links",
			'orders'					=>	"newsletters-orders",
			'stats'						=>	"newsletters-statistics",
			'settings'					=>	"newsletters-settings",
			'settings_subscribers'		=>	"newsletters-settings-subscribers",
			'settings_templates'		=>	"newsletters-settings-templates",
			'settings_system'			=>	"newsletters-settings-system",
			'settings_tasks'			=>	"newsletters-settings-tasks",
			'settings_updates'			=>	"newsletters-settings-updates",
			'extensions'				=>	"newsletters-extensions",
			'extensions_settings'		=>	"newsletters-extensions-settings",
			'support'					=>	"newsletters-support",
		);
		
		var $extensions = array();
		
		var $classes = array(
			'Subscriber',
			'Bounce',
			'Unsubscribe',
			'Mailinglist',
			'Queue',
			'Latestpost',
			'FieldsList',
			'Field',
			'History',
			'HistoriesList',
			'HistoriesAttachment',
			'Email',
			'wpmlOrder',
			'Post',
			'Theme',
			'Template',
			'SubscribersList',
			'wpmlCountry',
			'Autoresponder',
			'AutorespondersList',
			'Autoresponderemail',
			'wpmlGroup',
		);
		
		var $models = array('Link', 'Click', 'Content');
		
		var $helpers = array('Checkinit', 'Db', 'Html', 'Form', 'Metabox', 'Shortcode', 'Auth');	
		var $tables = array();
		var $tablenames = array();
		
		/**
		 * Register the plugin
		 * Sets the plugin name and base directory for universal use.
		 * @param STRING. Name of the plugin
		 * @param STRING. Base directory of the plugin
		 *
		 */
		function register_plugin($name = null, $base = null) {
			$this -> plugin_name = $name;
			$this -> plugin_base = rtrim(dirname($base), DS);
			define("NEWSLETTERS_LOG_FILE", $this -> plugin_base() . DS . "newsletters.log");
			$this -> sections = (object) $this -> sections;
			$this -> set_timezone();
			$this -> extensions = $this -> get_extensions();
					
			### Get our models ready for action!
			$this -> initialize_classes();
			
			global $wpdb;
			$wpdb -> query("SET sql_mode = '';");
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;
			$this -> debugging($this -> debugging);
			add_action('plugins_loaded', array($this, 'ci_initialize'));
			
			if (is_admin()) {
				global $extensions;
				require_once($this -> plugin_base() . DS . 'includes' . DS . 'extensions.php');
				$this -> extensions = $extensions;
			}
		}
		
		function media_insert($html = null, $id = null, $attachment = null) {
			$align = get_post_meta($id, '_wpmlalign', true);
			$hspace = get_post_meta($id, '_wpmlhspace', true);
			
			if (!empty($align) || !empty($hspace)) {
				$dom = new DOMDocument();
				$dom -> loadHTML($html);
				
				foreach ($dom -> getElementsByTagName('img') as $img) {				
					if (!empty($align) && $align != "none") {
						$img -> setAttribute('align', $align);
					}
					
					if (!empty($hspace)) {
						$img -> setAttribute('hspace', $hspace);
					}
				}
				
				$html = $dom -> saveHTML();
				$html = trim(preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $html));
			}
			
			return $html;
		}
		
		function attachment_fields_to_save($post = null, $attachment = null) {
		
			if (!empty($attachment[$this -> pre . 'align'])) {
				update_post_meta($post['ID'], '_wpmlalign', $attachment[$this -> pre . 'align']);
			} else {
				delete_post_meta($post['ID'], '_wpmlalign');
			}
			
			if (!empty($attachment[$this -> pre . 'hspace'])) {
				update_post_meta($post['ID'], '_wpmlhspace', $attachment[$this -> pre . 'hspace']);
			} else {
				delete_post_meta($post['ID'], '_wpmlhspace');
			}
			
			return $post;
		}
		
		function attachment_fields_to_edit($form_fields = null, $post = null) {		
			$align = get_post_meta($post -> ID, "_wpmlalign", true);
			$hspace = get_post_meta($post -> ID, "_wpmlhspace", true);
		
	        $html = '<label for="attachments_' . $post -> ID . '_wpmlalign_none"><input ' . ((empty($align) || (!empty($align) && $align == "none")) ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="none" id="attachments_' . $post -> ID . '_wpmlalign_none" /> ' . __('None', $this -> plugin_name) . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_left"><input ' . ((!empty($align) && $align == "left") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="left" id="attachments_' . $post -> ID . '_wpmlalign_left" /> ' . __('Left', $this -> plugin_name) . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_right"><input ' . ((!empty($align) && $align == "right") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="right" id="attachments_' . $post -> ID . '_wpmlalign_right" /> ' . __('Right', $this -> plugin_name) . '</label>';
	
	        $form_fields['wpmlalign'] = array(
	            'label' =>  __('Email Align', $this -> plugin_name),
	            'input' =>  'html',
	            'html'	=>	$html,
	            'value'	=>	$align,
	        );
	        
	        $form_fields['wpmlhspace'] = array(
	        	'label'	=>	__('Email Hspace', $this -> plugin_name),
	        	'input'	=>	'html',
	        	'html'	=>	'<input type="text" style="width:45px;" class="widefat" name="attachments[' . $post -> ID . '][wpmlhspace]" value="' . $hspace . '" id="attachments_' . $post -> ID . '_wpmlhspace" /> px',
	        	'value'	=>	$hspace,
	        );
		
		    return $form_fields;
		}
		
		function debugging($debug = false) {
			global $wpdb;
		
			if (!empty($debug) && $debug == true) {
				if (!defined('WP_DEBUG')) { define('WP_DEBUG', true); }
				$wpdb -> show_errors();
				
				if (!empty($this -> debug_level) && $this -> debug_level == 2) {
					error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
					@ini_set('display_errors', 1);
				}
			} else {
				$wpdb -> hide_errors();
				error_reporting(0);
				@ini_set('display_errors', 0);
			}
		}
		
		function after_plugin_row($plugin_name = null) {
	        $key = $this -> get_option('serialkey');
	        $update = $this -> vendor('update');
	        $version_info = $update -> get_version_info();
	        
	        if (!empty($version_info) && $version_info['is_valid_key'] == "0") {
		        echo '<tr class="plugin-update-tr">';
		        echo '<td colspan="3" class="plugin-update">';
		        echo '<div class="update-message">';
		        echo sprintf('Your download for the Newsletter plugin has expired, please <a href="%s" target="_blank">renew it</a> for updates!', $version_info['url']);
		        echo '</div>';
		        echo '</td>';
		        echo '</tr>';
	        }
	        
	        if (false && $this -> has_update()) {
	        	$new_version = __('There is a new version of the Newsletter plugin available.', $this -> plugin_name) .' <a class="thickbox" title="Newsletter plugin" href="plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', $this -> plugin_name), $version_info["version"]) . '</a>';
	        	$plugin_file = "wp-mailinglist/wp-mailinglist.php";
	        	$upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_name), 'upgrade-plugin_' . $plugin_file);
	        	$upgrade = sprintf(__('or <a href="%s">update automatically</a>.', $this -> plugin_name), $upgrade_url);
	            echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $new_version . ' ' . $upgrade . '</div></td>';	
	        }
	    }
	    
		/**
		 * This function outputs the changelog on the 'Plugins' page when the "View Details" link is clicked.
		 */
	    function display_changelog() {  	
	    	if (!empty($_GET['plugin']) && $_GET['plugin'] == $this -> plugin_name) {			
		    	$update = $this -> vendor('update');
		    	if ($changelog = $update -> get_changelog()) {				
					$this -> render('changelog', array('changelog' => $changelog), true, 'admin');
		    	}
		    	
		    	exit();
	    	}
	    }
		
		function has_update($cache = true) {
			$update = $this -> vendor('update');
	        $version_info = $update -> get_version_info($cache);
	        return version_compare($this -> version, $version_info["version"], '<');
	    }
		
		function check_update($option, $cache = true) {
			$update = $this -> vendor('update');
	        $version_info = $update -> get_version_info($cache);
	
	        if (!$version_info) { return $option; }
	        $plugin_path = 'wp-mailinglist/wp-mailinglist.php';
	        
	        if(empty($option -> response[$plugin_path])) {
				$option -> response[$plugin_path] = new stdClass();
	        }
	
	        //Empty response means that the key is invalid. Do not queue for upgrade
	        if(!$version_info["is_valid_key"] || version_compare($this -> version, $version_info["version"], '>=')){
	            unset($option -> response[$plugin_path]);
	        } else {
	            $option -> response[$plugin_path] -> url = "http://tribulant.com";
	            $option -> response[$plugin_path] -> slug = $this -> plugin_name;
	            $option -> response[$plugin_path] -> package = $version_info['url'];
	            $option -> response[$plugin_path] -> new_version = $version_info["version"];
	            $option -> response[$plugin_path] -> id = "0";
	        }
	
	        return $option;
	    }
	    
	    function ajax_getposts() {
	    	define('DOING_AJAX', true);
			define('SHORTINIT', true);
	    
		    if (!empty($_POST)) {
			    $arguments = array(
			    	'numberposts'			=>	"-1",
			    	'orderby'				=>	"post_title",
			    	'order'					=>	"ASC",
			    	'post_type'				=>	"post",
			    	'post_status'			=>	"publish",
			    );
			    
			    if (!empty($_POST['posttype'])) { $arguments['post_type'] = $_POST['posttype']; }
			    if (!empty($_POST['category'])) { $arguments['category'] = $_POST['category']; }
			    
			    if ($posts = get_posts($arguments)) {
				    ?>
				    
				    <ul class="insertfieldslist">
				    	<li>
				    		<span class="insertfieldslistcheckbox">
				    			<input onclick="jqCheckAll(this, false, 'insertposts');" type="checkbox" name="checkall_insertposts" id="checkall_insertposts" value="1" />
				    		</span>
				    		<span class="">
				    			<label for="checkall_insertposts" style="font-weight:bold;"><?php _e('Select All', $this -> plugin_name); ?></label>
				    		</span>
				    	</li>
				    	<?php foreach ($posts as $post) : ?>
				    		<li>
				    			<span class="insertfieldslistcheckbox"><input type="checkbox" name="insertposts[]" value="<?php echo $post -> ID; ?>" id="insertposts_<?php echo $post -> ID; ?>" /></span>
				    			
				    			<?php if ($this -> is_plugin_active('qtranslate')) : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo $post -> ID; ?>', false); return false;" class="button button-secondary press"><?php echo qtrans_use($_POST['language'], $post -> post_title, false); ?></a></span>
				    			<?php else : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo $post -> ID; ?>', false); return false;" class="button button-secondary press"><?php echo __($post -> post_title); ?></a></span>
				    			<?php endif; ?>
				    		</li>
				    	<?php endforeach; ?>
				    </ul>
				    
				    <span><input onclick="insert_single_multiple();" class="button button-primary" type="button" name="insert" value="<?php _e('Insert Selected', $this -> plugin_name); ?>" /></span>
				    
				    <?php
			    }
		    }
		    
		    exit();
		    die();
	    }
	    
	    function ajax_welcomestats() {
	    	define('DOING_AJAX', true);
			define('SHORTINIT', true);
	    
		    global $wpdb, $Html, $Subscriber, $Email, $Bounce, $Unsubscribe;
		    
		    $type = (empty($_GET['type'])) ? "days" : $_GET['type'];
			$fromdate = (empty($_GET['from'])) ? date("Y-m-d", strtotime("-31 days")) : $_GET['from'];
			$todate = (empty($_GET['to'])) ? date("Y-m-d", time()) : $_GET['to'];
			
			switch ($type) {
				case 'years'			:
					//$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `created` > ADDDATE(CURDATE(), INTERVAL -31 DAY) GROUP BY DATE(`created`)";
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $subscribers_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("Y", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $emails_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("Y", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $bounces_array = array();
				    if ($records = $wpdb -> get_results($query)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("Y", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $unsubscribes_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("Y", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$yearsdiff = round(abs($tostamp - $fromstamp) / (60 * 60 * 24 * 365));
				    
				    $j = 0;
				    for ($i = 0; $i <= $yearsdiff; $i++) {
				    	$datestring = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    $dates_data[$j] = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    
					    if (!empty($subscribers_array[$datestring])) {
						    $subscribers_data[$j] = $subscribers_array[$datestring];
					    } else {
						    $subscribers_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
					
					include_once($this -> plugin_base() . DS . 'vendors' . DS . 'ofc' . DS . 'open-flash-chart.php');
					$g = new graph();
					
					/* Unsubscribes */
					$g -> set_data($unsubscribes_data);
					$g -> line_hollow(3, 5, '#e66f00', 'Unsubscribes', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');		
					
					/* Subscribers */
					$g -> set_data($subscribers_data);
					$g -> line_hollow(3, 5, '#629632', 'Subscribers', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Bounced Emails */
					$g -> set_data($bounces_data);
					$g -> line_hollow(3, 5, '#CC0000', 'Bounced Emails', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Emails Sent */
					$g -> set_data($emails_data);
					$g -> line_hollow(3, 5, '#5FB7DD', 'Emails Sent', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					$g -> attach_to_y_right_axis(1);
					$g -> attach_to_y_right_axis(3);
					$g -> attach_to_y_right_axis(4);
					
					$g -> set_x_labels($dates_data);
					$g -> set_x_label_style(10, '#000000', 0, 1, '#E4F5FC');
					$g -> set_y_max($Html -> RoundUptoNearestN($y_max));
					$g -> set_y_right_max($Html -> RoundUptoNearestN($y_right_max));
					$g -> y_label_steps(5);
					$g -> set_inner_background('#FFFFFF', '#FFFFFF', 90);
					$g -> x_axis_colour('#333333', '#E4F5FC');
					$g -> y_axis_colour('#333333', '#E4F5FC');
					$g -> y_right_axis_colour('#333333', '#E4F5FC');
					$g -> set_y_legend(__('Subscribers', $this -> plugin_name), 12, '#164166' );
					$g -> set_y_right_legend(__('Emails', $this -> plugin_name), 12, '#164166');
					
					$g -> set_is_decimal_separator_comma(false);
					$g -> set_is_thousand_separator_disabled(true);
					$g -> bg_colour = "#FFFFFF";
					echo $g -> render();
					break;
				case 'months'			:
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
				    $subscribers_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("mY", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
				    $emails_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("mY", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $bounces_array = array();
				    if ($records = $wpdb -> get_results($query)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("mY", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $unsubscribes_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("mY", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$monthsdiff = round(abs($tostamp - $fromstamp) / 2628000);
				    
				    $j = 0;
				    for ($i = 0; $i <= $monthsdiff; $i++) {
				    	$datestring = date_i18n("mY", strtotime("-" . $i . " months", $tostamp));
					    $dates_data[$j] = date_i18n("F Y", strtotime("-" . $i . " months", $tostamp));
					    
					    if (!empty($subscribers_array[$datestring])) {
						    $subscribers_data[$j] = $subscribers_array[$datestring];
					    } else {
						    $subscribers_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
					
					include_once($this -> plugin_base() . DS . 'vendors' . DS . 'ofc' . DS . 'open-flash-chart.php');
					$g = new graph();
					
					/* Unsubscribes */
					$g -> set_data($unsubscribes_data);
					$g -> line_hollow(3, 5, '#e66f00', 'Unsubscribes', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');		
					
					/* Subscribers */
					$g -> set_data($subscribers_data);
					$g -> line_hollow(3, 5, '#629632', 'Subscribers', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Bounced Emails */
					$g -> set_data($bounces_data);
					$g -> line_hollow(3, 5, '#CC0000', 'Bounced Emails', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Emails Sent */
					$g -> set_data($emails_data);
					$g -> line_hollow(3, 5, '#5FB7DD', 'Emails Sent', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					$g -> attach_to_y_right_axis(1);
					$g -> attach_to_y_right_axis(3);
					$g -> attach_to_y_right_axis(4);
					
					$g -> set_x_labels($dates_data);
					$g -> set_x_label_style(10, '#000000', 0, 2, '#E4F5FC');
					$g -> set_y_max($Html -> RoundUptoNearestN($y_max));
					$g -> set_y_right_max($Html -> RoundUptoNearestN($y_right_max));
					$g -> y_label_steps(5);
					$g -> set_inner_background('#FFFFFF', '#FFFFFF', 90);
					$g -> x_axis_colour('#333333', '#E4F5FC');
					$g -> y_axis_colour('#333333', '#E4F5FC');
					$g -> y_right_axis_colour('#333333', '#E4F5FC');
					$g -> set_y_legend(__('Subscribers', $this -> plugin_name), 12, '#164166' );
					$g -> set_y_right_legend(__('Emails', $this -> plugin_name), 12, '#164166');
					
					$g -> set_is_decimal_separator_comma(false);
					$g -> set_is_thousand_separator_disabled(true);
					$g -> bg_colour = "#FFFFFF";
					echo $g -> render();
					break;
				case 'days'				:
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
				    $subscribers_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("m-d", strtotime($record -> date))] = $record -> subscriberscount; 
							
							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
				    $emails_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("m-d", strtotime($record -> date))] = $record -> emailscount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $bounces_array = array();
				    if ($records = $wpdb -> get_results($query)) {	    
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("m-d", strtotime($record -> date))] = $record -> bouncecount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }
				    
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $unsubscribes_array = array();
				    if ($records = $wpdb -> get_results($query)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("m-d", strtotime($record -> date))] = $record -> unsubscribescount; 
							
							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }
				    
				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    
				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$daysdiff = round(abs($tostamp - $fromstamp) / 86400);
				    
				    $j = 0;
				    for ($i = 0; $i <= $daysdiff; $i++) {
				    	$datestring = date_i18n("m-d", strtotime("-" . $i . " days", $tostamp));
					    $dates_data[$j] = date_i18n("M j", strtotime("-" . $i . " days", $tostamp));
					    
					    if (!empty($subscribers_array[$datestring])) {
						    $subscribers_data[$j] = $subscribers_array[$datestring];
					    } else {
						    $subscribers_data[$j] = 0;
					    }
					    
					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }
					    
					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }
					    
					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }
					    
					    $j++;
				    }
				    
				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
					
					include_once($this -> plugin_base() . '/vendors/ofc/open-flash-chart.php');
					$g = new graph();
					
					/* Unsubscribes */
					$g -> set_data($unsubscribes_data);
					$g -> line_hollow(3, 5, '#e66f00', 'Unsubscribes', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');		
					
					/* Subscribers */
					$g -> set_data($subscribers_data);
					$g -> line_hollow(3, 5, '#629632', 'Subscribers', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Bounced Emails */
					$g -> set_data($bounces_data);
					$g -> line_hollow(3, 5, '#CC0000', 'Bounced Emails', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					/* Emails Sent */
					$g -> set_data($emails_data);
					$g -> line_hollow(3, 5, '#5FB7DD', 'Emails Sent', 10);
					$g -> set_tool_tip('#x_label#<br>#key#: #val#');
					
					$g -> attach_to_y_right_axis(1);
					$g -> attach_to_y_right_axis(3);
					$g -> attach_to_y_right_axis(4);
					
					$g -> set_x_labels($dates_data);
					$g -> set_x_label_style(10, '#000000', 0, 4, '#E4F5FC');
					$g -> set_y_max($Html -> RoundUptoNearestN($y_max));
					$g -> set_y_right_max($Html -> RoundUptoNearestN($y_right_max));
					$g -> y_label_steps(5);
					$g -> set_inner_background('#FFFFFF', '#FFFFFF', 90);
					$g -> x_axis_colour('#333333', '#E4F5FC');
					$g -> y_axis_colour('#333333', '#E4F5FC');
					$g -> y_right_axis_colour('#333333', '#E4F5FC');
					$g -> set_y_legend(__('Subscribers', $this -> plugin_name), 12, '#164166' );
					$g -> set_y_right_legend(__('Emails', $this -> plugin_name), 12, '#164166');
					
					$g -> set_is_decimal_separator_comma(false);
					$g -> set_is_thousand_separator_disabled(true);
					$g -> bg_colour = "#FFFFFF";
					echo $g -> render();
					break;
			}
		    
		    exit();
		    die();
	    }
		
		function ajax_setvariables() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$this -> render('setvariables', array('noinsert' => true), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_executemail() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $History, $HistoriesAttachment;
			$subscriber = (object) maybe_unserialize(stripslashes($_REQUEST['subscriber']));
			
			if (!empty($_REQUEST)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $History -> table . " WHERE id = '" . $_REQUEST['history_id'] . "' LIMIT 1";
			
				if ($history = $wpdb -> get_row($historyquery)) {
					$subscriber_request = (object) maybe_unserialize(stripslashes($_REQUEST['subscriber']));								
					$subscriber = $Subscriber -> get($subscriber_request -> id, false);
					$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
					$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
					$content = $history -> message;
					$subject = $history -> subject;
					$history_id = $_REQUEST['history_id'];
					$post_id = $_REQUEST['post_id'];
					$theme_id = $_REQUEST['theme_id'];
					$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history_id . date_i18n("YmdH", time()));
					$shortlinks = true;
					
					$newattachments = array();
					$Db -> model = $HistoriesAttachment -> model;
					if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
						foreach ($attachments as $attachment) {
							$newattachments[] = array(
								'id'					=>	$attachment -> id,
								'title'					=>	$attachment -> title,
								'filename'				=>	$attachment -> filename,
							);	
						}
					}
					
					$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $theme_id, true);
					
					if ($this -> execute_mail($subscriber, false, $subject, $message, $newattachments, $history_id, $eunique)) {
						$success = "Y<|>" . $subscriber -> email . "<|>" . __('Success', $this -> plugin_name);
					} else {
						global $mailerrors;
						$success = "N<|>" . $subscriber -> email . "<|>" . strip_tags($mailerrors);
					}
				} else {
					$success = "N<|>" . $subscriber -> email . "<|>" . __('History email could not be read', $this -> plugin_name);
				}
			} else {
				$success = "N<|>" . $subscriber -> email . "<|>" . __('No data was posted', $this -> plugin_name);
			}
			
			echo $success;
		
			exit();
			die();
		}
		
		function ajax_queuemail() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $Queue, $History, $HistoriesAttachment;
			$subscriber = (object) maybe_unserialize(stripslashes($_REQUEST['subscriber']));
			
			if (!empty($_REQUEST)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $History -> table . " WHERE id = '" . $_REQUEST['history_id'] . "' LIMIT 1";
			
				if ($history = $wpdb -> get_row($historyquery)) {
					$subscriber_request = (object) maybe_unserialize(stripslashes($_REQUEST['subscriber']));								
					$subscriber = $Subscriber -> get($subscriber_request -> id, false);
					$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
					$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
					$content = $history -> message;
					$subject = $history -> subject;
					$history_id = $_REQUEST['history_id'];
					$post_id = $_REQUEST['post_id'];
					$theme_id = $_REQUEST['theme_id'];
					$senddate = $_REQUEST['senddate'];
					$eunique = md5($subscriber -> id . $subscriber -> mailinglist_id . $history_id . date_i18n("YmdH", time()));
					$shortlinks = true;
					
					$newattachments = array();
					$Db -> model = $HistoriesAttachment -> model;
					if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
						foreach ($attachments as $attachment) {
							$newattachments[] = array(
								'id'					=>	$attachment -> id,
								'title'					=>	$attachment -> title,
								'filename'				=>	$attachment -> filename,
							);	
						}
					}
					
					if ($Queue -> save($subscriber, false, $subject, $content, $newattachments, $post_id, $history_id, false, $theme_id, $senddate)) {
						$success = "Y<|>" . $subscriber -> email . "<|>" . __('Success', $this -> plugin_name);
					} else {
						$success = "N<|>" . $subscriber -> email . "<|>" . $Queue -> errors[0];
					}
				} else {
					$success = "N<|>" . $subscriber -> email . "<|>" . __('History email could not be read', $this -> plugin_name);
				}
			} else {
				$success = "N<|>" . $subscriber -> email . "<|>" . __('No data was posted', $this -> plugin_name);
			}
			
			echo $success;
		
			exit();
			die();
		}
		
		function ajax_exportsubscribers() {		
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Html;
			$exportfilename = $_REQUEST['exportfile'];
			$exportfilepath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
			$exportfilefull = $exportfilepath . $exportfilename;
			
			if ($fh = fopen($exportfilefull, "a")) {
				$subscriberrequest = @unserialize(stripslashes($_REQUEST['subscriber']));
				$row = '"' . implode('","', $subscriberrequest) . '"' . "\r\n";
				fwrite($fh, $row, 1024);
			}
			
			echo $subscriberrequest['email'];
			
			exit();
			die();
		}
		
		function ajax_importsubscribers() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Queue, $Subscriber, $SubscribersList;
		
			if (!empty($_REQUEST['subscriber'])) {
				$subscriber = maybe_unserialize(stripslashes($_REQUEST['subscriber']));				
				$subscriber['justsubscribe'] = true;
				$email = $subscriber['email'];
				$confirmation_subject = stripslashes($_REQUEST['confirmation_subject']);
				$confirmation_email = stripslashes($_REQUEST['confirmation_email']);
				
				if (!empty($subscriber)) {
					$subscriber['fromregistration'] = true;
					$subscriber['username'] = $email;
				
					if ($Subscriber -> save($subscriber, true, false)) {
						$subscriber_id = $Subscriber -> insertid;
						$afterlists = $subscriber['afterlists'];
					
						if (!empty($afterlists)) {
							foreach ($afterlists as $mailinglist) {
								$sl_data = array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist['id'], 'paid' => $mailinglist['paid'], 'active' => $mailinglist['active']);
								$sl_query = $SubscribersList -> save($sl_data, false, true);
								
								if (!empty($sl_query)) {
									$wpdb -> query($sl_query);
								}
							}
							
							if ($subscriber['active'] == "N") {	
								if (!empty($subscriber['mailinglists'])) {	
									$allmailinglists = $subscriber['mailinglists'];
									$Db -> model = $Subscriber -> model;
									$subscriber = $Db -> find(array('id' => $subscriber_id));
								
									foreach ($allmailinglists as $mailinglist_id) {						
										$subscriber -> mailinglist_id = $mailinglist_id;
										$subject = $confirmation_subject;
										$message = $confirmation_email;
										
										$Queue -> save(
											$subscriber,
											false, 
											$subject, 
											$message, 
											false, 
											false, 
											false, 
											false, 
											$this -> default_theme_id('system'), 
											false
										);
									}
								}
							}
						}
						
						$success = "Y<|>" . $email;
						$message = __('Subscriber was imported.', $this -> plugin_name);
					} else {
						$success = "N<|>" . $email;
						$message = implode(" | ", $Subscriber -> errors);
					}
				} else {
					$success = "N<|>" . $email;
					$message = __('No subscriber data is available.', $this -> plugin_name);
				}
			} else {
				$success = "N<|>" . $email;
				$message = __('No data was posted.', $this -> plugin_name);
			}
			
			echo $success . "<|>" . $message;
			
			exit();
			die();
		}
		
		function ajax_subscribe() {			
			global $Subscriber, $Mailinglist, $Html;
			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$widget_id = $_GET['widget_id'];
			$number = $_GET['number'];
			$instance = $this -> widget_instance($number);
			
			if (!empty($_POST['instance'])) {
				$r = wp_parse_args($_POST['instance'], $instance);
				$instance = $r;
			}
			
			$action = ($this -> is_plugin_active('qtranslate')) ? qtrans_convertURL($_SERVER['REQUEST_URI'], $instance['language']) : $_SERVER['REQUEST_URI'];
			$action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;
			
			if ($subscriber_id = $Subscriber -> optin($_POST)) {
				echo '<p class="newsletters-acknowledgement">' . __($instance['acknowledgement']) . '</p>';
				
				if ($paidlist_id = $Mailinglist -> has_paid_list($_POST['list_id'])) {
					$subscriber = $Subscriber -> get($subscriber_id, false);
					$paidlist = $Mailinglist -> get($paidlist_id, false);
					$this -> paidsubscription_form($subscriber, $paidlist, true);
				}
			
				if ($this -> get_option('subscriberedirect') == "Y") {
					$this -> redirect($this -> get_option('subscriberedirecturl'), false, false, true);
				}
			} else {			
				$errors = $Subscriber -> errors;
				$this -> render('widget', array('action' => $action, 'widget' => $_GET['widget'], 'errors' => $errors, 'args' => $widget, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), true, 'default');
			}
		
			exit();
			die();
		}
		
		function widget_object() {
			$widget = new Newsletters_Widget();
			return $widget;
		}
		
		function widget_settings() {
			$widget = new Newsletters_Widget();
			$settings = $widget -> get_settings();
			return $settings;
		}
		
		function widget_instance($number = null, $atts = array()) {
			if (!empty($number)) {
				$widget = new Newsletters_Widget();
				$settings = $widget -> get_settings();
				
				if (!empty($settings[$number])) {
					$instance = $settings[$number];
					
					if ($this -> is_plugin_active('qtranslate')) {
						$instance['lang'] = qtrans_getLanguage();
					}
				} else {
					if ($embed = $this -> get_option('embed')) {
						$instance = wp_parse_args($atts, $embed);
						
						if (empty($instance['list'])) {
							if ($instance['type'] == "list") {
								$instance['list'] = $instance['id'];
							} else {
								$instance['list'] = $instance['type'];
								if (empty($instance['lists']) && !empty($instance['id'])) {
									$instance['lists'] = $instance['id'];
								}
							}
						}
						
						unset($instance['type']);
						unset($instance['id']);
					}
				}
				
				return $instance;
			}
			
			return false;
		}
		
		function ajax_getlistfields() {		
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $FieldsList;
			$widget_id = $_GET['widget_id'];
			$instance = $_POST['instance'];
			
			if ($fields = $FieldsList -> fields_by_list($_POST['list_id'], "order", "ASC")) {		
				foreach ($fields as $field) {				
					$this -> render_field($field -> id, true, $widget_id, true, true, $instance);
				}
			}
			
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-checkbox').on('click', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
				jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-select').on('change', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
			});
			</script>
			
			<?php 
			
			exit();
			die();
		}
		
		function ajax_testsettings() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Subscriber;
			$errors = array();
			$success = false;
			
			if (!empty($_GET['init']) && !empty($_POST)) {		
				foreach ($_POST as $pkey => $pval) {
					$this -> update_option($pkey, $pval);
				}
			}
			
			if (empty($_GET['init']) && !empty($_POST)) {		
				if (empty($_POST['testemail'])) { $errors[] = __('Please fill in an email address', $this -> plugin_name); }
				elseif (!$Subscriber -> email_validate($_POST['testemail'])) { $errors[] = __('Please fill in a valid email address', $this -> plugin_name); }
				if (empty($_POST['subject'])) { $errors[] = __('Please fill in a subject', $this -> plugin_name); }
				if (empty($_POST['message'])) { $errors[] = __('Please fill in a message', $this -> plugin_name); }
				
				if (empty($errors)) {
					$subscriber = null;
					$subscriber -> email = $_POST['testemail'];
					$subject = $_POST['subject'];
					$message = $_POST['message'];
					
					$attachments = false;
					if (!empty($_POST['testattachment']) && $_POST['testattachment'] == 1) {
						$attachments = array(
							array(
								'filename'				=>	$this -> plugin_base() . DS . 'images' . DS . 'wp-mailinglist.jpg',
							)
						);
					}
					 
					if ($this -> execute_mail($subscriber, false, $subject, $message, $attachments, false, false, false)) {
						$success = true;
						$errors[] = __('Email was successfully sent, your settings are working!', $this -> plugin_name);	
					} else {
						global $mailerrors;
						$errors[] = $mailerrors;
					}
				}
			}
			
			echo '<div id="testsettingswrapper">';
			$this -> render('testsettings', array('errors' => $errors, 'success' => $success), true, 'admin');
			echo '</div>';
			
			exit();	
			die();
		}
		
		function ajax_dkimwizard() {			
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			switch ($_POST['goto']) {
				case 'step2'				:
					$this -> render('dkim' . DS . 'step2', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $_POST['public'], 'private' => $_POST['private']), true, 'admin');
					break;
				case 'step3'				:
					$this -> render('dkim' . DS . 'step3', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $_POST['public'], 'private' => $_POST['private']), true, 'admin');
					break;
				case 'step1'				:
				default 					:
					require_once $this -> plugin_base() . DS . 'vendors' . DS . 'dkim' . DS . 'Crypt' . DS . 'RSA.php';
					$rsa = new Crypt_RSA();
					$keys = $rsa -> createKey();					
					$this -> render('dkim' . DS . 'step1', array('domain' => $_POST['domain'], 'selector' => $_POST['selector'], 'public' => $keys['publickey'], 'private' => $keys['privatekey']), true, 'admin');
					break;
			}
			
			exit();
			die();
		}
		
		function ajax_testbouncesettings() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
	        require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.pop3_new.php');
	        $pop3 = new POP3();
	        
	        if ($pop3 -> connect($_POST['host'], $_POST['port'])) {
	        	if ($pop3 -> user($_POST['user'])) {
	        		$count = $pop3 -> pass($_POST['pass']);
	        		
	        		if ($count === false) {
		        		$success = false;
						$error = $pop3 -> ERROR;
	        		} elseif ($count === 0) {
		        		$success = true;
		        		$message = __('There are no emails in the mailbox', $this -> plugin_name);
	        		} elseif ($count > 0) {
		        		$success = true;
		        		$message = sprintf(__('There are %s emails in the mailbox', $this -> plugin_name), $count);
	        		}
	        	} else {
		        	$success = false;
		        	$error = $pop3 -> ERROR;
	        	}
	        } else {
	        	$success = false;
		        $error = $pop3 -> ERROR;
	        }
	        
	        $pop3 -> quit();	        
	        $this -> render('testbouncesettings', array('success' => $success, 'message' => $message, 'error' => $error), true, 'admin');
		
			exit();
			die();
		}
		
		function ajax_deletecontentarea() {
			
			if (!empty($_POST['number']) && !empty($_POST['history_id'])) {
				$this -> Content -> delete_all(array('number' => $_POST['number'], 'history_id' => $_POST['history_id']));
			}
			
			exit();
			die();
		}
		
		function ajax_themeedit() {
			global $Db, $Theme;
			$success = false;
			$errors = array();
			
			if (!empty($_REQUEST)) {
				if (!empty($_REQUEST['id'])) {
					if (!empty($_REQUEST['Theme'])) {
						$Db -> model = $Theme -> model;
						if ($Db -> save($_REQUEST)) {
							$success = true;
						} else {
							$errors = $Theme -> errors;
						}
					}
				
					$Db -> model = $Theme -> model;
					$Db -> find(array('id' => $_GET['id']));
					$Theme -> data -> paste = $Theme -> data -> content;
				} else {
					$errors[] = __('No theme was specified', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was specified', $this -> plugin_name);
			}
			
			$this -> render('themes' . DS . 'save-ajax', array('success' => $success, 'errors' => $errors), true, 'admin');
			
			exit();
			die();
		}
		
		function ajax_addcontentarea() {		
			?>
			
			<div class="postbox">
				<h3 class="hndle"><span><?php echo sprintf(__('Content Area %s', $this -> plugin_name), $_POST['contentarea']); ?></span></h3>
				<div class="inside">
					<?php 
					
					$settings = array(
						'wpautop'			=>	false,
						'media_buttons'		=>	true,
						'textarea_name'		=>	'contentarea[' . $_POST['contentarea'] . ']',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
					);
					
					wp_editor(false, 'contentarea' . $_POST['contentarea'], $settings); 
					
					?>
				</div>
			</div>
			
			<?php
			
			exit();
			die();
		}
		
		function ajax_previewrunner() {
	    	global $wpdb, $Db, $History;
	    	define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
	    	
	    	if (empty($_POST['content'])) { exit(); }
	    	if (empty($_POST['subject']) || $_POST['subject'] == __('Enter email subject here', $this -> plugin_name)) { exit(); }
	    	
	    	ob_start();
	    	$history_data = array(
				'subject'			=>	$_POST['subject'],
				'message'			=>	$_POST['content'],
				'theme_id'			=>	$_POST['theme_id'],
				'conditions'		=>	serialize($_POST['fields']),
				'mailinglists'		=>	serialize($_POST['mailinglists']),
				'groups'			=>	serialize($_POST['groups']),
				'senddate'			=>	$_POST['senddate'],
				'scheduled'			=>	$_POST['scheduled'],
			);
			
			if (!empty($_POST['ishistory'])) { 
				$history_data['id'] = $_POST['ishistory']; 
				$Db -> model = $History -> model;
				$history_curr = $Db -> find(array('id' => $history_data['id']));
				$history_data['sent'] = $history_curr -> sent;
			}
			
			if (!empty($_POST['sendrecurring'])) {
				if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
					$history_data['recurring'] = "Y";
					$history_data['recurringvalue'] = $_POST['sendrecurringvalue'];
					$history_data['recurringinterval'] = $_POST['sendrecurringinterval'];
					
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
					foreach ($_POST['contentarea'] as $number => $content) {
						$content_data = array(
							'number'			=>	$number,
							'history_id'		=>	$history_id,
							'content'			=>	$content,
						);
					
						$this -> Content -> save($content_data, true);
					}	
				}
			}
			
			$history_id = $History -> insertid;
	    	$_GET['id'] = $history_id;
	    	ob_get_clean();
	    	$preview = $this -> ajax_historyiframe(true);
	    	
	    	header("Content-Type: text/xml; charset=UTF-8");
	    	
	    	?>
	    	
	    	<result>
				<history_id><?php echo $history_id; ?></history_id>
				<previewcontent><![CDATA[<?php echo $preview; ?>]]></previewcontent>
			</result>
	    	
	    	<?php
		    
		    exit();
		    die();
	    }
	    
	    function ajax_spamscorerunner() {
	    	global $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
	    	$newsletters_presend = true;
	    	$subscriber_id = $Subscriber -> admin_subscriber_id();
	    	$subscriber = $Subscriber -> get($subscriber_id, false);
	    	$subject = $_POST['subject'];
	    	$history_id = $_POST['ishistory'];
			$message = $this -> render_email('send', array('message' => $_POST['content'], 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, $_POST['theme_id']);
			$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, false, false);
			$spamscore = $this -> spam_score($newsletters_emailraw, "long");
			
			$output = "";
			ob_start();
			?>
			
			<p style="text-align:center;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:true, href:'#spamscore_report'}); return false;"><?php _e('See Report', $this -> plugin_name); ?></a></p>
			<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo admin_url('admin-ajax.php'); ?>?action=newsletters_gauge&value=<?php echo $spamscore -> score; ?>"></iframe>
			
			<div style="display:none;">
				<div id="spamscore_report">
					<div class="wrap newsletters">
						<h2><?php _e('Spam Score Report', $this -> plugin_name); ?></h2>
						<h3><?php _e('Report', $this -> plugin_name); ?></h3>
						<p><?php echo nl2br($spamscore -> report); ?></p>
						<h3><?php _e('RAW Email', $this -> plugin_name); ?></h3>
						<p><?php echo nl2br($newsletters_emailraw); ?></p>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php _e('Close Report', $this -> plugin_name); ?></a></p>
					</div>
				</div>
			</div>
			
			<?php		
			$output = ob_get_clean();
			
			
			header("Content-Type: text/xml; charset=UTF-8");
	    	
	    	?>
	    	
	    	<result>
				<success><?php echo $spamscore -> success; ?></success>
				<report><![CDATA[<?php echo nl2br($spamscore -> report); ?>]]></report>
				<score><?php echo $spamscore -> score; ?></score>
				<output><![CDATA[<?php echo $output; ?>]]></output>
			</result>
	    	
	    	<?php
		    
		    exit();
		    die();
	    }
	    
	    function ajax_gauge() {	    
	    	$value = $_REQUEST['value'];
		    
		    ?>
		    
		    <html>
		    	<body style="margin:0; padding:0;">
				    <script type="text/javascript" src="<?php echo $this -> render_url('js/justgage.js', 'admin', false); ?>"></script>
				    <script type="text/javascript" src="<?php echo $this -> render_url('js/raphael.js', 'admin', false); ?>"></script>
				    <div id="gauge"></div>
				    
				    <script>
					  var g = new JustGage({
					    id: "gauge", 
					    value: <?php echo $value; ?>, 
					    min: 1,
					    max: 10,
					    title: "<?php echo ($value >= 5) ? __('This is spam!', $this -> plugin_name) : __('This is safe!', $this -> plugin_name); ?>",
					    label: "<?php _e('Spam Score', $this -> plugin_name); ?>",
					    levelColorsGradient: false
					  }); 
					</script>
		    	</body>
		    </html>
		    
		    <?php
		    
		    exit();
		    die();
	    }
		
		function ajax_historyiframe($returnoutput = false) {	
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $Db, $History, $Subscriber;
			$Db -> model = $History -> model;
			$email = $Db -> find(array('id' => $_GET['id']));
			
			$subscriber_id = $Subscriber -> admin_subscriber_id();
			$subscriber = $Subscriber -> get($subscriber_id);
			//$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber_id, $_REQUEST['mailinglists']);
			$subscriber -> mailinglists = $email -> mailinglists;
			
			if (!empty($email -> post_id)) {
				if ($thepost = get_post($email -> post_id)) {
					global $post;
					$post = $thepost;
				}
			}
			
			$message = $this -> render('newsletter', array('email' => $email, 'subscriber' => $subscriber), false, 'default');
			$content = $this -> render_email('send', array('message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $_GET['id']), false, true, true, $email -> theme_id, true);
			$output = "";
			ob_start();
			echo do_shortcode(stripslashes($content));
			$output = ob_get_clean();
			ob_start();
			echo $this -> process_set_variables($subscriber, $user, $output, $email -> id);
			$output = ob_get_clean();
			
			if ($returnoutput) {
				return $output;
			}
			
			echo $output;
			exit();
			die();
		}
		
		function ajax_serialkey() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$errors = array();
			$success = false;
			
			if (!empty($_GET['delete'])) {
				$this -> delete_option('serialkey');
				$errors[] = __('Serial key has been deleted.', $this -> plugin_name);
			} else {
				if (!empty($_POST)) {
					if (empty($_REQUEST['serialkey'])) { $errors[] = __('Please fill in a serial key.', $this -> plugin_name); }
					else { 
						$this -> update_option('serialkey', $_REQUEST['serialkey']);	//update the DB option
						
						if (!$this -> ci_serial_valid()) { $errors[] = __('Serial key is invalid, please try again.', $this -> plugin_name); }
						else { $success = true; }
					}
				}
			}
			
			if (empty($_POST)) { ?><div id="<?php echo $this -> pre; ?>submitserial"><?php }
			$this -> render('submitserial', array('errors' => $errors, 'success' => $success), true, 'admin');
			if (empty($_POST)) { ?></div><?php }
			
			exit();
			die();
		}
		
		function ajax_managementcustomfields() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $FieldsList;
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']))) {
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;	
						}
					}
					
					$fields = $FieldsList -> fields_by_list($lists, "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
				} else {
					$errors[] = __('Subscriber could not be read.', $this -> plugin_name);
				}	
			}
			
			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default');
			
			exit();
			die();	
		}
		
		function ajax_managementsavefields() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $wpdb, $Db, $Subscriber, $Field, $FieldsList;
			
			$errors = array();
			$oldpost = $_POST;
			$success = false;
			$successmessage = "";
			
			if (!empty($_POST)) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']))) {
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;	
						}
					}				
					
					$fields = $FieldsList -> fields_by_list($lists, 'order', "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
					
					$_POST = $oldpost;
					unset($_POST['action']);
					unset($_POST['subscriber_id']);
					
					if (!empty($_POST)) {
						$emailfield = $Field -> email_field();
					
						if (!empty($_POST['email'])) {
							if (!$Subscriber -> email_validate($_POST['email'])) { $errors['email'] = __($emailfield -> errormessage); }
							elseif ($_POST['email'] != $subscriber -> email && $Subscriber -> email_exists($_POST['email'])) { $errors[] = __('Email address is already in use, try another.', $this -> plugin_name); }
						} else {
							$errors['email'] = __($emailfield -> errormessage);
						}
						
						$Field -> validate_optin($_POST, 'management');
						if (!empty($Field -> errors)) {
							$errors = array_merge($errors, $Field -> errors);
						}
					
						if (empty($errors)) {
							foreach ($_POST as $pkey => $pval) {										
								if (is_array($pval)) {
									$pval = maybe_serialize($pval);
								}
								
								$Db -> model = $Subscriber -> model;
								$Db -> save_field($pkey, $pval, array('id' => $subscriber -> id));
							}
							
							$success = true;
							$successmessage = __('Additional data has been saved.', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No data was posted.', $this -> plugin_name);	
					}
				} else {
					$errors[] = __('Subscriber could not be read.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was posted.', $this -> plugin_name);
			}
			
			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors), true, 'default');
		
			exit();
			die();	
		}
		
		function ajax_managementcurrentsubscriptions() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber;
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']))) {
					$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default');
				}
			}
			
			exit();
			die();	
		}
		
		function ajax_managementnewsubscriptions() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Subscriber, $Mailinglist;
			$otherlists = array();
			
			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
							
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']))) {				
					if ($mailinglists = $Mailinglist -> select(false)) {
						foreach ($mailinglists as $mkey => $mval) {
							$otherlists[$mkey] = $mkey;	
						}
								
						if (!empty($subscriber -> subscriptions)) {			
							foreach ($subscriber -> subscriptions as $subscription) {						
								if (in_array($subscription -> mailinglist -> id, $otherlists)) {
									unset($otherlists[$subscription -> mailinglist -> id]);
								}
							}
						}
						
						$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'otherlists' => $otherlists), true, 'default');
					}
				}
			}
			
			exit();
			die();
		}
		
		function ajax_subscribercountdisplay() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			if (!empty($_GET) && is_array($_GET)) {
				$_POST = $_GET;
				
				if ($subscribers = $this -> ajax_subscribercount(false)) {
					$this -> debug($subscribers);
				}
			}
			
			exit(); die();
		}
		
		function ajax_subscribercount($count = true) {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
			global $wpdb, $Db, $Field, $Subscriber, $Mailinglist, $SubscribersList, $wpmlGroup;		
			$subscribercount = 0;
			
			if (!empty($_POST['groups'])) {
				foreach ($_POST['groups'] as $group_key => $group_id) {
					$Db -> model = $Mailinglist -> model;
					
					if ($lists = $Db -> find_all(array('group_id' => $group_id), array('id'))) {
						foreach ($lists as $list) {
							$_POST['mailinglists'][] = $list -> id;	
						}
					}
				}
			}
			
			// Count the users based on roles
			$users_count = 0;
			if (!empty($_POST['roles'])) {
				if ($count_users = count_users()) {				
					foreach ($count_users['avail_roles'] as $role => $count) {					
						if (array_key_exists($role, $_POST['roles'])) {
							$users_count += $count;
						}
					}
				}
			}
			
			if (!empty($_POST['mailinglists'])) {
				$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id FROM " . $wpdb -> prefix . "" . $SubscribersList -> table . " LEFT JOIN 
				" . $wpdb -> prefix . "" . $Subscriber -> table . " ON 
				" . $wpdb -> prefix . "" . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . "" . $Subscriber -> table . ".id LEFT JOIN 
				" . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = " . $wpdb -> prefix . $Mailinglist -> table . ".id WHERE (";
				
				$m = 1;
				foreach ($_POST['mailinglists'] as $mailinglist_id) {
					$query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";	
					
					if ($m < count($_POST['mailinglists'])) {
						$query .= " OR ";	
					}
					
					$m++;
				}
				
				$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
				$query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval 
				OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')";
				
				if (!empty($_POST['fields'])) {	
					$query .= " AND (";			
					$supportedfields = array('text', 'radio', 'select', 'pre_country', 'pre_gender');
					$scopeall = (empty($_POST['fieldsconditionsscope']) || $_POST['fieldsconditionsscope'] == "all") ? true : false;
					$f = 1;
					foreach ($_POST['fields'] as $fkey => $field) {					
						if (!empty($field[0]) && $field[1] != "") {
							if (preg_match("/\d+/si", $field[0], $matches)) {
								$field_id = $matches[0];	
								
								$Db -> model = $Field -> model;
								if ($customfield = $Db -> find(array('id' => $field_id), array('id', 'slug', 'type'))) {	
									$condition = $_POST['condquery'][$customfield -> slug];
															
									if (in_array($customfield -> type, $supportedfields)) {									
										switch ($condition) {
											case 'smaller'			:
												$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " < " . $field[1] . "";
												break;
											case 'larger'			:
												$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " > " . $field[1] . "";
												break;
											case 'contains'			:
												$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " LIKE '%" . $field[1] . "%'";
												break;
											case 'equals'			:
											default  				:
												$query .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " = '" . $field[1] . "'";
												break;
										}
										
										if ($f < count($_POST['fields'])) {
											$query .= ($scopeall) ? " AND" : " OR";
										}
									}
								}
							}
						}
						
						$f++;
					}
					
					$query .= ")";
				}
				
				$query = str_replace(" AND ()", "", $query);
				
				if (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") {
					if (!empty($_POST['daterangefrom']) && !empty($_POST['daterangeto'])) {
						$daterangefrom = date_i18n("Y-m-d", strtotime($_POST['daterangefrom']));
						$daterangeto = date_i18n("Y-m-d", strtotime($_POST['daterangeto']));
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
					}
				}
				
				if ($subscribers = $wpdb -> get_results($query)) {
					$subscribercount = count($subscribers);
					$subscribercount += $users_count;
				}
			}
			
			if (!empty($subscribercount)) {
				echo '' . $subscribercount . ' ' . __('subscribers total', $this -> plugin_name) . '';	
			} else {
				echo 0;	
			}
				
			die();
		}
		
		function ajax_managementsubscribe() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			global $wpdb, $Db, $Auth, $Subscriber, $Mailinglist;
			
			$errors = array();	
			$success = false;
			$successmessage = "";
			$otherlists = array();
			
			if ($mailinglists = $Mailinglist -> select()) {
				foreach ($mailinglists as $mailinglist_id => $mailinglist_title) {
					$otherlists[] = $mailinglist_id;	
				}
			}
			
			if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id'])) {
				$Db -> model = $Subscriber -> model;
				
				if ($subscriber = $Db -> find(array('id' => $_POST['subscriber_id']))) {
					$data['email'] = $subscriber -> email;
					$data['cookieauth'] = $Auth -> read_cookie();
					$Db -> model = $Mailinglist -> model;
					
					if ($mailinglist = $Db -> find(array('id' => $_POST['mailinglist_id']))) {
						$data['mailinglists'] = $data['list_id'] = array($_POST['mailinglist_id']);
						
						if ($mailinglist -> paid == "Y") {
							$data['active'] = "N";	
						} else {
							$data['active'] = "Y";
						}
					}
					
					if ($Subscriber -> optin($data, false, false, false)) {
						$success = true;
						
						if ($mailinglist -> paid == "Y") {
							$successmessage = __('Subscription successful, please click the "Pay Now" button under current subscriptions to make a payment and activate your subscription.', $this -> plugin_name);
						} else {
							$successmessage = __('Subscription successful and activated.', $this -> plugin_name);
						}
					} else {
						$errors[] = __('Subscription was not successful.', $this -> plugin_name);
					}
					
					// Other lists…
					$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);
					$alllists = $Mailinglist -> select(false);
					$otherlists = array();
					
					if (!empty($alllists)) {
						foreach ($alllists as $alist_id => $alist_title) {
							if (empty($otherlists) || (!empty($otherlists) && !in_array($alist_id, $otherlists))) {
								if (empty($subscribedlists) || (!empty($subscribedlists) && !in_array($alist_id, $subscribedlists))) {
									$otherlists[] = $alist_id;
								}
							}
						}
					}
				} else {
					$errors[] = __('Subscriber cannot be read.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No subscriber/mailing list data posted.', $this -> plugin_name);
			}
			
			$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors, 'otherlists' => $otherlists), true, 'default');
			
			exit();
			die();
		}
		
		function ajax_managementactivate() {	
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			$success = false;
			$successmessage = "";
			$errors = array();
			
			if (!empty($_POST)) {
				if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id']) && !empty($_POST['activate'])) {
					global $wpdb, $Db, $Subscriber, $SubscribersList, $Auth, $Mailinglist, $Autoresponderemail;
					
					if ($subscriber = $Auth -> logged_in()) {
						if ($subscriber -> id == $_POST['subscriber_id']) {
							$Db -> model = $Mailinglist -> model;
							$mailinglist = $wpdb -> get_row("SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $_POST['mailinglist_id'] . "'");
							$paid = $mailinglist -> paid;
							$subscriber -> mailinglist_id = $mailinglist -> id;
							$Db -> model = $SubscribersList -> model;
							
							if ($_POST['activate'] == "N") {
								if ($Db -> delete_all(array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']))) {																
								
									$Db -> model = $Autoresponderemail -> model;
									$Db -> delete_all(array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']));
									
									//Should the subscriber be deleted?
									$deleted = false;
									if ($this -> get_option('unsubscribedelete') == "Y") {											
										$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists		
										if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {							
											$Db -> model = $Subscriber -> model;
											$Db -> delete($subscriber -> id);
											$deleted = true;
										}
									}
									
									$this -> admin_unsubscription_notification($subscriber, $mailinglist);
								
									if (!empty($deleted) || $deleted == true) {
										$message = __('Subscription removed and subscriber record deleted', $this -> plugin_name);
										$this -> redirect(home_url(), 'success', $message, true);
									} else {
										$success = true;
										$successmessage = __('Subscription has been removed.', $this -> plugin_name);
										$subscriber = $Auth -> logged_in();
									}
								} else {
									$errors[] = __('Subscription could not be removed.', $this -> plugin_name);	
								}
							} else {							
								if (false && $this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {		
									$success = true;
									$successmessage = __('A confirmation email has been sent through to your email address.', $this -> plugin_name);
									$this -> subscription_confirm($subscriber);	
								} else {
									$Db -> model = $SubscribersList -> model;
									
									if ($Db -> save_field('active', "Y", array('subscriber_id' => $_POST['subscriber_id'], 'list_id' => $_POST['mailinglist_id']))) {
										$success = true;
										$successmessage = __('Subscription has been activated.', $this -> plugin_name);
										
										$subscriber = $Auth -> logged_in();
									} else {
										$errors[] = __('Subscription could not be activated.', $this -> plugin_name);	
									}
								}
							}
						} else {
							$errors[] = __('You are logged in as a different subscriber.', $this -> plugin_name);	
						}
					} else {
						$errors[] = __('You are not currently logged in.', $this -> plugin_name);	
					}
				} else {
					$errors[] = __('No subscriber/mailing list data posted.', $this -> plugin_name);
				}
			} else {
				$errors[] = __('No data was posted.', $this -> plugin_name);	
			}
			
			$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success, 'successmessage' => $successmessage), true, 'default');
			
			exit();
			die();
		}
		
		function autoresponders_send($subscriber = null, $mailinglist = null) {
			global $wpdb, $Db, $AutorespondersList, $Autoresponder, $History, $HistoriesAttachment, $Subscriber, $SubscribersList, $Html, $Autoresponderemail, $Email;
		
			if (!empty($subscriber) && !empty($mailinglist)) {
				$subscriber_id = $subscriber -> id;
				$Db -> model = $AutorespondersList -> model;							
				if ($autoresponserslists = $Db -> find_all(array('list_id' => $mailinglist -> id))) {								
					foreach ($autoresponserslists as $al) {
						$Db -> model = $Autoresponder -> model;
						$autoresponder = $Db -> find(array('id' => $al -> autoresponder_id));																				
						if (!empty($autoresponder -> status) && $autoresponder -> status == "active") {
							//Send the 0 delay autoresponders right now
							if (empty($autoresponder -> delay) || $autoresponder -> delay <= 0) {
								$Db -> model = $SubscribersList -> model;
								$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));								
								if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {								
									$Db -> model = $Autoresponderemail -> model;
									if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id)))) {									
										$Db -> model = $History -> model;
										$history = $Db -> find(array('id' => $autoresponder -> history_id));
										$history -> attachments = array();
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
										
										$subscriber -> mailinglist_id = $mailinglist -> id;
										$eunique = $Html -> eunique($subscriber, $history -> id);
										
										$autoresponderemail_data = array(
											'autoresponder_id'				=>	$autoresponder -> id,
											'list_id'						=>	$mailinglist -> id,
											'subscriber_id'					=>	$subscriber -> id,
											'senddate'						=>	date_i18n("Y-m-d H:i:s", strtotime("+" . $autoresponder -> delay . " " . $autoresponder -> delayinterval)),
										);
										
										$Db -> model = $Autoresponderemail -> model;
										$Db -> save($autoresponderemail_data, true);
										$ae_id = $Autoresponderemail -> insertid;
										$Db -> model = $Email -> model;
										$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
										
										if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique)) {								
											$Db -> model = $Autoresponderemail -> model;
											$Db -> save_field('status', "sent", array('id' => $ae_id));
										}		
									}
								}
							//Save the 1+ delay autoresponders to send later
							} else {
								$Db -> model = $SubscribersList -> model;
								$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));								
								if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {								
									$Db -> model = $Autoresponderemail -> model;
									if (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id))) {										
										$autoresponderemail_data = array(
											'autoresponder_id'				=>	$autoresponder -> id,
											'list_id'						=>	$mailinglist -> id,
											'subscriber_id'					=>	$subscriber -> id,
											'senddate'						=>	date_i18n("Y-m-d H:i:s", strtotime("+" . $autoresponder -> delay . " " . $autoresponder -> delayinterval)),
										);
										
										$Db -> model = $Autoresponderemail -> model;
										$Db -> save($autoresponderemail_data, true);		
									}
								}
							}
						}
					}
				}
			}
		}
		
		function paidsubscription_form($subscriber = null, $mailinglist = null, $autosubmit = true, $target = "_self") {
			global $Html;
		
			if (!empty($subscriber) && !empty($mailinglist)) {
				if ($this -> get_option('paymentmethod') == "2co") {
					$checkoutdata = array(
						'sid'					=>	$this -> get_option('tcovendorid'),
						'cart_order_id'			=>	$subscriber -> id . $mailinglist -> id,
						'total'					=>	$mailinglist -> price,
						'id_type'				=>	1,
						'c_prod'				=>	$mailinglist -> id . ',1',
						'c_name'				=>	__($mailinglist -> title),
						'c_description'			=>	__($mailinglist -> title),
						'c_price'				=>	$mailinglist -> price,
						'quantity'				=>	1,
						'return_url'			=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						'x_receipt_link_url'	=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						$this -> pre . 'method'	=>	'twocheckout',
						'subscriber_id'			=>	$subscriber -> id,
						'subscriber_email'		=>	$subscriber -> email,
						'mailinglist_id'		=>	$mailinglist -> id,
						'fixed'					=>	'Y',
						'demo'					=>	$this -> get_option('tcodemo'),
						'currency_code'			=>	$this -> get_option('currency'),
						'email'					=>	$subscriber -> email,
					);
					
					$formid = 'paidsubscriptionform' . $mailinglist -> id;
					$this -> render('twocheckout-form', array('checkoutdata' => $checkoutdata, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				} else {					
					$pp_return = ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once") ?
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true)) :
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true));
				
					$checkoutdata = array(
						'charset'							=>	get_option('blog_charset'),
						'return'							=>	$pp_return,
						'rm'								=>	2,
						'notify_url'						=>	home_url() . '/?' . $this -> pre . 'method=paypal',
						'cbt'								=>	__('Click here to complete your order', $this -> plugin_name),
						'currency_code'						=>	$this -> get_option('currency'),
						'business'							=>	$this -> get_option('paypalemail'),
						'item_name'							=>	__($mailinglist -> title),
						'item_number'						=>	$mailinglist -> id,
						'custom'							=>	urlencode(serialize(array(
							'subscriber_id'		=>	$subscriber -> id,
							'mailinglist_id'	=>	$mailinglist -> id
						))),
						'no_shipping'						=>	1,
						'no_note'							=>	1,
					);
					
					if ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once") {
						$checkoutdata['cmd'] = "_xclick-subscriptions";
						$checkoutdata['a3'] = number_format($mailinglist -> price, 2, '.', '');
						$checkoutdata['p3'] = $Html -> getpptd($mailinglist -> interval);
						$checkoutdata['t3'] = $Html -> getppt($mailinglist -> interval);
						$checkoutdata['src'] = 1;
						$checkoutdata['sra'] = 1;
					} else {
						$checkoutdata['cmd'] = "_xclick";
						$checkoutdata['amount'] = $mailinglist -> price;
						$checkoutdata['quantity'] = 1;
					}
					
					$formid = 'paidsubscriptionform' . $mailinglist -> id;
					$this -> render('paypal-form', array('checkoutdata' => $checkoutdata, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				}
			}
		}
		
		function sc_management($atts = array(), $content = null) {
			global $wpdb, $Db, $Subscriber, $SubscribersList, $Auth, $Field, $Unsubscribe, $Autoresponderemail;
			
			$output = "";
			$defaults = array();
			extract(shortcode_atts($defaults, $atts));
			$emailfield = $Field -> email_field();
			
			ob_start();
			
			switch ($_GET['method']) {
				case 'paidsubscriptionsuccess'	:
					$message = __('Thank you for your payment!<br/><br/>Please allow a moment for the subscription to be activated.', $this -> plugin_name);
					$this -> redirect($this -> get_managementpost(true), "success", $message);
					break;
				case 'unsubscribe'			:
					global $wpdb, $Html, $Auth, $Db, $Subscriber, $Mailinglist, $SubscribersList, $Queue;
				
					$data = (empty($_POST)) ? $_GET : $_POST;
					$error = false;
					$success = false;
					$deleted = false;
					$userfile = false;
					
					if (!empty($data[$this -> pre . 'subscriber_id']) || !empty($data['user_id'])) {
						if (($mailinglists = explode(",", $data[$this -> pre . 'mailinglist_id'])) !== false) {
							//do nothing, it's good...
						} else {
							$mailinglists = false;
						}
					
						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							$subscriber_query = "SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE id = '" . $data[$this -> pre . 'subscriber_id'] . "'";
									
							if ($subscriber = $wpdb -> get_row($subscriber_query)) {
								/* Management Auth */
								if (empty($data['cookieauth'])) {
									$Auth -> set_emailcookie($subscriber -> email);
									$subscriberauth = $Auth -> gen_subscriberauth();
									$Db -> model = $Subscriber -> model;
									$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
									$Auth -> set_cookie($subscriberauth);
								}
							
								$subscriber -> mailinglists = $mailinglists;
								
								if (empty($subscriber -> mailinglists)) {
									$errors[] = __('This email was not sent to any lists.', $this -> plugin_name);
								}
							} else {
								$errors[] = __('Your subscriber record cannot be read, please try again.', $this -> plugin_name);
							}	
						} elseif (!empty($data['user_id'])) {
							if ($user = $this -> userdata($data['user_id'])) {
								// all good
								$userfile = "-user";
							} else {
								$errors[] = __('User cannot be read', $this -> plugin_name);
							}
						} else {
							$errors[] = __('No subscriber or user was specified', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No subscriber ID was specified, please try again.', $this -> plugin_name);
					}
					
					if (!empty($data['confirm']) || $this -> get_option('unsubscribeconfirmation') == "N") {					
						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							if ($this -> get_option('unsubscribeconfirmation') == "N") {
								$data['unsubscribelists'] = $mailinglists;
							}
						
							if (!empty($data['unsubscribelists'])) {										
								foreach ($data['unsubscribelists'] as $unsubscribelist_id) {
									$Db -> model = $Mailinglist -> model;
									$mailinglist = $Db -> find(array('id' => $unsubscribelist_id));
									$subscriber -> mailinglist_id = $unsubscribelist_id;
									$this -> admin_unsubscription_notification($subscriber, $mailinglist);
									
									if (!empty($subscriber -> id) && !empty($unsubscribelist_id)) {
										$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
										$Db -> model = $Queue -> model;
										$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'mailinglist_id' => $unsubscribelist_id));
										$Db -> model = $Autoresponderemail -> model;
										$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
										
										$Db -> model = $Unsubscribe -> model;
										$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => $unsubscribelist_id, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
										$Db -> save($unsubscribe_data, true);
									}
								}
													
								if ($this -> get_option('unsubscriberemoveallsubscriptions') == "Y") {
									$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists							
									foreach ($subscribedlists as $subscribedlist_id) {
										if (!empty($subscriber -> id) && !empty($subscribedlist_id)) {
											$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $subscribedlist_id));
											$Db -> model = $Queue -> model;
											$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'mailinglist_id' => $subscribedlist_id));
										}
									}
								}
								
								//Should the subscriber be deleted?
								if ($this -> get_option('unsubscribedelete') == "Y") {											
									$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists		
									if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {							
										$Db -> model = $Subscriber -> model;
										$Db -> delete($subscriber -> id);
										$deleted = true;
									}
								}
								
								//$this -> admin_unsubscription_notification($subscriber, $unsubscribelists);						
								$success = true;
							} else {
								$errors[] = __('You did not select any list(s) to unsubscribe from.', $this -> plugin_name); 
								$success = false;
							}
						} elseif (!empty($data['user_id'])) {
							$Db -> model = $Unsubscribe -> model;
							$unsubscribe_data = array('user_id' => $user -> ID, 'email' => $user -> user_email, 'mailinglist_id' => false, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
							$Db -> save($unsubscribe_data, true);
							$success = true;
							$errors = false;
						} else {
							$errors[] = __('No subscriber or user was specified', $this -> plugin_name);
						}
					}
				
					$this -> render('unsubscribe' . $userfile, array('subscriber' => $subscriber, 'user' => $user, 'data' => $data, 'errors' => $errors, 'success' => $success, 'deleted' => $deleted), true, 'default');
					break;
				case 'logout'				:
					global $wpmljavascript;
					$subscriberemailauth = $Auth -> read_emailcookie();
					$subscriberauth = $Auth -> read_cookie();
					$Auth -> delete_cookie($Auth -> cookiename, $subscriberauth);
					$Auth -> delete_cookie($Auth -> emailcookiename, $subscriberemailauth);
					echo $wpmljavascript;
					$this -> render('management' . DS . 'logout-auth', false, true, 'default');
					break;
				case 'loginauth'			:
					if (empty($_GET['email'])) {
						$subscriberemailauth = $_POST['email'] = $Auth -> read_emailcookie();
					} else {
						$subscriberemailauth = $_GET['email'];	
					}
					
					$subscriberauth = $_GET['subscriberauth'];
					
					if (!empty($subscriberemailauth)) {
						if (!empty($subscriberauth)) {
							$Db -> model = $Subscriber -> model;							
							if ($subscriber = $Db -> find(array('email' => $subscriberemailauth, 'cookieauth' => $subscriberauth))) {
								global $wpmljavascript;
							
								if ($Auth -> set_cookie($subscriberauth)) {
									$Auth -> set_emailcookie($subscriberemailauth);
								} else {
									$errors[] = __('Please ensure that you have cookies enabled in your browser.', $this -> plugin_name);
								}
							} else {
								$errors[] = __('Authentication failed, please try again.', $this -> plugin_name);
							}
						} else {
							$errors[] = __('No authentication string passed, please click the link again.', $this -> plugin_name);
						}
					} else {
						$errors[] = __('No email saved, please try again.', $this -> plugin_name);
					}
					
					if (empty($errors)) {
						$this -> render('management' . DS . 'login-auth', false, true, 'default');
					} else {
						$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');	
					}
					break;
				case 'login'				:
					if (!empty($_POST)) {
						if (!empty($_POST['email'])) {
							if ($Subscriber -> email_validate($_POST['email'])) {
								$Db -> model = $Subscriber -> model;
								
								if ($subscriber = $Db -> find(array('email' => $_POST['email']))) {
									if ($subscriberauth = $Auth -> gen_subscriberauth()) {
										$Auth -> set_emailcookie($_POST['email']);
										
										$Db -> model = $Subscriber -> model;
										$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
										
										$subject = __($this -> get_option('managementloginsubject'));
										$message = $this -> render_email('management-login', array('subscriberauth' => $subscriberauth), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false);
										
										if ($this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false)) {
											$errors[] = __('Authentication email has been sent, please check your inbox.', $this -> plugin_name);
										} else {
											$errors[] = __('Authentication email could not be sent.', $this -> plugin_name);	
										}
									} else {
										$errors[] = __('Authentication string could not be created.', $this -> plugin_name);	
									}
								} else {
									$errors[] = __('Subscriber with that email address cannot be found, please try a different email address.', $this -> plugin_name);	
								}
							} else {
								$errors[] = __('Please fill in a valid email address.', $this -> plugin_name);	
							}
						} else {
							$errors[] = $emailfield -> error; 
						}
					} else {
						$errors[] = __('No data was posted.', $this -> plugin_name);	
					}
					
					if (empty($errors)) {
						//$this -> redirect(get_permalink($this -> get_option('managementpost')));
					} else {
						$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');	
					}
					break;
				default						:				
					if ($subscriber = $Auth -> logged_in()) {
						if ($this -> get_option('subscriptions') == "Y") {
							$SubscribersList -> check_expirations(false, false, true, $subscriber -> id);
						}
						
						$subscriber = $Auth -> logged_in();
						$this -> render('management' . DS . 'index', array('subscriber' => $subscriber), true, 'default');
					} else {
						$this -> render('management' . DS . 'login', false, true, 'default');	
					}
					break;	
			}
			
			global $wpmljavascript;
			if (!empty($wpmljavascript)) {
				echo $wpmljavascript;
			}
			
			$output = ob_get_clean();			
			global $Html;
			$output = $Html -> fragment_cache($output, 'this', 'sc_management', false);
			return $output;
		}
		
		function is_plugin_screen($screen = null) {
			if (!empty($_GET['page'])) {
				if (!empty($screen)) {
					if (in_array($_GET['page'], (array) $this -> sections -> {$screen})) {
						return true;
					}
				} else {
					if (in_array($_GET['page'], (array) $this -> sections)) {
						return true;	
					}
				}
			}
			
			return false;
		}
		
		function paginate($model = null, $fields = '*', $sub = null, $conditions = false, $searchterm = null, $per_page = 10, $order = array('modified', "DESC")) {
			global $wpdb, $Db, $Autoresponder, $Autoresponderemail, $Subscriber, $SubscribersList, $Mailinglist, 
			${$model}, $AutorespondersList, $Mailinglist;
			
			$object = (!is_object(${$model})) ? $this -> {$model} : ${$model};
		
			if (!empty($model)) {			
				global $paginate;
				$paginate = $this -> vendor('paginate');
				$paginate -> table = $wpdb -> prefix . $this -> pre . $object -> controller;
				$paginate -> sub = (empty($sub)) ? $object -> controller : $sub;
				$paginate -> fields = (empty($fields)) ? '*' : $fields;
				$paginate -> where = (empty($conditions)) ? false : $conditions;
				$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
				$paginate -> per_page = $per_page;
				$paginate -> order = $order;
				
				$data = $paginate -> start_paging($_GET[$this -> pre . 'page']);
				
				if (!empty($data)) {
					$newdata = array();
					$n = 0;
				
					foreach ($data as $record) {
						//$newdata[$n] = $this -> init_class($model, $record);
						$newdata[$n] = $record;
						
						switch ($model) {
							case 'Subscriber'					:							
								$Db -> model = $SubscribersList -> model;
								if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $record -> id))) {
									foreach ($subscriberslists as $sl) {
										$listquery = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $sl -> list_id . "' LIMIT 1";
										$newdata[$n] -> Mailinglist[] = $wpdb -> get_row($listquery);
										//$newdata[$n] -> subscriptions[] = $sl;
									}
								}
								break;
							case 'SubscribersList'				:
								$Db -> model = $Subscriber -> model;
								
								if ($subscriber = $Db -> find(array('id' => $record -> subscriber_id))) {								
									$newdata[$n] = $subscriber;
									
									foreach ($record as $rkey => $rval) {
										$newdata[$n] -> {$rkey} = $rval;	
									}
								}
								break;
							case 'Autoresponder'				:	
								/* Pending Emails */					
								$Db -> model = $Autoresponderemail -> model;
								$newdata[$n] -> pending = $Db -> count(array('autoresponder_id' => $record -> id, 'status' => "unsent"));
								
								/* Mailing Lists */
								$newdata[$n] -> mailinglists = array();
								$Db -> model = $AutorespondersList -> model;
								if ($autoresponderslists = $Db -> find_all(array('autoresponder_id' => $record -> id))) {				
									foreach ($autoresponderslists as $autoresponderslist) {
										$Db -> model = $Mailinglist -> model;
										$newdata[$n] -> lists[] = $autoresponderslist -> list_id;
										$newdata[$n] -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
									}
								}
								break;
							case 'Autoresponderemail'			:
								/* Autoresponder */
								$Db -> model = $Autoresponder -> model;
								$newdata[$n] -> autoresponder = $Db -> find(array('id' => $record -> autoresponder_id), false, false, false, false);
								
								/* Subscriber */
								$Db -> model = $Subscriber -> model;
								$newdata[$n] -> subscriber = $Db -> find(array('id' => $record -> subscriber_id), false, false, false, false);
								break;	
						}
						
						$n++;
					}
					
					$data = array();
					$data[$model] = (object) $newdata;
					$data['Paginate'] = $paginate;
				}
		
				return $data;
			}
			
			return false;
		}
		
		function ci_print_styles() {
			wp_enqueue_style('newsletters', $this -> render_url('css/wp-mailinglist.css', 'admin', false), null, $this -> version, "all");
			wp_enqueue_style('colorbox', plugins_url() . '/' . $this -> plugin_name . '/css/colorbox.css', false, $this -> version, "all");
		}
		
		function ci_print_scripts() {
			wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/' . $this -> plugin_name . '.js', array('jquery'), '1.0', true);	
			wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, true);
		}
		
		function print_scripts() {
			$this -> enqueue_scripts();	
		}
		
		function print_styles() {
			$this -> enqueue_styles();	
		}
		
		function enqueue_scripts() {	
			//enqueue jQuery JS Library
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-widget');
	
			if (is_admin()) {			
				if (preg_match("/(widgets\.php)/", $_SERVER['REQUEST_URI'], $matches)) {
					wp_enqueue_script('jquery-ui-tooltip', plugins_url() . '/' . $this -> plugin_name . '/js/jquery-ui-tooltip.js', array('jquery'), false, true);
				}
			
				if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
					 wp_enqueue_script(
				        'iris',
				        admin_url('js/iris.min.js'),
				        array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
				        false,
				        1
				    );
				    
				    wp_enqueue_script(
				        'wp-color-picker',
				        admin_url('js/color-picker.min.js'),
				        array( 'iris' ),
				        false,
				        1
				    );
					
					wp_enqueue_script('jquery-autoheight', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.autoheight.js', array('jquery'), false, true);
					wp_enqueue_script('jquery-ui-watermark', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.watermark.js', array('jquery'), false, true);
					wp_enqueue_script('jquery-ui-tooltip', plugins_url() . '/' . $this -> plugin_name . '/js/jquery-ui-tooltip.js', array('jquery'), false, true);
					
					//countdown script
					if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
						wp_enqueue_script('jquery-countdown', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.countdown.js', array('jquery'), false, true);
					}
					
					//sortables
					if ($_GET['page'] == $this -> sections -> fields && $_GET['method'] == "order") {
						wp_enqueue_script('jquery-ui-sortable', false, false, false, true);
					}
					
					/* Progress Bar */
					if ($_GET['page'] == $this -> sections -> importexport ||
						$_GET['page'] == $this -> sections -> send) {
						wp_enqueue_script('jquery-ui-progressbar', plugins_url() . '/' . $this -> plugin_name . '/js/jquery-ui-progressbar.js', array('jquery-ui-core', 'jquery-ui-widget'));
					}
					
					// Uploadify
					if (!empty($_GET['page']) && !empty($_GET['method']) && $_GET['page'] == $this -> sections -> subscribers && $_GET['method'] == "save") {
						wp_enqueue_script('uploadify', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.uploadify.js', array('jquery'), '1.0', false);
					}
					
					if ($_GET['page'] == $this -> sections -> welcome ||
						$_GET['page'] == $this -> sections -> send ||
						$_GET['page'] == $this -> sections -> autoresponders ||
						$_GET['page'] == $this -> sections -> templates_save ||
						$_GET['page'] == $this -> sections -> settings ||
						$_GET['page'] == $this -> sections -> settings_templates ||
						$_GET['page'] == $this -> sections -> settings_subscribers ||
						$_GET['page'] == $this -> sections -> settings_system ||
						$_GET['page'] == $this -> sections -> extensions_settings) {																				
							//meta boxes
							wp_enqueue_script('common', false, false, false, true);
							wp_enqueue_script('wp-lists', false, false, false, true);
							wp_enqueue_script('postbox', false, false, false, true);
							
							//editor
							wp_enqueue_script('editor', false, false, false, true);
							wp_enqueue_script('quicktags', false, false, false, true);
							wp_enqueue_script('wplink', false, false, false, true);
							wp_enqueue_script('wpdialogs-popup', false, false, false, true);
							wp_enqueue_style('wp-jquery-ui-dialog', false, false, false, true);
							wp_enqueue_script('word-count', false, false, false, true);
							wp_enqueue_script('media-upload', false, false, false, true);
							wp_admin_css();
							wp_enqueue_script('utils', false, false, false, true);
							
							//editors files
							if ($_GET['page'] == $this -> sections -> welcome) { wp_enqueue_script('welcome-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/welcome-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> send) { wp_enqueue_script('send-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/send-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> templates_save) { wp_enqueue_script('templates-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/templates-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings) { wp_enqueue_script('settings-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_templates) { wp_enqueue_script('settings-editor-templates', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-templates.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_subscribers) { wp_enqueue_script('settings-editor-subscribers', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-subscribers.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_system) { wp_enqueue_script('settings-editor-system', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-system.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> extensions_settings) { wp_enqueue_script('settings-editor-extensions-settings', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-extensions-settings.js', array('jquery'), false, true); }
					}
				}
				
				add_thickbox();				
				wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/' . $this -> plugin_name . '.js', array('jquery'), '1.0', true);
				wp_enqueue_script('jquery-ui-tabs');
				wp_enqueue_script('jquery-cookie', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.cookie.js', array('jquery'));
				wp_enqueue_script('jquery-shiftclick', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.shiftclick.js', array('jquery'));
				wp_enqueue_script('jquery-ui-droppable');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, true);
				
			/* Front-End Scripts */
			} else {						
				if (wpml_is_management()) {				
					if ($this -> get_option('loadscript_jqueryuitabs') == "Y") {
						wp_enqueue_script('jquery-ui-tabs', false, array('jquery'), false, true);
						wp_enqueue_script('jquery-cookie', $this -> render_url('js/jquery.cookie.js', 'admin', false), array('jquery'));
					}
				}
			
				//enqueue the plugin JS
				if ($this -> get_option('loadscript_jqueryuibutton') == "Y") { wp_enqueue_script('jquery-ui-button', array('jquery'), false, true); }
				if ($this -> get_option('loadscript_jqueryuiwatermark') == "Y") { wp_enqueue_script($this -> get_option('loadscript_jqueryuiwatermark_handle'), $this -> render_url('js/jquery.watermark.js', 'admin', false), array('jquery'), false, true); }
				if ($this -> get_option('loadscript_jqueryuploadify') == "Y") { wp_enqueue_script($this -> get_option('loadscript_jqueryuploadify_handle'), $this -> render_url('js/jquery.uploadify.js', 'admin', false), array('jquery'), false, true); }
				
				wp_enqueue_script($this -> plugin_name, $this -> render_url('js/wp-mailinglist.js', 'admin', false), array('jquery'), false, true);
				
				$captcha_type = $this -> get_option('captcha_type');
				if (!empty($captcha_type) && $captcha_type == "recaptcha") {
					wp_enqueue_script('recaptcha', "http://www.google.com/recaptcha/api/js/recaptcha_ajax.js");
				}
			}
			
			return true;
		}
		
		function enqueue_styles() {
			$load = false;
			$theme_folder = $this -> get_option('theme_folder');
			
			if (is_admin()) {
				//wp_register_style( 'newsletters_dashicons', plugins_url() . '/' . $this -> plugin_name . '/css/newsletters_dashicons.css', false, '1.0.0' );
			
				if (true || !empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
					$load = true;	
					$stylesource = plugins_url() . '/' . $this -> plugin_name . '/css/' . $this -> plugin_name . '.css';
					//wp_enqueue_style('farbtastic');
					wp_enqueue_style('wp-color-picker');
				}
				
				//$uisrc = plugins_url() . '/' . $this -> plugin_name . '/css/jquery-ui.css';
				$uisrc = $this -> render_url('css/jquery-ui.css', 'admin', false);
				wp_enqueue_style('jquery-ui', $uisrc, false, '1.0', "all");
				wp_enqueue_style('colorbox', $this -> render_url('css/colorbox.css', 'admin', false), false, $this -> version, "all");
				
				if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
					wp_enqueue_style('jquery-countdown', $this -> render_url('css/jquery-countdown.css', 'admin', false), false, false, "all");
				}
			} else {
				if ($this -> get_option('theme_usestyle') == "Y") {
					$load = true;	
					//$stylesource = plugins_url() . '/' . $this -> plugin_name . '/views/' . $theme_folder . '/css/style.css';
					$stylesource = $this -> render_url('css/style.css', 'default', false);
				}
			}
			
			if ($load) {
				if (!empty($stylesource)) { wp_enqueue_style($this -> plugin_name, $stylesource, false, $this -> version, "screen"); }
				wp_enqueue_style('uploadify', $this -> render_url('css/uploadify.css', 'default', false), false, $this -> version, "all");
				
				if ($this -> get_option('customcss') == "Y") {
					//$customsrc = plugins_url() . '/' . $this -> plugin_name . '/css/' . $this -> plugin_name . '-css.php';
					$customsrc = $this -> render_url('css/' . $this -> plugin_name . '-css.php', 'admin', false);
					wp_enqueue_style($this -> pre . '-custom', $customsrc, null, $this -> version, "screen");	
				}
			}
			
			return true;
		}
		
		function initialize_default_themes() {
			if (!is_admin()) return;
		
			$premade_include = $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS . 'premade.php';
			include_once($premade_include);
		}
		
		function set_timezone() {
			$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
			$current_offset = get_option('gmt_offset');
			$tzstring = get_option('timezone_string');
			$check_zone_info = true;
			if (false !== strpos($tzstring,'Etc/GMT')) { $tzstring = ''; }
					
			if (empty($tzstring)) {
				$check_zone_info = false;
				if (0 == $current_offset) {
					$tzstring = 'UTC+0';
				} elseif ($current_offset < 0) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}
		
			@putenv("TZ=" . $tzstring);
			@ini_set('date.timezone', $tzstring);
			
			if (function_exists('date_default_timezone_set')) {
				@date_default_timezone_set($tzstring);
			}
		}
		
		function init_class($name = null, $params = array()) {		
			if (!empty($name)) {
				$name = (!preg_match("/" . $this -> pre . "/si", $name)) ? $this -> pre . $name : $name;
			
				if (class_exists($name)) {
					if ($class = new $name($params)) {	
						return $class;
					}
				}
			}
			
			return false;
		}
		
		function initialize_classes() {
			global $wpdb;
			
			if (!empty($this -> models)) {
				foreach ($this -> models as $model) {								
					if (empty($this -> {$model}) || !is_object($this -> {$model})) {
						require_once $this -> plugin_base() . DS . 'models' . DS . strtolower($model) . '.php';
						$classname = $this -> pre . $model;
					
						if (class_exists($classname)) {
							global ${$this -> pre . $model};
							${$this -> pre . $model} = new $classname();
							$this -> {$model} = ${$this -> pre . $model};							
							
							$this -> tablenames[$this -> pre . $this -> {$model} -> controller] = $wpdb -> prefix . $this -> {$model} -> table;
							$this -> tables[$this -> pre . $this -> {$model} -> controller] = $this -> {$model} -> fields;
						}
					}
				}
			}
			
			if (!empty($this -> helpers)) {
				foreach ($this -> helpers as $helper) {
					global ${$helper};
					
					$helpername = $this -> pre . $helper . 'Helper';					
					if (!is_object(${$helper})) {
						${$helper} = $this -> init_class($helpername);
					}
				}
			}
		
			//make sure that we have some classes defined.
			if (!empty($this -> classes)) {			
				//loop our classes
				foreach ($this -> classes as $class) {	
					global ${$class};
				
					if (!is_object(${$class})) {
						switch ($class) {
							case 'wpmlGroup'		:
							case 'wpmlOrder'		:
							case 'wpmlCountry'		:
								${$class} = $this -> init_class($class);
								break;
							default					:
								${$class} = $this -> init_class($this -> pre . $class);
								break;
						}
						
						$this -> tablenames[$this -> pre . ${$class} -> controller] = $wpdb -> prefix . ${$class} -> table;
						$this -> tables[$this -> pre . ${$class} -> controller] = (empty(${$class} -> table_fields)) ? ${$class} -> fields : ${$class} -> table_fields;
					}
					
					if (empty($this -> {$class}) || !is_object($this -> {$class})) {
						$this -> {$class} = ${$class};
					}
				}
			}
			
			return true;
		}
		
		function activateaction_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_activateaction');
			$activateaction = $this -> get_option('activateaction');
			
			if (!empty($activateaction) && $activateaction != "none") {
				$timestamp = time();
				
				if (!wp_next_scheduled($this -> pre . '_activateaction')) {
					wp_schedule_event($timestamp, "hourly", $this -> pre . '_activateaction');
				}
			}
			
			return true;
		}
		
		function latestposts_scheduling() {		
			wp_clear_scheduled_hook($this -> pre . '_latestposts');
			
			if ($this -> get_option('latestposts') == "Y") {
				if ($interval = $this -> get_option('latestposts_interval')) {
					$schedules = wp_get_schedules();
					$schedules = $this -> cron_schedules($schedules);
					
					$latestposts_startdate = $this -> get_option('latestposts_startdate');
					if (empty($latestposts_startdate) || strtotime($latestposts_startdate) < time() || empty($_POST['latestposts_updateinterval']) || $_POST['latestposts_updateinterval'] == "N") {
						$new_timestamp = time() + $schedules[$interval]['interval'];
					} else {
						$new_timestamp = strtotime($latestposts_startdate);
					}
					
					if (!wp_next_scheduled($this -> pre . '_latestposts')) {
						wp_schedule_event($new_timestamp, $interval, $this -> pre . '_latestposts');
					}
				}
			}
		
			return true;	
		}
	
	    function pop_scheduling() {
	        wp_clear_scheduled_hook($this -> pre . '_pophook');
	
	        if ($this -> get_option('bouncemethod') == "pop") {
	            $schedules = wp_get_schedules();
	            $schedules = $this -> cron_schedules($schedules);
				$interval = $this -> get_option('bouncepop_interval');
				$new_timestamp = time() + $schedules[$interval]['interval'];
				
				if (!wp_next_scheduled($this -> pre . '_pophook')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_pophook');
				}
	        }
	
	        return;
	    }
		
		function importusers_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_importusers');
			
			if ($this -> get_option('importusers') == "Y") {
				$schedules = wp_get_schedules();
				$schedules = $this -> cron_schedules($schedules);
				$interval = $this -> get_option('importusersscheduling');
				$interval = (empty($interval)) ? "hourly" : $interval;
				$new_timestamp = time() + $schedules[$interval]['interval'];
				
				if (!wp_next_scheduled($this -> pre . '_importusers')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_importusers');
				}
			}
		}
		
		function autoresponder_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_autoresponders');
			
			$schedules = wp_get_schedules();
			$schedules = $this -> cron_schedules($schedules);
			$interval = $this -> get_option('autoresponderscheduling');
			$new_timestamp = time() + $schedules[$interval]['interval'];
			
			if (!wp_next_scheduled($this -> pre . '_autoresponders')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_autoresponders');
			}
			
			return true;
		}
		
		function captchacleanup_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_captchacleanup');
			
			$schedules = wp_get_schedules();
			$schedules = $this -> cron_schedules($schedules);
			$interval = $this -> get_option('captchainterval');
			$new_timestamp = time() + $schedules[$interval]['interval'];
			
			if (!wp_next_scheduled($this -> pre . '_captchacleanup')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_captchacleanup');
			}
			
			return true;
		}
		
		function scheduling($increase = false) {
			wp_clear_scheduled_hook($this -> pre . '_cronhook');
			if ($this -> get_option('scheduling') == "Y") {			
				if ($this -> get_option('schedulecrontype') == "wp") {			
					$schedules = wp_get_schedules();
					$schedules = $this -> cron_schedules($schedules);
					$interval = $this -> get_option('scheduleinterval');
					$new_timestamp = time() + $schedules[$interval]['interval'];
					if ($increase == true) { $new_timestamp += 300; }
					
					if (!wp_next_scheduled($this -> pre . '_cronhook')) {
						wp_schedule_event($new_timestamp, $interval, $this -> pre . '_cronhook');
					}
				}
			}
		}
		
		function get_custom_post_types($removedefaults = true) {
			if ($post_types = get_post_types(null, 'objects')) {
				$default_types = array('post', 'page', 'attachment', 'revision', 'nav_menu_item');
				
				if ($removedefaults) {
					foreach ($default_types as $dpt) {
						unset($post_types[$dpt]);
					}
				}
				
				return $post_types;
			}
			
			return false;
		}
		
		function check_tables() {
			$this -> initialize_classes();
			
			if (!empty($this -> tablenames)) {
				foreach ($this -> tablenames as $controller => $tablename) {			
					$this -> check_table($controller);
				}
			}
		}
		
		function check_table($name = null) {
			//global WP variables
			global $wpdb;		
			if (!is_admin()) return;
		
			//ensure that a "name" was passed
			if (!empty($name)) {
				//add the WP prefix to the table name
				$oldname = $name;
				$name = $wpdb -> prefix . $name;
				
				//make sure that the table fields are available
				if (!empty($this -> tables[$oldname])) {																		
					//check if the table exists. boolean value returns				
					$query = "SHOW TABLES LIKE '" . $name . "'";
					if (!$wpdb -> get_var($query)) {													
						//let's start the query for a new table!
						$query = "CREATE TABLE `" . $name . "` (";
						$c = 1;
						
						//loop the table fields.
						foreach ($this -> tables[$oldname] as $field => $attributes) {
							//we might need to use a KEY declaration
							//in case not "key", continue with normal attributes set.
							if ($field != "key") {
								//append the field name and attributes
								$query .= "`" . $field . "` " . $attributes . "";
							} else {
								//this is a "key" field. declare it
								$query .= "" . $attributes . "";
							}
							
							//the last query doesn't get a comma at the end.
							//ensure that it is not the last query section.
							if ($c < count($this -> tables[$oldname])) {
								//append a comma "," to the query
								$query .= ",";
							}
							
							$c++;
						}
						
						if (array_key_exists('id', $this -> tables[$oldname])) {
							$query .= ", INDEX (`id`)";
						}
						
						//end the query!
						$query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";
						
						if (!empty($query)) {
							$this -> table_query[$name] = $query;
						}
					} else {							
						//get the current fields for this table.
						$field_array = $this -> get_fields($oldname);
						
						//loop the fields of the table
						foreach ($this -> tables[$oldname] as $field => $attributes) {					
							//make sure that its not a KEY value.
							if ($field != "key") {
								//add the database table field.
								$this -> add_field($oldname, $field, $attributes);
							}
						}
					}
					
					//make sure that the query is not empty.
					if (!empty($this -> table_query)) {						
						foreach ($this -> table_query as $query) {
							$wpdb -> query($query);
						}
					}
				}
			} else {
				return false;
			}
		}
		
		/**
		 * Retrieves all the fields for a specific database table
		 * @param $table STRING The name of the table to check.
		 * @return $field_array ARRAY An array of fields for the specific table.
		 *
		 **/
		function get_fields($table = null) {	
			global $wpdb;
		
			//make sure the table nae is available
			if (!empty($table)) {
				$fullname = $wpdb -> prefix . $table;
				
				$field_array = array();
				if ($fields = $wpdb -> get_results("SHOW COLUMNS FROM " . $fullname)) {
					foreach ($fields as $field) {
						$field_array[] = $field -> Field;
					}
				}
				
				return $field_array;
			}
			
			return false;
		}
		
		function delete_field($table = null, $field = null) {
			global $wpdb;
			
			if (!empty($table)) {			
				if (!empty($field)) {				
					$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` DROP `" . $field . "`";
					
					if ($wpdb -> query($query)) {
						return true;
					}
				}
			}
	
			return false;
		}
		
		function change_field($table = null, $field = null, $newfield = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;
			
			if (!empty($table)) {		
				if (!empty($field)) {			
					if (!empty($newfield)) {				
						$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` CHANGE `" . $field . "` `" . $newfield . "` " . $attributes . "";
	
						if ($wpdb -> query($query)) {
							return true;
						}
					}
				}
			}
			
			return false;
		}
		
		function add_field($table = null, $field = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;
		
			if (!empty($table)) {
				if (!empty($field)) {
					$field_array = $this -> get_fields($table);
	
					if (!empty($field_array)) {				
						if (!in_array($field, $field_array)) {				
							$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` ADD `" . $field . "` " . $attributes . "";
							if ($field == "id") { $query .= ", ADD PRIMARY KEY (id)"; }
							
							if ($wpdb -> query($query)) {
								return true;
							}
						}
					}
				}
			}
			
			return false;
		}
		
		function generate_poststring($post = array()) {
			$_POST = (empty($post)) ? $_POST : $post;
		
			if (!empty($_POST)) {
				$string = '';
				$p = 1;
			
				foreach ($_POST as $key => $val) {
					if (!empty($val)) {
						if (is_array($val)) {
							foreach ($val as $vkey => $vval) {
								if (!empty($vval) && !is_array($vval) && !is_object($vval)) {							
									$string .= '' . $vkey . '=' . urlencode($vval) . '&';
								}
							}
						} else {
							$string .= '' . $key . '=' . urlencode($val) . '&';
						}
					}
				}
				
				$string = rtrim($string, '&');
				return $string;
			}
			
			return false;
		}
		
		function userdata($user_id = null) {
			if (!empty($user_id)) {
				if ($user = get_userdata($user_id)) {
					return $user;
				}
			}
		
			return false;
		}
	
		function vendor($name = null, $pre = 'class', $classit = true) {
			if (!empty($name)) {
				$filename = $pre . '.' . strtolower($name) . '.php';
				$filepath = rtrim(dirname(__FILE__), DS) . DS . 'vendors' . DS;
				$filefull = $filepath . $filename;
			
				if (file_exists($filefull)) {
					require_once($filefull);
					
					if ($classit == true) {
						$class = $this -> pre . $name;
						
						if (${$name} = new $class) {
							return ${$name};
						}
					} else {
						return true;
					}
				}
			}
		
			return false;
		}
		
		function render_field($field_id = null, $fieldset = true, $optinid = null, $showcaption = true, $watermark = true, $instance = null) {
			global $Field, $Html, $wpmltabindex, $Mailinglist, $Subscriber;
		
			if (!empty($field_id)) {
				if ($field = $Field -> get($field_id)) {
					echo '<div class="newsletters-fieldholder ' . $field -> slug . '">';
				
					if ($fieldset == true && $field -> type != "special") {
						echo '<label for="' . $this -> pre . '-' . $optinid . $field -> slug . '" class="' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
						_e($field -> title);
						if ($field -> required == "Y") { echo ' <span class="' . $this -> pre . 'required">&#42;</span>'; };
						echo '</label>';
					}
				
					switch ($field -> type) {
						case 'text'				:
							if (!empty($_GET['email']) && $field -> slug == "email") {
								$_POST['email'] = $_GET['email'];
							}
						
							echo '<input class="' . $this -> pre . ' widefat ' . $this -> pre . 'text' . ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' type="text" name="' . $field -> slug . '" value="' . esc_attr(stripslashes($_POST[$field -> slug])) . '" />';
							
							if (!empty($field -> watermark) && $watermark == true) {
								?>
								
								<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('#<?php echo $this -> pre; ?>-<?php echo $optinid; ?><?php echo $field -> slug; ?>').Watermark('<?php echo addslashes(__($field -> watermark)); ?>');
								});
								</script>
								
								<?php
							}
							
							break;
						case 'special'			:						
							switch ($field -> slug) {
								case 'list'				:
								default 				:								
									if (preg_match("/[0-9]/si", $optinid, $matches)) {
										$number = $matches[0];
										
										$list = (empty($instance['list'])) ? $_POST['list_id'][0] : $instance['list'];
										if ($this -> is_plugin_active('qtranslate')) {
											$list = qtrans_use($instance['lang'], $list, false);
										}
										
										if (!empty($list)) {
											if (is_numeric($list)) {
												echo '<input type="hidden" name="list_id[]" value="' . $list . '" />';
											} else {
												echo '<label for="' . $this -> pre . '-' . $optinid . $field -> slug . '" class="' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
												_e($field -> title);
												if ($field -> required == "Y") { echo ' <span class="' . $this -> pre . 'required">&#42;</span>'; };
												echo '</label>';
												
												$instance['lists'] = array_filter(explode(",", $instance['lists']));
												$lists = (empty($instance['lists'])) ? $Mailinglist -> select(false) : $Mailinglist -> select(true, $instance['lists']);
												
												if ($list == "checkboxes") {
													foreach ($lists as $list_id => $list_title) {
														echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '">';
														echo '<input' . ((!empty($_POST['list_id']) && in_array($list_id, $_POST['list_id'])) ? ' checked="checked"' : '') . ' type="checkbox" name="list_id[]" value="' . $list_id . '" class="newsletters-list-checkbox" id="' . $optinid . $field -> slug . '-list-checkbox" /> ';
														echo __($list_title) . '</label>';
													}
												} else {
													echo '<select class="' . ((!empty($Subscriber -> errors['list_id'])) ? ' wpmlfielderror ' : '') . $this -> pre . ' autowidth widefat ' . $this -> pre . 'select newsletters-list-select" id="' . $optinid . $field -> slug . '-list-select" name="list_id[]">';
													echo '<option value="">' . __('- Select -', $this -> plugin_name) . '</option>';
													
													foreach ($lists as $list_id => $list_title) {
														echo '<option' . ((!empty($_POST['list_id']) && $_POST['list_id'][0] == $list_id) ? ' selected="selected"' : '') . ' value="' . $list_id . '">' . __($list_title) . '</option>';
													}
													
													echo '</select>';
												}
												
												if (!empty($field -> caption) && $showcaption == true && $field -> type != "special") {
													echo '<span class="' . $this -> pre . 'customfieldcaption">' . __(stripslashes($field -> caption)) . '</span>';
												}
											}
										}
									}									
									break;
							}
							break;
						case 'textarea'			:
							echo '<textarea class="' . $this -> pre . ' widefat ' . $this -> pre . 'textarea' . ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' rows="3" name="' . $field -> slug . '">' . strip_tags($_POST[$field -> slug]) . '</textarea>';
							break;
						case 'select'			:
							echo '<select class="' . $this -> pre . ' widefat ' . $this -> pre . 'select' . ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : '') . '" style="width:auto;" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' name="' . $field -> slug . '">';
							echo '<option value="">' . __('- Select -', $this -> plugin_name) . '</option>';
							
							$options = unserialize($field -> fieldoptions);
							if (!empty($options)) {
								foreach ($options as $name => $value) {
									$select = (!empty($_POST[$field -> slug]) && $_POST[$field -> slug] == $name) ? 'selected="selected"' : '';
									echo '<option ' . $select . ' value="' . $name . '">' . __($value) . '</option>';
								}
							}
							
							echo '</select>';
							break;
						case 'radio'			:
							$options = unserialize($field -> fieldoptions);
							if (!empty($options)) {
								$r = 1;
								
								foreach ($options as $name => $value) {
									$checked = ($_POST[$field -> slug] == $name) ? 'checked="checked"' : '';
									echo '<label class="wpmlradiolabel ' . $this -> pre . '"><input class="' . $this -> pre . 'radio' . ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="radio" ' . $checked . ' name="' . $field -> slug . '" value="' . $name . '" /> ' . __($value) . '</label>';
									
									$r++;
								}
							}
							break;
						case 'checkbox'			:
							$options = unserialize($field -> fieldoptions);
							if (!empty($options)) {
								foreach ($options as $name => $value) {
									$checked = (!empty($_POST[$field -> slug]) && is_array($_POST[$field -> slug]) && @in_array($name, $_POST[$field -> slug])) ? 'checked="checked"' : '';
									echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '"><input class="' . $this -> pre . 'checkbox' . ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="checkbox" ' . $checked . ' name="' . $field -> slug . '[]" value="' . $name . '" /> ' . __($value) . '</label>';
								}
							}
							break;
						case 'file'				:						
							$filetypes = false;
							if (!empty($field -> filetypes)) {
								if (($types = @explode(",", $field -> filetypes)) !== false) {
									if (is_array($types)) {
										$f = 1;
										foreach ($types as $type) {
											$filetypes .= '*' . $type;
											if ($f < count($types)) { $filetypes .= "; "; }
											$f++;
										}
									}
								}
							}
						
							?>
							
							<input type="file" <?php echo $Html -> tabindex($optinid); ?> name="file_upload_<?php echo $field -> id; ?>" value="" id="file_upload_<?php echo $field -> id; ?><?php echo $optinid; ?>" />
							<input type="hidden" name="<?php echo $field -> slug; ?>" value="<?php echo esc_attr(stripslashes($_POST[$field -> slug])); ?>" id="<?php echo $this -> pre . '-' . $optinid . '' . $field -> slug; ?>" />
							
							<script type="text/javascript">
							jQuery(function() {
								jQuery('#file_upload_<?php echo $field -> id; ?><?php echo $optinid; ?>').uploadify({
									'swf'      			: 	'<?php echo $this -> url(); ?>/images/uploadify/uploadify.swf',
									'uploader' 			: 	'<?php echo $this -> url(); ?>/vendors/uploadify/upload.php',
									'buttonText'		:	'<?php _e('Select File', $this -> plugin_name); ?>',
									'debug'				:	false,
									'multi'				:	false,
									'uploadLimit'		:	'1',
									<?php if (!empty($filetypes)) : ?>'fileTypeExts'	:	'<?php echo $filetypes; ?>',<?php endif; ?>
									<?php if (!empty($field -> filesizelimit)) : ?>'fileSizeLimit'	:	'<?php echo $field -> filesizelimit; ?>',<?php endif; ?>
									'removeCompleted'	:	false,
									'onUploadStart'		:	function(file) {
										<?php if (!is_admin()) : ?>jQuery('.<?php echo $this -> pre; ?>button').button('option', "disabled", true);<?php endif; ?>
									},
									'onUploadError' 	: 	function(file, errorCode, errorMsg, errorString) {
										console.log('The file ' + file.name + ' could not be uploaded: ' + errorString);
									},
									'onUploadSuccess' 	: 	function(file, data, response) {
										console.log('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
										console.log('Updating field #<?php echo $this -> pre . '-' . $optinid . '' . $field -> slug; ?>');
										jQuery('#<?php echo $this -> pre . '-' . $optinid . '' . $field -> slug; ?>').val(data);
									},
									'onUploadComplete'	:	function(file) {
										<?php if (!is_admin()) : ?>jQuery('.<?php echo $this -> pre; ?>button').button('option', "disabled", false);<?php endif; ?>
									}
								});
							});
							</script>
							
							<?php						
							break;
						case 'pre_country'		:
							global $Db, $Form, $wpmlCountry;
							
							if ($countries = $wpmlCountry -> select()) {						
								?>
								
								<select class="<?php echo $this -> pre; ?>country <?php echo $this -> pre; ?> widefat <?php echo $this -> pre; ?>precountry<?php echo ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : ''); ?>" id="<?php echo $this -> pre; ?>-<?php echo $optinid . $field -> slug; ?>" <?php echo $Html -> tabindex($optinid); ?> name="<?php echo $field -> slug; ?>">
									<option value=""><?php _e('- Select Country -', $this -> plugin_name); ?></option>
									<?php foreach ($countries as $id => $value) : ?>
										<option <?php echo (!empty($_POST[$field -> slug]) && $_POST[$field -> slug] == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
								
								<?php
							}
							break;
						case 'pre_date'			:													
							?>
						
							<select class="<?php echo $this -> pre; ?> widefat <?php echo $this -> pre; ?>predate<?php echo ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : ''); ?>" style="width:auto;" id="<?php echo $this -> pre; ?>-<?php echo $optinid; ?><?php echo $field -> slug; ?>" <?php echo $Html -> tabindex($optinid); ?> class="autowidth" name="<?php echo $field -> slug; ?>[y]">
								<option value=""><?php _e('YYYY', $this -> plugin_name); ?></option>
								<?php for ($y = 00; $y <= 99; $y++) : ?>
								<?php $sel = ($_POST[$field -> slug][0] == '19' . $y || $_POST[$field -> slug]['y'] == '19' . $y) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="19<?php echo $y; ?>">19<?php echo $y; ?></option>
								<?php endfor; ?>
								<?php for ($y = 00; $y <= 99; $y++) : ?>
									<?php $sel = ($_POST[$field -> slug][0] == '20' . $y || $_POST[$field -> slug]['y'] == '20' . $y) ? 'selected="selected"' : ''; ?>
									<?php $y = (strlen($y) == 1) ? '0' . $y : $y; ?>
										<option <?php echo $sel; ?> value="20<?php echo $y; ?>">20<?php echo $y; ?></option>
								<?php endfor; ?>
							</select>
							
							<select class="<?php echo $this -> pre; ?> widefat<?php echo ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : ''); ?>" style="width:auto;" <?php echo $Html -> tabindex($optinid); ?> class="autowidth" name="<?php echo $field -> slug; ?>[m]">
								<option value=""><?php _e('MM', $this -> plugin_name); ?></option>
								<?php for ($m = 1; $m <= 12; $m++) : ?>
								<?php $sel = ($_POST[$field -> slug][1] == $m || $_POST[$field -> slug]['m'] == $m) ? 'selected="selected"' : ''; ?>
								<?php $number = (strlen($m) == 1) ? '0' . $m : $m; ?>
								<option <?php echo $sel; ?> value="<?php echo $number; ?>"><?php echo $number; ?></option>
								<?php endfor; ?>
							</select>
							
							<select class="<?php echo $this -> pre; ?> widefat<?php echo ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : ''); ?>" style="width:auto;" <?php echo $Html -> tabindex($optinid); ?> class="autowidth" name="<?php echo $field -> slug; ?>[d]">
								<option value=""><?php _e('DD', $this -> plugin_name); ?></option>
								<?php for ($d = 1; $d <= 31; $d++) : ?>
								<?php $sel = ($_POST[$field -> slug][2] == $d || $_POST[$field -> slug]['d'] == $d) ? 'selected="selected"' : ''; ?>
								<?php $number = (strlen($d) == 1) ? '0' . $d : $d; ?>
								<option <?php echo $sel; ?> value="<?php echo $number; ?>"><?php echo $number; ?></option>
								<?php endfor; ?>
							</select>
							
							<?php
							break;
						case 'pre_gender'		:
							?>
							
							<select class="<?php echo $this -> pre; ?> widefat <?php echo $this -> pre; ?>pregender<?php echo ((!empty($_POST[$this -> pre . 'errors'][$field -> slug])) ? ' ' . $this -> pre . 'fielderror' : ''); ?>" style="width:auto;" id="<?php echo $this -> pre; ?>-<?php echo $optinid; ?><?php echo $field -> slug; ?>" <?php echo $Html -> tabindex($optinid); ?> name="<?php echo $field -> slug; ?>">
								<option value=""><?php _e('- Select Gender -', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST[$field -> slug]) && $_POST[$field -> slug] == "male") ? 'selected="selected"' : ''; ?> value="male"><?php _e('Male', $this -> plugin_name); ?></option>
								<option <?php echo (!empty($_POST[$field -> slug]) && $_POST[$field -> slug] == "female") ? 'selected="selected"' : ''; ?> value="female"><?php _e('Female', $this -> plugin_name); ?></option>
							</select>
							
							<?php
							break;
					}
					
					if (!empty($field -> caption) && $showcaption == true && $field -> type != "special") {
						echo '<span class="' . $this -> pre . 'customfieldcaption">' . __(stripslashes($field -> caption)) . '</span>';
					}
					
					echo '</div>';
					
					if (!empty($field -> type) && $field -> type == "file") {
						if (!empty($field -> filesizelimit)) { echo '<small>' . sprintf(__('Maximum file size of <strong>%s</strong>', $this -> plugin_name), $field -> filesizelimit) . '</small><br/>'; }	
						if (!empty($filetypes)) { echo '<small>' . sprintf(__('Allowed file types are <strong>%s</strong>', $this -> plugin_name), $filetypes) . '</small><br/>'; }
						
						if (!empty($_POST[$field -> slug])) {
							echo $Html -> file_custom_field($_POST[$field -> slug], $field -> filesizelimit, $filetypes);
						}
					}
				}
			}
			
			return true;
		}
	
	    function output_custom_fields($subscriber = null) {
	        global $Db, $Html, $wpmlCountry, $Field;
	        $customfields = "";
	        ob_start();
	
	        if (!empty($subscriber)) {
	            $fields = $Field -> get_all();
	
	            if (!empty($fields)) {
	                $class = "alternate";
	
	                ?><table><tbody><?php
	
	  			    foreach ($fields as $field) {
						if (!empty($subscriber -> {$field -> slug})) {
	                        ?>
	
							<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
								<th nowrap="nowrap"><?php _e($field -> title); ?></th>
								<td>
									<?php if ($field -> type == "radio" || $field -> type == "select") : ?>
										<?php $fieldoptions = unserialize($field -> fieldoptions); ?>
										<?php echo __($fieldoptions[$subscriber -> {$field -> slug}]); ?>
									<?php elseif ($field -> type == "checkbox") : ?>
										<?php $supoptions = unserialize($subscriber -> {$field -> slug}); ?>
										<?php $fieldoptions = unserialize($field -> fieldoptions); ?>
										<?php if (!empty($supoptions) && is_array($supoptions)) : ?>
											<?php foreach ($supoptions as $supopt) : ?>
												&raquo;&nbsp;<?php echo __($fieldoptions[$supopt]); ?><br/>
											<?php endforeach; ?>
										<?php else : ?>
											<?php _e('none', $this -> plugin_name); ?>
										<?php endif; ?>
									<?php elseif ($field -> type == "file") : ?>
										<?php echo $Html -> file_custom_field($subscriber -> {$field -> slug}); ?>
									<?php elseif ($field -> type == "pre_country") : ?>
										<?php $Db -> model = $wpmlCountry -> model; ?>
										<?php echo $Db -> field('value', array('id' => $subscriber -> {$field -> slug})); ?>
									<?php elseif ($field -> type == "pre_date") : ?>
										<?php $date = @unserialize($subscriber -> {$field -> slug}); ?>
										<?php if (!empty($date) && is_array($date)) : ?>
											<?php echo $date['y']; ?>-<?php echo $date['m']; ?>-<?php echo $date['d']; ?>
										<?php endif; ?>
									<?php else : ?>
										<?php echo $subscriber -> {$field -> slug}; ?>
									<?php endif; ?>
								</td>
							</tr>
	
	                        <?php
						}
					}
	
	                ?></tbody></table><?php
				}
	        }
	        
	        $customfields = ob_get_clean();
	        return $customfields;
	    }
		
		function gen_auth($subscriber_id = null, $mailinglist_id = null) {
			$authkey = md5(rand(1, 999));
			
			if (!empty($subscriber_id)) {
				global $Db;
				
				if (!empty($mailinglist_id)) {
					$Db -> model = 'SubscribersList';
					if ($subscriberslist = $Db -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist_id))) {
						if ($subscriberslist -> authinprog == "Y" || !empty($subscriberslist -> authkey)) {
							$authkey = $subscriberslist -> authkey;
						} else {
							$Db -> save_field('authkey', $authkey, array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
							$Db -> save_field('authinprog', "Y", array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
						}
					}
				} else {
					$Db -> model = 'Subscriber';
					if ($subscriber = $Db -> find(array('id' => $subscriber_id))) {
						if ($subscriber -> authinprog == "Y" || !empty($subscriber -> authkey)) {
							$authkey = $subscriber -> authkey;
						} else {
							$Db -> save_field('authkey', $authkey, array('id' => $subscriber_id));
							$Db -> save_field('authinprog', "Y", array('id' => $subscriber_id));
						}
					}
				}
			}
			
			return $authkey;
		}
		
		function htmltf($format = 'html') {
			switch ($format) {
				case 'html'			:
					return true;
					break;
				case 'text'			:
					return false;
					break;
			}
			
			return true;
		}
		
		function gen_unsubscribe_link($subscriber = null, $user = null, $theme_id = null, $history_id = null, $alllists = false, $urlonly = false) {
			global $Html, $Subscriber;
		
			if (!empty($subscriber) || !empty($user)) {
				$linktext = __($this -> get_option('unsubscribetext'));
				$auth_id = (empty($subscriber)) ? $user -> ID : $subscriber -> id;
				$auth_string = (empty($subscriber)) ? $user -> roles[0] : $subscriber -> mailinglist_id;
				$authkey = $this -> gen_auth($auth_id, $auth_string);
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme, ${'newsletters_acolor'};
					
					if (empty(${'newsletters_acolor'})) {
						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
						$acolor = $wpdb -> get_var($acolorquery);
						${'newsletters_acolor'} = $acolor;
					} else {
						$acolor = ${'newsletters_acolor'};
					}
					
					$style = "color:" . $acolor . ";";
				}
				
				if (!empty($subscriber)) {
					$mailinglists = "";
					if (!empty($alllists) && $alllists == true) {				
						$slists = $Subscriber -> mailinglists($subscriber -> id);
						$mailinglists = implode(",", $slists);
						$linktext = __($this -> get_option('unsubscribealltext'));
					} else {				
						$slists = maybe_unserialize($subscriber -> mailinglists);
						
						if (!empty($slists)) {
							$mailinglists = implode(",", $slists);
						} else {
							$mailinglists = $subscriber -> mailinglist_id;
						}
					}
					
					$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'subscriber_email=' . $subscriber -> email . '&' . $this -> pre . 'mailinglist_id=' . $mailinglists . '&authkey=' . $authkey;
				} elseif (!empty($user)) {
					$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&user_id=' . $user -> ID . '&user_email=' . $user -> user_email . '&authkey=' . $authkey;
				}
					
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				
				if ((!empty($urlonly) && $urlonly == true) || empty($subscriber -> format) || $subscriber -> format == "html") {
					$unsubscribelink = '<a href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$unsubscribelink = $url;
				}
				
				return $unsubscribelink;
			}
			
			return false;
		}
		
		function gen_manage_link($subscriber = array(), $theme_id = null) {
			global $Db, $Subscriber, $Html, $Auth;
		
			if (!empty($subscriber)) {
				$linktext = $this -> get_option('managelinktext');
				
				if (empty($subscriber -> cookieauth)) {
					$subscriberauth = $Auth -> gen_subscriberauth();
					$Db -> model = $Subscriber -> model;
					$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
				} else {
					$subscriberauth = $subscriber -> cookieauth;
				}
				
				$url = $Html -> retainquery('method=loginauth&email=' . $subscriber -> email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true));
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme, ${'newsletters_acolor'};
					
					if (empty(${'newsletters_acolor'})) {
						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
						$acolor = $wpdb -> get_var($acolorquery);
						${'newsletters_acolor'} = $acolor;
					} else {
						$acolor = ${'newsletters_acolor'};
					}
						
					$style = "color:" . $acolor . ";";
				}
				
				if (empty($subscriber -> format) || $subscriber -> format == "html") {
					$managelink = '<a href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$managelink = $url;
				}
				
				return $managelink;
			}
			
			return false;
		}
		
		function gen_online_link($subscriber = null, $user = null, $history_id = null, $onlyurl = false, $theme_id = null) {	
			if (!empty($history_id)) {
				global $Db, $Html, $History;
				$Db -> model = $History -> model;
				
				if ($email = $Db -> find(array('id' => $history_id))) {
					$auth_id = (empty($subscriber)) ? $user -> ID : $subscriber -> id;
					$authkey = $this -> gen_auth($auth_id);
					
					if (!empty($subscriber)) {
						$url = home_url() . '/?' . $this -> pre . 'method=newsletter&id=' . $email -> id . '&mailinglist_id=' . $subscriber -> mailinglist_id . '&subscriber_id=' . $subscriber -> id . '&authkey=' . $authkey;
					} else {
						$querystring = $this -> pre . 'method=newsletter&id=' . $email -> id . '&user_id=' . $user -> ID . '&authkey=' . $authkey;
						$url = $Html -> retainquery($querystring, home_url());
					}
					
					if (!empty($theme_id)) {
						global $wpdb, $Theme, ${'newsletters_acolor'};
						
						if (empty(${'newsletters_acolor'})) {
							$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
							$acolor = $wpdb -> get_var($acolorquery);
							${'newsletters_acolor'} = $acolor;
						} else {
							$acolor = ${'newsletters_acolor'};
						}
						
						$style = "color:" . $acolor . ";";
					}
					
					if (!empty($onlyurl) && $onlyurl == true) {
						return $url;
					} else {
						if (empty($subscriber -> format) || $subscriber -> format == "html") {
							$onlinelink = '<a href="' . $url . '" style="' . $style . '">' . $this -> get_option('onlinelinktext') . '</a>';
						} else {
							$onlinelink = $url;
						}
					}
					
					return $onlinelink;
				}
			}
			
			return false;
		}
		
		function gen_activation_link($subscriber = array(), $theme_id = null) {
			global $Html;
		
			if (!empty($subscriber)) {
				$linktext = __($this -> get_option('activationlinktext'));
				$authkey = $this -> gen_auth($subscriber -> id, $subscriber -> mailinglist_id);
				
				if (!empty($theme_id)) {
					global $wpdb, $Theme, ${'newsletters_acolor'};
					
					if (empty(${'newsletters_acolor'})) {
						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . $theme_id . "' LIMIT 1";
						$acolor = $wpdb -> get_var($acolorquery);
						${'newsletters_acolor'} = $acolor;
					} else {
						$acolor = ${'newsletters_acolor'};	
					}
					
					$style = "color:" . $acolor . ";";
				}
				
				$mailinglist_id = (empty($subscriber -> mailinglists)) ? $subscriber -> mailinglist_id : @implode(",", $subscriber -> mailinglists);
				
				$querystring = $this -> pre . 'method=activate&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'subscriber_email=' . $subscriber -> email . '&' . $this -> pre . 'mailinglist_id=' . $mailinglist_id . '&authkey=' . $authkey;
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				
				if (empty($subscriber -> format) || $subscriber -> format == "html") {
					$activationlink = '<a href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$activationlink = $url;
				}
				
				return $activationlink;
			}
			
			return false;
		}
		
		function gen_tracking_link($eunique = null) {
			$tracking = "";
			
			if (!empty($eunique)) {
				if ($this -> get_option('tracking') == "Y") {
					$tracking = '<img style="display:none;" src="' . home_url() . '/?' . $this -> pre . 'method=track&id=' . $eunique . '" />';
				}	
			}
			
			return $tracking;
		}
		
		function strip_set_variables($message = null) {
			if (!empty($message)) {
				global $Db, $Field;
			
				$patterns = array("/\{email\}/", "/\{unsubscribe\}/", "/\{blogname\}/", "/\{siteurl\}/", "/\{activationlink\}/");			
				$newpatterns = array(
					"/\[" . $this -> pre . "email\]/", 
					'/\[' . $this -> pre . 'field name="email"\]/',
					"/\[" . $this -> pre . "unsubscribe\]/", 
					"/\[" . $this -> pre . "blogname\]/", 
					"/\[" . $this -> pre . "siteurl\]/", 
					"/\[" . $this -> pre . "activate\]/", 
					"/\[" . $this -> pre . "manage\]/", 
					"/\[" . $this -> pre . "online\]/", 
					"/\[" . $this -> pre . "track\]/", 
					"/\[" . $this -> pre . "mailinglist\]/",
					"/\[" . $this -> pre . "subject\]/",
					"/\[" . $this -> pre . "historyid\]/",
					"/\[" . $this -> pre . "unsubscribecomments\]/",
					"/\[" . $this -> pre . "bouncecount\]/",
					"/\[" . $this -> pre . "customfields\]/",
				);
				
				$Db -> model = $Field -> model;
				
				if ($fields = $Db -> find_all()) {
					foreach ($fields as $field) {
						$patterns[] = "/\{" . $field -> slug . "\}/";
						$newpatterns[] = '/\[' . $this -> pre . 'field name="' . $field -> slug . '"\]/';
					}
				}
				
				$message = preg_replace($patterns, "", $message);
				$message = preg_replace($newpatterns, "", $message);
				return $message;
			}
			
			return false;
		}
		
		function gen_role_names($user = null) {
			$role_names = "";
			
			if (!empty($user -> roles) && is_array($user -> roles)) {
				global $wp_roles;
				$role_names = array();
				
				foreach ($user -> roles as $role_key) {
					$role_names[] = $wp_roles -> role_names[$role_key];
				}
				
				$role_names = implode(", ", $role_names);
			}
			
			return $role_names;
		}
		
		function gen_mailinglist_names($subscriber = null) {
			global $wpdb, $Mailinglist;
			$mailinglist_names = "";
			$titles = array();
			
			if (empty($subscriber -> mailinglists)) {			
				if (!empty($_POST['mailinglists'])) {
					$subscriber -> mailinglists = $_POST['mailinglists'];
				} else {
					$subscriber -> mailinglists = array($subscriber -> mailinglist_id);
				}
			}
		
			if (!empty($subscriber -> mailinglists)) {			
				foreach ($subscriber -> mailinglists as $list_id) {
					if ($title = $Mailinglist -> get_title_by_id($list_id)) {
						$titles[] = __($title);
					}
				}
				
				$mailinglist_names = implode(", ", $titles);
			}
			
			return $mailinglist_names;
		}
		
		function process_set_variables($subscriber = null, $user = null, $message = null, $history_id = null, $eunique = null, $issubject = false) {
			global $wpdb, $Db, $wpmlCountry, $Field, $Mailinglist, $Html, $History, $Theme;
			
			if (!empty($issubject) && $issubject == true) {
				$subject = $message;
			}
			
			if (!empty($message)) {
				if (!empty($subscriber)) {			
					$Db -> model = $Mailinglist -> model;
					$mailinglist_name = $Db -> field('title', array('id' => $subscriber -> mailinglist_id));
					
					if (!empty($history_id)) {
						$Db -> model = $History -> model;
						$subject = $Db -> field('subject', array('id' => $history_id));
						
						$Db -> model = $History -> model;
						$post_id = $Db -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post;
							$post = $getpost;
						}
						
						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $history_id . "' LIMIT 1";
						$theme_id = $wpdb -> get_var($themeidquery);
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";
						$theme_id = $wpdb -> get_var($themeidquery);
					}
					
					$newsearch = array(
						"/\[" . $this -> pre . "email\]/", 
						'/\[' . $this -> pre . 'field name="email"\]/',
						"/\[" . $this -> pre . "unsubscribe\]/", 
						"/\[" . $this -> pre . "unsubscribeurl\]/", 
						"/\[" . $this -> pre . "unsubscribeall\]/",
						"/\[" . $this -> pre . "blogname\]/", 
						"/\[" . $this -> pre . "siteurl\]/", 
						"/\[" . $this -> pre . "activate\]/", 
						"/\[" . $this -> pre . "manage\]/", 
						"/\[" . $this -> pre . "online\]/", 
						"/\[" . $this -> pre . "track\]/", 
						"/\[" . $this -> pre . "mailinglist\]/",
						"/\[" . $this -> pre . "subject\]/",
						"/\[" . $this -> pre . "historyid\]/",
						"/\[" . $this -> pre . "unsubscribecomments\]/",
						"/\[" . $this -> pre . "bouncecount\]/",
						"/\[" . $this -> pre . "customfields\]/",
					);
					
					$newreplace = array(
						$subscriber -> email, 
						$subscriber -> email,
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false), 
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, true),
						get_bloginfo('name'), 
						home_url(), 
						$this -> gen_activation_link($subscriber, $theme_id), 
						$this -> gen_manage_link($subscriber, $theme_id), 
						$this -> gen_online_link($subscriber, false, $history_id, false, $theme_id), 
						$this -> gen_tracking_link($eunique), 
						$this -> gen_mailinglist_names($subscriber),
						stripslashes($subject),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						$subscriber -> bouncecount,
						$this -> output_custom_fields($subscriber),
					);
					
					$fields = $Field -> get_all();
					
					if (!empty($fields)) {
						foreach ($fields as $field) {
							$search[] = "/\{" . $field -> slug . "\}/";
							$newsearch[] = '/\[' . $this -> pre . 'field name="' . $field -> slug . '"\]/';
							
							switch ($field -> type) {
								case 'pre_country'		:
									$Db -> model = $wpmlCountry -> model;
									$replace[] = $Db -> field('value', array('id' => $subscriber -> {$field -> slug}));
									$newreplace[] = $Db -> field('value', array('id' => $subscriber -> {$field -> slug}));
									break;
								case 'pre_date'			:
									$date = @unserialize($subscriber -> {$field -> slug});
									if (!empty($date) && is_array($date)) {
										$replace[] = $date['y'] . '-' . $date['m'] . '-' . $date['d'];
										$newreplace[] = $date['y'] . '-' . $date['m'] . '-' . $date['d'];
									}
									break;
								case 'pre_gender'		:
									$replace[] = $Html -> gender($subscriber -> {$field -> slug});
									$newreplace[] = $Html -> gender($subscriber -> {$field -> slug});
									break;
								case 'radio'			:
								case 'select'			:
									$value = $subscriber -> {$field -> slug};
									$fieldoptions = maybe_unserialize($field -> fieldoptions);
									
									$replace[] = __($fieldoptions[$value]);
									$newreplace[] = __($fieldoptions[$value]);
									break;
								default					:
									$value = $subscriber -> {$field -> slug};
									if (!empty($value)) {
										if (($varray = @unserialize($value)) !== false) {
											$subscriber -> {$field -> slug} = '';
											$newline = (empty($subscriber -> format) || $subscriber -> format == "html") ? "<br/>" : "\r\n";
											
											foreach ($varray as $vkey => $vval) {
												$subscriber -> {$field -> slug} .= '&raquo; ' . __($vval) . $newline;
											}
										}
									}
								
									$replace[] = $subscriber -> {$field -> slug};
									$newreplace[] = $subscriber -> {$field -> slug};
									break;
							}
						}
					}
					
					$subject = preg_replace($newsearch, $newreplace, stripslashes($subject));
					$message = preg_replace($newsearch, $newreplace, stripslashes($message));
				} elseif (!empty($user)) {
					if (!empty($history_id)) {
						$Db -> model = $History -> model;
						$subject = $Db -> field('subject', array('id' => $history_id));
						
						$Db -> model = $History -> model;
						$post_id = $Db -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post;
							$post = $getpost;
						}
						
						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $history_id . "' LIMIT 1";
						$theme_id = $wpdb -> get_var($themeidquery);
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";
						$theme_id = $wpdb -> get_var($themeidquery);
					}
					
					$newsearch = array(
						"/\[" . $this -> pre . "email\]/", 
						'/\[' . $this -> pre . 'field name="email"\]/',
						"/\[" . $this -> pre . "unsubscribe\]/", 
						"/\[" . $this -> pre . "unsubscribeurl\]/", 
						"/\[" . $this -> pre . "unsubscribeall\]/",
						"/\[" . $this -> pre . "blogname\]/", 
						"/\[" . $this -> pre . "siteurl\]/", 
						"/\[" . $this -> pre . "activate\]/", 
						"/\[" . $this -> pre . "manage\]/", 
						"/\[" . $this -> pre . "online\]/", 
						"/\[" . $this -> pre . "track\]/", 
						"/\[" . $this -> pre . "mailinglist\]/",
						"/\[" . $this -> pre . "subject\]/",
						"/\[" . $this -> pre . "historyid\]/",
						"/\[" . $this -> pre . "unsubscribecomments\]/",
						"/\[" . $this -> pre . "bouncecount\]/",
						"/\[" . $this -> pre . "customfields\]/",
					);
					
					$newreplace = array(
						$user -> user_email, 
						$user -> user_email,
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false), 
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, true),
						get_bloginfo('name'), 
						home_url(), 
						"", 
						get_edit_user_link($user -> ID), 
						$this -> gen_online_link(false, $user, $history_id, false, $theme_id), 
						$this -> gen_tracking_link($eunique), 
						$this -> gen_role_names($user),
						stripslashes($subject),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						$subscriber -> bouncecount,
						"",
					);
					
					$subject = preg_replace($newsearch, $newreplace, stripslashes($subject));
					$message = preg_replace($newsearch, $newreplace, stripslashes($message));
				}
			}
			
			if (!empty($issubject) && $issubject == true) {	
				$subject = do_shortcode($subject);
				return $subject;
			} else {
				$message = do_shortcode($message);
				return $message;
			}
		}
		
		function gen_unsubscribe_comments() {
			/* Unsubscribe Comments */
			$unsubscribecomments = __('No feedback was provided by the subscriber.', $this -> plugin_name);
			if (!empty($_POST[$this -> pre . 'comments'])) {
				$unsubscribecomments = "";
				$unsubscribecomments .= __('Comments:', $this -> plugin_name) . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
				$unsubscribecomments .= stripslashes($_POST[$this -> pre . 'comments']) . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
			}
			
			return wpautop($unsubscribecomments);
		}
		
		function subscription_confirm($subscriber = array()) {
			global $wpdb, $Subscriber, $Mailinglist, $SubscribersList;
			
			if (!empty($subscriber)) {			
				if (!empty($_POST['list_id'])) {
					$subscriber -> mailinglists = $_POST['list_id'];	
				} elseif (!empty($subscriber -> mailinglists)) {
					//do nothing, it's ready
				} else {
					$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, false, false, "N");
				}
			
				if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {			
					if ($this -> get_option('activationemails') == "multiple") {
						foreach ($subscriber -> mailinglists as $list_id) {
							$isactive = $SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
							$subscriber -> mailinglist_id = $list_id;
							$subscriber -> mailinglists = array($list_id);
							$mailinglist = $Mailinglist -> get($list_id, false);
						
							if ($isactive == "N") {
								if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {									
									$subject = $this -> et_subject('confirm');
									$fullbody = $this -> et_message('confirm', $subscriber);
									$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
									$this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false);
								}
							}
						}
					} else {					
						$subject = $this -> et_subject('confirm');
						$fullbody = $this -> et_message('confirm', $subscriber);
						$message = $this -> render_email(false, array('subscriber' => $subscriber), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
						$this -> execute_mail($subscriber, false, $subject, $message, false, false, false, false);
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		function admin_subscription_notification($subscriber = array()) {	
			global $wpdb, $Mailinglist;
		
			if (!empty($subscriber)) {
				if ($this -> get_option('adminemailonsubscription') == "Y") {
					$adminemail = $this -> get_option('adminemail');
					
					if (!empty($subscriber -> mailinglist_id)) {
						$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $subscriber -> mailinglist_id . "'";					
						if ($email = $wpdb -> get_var($adminemailquery)) {
							$adminemail = $email;
						}
					}
				
					$to = (object) array('email' => $adminemail);
					$subject = $this -> et_subject('subscribe', $subscriber);
					$fullbody = $this -> et_message('subscribe', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
					
					$this -> execute_mail($to, false, $subject, $message, false, false, false, false);
					
					return true;
				}
			}
			
			return false;
		}
		
		function admin_unsubscription_notification($subscriber = array(), $mailinglist = array()) {
			global $wpdb, $Mailinglist, $Subscriber;
			
			if (!empty($subscriber) && !empty($mailinglist)) {
				if ($this -> get_option('adminemailonunsubscription') == "Y") {
					$adminemail = $this -> get_option('adminemail');
					
					if (!empty($subscriber -> mailinglist_id)) {
						$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $subscriber -> mailinglist_id . "'";					
						if ($email = $wpdb -> get_var($adminemailquery)) {
							$adminemail = $email;
						}
					}
					
					$to -> id = $Subscriber -> admin_subscriber_id();
					$to -> email = $adminemail;
					$subject = $this -> et_subject('unsubscribe', $subscriber);
					$fullbody = $this -> et_message('unsubscribe', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
					
					if ($this -> execute_mail($to, false, $subject, $message, false, false, false, false)) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function admin_bounce_notification($subscriber = array()) {	
			if ($this -> get_option('adminemailonbounce') == "Y") {		
				if (!empty($subscriber)) {			
					$to -> email = $this -> get_option('adminemail');
					$subject = $this -> et_subject('bounce', $subscriber);
					$fullbody = $this -> et_message('bounce', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('system'), false, $fullbody);
					$this -> execute_mail($to, false, $subject, $message, false, false, false, false);
					
					return true;
				}
			}
			
			return false;
		}
		
		function strip_title($title = null) {
			global $Html;
			return $Html -> sanitize($title, "_");
			
			return false;
		}
		
		function replace_history($match = array()) {
			if (!empty($match)) {
				global $Db, $History;
				
				$Db -> model = $History -> model;
				
				if ($emails = $Db -> find_all(array('sent' => "> 0"), false, array('modified', "DESC"))) {			
					$content = '';
				
					foreach ($emails as $email) {
						ob_start();
						
						?>
						
						<h3><a href="<?php echo get_option('home'); ?>?<?php echo $this -> pre; ?>method=newsletter&amp;id=<?php echo $email -> id; ?>" title="<?php echo $email -> subject; ?>"><?php echo $email -> subject; ?></a></h3>
						<div><small><?php _e('Sent on', $this -> plugin_name); ?> : <?php echo $email -> modified; ?></small></div>
						<?php echo $this -> strip_set_variables($email -> message); ?>
						
						<?php
						
						$content .= ob_get_clean();
					}
					
					$content = wpautop($content);
					return $content;
				}
			}
			
			return false;
		}
		
		function replace_meta($matches = array()) {
			if (!empty($matches[0])) {			
				if (preg_match("/" . $this -> pre . "meta\_([0-9]*)/i", $matches[0], $matches2)) {
					if (!empty($matches2[1])) {
						global $post_ID;
						$oldpostid = $post_ID;
						$post_ID = $matches2[1];
						
						ob_start();
						the_meta();
						$meta = ob_get_clean();
						
						$post_ID = $oldpostid;
						return $meta;
					}
				}
			}
			
			return false;
		}
		
		function remove_server_limits() {
			if (!ini_get('safe_mode')) {
				@set_time_limit(0);
				@ini_set('memory_limit', '1024M');
				@ini_set('upload_max_filesize', '128M');
				@ini_set('post_max_size', '1024M');
				@ini_set('max_execution_time', 720);
				@ini_set('max_input_time', 720);
				return true;
			}
	
			return false;
		}
		
		function set_time_limit($time = 0) {
			if (ini_get('max_execution_time')) {
				ini_set('max_execution_time', 0);
			}
	
			//ensure that "safe_mode" is turned off
			if (!ini_get('safe_mode')) {		
				//check if "set_time_limit" is available
				if (ini_get('set_time_limit')) {
					//set the "max_execution_time" to unlimited
					set_time_limit(0);
				}
			}
		}
		
		function gen_optin_id() {
			return (time() * rand(1,999));
		}
		
		function phpmailer_messageid() {
			$messageid = "<";
			$messageid .= md5(uniqid(time()));
			$messageid .= "@";
			$messageid .= $_SERVER['SERVER_NAME'];
			$messageid .= ">";
			return $messageid;
		}
		
		function spam_score($email = null, $options = "short") {
			$data = array("email" => $email, "options" => $options);                                                                    
			$data_string = json_encode($data);                                                                                   
			 
			$ch = curl_init('http://spamcheck.postmarkapp.com/filter');                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',                                                                                
			    'Content-Length: ' . strlen($data_string))                                                                       
			);                                                                                                                   
			 
			$result = curl_exec($ch);
			$result = json_decode($result);
			return $result;
		}
		
		function execute_mail($subscriber = null, $user = null, $subject = null, $message = null, $attachments = null, $history_id = null, $eunique = null, $shortlinks = true) {
			global $wpdb, $Db, $Html, $Email, $History, $phpmailer, $Mailinglist, $Subscriber, $SubscribersList, $orig_message, $wpml_message, $wpml_textmessage, $fromwpml;
			$sent = false;
			$fromwpml = true;
		
			if (empty($subscriber) && empty($user)) { $error[] = __("No subscriber specified", $this -> plugin_name); }
			if (empty($subject)) { $error[] = __('No subject specified', $this -> plugin_name); }
			if (empty($message)) { $error[] = __('No message specified', $this -> plugin_name); }
			
			global $wpdb, $History;
			if (!empty($history_id)) { 
				$query = "SELECT `from`, `fromname`, `text` FROM `" . $wpdb -> prefix . $History -> table . "` WHERE `id` = '" . $history_id . "'";
				$history = stripslashes_deep($wpdb -> get_row($query)); 
			}
			
			$smtpfrom = (empty($history -> from)) ? $this -> get_option('smtpfrom') : $history -> from;
			$smtpfromname = (empty($history -> fromname)) ? $this -> get_option('smtpfromname') : $history -> fromname;
			
			$validationdata = array('subscriber' => $subscriber, 'user' => $user, 'subject' => $subject, 'message' => $message, 'history_id' => $history_id);
			$error = apply_filters($this -> pre . '_sendmail_validation', $error, $validationdata);
			
			if (!empty($attachments) && $attachments != false) {
				$attachments = maybe_unserialize($attachments);
			}
			
			if (empty($error)) {
				$Db -> model = $Email -> model;			
				$subject = $this -> process_set_variables($subscriber, $user, stripslashes($subject), $history_id, $eunique, true);			
				$message = $this -> process_set_variables($subscriber, $user, stripslashes($message), $history_id, $eunique);
				$wpml_textmessage = $this -> process_set_variables($subscriber, $user, stripslashes($wpml_textmessage), $history_id, $eunique);
				
				if (!empty($subscriber -> id)) {
					$Subscriber -> inc_sent($subscriber -> id);
				}
				
				if (!empty($subscriber -> mailinglists)) {
					foreach ($subscriber -> mailinglists as $mailinglist_id) {
						$query = "SELECT `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "' LIMIT 1";
						$paid = $wpdb -> get_var($query);
						
						if (!empty($paid) && $paid == "Y") {
							$query = "UPDATE `" . $wpdb -> prefix . $SubscribersList -> table . "` SET `paid_sent` = (`paid_sent` + 1) WHERE `subscriber_id` = '" . $subscriber -> id . "' AND `list_id` = '" . $mailinglist_id . "' LIMIT 1";
							$wpdb -> query($query);
						}
					}
				}
				
				$mailtype = $this -> get_option('mailtype');
	
				if ($mailtype == "smtp" || $mailtype == "gmail") {
					if (!is_object($phpmailer)) {
						require_once(dirname(__FILE__) . DS . 'vendors' . DS . 'class.phpmailer.php');							
						$phpmailer = new PHPMailer();
					}
					
					//clear all recipients
					$phpmailer -> ClearAddresses();
					$phpmailer -> ClearAllRecipients();
					$phpmailer -> ClearCCs();
					$phpmailer -> ClearBCCs();
					$phpmailer -> ClearAttachments();
					$phpmailer -> ClearReplyTos();
					$phpmailer -> ClearCustomHeaders();
					
					//set the language
					$phpmailer_language = $this -> plugin_base() . DS . 'vendors' . DS . 'phpmailer-language';
					$phpmailer -> SetLanguage('en', $phpmailer_language);
				
					$phpmailer -> IsSMTP();
					$phpmailer -> Host = $this -> get_option('smtphost');
					$phpmailer -> Port = $this -> get_option('smtpport');
					$phpmailer -> SMTPKeepAlive = true;
					
					$smtpsecure = $this -> get_option('smtpsecure');
					if (!empty($smtpsecure) && $smtpsecure != "N") {
						$phpmailer -> SMTPSecure = $smtpsecure;
					}
					
					if ($this -> get_option('smtpauth') == "Y") {
						$phpmailer -> SMTPAuth = true;
						$phpmailer -> Username = $this -> get_option('smtpuser');
						$phpmailer -> Password = $this -> get_option('smtppass');
					}
					
					//DKIM-Signature (DomainKeys Identified Mail)
					if ($this -> get_option('dkim') == "Y") {
						$phpmailer -> DKIM_identity = $smtpfrom;
						$phpmailer -> DKIM_private = $this -> get_option('dkim_private');
						$phpmailer -> DKIM_domain = $this -> get_option('dkim_domain');
						$phpmailer -> DKIM_selector = $this -> get_option('dkim_selector');
					}
					
					if (!empty($attachments) && $attachments != false) {						
						if (is_array($attachments)) {							
							foreach ($attachments as $attachment) {								
								$phpmailer -> AddAttachment($attachment['filename'], $attachment['title']);	
							}
						} else {
							$phpmailer -> AddAttachment($attachments['filename'], $attachments['title']);
						}
					}
					
					//set the Charset to that of Wordpress
					$phpmailer -> CharSet = get_option('blog_charset');
					$phpmailer -> SetFrom($smtpfrom, $smtpfromname);
					$phpmailer -> Sender = $this -> get_option('bounceemail');
					
					if (!empty($subscriber)) {
						$to = $subscriber -> email;
					} elseif (!empty($user)) {
						$to = $user -> user_email;
					}
					
					$phpmailer -> AddAddress($to);
					$phpmailer -> AddReplyTo($smtpfrom, $smtpfromname);
	
					$htmlformat = (empty($subscriber -> format) || $subscriber -> format == "html") ? true : false;
					$phpmailer -> IsHTML($htmlformat);
					
					if (empty($subscriber -> format) || $subscriber -> format == "html") {
						$phpmailer -> IsHTML(true);
					}
					
					$phpmailer -> Subject = stripslashes($subject);
					$phpmailer -> Body = apply_filters($this -> pre . '_send_body', stripslashes($message), $phpmailer, $history_id);
					
					if ($this -> get_option('multimime') == "Y") {
						if (!empty($history -> text)) {
							$altbody = $history -> text;	
						} else {
							require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
							$htmlToText = new Html2Text($wpml_textmessage, 255);
							$altbody = $htmlToText -> convert();
						}
							
						$phpmailer -> AltBody = $altbody;	
					}
					
					$phpmailer -> Priority = $this -> get_option('mailpriority'); //set the email priority					
					$phpmailer -> WordWrap = 0;
					$phpmailer -> Encoding = $this -> get_option('emailencoding');
					$phpmailer -> MessageID = $this -> phpmailer_messageid();
					
					global $newsletters_presend, $newsletters_emailraw;
					if (!empty($newsletters_presend) && $newsletters_presend == true) {
						$phpmailer -> PreSend();
						//$header = $phpmailer -> CreateHeader();
						//$body = $phpmailer -> CreateBody();
						$header = $phpmailer -> MIMEHeader;
						$body = $phpmailer -> MIMEBody;
						$emailraw = $header . $body;
						$newsletters_emailraw = $emailraw;
						return $emailraw;
					}
					
					if ($phpmailer -> Send()) {
						$sent = true;
					} else {					
						global $mailerrors;
						$mailerrors = $phpmailer -> ErrorInfo;
						return false;	
					}
				} else {
					if (!empty($subscriber)) {
						$to = $subscriber -> email;					
					} elseif (!empty($user)) {
						$to = $user -> user_email;	
					}
					
					$subject = stripslashes($subject);
					$message = stripslashes($message);					
					$headers = '';
					$headers .= 'Content-Type: text/html; charset="UTF-8"' . "\r\n";
					$headers .= 'From: ' . $smtpfromname . ' <' . $smtpfrom . '>' . "\r\n";	
					
					$atts = false;
					if (!empty($attachments) && is_array($attachments)) {						
						foreach ($attachments as $attachment) {
							$atts[] = $attachment['filename'];
						}
					}
					
					global $wpml_message, $wpmlhistory_id;
					$wpml_message = $message;
					$wpmlhistory_id = $history_id;
					
					if ($result = wp_mail($to, $subject, $message, $headers, $atts)) {
						$sent = true;
					} else {
						global $mailerrors, $phpmailer;
						$mailerrors = $phpmailer -> ErrorInfo;
						return false;
					}
				}
				
				if (!empty($sent) && $sent == true) {	
					global $phpmailer;
						
					$e_data = array(
						'eunique'				=>	$eunique,
						'subscriber_id'			=>	(!empty($subscriber) ? $subscriber -> id : 0),
						'user_id'				=>	(!empty($user) ? $user -> ID : 0),
						'mailinglist_id'		=>	(!empty($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : ''),
						'mailinglists'			=>	(!empty($subscriber) ? maybe_serialize($Subscriber -> mailinglists($subscriber -> id, $subscriber -> mailinglists, false, "Y")) : ''),
						'history_id'			=>	$history_id,
						'read'					=>	"N",
						'status'				=>	"sent",
						'messageid'				=>	$phpmailer -> MessageID,
					);
					
					$Db -> model = $Email -> model;
					$Db -> save($e_data, true);	
				}
			}
			
			return $sent;
		}
		
		/**
		 * Prints a variable or an array encapsulated in PRE tags
		 * Creates an easy to read hierarchy/structure
		 *
		 * @param ARRAY/STRING
		 * @return BOOLEAN
		 */
		function debug($var = array(), $output = true) {
			if ($output == false) { ob_start(); }
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;
	
			if ($this -> debugging == true) {
				echo '<pre>' . print_r($var, true) . '</pre>';
			}
			
			if ($output == false) {
				$debug = ob_get_clean();
				ob_end_clean();
				return $debug;
			}
			
			return true;
		}
		
		function add_option($name = null, $value = null) {
			global $wpml_add_option_count;
		
			if (add_option($this -> pre . $name, $value)) {
				$wpml_add_option_count++;
				return true;
			}
			
			return false;
		}
		
		function update_option($name = null, $value = null) {
			if (update_option($this -> pre . $name, $value)) {
				return true;
			}
			
			return false;
		}
		
		function get_managementpost($permalink = false, $autocreate = false) {		
			global $wpdb, $user_ID, $wp_rewrite, $newsletters_managementpost_error;
			require_once(ABSPATH . WPINC . DS . 'rewrite.php');
			if (!is_object($wp_rewrite)) { $wp_rewrite = new WP_Rewrite(); }
			
			$managementpost = get_option($this -> pre . 'managementpost');
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . $managementpost . "' AND `post_status` = 'publish'";
			
			if (empty($managementpost) || !$post = $wpdb -> get_row($query)) {
				if ($autocreate == true) {
					$postdata = array(
						'post_title'			=>	__('Manage Subscriptions', $this -> plugin_name),
						'post_content'			=>	__('[wpmlmanagement]', $this -> plugin_name),
						'post_type'				=>	"page",
						'post_status'			=>	"publish",
						'post_author'			=>	$user_ID,
					);
					
					$post_id = wp_insert_post($postdata);
					update_option($this -> pre . 'managementpost', $post_id);
					
					if ($permalink == true) {
						return get_permalink($post_id);	
					} else {
						return $post_id;
					}
				} else {
					if (is_admin()) {
						if (!$newsletters_managementpost_error) {
							$error = sprintf(__('Newsletter plugin subscriber management post/page does not exist %s', $this -> plugin_name), '<a href="?page=' . $this -> sections -> settings . '&method=managementpost" class="button button-secondary">' . __('Create it Now', $this -> plugin_name) . '</a>');
							$this -> render_error($error);
							$newsletters_managementpost_error = true;
						}
					}	
				}
			} else {
				if ($permalink == true) {
					return get_permalink($post -> ID);	
				} else {
					return $post -> ID;	
				}
			}
			
			return false;
		}
		
		function get_imagespost() {
			global $wpdb, $user_ID;
			
			$imagespost = get_option($this -> pre . 'imagespost');
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . $imagespost . "'";
			
			if (empty($imagespost) || !$post = $wpdb -> get_row($query)) {
				$postdata = array(
					'post_title'			=>	__('Newsletter Images (do not remove)', $this -> plugin_name),
					'post_content'			=>	__('This is a placeholder for the Newsletter plugin images. You may edit and reuse this post but do not remove it.', $this -> plugin_name),
					'post_type'				=>	"post",
					'post_status'			=>	"draft",
					'post_author'			=>	$user_ID,
				);
				
				$post_id = wp_insert_post($postdata);
				update_option($this -> pre . 'imagespost', $post_id);
				return $post_id;
			} else {
				return $post -> ID;	
			}
			
			return false;
		}
		
		function get_option($name = null, $stripslashes = true) {
			switch ($name) {
				case 'imagespost'			:
					if ($imagespost = $this -> get_imagespost()) {
						$this -> update_option('imagespost', $imagespost);
					}
					break;	
			}
			
			if ($option = get_option($this -> pre . $name)) {
				if (!is_array($option)) {
					$data = @unserialize($option);
				
					if ($data !== false) {
						return unserialize($option);
					}
				}
				
				if ($stripslashes == true) {
					$option = stripslashes_deep($option);
				}
				
				return $option;
			}
			
			return false;
		}
		
		function delete_option($name = null) {
			if (!empty($name)) {
				if (delete_option($this -> pre . $name)) {
					return true;
				}
			}
			
			return false;
		}
		
		function ajax_latestposts_preview() {	
			define('DOING_AJAX', true);
	    	define('SHORTINIT', true);
			
			if ($this -> get_option('latestposts') == "Y") {
				if ($content = $this -> latestposts_hook(true)) {
					echo $content;
				}
			}
			
			exit(); die();
		}
		
		function ajax_lps_posts() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
		
			global $Db, $Latestpost;
			
			$Db -> model = $Latestpost -> model;
			$posts = $Db -> find_all(null, null, array('created', "DESC"));
			
			$this -> render('posts', array('posts' => $posts), true, 'admin');
			
			exit(); die();
		}
		
		function ajax_delete_lps_post() {
			define('DOING_AJAX', true);
			define('SHORTINIT', true);
			
			global $Db, $Latestpost;
		
			if (!empty($_POST['id'])) {
				$Db -> model = $Latestpost -> model;
				$Db -> delete($_POST['id']);
			}
			
			echo 1;
			
			exit(); die();
		}
		
		function get_latestposts() {
			global $wpdb, $post, $user_ID, $Db, $Latestpost, $Template, $Html, $History, $Mailinglist, $Queue, $Subscriber, $SubscribersList;
			$post_criteria = false;
		
			if ($this -> get_option('latestposts') == "Y") {
				$exclude = array();
				if ($latestposts_exclude = $this -> get_option('latestposts_exclude')) {
					if (($exclude = @explode(",", $latestposts_exclude)) !== false) {
						//exclude array exists…
						foreach ($exclude as $exkey => $exval) {
							$exclude[$exkey] = trim($exval);
						}
					}
				}
						
				$post_criteria = array(
					'numberposts'			=>	$this -> get_option('latestposts_number'),
					'category'				=>	implode(",", $this -> get_option('latestposts_categories')),
					'orderby'				=>	"post_date",
					'order'					=>	"ASC",
					'exclude'				=>	$exclude,
					'post_type'				=>	"post",
					'post_status'			=>	"publish",
				);
				
				$latestposts_takefrom = $this -> get_option('latestposts_takefrom');
				if (!empty($latestposts_takefrom) && $latestposts_takefrom == "posttypes") {
					$post_criteria['category'] = 0;
					$post_criteria['post_type'] = $this -> get_option('latestposts_posttypes');
				}
				
				$latestpostsquery = "SELECT id, post_id FROM " . $wpdb -> prefix . $Latestpost -> table . "";
				if ($latestposts = $wpdb -> get_results($latestpostsquery)) {
					foreach ($latestposts as $latestpost) {
						if (!empty($post_criteria['exclude'])) {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						} else {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						}
					}
				}
				
				$olderthanquery = "SELECT ID FROM " . $wpdb -> posts . " WHERE post_date < '" . date_i18n("Y-m-d H:i:s", strtotime($this -> get_option('latestposts_olderthan'))) . "'";
				if ($olderthan = $wpdb -> get_results($olderthanquery)) {
					foreach ($olderthan as $olderthanpost) {
						$post_criteria['exclude'][] = $olderthanpost -> ID;
					}
				}
				
				$Db -> model = $Latestpost -> model;
				$verylatestpost = $Db -> find(false, false, array('created', "DESC"));
				$vl_post = get_post($verylatestpost -> post_id);
			}
				
			return $post_criteria;
		}
		
		function updating_plugin() {
			if (!is_admin()) return;
		
			if (!$this -> get_option('version')) {
				$this -> add_option('version', $this -> version);
				$this -> update_options();
				return;
			}
	
			$cur_version = $this -> get_option('version');
			$version = $this -> version;
	
			if (version_compare($this -> version, $cur_version) === 1) {				
				if (version_compare("3.8.4", $cur_version) === 1) {											
					if (!empty($this -> classes)) {
						global $wpdb;
						$this -> update_options();
						$this -> initialize_classes();
						
						foreach ($this -> classes as $class_name) {
							global ${$class_name};
							
							$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";
							$wpdb -> query($query);
							
							if (!empty(${$class_name} -> tv_fields)) {									
								foreach (${$class_name} -> tv_fields as $table_field_name => $table_field_attributes) {
									if (!empty($table_field_name) && $table_field_name != "key") {
										if (!preg_match("/(INT|DATETIME)/si", $table_field_attributes[0])) {
											$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` CHANGE `" . $table_field_name . "` `" . $table_field_name . "` " . $table_field_attributes[0] . " CHARACTER SET utf8 COLLATE utf8_general_ci " . $table_field_attributes[1] . ";";		
											$wpdb -> query($query);
										}
									}
								}
							}
						}
						
						global $wpdb, $History, $Template, $Theme;
						$wpdb -> flush();
						
						$db_queries = array(
							"ALTER TABLE `" . $wpdb -> prefix . "" . $History -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $Template -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						);
						
						foreach ($db_queries as $db_query) {
							$wpdb -> query($db_query);
						}
					}
					
					$version = "3.8.4";
				} elseif (version_compare("3.8.4.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.4.1";	
				} elseif (version_compare("3.8.5.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.5.1";	
				} elseif (version_compare("3.8.6", $cur_version) === 1) {										
					$this -> update_options();				
					$this -> update_option('scheduling', 'Y');
					//$this -> add_option('schedulecrontype', "wp");
					$this -> update_option('scheduleinterval', "2minutes");
					$this -> update_option('cronrunning', "N");
					$this -> update_option('schedulenotify', "N");
					$this -> update_option('emailsperinterval', 20);
					$this -> scheduling();
					
					/* Currencies */
					global $currencies;
					require_once $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
					$this -> update_option('currencies', $currencies);
					
					/* Permissions */
					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['autoresponders'] = 10;
					$this -> update_option('permissions', $permissions);
					
					$this -> update_option('sendnewsletteronsubscribe', "N");
					$version = "3.8.6";	
				} elseif (version_compare("3.8.7", $cur_version) === 1) {	
					$this -> update_options();
							
					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['groups'] = "10";
					$this -> update_option('permissions', $permissions);
					$this -> get_managementpost();	
					
					global $wpdb, $History, $Template, $Theme, $Queue, $Autoresponderemail;
					$wpdb -> flush();
					
					$db_queries = array(
						"ALTER TABLE `" . $wpdb -> prefix . "" . $History -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Queue -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Template -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . $Autoresponderemail -> table . "` ADD UNIQUE INDEX (subscriber_id, autoresponder_id)",
					);
					
					foreach ($db_queries as $db_query) {
						$wpdb -> query($db_query);
					}
								
					$version = '3.8.7';	
				} elseif (version_compare("3.8.7.2", $cur_version) === 1) {
					$this -> update_options();
					$version = '3.8.7.2';
				}
				
				if (version_compare($cur_version, "3.8.9.2") < 0) {				
					$this -> update_options();
					$this -> initialize_default_themes();
					
					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							echo do_shortcode(stripslashes($theme -> content));
							$newcontent = ob_get_clean();
							
							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";
							$wpdb -> query($themequery);
						}
					}
					
					$version = "3.8.9.2";
				}
				
				if (version_compare($cur_version, "3.8.9.4") < 0) {
					global $wpdb, $Mailinglist;			
					$this -> update_options();
					
					$intervals = array(
						'daily'			=>	__('Daily', $this -> plugin_name),
						'weekly'		=>	__('Weekly', $this -> plugin_name),
						'monthly'		=>	__('Monthly', $this -> plugin_name),
						'2months'		=>	__('Every Two Months', $this -> plugin_name),
						'3months'		=>	__('Every Three Months', $this -> plugin_name),
						'biannually'	=>	__('Twice Yearly (Six Months)', $this -> plugin_name),
						'9months'		=>	__('Every Nine Months', $this -> plugin_name),
						'yearly'		=>	__('Yearly', $this -> plugin_name),
						'once'			=>	__('Once Off', $this -> plugin_name),
					);
					
					$this -> update_option('intervals', $intervals);
					
					$query = "ALTER TABLE `" . $wpdb -> prefix . $Mailinglist -> table . "` CHANGE `interval` `interval` ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once') NOT NULL DEFAULT 'monthly';";
					$wpdb -> query($query);
					
					$version = "3.8.9.4";
				}
				
				if (version_compare($cur_version, "3.9") < 0) {						
					$this -> update_options();				
					$this -> initialize_default_themes();
					
					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							echo do_shortcode(stripslashes(eval(' ?>' . $theme -> content . '<? ')));
							$newcontent = ob_get_clean();
							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";						
							$wpdb -> query($themequery);
						}
					}
					
					$version = "3.9";
				}
				
				if (version_compare($cur_version, "3.9.4") < 0) {
					global $wpdb;
					$this -> update_options();
					
					/* convert database tables to MyISAM */
					if (!empty($this -> tablenames)) {
						foreach ($this -> tablenames as $tablename) {					
							$query = "ALTER TABLE `" . $tablename . "` ENGINE=MyISAM;";
							$wpdb -> query($query);
						}
					}
					
					/* Auto import WordPress users */
					if ($importuserslist = $this -> get_option('importuserslist')) {
						$this -> update_option('importuserslists', array($importuserslist));
					}
					
					$version = "3.9.4";
				}
				
				if (version_compare($cur_version, "3.9.9") < 0) {				
					global $wpdb, $Queue, $Field;
					$this -> update_options();
					$wpdb -> query("ALTER TABLE `" . $wpdb -> prefix . $Queue -> table . "` ADD INDEX (`id`)");
					$wpdb -> query("ALTER TABLE `" . $wpdb -> prefix . $Field -> table . "` CHANGE `type` `type` VARCHAR(255) NOT NULL DEFAULT 'text'");
					$version = "3.9.9";
				}
				
				if (version_compare($cur_version, "4.1.2") < 0) {
					$this -> update_options();
					$version = "4.1.2";
				}
			
				//the current version is older.
				//lets update the database
				$this -> update_option('version', $version);
			}	
		}
		
		function update_options() {
			if (!is_admin()) return;
			$this -> check_tables();
			$this -> get_managementpost(false, true);
			
			global $wpml_add_option_count, $wpdb, $Theme;
			$wpml_add_option_count = 0;
					
			$options = array();	
			$options['screenoptions_subscribers_custom'] = array('gravatars');		
			$options['managementloginsubject'] = __('Authenticate Subscriber Account', $this -> plugin_name);
			$options['managementallowemailchange'] = "Y";
			$options['managementformatchange'] = "Y";
			$options['managementallownewsubscribes'] = "Y";
			$options['managementcustomfields'] = "Y"; 
			$options['cookieformat'] = "D, j M Y H:i:s";
			$options['defaultlistcreated'] = "N";
			$options['subscriptionmessage'] = __('Subscription Successful', $this -> plugin_name);
			$options['sendingprogress'] = "N";
			$options['createpreview'] = "Y";
			$options['emailencoding'] = "8bit";
			$options['clicktrack'] = "Y";
			$options['shortlinks'] = "N";
			$options['theme_folder'] = "default";
			$options['theme_usestyle'] = "Y";
			$options['customcss'] = "N";
			$options['loadscript_jqueryuitabs'] = "Y";
			$options['loadscript_jqueryuitabs_handle'] = "jquery-ui-tabs";
			$options['loadscript_jqueryuibutton'] = "Y";
			$options['loadscript_jqueryuibutton_handle'] = "jquery-ui-button";
			$options['loadscript_jqueryuiwatermark'] = "Y";
			$options['loadscript_jqueryuiwatermark_handle'] = "jquery-ui-watermark";
			$options['loadscript_jqueryuploadify'] = "Y";
			$options['loadscript_jqueryuploadify_handle'] = "jquery-uploadify";	
			$options['multimime'] = "N"; //should multi mime (text/html) emails be sent?
			$options['gzip'] = "N";
			$options['mailtype'] = 'mail';
			$options['smtphost'] = 'mail.domain.com';
			$options['smtpport'] = 25;
			$options['smtpsecure'] = "N";
			$options['smtpauth'] = 'N';
			$options['smtpuser'] = __('username', $this -> plugin_name);
			$options['smtppass'] = __('password', $this -> plugin_name);
			$options['adminemail'] = get_option('admin_email');
			$options['smtpfrom'] = get_option('admin_email');
			$options['smtpfromname'] = get_option('blogname');
			$options['dkim'] = "N";
			$options['dkim_domain'] = "domain.com";
			$options['dkim_selector'] = "newsletters";
			$options['tracking'] = "Y";
			$options['servertype'] = 'cpanel';
	        $options['mailpriority'] = 3; //set the mail priority to "Normal"
			$options['unsubscribeondelete'] = 'N';
			$options['unsubscribeemails'] = "single";
			$options['unsubscribeconfirmation'] = "Y";
			$options['unsubscribecomments'] = "Y";
			$options['registercheckbox'] = 'Y';
			$options['registerformlabel'] = __('Receive news updates via email from this site', $this -> plugin_name);		
			$options['checkboxon'] = 'N';
			$options['autosubscribelist'] = array(1);
			$options['sendonpublish'] = 'Y';
			$options['sendonpublishef'] = 'ep';
			$options['sendonpublishexcerptlength'] = 250;
			$options['sendonpublishunsubscribe'] = 'Y';
			$options['unsubscribetext'] = __('Unsubscribe from this newsletter', $this -> plugin_name);
			$options['unsubscribealltext'] = __('Unsubscribe from all emails', $this -> plugin_name);
			$options['unsubscribedelete'] = "N";
			$options['adminemailonsubscription'] = 'Y';
			$options['adminemailonunsubscription'] = 'Y';
			$options['activationlinktext'] = __('Confirm Subscription', $this -> plugin_name);
			$options['customactivateredirect'] = "N";
			$options['activateredirecturl'] = home_url();
			$options['managelinktext'] = __('Manage Subscriptions', $this -> plugin_name);
			$options['onlinelinktext'] = __('View in your browser', $this -> plugin_name);
			$options['scheduling'] = 'Y';
			$options['schedulecrontype'] = "wp";
			$options['scheduleinterval'] = "2minutes";
			$options['cronrunning'] = "N";
			$options['schedulenotify'] = "N";
			$options['emailsperinterval'] = 20;
			$options['autoresponderscheduling'] = "hourly";
			$options['tinymcebtn'] = "Y";
			$options['sendasnewsletterbox'] = "Y";
			$options['subscriberegister'] = "N";
			$options['importusers'] = "N";
			$options['importusersscheduling'] = "hourly";
			$options['importuserslists'] = array(1);
			$options['importusersrequireactivate'] = "N";
			$options['subscriptions'] = "Y";
			$options['paidsubscriptionredirect'] = "Y";
			$options['rssfeed'] = "N";
			$options['deleteonbounce'] = 'Y';
	        $options['bouncecount'] = 3;
			$options['adminemailonbounce'] = 'Y';
			$options['bounceemail'] = get_option('admin_email');
	        $options['bouncemethod'] = "cgi";
	        $options['bouncepop_interval'] = "3600";
	        $options['bouncepop_host'] = "localhost";
	        $options['bouncepop_user'] = "bounce@domain.com";
	        $options['bouncepop_pass'] = "mailboxpassword";
	        $options['bouncepop_port'] = "110";
			$options['subscriberexistsredirect'] = "management";
			$options['subscriberexistsmessage'] = __('You are already subscribed, redirecting to the management page...', $this -> plugin_name);
			$options['subscriberexistsredirecturl'] = get_permalink($this -> get_managementpost());
			$options['requireactivate'] = 'Y';	
			$options['activateaction'] = "none";
			$options['activatereminder'] = 3;
			$options['activatedelete'] = 7;
			$options['activationemails'] = "single";
			$options['tcodemo'] = 'N';
			$options['tcovendorid'] = '123456';
			$options['tcosecret'] = __('secretstring', $this -> plugin_name);
			$options['adminordernotify'] = 'Y';
			$options['subscriberedirect'] = "N";
			$options['subscriberedirecturl'] = $this -> get_managementpost(true);
			$options['paymentmethod'] = 'paypal';
			$options['captcha_type'] = ($this -> is_plugin_active('captcha')) ? 'rsc' : 'none';
			$options['recaptcha_theme'] = "custom";
			$options['recaptcha_customcss'] = '.recaptcha_widget { margin: 10px 0 15px 0; }
			.recaptcha_widget .recaptcha_image { margin: 10px 0 5px 0; }
			.recaptcha_widget .recaptcha_image img { width: 250px; box-shadow: none; }
			.recaptcha_widget .recaptcha_links { font-size: 85%; }
			.recaptcha_widget .recaptcha_response { }';
			$options['captcha_rgb'] = array(255, 255, 255);
			$options['captcha_bg'] = "#FFFFFF";
			$options['captcha_fg'] = array(0, 0, 0);
			$options['farbtastic_fg'] = "#000000";
			$options['captcha_size'] = array('w' => 72, 'h' => 24);
			$options['captcha_chars'] = "4";
			$options['captcha_font'] = "14";
			$options['captchainterval'] = "hourly";
			$this -> captchacleanup_scheduling();
			$options['commentformcheckbox'] = "Y";
			$options['commentformlabel'] = __('Receive news updates via email from this site', $this -> plugin_name);
			$options['commentformautocheck'] = "N";
			$options['commentformlist'] = "1";
			$options['excerpt_length'] = 55;
			$options['excerpt_more'] = __('Read more', $this -> plugin_name);
			$options['latestposts'] = "N";
			$options['latestposts_subject'] = __('Latest Posts from', $this -> plugin_name) . ' ' . get_bloginfo('name');
			$options['latestposts_number'] = 10;
			$options['latestposts_language'] = "en";
			$options['latestposts_categories'] = false;
			$options['latestposts_exclude'] = "";
			global $wpdb;
			$olderthanquery = "SELECT post_date FROM " . $wpdb -> posts . " ORDER BY post_date ASC LIMIT 1";
			$olderthan = $wpdb -> get_var($olderthanquery);
			$options['latestposts_olderthan'] = date_i18n("Y-m-d H:i:s", strtotime($olderthan));
			$options['latestposts_startdate'] = date_i18n("Y-m-d H:i:s", time());
			$options['latestposts_lists'] = false;
			$options['latestposts_interval'] = "weekly";
			$options['latestposts_time'] = "00:00:00";
			$options['latestposts_theme'] = false;
			$options['paypalemail'] = get_option('admin_email');
			$options['paypalsubscriptions'] = "N";
			$options['paypalsandbox'] = "N";
			$options['paypalliveurl'] = "https://www.paypal.com/cgi-bin/webscr";
			$options['paypalsandurl'] = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$options['countriesinserted'] = "N";
			$options['generalredirect'] = $this -> get_managementpost(true);
			$options['offsitetitle'] = get_bloginfo('name');
			$options['offsitelist'] = 'checkboxes';
			$options['offsitewidth'] = 400;
			$options['offsiteheight'] = 300;
			$options['offsitebutton'] = __('Subscribe Now', $this -> plugin_name);
			$options['currency'] = 'USD';
			include $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
			$options['currencies'] = $currencies;
			
			$intervals = array(
				'daily'			=>	__('Daily', $this -> plugin_name),
				'weekly'		=>	__('Weekly', $this -> plugin_name),
				'monthly'		=>	__('Monthly', $this -> plugin_name),
				'2months'		=>	__('Every Two Months', $this -> plugin_name),
				'3months'		=>	__('Every Three Months', $this -> plugin_name),
				'biannually'	=>	__('Twice Yearly (Six Months)', $this -> plugin_name),
				'9months'		=>	__('Every Nine Months', $this -> plugin_name),
				'yearly'		=>	__('Yearly', $this -> plugin_name),
				'once'			=>	__('Once Off', $this -> plugin_name),
			);
			
			$options['intervals'] = serialize($intervals);
			
			$embed = array(
				'acknowledgement'		=>	__('Thank you for subscribing.', $this -> plugin_name),			//default acknowledgement message
				'subtitle'				=>	__('Subscribe to our newsletter.', $this -> plugin_name),		//subtitle of the subscription form
				'subscribeagain'		=>	"N",															//show a "Subscribe again" link?
				'ajax'					=>	"N",															//turn on Ajax features?
				'button'				=>	__('Subscribe Now', $this -> plugin_name),						//button text
				'scroll'				=>	"Y",															//scroll to the subscription form?
				'captcha'				=>	"N",															//security captcha image?
			);
			
			if ($this -> is_plugin_active('qtranslate')) {
				global $q_config;
				
				foreach ($embed as $ekey => $eval) {
					$embed[$ekey][$q_config['default_language']] = $eval;	
				}
			}
			
			$options['embed'] = $embed;
			
			$poststatuses = array(
				'publish'			=>	__('Published', $this -> plugin_name),
				'pending'			=>	__('Pending', $this -> plugin_name),
				'draft'				=>	__('Draft', $this -> plugin_name),
				'private'			=>	__('Private', $this -> plugin_name)
			);
			
			$options['poststatuses'] = $poststatuses;
			
			foreach ($options as $okey => $oval) {
				$this -> add_option($okey, $oval);
			}
			
			$this -> get_imagespost();		
			$this -> get_managementpost();
			
			$themesquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` LIMIT 1";
			$themes = $wpdb -> get_results($themesquery);
			if (empty($themes)) { $this -> initialize_default_themes(); }
			
			$permissions = $this -> get_option('permissions');
			if (empty($permissions)) { $this -> init_roles(); }
			
			$this -> scheduling();
			$this -> autoresponder_scheduling();
			$this -> init_fieldtypes();
			$this -> predefined_templates();
			
			return $wpml_add_option_count;
		}
		
		function predefined_templates() {
			require_once $this -> plugin_base() . DS . 'includes' . DS . 'email-templates.php';
			
			if (!empty($email_templates)) {
				foreach ($email_templates as $etk => $email_template) {			
					$this -> add_option('etsubject_' . $etk, $email_template['subject']);
					$this -> add_option('etmessage_' . $etk, $email_template['message']);
				}
			}
		}
		
		function init_fieldtypes() {
			global $wpdb;
		
			$fieldtypes = array(
				'hidden'		=> 	__('Hidden', $this -> plugin_name),
				'special'		=>	__('Special', $this -> plugin_name),
				'text'			=>	__('Text Field', $this -> plugin_name),
				'textarea'		=>	__('Text Area', $this -> plugin_name),
				'select'		=>	__('Select Drop Down', $this -> plugin_name),
				'radio'			=>	__('Radio Buttons', $this -> plugin_name),
				'checkbox'		=>	__('Checkboxes', $this -> plugin_name),
				'file'			=>	__('File Upload', $this -> plugin_name),
				'pre_country'	=>	__('Predefined : Country Select', $this -> plugin_name),
				'pre_date'		=>	__('Predefined : Date Picker (YYYY-MM-DD)', $this -> plugin_name),
				'pre_gender'	=>	__('Predefined : Gender', $this -> plugin_name),
			);
			
			$this -> update_option('fieldtypes', $fieldtypes);
			
			$fieldtypesquery = "ALTER TABLE `" . $wpdb -> prefix . "wpmlfields` CHANGE `type` `type` ENUM('text','checkbox','radio','select','textarea','file','pre_country','pre_date','pre_gender','special') NOT NULL DEFAULT 'text';";
			$wpdb -> query($fieldtypesquery);
			return true;
		}
		
		function check_roles() {
			global $wp_roles;
			$permissions = $this -> get_option('permissions');
			
			if ($role = get_role('administrator')) {		
				if (!empty($this -> sections)) {			
					foreach ($this -> sections as $section_key => $section_menu) {								
						if (empty($role -> capabilities['newsletters_' . $section_key])) {
							$role -> add_cap('newsletters_' . $section_key);
							$permissions[$section_key][] = 'administrator';
						}
					}
					
					$this -> update_option('permissions', $permissions);
				}
			}
			
			return false;		
		}
		
		function init_roles($sections = null) {
			global $wp_roles;
			$sections = $this -> sections;
		
			/* Get the administrator role. */
			$role = get_role('administrator');
	
			/* If the administrator role exists, add required capabilities for the plugin. */
			if (!empty($role)) {
				if (!empty($sections)) {			
					foreach ($sections as $section_key => $section_menu) {
						$role -> add_cap('newsletters_' . $section_key);
					}
				}
			}
	
			/**
			 * If the administrator role does not exist for some reason, we have a bit of a problem 
			 * because this is a role management plugin and requires that someone actually be able to 
			 * manage roles.  So, we're going to create a custom role here.  The site administrator can 
			 * assign this custom role to any user they wish to work around this problem.  We're only 
			 * doing this for single-site installs of WordPress.  The 'super admin' has permission to do
			 * pretty much anything on a multisite install.
			 */
			elseif (empty($role) && !is_multisite()) {
				$newrolecapabilities = array();
				$newrolecapabilities[] = 'read';
			
				if (!empty($sections)) {
					foreach ($sections as $section_key => $section_menu) {
						$newrolecapabilities[] = 'newsletters_' . $section_key;
					}
				}
	
				add_role(
					'newsletters',
					_e('Newsletters Manager', $this -> plugin_name),
					$newrolecapabilities
				);
			}
			
			if (!empty($sections)) {
				$permissions = array();
			
				foreach ($sections as $section_key => $section_menu) {
					$wp_roles -> add_cap('administrator', 'newsletters_' . $section_key);
					$permissions[$section_key][] = 'administrator';
				}
				
				$this -> update_option('permissions', $permissions);
			}
		}
		
		function list_exists($list_id = null) {
			if (!empty($list_id)) {		
				if ($Mailinglist -> list_exists($list_id)) {
					return true;
				}
			}
			
			return false;
		}
		
		function array_to_object($array = array()) {
			if (!empty($array)) {
				return (object) $array;
			}
			
			return false;
		}
		
		function truncatetext($text = null, $start = 0, $end = 0, $append = '...') {
			return substr($text, $start, $end) . $append;
		}
		
		function gen_date($format = "Y-m-d H:i:s", $time = null) {
			$newtime = (empty($time)) ? time() : $time;
			return date_i18n($format, $newtime);
		}
		
		function override_mce_options($initArray = null) {
			if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {			
				$opts = '*[*]';
			    $initArray['valid_elements'] = $opts;
			    $initArray['extended_valid_elements'] = $opts;
			    //$initArray['verify_html'] = 0;
			    //$initArray['cleanup'] = 0;
			    //$initArray['validate_children'] = 0;
			    //$initArray['valid_children'] = $opts;
			}
			    
			return $initArray;
		}
		
		function stripext($filename = null, $return = 'ext') {
			if (!empty($filename)) { 
				//$extArray = split("[/\\.]", $filename); 
				
				if ($return == 'ext') {
					//$p = count($extArray) - 1; 
					//$extension = $extArray[$p]; 
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					return $extension;
				} else {
					///$p = count($extArray) - 2;
					//$filename = $extArray[$p];
					$file = pathinfo($filename, PATHINFO_FILENAME);
					return $file;
				}
			}
			
			return false;
		}
		
		function language_ready() {
			//the language plugin
			$language_plugin = "LangSwitch/langswitch.php";
		
			if ($active_plugins = get_option('active_plugins')) {
				if (!empty($active_plugins) && is_array($active_plugins) && in_array($language_plugin, $active_plugins)) {
					return true;
				}
			}	
		
			return false;
		}
		
		function bounce($email = null, $type = 'cgi') {
			global $wpdb, $Bounce, $Email, $Db, $Subscriber, $SubscribersList;
	
	        $deleted_subscribers = 0;
	        $deleted_emails = 0;
	
	        switch ($type) {
	            case 'cgi'              :
	        		if (!empty($email)) {
	        			$email = urldecode($email);
	        			preg_match_all("/[<](.*)[>]/i", $email, $matches);
	
	        			if ($this -> get_option('servertype') == "plesk") {
	        				$emailaddress = trim($matches[1][0]);
	        			} else {
	        				$emailaddress = trim($matches[1][2]);
	        			}
	
	        			$Db -> model = $Subscriber -> model;
	        			if ($subscriber = $Db -> find(array('email' => $email))) {                                	
	                        $bouncecount = $this -> get_option('bouncecount');
	                        $dodelete = false;
	
	                        if (empty($bouncecount) || $bouncecount < 1 || $bouncecount == 1) {
	                            $dodelete = true;
	                        } else {
	                            if (($subscriber -> bouncecount + 1) >= $bouncecount) {
	                                $dodelete = true;
	                            }
	                        }
	                        
	                        $Db -> model = $Subscriber -> model;
	                        if ($dodelete == true) {
	                        	if ($this -> get_option('deleteonbounce') == "Y") {
	                                $Db -> delete($subscriber -> id);
	                                $deleted_subscribers++;
	                            }
	                        } else {
	                            $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
	                        }
	                        
	                        $bouncedata = array('email' => $subscriber -> email);
	                        $Bounce -> save($bouncedata);
	                        
	                        //send a notification to the administrator
	                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
	                        $this -> admin_bounce_notification($subscriber);
	                        
	                        return true;
	                    }
	        		}
	
	        		return false;
	                break;
	            case 'pop'					:
	            	require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.bounce.php');
	            	$bouncehandler = new BounceHandler();
	            
					require_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.pop3_new.php');
					$pop_host = $this -> get_option('bouncepop_host');
					$pop_port = $this -> get_option('bouncepop_port');
					$pop_user = $this -> get_option('bouncepop_user');
					$pop_pass = $this -> get_option('bouncepop_pass');
					$pop3 = new POP3();
					
					if ($pop3 -> connect($pop_host, $pop_port)) {
						if ($pop3 -> user($pop_user)) {
							$count = $pop3 -> pass($pop_pass);
							
							if (!empty($count) && $count > 0) {
								for ($m = 1; $m <= $count; $m++) {
									$message = $pop3 -> get($m);
									$message_array = $pop3 -> get($m, "array");									
									$the_facts = $bouncehandler -> get_the_facts($message);
									
									foreach ($message_array as $mkey => $mval) {
										if (preg_match("/^Message\-ID\:(.*)/s", $mval, $matches)) {
											$messageid_array = explode(": ", $message_array[$mkey]);
											$messageid = trim($messageid_array[1]);
											
											if (!empty($messageid)) {
												$Db -> model = $Email -> model;
												$bouncedemail = $Db -> find(array('messageid' => $messageid));
												break;
											}
										}	
									}
									
									if (!empty($the_facts[0]['recipient']) && !empty($the_facts[0]['action'])) {
				                        if ($the_facts[0]['action'] == "failed") {
					                        $email = trim($the_facts[0]['recipient']);
					                        
					                 		$Db -> model = $Subscriber -> model;	                                
			                                if ($subscriber = $Db -> find(array('email' => $email))) {	 
			                                	$subscriber_id = $subscriber -> id;                               
			                                    $bouncecount = $this -> get_option('bouncecount');
			                                    $dodelete = false;
			
			                                    if (empty($bouncecount) || $bouncecount < 1 || $bouncecount == 1) {
			                                        $dodelete = true;
			                                    } else {
			                                        if (($subscriber -> bouncecount + 1) >= $bouncecount) {
			                                            $dodelete = true;
			                                        }
			                                    }
				                                
				                                $Db -> model = $Subscriber -> model;
			                                    if ($dodelete == true) {
			                                    	if ($this -> get_option('deleteonbounce') == "Y") {
				                                        $Db -> delete($subscriber -> id);
				                                        $deleted_subscribers++;
				                                    }
			                                    } else {
			                                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
			                                    }
			                                    
			                                    $bouncedata = array('email' => $subscriber -> email, 'history_id' => $bouncedemail -> history_id);
			                                    $Bounce -> save($bouncedata);
			                                    
			                                    $Db -> model = $Email -> model;
			                                    $Db -> save_field('bounced', "Y", array('id' => $bouncedemail -> id));
			                                    
			                                    //send a notification to the administrator
			                                    $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
				                                $this -> admin_bounce_notification($subscriber);
				                                
				                                $pop3 -> delete($m); //remove the email from the mailbox
				                                $deleted_emails++;  
			                                }     
				                        }
			                        }
								}
								
								$pop3 -> quit();
								return array($deleted_subscribers, $deleted_emails);
							} else {
								if (false === $count) {
									wp_die($pop3 -> ERROR);
								} elseif ($count === 0) {
									wp_die(__('There are no emails in the mailbox', $this -> plugin_name));
								}
							}
						} else {
							wp_die($pop3 -> ERROR);
						}
					} else {
						wp_die($pop3 -> ERROR);
					}
	            	break;
	            case 'sns'				:
	            	$Db -> model = $Subscriber -> model;	                                
                    if ($subscriber = $Db -> find(array('email' => $email))) {	 
                    	$subscriber_id = $subscriber -> id;                               
                        $bouncecount = $this -> get_option('bouncecount');
                        $dodelete = false;

                        if (empty($bouncecount) || $bouncecount < 1 || $bouncecount == 1) {
                            $dodelete = true;
                        } else {
                            if (($subscriber -> bouncecount + 1) >= $bouncecount) {
                                $dodelete = true;
                            }
                        }
                        
                        $Db -> model = $Subscriber -> model;
                        if ($dodelete == true) {
                        	if ($this -> get_option('deleteonbounce') == "Y") {
                                $Db -> delete($subscriber -> id);
                                $deleted_subscribers++;
                            }
                        } else {
                            $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
                        }
                        
                        $bouncedata = array('email' => $subscriber -> email);
                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;  
                    }
	            	
	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	        }
		}
		
		function add_action($action = null, $function = null, $priority = 10, $params = 1) {
			add_action($action, array(&$this, $function == '' ? $action : $function), $priority, $params);
		}
		
		function add_filter($filter = null, $function = null, $priority = 10, $params = 1) {
			add_filter($filter, array(&$this, $function == '' ? $filter : $function), $priority, $params);
		}
		
		function plugin_base() {
			return rtrim(dirname(__FILE__), '/');
		}
		
		function url() {
			$url = rtrim(site_url(), '/') . '/' . substr(str_replace("\\", "/", $this -> plugin_base()), strlen(ABSPATH));		
			return $url;
		}
		
		function redirect($location = null, $msgtype = null, $message = null, $jsredirect = false) {
			global $Html;
			$url = $location;
	
			if (!empty($msgtype)) {		
				if (is_admin()) {
					if ($msgtype == "message") {
						$url = $Html -> retainquery($this -> pre . 'updated=true', $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery($this -> pre . 'error=true', $url);
					}
				} else {
					if ($msgtype == "success") {
						$url = $Html -> retainquery('updated=1&success=' . $message, $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery('updated=1&error=' . $message, $url);
					}
				}
			}
			
			if (!empty($message) && is_admin()) {
				$url = $Html -> retainquery($this -> pre . 'message=' . urlencode($message), $url);
			}
			
			if (headers_sent() || $jsredirect == true) {
				?>
				
				<script type="text/javascript">
				window.location.href = '<?php echo $url; ?>';
				</script>
				
				<?php
				
				flush();
			} else {
				header("Location: " . $url . "");
				exit();	
			}
		}
		
		function render_error($message) {
			$this -> render_admin('error-top', array('message' => $message));
			flush();
		}
		
		function render_message($message) {
			$this -> render_admin('message', array('message' => $message));
			flush();
		}
		
		function et_subject($type = null, $subscriber = null, $language = null) {
			$subject = false;
		
			if (!empty($type)) {
				$subject = __($this -> get_option('etsubject_' . $type));
			}
		
			$subject = $this -> process_set_variables($subscriber, $user, $subject, false, false, true);
			return $subject;
		}
		
		function et_message($type = null, $subscriber = null, $language = null) {
			$message = false;
			
			if (!empty($type)) {
				$template = $this -> get_option('etmessage_' . $type);			
				
				switch ($type) {
					case 'posts'				:
						if (!empty($language) && $this -> is_plugin_active('qtranslate')) {
							$message = qtrans_use($language, $template, false);
						} else {
							$message = __($template);	
						}
						break;
					default 					:
						if (!empty($language) && $this -> is_plugin_active('qtranslate')) {
							$message = wpautop(qtrans_use($language, $template, false));	
						} else {
							$message = wpautop(__($template));
						}
					break;
				}
				
				$message = $this -> process_set_variables($subscriber, $user, $message, false, false, true);
			}
		
			return $message;
		}
		
		function get_themefolders() {
			$dir = $this -> plugin_base() . DS . 'views' . DS;
			$themefolders = array();
			
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						$filetype = filetype($dir . $file);
						if (!empty($filetype) && $filetype == "dir") {
							if ($file != "admin" && $file != "email" && $file != "." && $file != "..") {
								$themefolders[] = $file;
							}
						}
					}
					
					closedir($dh);	
				}
			}
			
			return $themefolders;
		}
		
		function render($file = null, $params = array(), $output = true, $folder = 'default', $extension = null) {	
			$this -> sections = (object) $this -> sections;
			//$this -> plugin_name = 'wp-mailinglist';
		
			if (!empty($file)) {				
				$filename = $file . '.php';
				
				if (!empty($folder) && $folder != "admin") {
					$theme_folder = $this -> get_option('theme_folder');
					$folder = (!empty($theme_folder)) ? $theme_folder : $folder;
					
					$template_url = get_stylesheet_directory_uri();
					$theme_path = get_stylesheet_directory();
					$full_path = $theme_path . DS . 'newsletters' . DS . $filename;
					
					if (!empty($theme_path) && file_exists($full_path)) {
						$folder = $theme_path . DS . 'newsletters';
						$theme_serve = true;
					}
				}
				
				if (!empty($extension)) {				
					if ($extensions = $this -> get_extensions()) {					
						foreach ($extensions as $ext) {
							if ($extension == $ext['slug']) {
								$extension_folder = $ext['plugin_name'];
							}
						}
					}
					
					$filepath = WP_CONTENT_DIR . DS . 'plugins' . DS . $extension_folder . DS;
				} else {					
					if (empty($theme_serve)) {
						$filepath = $this -> plugin_base() . DS . 'views' . DS . $folder . DS;
					} else {
						$filepath = $folder . DS;
					}
				}
				
				$filefull = $filepath . $filename;
				
				if (!empty($params)) {
					foreach ($params as $key => $val) {
						${$key} = $val;
					}
				}
				
				if (file_exists($filefull)) {
					if ($output == false) {
						ob_start();
					}
					
					if (!empty($this -> classes)) {
						foreach ($this -> classes as $class) {
							global ${$class};
						}
					}
					
					if (!empty($this -> helpers)) {
						foreach ($this -> helpers as $helper) {
							global ${$helper};
						}
					}
				
					include($filefull);
					
					if ($output == false) {
						$data = ob_get_clean();
						return $data;
					} else {
						flush();
						return true;
					}
				} else {
					echo sprintf(__('Rendering of %s has failed!', $this -> plugin_name), '"' . $filefull . '"');
				}
			} else {
				echo __('No file was specified for rendering', $this -> plugin_name);
			}
		}
		
		function render_url($file = null, $folder = 'admin', $extension = null) {	
			if (!empty($file)) {		
				if (!empty($folder) && $folder != "admin") {
					$theme_folder = $this -> get_option('theme_folder');
					$folder = (!empty($theme_folder)) ? $theme_folder : $folder;
					$folderurl = plugins_url() . '/' . $this -> plugin_name . '/views/' . $folder . '/';
				
					$template_url = get_stylesheet_directory_uri();
					$theme_path = get_stylesheet_directory();
					$full_path = $theme_path . DS . 'newsletters' . DS . $file;
					
					if (!empty($theme_path) && file_exists($full_path)) {
						$folderurl = $template_url . '/newsletters/';
					}
				} else {
					$folderurl = plugins_url() . '/' . $this -> plugin_name . '/';
				}
				
				$url = $folderurl . $file;
				return $url;
			}
			
			return false;
		}
		
		function default_theme_id($type = "sending") {
			global $Db, $Theme;
			$Db -> model = $Theme -> model;
			$theme_id = 0;
			
			switch ($type) {
				case 'system'			:
					if ($theme = $Db -> find(array('defsystem' => "Y"))) {
						return $theme -> id;
					}
					break;
				case 'sending'			:
				default 				:
					if ($theme = $Db -> find(array('def' => "Y"))) {
						return $theme -> id;
					}
					break;
			}
			
			return $theme_id;
		}
		
		function make_bitly_url($url, $format = 'txt') {		
			if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $url)) {			
				if (preg_match("/^http\:\/\//si", $url) || preg_match("/^https\:\/\//si", $url)) {
					$login = $this -> get_option('shortlinkLogin');
					$appkey = $this -> get_option('shortlinkAPI');
					$bitly = 'http://api.bit.ly/v3/shorten?longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;
					$bitlink = file_get_contents($bitly);			
					return $bitlink;
				}
			}
			
			return $url;
		}
		
		function hashlink($link = null, $history_id = null, $subscriber_id = null, $user_id = null) {
			global $Html, $wpmlLink;
			$hashlink = $link;
		
			if (!empty($link)) {			
				if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $link)) {
					if (preg_match("/^http\:\/\//si", $link) || preg_match("/^https\:\/\//si", $link)) {					
						$hash = md5($text . $link);
						
						$query = $this -> pre . 'link=' . $hash . '&history_id=' . $history_id;
						
						if (!empty($subscriber_id)) { $query .= '&subscriber_id=' . $subscriber_id; }
						if (!empty($user_id)) { $query .= '&user_id=' . $user_id; }
						
						$hashlink = $Html -> retainquery($query, home_url());
						
						if (!$curlink = $wpmlLink -> find(array('hash' => $hash))) {
							$link_data = array(
								'link'			=>	$link,
								'hash'			=>	$hash,
							);	
							
							$wpmlLink -> save($link_data, true);
						}
					}
				}
			}
			
			return $hashlink;
		}
		
		function render_email($file = null, $params = array(), $output = false, $html = true, $renderht = true, $theme_id = 0, $shortlinks = true, $fullbody = false) {
			$this -> plugin_name = 'wp-mailinglist';
						
			if (!empty($file) || !empty($fullbody)) {
				$head = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'head.php';
				$foot = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'foot.php';
				
				/* Go through the parameters */
				if (!empty($params)) {
					foreach ($params as $pkey => $pval) {
						${$pkey} = $pval;
						
						switch ($pkey) {
							case 'message'		:
								global $orig_message;
								$orig_message = stripslashes($pval);
								break;
						}
					}
				}
				
				if (!empty($this -> classes)) {
					foreach ($this -> classes as $class) {
						global ${$class};
					}
				}
				
				if (!empty($this -> helpers)) {
					foreach ($this -> helpers as $helper) {
						global ${$helper};
					}
				}
	
				/* Head */			
				if ($html == true) {
					if ($renderht == true && file_exists($head)) { 
						if ($output == false) { ob_start(); }
						include($head); 
						if ($output == false) { $head = ob_get_clean(); }
					} else {
						$head = "";
					}
				}
				
				if (empty($fullbody)) {
					$filefull = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . $file . '.php';
					
					if (file_exists($filefull)) {					
						if ($output == false) { ob_start(); }
						
						include($filefull);
						if ($output == false) { $body = ob_get_clean(); }
						
						if ($output == false && $html == true) {
							$body = wpautop($body);
						}
					}
				} else {
					$body = "";
					ob_start();
					echo wpautop(stripslashes($fullbody));
					$body = ob_get_clean();
				}
				
				/* Foot */
				if ($html == true) {
					if ($renderht == true && file_exists($foot)) { 
						if ($output == false) { ob_start(); }
						include($foot); 
						if ($output == false) { $foot = ob_get_clean(); }
					} else {
						$foot = "";
					}
				}
				
				//pass the $body through the shortcodes
				$body = do_shortcode($body);
				$body = str_replace("$", "&#36;", $body);
				
				global $wpml_textmessage;
				$wpml_textmessage = $body;
				
				$pattern = '/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si';				
				if (preg_match_all($pattern, $body, $regs)) {				
					/* Bit.ly if shortlinks are enabled */
					if (!empty($shortlinks) && $shortlinks == true && $this -> get_option('shortlinks') == "Y") {								
						if (!empty($regs[1])) {
							$results = $regs[1];
							foreach($results as $k => $v) {							
								if (apply_filters('wpml_bitlink_loop', true, $v, $regs)) {
									$bitlink = $this -> make_bitly_url($v);								
									if (!empty($bitlink)) {									
										$pattern = '/[\'"](' . preg_quote($v, '/') . ')[\'"]/si';									
										$body = preg_replace($pattern, '"' . $bitlink . '"', $body);
										$regs[1][$k] = $bitlink;
									}
								}
							}
						}
					}
				
					/* Click Tracking */
					if ($this -> get_option('clicktrack') == "Y") {					
						if (!empty($regs[1])) {
							$results = $regs[1];
							foreach ($results as $rkey => $result) {
								if (apply_filters('wpml_hashlink_loop', true, $result, $regs)) {
									$hashlink = $this -> hashlink($result, $history_id, $subscriber -> id, $user -> id);								
									$pattern = '/[\'"](' . preg_quote($result, '/') . ')[\'"]/si';									
									$body = preg_replace($pattern, '"' . $hashlink . '"', $body);
								}
							}
						}
					}	
				}
				
				if (!empty($history_id)) {
					$this -> history_id = $history_id;
				}
				
				if (empty($output) || $output == false) {					
					if ($html == true) {
						global $Db, $Theme;
						$Db -> model = $Theme -> model;
						
						if (!empty($theme_id) && $theme = $Db -> find(array('id' => $theme_id))) {
							$theme_content = "";
							ob_start();					
							echo do_shortcode(stripslashes($theme -> content));
							$theme_content = ob_get_clean();
							
							$body = '<div class="newsletters_content">' . apply_filters($this -> pre . '_wpmlcontent_before_replace', $body) . '</div>';
							$new_body = preg_replace("/\[wpmlcontent\]/si", $body, $theme_content);
							
							$pattern = "/\[(\[?)(newsletters_content)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s";
							$new_body = preg_replace_callback($pattern, array($this, 'newsletters_content'), $new_body);
							
							$new_body = apply_filters($this -> pre . '_wpmlcontent_after_replace', $new_body);
							$body = $new_body;
						} else {
							$body = do_shortcode($head) . $body . do_shortcode($foot);
						}
					}
				
					return $body;
				} else {
					return true;
				}
			}
			
			return false;
		}
		
		function newsletters_content($matches = null) {
			$output = "";
		
			if (!empty($matches)) {
				$atts = shortcode_parse_atts($matches['3']);
				
				if (!empty($this -> history_id) && !empty($atts['id'])) {
					if ($contentarea = $this -> Content -> find(array('number' => $atts['id'], 'history_id' => $this -> history_id))) {
						$output = stripslashes($contentarea -> content);
					}
				}
			}
			
			return $output;
		}
		
		function check_uploaddir() {
			if (!is_admin()) return;
		
			global $uploaddir, $Html;
			$uploaddir = $Html -> uploads_path() . DS . $this -> plugin_name . DS;
			
			if (file_exists($uploaddir)) {
				/* Export subscribers folder */
				$exportdir = $uploaddir . 'export' . DS;
				if (!file_exists($exportdir)) {
					@mkdir($exportdir, 0777);
					@chmod($exportdir, 0777);
				} else {
					$exportindex = $exportdir . 'index.php';
					$exportindexcontent = "<?php /* Silence */ ?>";
					$exporthtaccess = $exportdir . '.htaccess';
					$exporthtaccesscontent = "order allow,deny\r\ndeny from all\r\n\r\nOptions All -Indexes";
					if (!file_exists($exportindex) && $fh = fopen($exportindex, "w")) { fwrite($fh, $exportindexcontent); fclose($fh); }
					if (!file_exists($exporthtaccess) && $fh = fopen($exporthtaccess, "w")) { fwrite($fh, $exporthtaccesscontent); fclose($fh); }
				}
			
				/* Embedded images folder */
				if ($this -> is_plugin_active('embedimages')) {
					$embedimagesdir = $uploaddir . 'embedimages' . DS;
					if (!file_exists($embedimagesdir)) {
						@mkdir($embedimagesdir, 0777);
						@chmod($embedimagesdir, 0777);
					}
				}
				
				/* TimThumb Cache Folder */
				$cachedir = $uploaddir . 'cache' . DS;
				if (!file_exists($cachedir)) {
					@mkdir($cachedir, 0777);
					@chmod($cachedir, 0777);
				}
				
				/* Uploadify Folder */
				$uploadifydir = $uploaddir . 'uploadify' . DS;
				if (!file_exists($uploadifydir)) {
					@mkdir($uploadifydir, 0777);
					@chmod($uploadifydir, 0777);
				} else {
					$uploadifyindex = $uploadifydir . 'index.php';
					$uploadifyindexcontent = "<?php /* Silence */ ?>";
					$uploadifyhtaccess = $uploadifydir . '.htaccess';
					$uploadifyhtaccesscontent = "order allow,deny\r\ndeny from all\r\n\r\nOptions All -Indexes";
					if (!file_exists($uploadifyindex) && $fh = fopen($uploadifyindex, "w")) { fwrite($fh, $uploadifyindexcontent); fclose($fh); }
					if (!file_exists($uploadifyhtaccess) && $fh = fopen($uploadifyhtaccess, "w")) { fwrite($fh, $uploadifyhtaccesscontent); fclose($fh); }
				}
			} else {
				@mkdir($uploaddir, 0777);
				@chmod($uploaddir, 0777);
			}
		}
		
		function render_admin($file = null, $params = array(), $output = true) {	
			if (!empty($file)) {
				$this -> plugin_name = 'wp-mailinglist';
				$filefull = $this -> plugin_base() . DS . 'views' . DS . 'admin' . DS . $file . '.php';
			
				if (!empty($params)) {
					foreach ($params as $key => $val) {
						${$key} = $val;
					}
				}
			
				if (file_exists($filefull)) {
					if ($output == false) {
						ob_start();
					}
					
					if (!empty($this -> classes)) {
						foreach ($this -> classes as $class) {
							global ${$class};
						}
					}
					
					if (!empty($this -> helpers)) {
						foreach ($this -> helpers as $helper) {
							global ${$helper};
						}
					}
				
					include($filefull);
					
					if ($output == false) {
						$data = ob_get_clean();
						return $data;
					} else {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function extension_vendor($name = null) {
			if (!empty($name)) {
				switch ($name) {
					/* Embedded Images */
					case 'embedimages'						:
						$filepath = 'newsletters-embedimages' . DS . 'embedimages.php';
						break;
				}
				
				$filefull = WP_CONTENT_DIR . DS . 'plugins' . DS . $filepath;
				
				if (file_exists($filefull)) {
					
					require_once $filefull;
					$class = $this -> pre . $name;
					
					if (class_exists($class)) {
						${$name} = new $class;
						return ${$name};	
					}
				}
			}
		}
		
		function get_extensions() {
			include $this -> plugin_base() . DS . 'includes' . DS . 'extensions.php';
			$extensions = apply_filters($this -> pre . '_extensions_list', $extensions);
			$this -> extensions = $extensions;
			
			if (!empty($extensions) && is_array($extensions)) {
				$titles = array();
				foreach ($extensions as $extension) {
					$titles[] = $extension['name'];
				}
				
				array_multisort($titles, SORT_ASC, $extensions);
				return $extensions;
			}
			
			return false;
		}
		
		function is_plugin_active($name = null, $orinactive = false) {
			if (!empty($name)) {
				require_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'admin.php';
				
				if ($extensions = $this -> get_extensions()) {			
					foreach ($extensions as $extension) {
						if ($name == $extension['slug']) {
							$path = $extension['plugin_name'] . DS . $extension['plugin_file'];
						}
					}
				}
	
				if (empty($path)) {			
					switch ($name) {
						case 'embedimages'							:
							$path = 'newsletters-embedimages' . DS . 'embedimages.php';
							break;
						case 'newsletters-cforms'					:
							$path = 'newsletters-cforms' . DS . 'cforms.php';
							break;
						case 'qtranslate'							:
							$path = 'qtranslate' . DS . 'qtranslate.php';
							break;
						case 'captcha'								:
							$path = 'really-simple-captcha' . DS . 'really-simple-captcha.php';
							break;
					}
				}
				
				$path2 = str_replace("\\", "/", $path);
				
				if (!empty($path)) {
					$plugins = get_plugins();
					
					if (!empty($plugins)) {
						if (array_key_exists($path, $plugins) || array_key_exists($path2, $plugins)) {
							/* Let's see if the plugin is installed and activated */
							if (is_plugin_active(plugin_basename($path)) ||
								is_plugin_active(plugin_basename($path2))) {
								return true;
							}
							
							/* Maybe the plugin is installed but just not activated? */
							if (!empty($orinactive) && $orinactive == true) {
								if (is_plugin_inactive(plugin_basename($path)) ||
									is_plugin_inactive(plugin_basename($path2))) {
									return true;	
								}
							}	
						}
					}
				}
			}
			
			return false;
		}
	
		function use_captcha($status) {						
			if (true || $status == 'Y') {			
				$captcha_type = $this -> get_option('captcha_type');
				if (!empty($captcha_type)) {				
					switch ($captcha_type) {
						case 'rsc'				:
							if ($this -> is_plugin_active('captcha')) {
								return "rsc";
							}
							break;
						case 'recaptcha'		:
							return "recaptcha";
							break;
						case 'none'				:
						default 				:
							return false;
							break;
					}
				}
			}
			
			return false;
		}	
	}
}

class fakemailer {
    public function Send() {
        throw new phpmailerException( 'Cancelling mail' );
    }
}

if ( ! class_exists( 'phpmailerException' ) ) {
	/*class phpmailerException extends Exception {
	    public function errorMessage() {
	        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
	        return $errorMsg;
	    }
	}*/
}

?>