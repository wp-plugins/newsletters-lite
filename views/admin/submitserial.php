<script type="text/javascript">
var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>

<h3><?php _e('Serial Key', $this -> plugin_name); ?></h3>

<?php if (empty($success) || $success == false) : ?>
	<?php if (!$this -> ci_serial_valid()) : ?>
        <p style="width:400px;"><?php _e('You need to fill in a serial key to continue using the Newsletter plugin.', $this -> plugin_name); ?>
        <?php _e('Please obtain a serial key from the downloads section in your Tribulant Software account.', $this -> plugin_name); ?>
        <?php _e('Once in the downloads section, click the KEY icon to request a serial key.', $this -> plugin_name); ?>
        <a href="http://tribulant.com/downloads/" title="Tribulant Software Downloads" target="_blank"><?php _e('Downloads Section', $this -> plugin_name); ?></a></p>
    
        <div style="color:red;">
            <?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
        </div>
        
        <form onsubmit="wpml_submitserial(this); return false;" action="<?php echo home_url(); ?>/index.php?wpmlmethod=submitserial" method="post">
            <input type="text" class="widefat" style="width:400px;" name="serialkey" value="<?php echo esc_attr(stripslashes($_POST['serialkey'])); ?>" /><br/>
            <input type="button" class="button-secondary" name="close" onclick="jQuery.colorbox.close();" value="<?php _e('Cancel', $this -> plugin_name); ?>" />
            <input type="submit" class="button-primary" name="submit" value="<?php _e('Submit Serial Key', $this -> plugin_name); ?>" />
            <span style="display:none;" id="wpml_submitserial_loading"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" /></span>
        </form>        
    <?php else : ?>
        <p><?php _e('Serial Key:', $this -> plugin_name); ?> <strong><?php echo $this -> get_option('serialkey'); ?></strong></p>
        <p><?php _e('Your current serial is valid and working.', $this -> plugin_name); ?></p>
        <p>
        	<input type="button" onclick="jQuery.colorbox.close();" name="close" class="button-primary" value="<?php _e('Close', $this -> plugin_name); ?>" />
        	<input type="button" onclick="if (confirm('<?php _e('Are you sure you want to delete your serial key?', $this -> plugin_name); ?>')) { wpml_deleteserial(); } return false;" name="delete" class="button-secondary" value="<?php _e('Delete Serial', $this -> plugin_name); ?>" />
        	<span style="display:none;" id="wpml_submitserial_loading"><img src="<?php echo $this -> url(); ?>/images/loading.gif" alt="loading" /></span>
        </p>
    <?php endif; ?>
<?php else : ?>
    <p><?php _e('The serial key is valid and you can now continue using the Newsletter plugin. Thank you for your business and support!', $this -> plugin_name); ?></p>
    <p><input type="button" onclick="jQuery.colorbox.close(); parent.location = '<?php echo rtrim(get_admin_url(), '/'); ?>/admin.php?page=newsletters';" class="button-primary" name="close" value="<?php _e('Apply Serial and Close Window', $this -> plugin_name); ?>" /></p>
<?php endif; ?>