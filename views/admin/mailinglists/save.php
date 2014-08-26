<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Mailing List', $this -> plugin_name); ?></h2>
	<?php $this -> render_admin('error', array('errors' => $errors)); ?>
	<form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> lists; ?>&amp;method=save" method="post" id="mailinglistform">
		<?php echo $Form -> hidden('Mailinglist[id]'); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Mailinglist.title"><?php _e('List Title', $this -> plugin_name); ?></label></th>
					<td>
						<?php echo $Form -> text('Mailinglist[title]'); ?>
                    	<span class="howto"><?php _e('Fill in a title for your list as your users will see it.', $this -> plugin_name); ?></span>    
                    </td>
				</tr>
				<?php if (apply_filters('newsletters_admin_mailinglists_save_privatelist_show', true)) : ?>
					<tr>
						<th><label for="privatelist"><?php _e('Private List', $this -> plugin_name); ?></label></th>
						<td>
							<label><input <?php echo ($mailinglist -> privatelist == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[privatelist]" id="privatelist2" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
							<label><input <?php echo (empty($mailinglist -> privatelist) || $mailinglist -> privatelist == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[privatelist]" id="privatelist" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
							<?php echo $Html -> field_error('Mailinglist[privatelist]'); ?>
	                        <span class="howto"><?php _e('A private list is for internal use only and will not be visible to users.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ($fields = $Field -> select()) : ?>
					<tr>
						<th><label for="checkboxall"><?php _e('Custom Fields', $this -> plugin_name); ?></label></th>
						<td>
							<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Check All', $this -> plugin_name); ?></label>
							<div class="scroll-list">
								<?php echo $Form -> checkbox('Mailinglist[fields][]', $fields); ?>
							</div>
                            <span class="howto"><?php _e('Attach custom fields to this list to be displayed in the subscribe form.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				<?php endif; ?>
                <tr>
                	<th><label for="Mailinglist.group_id"><?php _e('Group', $this -> plugin_name); ?></label></th>
                    <td>
                    	<?php if ($groupsselect = $wpmlGroup -> select()) : ?>
                        	<?php echo $Form -> select('Mailinglist[group_id]', $groupsselect); ?>
                            <span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Put this mailing list into a group of lists.', $this -> plugin_name); ?></span>
                        <?php else : ?>
                        	<p class="<?php echo $this -> pre; ?>error"><?php _e('No groups are available.', $this -> plugin_name); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if (apply_filters('newsletters_admin_mailinglists_save_paidlist_show', true)) : ?>
					<tr>
						<th><label for="Mailinglist.paidNo"><?php _e('Paid List', $this -> plugin_name); ?></label></th>
						<td>
							<?php $radios = array('Y' => __('Yes', $this -> plugin_name), 'N' => __('No', $this -> plugin_name)); ?>
							<?php echo $Form -> radio('Mailinglist[paid]', $radios, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#paiddiv').show(); } else { jQuery('#paiddiv').hide(); }")); ?>
	                        <span class="howto"><?php _e('A paid list requires a payment per interval to keep the subscription active.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div id="paiddiv" style="display:<?php echo ($Html -> field_value('Mailinglist[paid]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Mailinglist.price"><?php _e('Subscription Price', $this -> plugin_name); ?></label></th>
						<td>
							<?php echo $Html -> currency(); ?><?php echo $Form -> text('Mailinglist[price]', array('width' => '65px')); ?>
                            <span class="howto"><?php _e('Payment price at the interval below to stay subscribed to this list.', $this -> plugin_name); ?></span>
                        </td>
					</tr>
					<tr>
						<th><label for="Mailinglist.interval"><?php _e('Subscription Interval', $this -> plugin_name); ?></label></th>
						<td>
							<?php echo $Form -> select('Mailinglist[interval]', $this -> get_option('intervals')); ?>
                        	<span class="howto"><?php _e('Interval at which to charge the payment price above.', $this -> plugin_name); ?></span>    
                        </td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (apply_filters('newsletters_admin_mailinglists_save_adminemail_show', true)) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Mailinglist.adminemail"><?php _e('Administrator Email', $this -> plugin_name); ?></label></th>
						<td>
							<?php echo $Form -> text('Mailinglist[adminemail]'); ?>
							<span class="howto"><small><?php _e('(optional)', $this -> plugin_name); ?></small> <?php _e('Email address to send notifications to for events of this mailing list.', $this -> plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>
		
		<p class="submit">
			<?php echo $Form -> submit(__('Save Mailing List', $this -> plugin_name)); ?>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="Mailinglist[title]"]').Watermark('<?php echo addslashes(__('Enter mailing list title here', $this -> plugin_name)); ?>');
	jQuery('[name="Mailinglist[adminemail]"]').Watermark('<?php echo addslashes(__('Enter a valid email address', $this -> plugin_name)); ?>');
});
</script>