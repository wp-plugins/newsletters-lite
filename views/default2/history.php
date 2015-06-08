<?php if (!empty($emails)) : ?>
	<div id="<?php echo $this -> pre; ?>history" class="<?php echo $this -> pre; ?>history">
    	<?php if (!empty($history_index) && $history_index == true) : ?>
            <ul class="<?php echo $this -> pre; ?>history_index">
            	<?php foreach ($emails as $email) : ?>
                	<li><a href="#<?php echo $this -> pre; ?>history_email<?php echo $email -> id; ?>"><?php echo stripslashes($email -> subject); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    	<div class="<?php echo $this -> pre; ?>history_emails">
			<?php foreach ($emails as $email) : ?>
                <div id="<?php echo $this -> pre; ?>history_email<?php echo $email -> id; ?>" class="<?php echo $this -> pre; ?>history_email">
                    <h3 class="<?php echo $this -> pre; ?>history_title"><a href="<?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=newsletter&amp;id=<?php echo $email -> id; ?>&amp;history=1" title="<?php echo esc_attr(stripslashes($email -> subject)); ?>"><?php echo stripslashes($email -> subject); ?></a></h3>
                    <div class="<?php echo $this -> pre; ?>history_meta"><small><?php _e('Sent on:', $this -> plugin_name); ?> <?php echo $email -> modified; ?></small></div>
                    <div class="<?php echo $this -> pre; ?>history_content"><?php echo wpautop($this -> strip_set_variables($email -> message)); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>