<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('pre_papers');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('pre_papers', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Add Sub Papers </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Pre Papers</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title">Add Pre Paper </h1>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <form action='update_pre_papers' id='update_frm' method='post' name="emp_frm" enctype='multipart/form-data'>

                            <div class="card">
                                <div class="bg-gray p-5 text-black">
                                    <b class="text-uppercase">Pre Papers Details</b>
                                </div>
                                <div class="card-body">
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <input type='hidden' value='<?php echo $isedit; ?>' name='isedit'>
                                    <div class="form-row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Sub Subject <?= imp() ?></label>
                                                <select class="form-control select2" name="sub_subject_id" id="sub_subject_id" required>
                                                    <option value="">Select Subject</option>
                                                    <?= dropdown_where('pre_sub_subject','id','sub_subject',['branch_id'=>$branch_id,'status'=>'ACTIVE'],$sub_subject_id) ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Sub Paper Name</label>
                                                <input type='text' name='paper_name' class='form-control' value='<?= $paper_name ?>' required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Max Marks</label>
                                                <input type='text' name='max_marks' class='form-control' value='<?= $max_marks ?>' required="">
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
</script>