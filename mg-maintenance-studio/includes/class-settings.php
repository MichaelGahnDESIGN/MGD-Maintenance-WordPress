<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Settings {
	public const DRAFT = 'mgms_draft';
	public const LIVE  = 'mgms_published';
	public const STATE = 'mgms_enabled';
	public static function get( bool $draft = false ): array {
		return array_replace( Schema::defaults(), (array) get_option( $draft ? self::DRAFT : self::LIVE, array() ) );
	}
	/** Nur Mediathek-Dateien vom selben Ursprung, keine CDN- oder Fremd-URLs. */
	public static function media_url( int $id, string $type = 'image' ): string {
		if ( ! $id || get_post_type( $id ) !== 'attachment' ) {
			return ''; }
		$mime    = get_post_mime_type( $id );
		$allowed = 'font' === $type ? array( 'font/woff2', 'application/font-woff2' ) : array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif' );
		$url     = wp_get_attachment_url( $id );
		$origin  = wp_parse_url( home_url() );
		$asset   = wp_parse_url( (string) $url );
		if ( ! in_array( $mime, $allowed, true ) || ! $url || ( $asset['host'] ?? '' ) !== ( $origin['host'] ?? '' ) || ( $asset['scheme'] ?? '' ) !== ( $origin['scheme'] ?? '' ) || ( $asset['port'] ?? null ) !== ( $origin['port'] ?? null ) ) {
			return ''; }
		if ( 'font' === $type && 'woff2' !== strtolower( pathinfo( $asset['path'] ?? '', PATHINFO_EXTENSION ) ) ) {
			return ''; }
		return $url;
	}
	public static function text( mixed $value ): string {
		return is_scalar( $value ) ? (string) $value : ''; }
	/** Ungültige Werte verwerfen den gesamten Speichervorgang, statt Freigaben still zu verändern. */
	public static function sanitize( array $input ): array|\WP_Error {
		$result = Schema::defaults();
		$errors = new \WP_Error();
		foreach ( Schema::fields() as $key => $field ) {
			$value = self::text( $input[ $key ] ?? '' );
			switch ( $field[1] ) {
				case 'checkbox':
					$result[ $key ] = '1' === $value;
					break;
				case 'number':
					if ( ! ctype_digit( $value ) || (int) $value < $field[4][0] || (int) $value > $field[4][1] ) {
						$errors->add( $key, $field[0] . ': Wert außerhalb des erlaubten Bereichs.' ); }
					$result[ $key ] = (int) $value;
					break;
				case 'color':
					$result[ $key ] = sanitize_hex_color( $value );
					if ( ! $result[ $key ] ) {
						$errors->add( $key, $field[0] . ': gültige Hex-Farbe erforderlich.' );
					} break;
				case 'select':
					if ( ! array_key_exists( $value, $field[4] ) ) {
						$errors->add( $key, 'Ungültige Auswahl: ' . $field[0] ); }
					$result[ $key ] = $value;
					break;
				case 'image':
				case 'font':
						$result[ $key ] = absint( $value );
					if ( $result[ $key ] && ! self::media_url( $result[ $key ], $field[1] ) ) {
						$errors->add( $key, $field[0] . ': nur passende lokale Mediathek-Dateien sind erlaubt.' );
					} break;
				case 'email':
					$result[ $key ] = sanitize_email( $value );
					if ( $value && ! is_email( $value ) ) {
						$errors->add( $key, 'Die E-Mail-Adresse ist ungültig.' );
					} break;
				case 'phone':
					$result[ $key ] = sanitize_text_field( $value );
					if ( $value && ! preg_match( '/^[+0-9() .\/-]{3,40}$/', $value ) ) {
						$errors->add( $key, 'Die Telefonnummer ist ungültig.' );
					} break;
				case 'url':
					$result[ $key ] = esc_url_raw( $value, array( 'https', 'http', 'mailto', 'tel' ) );
					if ( $value && ( ! $result[ $key ] || ! preg_match( '/^(https?:\/\/|mailto:|tel:)/i', $value ) ) ) {
						$errors->add( $key, 'Ungültiges Linkziel.' );
					} break;
				case 'datetime':
					$result[ $key ] = $value;
					if ( $value && ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?(Z|[+-]\d{2}:\d{2})$/', $value ) || false === strtotime( $value ) ) ) {
						$errors->add( $key, 'Rückkehrzeit benötigt Datum, Uhrzeit und Zeitzone, etwa 2030-05-01T12:00+02:00.' );
					} break;
				case 'roles':
					$result[ $key ] = array_values( array_intersect( array_keys( wp_roles()->roles ), array_map( 'sanitize_key', array_filter( (array) ( $input[ $key ] ?? array() ), 'is_string' ) ) ) );
					break;
				case 'cidr':
				case 'proxy':
					$lines = array_filter( array_map( 'trim', preg_split( '/\R/', $value ) ) );
					if ( count( $lines ) > 100 ) {
						$errors->add( $key, 'Maximal 100 Netzwerke sind erlaubt.' ); }
					foreach ( $lines as $line ) {
						if ( ! IP::valid( $line ) || ( 'proxy' === $field[1] && str_contains( $line, '/' ) ) ) {
							$errors->add( $key, 'Ungültiger IP-Eintrag. Proxys benötigen einzelne IP-Adressen.' );
							continue; }
						$parts = explode( '/', $line );
						if ( isset( $parts[1] ) && (int) $parts[1] < ( str_contains( $parts[0], ':' ) ? 64 : 24 ) ) {
							$errors->add( $key, 'Zu große Freigabe: IPv4-Netze müssen /24 oder enger, IPv6-Netze /64 oder enger sein.' ); }
					}
					$result[ $key ] = implode( "\n", array_unique( $lines ) );
					break;
				case 'textarea':
					$result[ $key ] = sanitize_textarea_field( $value );
					break;
				default:
					$result[ $key ] = sanitize_text_field( $value );
			}
		}
		if ( 'logo' === $result['brand_presentation'] && ! $result['logo'] ) {
			$errors->add( 'brand_presentation', 'Nur Logo benötigt ein Bild aus der Mediathek.' );
		}
		$result['preset'] = sanitize_key( self::text( $input['preset'] ?? 'custom' ) );
		foreach ( array_slice( (array) ( $input['legal'] ?? array() ), 0, 5 ) as $index => $row ) {
			if ( ! is_array( $row ) ) {
				continue; }
			$page = absint( $row['page'] ?? 0 );
			if ( $page && ( 'page' !== get_post_type( $page ) || 'publish' !== get_post_status( $page ) || post_password_required( $page ) ) ) {
				$errors->add( 'legal', 'Rechtslinks benötigen eine veröffentlichte, nicht passwortgeschützte Seite.' ); }
			// Keine Bilder, iframes, Shortcodes oder eingebetteten Fremdressourcen in Rechtstexten.
			$allowed           = array(
				'p'      => array(),
				'br'     => array(),
				'strong' => array(),
				'em'     => array(),
				'h2'     => array(),
				'h3'     => array(),
				'ul'     => array(),
				'ol'     => array(),
				'li'     => array(),
				'a'      => array(
					'href'  => true,
					'title' => true,
				),
			);
			$result['legal'][] = array(
				'title'   => sanitize_text_field( self::text( $row['title'] ?? '' ) ),
				'content' => wp_kses( self::text( $row['content'] ?? '' ), $allowed ),
				'enabled' => ! empty( $row['enabled'] ),
				'footer'  => ! empty( $row['footer'] ),
				'modal'   => ! empty( $row['modal'] ),
				'order'   => min( 99, absint( $row['order'] ?? $index ) ),
				'page'    => $page,
			);
		}
		return $errors->has_errors() ? $errors : $result;
	}
}
