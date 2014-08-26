<?php

$debugging = get_option('tridebugging');

?>

<div id="submitpost" class="submitbox">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				<a href="?page=<?php echo $this -> sections -> settings; ?>&amp;method=checkdb"><?php _e('Check/Optimize Database', $this -> plugin_name); ?></a>
				<?php echo $Html -> help(__('This function will check all database tables of the plugin to ensure that all fields/columns are available and created as intended. In addition to that, it will run a simple optimize query on each database table to clear overheads, fix indexes, etc.', $this -> plugin_name)); ?>
			</div>
			<div class="misc-pub-section">
				<a class="delete" onclick="if (!confirm('<?php _e('Are you sure you wish to reset all configuration settings to their defaults?', $this -> plugin_name); ?>')) { return false; }" href="?page=newsletters-settings&amp;method=reset" title="<?php _e('Reset all settings to their defaults', $this -> plugin_name); ?>"><?php _e('Reset Defaults', $this -> plugin_name); ?></a>
				<?php echo $Html -> help(__('Upon confirmation, this action will permanently reset all configuration settings to their defaults. You will not lose lists, subscribers, sent/draft emails or other data, just the actual configuration settings are reset.', $this -> plugin_name)); ?>
			</div>
			<div class="misc-pub-section">
				<a href="?page=<?php echo $this -> sections -> lists; ?>&amp;method=offsitewizard" title="<?php _e('Generate HTML code for an offsite subscription form', $this -> plugin_name); ?>"><?php _e('Generate Offsite Code', $this -> plugin_name); ?></a>
				<?php echo $Html -> help(__('The offsite wizard will assist you in generating static HTML code and a URL to use on any 3rd party website or some 3rd party applications accordingly.', $this -> plugin_name)); ?>
			</div>
			<div class="misc-pub-section misc-pub-section-last">
				<label><input <?php echo (!empty($debugging) && $debugging == 1) ? 'checked="checked"' : ''; ?> type="checkbox" name="debugging" value="1" id="debugging" /> <?php _e('Turn on debugging', $this -> plugin_name); ?></label>
				<?php echo $Html -> help(__('Ticking/checking this setting and saving the settings will turn on debugging. It will turn on PHP error reporting and also WordPress database errors. It will help you to troubleshoot problems where something is not working as expected or a blank page is appearing.', $this -> plugin_name)); ?>
			</div>
		</div>
	</div>
	<div id="major-publishing-actions">
		<div id="publishing-action">
			<input id="publish" class="button button-primary button-large" type="submit" name="save" value="<?php _e('Save Settings', $this -> plugin_name); ?>" class="button button-highlighted" />	
		</div>
		<br class="clear" />
	</div>
</div>