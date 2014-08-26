<p><small><?php _e('Each of these shortcodes below can be used inside the content of a newsletter to be replaced with an appropriate value automatically.', $this -> plugin_name); ?></small></p>

<div class="scroll-list">
    <table class="form-table">
        <thead>
            <tr>
                <th><?php _e('Code/String', $this -> plugin_name); ?></th>
                <th><?php _e('Description', $this -> plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $class = ''; ?>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[<?php echo $this -> pre; ?>post post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_insert();"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php _e('Inserts the excerpt of a single post.', $this -> plugin_name); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[<?php echo $this -> pre; ?>posts]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>posts]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php _e('Insert the excerpts of multiple posts.', $this -> plugin_name); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[<?php echo $this -> pre; ?>post_thumbnail post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_thumbnail_insert();"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php _e('Insert a post featured thumbnail image.', $this -> plugin_name); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[<?php echo $this -> pre; ?>post_permalink post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_permalink_insert();"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php _e('Insert the permalink URL of a post.', $this -> plugin_name); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[<?php echo $this -> pre; ?>email]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>email]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Inserts the email address of each user', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[<?php echo $this -> pre; ?>subject]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>subject]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Display the title/subject of this newsletter in the content.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[<?php echo $this -> pre; ?>historyid]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>historyid]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Display the history ID of this newsletter in the content.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[<?php echo $this -> pre; ?>unsubscribe]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>unsubscribe]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Generates an unsubscribe link for the specific list(s).', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[<?php echo $this -> pre; ?>unsubscribeall]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>unsubscribeall]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Generates an unsubscribe link for all mailing lists subscribed to.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                	<code>[<?php echo $this -> pre; ?>blogname]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>blogname]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Inserts the name of your website/blog', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>siteurl]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>siteurl]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Inserts the URL of your website/blog', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>mailinglist]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>mailinglist]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Outputs the name of the mailing list being sent to.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>activate]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>activate]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Generates an activation link for each subscriber', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>manage]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>manage]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Creates a link which takes the subscriber to a management page', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>online]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>online]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Display a link for subscribers to view the newsletter online', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>date {format}]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href='javascript:wpml_tinymcetag("[<?php echo $this -> pre; ?>date format=\"%d/%m/%Y\"]");'><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Output the current date and/or time. Optionally, specify a format parameter. Eg. [wpmldate format="%d/%m/%Y"] . It uses PHP strftime()', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>track]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>track]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Inserts a discreet tracking code into each email.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>bouncecount]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>bouncecount]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Output the total email bounces for the subscriber.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>customfields]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>customfields]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Output all custom fields with values in a table for the subscriber.', $this -> plugin_name); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[<?php echo $this -> pre; ?>subscriberscount]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[<?php echo $this -> pre; ?>subscriberscount]');"><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                </td>
                <td><?php _e('Display the total number of subscribers in the database.', $this -> plugin_name); ?></td>
            </tr>
            <?php $Db -> model = 'Field'; ?>
            <?php $fields = $Db -> find_all(false, array('id', 'title', 'slug'), array('title', "ASC")); ?>
            <?php if (!empty($fields)) : ?>
                <?php foreach ($fields as $field) : ?>
                    <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                        <td><code>[<?php echo $this -> pre; ?>field name="<?php echo $field -> slug; ?>"]</code>
                            <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href='javascript:wpml_tinymcetag("[<?php echo $this -> pre; ?>field name=\"<?php echo $field -> slug; ?>\"]");'><?php _e('Insert into Editor', $this -> plugin_name); ?></a></small><?php endif; ?>
                        </td>
                        <td><b><?php _e('Custom', $this -> plugin_name); ?>:</b> <?php echo __($field -> title); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
function wpmlpost_insert() {
	var post_id = prompt('<?php _e('What is the ID of the post you want to insert?', $this -> plugin_name); ?>');
	var eftype = prompt('<?php _e('Do you want to insert a full post or excerpt? Use "full" or "excerpt" to specify.', $this -> plugin_name); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[wpmlpost post_id="' + post_id + '" eftype="' + eftype + '"]');
	}
}

function wpmlpost_thumbnail_insert() {
	var post_id = prompt('<?php _e('What is the ID of the post to take the featured image from? \nIf you are sending/queuing this newsletter from a post/page, leave empty to use the current post/page ID automatically.', $this -> plugin_name); ?>');
	var size = prompt('<?php _e('Please fill in a size! Use either thumbnail, medium, large or full.', $this -> plugin_name); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[wpmlpost_thumbnail post_id="' + post_id + '" size="' + size + '"]');
	} else {
		wpml_tinymcetag('[wpmlpost_thumbnail size="' + size + ']');
	}
}

function wpmlpost_permalink_insert() {
	var post_id = prompt('<?php _e('What is the ID of the post to generate a permalink URL for? \nIf you are sending/queuing this newsletter from a post/page, leave empty to use the current post/page ID automatically.', $this -> plugin_name); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[wpmlpost_permalink post_id="' + post_id + '"]');
	} else {
		wpml_tinymcetag('[wpmlpost_permalink]');
	}
}
</script>