<?php

class Newsletters_Widget extends WP_Widget {
	
	public function __construct() {
		$widget_ops = array( 'classname' => 'newsletters widget_newsletters wpml', 'description' => __('Subscribe form for your sidebar(s)', 'wp-mailinglist'));
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'newsletters' );
		parent::__construct('newsletters', __('Newsletters', 'wp-mailinglist'), $widget_ops, $control_ops);
	}
	
	public function widget($args, $instance) {	
		global $FieldsList, $Html, $Subscriber;
				
		extract($args);		
		echo $before_widget;
		$title = apply_filters('widget_title', __($instance['title']));
		if (!empty($title)) { echo $before_title . __($title) . $after_title; }
		$subtitle = apply_filters('widget_subtitle', $instance['subtitle']);
		if (!empty($subtitle)) { echo '<p>' . __($subtitle) . '</p>'; }
		
		$wpMail = new wpMail();
		$widget = __CLASS__;
		
		$action = ($wpMail -> language_do()) ? $wpMail -> language_converturl($_SERVER['REQUEST_URI'], $instance['language']) : $_SERVER['REQUEST_URI'];
		$action = $Html -> retainquery($wpMail -> pre . 'method=optin', $action) . '#' . $widget_id;
		
		?>
		
		<div id="<?php echo $widget_id; ?>-wrapper" class="newsletters newsletters-widget-wrapper">
			<?php $wpMail -> render('widget', array('action' => $action, 'errors' => $Subscriber -> errors, 'widget' => $widget, 'args' => $args, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $this -> number), true, 'default'); ?>
		</div>
		
		<?php
		
		echo $after_widget;
	}
	
	public function form($instance = null) {
		global $Html;
	
		if (class_exists('wpMail')) {
			if ($wpMail = new wpMail()) {									
				if (empty($instance)) {
					$instance['title'] = __('Stay up to date', $wpMail -> plugin_name);
					$instance['list'] = "select";
					$instance['subtitle'] = __('Subscribe for email updates', $wpMail -> plugin_name);
					$instance['acknowledgement'] = __('Thank you for subscribing!', $wpMail -> plugin_name);
					$instance['ajax'] = "Y";
					$instance['scroll'] = 1;
					$instance['captcha'] = "N";
					$instance['button'] = __('Subscribe Now', $wpMail -> plugin_name);
				}
				
				if ($wpMail -> language_do()) {
					$languages = $wpMail -> language_getlanguages();
					
					foreach ($instance as $ikey => $ival) {
						$instance[$ikey] = $wpMail -> language_split($ival);
					}
				
					?>
					
					<div class="<?php echo $wpMail -> pre; ?>">
						<div id="languagetabs<?php echo $this -> number; ?>">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li>
										<a href="#languagetab<?php echo $this -> number . $language; ?>"><?php echo $wpMail -> language_flag($language); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="languagetab<?php echo $this -> number . $language; ?>">
									<p>
										<label for="<?php echo $this -> get_field_id('title'); ?>-<?php echo $language; ?>"><?php _e('Title:', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('The title of your widget used as a heading for display to your users on the front.', $wpMail -> plugin_name)); ?>
										<input class="widefat" name="<?php echo $this -> get_field_name('title'); ?>[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($instance['title'][$language])); ?>" id="<?php echo $this -> get_field_id('title'); ?>-<?php echo $language; ?>" />
									</p>
									<p>
										<label for="<?php echo $this -> get_field_id('list'); ?>-<?php echo $language; ?>"><?php _e('Mailing List:', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('Choose the list(s) that users will subscribe to. You can choose either a single, specific list or choose to have a select drop down of lists or checkboxes lists.', $wpMail -> plugin_name)); ?>
										<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#<?php echo $this -> get_field_id('lists'); ?>-<?php echo $language; ?>').show(); } else { jQuery('#<?php echo $this -> get_field_id('lists'); ?>-<?php echo $language; ?>').hide(); }" class="widefat" name="<?php echo $this -> get_field_name('list'); ?>[<?php echo $language; ?>]" id="<?php echo $this -> get_field_id('list'); ?>-<?php echo $language; ?>">
											<option value=""><?php _e('- Select -', $wpMail -> plugin_name); ?></option>
											<optgroup label="<?php _e('Multiple', $wpMail -> plugin_name); ?>">
												<option <?php echo ($instance['list'][$language] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Lists (no choice)', $wpMail -> plugin_name); ?></option>
												<option <?php echo ($instance['list'][$language] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php _e('Select Drop Down', $wpMail -> plugin_name); ?></option>
												<option <?php echo ($instance['list'][$language] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php _e('Checkbox List', $wpMail -> plugin_name); ?></option>
											</optgroup>
											<?php if ($lists = $wpMail -> Mailinglist -> select(true)) : ?>
												<optgroup label="<?php _e('Specific', $wpMail -> plugin_name); ?>">
													<?php foreach ($lists as $list_id => $list_title) : ?>
														<option <?php echo ($instance['list'][$language] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo $list_id; ?>"><?php echo __($list_title); ?></option>
													<?php endforeach; ?>
												</optgroup>
											<?php endif; ?>
										</select>
									</p>
									<div id="<?php echo $this -> get_field_id('lists'); ?>-<?php echo $language; ?>" style="display:<?php echo ($instance['list'][$language] == "select" || $instance['list'][$language] == "checkboxes") ? 'block' : 'none'; ?>;">
										<p>
											<label for="<?php echo $this -> get_field_id('lists'); ?>-<?php echo $language; ?>"><?php _e('Lists', $wpMail -> plugin_name); ?></label>
											<?php echo $Html -> help(__('If you are using a select drop down or checkboxes list, you can now specify which lists should be included. Use comma separated list IDs eg. 2,3. To use all non-private mailing lists, leave this empty.', $wpMail -> plugin_name)); ?>
											<input type="text" name="<?php echo $this -> get_field_name('lists'); ?>[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($instance['lists'][$language])); ?>" id="<?php echo $this -> get_field_id('lists'); ?>-<?php echo $language; ?>" class="widefat" />
										</p>
									</div>
									<p>
										<label for="<?php echo $this -> get_field_id('subtitle'); ?>-<?php echo $language; ?>"><?php _e('Subtitle:', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('Specify the subtitle to show below the title of the widget and above the fields.', $wpMail -> plugin_name)); ?>
										<input type="text" name="<?php echo $this -> get_field_name('subtitle'); ?>[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($instance['subtitle'][$language])); ?>" id="<?php echo $this -> get_field_id('subtitle'); ?>-<?php echo $language; ?>" class="widefat" />
									</p>
									<p>
										<label for="<?php echo $this -> get_field_id('acknowledgement'); ?>-<?php echo $language; ?>"><?php _e('Success Message:', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('The success message is the acknowledgement displayed to the user after successfully subscribing.', $wpMail -> plugin_name)); ?>
										<input type="text" name="<?php echo $this -> get_field_name('acknowledgement'); ?>[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($instance['acknowledgement'][$language])); ?>" id="<?php echo $this -> get_field_id('acknowledgement'); ?>-<?php echo $language; ?>" class="widefat" />
									</p>
									<p>
										<?php $captcha_type = $wpMail -> get_option('captcha_type'); ?>										
										<label for="<?php echo $this -> get_field_id('captcha'); ?>-<?php echo $language; ?>-N"><?php _e('Security Captcha:', $wpMail -> plugin_name); ?></label>
										<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($captcha_type) && $captcha_type != "none" && $instance['captcha'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('captcha'); ?>[<?php echo $language; ?>]" value="Y" id="<?php echo $this -> get_field_id('captcha'); ?>-<?php echo $language; ?>-Y" /> <?php _e('On', $wpMail -> plugin_name); ?></label>
										<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (empty($captcha_type) || $captcha_type == "none" || $instance['captcha'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('captcha'); ?>[<?php echo $language; ?>]" value="N" id="<?php echo $this -> get_field_id('captcha'); ?>-<?php echo $language; ?>-N" /> <?php _e('Off', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('Display a security captcha image on the subscribe form to prevent spam submissions. It is simply a "human" check to stop bots from subscribing. Configure a captcha under Newsletters > Configuration > System > Captcha accordingly.', $wpMail -> plugin_name)); ?>
										<?php if (empty($captcha_type) || $captcha_type == "none") : ?>
											<br/><small class="<?php echo $wpMail -> pre; ?>error"><?php _e('Please configure a security captcha under Newsletters > Configuration > System > Captcha in order to use this.', $wpMail -> plugin_name); ?></small>
										<?php endif; ?>
									</p>
									<p>
										<label for="<?php echo $this -> get_field_id('ajax'); ?>-<?php echo $language; ?>-Y"><?php _e('Ajax Features:', $wpMail -> plugin_name); ?></label>
										<label><input <?php echo ($instance['ajax'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('ajax'); ?>[<?php echo $language; ?>]" value="Y" id="<?php echo $this -> get_field_id('ajax'); ?>-<?php echo $language; ?>-Y" /> <?php _e('On', $wpMail -> plugin_name); ?></label>
										<label><input <?php echo ($instance['ajax'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('ajax'); ?>[<?php echo $language; ?>]" value="N" id="<?php echo $this -> get_field_id('ajax'); ?>-<?php echo $language; ?>-N" /> <?php _e('Off', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('Turn on/off Ajax for the subscribe form. If you turn Ajax on, the subscribe form will submit without any page refresh and it is much quicker. Turning it off will generate a page refresh as the user submits the form to subscribe.', $wpMail -> plugin_name)); ?>
									</p>
									<p>
										<label><input <?php echo (!empty($instance['scroll'][$language])) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $this -> get_field_name('scroll'); ?>[<?php echo $language; ?>]" value="1" id="<?php echo $this -> get_field_id('scroll'); ?>-<?php echo $language; ?>" /> <?php _e('Scroll to subscribe form', $this -> plugin_name); ?></label>
									</p>
									<p>
										<label for="<?php echo $this -> get_field_id('button'); ?>-<?php echo $language; ?>"><?php _e('Button Text:', $wpMail -> plugin_name); ?></label>
										<?php echo $Html -> help(__('The text to display on the subscribe button at the bottom of the subscribe form.', $wpMail -> plugin_name)); ?>
										<input type="text" name="<?php echo $this -> get_field_name('button'); ?>[<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($instance['button'][$language])); ?>" id="<?php echo $this -> get_field_id('button'); ?>-<?php echo $language; ?>" class="widefat" />
									</p>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.cookie)) {
							var widgettabscookie = jQuery.cookie('widgettabscookie') || 0;
						}
							
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#languagetabs<?php echo $this -> number; ?>').tabs({
								active: widgettabscookie,
								activate: function(event, ui) {
									if (jQuery.isFunction(jQuery.cookie)) {
										jQuery.cookie('widgettabscookie', ui.newTab.index(), {expires: 365, path: '/'});
									}
								}
							});
						}
					});
					</script>
					
					<?php
				} else {
					?>
					
					<p>
						<label for="<?php echo $this -> get_field_id('title'); ?>"><?php _e('Title:', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('The title of your widget used as a heading for display to your users on the front.', $wpMail -> plugin_name)); ?>
						<input class="widefat" name="<?php echo $this -> get_field_name('title'); ?>" value="<?php echo esc_attr(stripslashes($instance['title'])); ?>" id="<?php echo $this -> get_field_id('title'); ?>" />
					</p>
					<p>
						<label for="<?php echo $this -> get_field_id('list'); ?>"><?php _e('Mailing List:', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Choose the list(s) that users will subscribe to. You can choose either a single, specific list or choose to have a select drop down of lists or checkboxes lists.', $wpMail -> plugin_name)); ?>
						<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#<?php echo $this -> get_field_id('lists'); ?>').show(); } else { jQuery('#<?php echo $this -> get_field_id('lists'); ?>').hide(); }" class="widefat" name="<?php echo $this -> get_field_name('list'); ?>" id="<?php echo $this -> get_field_id('list'); ?>">
							<option value=""><?php _e('- Select -', $wpMail -> plugin_name); ?></option>
							<optgroup label="<?php _e('Multiple', $wpMail -> plugin_name); ?>">
								<option <?php echo ($instance['list'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Lists (no choice)', $wpMail -> plugin_name); ?></option>
								<option <?php echo ($instance['list'] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php _e('Select Drop Down', $wpMail -> plugin_name); ?></option>
								<option <?php echo ($instance['list'] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php _e('Checkbox List', $wpMail -> plugin_name); ?></option>
							</optgroup>
							<?php if ($lists = $wpMail -> Mailinglist -> select()) : ?>
								<optgroup label="<?php _e('Specific', $wpMail -> plugin_name); ?>">
									<?php foreach ($lists as $list_id => $list_title) : ?>
										<option <?php echo ($instance['list'] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo $list_id; ?>"><?php echo __($list_title); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						</select>
					</p>
					<div id="<?php echo $this -> get_field_id('lists'); ?>" style="display:<?php echo ($instance['list'] == "select" || $instance['list'] == "checkboxes") ? 'block' : 'none'; ?>;">
						<p>
							<label for="<?php echo $this -> get_field_id('lists'); ?>"><?php _e('Lists', $wpMail -> plugin_name); ?></label>
							<?php echo $Html -> help(__('If you are using a select drop down or checkboxes list, you can now specify which lists should be included. Use comma separated list IDs eg. 2,3. To use all non-private mailing lists, leave this empty.', $wpMail -> plugin_name)); ?>
							<input type="text" name="<?php echo $this -> get_field_name('lists'); ?>" value="<?php echo esc_attr(stripslashes($instance['lists'])); ?>" id="<?php echo $this -> get_field_id('lists'); ?>" class="widefat" />
						</p>
					</div>
					<p>
						<label for="<?php echo $this -> get_field_id('subtitle'); ?>"><?php _e('Subtitle:', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Specify the subtitle to show below the title of the widget and above the fields.', $wpMail -> plugin_name)); ?>
						<input type="text" name="<?php echo $this -> get_field_name('subtitle'); ?>" value="<?php echo esc_attr(stripslashes($instance['subtitle'])); ?>" id="<?php echo $this -> get_field_id('subtitle'); ?>" class="widefat" />
					</p>
					<p>
						<label for="<?php echo $this -> get_field_id('acknowledgement'); ?>"><?php _e('Success Message:', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('The success message is the acknowledgement displayed to the user after successfully subscribing.', $wpMail -> plugin_name)); ?>
						<input type="text" name="<?php echo $this -> get_field_name('acknowledgement'); ?>" value="<?php echo esc_attr(stripslashes($instance['acknowledgement'])); ?>" id="<?php echo $this -> get_field_id('acknowledgement'); ?>" class="widefat" />
					</p>
					<p>
						<?php $captcha_type = $wpMail -> get_option('captcha_type'); ?>
						<label for="<?php echo $this -> get_field_id('captcha'); ?>-N"><?php _e('Security Captcha:', $wpMail -> plugin_name); ?></label>
						<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($captcha_type) && $instance['captcha'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('captcha'); ?>" value="Y" id="<?php echo $this -> get_field_id('captcha'); ?>-Y" /> <?php _e('On', $wpMail -> plugin_name); ?></label>
						<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (empty($captcha_type) || $captcha_type == "none" || $instance['captcha'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('captcha'); ?>" value="N" id="<?php echo $this -> get_field_id('captcha'); ?>-N" /> <?php _e('Off', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Display a security captcha image on the subscribe form to prevent spam submissions.', $wpMail -> plugin_name)); ?>
					</p>
					<p>
						<label for="<?php echo $this -> get_field_id('ajax'); ?>-Y"><?php _e('Ajax Features:', $wpMail -> plugin_name); ?></label>
						<label><input <?php echo ($instance['ajax'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('ajax'); ?>" value="Y" id="<?php echo $this -> get_field_id('ajax'); ?>-Y" /> <?php _e('On', $wpMail -> plugin_name); ?></label>
						<label><input <?php echo ($instance['ajax'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> get_field_name('ajax'); ?>" value="N" id="<?php echo $this -> get_field_id('ajax'); ?>-N" /> <?php _e('Off', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Turn on/off Ajax for the subscribe form. If you turn Ajax on, the subscribe form will submit without any page refresh and it is much quicker. Turning it off will generate a page refresh as the user submits the form to subscribe.', $wpMail -> plugin_name)); ?>
					</p>
					<p>
						<label><input <?php echo (!empty($instance['scroll'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $this -> get_field_name('scroll'); ?>" value="1" id="<?php echo $this -> get_field_id('scroll'); ?>" /> <?php _e('Scroll to subscribe form', $this -> plugin_name); ?></label>
					</p>
					<p>
						<label for="<?php echo $this -> get_field_id('button'); ?>"><?php _e('Button Text:', $wpMail -> plugin_name); ?></label>
						<?php echo $Html -> help(__('The text to display on the subscribe button at the bottom of the subscribe form.', $wpMail -> plugin_name)); ?>
						<input type="text" name="<?php echo $this -> get_field_name('button'); ?>" value="<?php echo esc_attr(stripslashes($instance['button'])); ?>" id="<?php echo $this -> get_field_id('button'); ?>" class="widefat" />
					</p>
					
					<?php
				}
				
				?>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					if (jQuery.isFunction(jQuery.fn.tooltip)) {
						jQuery(".wpmlhelp a").tooltip();
					}
				});
				</script>
				
				<?php
			}
		}
	}
	
	public function update($new_instance, $old_instance) {		
		$instance = array();
			
		if (class_exists('wpMail')) {
			if ($wpMail = new wpMail()) {
				if ($wpMail -> language_do()) {
					foreach ($new_instance as $nikey => $nival) {
						$instance[$nikey] = $nival;
					
						if (is_array($new_instance[$nikey])) {
							$instance[$nikey] = $wpMail -> language_join($nival);
						}
					}
				} else {
					$instance = $new_instance;
				}
			}
		}
		
		return $instance;
	}
}

?>