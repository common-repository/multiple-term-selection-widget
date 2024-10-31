
<table class="form-table">
	<tbody>

		<tr valign="top"> 
			<th scope="row">
				<label>
					<?php _e('Id', 'mtsw'); ?>
				</label>
			</th> 
			<td>
				<input name="mtsw_default_form[id]" type="hidden" value="mtsw-default"/>
				mtsw-default
			</td> 
		</tr>

		<tr valign="top"> 
			<th scope="row">
				<label for="mtsw_default_form[title]">
					<?php _e('Title', 'mtsw'); ?>
				</label>
			</th> 
			<td>
				<input name="mtsw_default_form[title]" type="text" id="mtsw_default_form[title]" value="<?php echo $mtsw_options->title; ?>" class="regular-text"/>
			</td> 
		</tr>

		<tr valign="top"> 
			<th scope="row">
				<label for="mtsw_default_form[post_type]">
					<?php _e('Post Type', 'mtsw'); ?>
				</label>
			</th> 
			<td>
				<select name="mtsw_default_form[post_type]" id="mtsw_default_form[post_type]">
				<?php
					$mtsw_post_types = $mtsw->get_mtsw_post_types();
					foreach( $mtsw_post_types as $post_type ) : ?>
						<option value="<?php echo $post_type->name; ?>" <?php if ( $mtsw_options->post_type == $post_type->name ) echo 'selected="selected"' ?> >
							<?php echo $post_type->labels->singular_name; ?>
						</option>
				<?php endforeach; ?>
				</select>
			</td> 
		</tr>

		<tr valign="top"> 
			<th scope="row">
				<label for="mtsw_default_form[taxonomy]">
					<?php _e('Taxonomy', 'mtsw'); ?>
				</label>
			</th> 
			<td>
				<select name="mtsw_default_form[taxonomy]" id="mtsw_default_form[taxonomy]">
				<?php
					$mtsw_taxonomies = $mtsw->get_mtsw_taxonomies();
					foreach ($mtsw_taxonomies[$mtsw_options->post_type] as $taxonomy) : ?>
						<option value="<?php echo $taxonomy->name; ?>" <?php if ( $mtsw_options->taxonomy == $taxonomy->name) echo 'selected="selected"' ?> >
							<?php echo $taxonomy->labels->singular_name; ?>
						</option>
				<?php endforeach; ?>
				</select>
			</td> 
		</tr>

		<tr valign="top"> 
			<th scope="row">
				<label for="mtsw_default_form[included_parent_term_ids]">
					<?php _e('Included Terms', 'mtsw'); ?>
				</label>
			</th>
			<td>
				<fieldset id="mtsw_default_form-included_terms" class="included_terms">
					<legend class="screen-reader-text">
						<span><?php _e('Included Terms', 'mtsw'); ?></span>
					</legend>
					<ul class="ui-sortable">
					<?php
						$mtsw_parent_terms = $mtsw_options->get_mtsw_parent_terms();
						$mtsw_children_terms = $mtsw_options->get_mtsw_children_terms();
						foreach ( $mtsw_parent_terms as $parent_term ) :
							$is_parent_term_checked = in_array( $parent_term->term_id, $mtsw_options->included_parent_term_ids ); ?>
						<li class="ui-state-default<?php if ( !$is_parent_term_checked ) echo ' ui-state-disabled'; ?>">
							<label>
								<input name="mtsw_default_form[included_parent_term_ids][]" type="checkbox" value="<?php echo $parent_term->term_id; ?>" <?php if ( $is_parent_term_checked ) echo 'checked="checked"'; ?>/>
								<?php echo $parent_term->name; ?>
							</label>
							<fieldset class="included_children_terms" id="included_children_terms_<?php echo $parent_term->term_id; ?>" style="<?php if ( !$is_parent_term_checked ) echo 'display:none;'; ?>">
								<legend class="screen-reader-text">
									<span><?php printf( __('Included Children Terms of %s', 'mtsw'), $parent_term->name ); ?></span>
								</legend>
								<ul class="ui-sortable">
								<?php
								foreach ( $mtsw_children_terms[$parent_term->term_id] as $children_term ) :
									$is_children_term_checked = ( preg_match( '/^1/', $mtsw_options->automatically_add_children ) ) ? ( !in_array( $children_term['term']->term_id, $mtsw_options->excluded_children_term_ids ) ) : ( in_array( $children_term['term']->term_id, $mtsw_options->included_children_term_ids ) ); ?>
									<li class="ui-state-default<?php if ( !$is_children_term_checked ) echo ' ui-state-disabled'; ?>" data-position="<?php echo $children_term['position']; ?>">
										<label>
										<input name="mtsw_default_form[included_children_term_ids][]" type="checkbox" value="<?php echo $children_term['term']->term_id; ?>" <?php if ( $is_children_term_checked ) echo 'checked="checked"'; ?>/>
										<?php echo $children_term['term']->name; ?>
										</label>
									</li>
							<?php endforeach; ?>
								</ul>
							</fieldset>
						</li>
					<?php endforeach; ?>
					</ul>
					<input name="mtsw_default_form[excluded_children_term_ids]" type="hidden" value="<?php echo implode( ',', $mtsw_options->excluded_children_term_ids ); ?>" />
				</fieldset>
			</td> 
		</tr>

		<tr>
			<th scope="row">
				<?php _e('Automatically add new children terms', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Automatically add new children terms', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_default_form[automatically_add_children]' value='0' <?php if ( $mtsw_options->automatically_add_children == '0' ) echo 'checked="checked"'; ?> />
						<span><?php _e('No', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[automatically_add_children]' value='1' <?php if ( $mtsw_options->automatically_add_children == '1' ) echo 'checked="checked"'; ?> />
						<span><?php _e('Yes', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[automatically_add_children]' value='1alpha' <?php if ( $mtsw_options->automatically_add_children == '1alpha' ) echo 'checked="checked"'; ?> />
						<span><?php _e('Yes with alphabetical reordering', 'mtsw'); ?></span>
					</label>
					<br />
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e('Multi-selection', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Multi-selection', 'mtsw'); ?></span>
					</legend>
					<label for="mtsw_default_form[multi_selection]">
						<input name="mtsw_default_form[multi_selection]" type="checkbox" id="mtsw_default_form[multi_selection]" <?php if ( $mtsw_options->multi_selection ) echo 'checked="checked"'; ?> value="1"/>
						<?php _e('Yes', 'mtsw'); ?>
					</label>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e('Search Type', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('User chooses search type', 'mtsw'); ?></span>
					</legend>
					<label for="mtsw_default_form[user_choice]">
						<input name="mtsw_default_form[user_choice]" type="checkbox" id="mtsw_default_form[user_choice]" <?php if ( $mtsw_options->user_choice ) echo 'checked="checked"'; ?> value="1"/>
						<?php _e('User Choice', 'mtsw'); ?>
					</label>
				</fieldset>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php _e('Default Search Type', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Default Search Type', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_default_form[def_search_type]' value='or' <?php if ( $mtsw_options->def_search_type == 'or' ) echo 'checked="checked"'; ?> />
						<span><?php _e('Any', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[def_search_type]' value='and' <?php if ( $mtsw_options->def_search_type == 'and' ) echo 'checked="checked"'; ?> />
						<span><?php _e('All', 'mtsw'); ?></span>
					</label>
					<br />
				</fieldset>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php _e('Blank Search Results', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Blank Search Results', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_default_form[blank_search_type]' value='none' <?php if ( $mtsw_options->blank_search_type == 'none' ) echo 'checked="checked"'; ?> />
						<span><?php _e('None', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[blank_search_type]' value='all' <?php if ( $mtsw_options->blank_search_type == 'all' ) echo 'checked="checked"'; ?> />
						<span><?php _e('All', 'mtsw'); ?></span>
					</label>
					<br />
				</fieldset>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php _e('Ordering', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Ordering', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_default_form[order]' value='default' <?php if ( $mtsw_options->order == 'default' ) echo 'checked="checked"'; ?> />
						<span><?php _e('By default', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[order]' value='title' <?php if ( $mtsw_options->order == 'title' ) echo 'checked="checked"'; ?> />
						<span><?php _e('By title', 'mtsw'); ?></span>
					</label>
					<br />
				</fieldset>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php _e('Hide empty terms', 'mtsw'); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Hide empty terms', 'mtsw'); ?></span>
					</legend>
					<label>
						<input type='radio' name='mtsw_default_form[hide_empty]' value='0' <?php if ( !$mtsw_options->hide_empty ) echo 'checked="checked"'; ?> />
						<span><?php _e('No', 'mtsw'); ?></span>
					</label>
					<br />
					<label>
						<input type='radio' name='mtsw_default_form[hide_empty]' value='1' <?php if ( $mtsw_options->hide_empty ) echo 'checked="checked"'; ?> />
						<span><?php _e('Yes', 'mtsw'); ?></span>
					</label>
					<br />
				</fieldset>
			</td>
		</tr>

		<tr valign="top"> 
			<th scope="row">
				<label for="mtsw_default_form[submit_button]">
					<?php _e('Submit Button Text', 'mtsw'); ?>
				</label>
			</th> 
			<td>
				<input name="mtsw_default_form[submit_button]" type="text" id="mtsw_default_form[submit_button]" value="<?php echo $mtsw_options->submit_button; ?>" class="regular-text"/>
			</td> 
		</tr>

	</tbody>
</table>