<?php 
/**
* Super Admin control
*/
function db_select() {
    $curr = $_SERVER['SERVER_NAME'];
    if($curr != 'crm.experteasegroup.com'){
        $subdb = explode('.', $curr)[0];
        $dbname = 'main_crm_' . preg_replace('/[^a-zA-Z0-9]+/', '', $subdb);
        return $dbname;
    } else {
        return APP_DB_NAME;
    }
}

$curr = $_SERVER['SERVER_NAME'];
if($curr != 'crm.experteasegroup.com'){
    $httpstr = explode("://", APP_BASE_URL)[0];
    $config['base_url'] = $httpstr."://".$_SERVER['SERVER_NAME']."/";
}
