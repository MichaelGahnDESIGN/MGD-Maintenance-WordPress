<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Admin {
	// Einstellungsseite hängt unter MGD Tools (Baukasten), Seiten-Kennung bleibt mgms-dashboard
	public const PAGE = 'mgms-dashboard';
	private const VIEWS = array(
		'overview' => 'Übersicht',
		'editor'   => 'Wartungsseite',
		'design'   => 'Layouts & Design',
		'access'   => 'Zugriffe',
		'errors'   => 'Fehlerseiten',
	);
	// alte Seiten-Kennungen > Ansicht, alte Links leiten um
	private const LEGACY = array(
		'mg-maintenance-studio' => 'editor',
		'mgms-dashboard'        => 'overview',
		'mgms-editor'           => 'editor',
		'mgms-design'           => 'design',
		'mgms-access'           => 'access',
		'mgms-error-pages'      => 'errors',
	);
	public static function url( string $view = 'overview', array $args = array() ): string {
		$args = array_merge( array( 'page' => self::PAGE ), 'overview' === $view ? array() : array( 'view' => $view ), $args );
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}
	public static function view(): string {
		$view = sanitize_key( wp_unslash( $_GET['view'] ?? 'overview' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur Anzeige
		return isset( self::VIEWS[ $view ] ) ? $view : 'overview';
	}
	// alte Menü-Adressen auf die neue Seite umleiten
	public static function legacy_redirect(): void {
		$page = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur Weiterleitung
		if ( '' === $page || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$args = array_filter( array_map( 'sanitize_text_field', array_map( 'wp_unslash', array_filter( $_GET, 'is_string' ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $args['page'], $args['view'] );
		if ( 'mgms-updates' === $page ) {
			wp_safe_redirect( \Mgd\Maintenance\Kit\Platform\WordPress\Tools::url() );
			exit;
		}
		if ( self::PAGE === $page || ! isset( self::LEGACY[ $page ] ) ) {
			return;
		}
		wp_safe_redirect( self::url( self::LEGACY[ $page ], $args ) );
		exit;
	}
	// Reiter für die fünf Bereiche, darunter die jeweilige Ansicht
	public static function tools_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) );
		}
		$view = self::view();
		echo '<div class="wrap mgms-tools-tabs"><nav class="nav-tab-wrapper" aria-label="Bereiche von Maintenance">';
		foreach ( self::VIEWS as $key => $label ) {
			echo '<a class="nav-tab' . ( $key === $view ? ' nav-tab-active' : '' ) . '" href="' . esc_url( self::url( $key ) ) . '"' . ( $key === $view ? ' aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a>';
		}
		echo '</nav></div>';
		if ( 'overview' === $view ) {
			Dashboard::page();
		} elseif ( 'errors' === $view ) {
			Error_Pages_Admin::page();
		} else {
			self::page();
		}
	}
	private static function is_tools_hook( string $hook ): bool {
		return str_ends_with( $hook, '_page_' . self::PAGE );
	}
	public static function assets( string $hook ): void {
		if ( ! self::is_tools_hook( $hook ) ) {
			return; }
		wp_enqueue_style( 'mgms-admin', MGMS_URL . 'assets/admin.css', array(), MGMS_VERSION );
		wp_enqueue_script( 'mgms-admin', MGMS_URL . 'assets/admin.js', array(), MGMS_VERSION, true );
		if ( ! in_array( self::view(), array( 'editor', 'design', 'access' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_localize_script( 'mgms-admin', 'mgmsPresets', self::presets() );
		// Mediathek nur mit Formaten, die Speichern auch annimmt
		wp_localize_script( 'mgms-admin', 'mgmsMedia', array( 'image' => Settings::image_mimes() ) );
	}
	public static function presets(): array {
		$out = array();
		foreach ( array( 'elegant-gold', 'ruhiges-atelier', 'minimalistisch-hell', 'nachtstudio', 'verspielt' ) as $name ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Ausschließlich fest benannte lokale Preset-Dateien.
			$out[ $name ] = json_decode( file_get_contents( MGMS_DIR . 'presets/' . $name . '.json' ), true ); }
		// Pro-Szenen aus dem Paket dazu
		foreach ( Pro_Store::scenes() as $key => $scene ) {
			$out[ 'pro-' . $key ] = array(
				'name'   => $scene['name'],
				'pro'    => true,
				'values' => $scene['values'],
			);
		}
		return $out;
	}
	private static function guard(): void {
		if ( 'POST' !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) ); }
	}
	private static function redirect( string $notice ): void {
		$return_page = sanitize_key( wp_unslash( $_POST['return_page'] ?? 'mg-maintenance-studio' ) );
		wp_safe_redirect( self::url( self::LEGACY[ $return_page ] ?? 'editor', array( 'mgms_notice' => $notice ) ) );
		exit;
	}
	public static function save(): void {
		self::guard();
		check_admin_referer( 'mgms_save' );
		// Veraltete Tabs dürfen neuere Entwürfe nicht überschreiben.
		if ( ! hash_equals( self::revision(), sanitize_text_field( wp_unslash( $_POST['revision'] ?? '' ) ) ) ) {
			wp_die( 'Der Entwurf wurde inzwischen geändert. Bitte Editor neu laden; Eingaben vorher kopieren.', '', array( 'response' => 409 ) ); }
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Settings prüft jedes Feld; Fehler verhindern den gesamten Schreibvorgang.
		$s = Settings::sanitize( (array) wp_unslash( $_POST['settings'] ?? array() ) );
		if ( is_wp_error( $s ) ) {
			wp_die(
				esc_html( implode( ' ', $s->get_error_messages() ) ) . ' Bitte zurückgehen und korrigieren.',
				'',
				array(
					'response'  => 400,
					'back_link' => true,
				)
			); }
		$publish = 'publish' === sanitize_key( wp_unslash( $_POST['intent'] ?? '' ) );
		if ( $publish && empty( $_POST['confirm_publish'] ) ) {
			wp_die(
				'Veröffentlichung bitte ausdrücklich bestätigen.',
				'',
				array(
					'response'  => 400,
					'back_link' => true,
				)
			); }
		$old = Settings::get( true );
		update_option( 'mgms_previous_draft', $old, false );
		update_option( Settings::DRAFT, $s, false );
		if ( $publish ) {
			update_option( 'mgms_previous_published', Settings::get(), false );
			update_option( Settings::LIVE, $s, false );
		}
		if ( ! $s['preview_enabled'] ) {
			delete_metadata( 'user', 0, 'mgms_preview', '', true ); }
		self::redirect( $publish ? 'published' : 'saved' );
	}
	public static function state(): void {
		self::guard();
		check_admin_referer( 'mgms_state' );
		$action = sanitize_key( wp_unslash( $_POST['intent'] ?? '' ) );
		// Der Kopf-Schalter ist selbst die ausdrückliche Handlung; die anderen Aktionen behalten ihre Bestätigung.
		if ( 'set_state' !== $action && empty( $_POST['confirm_state'] ) ) {
			wp_die(
				'Bitte Auswirkung bestätigen.',
				'',
				array(
					'response'  => 400,
					'back_link' => true,
				)
			); }
		if ( 'set_state' === $action ) {
			update_option( Settings::STATE, ! empty( $_POST['enabled'] ), false );
		} elseif ( 'enable' === $action || 'disable' === $action ) {
			update_option( Settings::STATE, 'enable' === $action, false );
		} elseif ( 'restore_draft' === $action ) {
			$previous = get_option( 'mgms_previous_draft' );
			if ( is_array( $previous ) ) {
				update_option( Settings::DRAFT, $previous, false ); }
		} elseif ( 'restore_published' === $action ) {
			$previous = get_option( 'mgms_previous_published' );
			if ( is_array( $previous ) ) {
				update_option( Settings::LIVE, $previous, false ); }
		} elseif ( 'prune' === $action ) {
			delete_option( 'mgms_previous_draft' );
			delete_option( 'mgms_previous_published' );
			delete_metadata( 'user', 0, 'mgms_preview', '', true );
		} else {
			wp_die( 'Unbekannte Aktion.', '', array( 'response' => 400 ) ); }
		self::redirect( 'changed' );
	}
	public static function revision(): string {
		return hash( 'sha256', wp_json_encode( Settings::get( true ) ) ); }
	public static function conflicts(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return; }
		$active = array_merge( (array) get_option( 'active_plugins', array() ), array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		foreach ( $active as $plugin ) {
			if ( ! str_starts_with( $plugin, 'mg-maintenance-studio/' ) && preg_match( '/maintenance|coming-soon|seedprod|under-construction/i', $plugin ) ) {
				echo '<div class="notice notice-warning"><p>MGD Maintenance Studio: Ein weiteres Wartungsplugin ist aktiv. Gegenseitige Ausgabe- und Cache-Konflikte bitte vor Veröffentlichung prüfen.</p></div>';
				break;
			}
		}
	}
	public static function page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return; }
		$s       = Settings::get( true );
		$live    = Settings::get();
		$enabled = (bool) get_option( Settings::STATE );
		$tabs    = array( 'Übersicht', 'Wartungsmodus', 'Design', 'Rechtstexte', 'IP-Freigaben', 'Eingeloggte Benutzer', 'SEO und Crawler', 'Erweitert', 'Hilfe' );
		$notices = array(
			'saved'     => 'Entwurf gespeichert. Der veröffentlichte Stand bleibt unverändert.',
			'published' => 'Einstellungen ausdrücklich veröffentlicht. Der Wartungsstatus wurde nicht verändert.',
			'changed'   => 'Bestätigte Aktion ausgeführt.',
		);
		$view          = self::view();
		$page_slug     = array_search( $view, self::LEGACY, true ) ?: 'mgms-editor';
		$initial_panel = array( 'editor' => 1, 'design' => 2, 'access' => 4 )[ $view ] ?? 0;
		require MGMS_DIR . 'templates/admin.php';
	}
	/** Einheitlicher, bewusst sichtbarer Statusschalter für alle Pluginseiten. */
	public static function status_switch( string $return_page, bool $enabled ): void {
		?>
		<form class="mgms-header-switch" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'mgms_state' ); ?>
			<input type="hidden" name="action" value="mgms_state"><input type="hidden" name="return_page" value="<?php echo esc_attr( $return_page ); ?>"><input type="hidden" name="intent" value="set_state">
			<label for="mgms-header-enabled"><span>Wartungsmodus</span><strong data-state-label><?php echo $enabled ? 'AN' : 'AUS'; ?></strong><input id="mgms-header-enabled" type="checkbox" name="enabled" value="1" <?php checked( $enabled ); ?>><span class="mgms-switch-ui" aria-hidden="true"></span></label><button class="screen-reader-text" type="submit">Wartungsmodus speichern</button>
		</form>
		<?php
	}
	public static function field( string $key, array $field, array $s ): void {
		$name  = 'settings[' . $key . ']';
		$value = $s[ $key ];
		echo '<div class="field"><label for="mgms-' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label>';
		$attrs = ' id="mgms-' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" data-key="' . esc_attr( $key ) . '"';
		if ( 'checkbox' === $field[1] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
			echo '<input type="checkbox"' . $attrs . ' value="1" ' . checked( $value, true, false ) . '>';
		} elseif ( in_array( $field[1], array( 'textarea', 'cidr', 'proxy' ), true ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
			echo '<textarea rows="4"' . $attrs . '>' . esc_textarea( $value ) . '</textarea>';
		} elseif ( 'select' === $field[1] || 'roles' === $field[1] ) {
			$options = 'roles' === $field[1] ? array_map( static fn( $role ) => $role['name'], wp_roles()->roles ) : $field[4];
			if ( 'roles' === $field[1] ) {
				$attrs = str_replace( 'name="' . esc_attr( $name ) . '"', 'name="' . esc_attr( $name ) . '[]" multiple', $attrs ); }
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
			echo '<select' . $attrs . '>';
			foreach ( $options as $id => $label ) {
				echo '<option value="' . esc_attr( $id ) . '" ' . selected( in_array( (string) $id, array_map( 'strval', (array) $value ), true ), true, false ) . '>' . esc_html( $label ) . '</option>'; }
			echo '</select>';
		} else {
			$type = in_array( $field[1], array( 'number', 'email' ), true ) ? $field[1] : 'text';
			// Farbwähler vom Baukasten
			if ( 'color' === $field[1] ) {
				$attrs .= ' data-mgd-color';
			}
			if ( 'image' === $field[1] ) {
				$type = 'hidden';
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
			echo '<input type="' . esc_attr( $type ) . '"' . $attrs . ' value="' . esc_attr( $value ) . '"';
			if ( 'number' === $type ) {
				echo ' min="' . (int) $field[4][0] . '" max="' . (int) $field[4][1] . '"'; }
			echo '>';
			if ( in_array( $field[1], array( 'image', 'font' ), true ) ) {
				echo '<button type="button" class="button media-picker" data-target="mgms-' . esc_attr( $key ) . '" data-kind="' . esc_attr( $field[1] ) . '">Mediathek öffnen</button>'; }
			if ( 'image' === $field[1] ) {
				$image = Settings::media_url( absint( $value ) );
				echo '<div class="mgms-image-selection" id="mgms-' . esc_attr( $key ) . '-preview">';
				if ( $image ) {
					echo '<img src="' . esc_url( $image ) . '" alt="Ausgewählte Mediendatei"><span class="mgms-image-selection__label">Bild aus der Mediathek ausgewählt</span>';
				} else {
					echo '<span>Kein Bild gewählt</span>';
				}
				echo '</div><button type="button" class="button mgms-media-clear" data-target="mgms-' . esc_attr( $key ) . '">Bild entfernen</button>';
			}
		}
		echo '</div>';
	}
}
