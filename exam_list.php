<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
$table = 'exam_list';
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
        <h1> Exam Management </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#Exam">Exam</a></li>
            <li class="breadcrumb-item active">Exam List </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add and Update Exam</h3>

                <div class="box-tools pull-right">
                <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class='row'>

                    <div class="col-lg-4 col-sm-6">

                        <form action='update_exam_list' id='update_frm' method='post' enctype='multipart/form-data'>
                            <input type='hidden' value='<?php echo $id; ?>' name='id'>
                            <input type='hidden' value='<?= $isedit; ?>' name='isedit'>
                            <div class="form-group">
                                <label> Exam Name </label>
                                <input class="form-control" required name='exam_name' value='<?php echo $exam_name; ?>'>
                            </div>
                            <div class="form-group">
                                <label> Start Date </label>
                                <input class="form-control" type='date' required name='start_date' value='<?php echo $start_date; ?>'>
                            </div>
                            <div class="form-group">
                                <label> End Date </label>
                                <input class="form-control" type='date' required name='end_date' value='<?php echo $end_date; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Status </label>
                                <select name='status' class='form-control'>
                                    <?php dropdown($status_list, $status); ?>
                                </select>
                            </div>

                        </form>
                       
                    </div>

                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr class='op-thead'>
                                        <th> Exam Name</th>
                                        <th> Tentative Date </th>
                                        <th> Status </td>
                                        <th> Operation </td>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $res = get_all($table);
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            echo "<tr>";
                                            echo "<td>" . $row['exam_name'] . "</td>";
                                            echo "<td>" . date('d-M-Y', strtotime($row['start_date'])) ." - ". date('d-M-Y', strtotime($row['end_date']))  . "</td>";
                                            echo "<td>" . $row['status'] . "</td>";
                                            echo "<td>" .
                                                btn_view($table, $id, 'Exam Details') .
                                                btn_edit($table, $id) .
                                                btn_delete($table, $id);
                                            echo  "</td>";
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