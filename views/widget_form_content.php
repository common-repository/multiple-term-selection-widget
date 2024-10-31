
	<fieldset>
		<input type="hidden" name="mtsw-form[id]" value="<?php echo $mtsw_widget_options->id; ?>"  />
		<input type="hidden" name="mtsw-form[post_type]" value="<?php echo $mtsw_widget_options->post_type; ?>"  />
    <input type="hidden" name="mtsw-form[taxonomy]" value="<?php echo $mtsw_widget_options->taxonomy; ?>"  />
    <input type="hidden" name="mtsw-form[blank_search_type]" value="<?php echo $mtsw_widget_options->blank_search_type; ?>" />
    <input type="hidden" name="mtsw-form[order]" value="<?php echo $mtsw_widget_options->order; ?>"  />
	   <?php foreach ( $included_parent_terms as $parent_term ) : ?>
	    <div class="mtsw-select-wrapper">
	    	<label for="mtsw-form-children-term-ids-<?php echo $parent_term->term_id; ?>"><?php echo $parent_term->name; ?> :</label>
	    	<?php if ( !$mtsw->global_options->select2 && $mtsw_widget_options->multi_selection ): ?>
		    	<span class="mtsw-indication">
		    		<?php _e('You can select multiple options', 'mtsw'); ?>
		    	</span>
	    	<?php endif; ?>
				<select name="mtsw-form[children_term_ids][<?php echo $parent_term->term_id; ?>][]" id="mtsw-form-children-term-ids-<?php echo $parent_term->term_id; ?>" <?php if ( $mtsw_widget_options->multi_selection ) echo 'multiple'; ?>
					<?php if ( $mtsw->global_options->select2 ) : ?>
						data-placeholder="<?php if ( $mtsw_widget_options->multi_selection ) _e('Select some options', 'mtsw'); else _e('Select an option', 'mtsw'); ?>"
					<?php endif; ?> >
					
					<?php if ( !$mtsw->global_options->select2 ) : ?>
						<option value="0">
							<?php if ( $mtsw_widget_options->multi_selection ) _e('- No option -', 'mtsw'); else _e('&raquo; Select an option', 'mtsw'); ?>
						</option>
					<?php else: ?>
						<option></option>
					<?php endif; ?>
					<?php foreach ( $allowed_children_terms[$parent_term->term_id] as $children_term ) : ?>
						<option value="<?php echo $children_term['term']->term_id; ?>" <?php if ( in_array( $children_term['term']->term_id, $selected_term_ids ) ) echo 'selected'; else if ( $mtsw_widget_options->hide_empty && ( $search_type == 'and' ) && !in_array( $children_term['term']->term_id, $non_empty_children_term_ids[$parent_term->term_id] ) ) echo 'disabled'; ?> >
							<?php echo $children_term['term']->name; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endforeach; ?>
		<?php if ( $mtsw_widget_options->user_choice ) : ?>
			<div class="mtsw-select-wrapper">
        <label><?php _e('Search Type:', 'mtsw'); ?></label>
        <label class="mtsw-radio-search-type">
        	<input type="radio" name="mtsw-form[def_search_type]" value="or" title="Any" <?php if ( $search_type == 'or' ) echo 'checked="checked"'; ?> />
          <?php _e('Any', 'mtsw'); ?>
        </label>
        <label class="mtsw-radio-search-type">
          <input type="radio" name="mtsw-form[def_search_type]" value="and" title="And" <?php if ( $search_type == 'and' ) echo 'checked="checked"'; ?> />
          <?php _e('All', 'mtsw'); ?>
        </label>
      </div>
    <?php else : ?>
    	<input type="hidden" name="mtsw-form[def_search_type]" value="<?php echo $mtsw_widget_options->def_search_type; ?>" />
    <?php endif; ?>
		<input type="submit" value="<?php echo $mtsw_widget_options->submit_button; ?>" name="mtsw-form[submit]" />
	</fieldset>