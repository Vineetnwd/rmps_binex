<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('pre_sub_subject');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('pre_sub_subject', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Add Sub Subject </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Sub Subjects</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title">Add Sub Subjects </h1>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <form action='update_sub_subject' id='update_frm' method='post' name="emp_frm" enctype='multipart/form-data'>

                            <div class="card">
                                <div class="bg-gray p-5 text-black">
                                    <b class="text-uppercase">Sub Subject Details</b>
                                </div>
                                <div class="card-body">
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <input type='hidden' value='<?php echo $isedit; ?>' name='isedit'>
                                    <div class="form-row">
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label>Subject <?= imp() ?></label>
                                                <select class="form-control select2" name="subject_id" id="subject_id" required>
                                                    <option value="">Select Subject</option>
                                                    <?= dropdown_where('subject','id','subject_name',['branch_id'=>$branch_id,'status'=>'ACTIVE'],$subject_id) ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label>Sub Subject Name</label>
                                                <input type='text' name='sub_subject' class='form-control' value='<?= $sub_subject ?>' required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</div>
<?php require_once('system/footer.php'); ?>
<script>
    function reset() {
        $("#update_frm").reset();
    }
    
$(document).ready(function () {
    const branchId = "<?= $_SESSION['branch_id']; ?>";

    $.ajax({
        url: 'system/master_process.php?task=get_sub_subjects_br', 
        type: 'POST',
        data: {
            branch_id: branchId
        },
        success: function(response) {
            console.log("Response from server:", response);
            $('#subject_id').html(response);
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
});

  
</script>