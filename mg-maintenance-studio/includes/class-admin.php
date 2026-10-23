<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Admin {
	public static function menu_last( $order ) {
		if ( ! is_array( $order ) || ! in_array( 'mgms-dashboard', $order, true ) ) {
			return $order;
		}
		$order = array_values( array_diff( $order, array( 'mgms-dashboard' ) ) );
		$order[] = 'mgms-dashboard';
		return $order;
	}
	private static array $hooks = array();
	private const EDITOR_PAGES = array( 'mg-maintenance-studio', 'mgms-dashboard', 'mgms-editor', 'mgms-design', 'mgms-access', 'mgms-error-pages', 'mgms-updates' );
	public static function menu(): void {
		// Die alte Einstellungs-URL bleibt für gespeicherte Links gültig.
		self::$hooks['editor'] = array_filter( array(
			add_options_page( 'MG Maintenance Studio', 'Maintenance Studio', 'manage_options', 'mg-maintenance-studio', array( self::class, 'page' ) ),
		) );
		remove_submenu_page( 'options-general.php', 'mg-maintenance-studio' );
		// Die endgültige Reihenfolge wird nach allen registrierten Menüs gesetzt.
		self::$hooks['dashboard'] = add_menu_page( 'MG Maintenance Studio', 'Maintenance Studio', 'manage_options', 'mgms-dashboard', array( Dashboard::class, 'page' ), MGMS_URL . 'assets/icon.svg', 99 );
		add_submenu_page( 'mgms-dashboard', 'Übersicht', 'Übersicht', 'manage_options', 'mgms-dashboard', array( Dashboard::class, 'page' ) );
		foreach ( array( 'mgms-editor' => 'Wartungsseite', 'mgms-design' => 'Layouts & Design', 'mgms-access' => 'Zugriffe' ) as $slug => $label ) {
			self::$hooks['editor'][] = add_submenu_page( 'mgms-dashboard', $label, $label, 'manage_options', $slug, array( self::class, 'page' ) );
		}
		self::$hooks['errors'] = add_submenu_page( 'mgms-dashboard', 'Fehlerseiten', 'Fehlerseiten', 'manage_options', 'mgms-error-pages', array( Error_Pages_Admin::class, 'page' ) );
		self::$hooks['editor'][] = add_submenu_page( 'mgms-dashboard', 'Updates', 'Updates', 'manage_options', 'mgms-updates', array( self::class, 'page' ) );
	}
	public static function assets( string $hook ): void {
		if ( ! in_array( $hook, array_merge( self::$hooks['editor'] ?? array(), array( self::$hooks['dashboard'] ?? '', self::$hooks['errors'] ?? '' ) ), true ) ) {
			return; }
		wp_enqueue_style( 'mgms-admin', MGMS_URL . 'assets/admin.css', array(), MGMS_VERSION );
		wp_enqueue_script( 'mgms-admin', MGMS_URL . 'assets/admin.js', array(), MGMS_VERSION, true );
		if ( ! in_array( $hook, self::$hooks['editor'] ?? array(), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_localize_script( 'mgms-admin', 'mgmsPresets', self::presets() );
	}
	public static function presets(): array {
		$out = array();
		foreach ( array( 'elegant-gold', 'ruhiges-atelier', 'minimalistisch-hell', 'nachtstudio' ) as $name ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Ausschließlich fest benannte lokale Preset-Dateien.
			$out[ $name ] = json_decode( file_get_contents( MGMS_DIR . 'presets/' . $name . '.json' ), true ); }
		return $out;
	}
	private static function guard(): void {
		if ( 'POST' !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) ); }
	}
	private static function redirect( string $notice ): void {
		$return_page = sanitize_key( wp_unslash( $_POST['return_page'] ?? 'mg-maintenance-studio' ) );
		if ( ! in_array( $return_page, self::EDITOR_PAGES, true ) ) {
			$return_page = 'mg-maintenance-studio';
		}
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'        => $return_page,
					'mgms_notice' => $notice,
				),
				admin_url( 'mg-maintenance-studio' === $return_page ? 'options-general.php' : 'admin.php' )
			)
		);
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
				echo '<div class="notice notice-warning"><p>MG Maintenance Studio: Ein weiteres Wartungsplugin ist aktiv. Gegenseitige Ausgabe- und Cache-Konflikte bitte vor Veröffentlichung prüfen.</p></div>';
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
		$tabs    = array( 'Übersicht', 'Wartungsmodus', 'Design', 'Rechtstexte', 'IP-Freigaben', 'Eingeloggte Benutzer', 'SEO und Crawler', 'Erweitert', 'Hilfe', 'Updates' );
		$notices = array(
			'saved'     => 'Entwurf gespeichert. Der veröffentlichte Stand bleibt unverändert.',
			'published' => 'Einstellungen ausdrücklich veröffentlicht. Der Wartungsstatus wurde nicht verändert.',
			'changed'   => 'Bestätigte Aktion ausgeführt.',
		);
		$page_slug = sanitize_key( wp_unslash( $_GET['page'] ?? 'mg-maintenance-studio' ) );
		$initial_panel = array( 'mgms-editor' => 1, 'mgms-design' => 2, 'mgms-access' => 4, 'mgms-updates' => 9 )[ $page_slug ] ?? 0;
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
			$type = in_array( $field[1], array( 'color', 'number', 'email' ), true ) ? $field[1] : 'text';
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
