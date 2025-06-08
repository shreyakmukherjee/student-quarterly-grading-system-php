<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `assessment_list` where assessment_id = '{$_GET['id']}'");
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="assessment-form">
        <input type="hidden" name="id" value="<?php echo isset($assessment_id) ? $assessment_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="class_id" class="control-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select form-select-sm rounded-0 select2" required>
                            <option <?php echo (!isset($class_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $dept_qry = $conn->query("SELECT c.*, CONCAT(s.name , ' ' , c.grade , ' - ' , c.section) as class FROM `class_list` c inner join `subjects` s on c.subject_id = s.subject_id order by `class` asc");
                            while($row= $dept_qry->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['class_id'] ?>" <?php echo (isset($class_id) && $class_id == $row['class_id'] ) ? 'selected' : '' ?>><?php echo $row['class'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="component_id" class="control-label">Component</label>
                        <select name="component_id" id="component_id" class="form-select form-select-sm rounded-0 select2" required>
                            <option <?php echo (!isset($component_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $dept_qry = $conn->query("SELECT * FROM `grading_components` where delete_flag = 0 order by `name` asc");
                            while($row= $dept_qry->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['component_id'] ?>" <?php echo (isset($component_id) && $component_id == $row['component_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quarter" class="control-label">Quarter</label>
                        <select name="quarter" id="quarter" class="form-select form-select-sm rounded-0" required>
                            <option value="1" <?php echo (isset($quarter) && $quarter == 1)? 'selected' : '' ?>>First</option>
                            <option value="2" <?php echo (isset($quarter) && $quarter == 2)? 'selected' : '' ?>>Second</option>
                            <option value="3" <?php echo (isset($quarter) && $quarter == 3)? 'selected' : '' ?>>Third</option>
                            <option value="4" <?php echo (isset($quarter) && $quarter == 4)? 'selected' : '' ?>>Fourth</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" name="name" autofocus id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="hps" class="control-label">HPS</label>
                        <input type="number" step="any" name="hps" autofocus id="hps" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($hps) ? $hps : '' ?>">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#assessment-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_assessment',
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
                        if("<?php echo isset($assessment_id) ?>" != 1){
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