<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Generate Report card </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#transport">Exam</a></li>
            <li class="breadcrumb-item active">Report card</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Generate Report Card</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class='row justify-content-center'>
                    <div class='col-md-4'>
                        <!--<form action='report_card.php' method='post' target='otpl' id='reminder_frm' target='_blank'>-->
                        <form action='exam_report_card.php' method='post' target='otpl' id='reminder_frm' target='_blank'>
                            <div class="form-group">
                                <label>Select Class </label>
                                <select class="form-control" name='student_class' required>
                                    <?php dropdown($class_list);?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Section </label>
                                <select class="form-control" name='student_section' required>
                                    <?php dropdown($section_list);?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Select Student Type </label>
                                <?php check_list('student_type',$student_type_list ,null, '120px');?>

                            </div>



                    </div>
                    <div class='col-md-4'>
                        <div class="form-group text-center">
                            <label>OR <br>Admission Number List </label>
                            <textarea class="form-control" name='student_admission' placeholder='12,58,79'></textarea>
                        </div>
                        <div class="form-group">
                            <label>Exam Name </label>
                            <select class="form-control" name='exam_id' required id='task'>
                                <?php dropdown_list('exam_list','exam_name','exam_name');?>
                            </select>
                        </div>

                        <div class="form-group">

                            <input type="submit" class="btn btn-orange btn-block " value=' Submit ' name='submit'>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once('system/footer.php'); ?>
<script>
$("#task").on('change', function() {
    var task = $(this).children("option:selected").val();
    $("#reminder_frm").attr('action', task);
});
</script>