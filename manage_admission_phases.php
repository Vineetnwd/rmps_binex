<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<link rel="stylesheet" href="assets/css/teacher_premium.css">

<?php
$table = 'admission_phase';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $data = insert_row($table);
    $id = $data['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data($table, $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Admission Phase Management </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="application_list">Enquiry</a></li>
            <li class="breadcrumb-item active">Admission Phases</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo ($isedit == 'yes') ? 'Edit Phase Details' : 'Create New Admission Phase'; ?></h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save Phase </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class='row'>

                    <div class="col-lg-4 col-sm-6">
                        <form action='update_admission_phase' id='update_frm' method='post'>
                            <input type='hidden' value='<?php echo $id; ?>' name='id'>
                            <input type='hidden' value='<?= $isedit; ?>' name='isedit'>
                            
                            <div class="form-group">
                                <label><i class="fa fa-tag"></i> Phase Name </label>
                                <input class="form-control form-control-premium" required name='phase_name' value='<?php echo isset($phase_name) ? $phase_name : ''; ?>' placeholder="e.g. 1st Phase, 2nd Phase">
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fa fa-calendar"></i> End Date (Last Date for Phase) </label>
                                <input class="form-control form-control-premium" type='date' required name='end_date' value='<?php echo isset($end_date) ? $end_date : ''; ?>'>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fa fa-clock-o"></i> Exam Date Display Value </label>
                                <input class="form-control form-control-premium" required name='exam_date' value='<?php echo isset($exam_date) ? $exam_date : ''; ?>' placeholder="e.g. 18 Jan 2026, 15 Feb 2026">
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fa fa-check-circle"></i> Status </label>
                                <select name='status' class='form-control form-control-premium'>
                                    <?php dropdown($status_list, isset($status) ? $status : 'ACTIVE'); ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr class='op-thead'>
                                        <th> Phase Name </th>
                                        <th> End Date </th>
                                        <th> Exam Date </th>
                                        <th> Status </th>
                                        <th> Operations </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res_all = get_all($table);
                                    if ($res_all['count'] > 0) {
                                        foreach ($res_all['data'] as $row) {
                                            $phase_id = $row['id'];
                                            echo "<tr>";
                                            echo "<td><strong>" . $row['phase_name'] . "</strong></td>";
                                            echo "<td>" . date('d-M-Y', strtotime($row['end_date'])) . "</td>";
                                            echo "<td>" . $row['exam_date'] . "</td>";
                                            echo "<td>";
                                            if ($row['status'] == 'ACTIVE') {
                                                echo "<span class='badge badge-premium status-done'>Active</span>";
                                            } else {
                                                echo "<span class='badge badge-premium status-pending'>" . $row['status'] . "</span>";
                                            }
                                            echo "</td>";
                                            echo "<td>" .
                                                btn_view($table, $phase_id, 'Phase Details') .
                                                btn_edit('manage_admission_phases', $phase_id) .
                                                btn_delete($table, $phase_id);
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>
