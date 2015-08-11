<?php
												
$settings = array(
	//'wpautop'			=>	false,
	'media_buttons'		=>	true,
	'textarea_name'		=>	'contentarea[' . $contentarea -> number . ']',
	'textarea_rows'		=>	10,
	'quicktags'			=>	true,
	'entities'			=>	"",
	'entity_encoding'	=>	"raw",
);

wp_editor(wpautop(stripslashes($contentarea -> content)), 'contentarea' . $contentarea -> number, $settings); 

?>
<table id="post-status-info" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td id="wp-word-count">
				<span id="word-count">
					<?php echo sprintf(__('Use shortcode %s to display this content', $this -> plugin_name), '<code>[newsletters_content id="' . $contentarea -> number . '"]</code>'); ?>
					<br/><?php echo sprintf(__('And use %s to conditionally display if it is available.', $this -> plugin_name), '<code>[newsletters_if newsletters_content id="' . $contentarea -> number . '"]...[/newsletters_if]</code>'); ?>
				</span>
			</td>
			<td class="autosave-info">
				<span id="autosave" style="display:none;"></span>
			</td>
		</tr>
	</tbody>
</table>

<p>
	<a href="" onclick="if (confirm('<?php echo __('Are you sure you want to remove this content area?', $this -> plugin_name); ?>')) { deletecontentarea('<?php echo $contentarea -> number; ?>', '<?php echo $contentarea -> history_id; ?>'); } return false;" class="button button-secondary"><?php _e('Delete', $this -> plugin_name); ?></a>
</p>

<script type="text/javascript">
contentarea++;
</script>