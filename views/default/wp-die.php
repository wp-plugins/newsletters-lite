<?php $oldcontent = $content; ?>
<?php $content = '<link rel="stylesheet" href="' . $this -> url() . '/css/' . $this -> plugin_name . '-die.css" type="text/css" media="screen" />'; ?>
<?php $content .= '<script type="text/javascript" src="' . $this -> url() . '/js/' . $this -> plugin_name . '.js"></script>'; ?>
<?php $content .= $oldcontent; ?>
<?php wp_die($content, $title); ?>