<form action="" method="post">
	<input type="hidden" name="screenoptions" value="1" />

	<?php if (!empty($fields)) : ?>
    	<?php $curfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_fields')); ?>
    	<?php $curcustomfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_custom')); ?>
		<h5><?php _e('Show on screen', $this -> plugin_name); ?></h5>
        <div class="metabox-prefs">
        	<label><input <?php echo (!empty($curcustomfields) && in_array('gravatars', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="gravatars" id="custom_gravatars" /> <?php _e('Gravatars', $this -> plugin_name); ?></label>
        	<label><input <?php echo (!empty($curcustomfields) && in_array('mandatory', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="mandatory" id="custom_mandatory" /> <?php _e('Mandatory', $this -> plugin_name); ?></label>
        	<?php foreach ($fields as $field) : ?>
            	<label><input <?php echo (!empty($curfields) && in_array($field -> id, $curfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="fields[]" value="<?php echo $field -> id; ?>" id="fields_<?php echo $field -> id; ?>" /> <?php echo __($field -> title); ?></label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

	<input onclick="" type="submit" class="button" value="<?php _e('Apply', $this -> plugin_name); ?>" />
</form>