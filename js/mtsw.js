jQuery(document).ready(function($) {

	var select2Options = {
		'width': 'resolve',
		'allowClear': true,
		'minimumResultsForSearch': -1,
	}

	if (mtsw_object.mtsw_select2_css)
		select2Options.dropdownCssClass = 'mtsw-select2';

	$('select[name^="mtsw-form"]').select2(select2Options);

});