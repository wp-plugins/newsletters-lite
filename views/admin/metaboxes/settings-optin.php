<!-- Default Subscription Form Settings -->
<?php 

$embed = $this -> get_option('embed'); 
$captcha_type = $this -> get_option('captcha_type');
$rr_active = (empty($captcha_type) || $captcha_type == "none") ? false : true;

?>

<p>
	<?php _e('These settings will affect post/page embedded and hardcoded subscribe forms.', $this -> plugin_name); ?>
</p>

<?php if ($this -> is_plugin_active('qtranslate')) : ?>
	<?php global $q_config; ?>
    <?php 
    
    $el = qtrans_getSortedLanguages(); 
    
    if (!empty($embed)) {
	    foreach ($embed as $ekey => $eval) {
		    $embed[$ekey] = qtrans_split($eval);
	    }
    }
    
    ?>
    
    <?php if (!empty($el) && is_array($el)) : ?>
    	<div id="languagetabs">
        	<ul>
				<?php $tabnumber = 1; ?>
                <?php foreach ($el as $language) : ?>
                 	<li><a href="#languagetab<?php echo $tabnumber; ?>"><img src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>   
                    <?php $tabnumber++; ?>
                <?php endforeach; ?>
            </ul>
            
            <?php $tabnumber = 1; ?>
            <?php foreach ($el as $language) : ?>
            	<div id="languagetab<?php echo $tabnumber; ?>">
                	<table class="form-table">
                    	<tbody>
                        	<tr>
                                <th><label for="<?php echo $this -> pre; ?>embed_acknowledgement_<?php echo $language; ?>"><?php _e('Acknowledgement', $this -> plugin_name); ?></label></th>
                                <td><input type="text" class="widefat" id="<?php echo $this -> pre; ?>embed_acknowledgement_<?php echo $language; ?>" name="embed[acknowledgement][<?php echo $language; ?>]" value="<?php echo $embed['acknowledgement'][$language]; ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo $this -> pre; ?>embed_ajax_<?php echo $language; ?>"><?php _e('Ajax Features', $this -> plugin_name); ?></label></th>
                                <td>
                                    <label><input id="<?php echo $this -> pre; ?>embed_ajax_<?php echo $language; ?>" <?php echo ($embed['ajax'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax][<?php echo $language; ?>]" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                                    <label><input <?php echo ($embed['ajax'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax][<?php echo $language; ?>]" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo $this -> pre; ?>embed_button_<?php echo $language; ?>"><?php _e('Button Text', $this -> plugin_name); ?></label></th>
                                <td><input type="text" class="widefat" id="<?php echo $this -> pre; ?>embed_button_<?php echo $language; ?>" name="embed[button][<?php echo $language; ?>]" value="<?php echo $embed['button'][$language]; ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="embed_scrollY_<?php echo $language; ?>"><?php _e('Scroll to Subscription Form', $this -> plugin_name); ?></label></th>
                                <td>
                                    <label><input <?php echo ($embed['scroll'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll][<?php echo $language; ?>]" value="Y" id="embed_scrollY_<?php echo $language; ?>" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                                    <label><input <?php echo ($embed['scroll'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll][<?php echo $language; ?>]" value="N" id="embed_scrollN_<?php echo $language; ?>" /> <?php _e('No', $this -> plugin_name); ?></label>
                                    <span class="howto"><?php _e('Should the page scroll to focus on the subscription form?', $this -> plugin_name); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="captchaN_<?php echo $language; ?>"><?php _e('Use Captcha for Form', $this -> plugin_name); ?></label></th>
                                <td>
                                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo ($embed['captcha'][$language] == "Y") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha][<?php echo $language; ?>]" value="Y" id="captchaY_<?php echo $language; ?>" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo (empty($embed['captcha'][$language]) || $embed['captcha'][$language] == "N") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha][<?php echo $language; ?>]" value="N" id="captchaN_<?php echo $language; ?>" /> <?php _e('No', $this -> plugin_name); ?></label>
                                    <?php if (!$rr_active) : ?>
                                        <br/><span class="<?php echo $this -> pre; ?>error"><?php _e('Please configure a security captcha under Newsletters > Configuration > System > Captcha in order to use this.', $this -> plugin_name); ?></span>
                                        <input type="hidden" name="captcha" value="N" />
                                    <?php endif; ?>
                                    <span class="howto"><?php _e('Requires captcha/turing image input upon subscribing.', $this -> plugin_name); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php $tabnumber++; ?>
            <?php endforeach; ?>
        </div>
        
        <script type="text/javascript">
		jQuery(document).ready(function(e) {
            jQuery('#languagetabs').tabs();
        });
		</script>
    <?php else : ?>
    	<p class="<?php echo $this -> pre; ?>error"><?php _e('No qTranslate languages have been defined.', $this -> plugin_name); ?></p>
    <?php endif; ?>
<?php else : ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="<?php echo $this -> pre; ?>embed_acknowledgement"><?php _e('Acknowledgement', $this -> plugin_name); ?></label></th>
                <td>
                	<input type="text" class="widefat" id="<?php echo $this -> pre; ?>embed_acknowledgement" name="embed[acknowledgement]" value="<?php echo $embed['acknowledgement']; ?>" />
                	<span class="howto"><?php _e('Acknowledgement message to show after a successful subscribe.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $this -> pre; ?>embed_subscribeagain"><?php _e('Subscribe Again Link', $this -> plugin_name); ?></label></th>
                <td>
                    <label><input id="<?php echo $this -> pre; ?>embed_subscribeagain" <?php echo (empty($embed['subscribeagain']) || $embed['subscribeagain'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[subscribeagain]" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input <?php echo (!empty($embed['subscribeagain']) && $embed['subscribeagain'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[subscribeagain]" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
                    <span class="howto"><?php _e('Display a "subscribe again" link on success. useful for forms with multiple mailing lists.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $this -> pre; ?>embed_ajax"><?php _e('Ajax Features', $this -> plugin_name); ?></label></th>
                <td>
                    <label><input id="<?php echo $this -> pre; ?>embed_ajax" <?php echo ($embed['ajax'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax]" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input <?php echo ($embed['ajax'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax]" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo $this -> pre; ?>embed_button"><?php _e('Button Text', $this -> plugin_name); ?></label></th>
                <td><input type="text" class="widefat" id="<?php echo $this -> pre; ?>embed_button" name="embed[button]" value="<?php echo $embed['button']; ?>" /></td>
            </tr>
            <tr>
                <th><label for="embed_scrollY"><?php _e('Scroll to Subscription Form', $this -> plugin_name); ?></label></th>
                <td>
                    <label><input <?php echo ($embed['scroll'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll]" value="Y" id="embed_scrollY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input <?php echo ($embed['scroll'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll]" value="N" id="embed_scrollN" /> <?php _e('No', $this -> plugin_name); ?></label>
                    <span class="howto"><?php _e('Should the page scroll to focus on the subscription form?', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captchaN"><?php _e('Use Captcha for Form', $this -> plugin_name); ?></label></th>
                <td>
                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo ($embed['captcha'] == "Y") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha]" value="Y" id="captchaY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo ($embed['captcha'] == "N") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha]" value="N" id="captchaN" /> <?php _e('No', $this -> plugin_name); ?></label>
                    <span class="howto"><?php _e('Requires captcha image input upon subscribing.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>