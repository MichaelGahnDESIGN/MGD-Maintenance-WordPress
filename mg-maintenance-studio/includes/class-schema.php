<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/** Felddefinitionen werden für Editor und serverseitige Prüfung gemeinsam verwendet. */
final class Schema {
	public static function fields(): array {
		return array(
			'title'            => array( 'Wartungstitel', 'text', 'Wir sind bald wieder da.', 'Wartungsmodus' ),
			'description'      => array( 'Beschreibung', 'textarea', 'Unsere Website wird gerade überarbeitet. Bitte schauen Sie etwas später noch einmal vorbei.', 'Wartungsmodus' ),
			'notice'           => array( 'Rückkehr- oder Fortschrittshinweis', 'text', 'Vielen Dank für Ihre Geduld.', 'Wartungsmodus' ),
			'newsletter'       => array( 'Newsletter- oder Rückrufhinweis (nur Text)', 'textarea', '', 'Wartungsmodus' ),
			'countdown'        => array( 'Rückkehrzeit mit Zeitzone (ISO 8601)', 'datetime', '', 'Wartungsmodus' ),
			'status'           => array(
				'HTTP-Status',
				'select',
				'503',
				'SEO und Crawler',
				array(
					'503' => '503 – vorübergehend nicht erreichbar',
					'200' => '200 – Coming Soon',
					'403' => '403 – Zugriff gesperrt',
				),
			),
			'retry'            => array( 'Retry-After in Sekunden', 'number', 3600, 'SEO und Crawler', array( 60, 86400 ) ),
			'robots'           => array( 'Virtuelle robots.txt um Disallow ergänzen', 'checkbox', false, 'SEO und Crawler' ),
			'brand'            => array( 'Markenname', 'text', 'Ihre Website', 'Design' ),
			'brand_presentation' => array(
				'Markenbereich',
				'select',
				'text',
				'Design',
				array(
					'text'          => 'Nur Text',
					'logo'          => 'Nur Logo',
					'logo-and-text' => 'Logo und Text',
				),
			),
			'layout'           => array(
				'Seitenlayout',
				'select',
				'editorial',
				'Design',
				array(
					'editorial' => 'Editorial – großzügig und linksbündig',
					'centered'  => 'Zentriert – ruhig und fokussiert',
					'split'     => 'Geteilt – Text und Informationen nebeneinander',
					'poster'    => 'Poster – prägnante Botschaft',
				),
			),
			'tagline'          => array( 'Untertitel', 'text', 'Wartungsarbeiten', 'Design' ),
			'logo'             => array( 'Logo aus der Mediathek', 'image', 0, 'Design' ),
			'logo_alt'         => array( 'Alternativtext des Logos', 'text', '', 'Design' ),
			'logo_width'       => array( 'Logo-Breite in Pixeln', 'number', 120, 'Design', array( 32, 400 ) ),
			'logo_link'        => array( 'Logo mit Startseite verlinken', 'checkbox', false, 'Design' ),
			'align'            => array(
				'Ausrichtung',
				'select',
				'left',
				'Design',
				array(
					'left'   => 'Links',
					'center' => 'Zentriert',
					'right'  => 'Rechts',
				),
			),
			'background_image' => array( 'Lokales Hintergrundbild', 'image', 0, 'Design' ),
			// Das Gimmick bleibt bewusst ein lokales Mediathek-Bild. So entstehen weder Tracking
			// noch unerwartete Fremd-Inhalte auf einer Wartungsseite.
			'gimmick_image'    => array( 'Optionales Gimmick-Bild aus der Mediathek', 'image', 0, 'Design' ),
			'gimmick_position' => array(
				'Position des Gimmicks',
				'select',
				'bottom-right',
				'Design',
				array(
					'top-right'    => 'Oben rechts',
					'bottom-right' => 'Unten rechts',
					'bottom-left'  => 'Unten links',
				),
			),
			'gimmick_motion'   => array(
				'Bewegung des Gimmicks',
				'select',
				'float',
				'Design',
				array(
					'none'   => 'Keine Bewegung',
					'float'  => 'Sanft schweben',
					'wobble' => 'Sanft wippen',
				),
			),
			'background'       => array( 'Hintergrund', 'color', '#f7f5ef', 'Design' ),
			'text'             => array( 'Textfarbe', 'color', '#282925', 'Design' ),
			'accent'           => array( 'Akzentfarbe', 'color', '#795c25', 'Design' ),
			'button'           => array( 'Button-Hintergrund', 'color', '#795c25', 'Design' ),
			'button_text'      => array( 'Button-Text', 'color', '#ffffff', 'Design' ),
			'card'             => array( 'Kartenfarbe', 'color', '#ffffff', 'Design' ),
			'border_color'     => array( 'Rahmenfarbe', 'color', '#ccc8be', 'Design' ),
			'font'             => array(
				'Systemschrift',
				'select',
				'serif',
				'Design',
				array(
					'serif'      => 'Elegante Serifenschrift',
					'sans-serif' => 'Klare Systemschrift',
				),
			),
			'font_file'        => array( 'Lokale WOFF2-Schrift aus der Mediathek (ID)', 'font', 0, 'Design' ),
			'container'        => array(
				'Containerstil',
				'select',
				'plain',
				'Design',
				array(
					'card'  => 'Karte',
					'plain' => 'Ohne Karte',
				),
			),
			'width'            => array( 'Maximale Inhaltsbreite', 'number', 1040, 'Design', array( 320, 1440 ) ),
			'padding'          => array( 'Abstand Desktop', 'number', 48, 'Design', array( 16, 120 ) ),
			'padding_tablet'   => array( 'Abstand Tablet', 'number', 48, 'Design', array( 16, 80 ) ),
			'padding_mobile'   => array( 'Abstand Smartphone', 'number', 24, 'Design', array( 16, 48 ) ),
			'radius'           => array( 'Abgerundete Ecken', 'number', 0, 'Design', array( 0, 80 ) ),
			'border'           => array( 'Rahmenbreite', 'number', 1, 'Design', array( 0, 8 ) ),
			'shadow'           => array( 'Dezenter Schatten', 'checkbox', false, 'Design' ),
			'animation'        => array( 'Sanftes Einblenden', 'checkbox', false, 'Design' ),
			'email'            => array( 'Kontakt-E-Mail', 'email', '', 'Wartungsmodus' ),
			'phone'            => array( 'Telefonnummer', 'phone', '', 'Wartungsmodus' ),
			'contact_label'    => array( 'Beschriftung Kontaktbutton', 'text', 'Kontakt aufnehmen', 'Wartungsmodus' ),
			'contact_url'      => array( 'Kontaktziel (https, mailto oder tel)', 'url', '', 'Wartungsmodus' ),
			'credit'           => array( 'Unterstützerhinweis anzeigen', 'checkbox', true, 'Design' ),
			'credit_url'       => array( 'Website des Unterstützers', 'url', '', 'Design' ),
			'ip_enabled'       => array( 'IP-Freigaben aktivieren', 'checkbox', false, 'IP-Freigaben' ),
			'ip_list'          => array( 'Freigegebene IPs / CIDR-Netze (eine Zeile je Eintrag)', 'cidr', '', 'IP-Freigaben' ),
			'proxies'          => array( 'Vertrauenswürdige Proxy-IP-Adressen (eine je Zeile)', 'proxy', '', 'IP-Freigaben' ),
			'users'            => array(
				'Zugriff eingeloggter Benutzer',
				'select',
				'admins',
				'Eingeloggte Benutzer',
				array(
					'admins' => 'Nur Administratoren',
					'all'    => 'Alle eingeloggten Benutzer',
					'roles'  => 'Ausgewählte Rollen',
					'none'   => 'Alle sehen Wartung',
				),
			),
			'roles'            => array( 'Freigegebene Rollen', 'roles', array(), 'Eingeloggte Benutzer' ),
			'admin_bypass'     => array( 'Administratoren sehen normale Website', 'checkbox', true, 'Eingeloggte Benutzer' ),
			'badge'            => array( 'Wartungshinweis für freigegebene eingeloggte Benutzer', 'checkbox', true, 'Eingeloggte Benutzer' ),
			'badge_position'   => array(
				'Position des Hinweises',
				'select',
				'top-left',
				'Eingeloggte Benutzer',
				array(
					'top-left'     => 'Oben links',
					'top-right'    => 'Oben rechts',
					'bottom-left'  => 'Unten links',
					'bottom-right' => 'Unten rechts',
				),
			),
			'badge_color'      => array( 'Hinweis Hintergrund', 'color', '#1d2727', 'Eingeloggte Benutzer' ),
			'badge_text'       => array( 'Hinweis Textfarbe', 'color', '#ffffff', 'Eingeloggte Benutzer' ),
			'badge_size'       => array( 'Hinweis Schriftgröße', 'number', 13, 'Eingeloggte Benutzer', array( 12, 20 ) ),
			'modal_backdrop'   => array( 'Modal durch Klick auf Hintergrund schließen', 'checkbox', true, 'Rechtstexte' ),
			'preview_enabled'  => array( 'Geschützte Vorschau erlauben', 'checkbox', true, 'Erweitert' ),
			'delete_data'      => array( 'Bei Deinstallation Plugin-Einstellungen löschen', 'checkbox', false, 'Erweitert' ),
		);
	}
	public static function defaults(): array {
		$data = array();
		foreach ( self::fields() as $key => $field ) {
			$data[ $key ] = $field[2]; }
		$data['preset'] = 'elegant-gold';
		$data['legal']  = array();
		return $data;
	}
}
