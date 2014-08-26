<div class="<?php echo $this -> pre; ?> newsletters">
	<?php if ($this -> is_plugin_active('qtranslate')) : ?>
		<div class="misc-pub-section">
		<p><strong><?php _e('Language', $this -> plugin_name); ?></strong></h4>
	    <p><?php _e('Choose which title/content in the editor above should be sent to the mailing list(s) chosen below.', $this -> plugin_name); ?></p>
	    <?php if ($el = $this -> language_getlanguages()) : ?>
	    	<p>
				<?php foreach ($el as $language) : ?>
	                <label><input <?php echo ($this -> language_default() == $language) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>qtranslate_language" value="<?php echo $language; ?>" id="<?php echo $this -> pre; ?>qtranslate_language_<?php echo $language; ?>" /> <?php echo $this -> language_flag($language); ?> <?php echo stripslashes($this -> language_name($language)); ?></label><br/>
	            <?php endforeach; ?>
	        </p>
	    <?php else : ?>
	    	<p style="color:red;"><?php _e('No languages are available, please enable languages first.', $this -> plugin_name); ?></p>
	    <?php endif; ?>
	    </div>
	<?php endif; ?>
	
	<div class="misc-pub-section">
	<p><strong><?php _e('Full Post or Excerpt', $this -> plugin_name); ?></strong></p>
	<p>
	<label><input <?php echo ($this -> get_option('sendonpublishef') == "fp") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>sendonpublishef" value="fp" /> <?php _e('Full Post', $this -> plugin_name); ?></label>
	<label><input <?php echo ($this -> get_option('sendonpublishef') == "ep") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>sendonpublishef" value="ep" /> <?php _e('Excerpt of Post', $this -> plugin_name); ?></label>
	</p>
	</div>
	
	<div class="misc-pub-section">
	<p><strong><?php _e('Select a Theme', $this -> plugin_name); ?></strong></p>
	<p>
		<label><input <?php echo (empty($_POST[$this -> pre . 'theme_id'])) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>theme_id" value="0" id="theme0" /> <?php _e('NONE', $this -> plugin_name); ?></label>
		<?php $Db -> model = $Theme -> model; ?>
		<?php if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) : ?>
			<div class="scroll-list">
				<?php $default_theme_id = $this -> default_theme_id('sending'); ?>
			    <?php foreach ($themes as $theme) : ?>
			        <label><input <?php echo ((!empty($_POST[$this -> pre . 'theme_id']) && $_POST[$this -> pre . 'theme_id'] == $theme -> id) || $theme -> id == $default_theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>theme_id" value="<?php echo $theme -> id; ?>" id="theme<?php echo $theme -> id; ?>" /> <?php echo $theme -> title; ?></label> 
			        <a href="" onclick="jQuery.colorbox({title:'<?php echo __($theme -> title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme -> id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_preview"></a>
			        <a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Theme: %s', $this -> plugin_name), $theme -> title); ?>', href:wpmlajaxurl + '?action=newsletters_themeedit&amp;id=<?php echo $theme -> id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_edit"></a>
			        <br/>
			    <?php endforeach; ?>
			</div>
		<?php endif; ?>
	</p>
	</div>
	
	<div class="misc-pub-section">
	<p><strong><?php _e('Select Mailing List(s)', $this -> plugin_name); ?></strong></p>
	<div class="scroll-list">
		<table class="widefat">
			<tbody>
				<?php if ($mailinglists = $Mailinglist -> select($privatelists = true)) : ?>
					<tr>
						<th><input type="checkbox" name="mailinglistsselectall" value="1" id="mailinglistsselectall" onclick="jqCheckAll(this, 'post', 'wpmlmailinglists');" /></th>
						<td><label for="mailinglistsselectall" style="font-weight:bold;"><?php _e('Select All', $this -> plugin_name); ?></label></td>
					</tr>
					<?php foreach ($mailinglists as $id => $title) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th><input id="checklist<?php echo $id; ?>" <?php echo (!empty($_POST[$this -> pre . 'mailinglists']) && in_array($id, $_POST[$this -> pre . 'mailinglists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo $this -> pre; ?>mailinglists[]" value="<?php echo $id; ?>" /></th>
							<td><label for="checklist<?php echo $id; ?>"><?php echo $title; ?> (<?php echo $SubscribersList -> count(array('list_id' => $id, 'active' => "Y")); ?> <?php _e('active subscribers', $this -> plugin_name); ?>)</label></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="<?php echo $this -> pre; ?>error"><?php _e('No mailing lists are available', $this -> plugin_name); ?></p>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	</div>
	
	<br class="clear" />
</div>