<?php

class wpmlFormHelper extends wpMailPlugin {

	var $name = 'Form';
	
	function wpmlFormHelper() {
		return true;
	}
	
	function file($name = null, $options = array()) {
		global $Html;
		
		$defaults = array(
			'error' 		=> 	true,
			'class'			=>	"widefat",
			'width'			=>	"auto",
		);

		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?><input class="<?php echo $class; ?>" style="width:<?php echo $width; ?>;" type="file" name="<?php echo $name; ?>" id="<?php echo $Html -> field_id($name); ?>" /><?php
		
		if ($error == true) {
			echo $Html -> field_error($name);
		}
		
		$file = ob_get_clean();
		return $file;
	}
	
	function hidden($name = null, $options = array()) {
		global $Html;
		
		$defaults = array();
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?><input type="hidden" name="<?php echo $name; ?>" value="<?php echo esc_attr(stripslashes($Html -> field_value($name))); ?>" /><?php
		
		$hidden = ob_get_clean();
		return $hidden;
	}
	
	function text($name = null, $options = array()) {
		global $Html;
		
		$defaults = array(
			'width' 		=> 	"100%", 
			'class'			=>	"widefat",
			'error' 		=> 	true, 
			'id' 			=> 	$Html -> field_id($name),
			'autocomplete'	=>	"on",
			'tabindex'		=>	false,
			'placeholder'	=>	false,
		);
		
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		if ($Html -> has_field_error($name)) {
			$class .= ' newsletters_fielderror';
		}
		
		ob_start();
		
		?><input placeholder="<?php echo esc_attr(stripslashes($placeholder)); ?>" autocomplete="<?php echo $autocomplete; ?>" <?php echo (empty($tabindex)) ? '' : 'tabindex="' . esc_attr(stripslashes($tabindex)) . '"'; ?> class="<?php echo $class; ?>" style="width:<?php echo $width; ?>;" id="<?php echo $id; ?>" type="text" name="<?php echo $name; ?>" value="<?php echo esc_attr(stripslashes($Html -> field_value($name))); ?>" /><?php
		
		if ($error != false) {
			echo $Html -> field_error($name);
		}
		
		$text = ob_get_clean();
		return $text;
	}
	
	function textarea($name = null, $options = array()) {
		global $Html;
		
		$defaults = array('error' => true, 'width' => "100%", 'cols' => "100%", 'class' => "widefat", 'rows' => 5);
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?><textarea style="width:<?php echo $width; ?>;" class="<?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $Html -> field_id($name); ?>" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>"><?php echo stripslashes($Html -> field_value($name)); ?></textarea><?php
		
		if ($error != false) {
			echo $Html -> field_error($name);
		}
		
		$textarea = ob_get_clean();
		return $textarea;
	}
	
	function radio($name = null, $buttons = array(), $options = array()) {
		global $Html;
		
		$defaults = array('error' => true, 'onclick' => 'return;', 'separator' => '<br/>');
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		$value = $Html -> field_value($name);
		
		ob_start();
		
		?>
		
		<?php if (!empty($buttons)) : ?>
			<?php foreach ($buttons as $bkey => $bval) : ?>
				<label><input id="<?php echo $Html -> field_id($name); ?><?php echo $bval; ?>" <?php echo ((!empty($value) && $value == $bkey) || (empty($value) && !empty($default) && $bkey == $default)) ? 'checked="checked"' : ''; ?> onclick="<?php echo $onclick; ?>" type="radio" name="<?php echo $name; ?>" value="<?php echo esc_attr(stripslashes($bkey)); ?>" /> <?php echo __($bval); ?></label><?php echo $separator; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php
		
		if ($error != false) {
			echo $Html -> field_error($name);
		}
		
		$radio = ob_get_clean();
		return $radio;
	}
	
	function checkbox($name = null, $boxes = array(), $options = array()) {
		global $Html;
		
		$defaults = array('separator' => '<br/>');
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?>
		
		<?php if (!empty($boxes)) : ?>
			<?php foreach ($boxes as $bkey => $bval) : ?>
				<label><input <?php echo (is_array($Html -> field_value($name)) && in_array($bkey, $Html -> field_value($name))) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $name; ?>" id="<?php echo $Html -> field_id($name); ?>checklist<?php echo $bkey; ?>" value="<?php echo esc_attr(stripslashes($bkey)); ?>" /> <?php echo __($bval); ?></label><?php echo $separator; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php
		
		$checkbox = ob_get_clean();
		return $checkbox;
	}
	
	function select($name = null, $selects = array(), $options = array()) {
		global $Html;
		
		$defaults = array('error' => true, 'class' => "widefat", 'width' => "auto", 'onchange' => 'return;');
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?>
		
		<select class="<?php echo $class; ?>" style="width:<?php echo $width; ?>;" onchange="<?php echo $onchange; ?>" class="<?php echo $class; ?>" id="<?php echo $Html -> field_id($name); ?>" name="<?php echo $name; ?>">
			<option value="">- <?php _e('Select', $this -> plugin_name); ?> -</option>
			<?php if (!empty($selects)) : ?>
				<?php foreach ($selects as $skey => $sval) : ?>
					<option <?php echo ($Html -> field_value($name) == $skey) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr(stripslashes($skey)); ?>"><?php echo __($sval); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		
		<?php
		
		if ($error != false) { 
			echo $Html -> field_error($name); 
		}
		
		$select = ob_get_clean();
		return $select;
	}
	
	function submit($name = null, $options = array()) {
		global $Html;
		
		$defaults = array('class' => "button-primary");
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start();
		
		?><input type="submit" name="<?php echo $Html -> sanitize($name); ?>" value="<?php echo esc_attr(stripslashes($name)); ?>" class="<?php echo $class; ?>" /><?php
		
		$submit = ob_get_clean();
		return $submit;
	}
}

?>