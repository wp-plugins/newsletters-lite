<!-- Subscribe Form -->

<?php do_action('newsletters_subscribe_before_form', $instance); ?>

<?php $currentusersubscribed = $this -> get_option('currentusersubscribed'); ?>
<?php if (!empty($currentusersubscribed)) : ?>
	<?php if (is_user_logged_in()) : ?>
		<?php $current_user = wp_get_current_user(); ?>
		<?php global $wpdb; ?>
		<?php if ($wpdb -> get_row("SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `email` = '" . $current_user -> user_email . "' AND `user_id` = '" . $current_user -> ID . "'")) : ?>
			<div class="alert alert-success">
				<p><i class="fa fa-check"></i> <?php echo sprintf(__('You are already subscribed with email address %s. Go to the %s page to manage your subscription.', $this -> plugin_name), '<strong>' . $current_user -> user_email . '</strong>', '<a href="' . $this -> get_managementpost(true) . '">' . __('manage subscriptions', $this -> plugin_name) . '</a>'); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<form action="<?php echo $action; ?>" method="post" id="<?php echo $widget_id; ?>-form" class="newsletters-form">

	<?php $hidden_values = array('ajax', 'scroll', 'captcha', 'list'); ?>
	<?php foreach ($instance as $ikey => $ival) : ?>
		<?php if (!empty($ikey) && in_array($ikey, $hidden_values)) : ?>
			<input type="hidden" name="instance[<?php echo $ikey; ?>]" value="<?php echo esc_attr(stripslashes(__($ival))); ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
	
	<?php do_action('newsletters_subscribe_inside_form_top', $instance); ?>

	<div id="<?php echo $widget_id; ?>-fields" class="newsletters-form-fields">
		<?php 
		
		$list_id = (empty($_POST['list_id'])) ? __($instance['list']) : __($_POST['list_id']); 
		
		?>
		<?php if ($fields = $FieldsList -> fields_by_list($list_id)) : ?>
			<?php foreach ($fields as $field) : ?>
				<?php $this -> render_field($field -> id, true, $widget_id, true, true, $instance, false, $errors); ?>
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
		<div class="newsletters-fieldholder newsletters-captcha">
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
		    	<div class="form-group<?php echo (!empty($errors['captcha_code'])) ? ' has-error' : ''; ?>">
		            <label class="control-label" for="<?php echo $this -> pre; ?>captcha_code"><?php _e('Please fill in the code below:', $this -> plugin_name); ?></label>
		            <input <?php echo $Html -> tabindex($widget_id); ?> class="form-control <?php echo $this -> pre; ?>captchacode <?php echo $this -> pre; ?>text <?php echo (!empty($errors['captcha_code'])) ? $this -> pre . 'fielderror' : ''; ?>" type="text" name="captcha_code" id="<?php echo $this -> pre; ?>captcha_code" value="" />
		            <img src="<?php echo $captcha_file; ?>" alt="captcha" />
		            <input type="hidden" name="captcha_prefix" value="<?php echo $captcha_prefix; ?>" />
		    	</div>
			<?php elseif ($captcha_type == "recaptcha") : ?>
				<?php 
				
				$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
				$recaptcha_language = $this -> get_option('recaptcha_language'); 
				$recaptcha_theme = $this -> get_option('recaptcha_theme');
				$recaptcha_customcss = $this -> get_option('recaptcha_customcss');
				
				?>
				
				<div id="<?php echo $widget_id; ?>-recaptcha" class="newsletters_recaptcha_widget" style="">
					<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_publickey; ?>" data-theme="<?php echo $recaptcha_theme; ?>" data-tabindex="<?php echo $Html -> tabindex($widget_id, true); ?>"></div>
		            <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $recaptcha_language; ?>"></script>
		            <input type="hidden" name="captcha_recaptcha" value="1" />
				</div>
			<?php endif; ?>
			
			<?php if (!empty($errors['captcha_code'])) : ?>
				<div id="newsletters-<?php echo $number; ?>-captcha-error" class="newsletters-field-error alert alert-danger">
					<p><i class="fa fa-exclamation-triangle"></i> <?php echo stripslashes($errors['captcha_code']); ?></p>
				</div>
			<?php endif; ?>
		</div>
    <?php endif; ?>
    
    <div class="newslettername-wrapper" style="display:none;">
    	<input type="text" name="newslettername" value="" id="<?php echo $widget_id; ?>newslettername" class="newslettername" />
    </div>
	
	<div class="clearfix"></div>
	
	<div id="<?php echo $widget_id; ?>-submit" class="newsletters-fieldholder newsletters_submit">
		<div class="form-group">
			<span id="newsletters_buttonwrap">
				<input type="submit" class="button btn btn-primary" name="submit" value="<?php echo esc_attr(stripslashes(__($instance['button']))); ?>" id="<?php echo $widget_id; ?>-button" />
			</span>
			<span id="<?php echo $widget_id; ?>-loading" class="newsletters_loading_wrapper" style="display:none;">
				<i class="fa fa-refresh fa-spin fa-fw"></i>
			</span>
		</div>
	</div>
</form>

<?php do_action('newsletters_subscribe_after_form', $instance); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php 
	
	$ajax = __($instance['ajax']); 
	$scroll = __($instance['scroll']);
	
	?>
	<?php if (!empty($ajax) && $ajax == "Y") : ?>
		jQuery('#<?php echo $widget_id; ?>-form').submit(function() {
			jQuery('#<?php echo $widget_id; ?>-loading').show();
			jQuery('#<?php echo $widget_id; ?>-button').prop('disabled', true);
			jQuery('#<?php echo $widget_id; ?> .wpmlfieldholder :input').attr('readonly', true);
			jQuery('div.newsletters-field-error', this).slideUp();
			jQuery(this).find('.<?php echo $this -> pre; ?>fielderror').removeClass('<?php echo $this -> pre; ?>fielderror');
		
			jQuery.ajax({
				url: wpmlajaxurl + 'action=wpmlsubscribe&widget=<?php echo $widget; ?>&widget_id=<?php echo $widget_id; ?>&number=<?php echo $number; ?>',
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
	
	if (jQuery.isFunction(jQuery.fn.select2)) {
		jQuery('.newsletters select').select2();
	}
	
	jQuery('input:not(:button,:submit),textarea,select').focus(function(element) {
		jQuery(this).removeClass('<?php echo $this -> pre; ?>fielderror').nextAll('div.newsletters-field-error').slideUp();	
	});
	
	jQuery('.newsletters-management .newsletters-fieldholder, .entry-content .newsletters-fieldholder, .entry .newsletters-fieldholder').addClass('col-md-6');
});
</script>