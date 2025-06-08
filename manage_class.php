<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `class_list` where class_id = '{$_GET['id']}'");
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="class-form">
        <input type="hidden" name="id" value="<?php echo isset($class_id) ? $class_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="subject_id" class="control-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select form-select-sm rounded-0 select2" required>
                            <option <?php echo (!isset($subject_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $subj_qry = $conn->query("SELECT * FROM subjects where delete_flag = 0 ".(isset($subject_id) ? " or subject_id = '{$subject_id}' " : "")." order by `name` asc");
                            while($row= $subj_qry->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['subject_id'] ?>" <?php echo (isset($subject_id) && $subject_id == $row['subject_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grade" class="control-label">Grade</label>
                        <input type="text" name="grade" autofocus id="grade" required class="form-control form-control-sm rounded-0" value="<?php echo isset($grade) ? $grade : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="section" class="control-label">Section</label>
                        <input type="text" name="section" autofocus id="section" required class="form-control form-control-sm rounded-0" value="<?php echo isset($section) ? $section : '' ?>">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#class-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_class',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
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
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($class_id) ?>" != 1){
                            _this.get(0).reset();
                            $('.select2').val('').trigger('change')
                        }
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