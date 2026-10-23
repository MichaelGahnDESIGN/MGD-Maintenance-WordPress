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
		add_option( Error_Pages::DRAFT, Error_Pages::defaults(), '', false );
		add_option( Error_Pages::LIVE, Error_Pages::defaults(), '', false );
	}
	public static function deactivate(): void {
		// Bewussten Wartungsstatus behalten. Reaktivierung setzt die bisherige Entscheidung fort.
		delete_metadata( 'user', 0, 'mgms_preview', '', true );
	}
	public static function boot(): void {
		Presentation::register();
		add_action( 'admin_menu', array( Admin::class, 'menu' ) );
		add_filter( 'custom_menu_order', '__return_true', PHP_INT_MAX );
		add_filter( 'menu_order', array( Admin::class, 'menu_last' ), PHP_INT_MAX );
		add_action( 'admin_enqueue_scripts', array( Admin::class, 'assets' ) );
		add_action( 'admin_post_mgms_save', array( Admin::class, 'save' ) );
		add_action( 'admin_post_mgms_state', array( Admin::class, 'state' ) );
		add_action( 'admin_post_mgms_preview', array( Preview::class, 'render' ) );
		add_action( 'admin_post_mgms_error_save', array( Error_Pages_Admin::class, 'save' ) );
		add_action( 'admin_post_mgms_error_preview', array( Error_Pages_Admin::class, 'preview' ) );
		add_action( 'admin_notices', array( Admin::class, 'conflicts' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'badge_assets' ) );
		add_action( 'init', array( self::class, 'cache' ), 1 );
		add_action( 'template_redirect', array( self::class, 'frontend' ), 0 );
		add_filter( 'template_include', array( Error_Pages::class, 'template' ), 99 );
		add_filter( 'wp_die_handler', array( Error_Pages::class, 'die_handler' ) );
		add_filter( 'robots_txt', array( self::class, 'robots' ), 99, 1 );
		add_filter( 'rest_pre_dispatch', array( self::class, 'rest' ), 99, 1 );
		add_action( 'wp_footer', array( self::class, 'badge' ) );
		add_action( 'wp_ajax_mgms_frontend_state', array( self::class, 'frontend_state' ) );
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
	/** Lädt die kleine Steuerung nur für berechtigte Administratorkonten. */
	public static function badge_assets(): void {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) || ! Settings::get( true )['badge'] ) {
			return;
		}
		wp_enqueue_style( 'mgms-status-control', MGMS_URL . 'assets/status-control.css', array(), MGMS_VERSION );
		wp_enqueue_script( 'mgms-status-control', MGMS_URL . 'assets/status-control.js', array(), MGMS_VERSION, true );
		wp_localize_script(
			'mgms-status-control',
			'mgmsStatusControl',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mgms_frontend_state' ),
				'error'   => 'Der Wartungsmodus konnte nicht geändert werden. Bitte erneut versuchen.',
			)
		);
	}
	/** Ändert den Status aus der geschützten Frontend-Steuerung. */
	public static function frontend_state(): void {
		check_ajax_referer( 'mgms_frontend_state', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Keine Berechtigung.' ), 403 );
		}
		$enabled = '1' === sanitize_text_field( wp_unslash( $_POST['enabled'] ?? '' ) );
		update_option( Settings::STATE, $enabled, false );
		wp_send_json_success(
			array(
				'enabled' => $enabled,
				'label'   => $enabled ? 'AN' : 'AUS',
				'message' => $enabled ? 'Wartungsmodus ist jetzt AN.' : 'Wartungsmodus ist jetzt AUS.',
			)
		);
	}
	public static function badge(): void {
		$s = Settings::get();
		if ( ! $s['badge'] || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) || ( get_option( Settings::STATE ) && ! Access::allowed( $s ) ) ) {
			return; }
		$position = explode( '-', $s['badge_position'] );
		$enabled  = (bool) get_option( Settings::STATE );
		$css      = 'position:fixed;z-index:99999;' . $position[0] . ':' . ( 'top' === $position[0] ? '52' : '12' ) . 'px;' . $position[1] . ':12px;--mgms-control-background:' . sanitize_hex_color( $s['badge_color'] ) . ';--mgms-control-text:' . sanitize_hex_color( $s['badge_text'] ) . ';font-size:' . absint( $s['badge_size'] ) . 'px';
		?>
		<form class="mgms-status-control" style="<?php echo esc_attr( $css ); ?>" data-mgms-status-control>
			<label for="mgms-frontend-enabled"><span>Wartungsmodus</span><strong data-state-label><?php echo $enabled ? 'AN' : 'AUS'; ?></strong><input id="mgms-frontend-enabled" type="checkbox" <?php checked( $enabled ); ?>><span class="mgms-status-switch" aria-hidden="true"></span></label>
			<span class="screen-reader-text" data-status-message aria-live="polite"></span>
		</form>
		<?php
	}
}
