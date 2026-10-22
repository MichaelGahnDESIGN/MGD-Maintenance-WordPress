<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class IP {
	public static function valid( string $network ): bool {
		$parts = explode( '/', $network );
		if ( count( $parts ) > 2 || ! filter_var( $parts[0], FILTER_VALIDATE_IP ) ) {
			return false; }
		$bits = strlen( inet_pton( $parts[0] ) ) * 8;
		return ! isset( $parts[1] ) || ( ctype_digit( $parts[1] ) && (int) $parts[1] <= $bits );
	}
	public static function contains( string $ip, string $network ): bool {
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) || ! self::valid( $network ) ) {
			return false; }
		$parts   = explode( '/', $network );
		$address = inet_pton( $ip );
		$base    = inet_pton( $parts[0] );
		if ( strlen( $address ) !== strlen( $base ) ) {
			return false; }
		$bits  = isset( $parts[1] ) ? (int) $parts[1] : strlen( $base ) * 8;
		$bytes = intdiv( $bits, 8 );
		$rest  = $bits % 8;
		return substr( $address, 0, $bytes ) === substr( $base, 0, $bytes ) && ( ! $rest || ( ord( $address[ $bytes ] ) & ( 255 << ( 8 - $rest ) ) ) === ( ord( $base[ $bytes ] ) & ( 255 << ( 8 - $rest ) ) ) );
	}
	public static function listed( string $ip, string $networks ): bool {
		foreach ( preg_split( '/\R/', trim( $networks ) ) as $network ) {
			if ( self::contains( $ip, trim( $network ) ) ) {
				return true; }
		}
		return false;
	}
	/** Header nur hinter explizit vertrauten Proxys von rechts nach links auswerten. */
	public static function detect( array $server, string $proxies ): string {
		$peer = $server['REMOTE_ADDR'] ?? '';
		if ( ! filter_var( $peer, FILTER_VALIDATE_IP ) ) {
			return ''; }
		if ( ! self::listed( $peer, $proxies ) ) {
			return $peer; }
		$raw = $server['HTTP_X_FORWARDED_FOR'] ?? '';
		if ( strlen( $raw ) > 2048 ) {
			return $peer; }
		$chain = array_map( 'trim', explode( ',', $raw ) );
		foreach ( $chain as $ip ) {
			if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $peer; }
		}
		$current = $peer;
		foreach ( array_reverse( $chain ) as $ip ) {
			if ( ! self::listed( $current, $proxies ) ) {
				break; }
			$current = $ip;
		}
		return $current;
	}
}
