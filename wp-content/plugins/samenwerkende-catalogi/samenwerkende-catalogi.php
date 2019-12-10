<?php
/**
 * Plugin Name: Samenwerkende Catalogi
 * Plugin URI: http://www.heerenveen.nl/plugins/samenwerkende-catalogi/
 * Description: Plugin voor het creëren van een XML-feed voor de Samenwerkende Catalogi
 * Version: 1.1.2
 * Author: Gemeente Heerenveen
 * Author URI: https://www.heerenveen.nl
 * Requires at least: 3.0
 * Tested up to: 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined('SC_PLUGIN_VERSION')) define('SC_PLUGIN_VERSION', '1.1.1');

if ( ! defined('SC_PLUGIN_FILE')) define('SC_PLUGIN_FILE', __FILE__);
if ( ! defined('SC_PLUGIN_DIR')) define('SC_PLUGIN_DIR', dirname(__FILE__));
if ( ! defined('SC_PLUGIN_URL')) define('SC_PLUGIN_URL', plugins_url('samenwerkende-catalogi'));

/**
 * Main Gemeente Heerenveen Plugin Class
 *
 * @class SamenwerkendeCatalogi
 * @version  1.1.0
 */
class SamenwerkendeCatalogi {

	public function __construct() {

		include SC_PLUGIN_DIR . '/lib/helpers.php';

		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-init.php';

		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-input-fields.php';
		
		include SC_PLUGIN_DIR . '/classes/class-sc-plugin-meta-box.php';

	}

}


$GLOBALS['samenwerkende_catalogi'] = new SamenwerkendeCatalogi();