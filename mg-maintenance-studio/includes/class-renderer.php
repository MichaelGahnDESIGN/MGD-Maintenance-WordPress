<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Renderer {
	public static function headers( array $settings, bool $preview ): void {
		status_header( $preview ? 200 : (int) $settings['status'] );
		nocache_headers();
		header( 'Cache-Control: no-store, private, max-age=0' );
		header( 'X-Robots-Tag: noindex, nofollow, noarchive' );
		header( 'Referrer-Policy: no-referrer' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		header( "Content-Security-Policy: default-src 'none'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'none'; base-uri 'none'; form-action 'none'; frame-ancestors 'self'" );
		if ( ! $preview && '503' === $settings['status'] ) {
			header( 'Retry-After: ' . (int) $settings['retry'] ); }
	}
	public static function variables( array $s ): string {
		$css = '';
		foreach ( array( 'background', 'text', 'accent', 'button', 'button_text', 'card', 'border_color' ) as $key ) {
			$css .= '--' . str_replace( '_', '-', $key ) . ':' . sanitize_hex_color( $s[ $key ] ) . ';'; }
		foreach ( array( 'width', 'padding', 'padding_tablet', 'padding_mobile', 'radius', 'border', 'logo_width' ) as $key ) {
			$css .= '--' . str_replace( '_', '-', $key ) . ':' . absint( $s[ $key ] ) . 'px;'; }
		return $css . '--align:' . ( in_array( $s['align'], array( 'left', 'right', 'center' ), true ) ? $s['align'] : 'center' ) . ';--heading-font:' . ( 'serif' === $s['font'] ? 'Georgia,serif' : 'system-ui,sans-serif' ) . ';';
	}
	public static function render( array $s, bool $preview = false ): void {
		self::headers( $s, $preview );
		$legal = array_filter( $s['legal'], static fn( $row ) => $row['enabled'] && $row['title'] );
		uasort( $legal, static fn( $a, $b ) => $a['order'] <=> $b['order'] );
		$logo       = Settings::media_url( (int) $s['logo'] );
		$background = Settings::media_url( (int) $s['background_image'] );
		$font       = Settings::media_url( (int) $s['font_file'], 'font' );
		require MGMS_DIR . 'templates/maintenance.php';
	}
}
