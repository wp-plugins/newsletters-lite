<div class="submitbox">
	<div class="misc-pub-section misc-pub-section-last">        
        <div class="scroll-list">
            <?php if (apply_filters($this -> pre . '_admin_nonetheme', true)) : ?><div><label><input <?php echo (empty($_POST['theme_id']) && $this -> default_theme_id('sending') == "") ? 'checked="checked"' : ''; ?> type="radio" name="theme_id" value="0" id="theme0" /> <?php _e('NONE', $this -> plugin_name); ?></label></div><?php endif; ?>
            <?php if ($themes = $Theme -> select()) : ?>
                <?php $default_theme_id = $this -> default_theme_id('sending'); ?>
                <?php foreach ($themes as $theme_id => $theme_title) : ?>
                    <div><label><input <?php echo ((!empty($_POST['theme_id']) && $_POST['theme_id'] == $theme_id) || (empty($_POST['theme_id']) && $theme_id == $default_theme_id)) ? 'checked="checked"' : ''; ?> type="radio" name="theme_id" value="<?php echo $theme_id; ?>" id="theme<?php echo $theme_id; ?>" /> <?php echo __($theme_title); ?></label> 
                    <?php if (apply_filters($this -> pre . '_admin_themepreview', true)) : ?><a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo __($theme_title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo $theme_id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_preview"></a><?php endif; ?>
                    <?php if (apply_filters('newsletters_admin_createnewsletter_themeedit', true)) : ?><a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', $this -> plugin_name), $theme_title); ?>', href:wpmlajaxurl + '?action=newsletters_themeedit&amp;id=<?php echo $theme_id; ?>'}); return false;" class="newsletters_dashicons newsletters_theme_edit"></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>