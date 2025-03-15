<?php
/**
 * Plugin Name: WooCommerce PDF Invoice Generator
 * Description: Generates PDF invoices for WooCommerce orders using Dompdf.
 * Version: 1.0
 * Author: Daniel Holm
 */

require_once __DIR__ . '/vendor/autoload.php';

define('PLUGIN_ROOT', dirname(__FILE__)  );

include_once(plugin_dir_path(__FILE__) . 'lib/mail.php');
include_once(plugin_dir_path(__FILE__) . 'lib/admin.php');
include_once(plugin_dir_path(__FILE__) . 'lib/helpers.php');
include_once(plugin_dir_path(__FILE__) . 'lib/invoice.php');






