<?php
if(PHP_SAPI!=='cli'||getenv('MGMS_TEST')!=='isolated-local'){exit(1);}
$_SERVER['HTTP_HOST']='127.0.0.1:18865';
require '/var/www/html/wp-load.php';
$action=$argv[1]??'';
if($action==='reader') {
 $user=get_user_by('login','local-reader');$password=wp_generate_password(40,false);wp_set_password($password,$user->ID);
 file_put_contents('/tmp/mgms-reader.json',json_encode(['user'=>'local-reader','password'=>$password]));chmod('/tmp/mgms-reader.json',0600);
 update_option('mgms_enabled',true);
} elseif($action==='expire') {
 $admin=get_user_by('login','local-admin');$data=get_user_meta($admin->ID,'mgms_preview',true);$data['expires']=time()-1;update_user_meta($admin->ID,'mgms_preview',$data);
} elseif($action==='finish') {update_option('mgms_enabled',false);}
