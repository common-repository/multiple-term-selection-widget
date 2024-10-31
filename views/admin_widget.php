
      <p>
        <label>
          <?php _e('Id:', 'mtsw'); ?>  <span style="cursor:auto;">mtsw-<?php echo $this->number; ?></span>
        </label> 
        <input name="<?php echo $this->get_field_name('id'); ?>" type="hidden" value="mtsw-<?php echo $this->number; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">
          <?php _e('Title:', 'mtsw'); ?>
        </label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $mtsw_widget_options->title; ?>" />
      </p>

  		<p>
  			<label for="<?php echo $this->get_field_id('post_type'); ?>">
  				<?php _e('Post Type:', 'mtsw'); ?>
  			</label> 
  			<select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" >
  			<?php
          foreach ( $mtsw_post_types as $post_type ) : ?>
  				<option value="<?php echo $post_type->name; ?>" <?php if ( $post_type->name == $mtsw_widget_options->post_type ) echo 'selected="selected"' ?> >
            <?php echo $post_type->labels->singular_name; ?>
          </option>
  			<?php endforeach; ?>
  			</select>
  		</p>

  		<p>
  			<label for="<?php echo $this->get_field_id('taxonomy'); ?>">
  				<?php _e('Taxonomy:', 'mtsw'); ?>
  			</label> 
  			<select id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" >
  			<?php
          $mtsw_taxonomies = $mtsw->get_mtsw_taxonomies();
          foreach ( $mtsw_taxonomies[$mtsw_widget_options->post_type] as $taxonomy) :?>
  				<option value="<?php echo $taxonomy->name; ?>" <?php if ( $taxonomy->name == $mtsw_widget_options->taxonomy ) echo 'selected="selected"'; ?> >
            <?php echo $taxonomy->labels->singular_name; ?>
          </option>
  			<?php endforeach; ?>
  			</select>
  		</p>

      <p>
        <label for="<?php echo $this->get_field_id('included_terms'); ?>">
          <?php _e('Included Terms:', 'mtsw'); ?>
        </label>
      </p>

      <p>
        <fieldset id="<?php echo $this->get_field_id('included_terms'); ?>" class="included_terms">
          <legend class="screen-reader-text">
            <span><?php _e('Included Terms', 'mtsw'); ?></span>
          </legend>
          <ul class="ui-sortable">
          <?php
            $mtsw_parent_terms = $mtsw_widget_options->get_mtsw_parent_terms();
            $mtsw_children_terms = $mtsw_widget_options->get_mtsw_children_terms();
            foreach ( $mtsw_parent_terms as $parent_term ) :
              $is_parent_term_checked = in_array( $parent_term->term_id, $mtsw_widget_options->included_parent_term_ids ); ?>
            <li class="ui-state-default<?php if ( !$is_parent_term_checked ) echo ' ui-state-disabled'; ?>">
              <label>
                <input name="<?php echo $this->get_field_name('included_parent_term_ids') . '[]'; ?>" type="checkbox" value="<?php echo $parent_term->term_id; ?>" <?php if ( $is_parent_term_checked ) echo 'checked="checked"'; ?>/>
                <?php echo $parent_term->name; ?>
              </label>
              <fieldset class="included_children_terms" id="included_children_terms_<?php echo $parent_term->term_id; ?>" style="<?php if ( !$is_parent_term_checked ) echo 'display:none;'; ?>">
                <legend class="screen-reader-text">
                  <span><?php printf( __('Included Children Terms of %s', 'mtsw'), $parent_term->name ); ?></span>
                </legend>
                <ul class="ui-sortable">
                <?php
                foreach ( $mtsw_children_terms[$parent_term->term_id] as $children_term ) :
                  $is_children_term_checked = ( preg_match( '/^1/', $mtsw_widget_options->automatically_add_children ) ) ? ( !in_array( $children_term['term']->term_id, $mtsw_widget_options->excluded_children_term_ids ) ) : ( in_array( $children_term['term']->term_id, $mtsw_widget_options->included_children_term_ids ) ); ?>
                  <li class="ui-state-default<?php if ( !$is_children_term_checked ) echo ' ui-state-disabled'; ?>" data-position="<?php echo $children_term['position']; ?>">
                    <label>
                    <input name="<?php echo $this->get_field_name('included_children_term_ids') . '[]'; ?>" type="checkbox" value="<?php echo $children_term['term']->term_id; ?>" <?php if ( $is_children_term_checked ) echo 'checked="checked"'; ?>/>
                    <?php echo $children_term['term']->name; ?>
                    </label>
                  </li>
              <?php endforeach; ?>
                </ul>
              </fieldset>
            </li>
          <?php endforeach; ?>
          </ul>
          <input name="<?php echo $this->get_field_name('excluded_children_term_ids'); ?>" type="hidden" value="<?php echo implode( ',', $mtsw_widget_options->excluded_children_term_ids ); ?>" />
        </fieldset>
      </p>

      <p>
        <label>
          <?php _e('Automatically add new children terms:', 'mtsw'); ?>
        </label>
      </p>

      <p>
        <input name="<?php echo $this->get_field_name('automatically_add_children'); ?>" type="radio" value="0" <?php if ( $mtsw_widget_options->automatically_add_children == '0' ) echo 'checked="checked"'; ?> />
        <?php _e('No', 'mtsw'); ?>
      </p>

      <p>
        <input name="<?php echo $this->get_field_name('automatically_add_children'); ?>" type="radio" value="1" <?php if ( $mtsw_widget_options->automatically_add_children == '1' ) echo 'checked="checked"'; ?> />
        <?php _e('Yes', 'mtsw'); ?>
      </p>

      <p>
        <input name="<?php echo $this->get_field_name('automatically_add_children'); ?>" type="radio" value="1alpha" <?php if ( $mtsw_widget_options->automatically_add_children == '1alpha' ) echo 'checked="checked"'; ?> />
        <?php _e('Yes with alphabetical reordering', 'mtsw'); ?>
      </p>

      <p>
        <input id="<?php echo $this->get_field_id('multi_selection'); ?>" name="<?php echo $this->get_field_name('multi_selection'); ?>" type="checkbox" value="1" <?php if ( $mtsw_widget_options->multi_selection ) echo 'checked="checked"'; ?> /> 
        <label for="<?php echo $this->get_field_id('multi_selection'); ?>">
          <?php _e('Multi-selection', 'mtsw'); ?>
        </label>
      </p>

      <p>
      	<input id="<?php echo $this->get_field_id('user_choice'); ?>" name="<?php echo $this->get_field_name('user_choice'); ?>" type="checkbox" value="1" <?php if ( $mtsw_widget_options->user_choice ) echo 'checked="checked"'; ?> /> 
      	<label for="<?php echo $this->get_field_id('user_choice'); ?>">
      		<?php _e('User Chooses Search Type', 'mtsw'); ?>
      	</label>
      </p>

      <p>
      	<label>
      		<?php _e('Default Search Type:', 'mtsw'); ?>
      	</label>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('def_search_type'); ?>" type="radio" value="and" <?php if ( $mtsw_widget_options->def_search_type == 'and' ) echo 'checked="checked"'; ?> />
      	<?php _e('All', 'mtsw'); ?>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('def_search_type'); ?>" type="radio" value="or"  <?php if ( $mtsw_widget_options->def_search_type == 'or' ) echo 'checked="checked"'; ?> />
      	<?php _e('Any', 'mtsw'); ?>
      </p>

      <p>
      	<label>
      		<?php _e('Blank Search Results:', 'mtsw'); ?>
      	</label>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('blank_search_type'); ?>" type="radio" value="none" <?php if ( $mtsw_widget_options->blank_search_type == 'none' ) echo 'checked="checked"'; ?> />
      	<?php _e('None', 'mtsw'); ?>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('blank_search_type'); ?>" type="radio" value="all"  <?php if ( $mtsw_widget_options->blank_search_type == 'all' ) echo 'checked="checked"'; ?> />
      	<?php _e('All', 'mtsw'); ?>
      </p>

      <p>
      	<label>
      		<?php _e('Ordering:', 'mtsw'); ?>
      	</label>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('order'); ?>" type="radio" value="default" <?php if ( $mtsw_widget_options->order == 'default' ) echo 'checked="checked"'; ?> />
      	<?php _e('By default', 'mtsw'); ?>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('order'); ?>" type="radio" value="title"  <?php if ( $mtsw_widget_options->order == 'title' ) echo 'checked="checked"'; ?> />
      	<?php _e('By title', 'mtsw'); ?>
      </p>

  		<p>
  			<label>
  				<?php _e('Hide empty terms:', 'mtsw'); ?>
  			</label>
  		</p>

      <p>
      	<input name="<?php echo $this->get_field_name('hide_empty'); ?>" type="radio" value="0" <?php if ( !$mtsw_widget_options->hide_empty ) echo 'checked="checked"'; ?> />
      	<?php _e('No', 'mtsw'); ?>
      </p>

      <p>
      	<input name="<?php echo $this->get_field_name('hide_empty'); ?>" type="radio" value="1" <?php if ( $mtsw_widget_options->hide_empty ) echo 'checked="checked"'; ?> />
      	<?php _e('Yes', 'mtsw'); ?>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('submit_button'); ?>">
          <?php _e('Submit Button Text:', 'mtsw'); ?>
        </label> 
        <input class="widefat" id="<?php echo $this->get_field_id('submit_button'); ?>" name="<?php echo $this->get_field_name('submit_button'); ?>" type="text" value="<?php echo $mtsw_widget_options->submit_button; ?>" />
      </p>