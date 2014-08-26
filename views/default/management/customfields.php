<h3><?php _e('Additional Data', $this -> plugin_name); ?></h3>
<p><?php _e('Manage your subscriber profile data in the fields below.', $this -> plugin_name); ?></p>

<?php if (!empty($errors)) : ?>
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
<?php endif; ?>
	
<?php if (!empty($success) && $success == true) : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php echo $successmessage; ?></p>
<?php endif; ?>

<?php if (!empty($fields) && is_array($fields)) : ?>
	<form action="" method="post" onsubmit="jQuery.Watermark.HideAll(); wpmlmanagement_savefields(); return false;" id="subscribersavefieldsform">
    	<input type="hidden" name="subscriber_id" value="<?php echo $subscriber -> id; ?>" />
    
		<?php foreach ($fields as $field) : ?>
            <?php $this -> render_field($field -> id, true, 'manage'); ?>
        <?php endforeach; ?>
        
        <?php $managementformatchange = $this -> get_option('managementformatchange'); ?>
        <?php if (!empty($managementformatchange) && $managementformatchange == "Y") : ?>
	        <div class="newsletters-fieldholder format">
	        	<label for="format_html" class="wpmlcustomfield"><?php _e('Email Format:', $this -> plugin_name); ?></label>
	        	<label><input <?php echo ($subscriber -> format == "html") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="html" id="format_html" /> <?php _e('HTML (recommended)', $this -> plugin_name); ?></label>
	        	<label><input <?php echo ($subscriber -> format == "text") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="text" id="format_text" /> <?php _e('TEXT', $this -> plugin_name); ?></label>
	        </div>
	    <?php endif; ?>
        
        <div class="wpmlsubmitholder">
            <input class="<?php echo $this -> pre; ?>button" type="submit" name="savefields" value="<?php _e('Save Profile', $this -> plugin_name); ?>" id="savefields" />
            <span id="savefieldsloading" style="display:none;"><img src="<?php echo $this -> url(); ?>/views/default/img/loading.gif" alt="loading" /></span>
        </div>
    </form>
    
    <script type="text/javascript">jQuery(document).ready(function() { jQuery('#savefields').button(); });</script>
<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No custom fields are available at this time.', $this -> plugin_name); ?></p>
<?php endif; ?>