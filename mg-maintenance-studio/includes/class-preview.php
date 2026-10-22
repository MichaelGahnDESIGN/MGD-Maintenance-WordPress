<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Preview {
	/** Einmal-Token bleibt im POST-Body und wird nur als Hash gespeichert. */
	public static function issue(): string {
		$token = bin2hex( random_bytes( 32 ) );
		update_user_meta(
			get_current_user_id(),
			'mgms_preview',
			array(
				'hash'    => hash( 'sha256', $token ),
				'expires' => time() + 120,
				'session' => hash( 'sha256', wp_get_session_token() ),
			)
		);
		return $token;
	}
	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) ); }
		check_admin_referer( 'mgms_preview' );
		if ( ! Settings::get( true )['preview_enabled'] ) {
			wp_die( 'Vorschau ist deaktiviert.', '', array( 'response' => 403 ) ); }
		$saved = get_user_meta( get_current_user_id(), 'mgms_preview', true );
		$token = sanitize_text_field( wp_unslash( $_POST['preview_token'] ?? '' ) );
		if ( ! is_array( $saved ) || $saved['expires'] < time() || ! hash_equals( $saved['session'], hash( 'sha256', wp_get_session_token() ) ) || ! hash_equals( $saved['hash'], hash( 'sha256', $token ) ) ) {
			wp_die( 'Vorschau abgelaufen oder bereits verwendet. Editor neu laden.', '', array( 'response' => 403 ) ); }
		// Bedingtes Löschen verhindert eine doppelte Verwendung bei parallelen Requests.
		if ( ! delete_user_meta( get_current_user_id(), 'mgms_preview', $saved ) ) {
			wp_die( 'Vorschau bereits verwendet.', '', array( 'response' => 403 ) ); }
		Renderer::render( Settings::get( true ), true );
		exit;
	}
}
