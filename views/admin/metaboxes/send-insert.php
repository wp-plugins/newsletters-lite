<?php

$inserttabs = array('fields' => __('Fields', $this -> plugin_name), 'posts' => __('Posts', $this -> plugin_name), 'snippets' => __('Snippets', $this -> plugin_name));
$inserttabs = apply_filters($this -> pre . '_admin_createnewsletter_inserttabs', $inserttabs);

?>

<div id="inserttabs">
	<ul>
		<?php if (!empty($inserttabs['fields'])) : ?><li><a href="#inserttabs-1"><?php _e('Fields', $this -> plugin_name); ?></a></li><?php endif; ?>
		<?php if (!empty($inserttabs['posts'])) : ?><li><a href="#inserttabs-2"><?php _e('Posts', $this -> plugin_name); ?></a></li><?php endif; ?>
		<?php if (!empty($inserttabs['snippets'])) : ?><li><a href="#inserttabs-3"><?php echo apply_filters('newsletters_admin_tabtitle_createnewsletter_insertsnippets', __('Snippets', $this -> plugin_name)); ?></a></li><?php endif; ?>
	</ul>
	
	<?php if (!empty($inserttabs['fields'])) : ?>
		<div id="inserttabs-1">
			<h4><?php _e('Insert Custom Fields', $this -> plugin_name); ?> <?php echo $Html -> help(__('Below are all custom fields for your subscribers. Click on the custom field that you want to insert into the newsletter and the shortcode will be replaced with the value for each respective subscriber as the newsletter is sent. You can use this to personalize your newsletters.', $this -> plugin_name)); ?></h4>
			
			<?php $Db -> model = $Field -> model; ?>
	        <?php $fields = $Db -> find_all(false, array('id', 'title', 'slug'), array('title', "ASC")); ?>
	        <?php if (!empty($fields)) : ?>
	        	<ul class="insertfieldslist">
		            <?php foreach ($fields as $field) : ?>
		            	<li>
		            		<a href="" class="press button button-secondary" onclick='wpml_tinymcetag("[<?php echo $this -> pre; ?>field name=\"<?php echo $field -> slug; ?>\"]"); return false;'><?php echo __($field -> title); ?></a>
		            	</li>
		            <?php endforeach; ?>
	        	</ul>
	        <?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if (!empty($inserttabs['posts'])) : ?>
		<div id="inserttabs-2">
			<h4><?php _e('Insert Posts', $this -> plugin_name); ?> <?php echo $Html -> help(__('Insert single posts, multiple posts and post featured images into your newsletter as needed. Follow the selections below to make the posts available and then click to insert.', $this -> plugin_name)); ?></h4>
		
			<p>
				<label><input type="radio" name="ptype" checked="checked" value="single" id="ptype_single" /> <?php _e('Single', $this -> plugin_name); ?></label>
				<label><input type="radio" name="ptype" value="page" id="ptype_page" /> <?php _e('Page', $this -> plugin_name); ?></label>
				<br/>
				<label><input type="radio" name="ptype" value="multiple" id="ptype_multiple" /> <?php _e('Multiple', $this -> plugin_name); ?></label>
				<label><input type="radio" name="ptype" value="thumbnail" id="ptype_thumbnail" /> <?php _e('Thumbnail', $this -> plugin_name); ?></label>
			</p>
			
			<div id="ptypeglobal" style="display:block;">
				<?php if ($this -> is_plugin_active('qtranslate')) : ?>
					<label for=""><?php _e('Language:', $this -> plugin_name); ?></label>
		        	<?php if ($el = $this -> language_getlanguages()) : ?>
		                <?php foreach ($el as $language) : ?>
		                    <label><input <?php echo ($language == $this -> language_default()) ? 'checked="checked"' : ''; ?> onclick="get_posts();" type="radio" name="postslanguage" value="<?php echo $language; ?>" id="postslanguage<?php echo $language; ?>" /> <?php echo $this -> language_flag($language); ?></label>
		                <?php endforeach; ?>
		            <?php else : ?>
		            
		            <?php endif; ?>
		            <?php echo $Html -> help(__('Since you are using multilingual, choose the language of the post(s) that you want to use in the newsletter.', $this -> plugin_name)); ?>
		        <?php endif; ?>
		        
		        <p>
					<label for="post_showdate_Y"><?php _e('Show Date:', $this -> plugin_name); ?></label>
					<label><input type="radio" name="post_showdate" value="Y" id="post_showdate_Y" checked="checked" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input type="radio" name="post_showdate" value="N" id="post_showdate_N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('Choose whether or not to show the published date of the post.', $this -> plugin_name)); ?>
				</p>
				
				<p>
					<label for="post_eftype_excerpt"><?php _e('Display:', $this -> plugin_name); ?></label>
					<label><input type="radio" name="post_eftype" value="full" id="post_eftype_full" /> <?php _e('Full', $this -> plugin_name); ?></label>
					<label><input type="radio" name="post_eftype" value="excerpt" id="post_eftype_excerpt" checked="checked" /> <?php _e('Excerpt/Short', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('Do you want to display the full post or an excerpt of the post? Note that the excerpt is a short version of the first few characters of the post and all HTML will be stripped from it.', $this -> plugin_name)); ?>
				</p>
			</div>
			
			<div id="ptypediv_single" style="display:block;">
				<?php if ($posttypes = $this -> get_custom_post_types(true)) : ?>
					<p>
						<label for="posttype"><?php _e('Post Type:', $this -> plugin_name); ?></label><br/>
						<select style="max-width:200px;" onchange="change_posttype(this.value)" name="posttype" id="posttype">
							<option value="post"><?php _e('Post', $this -> plugin_name); ?></option>
							<?php foreach ($posttypes as $posttypekey => $posttype) : ?>
								<option value="<?php echo $posttypekey; ?>"><?php echo $posttype -> labels -> name; ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo $Html -> help(__('Since you have custom post types available, this menu is showing. Choose the post type to fetch posts from.', $this -> plugin_name)); ?>
					</p>
				<?php else : ?>
					<input type="hidden" id="posttype" name="posttype" value="post" />
				<?php endif; ?>
				
				<div id="posttype_post" style="display:block">
					<p>
						<label for="posts_category_menu"><?php _e('Post Category:', $this -> plugin_name); ?></label><br/>
						<?php $select = wp_dropdown_categories(array('show_option_none' => __('- Select Category -', $this -> plugin_name), 'echo' => 0, 'name' => "posts_single_category", 'id' => "posts_category_menu", 'hide_empty' => 0, 'show_count' => 1)); ?>
		                <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="get_posts();" style="max-width:200px;">', $select); ?>
		                <?php echo $select; ?>
		                <?php echo $Html -> help(__('Select a post category to narrow down posts by category for easier selection.', $this -> plugin_name)); ?>
					</p>
				</div>
				
				<span id="postsloading" style="display:none;"><span class="newsletters_loading"></span></span>
				
				<div id="postsdiv" style="display:none;">
					<p>
						<label for=""><?php _e('Choose Post:', $this -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Click on a post below to insert it into your newsletter or use the checkboxes (you can select a range by holding Shift and clicking) to tick multiple posts and then click the "Insert Selected" button to insert all the selected posts.', $this -> plugin_name)); ?>
						<div id="ajaxposts">
							<span class="howto"><?php _e('Choose all settings above.', $this -> plugin_name); ?></span>
						</div>
					</p>
				</div>
			</div>
			
			<div id="ptypediv_page" style="display:none;">
				<p>
					<label for="page_id"><?php _e('Page:', $this -> plugin_name); ?></label>
					<?php wp_dropdown_pages(array('depth' => 0, 'child_of' => 0, 'echo' => 1, 'name' => "page_id", 'show_option_none' => false)); ?>
				</p>
				
				<input type="button" class="button button-secondary" onclick="insert_post(jQuery('#page_id').val(), false);" name="insertpage" value="<?php _e('Insert Page', $this -> plugin_name); ?>" />
			</div>
			
			<div id="ptypediv_multiple" style="display:none;">
				<p>
					<label for=""><?php _e('Number:', $this -> plugin_name); ?></label>
					<input type="text" name="posts_number" value="10" id="posts_number" class="widefat" style="width:45px;" />
					<?php echo $Html -> help(__('Number', $this -> plugin_name)); ?>
				</p>
				
				<p>
					<label for=""><?php _e('Order:', $this -> plugin_name); ?></label><br/>
					<select name="posts_orderby" id="posts_orderby" style="max-width:100px;">
	                	<option value="post_date"><?php _e('Date', $this -> plugin_name); ?></option>
	                    <option value="author"><?php _e('Author', $this -> plugin_name); ?></option>
	                    <option value="category"><?php _e('Category', $this -> plugin_name); ?></option>
	                    <option value="content"><?php _e('Post Content', $this -> plugin_name); ?></option>
	                    <option value="ID"><?php _e('Post ID', $this -> plugin_name); ?></option>
	                    <option value="menu_order"><?php _e('Menu Order', $this -> plugin_name); ?></option>
	                    <option value="title"><?php _e('Post Title', $this -> plugin_name); ?></option>
	                    <option value="rand"><?php _e('Random Order', $this -> plugin_name); ?></option>
	                </select>
	                <select name="posts_order" id="posts_order" style="max-width:100px;">
	                	<option value="ASC"><?php _e('Ascending', $this -> plugin_name); ?></option>
	                	<option value="DESC"><?php _e('Descending', $this -> plugin_name); ?></option>
	                </select>
	                <?php echo $Html -> help(__('Order', $this -> plugin_name)); ?>
				</p>
				
				<p>
					<label for=""><?php _e('Posts Category:', $this -> plugin_name); ?></label><br/>
					<?php $select = wp_dropdown_categories(array('show_option_all' => __('- All Categories -', $this -> plugin_name), 'echo' => 0, 'name' => "posts_categories", 'id' => "posts_categories", 'hide_empty' => 0, 'show_count' => 1)); ?>
	                <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="change_category();" style="max-width:200px;">', $select); ?>
	                <?php echo $select; ?>
				</p>
				
				<p>
					<label for=""><?php _e('Post Types:', $this -> plugin_name); ?></label>
					<?php if ($post_types = $this -> get_custom_post_types()) : ?>
		            	<ul>
		        			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
		        				<label><input onclick="jQuery('#posts_categories').val('0');" type="checkbox" name="posts_types[]" value="<?php echo $ptypekey; ?>" id="posts_types_<?php echo $ptypekey; ?>" /> <?php echo $ptype -> labels -> name; ?></label><br/>
		        			<?php endforeach; ?>
		            	</ul>
		            <?php endif; ?>
				</p>
				
				<input onclick="insert_post(false, false);" type="button" name="insertmultiple" class="button button-secondary" value="<?php _e('Insert Posts', $this -> plugin_name); ?>" />
			</div>
			
			<div id="ptypediv_thumbnail" style="display:none;">
				<p>
					<label for="thumbnail_post_id"><?php _e('Post ID:', $this -> plugin_name); ?></label>
					<input type="text" class="widefat" style="width:65px;" name="thumbnail_post_id" id="thumbnail_post_id" value="" />
					<?php echo $Html -> help(__('Specify the ID of the post', $this -> plugin_name)); ?>
				</p>
				
				<p>
					<label for="thumbnail_size"><?php _e('Thumbnail Size:', $this -> plugin_name); ?></label><br/>
					<select name="thumbnail_size" id="thumbnail_size">
						<option value="thumbnail"><?php _e('Thumbnail', $this -> plugin_name); ?></option>
						<option value="medium"><?php _e('Medium', $this -> plugin_name); ?></option>
						<option value="large"><?php _e('Large', $this -> plugin_name); ?></option>
						<option value="full"><?php _e('Full', $this -> plugin_name); ?></option>
					</select>
				</p>
				
				<input type="button" class="button button-secondary" onclick="insert_post(false, false);" name="insertthumbnail" value="<?php _e('Insert Thumbnail', $this -> plugin_name); ?>" />
			</div>
			
			<script type="text/javascript">	
			jQuery('input[name="posts_types[]"]').shiftClick();
			
			jQuery('input[name="ptype"]').click(function() {
				var ptype = jQuery(this).val();
				jQuery('div[id^="ptypediv"]').hide();
				jQuery('#ptypediv_' + ptype).show();
				if (ptype == "thumbnail") { jQuery('#ptypeglobal').hide(); }
				else { jQuery('#ptypeglobal').show(); }
			});
								
			function insert_single_multiple() {
				var multishortcode = "";
				
				jQuery('input[name="insertposts[]"]:checked').each(function() {
					var post_id = jQuery(this).val();
					multishortcode += insert_post(post_id, true) + "<br/>";
				});
				
				wpml_tinymcetag(multishortcode);
			}
			
			function insert_post(post_id, returnshortcode) {
				var ptype = jQuery('input[name="ptype"]:checked').val();
				if (jQuery('input[name="postslanguage"]').length > 0) { var postslanguage = jQuery('input[name="postslanguage"]:checked').val(); } 
				else { var postslanguage = false; }
				
				if (ptype == "single") {
					var shortcode = "";
					shortcode += '[newsletters_post post_id="' + post_id + '"';					
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
					shortcode += ' showdate="' + post_showdate + '"';
					shortcode += ' eftype="' + post_eftype + '"';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ']';	
				} else if (ptype == "page") {
					var page_id = post_id;
					var shortcode = "";
					shortcode += '[newsletters_post post_id="' + page_id + '"';					
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
					shortcode += ' showdate="' + post_showdate + '"';
					shortcode += ' eftype="' + post_eftype + '"';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ']';
				} else if (ptype == "multiple") {
					var shortcode = "";
					shortcode += '[newsletters_posts';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ' numberposts="' + jQuery('#posts_number').val() + '"';
					shortcode += ' showdate="' + jQuery('input[name="post_showdate"]:checked').val() + '"';
					shortcode += ' eftype="' + jQuery('input[name="post_eftype"]:checked').val() + '"';
					shortcode += ' orderby="' + jQuery('#posts_orderby').val() + '"';
					shortcode += ' order="' + jQuery('#posts_order').val() + '"';
					shortcode += ' category="' + jQuery('#posts_categories').val() + '"';
					if (jQuery('input[name="posts_types[]"]').length > 0) {
						var posts_types = new Array();
						jQuery('input[name="posts_types[]"]:checked').each(function() {
							posts_types.push(jQuery(this).val());
						});
						
						if (posts_types != "") { 
							shortcode += ' post_type="' + posts_types + '"';
						}
					}
					shortcode += ']';
				} else if (ptype == "thumbnail") {
					var shortcode = "";
					var thumbnail_post_id = jQuery('#thumbnail_post_id').val();
					var thumbnail_size = jQuery('#thumbnail_size').val();
					shortcode += '[newsletters_post_thumbnail';
					
					if (thumbnail_post_id != "") {
						shortcode += ' post_id="' + thumbnail_post_id + '"';
					}
					
					shortcode += ' size="' + thumbnail_size + '"';
					shortcode += ']';
				}
				
				if (returnshortcode == true) {
					return shortcode;
				} else {
					wpml_tinymcetag(shortcode);
				}
			}
			
			function change_category() {
				jQuery('input[name="posts_types[]"]:checked').attr('checked', false);
			}
			
			function change_posttype(posttype) {
				jQuery('div[id^="posttype"]').hide();
				jQuery('div#posttype_' + posttype).show();
				
				if (posttype != "post") { 
					jQuery('#posts_category_menu').val('-1');
					get_posts(); 
				}
			}
			
			function get_posts() {
				jQuery('#postsdiv').hide();
				jQuery('#postsloading').show();
					
				var arguments = {
					language: jQuery('input[name="postslanguage"]:checked').val(),
					posttype: jQuery('#posttype').val(),
					category: jQuery('#posts_category_menu').val(),	
				};
			
				jQuery.ajax(wpmlajaxurl + '?action=wpmlgetposts', {
					type: 'POST',
					data: arguments,
					success: function(response) {
						jQuery('#postsloading').hide();
						
						if (response != "") { 
							jQuery('#postsdiv').show();
							jQuery('#ajaxposts').html(response); 
						}
						
						jQuery('input[name="insertposts[]"]').shiftClick();
					}
				});
			}
			</script>
		</div>
	<?php endif; ?>
	<?php if (!empty($inserttabs['snippets'])) : ?>
		<div id="inserttabs-3">
			<h4><?php echo apply_filters('newsletters_admin_tabheading_createnewsletter_insertsnippets', __('Insert Snippets', $this -> plugin_name)); ?> <?php echo $Html -> help(apply_filters('newsletters_admin_tooltip_createnewsletter_insertsnippets', __('Below are all your email snippets. Click on the snippet to insert it into the newsletter and the shortcode will be replaced with the content of the email snippet. Alternatively click "Load into Editor" to load the email snippet into the editor in full.', $this -> plugin_name))); ?></h4>
			
			<?php $Db -> model = $Template -> model; ?>
			<?php if ($templates = $Db -> find_all(false, array('id', 'title'), array('title', "ASC"))) : ?>
				<?php $templates = apply_filters('newsletters_admin_createnewsletter_snippets', $templates); ?>
				<ul class="insertfieldslist">
					<?php foreach ($templates as $template) : ?>
						<li>
							<a href="<?php echo apply_filters($this -> pre . '_admin_createnewsletter_snippetbuttonhref', "", $template); ?>" class="press button button-secondary" onclick='<?php echo apply_filters($this -> pre . '_admin_createnewsletter_snippetbuttononclick', 'wpml_tinymcetag("[' . $this -> pre . 'snippet id=\"' . $template -> id . '\"]"); return false;', $template); ?>'><?php echo __($template -> title); ?></a>
							<?php if (apply_filters($this -> pre . '_admin_createnewsletter_loadintoeditorlinks', true)) : ?><small><a href="?page=<?php echo $this -> sections -> send; ?>&method=template&id=<?php echo $template -> id; ?>" class=""><?php _e('Load into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="<?php echo $this -> pre; ?>error"><?php _e('No email snippets available.', $this -> plugin_name); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	if (jQuery.isFunction(jQuery.fn.cookie)) {
		var inserttabscookieid = jQuery.cookie('inserttabscookie') || 0;
	}
		
	jQuery('#inserttabs').tabs({
		active:inserttabscookieid,
		activate: function(event, ui) {
			if (jQuery.isFunction(jQuery.fn.cookie)) {
				jQuery.cookie("inserttabscookie", ui.newTab.index(), {expires:365, path:'/'});
			}
		}
	});
});
</script>