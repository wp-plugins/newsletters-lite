<?php

// All extensions are listed in this file and can be seen under Newsletters > Extensions in the plugin.

global $extensions;

$extensions = array(
	'embedimages'		=>	array(
		'name' 				=> 	__('Embedded Images', $this -> plugin_name),
		'description'		=>	__("Attach/embed images into emails instead of loading the remotely to prevent image disabling and immediate loading of newsletters.", $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/6/embedded-images",
		'slug'				=>	'embedimages',
		'plugin_name'		=>	'newsletters-embedimages',
		'plugin_file'		=>	'embedimages.php',
	),
	'formidable'		=>	array(
		'name' 				=> 	__('Formidable Subscribers', $this -> plugin_name),
		'description'		=>	__("Subscribe users from Formidable forms entries into the Newsletter plugin.", $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/16/formidable-subscribers",
		'slug'				=>	'formidable',
		'plugin_name'		=>	'newsletters-formidable',
		'plugin_file'		=>	'formidable.php',
	),
	'gforms'			=>	array(
		'name' 				=> 	__('Gravity Forms', $this -> plugin_name),
		'description'		=>	__("Capture subscribers through Gravity Forms into the Newsletter plugin.", $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/17/gravity-forms",
		'slug'				=>	'gforms',
		'plugin_name'		=>	'newsletters-gforms',
		'plugin_file'		=>	'gforms.php',
	),
	'mscontrol'			=>	array(
		'name'				=>	__('Total MS Control', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/26/total-ms-control",
		'description'		=>	__("Total control over your Newsletter plugin installation for WordPress multi-site.", $this -> plugin_name),
		'slug'				=>	'mscontrol',
		'plugin_name'		=>	'newsletters-mscontrol',
		'plugin_file'		=>	'mscontrol.php',
	),
	'cf7'				=>	array(
		'name'				=>	__('Contact Form 7 Subscribers', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/28/contact-form7-subscribers",
		'description'		=>	__('Capture newsletter subscribers into the Newsletter plugin from your Contact Form 7 plugin forms.', $this -> plugin_name),
		'slug'				=>	'cf7',
		'plugin_name'		=>	'newsletters-cf7',
		'plugin_file'		=>	'cf7.php',
	),
	'dap'				=>	array(
		'name'				=>	__('Digital Access Pass', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/43/digital-access-pass",
		'description'		=>	sprintf(__("Capture email/newsletters subscribes from %sDigital Access Pass%s.", $this -> plugin_name), '<a href="http://digitalaccesspass.com" target="_blank">', '</a>'),
		'slug'				=>	'dap',
		'plugin_name'		=>	'newsletters-dap',
		'plugin_file'		=>	'dap.php',
	),
	'newsletters_ga'	=>	array(
		'name'				=>	__('Google Analytics', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/",
		'description'		=>	sprintf(__("Google Analytics link tracking for the %s.", $this -> plugin_name), '<a href="http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">' . __('Newsletter plugin', $this -> plugin_name) . '</a>'),
		'slug'				=>	'newsletters_ga',
		'plugin_name'		=>	'newsletters-ga',
		'plugin_file'		=>	'ga.php',
	),
	'wpemember'			=>	array(
		'name'				=>	__('WP eMember Subscribers', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/31/wp-emember-subscribers",
		'description'		=>	__('Capture subscribers from WP eMember into the Newsletter plugin.', $this -> plugin_name),
		'slug'				=>	'wpemember',
		'plugin_name'		=>	'newsletters-wpemember',
		'plugin_file'		=>	'wpemember.php',
	),
	's2member'			=>	array(
		'name'				=>	__('s2Member Subscribers', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/32/s2member-subscribers",
		'description'		=>	__('Capture subscribers from s2Member into the Newsletter plugin.', $this -> plugin_name),
		'slug'				=>	's2member',
		'plugin_name'		=>	'newsletters-s2member',
		'plugin_file'		=>	's2member.php',
	),
	'sendtofriend' 		=> array(
		'name'				=>	__('Send to Friend', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/",
		'description'		=>	sprintf(__("Let subscribers suggest newsletters to friends for the %s.", $this -> plugin_name), '<a href="http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">' . __('Newsletter plugin', $this -> plugin_name) . '</a>'),
		'slug'				=>	'sendtofriend',
		'plugin_name'		=>	'newsletters-sendtofriend',
		'plugin_file'		=>	'sendtofriend.php',
	),
	'control'			=>	array(
		'name'				=>	__('Total Control', $this -> plugin_name),
		'link'				=>	"http://tribulant.com/extensions/view/36/total-control",
		'description'		=>	__('Total control over your Newsletter plugin for WordPress.', $this -> plugin_name),
		'slug'				=>	'control',
		'plugin_name'		=>	'newsletters-control',
		'plugin_file'		=>	'control.php',
	),
	'newsletters_woocommerce'	=>	array(
		'name'			=>	__('WooCommerce Subscribers', $this -> plugin_name),
		'link'			=>	"http://tribulant.com/extensions/view/42/woocommerce-subscribers",
		'description'	=>	sprintf(__("Capture email subscribers from the WooCommerce plugin to the %sNewsletter plugin%s.", $this -> plugin_name), '<a href="http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">', '</a>'),
		'slug'			=>	'woocommerce',
		'plugin_name'	=>	'newsletters-woocommerce',
		'plugin_file'	=>	'woocommerce.php',
	),			
);

$titles = array();
foreach ($extensions as $ext) {
	$titles[] = $ext['name'];
}

array_multisort($titles, SORT_ASC, $extensions);

?>