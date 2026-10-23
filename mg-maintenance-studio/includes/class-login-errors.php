<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Login_Errors {
	public static function register(): void {
		add_filter( 'login_errors', array( self::class, 'message' ), PHP_INT_MAX );
	}

	public static function message( string $message ): string {
		// Nur den sichtbaren Text ersetzen. Anmeldung, Fehlercodes und Sperren bleiben unverändert.
		return '' === trim( $message ) ? '' : 'Die Anmeldung war nicht möglich. Bitte prüfe deine Eingaben.';
	}
}
