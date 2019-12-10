<?php
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