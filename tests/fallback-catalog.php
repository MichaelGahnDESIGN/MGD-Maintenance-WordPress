<?php
// Weitere Plugins: Ersatzliste hat Katalog-Kennungen, alte Katalogseite ist weg
$root = dirname( __DIR__ ) . '/mg-maintenance-studio/';
function check( bool $ok, string $label ): void { if ( ! $ok ) { fwrite( STDERR, "FAIL $label\n" ); exit( 1 ); } echo "PASS $label\n"; }
$list = json_decode( file_get_contents( $root . 'includes/plugin-catalog/plugins.json' ), true );
check( is_array( $list ) && count( $list ) >= 6, 'Ersatzliste lesbar' );
foreach ( $list as $repo => $row ) {
	check( is_string( $row['slug'] ?? null ) && preg_match( '/^[a-z0-9-]+$/', $row['slug'] ) === 1, "Kennung für $repo" );
}
$code = '';
foreach ( array_merge( array( $root . 'mg-maintenance-studio.php' ), glob( $root . 'includes/*.php' ) ) as $f ) { $code .= file_get_contents( $f ); }
check( ! file_exists( $root . 'includes/class-mgd-wordpress-plugin-catalog.php' ) && ! str_contains( $code, 'MGD_WordPress_Plugin_Catalog' ), 'Alte Katalogseite entfernt' );
// Installieren meldet der Baukasten selbst über Setup::updater an (ab Kit 6a64577)
$kit = file_get_contents( $root . 'includes/Kit/Platform/WordPress/Setup.php' );
check( ! str_contains( $code, 'Setup::catalogActions(' ) && preg_match( '/function updater.*?catalog/s', $kit ) === 1, 'Installieren-Knopf kommt vom Baukasten' );
check( ! str_contains( $code, 'Updates::register()' ) && ! str_contains( $code, 'MGD_Plugin_Admin_Tools_20261023::register' ), 'Alter Updater abgeschaltet' );
