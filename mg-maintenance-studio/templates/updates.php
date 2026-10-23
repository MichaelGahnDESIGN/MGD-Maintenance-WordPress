<?php
namespace MGMS;
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section class="studio-panel" id="panel-9">
<h2>WordPress.org-Updates</h2>
<p>Diese Ausgabe verwendet die native Update-Verwaltung von WordPress.org. Eine frei einstellbare Update-Quelle ist nicht vorhanden.</p>
<p>Nach der Freigabe und Veröffentlichung im Plugin-Verzeichnis meldet WordPress verfügbare neue Versionen.</p>
<?php if ( current_user_can( 'update_plugins' ) ) : ?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mgd_org_check_updates' ); ?><input type="hidden" name="action" value="mgd_org_check_updates">
<p><button class="button">Updates jetzt prüfen</button></p>
</form>
<?php endif; ?>
</section>
