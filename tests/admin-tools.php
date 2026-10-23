<?php
define('ABSPATH', '/tmp/'); define('HOUR_IN_SECONDS',3600); define('MINUTE_IN_SECONDS',60);
$hooks=[];$transients=[];$options=[];$request=[];$allow=true;$redirect='';$checks=0;
function add_action($n,$c,...$args){global $hooks;$hooks[$n][]=$c;}
function add_filter($n,$c,...$args){add_action($n,$c);}
function plugin_basename($p){return basename(dirname($p)).'/'.basename($p);}
function current_user_can($p){global $allow;return $allow;}
function wp_die(...$a){throw new RuntimeException('denied');}
function check_admin_referer($n){if(($_POST['_wpnonce']??'')!==$n)throw new RuntimeException('nonce');}
function get_site_transient($k){global $transients;return $transients[$k]??false;}
function set_site_transient($k,$v,...$args){global $transients;$transients[$k]=$v;}
function delete_site_transient($k){global $transients;unset($transients[$k]);}
function get_transient($k){return get_site_transient($k);}
function set_transient($k,$v,...$args){set_site_transient($k,$v);}
function delete_transient($k){delete_site_transient($k);}
function wp_update_plugins(){global $checks;$checks++;}
function wp_safe_redirect($url){global $redirect;$redirect=$url;throw new RuntimeException('redirect');}
function admin_url($p){return 'https://example.test/wp-admin/'.$p;}
function plugins_url($p,$f){return 'https://example.test/'.basename(dirname($f)).'/'.$p;}
function is_wp_error($p){return false;}
function esc_url($v){return htmlspecialchars($v,ENT_QUOTES);}
function esc_html($v){return htmlspecialchars($v,ENT_QUOTES);}
function wp_safe_remote_get($url,$args){global $request;return $request;}
function wp_remote_retrieve_response_code($r){return $r['code'];}
function wp_remote_retrieve_body($r){return json_encode($r['body']);}
function get_option($k,$d=false){global $options;return $options[$k]??$d;}
function update_option($k,$v,...$args){global $options;$options[$k]=$v;}
function wp_nonce_field($n){echo '<input name="_wpnonce" value="'.$n.'">';}
function ok($v,$m){if(!$v)throw new RuntimeException($m);echo "PASS $m\n";}
require dirname(__DIR__).'/mg-maintenance-studio/includes/class-mgd-plugin-admin-tools.php';
$p=['file'=>'/plugins/mgd-share-wordpress/mgd-share-wordpress.php','slug'=>'mgd-share-wordpress','repo'=>'MGD-Share-WordPress','version'=>'0.1.1','name'=>'MGD Share','description'=>'Teilen','requires'=>'6.5','php'=>'8.1','settings'=>'admin.php?page=mgd-share','package'=>'mgd-share-wordpress-{version}.zip','caches'=>['cache-one']];
MGD_Plugin_Admin_Tools_20261023::register($p);MGD_Plugin_Admin_Tools_20261023::register($p+['name'=>'other']);
ok(count($hooks['admin_notices'])===1,'Gemeinsamen Prüfknopf nur einmal registriert');
$tag='v0.1.2';$url='https://github.com/MichaelGahnDESIGN/MGD-Share-WordPress/releases/download/'.$tag.'/mgd-share-wordpress-0.1.2.zip';
$request=['code'=>200,'body'=>['tag_name'=>$tag,'assets'=>[['name'=>'mgd-share-wordpress-0.1.2.zip','browser_download_url'=>$url]]]];
ok(MGD_Plugin_Admin_Tools_20261023::offer(null,[],plugin_basename($p['file']))['version']==='0.1.2','Passendes GitHub-Paket akzeptiert');
delete_site_transient('mgd_admin_release_mgd-share-wordpress');$request['body']['assets'][0]['browser_download_url']='https://evil.test/plugin.zip';
ok(MGD_Plugin_Admin_Tools_20261023::offer(null,[],plugin_basename($p['file']))===false,'Fremde Download-URL abgelehnt');
delete_site_transient('mgd_admin_release_mgd-share-wordpress');$request['body']['assets'][0]['browser_download_url']=$url;$request['body']['prerelease']=true;
ok(MGD_Plugin_Admin_Tools_20261023::offer(null,[],plugin_basename($p['file']))===false,'Vorabversion abgelehnt');
$_SERVER['REQUEST_METHOD']='POST';$allow=false;
try{MGD_Plugin_Admin_Tools_20261023::check();}catch(RuntimeException $e){ok($e->getMessage()==='denied','Prüfung ohne Berechtigung gesperrt');}
$allow=true;try{MGD_Plugin_Admin_Tools_20261023::check();}catch(RuntimeException $e){ok($e->getMessage()==='nonce','Prüfung ohne gültige Nonce gesperrt');}
$transients['cache-one']='cached';$transients['unrelated']='keep';$transients['update_plugins']=(object)['checked'=>['a'=>'1'],'last_checked'=>100];$_POST['_wpnonce']='mgd_check_plugin_updates';
try{MGD_Plugin_Admin_Tools_20261023::check();}catch(RuntimeException $e){ok($e->getMessage()==='redirect','Prüfung kehrt zu Aktualisierungen zurück');}
ok($checks===1 && !isset($transients['cache-one']) && $transients['unrelated']==='keep' && $transients['update_plugins']->checked===['a'=>'1'],'Nur eigene Caches geleert und native Prüfung ausgeführt');
$details=MGD_Plugin_Admin_Tools_20261023::information(false,'plugin_information',(object)['slug'=>$p['slug']]);
ok(isset($details->banners['low'],$details->sections['faq'],$details->icons['svg']),'Lokale Details mit Banner, Icon und FAQ verfügbar');
require dirname(__DIR__).'/mg-maintenance-studio/includes/class-admin.php';
require dirname(__DIR__).'/mg-maintenance-studio/includes/class-updates.php';
ok(MGMS\Admin::menu_last(['index.php','mgms-dashboard','later-plugin'])===['index.php','later-plugin','mgms-dashboard'],'Maintenance Studio hinter späteren Menüs');
$options['mgms_updates']=['repository'=>'MichaelGahnDESIGN/Broken','enabled'=>false];$options['mgms_last_good_repository']='MichaelGahnDESIGN/Good';$_POST=['_wpnonce'=>'mgms_restore_updates','source'=>'last'];
try{MGMS\Updates::restore();}catch(RuntimeException $e){}
ok($options['mgms_updates']===['repository'=>'MichaelGahnDESIGN/Good','enabled'=>false],'Letzte Quelle wiederhergestellt ohne Zustimmung zu verändern');
$_POST['source']='original';try{MGMS\Updates::restore();}catch(RuntimeException $e){}
ok($options['mgms_updates']['repository']===MGMS\Updates::ORIGINAL,'Originalquelle sicher wiederhergestellt');
