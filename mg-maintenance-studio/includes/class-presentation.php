<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
final class Presentation {
	public static function register(): void {
		add_filter( 'plugin_action_links_' . plugin_basename( MGMS_FILE ), array( self::class, 'links' ) );
		add_filter( 'plugin_row_meta', array( self::class, 'details' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'assets' ) );
	}
	public static function links( array $links ): array {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=mgms-dashboard' ) ) . '">Einstellungen</a>' );
		return $links;
	}
	public static function details( array $links, string $file ): array {
		if ( plugin_basename( MGMS_FILE ) === $file ) {
			$links[] = '<a class="thickbox open-plugin-details-modal" href="' . esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=mg-maintenance-studio&TB_iframe=true&width=600&height=550' ) ) . '">Details ansehen</a>';
		}
		return $links;
	}
	public static function assets( string $hook ): void {
		if ( 'plugins.php' === $hook ) {
			wp_enqueue_style( 'mgms-plugin-list', MGMS_URL . 'assets/plugin-list.css', array(), MGMS_VERSION );
		}
	}
}
