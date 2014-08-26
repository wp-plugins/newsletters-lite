<!-- TEXT Version for Multipart Emails -->

<p class="howto">
	<?php _e('By default, the TEXT version of multipart emails is automatically generated.', $this -> plugin_name); ?><br/>
	<?php _e('You may override that and specify your own TEXT version below.', $this -> plugin_name); ?>
</p>

<p>
	<label>
		<input onclick="if (jQuery(this).is(':checked')) { jQuery('#multimime_div').show(); } else { jQuery('#multimime_div').hide(); }" <?php echo (!empty($_POST['customtexton'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="customtexton" value="1" id="customtexton" />
		<?php _e('Specify a custom TEXT version of this newsletter', $this -> plugin_name); ?>
	</label>
</p>

<div id="multimime_div" style="display:<?php echo (!empty($_POST['customtext'])) ? 'block' : 'none'; ?>;">
	<textarea name="customtext" id="customtext" rows="6" cols="100%" class="widefat"><?php echo esc_attr(strip_tags(stripslashes($_POST['customtext']))); ?></textarea>
	<span class="howto"><?php _e('Specify the TEXT version of this multipart email. Only plain TEXT, no HTML is allowed.', $this -> plugin_name); ?></span>
</div>