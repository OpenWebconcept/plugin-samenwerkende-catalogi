document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('sc-product-entries');
    const addBtn = document.getElementById('add-product-group');
    const template = document.getElementById('sc-product-template').innerHTML;

    const entries = window.scProductEntries?.length ? window.scProductEntries : [{}];

    function renderTemplate(index, data = {}) {
        let html = template.replace(/{{index}}/g, index);
        html = html.replace(/{{number}}/g, index + 1);
        ['upn', 'url'].forEach(key => {
            html = html.replace(new RegExp(`{{${key}}}`, 'g'), data[key] || '');
        });
        if(data['upn'] && window.scUPNEntries[parseInt(data['upn'], 10)]) {
            html = html.replace(new RegExp(`{{upn_label}}`, 'g'), window.scUPNEntries[parseInt(data['upn'], 10)]);
        }

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const group = wrapper.firstElementChild;

        const inputParticulier = group.querySelector('input[name$="[particulier]"]');
        const inputOndernemer = group.querySelector('input[name$="[ondernemer]"]');
        const inputAanvragen = group.querySelector('select[name$="[aanvragen]"]');
        const inputUPN = group.querySelector('select[name$="[upn]"]');
        const inputURL = group.querySelector('input[name$="[url]"]');

        if(data['particulier']?.length > 0) {
            inputParticulier.setAttribute('checked', 'checked');
        }
        if(data['ondernemer']?.length > 0) {
            inputOndernemer.setAttribute('checked', 'checked');
        }
        inputAanvragen.querySelectorAll('option').forEach(option => {
            if(option.value == data['aanvragen']) {
                option.setAttribute('selected', 'selected');
            }
        });
        if(parseInt(data['upn'], 10) == 0) {
            inputUPN.querySelector('option').remove();
        }

        jQuery(inputUPN).select2({
            ajax: {
                url: sc_ajax_object.ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        action: jQuery(this).data('action'),
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

        jQuery(inputAanvragen).on('change', function(e) {
            e.preventDefault();
            const value = jQuery(this).val();
            if(value === 'ja' || value === 'digid') {
                jQuery(group).find('.sc_plugin_meta_box--url').show();
            }
            else {
                jQuery(group).find('.sc_plugin_meta_box--url').hide();
            }
        }).trigger('change');

        group.querySelector('.remove-product-group')?.addEventListener('click', () => group.remove());
        container.appendChild(group);
    }

    entries.forEach((entry, i) => renderTemplate(i, entry));

    addBtn.addEventListener('click', () => {
        const index = container.querySelectorAll('.sc-product-group').length;
        renderTemplate(index);
    });

});

jQuery(function() {
    jQuery('#sc_plugin_product').on('change', function(e){
        e.preventDefault();
        if( jQuery(this).is(":checked") ) {
            jQuery('#sc_plugin_meta_box--container').show();
            jQuery('#add-product-group').show();
        } else {
            jQuery('#sc_plugin_meta_box--container').hide();
            jQuery('#add-product-group').hide();
        }
    }).trigger('change');
});
