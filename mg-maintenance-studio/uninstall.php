<?php
/** Daten nur nach bewusst gesetzter Löschoption entfernen. Mediathek-Dateien bleiben erhalten. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; }
$mgms_cleanup = static function (): void {
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
