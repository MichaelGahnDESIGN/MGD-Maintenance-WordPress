<?php
/**
 * Plugin Name: MG Maintenance Studio
 * Plugin URI: https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress
 * Description: Lokaler Design-Editor für einen bewussten, barrierearmen Wartungsmodus.
 * Version: 0.5.3
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: Michael Gahn DESIGN
 * Update URI: https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress
 * License: GPL-2.0-or-later
 * Text Domain: mg-maintenance-studio
 */
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
define( 'MGMS_VERSION', '0.5.3' );
define( 'MGMS_FILE', __FILE__ );
define( 'MGMS_DIR', __DIR__ . '/' );
define( 'MGMS_URL', plugin_dir_url( __FILE__ ) );
foreach ( array( 'schema', 'settings', 'ip', 'access', 'renderer', 'preview', 'error-pages', 'error-pages-admin', 'dashboard', 'admin', 'updates', 'presentation', 'login-errors', 'mgd-wordpress-plugin-catalog', 'plugin' ) as $file ) {
	require_once MGMS_DIR . 'includes/class-' . $file . '.php';
}
register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );
Login_Errors::register();
Plugin::boot();

require_once __DIR__ . '/includes/class-mgd-plugin-admin-tools.php';
\MGD_Plugin_Admin_Tools_20261023::register([
    'file' => __FILE__,
    'repo' => 'MGD-Maintenance-WordPress',
    'slug' => 'mg-maintenance-studio',
    'name' => 'MG Maintenance Studio',
    'description' => 'Lokaler Design-Editor für einen bewussten, barrierearmen Wartungsmodus.',
    'version' => '0.5.3',
    'settings' => 'admin.php?page=mgms-dashboard',
    'requires' => '6.5',
    'php' => '8.1',
    'caches' => [],
    'package' => '',
]);
