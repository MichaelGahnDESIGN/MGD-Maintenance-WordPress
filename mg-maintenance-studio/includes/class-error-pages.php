<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

final class Error_Pages {
	public const DRAFT = 'mgms_error_draft';
	public const LIVE  = 'mgms_error_published';

	public static function defaults(): array {
		return array(
			'404' => array( 'enabled' => false, 'template' => 'clean', 'title' => 'Diese Seite gibt es nicht mehr.', 'message' => 'Vielleicht wurde die Adresse geändert. Über die Suche oder die Startseite finden Sie weiter.', 'button' => 'Zur Startseite', 'search' => true, 'background' => '#f7f5f1', 'accent' => '#244e43', 'text' => '#202a28' ),
			'403' => array( 'enabled' => false, 'template' => 'clean', 'title' => 'Zugriff nicht möglich.', 'message' => 'Für diese Seite fehlt Ihnen die Berechtigung.', 'button' => 'Zur Startseite', 'search' => false, 'background' => '#f7f5f1', 'accent' => '#244e43', 'text' => '#202a28' ),
		);
	}

	public static function templates(): array {
		return array( 'clean' => 'Klar', 'editorial' => 'Editorial', 'dark' => 'Dunkel' );
	}

	public static function get( bool $draft = false ): array {
		$stored = get_option( $draft ? self::DRAFT : self::LIVE, array() );
		$result = self::defaults();
		foreach ( $result as $code => $defaults ) {
			if ( is_array( $stored ) && isset( $stored[ $code ] ) && is_array( $stored[ $code ] ) ) {
				$result[ $code ] = array_replace( $defaults, array_intersect_key( $stored[ $code ], $defaults ) );
			}
		}
		return $result;
	}

	/** Ungültige Eingaben stoppen die ganze Veröffentlichung. */
	public static function sanitize( array $input ): array|\WP_Error {
		$result = self::defaults();
		$errors = new \WP_Error();
		foreach ( $result as $code => $defaults ) {
			$row = $input[ $code ] ?? array();
			if ( ! is_array( $row ) ) {
				$errors->add( $code, 'Ungültige Einstellungen für ' . $code . '.' );
				continue;
			}
			$result[ $code ]['enabled'] = ! empty( $row['enabled'] );
			$result[ $code ]['search']  = '404' === $code && ! empty( $row['search'] );
			$template = sanitize_key( Settings::text( $row['template'] ?? '' ) );
			if ( ! isset( self::templates()[ $template ] ) ) {
				$errors->add( $code, 'Unbekannte Vorlage für ' . $code . '.' );
			} else {
				$result[ $code ]['template'] = $template;
			}
			foreach ( array( 'title' => 110, 'message' => 600, 'button' => 60 ) as $key => $limit ) {
				$value = sanitize_text_field( Settings::text( $row[ $key ] ?? '' ) );
				$length = function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
				if ( '' === $value || $length > $limit ) {
					$errors->add( $code, $code . ': ' . $key . ' muss 1 bis ' . $limit . ' Zeichen enthalten.' );
				} else {
					$result[ $code ][ $key ] = $value;
				}
			}
			foreach ( array( 'background', 'accent', 'text' ) as $key ) {
				$color = sanitize_hex_color( Settings::text( $row[ $key ] ?? '' ) );
				if ( ! $color || ! preg_match( '/^#[0-9a-f]{6}$/i', $color ) ) {
					$errors->add( $code, $code . ': ungültige Farbe.' );
				} else {
					$result[ $code ][ $key ] = $color;
				}
			}
			if ( 'dark' !== $result[ $code ]['template'] && self::contrast( $result[ $code ]['background'], $result[ $code ]['text'] ) < 4.5 ) {
				$errors->add( $code, $code . ': Text und Hintergrund benötigen mehr Kontrast.' );
			}
			if ( 'dark' !== $result[ $code ]['template'] && self::contrast( $result[ $code ]['accent'], '#ffffff' ) < 4.5 ) {
				$errors->add( $code, $code . ': Akzent und weißer Buttontext benötigen mehr Kontrast.' );
			}
		}
		return $errors->has_errors() ? $errors : $result;
	}

	private static function contrast( string $a, string $b ): float {
		$lum = static function ( string $hex ): float {
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

	public static function template( string $path ): string {
		if ( ! is_admin() && ! get_option( Settings::STATE ) && is_404() && self::get()['404']['enabled'] && ! is_feed() ) {
			return MGMS_DIR . 'templates/error-page.php';
		}
		return $path;
	}

	/** Nur WordPress-403 im öffentlichen HTML ersetzen. Server-, Login- und API-Sperren bleiben unberührt. */
	public static function die_handler( callable $handler ): callable {
		global $pagenow;
		if ( ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! is_user_logged_in() && ! in_array( $pagenow, array( 'wp-login.php', 'wp-signup.php' ), true ) && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) && ! ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) && ! wp_is_json_request() && ! get_option( Settings::STATE ) && self::get()['403']['enabled'] ) {
			return array( self::class, 'handle_die' );
		}
		return $handler;
	}

	public static function handle_die( $message, $title = '', $args = array() ): void {
		$code = is_array( $args ) ? (int) ( $args['response'] ?? 500 ) : (int) $args;
		if ( 403 !== $code ) {
			_default_wp_die_handler( $message, $title, $args );
			return;
		}
		self::render( '403' );
		exit;
	}

	public static function render( string $code, bool $preview = false ): void {
		$s = self::get( $preview )[ $code ];
		if ( ! $preview ) {
			status_header( (int) $code );
		}
		nocache_headers();
		header( 'Cache-Control: no-store, private, max-age=0', true );
		header( 'X-Robots-Tag: noindex, nofollow', true );
		header( 'X-Content-Type-Options: nosniff', true );
		header( "Content-Security-Policy: default-src 'none'; style-src 'self' 'unsafe-inline'; form-action 'self'; base-uri 'none'; frame-ancestors 'self'", true );
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ), true );
		$home = home_url( '/' );
		require MGMS_DIR . 'templates/error-page-content.php';
	}
}
