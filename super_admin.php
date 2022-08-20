<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: SaaS - Super Admin
Description: Allows Perfex to become a Multi Tenant / SaaS Application.
Version: 1.0
Requires at least: 1.0
*/

ini_set('display_errors', 1);
hooks()->add_action('admin_init', 'super_admin_init_menu_items');

function super_admin_init_menu_items(){
    $CI  =&get_instance();
    if (!$CI->db->table_exists(db_prefix() . 'super_admin')) {
        require_once(__DIR__ . '/install.php');
    }
    
    $myId = $CI->session->userdata("staff_user_id");
    $myInfo = $CI->db->query("select `firstname`, `lastname`, `email` from `tblstaff` where staffid='$myId'")->row();
    $myMail = $myInfo->email;
    
    $sql = "SELECT * from `" . db_prefix() . "modules` where module_name = 'super_admin'";
    $checkrow = $CI->db->query($sql)->row();
    if($checkrow && SUPER_ADMIN_MAIL==$myMail) {
        $CI->app_menu->add_sidebar_menu_item('super_admin', [
            'name'     => 'Super Admin', // The name if the item
            'href'     => site_url('super_admin/index'), // URL of the item
            'position' => 2, // The menu position, see below for default positions.
            'icon'     => 'fa fa-sitemap', // Font awesome icon
        ]);
    }
}

/**
* Register activation module hook
*/
register_activation_hook('super_admin', 'super_admin_module_activation_hook');

function super_admin_module_activation_hook()
{
    $path = __DIR__.'/../../application/config/super-admin-config.php';
    if (file_exists($path)) {
       unlink($path);
    }
    require_once(__DIR__ . '/install.php');
}
