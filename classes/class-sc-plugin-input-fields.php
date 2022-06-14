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
	class ScPluginFieldInputs {
		
		public static function text($post_id, $field_name, $default_value = '') {
			$current_value = get_post_meta($post_id, $field_name, true);
			$current_value = ($current_value && $current_value !== '' ? $current_value : $default_value);

			include SC_PLUGIN_DIR . '/views/inputs/input-text.php';
		}

		public static function datepicker($post_id, $field_name, $default_value = '') {
			$current_value = get_post_meta($post_id, $field_name, true);
			$current_value = ($current_value && $current_value !== '' ? $current_value : $default_value);

			include SC_PLUGIN_DIR . '/views/inputs/input-datepicker.php';
		}

		public static function timepicker($post_id, $field_name, $default_value = '') {
			$current_value = get_post_meta($post_id, $field_name, true);
			$current_value = ($current_value && $current_value !== '' ? $current_value : $default_value);

			include SC_PLUGIN_DIR . '/views/inputs/input-timepicker.php';
		}

		public static function checkbox($post_id, $field_name, $label = 'Inschakelen') {
			$checked = get_post_meta($post_id, $field_name, true);

			include SC_PLUGIN_DIR . '/views/inputs/input-checkbox.php';
		}

		public static function select($post_id, $field_name, $options = array(), $multi = false) {
			$current_id = get_post_meta($post_id, $field_name, true);
			
			include SC_PLUGIN_DIR . '/views/inputs/input-select.php';
		}

		public static function select2_ajax($post_id, $field_name, $action = '', $multi = false, $value_function = false) {
			$current_id 	= get_post_meta($post_id, $field_name, true);
			$current_value 	= $current_id;

			if( $value_function && function_exists($value_function) ) {
				$current_value = call_user_func_array($value_function, array($current_id));
			}
			
			include SC_PLUGIN_DIR . '/views/inputs/input-select-ajax.php';
		}

		public static function fileUpload($post_id, $field_name) {
			$current_id 	= get_post_meta($post_id, $field_name, true);
			$current_img 	= false;
			if( $current_id && $current_id !== '' ) {
				$current_img = wp_get_attachment_image_src($current_id, 'thumbnail');
			}

			include SC_PLUGIN_DIR . '/views/inputs/input-file-upload.php';
		}

		public static function selectPostType($post_id, $field_name, $post_type = 'page', $taxonomy = false) {
			$options 	= array();
			$args 		= array('post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => -1);

			if( $taxonomy && is_array($taxonomy) ) {
				$args += $taxonomy;
			}

			$posts 		= get_posts( $args );
			$current_id = get_post_meta($post_id, $field_name, true);
			
			foreach ($posts as $post_item) {
				$options[$post_item->ID] = $post_item->post_title;
			}
			
			include SC_PLUGIN_DIR . '/views/inputs/input-select.php';
		}

		public static function selectTaxonomy($post_id, $field_name, $taxonomy = 'category') {
			$options 	= array();
			$current_id = get_post_meta($post_id, $field_name, true);
			$terms 		= get_terms( array(
				'taxonomy' 		=> $taxonomy,
				'hide_empty' 	=> false,
			) );

			foreach ($terms as $term) {
				$options[$term->term_id] = $term->name;
			}

			include SC_PLUGIN_DIR . '/views/inputs/input-select.php';
		}
		
		public static function map($post_id, $field_name, $placeholder = '') {
			$current_title 		= get_post_meta($post_id, $field_name.'_title', true);
			$current_lat 		= get_post_meta($post_id, $field_name.'_lat', true);
			$current_long 		= get_post_meta($post_id, $field_name.'_long', true);
			$current_zipcode 	= get_post_meta($post_id, $field_name.'_zipcode', true);
			
			include SC_PLUGIN_DIR . '/views/inputs/input-map.php';
		}

	}