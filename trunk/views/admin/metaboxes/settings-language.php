<?php if ($num_langs = get_option('langswitch_num_langs')) : ?>
	<?php $n = 0; ?>
	<?php $languages = $this -> get_option('languages'); ?>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th></th>
				<th><?php _e('Default Mailing List', $this -> plugin_name); ?></th>
				<td>
					<?php if ($lists = $Mailinglist -> select(true)) : ?>
						<select name="languages_default">
							<?php foreach ($lists as $list_id => $list_title) : ?>
								<option value="<?php echo $list_id; ?>"><?php echo $list_title; ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
					<span class="howto"><?php _e('default mailing list to use when a language is chosen for which a default list wasn\'t specified below', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<?php while ($n < $num_langs) : ?>
				<?php if (${'langswitch_info' . $n} = get_option('langswitch_lang_info' . $n . 'language')) : ?>
					<?php ${'langswitch_code' . $n} = get_option('langswitch_lang_info' . $n . 'code'); ?>
					<tr>
						<th style="width:2.2em; padding:7px 0 22px;" class="check-column">
							<input <?php echo (!empty($languages['check']) && in_array($n, $languages['check'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="languages[check][<?php echo ${'langswitch_code' . $n}; ?>]" value="<?php echo $n; ?>" id="langswitch_check<?php echo $n; ?>" /></th>
						<th><label for="langswitch_check<?php echo $n; ?>"><?php echo ${'langswitch_info' . $n}; ?></label></th>
						<td>
							<?php if ($lists = $Mailinglist -> select(true)) : ?>
								<select name="languages[lists][<?php echo $n; ?>]">
									<option value="">- <?php _e('Select Default List', $this -> plugin_name); ?> -</option>
									<?php foreach ($lists as $list_id => $list_title) : ?>
										<option <?php echo (!empty($languages['lists'][$n]) && $languages['lists'][$n] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo $list_id; ?>"><?php echo $list_title; ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
				<?php $n++; ?>
			<?php endwhile; ?>
		</tbody>
	</table>
<?php endif; ?>