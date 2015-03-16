<h3><?php _e('Current Subscriptions', $this -> plugin_name); ?></h3><?php if (!empty($subscriber -> subscriptions)) : ?>        <p><?php _e('Below are your current subscriptions to our list(s).', $this -> plugin_name); ?><br/>    <?php _e('An Active status indicates that you will receive emails on that list.', $this -> plugin_name); ?></p>    <?php if (!empty($errors)) : ?>    	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>    <?php endif; ?>    <?php if (!empty($success) && $success == true) : ?>    	<p class="<?php echo $this -> pre; ?>success"><?php echo $successmessage; ?></p>    <?php endif; ?>	<table>    	<thead>        	<tr>                <td><strong><?php _e('Mailing List', $this -> plugin_name); ?></strong></td>                <td><strong><?php _e('Status', $this -> plugin_name); ?></strong></td>                <td><strong><?php _e('Action', $this -> plugin_name); ?></strong></td>            </tr>        </thead>    	<tbody>    		<?php $intervals = $this -> get_option('intervals'); ?>        	<?php foreach ($subscriber -> subscriptions as $subscription) : ?>            	<tr id="currentsubscription<?php echo $subscription -> mailinglist -> id; ?>">                	<td>                    	<label for="mailinglists<?php echo $subscription -> mailinglist -> id; ?>"><?php echo __($subscription -> mailinglist -> title); ?></label>                    	<?php if ($subscription -> mailinglist -> paid == "Y") : ?>                    		<span class="wpmlcustomfieldcaption"><?php echo $Html -> currency() . '' . number_format($subscription -> mailinglist -> price, 2, '.', '') . ' ' . $intervals[$subscription -> mailinglist -> interval]; ?></span>                    	<?php endif; ?>                        <?php if ($subscription -> mailinglist -> paid == "Y" && $subscription -> active == "Y") : ?>                        	<?php $expiresdate = (!empty($subscription -> mailinglist -> interval) && $subscription -> mailinglist -> interval != "once") ? date_i18n("Y-m-d", strtotime($Mailinglist -> gen_expiration_date($subscriber -> id, $subscription -> mailinglist -> id))) : __('Never', $this -> plugin_name); ?>                        	<span class="wpmlcustomfieldcaption"><?php _e('Expires:', $this -> plugin_name); ?> <strong><?php echo $expiresdate; ?></strong></span>                        	<?php if (!empty($subscription -> mailinglist -> maxperinterval)) : ?>                        		<span class="wpmlcustomfieldcaption"><?php echo sprintf(__('%s out of %s sent', $this -> plugin_name), $subscription -> paid_sent, $subscription -> mailinglist -> maxperinterval); ?></span>                        	<?php endif; ?>                        <?php endif; ?>                    </td>                    <td><label for="mailinglists<?php echo $subscription -> mailinglist -> id; ?>"><?php echo ($subscription -> active == "Y") ? '<span class="newsletters_success">' . __('Active', $this -> plugin_name) . '</span>' : '<span class="newsletters_error">' . __('Inactive', $this -> plugin_name) . '</span>'; ?></label></td>                    <td>                    	<span id="activatelink<?php echo $subscription -> mailinglist -> id; ?>">                    	<?php if ($subscription -> active == "Y") : ?>                        	<a href="javascript:wpmlmanagement_activate('<?php echo $subscriber -> id; ?>','<?php echo $subscription -> mailinglist -> id; ?>','N');" onclick="if (!confirm('<?php _e('Are you sure you want to remove this subscription?', $this -> plugin_name); ?>')) { return false; }" class="<?php echo $this -> pre; ?>button activatebutton"><?php _e('Remove', $this -> plugin_name); ?></a>                        <?php else : ?>                        	<?php if (!empty($subscription -> mailinglist -> paid) && $subscription -> mailinglist -> paid == "Y") : ?>                        		<?php $this -> paidsubscription_form($subscriber, $subscription -> mailinglist, false, "_blank"); ?>                        	<?php else : ?>                        		<a href="javascript:wpmlmanagement_activate('<?php echo $subscriber -> id; ?>','<?php echo $subscription -> mailinglist -> id; ?>','Y');" onclick="if (!confirm('<?php _e('Are you sure you want to activate this subscription?', $this -> plugin_name); ?>')) { return false; }" class="<?php echo $this -> pre; ?>button activatebutton"><?php _e('Activate', $this -> plugin_name); ?></a>                        	<?php endif; ?>                        	<a href="javascript:wpmlmanagement_activate('<?php echo $subscriber -> id; ?>','<?php echo $subscription -> mailinglist -> id; ?>','N');" onclick="if (!confirm('<?php _e('Are you sure you want to remove this subscription?', $this -> plugin_name); ?>')) { return false; }" class="<?php echo $this -> pre; ?>button activatebutton"><?php _e('Remove', $this -> plugin_name); ?></a>                        <?php endif; ?>                       	</span>                    </td>                </tr>                <?php $subscribedlists[] = $subscription -> mailinglist -> id; ?>            <?php endforeach; ?>        </tbody>    </table>        <script type="text/javascript">jQuery(document).ready(function() { if (jQuery.isFunction(jQuery.fn.button)) { jQuery('.<?php echo $this -> pre; ?>button').button(); } });</script><?php else : ?>	<p class="<?php echo $this -> pre; ?>error"><?php _e('You are not subscribed to any lists.', $this -> plugin_name); ?><?php endif; ?>