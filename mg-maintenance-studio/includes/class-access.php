<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Access {
	public static function user_allowed( array $settings ): bool {
		if ( ! is_user_logged_in() ) {
			return false; }
		if ( current_user_can( 'manage_options' ) ) {
			return (bool) $settings['admin_bypass']; }
		if ( 'all' === $settings['users'] ) {
			return true; }
		return 'roles' === $settings['users'] && (bool) array_intersect( wp_get_current_user()->roles, $settings['roles'] );
	}
	public static function allowed( array $settings ): bool {
		return self::user_allowed( $settings ) || ( $settings['ip_enabled'] && IP::listed( IP::detect( $_SERVER, $settings['proxies'] ), $settings['ip_list'] ) );
	}
}
