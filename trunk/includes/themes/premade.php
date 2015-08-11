<?php

global $wpdb, $Db, $Theme;

$themes = array(
	'blueretro'			=>	array('title' => __('Blue Retro', $this -> plugin_name), 'name' => "blueretro"),
	'getaway'			=>	array('title' => __('Getaway', $this -> plugin_name), 'name' => "getaway"),
	'nightlife'			=>	array('title' => __('Night Life', $this -> plugin_name), 'name' => "nightlife"),
	'paperphase'		=>	array('title' => __('Paper Phase', $this -> plugin_name), 'name' => "paperphase"),
	'redray'			=>	array('title' => __('Red Ray', $this -> plugin_name), 'name' => "redray"),
	'simplyelegant'		=>	array('title' => __('Simply Elegant', $this -> plugin_name), 'name' => "simplyelegant"),
	'snazzy'			=>	array('title' => __('Snazzy', $this -> plugin_name), 'name' => "snazzy"),
	'pronews'			=>	array('title' => __('Pro News', $this -> plugin_name), 'name' => "pronews"),
	'lagoon'			=>	array('title' => __('Lagoon', $this -> plugin_name), 'name' => "lagoon"),
	'themailer'			=>	array('title' => __('The Mailer', $this -> plugin_name), 'name' => "themailer"),
);

$themespath = $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS;

foreach ($themes as $theme) {
	$themequery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . " WHERE name = '" . $theme['name'] . "' LIMIT 1";

	if (!$wpdb -> get_row($themequery)) {
		$themefile = $themespath . $theme['name'] . DS . 'index.html';
		
		if (file_exists($themefile)) {
			if ($fh = fopen($themefile, "r")) {
				$content = "";
				
				while (!feof($fh)) {
					$content .= fread($fh, 1024);
				}
				
				fclose($fh);
			}
			
			$newcontent = "";
			ob_start();
			eval('?>' . stripslashes($content) . '<?php ');
			$newcontent = ob_get_clean();
			
			$theme_data = array(
				'title'				=>	$theme['title'],
				'name'				=>	$theme['name'],
				'premade'			=>	"Y",
				'paste'				=>	$newcontent,
				'type'				=>	"paste",
				'def'				=>	"N",
			);
			
			$Db -> model = $Theme -> model;
			$Db -> save($theme_data);
			
			switch ($theme['name']) {
				case 'pronews'				:
					$pronews_address = "123 My Street" . "\n" . "My City" . "\n" . "My State" . "\n" . "Website: www.domain.com" . "\n" . "Phone: 0123456789";
					$this -> add_option('pronews_address', $pronews_address);
					$pronews_facebook = "http://facebook.com";
					$this -> add_option('pronews_facebook', $pronews_facebook);
					$pronews_twitter = "http://twitter.com";
					$this -> add_option('pronews_twitter', $pronews_twitter);
					$pronews_rss = get_bloginfo('rss2_url');
					$this -> add_option('pronews_rss', $pronews_rss);
					break;
				case 'themailer'			:
					$themailer_address = "123 My Street" . "\n" . "My City" . "\n" . "My State" . "\n" . "Website: www.domain.com" . "\n" . "Phone: 0123456789";
					$this -> add_option('themailer_address', $themailer_address);
					$themailer_facebook = "http://facebook.com";
					$this -> add_option('themailer_facebook', $themailer_facebook);
					$themailer_twitter = "http://twitter.com";
					$this -> add_option('themailer_twitter', $themailer_twitter);
					$themailer_rss = get_bloginfo('rss2_url');
					$this -> add_option('themailer_rss', $themailer_rss);
					break;
				case 'lagoon'				:
					$lagoon_address = "123 My Street" . "\n" . "My City" . "\n" . "My State" . "\n" . "Website: www.domain.com" . "\n" . "Phone: 0123456789";
					$this -> add_option('lagoon_address', $lagoon_address);
					$lagoon_facebook = "http://facebook.com";
					$this -> add_option('lagoon_facebook', $lagoon_facebook);
					$lagoon_twitter = "http://twitter.com";
					$this -> add_option('lagoon_twitter', $lagoon_twitter);
					$lagoon_rss = get_bloginfo('rss2_url');
					$this -> add_option('lagoon_rss', $lagoon_rss);
					break;
			}
		}
	}
}

?>