<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Plugin {
	public static function activate( bool $network_wide = false ): void {
		if ( $network_wide ) {
			wp_die( 'Bitte MG Maintenance Studio je Website einzeln aktivieren.' ); }
		add_option( Settings::DRAFT, Schema::defaults(), '', false );
		add_option( Settings::LIVE, Schema::defaults(), '', false );
		add_option( Settings::STATE, false, '', false );
	}
	public static function deactivate(): void {
		// Bewussten Wartungsstatus behalten. Reaktivierung setzt die bisherige Entscheidung fort.
		delete_metadata( 'user', 0, 'mgms_preview', '', true );
	}
	public static function boot(): void {
		Updates::register();
		Presentation::register();
		add_action( 'admin_menu', array( Admin::class, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( Admin::class, 'assets' ) );
		add_action( 'admin_post_mgms_save', array( Admin::class, 'save' ) );
		add_action( 'admin_post_mgms_state', array( Admin::class, 'state' ) );
		add_action( 'admin_post_mgms_preview', array( Preview::class, 'render' ) );
		add_action( 'admin_notices', array( Admin::class, 'conflicts' ) );
		add_action( 'init', array( self::class, 'cache' ), 1 );
		add_action( 'template_redirect', array( self::class, 'frontend' ), 0 );
		add_filter( 'robots_txt', array( self::class, 'robots' ), 99, 1 );
		add_filter( 'rest_pre_dispatch', array( self::class, 'rest' ), 99, 1 );
		add_action( 'wp_footer', array( self::class, 'badge' ) );
	}
	public static function cache(): void {
		if ( get_option( Settings::STATE ) && ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true ); }
	}
	public static function frontend(): void {
		if ( ! get_option( Settings::STATE ) || is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return; }
		$s = Settings::get();
		nocache_headers();
		if ( Access::allowed( $s ) || is_robots() ) {
			return; }
		// Reguläre Rechtsseiten werden nur bei einer explizit veröffentlichten Freigabe ausgeliefert.
		foreach ( $s['legal'] as $row ) {
			if ( $row['enabled'] && $row['page'] && is_page( $row['page'] ) ) {
				return; }
		}
		Renderer::render( $s );
		exit;
	}
	public static function rest( mixed $result ): mixed {
		if ( get_option( Settings::STATE ) && ! is_user_logged_in() && ! Access::allowed( Settings::get() ) ) {
			return new \WP_Error( 'mgms_maintenance', 'Die Website wird gewartet.', array( 'status' => 503 ) );
		}
		return $result;
	}
	public static function robots( string $output ): string {
		if ( get_option( Settings::STATE ) && Settings::get()['robots'] ) {
			return "User-agent: *\nDisallow: /\n"; }
		return $output;
	}
	public static function badge(): void {
		$s = Settings::get();
		if ( ! get_option( Settings::STATE ) || ! $s['badge'] || ! is_user_logged_in() || ! Access::allowed( $s ) ) {
			return; }
		$position = explode( '-', $s['badge_position'] );
		$css      = 'position:fixed;z-index:99999;' . $position[0] . ':' . ( 'top' === $position[0] ? '52' : '12' ) . 'px;' . $position[1] . ':12px;padding:8px 12px;border-radius:8px;max-width:calc(100vw - 48px);pointer-events:none;background:' . sanitize_hex_color( $s['badge_color'] ) . ';color:' . sanitize_hex_color( $s['badge_text'] ) . ';font: ' . absint( $s['badge_size'] ) . 'px/1.4 system-ui,sans-serif';
		echo '<span class="mgms-badge" style="' . esc_attr( $css ) . '">Wartungsmodus aktiv</span>';
	}
}
