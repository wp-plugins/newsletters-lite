<table class="form-table">
	<tbody>
    	<tr>
        	<th><label for="latestposts_N"><?php _e('Posts Subscription', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#latestposts_div').show();" <?php echo ($this -> get_option('latestposts') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="latestposts" value="Y" id="latestposts_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
                <label><input onclick="jQuery('#latestposts_div').hide();" <?php echo ($this -> get_option('latestposts') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="latestposts" value="N" id="latestposts_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('Turn on to configure the sending of posts to subscribers at an interval.', $this -> plugin_name); ?></span>
            </td>
        </tr>
        <tr>
        	<th><label for=""><?php _e('Next Schedule', $this -> plugin_name); ?></label></th>
            <td>
            	<?php if ($schedule_interval = wp_get_schedule($this -> pre . '_latestposts')) : ?>
                	<?php
					
					$schedules = wp_get_schedules();
					$next_scheduled = wp_next_scheduled($this -> pre . '_latestposts');
					echo $schedules[$schedule_interval]['display'] . ' - <strong>' . date_i18n("Y-m-d H:i:s", $next_scheduled) . '</strong>';
					
					?>
                <?php else : ?>
                	<span class="<?php echo $this -> pre; ?>error"><?php _e('No schedule has been set yet, please update the interval in the settings below.', $this -> plugin_name); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
        	<th><label for=""><?php _e('Clear Posts History', $this -> plugin_name); ?></label></th>
        	<td>
        		<span class="delete"><a class="button button-secondary button-small submitdelete" href="?page=<?php echo $this -> sections -> settings; ?>&amp;method=clearlpshistory" onclick="if (!confirm('<?php _e('Are you sure you want to clear all Latest Posts Subscriptions post history?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Clear Posts History', $this -> plugin_name); ?></a></span>
        		<span class="edit"><a onclick="jQuery.colorbox({href:'<?php echo admin_url('admin-ajax.php'); ?>?action=newsletters_lpsposts'}); return false;" class="button button-secondary button-small" href=""><?php _e('View Posts', $this -> plugin_name); ?></a></span>
        		<?php
        		
        		global $wpdb;
        		$countquery = "SELECT COUNT(id) FROM " . $wpdb -> prefix . $Latestpost -> table . "";
        		$count = $wpdb -> get_var($countquery);
        		
        		?>
        		<span class="howto"><?php echo sprintf(__('%s posts already logged as sent with the Latest Posts Subscription.', $this -> plugin_name), $count); ?></span>
        	</td>
        </tr>
    </tbody>
</table>

<div id="latestposts_div" style="display:<?php echo ($this -> get_option('latestposts') == "Y") ? 'block' : 'none'; ?>;">

	<?php if ($this -> get_option('latestposts') == "Y") : ?>
		<h4><?php _e('Latest Posts Subscription Preview', $this -> plugin_name); ?></h4>
		<p>
			<?php _e('Below is a preview of what the next latest posts subscription email will look like.', $this -> plugin_name); ?><br/>
			<?php _e('Save settings to update the preview after making changes to settings.', $this -> plugin_name); ?>
		</p>
		<iframe width="100%" height="300" frameborder="0" scrolling="auto" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo admin_url('admin-ajax.php'); ?>?action=<?php echo $this -> pre; ?>latestposts_preview" id="latestpostsiframe">
			<?php _e('Nothing to show yet, please configure your latest posts subscription and save the settings.', $this -> plugin_name); ?>
		</iframe>
	<?php endif; ?>

	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="latestposts_subject"><?php _e('Email Subject', $this -> plugin_name); ?></label></th>
                <td>
                	<input class="widefat" type="text" name="latestposts_subject" value="<?php echo esc_attr(stripslashes($this -> get_option('latestposts_subject'))); ?>" id="latestposts_subject" />
                    <span class="howto"><?php _e('subject of the email to the subscribers.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
            	<th><label for="latestposts_number"><?php _e('Number of Posts', $this -> plugin_name); ?></label></th>
                <td>
                	<input type="text" class="widefat" style="width:65px;" name="latestposts_number" value="<?php echo esc_attr(stripslashes($this -> get_option('latestposts_number'))); ?>" id="latestposts_number" />
                    <span class="howto"><?php _e('only new posts will be sent out and each post not more than once.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <?php if ($this -> is_plugin_active('qtranslate')) : ?>
            	<?php global $q_config; ?>
                <?php $latestposts_language = $this -> get_option('latestposts_language'); ?>
                <?php if (function_exists('qtrans_getSortedLanguages') && $el = qtrans_getSortedLanguages()) : ?>
                	<tr>
                    	<th><?php _e('qTranslate Language', $this -> plugin_name); ?></th>
                        <td>
		                	<?php foreach ($el as $language) : ?>
								<label><input <?php echo (!empty($latestposts_language) && $latestposts_language == $language) ? 'checked="checked"' : ''; ?> type="radio" name="latestposts_language" value="<?php echo esc_attr($language); ?>" id="latestposts_language_<?php echo $language; ?>" /> <img style="border:none;" src="<?php echo WP_CONTENT_URL; ?>/<?php echo $q_config['flag_location']; ?>/<?php echo $q_config['flag'][$language]; ?>" alt="<?php echo $language; ?>" /></label>
		                    <?php endforeach; ?>
		                    <span class="howto"><?php _e('Choose the qTranslate language part which should be used for the posts.', $this -> plugin_name); ?></span>
                    	</td>
				<?php endif; ?>
            <?php endif; ?>
            <?php $latestposts_takefrom = $this -> get_option('latestposts_takefrom'); ?>
            <tr>
            	<th><label for="latestposts_takefrom_categories"><?php _e('Take Posts From', $this -> plugin_name); ?></label></th>
            	<td>
            		<label><input onclick="jQuery('#posttypesdiv').hide(); jQuery('#postcategoriesdiv').show();" <?php echo (empty($latestposts_takefrom) || (!empty($latestposts_takefrom) && $latestposts_takefrom == "categories")) ? 'checked="checked"' : ''; ?> type="radio" name="latestposts_takefrom" value="categories" id="latestposts_takefrom_categories" /> <?php _e('Post Categories', $this -> plugin_name); ?></label>
            		<label><input onclick="jQuery('#posttypesdiv').show(); jQuery('#postcategoriesdiv').hide();" <?php echo (!empty($latestposts_takefrom) && $latestposts_takefrom == "posttypes") ? 'checked="checked"' : ''; ?> type="radio" name="latestposts_takefrom" value="posttypes" id="latestposts_takefrom_posttypes" /> <?php _e('Custom Post Types', $this -> plugin_name); ?></label>
            		<span class="howto"><?php _e('Should posts be regular posts in categories or posts from custom post types?', $this -> plugin_name); ?></span>
            	</td>
            </tr>
        </tbody>
    </table>
    
    <div id="posttypesdiv" style="display:<?php echo (!empty($latestposts_takefrom) && $latestposts_takefrom == "posttypes") ? 'block' : 'none'; ?>;">
    	<table class="form-table">
    		<tbody>
    			<tr>
    				<?php $latestposts_posttypes = $this -> get_option('latestposts_posttypes'); ?>
    				<th><label for="posttypesselectall"><?php _e('Custom Post Types', $this -> plugin_name); ?></label></th>
    				<td>
    					<div>
							<input type="checkbox" name="posttypesselectall" value="1" id="posttypesselectall" onclick="jqCheckAll(this, '<?php echo $this -> sections -> settings; ?>', 'latestposts_posttypes');" />
							<label for="posttypesselectall"><strong><?php _e('Select All', $this -> plugin_name); ?></strong></label>
	                    </div>
	                    <div class="scroll-list">
	    					<label><input <?php echo (!empty($latestposts_posttypes) && in_array('post', $latestposts_posttypes)) ? 'checked="checked"' : ''; ?> type="checkbox" name="latestposts_posttypes[]" value="post" id="latestposts_posttypes_post" /> <?php _e('Post', $this -> plugin_name); ?></label>
	    					<?php if ($post_types = $this -> get_custom_post_types()) : ?>
	    						<?php foreach ($post_types as $ptypekey => $ptype) : ?>
	    							<br/><label><input <?php echo (!empty($latestposts_posttypes) && in_array($ptypekey, $latestposts_posttypes)) ? 'checked="checked"' : ''; ?> type="checkbox" name="latestposts_posttypes[]" value="<?php echo $ptypekey; ?>" id="latestposts_posttype_<?php echo $ptypekey; ?>" /> <?php echo $ptype -> labels -> name; ?></label>
	    						<?php endforeach; ?>
	    					<?php endif; ?>
	    				</div>
	    				<span class="howto"><?php _e('Tick/check custom post types to take posts from for sending.', $this -> plugin_name); ?></span>
    				</td>
    			</tr>
    		</tbody>
    	</table>
    </div>
			
	<div id="postcategoriesdiv" style="display:<?php echo (empty($latestposts_takefrom) || (!empty($latestposts_takefrom) && $latestposts_takefrom == "categories")) ? 'block' : 'none'; ?>;">            
		<table class="form-table">
			<tbody>
	            <tr>
	            	<th><label for="categoriesselectall"><?php _e('Post Categories', $this -> plugin_name); ?></label></th>
	                <td>
	                	<?php if ($categories = get_categories(array('hide_empty' => 0, 'pad_counts' => 1))) : ?>
	                    	<?php $latestposts_categories = $this -> get_option('latestposts_categories'); ?>
							<div>
								<input type="checkbox" name="categoriesselectall" value="1" id="categoriesselectall" onclick="jqCheckAll(this, '<?php echo $this -> sections -> settings; ?>', 'latestposts_categories');" />
								<label for="categoriesselectall"><strong><?php _e('Select All', $this -> plugin_name); ?></strong></label>
	                        </div>
	                    	<div class="scroll-list">
	                        	<?php foreach ($categories as $category) : ?>
	                            	<label><input <?php echo (!empty($latestposts_categories) && in_array($category -> cat_ID, $latestposts_categories)) ? 'checked="checked"' : ''; ?> type="checkbox" name="latestposts_categories[]" value="<?php echo $category -> cat_ID; ?>" id="latestposts_categories_<?php echo $category -> cat_ID; ?>" /> <?php echo $category -> cat_name; ?></label><br/>
	                            <?php endforeach; ?>
	                        </div>
	                        
	                        <span class="howto"><?php _e('categories for posts to be taken from.', $this -> plugin_name); ?></span>
	                    <?php else : ?>
	                    	<p class="<?php echo $this -> pre; ?>error"><?php _e('No categories are available', $this -> plugin_name); ?></p>
	                    <?php endif; ?>
	                </td>
	            </tr>
	        </tbody>
	    </table>
    </div> 
	           
	<table class="form-table">
	    <tbody>
            <tr>
            	<th><label for="latestposts_exclude"><?php _e('Exclude Posts', $this -> plugin_name); ?></label></th>
                <td>
                	<input class="widefat" style="width:250px;" type="text" name="latestposts_exclude" value="<?php echo esc_attr(stripslashes($this -> get_option('latestposts_exclude'))); ?>" id="latestposts_exclude" />
                	<span class="howto"><?php _e('optional. comma separated post IDs to exclude from the latest posts subscription email.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
            	<th><label for="latestposts_olderthan"><?php _e('Oldest Post Date/Time', $this -> plugin_name); ?></label></th>
            	<td>
            		<input type="text" name="latestposts_olderthan" value="<?php echo esc_attr(stripslashes(date("Y-m-d H:i:s", strtotime($this -> get_option('latestposts_olderthan'))))); ?>" id="latestposts_olderthan" />
            		<span class="howto"><small><?php _e('(format: YYYY-MM-DD HH:MM:SS)', $this -> plugin_name); ?></small> <?php _e('Show posts with a publish date no older than the specified date above.', $this -> plugin_name); ?></span>
            	</td>
            </tr>
            <tr>
            	<th><label for="mailinglistsselectall"><?php _e('Mailing List(s)', $this -> plugin_name); ?></label></th>
                <td>
                	<?php if ($lists = $Mailinglist -> select(true)) : ?>
                    	<label style="font-weight:bold;"><input type="checkbox" name="mailinglistsselectall" value="1" id="mailinglistsselectall" onclick="jqCheckAll(this, '<?php echo $this -> sections -> settings; ?>', 'latestposts_lists');" /> <?php _e('Select All', $this -> plugin_name); ?></label><br/>
                    	<?php $latestposts_lists = $this -> get_option('latestposts_lists'); ?>
                    	<div class="scroll-list">
                        	<?php foreach ($lists as $list_id => $list_title) : ?>
                            	<label><input <?php echo (!empty($latestposts_lists) && in_array($list_id, $latestposts_lists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="latestposts_lists[]" value="<?php echo $list_id; ?>" id="latestposts_lists_<?php echo $list_id; ?>" /> <?php echo $list_title; ?> (<?php echo $SubscribersList -> count(array('list_id' => $list_id, 'active' => "Y")); ?> <?php _e('active', $this -> plugin_name); ?>)</label><br/>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                    	<p class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
                    <?php endif; ?>
                	<span class="howto"><?php _e('mailing list(s) to send latest posts subscriptions to.', $this -> plugin_name); ?></span>
                </td>
            </tr>
            <tr>
            	<th><label for="latestposts_updateinterval_N"><?php _e('Update Schedule Interval?', $this -> plugin_name); ?></label></th>
                <td>
                	<label><input onclick="jQuery('#latestposts_updateinterval_div').show();" type="radio" name="latestposts_updateinterval" value="Y" id="latestposts_updateinterval_Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
                    <label><input onclick="jQuery('#latestposts_updateinterval_div').hide();" type="radio" name="latestposts_updateinterval" value="N" id="latestposts_updateinterval_N" checked="checked" /> <?php _e('No', $this -> plugin_name); ?>
                	<span class="howto"><?php _e('leave this as No to leave the interval and current schedule unchanged.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div id="latestposts_updateinterval_div" style="display:none;">
    	<table class="form-table">
        	<tbody>
        		<tr>
	            	<th><label for="latestposts_startdate"><?php _e('Start Date/Time', $this -> plugin_name); ?></label></th>
	            	<td>
	            		<?php
	            		
	            		$latestposts_startdate = $this -> get_option('latestposts_startdate');
	            		if (empty($latestposts_startdate)) { $latestposts_startdate = date_i18n("Y-m-d H:i:s", time()); }
	            		
	            		?>
	            	
	            		<input type="text" name="latestposts_startdate" value="<?php echo esc_attr(stripslashes(date("Y-m-d H:i:s", strtotime($latestposts_startdate)))); ?>" id="latestposts_startdate" /> <strong><?php _e('Current Date/Time:', $this -> plugin_name); ?> <?php echo date_i18n("Y-m-d H:i:s", time()); ?></strong>
	            		<span class="howto"><small><?php _e('(format: YYYY-MM-DD HH:MM:SS)', $this -> plugin_name); ?></small> <?php _e('Choose the day to start sending these posts for the first time with the settings configured.', $this -> plugin_name); ?></span>
	            	</td>
	            </tr>
                <tr>
                    <th><label for="latestposts_interval"><?php _e('Sending Interval', $this -> plugin_name); ?></label></th>
                    <td>
                        <?php if ($schedules = wp_get_schedules()) : ?>
                            <?php $latestposts_interval = $this -> get_option('latestposts_interval'); ?>
                            <select name="latestposts_interval" id="latestposts_interval">
                                <option value=""><?php _e('- Select Schedule -', $this -> plugin_name); ?></option>
                                <?php foreach ($schedules as $skey => $sval) : ?>
                                    <option <?php echo (!empty($latestposts_interval) && $skey == $latestposts_interval) ? 'selected="selected"' : ''; ?> value="<?php echo $skey; ?>"><?php echo $sval['display']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <p class="<?php echo $this -> pre; ?>error"><?php _e('No schedules are available', $this -> plugin_name); ?></p>
                        <?php endif; ?>
                        <span class="howto"><?php _e('set how often the latest posts subscription should be sent out.', $this -> plugin_name); ?></span>
                        <span class="howto"><?php _e('the first execution will be the time of the interval from the current date/time.', $this -> plugin_name); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <table class="form-table">
    	<tbody>
            <tr>
            	<th><label for="latestposts_theme_0"><?php _e('Email Theme', $this -> plugin_name); ?></label></th>
                <td>
                	<?php $Db -> model = $Theme -> model; ?>
                    <?php if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) : ?>
                    	<?php $latestposts_theme = $this -> get_option('latestposts_theme'); ?>
                        <?php $default_theme_id = $this -> default_theme_id('sending'); ?>
                    	<div class="scroll-list">
                        	<label><input type="radio" name="latestposts_theme" value="0" id="latestposts_theme_0" /> <?php _e('NONE', $this -> plugin_name); ?></label><br/>
                        	<?php foreach ($themes as $theme) : ?>
                            	<label><input <?php echo ((!empty($latestposts_theme) && $theme -> id == $latestposts_theme) || $theme -> id == $default_theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="latestposts_theme" value="<?php echo $theme -> id; ?>" id="latestposts_theme_<?php echo $theme -> id; ?>" /> <?php echo $theme -> title; ?></label> <a class="newsletters_dashicons newsletters_theme_preview" href="" onclick="jQuery.colorbox({href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;"></a> <a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Theme: %s', $this -> plugin_name), $theme -> title); ?>', href:wpmlajaxurl + '?action=newsletters_themeedit&amp;id=<?php echo $theme -> id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_edit"></a><br/>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                    	<p class=""><?php _e('No themes are available', $this -> plugin_name); ?></p>
                    <?php endif; ?>
                	<span class="howto"><?php _e('theme to use for the latest posts subscription email.', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>