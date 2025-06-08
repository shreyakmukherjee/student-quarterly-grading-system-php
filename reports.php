<div class="card rounded-0 shadow">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Class Quarterly Grade Report</h3>
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
                            $class_qry = $conn->query("SELECT c.*, CONCAT(s.name , ' ' , c.grade , ' - ' , c.section) as class,s.name as sname,CONCAT(c.grade , ' - ' , c.section) as grade_sec FROM `class_list` c inner join `subjects` s on c.subject_id = s.subject_id order by `class` asc");
                            while($row= $class_qry->fetch_assoc()):
                                $class[$row['class_id']] = $row;
                            ?>
                                <option <?php echo isset($_GET['class_id']) && $_GET['class_id'] == $row['class_id'] ? "selected" : '' ?> value="<?php echo $row['class_id'] ?>"><?php echo $row['class'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="quarter" class="control-label">Quarter</label>
                        <select name="quarter" id="quarter" class="form-select form-select-sm select2 rounded-0" required>
                        
                            <option value="1" <?php echo (isset($_GET['quarter']) && $_GET['quarter'] == 1) ? "selected" : '' ?>>First</option>
                            <option value="2" <?php echo (isset($_GET['quarter']) && $_GET['quarter'] == 2) ? "selected" : '' ?>>Second</option>
                            <option value="3" <?php echo (isset($_GET['quarter']) && $_GET['quarter'] == 3) ? "selected" : '' ?>>Third</option>
                            <option value="4" <?php echo (isset($_GET['quarter']) && $_GET['quarter'] == 4) ? "selected" : '' ?>>Fourth</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <button class="btn-sm btn btn-primary rounded-0" type="button" id="filter">Filter</button>
                        <button class="btn-sm btn btn-success ms-1 rounded-0" type="button" id="print">Print</button>
                    </div>
                </div>
                <hr>
                <?php
                if(isset($_GET['class_id']) && $_GET['quarter']): 
                    $quarter = array("","First","Second","Third","Fourth");
                ?>
                <div class="col-12" id="outprint">
                    <div class="row" >
                        <div class="col-6">
                            <div class="w-100 d-flex">
                                <div class="col-auto pe-1">Class: </div>
                                <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                    <?php echo isset($class[$_GET['class_id']]['grade_sec']) ? $class[$_GET['class_id']]['grade_sec'] : "N/A" ?>
                                </div>
                            </div>
                            <div class="w-100 d-flex">
                                <div class="col-auto pe-1">Subject: </div>
                                <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                    <?php echo isset($class[$_GET['class_id']]['sname']) ? $class[$_GET['class_id']]['sname'] : "N/A" ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="w-100 d-flex">
                                <div class="col-auto pe-1">Quarter: </div>
                                <div class="col-auto flex-grow-1 text-center border-bottom border-dark">
                                    <?php echo isset($quarter[$_GET['quarter']]) ? $quarter[$_GET['quarter']] : "N/A" ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end mb-1">
                            <div class="col-auto">
                                <button class="btn btn-sm btn-dark rounded-0" type="button" id="transmuted_table">View Transmutation Table</button>
                            </div>
                        </div>
                        <div class="col-md-12 py-3 w-100 overflow-auto justify-content-center">
                            <?php 
                            $components = array();
                            $components_query=$conn->query("SELECT c.*,g.name FROM `component_subject_percentage` c inner join `grading_components` g on c.component_id = g.component_id where c.subject_id = '{$class[$_GET['class_id']]['subject_id']}' order by component_id asc ");
                            while($row = $components_query->fetch_assoc()){
                                $components[$row['component_id']] = $row;
                            }
                            $assess = array();
                            $assess_ids = array();
                            $assess_query=$conn->query("SELECT * FROM `assessment_list` where class_id = '{$_GET['class_id']}' and quarter = '{$_GET['quarter']}' order by `name` asc ");
                            while($row = $assess_query->fetch_assoc()){
                                $assess[$row['component_id']][$row['assessment_id']] = $row;
                                $assess_ids[] = $row['assessment_id'];
                            }
                            $a_ids = implode(",",$assess_ids);
                            $marks = array();
                            $students = array();
                            $marks_qry = $conn->query("SELECT * FROM `mark_list`  where assessment_id in ({$a_ids}) ");
                            while($row = $marks_qry->fetch_assoc()){
                                $marks[$row['student_id']][$row['assessment_id']] = $row['mark'];
                            }
                            $student_query = $conn->query("SELECT * FROM student_list where class_id = '{$_GET['class_id']}' order by name asc");
                            while($row = $student_query->fetch_assoc()){
                                $students[$row['student_id']] = $row['name'];
                            }
                            $c_perc_arr = array();
                            foreach($components as $row){
                                $c_perc_arr[$row['component_id']] =( $row['percentage']/100);
                            }
                            $transmutaion_query = $conn->query("SELECT * FROM transmutation_table");
                            $trans_tbl = array();
                            while($row = $transmutaion_query->fetch_assoc()){
                                $trans_tbl[$row['trans_id']] = $row;
                            }
                            function transmute($grade = 0){
                                global $trans_tbl;
                                $transmuted = 0;
                                foreach($trans_tbl as $row){
                                    if($row['from'] <= $grade && $grade <= $row['to']){
                                        $transmuted = $row['grade'];
                                    }
                                }
                                return $transmuted;
                            }
                            ?>
                            <table class="table table-striped table-hover table-bordered w-auto">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <th class="py-0 px-1 text-center align-middle" rowspan="2" width="200px">Student</th>
                                        <?php foreach($components as $row): ?>
                                        <th class="py-0 px-1 text-center" colspan="<?php echo isset($assess[$row['component_id']]) ? count($assess[$row['component_id']]) + 2 : 3 ?>"><?php echo $row['name'] ?> (<?php echo $row['percentage'] ?>%)</th>
                                        <?php endforeach; ?>
                                        <th class="py-0 px-1 text-center align-middle" rowspan="3">Initial Grade</th>
                                        <th class="py-0 px-1 text-center align-middle" rowspan="3">Quarterly Grade</th>
                                    </tr>
                                    <tr>
                                        <?php 
                                            foreach($components as $k => $v): 
                                                if(isset($assess[$k])):
                                                foreach($assess[$k] as $row): 
                                        ?>
                                                    <th class="py-0 px-1 text-center bg-info bg-opacity-50" width="50px"><?php echo $row['name'] ?></th>
                                        <?php 
                                                    endforeach; 
                                                else:
                                        ?>
                                                    <th class="py-0 px-1 text-center bg-info bg-opacity-50" width="50px">N/A</th>
                                        <?php
                                                endif;
                                        ?>
                                                    <th class="py-0 px-1 text-center bg-warning bg-opacity-50" width="50px">Total</th>
                                                    <th class="py-0 px-1 text-center bg-dark bg-opacity-50" width="50px">%</th>
                                        <?php
                                            endforeach; 
                                        ?>
                                    </tr>
                                    <tr class="bg-warning bg-opacity-50">
                                        <th class="py-0 px-1 text-center">HPS</th>
                                        <?php 
                                            $total_hps_arr = array();
                                            foreach($components as $k => $v): 
                                                $total_hps = 0;
                                                $perc_hps = 100;
                                                if(isset($assess[$k])):
                                                foreach($assess[$k] as $row): 
                                                    $total_hps += $row['hps'];
                                        ?>
                                                    <th class="py-0 px-1 text-center" width="50px"><?php echo $row['hps'] ?></th>
                                        <?php 
                                                    endforeach; 
                                                else:
                                        ?>
                                                    <th class="py-0 px-1 text-center" width="50px">N/A</th>
                                        <?php
                                                endif;
                                        ?>
                                                    <th class="py-0 px-1 text-center" width="50px"><?php echo $total_hps_arr[$k] = $total_hps ?></th>
                                                    <th class="py-0 px-1 text-center bg-dark bg-opacity-50  text-light" width="50px"><?php echo $perc_hps ?></th>
                                        <?php
                                            endforeach; 
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach($students as $k =>$v):
                                        $initial = 0;
                                    ?>
                                        <tr>
                                            <td class="py-0 px-1"><?php  echo $v ?></td>
                                            <?php 
                                            foreach($components as $cid => $v): 
                                                    $total_hps = 0;
                                                    $perc_hps = 0;
                                                    if(isset($assess[$cid])):
                                                    foreach($assess[$cid] as $row): 
                                                        $total_hps += isset($marks[$k][$row['assessment_id']]) ? $marks[$k][$row['assessment_id']] : 0;
                                            ?>
                                                    <td class="py-0 px-1 text-center" width="50px"><?php echo isset($marks[$k][$row['assessment_id']]) ? $marks[$k][$row['assessment_id']] : 0 ?></td>
                                            <?php 
                                                    endforeach; 
                                                    $perc_hps = round((($total_hps/$total_hps_arr[$cid]) * 100),2);
                                                else:
                                            ?>
                                                        <td class="py-0 px-1 text-center" width="50px">N/A</td>
                                            <?php
                                                endif;
                                            ?>
                                                    <td class="py-0 px-1 text-center  bg-warning bg-opacity-50" width="50px"><?php echo $total_hps ?></td>
                                                    <td class="py-0 px-1 text-center  bg-dark bg-opacity-50  text-light" width="50px"><?php echo $perc_hps ?></td>
                                            <?php
                                            $initial += ($perc_hps * $c_perc_arr[$cid]);
                                            endforeach; 
                                            ?>
                                            <td class="py-0 px-1 text-center bg-dark bg-opacity-50 text-light" width="50px"><?php echo $initial = round($initial,2) ?></td>
                                            <td class="py-0 px-1 text-center bg-dark bg-opacity-50 text-light" width="50px"><?php echo transmute($initial) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<script>
    $(function(){
        $('#filter').click(function(){
            var class_id = $('#class_id').val()
            var quarter = $('#quarter').val()
            if(class_id <= 0 || quarter <= 0){
                alert("Please select class and assessment first.")
            }else{
                location.href = "./?page=reports&class_id="+class_id+"&quarter="+quarter;
            }
        })
        $('#transmuted_table').click(function(){
            uni_modal('Transmutation Table','transmutation_table.php','mid-large');
        })
        $('#print').click(function(){
            var _p = $('#outprint').clone()
            _p.find('#transmuted_table').remove();
            var _el = $('<div class=".col-12">')
            var _head = $('head').clone()
            _el.append(_head)
            _el.append("<div class='lh-1'>"+
                        '<h3 class="text-center fw-bold">Student Quarterly Grading System</h3>'+
                        "</div><hr>");
            _el.append(_p)
            var nw = window.open("","","width=1000,height=900");
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()
                    }, 200);
                }, 200);
        })
    })
</script>