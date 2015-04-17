<h3><?php _e('Additional Data', $this -> plugin_name); ?></h3>
<p><?php _e('Manage your subscriber profile data in the fields below.', $this -> plugin_name); ?></p>

<?php if (!empty($errors)) : ?>
	<?php /*$this -> render('error', array('errors' => $errors), true, 'default');*/ ?>
<?php endif; ?>
	
<?php if (!empty($success) && $success == true) : ?>
	<div class="ui-state-highlight ui-corner-all">
		<p><i class="fa fa-check"></i> <?php echo $successmessage; ?></p>
	</div>
<?php endif; ?>

<?php if (!empty($fields) && is_array($fields)) : ?>
	<form action="" method="post" onsubmit="jQuery.Watermark.HideAll(); wpmlmanagement_savefields(this); return false;" id="subscribersavefieldsform">
    	<input type="hidden" name="subscriber_id" value="<?php echo $subscriber -> id; ?>" />
    
		<?php foreach ($fields as $field) : ?>
            <?php $this -> render_field($field -> id, true, 'manage', true, true, false, false, $errors); ?>
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
            <input id="savefieldsbutton" class="<?php echo $this -> pre; ?>button ui-button-primary" type="submit" name="savefields" value="<?php _e('Save Profile', $this -> plugin_name); ?>" />
            <span id="savefieldsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
        </div>
    </form>
    
    <script type="text/javascript">
	jQuery(document).ready(function() { 
		if (jQuery.isFunction(jQuery.fn.button)) { jQuery('#savefieldsbutton').button(); } 
		if (jQuery.isFunction(jQuery.fn.select2)) { jQuery('.newsletters select').select2(); }
		
		jQuery('input:not(:button,:submit),textarea,select').focus(function(element) {
			jQuery(this).removeClass('<?php echo $this -> pre; ?>fielderror').nextAll('div.newsletters-field-error').slideUp();	
		});
	});
	</script>
<?php else : ?>
	<div class="ui-state-error ui-corner-all">
		<p><i class="fa fa-exclamation-triangle"></i> <?php _e('No custom fields are available at this time.', $this -> plugin_name); ?></p>
	</div>
<?php endif; ?>