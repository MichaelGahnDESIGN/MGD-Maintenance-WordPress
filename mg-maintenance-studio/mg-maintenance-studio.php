<?php
/**
 * Plugin Name: MGD Maintenance Studio
 * Plugin URI: https://theme.michael-gahn.de/plugins/plugin-maintenance-wordpress
 * Description: Lokaler Design-Editor für einen bewussten, barrierearmen Wartungsmodus.
 * Version: 2.1.4
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: Michael Gahn DESIGN
 * Author URI: https://michael-gahn.de
 * Update URI: https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress
 * License: GPL-2.0-or-later
 * Text Domain: mg-maintenance-studio
 */
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
define( 'MGMS_VERSION', '2.1.4' );
define( 'MGMS_FILE', __FILE__ );
define( 'MGMS_DIR', __DIR__ . '/' );
define( 'MGMS_URL', plugin_dir_url( __FILE__ ) );
foreach ( array( 'schema', 'pro-store', 'pro-sync', 'settings', 'ip', 'access', 'renderer', 'preview', 'error-pages', 'error-pages-admin', 'dashboard', 'admin', 'login-errors', 'plugin' ) as $file ) {
	require_once MGMS_DIR . 'includes/class-' . $file . '.php';
}
register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );
Login_Errors::register();
Plugin::boot();

// Baukasten aus MichaThemeV5-Pro > includes/Kit (bin/build-plugin-kit.php)
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'Mgd\\Maintenance\\Kit\\';
		if ( str_starts_with( $class, $prefix ) ) {
			$file = MGMS_DIR . 'includes/Kit/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( is_file( $file ) ) {
				require $file;
			}
		}
	}
);

// Updater + MGD Tools Menü vom Baukasten
use Mgd\Maintenance\Kit\Platform\WordPress\Setup;
use Mgd\Maintenance\Kit\Platform\WordPress\Tools;

Plugin::$updater = Setup::updater( __FILE__, 'mgd-maintenance-wordpress', 'wordpress', MGMS_VERSION, 'MGD Maintenance Studio', Tools::url() );
// Reste vom alten Updater wegräumen, 1x pro Version
Setup::cleanupLegacy( 'mgd-maintenance-wordpress', MGMS_VERSION, array( 'mgd_admin_release_*' ) );
// Einstellungen unter MGD Tools - Seiten-Kennung bleibt mgms-dashboard, Lizenz zentral (Pro-Szenen)
Setup::tools(
	__FILE__,
	'mgd-maintenance-wordpress',
	'wordpress',
	MGMS_VERSION,
	'MGD Maintenance Studio',
	Plugin::$updater,
	array(
		'slug'     => 'plugin-maintenance-wordpress',
		'repo'     => 'MGD-Maintenance-WordPress',
		'settings' => array(
			'page'   => Admin::PAGE,
			'title'  => 'Maintenance',
			'cap'    => 'manage_options',
			'render' => static function (): void {
				Admin::tools_page();
			},
		),
		'license'  => true,
		'pro'      => array( 'mgd-maintenance-pro' => 'Maintenance Pro' ),
	)
);
