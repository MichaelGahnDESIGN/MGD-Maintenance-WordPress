<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
?>
<div class="wrap mgms-admin">
<header class="studio-header"><div class="studio-brand"><img src="<?php echo esc_url( MGMS_URL . 'assets/icon.svg' ); ?>" width="56" height="56" alt=""><div><h1>MG Maintenance Studio</h1><p>Michael Gahn DESIGN <span aria-hidden="true">·</span> Version <?php echo esc_html( MGMS_VERSION ); ?></p></div></div><span class="state-pill"><?php echo $enabled ? 'Wartungsmodus aktiv' : 'Wartungsmodus inaktiv'; ?></span></header>
<p><a class="button" href="<?php echo esc_url( admin_url( 'options-general.php?page=mgms-error-pages' ) ); ?>">404- und 403-Seiten gestalten</a></p>
<?php
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Feste Meldungsauswahl ohne Zustandsänderung.
$notice = sanitize_key( wp_unslash( $_GET['mgms_notice'] ?? '' ) ); if ( isset( $notices[ $notice ] ) ) :
	?>
	<div class="notice notice-success" role="status"><p><?php echo esc_html( $notices[ $notice ] ); ?></p></div><?php endif; ?>
<nav class="studio-tabs" aria-label="Einstellungsbereiche">
<?php
foreach ( $tabs as $i => $panel_label ) :
	?>
	<a href="#panel-<?php echo (int) $i; ?>"><?php echo esc_html( $panel_label ); ?></a><?php endforeach; ?></nav>
<section class="studio-panel" id="panel-0"><h2>Status der Website</h2><p>Öffentliche Website: <strong><?php echo $enabled ? 'Wartungsseite gemäß veröffentlichten Zugriffsregeln' : 'Normale Website'; ?></strong></p>
<div class="overview-grid"><div><small>Veröffentlichte Vorlage</small><strong><?php echo esc_html( Admin::presets()[ $live['preset'] ]['name'] ?? 'Eigene Gestaltung' ); ?></strong></div><div><small>Rechtstexte</small><strong><?php echo count( array_filter( $live['legal'], static fn( $r ) => $r['enabled'] && ( $r['content'] || $r['page'] ) ) ) ? 'Eingerichtet · Inhalt selbst prüfen' : 'Noch nicht eingerichtet'; ?></strong></div><div><small>IP-Freigaben</small><strong><?php echo $live['ip_enabled'] ? 'Aktiv' : 'Inaktiv'; ?></strong></div><div><small>Benutzerzugriff</small><strong><?php echo esc_html( Schema::fields()['users'][4][ $live['users'] ] ); ?> · Admin-Ausnahme <?php echo $live['admin_bypass'] ? 'an' : 'aus'; ?></strong></div></div>
<p class="studio-warning">Seiten-Caches und CDNs können PHP umgehen. Nach jeder Statusänderung Cache gezielt leeren und anonym prüfen. Eine Plugin-Deaktivierung beendet die Ausgabe; der gespeicherte Status bleibt für eine Reaktivierung erhalten.</p>
<?php
if ( '503' !== $live['status'] ) :
	?>
	<p class="studio-warning">Der veröffentlichte HTTP-Status ist nicht 503. Suchmaschinen können den Inhalt anders behandeln.</p><?php endif; ?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'mgms_state' ); ?><input type="hidden" name="action" value="mgms_state"><label><input type="checkbox" name="confirm_state" value="1" required> Ich bestätige: Die öffentliche Ausgabe wird jetzt <?php echo $enabled ? 'auf die normale Website umgestellt' : 'auf den veröffentlichten Wartungsstand umgestellt'; ?>.</label><p><button class="button button-primary" name="intent" value="<?php echo $enabled ? 'disable' : 'enable'; ?>"><?php echo $enabled ? 'Wartungsmodus ausdrücklich deaktivieren' : 'Wartungsmodus ausdrücklich aktivieren'; ?></button></p></form>
</section>
<?php require MGMS_DIR . 'templates/updates.php'; ?>
<div class="editor-layout"><form id="mgms-editor" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mgms_save' ); ?><input type="hidden" name="action" value="mgms_save"><input type="hidden" name="revision" value="<?php echo esc_attr( Admin::revision() ); ?>"><input type="hidden" id="mgms-preset" name="settings[preset]" value="<?php echo esc_attr( $s['preset'] ); ?>">
<?php
foreach ( $tabs as $i => $panel_label ) :
	?>
	<?php
	if ( 0 === $i || 'Updates' === $panel_label ) {
		continue; }
	?>
<section class="studio-panel" id="panel-<?php echo (int) $i; ?>"><h2><?php echo esc_html( $panel_label ); ?></h2>
	<?php
	if ( 'Design' === $panel_label ) :
		?>
		<p>Vorlagen ändern nur Designwerte im Formular. Speichern und Veröffentlichen bleiben eigene Aktionen.</p><div class="preset-list">
		<?php
		foreach ( Admin::presets() as $key => $preset ) :
			?>
		<button type="button" class="preset" data-preset="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $preset['name'] ); ?></button><?php endforeach; ?><button type="button" class="button" id="undo-preset" disabled>Letzte Vorlage rückgängig</button></div><p id="contrast-hint" role="status"></p><?php endif; ?>
	<?php
	if ( 'IP-Freigaben' === $panel_label ) :
		?>
		<p>Erkannte IP dieser Admin-Anfrage: <code><?php echo esc_html( IP::detect( $_SERVER, $live['proxies'] ) ); ?></code></p><p>Freigaben umgehen nur die Wartungsseite, niemals die WordPress-Anmeldung. IPv4-Netze unter /24 und IPv6-Netze unter /64 werden abgelehnt. Proxy-Einträge nur nach Prüfung der eigenen Infrastruktur setzen.</p><?php endif; ?>
	<?php
	if ( 'SEO und Crawler' === $panel_label ) :
		?>
		<p>Meta-Robots und X-Robots-Tag sind immer aktiv. 503 mit Retry-After ist die empfohlene Einstellung für vorübergehende Wartung. robots.txt ist freiwillig beachtet und sperrt keine Bots technisch. Bei Disallow können Crawler das noindex-Signal nicht lesen. Physische robots.txt-Dateien werden nicht verändert.</p><?php endif; ?>
	<?php
	if ( 'Erweitert' === $panel_label ) :
		?>
		<p>Kein Tracking, keine Telemetrie, kein Kontaktformular. Die Schriftdatei muss bereits als lokale WOFF2-Datei in der Mediathek verfügbar sein. Das Plugin erweitert keine Upload-Rechte.</p><?php endif; ?>
	<?php
	foreach ( Schema::fields() as $key => $field ) {
		if ( $field[3] === $panel_label ) {
			Admin::field( $key, $field, $s ); }
	}
	?>
	<?php
	if ( 'Rechtstexte' === $panel_label ) :
		?>
		<p>Nur eigene Betreibertexte eintragen. Dieses Plugin ersetzt keine Rechtsberatung. HTML wird auf sichere Textformatierung und Links begrenzt. Eine verlinkte reguläre Seite bleibt während Wartung öffentlich erreichbar und verwendet ihr Theme.</p>
		<?php
		foreach ( array( 'Impressum', 'Datenschutz', 'Cookie-Hinweise', 'Weitere rechtliche Hinweise', 'Wartungshinweis' ) as $index => $legal_title ) :
			?>
			<?php
			$row    = $s['legal'][ $index ] ?? array(
				'title'   => $legal_title,
				'content' => '',
				'enabled' => false,
				'footer'  => true,
				'modal'   => true,
				'order'   => $index,
				'page'    => 0,
			);
			$prefix = 'settings[legal][' . $index . ']';
			?>
<fieldset class="legal-editor"><legend><?php echo esc_html( $legal_title ); ?></legend>
<label>Titel <input type="text" name="<?php echo esc_attr( $prefix . '[title]' ); ?>" value="<?php echo esc_attr( $row['title'] ); ?>"></label>
<label>Eigener Inhalt (einfaches HTML möglich)<textarea rows="6" name="<?php echo esc_attr( $prefix . '[content]' ); ?>"><?php echo esc_textarea( $row['content'] ); ?></textarea></label>
			<?php
			foreach ( array(
				'enabled' => 'Aktiv',
				'footer'  => 'Im Footer',
				'modal'   => 'Als Modal',
			) as $key => $label ) :
				?>
																			<label><input type="checkbox" name="<?php echo esc_attr( $prefix . '[' . $key . ']' ); ?>" value="1" <?php checked( $row[ $key ] ); ?>><?php echo esc_html( $label ); ?></label><?php endforeach; ?>
<label>Sortierung <input type="number" min="0" max="99" name="<?php echo esc_attr( $prefix . '[order]' ); ?>" value="<?php echo (int) $row['order']; ?>"></label>
<label>ID einer regulären WordPress-Seite (0 = eigener Text) <input type="number" min="0" name="<?php echo esc_attr( $prefix . '[page]' ); ?>" value="<?php echo (int) $row['page']; ?>"></label>
</fieldset><?php endforeach; ?><?php endif; ?>
	<?php
	if ( 'Hilfe' === $panel_label ) :
		?>
		<ol><li>Entwurf gestalten, eigene Rechtstexte eintragen und speichern.</li><li>Geschützte Vorschau öffnen. Änderungen im Formular aktualisieren Text und Design sofort; strukturelle Änderungen nach erneutem Speichern und Neuladen prüfen.</li><li>Veröffentlichung ausdrücklich bestätigen. Damit ändern Sie den für Wartung bereitstehenden Stand.</li><li>Wartungsmodus separat in der Übersicht einschalten und anonym prüfen.</li></ol><p>Im Plugin-Verzeichnis liegt die Dokumentation mit Installation, Datenschutz, Abnahme und Rückfallanleitung. Multisite: je Website aktivieren, keine Netzwerkaktivierung. REST: anonyme Zugriffe werden während Wartung abgewiesen; angemeldete API-Nutzer, Admin, Login, Cron und AJAX behalten ihre eigenen WordPress-Berechtigungen. Statische Dateien, XML-RPC und Server-Caches werden nicht abgeschirmt. Dieses Plugin ist kein Zugriffsschutz für vertrauliche Inhalte.</p><?php endif; ?>
</section><?php endforeach; ?>
<div class="save-bar"><button class="button button-primary" name="intent" value="draft">Entwurf speichern</button><label><input type="checkbox" name="confirm_publish" value="1"> Ich bestätige die Veröffentlichung dieser Einstellungen. Bei aktivem Wartungsmodus wird die Änderung sofort sichtbar.</label><button class="button" name="intent" value="publish">Einstellungen veröffentlichen</button><span id="dirty-state" role="status"></span></div></form>
<aside class="preview-panel"><h2>Vorschau</h2><p>Vorschau nur für Ihr Administratorkonto. Einmal gültig, zwei Minuten zum Öffnen.</p>
<?php
if ( $s['preview_enabled'] ) :
	?>
	<form id="preview-form" method="post" target="mgms-preview" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'mgms_preview' ); ?><input type="hidden" name="action" value="mgms_preview"><input type="hidden" name="preview_token" value="<?php echo esc_attr( Preview::issue() ); ?>"><button class="button" type="submit">Geschützte Vorschau öffnen</button></form><iframe name="mgms-preview" id="mgms-preview" title="Geschützte Wartungsvorschau" sandbox="allow-scripts allow-same-origin"></iframe>
	<?php
else :
	?>
	<p>Vorschau ist im Entwurf deaktiviert.</p><?php endif; ?>
</aside></div>
<section class="studio-panel"><h2>Wiederherstellen und aufräumen</h2><p>Es bleibt jeweils ein vorheriger Entwurf und veröffentlichter Stand erhalten. Die Wiederherstellung eines veröffentlichten Stands wirkt bei aktivem Wartungsmodus sofort.</p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'mgms_state' ); ?><input type="hidden" name="action" value="mgms_state"><label><input type="checkbox" name="confirm_state" value="1" required> Ich bestätige die Auswirkung der gewählten Aktion.</label><p><button class="button" name="intent" value="restore_draft">Vorherigen Entwurf wiederherstellen</button> <button class="button" name="intent" value="restore_published">Vorherige Veröffentlichung wiederherstellen</button> <button class="button" name="intent" value="prune">Alte Stände und Vorschau-Freigaben löschen</button></p></form></section>
</div>
