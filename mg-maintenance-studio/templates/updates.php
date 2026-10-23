<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$config = Updates::config();
$cached = get_transient( 'mgms_release' );
?>
<section class="studio-panel" id="panel-9">
<h2>Version und Updates</h2>
<?php if ( current_user_can( 'update_plugins' ) ) : ?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mgd_check_plugin_updates' ); ?><input type="hidden" name="action" value="mgd_check_plugin_updates">
<p><button class="button">Updates jetzt prüfen</button> <span>Prüft gespeicherte Quellen, ohne Einstellungen zu speichern oder Updates zu installieren.</span></p>
</form>
<?php endif; ?>
<p>Neue Versionen erscheinen wie gewohnt unter <strong>Plugins</strong> und <strong>Dashboard → Aktualisierungen</strong>. Die Installation übernimmt WordPress.</p>
<div class="overview-grid">
<div><small>Installierte Version</small><strong><?php echo esc_html( MGMS_VERSION ); ?></strong></div>
<div><small>GitHub-Prüfung</small><strong><?php echo $config['enabled'] ? 'Eingeschaltet' : 'Ausgeschaltet'; ?></strong></div>
<div><small>Letztes Ergebnis</small><strong><?php echo esc_html( $cached['message'] ?? 'Noch nicht geprüft.' ); ?></strong></div>
<?php
if ( ! empty( $cached['checked'] ) ) :
	?>
	<div><small>Zuletzt geprüft</small><strong><?php echo esc_html( wp_date( 'd.m.Y H:i', $cached['checked'] ) ); ?></strong></div><?php endif; ?>
</div>
<?php if ( current_user_can( 'update_plugins' ) ) : ?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php wp_nonce_field( 'mgms_updates' ); ?><input type="hidden" name="action" value="mgms_updates">
<div class="field"><label for="mgms-repository">GitHub-Repository</label><input id="mgms-repository" name="repository" type="text" maxlength="120" placeholder="MichaelGahnDESIGN/Repository" value="<?php echo esc_attr( $config['repository'] ); ?>" aria-describedby="mgms-repository-help"></div>
<p id="mgms-repository-help">Eine falsche Quelle kann Updates verhindern. Nur ein stabiles Release mit passendem Paket wird akzeptiert. Die letzte erfolgreich geprüfte Quelle bleibt als Rückfall gespeichert.</p>
<p><label><input type="checkbox" name="enabled" value="1" <?php checked( $config['enabled'] ); ?>> GitHub zur Prüfung neuer Versionen kontaktieren</label></p>
<p class="description">GitHub erhält die Server-IP und die Pluginversion. Es werden keine Inhalte, Benutzerkonten oder Zugangsdaten übertragen. Die Prüfung wird zwölf Stunden zwischengespeichert. Automatische Installationen richten Sie bei Bedarf in der WordPress-Pluginliste ein.</p>
<p><button class="button button-primary">Speichern und jetzt prüfen</button> <a class="button" href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>">WordPress-Aktualisierungen öffnen</a></p>
</form>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mgms_restore_updates' ); ?><input type="hidden" name="action" value="mgms_restore_updates">
<p>Letzte geprüfte Quelle: <code><?php echo esc_html( get_option( 'mgms_last_good_repository', Updates::ORIGINAL ) ); ?></code></p>
<p><button class="button" name="source" value="last">Letzte geprüfte Quelle wiederherstellen</button> <button class="button" name="source" value="original">Originalquelle wiederherstellen</button></p>
</form>
<?php endif; ?>
</section>
