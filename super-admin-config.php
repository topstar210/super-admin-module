<?php 
/**
* Super Admin control
*/
define("SUPER_ADMIN_MAIL","@gmail.com");   // super admin email
define("ROOT_DOMAIN",".com");        // maindomain of subdomain
define("DOCUMENT_ROOT","/perfex_crm");     // subdomain root directory
define("CPN_USER","");                  // cpanel user
define("CPN_PWD","");     // cpanel password

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
