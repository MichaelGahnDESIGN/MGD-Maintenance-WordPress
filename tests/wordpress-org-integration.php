<?php
/** Reale Integrationstests in einer wegwerfbaren WordPress-Testinstallation. */
if (!defined('WP_CLI') || !WP_CLI || getenv('MGD_ORG_TEST') !== 'isolated-local') { exit(1); }
function mgd_org_verify($condition, $message) { if (!$condition) { throw new RuntimeException($message); } echo "PASS $message\n"; }
wp_set_current_user(get_user_by('login', 'review')->ID);
$settings = new MGD_Barrierefrei_Settings();
$old = get_option(MGD_Barrierefrei_Settings::OPTION_NAME, false);
try {
    $s = MGD_Barrierefrei_Settings::defaults();
    update_option(MGD_Barrierefrei_Settings::OPTION_NAME, $s, false);
    $renderer = new MGD_Barrierefrei_Renderer($settings);
    ob_start(); $renderer->markup(); $html = ob_get_clean();
    mgd_org_verify(strpos($html, 'https://michael-gahn.de') === false, 'Herstellerlink standardmäßig aus');
    $s['show_credit'] = true; update_option(MGD_Barrierefrei_Settings::OPTION_NAME, $s, false);
    ob_start(); $renderer->markup(); $html = ob_get_clean();
    mgd_org_verify(strpos($html, 'https://michael-gahn.de') !== false && strpos($html, 'rel="noopener noreferrer"') !== false, 'Herstellerlink erst nach ausdrücklicher Auswahl mit sicherem neuen Tab');
    $content = (new MGD_Barrierefrei_Statement($settings))->content($s);
    mgd_org_verify(strpos($content, 'keine Rechtsberatung') !== false, 'Erklärung kennzeichnet die fachliche Prüfungspflicht');
    $_REQUEST['_wpnonce'] = wp_create_nonce('mgd_barrierefrei_create_statement_page');
    $redirect = static function() { throw new RuntimeException('expected-redirect'); };
    add_filter('wp_redirect', $redirect);
    try { (new MGD_Barrierefrei_Statement($settings))->create_or_update_page(); } catch (RuntimeException $e) { if ($e->getMessage() !== 'expected-redirect') { throw $e; } }
    remove_filter('wp_redirect', $redirect);
    $created = (int) $settings->all()['statement_page_id'];
    mgd_org_verify($created > 0 && get_post_status($created) === 'draft', 'Erklärung wird als unveröffentlichter Entwurf angelegt');
    wp_delete_post($created, true);
} finally {
    if ($old === false) { delete_option(MGD_Barrierefrei_Settings::OPTION_NAME); } else { update_option(MGD_Barrierefrei_Settings::OPTION_NAME, $old, false); }
}
$order = apply_filters('menu_order', array('index.php', 'mgms-dashboard', 'tools.php', 'later-plugin'));
mgd_org_verify(end($order) === 'mgms-dashboard', 'Maintenance-Menü am Ende');
$form = wp_insert_post(array('post_type' => 'mgd_giveaway_form', 'post_title' => 'Lokaler Formular-Test', 'post_status' => 'publish'));
$plugin = MGD_Giveaway_Plugin::instance();
$reject = static function() { return static function($message) { throw new RuntimeException('request-rejected'); }; };
add_filter('wp_die_handler', $reject);
try {
    $html = $plugin->render_shortcode(array('id' => $form));
    mgd_org_verify(strpos($html, '_wpnonce') !== false, 'Giveaway-Formular enthält WordPress-Nonce');
    $_POST = array('form_id' => $form); $_REQUEST = $_POST;
    try { $plugin->handle_frontend_submit(); throw new LogicException('Unsichere Anfrage akzeptiert'); } catch (RuntimeException $e) { mgd_org_verify($e->getMessage() === 'request-rejected', 'Giveaway lehnt fehlende Nonce ab'); }
    preg_match('/name="mgd_giveaway_started" value="([^"]+)"/', $html, $m);
    $_POST = array('form_id' => $form, '_wpnonce' => wp_create_nonce('mgd_giveaway_submit_' . $form), 'email' => 'test@example.test', 'privacy' => '0', 'mgd_giveaway_started' => html_entity_decode($m[1])); $_REQUEST = $_POST;
    try { $plugin->handle_frontend_submit(); throw new LogicException('Fehlende Zustimmung akzeptiert'); } catch (RuntimeException $e) { mgd_org_verify($e->getMessage() === 'request-rejected', 'Giveaway lehnt nicht bestätigte Pflicht-Checkbox ab'); }
} finally { remove_filter('wp_die_handler', $reject); wp_delete_post($form, true); $_POST = array(); $_REQUEST = array(); }
echo "Integration abgeschlossen.\n";
