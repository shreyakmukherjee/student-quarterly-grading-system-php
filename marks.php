<div class="card rounded-0 shadow">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Class Assessment Marks</h3>
    </div>
    <div class="card-body">
        <form action="" id="marks-form">
            <div class="col-12">
                <div class="row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="class_id" class="control-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select form-select-sm select2 rounded-0" required>
                            <option <?php echo (!isset($class_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $class_qry = $conn->query("SELECT c.*, CONCAT(s.name , ' ' , c.grade , ' - ' , c.section) as class FROM `class_list` c inner join `subjects` s on c.subject_id = s.subject_id order by `class` asc");
                            while($row= $class_qry->fetch_assoc()):
                                $class[$row['class_id']] = $row['class'];
                            ?>
                                <option <?php echo isset($_GET['class_id']) && $_GET['class_id'] == $row['class_id'] ? "selected" : '' ?> value="<?php echo $row['class_id'] ?>"><?php echo $row['class'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="assessment_id" class="control-label">Assessment</label>
                        <select name="assessment_id" id="assessment_id" class="form-select form-select-sm select2 rounded-0" required>
                            <option <?php echo (!isset($assessment_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $dept_qry = $conn->query("SELECT * FROM assessment_list order by quarter asc, `name` asc");
                            $quarter = array('','First','Second','Third','Fourth');
                            while($row= $dept_qry->fetch_assoc()):
                                $assess_quarter[$row['assessment_id']] = $quarter[$row['quarter']];
                                $assess[$row['assessment_id']] = $row;
                            ?>
                                <option style="display:none" value="<?php echo $row['assessment_id'] ?>" data-class="<?php echo $row['class_id'] ?>" class="item" <?php echo isset($_GET['assessment_id']) && $_GET['assessment_id'] == $row['assessment_id'] ? "selected" : '' ?>><?php echo $quarter[$row['quarter']].' - '.$row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <button class="btn-sm btn btn-primary rounded-0" type="button" id="filter">Filter</button>
                    </div>
                </div>
                <hr>
                <?php if(isset($_GET['class_id']) && $_GET['assessment_id']): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="w-100 d-flex">
                            <div class="col-auto pe-1">Class: </div>
                            <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                <?php echo isset($class[$_GET['class_id']]) ? $class[$_GET['class_id']] : "N/A" ?>
                            </div>
                        </div>
                        <div class="w-100 d-flex">
                            <div class="col-auto pe-1">Quarter: </div>
                            <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                <?php echo isset($assess_quarter[$_GET['assessment_id']]) ? $assess_quarter[$_GET['assessment_id']] : "N/A" ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="w-100 d-flex">
                            <div class="col-auto pe-1">Assessment: </div>
                            <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                <?php echo isset($assess[$_GET['assessment_id']]) ? $assess[$_GET['assessment_id']]['name'] : "N/A" ?>
                            </div>
                        </div>
                        <div class="w-100 d-flex">
                            <div class="col-auto pe-1">Highest Possible Score: </div>
                            <div class="col-auto flex-grow-1 text-center border-bottom border-dark" id="hps">
                                <?php echo isset($assess[$_GET['assessment_id']]) ? $assess[$_GET['assessment_id']]['hps'] : 0 ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 py-3">
                        <table class="table table-striped table-hover">
                            <colgroup>
                            <col width="50%">
                            <col width="50%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="px-1 py-0">Student</th>
                                    <th class="px-1 py-0">Mark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $marks_qry = $conn->query("SELECT * FROM `mark_list` where assessment_id='{$_GET['assessment_id']}'");
                                    $marks = array();
                                    while($row = $marks_qry->fetch_assoc()){
                                        $marks[$row['student_id']] = $row['mark'];
                                    }
                                    $student = $conn->query("SELECT * FROM `student_list` where class_id = '{$_GET['class_id']}' order by `name` asc");
                                    while($row = $student->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="px-1py-0">
                                        <input type="hidden" name="student_id[]" value="<?php echo $row['student_id'] ?>">
                                        <?php echo $row['name'] ?>
                                    </td>
                                    <td>
                                    <input type="number" step="any" class="form-control form-control-sm rounded-0 w-100 text-end" name="mark[]" value="<?php echo (isset($marks[$row['student_id']])) ? $marks[$row['student_id']] : 0 ?>" required>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($student->num_rows <= 0): ?>
                                <tr><th colspan="2"><center><i><b>No Student listed yet.</b></i></center></th></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                        <div class="form-group">
                            <center>
                                <button class="btn btn-sm btn-primary rounded-0">Save</button>
                            </center>
                        </div>
                </div>
                <?php endif; ?>
            </div>
            </form>
    </div>
</div>


<script>
    $(function(){
        $('#class_id').change(function(){
            var class_id = $(this).val()
            $('#assessment_id option.item').hide()
            $('#assessment_id option.item[data-class="'+class_id+'"]').show()
        })
        $('#filter').click(function(){
            var class_id = $('#class_id').val()
            var assessment_id = $('#assessment_id').val()
            if(class_id <= 0 || assessment_id <= 0){
                alert("Please select class and assessment first.")
            }else{
                location.href = "./?page=marks&class_id="+class_id+"&assessment_id="+assessment_id;
            }
        })
        if('<?php echo isset($_GET['class_id']) && isset($_GET['assessment_id']) ?>' == 1){
            $('#class_id').trigger('change')
        $('#marks-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var hps = parseFloat($('#hps').text())
            if($('input[name="mark[]"]').length <= 0 ){
                alert("No Student is listed yet in selected class.")
                return false;
            }
            $('input[name="mark[]"]').removeClass('border-danger')
            $('input[name="mark[]"]').each(function(){
                if(parseFloat($(this).val()) > hps){
                    $(this).addClass('border-danger')
                }
            })
            if($('input.border-danger[name="mark[]"]').length > 0){
                alert("Entered mark is greater than Heighest Possible Score.")
                return false;
            }
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_mark',
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
                     _this.find('button').attr('disabled',false)
                     _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     _this.find('button').attr('disabled',false)
                     _this.find('button[type="submit"]').text('Save')
                }
            })
        })
        }
    })
</script>