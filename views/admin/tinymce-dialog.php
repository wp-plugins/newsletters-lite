<?php

global $wpdb, $Mailinglist, $Template;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php _e('Newsletter Functions', $this -> plugin_name); ?></title>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var wpmlajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		
		var _self = tinyMCEPopup;
		function init () {
			tinyMCEPopup.resizeToInnerSize();
		}
		
		function closePopup() {
			tinyMCEPopup.close();
		}
		
		function insertTag() {
			var tag = "";
			var subscriptionpanel = document.getElementById('subscription_panel');
			var postspanel = document.getElementById('posts_panel');
			var thumbnailpanel = document.getElementById('thumbnail_panel');
			var historypanel = document.getElementById('history_panel');
			var templatepanel = document.getElementById('template_panel');
			
			if (subscriptionpanel.className.indexOf('current') != -1) {
				var list = jQuery('#list').val();
				var lists = jQuery('#lists').val();
				
				if (list == "select") {
					tag += '[newsletters_subscribe list="select"';
					if (lists) { tag += ' lists="' + lists + '"'; }
				} else if (list == "checkboxes") {
					tag += '[newsletters_subscribe list="checkboxes"';
					if (lists) { tag += ' lists="' + lists + '"'; }
				} else {
					tag += '[newsletters_subscribe list="' + list + '"';
				}
				
				tag += ']';
			}
			
			if (postspanel.className.indexOf('current') != -1) {
				var posts_insert = jQuery('input[name=posts_insert]:checked').val();
				
				if (jQuery('input[name="postslanguage"]').length > 0) {
					var postslanguage = jQuery('input[name="postslanguage"]:checked').val();
				} else { var postslanguage = false; }
				
				if (posts_insert == "single") {
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
									
					if (posts_single_id = jQuery('#posts_post_menu').val()) {
						tag += '[newsletters_post post_id="' + posts_single_id + '" showdate="' + post_showdate + '" eftype="' + post_eftype + '"';
						if (postslanguage) { tag += ' language="' + postslanguage + '"'; }
						tag += ']';
					} else {
						alert('<?php _e('Please select a post from the menu.', $this -> plugin_name); ?>');	
					}
				} else if (posts_insert == "multiple") {
					var posts_number = jQuery('#posts_number').val();
					var posts_showdate = jQuery('input[name="posts_showdate"]:checked').val();
					var posts_eftype = jQuery('input[name="posts_eftype"]:checked').val();
					var posts_orderby = jQuery('#posts_orderby').val();
					var posts_order = jQuery('#posts_order').val();
					var posts_categories = jQuery('#posts_categories').val();
					
					tag += '[newsletters_posts numberposts="' + posts_number + '" orderby="' + posts_orderby + '" showdate="' + posts_showdate + '" eftype="' + posts_eftype + '" order="' + posts_order + '" category="' + posts_categories + '"';
					if (postslanguage) { tag += ' language="' + postslanguage + '"'; }
					
					/* Are there custom post type checkboxes? */
					if (jQuery('input[name="posts_types[]"]').length > 0) {
						var posts_types = new Array();
						jQuery('input[name="posts_types[]"]:checked').each(function() {
							posts_types.push(jQuery(this).val());
						});
						
						tag += ' post_type="' + posts_types + '"';
					}
					
					tag += ']';
				}
			}
			
			if (thumbnailpanel.className.indexOf('current') != -1) {
				var thumbnail_post_id = jQuery('#thumbnail_post_id').val();
				var thumbnail_size = jQuery('#thumbnail_size').val();
				tag += '[newsletters_post_thumbnail';
				
				if (thumbnail_post_id != "") {
					tag += ' post_id="' + thumbnail_post_id + '"';
				}
				
				tag += ' size="' + thumbnail_size + '"';
				tag += ']';
			}
			
			if (historypanel.className.indexOf('current') != -1) {
				tag += "[newsletters_history";
				
				if (history_number = jQuery('#history_number').val()) {
					tag += ' number="' + history_number + '"';
				}
				
				if (history_orderby = jQuery('#history_orderby').val()) {
					tag += ' orderby="' + history_orderby + '"';	
				}
				
				if (history_order = jQuery('#history_order').val()) {
					tag += ' order="' + history_order + '"';
				}
				
				if (history_lists = jQuery('#history_lists').val()) {
					if (history_lists != "") {
						tag += ' list_id="' + history_lists + '"';	
					}
				}
				
				tag += "]";
			}
			
			if (templatepanel.className.indexOf('current') != -1) {
				var template_id = jQuery('#template').val();
				if (!template_id) {
					alert('<?php echo __('Choose a snippet', $this -> plugin_name); ?>');	
				} else {
					tag += '[newsletters_template id="' + template_id + '"]';
				}
			}
			
			if (window.tinyMCE && tag != "") {
				window.tinyMCE.execCommand('mceInsertContent', false, tag);
				tinyMCEPopup.editor.execCommand('mceRepaint');
				tinyMCEPopup.close();
			}
			return;
		}	
		
		function posts_changeCategory() {			
			var posts_category_menu = jQuery('#posts_category_menu');
			var posts_post_menu = jQuery('#posts_post_menu');	
			
			if (jQuery('input[name="postslanguage"]').length > 0) {				
				var postslanguage = jQuery('input[name="postslanguage"]:checked').val();
	
				if (postslanguage == "" || postslanguage == "undefined" || postslanguage == undefined) {
					alert('<?php _e('Please choose a language.', $this -> plugin_name); ?>');
					return false;
				}
			}
			
			var post_type = new Array();
			if (jQuery('input[name="post_types[]"]').length > 0) {
				jQuery('input[name="post_types[]"]:checked').each(function() {
					post_type.push(jQuery(this).val());
				});
			} else { post_type.push('post'); }

			jQuery('#posts_multiple_message').show();
			
			jQuery.post(wpmlajaxurl + "?action=newsletters_posts_by_category&cat_id=" + posts_category_menu.val(), {category:posts_category_menu.val(),language:postslanguage,post_type:post_type}, function(response) {
				posts_post_menu.empty().html(response);
				jQuery('#posts_multiple_message').hide();
			});
		}
		</script>
		
		<style type="text/css" media="screen">
			table th { vertical-align: top; }
			.panel_wrapper { border-top: none !important; }
			.panel_wrapper div.current { height: auto !important; }
			#list { width: 200px; }
			label { cursor:pointer; }
		</style>	
	</head>

	<body onload="init(); document.body.style.display = '';">	
		<form onsubmit="insertTag(); return false;" action="#">
        	<div class="tabs">
				<ul>
					<li id="subscription_tab" class="current"><span><a href="javascript:mcTabs.displayTab('subscription_tab','subscription_panel');" onmousedown="return false;"><?php _e('Subscription Form', $this -> plugin_name); ?></a></span></li>
					<li id="posts_tab"><span><a href="javascript:mcTabs.displayTab('posts_tab','posts_panel');" onmousedown="return false;"><?php _e('Posts', $this -> plugin_name); ?></a></span></li>
					<li id="thumbnail_tab"><span><a href="javascript:mcTabs.displayTab('thumbnail_tab','thumbnail_panel');" onmousedown="return false;"><?php _e('Thumbnail', $this -> plugin_name); ?></a></span></li>
					<li id="history_tab"><span><a href="javascript:mcTabs.displayTab('history_tab','history_panel');" onmousedown="return false;"><?php _e('Email History', $this -> plugin_name); ?></a></span></li>
					<li id="template_tab"><span><a href="javascript:mcTabs.displayTab('template_tab','template_panel');" onmousedown="return false;"><?php _e('Template', $this -> plugin_name); ?></a></span></li>
				</ul>
			</div>
        
			<div class="panel_wrapper">
				<div id="subscription_panel" class="panel current">
                	<br/>
                    
                    <p><?php _e('Choose to which list(s) subscribers will be subscribed to.', $this -> plugin_name); ?></p>
                    
					<table cellpadding="4" cellspacing="4" border="0">
						<tbody>
							<tr>
								<td nowrap="nowrap" valign="top"><label for="list"><?php _e('Mailing List', $this -> plugin_name); ?>:</label></td>
								<td>                                															
									<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#listsdiv').show(); } else { jQuery('#listsdiv').hide(); }" class="widefat" style="width:210px;" name="list" id="list">
										<optgroup label="<?php _e('Multiple Choice', $this -> plugin_name); ?>">
											<option value="select"><?php _e('Show Select Drop Down', $this -> plugin_name); ?></option>
											<option value="checkboxes"><?php _e('Show Checkbox List', $this -> plugin_name); ?></option>
										</optgroup>
										
										<?php if ($lists = $Mailinglist -> select($private = true)) : ?>
											<optgroup label="<?php _e('Specific Mailing List', $this -> plugin_name); ?>">
											<?php foreach ($lists as $list_id => $list_title) : ?>
												<option value="<?php echo $list_id; ?>"><?php echo $list_title; ?></option>
											<?php endforeach; ?>
											</optgroup>
										<?php endif; ?>
									</select>
                                    <br/><small><?php _e('You can allow multiple choices or a specific mailinglist.', $this -> plugin_name); ?></small>
								</td>
							</tr>
							<tr id="listsdiv">
								<td nowrap="nowrap" valign="top"><label for="lists"><?php _e('Lists:', $this -> plugin_name); ?></label></td>
								<td>
									<input type="text" name="lists" value="" id="lists" />
									<br/><small><?php _e('Comma separated list IDs to show (eg. 11,3,7), leave empty for all.', $this -> plugin_name); ?></small>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
                
                <!-- WordPress Posts -->
                <div id="posts_panel" class="panel">
                	<br/>
                    <table cellpadding="4" cellspacing="4" border="0">
                    	<tbody>
                        	<?php if ($this -> language_do()) : ?>
                                <tr>
                                    <td nowrap="nowrap" valign="top"><label for=""><?php _e('Language:', $this -> plugin_name); ?></label></td>
                                    <td>
                                        <?php if ($el = $this -> language_getlanguages()) : ?>
                                            <?php foreach ($el as $language) : ?>
                                                <label><input onclick="posts_changeCategory();" type="radio" name="postslanguage" value="<?php echo $language; ?>" id="postslanguage<?php echo $language; ?>" /> <?php echo $this -> language_flag($language); ?></label>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                        
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        	<tr>
                            	<td nowrap="nowrap" valign="top"><label for="posts_insert_single"><?php _e('Insert:', $this -> plugin_name); ?></label></td>
                                <td>
                                	<label><input onclick="jQuery('#posts_insert_single_div').show(); jQuery('#posts_insert_multiple_div').hide();" type="radio" name="posts_insert" value="single" id="posts_insert_single" /> <?php _e('Single Post', $this -> plugin_name); ?></label>
                                    <label><input checked="checked" onclick="jQuery('#posts_insert_single_div').hide(); jQuery('#posts_insert_multiple_div').show();" type="radio" name="posts_insert" value="multiple" id="posts_insert_multiple" /> <?php _e('Multiple Posts', $this -> plugin_name); ?></label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Single Post -->
                    <div id="posts_insert_single_div" style="display:none;">
                    	<table cellpadding="4" cellspacing="4" border="0">
                        	<tbody>
                        		<tr>
                                	<td nowrap="nowrap" valign="top"><label for="post_showdate_Y"><?php _e('Show Date:', $this -> plugin_name); ?></label></td>
                                	<td>
                                		<label><input checked="checked" type="radio" name="post_showdate" value="Y" id="post_showdate_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                                		<label><input type="radio" name="post_showdate" value="N" id="post_showdate_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                                		<br/><small><?php _e('Show the posted date for this post?', $this -> plugin_name); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="post_eftype_excerpt"><?php _e('Full/Excerpt:', $this -> plugin_name); ?></label></td>
                                	<td>
                                		<label><input type="radio" name="post_eftype" value="full" id="post_eftype_full" /> <?php _e('Full', $this -> plugin_name); ?></label>
                                		<label><input checked="checked" type="radio" name="post_eftype" value="excerpt" id="post_eftype_excerpt" /> <?php _e('Excerpt', $this -> plugin_name); ?></label>
                                		<br/><small><?php _e('Insert a full post or the excerpt of a post.', $this -> plugin_name); ?></small>
                                	</td>
                                </tr>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_category_menu"><?php _e('Category:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<?php $select = wp_dropdown_categories(array('show_option_none' => __('- Select Category -', $this -> plugin_name), 'echo' => 0, 'name' => "posts_single_category", 'id' => "posts_category_menu", 'hide_empty' => 0, 'show_count' => 1)); ?>
                                        <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="posts_changeCategory();" style="width:210px;">', $select); ?>
                                        <?php echo $select; ?>
                                        <span id="posts_multiple_message" style="display:none;"><span class="newsletters_loading"></span></span>
                                    </td>
                                </tr>
                                <?php if ($post_types = $this -> get_custom_post_types()) : ?>
                                	<tr>
                                		<td nowrap="nowrap" valign="top"><label for=""><?php _e('Custom Post Types:', $this -> plugin_name); ?></label></td>
                                		<td>
                                			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
                                				<label><input onclick="jQuery('#posts_category_menu').val('-1'); posts_changeCategory();" type="checkbox" name="post_types[]" value="<?php echo $ptypekey; ?>" id="post_types_<?php echo $ptypekey; ?>" /> <?php echo $ptype -> labels -> name; ?></label><br/>
                                			<?php endforeach; ?>
                                			<small><?php _e('Choose the custom post types to take posts from.', $this -> plugin_name); ?></small>
                                		</td>
                                	</tr>
                                <?php endif; ?>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_post_menu"><?php _e('Choose Post:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<select name="posts_single_post" id="posts_post_menu" size="6" style="width:210px;">
                                        
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Multiple Posts -->
                    <div id="posts_insert_multiple_div" style="display:block;">
                    	<table cellpadding="4" cellspacing="4" border="0">
                        	<tbody>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_number"><?php _e('Number of Posts:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<input style="width:45px;" type="text" name="posts_number" value="10" id="posts_number" />
                                    </td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_showdate_Y"><?php _e('Show Date', $this -> plugin_name); ?></label></td>
                                	<td>
                                		<label><input checked="checked" type="radio" name="posts_showdate" value="Y" id="posts_showdate_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                                		<label><input type="radio" name="posts_showdate" value="N" id="posts_showdate_N" /> <?php _e('No', $this -> plugin_name); ?></label>
                                		<br/><small><?php _e('Show the posted date of each post?', $this -> plugin_name); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_eftype_excerpt"><?php _e('Full/Excerpt:', $this -> plugin_name); ?></label></td>
                                	<td>
                                		<label><input type="radio" name="posts_eftype" value="full" id="posts_eftype_full" /> <?php _e('Full', $this -> plugin_name); ?></label>
                                		<label><input checked="checked" type="radio" name="posts_eftype" value="excerpt" id="posts_eftype_excerpt" /> <?php _e('Excerpt', $this -> plugin_name); ?></label>
                                		<br/><small><?php _e('Insert full posts or the excerpt of posts.', $this -> plugin_name); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_orderby"><?php _e('Order By:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<select name="posts_orderby" id="posts_orderby" style="width:210px;">
                                        	<option value="post_date"><?php _e('Date', $this -> plugin_name); ?></option>
                                            <option value="author"><?php _e('Author', $this -> plugin_name); ?></option>
                                            <option value="category"><?php _e('Category', $this -> plugin_name); ?></option>
                                            <option value="content"><?php _e('Post Content', $this -> plugin_name); ?></option>
                                            <option value="ID"><?php _e('Post ID', $this -> plugin_name); ?></option>
                                            <option value="menu_order"><?php _e('Menu Order', $this -> plugin_name); ?></option>
                                            <option value="title"><?php _e('Post Title', $this -> plugin_name); ?></option>
                                            <option value="rand"><?php _e('Random Order', $this -> plugin_name); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_order"><?php _e('Order Direction:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<select style="width:210px;" name="posts_order" id="posts_order">
                                        	<option value="DESC"><?php _e('Descending (new to old/Z to A/Large to Small)', $this -> plugin_name); ?></option>
                                        	<option value="ASC"><?php _e('Ascending (old to new/A to Z/Small to Large)', $this -> plugin_name); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_categories"><?php _e('Posts Category:', $this -> plugin_name); ?></label></td>
                                    <td>
                                    	<?php $select = wp_dropdown_categories(array('show_option_all' => __('- All Categories -', $this -> plugin_name), 'echo' => 0, 'name' => "posts_categories", 'id' => "posts_categories", 'hide_empty' => 0, 'show_count' => 1)); ?>
                                        <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 style="width:210px;">', $select); ?>
                                        <?php echo $select; ?>
                                    </td>
                                </tr>
                                <?php if ($post_types = $this -> get_custom_post_types()) : ?>
                                	<tr>
                                		<td nowrap="nowrap" valign="top"><label for=""><?php _e('Custom Post Types:', $this -> plugin_name); ?></label></td>
                                		<td>
                                			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
                                				<label><input onclick="jQuery('#posts_categories').val('0');" type="checkbox" name="posts_types[]" value="<?php echo $ptypekey; ?>" id="posts_types_<?php echo $ptypekey; ?>" /> <?php echo $ptype -> labels -> name; ?></label><br/>
                                			<?php endforeach; ?>
                                			<small class="howto"><?php _e('Choose the custom post types to take posts from.', $this -> plugin_name); ?></small>
                                		</td>
                                	</tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Featured Image Thumbnail -->
                <div id="thumbnail_panel" class="panel">
                	<br/>
                	<table cellpadding="4" cellspacing="4" border="0">
                		<tbody>
                			<tr>
                				<td nowrap="nowrap" valign="top">
                					<label for="thumbnail_post_id"><?php _e('Post ID:', $this -> plugin_name); ?></label>
                				</td>
                				<td>
                					<input type="text" name="thumbnail_post_id" value="" id="thumbnail_post_id" />
                					<br/>
                					<small class="howto"><?php _e('Featured image post ID. Leave empty for current post.', $this -> plugin_name); ?></small>
                				</td>
                			</tr>
                			<tr>
                				<td nowrap="nowrap" valign="top">
                					<label for="thumbnail_size"><?php _e('Size:', $this -> plugin_name); ?></label>
                				</td>
                				<td>
                					<select name="thumbnail_size" id="thumbnail_size">
                						<option value="thumbnail"><?php _e('Thumbnail', $this -> plugin_name); ?></option>
                						<option value="medium"><?php _e('Medium', $this -> plugin_name); ?></option>
                						<option value="large"><?php _e('Large', $this -> plugin_name); ?></option>
                						<option value="full"><?php _e('Full', $this -> plugin_name); ?></option>
                					</select>
                					<br/>
                					<small class="howto"><?php _e('Preferred size of the image.', $this -> plugin_name); ?>
                				</td>
                			</tr>
                		</tbody>
                	</table>
                </div>
                
                <!-- Email History -->
                <div id="history_panel" class="panel">
                	<br/>
                	<table cellpadding="4" cellspacing="4" border="0">
                    	<tbody>
                        	<tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_number"><?php _e('Number of Emails:', $this -> plugin_name); ?></label></td>
                                <td>
                                	<input style="width:45px;" type="text" name="history_number" value="" id="history_number" /><br/>
                                    <span class="howto"><?php _e('Leave empty for unlimited/all emails.', $this -> plugin_name); ?></span>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_orderby"><?php _e('Order By:', $this -> plugin_name); ?></label></td>
                                <td>
                                	<select name="history_orderby" id="history_orderby" style="width:210px;">
                                    	<option value="modified"><?php _e('Date', $this -> plugin_name); ?></option>
                                        <option value="subject"><?php _e('Email Subject', $this -> plugin_name); ?></option>
                                        <option value="sent"><?php _e('Times Sent', $this -> plugin_name); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_order"><?php _e('Order Direction:', $this -> plugin_name); ?></label></td>
                                <td>
                                	<select style="width:210px;" name="history_order" id="history_order">
                                    	<option value="DESC"><?php _e('Descending (new to old/Z to A/Large to Small)', $this -> plugin_name); ?></option>
                                        <option value="ASC"><?php _e('Ascending (old to new/A to Z/Small to Large)', $this -> plugin_name); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_lists"><?php _e('Mailing List(s):', "wp-mailingliist"); ?></label></td>
                                <td>
                                	<select name="history_lists" id="history_lists" multiple="multiple">
                                    	<option value=""><?php _e('- All Mailing Lists -', $this -> plugin_name); ?></option>
                                        <?php if ($mailinglists = $Mailinglist -> select()) : ?>
                                        	<?php foreach ($mailinglists as $list_id => $list_title) : ?>
                                            	<option value="<?php echo $list_id; ?>"><?php echo $list_title; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <br/><span class="howto"><?php _e('You can select multiple mailing lists by holding CTRL key.', $this -> plugin_name); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Template -->
				<div id="template_panel" class="panel">
					<br/>
					<table cellpadding="4" cellspacing="4" border="0">
						<tbody>
							<tr>
								<td nowrap="nowrap" valign="top"><label for="template"><?php _e('Snippet:', $this -> plugin_name); ?></label></td>
								<td>
									<?php
									
									$templatesquery = "SELECT * FROM " . $wpdb -> prefix . $Template -> table . " ORDER BY title ASC";
									$templates = $wpdb -> get_results($templatesquery);
									
									?>
									<?php if (!empty($templates)) : ?>
										<select name="template" id="template">
											<option value=""><?php _e('- Select Template -', $this -> plugin_name); ?></option>
											<?php foreach ($templates as $template) : ?>
												<option value="<?php echo $template -> id; ?>"><?php echo $template -> title; ?></option>
											<?php endforeach; ?>
										</select>
									<?php else : ?>
									
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="mceActionPanel">
				<div style="float:left;">
					<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="closePopup();"/>
				</div>
		
				<div style="float:right;">
					<input type="button" id="insert" name="insert" value="{#insert}" onclick="insertTag();" />
				</div>
			</div>
		</form>
	</body>
</html>