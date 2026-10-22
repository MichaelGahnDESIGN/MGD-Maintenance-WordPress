<?php
/** WordPress lädt uninstall.php nur einmal pro Request; deshalb getrennte Prozesse. */
if (PHP_SAPI!=='cli'||getenv('MGMS_TEST')!=='isolated-local') {exit(1);}
$_SERVER['HTTP_HOST']='127.0.0.1:18865';
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
$names=['mgms_draft','mgms_published','mgms_enabled','mgms_previous_draft','mgms_previous_published'];
$plugin='mg-maintenance-studio/mg-maintenance-studio.php';
$phase=$argv[1]??'';
if($phase==='preserve') {
 $snapshot=[];foreach($names as $name){$snapshot[$name]=get_option($name);}
 file_put_contents('/tmp/mgms-lifecycle-snapshot.json',json_encode($snapshot));
 deactivate_plugins($plugin);
 $draft=get_option('mgms_draft');$draft['delete_data']=false;update_option('mgms_draft',$draft);
 uninstall_plugin($plugin);
 if(get_option('mgms_draft')!==$draft){throw new RuntimeException('Ungewollte Datenlöschung');}
 echo "PASS Deinstallation ohne Löschfreigabe erhält Daten\n";
} elseif($phase==='delete') {
 $draft=get_option('mgms_draft');$draft['delete_data']=true;update_option('mgms_draft',$draft);
 uninstall_plugin($plugin);
 foreach($names as $name){if(get_option($name)!==false){throw new RuntimeException('Plugin-Daten nicht entfernt: '.$name);}}
 echo "PASS Deinstallation mit Löschfreigabe entfernt alle fünf Optionswerte\n";
} elseif($phase==='restore') {
 activate_plugin($plugin);
 if(get_option('mgms_enabled')){throw new RuntimeException('Wartung automatisch eingeschaltet');}
 echo "PASS Neuaktivierung nach Löschung startet inaktiv\n";
 $snapshot=json_decode(file_get_contents('/tmp/mgms-lifecycle-snapshot.json'),true);
 foreach($snapshot as $name=>$value){if($value!==false)update_option($name,$value,false);}
 echo "PASS Lokale Testdaten wiederhergestellt\n";
} else {exit(1);}
