<div class="tribulant_plugin_tabs">
	<ul>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> extensions) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> extensions; ?>"><?php _e('Manage', $this -> plugin_name); ?></a><span class="border-right"></span></li>
        <li <?php echo (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> extensions_settings) ? 'class="active"' : ''; ?>><span class="border-left"></span><a href="?page=<?php echo $this -> sections -> extensions_settings; ?>"><?php _e('Settings', $this -> plugin_name); ?></a><span class="border-right"></span></li>
    </ul>
    <br style="display:block; width:100%; clear:both; visibility:hidden;" />
</div>