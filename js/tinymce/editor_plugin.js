/**
 * Newsletters TinyMCE Plugin
 * @author Tribulant Software
 */

(function() {
	tinymce.PluginManager.add('Newsletters', function(editor, url) {
	
		var self = this, post_element;
	
		function post_change_category(category_id) {			
			tinyMCE.activeEditor.plugins.Newsletters.refresh([{text:'loading...', value:false}]);
			
			jQuery.post(ajaxurl + '?action=newsletters_posts_by_category&cat_id=' + category_id, {category:category_id}, function(response) {
				tinyMCE.activeEditor.plugins.Newsletters.refresh(response);
			});
			
			return false;
		}
		
		self.refresh = function(values) {
			post_element.menu = null;
			post_element.settings.menu = values;
			post_element.value(values[0]['value']);
			post_element.focus();
		}
	
		editor.addButton( 'Newsletters', {
			icon: 'newsletters',
			type: 'menubutton',
			menu: [{
					text: "Anchor Link",
					onclick: function() {
						editor.windowManager.open({
							title: 'Insert Email Anchor',
							body: [{
								type: 'textbox',
								name: 'newsletters_anchor_text',
								label: 'Anchor Name',
								tooltip: 'Inserts an anchor link with this value as the "name" attribute eg. mynameattribute. You can then link to the anchor with hash eg. #mynameattribute'
							}],
							onsubmit: function(e) {
								if (e.data.newsletters_anchor_text.length > 0) {
									editor.insertContent('<a name="' + e.data.newsletters_anchor_text + '"></a>');
								} else {
									alert('Fill in a "name" attribute to use');
									return false;
								}
							}
						});
					}
				}, {
					text: "Subscribe Form",
					onclick: function() {
						var mailinglists = [{text:'MULTI - Select Drop Down', value:'select'}, {text:'MULTI - Checkboxes List', value:'checkboxes'}];
						var index;
						for (index = 0; index < tinymce.settings.newsletters_mailinglists_list.length; index++) {
							mailinglists.push(tinymce.settings.newsletters_mailinglists_list[index]);
						}
					
						editor.windowManager.open({
							title: 'Insert Subscribe Form',
							body: [{
								type: 'listbox',
								name: 'newsletters_subscribe_list',
								label: 'Mailing List(s)',
								values: mailinglists,
								tooltip: 'Either multiple (select drop down or checkboxes list) or a specific mailing list.',
								onSelect: function() {
									if (this.value() == "select" || this.value() == "checkboxes") {
										subscribeinclude_element.show();
									} else {
										subscribeinclude_element.hide();
									}
								}
							}, {
								type: 'textbox',
								name: 'newsletters_subscribe_include',
								label: 'Include',
								tooltip: 'Optional. When using multiple, you can specify a comma separated list of mailing list IDs to show',
								onPostRender: function() {
									subscribeinclude_element = this;
								}
							}],
							onsubmit: function(e) {
								var newsletters_subscribe = '[newsletters_subscribe';
								if (e.data.newsletters_subscribe_include.length > 0) {
									newsletters_subscribe += ' lists="' + e.data.newsletters_subscribe_include + '"';
								}
								newsletters_subscribe += ' list="' + e.data.newsletters_subscribe_list + '"';
								newsletters_subscribe += ']';
								editor.insertContent(newsletters_subscribe);
							}
						});
					}
				}, {
					text: "Single Post",
					onclick: function() {
						var newsletters_post_body = [];
						
						if (typeof(tinymce.settings.newsletters_languages) !== 'undefined' && tinymce.settings.newsletters_languages.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_language',
								label: 'Language',
								values: tinymce.settings.newsletters_languages,
								tooltip: 'Choose the language of the post to use'
							});
						}
						
						newsletters_post_body.push({
							type: 'checkbox',
							name: 'newsletters_post_showdate',
							label: 'Show Date',
							text: 'Yes, show the post date',
							tooltip: 'Choose whether or not to show the date of the post'
						});
						
						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_eftype',
							label: 'Type',
							values: [{text:'Excerpt', value:'excerpt'}, {text:'Full Post', value:'full'}],
							tooltip: 'Either full post or excerpt'
						});
						
						if (tinymce.settings.newsletters_thumbnail_sizes.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_thumbnail_size',
								label: 'Thumbnail Size',
								values: tinymce.settings.newsletters_thumbnail_sizes,
								tooltip: 'Choose the size of the thumbnail'
							});
						}
						
						if (tinymce.settings.newsletters_thumbnail_align.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_thumbnail_align',
								label: 'Thumbnail Align',
								values: tinymce.settings.newsletters_thumbnail_align,
								tooltip: 'Choose the alignment of the thumbnail'
							});
						}
						
						newsletters_post_body.push({
							type: 'textbox',
							name: 'newsletters_post_thumbnail_hspace',
							label: 'Thumbnail Space',
							value: '15',
							tooltip: 'The spacing of the thumbnail',
						});
						
						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_category',
							label: 'Category',
							values: tinymce.settings.newsletters_post_categories,
							tooltip: 'Choose a category to populate the posts below in order to choose a post',
							onSelect: function(e) {
								post_change_category(this.value());
								this.value(null);
							}
						});
						
						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_id',
							label: 'Post',
							values: [{text:'- Choose Category Above -', value:false}],
							tooltip: 'First choose a category above, then choose the post to insert',
							onPostRender: function() {
								post_element = this;
							}
						});
					
						editor.windowManager.open({
							title: 'Insert Single Post',
							body: newsletters_post_body,
							onsubmit: function(e) {
								var newsletters_post = '[newsletters_post';
								
								if (e.data.newsletters_post_showdate == true) {
									newsletters_post += ' showdate="Y"';
								} else {
									newsletters_post += ' showdate="N"';
								}
								
								newsletters_post += ' eftype="' + e.data.newsletters_post_eftype + '"';
								newsletters_post += ' post_id="' + e.data.newsletters_post_id + '"';
								newsletters_post += ' thumbnail_size="' + e.data.newsletters_post_thumbnail_size + '"';
								newsletters_post += ' thumbnail_align="' + e.data.newsletters_post_thumbnail_align + '"';
								newsletters_post += ' thumbnail_hspace="' + e.data.newsletters_post_thumbnail_hspace + '"';
								
								if (e.data.newsletters_post_id == false || e.data.newsletters_post_id.length <= 0) {
									alert('Choose a post');
									return false;
								}
								
								if (typeof(e.data.newsletters_post_language) !== 'undefined' && e.data.newsletters_post_language.length > 0) {
									newsletters_post += ' language="' + e.data.newsletters_post_language + '"';
								}
								
								newsletters_post += ']';
								editor.insertContent(newsletters_post);
							}
						});
					}
				}, {
					text: "Multiple Posts",
					onclick: function() {
						var newsletters_posts_body = [];
						
						if (typeof(tinymce.settings.newsletters_languages) !== 'undefined' && tinymce.settings.newsletters_languages.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_language',
								label: 'Language',
								values: tinymce.settings.newsletters_languages,
								tooltip: 'Choose the language of the posts to use'
							});
						}
						
						newsletters_posts_body.push({
							type: 'textbox',
							name: 'newsletters_posts_number',
							label: 'Number',
							tooltip: 'Optional. Choose the number of posts to show'
						});
						
						newsletters_posts_body.push({
							type: 'checkbox',
							name: 'newsletters_posts_showdate',
							label: 'Show Date',
							text: 'Yes, show the post date',
							tooltip: 'Choose whether or not to show the date of the post'
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_eftype',
							label: 'Type',
							values: [{text:'Excerpt', value:'excerpt'}, {text:'Full Post', value:'full'}],
							tooltip: 'Either full post or excerpt'
						});

						if (tinymce.settings.newsletters_thumbnail_sizes.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_thumbnail_size',
								label: 'Thumbnail Size',
								values: tinymce.settings.newsletters_thumbnail_sizes,
								tooltip: 'Choose the size of the thumbnail'
							});
						}
						
						if (tinymce.settings.newsletters_thumbnail_align.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_thumbnail_align',
								label: 'Thumbnail Align',
								values: tinymce.settings.newsletters_thumbnail_align,
								tooltip: 'Choose the alignment of the thumbnail'
							});
						}
						
						newsletters_posts_body.push({
							type: 'textbox',
							name: 'newsletters_posts_thumbnail_hspace',
							label: 'Thumbnail Space',
							value: '15',
							tooltip: 'The spacing of the thumbnail',
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_orderby',
							label: 'Order By',
							values: [{text:'Date', value:'post_date'}, 
									{text:'Author', value:'author'}, 
									{text:'Category', value:'category'}, 
									{text:'Post Content', value:'content'}, 
									{text:'Post ID', value:'ID'}, 
									{text:'Menu Order', value:'menu_order'},
									{text:'Title', value:'title'},
									{text:'Random Order', value:'rand'}],
							tooltip: 'Choose by what value posts should be ordered'
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_order',
							label: 'Order',
							values: [{text:'Ascending', value:'ASC'}, {text:'Descending', value:'DESC'}],
							tooltip: 'Choose in what direction posts should be ordered'
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_category',
							label: 'Category',
							values: tinymce.settings.newsletters_post_categories,
							tooltip: 'Choose the category to take posts from'
						});
						
						if (tinymce.settings.newsletters_post_types.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_posttype',
								label: 'Post Type',
								values: tinymce.settings.newsletters_post_types,
								tooltip: 'Optional. Choose a custom post type to take posts from'
							});
						}
					
						editor.windowManager.open({
							title: 'Insert Multiple Posts',
							body: newsletters_posts_body,
							onsubmit: function(e) {								
								var newsletters_posts = '[newsletters_posts';
								
								if (typeof(e.data.newsletters_posts_language) !== 'undefined' && e.data.newsletters_posts_language.length > 0) {
									newsletters_posts += ' language="' + e.data.newsletters_posts_language + '"';
								}
								
								if (e.data.newsletters_posts_number.length > 0) {
									newsletters_posts += ' numberposts="' + e.data.newsletters_posts_number + '"';
								}
								
								if (e.data.newsletters_posts_showdate == true) {
									newsletters_posts += ' showdate="Y"';
								} else {
									newsletters_posts += ' showdate="N"';
								}
								
								newsletters_posts += ' orderby="' + e.data.newsletters_posts_orderby + '"';
								newsletters_posts += ' order="' + e.data.newsletters_posts_order + '"';
								newsletters_posts += ' category="' + e.data.newsletters_posts_category + '"';
								newsletters_posts += ' thumbnail_size="' + e.data.newsletters_posts_thumbnail_size + '"';
								newsletters_posts += ' thumbnail_align="' + e.data.newsletters_posts_thumbnail_align + '"';
								newsletters_posts += ' thumbnail_hspace="' + e.data.newsletters_posts_thumbnail_hspace + '"';
								
								if (e.data.newsletters_posts_category == false || e.data.newsletters_posts_category.length <= 0) {
									alert('Choose a category');
									return false;
								}
								
								if (tinymce.settings.newsletters_post_types.length > 0) {
									if (e.data.newsletters_posts_posttype.length > 0) {
										newsletters_posts += ' post_type="' + e.data.newsletters_posts_posttype + '"';
									}
								}
								
								newsletters_posts += ']';
								editor.insertContent(newsletters_posts);
							}
						});
					}
				}, {
					text: "Featured Image",
					onclick: function() {
						editor.windowManager.open({
							title: 'Insert Featured Image',
							body: [{
								type: 'textbox',
								name: 'newsletters_thumbnail_postid',
								label: 'Post ID',
								value: tinymce.settings.newsletters_post_id,
								tooltip: 'Specify the ID of the post to take the featured image from'
							}, {
								type: 'listbox',
								name: 'newsletters_thumbnail_size',
								label: 'Size',
								values: [{text:'Thumbnail', value:'thumbnail'}, {text:'Medium', value:'medium'}, {text:'Large', value:'large'}, {text:'Full', value:'full'}],
								tooltip: 'Choose the size of the image to show. Sizes can be configured under Settings > Media in your dashboard'
							}],
							onsubmit: function(e) {
								if (e.data.newsletters_thumbnail_size.length > 0) {
									var newsletters_thumbnail = '[newsletters_post_thumbnail';
									
									if (e.data.newsletters_thumbnail_postid.length > 0) {
										newsletters_thumbnail += ' post_id="' + e.data.newsletters_thumbnail_postid + '"';
									}
									
									newsletters_thumbnail += ' size="' + e.data.newsletters_thumbnail_size + '"';
									newsletters_thumbnail += ']';
									
									editor.insertContent(newsletters_thumbnail);
								} else {
									alert('Please choose an image size');
									return false;
								}
							}
						});
					}
				}, {
					text: "Email History",
					onclick: function() {
						var newsletters_history_body = [{
							type: 'textbox',
							name: 'newsletters_history_number',
							label: 'Number',
							tooltip: 'Specify the number of emails/newsletters to display. Leave empty for all.'
						}, {
							type: 'listbox',
							name: 'newsletters_history_orderby',
							label: 'Order By',
							values: [{text:'Date', value:'modified'}, {text:'Subject', value:'subject'}, {text:'Times Sent', value:'sent'}]
						}, {
							type: 'listbox',
							name: 'newsletters_history_order',
							label: 'Order',
							values: [{text:'Descending (new to old/Z to A/Large to Small)', value:'DESC'}, {text:'Ascending (old to new/A to Z/Small to Large)', value:'ASC'}]
						}, {
							type: 'textbox',
							name: 'newsletters_history_lists',
							label: 'Mailing List(s)',
							values: tinymce.settings.newsletters_mailinglists_list,
							tooltip: 'Leave empty for all else fill in comma separated list IDs'
						}];
					
						editor.windowManager.open({
							title: 'Insert Email History',
							body: newsletters_history_body,
							onsubmit: function(e) {
								var newsletters_history = '[newsletters_history';
								
								if (e.data.newsletters_history_number.length > 0) {
									newsletters_history += ' number="' + e.data.newsletters_history_number + '"';
								}
								
								if (e.data.newsletters_history_order.length > 0 && e.data.newsletters_history_orderby.length > 0) {
									newsletters_history += ' order="' + e.data.newsletters_history_order + '"';
									newsletters_history += ' orderby="' + e.data.newsletters_history_orderby + '"';
								}
								
								if (e.data.newsletters_history_lists.length > 0) {
									newsletters_history += ' list_id="' + e.data.newsletters_history_lists + '"';
								}
								
								newsletters_history += ']';
							
								editor.insertContent(newsletters_history);
							}
						});
					}
				}, {
					text: "Snippet",
					onclick: function() {
						editor.windowManager.open({
							title: 'Insert Email Snippet',
							width: 350,
							height: 75,
							body: [{
								type: 'listbox',
                                name: 'newsletters_snippet_list',
                                label: 'Snippet',
                                values: tinymce.settings.newsletters_snippet_list,
                                tooltip: 'Choose the snippet to insert'
							}],
							onsubmit: function(e) {
								editor.insertContent('[newsletters_snippet id="' + e.data.newsletters_snippet_list + '"]');
							}
						});
					}
				}
			]
		});
	});
})();