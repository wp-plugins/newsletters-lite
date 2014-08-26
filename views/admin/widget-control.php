<?php if ($this -> is_plugin_active('qtranslate')) : ?>
	<?php 
	
	global $q_config;
	$el = qtrans_getSortedLanguages();
	$langnumber = (empty($number) || $number == "%i%") ? "" : $number;
	
	?>
    
    <?php if (!empty($el) && is_array($el)) : ?>
    	<div class="<?php echo $this -> pre; ?>">
	    	<div id="languagetabs<?php echo $langnumber; ?>">
	        	<ul>
					<?php $tabnumber = 1; ?>
	                <?php foreach ($el as $language) : ?>
	                	<li><a href="#languagetab<?php echo $langnumber; ?>-<?php echo $tabnumber; ?>"><img src="<?php echo content_url(); ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></a></li>
	                    <?php $tabnumber++; ?>
	                <?php endforeach; ?>
	            </ul>
	        
	        	<?php $tabnumber = 1; ?>
				<?php foreach ($el as $language) : ?>
	                <div id="languagetab<?php echo $langnumber; ?>-<?php echo $tabnumber; ?>">
	                    <p>
	                        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_title_<?php echo $language; ?>">
	                            <?php _e('Title:', $this -> plugin_name); ?>
	                            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_title_<?php echo $language; ?>" class="widefat" type="text" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][title][<?php echo $language; ?>]" value="<?php echo $options[$number]['title'][$language]; ?>" />
	                        </label>
	                    </p>
	                    <p>
	                        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_list_<?php echo $language; ?>">
	                            <?php _e('Mailing List:', $this -> plugin_name); ?><br/>
	                            <select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_lists_<?php echo $language; ?>_div').show(); } else { jQuery('#<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_lists_<?php echo $language; ?>_div').hide(); }" class="widefat" style="width:auto;" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][list][<?php echo $language; ?>]" id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_list_<?php echo $language; ?>">
	                                <optgroup label="<?php _e('Multiple Choice', $this -> plugin_name); ?>">
	                                    <option <?php echo ($options[$number]['list'][$language] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php _e('Show Select Drop Down', $this -> plugin_name); ?></option>
	                                    <option <?php echo ($options[$number]['list'][$language] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php _e('Show Checkbox List', $this -> plugin_name); ?></option>
	                                    <?php if ($this -> language_ready()) : ?>
	                                        <option <?php echo ($options[$number]['list'] == "language") ? 'selected="selected"' : ''; ?> value="language"><?php _e('Auto by Language', $this -> plugin_name); ?></option>						
										<?php endif; ?>
	                                </optgroup>
	                                <?php if ($mailinglists = $this -> Mailinglist -> select()) : ?>
	                                    <optgroup label="<?php _e('Specific Mailing List', $this -> plugin_name); ?>">
	                                    <?php foreach ($mailinglists as $id => $title) : ?>
	                                        <option <?php echo (!empty($options[$number]['list'][$language]) && $options[$number]['list'][$language] == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $id; ?> - <?php echo $title; ?></option>
	                                    <?php endforeach; ?>
	                                    </optgroup>
	                                <?php endif; ?>
	                            </select>
	                        </label>
	                    </p>
	                    <p>
	                    	<span id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_lists_<?php echo $language; ?>_div">
		                    	<label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_lists_<?php echo $language; ?>">
		                    		<?php _e('Lists', $this -> plugin_name); ?>
		                    		<br/><input class="widefat" type="text" value="<?php echo $options[$number]['lists'][$language]; ?>" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][lists][<?php echo $language; ?>]" id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_lists_<?php echo $language; ?>" />
		                    		<br/><small><?php _e('Comma separated list IDs to show (eg. 11,3,7), leave empty for all.', $this -> plugin_name); ?></small>
		                    	</label>
	                    	</span>
	                    </p>
	                    <p>
	                        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_subtitle_<?php echo $language; ?>">
	                            <?php _e('Subtitle', $this -> plugin_name); ?> :
	                            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_subtitle_<?php echo $language; ?>" type="text" class="widefat" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subtitle][<?php echo $language; ?>]" value="<?php echo $options[$number]['subtitle'][$language]; ?>" />
	                        </label>
	                    </p>
	                    <p>
	                        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_acknowledgement_<?php echo $language; ?>">
	                            <?php _e('Acknowledgement Message', $this -> plugin_name); ?> :
	                            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_acknowledgement_<?php echo $language; ?>" class="widefat" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][acknowledgement][<?php echo $language; ?>]" type="text" value="<?php echo $options[$number]['acknowledgement'][$language]; ?>" />
	                        </label>
	                    </p>
	                    <p>
	                        <?php _e('Subscribe Again Link:', $this -> plugin_name); ?><br/>
	                        <label><input <?php echo (empty($options[$number]['subscribeagain'][$language]) || $options[$number]['subscribeagain'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subscribeagain][<?php echo $language; ?>]" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
	                        <label><input <?php echo ($options[$number]['subscribeagain'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subscribeagain][<?php echo $language; ?>]" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
	                        <span class="howto"><?php _e('display a "subscribe again" link on success', $this -> plugin_name); ?></span>
	                    </p>
	                    <p>
	                        <?php _e('Ajax Features:', $this -> plugin_name); ?><br/>
	                        <label><input <?php echo ($options[$number]['ajax'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][ajax][<?php echo $language; ?>]" value="Y" /> <?php _e('Yes, turn on Ajax features', $this -> plugin_name); ?></label><br/>
	                        <label><input <?php echo (empty($options[$number]['ajax'][$language]) || $options[$number]['ajax'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][ajax][<?php echo $language; ?>]" value="N" /> <?php _e('No, just normal page refresh', $this -> plugin_name); ?></label>
	                    </p>
	                    <p>
	                        <?php _e('Use Captcha for form', $this -> plugin_name); ?> :<br/>
	                        <?php $rr_active = (is_plugin_active(plugin_basename('really-simple-captcha/really-simple-captcha.php'))) ? true : false; ?>
	                        <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo ($options[$number]['captcha'][$language] == "Y") ? 'checked="checked"' : ''; } ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][captcha][<?php echo $language; ?>]" value="Y" id="captchaY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
	                        <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo (empty($options[$number]['captcha'][$language]) || $options[$number]['captcha'][$language] == "N") ? 'checked="checked"' : ''; } ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][captcha][<?php echo $language; ?>]" value="N" id="captchaN" /> <?php _e('No', $this -> plugin_name); ?></label>
	                        <?php if (!$rr_active) : ?>
	                            <br/><span style="color:red;"><small><?php _e('You need to install and activate the <a target="_blank" href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple Captcha plugin</a>.', $this -> plugin_name); ?></small></span>
	                            <input type="hidden" name="captcha" value="N" />
	                        <?php endif; ?>
	                    </p>
	                    <p>
	                        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_button_<?php echo $language; ?>">
	                            <?php _e('Button Text', $this -> plugin_name); ?>
	                            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_button_<?php echo $language; ?>" class="widefat" type="text" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][button][<?php echo $language; ?>]" value="<?php echo $options[$number]['button'][$language]; ?>" />
	                        </label>
	                    </p>
	                </div>
	                <?php $tabnumber++; ?>
	            <?php endforeach; ?>
	        </div>
	    </div>
        
        <script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#languagetabs<?php echo $langnumber; ?>').tabs();
		});
		</script>
    <?php else : ?>
    	<p class="<?php echo $this -> pre; ?>error"><?php _e('No qTranslate languages have been defined.', $this -> plugin_name); ?></p>
    <?php endif; ?>
<?php else : ?>
    <p>
        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_title">
            <?php _e('Title', $this -> plugin_name); ?> :
            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_title" class="widefat" type="text" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][title]" value="<?php echo $options[$number]['title']; ?>" />
        </label>
    </p>
    <p>
        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_list">
            <?php _e('Mailing List', $this -> plugin_name); ?> :<br/>
            <select class="widefat" style="width:auto;" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][list]" id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_list">
                <optgroup label="<?php _e('Multiple Choice', $this -> plugin_name); ?>">
                    <option <?php echo ($options[$number]['list'] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php _e('Show Select Drop Down', $this -> plugin_name); ?></option>
                    <option <?php echo ($options[$number]['list'] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php _e('Show Checkbox List', $this -> plugin_name); ?></option>
                    <?php if ($this -> language_ready()) : ?>
                        <option <?php echo ($options[$number]['list'] == "language") ? 'selected="selected"' : ''; ?> value="language"><?php _e('Auto by Language', $this -> plugin_name); ?></option>						<?php endif; ?>
                </optgroup>
                <?php if ($mailinglists = $this -> Mailinglist -> select()) : ?>
                    <optgroup label="<?php _e('Specific Mailing List', $this -> plugin_name); ?>">
                    <?php foreach ($mailinglists as $id => $title) : ?>
                        <option <?php echo (!empty($options[$number]['list']) && $options[$number]['list'] == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $title; ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
            </select>
        </label>
    </p>
    <p>
        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_subtitle">
            <?php _e('Subtitle', $this -> plugin_name); ?> :
            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_subtitle" type="text" class="widefat" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subtitle]" value="<?php echo $options[$number]['subtitle']; ?>" />
        </label>
    </p>
    <p>
        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_acknowledgement">
            <?php _e('Acknowledgement Message', $this -> plugin_name); ?> :
            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_acknowledgement" class="widefat" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][acknowledgement]" type="text" value="<?php echo $options[$number]['acknowledgement']; ?>" />
        </label>
    </p>
    <p>
        <?php _e('Subscribe Again Link', $this -> plugin_name); ?> :<br/>
        <label><input <?php echo (empty($options[$number]['subscribeagain']) || $options[$number]['subscribeagain'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subscribeagain]" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
        <label><input <?php echo ($options[$number]['subscribeagain'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][subscribeagain]" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
        <span class="howto"><?php _e('display a "subscribe again" link on success', $this -> plugin_name); ?></span>
    </p>
    <p>
        <?php _e('Ajax Features', $this -> plugin_name); ?> :<br/>
        <label><input <?php echo ($options[$number]['ajax'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][ajax]" value="Y" /> <?php _e('Yes, turn on Ajax features', $this -> plugin_name); ?></label><br/>
        <label><input <?php echo (empty($options[$number]['ajax']) || $options[$number]['ajax'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][ajax]" value="N" /> <?php _e('No, just normal page refresh', $this -> plugin_name); ?></label>
    </p>
    <p>
        <?php _e('Use Captcha for form', $this -> plugin_name); ?> :<br/>
        <?php $rr_active = (is_plugin_active(plugin_basename('really-simple-captcha/really-simple-captcha.php'))) ? true : false; ?>
            <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo ($options[$number]['captcha'] == "Y") ? 'checked="checked"' : ''; } ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][captcha]" value="Y" id="captchaY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
            <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo ($options[$number]['captcha'] == "N") ? 'checked="checked"' : ''; } ?> type="radio" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][captcha]" value="N" id="captchaN" /> <?php _e('No', $this -> plugin_name); ?></label>
            <?php if (!$rr_active) : ?>
                <br/><span style="color:red;"><small><?php _e('You need to install and activate the <a target="_blank" href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple Captcha plugin</a>.', $this -> plugin_name); ?></small></span>
                <input type="hidden" name="captcha" value="N" />
            <?php endif; ?>
    </p>
    <p>
        <label for="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_button">
            <?php _e('Button Text', $this -> plugin_name); ?>
            <input id="<?php echo $this -> pre; ?>widget_<?php echo $number; ?>_button" class="widefat" type="text" name="<?php echo $this -> pre; ?>widget[<?php echo $number; ?>][button]" value="<?php echo $options[$number]['button']; ?>" />
        </label>
    </p>
<?php endif; ?>