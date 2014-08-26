<?php

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

$root = __FILE__;
for ($i = 0; $i < 6; $i++) $root = dirname($root);
for ($i = 0; $i < 5; $i++) $rootup = dirname($root);

if (file_exists($root . DS . 'wp-config.php')) {
	require_once($root . DS . 'wp-config.php');
} else {
	require_once($rootup . DS . 'wp-config.php');
}

// Define a destination
$upload_dir = wp_upload_dir();
$targetFolder = $upload_dir['basedir'] . DS . 'wp-mailinglist' . DS . 'uploadify';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $targetFolder;
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$targetFileName = time() . '.' . $fileParts['extension'];
	$targetFile = rtrim($targetPath, '/') . '/' . $targetFileName;
    $dangerFileTypes = array('php', 'cgi', 'asp', 'aspx', 'pl', 'js', 'java', 'class', 'shtml', 'cfm', 'cfml');

    if (!in_array($fileParts['extension'], $dangerFileTypes)) {
		if (move_uploaded_file($tempFile, $targetFile)) {
			echo $targetFileName;
		} else {
			echo __('File could not be moved from tmp', "wp-mailinglist");
		}
	} else {
		echo __('Invalid file type', "wp-mailinglist");
	}
} else {
	echo __('No file data was posted', "wp-mailinglist");
}

?>