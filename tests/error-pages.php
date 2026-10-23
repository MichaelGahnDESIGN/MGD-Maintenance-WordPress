<?php
/** Schnelle Grenzfallprüfung ohne WordPress-Datenbank. */
define( 'ABSPATH', __DIR__ );
define( 'MGMS_DIR', dirname( __DIR__ ) . '/mg-maintenance-studio/' );
$GLOBALS['options'] = array();
$GLOBALS['is_admin'] = false;
$GLOBALS['is_404'] = false;
$GLOBALS['logged_in'] = false;
class WP_Error {
	private array $errors = array();
	public function add( $key, $message ): void { $this->errors[] = $message; }
	public function has_errors(): bool { return (bool) $this->errors; }
}
function get_option( $key, $fallback = false ) { return $GLOBALS['options'][ $key ] ?? $fallback; }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_hex_color( $value ) { return preg_match( '/^#[0-9a-f]{6}$/i', (string) $value ) ? strtolower( $value ) : null; }
function is_admin() { return $GLOBALS['is_admin']; }
function is_404() { return $GLOBALS['is_404']; }
function is_feed() { return false; }
function is_user_logged_in() { return $GLOBALS['logged_in']; }
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return false; }
function wp_is_json_request() { return false; }
function _default_wp_die_handler() {}
require MGMS_DIR . 'includes/class-settings.php';
require MGMS_DIR . 'includes/class-error-pages.php';
function check( bool $okay, string $label ): void { if ( ! $okay ) { throw new RuntimeException( $label ); } echo "PASS $label\n"; }
$defaults = MGMS\Error_Pages::defaults();
check( ! $defaults['404']['enabled'] && ! $defaults['403']['enabled'], 'Beide Fehlerseiten initial aus' );
$valid = MGMS\Error_Pages::sanitize( $defaults );
check( is_array( $valid ), 'Standardwerte gültig' );
$bad = $defaults;
$bad['404']['template'] = 'template/../../';
check( $bad && MGMS\Error_Pages::sanitize( $bad ) instanceof WP_Error, 'Unbekannte Vorlage abgewiesen' );
$bad = $defaults;
$bad['404']['title'] = '<script>alert(1)</script>Hallo';
check( 'alert(1)Hallo' === MGMS\Error_Pages::sanitize( $bad )['404']['title'], 'HTML aus Titel entfernt' );
$bad = $defaults;
$bad['404']['accent'] = '#ffffff';
check( MGMS\Error_Pages::sanitize( $bad ) instanceof WP_Error, 'Kontrastproblem abgewiesen' );
$GLOBALS['is_404'] = true;
check( 'original.php' === MGMS\Error_Pages::template( 'original.php' ), 'Theme-Vorlage bei ausgeschalteter Funktion' );
$valid['404']['enabled'] = true;
$GLOBALS['options'][MGMS\Error_Pages::LIVE] = $valid;
check( str_ends_with( MGMS\Error_Pages::template( 'original.php' ), '/templates/error-page.php' ), '404-Vorlage nur nach Veröffentlichung' );
$GLOBALS['options'][MGMS\Settings::STATE] = true;
check( 'original.php' === MGMS\Error_Pages::template( 'original.php' ), 'Wartungsmodus hat Vorrang' );
$GLOBALS['options'][MGMS\Settings::STATE] = false;
$valid['403']['enabled'] = true;
$GLOBALS['options'][MGMS\Error_Pages::LIVE] = $valid;
$default_handler = '_default_wp_die_handler';
$GLOBALS['pagenow'] = 'wp-login.php';
check( $default_handler === MGMS\Error_Pages::die_handler( $default_handler ), 'Anmeldung bleibt bei WordPress' );
$GLOBALS['pagenow'] = 'index.php';
check( is_array( MGMS\Error_Pages::die_handler( $default_handler ) ), 'Öffentlicher 403 nutzt eigene Ansicht' );
$GLOBALS['is_admin'] = true;
check( $default_handler === MGMS\Error_Pages::die_handler( $default_handler ), 'Admin bleibt bei WordPress' );
