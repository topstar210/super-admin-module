<?php

defined('BASEPATH') or exit('No direct script access allowed');
$CI  =&get_instance();

// $CI->db->query("DROP TABLE IF EXISTS `".db_prefix()."super_admin`");
if (!$CI->db->table_exists(db_prefix() . 'super_admin')) {
    $query = 'CREATE TABLE `' . db_prefix() . "super_admin` (
        `id` int NOT NULL AUTO_INCREMENT,
        `company` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        `db_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        `staff_id` int NOT NULL,
        `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        `password` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        `active` int NOT NULL,
        `created_at` datetime(0) NOT NULL,
        PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';';
    $CI->db->query($query);
}

if (!file_exists(__DIR__.'/../../application/config/super-admin-config.php')) 
{
    if (@copy(__DIR__.'/super-admin-config.php', __DIR__.'/../../application/config/super-admin-config.php') == true) {
        
        $config_path    = __DIR__.'/../../application/config/config.php';
        @chmod($config_path, FILE_WRITE_MODE);
        $config_file = file_get_contents($config_path);
        
        $pos = strpos($config_file, "super admin config");
        if ($pos === false){
            $config_file .= "
            /*
            ** super admin config path start
            */
            include_once(APPPATH . 'config/super-admin-config.php');
            /* super admin config path end
            */
            ";
        }
        if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
            return false;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $config_file, strlen($config_file));
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($config_path, FILE_READ_MODE);
    
    
        $database_config_path    = __DIR__.'/../../application/config/database.php';
        @chmod($database_config_path, FILE_WRITE_MODE);
        $config_file = file_get_contents($database_config_path);
        $config_file = str_replace('APP_DB_NAME', 'db_select()', $config_file);
    
        if (!$fp = fopen($database_config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
            return false;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $config_file, strlen($config_file));
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($database_config_path, FILE_READ_MODE);
    }   
}