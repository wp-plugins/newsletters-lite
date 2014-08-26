<?php $update_icon = ($this -> has_update()) ? ' <span class="update-plugins count-1"><span class="update-count">1</span></span>' : ''; ?>
<div class="tribulant_plugin_tabs">
	<ul>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> settings) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> settings; ?>"><?php _e('General', $this -> plugin_name); ?></a><span class="border-right"></span></li>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> settings_subscribers) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> settings_subscribers; ?>"><?php _e('Subscribers', $this -> plugin_name); ?></a><span class="border-right"></span></li>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> settings_templates) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> settings_templates; ?>"><?php _e('Email Templates', $this -> plugin_name); ?></a><span class="border-right"></span></li>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> settings_system) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> settings_system; ?>"><?php _e('System', $this -> plugin_name); ?></a><span class="border-right"></span></li>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> settings_tasks) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> settings_tasks; ?>"><?php _e('Scheduled Tasks', $this -> plugin_name); ?></a><span class="border-right"></span></li>
    </ul>
    <br style="display:block; width:100%; clear:both; visibility:hidden;" />
</div>