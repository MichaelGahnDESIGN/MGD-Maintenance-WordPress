<?php
// Ausschließlich in der neu angelegten lokalen Testinstallation ausführen.
if (PHP_SAPI !== 'cli' || getenv('MGMS_TEST') !== 'isolated-local') { exit(1); }
define('WP_INSTALLING', true);
$_SERVER['HTTP_HOST']='127.0.0.1:18865';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
add_filter('pre_wp_mail', '__return_false');
if (!is_blog_installed()) {
 $password = bin2hex(random_bytes(24));
 wp_install('MG Studio – lokale Prüfung', 'local-admin', 'local-admin@example.invalid', false, '', $password);
 file_put_contents('/tmp/mgms-login.json', json_encode(['user'=>'local-admin','password'=>$password]));
 chmod('/tmp/mgms-login.json',0600);
}
update_option('home','http://127.0.0.1:18865'); update_option('siteurl','http://127.0.0.1:18865');
$result=activate_plugin('mg-maintenance-studio/mg-maintenance-studio.php');
if(is_wp_error($result)){fwrite(STDERR,$result->get_error_message());exit(1);}
echo 'Installed WordPress '.get_bloginfo('version').' PHP '.PHP_VERSION."\n";
