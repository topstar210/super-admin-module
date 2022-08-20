<?php 
/**
* Super Admin control
*/
define("SUPER_ADMIN_MAIL","happycamper0210@gmail.com");   // super admin email
define("ROOT_DOMAIN","experteasegroup.com");        // maindomain of subdomain
define("DOCUMENT_ROOT","developer/perfex_crm");     // subdomain root directory
define("CPN_USER","main");                  // cpanel user
define("CPN_PWD","5w9DyWMK38VwJVyXFr");     // cpanel password

function db_select() {
    $curr = $_SERVER['SERVER_NAME'];
    if($curr != 'dev.'.ROOT_DOMAIN){
        $subdb = explode('.', $curr)[0];
        $dbname = 'main_crm_' . preg_replace('/[^a-zA-Z0-9]+/', '', $subdb);
        return $dbname;
    } else {
        return APP_DB_NAME;
    }
}

$curr = $_SERVER['SERVER_NAME'];
if($curr != 'dev.'.ROOT_DOMAIN){
    $httpstr = explode("://", APP_BASE_URL)[0];
    $config['base_url'] = $httpstr."://".$_SERVER['SERVER_NAME']."/";
}
