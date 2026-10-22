<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
final class Admin {
	public static function menu(): void {
		add_options_page( 'MG Maintenance Studio', 'Maintenance Studio', 'manage_options', 'mg-maintenance-studio', array( self::class, 'page' ) ); }
	public static function assets( string $hook ): void {
		if ( 'settings_page_mg-maintenance-studio' !== $hook ) {
			return; }
		wp_enqueue_media();
		wp_enqueue_style( 'mgms-admin', MGMS_URL . 'assets/admin.css', array(), MGMS_VERSION );
		wp_enqueue_script( 'mgms-admin', MGMS_URL . 'assets/admin.js', array(), MGMS_VERSION, true );
		wp_localize_script( 'mgms-admin', 'mgmsPresets', self::presets() );
	}
	public static function presets(): array {
		$out = array();
		foreach ( array( 'elegant-gold', 'ruhiges-atelier', 'minimalistisch-hell' ) as $name ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Ausschließlich fest benannte lokale Preset-Dateien.
			$out[ $name ] = json_decode( file_get_contents( MGMS_DIR . 'presets/' . $name . '.json' ), true ); }
		return $out;
	}
	private static function guard(): void {
		if ( 'POST' !== sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.', '', array( 'response' => 403 ) ); }
	}
	private static function redirect( string $notice ): void {
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'        => 'mg-maintenance-studio',
					'mgms_notice' => $notice,
				),
				admin_url( 'options-general.php' )
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
		if ( empty( $_POST['confirm_state'] ) ) {
			wp_die(
				'Bitte Auswirkung bestätigen.',
				'',
				array(
					'response'  => 400,
					'back_link' => true,
				)
			); }
		$action = sanitize_key( wp_unslash( $_POST['intent'] ?? '' ) );
		if ( 'enable' === $action || 'disable' === $action ) {
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
		require MGMS_DIR . 'templates/admin.php';
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
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute werden oben einzeln mit esc_attr aufgebaut.
			echo '<input type="' . esc_attr( $type ) . '"' . $attrs . ' value="' . esc_attr( $value ) . '"';
			if ( 'number' === $type ) {
				echo ' min="' . (int) $field[4][0] . '" max="' . (int) $field[4][1] . '"'; }
			echo '>';
			if ( in_array( $field[1], array( 'image', 'font' ), true ) ) {
				echo '<button type="button" class="button media-picker" data-target="mgms-' . esc_attr( $key ) . '" data-kind="' . esc_attr( $field[1] ) . '">Mediathek öffnen</button>'; }
		}
		echo '</div>';
	}
}
