<!-- Subscribe Form -->

<?php do_action('newsletters_subscribe_before_form', $instance); ?>

<form action="<?php echo $action; ?>" onsubmit="jQuery.Watermark.HideAll();" method="post" id="<?php echo $widget_id; ?>-form" class="newsletters-form">

	<?php $hidden_values = array('ajax', 'scroll', 'captcha', 'list'); ?>
	<?php foreach ($instance as $ikey => $ival) : ?>
		<?php if (!empty($ikey) && in_array($ikey, $hidden_values)) : ?>
			<input type="hidden" name="instance[<?php echo $ikey; ?>]" value="<?php echo esc_attr(stripslashes(__($ival))); ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
	
	<?php do_action('newsletters_subscribe_inside_form_top', $instance); ?>

	<div id="<?php echo $widget_id; ?>-fields">
		<?php 
		
		$list_id = (empty($_POST['list_id'])) ? $instance['list'] : $_POST['list_id']; 
		
		?>
		<?php if ($fields = $FieldsList -> fields_by_list($list_id)) : ?>
			<?php foreach ($fields as $field) : ?>
				<?php $this -> render_field($field -> id, true, $widget_id, true, true, $instance); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-checkbox').on('click', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
			jQuery('#<?php echo $widget_id; ?>-form .newsletters-list-select').on('change', function() { newsletters_refreshfields('<?php echo $widget_id; ?>'); });
		});
		</script>
	</div>
	
	<?php if ($captcha_type = $this -> use_captcha(__($instance['captcha']))) : ?>		
		<?php if ($captcha_type == "rsc") : ?>
	    	<?php 
	    	
	    	$captcha = new ReallySimpleCaptcha();
	    	$captcha -> bg = $Html -> hex2rgb($this -> get_option('captcha_bg')); 
	    	$captcha -> fg = $Html -> hex2rgb($this -> get_option('captcha_fg'));
	    	$captcha_size = $this -> get_option('captcha_size');
	    	$captcha -> img_size = array($captcha_size['w'], $captcha_size['h']);
	    	$captcha -> char_length = $this -> get_option('captcha_chars');
	    	$captcha -> font_size = $this -> get_option('captcha_font');
	    	$captcha_word = $captcha -> generate_random_word();
	    	$captcha_prefix = mt_rand();
	    	$captcha_filename = $captcha -> generate_image($captcha_prefix, $captcha_word);
	        $captcha_file = plugins_url() . '/really-simple-captcha/tmp/' . $captcha_filename; 
	    	
	    	?>
	    	<div class="newsletters-fieldholder <?php echo $this -> pre; ?>captcha">
	        	<input type="hidden" name="captcha_prefix" value="<?php echo $captcha_prefix; ?>" />
	            <label for="<?php echo $this -> pre; ?>captcha_code"><?php _e('Please fill in the code below:', $this -> plugin_name); ?></label>
	            <img src="<?php echo $captcha_file; ?>" alt="captcha" />
	            <input <?php echo $Html -> tabindex($widget_id); ?> class="<?php echo $this -> pre; ?>captchacode <?php echo $this -> pre; ?>text <?php echo (!empty($errors['captcha_code'])) ? $this -> pre . 'fielderror' : ''; ?>" type="text" name="captcha_code" id="<?php echo $this -> pre; ?>captcha_code" value="" />
	        </div>
		<?php elseif ($captcha_type == "recaptcha") : ?>
			<?php 
			
			$recaptcha_publickey = $this -> get_option('recaptcha_publickey'); 
			$recaptcha_theme = $this -> get_option('recaptcha_theme');
			$recaptcha_customcss = $this -> get_option('recaptcha_customcss');
			
			?>
			<div id="<?php echo $widget_id; ?>-recaptcha" style="display:none;" class="recaptcha_widget">
				<?php if (!empty($recaptcha_theme) && $recaptcha_theme == "custom") : ?>
					<div id="recaptcha_image" class="recaptcha_image"></div>
					<div id="recaptcha_links" class="recaptcha_links">
						<a href="javascript:Recaptcha.reload()">reload</a> |
						<a class="recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio')">switch to audio</a>
						<a class="recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image')">switch to image</a> |
						<a href="javascript:Recaptcha.showhelp()">help</a>
					</div>
					<div class="newsletters-fieldholder recaptcha_response">
						<label for="recaptcha_response_field" class="wpmlcustomfield"><?php _e('Type the words', $this -> plugin_name); ?></label>
						<input type="text" id="recaptcha_response_field" class="wpml widefat wpmltext recaptcha_response_field" name="recaptcha_response_field">
					</div>					
					<style type="text/css">
					<?php echo $recaptcha_customcss; ?>
					</style>
				<?php endif; ?>
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function() { 
				Recaptcha.create("<?php echo $recaptcha_publickey; ?>", "<?php echo $widget_id; ?>-recaptcha", {
					theme:"<?php echo $recaptcha_theme; ?>",
					tabindex:<?php echo $Html -> tabindex($widget_id); ?>
				}); 
			});
			</script>
		<?php endif; ?>
    <?php endif; ?>
    
    <div class="newslettername-wrapper">
    	<input type="text" name="newslettername" value="" id="<?php echo $widget_id; ?>newslettername" class="newslettername" />
    </div>
	
	<div id="<?php echo $widget_id; ?>-submit" class="newsletters_submit">
		<span id="newsletters_buttonwrap">
			<input type="submit" class="button" name="submit" value="<?php echo esc_attr(stripslashes(__($instance['button']))); ?>" id="<?php echo $widget_id; ?>-button" />
		</span>
		<span id="<?php echo $widget_id; ?>-loading" class="newsletters_loading" style="display:none;">
			<img src="<?php echo $this -> render_url('img' . DS . 'loading.gif', 'default', false); ?>" alt="loading" />
		</span>
	</div>
</form>

<?php do_action('newsletters_subscribe_after_form', $instance); ?>

<?php $this -> render('error', array('errors' => $Subscriber -> errors)); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php 
	
	$ajax = __($instance['ajax']); 
	$scroll = __($instance['scroll']);
	
	?>
	<?php if (!empty($ajax) && $ajax == "Y") : ?>
		jQuery('#<?php echo $widget_id; ?>-form').submit(function() {
			jQuery('#<?php echo $widget_id; ?>-loading').show();
			jQuery('#<?php echo $widget_id; ?>-button').button('option', 'disabled', true);
			jQuery('#<?php echo $widget_id; ?> .wpmlfieldholder :input').attr('readonly', true);
		
			jQuery.ajax({
				url: wpmlajaxurl + 'action=wpmlsubscribe&widget=<?php echo $widget; ?>&widget_id=<?php echo $widget_id; ?>&number=<?php echo $number; ?>&nonce=<?php echo wp_create_nonce($widget); ?>',
				data: jQuery('#<?php echo $widget_id; ?>-form').serialize(),
				type: "POST",
				cache: false,
				success: function(response) {
					jQuery('#<?php echo $widget_id; ?>-wrapper').html(response);
					<?php if (!empty($scroll)) : ?>
						wpml_scroll(jQuery('#<?php echo $widget_id; ?>'));
					<?php endif; ?>
				}
			});
			
			return false;
		});
	<?php endif; ?>
			
	jQuery('.widget_newsletters .button').button();
});
</script>