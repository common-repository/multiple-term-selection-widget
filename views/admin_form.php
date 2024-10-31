<?php global $mtsw; ?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e('Multiple Term Selection Widget Settings', 'mtsw'); ?></h2>

	<?php if ( $mtsw->get_mtsw_post_types() ) : ?>

	<form method="post" action="options.php" id="mtsw_form">                                                                                                                
		<?php settings_fields( 'mtsw' ); ?>
		<?php do_settings_sections( 'mtsw' ); ?>

		<p class="submit"><input type="submit" name="mtsw_form_submit" class="button-primary" value="<?php _e('Save Changes', 'mtsw'); ?>"/></p>
	</form>

	<form method="post" action="options.php" id="mtsw_default_form">                                                                                                                
		<?php settings_fields( 'mtsw_default' ); ?>
		<?php do_settings_sections( 'mtsw_default' ); ?>

		<p class="submit"><input type="submit" name="mtsw_default_form_submit" class="button-primary" value="<?php _e('Save Changes', 'mtsw'); ?>"/></p>
	</form>

	<?php else : ?>

	<p><b><?php _e('No Post Types can be used. You need at least one hierarchical taxonomy (with at least one parent and one child terms) related to one post type to use Multiple Term Selection Widget !', 'mtsw'); ?></b></p>

	<?php endif; ?>
</div>