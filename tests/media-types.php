<?php
// Bild-Formate für Logo & Co ohne WP-Datenbank prüfen (Bug: SVG-Logo -> Fehlerseite beim Speichern)
define( 'ABSPATH', __DIR__ );
define( 'MGMS_DIR', dirname( __DIR__ ) . '/mg-maintenance-studio/' );
// Fake Mediathek: ID => [mime, url]
$GLOBALS['media'] = array(
	11 => array( 'image/png', 'https://example.com/wp-content/uploads/logo.png' ),
	12 => array( 'image/svg+xml', 'https://example.com/wp-content/uploads/logo.svg' ),
	13 => array( 'application/pdf', 'https://example.com/wp-content/uploads/datei.pdf' ),
	14 => array( 'image/svg+xml', 'https://cdn.fremd.example/logo.svg' ),
	15 => array( 'font/woff2', 'https://example.com/wp-content/uploads/schrift.woff2' ),
);
function get_post_type( $id ) { return isset( $GLOBALS['media'][ $id ] ) ? 'attachment' : false; }
function get_post_mime_type( $id ) { return $GLOBALS['media'][ $id ][0] ?? false; }
function wp_get_attachment_url( $id ) { return $GLOBALS['media'][ $id ][1] ?? false; }
function home_url() { return 'https://example.com'; }
function wp_parse_url( $url ) { return parse_url( $url ); }
require MGMS_DIR . 'includes/class-settings.php';
function check( bool $okay, string $label ): void { if ( ! $okay ) { fwrite( STDERR, "FAIL $label\n" ); exit( 1 ); } echo "PASS $label\n"; }

use MGMS\Settings;
check( '' !== Settings::media_url( 11 ), 'PNG-Logo erlaubt' );
check( '' !== Settings::media_url( 12 ), 'SVG-Logo aus der Mediathek erlaubt' );
check( '' === Settings::media_url( 13 ), 'PDF als Bild abgewiesen' );
check( '' === Settings::media_url( 14 ), 'SVG von fremdem Host abgewiesen' );
check( '' === Settings::media_url( 12, 'font' ), 'SVG nicht als Schrift' );
check( '' !== Settings::media_url( 15, 'font' ), 'WOFF2-Schrift erlaubt' );
check( in_array( 'image/svg+xml', Settings::image_mimes(), true ) && ! in_array( 'application/pdf', Settings::image_mimes(), true ), 'Mediathek-Filter kennt die erlaubten Bildformate' );
