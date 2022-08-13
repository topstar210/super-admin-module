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

                        <?php echo form_open(admin_url('super_admin/addcompany'), array('class'=>'add-company-form','autocomplete'=>'on')); ?>
                            <div class="bg-stripe mbot15">
                                <div class="text-center" style="font-size: 18px;">
                                    Main Domain: <span>crm.experteasegroup.com</span>  <br />
                                    <small style="color:#fb717e">Confirm the Database and Subdomain to create company!</small>
                                    <hr />
                                    <input type="hidden" name="main_domain" value="crm.experteasegroup.com" />
                                </div>
                                <div class="form-group" app-field-wrapper="company_name"><label for="company_name"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Company Name</label>
                                    <input type="text" id="company_name" name="company" class="form-control"
                                        aria-invalid="true">
                                </div>
                                <div class="form-group" app-field-wrapper="domain"><label for="domain"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Domain Name</label>
                                    <input type="text" id="domain" name="domain" class="form-control"
                                        placeholder="company1.experteasegroup.com"
                                        aria-invalid="true">
                                </div>
                                <p class="mb-3 text-center"> Admin Setting</p>
                                <div class="form-group" app-field-wrapper="first_name"><label for="first_name"
                                        class="control-label">First Name</label>
                                    <input type="text" id="first_name" name="firstname" class="form-control"
                                        aria-invalid="true">
                                </div>
                                <div class="form-group" app-field-wrapper="last_name"><label for="last_name"
                                        class="control-label">Last Name</label>
                                    <input type="text" id="last_name" name="lastname" class="form-control"
                                        aria-invalid="true">
                                </div>
                                <div class="form-group" app-field-wrapper="email_address"><label for="email_address"
                                        class="control-label">
                                        <small class="req text-danger">*
                                        </small>Admin Email</label>
                                    <input type="email" id="email_address" name="admin_email" class="form-control"
                                        aria-invalid="true">
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
            <button type="button" class="btn btn-info create-company-crm customer-form-submiter">
                Create Company's CRM    
            </button>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(".create-company-crm").click(function(){
        if(validate_form()) return;

        $("#loading_img").show();
        $(this).attr("disabled", "desabled");
        $(".add-company-form").submit();
    });
    function validate_form(){
        $domainVal = $("#domain").val();
        if($("#company_name").val() == ""){
            alert_float('danger', "Don't enter your Company Name");
            $("#company_name").focus(); return true;
        } else if ($domainVal == "") {
            alert_float('danger', "Don't enter your Domain Name");
            $("#domain").focus(); return true;
        } else if ($("#email_address").val() == ""){
            alert_float('danger', "Don't enter your Email");
            $("#email_address").focus(); return true;
        } else if ($("#password").val() == ""){
            alert_float('danger', "Don't enter your Password");
            $("#password").focus(); return true;
        }
        if(!validURL($domainVal)) {
            alert_float('danger', "Invalid Domain");
            $("#domain").focus(); return true;
        }
        return false;
    }
    function validURL(str) {
        var res = str.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
        return (res !== null)
    }
</script>