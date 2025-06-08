
<div class="card h-100 d-flex flex-column">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Maintenance</h3>
        <div class="card-tools align-middle">
            <!-- <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button> -->
        </div>
    </div>
    <div class="card-body flex-grow-1">
        <div class="col-12 h-100">
            <div class="row h-100">
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Component List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_component" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add Component"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `grading_components` where delete_flag = 0 order by `name` asc");
                            while($row = $dept_qry->fetch_assoc()):
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['name'] ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_component btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Component Details" data-id="<?php echo $row['component_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>
                                    <a href="javascript:void(0)" class="delete_component btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete component" data-id="<?php echo $row['component_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if($dept_qry->num_rows <= 0): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Subject List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_subject" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add subject"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php 
                            $dept_qry = $conn->query("SELECT * FROM `subjects` where delete_flag = 0 order by `name` asc");
                            while($row = $dept_qry->fetch_assoc()):
                            ?>
                            <li class="list-group-item d-flex">
                                <div class="col-auto flex-grow-1">
                                    <?php echo $row['name'] ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="manage_percentage btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Manage Subject's Component Percentage" data-id="<?php echo $row['subject_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-th-list"></span></a>

                                    <a href="javascript:void(0)" class="edit_subject btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Subject Details" data-id="<?php echo $row['subject_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_subject btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete subject" data-id="<?php echo $row['subject_id'] ?>"  data-name="<?php echo $row['name'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if($dept_qry->num_rows <= 0): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        // Components Functions
        $('#new_component').click(function(){
            uni_modal('Add New Component',"manage_component.php")
        })
        $('.edit_component').click(function(){
            uni_modal('Edit Component Details',"manage_component.php?id="+$(this).attr('data-id'))
        })
        $('.manage_percentage').click(function(){
            uni_modal('Manage Subject\'s Components Percentage',"manage_percentage.php?id="+$(this).attr('data-id'))
        })
        $('.delete_component').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Component List?",'delete_component',[$(this).attr('data-id')])
        })

        // Subjects function
        $('#new_subject').click(function(){
            uni_modal('Add New Subject',"manage_subject.php")
        })
        $('.edit_subject').click(function(){
            uni_modal('Edit Subject Details',"manage_subject.php?id="+$(this).attr('data-id'))
        })
        $('.delete_subject').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Subject List?",'delete_subject',[$(this).attr('data-id')])
        })
       
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:6 }
            ]
        })
    })
    function delete_subject($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_subject',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_component($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_component',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>