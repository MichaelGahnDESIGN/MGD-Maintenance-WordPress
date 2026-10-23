<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

final class Error_Pages_Admin {
	public static function page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = Error_Pages::get( true );
		$live = Error_Pages::get();
		$enabled = (bool) get_option( Settings::STATE );
		require MGMS_DIR . 'templates/error-pages-admin.php';
	}

	public static function revision(): string {
		return hash( 'sha256', wp_json_encode( Error_Pages::get( true ) ) );
	}

	public static function save(): void {
		if ( ! current_user_can( 'manage_options' ) || 'POST' !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'mgms_error_save' );
		if ( ! hash_equals( self::revision(), sanitize_text_field( wp_unslash( $_POST['revision'] ?? '' ) ) ) ) {
			wp_die( 'Der Entwurf wurde inzwischen geändert. Bitte die Seite neu laden.', '', array( 'response' => 409 ) );
		}
		// Jedes Feld wird vor dem Speichern einzeln geprüft.
		$settings = Error_Pages::sanitize( (array) wp_unslash( $_POST['settings'] ?? array() ) );
		if ( is_wp_error( $settings ) ) {
			wp_die( esc_html( implode( ' ', $settings->get_error_messages() ) ), '', array( 'response' => 400, 'back_link' => true ) );
		}
		$publish = 'publish' === sanitize_key( wp_unslash( $_POST['intent'] ?? '' ) );
		if ( $publish && empty( $_POST['confirm_publish'] ) ) {
			wp_die( 'Veröffentlichung bitte ausdrücklich bestätigen.', '', array( 'response' => 400, 'back_link' => true ) );
		}
		update_option( Error_Pages::DRAFT, $settings, false );
		if ( $publish ) {
			update_option( Error_Pages::LIVE, $settings, false );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'mgms-error-pages', 'mgms_notice' => $publish ? 'published' : 'saved' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function preview(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'mgms_error_preview' );
		$code = sanitize_key( wp_unslash( $_GET['code'] ?? '' ) );
		if ( ! in_array( $code, array( '404', '403' ), true ) ) {
			wp_die( 'Unbekannte Fehlerseite.', '', array( 'response' => 400 ) );
		}
		Error_Pages::render( $code, true );
		exit;
	}
}
