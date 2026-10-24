<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('teacher_subject');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('teacher_subject', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Add Teacher Subject </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title">Add Teacher Subject </h1>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <form action='update_teacher_subject' id='update_frm' method='post' name="emp_frm" enctype='multipart/form-data'>

                            <div class="card">
                                <div class="bg-gray p-5 text-black">
                                    <b class="text-uppercase">Employee Subject Details</b>
                                </div>
                                <div class="card-body">
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <input type='hidden' value='<?php echo $isedit; ?>' name='isedit'>
                                    <div class="form-row">
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="form-group">
                                                <label>Teacher <?= imp() ?></label>
                                                <select class='form-control select2' name='teacher_id' id='teacher_id'>
                                                    <option>Select Teacher</option>
                                                    <?= dropdown_where('employee','id','name',['emp_type'=>'TEACHER','branch_id'=>$branch_id],$teacher_id);?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6">
                                            <div class="form-group">
                                                <label>Class <?= imp() ?></label>
                                                <?php
                                                    $branch_data = get_data('branch_list',$branch_id,null,'branch_id')['data'];
                                                    $branch_classes = explode(",",$branch_data['class_list']);
                                                ?>
                                                <select class="form-control select2" name="class_name" id="class_name">
                                                    <option value="">Select Class</option>
                                                    <?php
                                                    foreach ($branch_classes as $branch_class) {
                                                        $selected = ($branch_class == $class_name) ? "selected" : "";
                                                        echo "<option value='$branch_class' $selected>$branch_class</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6">
                                            <div class="form-group">
                                                <label>Section <?= imp() ?></label>
                                                <?php
                                                    $branch_sections = explode(",", $branch_data['section_list']);
                                                ?>
                                                <select class="form-control select2" name="section_name" id="section_name" required>
                                                    <option value="">Select Section</option>
                                                    <?php
                                                        foreach ($branch_sections as $branch_section) {
                                                            $branch_section = trim($branch_section);
                                                            $selected = ($branch_section == $section_name) ? "selected" : "";
                                                            echo "<option value='" . $branch_section . "' $selected>" . $branch_section . "</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-sm-6">
                                            <div class="form-group">
                                                <label>Subject <?= imp() ?></label>
                                                <select class="form-control select2" name="subject_name" id="subject_name" required>
                                                    <option value="">Select Subject</option>
                                                    <?= dropdown_where('subject','id','subject_name',['branch_id'=>$branch_id],$subject_name) ?>
                                                </select>
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
<?php if (!empty($class_name)) : ?>
<script>
$(document).ready(function() {
    $('#class_name').trigger('change'); // Load subjects on load
});
</script>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#class_name').change(function() {
        let className = $(this).val();
        let branchId = <?= $branch_id ?>;

        if (className != "") {
            $.ajax({
                url: 'system/master_process?task=get_subjects',
                type: 'POST',
                data: {
                    class_name: className,
                    branch_id: branchId
                },
                success: function(response) {
                    $('#subject_name').html(response);
                },
                error: function() {
                    alert("Failed to load subjects");
                }
            });
        } else {
            $('#subject_name').html('<option value="">Select Subject</option>');
        }
    });
});
</script>
