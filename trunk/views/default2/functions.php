<?php
	
if (!class_exists('newslettersBootstrap')) {
	class newslettersBootstrap extends wpMailPlugin {
		
		function default_styles($defaultstyles = array()) {
			
			$defaultstyles = array();
			
			$defaultstyles['bootstrap'] = array(
				'name'					=>	"Bootstrap",
				//'url'					=>	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
				'url'					=>	$this -> render_url('css/bootstrap.css', 'default2', false),
				'version'				=>	'3.3.4',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['bootstrap-datepicker'] = array(
				'name'					=>	"Bootstrap Datepicker",
				'url'					=>	'//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css',
				'version'				=>	'1.4.0',
				'deps'					=>	array('bootstrap'),
				'media'					=>	"all",
			);
			
			$defaultstyles['fontawesome'] = array(
				'name'					=>	"FontAwesome",
				'url'					=>	'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
				'version'				=>	'4.3.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['select2'] = array(
				'name'					=>	"Select2",
				'url'					=>	'//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css',
				'version'				=>	'4.0.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['uploadify'] = array(
				'name'					=>	"Uploadify",
				'url'					=>	$this -> render_url('css/uploadify.css', 'default2', false),
				'version'				=>	'2.2',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['newsletters'] = array(
				'name'					=>	"Theme Folder style.css (recommended)",
				'url'					=>	$this -> render_url('css/style.css', 'default2', false),
				'version'				=>	false,
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			return $defaultstyles;
		}
		
		function default_scripts($defaultscripts = array()) {
			
			$defaultscripts = array();
			
			$defaultscripts['bootstrap'] = array(
				'name'					=>	"Bootstrap",
				'url'					=>	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js',
				'version'				=>	'3.3.4',
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			$defaultscripts['bootstrap-datepicker'] = array(
				'name'					=>	"Bootstrap Datepicker",
				'url'					=>	"//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js",
				'version'				=>	"1.4.0",
				'deps'					=>	array('jquery', 'bootstrap'),
				'footer'				=>	false,
			);
			
			$defaultscripts['select2'] = array(
				'name'					=>	"Select2",
				'url'					=>	"//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js",
				'version'				=>	"4.0.0",
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			$defaultscripts['uploadify'] = array(
				'name'					=>	"Uploadify",
				'url'					=>	$this -> render_url('js/jquery.uploadify.js', 'default2', false),
				'version'				=>	"2.2",
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			return $defaultscripts;
		}
		
		function enqueuescript_after($handle = null, $script = null) {
			if (!empty($handle) && $handle == "bootstrap-datepicker") {
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
		}
		
		function datepicker_output($output = null, $optinid = null, $field = null) {
			global $Html;
			
			if (is_admin() && !defined('DOING_AJAX')) {
				return $output;
			}
			
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
			
			<div id="newsletters-<?php echo $optinid . $field -> slug; ?>-dateholder" class="newsletters-dateholder">
				<div class="input-group date">
					<input type="text" class="form-control wpmlpredate wpmltext wpml wpmlpredate<?php echo ((!empty($_POST['wpmlerrors'][$field -> slug])) ? ' ' . 'wpmlfielderror' : ''); ?>" value="<?php echo $currentDate; ?>" name="<?php echo $field -> slug; ?>" id="wpml-<?php echo $optinid . $field -> slug; ?>" />
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
			
			<?php if (empty($offsite)) : ?>
				<script type="text/javascript">			
				jQuery(document).ready(function() {
					jQuery('#newsletters-<?php echo $optinid . $field -> slug; ?>-dateholder .input-group.date').datepicker({
						container: '#newsletters-<?php echo $optinid . $field -> slug; ?>-dateholder',
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
		    for ($i = 0; $i < strlen($php_format); $i++) {
		        $char = $php_format[$i];
		        if ($char === '\\') {
		            $i++;
		            if($escaping) $jqueryui_format .= $php_format[$i];
		            else $jqueryui_format .= '\'' . $php_format[$i];
		            $escaping = true;
		        } else {
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
	
	add_filter('newsletters_default_styles', array($newslettersBootstrap, 'default_styles'));
	add_filter('newsletters_default_scripts', array($newslettersBootstrap, 'default_scripts'));
	add_action('newsletters_enqueuescript_after', array($newslettersBootstrap, 'enqueuescript_after'), 10, 2);
	add_filter('newsletters_datepicker_output', array($newslettersBootstrap, 'datepicker_output'), 10, 3);
}	
	
?>