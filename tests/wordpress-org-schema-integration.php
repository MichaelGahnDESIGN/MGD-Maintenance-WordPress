<?php
/** Schema-Aufbau in künstlichen Tabellen prüfen, ohne vorhandene Daten anzufassen. */
if (!defined('WP_CLI') || !WP_CLI || getenv('MGD_ORG_TEST') !== 'isolated-local' || home_url() !== 'http://127.0.0.1:18867') { exit(1); }
function mgd_schema_verify($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS $message\n";
}
global $wpdb;
$old_prefix = $wpdb->prefix;
$old_version = get_option('mgd_notification_schema_version', false);
$old_suppression = $wpdb->suppress_errors(true);
$test_prefix = $old_prefix . 'org_schema_test_';
$failed_prefix = $old_prefix . 'org_schema_fail_';
$reject_creation = static function($sql) use ($failed_prefix) {
    return strpos($sql, 'CREATE TABLE ' . $failed_prefix) === 0 ? 'SELECT * FROM __mgd_schema_failure_fixture__' : $sql;
};
try {
    $wpdb->prefix = $test_prefix;
    delete_option('mgd_notification_schema_version');
    mgd_schema_verify(MGD_Notification_Plugin::ensure_schema(), 'Notification legt fehlende Tabellen für das aktuelle Site-Präfix an');
    mgd_schema_verify(get_option('mgd_notification_schema_version') === '1', 'Schema-Version wird erst nach bestätigtem Tabellenaufbau gespeichert');
    $before = $wpdb->num_queries;
    mgd_schema_verify(MGD_Notification_Plugin::ensure_schema() && $before === $wpdb->num_queries, 'Bestätigte Schema-Version verursacht keine wiederholte Installationsabfrage');
    $wpdb->prefix = $failed_prefix;
    delete_option('mgd_notification_schema_version');
    add_filter('query', $reject_creation);
    mgd_schema_verify(!MGD_Notification_Plugin::ensure_schema(), 'Fehlgeschlagener Tabellenaufbau wird erkannt');
    mgd_schema_verify(get_option('mgd_notification_schema_version', false) === false, 'Fehlgeschlagener Aufbau wird nicht als erfolgreich markiert');
} finally {
    remove_filter('query', $reject_creation);
    $wpdb->prefix = $old_prefix;
    foreach (array($test_prefix, $failed_prefix) as $prefix) {
        foreach (array('mgd_notifications', 'mgd_notification_recipients') as $suffix) {
            $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $prefix . $suffix));
        }
    }
    $wpdb->suppress_errors($old_suppression);
    if ($old_version === false) { delete_option('mgd_notification_schema_version'); } else { update_option('mgd_notification_schema_version', $old_version, false); }
}
