<?php
/**
 * Plugin Name: OrgaPress Lite
 * Description: Lite Version des OrgaPress Enterprise Frameworks – SMTP, DSGVO Cookie Banner, Rollen, SEO & Security Basics.
 * Version: 1.0.1
 * Author: RheinMainTech GmbH
 * License: GPLv2 or later
 * Text Domain: orgapress-lite
 * Plugin URI: https://orgapress.com
 */

if (!defined('ABSPATH')) {
exit;
}

define('ORGAPRESS_LITE_VERSION', '1.0.0');
define('ORGAPRESS_LITE_PATH', plugin_dir_path(__FILE__));
define('ORGAPRESS_LITE_URL', plugin_dir_url(__FILE__));

require_once ORGAPRESS_LITE_PATH . 'includes/class-core.php';

register_activation_hook(__FILE__, [\OrgaPressLite\Core::class, 'activate']);

add_action('plugins_loaded', function () {
\OrgaPressLite\Core::init();
}); 
