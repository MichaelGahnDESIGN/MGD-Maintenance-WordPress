<?php
// Pro-Szenen lokal: speichern, Felder säubern, Ablauf, Rückfall, Löschen - ohne WordPress
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 );
$GLOBALS['opt'] = array();
$GLOBALS['up']  = sys_get_temp_dir() . '/mgms-pro-test-' . bin2hex( random_bytes( 4 ) );
mkdir( $GLOBALS['up'] );
class WP_Error { public function __construct( public string $code = '', public string $msg = '' ) {} }
function get_option( $k, $d = false ) { return $GLOBALS['opt'][ $k ] ?? $d; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opt'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['opt'][ $k ] ); return true; }
function wp_upload_dir( $t = null, $c = true ) { return array( 'basedir' => $GLOBALS['up'], 'baseurl' => 'https://example.com/wp-content/uploads' ); }
function trailingslashit( $s ) { return rtrim( $s, '/' ) . '/'; }
function wp_mkdir_p( $p ) { return is_dir( $p ) || mkdir( $p, 0777, true ); }
function wp_delete_file( $f ) { @unlink( $f ); }
function sanitize_hex_color( $c ) { return preg_match( '/^#[0-9a-f]{6}$/i', (string) $c ) ? $c : null; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
require dirname( __DIR__ ) . '/mg-maintenance-studio/includes/class-schema.php';
require dirname( __DIR__ ) . '/mg-maintenance-studio/includes/class-pro-store.php';
use MGMS\Pro_Store;
use MGMS\Schema;
function check( bool $ok, string $label ): void { if ( ! $ok ) { fwrite( STDERR, "FAIL $label\n" ); exit( 1 ); } echo "PASS $label\n"; }
$free = array_keys( Schema::fields( false )['layout'][4] );

check( ! Pro_Store::valid() && Pro_Store::resolve_layout( 'storybook', $free ) === 'atelier', 'Ohne Paket fällt Märchenbuch auf Atelier zurück' );
check( Pro_Store::resolve_layout( 'unbekannt', $free ) === 'editorial', 'Unbekannte Szene fällt auf Editorial zurück' );
check( ! isset( Schema::fields()['layout'][4]['storybook'] ), 'Ohne Paket keine Pro-Szene speicherbar' );

$preset = json_encode( array( 'name' => 'Märchenbuch<script>', 'values' => array( 'layout' => 'storybook', 'background' => '#2a1d14', 'radius' => 6, 'evil' => 'x', 'text' => 'red', 'effect' => 'sparkles', 'animation' => true ) ) );
$now = time();
check( Pro_Store::save( array( 'storybook.css' => '.mgms-layout-storybook{}', 'storybook.json' => $preset ), array( 'iat' => $now, 'exp' => $now + 8 * 86400 ) ) instanceof WP_Error, 'Paket länger als 7 Tage abgelehnt' );
check( Pro_Store::save( array( 'storybook.css' => 'x', 'storybook.json' => $preset ), array( 'iat' => $now - 100, 'exp' => $now - 1 ) ) instanceof WP_Error, 'Abgelaufenes Paket abgelehnt' );
check( Pro_Store::save( array( '../evil.css' => 'x', 'zen.json' => $preset ), array( 'iat' => $now, 'exp' => $now + 3600 ) ) instanceof WP_Error, 'Ohne passende CSS + Vorlage keine Szene' );

$ok = Pro_Store::save( array( 'storybook.css' => '/* mgd:abc */.mgms-layout-storybook{color:red}', 'storybook.json' => $preset, 'evil.php' => '<?php x' ), array( 'iat' => $now, 'exp' => $now + 6 * 86400, 'version' => '0.1.0', 'watermark' => 'abc123' ) );
check( true === $ok && Pro_Store::valid() && Pro_Store::has_scene( 'storybook' ), 'Gültiges Paket gespeichert' );
$v = Pro_Store::scenes()['storybook']['values'];
check( ! isset( $v['evil'] ) && ! isset( $v['text'] ) && $v['background'] === '#2a1d14' && $v['radius'] === 6 && $v['effect'] === 'sparkles', 'Vorlage: nur bekannte Felder mit gültigen Werten' );
check( Pro_Store::scenes()['storybook']['name'] === 'Märchenbuch', 'Name ohne HTML' );
$dir = $GLOBALS['up'] . '/' . Pro_Store::data()['dir'];
check( is_file( $dir . '/storybook.css' ) && ! is_file( $dir . '/evil.php' ) && is_file( $dir . '/index.php' ), 'Nur CSS + index.php im Ordner, kein PHP aus dem Paket' );
check( preg_match( '/^mgms-pro-[a-f0-9]{16}$/', Pro_Store::data()['dir'] ) === 1, 'Ordnername zufällig' );
check( isset( Schema::fields()['layout'][4]['storybook'] ) && Pro_Store::resolve_layout( 'storybook', $free ) === 'storybook', 'Mit Paket ist die Pro-Szene wählbar' );
check( str_ends_with( Pro_Store::css_url( 'storybook' ), '/storybook.css' ) && Pro_Store::css_url( 'zen' ) === '', 'CSS-Adresse nur für vorhandene Szenen' );
check( ! Pro_Store::needs_refresh(), 'Kein Neuabruf bei 6 Tagen Rest' );

$GLOBALS['opt']['mgms_pro']['exp'] = $now - 1;
Pro_Store::expire();
check( ! Pro_Store::valid() && ! is_dir( $dir ) && Pro_Store::resolve_layout( 'storybook', $free ) === 'atelier', 'Nach Ablauf gelöscht, Rückfall auf Free' );
@rmdir( $GLOBALS['up'] );
