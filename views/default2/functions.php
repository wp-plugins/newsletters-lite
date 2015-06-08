<?php
	
if (!class_exists('newslettersBootstrap')) {
	class newslettersBootstrap extends wpMailPlugin {
		
		function enqueue_scripts() {
			// load custom scripts here using wp_enqueue_script as needed...
			
			wp_enqueue_script('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array('jquery'), '3.3.4', false);
			wp_enqueue_script('bootstrap-datepicker', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js', array('jquery', 'bootstrap'), '1.4.0', false);
			wp_enqueue_script('bootstrap-datepicker-i18n', $this -> render_url('js/datepicker-i18n.js', 'default2', false), array('jquery', 'bootstrap', 'bootstrap-datepicker'));
			
			//localize our js
			global $Html, $wp_locale;
		    
		    $aryArgs = array(
			    'days'				=>	$Html -> strip_array_indices($wp_locale -> weekday),
			    'daysShort'			=>	$Html -> strip_array_indices($wp_locale -> weekday_abbrev),
			    'daysMin'			=>	$Html -> strip_array_indices($wp_locale -> weekday_initial),
			    'months'			=>	$Html -> strip_array_indices($wp_locale -> month),
			    'monthsShort'		=>	$Html -> strip_array_indices($wp_locale -> month_abbrev),
			    'today'				=>	__('Today', $this -> plugin_name),
			    'clear'				=>	__('Clear', $this -> plugin_name),
			    'rtl'				=>	(!empty($wp_locale -> is_rtl) ? true : false),
		    );
		 
		    // Pass the localized array to the enqueued JS
		    wp_localize_script('bootstrap-datepicker-i18n', 'bootstrap_datepicker_dates', $aryArgs);
		}
		
		function enqueue_styles() {
			// load custom styles here using wp_enqueue_style as needed...
			
			wp_enqueue_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', false, '3.3.4', "all");
			wp_enqueue_style('bootstrap-datepicker', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css', false, '1.4.0', "all");
		}
		
		function datepicker_output($output = null, $optinid = null, $field = null) {
			global $Html;
			
			$output = "";
			$locale = get_locale();
			
			ob_start();
			
			if (!empty($_POST[$field -> slug])) {
				$_POST[$field -> slug] = maybe_unserialize($_POST[$field -> slug]);
			}
			
			$currentDate = "";
			if (!empty($_POST[$field -> slug])) {
				if (is_array($_POST[$field -> slug])) {
					$currentDate = date_i18n(get_option('date_format'), strtotime($_POST[$field -> slug]['d'] . '/' . $_POST[$field -> slug]['m'] . '/' . $_POST[$field -> slug]['y']));
				} else {
					$currentDate = date_i18n(get_option('date_format'), strtotime($_POST[$field -> slug]));
				}
			}
			
			if (!empty($currentDate)) {
				$defaultDate = 'new Date(' . date_i18n("Y", strtotime($currentDate)) . ', ' . date_i18n("m", strtotime($currentDate)) . ', ' . date_i18n("d", strtotime($currentDate)) . ')';
			} else {
				$defaultDate = 'new Date(' . date_i18n("Y") . ', ' . date_i18n("m") . ', ' . date_i18n("d") . ')';
			}
			
			?>
			
			<div id="newsletters-<?php echo $optinid . $field -> slug; ?>-dateholder">
				<div class="input-group date">
					<input type="text" class="form-control wpmlpredate wpmltext wpml wpmlpredate<?php echo ((!empty($_POST['wpmlerrors'][$field -> slug])) ? ' ' . 'wpmlfielderror' : ''); ?>" value="<?php echo $currentDate; ?>" name="<?php echo $field -> slug; ?>" id="wpml-<?php echo $optinid . $field -> slug; ?>" />
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
			
			<?php if (empty($offsite)) : ?>
				<script type="text/javascript">			
				jQuery(document).ready(function() {
					jQuery('#newsletters-<?php echo $optinid . $field -> slug; ?>-dateholder .input-group.date').datepicker({
						autoclose: true,
						format: '<?php echo $this -> dateformat_php_to_bootstrap_datepicker(get_option('date_format')); ?>',
						//language: '<?php echo str_replace("_", "-", $locale); ?>',
						language: 'en',
						todayBtn: true,
						todayHighlight: true
					})
				});
				</script>
			<?php endif; ?>
			
			<?php
				
			$output = ob_get_clean();
			
			return $output;
		}
		
		function dateformat_php_to_bootstrap_datepicker($php_format = null) {
		    $SYMBOLS_MATCHING = array(
		        // Day
		        'd' => 'dd',
		        'D' => 'D',
		        'j' => 'd',
		        'l' => 'DD',
		        'N' => '',
		        'S' => '',
		        'w' => '',
		        'z' => 'o',
		        // Week
		        'W' => '',
		        // Month
		        'F' => 'MM',
		        'm' => 'mm',
		        'M' => 'M',
		        'n' => 'm',
		        't' => '',
		        // Year
		        'L' => '',
		        'o' => '',
		        'Y' => 'yyyy',
		        'y' => 'yy',
		        // Time
		        'a' => '',
		        'A' => '',
		        'B' => '',
		        'g' => '',
		        'G' => '',
		        'h' => '',
		        'H' => '',
		        'i' => '',
		        's' => '',
		        'u' => ''
		    );
		    $jqueryui_format = "";
		    $escaping = false;
		    for($i = 0; $i < strlen($php_format); $i++) {
		        $char = $php_format[$i];
		        if($char === '\\') // PHP date format escaping character
		        {
		            $i++;
		            if($escaping) $jqueryui_format .= $php_format[$i];
		            else $jqueryui_format .= '\'' . $php_format[$i];
		            $escaping = true;
		        }
		        else
		        {
		            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
		            if(isset($SYMBOLS_MATCHING[$char]))
		                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
		            else
		                $jqueryui_format .= $char;
		        }
		    }
		    return $jqueryui_format;
		}
	}
	
	$newslettersBootstrap = new newslettersBootstrap();
	add_action('wp_enqueue_scripts', array($newslettersBootstrap, 'enqueue_scripts'));
	add_action('wp_enqueue_scripts', array($newslettersBootstrap, 'enqueue_styles'));
	add_filter('newsletters_datepicker_output', array($newslettersBootstrap, 'datepicker_output'), 10, 3);
}	
	
?>