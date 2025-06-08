<?php
require_once("DBConnection.php");
$qry = $conn->query("SELECT * FROM `admin_list` where admin_id = '{$_SESSION['admin_id']}'");
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v;
    }
?>
<div class="card rounded-0 shadow">
    <div class="card-header">
        <h3 class="card-title">Manage Account</h3>
    </div>
    <div class="card-body">
        <div class="col-md-6">
            <form action="" id="user-form">
                <input type="hidden" name="id" value="<?php echo isset($admin_id) ? $admin_id : '' ?>">
                <div class="form-group">
                    <label for="fullname" class="control-label">Full Name</label>
                    <input type="text" name="fullname" id="fullname" required class="form-control rounded-0" value="<?php echo isset($fullname) ? $fullname : '' ?>">
                </div>
                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" name="username" id="username" required class="form-control rounded-0" value="<?php echo isset($username) ? $username : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">New Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="password" id="password" class="form-control form-control-sm rounded-0" value="">
                        <div class="input-group-append"><button type="button" class="pass_view border rounded-0 btn btn-outline" tabindex="-1"><i class="fa fa-eye-slash"></i></button></div>
                    </div>
                </div>
                <div class="form-group">
                    <small class="text-muted"><i>Leave the New Password field blank if you don't want update your password.</i></small>
                </div>
                <div class="form-group">
                    <label for="old_password" class="control-label">Old Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="old_password" id="old_password" class="form-control form-control-sm rounded-0" value="" required>
                        <div class="input-group-append"><button type="button" class="pass_view border rounded-0 btn btn-outline" tabindex="-1"><i class="fa fa-eye-slash"></i></button></div>
                    </div>
                </div>
                <div class="clear-fix mb-2"></div>
                <div class="form-group d-flex w-100 justify-content-end">
                    <button class="btn btn-sm btn-primary rounded-0 my-1">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(function(){
        $('.pass_view').click(function(){
            var _parent = $(this).closest('.input-group')
            var _type = _parent.find('input').attr('type')
            $(this).html('')
            console.log(_type)
            if(_type == 'password'){
                $(this).html('<i class="fa fa-eye"></i>')
                _parent.find('input').attr('type','text').focus()
            }else{
                $(this).html('<i class="fa fa-eye-slash"></i>')
                _parent.find('input').attr('type','password').focus()
            }
        })
        $('#user-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=update_credentials',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                            location.reload()
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>