<!-- Permissions Settings -->

<?php

global $wp_roles;
$roles = $this -> get_option('wproles');
$permissions = $this -> get_option('permissions');

?>

<?php if (current_user_can('edit_users') || is_super_admin()) : ?>
	<table class="form-table">
        <tbody>
        	<tr>
        		<th><label for=""><?php _e('Send to Roles Permissions', $this -> plugin_name); ?></label>
        		<?php echo $Html -> help(__('Choose which user roles are able to see the roles checkboxes list under Newsletters > Create Newsletter to send to users.', $this -> plugin_name)); ?></th>
        		<td>
        			<label style="font-weight:bold;"><input type="checkbox" name="" value="" id="" /> <?php _e('Select all', $this -> plugin_name); ?></label><br/>
        			<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
        				<label><input <?php echo (!empty($permissions['newsletters_admin_send_sendtoroles']) && in_array($role_key, $permissions['newsletters_admin_send_sendtoroles'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="permissions[newsletters_admin_send_sendtoroles][]" value="<?php echo $role_key; ?>" id="permissions_<?php echo $block; ?>_<?php echo $role_key; ?>" /> <?php echo __($role_name); ?></label><br/>
        			<?php endforeach; ?>
        		</td>
        	</tr>
        </tbody>
    </table>
    
    <h2><?php _e('Global Plugin Permissions', $this -> plugin_name); ?></h2>

	<div class="scroll-list" style="max-height:400px;">
		<table class="form-table">
			<thead>
				<tr>
					<th>&nbsp;</th>
	    			<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
	    				<th style="font-weight:bold; text-align:center; white-space:nowrap;">
	    					<?php echo $role_name; ?>
	    				</th>	
	    			<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this -> sections as $section_key => $section_menu) : ?>
					<tr class="<?php echo $class = (empty($class)) ? 'arow' : ''; ?>">
						<th style="white-space:nowrap; text-align:right;"><?php echo $Html -> section_name($section_key); ?></th>
						<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
							<td style="text-align:center;"><input <?php echo ($role_key == "administrator" || (!empty($permissions[$section_key]) && in_array($role_key, $permissions[$section_key]))) ? 'checked="checked"' : ''; ?> type="checkbox" name="permissions[<?php echo $section_key; ?>][]" value="<?php echo esc_attr(stripslashes($role_key)); ?>" id="<?php echo $section_key; ?>_<?php echo $role_key; ?>" /></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>