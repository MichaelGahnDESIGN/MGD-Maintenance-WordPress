<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Updates {
	public static function register(): void {
		add_filter( 'update_plugins_github.com', array( self::class, 'offer' ), 10, 3 );
		add_filter( 'plugins_api', array( self::class, 'information' ), 20, 3 );
		add_action( 'admin_post_mgms_updates', array( self::class, 'save' ) );
	}
	public static function config(): array {
		return array_merge(
			array(
				'repository' => 'MichaelGahnDESIGN/MGD-Maintenance-WordPress',
				'enabled'    => false,
			),
			(array) get_option( 'mgms_updates', array() )
		);
	}
	public static function valid_repository( string $repository ): bool {
		// Nur Michaels öffentliche Releases; keine frei eingebbaren Server oder Tokens.
		return 1 === preg_match( '~^MichaelGahnDESIGN/[A-Za-z0-9][A-Za-z0-9._-]{0,99}$~D', $repository );
	}
	public static function normalize( array $data, string $repository ): array {
		$tag = $data['tag_name'] ?? null;
		if ( ! self::valid_repository( $repository ) || ! is_string( $tag ) || ! empty( $data['draft'] ) || ! empty( $data['prerelease'] ) || ! preg_match( '/^v?((?:0|[1-9][0-9]*)\.(?:0|[1-9][0-9]*)\.(?:0|[1-9][0-9]*))$/D', $tag, $matches ) ) {
			return array();
		}
		$name = 'mg-maintenance-studio-' . $matches[1] . '.zip';
		$url  = 'https://github.com/' . $repository . '/releases/download/' . $tag . '/' . $name;
		foreach ( (array) ( $data['assets'] ?? array() ) as $asset ) {
			// phpcs:ignore WordPress.PHP.YodaConditions.NotYoda -- Beide Vergleichswerte werden aus der geprüften Version aufgebaut.
			if ( is_array( $asset ) && $name === ( $asset['name'] ?? '' ) && $url === ( $asset['browser_download_url'] ?? '' ) ) {
				return array(
					'version' => $matches[1],
					'package' => $url,
				);
			}
		}
		return array();
	}
	public static function latest(): array {
		$config = self::config();
		if ( ! $config['enabled'] || ! self::valid_repository( $config['repository'] ) ) {
			return array();
		}
		$cached = get_transient( 'mgms_release' );
		if ( is_array( $cached ) && ( $cached['repository'] ?? '' ) === $config['repository'] ) {
			return $cached['release'];
		}
		$response = wp_safe_remote_get(
			'https://api.github.com/repos/' . $config['repository'] . '/releases/latest',
			array(
				'timeout'             => 8,
				'redirection'         => 0,
				'limit_response_size' => 262144,
				'headers'             => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'MG-Maintenance-Studio/' . MGMS_VERSION,
				),
			)
		);
		$release  = array();
		$message  = 'GitHub konnte nicht geprüft werden. Bitte später erneut versuchen.';
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$data    = json_decode( wp_remote_retrieve_body( $response ), true );
			$release = is_array( $data ) ? self::normalize( $data, $config['repository'] ) : array();
			$message = $release ? ( version_compare( $release['version'], MGMS_VERSION, '>' ) ? 'Eine neue Version ist verfügbar.' : 'Kein neueres Release verfügbar.' ) : 'Kein passendes stabiles Release-Paket gefunden.';
		} elseif ( ! is_wp_error( $response ) && 404 === wp_remote_retrieve_response_code( $response ) ) {
			$message = 'Repository oder öffentliches Release noch nicht verfügbar.';
		}
		// Auch Fehler kurz zwischenspeichern, damit ein Ausfall keine Abfrageflut auslöst.
		set_transient(
			'mgms_release',
			array(
				'repository' => $config['repository'],
				'release'    => $release,
				'message'    => $message,
				'checked'    => time(),
			),
			$release ? 12 * HOUR_IN_SECONDS : 15 * MINUTE_IN_SECONDS
		);
		return $release;
	}
	public static function offer( mixed $update, array $plugin_data, string $plugin_file ): mixed {
		if ( plugin_basename( MGMS_FILE ) !== $plugin_file ) {
			return $update;
		}
		$release = self::latest();
		if ( ! $release ) {
			return false;
		}
		return array_merge(
			$release,
			array(
				'slug'         => 'mg-maintenance-studio',
				'url'          => 'https://github.com/' . self::config()['repository'],
				'requires_php' => '8.1',
				'requires'     => '6.5',
				'icons'        => array(
					'svg' => MGMS_URL . 'assets/icon.svg',
					'1x'  => MGMS_URL . 'assets/icon-128.png',
					'2x'  => MGMS_URL . 'assets/icon-256.png',
				),
			)
		);
	}
	public static function information( mixed $result, string $action, mixed $args ): mixed {
		if ( 'plugin_information' !== $action || 'mg-maintenance-studio' !== ( $args->slug ?? '' ) ) {
			return $result;
		}
		$release = self::latest();
		return (object) array(
			'name'          => 'MG Maintenance Studio',
			'slug'          => 'mg-maintenance-studio',
			'version'       => $release['version'] ?? MGMS_VERSION,
			'author'        => 'Michael Gahn DESIGN',
			'requires'      => '6.5',
			'requires_php'  => '8.1',
			'download_link' => $release['package'] ?? '',
			'icons'         => array(
				'svg' => MGMS_URL . 'assets/icon.svg',
				'1x'  => MGMS_URL . 'assets/icon-128.png',
				'2x'  => MGMS_URL . 'assets/icon-256.png',
			),
			'sections'      => array(
				'description'  => '<p>Wartungsseiten gestalten, geschützt prüfen und bewusst veröffentlichen. Mit Rechtstexten, Rollen- und IP-Freigaben.</p>',
				'installation' => '<p>ZIP unter Plugins → Installieren hochladen. Einstellungen → Maintenance Studio öffnen. Aktivierung schaltet den Wartungsmodus nicht automatisch ein.</p>',
				'changelog'    => '<h4>0.3.0</h4><p>GitHub-Updates, eigenes Icon, Plugin-Details und überarbeitete Verwaltung.</p>',
			),
		);
	}
	public static function save(): void {
		if ( ! current_user_can( 'update_plugins' ) || ! current_user_can( 'manage_options' ) || 'POST' !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'mgms_updates' );
		$repository = sanitize_text_field( wp_unslash( $_POST['repository'] ?? '' ) );
		$enabled    = ! empty( $_POST['enabled'] );
		if ( ( $repository || $enabled ) && ! self::valid_repository( $repository ) ) {
			wp_die(
				'Bitte ein Repository im Format MichaelGahnDESIGN/Repository eintragen.',
				'',
				array(
					'response'  => 400,
					'back_link' => true,
				)
			);
		}
		update_option(
			'mgms_updates',
			array(
				'repository' => $repository,
				'enabled'    => $enabled,
			),
			false
		);
		delete_transient( 'mgms_release' );
		delete_site_transient( 'update_plugins' );
		if ( $enabled ) {
			self::latest();
			wp_update_plugins();
		}
		wp_safe_redirect( admin_url( 'admin.php?page=mgms-updates#panel-9' ) );
		exit;
	}
}
