<?php
/** Prüft Markenmodi und lokale Medien ausschließlich in der wegwerfbaren WordPress-Installation. */
if ( PHP_SAPI !== 'cli' || getenv( 'MGMS_TEST' ) !== 'isolated-local' ) {
	exit( 1 );
}
$_SERVER['HTTP_HOST'] = '127.0.0.1:18865';
require '/var/www/html/wp-load.php';

use MGMS\Settings;
use MGMS\Schema;

function mgms_branding_check( bool $value, string $message ): void {
	if ( ! $value ) {
		throw new RuntimeException( $message );
	}
	echo "PASS {$message}\n";
}

// Minimaler lokaler PNG-Anhang; der Test überträgt oder verwendet keine Fremdquelle.
$file = wp_upload_bits( 'mgms-test-logo.png', null, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlWZLAAAAAASUVORK5CYII=' ) );
mgms_branding_check( empty( $file['error'] ), 'Lokales Testbild angelegt' );
$id = wp_insert_attachment(
	array(
		'post_mime_type' => 'image/png',
		'post_title'     => 'MGMS Testlogo',
		'post_status'    => 'inherit',
	),
	$file['file']
);
mgms_branding_check( $id > 0 && '' !== Settings::media_url( $id ), 'Lokales Bild wird als zulässiges Logo erkannt' );

$input = Schema::defaults();
foreach ( Schema::fields() as $key => $field ) {
	if ( 'checkbox' === $field[1] ) {
		$input[ $key ] = $input[ $key ] ? '1' : '0';
	}
}
$input['brand_presentation'] = 'logo';
$invalid = Settings::sanitize( $input );
mgms_branding_check( is_wp_error( $invalid ), 'Nur-Logo ohne Bild wird abgewiesen' );
$input['logo'] = (string) $id;
$valid = Settings::sanitize( $input );
mgms_branding_check( is_array( $valid ) && 'logo' === $valid['brand_presentation'] && $id === $valid['logo'], 'Nur-Logo mit lokalem Bild wird gespeichert' );
$input['brand_presentation'] = 'logo-and-text';
$combined = Settings::sanitize( $input );
mgms_branding_check( is_array( $combined ) && 'logo-and-text' === $combined['brand_presentation'], 'Text und Logo werden gemeinsam gespeichert' );

update_option( Settings::DRAFT, $combined, false );
update_option( Settings::LIVE, $combined, false );
update_option( Settings::STATE, true, false );
echo "TOTAL Markenmodus und Medienprüfung bestanden\n";
