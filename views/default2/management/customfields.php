<?php
	
global $newsletters_is_management;
$newsletters_is_management = true;	
	
?>

<h3><?php _e('Additional Data', $this -> plugin_name); ?></h3>
<p><?php _e('Manage your subscriber profile data in the fields below.', $this -> plugin_name); ?></p>

<?php if (!empty($errors)) : ?>
	<div class="alert alert-danger">
		<p><i class="fa fa-exclamation-triangle"></i> <?php _e('Profile could not be saved, see errors below.', $this -> plugin_name); ?></p>
	</div>
<?php endif; ?>
	
<?php if (!empty($success) && $success == true) : ?>
	<div class="alert alert-success">
		<p><i class="fa fa-check"></i> <?php echo $successmessage; ?></p>
	</div>
<?php endif; ?>

<?php if (!empty($fields) && is_array($fields)) : ?>
	<form action="" method="post" onsubmit="wpmlmanagement_savefields(this); return false;" id="subscribersavefieldsform">
    	<input type="hidden" name="subscriber_id" value="<?php echo $subscriber -> id; ?>" />
    
		<?php foreach ($fields as $field) : ?>
            <?php $this -> render_field($field -> id, true, 'manage', true, true, false, false, $errors); ?>
        <?php endforeach; ?>
        
        <?php $managementformatchange = $this -> get_option('managementformatchange'); ?>
        <?php if (!empty($managementformatchange) && $managementformatchange == "Y") : ?>
	        <div class="newsletters-fieldholder format">
		        <div class="form-group">
		        	<label for="format_html" class="control-label wpmlcustomfield"><?php _e('Email Format:', $this -> plugin_name); ?></label>
		        	<div class="radio">
		        		<label><input <?php echo ($subscriber -> format == "html") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="html" id="format_html" /> <?php _e('HTML (recommended)', $this -> plugin_name); ?></label>
						<label><input <?php echo ($subscriber -> format == "text") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="text" id="format_text" /> <?php _e('TEXT', $this -> plugin_name); ?></label>
		        	</div>
		        </div>
	        </div>
	    <?php endif; ?>
	    
	    <div class="clearfix"></div>
        
        <div id="<?php echo $widget_id; ?>-submit" class="newsletters-fieldholder newsletters_submit">
			<div class="form-group">
		        <div class="wpmlsubmitholder">
		            <input id="savefieldsbutton" class="<?php echo $this -> pre; ?>button btn btn-primary" type="submit" name="savefields" value="<?php _e('Save Profile', $this -> plugin_name); ?>" />
		            <span id="savefieldsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
		        </div>
			</div>
        </div>
    </form>
    
    <script type="text/javascript">jQuery(document).ready(function() { if (jQuery.isFunction(jQuery.fn.select2)) { jQuery('.newsletters select').select2(); } jQuery('input:not(:button,:submit),textarea,select').focus(function(element) { jQuery(this).removeClass('<?php echo $this -> pre; ?>fielderror').nextAll('div.newsletters-field-error').slideUp(); }); });</script>
<?php else : ?>
	<div class="alert alert-danger">
		<p><i class="fa fa-exclamation-triangle"></i> <?php _e('No custom fields are available at this time.', $this -> plugin_name); ?></p>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() { jQuery('.newsletters-management .newsletters-fieldholder, .entry-content .newsletters-fieldholder, .entry .newsletters-fieldholder').addClass('col-md-6'); });
</script>