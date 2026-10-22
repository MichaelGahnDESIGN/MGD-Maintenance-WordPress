<?php
/** Nur über CLI gegen die frische, isolierte Testdatenbank ausführen. */
if ( PHP_SAPI !== 'cli' || getenv('MGMS_TEST') !== 'isolated-local' ) { exit(1); }
$_SERVER['HTTP_HOST'] = '127.0.0.1:18865';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
use MGMS\IP;
use MGMS\Settings;
use MGMS\Schema;
use MGMS\Access;
$passed = 0;
function check($value,$message) { global $passed; if(!$value){throw new RuntimeException($message);} $passed++; echo "PASS $message\n"; }
check(!get_option(Settings::STATE),'Aktivierung schaltet Wartung nicht ein');
foreach ([['192.0.2.1','192.0.2.0/24',true],['192.0.3.1','192.0.2.0/24',false],['2001:db8::2','2001:db8::/64',true],['2001:db9::2','2001:db8::/64',false],['127.0.0.1','::1',false],['2001:db8::3','2001:db8::2/127',true],['192.0.2.129','192.0.2.0/25',false]] as [$ip,$cidr,$expected]) {check(IP::contains($ip,$cidr)===$expected,'CIDR '.$ip.' '.$cidr);}
foreach(['127.0.0.1/-1','127.0.0.1/33','::1/129','abc','127.0.0.1/24/2'] as $invalid){check(!IP::valid($invalid),'Ungültiges Netz abgewiesen');}
check(IP::detect(['REMOTE_ADDR'=>'192.0.2.4','HTTP_X_FORWARDED_FOR'=>'127.0.0.1'],'')==='192.0.2.4','Gefälschten Forwarded-Header ignoriert');
check(IP::detect(['REMOTE_ADDR'=>'192.0.2.4','HTTP_X_FORWARDED_FOR'=>'203.0.113.1, 192.0.2.5'],"192.0.2.4\n192.0.2.5")==='203.0.113.1','Vertrauenswürdige Proxy-Kette');
check(IP::detect(['REMOTE_ADDR'=>'192.0.2.4','HTTP_X_FORWARDED_FOR'=>'127.0.0.1, 203.0.113.1'],'192.0.2.4')==='203.0.113.1','Abbruch am ersten nicht vertrauten Proxy');
$input=Schema::defaults(); foreach(Schema::fields() as $k=>$f){if($f[1]==='checkbox')$input[$k]=$input[$k]?'1':'0';}
$sanitized=Settings::sanitize($input); check(is_array($sanitized),'Standardwerte validieren');
$bad=$input;$bad['ip_list']='0.0.0.0/0';check(is_wp_error(Settings::sanitize($bad)),'Zu breite Freigabe abgewiesen');
$bad=$input;$bad['contact_url']='javascript:alert(1)';check(is_wp_error(Settings::sanitize($bad)),'JavaScript-URL abgewiesen');
$bad=$input;$bad['background']='red;display:none';check(is_wp_error(Settings::sanitize($bad)),'CSS-Injektion abgewiesen');
$bad=$input;$bad['legal']=[['content'=>'<script>alert(1)</script><img src="https://example.invalid/x"><p>Text</p>']];$clean=Settings::sanitize($bad);check(!str_contains($clean['legal'][0]['content'],'<script')&&!str_contains($clean['legal'][0]['content'],'<img'),'Rechtstext enthält keine aktiven Fremdressourcen');
$admin=get_user_by('login','local-admin');wp_set_current_user($admin->ID);check(Access::user_allowed($sanitized),'Administrator-Ausnahme');
$sanitized['admin_bypass']=false;check(!Access::user_allowed($sanitized),'Administrator sieht Wartung bei ausgeschalteter Ausnahme');
$id=wp_create_user('local-reader',wp_generate_password(40),'reader@example.invalid');if(is_wp_error($id))$id=get_user_by('login','local-reader')->ID;
wp_set_current_user($id);check(!Access::user_allowed($sanitized),'Abonnent ohne Freigabe');
$sanitized['users']='all';check(Access::user_allowed($sanitized),'Eingeloggte Benutzer freigegeben');
$sanitized['users']='roles';$sanitized['roles']=['subscriber'];check(Access::user_allowed($sanitized),'Rollenfreigabe');
wp_set_current_user(0);check(!Access::user_allowed($sanitized),'Anonym ohne Benutzerfreigabe');
// Testdaten enthalten ausdrücklich keine Rechtstext-Vorlagen für den produktiven Einsatz.
$s=Schema::defaults();$s['legal']=[['title'=>'Test-Impressum','content'=>'<p>Ausschließlich lokaler Funktionstest.</p><a href="#main">Testlink</a>','enabled'=>true,'footer'=>true,'modal'=>true,'page'=>0,'order'=>0]];
update_option(Settings::DRAFT,$s); update_option(Settings::LIVE,$s); update_option(Settings::STATE,true);
deactivate_plugins('mg-maintenance-studio/mg-maintenance-studio.php');check(get_option(Settings::STATE)===true,'Deaktivierung bewahrt gespeicherten Status');
activate_plugin('mg-maintenance-studio/mg-maintenance-studio.php');check(get_option(Settings::STATE)===true,'Reaktivierung bewahrt gespeicherten Status');
$before=get_option(Settings::DRAFT);update_option('mgms_previous_draft',$before);$changed=$before;$changed['title']='Rollback-Test';update_option(Settings::DRAFT,$changed);update_option(Settings::DRAFT,get_option('mgms_previous_draft'));check(get_option(Settings::DRAFT)===$before,'Datenbank-Rollback stellt Entwurf exakt wieder her');
echo "TOTAL $passed\n";
