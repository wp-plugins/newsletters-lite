<div id="submitpost" class="submitbox">
	<div id="minor-publishing">
		
	</div>
	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php if ($Html -> field_value('Template[id]') != "") : ?>
				<a class="submitdelete deletion" href="?page=<?php echo $this -> sections -> templates; ?>&amp;method=delete&amp;id=<?php echo $Html -> field_value('Template[id]'); ?>" title="<?php _e('Delete this template', $this -> plugin_name); ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to remove this snippet?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Delete Snippet', $this -> plugin_name); ?></a>
			<?php endif; ?>
		</div>
		<div id="publishing-action">
			<input id="publish" type="submit" class="button button-primary button-large" name="save" value="<?php _e('Save Snippet', $this -> plugin_name); ?>" />
		</div>
		<br class="clear" />
	</div>
</div>