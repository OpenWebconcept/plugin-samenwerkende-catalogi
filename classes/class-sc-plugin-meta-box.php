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
		if ( !current_user_can('edit_post', $post->ID) ) {
			return $post->ID;
		}

		if( in_array($post->post_type, get_sc_post_types()) ) {
			$data = array(
				'sc_plugin_product' 		=> ($_POST['sc_plugin_product'] && $_POST['sc_plugin_product'] == 'on' ? 'on' : ''),
				'sc_plugin_particulier' 	=> ($_POST['sc_plugin_particulier'] && $_POST['sc_plugin_particulier'] == 'on' ? 'on' : ''),
				'sc_plugin_ondernemer' 		=> ($_POST['sc_plugin_ondernemer'] && $_POST['sc_plugin_ondernemer'] == 'on' ? 'on' : ''),
				'sc_plugin_aanvragen' 		=> sanitize_text_field($_POST['sc_plugin_aanvragen']),
				'sc_plugin_url' 			=> sanitize_text_field($_POST['sc_plugin_url']),
				'sc_plugin_upn' 			=> intval($_POST['sc_plugin_upn'])
			);

			$this->save_data($post->ID, $data);
		}
	}

	public function save_data($post_id, $data) {
		foreach ($data as $key => $value) {
			update_post_meta($post_id, $key, $value);
		}
	}

}

new ScPluginMetaBox();

function sc_plugin_meta_box() {
	global $post;
	?>
	<div class="sc_plugin_meta_box">
		<div class="sc_plugin_meta_box--item">
			<label>Type</label>
			<?php ScPluginFieldInputs::checkbox($post->ID, 'sc_plugin_product', 'Product'); ?>
		</div>
		<div id="sc_plugin_meta_box--container">
			<div class="sc_plugin_meta_box--item">
				<label>Doelgroep</label>
				<?php ScPluginFieldInputs::checkbox($post->ID, 'sc_plugin_particulier', 'Particulier'); ?>
				<?php ScPluginFieldInputs::checkbox($post->ID, 'sc_plugin_ondernemer', 'Ondernemer'); ?>
			</div>
			<div class="sc_plugin_meta_box--item">
				<label>Online aanvragen</label>
				<?php ScPluginFieldInputs::select($post->ID, 'sc_plugin_aanvragen', array('nee' => 'Nee', 'ja' => 'Ja', 'digid' => 'Ja, met DigiD')); ?>
			</div>
			<div class="sc_plugin_meta_box--item">
				<label>UPN product</label>
				<?php ScPluginFieldInputs::select2_ajax($post->ID, 'sc_plugin_upn', 'sc-upn-product-options', false, 'get_sc_upn_label_by_id'); ?>
			</div>
			<div id="sc_plugin_meta_box--url" class="sc_plugin_meta_box--item">
				<label>Aanvraag URL</label>
				<?php ScPluginFieldInputs::text($post->ID, 'sc_plugin_url'); ?>
			</div>
		</div>
	</div>
	<?php
}