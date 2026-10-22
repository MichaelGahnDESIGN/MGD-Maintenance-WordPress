<?php
// Dateiaustausch ausschließlich in einer wegwerfbaren Kopie im lokalen Container.
if (PHP_SAPI !== 'cli' || getenv('MGMS_TEST') !== 'isolated-local') { exit(1); }
define('WP_PLUGIN_DIR','/tmp/mgms-upgrade/plugins');
define('FS_METHOD','direct');
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/misc.php';
require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
$slug='mg-maintenance-studio/mg-maintenance-studio.php';
$before=get_option('mgms_published');$state=get_option('mgms_enabled');$active=get_option('active_plugins');
$package='https://github.com/MichaelGahnDESIGN/MGMS-Test/releases/download/v0.3.0/mg-maintenance-studio-0.3.0.zip';
add_filter('pre_http_request',static fn()=>new WP_Error('test_no_network','Nur lokales Paket'),10,3);
add_filter('upgrader_pre_download',static function($reply,$url) use($package) {
 if($url!==$package){return new WP_Error('unexpected_package','Unerwartetes Paket');}
 copy('/tmp/mgms-package.zip','/tmp/mgms-install-package.zip');return '/tmp/mgms-install-package.zip';
},10,2);
try {
 if(get_plugin_data(WP_PLUGIN_DIR.'/'.$slug)['Version']!=='0.2.0'){throw new RuntimeException('Ausgangsversion fehlt');}
 set_site_transient('update_plugins',(object)['response'=>[$slug=>(object)['new_version'=>'0.3.0','package'=>$package,'plugin'=>$slug,'slug'=>'mg-maintenance-studio']], 'checked'=>[$slug=>'0.2.0']]);
 $skin=new Automatic_Upgrader_Skin();$upgrader=new Plugin_Upgrader($skin);$result=$upgrader->upgrade($slug);
 if($result!==true){throw new RuntimeException('WordPress-Installation fehlgeschlagen: '.json_encode($skin->get_errors()->get_error_messages()));}
 wp_clean_plugins_cache(false);
 if(get_plugin_data(WP_PLUGIN_DIR.'/'.$slug)['Version']!=='0.3.0'){throw new RuntimeException('Zielversion fehlt');}
 if(get_option('mgms_published')!==$before || get_option('mgms_enabled')!==$state){throw new RuntimeException('Einstellungen verändert');}
 echo "PASS WordPress Plugin_Upgrader ersetzt 0.2.0 durch ZIP 0.3.0 im gleichen Ordner\nPASS Veröffentlichte Einstellungen und Wartungsstatus unverändert\n";
} finally {update_option('active_plugins',$active);delete_site_transient('update_plugins');}
