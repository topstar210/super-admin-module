<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $CI  =&get_instance(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-sm-12 text-center" id="loading_img" style="display:none; position:absolute;z-index: 100000;">
                            <img src="https://i.gifer.com/4V0b.gif" class=""  alt="Loading" />
                        </div>
                        <?php echo form_open(admin_url('super_admin/savecompany'), array('class'=>'edit-company-form','autocomplete'=>'on')); ?>
                            <div class="bg-stripe mbot15">
                                <div class="form-group" app-field-wrapper="company_name"><label for="company_name"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Company Name</label>
                                    <input type="text" id="company_name" name="company" value="<?php echo $company->company; ?>" class="form-control"
                                        aria-invalid="true">
                                </div>
                                <div class="form-group" app-field-wrapper="domain"><label for="domain"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Domain Name</label>
                                    <input type="text" id="domain" name="domain" value="<?php echo $company->domain; ?>" readonly class="form-control"
                                        placeholder="company1.experteasegroup.com"
                                        aria-invalid="true">
                                </div>
                                <p class="mb-3 text-center"> Admin Setting</p>
                                <div class="form-group" app-field-wrapper="email_address"><label for="email_address"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Admin Email</label>
                                    <input type="email" id="email_address" name="admin_email" value="<?php echo $company->email; ?>" class="form-control"
                                        aria-invalid="true">
                                        
                                    <input type="hidden" id="staff_id" name="staff_id" value="<?php echo $company->staff_id; ?>">
                                </div>
                                <div class="form-group" app-field-wrapper="site_pwd"><label for="site_pwd"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Admin Password</label>
                                    <input type="password" id="site_pwd" name="admin_passwordr" class="form-control"
                                        aria-invalid="true">
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <a class="btn btn-info only-save customer-form-submiter" href="<?php echo admin_url('super_admin/index'); ?>"> Back To Page </a>
            <button type="button" class="btn btn-info edit-company customer-form-submiter">
                Edit Company Info
            </button>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(".edit-company").click(function(){
        $(".edit-company-form").submit();
    });
</script>