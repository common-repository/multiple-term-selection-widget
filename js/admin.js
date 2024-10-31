jQuery(document).ready(function($) {

	function mtsw_admin_sortable() {
		$('.ui-sortable').sortable({
			cursor: "move",
			placeholder: "ui-state-highlight",
			cancel: ".ui-state-disabled"
		});
	}
	mtsw_admin_sortable();

	$('input[name="mtsw_form[select2]"]').change(function(){
		if ($(this).val() == '0')
			$('#mtsw-tr-css').hide();
		else
			$('#mtsw-tr-css').show();
	})

	// Modify taxonomy options if post-type is changed
	$('body').on('change', 'select[name$="[post_type]"]', function() {
		var attrName = $(this).attr('name');

		if (/^((?:mtsw_default_form|widget-mtsw_widget)(?:\[\d+\])?)\[post_type\]$/.test(attrName)) {
			var selectTaxonomy = $('select[name="' + RegExp.$1 + '[taxonomy]"]');

			if (selectTaxonomy.length) {
				
				var data = {
					action: 'post_type_change',
					post_type: $(this).val()
				};

				$.post(ajaxurl, data, function(response) {
					if (response)
						selectTaxonomy.html(response);
						selectTaxonomy.trigger("change");
				});

			}
		}
	});

	// Modify terms options if taxonomy is changed
	$('body').on('change', 'select[name$="[taxonomy]"]', function() {
		var attrName = $(this).attr('name');

		if (/^((mtsw_default_form|widget-mtsw_widget)(?:\[(\d+)\])?)\[taxonomy\]$/.test(attrName)) {
			var attrPreName = RegExp.$1;
			var selectPostType = $('select[name="' + attrPreName + '[post_type]"]');
			var middleId = '';
			if (RegExp.$3 !== "")
				middleId = RegExp.$3 + '-';
			var includedTerms = $('#' + RegExp.$2 + '-' + middleId + 'included_terms');
				
			var data = {
				action: 'taxonomy_change',
				post_type: selectPostType.val(),
				taxonomy: $(this).val(),
				attr_pre_name: attrPreName
			};

			$.post(ajaxurl, data, function(response) {
				if (response) {
					includedTerms.html(response);
					mtsw_admin_sortable();
				}
			});
		}
	});

	// Show or hide children terms if parent term is checked or unchecked 
	$('body').on('change', 'input[name$="[included_parent_term_ids][]"]', function() {
		var attrName = $(this).attr('name');

		if (/^((?:mtsw_default_form|widget-mtsw_widget)(?:\[\d+\])?)\[included_parent_term_ids\]\[\]$/.test(attrName)) {

			var childrenSelector = 'input[name="' + RegExp.$1 + '[included_children_term_ids][]"]';
			var parentId = $(this).val();
			var childrenFieldset = $(this).parent().nextAll('#included_children_terms_' + parentId);

			if (this.checked) {
				$(this).closest('li.ui-state-default').removeClass('ui-state-disabled');
				childrenFieldset.find(childrenSelector).prop('checked', true).each(function() {
					$(this).closest('li.ui-state-default').removeClass('ui-state-disabled').css('width', '');
				});
				childrenFieldset.show('fast');
	    } else {
	    	$(this).closest('li.ui-state-default').addClass('ui-state-disabled');
	    	childrenFieldset.find(childrenSelector).prop('checked', false);
	    	childrenFieldset.hide('fast');
	    	var childrenFieldsetUl = childrenFieldset.find('ul').first();
	    	childrenFieldsetUl.children('li').sort(function(a, b) {
        	return (parseInt($(a).data('position')) < parseInt($(b).data('position'))) ? -1 : 1;
	    	}).appendTo(childrenFieldsetUl);
	    }
	  }
	});

	// Add or remove children term from jquery sortable if checked or unchecked
	$('body').on('change', 'input[name$="[included_children_term_ids][]"]', function() {
		var attrName = $(this).attr('name');

		if (/^((?:mtsw_default_form|widget-mtsw_widget)(?:\[\d+\])?)\[included_children_term_ids\]\[\]$/.test(attrName)) {

			if (this.checked) {
				$(this).closest('li.ui-state-default').removeClass('ui-state-disabled').css('width', '');
	    } else {
	    	$(this).closest('li.ui-state-default').addClass('ui-state-disabled').width($(this).parent().width() + 5); // +5 for IE
	    }
	  }
	});

	// Resolve a width bug on label
	$('#mtsw_default_form .included_children_terms > ul > li.ui-state-disabled').each(function() {
		$(this).width($(this).children('label').width() + 5); // + 5 for IE
	});

	// Return a list from a jquery array
	function inputImplode(input) {
		return input.map(function() {
						return $(this).val();
					})
					.get()
					.join(',');
	}

	// Create the excluded children terms list before form submit
	$('#mtsw_default_form').submit(function() {
		var excludedChildrenTerms = $(this).find('input[name="mtsw_default_form[included_children_term_ids][]"]:not(:checked)');
		var inputExcludedChildrenTerms = $(this).find('input[name="mtsw_default_form[excluded_children_term_ids]"]');

		if (excludedChildrenTerms.length)
			inputExcludedChildrenTerms.val(inputImplode(excludedChildrenTerms));
		else
			inputExcludedChildrenTerms.val('');
	});

	// Create the excluded children terms list before ajax widget form submit
	$( document ).ajaxSend(function( event, jqxhr, settings ) {
		if (settings.data.search('action=save-widget') != -1 && 
				settings.data.search('id_base=mtsw_widget') != -1) {

			if (/widget_number=(\d+)&?.*$/.test(settings.data)) {

				var widgetNumber = RegExp.$1;
				var excludedChildrenTerms = $('input[name="widget-mtsw_widget[' + widgetNumber + '][included_children_term_ids][]"]:not(:checked)');
				var inputExcludedChildrenTerms = $(this).find('input[name="widget-mtsw_widget[' + widgetNumber + '][excluded_children_term_ids]"]');

				if (excludedChildrenTerms.length)
					inputExcludedChildrenTerms.val(inputImplode(excludedChildrenTerms));
				else
					inputExcludedChildrenTerms.val('');

				var re = new RegExp('(widget-mtsw_widget\\[' + widgetNumber + '\\]\\[excluded_children_term_ids\\]=).*?(&.*)?$');
				settings.data = decodeURIComponent(settings.data);
				settings.data = settings.data.replace(re, '$1' + inputExcludedChildrenTerms.val() + '$2');
				settings.data = encodeURI(settings.data);
			}
		}
	});

	// re-sort after widget form submit ajax success
	$( document ).ajaxSuccess(function( event, jqxhr, settings ) {
		if (settings.data.search('action=save-widget') != -1 && 
				settings.data.search('id_base=mtsw_widget') != -1) {
			mtsw_admin_sortable();
		}
	});
});