<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Group', $this -> plugin_name); ?></h2>
	<form onsubmit="jQuery.Watermark.HideAll();" action="?page=<?php echo $this -> sections -> groups; ?>&amp;method=save" method="post" id="groupform">
		<?php echo $Form -> hidden('wpmlGroup[id]'); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmlGroup.title"><?php _e('Group Title', $this -> plugin_name); ?></label></th>
					<td><?php echo $Form -> text('wpmlGroup[title]'); ?></td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php echo $Form -> submit(__('Save Group', $this -> plugin_name)); ?>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="wpmlGroup[title]"]').Watermark('<?php echo addslashes(__('Enter group title here', $this -> plugin_name)); ?>');
});
</script>