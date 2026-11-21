<?php
/** Daten nur nach bewusst gesetzter Löschoption entfernen. Mediathek-Dateien bleiben erhalten. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; }
// Abmelden bei theme.michael-gahn.de (Baukasten), auch wenn der Server weg ist
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'Mgd\\Maintenance\\Kit\\';
		if ( str_starts_with( $class, $prefix ) ) {
			$file = __DIR__ . '/includes/Kit/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( is_file( $file ) ) {
				require $file;
			}
		}
	}
);
\Mgd\Maintenance\Kit\Platform\WordPress\Setup::uninstall( 'mgd-maintenance-wordpress', 'wordpress' );
$mgms_cleanup = static function (): void {
	// Pro-Szenen sind nur Zwischenspeicher, immer löschen
	$pro = get_option( 'mgms_pro', array() );
	if ( is_array( $pro ) && ! empty( $pro['dir'] ) && preg_match( '/^mgms-pro-[a-f0-9]{16}$/D', $pro['dir'] ) ) {
		$up = wp_upload_dir( null, false );
		foreach ( glob( trailingslashit( $up['basedir'] ) . $pro['dir'] . '/*' ) ?: array() as $file ) {
			wp_delete_file( $file );
		}
		@rmdir( trailingslashit( $up['basedir'] ) . $pro['dir'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}
	delete_option( 'mgms_pro' );
	delete_option( 'mgms_pro_too_old' );
	wp_clear_scheduled_hook( 'mgms_pro_sync' );
	delete_transient( 'mgms_release' );
	$draft = get_option( 'mgms_draft', array() );
	if ( ! empty( $draft['delete_data'] ) ) {
		foreach ( array( 'mgms_draft', 'mgms_published', 'mgms_enabled', 'mgms_previous_draft', 'mgms_previous_published', 'mgms_error_draft', 'mgms_error_published', 'mgms_updates', 'mgms_last_good_repository' ) as $option ) {
			delete_option( $option ); }
	}
};
if ( is_multisite() ) {
	$mgms_offset = 0;
	do {
		$mgms_sites = get_sites(
			array(
				'fields' => 'ids',
				'number' => 100,
				'offset' => $mgms_offset,
			)
		);
		foreach ( $mgms_sites as $mgms_site ) {
			switch_to_blog( $mgms_site );
			$mgms_cleanup();
			restore_current_blog(); }
		$mgms_offset += 100;
		$mgms_count   = count( $mgms_sites );
	} while ( 100 === $mgms_count );
} else {
	$mgms_cleanup(); }
delete_metadata( 'user', 0, 'mgms_preview', '', true );
