<?php

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

$root = __FILE__;
for ($i = 0; $i < 5; $i++) $root = dirname($root);
require_once($root . DS . 'wp-config.php');
require_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'admin.php');

header("Content-Type: text/css");

?>

<?php if (get_option('wpmlcustomcss') == "Y") : ?>
	<?php echo stripslashes(get_option('wpmlcustomcsscode')); ?>
<?php endif; ?>