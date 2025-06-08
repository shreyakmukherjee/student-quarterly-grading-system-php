<?php require_once('DBConnection.php'); ?>
<style>
    #uni_modal .modal-footer{
        display: none !important;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="number" step="any" class="form-control form-control-sm text-right" id="grade" placefolder="Enter Grade Here">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 overflow-auto" style="height:50vh">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr class="position-sticky">
                            <th class="px-1 py-0 text-center">FROM</th>
                            <th class="px-1 py-0 text-center">To</th>
                            <th class="px-1 py-0 text-center">Transmuted Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $transmutaion_query = $conn->query("SELECT * FROM `transmutation_table` order by CAST(grade as integer) asc");
                        while($row = $transmutaion_query->fetch_assoc()):
                        ?>
                        <tr class="trans-item">
                            <td class="px-1 py-0 text-center from"><?php echo $row['from'] ?></td>
                            <td class="px-1 py-0 text-center to"><?php echo $row['to'] ?></td>
                            <td class="px-1 py-0 text-center"><?php echo $row['grade'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-auto px-2">
                <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#grade').on('input',function(){
            var grade = $(this).val()
            if(grade != ''){
                $('.trans-item').each(function(){
                    var from = $(this).find('.from').text()
                    var to = $(this).find('.to').text()
                    if(from <= grade && grade <= to){
                        $(this).toggle(true)
                    }else{
                        $(this).toggle(false)
                    }
                })
            }else{
                $('.trans-item').toggle(true)
            }
        })
    })
</script>