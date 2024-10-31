
<table class="form-table">
	<tbody>

		<tr>
			<th scope="row">
				<?php _e('Use jQuery plugin Select2', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Use jQuery plugin Select2', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_form[select2]' value='1' <?php if ( $mtsw_global_options->select2 ) echo 'checked="checked"'; ?> />
						<span><?php _e('Yes', 'mtsw'); ?></span>
					</label>
					<label>
						<input type='radio' name='mtsw_form[select2]' value='0' <?php if ( !$mtsw_global_options->select2 ) echo 'checked="checked"'; ?> />
						<span><?php _e('No', 'mtsw'); ?></span>
					</label>
				</fieldset>
			</td>
		</tr>

		<tr id="mtsw-tr-css" <?php if ( !$mtsw_global_options->select2 ) echo 'style="display:none"'; ?> >
			<th scope="row">
				<?php _e('Use MTSW customize css for plugin Select2', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Use MTSW customize css for plugin Select2', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_form[mtsw_select2_css]' value='1' <?php if ( $mtsw_global_options->mtsw_select2_css ) echo 'checked="checked"'; ?> />
						<span><?php _e('Yes', 'mtsw'); ?></span>
					</label>
					<label>
						<input type='radio' name='mtsw_form[mtsw_select2_css]' value='0' <?php if ( !$mtsw_global_options->mtsw_select2_css ) echo 'checked="checked"'; ?> />
						<span><?php _e('No', 'mtsw'); ?></span>
					</label>
				</fieldset>
			</td>
		</tr>

	</tbody>
</table>