<?php

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