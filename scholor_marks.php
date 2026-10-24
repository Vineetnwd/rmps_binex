<?php 
require_once('system/header.php'); 
require_once('system/menu.php');

extract(post_clean($_GET));

$table_name = 'admission';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : $today;
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : $today;

if (isset($_GET['from_date'])) {
    $sql = "SELECT * FROM scholorship WHERE DATE(pay_date) BETWEEN '$from_date' AND '$to_date' AND payment_status = 'PAID'";
    $res = direct_sql($sql);
} else {
    $res = fetch_all('scholorship','*',['payment_status'=>'PAID'],[],'student_class');
    // $sql = "SELECT * FROM scholorship WHERE payment_status = 'PAID' ORDER BY student_class ASC";
}

?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>Update Scholarship Marks</h1>
        <ol class="breadcrumb">
            <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Student</li>
            <li class="active">Update Scholarship Marks</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update Scholarship Marks</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="op-thead">
                                        <th>Sl.</th>
                                        <th>Name</th>
                                        <th>Roll No.</th>
                                        <th>Class</th>
                                        <!--<th>Full Marks</th>-->
                                        <th>Pass Marks</th>
                                        <th>Obtained Marks</th>
                                        <th>Interview</th>
                                        <th>Interview Marks</th>
                                        <th>Result</th>
                                        <th>Rank</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            ?>
                                            <tr>
                                                <form action="system/master_process?task=update_scholor_marks" method="post">
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['student_roll']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['student_class']); ?></td>
                                                    <!--<td><?php echo htmlspecialchars($row['full_marks']); ?></td>-->
                                                    <td><input type='text' class='form-control' name='pass_marks' value='<?php echo htmlspecialchars($row['pass_marks']); ?>'></td>
                                                    <td><?php echo htmlspecialchars($row['obtained_marks']); ?></td>
                                                    <td><input type=type='text' class='form-control' name='interview' value='<?php echo htmlspecialchars($row['interview']); ?>' ></td>
                                                    <td><input type="text" class="form-control" name="i_result" value="<?php echo htmlspecialchars($row['i_result']); ?>"></td>
                                                    <td><input type="text" class="form-control" name="rank" value="<?php echo htmlspecialchars($row['rank']); ?>"></td>
                                                    <!--<td><input type="text" class="form-control" name="obtained_marks" value="<?php echo htmlspecialchars($row['obtained_marks']); ?>"></td>-->
                                                    
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
                                                    <input type="hidden" name="branch_id" value="<?= htmlspecialchars($row['branch_id']); ?>">
                                                    
                                                    <td><button type="submit" class="btn btn-success">Update</button></td>
                                                </form>
                                            </tr>
                                            <?php
                                            $i++;
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
