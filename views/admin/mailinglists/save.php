<!-- Save a Mailing List -->

<?php

$doubleopt = $mailinglist -> doubleopt;

if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
	$mailinglist_title = $this -> language_split($Mailinglist -> data[$Mailinglist -> model] -> title);
}

?>

<div class="wrap <?php echo $this -> pre; ?> newsletters">
	<h2><?php _e('Save a Mailing List', $this -> plugin_name); ?></h2>
	<?php $this -> render_admin('error', array('errors' => $errors)); ?>
	<form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> lists; ?>&amp;method=save" method="post" id="mailinglistform">
		<?php echo $Form -> hidden('Mailinglist[id]'); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Mailinglist.title"><?php _e('List Title', $this -> plugin_name); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="mailinglist-title-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#mailinglist-title-tabs-<?php echo $language; ?>"><?php echo $this -> language_flag($language); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="mailinglist-title-tabs-<?php echo $language; ?>">
										<input type="text" class="widefat" name="Mailinglist[title][<?php echo $language; ?>]" value="<?php echo esc_attr(stripslashes($mailinglist_title[$language])); ?>" id="Mailinglist_title_<?php echo $language; ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#mailinglist-title-tabs').tabs();
							});
							</script>
						<?php else : ?>
							<?php echo $Form -> text('Mailinglist[title]'); ?>
						<?php endif; ?>
                    	<span class="howto"><?php _e('Fill in a title for your list as your users will see it.', $this -> plugin_name); ?></span>    
                    </td>
				</tr>
				<?php if (apply_filters('newsletters_admin_mailinglists_save_privatelist_show', true)) : ?>
					<tr>
						<th><label for="privatelist"><?php _e('Private List', $this -> plugin_name); ?></label>
						<?php echo $Html -> help(__('A private list will not be shown to users/subscribers publicly, it is for internal use. You can send to subscribers in a private list and they can still unsubscribe from it though.', $this -> plugin_name)); ?></th>
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
							<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Select all', $this -> plugin_name); ?></label>
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
                <tr>
                	<th><label for="doubleopt_Y"><?php _e('Double Opt-In', $this -> plugin_name); ?></label>
                	<?php echo $Html -> help(__('With "Require Activation?" setting turned on in configuration, this is effective. You can then specify for this specific list whether double opt-in is required or not. If you specify Yes, a subscriber will need to activate/confirm via an email with confirmation link.', $this -> plugin_name)); ?></th>
                	<td>
                		<label><input <?php echo (empty($doubleopt) || (!empty($doubleopt) && $doubleopt == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[doubleopt]" value="Y" id="doubleopt_Y" /> <?php _e('Yes, require activation', $this -> plugin_name); ?></label>
                		<label><input <?php echo (!empty($doubleopt) && $doubleopt == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[doubleopt]" value="N" id="doubleopt_N" /> <?php _e('No, activate immediately', $this -> plugin_name); ?></label>
                		<span class="howto"><?php _e('This is only effective when "Require Activation?" is turned on in configuration', $this -> plugin_name); ?></span>
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
					<tr>
						<th><label for="Mailinglist.maxperinterval"><?php _e('Max Emails per Interval', $this -> plugin_name); ?></label>
						<?php echo $Html -> help(__('Specify the maximum number of emails/newsletters the subscriber may receive on this paid mailing list before it stops sending. Leave it zero (0) or empty for no limit.', $this -> plugin_name)); ?></th>
						<td>
							<?php echo $Form -> text('Mailinglist[maxperinterval]', array('width' => "65px")); ?>
							<span class="howto"><?php _e('Maximum allowed emails per interval set above.', $this -> plugin_name); ?></span>
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