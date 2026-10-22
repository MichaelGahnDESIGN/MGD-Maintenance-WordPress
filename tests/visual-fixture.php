<?php
if(PHP_SAPI!=='cli'||getenv('MGMS_TEST')!=='isolated-local'){exit(1);}
$_SERVER['HTTP_HOST']='127.0.0.1:18865';
require '/var/www/html/wp-load.php';
$action=$argv[1]??'';
if($action==='save') {file_put_contents('/tmp/mgms-visual-state.json',json_encode(['live'=>get_option('mgms_published'),'enabled'=>get_option('mgms_enabled')]));}
elseif($action==='restore') {$state=json_decode(file_get_contents('/tmp/mgms-visual-state.json'),true);update_option('mgms_published',$state['live']);update_option('mgms_enabled',$state['enabled']);}
elseif(in_array($action,['elegant-gold','ruhiges-atelier','minimalistisch-hell'],true)) {
 $s=MGMS\Schema::defaults();$preset=json_decode(file_get_contents(MGMS_DIR.'presets/'.$action.'.json'),true);$s=array_replace($s,$preset['values']);$s['preset']=$action;update_option('mgms_published',$s);update_option('mgms_enabled',true);
}else{exit(1);}
