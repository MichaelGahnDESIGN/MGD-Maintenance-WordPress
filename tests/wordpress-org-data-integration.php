<?php
/** Datenzugriff nur in der lokalen, künstlich befüllten Testinstallation prüfen. */
if (!defined('WP_CLI') || !WP_CLI || getenv('MGD_ORG_TEST') !== 'isolated-local' || home_url() !== 'http://127.0.0.1:18867') { exit(1); }

function mgd_org_data_check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS $message\n";
}

global $wpdb;
$repository = new MGD_Notification_Repository();
$user = get_user_by('login', 'review');
wp_set_current_user($user->ID);
$ids = array();
$form = 0;
$submission = 0;
try {
    $public = array('scope' => 'public', 'status' => 'published', 'title' => 'Lokale Testmeldung', 'message' => '<strong>Test</strong><script>unsafe()</script>', 'source_key' => 'org-local-idempotent');
    $ids[] = $repository->create_once($public);
    mgd_org_data_check($ids[0] > 0 && $repository->create_once($public) === $ids[0], 'Notification-Ereignis bleibt bei Wiederholung eindeutig');
    $personal = array('scope' => 'personal', 'status' => 'published', 'title' => 'Persönlicher Test', 'message' => 'Nur für das Testkonto');
    $ids[] = $repository->create($personal, array($user->ID));
    mgd_org_data_check(in_array($ids[0], array_map('intval', array_column($repository->public_feed(), 'id')), true), 'Öffentliche Meldung im öffentlichen Feed');
    mgd_org_data_check(!in_array($ids[1], array_map('intval', array_column($repository->public_feed(), 'id')), true), 'Persönliche Meldung bleibt aus öffentlichem Feed ausgeschlossen');
    mgd_org_data_check(in_array($ids[1], array_map('intval', array_column($repository->personal_feed($user->ID), 'id')), true), 'Zugeordneter Empfänger erhält persönliche Meldung');
    mgd_org_data_check($repository->personal_feed(0) === array(), 'Anonymes Konto erhält keine persönliche Meldung');
    mgd_org_data_check($repository->mark_read($ids[1], $user->ID), 'Empfänger kann eigene Meldung als gelesen markieren');
    mgd_org_data_check(strpos($repository->get_for_admin($ids[0])['message'], '<script>') === false, 'Notification entfernt ausführbares HTML');
    mgd_org_data_check($repository->update_manual($ids[1], $personal, array()), 'Empfängerliste kann aktualisiert werden');
    mgd_org_data_check($repository->personal_feed($user->ID) === array(), 'Entfernter Empfänger erhält Meldung nicht mehr');
    mgd_org_data_check(count($repository->list_for_admin()) >= 2, 'Verwaltungsabfrage mit vorbereiteten Tabellen funktioniert');

    $form = wp_insert_post(array('post_type' => 'mgd_giveaway_form', 'post_status' => 'publish', 'post_title' => 'Lokaler Datentest'));
    $plugin = MGD_Giveaway_Plugin::instance();
    $store = new ReflectionMethod($plugin, 'store_submission'); $store->setAccessible(true);
    $submission = $store->invoke($plugin, $form, 'org-local@example.test', array('source' => 'synthetic'));
    $increment = new ReflectionMethod($plugin, 'increment_download_count'); $increment->setAccessible(true); $increment->invoke($plugin, $submission);
    $count = new ReflectionMethod($plugin, 'count_submissions'); $count->setAccessible(true);
    mgd_org_data_check($submission > 0 && $count->invoke($plugin, $form) === 1, 'Giveaway speichert und zählt eine Einsendung');
    $download_count = $wpdb->get_var($wpdb->prepare('SELECT download_count FROM %i WHERE id = %d', $wpdb->prefix . 'mgd_giveaway_submissions', $submission));
    mgd_org_data_check((int) $download_count === 1, 'Giveaway erhöht Downloadzähler über vorbereitete Tabellenabfrage');
    $usage = new ReflectionMethod($plugin, 'get_log_storage_usage'); $usage->setAccessible(true);
    mgd_org_data_check(is_string($usage->invoke($plugin)), 'Giveaway kann Loggröße ermitteln');
    $before = count(MGD_WordPress_MCP_Audit::latest(200));
    MGD_WordPress_MCP_Audit::log('org-local-review', 'read', true, 'Künstlicher Prüfeintrag');
    mgd_org_data_check(count(MGD_WordPress_MCP_Audit::latest(200)) === $before + 1, 'MCP-Audit schreibt und liest über vorbereitete Tabellen');
} finally {
    foreach ($ids as $id) {
        $wpdb->delete($wpdb->prefix . 'mgd_notification_recipients', array('notification_id' => $id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'mgd_notifications', array('id' => $id), array('%d'));
    }
    if ($submission) { $wpdb->delete($wpdb->prefix . 'mgd_giveaway_submissions', array('id' => $submission), array('%d')); }
    if ($form) { wp_delete_post($form, true); }
    $wpdb->delete(MGD_WordPress_MCP_Audit::table_name(), array('ability' => 'org-local-review'), array('%s'));
}
