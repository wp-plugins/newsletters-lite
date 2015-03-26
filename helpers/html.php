<?php

class wpmlHtmlHelper extends wpMailPlugin {

	var $name = 'Html';
	
	function wpmlHtmlHelper() {
		return true;
	}
	
	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	 */
	function strip_array_indices( $ArrayToStrip ) {
	    foreach( $ArrayToStrip as $objArrayItem) {
	        $NewArray[] =  $objArrayItem;
	    }
	 
	    return( $NewArray );
	}
	 
	/**
	 * Convert the php date format string to a js date format
	 */
	function date_format_php_to_js( $sFormat ) {
	    switch( $sFormat ) {
	        //Predefined WP date formats
	        case 'F j, Y':
	            return( 'MM dd, yy' );
	            break;
	        case 'Y/m/d':
	            return( 'yy/mm/dd' );
	            break;
	        case 'm/d/Y':
	            return( 'mm/dd/yy' );
	            break;
	        case 'd/m/Y':
	            return( 'dd/mm/yy' );
	            break;
	     }
	}
	
	function is_json($string = null) {
		if (!empty($string)) {
			if (is_string($string) && is_object(json_decode($string))) {
				return true;
			}
		}
		
		return false;
	}
	
	function hidden_type_operator($key = null) {
		$operator = false;
	
		if (!empty($key)) {
			
			switch ($key) {
				case 'post'			:
					$operator = "&#36;_POST";
					break;
				case 'get'			:
					$operator = "&#36;_GET";
					break;
				case 'global'		:
					$operator = "&#36;GLOBALS";
					break;
				case 'cookie'		:
					$operator = "&#36;_COOKIE";
					break;
				case 'session'		:
					$operator = "&#36;_SESSION";
					break;
				case 'server'		:
					$operator = "&#36;_SERVER";
					break;
			}
		}
		
		return $operator;
	}
	
	function fragment_cache($content = null, $object = null, $method = null, $data = null) {
		$output = "";
	
		if (!empty($content)) {				
			if (is_plugin_active(plugin_basename('wp-super-cache/wp-cache.php'))) {			
				return $content;
			
				//global $wp_cache_config_file, $newsletters_wpsc_cachedata;
				//include $wp_cache_config_file;
				//if (empty($wp_cache_mfunc_enabled)) { wp_cache_replace_line('^ *\$wp_cache_mfunc_enabled', "\$wp_cache_mfunc_enabled = 1;", $wp_cache_config_file); }
				//if (empty($wp_super_cache_late_init)) { wp_cache_replace_line('^ *\$wp_super_cache_late_init', "\$wp_super_cache_late_init = 1;", $wp_cache_config_file); }
				//if (empty($wp_cache_mod_rewrite)) { wp_cache_replace_line('^ *\$wp_cache_mod_rewrite', "\$wp_cache_mod_rewrite = 0;", $wp_cache_config_file); }
			} elseif (is_plugin_active(plugin_basename('w3-total-cache/w3-total-cache.php'))) {	
				$output .= '<!--mfunc ' . W3TC_DYNAMIC_SECURITY . ' ?>' . $content . '<?php -->';
				$output .= $content;
				$output .= '<!--/mfunc ' . W3TC_DYNAMIC_SECURITY . ' -->';
			} elseif (is_plugin_active(plugin_basename('quick-cache/quick-cache.php'))) {
				define('QUICK_CACHE_ALLOWED', FALSE);
				$output = $content;
			} else {
				$output = $content;
			}
		}
		
		return $output;
	}
	
	function wp_has_current_submenu($submenu = false) {
		$menu = false;
		if (!empty($submenu)) {
			if (preg_match("/^newsletters\-([^-]+)?/si", $submenu, $matches)) {
				$menu = $matches[0];
			}
		}
	
		?>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('li#toplevel_page_newsletters').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
			jQuery('li#toplevel_page_newsletters > a').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
			<?php if (!empty($menu)) : ?>jQuery('li#toplevel_page_newsletters ul.wp-submenu li a[href="admin.php?page=<?php echo $menu; ?>"]').attr('class', "current").parent().attr('class', "current");<?php endif; ?>
		});
		</script>
		
		<?php
	}
	
	function help($help = null) {
		if (!empty($help)) {
			ob_start();
		
			?>
			
			<span class="wpmlhelp"><a href="" onclick="return false;" title="<?php echo esc_attr(stripslashes($help)); ?>"></a></span>
			
			<?php
			
			$html = ob_get_clean();
			return $html;
		}
	}
	
	function hex2rgb( $colour ) {
	    if ( $colour[0] == '#' ) {
	            $colour = substr( $colour, 1 );
	    }
	    if ( strlen( $colour ) == 6 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	    } elseif ( strlen( $colour ) == 3 ) {
	            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	    } else {
	            return false;
	    }
	    $r = hexdec( $r );
	    $g = hexdec( $g );
	    $b = hexdec( $b );
	    
	    return array($r, $g, $b);
	}
	
	function eunique($subscriber_id = null, $email_id = null) {
		if (!empty($subscriber_id) && !empty($email_id)) {
			return md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> id . date_i18n("YmdH", time()));	
		}
	}
	
	function time_difference($time_one = null, $time_two = null, $interval = 'days') {
		$difference = 0;
	
		if (!empty($time_one) && !empty($time_two)) {
			switch ($interval) {
				case 'minutes'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60));
					break;
				case 'hours'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60));
					break;
				case 'days'					:
				default						:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24));
					break;
				case 'weeks'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24 * 7));
					break;
				case 'years'				:
					$one = strtotime($time_one);
					$two = strtotime($time_two);			
					$difference = floor(($one - $two) / (60 * 60 * 24 * 7 * 52));
					break;
			}
		}
		
		return $difference;
	}
	
	/*
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	function dateformat_PHP_to_jQueryUI($php_format)
	{
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}
	
	function days_difference($date_one = null, $date_two = null) {
		return $this -> time_difference($date_one, $date_two, 'days');
	
		$days = 0;
		
		if (!empty($date_one) && !empty($date_two)) {
			$one = strtotime($date_one);
			$two = strtotime($date_two);			
			$days = floor(($one - $two) / (60 * 60 * 24));
		}
		
		return $days;	
	}
	
	function field_type($type = null) {	
		if (!empty($type)) {
			$fieldtypes = array(
				'special'		=>	__('Special', $this -> plugin_name),
				'text'			=>	__('Text Field', $this -> plugin_name),
				'textarea'		=>	__('Text Area', $this -> plugin_name),
				'select'		=>	__('Select Drop Down', $this -> plugin_name),
				'radio'			=>	__('Radio Buttons', $this -> plugin_name),
				'checkbox'		=>	__('Checkboxes', $this -> plugin_name),
				'file'			=>	__('File Upload', $this -> plugin_name),
				'pre_country'	=>	__('Predefined : Country Select', $this -> plugin_name),
				'pre_date'		=>	__('Predefined : Date Picker', $this -> plugin_name),
				'pre_gender'	=>	__('Predefined : Gender', $this -> plugin_name),
				'hidden'		=>	__('Hidden', $this -> plugin_name),
			);	
			
			return $fieldtypes[$type];
		}
	
		return false;	
	}
	
	function uploads_path($dated = false) {
		if ($upload_dir = wp_upload_dir()) {	
			if ($dated) {
				return str_replace("\\", "/", $upload_dir['path']);	
			} else {
				return str_replace("\\", "/", $upload_dir['basedir']);
			}
		}
		
		return str_replace("\\", "/", WP_CONTENT_DIR . '/uploads');
	}
	
	function uploads_subdir() {
		$subdir = '';
	
		if ($upload_dir = wp_upload_dir()) {
			if (!empty($upload_dir['subdir'])) {
				$subdir = $upload_dir['subdir'];
			}
		}
		
		return $subdir;
	}
	
	function uploads_url() {
		if ($upload_dir = wp_upload_dir()) {
			return $upload_dir['baseurl'];
		}
		
		return site_url() . '/wp-content/uploads';
	}
	
	function file_custom_field($value = null, $limit = false, $types = false) {	
		$output = false;
		
		if (!empty($value)) {
			$currentfile = '<p class="currentfile">';						
			$imagetypes = array('jpg','jpeg','gif','png');
			$imagename = $value;
			$imagepath = $this -> uploads_path() . '/' . $this -> plugin_name . '/uploadify/' . $imagename;
			$imageurl = $this -> uploads_url() . '/' . $this -> plugin_name . '/uploadify/' . $imagename;
			$imageinfo = pathinfo($imagepath);
			$ajaxuploadurl = site_url() . '/?' . $this -> pre . 'method=ajaxupload&file=' . urlencode($imagename);
			$currentfile .= '<a href="' . $ajaxuploadurl . '" target="_blank">' . __('Uploaded file', $this -> plugin_name) . '</a>';			
			$currentfile .= '</p>';
			$output .= $currentfile;
		}

		return $output;
	}
	
	function get_gravatar($email = null, $s = 50, $d = 'mm', $r = 'g', $img = true, $atts = array() ) {
		/*$src = 'http://www.gravatar.com/avatar/';
		$src .= md5( strtolower( trim( $email ) ) );
		$src .= "?s=$s&d=$d&r=$r";
		if ($img) {
			$url = '';
			$url .= '<div class="newsletters_gravatar_wrapper">';
			$url .= '<img class="newsletters_gravatar newsletters_circular" src="' . $src . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
			$url .= '</div>';
		}
		return $url;*/
		
		return get_avatar($email, $s, null, false);
	}
	
	function wordpress_usermeta_fields() {
		$usermeta = array(
			'first_name'				=>	__('First Name', $this -> plugin_name),
			'last_name'					=>	__('Last Name', $this -> plugin_name),
			'nickname'					=>	__('Nickname', $this -> plugin_name),
			'description'				=>	__('Biographical Info', $this -> plugin_name),
		);
		
		return $usermeta;
	}
	
	function RoundUptoNearestN($biggs){
       $rounders = (strlen($biggs) - 2) * -1;
       $places = (strlen($biggs) -2);

       $counter = 0;
       while ($counter <= $places) {
           $counter++;
               if($counter == 1) {
               		$holder = $holder . '1'; }
               else {
	                $holder = $holder . '0'; }
       }

       $biggs = $biggs + $holder;
       $biggs = round($biggs, $rounders);
       if ($biggs < 30) { $biggs = 30; } elseif ($biggs < 50) { $biggs = 50; } elseif ($biggs < 80) { $biggs = 80; } elseif ($biggs < 100) { $biggs = 100; }
       return $biggs;
	}
	
	function next_scheduled($hook = null, $args = null) {
		if (!preg_match("/(newsletters)/si", $hook)) {
			$hook = $this -> pre . '_' . $hook;
		}
		
		$args = (empty($args)) ? array() : $args;
	
		if (!empty($hook) && $schedules = wp_get_schedules()) {	
			if ($hookinterval = wp_get_schedule($hook, $args)) {
				if ($hookschedule = wp_next_scheduled($hook, $args)) {				
					return $schedules[$hookinterval]['display'] . ' - <strong>' . date_i18n("Y-m-d H:i:s", $hookschedule) . '</strong>';
				} else {
					return __('This task does not have a next schedule.', $this -> plugin_name);	
				}
			} else {
				return __('No schedule has been set for this task.', $this -> plugin_name);	
			}
		} else {
			return __('No cron schedules are available or no task was specified.', $this -> plugin_name);	
		}
		
		return false;
	}
	
	function attachment_link($attachment = null, $icononly = false, $truncate = 20) {		
		$attachmentfile = "";
		
		if (!empty($attachment['subdir'])) {
			$attachmentfile .= $attachment['subdir'] . '/';
		}
		
		$attachmentfile .= basename($attachment['filename']);
		$attachmentfile = ltrim($attachmentfile, "/");
	
		if (!empty($attachmentfile)) {			
			if ($icononly == false) {
				return '<a class="button newsletters_attachment_link" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '">' . $this -> truncate(basename($attachmentfile), $truncate) . '</a>';
			} else {
				return '<a class="button newsletters_attachment_link" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '"></a>';
			}
		}
		
		return false;
	}
	
	function section_name($slug = null) {
		$name = "";
		
		if (!empty($slug)) {
			switch ($slug) {
				case 'welcome'			:
					$name = __('Overview', $this -> plugin_name);
					break;
				case 'submitserial'		:
					$name = __('Submit Serial', $this -> plugin_name);
					break;
				case 'send'				:
					$name = __('Create Newsletter', $this -> plugin_name);
					break;
				case 'autoresponders'	:
					$name = __('Autoresponders', $this -> plugin_name);
					break;
				case 'autoresponderemails'	:
					$name = __('Autoresponder Emails', $this -> plugin_name);
					break;
				case 'lists'			:
					$name = __('Mailing Lists', $this -> plugin_name);
					break;
				case 'groups'			:
					$name = __('Groups', $this -> plugin_name);
					break;
				case 'subscribers'		:
					$name = __('Subscribers', $this -> plugin_name);
					break;
				case 'fields'			:
					$name = __('Custom Fields', $this -> plugin_name);
					break;
				case 'importexport'		:
					$name = __('Import/Export', $this -> plugin_name);
					break;
				case 'themes'			:
					$name = __('Templates', $this -> plugin_name);
					break;
				case 'templates'		:
					$name = __('Email Snippets', $this -> plugin_name);
					break;
				case 'templates_save'	:
					$name = __('Save Email Snippets', $this -> plugin_name);
					break;
				case 'queue'			:
					$name = __('Email Queue', $this -> plugin_name);
					break;
				case 'history'			:
					$name = __('Sent &amp; Draft Emails', $this -> plugin_name);
					break;
				case 'links'			:
					$name = __('Links', $this -> plugin_name);
					break;
				case 'clicks'			:
					$name = __('Clicks', $this -> plugin_name); 
					break;
				case 'orders'			:
					$name = __('Subscribe Orders', $this -> plugin_name);
					break;
				case 'settings'			:
					$name = __('General Configuration', $this -> plugin_name);
					break;
				case 'settings_subscribers'	:
					$name = __('Subscribers Configuration', $this -> plugin_name);
					break;
				case 'settings_templates'	:
					$name = __('System Emails Configuration', $this -> plugin_name);
					break;
				case 'settings_system'		:
					$name = __('System Configuration', $this -> plugin_name);
					break;
				case 'settings_tasks'		:
					$name = __('Scheduled Tasks', $this -> plugin_name); 
					break;
				case 'settings_updates'		:
					$name = __('Updates', $this -> plugin_name); 
					break;
				case 'settings_api'			:
					$name = __('API', $this -> plugin_name);
					break;
				case 'extensions'			:
					$name = __('Extensions', $this -> plugin_name);
					break;
				case 'extensions_settings'	:
					$name = __('Extensions Settings', $this -> plugin_name);
					break;
				case 'support'				:
					$name = __('Support &amp; Help', $this -> plugin_name);
					break;
				case 'lite_upgrade'			:
					$name = __('Upgrade to PRO', $this -> plugin_name);
					break;
			}
		}
		
		return $name;
	}
	
	function getppt($interval = null) {
		switch ($interval) {
			case 'daily'		:
				$t = "D";
				break;
			case 'weekly'		:
				$t = "W";
				break;
			case 'monthly'		:
			case '2months'		:
			case '3months'		:
			case 'biannually'	:
			case '9months'		:
				$t = "M";
				break;
			case 'yearly'		:
				$t = "Y";
				break;
			default				:
				$t = "D";
				break;
		}
		
		return $t;
	}
	
	function getpptd($interval) {
		switch ($interval) {
			case 'daily'		:
				$d = "1";
				break;
			case 'weekly'		:
				$d = "1";
				break;
			case 'monthly'		:
				$d = "1";
				break;
			case '2months'		:
				$d = "2";
				break;
			case '3months'		:
				$d = "3";
				break;
			case 'biannually'	:
				$d = "6";
				break;
			case '9months'		:
				$d = "9";
				break;
			case 'yearly'		:
				$d = "1";
				break;
			default				:
				$d = "1";
				break;
		}
		
		return $d;
	}

    function priority_val($priority_key) {
        switch ($priority_key) {
            case 1              :
                $priority_val = "High";
                break;
            case 3              :
                $priority_val = "Normal";
                break;
            case 5              :
                $priority_val = "Low";
                break;
            default             :
                $priority_val = "Normal";
                break;
        }

        return $priority_val;
    }
	
	function link($name = null, $href = null, $options = array()) {
		if (!empty($name) || $name == "0") {
			$defaults = array(
				'target' 		=> 	'_self', 
				'title' 		=> 	$name,
				'onclick'		=>	"",
				'class'			=>	"",
			);
			
			$r = wp_parse_args($options, $defaults);
			extract($r, EXTR_SKIP);
				
			ob_start();
			
			?>
			
			<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" onclick="<?php echo $onclick; ?>" title="<?php echo $title; ?>" target="<?php echo $target; ?>"><?php echo $name; ?></a>
			
			<?php
			
			$link = ob_get_clean();
			return $link;
		}
		
		return false;
	}
	
	function tabi() {
		global $wpmltabindex;
		if (empty($wpmltabindex) || !$wpmltabindex) { $wpmltabindex = 1; };
		return $wpmltabindex;
	}
	
	function tabindex($optinid = null) {
		global $wpmltabindex;
		
		if (empty($wpmltabindex) || !$wpmltabindex) {
			$wpmltabindex = 1;
		}
		
		$wpmltabindex++;
		$string = $optinid . $wpmltabindex;
		$tabindex = 'tabindex="9' . preg_replace("/[^0-9]+/si", "", $string) . '"';
		return $tabindex;
	}
	
	function gen_date($format = "Y-m-d H:i:s", $time = false) {
		if (empty($format)) { $format = "Y-m-d H:i:s"; } 
		$this -> set_timezone();
		$newtime = (empty($time)) ? time() : $time;
		return date_i18n($format, $newtime);
	}
	
	function gender($gender = null) {
		switch ($gender) {
			case 'male'			:
				return __('Male', $this -> plugin_name);
				break;
			case 'female'		:
				return __('Female', $this -> plugin_name);
				break;	
		}
	}
	
	function currency() {
		$currency = $this -> get_option('currency');
		$currencies = maybe_unserialize($this -> get_option('currencies'));		
		return $currencies[$currency]['symbol'];
	}
	
	function field_value($name = null, $language = false) {
		$value = "";
		
		if (!empty($name)) {				
			if ($mn = $this -> strip_mn($name)) {
				global ${$mn[1]};

				if (is_array(${$mn[1]} -> data) && !empty(${$mn[1]} -> data[$mn[1]])) {
					$value = ${$mn[1]} -> data[$mn[1]] -> {$mn[2]};
				} else {
					$value = ${$mn[1]} -> data -> {$mn[2]};
				}
				
				if ($this -> language_do() && $language) {
					if ($mn[2] == "fieldoptions") {
						$alloptions = maybe_unserialize($value);
						$optionarray = array();
						
						if (!empty($alloptions)) {
							foreach ($alloptions as $alloption) {
								$alloptionsplit = $this -> language_split($alloption);
								$optionarray[] = trim($alloptionsplit[$language]);
							}
						}
						
						return trim(@implode("\r\n", $optionarray));
					} else {
						if ($texts = $this -> language_split($value)) {
							if (!empty($texts[$language])) {
								return $texts[$language];
							}
						}
					}
				}
			}
		}

        return $value;
	}
	
	function field_error($name = null) {
		if (!empty($name)) {		
			if ($mn = $this -> strip_mn($name)) {
				global ${$mn[1]};
				if (!empty(${$mn[1]} -> errors[$mn[2]])) {
					ob_start();
					echo '<div class="' . $this -> pre . 'error">' . ${$mn[1]} -> errors[$mn[2]] . '</div>';
					return ob_get_clean();
				}
			}
		}
		
		return false;
	}
	
	function field_id($name = null) {
		if (!empty($name)) {
			if ($matches = $this -> strip_mn($name)) {
				$id = $matches[1] . '.' . $matches[2];
				return $id;
			}
		}
	
		return false;
	}
	
	function file_upload_error($code = 0) {
		if (!empty($code)) {
			switch ($code) {
				case 1			:
					$error = __('The uploaded file exceeds the PHP upload_max_filesize directive.', $this -> plugin_name);
					break;
				case 2			:
					$error = __('The uploaded file exceeds the max_file_size directive specified in the form.', $this -> plugin_name);
					break;
				case 3			:
					$error = __('The uploaded file was only partially uploaded.', $this -> plugin_name);
					break;
				case 4			:
					$error = __('No file was uploaded.', $this -> plugin_name);
					break;
				case 6			:
					$error = __('Missing a temporary folder.', $this -> plugin_name);
					break;
				case 7			:
					$error = __('Failed to write file to disk.', $this -> plugin_name);
					break;
				case 8			:
					$error = __('A PHP extension stopped the file upload.', $this -> plugin_name);
					break;
				default			:
					$error = __('An error occurred. Please try again.', $this -> plugin_name);
					break;
			}
			
			return $error;
		}
		
		return false;
	}
	
	function sanitize($string = null, $sep = '-') {
		if (!empty($string)) {
			//$string = ereg_replace("[^0-9a-z" . $sep . "]", "", strtolower(str_replace(" ", $sep, $string)));
			$string = strtolower(preg_replace("/[^0-9A-Za-z" . $sep . "]/si", "", str_replace(" ", $sep, $string)));
			$string = preg_replace("/" . $sep . "[" . $sep . "]*/si", $sep, $string);
			
			return $string;
		}
	
		return false;
	}
	
	function strip_mn($name = null) {
		if (!empty($name)) {
			if (preg_match("/(.*?)\[(.*?)\]/si", $name, $matches)) {
				return $matches;
			}
		}
	
		return false;
	}
	
	function truncate($text = null, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		if (is_array($ending)) {
			extract($ending);
		}
		
		if ($considerHtml) {
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}

			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {
				if (!empty($line_matchings[1])) {
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					$truncate .= $line_matchings[1];
				}

				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
					$left = $length - $total_length;
					$entities_length = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}

				if ($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}

		if (!$exact) {
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = substr($truncate, 0, $spacepos);
			}
		}

		$truncate .= $ending;

		if ($considerHtml) {
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
	
	function queryString($params, $name = null) {
		$ret = "";
		foreach ($params as $key => $val) {
			if (is_array($val)) {
				if ($name == null) {
					$ret .= $this -> queryString($val, $key);
				} else {
					$ret .= $this -> queryString($val, $name . "[$key]");   
				}
			} else {
				if ($name != null) {
					$ret .= $name . "[$key]" . "=" . $val . "&";
				} else {
					$ret .= $key . "=" . $val . "&";
				}
			}
		}
		
		return rtrim($ret, "&");   
	} 
	
	function retainquery($add = null, $old_url = null, $endslash = true, $onlyquery = false) {
		if (is_array($add)) {
			$add = implode("&", $add);
		}
	
		$url = (empty($old_url)) ? $_SERVER['REQUEST_URI'] : rtrim($old_url, '&');
		$url = rawurldecode($url);
		$url = preg_replace("/\&?wpmlmessage\=(.*)\&?/si", "", $url);
		//$url = preg_replace("/\&?wpmlupdated\=(.*)\&?/si", "", $url);
		
		$urls = @explode("?", $url);
		$add = ltrim($add, '&');
		
		$url_parts = @parse_url($url);
		parse_str($url_parts['query'], $path_parts);
		$add = str_replace("&amp;", "&", $add);
		parse_str($add, $add_parts);
		
		if (empty($path_parts) || !is_array($path_parts)) {
			$path_parts = array();	
		}
			
		if (!empty($add_parts) && is_array($add_parts)) {
			foreach ($add_parts as $addkey => $addvalue) {
				//$path_parts[$addkey] = stripslashes($addvalue);
				$path_parts[$addkey] = $addvalue;
			}
		}

		$querystring = $this -> queryString($path_parts);
		
		//$urls[1] = preg_replace("/[\&|\?]" . $this -> pre . "message\=([0-9a-z-_+]*)/i", "", $urls[1]);
		//$urls[1] = preg_replace("/[\&|\?]page\=/si", "", $urls[1]);
		
		$url = $urls[0];
		$url .= '?';
		
		if (!empty($querystring)) {
			$url .= '&' . $querystring;
			
			if ($onlyquery) {
				return $querystring;
			}
		}
				
		return preg_replace("/\?(\&)?/si", "?", $url);
	}
}

?>