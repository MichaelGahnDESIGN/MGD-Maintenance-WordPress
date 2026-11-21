<?php
/** Prüft die ausgelieferten Vorlagen ohne WordPress-Datenbank. */
define( 'ABSPATH', __DIR__ . '/' );
require __DIR__ . '/../mg-maintenance-studio/includes/class-schema.php';

$allowed = array_keys( MGMS\Schema::fields()['layout'][4] );
$expected = array(
	'elegant-gold'        => 'cinema',
	'minimalistisch-hell' => 'swiss',
	'ruhiges-atelier'     => 'atelier',
	'nachtstudio'         => 'glass',
	'verspielt'           => 'bloom',
);
if ( 'editorial' !== MGMS\Schema::defaults()['layout'] ) {
	throw new RuntimeException( 'Das Layout bestehender Einstellungen darf sich nicht ändern.' );
}
if ( 'text' !== MGMS\Schema::defaults()['brand_presentation'] ) {
	throw new RuntimeException( 'Bestehende Installationen müssen weiterhin die Textmarke anzeigen.' );
}
if ( array_keys( MGMS\Schema::fields()['brand_presentation'][4] ) !== array( 'text', 'logo', 'logo-and-text' ) ) {
	throw new RuntimeException( 'Die drei Markenmodi fehlen oder sind nicht eindeutig.' );
}
// Bildwirkung: Standard bleibt dezent, sonst ändern sich alte Seiten
if ( 'subtle' !== MGMS\Schema::defaults()['image_effect'] ) {
	throw new RuntimeException( 'Bestehende Seiten müssen die dezente Bildwirkung behalten.' );
}
foreach ( array( 'editorial', 'centered', 'split', 'poster' ) as $old_layout ) {
	if ( ! in_array( $old_layout, $allowed, true ) ) {
		throw new RuntimeException( 'Altes Layout fehlt: ' . $old_layout );
	}
}
$css = file_get_contents( __DIR__ . '/../mg-maintenance-studio/assets/frontend.css' );
$js  = file_get_contents( __DIR__ . '/../mg-maintenance-studio/assets/preview.js' );
foreach ( $expected as $layout ) {
	if ( ! str_contains( $css, '.mgms-layout-' . $layout ) || ! str_contains( $js, '"' . $layout . '"' ) ) {
		throw new RuntimeException( 'Szene ohne CSS oder Live-Vorschau: ' . $layout );
	}
}
if ( count( array_unique( array_values( $expected ) ) ) !== 5 ) {
	throw new RuntimeException( 'Jede Vorlage braucht eine eigene Szene.' );
}
foreach ( $expected as $name => $layout ) {
	$preset = json_decode( file_get_contents( __DIR__ . '/../mg-maintenance-studio/presets/' . $name . '.json' ), true, 512, JSON_THROW_ON_ERROR );
	if ( ! in_array( $layout, $allowed, true ) || ( $preset['values']['layout'] ?? null ) !== $layout || empty( $preset['name'] ) ) {
		throw new RuntimeException( 'Ungültige Vorlage: ' . $name );
	}
}
echo 'PASS Fünf eigenständige Szenen, alte Layouts und kompatible Standards' . PHP_EOL;
