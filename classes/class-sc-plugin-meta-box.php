<?php
/*
* Copyright 2018-2022 Gemeente Heerenveen
*
* Licensed under the EUPL, Version 1.2 or – as soon they will be approved by the European Commission - subsequent versions of the EUPL (the "Licence");
* You may not use this work except in compliance with the Licence.
* You may obtain a copy of the Licence at:
*
* https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
*
* Unless required by applicable law or agreed to in writing, software distributed under the Licence is distributed on an "AS IS" basis,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the Licence for the specific language governing permissions and limitations under the Licence.
*/
class ScPluginMetaBox {
	public function __construct() {
		add_action( 'add_meta_boxes', array($this, 'meta_boxes') );
		add_action( 'save_post', array($this, 'save_fields'), 1, 2 );
	}

	public function meta_boxes() {
		foreach (get_sc_post_types() as $allowed_type) {
			add_meta_box(
				'sc_plugin_meta_box', // $id
				__('Samenwerkende Catalogi', 'sc_plugin'), // $title
				'sc_plugin_meta_box', // $callback
				$allowed_type, // $page
				'side', // $context
				'high'
			);
		}
	}

    public function save_fields($post_id, $post) {
        if (!isset($_POST['sc_plugin_nonce']) || !wp_verify_nonce($_POST['sc_plugin_nonce'], 'sc_plugin_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post->ID)) return;
        if (!in_array($post->post_type, get_sc_post_types())) return;

        update_post_meta($post->ID, 'sc_plugin_product', $_POST['sc_plugin_product'] && $_POST['sc_plugin_product'] == 'on' ? 'on' : '');

        $entries = $_POST['sc_plugin_products'] ?? [];
        $sanitized = [];

        foreach($entries as $entry) {
            $sanitized[] = [
                'particulier' => ($entry['sc_plugin_particulier'] && $entry['sc_plugin_particulier'] == 'on' ? 'on' : ''),
                'ondernemer'  => ($entry['sc_plugin_ondernemer'] && $entry['sc_plugin_ondernemer'] == 'on' ? 'on' : ''),
                'aanvragen'   => sanitize_text_field($entry['sc_plugin_aanvragen']),
                'url'         => sanitize_text_field($entry['sc_plugin_url']),
                'upn'         => intval($entry['sc_plugin_upn']),
            ];
        }

        update_post_meta($post->ID, 'sc_plugin_products', $sanitized);

        // Cleanup old metadata
        $old_fields = [
            'sc_plugin_particulier', 'sc_plugin_ondernemer',
            'sc_plugin_aanvragen', 'sc_plugin_url', 'sc_plugin_upn'
        ];
        foreach($old_fields as $key) {
            if(metadata_exists('post', $post->ID, $key)) {
                delete_post_meta($post->ID, $key);
            }
        }
    }

}

new ScPluginMetaBox();

function sc_plugin_meta_box() {
	global $post;
    wp_nonce_field('sc_plugin_save_meta', 'sc_plugin_nonce');

    $product_type = get_post_meta($post->ID, 'sc_plugin_product', true);
    $product_entries = get_post_meta($post->ID, 'sc_plugin_products', true);
    $products = is_array($product_entries) ? $product_entries : [];

    // Convert previous meta data format
    if(empty($products)) {
        $products[] = [
            'particulier' => get_post_meta($post->ID, 'sc_plugin_particulier', true),
            'ondernemer' => get_post_meta($post->ID, 'sc_plugin_ondernemer', true),
            'aanvragen' => get_post_meta($post->ID, 'sc_plugin_aanvragen', true),
            'url' => get_post_meta($post->ID, 'sc_plugin_url', true),
            'upn' => get_post_meta($post->ID, 'sc_plugin_upn', true),
        ];
    }

    // Preselect used UPN labels
    $upn_entries = [];
    foreach($products as $product) {
        if($product['upn'] > 0) {
            $upn_entries[$product['upn']] = get_sc_upn_label_by_id($product['upn']);
        }
    }

    ?>

    <div class="sc_plugin_meta_box">
        <div class="sc_plugin_meta_box--item">
            <label>Type</label>
            <?php ScPluginFieldInputs::checkbox($post->ID, 'sc_plugin_product', 'Product'); ?>
        </div>
        <div id="sc_plugin_meta_box--container">
            <div id="sc-product-entries"></div>
        </div>
        <button type="button" class="button" id="add-product-group">Toevoegen</button>
        <script>
            window.scProductEntries = <?php echo json_encode($products) ?>;
            window.scUPNEntries = <?php echo json_encode($upn_entries) ?>;
        </script>
    </div>

    <template id="sc-product-template">
        <div class="sc-product-group" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <div class="sc_plugin_meta_box--item">
                <label>Doelgroep</label>
                <label class="checkbox-label">
                    <input type="checkbox" name="sc_plugin_products[{{index}}][particulier]" value="on"> Particulier
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="sc_plugin_products[{{index}}][ondernemer]" value="on"> Ondernemer
                </label>
            </div>
            <div class="sc_plugin_meta_box--item">
                <label>Online aanvragen</label>
                <select name="sc_plugin_products[{{index}}][aanvragen]">
                    <option value="nee">Nee</option>
                    <option value="ja">Ja</option>
                    <option value="digid">Ja, met DigiD</option>
                </select>
            </div>
            <div class="sc_plugin_meta_box--item">
                <label>UPN</label>
                <select name="sc_plugin_products[{{index}}][upn]" data-action="sc-upn-product-options" class="sc-select2-ajax">
                    <option value="{{upn}}">{{upn_label}}</option>
                </select>
            </div>
            <div class="sc_plugin_meta_box--url sc_plugin_meta_box--item">
                <label>Aanvraag URL</label>
                <input type="text" name="sc_plugin_products[{{index}}][url]" value="{{url}}" class="regular-text">
            </div>
            <button type="button" class="button remove-product-group">Verwijder</button>
        </div>
    </template>

    <?php
}