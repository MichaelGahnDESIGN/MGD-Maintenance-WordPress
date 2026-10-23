<?php
/** Prüft die ausgelieferten Vorlagen ohne WordPress-Datenbank. */
define( 'ABSPATH', __DIR__ . '/' );
require __DIR__ . '/../mg-maintenance-studio/includes/class-schema.php';

$allowed = array_keys( MGMS\Schema::fields()['layout'][4] );
$expected = array(
	'elegant-gold'       => 'editorial',
	'minimalistisch-hell' => 'centered',
	'ruhiges-atelier'    => 'split',
	'nachtstudio'        => 'poster',
);
if ( 'editorial' !== MGMS\Schema::defaults()['layout'] ) {
	throw new RuntimeException( 'Das Layout bestehender Einstellungen darf sich nicht ändern.' );
}
foreach ( $expected as $name => $layout ) {
	$preset = json_decode( file_get_contents( __DIR__ . '/../mg-maintenance-studio/presets/' . $name . '.json' ), true, 512, JSON_THROW_ON_ERROR );
	if ( ! in_array( $layout, $allowed, true ) || ( $preset['values']['layout'] ?? null ) !== $layout || empty( $preset['name'] ) ) {
		throw new RuntimeException( 'Ungültige Vorlage: ' . $name );
	}
}
echo 'PASS Vier Layout-Vorlagen und kompatibler Standard' . PHP_EOL;
