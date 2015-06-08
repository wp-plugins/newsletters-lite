<div class="wrap newsletters <?php echo $this -> pre; ?>">
	<h2><?php _e('Save a Group', $this -> plugin_name); ?></h2>
	<form action="?page=<?php echo $this -> sections -> groups; ?>&amp;method=save" method="post" id="groupform">
		<?php echo $Form -> hidden('wpmlGroup[id]'); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmlGroup.title"><?php _e('Group Title', $this -> plugin_name); ?></label></th>
					<td><?php echo $Form -> text('wpmlGroup[title]', array('placeholder' => __('Enter group title here', $this -> plugin_name))); ?></td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php echo $Form -> submit(__('Save Group', $this -> plugin_name)); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php _e('Continue editing', $this -> plugin_name); ?></label>
			</div>
		</p>
	</form>
</div>