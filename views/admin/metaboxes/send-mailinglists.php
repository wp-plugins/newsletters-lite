<!-- Mailinglists/Subscribers Box -->

<?php

global $wpdb, $wp_roles;
$roles = $wp_roles -> get_names();
$count_users = count_users();

?>

<div id="mailingliststabs">
	<ul>
		<li><a href="#mailingliststabs-subscribers"><?php _e('Subscribers', $this -> plugin_name); ?></a></li>
		<li><a href="#mailingliststabs-segment"><?php _e('Segment', $this -> plugin_name); ?></a></li>
	</ul>
	
	<div id="mailingliststabs-subscribers">
		<div id="groupsdiv">
            <?php if ($groups = $wpmlGroup -> select()) : ?>
                <div><label class="selectit" style="font-weight:bold;"><input type="checkbox" id="groupsselectall" name="groupsselectall" value="1" onclick="jqCheckAll(this, 'post', 'groups'); update_subscribers();" /> <?php _e('Select all Groups', $this -> plugin_name); ?></label></div>
                <div class="scroll-list">
                    <?php foreach ($groups as $group_id => $group_title) : ?>
                        <div><label class="selectit"><input onclick="update_subscribers();" <?php echo (!empty($_POST['groups']) && is_array($_POST['groups']) && in_array($group_id, $_POST['groups'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="groups[]" id="checklist<?php echo $group_id; ?>" value="<?php echo $group_id; ?>" /> <?php echo __($group_title); ?> (<?php echo $Mailinglist -> count(array('group_id' => $group_id)); ?> <?php _e('lists', $this -> plugin_name); ?>)</label></div>
                    <?php endforeach; ?>
                </div>
                <br/>
            <?php else : ?>
            
            <?php endif; ?>
        </div>
    	<div id="listsdiv">
            <?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
                <div><label class="selectit" style="font-weight:bold;"><input type="checkbox" id="mailinglistsselectall" name="mailinglistsselectall" value="1" onclick="jqCheckAll(this, 'post', 'mailinglists'); update_subscribers();" /> <?php _e('Select all Lists', $this -> plugin_name); ?></label></div>
                <div class="scroll-list">
                    <?php foreach ($mailinglists as $list_id => $list_title) : ?>
                        <div><label class="selectit"><input onclick="update_subscribers();" <?php echo (!empty($_POST['mailinglists']) && is_array($_POST['mailinglists']) && in_array($list_id, $_POST['mailinglists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="mailinglists[]" id="checklist<?php echo $list_id; ?>" value="<?php echo $list_id; ?>" /> <?php echo $list_title; ?> (<?php echo $SubscribersList -> count(array('list_id' => $list_id, 'active' => "Y")); ?> <?php _e('active', $this -> plugin_name); ?>)</label></div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="<?php echo $this -> pre; ?>error"><?php _e('No lists are available', $this -> plugin_name); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($roles)) : ?>
        	<br/>
        	<div id="usersdiv">
        		<div><label class="selectit" style="font-weight:bold;"><input type="checkbox" name="rolesselectall" value="1" id="rolesselectall" onclick="jqCheckAll(this, false, 'roles'); update_subscribers();" /> <?php _e('Select all Roles', $this -> plugin_name); ?></label></div>
        		<div class="scroll-list">
        			<?php foreach ($roles as $role_key => $role_name) : ?>
        				<div><label class="selectit"><input onclick="update_subscribers();" <?php echo (!empty($_POST['roles']) && is_array($_POST['roles']) && in_array($role_key, $_POST['roles'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="roles[]" value="<?php echo $role_key; ?>" id="roles_<?php echo $role_key; ?>" /> <?php echo __($role_name); ?><?php echo (!empty($count_users['avail_roles'][$role_key])) ? ' (' . sprintf(__('%s users'), $count_users['avail_roles'][$role_key]) . ')' : ''; ?></label></div>
        			<?php endforeach; ?>
        		</div>
        	</div>
        <?php endif; ?>
	</div>
	<div id="mailingliststabs-segment">
		<?php if (apply_filters('newsletters_admin_createnewsletter_daterangesettings', true)) : ?>
	        <div class="misc-pub-section">
	        	<h4><label><input onclick="update_subscribers(); if (this.checked == true) { jQuery('#daterange_div').show(); } else { jQuery('#daterange_div').hide(); }" <?php echo (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="daterange" value="Y" id="daterange" /> <?php _e('Specify date range', $this -> plugin_name); ?></label>
	        	<?php echo $Html -> help(__('Specify a date range with a from/to date that subscribers subscribed to include in this newsletter. Both the From and To dates are required and should be in the format YYYY-MM-DD (without time).', $this -> plugin_name)); ?></h4>
	        	
	        	<div id="daterange_div" style="display:<?php echo (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") ? 'block' : 'none'; ?>;">
	        		<p>
	        			<label for="daterangefrom"><?php _e('From Date', $this -> plugin_name); ?></label>
	        			<input onblur="update_subscribers();" onkeyup="update_subscribers();" type="text" name="daterangefrom" value="<?php echo esc_attr(stripslashes($_POST['daterangefrom'])); ?>" id="daterangefrom" class="widefat" style="width:120px;" />
	        		</p>
	        		<p>
	        			<label for="daterangeto"><?php _e('To Date', $this -> plugin_name); ?></label>
	        			<input onblur="update_subscribers();" onkeyup="update_subscribers();" type="text" name="daterangeto" value="<?php echo esc_attr(stripslashes($_POST['daterangeto'])); ?>" id="daterangeto" class="widefat" style="width:120px;" />
	        		</p>
	        	</div>
	        	
	        	<script type="text/javascript">
	        	jQuery(document).ready(function() {
		        	jQuery('#daterangefrom').datepicker({showButtonPanel:true, numberOfMonths:1, changeMonth:true, changeYear:true, defaultDate:"<?php echo $_POST['daterangefrom']; ?>", dateFormat:"yy-mm-dd"});
		        	jQuery('#daterangeto').datepicker({showButtonPanel:true, numberOfMonths:1, changeMonth:true, changeYear:true, defaultDate:"<?php echo $_POST['daterangeto']; ?>", dateFormat:"yy-mm-dd"});
	        	});
	        	</script>
	        </div>
	    <?php endif; ?>
	    <?php if (apply_filters('newsletters_admin_createnewsletter_fieldsconditionssettings', true)) : ?>
	        <?php $Db -> model = $Field -> model; ?>
	        <?php $fieldsquery = "SELECT `id`, `title`, `type`, `validation`, `slug`, `fieldoptions` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `type` = 'text' OR `type` = 'radio' OR `type` = 'select' OR `type` = 'pre_country' OR `type` = 'pre_gender' ORDER BY `order` ASC"; ?>
	        <?php if ($fields = $wpdb -> get_results($fieldsquery)) : ?>
	        	<div class="misc-pub-section">
	                <h4><label><input <?php echo (!empty($_POST['dofieldsconditions']) && !empty($_POST['conditions'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="dofieldsconditions" value="1" id="dofieldsconditions" onclick="update_subscribers(); if (this.checked == true) { jQuery('#fieldsconditions').show(); } else { jQuery('#fieldsconditions').hide(); }" /> <?php _e('Fields Conditions', $this -> plugin_name); ?></label>
	                <?php echo $Html -> help(__('The fields conditions work on the custom fields of your subscribers. You can filter or segment the subscribers in the chosen mailing list(s) to queue/send to subscribers with specific custom field values only. For example, with a "Gender" custom field, you can choose "Male" here under fields conditions to send only to male subscribers.', $this -> plugin_name)); ?></h4>
	                
	                <div id="fieldsconditions" style="display:<?php echo (!empty($_POST['dofieldsconditions']) && !empty($_POST['conditions'])) ? 'block' : 'none'; ?>;">
	                	<p>
		                	<?php _e('Match', $this -> plugin_name); ?>
		                	<select onchange="update_subscribers();" name="fieldsconditionsscope" id="fieldsconditionsscope">
		                		<option <?php echo (empty($_POST['conditionsscope']) || $_POST['conditionsscope'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php _e('all', $this -> plugin_name); ?></option>
		                		<option <?php echo (!empty($_POST['conditionsscope']) && $_POST['conditionsscope'] == "any") ? 'selected="selected"' : ''; ?> value="any"><?php _e('any', $this -> plugin_name); ?></option>
		                	</select>
		                	<?php _e('of these conditions:', $this -> plugin_name); ?>
		                </p>
	                
						<?php foreach ($fields as $field) : ?>
	                    	<?php $supportedfields = array('text', 'radio', 'select', 'pre_country', 'pre_gender'); ?>
	                    	<?php if (!empty($field -> type) && in_array($field -> type, $supportedfields)) : ?>
	                            <p>
	                                <label for="fields_<?php echo $field -> id; ?>" style="font-weight:normal;"><?php echo __($field -> title); ?></label><br/>
	                                
	                                <small>
	                                <?php
	                                
	                                $condquery = false;
	                                $condquery = $_POST['condquery'][$field -> slug];
	                                
	                                switch ($field -> validation) {
	                                	case 'numeric'					:
	                                		?>
	                                		<label><input onclick="update_subscribers();" <?php echo (!empty($condquery) && $condquery == "smaller") ? 'checked="checked"' : ''; ?> type="radio" name="condquery[<?php echo $field -> slug; ?>]" value="smaller" id="condquery_<?php echo $field -> slug; ?>_smaller" /> <?php _e('Smaller', $this -> plugin_name); ?></label>
	                                		<label><input onclick="update_subscribers();" <?php echo (!empty($condquery) && $condquery == "larger") ? 'checked="checked"' : ''; ?> type="radio" name="condquery[<?php echo $field -> slug; ?>]" value="larger" id="condquery_<?php echo $field -> slug; ?>_larger" /> <?php _e('Larger', $this -> plugin_name); ?></label>
	                                		<?php
		                                case 'notempty'					:
		                                default							:
		                                	?>
	                                		<label><input onclick="update_subscribers();" <?php echo (empty($condquery) || (!empty($condquery) && $condquery == "equals")) ? 'checked="checked"' : ''; ?> type="radio" name="condquery[<?php echo $field -> slug; ?>]" value="equals" id="condquery_<?php echo $field -> slug; ?>_equals" /> <?php _e('Equals', $this -> plugin_name); ?></label>
											<label><input onclick="update_subscribers();" <?php echo (!empty($condquery) && $condquery == "contains") ? 'checked="checked"' : ''; ?> type="radio" name="condquery[<?php echo $field -> slug; ?>]" value="contains" id="condquery_<?php echo $field -> slug; ?>_contains" /> <?php _e('Contains', $this -> plugin_name); ?></label>
		                                	<?php
		                                	break;
	                                }
	                                
	                                ?>
	                                </small>	                                
	                                
	                                <?php
	                                
	                                switch ($field -> type) {
	                                	case 'text'				:
	                                		?>
	                                		
	                                		<input onkeyup="update_subscribers();" type="text" name="fields[<?php echo $field -> slug; ?>]" value="<?php echo esc_attr(stripslashes($_POST['fields'][$field -> slug])); ?>" id="fields_<?php echo $field -> id; ?>" />
	                                		
	                                		<?php
	                                		break;
										case 'radio'			:									
											?>
	                                        
	                                        <?php if ($fieldoptions = unserialize($field -> fieldoptions)) : ?>
	                                        	<?php $r = 1; ?>
	                                            <label><input <?php echo (empty($_POST['fields'][$field -> slug])) ? 'checked="checked"' : ''; ?> type="radio" name="fields[<?php echo $field -> slug; ?>]" value="" onclick="update_subscribers();" id="fields_<?php echo $field -> id; ?>-0" /> <?php _e('ALL', $this -> plugin_name); ?></label><br/>
	                                        	<?php foreach ($fieldoptions as $fieldoption_key => $fieldoption_val) : ?>
	                                            	<label><input <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == $fieldoption_key) ? 'checked="checked"' : ''; ?> onclick="update_subscribers();" type="radio" name="fields[<?php echo $field -> slug; ?>]" value="<?php echo $fieldoption_key; ?>" id="fields_<?php echo $field -> id; ?>-<?php echo $r; ?>"  /> <?php echo __($fieldoption_val); ?></label><br/>
	                                                <?php $r++; ?>
	                                            <?php endforeach; ?>
	                                        <?php endif; ?>
	                                        
	                                        <?php
											break;
	                                    case 'select'			:
	                                        ?><select style="max-width:250px;" name="fields[<?php echo $field -> slug; ?>]" id="fields_<?php echo $field -> id; ?>" onchange="update_subscribers();">
	                                        <option value=""><?php _e('- Select -', $this -> plugin_name); ?></option>
	                                        <?php 
	                                        
	                                        $fieldoptions = @unserialize($field -> fieldoptions);
	                                        foreach ($fieldoptions as $fieldoption_key => $fieldoption_val) {
	                                            ?><option <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == $fieldoption_key) ? 'selected="selected"' : ''; ?> value="<?php echo $fieldoption_key; ?>"><?php echo __($fieldoption_val); ?></option><?php	
	                                        }
	                                        
	                                        ?>
	                                        </select><?php
	                                        break;
										case 'pre_country'		:
											?>
	                                        
	                                        <?php if ($countries = $wpmlCountry -> select()) : ?>
	                                            <select style="max-width:250px;" name="fields[<?php echo $field -> slug; ?>]" id="fields_<?php echo $field -> id; ?>" onchange="update_subscribers();">
	                                                <option value=""><?php _e('- Select Country -', $this -> plugin_name); ?></option>
	                                                <?php foreach ($countries as $country_id => $country_name) : ?>
	                                                	<option <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == $country_id) ? 'selected="selected"' : ''; ?> value="<?php echo $country_id; ?>"><?php echo $country_name; ?></option>
	                                                <?php endforeach; ?>
	                                            </select>
	                                        <?php endif; ?>
	                                        
	                                        <?php
											break;
										case 'pre_gender'		:
											?>
	                                        
	                                        <select style="max-width:250px;" name="fields[<?php echo $field -> slug; ?>]" id="fields_<?php echo $field -> id; ?>" onchange="update_subscribers();">
	                                        	<option value=""><?php _e('- Select Gender -', $this -> plugin_name); ?></option>
	                                            <option <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == "male") ? 'selected="selected"' : ''; ?> value="male"><?php _e('Male', $this -> plugin_name); ?></option>
	                                            <option <?php echo (!empty($_POST['fields'][$field -> slug]) && $_POST['fields'][$field -> slug] == "female") ? 'selected="selected"' : ''; ?> value="female"><?php _e('Female', $this -> plugin_name); ?></option>
	                                        </select>
	                                        
	                                        <?php
											break;	
	                                }
	                                
	                                ?>
	                            </p>
	                        <?php endif; ?>
	                    <?php endforeach; ?>
	                </div>
	            </div>
	        <?php endif; ?>
	    <?php endif; ?>
	</div>
</div>

<div class="submitbox">
	<div>
		<div class="misc-pub-section">
		
		</div>
		 
        <!-- Mailing Lists Errors -->
        <?php global $errors, $wpdb; ?>
        <?php if (!empty($errors['mailinglists'])) : ?>
            <p class="<?php echo $this -> pre; ?>error"><?php echo $errors['mailinglists']; ?></p>
        <?php endif; ?>
        
        <?php if (apply_filters('newsletters_admin_createnewsletter_subscribercount', true)) : ?>
	        <div class="misc-pub-section misc-pub-section-last">
	            <div id="subscriberscount">
	                <p><?php _e('0 subscribers total', $this -> plugin_name); ?></p>
	            </div>
	            
	            <p>
	            	<a class="button button-secondary" id="updatesubscriberscountbutton" href="javascript:update_subscribers();"><?php _e('Update Count', $this -> plugin_name); ?></a>
	            	<?php echo $Html -> help(__('Click this button to update the subscribers count above in real-time. The subscribers count is an accurate count of how many subscribers this newsletter will be sent to based on the group, mailing list, fields conditions and other selections made.', $this -> plugin_name)); ?>	
	            </p>
	        </div>
	    <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
var srequest = false;

jQuery(document).ready(function() {
	<?php if (!empty($_POST['mailinglists']) || !empty($_POST['roles'])) : ?>
		update_subscribers();
	<?php endif; ?>	
	
	var mailingliststabscookieid = jQuery.cookie('mailingliststabscookie') || 0;
	jQuery('#mailingliststabs').tabs({
		active:mailingliststabscookieid,
		activate: function(event, ui) {
			jQuery.cookie("mailingliststabscookie", ui.newTab.index(), {expires:365, path:'/'});
		}
	});
});

function update_subscribers() {
	var data = {action:"subscribercount", fieldsconditionsscope:jQuery('#fieldsconditionsscope').val(), mailinglists:[], roles:{}, groups:[], fields:[], condquery:{}};
	if (srequest) { srequest.abort(); }
	jQuery('#updatesubscriberscountbutton').attr('disabled', "disabled");
	
	jQuery('input:checkbox[name="mailinglists[]"]:checked').each(function() {
		var mailinglist_id = jQuery(this).val();
		data['mailinglists'].push(mailinglist_id);
	});
	
	jQuery('input:checkbox[name="roles[]"]:checked').each(function() {
		var role_key = jQuery(this).val();
		data['roles'][role_key] = 1;
	});
	
	jQuery('input:checkbox[name="groups[]"]:checked').each(function() {
		var group_id = jQuery(this).val();
		data['groups'].push(group_id);	
	});
	
	if (jQuery('#daterange').is(':checked')) {
		data['daterange'] = "Y";
		data['daterangefrom'] = jQuery('#daterangefrom').val();
		data['daterangeto'] = jQuery('#daterangeto').val();
	}
	
	if (jQuery('input:checkbox[id="dofieldsconditions"]:checked').length > 0) {			
		var fieldsarray = new Array();
		var f = 0;
		jQuery('[name^="fields"]').each(function() {
			if (jQuery(this).attr('type') == "radio") {					
				if (jQuery(this).is(":checked")) {
					if (jQuery(this).val() != "") {
						fieldsarray[f] = new Array(jQuery(this).attr('id'), jQuery(this).val());	
					}
				}
			} else {
				if (jQuery(this).val() != "") {
					fieldsarray[f] = new Array(jQuery(this).attr('id'), jQuery(this).val());
				}
			}
			
			f++;
		});
		
		data['fields'] = fieldsarray;
		
		var condqueryarray = {};	//create an associative array for the condition queries
		var f = 0;
		jQuery('[name^="condquery"]').each(function() {
			if (jQuery(this).is(":checked")) {
				condqueryarray[jQuery(this).attr('name').split('[').pop().replace(']', '')] = jQuery(this).val();
			}
		});
		
		data['condquery'] = condqueryarray;
	}
	
	jQuery("#subscriberscount").html('<p><img src="<?php echo $this -> url(); ?>/images/loading.gif" /> <?php echo addslashes(__('loading subscriber count...', $this -> plugin_name)); ?></p>');
		
	srequest = jQuery.post(ajaxurl, data, function(response) {
		if (response == "0") {
			jQuery('#sendbutton').attr('disabled', "disabled");
			jQuery('#subscriberscount').html('<?php echo addslashes(__('No subscribers available.', $this -> plugin_name)); ?>');
		} else {
			delete data.action;
			//newresponse = '<p><a href="" onclick="jQuery.colorbox({href:\'' + ajaxurl + '?action=subscribercountdisplay&' + jQuery.param(data) + '\'}); return false;">' + response + '</a></p>';
			newresponse = '<p>' + response + '</p>';
			jQuery('#sendbutton').removeAttr('disabled');
			jQuery('#subscriberscount').html(newresponse);
			jQuery('#updatesubscriberscountbutton').removeAttr('disabled');
		}
	});
}
</script>