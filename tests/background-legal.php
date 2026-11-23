<?php
// Hintergrundbild-Darstellung und Rechtstexte (Modal als Standard), ohne WordPress
define( 'ABSPATH', __DIR__ . '/' );
require __DIR__ . '/../mg-maintenance-studio/includes/class-schema.php';
use MGMS\Schema;
function check( bool $ok, string $label ): void { if ( ! $ok ) { fwrite( STDERR, "FAIL $label\n" ); exit( 1 ); } echo "PASS $label\n"; }

$f = Schema::fields( false );
check( isset( $f['background_fit'], $f['background_position'] ), 'Felder für Bilddarstellung und Ausrichtung da' );
check( 'cover' === Schema::defaults()['background_fit'] && 'center' === Schema::defaults()['background_position'], 'Standard bleibt Ausschnitt + zentriert (alte Seiten sehen gleich aus)' );
check( array( 'cover', 'fill', 'contain', 'repeat', 'original' ) === array_keys( $f['background_fit'][4] ), 'Darstellung: Ausschnitt, gestreckt, ganz, wiederholen, Original' );
check( 9 === count( $f['background_position'][4] ), 'Ausrichtung: 9 Positionen' );

$v = Schema::background_css( 'cover', 'top-left' );
check( 'cover' === $v['fit'] && 'top left' === $v['pos'] && ! $v['tile'], 'Ausschnitt oben links' );
$v = Schema::background_css( 'fill', 'center' );
check( 'fill' === $v['fit'] && 'center' === $v['pos'], 'Gestreckt' );
$v = Schema::background_css( 'original', 'bottom' );
check( 'none' === $v['fit'] && 'bottom' === $v['pos'], 'Originalgröße > object-fit none' );
$v = Schema::background_css( 'repeat', 'center' );
check( $v['tile'], 'Wiederholen > Kachelmodus' );
$v = Schema::background_css( 'evil;}', 'url(x)' );
check( 'cover' === $v['fit'] && 'center' === $v['pos'] && ! $v['tile'], 'Unbekannte Werte fallen auf Standard zurück' );

check( 'modal' === Schema::legal_mode( array() ), 'Rechtstext ohne Angabe: Modal' );
check( 'modal' === Schema::legal_mode( array( 'display' => 'modal' ) ), 'Modal gewählt' );
check( 'text' === Schema::legal_mode( array( 'display' => 'text' ) ), 'Als Text gewählt' );
check( 'modal' === Schema::legal_mode( array( 'display' => 'quatsch' ) ), 'Ungültig > Modal' );
check( 'modal' === Schema::legal_mode( array( 'modal' => true, 'footer' => false ) ), 'Altes Modal ohne Footer-Haken > jetzt Link im Modal' );
check( 'text' === Schema::legal_mode( array( 'modal' => false ) ), 'Altes "kein Modal" > Text' );
