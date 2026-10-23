<?php
/** Cache- und Datenschutzgrenzen in einer wegwerfbaren lokalen WordPress-Installation. */
if (!defined('WP_CLI') || !WP_CLI || getenv('MGD_ORG_TEST') !== 'isolated-local' || home_url() !== 'http://127.0.0.1:18867') { exit(1); }

function mgd_cache_verify($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS $message\n";
}
function mgd_cache_call($object, $method, ...$args) {
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);
    return $reflection->invoke($object, ...$args);
}

global $wpdb;
wp_set_current_user(get_user_by('login', 'review')->ID);
$user_id = get_current_user_id();
$repository = new MGD_Notification_Repository();
$second = new MGD_Notification_Repository();
$ids = array(); $forms = array(); $submissions = array(); $pages = array();
$old_page_option = get_option('mgd_ail_ai_philosophy_page_id', false);
$query_count = 0;
$ai_meta_searches = 0;
$query_filter = static function($sql) use (&$query_count, &$ai_meta_searches, $wpdb) {
    if (strpos($sql, 'JOIN ' . $wpdb->postmeta) !== false) { ++$ai_meta_searches; }
    if (stripos(ltrim($sql), 'SELECT') === 0 && (strpos($sql, 'mgd_notifications') !== false || strpos($sql, 'mgd_notification_recipients') !== false || strpos($sql, 'mgd_giveaway_') !== false || strpos($sql, 'mgd_wordpress_mcp_audit') !== false)) { ++$query_count; }
    return $sql;
};
add_filter('query', $query_filter);
try {
    $repository->list_for_admin(); $before = $query_count;
    $second->list_for_admin();
    mgd_cache_verify($query_count === $before, 'Notification nutzt Anfragecache auch über zwei Repository-Instanzen');
    $public = array('scope' => 'public', 'status' => 'published', 'title' => 'Cachetest öffentlich', 'message' => 'Künstliche Daten');
    $ids[] = $repository->create($public);
    mgd_cache_verify(in_array($ids[0], array_map('intval', array_column($second->list_for_admin(), 'id')), true), 'Neue Meldung invalidiert eine zuvor leere Liste');
    $repository->get_for_admin($ids[0]); $before = $query_count;
    $second->get_for_admin($ids[0]);
    mgd_cache_verify($query_count === $before, 'Einzelmeldung wird innerhalb der Anfrage nur einmal gelesen');
    $personal = array('scope' => 'personal', 'status' => 'published', 'title' => 'Cachetest privat', 'message' => 'Nur für Empfänger');
    $ids[] = $repository->create($personal, array($user_id));
    $mine = $repository->personal_feed($user_id);
    mgd_cache_verify(count($mine) === 1 && $repository->personal_feed(0) === array(), 'Persönliche Feed-Caches sind nach Empfänger getrennt');
    mgd_cache_verify(!in_array($ids[1], array_map('intval', array_column($repository->public_feed(), 'id')), true), 'Persönliche Meldung gelangt nicht in den öffentlichen Cache');
    $repository->mark_read($ids[1], $user_id);
    $mine = $second->personal_feed($user_id);
    mgd_cache_verify(!empty($mine[0]['read_at']), 'Gelesen-Markierung invalidiert den persönlichen Feed');
    $second->update_manual($ids[1], $personal, array());
    mgd_cache_verify($repository->personal_feed($user_id) === array(), 'Empfängerentzug ist sofort sichtbar');
    $public['title'] = 'Geänderter Titel'; $second->update_manual($ids[0], $public);
    mgd_cache_verify($repository->get_for_admin($ids[0])['title'] === 'Geänderter Titel', 'Bearbeitung invalidiert den Einzelmeldungs-Cache');

    $plugin = MGD_Giveaway_Plugin::instance();
    $forms[] = wp_insert_post(array('post_type' => 'mgd_giveaway_form', 'post_status' => 'publish', 'post_title' => 'Lokaler Cachetest'));
    $form = $forms[0];
    mgd_cache_call($plugin, 'count_submissions', $form); $before = $query_count;
    mgd_cache_call($plugin, 'count_submissions', $form);
    mgd_cache_verify($query_count === $before, 'Giveaway zählt eine unveränderte Formularstatistik nur einmal');
    $submissions[] = mgd_cache_call($plugin, 'store_submission', $form, 'cachetest@example.test', array('source' => 'synthetic'));
    mgd_cache_verify(mgd_cache_call($plugin, 'count_submissions', $form) === 1, 'Neue Einsendung invalidiert die Null-Statistik');
    $log_count = mgd_cache_call($plugin, 'count_logs'); $before = $query_count;
    mgd_cache_call($plugin, 'count_logs');
    mgd_cache_verify($query_count === $before, 'Giveaway verwendet Cache für Loganzahl');
    mgd_cache_call($plugin, 'get_log_storage_usage');
    mgd_cache_call($plugin, 'add_log', 'info', 'org-cache-test', 'Künstlicher Cache-Prüfeintrag');
    mgd_cache_verify(mgd_cache_call($plugin, 'count_logs') === $log_count + 1, 'Neuer Logeintrag invalidiert Logstatistik');
    $before = $query_count; mgd_cache_call($plugin, 'get_log_storage_usage');
    mgd_cache_verify($query_count > $before, 'Neuer Logeintrag invalidiert auch die Größenaggregation');

    MGD_WordPress_MCP_Audit::latest(199); $before = $query_count;
    MGD_WordPress_MCP_Audit::latest(199);
    mgd_cache_verify($query_count === $before, 'MCP liest dieselbe Audit-Ansicht nur einmal pro Anfrage');
    MGD_WordPress_MCP_Audit::log('org-cache-test', 'read', true, 'Künstlicher Cache-Prüfeintrag');
    $latest = MGD_WordPress_MCP_Audit::latest(199);
    mgd_cache_verify($latest[0]['ability'] === 'org-cache-test', 'Neue MCP-Aktion invalidiert Audit-Ansicht sofort');

    delete_option('mgd_ail_ai_philosophy_page_id');
    $pages[] = wp_insert_post(array('post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Lokale alte AI-Seite'));
    update_post_meta($pages[0], '_mgd_ail_ai_philosophy_page', '1');
    $result = MGD_AI_Image_Labels_AI_Philosophy::create_page();
    mgd_cache_verify($result['page_id'] === $pages[0], 'Alte AI-Seite wird über ihren Marker wiedererkannt');
    $autoload = $wpdb->get_var($wpdb->prepare('SELECT autoload FROM %i WHERE option_name = %s', $wpdb->options, 'mgd_ail_ai_philosophy_page_id'));
    mgd_cache_verify((int) get_option('mgd_ail_ai_philosophy_page_id') === $pages[0] && !in_array($autoload, array('yes', 'on', 'auto-on', 'auto'), true), 'AI-Seiten-ID wird ohne Autoload gespeichert');
    $before = $ai_meta_searches;
    $lookup = new ReflectionMethod('MGD_AI_Image_Labels_AI_Philosophy', 'find_existing_page_id'); $lookup->setAccessible(true);
    mgd_cache_verify($lookup->invoke(null) === $pages[0] && $ai_meta_searches === $before, 'Verifizierte AI-Seiten-ID benötigt keine erneute Meta-Suche');
    wp_delete_post($pages[0], true);
    $result = MGD_AI_Image_Labels_AI_Philosophy::create_page(); $pages[] = $result['page_id'];
    mgd_cache_verify($result['page_id'] > 0 && $result['page_id'] !== $pages[0], 'Gelöschte AI-Seite wird nicht aus einem veralteten ID-Eintrag übernommen');
} finally {
    remove_filter('query', $query_filter);
    foreach ($ids as $id) {
        $wpdb->delete($wpdb->prefix . 'mgd_notification_recipients', array('notification_id' => $id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'mgd_notifications', array('id' => $id), array('%d'));
    }
    foreach ($submissions as $id) { $wpdb->delete($wpdb->prefix . 'mgd_giveaway_submissions', array('id' => $id), array('%d')); }
    foreach ($forms as $id) { wp_delete_post($id, true); }
    foreach ($pages as $id) { wp_delete_post($id, true); }
    $wpdb->delete($wpdb->prefix . 'mgd_giveaway_logs', array('event' => 'org-cache-test'), array('%s'));
    $wpdb->delete(MGD_WordPress_MCP_Audit::table_name(), array('ability' => 'org-cache-test'), array('%s'));
    if ($old_page_option === false) { delete_option('mgd_ail_ai_philosophy_page_id'); } else { update_option('mgd_ail_ai_philosophy_page_id', $old_page_option, false); }
}
