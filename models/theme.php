<?php

class wpmlTheme extends wpMailPlugin {

	var $name = 'wpmlTheme';
	var $model = 'Theme';
	var $controller = 'themes';
	var $table_name = 'wpmlthemes';
	var $recursive = true;
	
	var $fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'title'			=>	"VARCHAR(150) NOT NULL DEFAULT ''",
		'name'			=>	"VARCHAR(50) NOT NULL DEFAULT ''",
		'premade'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'type'			=>	"ENUM('upload','paste') NOT NULL DEFAULT 'paste'",
		'content'		=>	"LONGTEXT NOT NULL",
		'def'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'defsystem'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'acolor'		=>	"VARCHAR(20) NOT NULL DEFAULT '#333333'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`def`), INDEX(`defsystem`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'title'			=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
		'name'			=>	array("VARCHAR(50)", "NOT NULL DEFAULT ''"),
		'premade'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'type'			=>	array("ENUM('upload','paste')", "NOT NULL DEFAULT 'paste'"),
		'content'		=>	array("LONGTEXT", "NOT NULL"),
		'def'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'defsystem'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'acolor'		=>	array("VARCHAR(20)", "NOT NULL DEFAULT '#333333'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`def`), INDEX(`defsystem`)"					   
	);
	
	var $indexes = array('def', 'defsystem');
	
	function wpmlTheme($data = array()) {
		global $Db;
	
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = $val;
			}
		}
		
		$Db -> model = $this -> model;
		return;
	}
	
	function defaults() {
		global $Html;
		
		$defaults = array(
			'created'			=>	$Html -> gen_date(),
			'modified'			=>	$Html -> gen_date(),
		);
		
		return $defaults;
	}
	
	function validate($data = array()) {
		global $Html;
		$this -> errors = array();
		
		$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
		$r = wp_parse_args($data, $defaults);
		extract($r, EXTR_SKIP);
		
		if (!empty($data)) {
			if (empty($title)) { $this -> errors['title'] = __('Please fill in a title', $this -> plugin_name); }
			else {
				if (!empty($id) && empty($name)) {
					$this -> data -> name = $Html -> sanitize($title, '');
				}
			}
			
			if (empty($type)) { $this -> errors['type'] = __('Please choose a submission type', $this -> plugin_name); }
			else {				
				switch ($type) {
					case 'upload'		:
						if ($_FILES['upload']['error'] > 0) {
							$this -> errors['upload'] = $Html -> file_upload_error($_FILES['upload']['error']);	
						} else {							
							if (empty($_FILES['upload']['name'])) { $this -> errors['upload'] = __('Please choose an HTML file for uploading', $this -> plugin_name); }
							elseif (!is_uploaded_file($_FILES['upload']['tmp_name'])) { $this -> errors['upload'] = __('HTML file could not be uploaded', $this -> plugin_name); }
							elseif ($_FILES['upload']['type'] != "text/html") { $this -> errors['upload'] = __('This is not a valid HTML file. Ensure that it has a .html extension', $this -> plugin_name); }
							else {
								@chmod($_FILES['upload']['tmp_name'], 0777);
								
								if ($fh = fopen($_FILES['upload']['tmp_name'], "r")) {
									$html = "";
									
									while (!feof($fh)) {
										$html .= fread($fh, 1024);
									}
									
									fclose($fh);
									$this -> data -> content = $this -> data -> paste = $html;
									$this -> data -> type = "paste";
								} else {
									$this -> errors['upload'] = __('HTML file could not be opened for reading. Please check its permissions', $this -> plugin_name);	
								}
							}
						}
						break;
					default				:
						if (empty($paste)) { $this -> errors['paste'] = __('Please paste HTML code for your template', $this -> plugin_name); }
						else {
							$this -> data -> content = stripslashes($paste);	
						}
						break;
				}
				
				if (!empty($this -> data -> content)) {					
					if (!preg_match("/\[wpmlcontent\]/si", $this -> data -> content)) {
						//$this -> errors['paste'] = 	__('Your template does not have the [wpmlcontent] tag', $this -> plugin_name);
					}
				}
			}
		} else {
			$this -> errors[] = __('No data was posted', $this -> plugin_name);
		}
		
		if (empty($this -> errors)) {
			if (!empty($this -> data -> inlinestyles) && $this -> data -> inlinestyles == "Y") {				
				$url = "http://premailer.dialect.ca/api/0.1/documents";
				
				$postfields = array(
					'html'						=>	$this -> data -> content,
					'adapter'					=>	'hpricot', //nokogiri
					'preserve_styles'			=>	true,
					'remove_ids'				=>	false,
					'remove_classes'			=>	false,
					'remove_comments'			=>	false,
				);
				
				if (function_exists('curl_init') && $ch = curl_init($url)) {
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);	
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, false);
					$result = curl_exec($ch);
					//$this -> data -> content = trim(html_entity_decode(urldecode($result)));
					curl_close($ch);
					
					$result = json_decode($result);
					$remote = wp_remote_get(trim($result -> documents -> html));
					$this -> data -> content = trim(html_entity_decode(urldecode($remote['body'])));
				}
			}
			
			if (!empty($this -> data -> imgprependurl)) {
				if (preg_match_all('/<img.*?>/', $this -> data -> content, $matches)) {
					if (!empty($matches[0])) {
						foreach ($matches[0] as $img) {
					        if (preg_match('/src="(.*?)"/', $img, $m)) {
						    	if (!empty($m)) {
						    		$this -> data -> content = str_replace($m[0], 'src="' . rtrim($this -> data -> imgprependurl, '/') . '/' . $m[1] . '"', $this -> data -> content); 
						    	}
					        }
					    }
					}
				}
			}
		}
		
		return $this -> errors;
	}
	
	function select() {
		global $Db;
		$Db -> model = $this -> model;
		$themeselect = array();
		
		if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) {
			foreach ($themes as $theme) {
				$themeselect[$theme -> id] = __($theme -> title);	
			}
		}
		
		return apply_filters($this -> pre . '_themes_select', $themeselect);
	}
}

?>