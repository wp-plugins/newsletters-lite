<?php

class wpmlAuthHelper extends wpMailPlugin {

	var $name = 'Auth';
	var $cookiename = 'subscriberauth';
	var $emailcookiename = 'subscriberemailauth';
	
	function logged_in() {
		global $wpdb, $Db, $Subscriber, $user_ID;
		
		if ($subscriberauth = $this -> read_cookie()) {
			$Db -> model = $Subscriber -> model;			
			if ($subscriber = $Db -> find(array('cookieauth' => $subscriberauth))) {			
				return $subscriber;	
			}
		} elseif ($user_ID) {
			$Db -> model = $Subscriber -> model;
			if ($subscriber = $Db -> find(array('user_id' => $user_ID))) {
				return $subscriber;
			}
		}
		
		return false;	
	}
	
	function read_cookie($create = false) {
		if (isset($_COOKIE[$this -> cookiename])) {
			return $_COOKIE[$this -> cookiename];
		}
		
		return false;
	}
	
	function read_emailcookie() {
		if (isset($_COOKIE[$this -> emailcookiename])) {
			return $_COOKIE[$this -> emailcookiename];
		}
		
		return false;
	}
	
	function write_db() {
		
	}
	
	function set_emailcookie($email = null, $days = "+30 days") {
		if (!empty($email)) {
			$this -> delete_cookie($this -> emailcookiename, $email);
			
			if (!headers_sent()) {
				setcookie($this -> emailcookiename, $email, strtotime($days), '/');
			} else {
				$this -> javascript_cookie($this -> emailcookiename, $email);	
			}
		}
		
		return false;
	}
	
	function set_cookie($value = null, $days = "+30 days") {
		if (is_feed()) {
			return false;	
		}
		
		$this -> delete_cookie($this -> cookiename, $value);
		
		if (!headers_sent()) {
			setcookie($this -> cookiename, $value, strtotime($days), '/');
		} else {
			$this -> javascript_cookie($this -> cookiename, $value);	
		}
			
		return true;
	}
	
	function delete_cookie($cookiename = null, $cookievalue = null) {
		if (false && !headers_sent() ) {
			setcookie($cookiename, $cookievalue, time() - 3600);
		} else {
			global $wpmljavascript;
			ob_start();
			
			?>
			
			<script type="text/javascript">
			document.cookie = "<?php echo $cookiename; ?>=<?php echo $value; ?>; expires=<?php echo date_i18n($this -> get_option('cookieformat'), time() - 3600); ?> UTC; path=/";
			</script>
			
			<?php	
			
			$newjavascript = ob_get_clean();
			$wpmljavascript .= $newjavascript;
			return $wpmljavascript;
		}
	}
	
	function javascript_cookie($cookiename = null, $value = null) {
		if (!empty($cookiename) && !empty($value)) {
			global $wpmljavascript;
			ob_start();
		
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				document.cookie = "<?php echo $cookiename; ?>=<?php echo $value; ?>; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("-30 days")); ?> UTC; path=/";
				document.cookie = "<?php echo $cookiename; ?>=<?php echo $value; ?>; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC; path=/";
			});
			</script>
			
			<?php	
			
			$newjavascript = ob_get_clean();
			$wpmljavascript .= $newjavascript;
			return $wpmljavascript;
		} 
		
		return false;
	}
	
	function gen_subscriberauth() {
		$subscriberauth = md5(time());	
		return $subscriberauth;	
	}
}

?>