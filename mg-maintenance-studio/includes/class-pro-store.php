<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
// Pro-Szenen lokal - speichern, prüfen, löschen, Rückfall auf Free
final class Pro_Store {
	public const ENTITLEMENT = 'mgd-maintenance-pro';
	public const PRODUCT     = 'mgd-maintenance';
	public const PACKAGE     = 'szenen';
	public const OPTION      = 'mgms_pro';
	public const MAX_TTL     = 604800;
	private const KEY_RE     = '/^[a-z]{3,20}$/D';
	// Rückfall wenn Paket weg - jede Pro-Szene auf eine ähnliche Free-Szene
	public const FALLBACK = array(
		'storybook' => 'atelier',
		'aurora'    => 'glass',
		'neon'      => 'glass',
		'countdown' => 'cinema',
		'newspaper' => 'swiss',
		'cosmos'    => 'glass',
		'zen'       => 'swiss',
		'collage'   => 'bloom',
	);
	// nur Namen für die Schloss-Karten, die Szenen selbst kommen vom Server
	public const CATALOG = array(
		'storybook' => array( 'Märchenbuch', 'Aufgeschlagenes Buch mit Papier und Goldschnitt' ),
		'aurora'    => array( 'Nordlicht', 'Wehende Polarlichter über dunklem Himmel' ),
		'neon'      => array( 'Neon', 'Leuchtschrift und Retro-Gitter' ),
		'countdown' => array( 'Countdown-Bühne', 'Riesige Rückkehrzeit im Mittelpunkt' ),
		'newspaper' => array( 'Zeitung', 'Titelseite mit Schlagzeile und Spalten' ),
		'cosmos'    => array( 'Weltall', 'Planet mit Ring und Mond' ),
		'zen'       => array( 'Zen', 'Pinselkreis und viel Ruhe' ),
		'collage'   => array( 'Collage', 'Dein Bild als Polaroid' ),
	);

	public static function data(): array {
		$d = get_option( self::OPTION, array() );
		return is_array( $d ) ? $d : array();
	}
	/** Paket da und noch gültig? */
	public static function valid(): bool {
		$d = self::data();
		return ! empty( $d['scenes'] ) && (int) ( $d['exp'] ?? 0 ) > time();
	}
	/** @return array<string,array{name:string,description:string,values:array}> */
	public static function scenes(): array {
		return self::valid() ? (array) self::data()['scenes'] : array();
	}
	public static function has_scene( string $key ): bool {
		return isset( self::scenes()[ $key ] );
	}
	/** Layout für die Ausgabe: Pro-Szene ohne Paket fällt auf Free zurück */
	public static function resolve_layout( string $layout, array $free ): string {
		if ( in_array( $layout, $free, true ) || self::has_scene( $layout ) ) {
			return $layout;
		}
		return self::FALLBACK[ $layout ] ?? 'editorial';
	}
	/** Einstellungen für die Ausgabe: Rückfall + lesbarer Text, wenn die Pro-Farben nicht zur Free-Szene passen */
	public static function fallback( array $s, array $free ): array {
		$layout      = (string) ( $s['layout'] ?? '' );
		$s['layout'] = self::resolve_layout( $layout, $free );
		if ( $s['layout'] === $layout || ! sanitize_hex_color( $s['background'] ?? '' ) || ! sanitize_hex_color( $s['text'] ?? '' ) ) {
			return $s;
		}
		// nur beim Rückfall, eigene Free-Farben fassen wir nie an
		if ( self::contrast( $s['background'], $s['text'] ) < 4.5 ) {
			$s['text'] = self::contrast( $s['background'], '#f7f3ec' ) >= self::contrast( $s['background'], '#1c1a17' ) ? '#f7f3ec' : '#1c1a17';
		}
		// Kasten passt nicht mehr zum Text > Kasten leicht aus dem Hintergrund ableiten
		if ( sanitize_hex_color( $s['card'] ?? '' ) && self::contrast( $s['card'], $s['text'] ) < 4.5 ) {
			$s['card'] = self::mix( $s['background'], $s['text'], 0.08 );
		}
		return $s;
	}
	private static function mix( string $a, string $b, float $share ): string {
		$out = '#';
		foreach ( array( 1, 3, 5 ) as $offset ) {
			$out .= sprintf( '%02x', (int) round( hexdec( substr( $a, $offset, 2 ) ) * ( 1 - $share ) + hexdec( substr( $b, $offset, 2 ) ) * $share ) );
		}
		return $out;
	}
	/** Kontrast nach WCAG zwischen zwei Hex-Farben */
	public static function contrast( string $a, string $b ): float {
		$lum    = static function ( string $hex ): float {
			$parts = array();
			foreach ( array( 1, 3, 5 ) as $offset ) {
				$channel = hexdec( substr( $hex, $offset, 2 ) ) / 255;
				$parts[] = $channel <= 0.04045 ? $channel / 12.92 : ( ( $channel + 0.055 ) / 1.055 ) ** 2.4;
			}
			return 0.2126 * $parts[0] + 0.7152 * $parts[1] + 0.0722 * $parts[2];
		};
		$first  = $lum( $a );
		$second = $lum( $b );
		return ( max( $first, $second ) + 0.05 ) / ( min( $first, $second ) + 0.05 );
	}
	public static function css_url( string $key ): string {
		$d = self::data();
		if ( ! self::has_scene( $key ) || empty( $d['dir'] ) ) {
			return '';
		}
		$up = wp_upload_dir( null, false );
		return trailingslashit( $up['baseurl'] ) . $d['dir'] . '/' . $key . '.css';
	}
	/** Läuft das Paket bald ab? Dann neu holen (Vertrag: spätestens 1 Tag vor exp) */
	public static function needs_refresh(): bool {
		return (int) ( self::data()['exp'] ?? 0 ) - time() < DAY_IN_SECONDS;
	}

	/**
	 * Geprüftes Paket übernehmen. Signatur/Domain/sha256 prüft vorher der Baukasten.
	 * $files: Liste [name => inhalt], $meta: exp, iat, version, watermark
	 */
	public static function save( array $files, array $meta ): bool|\WP_Error {
		$exp = (int) ( $meta['exp'] ?? 0 );
		$iat = (int) ( $meta['iat'] ?? 0 );
		if ( $exp <= time() || $exp - $iat > self::MAX_TTL ) {
			return new \WP_Error( 'mgms_pro_time', 'Paket abgelaufen oder zu lange gültig.' );
		}
		$scenes = array();
		$css    = array();
		foreach ( $files as $name => $content ) {
			if ( ! is_string( $name ) || ! is_string( $content ) || ! preg_match( '/^([a-z]{3,20})\.(css|json)$/D', $name, $m ) ) {
				continue;
			}
			if ( 'css' === $m[2] ) {
				$css[ $m[1] ] = $content;
				continue;
			}
			$preset = json_decode( $content, true );
			$scene  = self::clean_preset( $m[1], is_array( $preset ) ? $preset : array() );
			if ( $scene ) {
				$scenes[ $m[1] ] = $scene;
			}
		}
		// nur Szenen mit CSS und Vorlage
		$scenes = array_intersect_key( $scenes, $css );
		if ( ! $scenes ) {
			return new \WP_Error( 'mgms_pro_empty', 'Paket enthält keine gültigen Szenen.' );
		}
		$old = self::data();
		$dir = ! empty( $old['dir'] ) && preg_match( '/^mgms-pro-[a-f0-9]{16}$/D', $old['dir'] ) ? $old['dir'] : 'mgms-pro-' . bin2hex( random_bytes( 8 ) );
		$up  = wp_upload_dir( null, true );
		$path = trailingslashit( $up['basedir'] ) . $dir;
		if ( ! wp_mkdir_p( $path ) ) {
			return new \WP_Error( 'mgms_pro_dir', 'Ordner für Pro-Szenen konnte nicht angelegt werden.' );
		}
		// leeres index.php gegen Verzeichnislisten
		file_put_contents( $path . '/index.php', "<?php\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		foreach ( glob( $path . '/*.css' ) ?: array() as $stale ) {
			wp_delete_file( $stale );
		}
		foreach ( array_keys( $scenes ) as $key ) {
			file_put_contents( $path . '/' . $key . '.css', $css[ $key ] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}
		update_option(
			self::OPTION,
			array(
				'dir'       => $dir,
				'exp'       => $exp,
				'iat'       => $iat,
				'version'   => (string) ( $meta['version'] ?? '' ),
				'watermark' => preg_replace( '/[^a-zA-Z0-9]/', '', (string) ( $meta['watermark'] ?? '' ) ),
				'scenes'    => $scenes,
			),
			false
		);
		return true;
	}
	/** Vorlage aus dem Paket nur mit bekannten Feldern und gültigen Werten */
	private static function clean_preset( string $key, array $preset ): array {
		if ( ! preg_match( self::KEY_RE, $key ) || ( $preset['values']['layout'] ?? '' ) !== $key ) {
			return array();
		}
		$fields = Schema::fields( false );
		$values = array( 'layout' => $key );
		foreach ( (array) $preset['values'] as $field => $value ) {
			if ( 'layout' === $field || ! isset( $fields[ $field ] ) ) {
				continue;
			}
			$type = $fields[ $field ][1];
			if ( 'color' === $type && is_string( $value ) && sanitize_hex_color( $value ) ) {
				$values[ $field ] = sanitize_hex_color( $value );
			} elseif ( 'checkbox' === $type && is_bool( $value ) ) {
				$values[ $field ] = $value;
			} elseif ( 'number' === $type && is_int( $value ) && $value >= $fields[ $field ][4][0] && $value <= $fields[ $field ][4][1] ) {
				$values[ $field ] = $value;
			} elseif ( 'select' === $type && is_string( $value ) && array_key_exists( $value, $fields[ $field ][4] ) ) {
				$values[ $field ] = $value;
			}
		}
		return array(
			'name'        => sanitize_text_field( (string) ( $preset['name'] ?? self::CATALOG[ $key ][0] ?? $key ) ),
			'description' => sanitize_text_field( (string) ( $preset['description'] ?? '' ) ),
			'values'      => $values,
		);
	}
	/** Pro-Dateien und Daten weg - bei Widerruf, Ablauf oder Deinstallation */
	public static function clear(): void {
		$d = self::data();
		if ( ! empty( $d['dir'] ) && preg_match( '/^mgms-pro-[a-f0-9]{16}$/D', $d['dir'] ) ) {
			$up   = wp_upload_dir( null, false );
			$path = trailingslashit( $up['basedir'] ) . $d['dir'];
			foreach ( glob( $path . '/*' ) ?: array() as $file ) {
				wp_delete_file( $file );
			}
			@rmdir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
		}
		delete_option( self::OPTION );
	}
	/** abgelaufenes Paket aufräumen (ohne Server ist nach exp Schluss) */
	public static function expire(): void {
		$d = self::data();
		if ( $d && (int) ( $d['exp'] ?? 0 ) <= time() ) {
			self::clear();
		}
	}
}
