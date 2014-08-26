<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Campaign', $this -> plugin_name); ?></h2>
	
	<form action="" method="">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Campaign.title"><?php _e('Campaign Name', $this -> plugin_name); ?></label></th>
					<td>
						<?php echo $Form -> text('Campaign.title'); ?>
						<span class="howto"><?php _e('Give your campaign a name/title for identification purposes.', $this -> plugin_name); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for=""><?php _e('Sent &amp; Draft Emails', $this -> plugin_name); ?></label></th>
					<td>
						<?php $Db -> model = $History -> model; ?>
						<?php if ($allemails = $Db -> find_all(false, false, array('subject', "ASC"))) : ?>
                        	<div>
                            	<ul>
									<?php foreach ($allemails as $allemail) : ?>
                                        <li><label><input type="checkbox" name="Campaign[newsletters]" value="<?php echo $allemail -> id; ?>" id="Campaign.newsletters<?php echo $allemail -> id; ?>" /> <?php echo $allemail -> subject; ?></label></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
						<?php endif; ?>
						<span class="howto"><?php _e('Choose the emails and sequence/order of the emails to include in the sending of this campaign.', $this -> plugin_name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>