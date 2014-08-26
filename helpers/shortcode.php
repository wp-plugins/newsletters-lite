<?php

class wpmlShortcodeHelper extends wpMailPlugin {

	var $name = 'Shortcode';

	function wpmlShortcodeHelper() {
		return true;
	}
	
	function subscriberscount($atts = array(), $content = null) {
		global $wpdb, $Subscriber;
		$subscriberscount = 0;
		
		$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Subscriber -> table . "`";
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			return ${'newsletters_query_' . $query_hash};
		} else {
			$count = $wpdb -> get_var($query);
			${'newsletters_query_' . $query_hash} = $count;
		}
		
		if (!empty($count)) {
			$subscriberscount = $count;
		}
		
		return $subscriberscount;
	}
	
	function post_thumbnail($atts = array(), $content = null) {	
		global $post;
		$output = "";
		$thepost = (empty($atts['post_id'])) ? $post : get_post($atts['post_id']);
		$atts['post_id'] = $thepost -> ID;
	
		$defaults = array(
			'post_id'			=>	false,
			'size'				=>	"thumbnail",
			'alt'				=>	$thepost -> post_title,
			'link'				=>	$thepost -> guid,
			'title'				=>	$thepost -> post_title,
		);
		
		extract(shortcode_atts($defaults, $atts));
		
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($thepost -> ID)) {
			$output .= (!empty($link)) ? '<a href="' . $link . '" title="' . esc_attr(__($title)) . '">' : '';
			
			$thumbnail_attr = array('align' => "left", 'style' => "margin-right:15px;", 'hspace' => "15", 'title' => __($title), 'alt' => $alt);
			$thumbnail_attr = apply_filters('newsletters_post_thumbnail_attr', $thumbnail_attr, $thepost -> ID);
			
			$output .= get_the_post_thumbnail($thepost -> ID, $size, $thumbnail_attr);
			$output .= (!empty($link)) ? '</a>' : '';
		}
		
		wp_reset_query();
		return $output;
	}
	
	function post_permalink($atts = array(), $content = null) {
		global $post;
		$output = "";
		$post_id = (empty($atts['post_id'])) ? $post -> ID : $atts['post_id'];
		
		if ($permalink = get_permalink($post_id)) {
			$output .= $permalink;
		}
				
		wp_reset_query();
		return $output;
	}
	
	function posts_single($atts = array(), $content = null) {
		global $post, $shortcode_posts, $shortcode_post_language, $shortcode_post_showdate;
		$atts['post_id'] = (empty($atts['post_id'])) ? $post -> ID : $atts['post_id'];
			
		$output = "";
		$defaults = array(
			'post_id'			=> 	false,
			'showdate'			=>	"Y",
			'language'			=>	false,
			'eftype'			=>	"excerpt",
			'target'			=>	"_self",
		);
			
		$r = shortcode_atts($defaults, $atts);
		extract($r);
		
		$arguments = array(
			'post_id'			=>	$post_id,
			'showdate'			=>	$showdate,
			'language'			=>	$language,
		);
		
		foreach ($r as $rkey => $rval) {
			global ${'wpml_' . $rkey};
			${'wpml_' . $rkey} = $rval;
		}
		
		if (!empty($post_id)) {
			if ($post = get_post($post_id)) {
				$shortcode_post_showdate = $showdate;
				
				if ($this -> language_do()) {
					$shortcode_post_language = $language;
					$post = $this -> language_use($language, $post, false);
					$shortcode_posts = array($post);
					$output = do_shortcode($this -> et_message('posts', false, $language));
				} else {
					$shortcode_posts = array($post);
					$output = do_shortcode($this -> et_message('posts'));
				}
			}
		}
		
		wp_reset_query();
		return $output;
	}
	
	function posts_multiple($atts = array(), $content = null) {
		global $shortcode_posts, $shortcode_post_language, $shortcode_post_showdate;
		$output = "";
		
		$defaults = array(
			'numberposts' 			=> 	10, 
			'showdate'				=>	"Y",
			'orderby' 				=> 	"post_date", 
			'order' 				=> 	"DESC", 
			'category' 				=> 	null,
			'language'				=>	false,
			'post_type'				=>	"post",
			'eftype'				=>	"excerpt",
			'target'				=>	"_self",
		);
		
		$arguments = shortcode_atts($defaults, $atts);
		if (empty($arguments['category'])) { $arguments['category'] = false; }
		
		if (!empty($arguments['post_type'])) {
			if (($new_post_types = @explode(",", $arguments['post_type'])) !== false) {
				$arguments['post_type'] = $new_post_types;
			}
		}
		
		global $wpml_eftype, $wpml_target;
		$wpml_eftype = $eftype;
		$wpml_target = $target;
		
		extract($arguments);
							   
		if ($posts = get_posts($arguments)) {	
			$shortcode_post_showdate = $showdate;
			
			if ($this -> is_plugin_active('qtranslate')) {
				$shortcode_post_language = $arguments['language'];
			
				foreach ($posts as $pkey => $post) {
					$posts[$pkey] = $this -> language_use($arguments['language'], $post, false);
				}
				
				$shortcode_posts = $posts;
				$output = do_shortcode($this -> et_message('posts', false, $language));
			} else {
				$shortcode_posts = $posts;
				$output = do_shortcode($this -> et_message('posts'));
			}
		}
		
		wp_reset_query();
		return $output;
	}
	
	function shortcode_posts($atts = array(), $content = null, $tag = null) {
		global $wpml_eftype, $wpml_target, $Html, $shortcode_posts, $shortcode_categories, $shortcode_category, $shortcode_categories_done, $shortcode_post, $shortcode_post_row, $shortcode_post_language, $shortcode_post_showdate;
		$return = "";
		
		if (!empty($wpml_eftype) && $wpml_eftype == "full" && $tag == "post_excerpt") {
			$tag = "post_content";
		}
		
		switch ($tag) {
			case 'category_heading'						:
			case 'newsletters_category_heading'			:
				$category_heading = "";
				
				if (!empty($shortcode_category)) {
					if (empty($shortcode_categories_done) || (!empty($shortcode_categories_done) && !in_array($shortcode_category -> cat_ID, $shortcode_categories_done))) {
						$category_heading = '<a href="' . get_category_link($shortcode_category -> cat_ID) . '">' . __($shortcode_category -> name) . '</a>';
						$shortcode_categories_done[] = $shortcode_category -> cat_ID;
					}					
				}
				
				return $category_heading;
				break;
			case 'post_loop'				:
			case 'newsletters_post_loop'	:			
				if (!empty($shortcode_categories)) {	
					$shortcode_post_row = 1;			
					foreach ($shortcode_categories as $category) {
						$shortcode_category = $category['category'];
						$shortcode_posts = $category['posts'];
						
						foreach ($shortcode_posts as $post) {
							$shortcode_post = $post;
							$return .= do_shortcode($content);
							$shortcode_post_row++;
						}
					}
				} elseif (!empty($shortcode_posts)) {
					$shortcode_post_row = 1;				
					foreach ($shortcode_posts as $post) {
						$shortcode_post = $post;
						$return .= do_shortcode($content);
						$shortcode_post_row++;
					}
				}
				
				return $return;
				break;
			case 'post_id'					:
			case 'newsletters_post_id'		:
				if (!empty($shortcode_post)) {
					return $shortcode_post -> ID;
				}
				break;
			case 'post_author'				:	
			case 'newsletters_post_author'	:		
				setup_postdata($shortcode_post);
				$return = get_the_author();
				wp_reset_postdata();
				return $return;
				break;
			case 'post_title'				:
			case 'newsletters_post_title'	:
				if (!empty($shortcode_post)) {
					return $shortcode_post -> post_title;
				}
				break;
			case 'post_link'				:
			case 'newsletters_post_link'	:			
				if (!empty($shortcode_post)) {
					return $this -> direct_post_permalink($shortcode_post -> ID);
				}
				break;
			case 'post_date_wrapper'				:
			case 'newsletters_post_date_wrapper'	:							
				if (empty($shortcode_post_showdate) || (!empty($shortcode_post_showdate) && $shortcode_post_showdate == "Y")) {
					return do_shortcode($content);
				} else {
					return "";
				}
				break;
			case 'post_date'				:
			case 'newsletters_post_date'	:			
				$defaults = array('format' => "F jS, Y");
				extract(shortcode_atts($defaults, $atts));
			
				if (!empty($shortcode_post)) {
					return $Html -> gen_date($format, strtotime($shortcode_post -> post_date));
				}
				break;
			case 'post_thumbnail'			:
			case 'newsletters_post_thumbnail'	:
				$defaults = array('size' => "thumbnail");
				extract(shortcode_atts($defaults, $atts));
				if (!empty($shortcode_post)) {
					if (function_exists('has_post_thumbnail')) {
						if (has_post_thumbnail($shortcode_post -> ID)) {							
							$return .= '<a target="' . $wpml_target . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '">';
							$attr = apply_filters('newsletters_post_thumbnail_attr', array('style' => "margin-right:15px;", 'align' => "left", 'hspace' => "15", 'class' => "post_thumbnail"), $shortcode_post -> ID);
							$return .= get_the_post_thumbnail($shortcode_post -> ID, $size, $attr);
							$return .= '</a>';						
							return $return;
						}
					}
				}
				break;
			case 'post_excerpt'				:
			case 'newsletters_post_excerpt'	:
				if (empty($wpml_eftype) || (!empty($wpml_eftype) && $wpml_eftype != "full")) {					
					$this -> add_filter('excerpt_length');
					$this -> add_filter('excerpt_more');
					
					if (!empty($shortcode_post)) {
						setup_postdata($shortcode_post);
						$return .= get_the_excerpt();
						wp_reset_postdata();
					}
				} else {
					setup_postdata($shortcode_post);
					$return = wpautop(get_the_content());
					wp_reset_postdata();
				}
				
				return $return;
			case 'post_content'				:
			case 'newsletters_post_content'	:
				if (empty($wpml_eftype) || (!empty($wpml_eftype) && $wpml_eftype != "excerpt")) {
					setup_postdata($shortcode_post);
					$return = wpautop(get_the_content());
					wp_reset_postdata();
				} else {
					$this -> add_filter('excerpt_length');
					$this -> add_filter('excerpt_more');
					
					if (!empty($shortcode_post)) {
						setup_postdata($shortcode_post);
						$return .= get_the_excerpt();
						wp_reset_postdata();
					}
				}
				
				return $return;
				break;
		}
		
		return do_shortcode(stripslashes($content));
	}
	
	function direct_post_permalink($id = null) {
		global $Html, $shortcode_post_language, $shortcode_post;
		
		$post_id = (empty($id)) ? $shortcode_post -> ID : $id;
		
		if (!empty($post_id)) {
			if ($permalink = get_permalink($post_id)) {
				if ($this -> language_do()) {
					$permalink = $this -> language_converturl($permalink, $shortcode_post_language);
					$permalink = $Html -> retainquery('lang=' . $shortcode_post_language, $permalink);
				}
				
				return $permalink;
			}
		}
		
		return false;
	}
	
	function excerpt_length($length = null) {
		$length = $this -> get_option('excerpt_length');		
		return $length;
	}
	
	function excerpt_more($more = null) {
		global $shortcode_post, $shortcode_post_language, $wpml_target;
		$excerpt_more = ($this -> language_do()) ? $this -> language_use($shortcode_post_language, $this -> language_join($this -> get_option('excerpt_more'))) : $this -> get_option('excerpt_more');
		
		global ${'newsletters_acolor'};
		if (!empty(${'newsletters_acolor'})) {
			$style = ' style="color:' . ${'newsletters_acolor'} . ';"';
		}
		
		$more = ' <a target="' . $wpml_target . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '"' . $style . '>' . __($excerpt_more) . '</a>';
		return $more;
	}
	
	function datestring($atts = array(), $content = null) {	
		$defaults = array('format' => "%d/%m/%Y");
		extract(shortcode_atts($defaults, $atts));
		$output = strftime($format, time());
		return $output;
	}
	
	function template($atts = array(), $content = null) {
		global $wpdb, $Template;
	
		$defaults = array('id' => false);
		extract(shortcode_atts($defaults, $atts));
		
		if (!empty($id)) {
			$templatequery = "SELECT * FROM " . $wpdb -> prefix . $Template -> table . " WHERE id = '" . $id . "' LIMIT 1";
			
			$query_hash = md5($templatequery);
			global ${'newsletters_query_' . $query_hash};
			if (!empty(${'newsletters_query_' . $query_hash})) {
				$template = ${'newsletters_query_' . $query_hash};
			} else {
				$template = $wpdb -> get_row($templatequery);
				${'newsletters_query_' . $query_hash} = $template;
			}
		
			if (!empty($template)) {
				$output = wpautop(__($template -> content));
			}
		}
		
		return $output;
	}
	
	function history($atts = array(), $content = null) {
		global $wpdb, $Db, $History, $HistoriesList;
	
		$defaults = array('number' => false, 'order' => "DESC", 'orderby' => "modified", 'list_id' => false, 'index' => true);
		extract(shortcode_atts($defaults, $atts));
		
		$listscondition = "";
		$l = 1;
		
		if (!empty($list_id)) {
			if ($mailinglists = explode(",", $list_id)) {
				$listscondition = " (";
				
				foreach ($mailinglists as $mailinglist_id) {
					$listscondition .= "" . $wpdb -> prefix . $HistoriesList -> table . ".list_id = '" . $mailinglist_id . "'";
					
					if (count($mailinglists) > $l) {
						$listscondition .= " OR ";	
					}
					
					$l++;
				}
				
				$listscondition .= ")";
			}
		} else {
			$listscondition = " 1 = 1";
		}
		
		$query = "SELECT DISTINCT " . $wpdb -> prefix . $HistoriesList -> table . ".history_id, " .
		$wpdb -> prefix . $History -> table . ".id, " .
		$wpdb -> prefix . $History -> table . ".message, " .
		$wpdb -> prefix . $History -> table . ".modified, " . 
		$wpdb -> prefix . $HistoriesList -> table . ".history_id, " . $wpdb -> prefix . $History -> table . ".subject FROM `" . $wpdb -> prefix . $HistoriesList -> table . "` LEFT JOIN `" . 
		$wpdb -> prefix . $History -> table . "` ON " . 
		$wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . $History -> table . ".id" .
		" WHERE" . $listscondition . " AND " . $wpdb -> prefix . $History -> table . ".sent > '0'" .
		" ORDER BY " . $wpdb -> prefix . $History -> table . "." . $orderby . " " . $order . "";
		if (!empty($number)) { $query .= " LIMIT " . $number . ""; }	
		
		$query_hash = md5($query);
		global ${'newsletters_query_' . $query_hash};
		if (!empty(${'newsletters_query_' . $query_hash})) {
			$emails = ${'newsletters_query_' . $query_hash};
		} else {
			$emails = $wpdb -> get_results($query);
			${'newsletters_query_' . $query_hash} = $emails;
		}
		
		if (!empty($emails)) {
			$content = $this -> render('history', array('emails' => $emails, 'history_index' => $index), false, 'default');
			$content = do_shortcode($content);
			return $content;
		}
		
		return $content;
	}
	
	function meta($atts = array(), $content = null) {
		$defaults = array('post_id' => false);
		extract(shortcode_atts($defaults, $atts));
		
		if (!empty($post_id)) {
			global $post_ID;
			$oldpostid = $post_ID;
			$post_ID = $post_id;
			
			ob_start();
			the_meta();
			$content = ob_get_clean();
			
			$post_ID = $oldpostid;
		}
	
		return $content;
	}
	
	function subscribe($atts = array(), $content = null) {
		global $Html, $Subscriber;
	
		if (is_feed()) return;
		
		$number = 'embed' . rand(999, 9999);
		$widget_id = 'newsletters-' . $number;
		$instance = $this -> widget_instance($number, $atts);
		
		$defaults = array(
			'list' 				=> 	"select", 
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
		
		$output = "";
		$output .= '<div id="' . $widget_id . '" class="' . $this -> pre . ' widget_newsletters">';
		$output .= '<div id="' . $widget_id . '-wrapper">';
		
		if (!empty($_GET['success'])) {
			$output .= '<p class="newsletters-acknowledgement">' . __($instance['acknowledgement']) . '</p>';
		} else {
			$output .= $this -> render('widget', array('action' => $action, 'errors' => $Subscriber -> errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), false, 'default');
		}
		
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
	
	/* The Mailer theme shortcodes */
	function themailer_address($atts = array(), $content = null) {
		return nl2br(stripslashes($this -> get_option('themailer_address')));
	}
	
	function themailer_facebookurl($atts = array(), $content = null) {
		return $this -> get_option('themailer_facebook');
	}
	
	function themailer_twitterurl($atts = array(), $content = null) {
		return $this -> get_option('themailer_twitter');
	}
	
	function themailer_rssurl($atts = array(), $content = null) {
		return $this -> get_option('themailer_rss');
	}
	
	/* Pro News theme shortcodes */
	function pronews_address($atts = array(), $content = null) {
		return nl2br(stripslashes($this -> get_option('pronews_address')));
	}
	
	function pronews_facebookurl($atts = array(), $content = null) {
		return $this -> get_option('pronews_facebook');
	}
	
	function pronews_twitterurl($atts = array(), $content = null) {
		return $this -> get_option('pronews_twitter');
	}
	
	function pronews_rssurl($atts = array(), $content = null) {
		return $this -> get_option('pronews_rss');
	}
	
	/* Lagoon theme shortcodes */
	function lagoon_address($atts = array(), $content = null) {
		return nl2br(stripslashes($this -> get_option('lagoon_address')));
	}
	
	function lagoon_facebookurl($atts = array(), $content = null) {
		return $this -> get_option('lagoon_facebook');
	}
	
	function lagoon_twitterurl($atts = array(), $content = null) {
		return $this -> get_option('lagoon_twitter');
	}
	
	function lagoon_rssurl($atts = array(), $content = null) {
		return $this -> get_option('lagoon_rss');
	}
}

?>