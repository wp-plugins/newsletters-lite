<?php
	
$defaultscripts = array(
	/*'jquery-ui-tabs'		=>	array(
		'name'					=>	__('jQuery UI Tabs', $this -> plugin_name),
		'url'					=>	false,
		'version'				=>	false,
		'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
		'footer'				=>	false,
	),
	'jquery-ui-button'		=>	array(
		'name'					=>	__('jQuery UI Button', $this -> plugin_name),
		'url'					=>	false,
		'version'				=>	false,
		'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
		'footer'				=>	false,
	),*/
	/*'jquery-ui-dialog'		=>	array(
		'name'					=>	__('jQuery UI Dialog', $this -> plugin_name),
		'url'					=>	false,
		'version'				=>	false,
		'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
		'footer'				=>	false,
	),*/
	/*'jquery-ui-datepicker'	=>	array(
		'name'					=>	__('jQuery UI Datepicker', $this -> plugin_name),
		'url'					=>	false,
		'version'				=>	false,
		'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
		'footer'				=>	false,
	),*/
	/*'jquery-cookie'			=>	array(
		'name'					=>	__('jQuery Cookie', $this -> plugin_name),
		'url'					=>	$this -> render_url('js/jquery.cookie.js', 'admin', false),
		'version'				=>	'1.4.1',
		'deps'					=>	array('jquery'),
		'footer'				=>	false,
	),*/
	'jquery-uploadify'		=>	array(
		'name'					=>	__('Uploadify - File Uploader', $this -> plugin_name),
		'url'					=>	$this -> render_url('js/jquery.uploadify.js', 'admin', false),
		'version'				=>	'2.2',
		'deps'					=>	array('jquery'),
		'footer'				=>	false,
	),
	'select2'				=>	array(
		'name'					=>	__('Select2 - Drop Downs', $this -> plugin_name),
		//'url'					=>	$this -> render_url('js/select2.js', 'admin', false),
		'url'					=>	'//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js',
		'version'				=>	'4.0.0',
		'deps'					=>	array('jquery'),
		'footer'				=>	false,
	)
);

$defaultscripts = apply_filters('newsletters_default_scripts', $defaultscripts);

$validation_rules = array(
	'notempty'				=>	array(
		'title'					=>	__('Not Empty', $this -> plugin_name),
		'regex'					=>	"",
	),
	'alphanumeric'			=>	array(
		'title'					=>	__('Alpha-Numeric', $this -> plugin_name),
		'regex'					=>	"/^[a-zA-Z0-9]+$/",
	),
	'alphabetic'			=>	array(
		'title'					=>	__('Alphabetic', $this -> plugin_name),
		'regex'					=>	"/^[a-zA-Z]+$/",
	),
	'numeric'				=>	array(
		'title'					=>	__('Numeric', $this -> plugin_name),
		'regex'					=>	"/^[0-9]+$/",
	),
	'email'					=>	array(
		'title'					=>	__('Email', $this -> plugin_name),
		'regex'					=>	"/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/",
	),
	'ipaddress'				=>	array(
		'title'					=>	__('IP Address', $this -> plugin_name),
		'regex'					=>	"/^((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))*$/",
	),
	'urls'					=>	array(
		'title'					=>	__('URLs', $this -> plugin_name),
		'regex'					=>	"/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/",
	),
);

$validation_rules = apply_filters('newsletters_validation_rules', $validation_rules);
$validation_rules['custom'] = array('title' => __('CUSTOM REGEX', $this -> plugin_name));

$wordpress_reserved_terms = array(
	'attachment',
	'attachment_id',
	'author',
	'author_name',
	'calendar',
	'cat',
	'category',
	'category__and',
	'category__in',
	'category__not_in',
	'category_name',
	'comments_per_page',
	'comments_popup',
	'customize_messenger_channel',
	'customized',
	'cpage',
	'day',
	'debug',
	'error',
	'exact',
	'feed',
	'hour',
	'link_category',
	'm',
	'minute',
	'monthnum',
	'more',
	'name',
	'nav_menu',
	'nonce',
	'nopaging',
	'offset',
	'order',
	'orderby',
	'p',
	'page',
	'page_id',
	'paged',
	'pagename',
	'pb',
	'perm',
	'post',
	'post__in',
	'post__not_in',
	'post_format',
	'post_mime_type',
	'post_status',
	'post_tag',
	'post_type',
	'posts',
	'posts_per_archive_page',
	'posts_per_page',
	'preview',
	'robots',
	's',
	'search',
	'second',
	'sentence',
	'showposts',
	'static',
	'subpost',
	'subpost_id',
	'tag',
	'tag__and',
	'tag__in',
	'tag__not_in',
	'tag_id',
	'tag_slug__and',
	'tag_slug__in',
	'taxonomy',
	'tb',
	'term',
	'theme',
	'type',
	'w',
	'withcomments',
	'withoutcomments',
	'year',
);

$wordpress_reserved_terms = apply_filters('newsletters_wordpress_reserved_terms', $wordpress_reserved_terms);

?>