<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .company-hover{ cursor: pointer !important; }
    .company-selected{ background: #b8c1cd; }
</style>
<script>
    var db = "<?php echo $this->input->get('p'); ?>";
</script>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="col-sm-12 text-center" id="loading_img" style="display:none; position:absolute;z-index: 100000;">
                        <img src="https://i.gifer.com/4V0b.gif" class=""  alt="Loading" />
                    </div>
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('super_admin/newcompany'); ?>" class="btn btn-info pull-left display-block">New Company</a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Site Url</th>
                                    <th>Admin Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $trs = '';
                                    if(count($companies) > 0){
                                        foreach($companies as $company){
                                            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                                            $site_url = $http."://" . $company['domain'].".".ROOT_DOMAIN;
                                            $db = explode(".", $company['domain'])[0];
                                            $trs .= '<tr rid="'.$company['id'].'" class="company_row">';
                                                $trs .= '<td>'.$company['company'].'</td>';
                                                $trs .= '<td target="domain" db="'.$db.'"><a href="'.$site_url.'" target="_blank">
                                                    '.$company['domain'].".".ROOT_DOMAIN.'
                                                </a></td>';
                                                $trs .= '<td>'.$company['email'].'</td>';
                                                $trs .= '<td class="del-com">
                                                            <a href="javascript:void(0);" class="company-edit"><i class="fa fa-edit"></i></a>
                                                            / 
                                                            <a href="javascript:void(0);" class="company-delete"><i class="fa fa-remove"></i></a>
                                                        </td>';
                                            $trs .= '</tr>';
                                        }
                                    }
                                    echo $trs;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row text-center">
                            <p class="" style="font-size:18px">Module Manager</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-type="asc" data-order-col="0">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _l('module'); ?>
                                        </th>
                                        <th>
                                            <?php echo _l('module_description'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module) {
                                        $system_name = $module['system_name'];
                                        ?>
                                        <tr class="<?php if($module['activated'] === 1){echo 'alert-info';} ?>">
                                            <td data-order="<?php echo $system_name; ?>">
                                                <p>
                                                    <b>
                                                        <?php echo $module['headers']['module_name']; ?>
                                                    </b>
                                                </p>
                                                <?php
                                                $action_links = [];
                                                // $versionRequirementMet = $this->app_modules->is_minimum_version_requirement_met($system_name);
                                                $action_links = hooks()->apply_filters("module_{$system_name}_action_links", $action_links);

                                                if($module['activated'] === 0) {
                                                    array_unshift($action_links, '<a href="javascript:void(0);" is_act="'.$module['activated'].'" class="moduleActiveCtrl" ins_ver="'.$module['headers']['version'].'" modulename="'.$system_name.'">' . _l('module_activate') . '</a>');
                                                }
    
                                                if($module['activated'] === 1){
                                                    array_unshift($action_links, '<a href="javascript:void(0);" is_act="'.$module['activated'].'" class="moduleActiveCtrl" ins_ver="'.$module['headers']['version'].'" modulename="'.$system_name.'">' . _l('module_deactivate') . '</a>');
                                                }
    
                                                echo implode('&nbsp;|&nbsp;', $action_links);
                                                ?>
                                            </td>
                                            <td>
                                                <p>
                                                    <?php echo isset($module['headers']['description']) ? $module['headers']['description'] : ''; ?>
                                                </p>
                                                <?php

                                                $module_description_info = [];
                                                hooks()->apply_filters("module_{$system_name}_description_info", $module_description_info);

                                                if (isset($module['headers']['author'])) {
                                                    $author = $module['headers']['author'];
                                                    if (isset($module['headers']['author_uri'])) {
                                                        $author = '<a href="' . $module['headers']['author_uri'] . '">' . $author . '</a>';
                                                    }
                                                    array_unshift($module_description_info, _l('module_by', $author));
                                                }

                                                array_unshift($module_description_info, _l('module_version', $module['headers']['version']));
                                                echo implode('&nbsp;|&nbsp;', $module_description_info); ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_open(admin_url('super_admin/deletecompany'), array('class'=>'delete-company-form','autocomplete'=>'off')); ?>
                <input type="hidden" name="db" id="db" />
                <input type="hidden" name="rowid" id="rowid" />
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        if(db !== '') {
            $("td[db="+db+"]").parent().addClass("company-selected");
        }       

        $(document).on("mouseover", "tr.company_row", function(e){
            $("tr.company_row").removeClass("company-hover");
            $(this).addClass("company-hover");
        });
    
        $(document).on("click", "tr.company_row td:eq(0)", function(e){
            $("tr.company_row").removeClass("company-selected");
            $(this).parent().addClass("company-selected");
            var db = $(this).parent().find("td[target=domain]").attr('db').trim();
            
            location.href="<?php echo admin_url('super_admin/index?p='); ?>" + db;
        });
        
        $(document).on("click", ".moduleActiveCtrl", function(e){
            var __self = $(this);
            var modulename = __self.attr("modulename");
            var is_act = __self.attr("is_act")*1;
            var installed_version = __self.attr("ins_ver");
            $.post('<?php echo admin_url('super_admin/activemodule'); ?>',{
                modulename,
                is_act,
                installed_version,
                db
            }, function(res){
                if(is_act == 0) {
                    __self.attr('is_act', 1);
                    __self.text('Deactivate');
                } else {
                    __self.attr('is_act', 0);
                    __self.text('Activate');
                }
            })
        });

        $(".company-delete").click(function(){
            var selectedDomain = $(this).parent().siblings("td[target=domain]");
            if(!confirm("Are you going to delete the company(domain: "+selectedDomain.text()+")?")) return;
            var db = selectedDomain.attr('db').trim();
            var rowid = selectedDomain.parents("tr").attr("rid");
            $("#db").val(db);
            $("#rowid").val(rowid);
            $("#loading_img").show();
            $(".delete-company-form").submit();
        });
        $(".company-edit").click(function() {
            var selectedDomain = $(this).parent().siblings("td[target=domain]");
            var rowid = selectedDomain.parents("tr").attr("rid");
            location.href="<?php echo admin_url('super_admin/editcompany?r='); ?>" + rowid;
        })
    });
</script>
</body>

</html>