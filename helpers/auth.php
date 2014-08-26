<?php

class wpmlAuthHelper extends wpMailPlugin {

	var $name = 'Auth';
	var $cookiename = 'subscriberauth';
	var $emailcookiename = 'subscriberemailauth';
	
	function logged_in() {
		global $wpdb, $Db, $Subscriber, $user_ID;
		
		$Db -> model = $Subscriber -> model;
		if ($subscriberauth = $this -> read_cookie()) {
			$Db -> model = $Subscriber -> model;			
			if ($subscriber = $Db -> find(array('cookieauth' => $subscriberauth))) {			
				return $subscriber;	
			}
		} elseif ($user_ID && !empty($user_ID) && $subscriber = $Db -> find(array('user_id' => $user_ID))) {
			return $subscriber;
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
		if (is_feed()) {
			return false;
		}
	
		if (!empty($email)) {			
			if (!empty($_COOKIE[$this -> emailcookiename]) && $_COOKIE[$this -> emailcookiename]) {
				return true;
			}
			
			if (!headers_sent()) {
				setcookie($this -> emailcookiename, $email, strtotime($days), '/');
			} else {
				$this -> javascript_cookie($this -> emailcookiename, $email);	
			}
			
			$_COOKIE[$this -> emailcookiename] = $email;
		}
		
		return false;
	}
	
	function set_cookie($value = null, $days = "+30 days") {
		if (is_feed()) {
			return false;	
		}
		
		if (!empty($value)) {			
			if (!empty($_COOKIE[$this -> cookiename]) && $_COOKIE[$this -> cookiename] == $value) {
				return true;
			}
			
			if (!headers_sent()) {
				setcookie($this -> cookiename, $value, strtotime($days), '/');
			} else {
				$this -> javascript_cookie($this -> cookiename, $value);	
			}
			
			$_COOKIE[$this -> cookiename] = $value;
		}
			
		return true;
	}
	
	function delete_cookie($cookiename = null, $cookievalue = null) {
		if (!headers_sent() ) {
			setcookie($cookiename, $cookievalue, time() - 3600);
		} else {
			$this -> javascript_cookie($cookiename, $cookievalue, true);
		}
	}
	
	function javascript_cookie($cookiename = null, $value = null, $delete = false) {
		if (!empty($cookiename) && !empty($value)) {
			global $wpmljavascript;
			ob_start();
		
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php if (!empty($delete)) : ?>
					document.cookie = "<?php echo $cookiename; ?>=<?php echo $value; ?>; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("-30 days")); ?> UTC;";
				<?php else : ?>
					document.cookie = "<?php echo $cookiename; ?>=<?php echo $value; ?>; expires=<?php echo date_i18n($this -> get_option('cookieformat'), strtotime("+30 days")); ?> UTC;";
				<?php endif; ?>
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
		$subscriberauth = md5(microtime());	
		return $subscriberauth;	
	}
}

?>