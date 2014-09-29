<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo $this -> pre; ?>publishpostN"><?php _e('Publish as Post', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo (!empty($_POST['post_id'])) ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($_POST['post_id']) || (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" onclick="jQuery('#publishpostdiv').show();" name="publishpost" value="Y" id="<?php echo $this -> pre; ?>publishpostY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo (!empty($_POST['post_id'])) ? 'disabled="disabled"' : ''; ?> <?php echo (empty($_POST['post_id']) && (empty($_POST['publishpost']) || $_POST['publishpost'] == "N")) ? 'checked="checked"' : ''; ?> type="radio" onclick="jQuery('#publishpostdiv').hide();" name="publishpost" value="N" id="<?php echo $this -> pre; ?>publishpostN" /> <?php _e('No', $this -> plugin_name); ?></label>
			</td>
		</tr>
		<?php if (!empty($_POST['post_id'])) : ?>
			<?php $post = get_post($_POST['post_id']); ?>
			<tr>
				<th><label for=""><?php _e('Existing Post', $this -> plugin_name); ?></label></th>
				<td>
					<a href="<?php echo get_permalink($post -> ID); ?>" target="_blank"><?php echo __($post -> post_title); ?></a>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
							
<div id="publishpostdiv" style="display:<?php echo (!empty($_POST['post_id']) || (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y")) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php _e('Post Categories', $this -> plugin_name); ?></th>
				<td>
					<div style="overflow:auto; max-height:100px;">
						<?php $categories = get_categories(array('hide_empty' => false)); ?>
						<?php if (!empty($categories)) : ?>
							<?php foreach ($categories as $category) : ?>
								<?php if (!empty($_POST['cat']) && is_array($_POST['cat'])) {
									$isthiscat = (in_array($category -> cat_ID, $_POST['cat'])) ? true : false;
								} else {
									$isthiscat = false;
								} ?>
								<label><input <?php echo $check = ($isthiscat) ? 'checked="checked"' : ''; ?> type="checkbox" name="cat[]" value="<?php echo $category -> cat_ID; ?>" /> <?php echo $category -> cat_name; ?></label><br/>
							<?php endforeach; ?>
						<?php else : ?>
							<span style="<?php echo $this -> pre; ?>error"><?php _e('No categories were found', $this -> name); ?></span>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			<tr>
				<th><label for="post_author"><?php _e('Post Author', $this -> plugin_name); ?></label></th>
				<td>
					<?php wp_dropdown_users(array('who' => "authors", 'name' => 'post_author', 'selected' => get_current_user_id())); ?>
				</td>
			</tr>
			<tr>
				<th><label for="post_status"><?php _e('Post Status', $this -> plugin_name); ?></label></th>
				<td>
					<?php $statuses = $this -> get_option('poststatuses'); ?>
					<select class="widefat" style="width:auto;" id="post_status" name="post_status">
						<?php foreach ($statuses as $key => $val) : ?>
							<?php $sel = ($_POST['post_status'] == $key) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="newsletters_post_type"><?php _e('Post Type', $this -> plugin_name); ?></label></th>
				<td>
					<?php if ($post_types = $this -> get_custom_post_types(false)) : ?>
						<select name="newsletters_post_type" id="newsletters_post_type">
							<?php foreach ($post_types as $ptypekey => $ptype) : ?>
								<option <?php echo (!empty($_POST['newsletters_post_type']) && $_POST['newsletters_post_type'] == $ptypekey) ? 'selected="selected"' : ''; ?> value="<?php echo $ptypekey; ?>"><?php echo $ptype -> labels -> name; ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php _e('Post Slug', $this -> plugin_name);?></th>
				<td>
					<input class="widefat" type="text" size="25" name="post_slug" value="<?php echo esc_attr(stripslashes($_POST['post_slug'])); ?>" />
					<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Post slug to use for this post', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>