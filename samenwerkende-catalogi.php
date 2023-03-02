<?php
/*
Plugin Name: Samenwerkende Catalogi
Plugin URI: https://github.com/OpenWebconcept/plugin-samenwerkende-catalogi
Description: Plugin voor het creëren van een XML-feed voor de Samenwerkende Catalogi
Version: 1.2.1
Requires at least: 3.0
Tested up to: 4.1.1
Author: Gemeente Heerenveen
Author URI: https://www.heerenveen.nl
License URI: https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
License: EUPL v1.2
Copyright 2018-2022 Gemeente Heerenveen

Licensed under the EUPL, Version 1.2 or – as soon they will be approved by the European Commission - subsequent versions of the EUPL (the "Licence");
You may not use this work except in compliance with the Licence.
You may obtain a copy of the Licence at:

https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12

Unless required by applicable law or agreed to in writing, software distributed under the Licence is distributed on an "AS IS" basis,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the Licence for the specific language governing permissions and limitations under the Licence.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined('SC_PLUGIN_VERSION')) define('SC_PLUGIN_VERSION', '1.2.1');

if ( ! defined('SC_PLUGIN_FILE')) define('SC_PLUGIN_FILE', __FILE__);
if ( ! defined('SC_PLUGIN_DIR')) define('SC_PLUGIN_DIR', dirname(__FILE__));
if ( ! defined('SC_PLUGIN_URL')) define('SC_PLUGIN_URL', plugins_url('samenwerkende-catalogi'));

/**
 * Main Gemeente Heerenveen Plugin Class
 *
 * @class SamenwerkendeCatalogi
 */
class SamenwerkendeCatalogi {

	public function __construct() {

		include SC_PLUGIN_DIR . '/vendor/autoload.php';

		include SC_PLUGIN_DIR . '/lib/helpers.php';

		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-init.php';

		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-input-fields.php';
		
		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-meta-box.php';

	}

}


$GLOBALS['samenwerkende_catalogi'] = new SamenwerkendeCatalogi();

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/OpenWebconcept/plugin-samenwerkende-catalogi',
	__FILE__,
	'samenwerkende-catalogi'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();