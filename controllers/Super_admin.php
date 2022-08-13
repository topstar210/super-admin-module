<?php
@ini_set('max_execution_time', 240);

use PhpOffice\PhpSpreadsheet\Reader\Xls\MD5;

defined('BASEPATH') or exit('No direct script access allowed');

class Super_admin extends AdminController
{
    private $ci;

    /**
     * The modules info that is stored in database
     * @var array
     */
    private $db_modules = [];

    /**
     * All valid modules
     * @var array
     */
    private $modules = [];

    /**
     * All activated modules
     * @var array
     */
    private $active_modules = [];

    /**
     * Sub domain dbname
     * @var string
     */
    private $dbname = '';

    /**
     * Sub domain link
     * @var object
     */
    private $sublink = null;

    public function __construct()
    {
        parent::__construct();
        $this->ci = &get_instance();
        $this->ci->load->helper('directory');
        $this->load->model('super_admin_model');
    }

    public function index($module = null)
    {
        $db = $this->input->get("p");
        
        if ($db != "") {
            $this->make_sub_link($db);
            $this->initializeModules();
            if (!$module) {
                $modules = $this->modules;

                /* Sort modules by name */
                usort($modules, function ($a, $b) {
                    return strcmp(strtolower($a['headers']['module_name']), strtolower($b['headers']['module_name']));
                });
            }

            if (isset($this->modules[$module])) {
                $modules = $this->modules[$module];
            }
        }
        if (!isset($modules)) $modules = [];
        $data['companies'] = $this->db->get(db_prefix() . 'super_admin')->result_array();
        $data['title'] = "Super Admin";
        $data['modules'] = $modules;
        $this->load->view('manage', $data);
    }

    public function newcompany()
    {
        $data['title'] = "Add Company";
        $this->load->view('add_company', $data);
    }

    public function addcompany()
    {
        $maindomain = $_POST['main_domain'];
        $subdomain = $_POST['domain'];
        $db = explode(".", $subdomain)[0];
        $dbname = "main_crm_" . preg_replace("/[^a-zA-Z0-9]+/", "", $db);
        $this->dbname = $dbname;
        
        include_once(__DIR__ . '/sqlparser.php');
        $parser = new SqlScriptParser();
        $sqlStatements = $parser->parse(__DIR__ . '/database.sql');
        
        $this->db->query("CREATE DATABASE IF NOT EXISTS `".$this->dbname."`;");
        $this->make_sub_link($db);

        foreach ($sqlStatements as $statement) {
            $distilled = $parser->removeComments($statement);
            // $completedQuery = str_replace('`tbl', '`' . $prefix, $distilled);
            if (!empty($distilled)) {
                $this->sublink->query($distilled);
            }
        }

        include_once(__DIR__ . '/phpass.php');
        $hasher    = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $password  = $hasher->HashPassword($_POST['admin_passwordr']);
        $email     = $this->sublink->escape_string($_POST['admin_email']);
        $firstname = $this->sublink->escape_string($_POST['firstname']);
        $lastname  = $this->sublink->escape_string($_POST['lastname']);
        $datecreated = date('Y-m-d H:i:s');

        $di  = time();
        $sql = "UPDATE `tbloptions` SET value='$di' WHERE name='di'";
        $this->sublink->query($sql);

        $sql = "INSERT INTO `tblstaff` 
            (`firstname`, `lastname`, `password`, `email`, `datecreated`, `admin`, `active`) 
            VALUES('$firstname', '$lastname', '$password', '$email', '$datecreated', 1, 1)";
        $this->sublink->query($sql);
        $last_id = $this->sublink->insert_id;;

        $data = array(
            'company' => $_POST['company'],
            'domain' => $_POST['domain'],
            'email' => $email,
            'db_name' => $this->dbname,
            'staff_id' => $last_id,
            'password' => $password,
            'created_at' => $datecreated
        );
        $this->db->insert('tblsuper_admin', $data);
        
        set_alert('success', _l('Inserted', $_POST['company'] . " Company"));
        redirect(admin_url('super_admin/index'));
    }

    public function activemodule()
    {
        $name = $this->input->post('modulename');
        $is_act = $this->input->post('is_act') * 1;
        $db = $this->input->post('db');
        $this->make_sub_link($db);

        $installed_version = $this->input->post('installed_version');
        $active = $is_act ? 0 : 1;

        $checkrow = $this->sublink->query("SELECT * from `tblmodules` where module_name = '" . $name . "'")->fetch_assoc();

        if (!$checkrow) {
            $this->sublink->query("INSERT INTO `tblmodules` 
                (`module_name`,`active`,`installed_version`) 
                VALUES 
                ('{$name}','{$active}','{$installed_version}')");
        } else {
            $sql = "UPDATE `tblmodules` SET active = " . $active . " WHERE module_name = '" . $name . "'";
            $this->sublink->query($sql);
        }
        if ($is_act == 0) {
            $superTb = db_prefix().str_replace("Perfex_","_",$name);;
            if ($this->db->table_exists($superTb)){
                $result = $this->sublink->query("SHOW TABLES LIKE '".db_prefix().$name."'");
                if($result->num_rows != 1){
                    $this->db->query("CREATE TABLE `".$this->dbname."`.`".$superTb."` AS (SELECT * FROM `".APP_DB_NAME."`.`".$superTb."`)");
                }
            }
        }
        exit("OK");
    }

    public function editcompany()
    {
        $rid = $this->input->get("r");
        $this->db->where('id', $rid);
        $data['company'] = $this->db->get(db_prefix() . 'super_admin')->row();
        $data['title'] = "Edit Company";
        $this->load->view('edit_company', $data);
    }

    public function savecompany()
    {
        $db = explode(".", $_POST['domain'])[0];
        $this->make_sub_link($db);

        include_once(__DIR__ . '/phpass.php');
        $hasher    = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $password  = $hasher->HashPassword($_POST['admin_passwordr']);
        $email     = $_POST['admin_email'];

        $sql = "UPDATE `".$this->dbname."`.`tblstaff`
                    SET `password` = '$password', 
                        `email` = '$email' 
                        WHERE staffid=" . $_POST['staff_id'];
        $this->db->query($sql);

        $data = array(
            'company' => $_POST['company'],
            'email' => $email,
            'password' => $password,
        );
        $this->db->update(db_prefix() . 'super_admin', $data);

        set_alert('success', _l('Updated', "Company Info"));
        redirect(admin_url('super_admin/index'));
    }

    public function deletecompany()
    {
        $db = $this->input->post("db");
        $this->make_sub_link($db);
        $id = $this->input->post("rowid");

        $this->db->query("Drop Database `".$this->dbname."`");

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'super_admin');

        set_alert('success', _l('deleted', "Company"));
        redirect(admin_url('super_admin/index'));
    }


    /**
     * InitializeModules all modules
     * @return null
     */
    public function initializeModules()
    {
        // For caching
        $this->query_db_modules();

        foreach (static::get_valid_modules() as $module) {
            $name = $module['name'];
            // If the module hasn't already been added and isn't a file
            if (!isset($this->modules[$name])) {
                /**
                 * System name
                 */
                $this->modules[$name]['system_name'] = $name;

                /**
                 * Module headers
                 */
                $this->modules[$name]['headers'] = $this->get_headers($module['init_file']);
                /**
                 * Init file path
                 * The file name must be the same like the module folder name
                 */
                $this->modules[$name]['init_file'] = $module['init_file'];
                /**
                 * Module path
                 */
                $this->modules[$name]['path'] = $module['path'];

                // Check if module is activated
                $moduleDB = $this->get_database_module($name);

                if ($moduleDB && $moduleDB['active'] == 1) {
                    $this->modules[$name]['activated'] = 1;
                    // Add to active modules handler
                    $this->active_modules[$name] = $this->modules[$name];
                } else {
                    $this->modules[$name]['activated'] = 0;
                }
                /**
                 * Installed version
                 */
                $this->modules[$name]['installed_version'] = $moduleDB ? $moduleDB['installed_version'] : false;
            }
        }
    }

    /**
     * Get module headers info
     * @param  string $module_source the module init file location
     * @return array
     */
    public function get_headers($module_source)
    {
        $module_data = read_file($module_source); // Read the module init file.

        preg_match('|Module Name:(.*)$|mi', $module_data, $name);
        preg_match('|Module URI:(.*)$|mi', $module_data, $uri);
        preg_match('|Version:(.*)|i', $module_data, $version);
        preg_match('|Description:(.*)$|mi', $module_data, $description);
        preg_match('|Author:(.*)$|mi', $module_data, $author_name);
        preg_match('|Author URI:(.*)$|mi', $module_data, $author_uri);
        preg_match('|Requires at least:(.*)$|mi', $module_data, $requires_at_least);

        $arr = [];

        if (isset($name[1])) {
            $arr['module_name'] = trim($name[1]);
        }

        if (isset($uri[1])) {
            $arr['uri'] = trim($uri[1]);
        }

        if (isset($version[1])) {
            $arr['version'] = trim($version[1]);
        } else {
            $arr['version'] = 0;
        }

        if (isset($description[1])) {
            $arr['description'] = trim($description[1]);
        }

        if (isset($author_name[1])) {
            $arr['author'] = trim($author_name[1]);
        }

        if (isset($author_uri[1])) {
            $arr['author_uri'] = trim($author_uri[1]);
        }

        if (isset($requires_at_least[1])) {
            $arr['requires_at_least'] = trim($requires_at_least[1]);
        }

        return $arr;
    }

    /**
     * Get valid modules
     * @return array
     */
    public static function get_valid_modules()
    {
        /**
         * Modules path
         *
         * APP_MODULES_PATH constant is defined in application/config/constants.php
         *
         * @var array
         */
        $modules = directory_map(APP_MODULES_PATH, 1);

        $valid_modules = [];

        if ($modules) {
            foreach ($modules as $name) {
                $name = strtolower(trim($name));

                /**
                 * Filename may be returned like chat/ or chat\ from the directory_map function
                 */
                foreach (['\\', '/'] as $trim) {
                    $name = rtrim($name, $trim);
                }

                // If the module hasn't already been added and isn't a file
                if (!stripos($name, '.')) {
                    $module_path = APP_MODULES_PATH . $name . '/';
                    $init_file   = $module_path . $name . '.php';

                    // Make sure a valid module file by the same name as the folder exists
                    if (file_exists($init_file)) {
                        $valid_modules[] = [
                            'init_file' => $init_file,
                            'name'      => $name,
                            'path'      => $module_path,
                        ];
                    }
                }
            }
        }

        return $valid_modules;
    }

    
    /**
     * Get module from database
     * @param  string $name module system name
     * @return mixed
     */
    public function get_database_module($name)
    {
        if (isset($this->db_modules[$name])) {
            return $this->db_modules[$name];
        }

        $sql = "select * from `tblmodules` where module_name='" . $name . "'";

        return $this->sublink->query($sql)->fetch_assoc();
    }

    private function query_db_modules()
    {
        $sql = "select * from `tblmodules`";
        $db_modules = $this->sublink->query($sql);

        if ($db_modules->num_rows > 0) {
            while($row = $db_modules->fetch_assoc()) {
                $this->db_modules[$row['module_name']] = $row;
            }
        } else {
            $this->db_modules = [];
        }
    }

    private function make_sub_link($db){
        $dbname = "main_crm_" . preg_replace("/[^a-zA-Z0-9]+/", "", $db);
        $this->dbname = $dbname;
        
        $h = trim(APP_DB_HOSTNAME);
        $u = trim(APP_DB_USERNAME);
        $p = trim(APP_DB_PASSWORD);
        $d = trim($dbname);
        try {
            $this->sublink = new mysqli($h, $u, $p, $d);
        } catch (Exception $e) {
        }
    }
}
