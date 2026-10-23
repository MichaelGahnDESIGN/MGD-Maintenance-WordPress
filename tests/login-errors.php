<?php
/** Gezielte Ausgabeprüfung ohne Datenbank, Konto oder Anmeldeversuch. */
define( 'ABSPATH', __DIR__ );
$GLOBALS['registered'] = array();
function add_filter( $name, $callback, $priority ): void {
	$GLOBALS['registered'][] = array( $name, $callback, $priority );
}
require dirname( __DIR__ ) . '/mg-maintenance-studio/includes/class-login-errors.php';
MGMS\Login_Errors::register();
$expected = 'Die Anmeldung war nicht möglich. Bitte prüfe deine Eingaben.';
if ( count( $GLOBALS['registered'] ) !== 1 || $GLOBALS['registered'][0][0] !== 'login_errors' || $GLOBALS['registered'][0][2] !== PHP_INT_MAX ) {
	throw new RuntimeException( 'Falscher Hook oder Priorität.' );
}
$callback = $GLOBALS['registered'][0][1];
foreach ( array( 'Unbekannte E-Mail-Adresse.', 'Unbekannter Benutzername.', 'Das Passwort ist falsch.', 'Benutzername fehlt.', 'Passwort fehlt.', '<strong>Geheimer Kontoname</strong>', 'Zugang gesperrt.' ) as $message ) {
	if ( call_user_func( $callback, $message ) !== $expected ) {
		throw new RuntimeException( 'Konkreter Fehlertext gelangt in die Ausgabe.' );
	}
}
foreach ( array( '', " \n\t" ) as $message ) {
	if ( '' !== call_user_func( $callback, $message ) ) {
		throw new RuntimeException( 'Leere Meldung erzeugt einen Fehler.' );
	}
}
echo "PASS: nativer Textfilter, späte Priorität, sieben neutrale Fehlerfälle und leere Meldungen.\n";
