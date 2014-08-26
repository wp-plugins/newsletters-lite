<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Subscriber', $this -> plugin_name); ?></h2>
	<?php $this -> render('error', array('errors' => $errors)); ?>
	<form onsubmit="jQuery.Watermark.HideAll();" id="optinform<?php echo $subscriber -> id; ?>" name="optinform<?php echo $subscriber -> id; ?>" action="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save" method="post">
		<?php echo $Form -> hidden('Subscriber[id]'); ?>
		<input type="hidden" name="Subscriber[active]" value="Y" />
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Subscriber.email"><?php _e('Email Address', $this -> plugin_name); ?></label>
					<?php echo $Html -> help(__('This is the email address of the subscriber on which the subscriber will receive email newsletters and other notifications.', $this -> plugin_name)); ?></th>
					<td>
						<?php echo $Form -> text('Subscriber[email]'); ?>
						<span class="howto"><?php _e('Valid email address of the subscriber to receive newsletters.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="checkboxall"><?php _e('Mailing List(s)', $this -> plugin_name); ?><label>
					<?php echo $Html -> help(__('Choose the mailing list(s) to subscribe this user to. Sending to any of the list(s) that you subscribe this user to will result in this user receiving the email newsletter.', $this -> plugin_name)); ?></th>
					<td>
						<div><label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Check All', $this -> plugin_name); ?></label></div>
						<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
							<?php foreach ($mailinglists as $key => $val) : ?>
								<?php $mailinglists[$key] = $val . ' (' . $SubscribersList -> count(array('list_id' => $key)) . ' ' . __('subscribers', $this -> plugin_name) . ')'; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<div class="scroll-list">
							<?php echo $Form -> checkbox('Subscriber[mailinglists][]', $mailinglists); ?>
						</div>
						<?php echo $Html -> field_error('Subscriber[mailinglists]'); ?>
						<span class="howto"><?php _e('All ticked/checked subscriptions are activated immediately.', $this -> plugin_name); ?></span>													
					</td>
				</tr>
				<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>										
				<tr>
					<th><?php _e('Register as WordPress user?', $this -> plugin_name); ?>
					<?php echo $Html -> help(__('Would you like to register this subscriber as a WordPress user? The subscribers are separate from WordPress users at all times and is not the same list of emails. In this case you can add this subscriber as a user in WordPress.', $this -> plugin_name)); ?></th>
					<td>
						<?php $registered = array('Y' => __('Yes', $this -> plugin_name), 'N' => __('No', $this -> plugin_name)); ?>
						<?php echo $Form -> radio('Subscriber[registered]', $registered, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#registereddiv').show(); } else { jQuery('#registereddiv').hide(); }")); ?>
					</td>
				</tr>	
				<?php endif; ?>			
			</tbody>
		</table>
		
		<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>
		<div id="registereddiv" style="display:<?php echo ($Html -> field_value('Subscriber[registered]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e('Wordpress Username', $this -> plugin_name); ?></th>
						<td><?php echo $Form -> text('Subscriber[username]'); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Email Format', $this -> plugin_name); ?>
					<?php echo $Html -> help(__('The preferred email format that this subscriber wants to receive. Available formats are HTML and PLAIN TEXT. If you are going to send multi-mime emails, this setting is ineffective and the email/webmail client of the subscriber will automatically decide.', $this -> plugin_name)); ?></th>
					<td>
						<?php $formats = array('html' => __('Html', $this -> plugin_name), 'text' => __('Text', $this -> plugin_name)); ?>
						<?php echo $Form -> radio('Subscriber[format]', $formats, array('default' => "html", 'separator' => false)); ?>
						
						<span class="howto"><?php _e('it is recommended that you use HTML format and turn on multi-part emails under Newsletters > Configuration for compatibility.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>
		
		<?php
		
		global $wpdb;
		$fieldsquery = "SELECT * FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
		$objectcache = $this -> get_option('objectcache');
		$query_hash = md5($fieldsquery);
		if (!empty($objectcache) && $oc_fields = wp_cache_get($query_hash, 'newsletters')) {
			$fields = $oc_fields;
		} else {
			$fields = $wpdb -> get_results($fieldsquery);
			if (!empty($objectcache)) {
				wp_cache_set($query_hash, $fields, 'newsletters', 0);
			}
		}
		
		?>
		
        <?php if (!empty($fields)) : ?>
			<br/>
			<h3><?php _e('Custom Fields', $this -> plugin_name); ?> (<?php echo $Html -> link(__('show/hide', $this -> plugin_name), '#void', array('onclick' => "jQuery('#customfieldsdiv').toggle();")); ?>)
			<?php echo $Html -> help(__('Click "show/hide" to display the available custom fields and fill in values for the custom fields for this subscriber.', $this -> plugin_name)); ?></h3>
			<div id="customfieldsdiv" style="display:block;">
				<table class="form-table">
					<tbody>
                    	<?php $optinid = rand(1, 999); ?>
						<?php foreach ($fields as $field) : ?>
							<tr>
								<th><label for="<?php echo $field -> slug; ?>"><?php echo __($field -> title); ?></label></th>
								<td><?php $this -> render_field($field -> id, false, $optinid); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		
		<p class="submit">
			<?php echo $Form -> submit(__('Save Subscriber', $this -> plugin_name)); ?>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="Subscriber[email]"]').Watermark('<?php echo addslashes(__('Enter email address here', $this -> plugin_name)); ?>');
	jQuery('[name="Subscriber[username]"]').Watermark('<?php echo addslashes(__('Enter username here', $this -> plugin_name)); ?>');
});
</script>