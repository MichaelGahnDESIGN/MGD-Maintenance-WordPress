<?php
// MGD Tools: kein eigenes Menü, Kopf nach Norm, Icons, Farbfelder, alte Adressen - ohne WordPress
$root = dirname( __DIR__ ) . '/mg-maintenance-studio/';
function check( bool $ok, string $label ): void { if ( ! $ok ) { fwrite( STDERR, "FAIL $label\n" ); exit( 1 ); } echo "PASS $label\n"; }
$main = file_get_contents( $root . 'mg-maintenance-studio.php' );
$code = '';
foreach ( glob( $root . 'includes/*.php' ) as $f ) { $code .= file_get_contents( $f ); }
foreach ( glob( $root . 'templates/*.php' ) as $f ) { $code .= file_get_contents( $f ); }

check( str_contains( $main, 'Setup::tools(' ) && str_contains( $main, "'page'   => Admin::PAGE" ), 'Setup::tools mit eigener Seite' );
check( 'mgms-dashboard' === ( preg_match( "/const PAGE = '([^']+)'/", file_get_contents( $root . 'includes/class-admin.php' ), $m ) ? $m[1] : '' ), 'Seiten-Kennung bleibt mgms-dashboard' );
check( ! preg_match( '/add_(menu|submenu|options|management|media)_page\s*\(/', $code ), 'Kein eigenes Menü' );
check( ! file_exists( $root . 'includes/class-mgd-plugin-admin-tools.php' ) && ! file_exists( $root . 'includes/class-presentation.php' ), 'Alter Admin-Tools-Baustein und eigene Plugin-Links weg' );
check( preg_match( '#Plugin URI: https://theme\.michael-gahn\.de/plugins/plugin-maintenance-wordpress#', $main ) === 1 && str_contains( $main, 'Author URI: https://michael-gahn.de' ), 'Plugin-Kopf nach Norm' );
check( 1 === preg_match_all( '/^ \* Author:/m', $main ), 'Autor genau einmal' );
check( is_file( $root . 'assets/icon-128x128.png' ) && is_file( $root . 'assets/icon-256x256.png' ), 'Icons 128 und 256' );
check( ! preg_match( '/type="color"/', $code ), 'Keine type=color Felder, nur data-mgd-color' );
check( substr_count( $code, 'data-mgd-color' ) >= 2, 'Farbfelder mit data-mgd-color' );
check( ! str_contains( $code, 'Setup::dashboard(' ) && ! str_contains( $code, 'admin.php?page=mgms-' ), 'Keine alten Dashboard- und Menü-Links' );
check( str_contains( file_get_contents( $root . 'templates/admin-design.php' ), 'Tools::PAGE_LICENSE' ), 'Schloss-Karte verlinkt MGD Tools > Lizenz' );
check( str_contains( $code, "'mgms-updates' === \$page" ) && str_contains( $code, "'mgms-error-pages'      => 'errors'" ), 'Alte Adressen leiten um' );
