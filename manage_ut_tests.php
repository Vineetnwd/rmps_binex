<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
$table = 'ut_tests';

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

<title> <?php echo $inst_name; ?> - UT Test Management </title>

<div class="content-wrapper">
    <section class="content-header">
        <h1> UT Test Management <small>Admin Options</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#Settings">Settings</a></li>
            <li class="breadcrumb-item active">Manage UT Tests</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= ($isedit == 'yes') ? 'Edit UT Test' : 'Create UT Test' ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-premium btn-premium-primary" id="update_btn"><i class="fa fa-save"></i> Save
                        Test </button>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <!-- Left Form Column -->
                    <div class="col-lg-4 col-md-5">
                        <div class="premium-card">
                            <form action="update_ut_test" id="update_frm" method="post">
                                <input type="hidden" value="<?php echo $id; ?>" name="id">
                                <input type="hidden" value="<?= $isedit; ?>" name="isedit">

                                <div class="form-group">
                                    <label><i class="fa fa-list-ol"></i> UT Number</label>
                                    <input type="text" class="form-control form-control-premium" name="ut_number"
                                        required value="<?= $ut_number ?? '' ?>">
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-calendar"></i> Exam Date</label>
                                    <input type="date" class="form-control form-control-premium" name="date" required
                                        value="<?= $date ?? date('Y-m-d') ?>">
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-toggle-on"></i> Status</label>
                                    <select name="status" class="form-control form-control-premium">
                                        <?php dropdown($status_list, $status ?? 'ACTIVE'); ?>
                                    </select>
                                </div>

                                <h5 class="mt-4 mb-3 text-dark font-weight-bold" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;"><i class="fa fa-book"></i> Class-wise Subject</h5>
                                <div style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                                    <?php
                                    $branch_data = get_data('branch_list', $_SESSION['branch_id']);
                                    $class_list = [];
                                    if ($branch_data['count'] > 0) {
                                        $class_list = explode(",", $branch_data['data']['class_list']);
                                        $class_list = array_map('trim', $class_list);
                                        $class_list = array_filter($class_list);
                                    }

                                    foreach ($class_list as $cls_name) {
                                        $scls_esc = mysqli_real_escape_string($con, $cls_name);
                                        // Fetch scholastic active subjects for this class
                                        $sub_q = "SELECT DISTINCT subject_name FROM subject WHERE FIND_IN_SET('$scls_esc', student_class) AND status = 'ACTIVE' AND branch_id = '{$_SESSION['branch_id']}' ORDER BY subject_name ASC";
                                        $sub_res = direct_sql($sub_q);

                                        // Fetch current scheduled subject for this class under this UT number (if editing)
                                        $current_subject = '';
                                        if (isset($ut_number) && $ut_number !== '') {
                                            $ut_num_esc = mysqli_real_escape_string($con, $ut_number);
                                            $curr_q = "SELECT subject FROM ut_tests WHERE ut_number = '$ut_num_esc' AND student_class = '$scls_esc' AND (student_section = '' OR student_section IS NULL) AND status = 'ACTIVE' AND branch_id = '{$_SESSION['branch_id']}' LIMIT 1";
                                            $curr_res = direct_sql($curr_q);
                                            if ($curr_res['count'] > 0) {
                                                $current_subject = $curr_res['data'][0]['subject'];
                                            }
                                        }
                                        ?>
                                        <div class="form-group mb-3">
                                            <label style="font-size: 13px; font-weight: 600; color: #475569; display: flex; justify-content: space-between; align-items: center;">
                                                <span>Class <?= htmlspecialchars($cls_name) ?></span>
                                                <?php if ($current_subject): ?>
                                                    <span class="badge badge-success" style="font-size: 9px; padding: 2px 6px; background-color: #10b981; color:#fff;">Scheduled</span>
                                                <?php endif; ?>
                                            </label>
                                            <select name="class_subject[<?= htmlspecialchars($cls_name) ?>]" class="form-control form-control-premium select2-subject">
                                                <option value="">-- No Test / Not Scheduled --</option>
                                                <?php
                                                if ($sub_res['count'] > 0) {
                                                    foreach ($sub_res['data'] as $sub_row) {
                                                        $s_name = $sub_row['subject_name'];
                                                        $sel = ($current_subject == $s_name) ? 'selected' : '';
                                                        echo "<option value='" . htmlspecialchars($s_name) . "' $sel>" . htmlspecialchars($s_name) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right List Column -->
                    <div class="col-lg-8 col-md-7">
                        <div class="premium-card">
                            <h4 class="mb-4 text-dark font-weight-bold">Scheduled Unit Tests</h4>
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="op-thead">
                                            <th>Test Name</th>
                                            <th>Date</th>
                                            <?php foreach ($class_list as $cls_name) { ?>
                                                <th class="text-center" style="font-size: 10px;"><?= htmlspecialchars($cls_name) ?></th>
                                            <?php } ?>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query only master UT test configurations (where class, section, subject are all empty/NULL)
                                        $res = direct_sql("SELECT * FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND status NOT IN ('AUTO','DELETED') AND branch_id = '$branch_id' ORDER BY id DESC");
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                $row_id = $row['id'];
                                                $ut_num = $row['ut_number'];
                                                $badge_class = ($row['status'] == 'ACTIVE') ? 'status-done' : 'status-pending';

                                                echo "<tr>";
                                                echo "<td class='font-weight-bold'>UT Test " . htmlspecialchars($ut_num) . "</td>";
                                                echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";

                                                // For each class, query what subject is scheduled for this UT
                                                foreach ($class_list as $cls_name) {
                                                    $cls_name_esc = mysqli_real_escape_string($con, $cls_name);
                                                    $ut_num_esc = mysqli_real_escape_string($con, $ut_num);

                                                    $sub_q = "SELECT subject FROM ut_tests 
                                                              WHERE ut_number = '$ut_num_esc' 
                                                              AND student_class = '$cls_name_esc' 
                                                              AND (student_section = '' OR student_section IS NULL) 
                                                              AND status = 'ACTIVE' 
                                                              AND branch_id = '$branch_id' LIMIT 1";
                                                    $sub_res = direct_sql($sub_q);

                                                    $sub_lbl = '-';
                                                    $td_style = 'color: #94a3b8; font-size: 11.5px;';
                                                    if ($sub_res['count'] > 0) {
                                                        $sub_lbl = $sub_res['data'][0]['subject'];
                                                        $td_style = 'color: #0f172a; font-weight: 700; font-size: 12px; background-color: #f8fafc;';
                                                    }

                                                    echo "<td class='text-center' style='{$td_style}'>" . htmlspecialchars($sub_lbl) . "</td>";
                                                }

                                                echo "<td><span class='badge-premium $badge_class'>" . htmlspecialchars($row['status']) . "</span></td>";
                                                echo "<td>" .
                                                    btn_edit('manage_ut_tests', $row_id) .
                                                    btn_delete($table, $row_id);
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
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>