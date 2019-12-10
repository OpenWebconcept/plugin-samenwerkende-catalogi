jQuery(document).ready(function($){
	jQuery('#sc_plugin_product').on('change', function(e){
		e.preventDefault();

		if( jQuery(this).is(":checked") ) {
			jQuery('#sc_plugin_meta_box--container').show();
		} else {
			jQuery('#sc_plugin_meta_box--container').hide();
		}
	}).trigger('change');

	jQuery('#sc_plugin_aanvragen').on('change', function(e){
		e.preventDefault();

		if( jQuery(this).val() == 'ja' || jQuery(this).val() == 'digid' ) {
			jQuery('#sc_plugin_meta_box--url').show();
		} else {
			jQuery(this).val('nee');
			jQuery('#sc_plugin_meta_box--url').hide();
		}
	}).trigger('change');



	$(".sc-select2-ajax").each(function(){

		$(this).select2({
			ajax: {
				url: sc_ajax_object.ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						action: $(this).data('action'),
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, params) {
					// parse the results into the format expected by Select2
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data, except to indicate that infinite
					// scrolling can be used
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination: {
							more: (params.page * 20) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: 'UPN product zoeken',
			escapeMarkup: function (markup) {
				return markup;
			}, // let our custom formatter work
			minimumInputLength: 1
		});

	});

});