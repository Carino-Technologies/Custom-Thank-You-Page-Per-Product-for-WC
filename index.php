<?php

/**
 * Plugin Name: Custom Thank You Page Per Product for WC
 * Plugin URI: https://carinotech.com
 * Description: Free WooCommerce extension that enable to assign custom thank you page to any product specifically.
 * Version: 1.0
 * Author: Saad Qureshi
 * Author URI: https://github.com/Carino-Technologies
 * Requires at least: 4.0
 * Tested up to: 4.6.1
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: custom-thank-you-page
 * Domain Path: /languages/
 */

// Include only the basics
require __DIR__."/inc/thank-you.php";

// Start the plugin
\WCCustomThankYouPage\WCCustomThankYouPage::start();