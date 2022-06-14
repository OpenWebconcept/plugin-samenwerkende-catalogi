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

class ScPluginInit {

	private $upl_url 	= 'http://standaarden.overheid.nl/owms/oquery/UPL-gemeente.json';
	private $max_import = 100;

	public function __construct() {

		register_activation_hook( SC_PLUGIN_FILE, array($this, 'create_db_table') );

		register_activation_hook( SC_PLUGIN_FILE, array($this, 'create_cronjob') );

		register_deactivation_hook( SC_PLUGIN_FILE, array($this, 'delete_cronjob') );

		add_filter( 'cron_schedules', array($this, 'add_cron_schedule') );

		add_action( 'init', array($this, 'rewrite_rules') );

		add_filter( 'query_vars', array($this, 'custom_query_vars') );

		add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

		add_action( 'template_redirect', array($this, 'show_xml') );

		add_action( 'sc_import_upn', array($this, 'import_upn') );

		add_action( 'wp_ajax_sc-upn-product-options', array($this, 'ajax_get_upn_product_options') );

	}

	public function create_db_table() {

		global $wpdb;

		$charset_collate 	= $wpdb->get_charset_collate();
		$table_name 		= $wpdb->prefix . 'sc_uniform_product_names';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			label varchar(255) DEFAULT '' NOT NULL,
			name varchar(255) DEFAULT '' NOT NULL,
			uri varchar(255) DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	public function create_cronjob() {

		if (! wp_next_scheduled ( 'sc_import_upn' )) {
			wp_schedule_event(time(), '15min', 'sc_import_upn');
		}

	}

	public function delete_cronjob() {

		wp_clear_scheduled_hook('sc_import_upn');

	}

	public function add_cron_schedule( $schedules ) {

		if( !isset($schedules["5min"]) ) {
			
			$schedules["15min"] = array(
				'interval' 	=> 15*60,
				'display' 	=> __('Once every 15 minutes')
			);

		}

		return $schedules;

	}

	public function rewrite_rules() {
	
		add_rewrite_rule( '^samenwerkende-catalogi/?', 'index.php?show_sc_plugin=xml', 'top' );
	
	}

	public function custom_query_vars( $query_vars ) {
	
		$query_vars[] = 'show_sc_plugin';
		return $query_vars;
	
	}

	public function admin_enqueue_scripts() {
	
		// Enqueue admin styles
		wp_enqueue_style( 'select2', SC_PLUGIN_URL . '/assets/css/select2.min.css', false, SC_PLUGIN_VERSION );
		wp_enqueue_style( 'samenwerkende_catalogi_style', SC_PLUGIN_URL . '/assets/css/style.css', false, SC_PLUGIN_VERSION );

		// Enqueue admin scripts
		wp_enqueue_script( 'select2', SC_PLUGIN_URL . '/assets/js/select2.full.min.js', array('jquery'), false, SC_PLUGIN_VERSION );
		wp_enqueue_script( 'samenwerkende_catalogi_script', SC_PLUGIN_URL . '/assets/js/base.js', array('jquery', 'select2'), false, SC_PLUGIN_VERSION );
		wp_localize_script( 'samenwerkende_catalogi_script', 'sc_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}

	public function show_xml() {
		
		if ( get_query_var('show_sc_plugin') && get_query_var('show_sc_plugin') == 'xml'  ) {

			header('Content-type: text/xml');

			include SC_PLUGIN_DIR . '/views/xml/output.php';
			
			exit;

		}
	
	}

	public function import_upn() {

		$uploads = wp_upload_dir();

		if( isset($uploads['basedir']) && is_dir($uploads['basedir']) ) {
			
			$upl_content 	= false;
			$upl_file 		= $uploads['basedir'] . '/UPL-gemeente.json';
			
			if( !file_exists($upl_file) ) {
				$upl_content = file_get_contents($this->upl_url);
			} else {
				$upl_content = file_get_contents($upl_file);
			}

			if( $upl_content ) {

				$import_i 	= 0;
				$upl_array 	= json_decode($upl_content, true);

				if( isset($upl_array['results']['bindings']) ) {

					foreach( $upl_array['results']['bindings'] as $key => $upl_item ) {

						if( $this->max_import > $import_i ) {

							if( isset($upl_item['UniformeProductnaam']['value']) && isset($upl_item['URI']['value']) ) {
								
								$label 	= $upl_item['UniformeProductnaam']['value'];
								$uri 	= $upl_item['URI']['value'];
								
								// Insert if not exist, else update if changed
								$this->tryInsertUpn($uri, $label);

								$import_i++;

							}

							unset($upl_array['results']['bindings'][$key]);
						
						} else {

							break;

						}

					}

				}

				if( $import_i > 0 ) {

					// Save the file
					$new_content = json_encode($upl_array);
					file_put_contents($upl_file, $new_content);

				} else {

					// Delete the file (import completed)
					if ( !unlink($upl_file) ) {
						wp_die("Error deleting $upl_file");
					}

				}

			}

		}

	}

	public function ajax_get_upn_product_options() {

		global $wpdb;

		$results = $items = array();

		$per_page 		= 20;

		$page 			= (isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1);
		$search 		= (isset($_REQUEST['q']) ? sanitize_text_field($_REQUEST['q']) : '');
		$offset 		= ($page*$per_page)-$per_page;
		
		$table_name 	= $wpdb->prefix . 'sc_uniform_product_names';
		$query 			= $wpdb->prepare("SELECT * FROM {$table_name} WHERE label LIKE %s OR name LIKE %s LIMIT {$per_page} OFFSET {$offset}", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
		$query_count 	= $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE label LIKE %s OR name LIKE %s", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
		$total_items 	= $wpdb->get_var( $query_count );

		$upn_items = $wpdb->get_results( $query );

		if( $upn_items && is_array($upn_items) ) {

			foreach( $upn_items as $upn_item ) {
				
				$items[] = array(
					'id' 	=> $upn_item->id,
					'text' 	=> $upn_item->label
				);

			}

		}

		$results = array(
			'items' 		=> $items,
			'total_count' 	=> $total_items,
		);

		header('Content-Type: application/json');
		echo json_encode($results);
		wp_die();

	}

	public function getUpnByUri($uri) {

		$parts = explode('/terms/', $uri);

		return ($parts && is_array($parts) && !empty($parts) ? end($parts) : '');

	}

	private function tryInsertUpn($uri, $label) {

		global $wpdb;

		$name 		= $this->getUpnByUri($uri);
		$table_name = $wpdb->prefix . 'sc_uniform_product_names';
		$upn_row 	= $wpdb->get_row( "SELECT * FROM $table_name WHERE (name='$name' OR label='$label' OR uri='$uri')" );

		if( $upn_row ) {

			if( $upn_row->label !== $label || $upn_row->name !== $name || $upn_row->uri !== $uri ) {
				
				// Update the fields with the new values
				$result = $wpdb->update( 
					$table_name, 
					array( 
						'label' => $label, 
						'name' 	=> $name, 
						'uri' 	=> $uri
					), 
					array( 'id' => $upn_row->id ), 
					array( 
						'%s', 
						'%s', 
						'%s' 
					), 
					array( '%d' ) 
				);
				
				return ( $result !== false  );

			}

		} else {

			// Insert the new UPN
			$result = $wpdb->insert( 
				$table_name, 
				array( 
					'label' => $label, 
					'name' 	=> $name, 
					'uri' 	=> $uri
				), 
				array( 
					'%s', 
					'%s', 
					'%s' 
				) 
			);

			return ( $result !== false  );

		}

		return false;

	}

}
new ScPluginInit();