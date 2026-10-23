<?php
namespace MGMS;
if ( ! defined( 'ABSPATH' ) ) {
	exit; }
?>
<div class="wrap mgms-admin mgms-errors-admin">
<header class="studio-header"><div class="studio-brand"><img src="<?php echo esc_url( MGMS_URL . 'assets/icon.svg' ); ?>" width="56" height="56" alt=""><div><h1>Fehlerseiten</h1><p>Maintenance Studio <span aria-hidden="true">·</span> 404 und 403 getrennt gestalten</p></div></div><div class="mgms-header-actions"><?php Admin::status_switch( 'mgms-error-pages', $enabled ); ?><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=mgms-dashboard' ) ); ?>">Zur Übersicht</a></div></header>
<?php
// Feste Meldungen, keine Zustandsänderung durch die URL.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nur feste Rückmeldung eines geschützten Speichervorgangs.
$mgms_notice = sanitize_key( wp_unslash( $_GET['mgms_notice'] ?? '' ) );
if ( in_array( $mgms_notice, array( 'saved', 'published' ), true ) ) :
	?><div class="notice notice-success" role="status"><p><?php echo 'saved' === $mgms_notice ? 'Entwurf gespeichert. Die Website bleibt unverändert.' : 'Fehlerseiten veröffentlicht. Aktivierte Seiten sind jetzt sichtbar.'; ?></p></div><?php endif; ?>
<p class="studio-warning">Neue Fehlerseiten sind zunächst ausgeschaltet. Speichern legt nur einen Entwurf an. Erst „Veröffentlichen“ übernimmt die aktivierten Seiten. Bei aktivem Wartungsmodus hat die Wartungsseite Vorrang.</p>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mgms_error_save' ); ?><input type="hidden" name="action" value="mgms_error_save"><input type="hidden" name="revision" value="<?php echo esc_attr( Error_Pages_Admin::revision() ); ?>">
<?php foreach ( $settings as $mgms_code => $mgms_row ) :
	$mgms_prefix = 'settings[' . $mgms_code . ']';
	$preview = wp_nonce_url( add_query_arg( array( 'action' => 'mgms_error_preview', 'code' => $mgms_code ), admin_url( 'admin-post.php' ) ), 'mgms_error_preview' );
	?>
<section class="studio-panel" id="error-<?php echo esc_attr( $mgms_code ); ?>"><div class="mgms-error-heading"><div><p class="mgms-error-kicker"><?php echo esc_html( $mgms_code ); ?> · <?php echo '404' === $mgms_code ? 'Seite nicht gefunden' : 'Zugriff verweigert'; ?></p><h2><?php echo '404' === $mgms_code ? 'Nicht gefunden' : 'Kein Zugriff'; ?></h2></div><span class="state-pill"><?php echo $live[ $mgms_code ]['enabled'] ? 'Veröffentlicht und aktiv' : 'Aktuell ausgeschaltet'; ?></span></div>
<p><?php echo '404' === $mgms_code ? 'Erscheint bei nicht gefundenen Adressen. Die Suchfunktion lässt sich separat abschalten.' : 'Erscheint bei WordPress-403-Fehlern im öffentlichen HTML. Login, Admin, REST und serverseitige Sperren behalten ihre eigene Fehlerseite.'; ?></p>
<div class="mgms-error-fields"><label class="mgms-error-switch"><input type="checkbox" name="<?php echo esc_attr( $mgms_prefix . '[enabled]' ); ?>" value="1" <?php checked( $mgms_row['enabled'] ); ?>> Eigene <?php echo esc_html( $mgms_code ); ?>-Seite aktivieren</label>
<fieldset><legend>Vorlage</legend><div class="mgms-template-options"><?php foreach ( Error_Pages::templates() as $mgms_key => $mgms_label ) : ?><label><input type="radio" name="<?php echo esc_attr( $mgms_prefix . '[template]' ); ?>" value="<?php echo esc_attr( $mgms_key ); ?>" <?php checked( $mgms_row['template'], $mgms_key ); ?>><span class="mgms-template-swatch mgms-template-swatch--<?php echo esc_attr( $mgms_key ); ?>" aria-hidden="true"></span><strong><?php echo esc_html( $mgms_label ); ?></strong></label><?php endforeach; ?></div></fieldset>
<div class="mgms-error-form-grid"><label>Überschrift<input type="text" maxlength="110" required name="<?php echo esc_attr( $mgms_prefix . '[title]' ); ?>" value="<?php echo esc_attr( $mgms_row['title'] ); ?>"></label><label>Buttontext<input type="text" maxlength="60" required name="<?php echo esc_attr( $mgms_prefix . '[button]' ); ?>" value="<?php echo esc_attr( $mgms_row['button'] ); ?>"></label></div>
<label>Beschreibung<textarea rows="3" maxlength="600" required name="<?php echo esc_attr( $mgms_prefix . '[message]' ); ?>"><?php echo esc_textarea( $mgms_row['message'] ); ?></textarea></label>
<?php if ( '404' === $mgms_code ) : ?><label class="mgms-error-switch"><input type="checkbox" name="<?php echo esc_attr( $mgms_prefix . '[search]' ); ?>" value="1" <?php checked( $mgms_row['search'] ); ?>> Suche anzeigen</label><?php endif; ?>
<div class="mgms-error-form-grid mgms-error-colors"><?php foreach ( array( 'background' => 'Hintergrund', 'accent' => 'Akzent', 'text' => 'Text' ) as $mgms_key => $mgms_label ) : ?><label><?php echo esc_html( $mgms_label ); ?><input type="color" name="<?php echo esc_attr( $mgms_prefix . '[' . $mgms_key . ']' ); ?>" value="<?php echo esc_attr( $mgms_row[ $mgms_key ] ); ?>"></label><?php endforeach; ?></div><p class="description">Die dunkle Vorlage verwendet für Lesbarkeit feste dunkle Farben. Eigene Farben gelten für „Klar“ und „Editorial“.</p>
<a class="button" href="<?php echo esc_url( $preview ); ?>" target="_blank" rel="noopener">Gespeicherten Entwurf ansehen: <?php echo esc_html( $mgms_code ); ?></a></div></section>
<?php endforeach; ?>
<div class="save-bar"><button class="button button-primary" name="intent" value="draft">Entwurf speichern</button><label><input type="checkbox" name="confirm_publish" value="1"> Ich bestätige, dass aktivierte Fehlerseiten nach der Veröffentlichung öffentlich angezeigt werden.</label><button class="button" name="intent" value="publish">Fehlerseiten veröffentlichen</button></div></form>
<p class="description">Vorschau zeigt den zuletzt gespeicherten Entwurf. Nach der Veröffentlichung sollten Sie eine unbekannte URL anonym testen. Einen 403-Fehler nur mit einer geeigneten Testseite prüfen; öffentliche Login- oder Sicherheitsmechanismen bleiben unverändert.</p>
</div>
