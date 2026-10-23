<?php
// Echte WordPress-Filter, aber sämtliche HTTP-Antworten kommen aus Testdaten.
if ( PHP_SAPI !== 'cli' || getenv('MGMS_TEST') !== 'isolated-local' ) { exit(1); }
require '/var/www/html/wp-load.php';
use MGMS\Updates;
$old = get_option('mgms_updates', false);
$count = 0;
function verify($condition, $label) { global $count; if (!$condition) { throw new RuntimeException($label); } ++$count; echo "PASS $label\n"; }
$repo='MichaelGahnDESIGN/MGMS-Test';
$parts=array_map('intval',explode('.',MGMS_VERSION));
$next=$parts[0].'.'.$parts[1].'.'.($parts[2]+1);
$release=['tag_name'=>'v'.$next,'draft'=>false,'prerelease'=>false,'assets'=>[['name'=>'mg-maintenance-studio-'.$next.'.zip','browser_download_url'=>'https://github.com/'.$repo.'/releases/download/v'.$next.'/mg-maintenance-studio-'.$next.'.zip']]];
$calls=0; $mode='ok';
add_filter('pre_http_request',function($pre,$args,$url) use (&$calls,&$mode,&$release) {
 if (str_starts_with($url,'https://api.github.com/')) { ++$calls; return $mode==='error' ? new WP_Error('timeout','Test timeout') : ['response'=>['code'=>200],'body'=>json_encode($release),'headers'=>[]]; }
 return ['response'=>['code'=>200], 'body'=>json_encode(['plugins'=>[], 'no_update'=>[], 'translations'=>[]]), 'headers'=>[]];
},10,3);
try {
 update_option('mgms_updates',['repository'=>$repo,'enabled'=>false]); delete_transient('mgms_release');
 verify(Updates::latest()===[] && $calls===0,'Ohne Zustimmung kein HTTP-Aufruf');
 verify(Updates::normalize($release,$repo)['version']===$next,'Stabiles, genau passendes Paket akzeptiert');
 foreach (['draft','prerelease'] as $flag) { $bad=$release; $bad[$flag]=true; verify(Updates::normalize($bad,$repo)===[],"$flag abgelehnt"); }
 foreach (['0.4.1-beta','v01.2.0',[], 'v'.$next.'/../'] as $tag) { $bad=$release; $bad['tag_name']=$tag; verify(Updates::normalize($bad,$repo)===[],'Ungültiger Tag abgelehnt'); }
 foreach (['https://evil.example/p.zip',$release['assets'][0]['browser_download_url'].'?x=1',str_replace('/v'.$next.'/','/v0.3.0/',$release['assets'][0]['browser_download_url'])] as $url) { $bad=$release;$bad['assets'][0]['browser_download_url']=$url;verify(Updates::normalize($bad,$repo)===[],'Fremdes oder abweichendes Paket abgelehnt'); }
 verify(!Updates::valid_repository('MichaelGahnDESIGN/../test'),'Pfadmanipulation abgelehnt');
 update_option('mgms_updates',['repository'=>$repo,'enabled'=>true]);
 Updates::latest(); Updates::latest(); verify($calls===1,'Erfolg zwölf Stunden zwischengespeichert');
 verify(Updates::offer('other',[],'other/main.php')==='other','Fremde Plugins unberührt');
 delete_site_transient('update_plugins'); wp_update_plugins(); $updates=get_site_transient('update_plugins');
 verify(($updates->response[plugin_basename(MGMS_FILE)]->new_version??'')===$next,'Core zeigt neue Version über Update-URI an');
 $release['tag_name']='v'.MGMS_VERSION;$release['assets'][0]=['name'=>'mg-maintenance-studio-'.MGMS_VERSION.'.zip','browser_download_url'=>'https://github.com/'.$repo.'/releases/download/v'.MGMS_VERSION.'/mg-maintenance-studio-'.MGMS_VERSION.'.zip'];
 delete_transient('mgms_release'); delete_site_transient('update_plugins');wp_update_plugins();$updates=get_site_transient('update_plugins');
 verify(!isset($updates->response[plugin_basename(MGMS_FILE)]) && isset($updates->no_update[plugin_basename(MGMS_FILE)]),'Gleiche Version erzeugt kein Update');
 $mode='error';delete_transient('mgms_release');$before=$calls;Updates::latest();Updates::latest();verify($calls===$before+1,'Fehler wird kurz zwischengespeichert');
 $info=Updates::information(false,'plugin_information',(object)['slug'=>'mg-maintenance-studio']);verify($info->version===MGMS_VERSION && $info->download_link==='','Details bleiben bei Ausfall lokal verfügbar');
 echo "$count Updateprüfungen erfolgreich.\n";
} finally { if($old===false){delete_option('mgms_updates');}else{update_option('mgms_updates',$old);}delete_transient('mgms_release');delete_site_transient('update_plugins'); }
