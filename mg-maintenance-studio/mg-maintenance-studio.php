<?php
/**
 * Plugin Name: MG Maintenance Studio
 * Plugin URI: https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress
 * Description: Lokaler Design-Editor für einen bewussten, barrierearmen Wartungsmodus.
 * Version: 0.5.0
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
define( 'MGMS_VERSION', '0.5.0' );
define( 'MGMS_FILE', __FILE__ );
define( 'MGMS_DIR', __DIR__ . '/' );
define( 'MGMS_URL', plugin_dir_url( __FILE__ ) );
foreach ( array( 'schema', 'settings', 'ip', 'access', 'renderer', 'preview', 'error-pages', 'error-pages-admin', 'dashboard', 'admin', 'updates', 'presentation', 'plugin' ) as $file ) {
	require_once MGMS_DIR . 'includes/class-' . $file . '.php';
}
register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );
Plugin::boot();
