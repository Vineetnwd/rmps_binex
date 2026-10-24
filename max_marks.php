<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $fee = insert_row('max_marks');
    $id = $fee['id'];
}

if ($id != '') {
    $res = get_data('max_marks', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}

// Fetch dynamic classes from branch configuration
$branch_data = get_data('branch_list', $_SESSION['branch_id'] ?? 1, null, 'branch_id')['data'];
$branch_classes = explode(",", $branch_data['class_list']);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1> Max Marks Settings </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#settings">Admin Settings</a></li>
            <li class="breadcrumb-item active">Max Marks</li>
        </ol>
    </section>

    <section class="content">
        <div class='box box-default'>
            <div class="box-header with-border">
                <h3 class="box-title">Manage Max Marks Settings</h3>
                <div class="box-tools pull-right">
                    <div class='float-right'><a class='fa fa-plus text-orange btn btn-xs btn-outline-warning'
                            href='max_marks' title='Add New Config'> Add New Settings</a></div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form action='update_max_marks' method='post' id='update_frm' enctype='multipart/form-data'>
                            <input class="form-control" value='<?php echo $id; ?>' name='id' type='hidden'>
                            <input type="hidden" name="branch_id" value="<?php echo $_SESSION['branch_id'] ?? 1; ?>">

                            <div class="row">
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Exam</label>
                                        <select class="form-control select2" name="exam_id" required>
                                            <option value="">Select Exam</option>
                                            <?php
                                            echo dropdown('exam_list', 'id', 'exam_name', $exam_id ?? '');
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Class <?= imp() ?></label>
                                        <select class="form-control select2" name="class" id="class_name" required>
                                            <option value="">Select Class</option>
                                            <?php
                                            foreach ($branch_classes as $branch_class) {
                                                $branch_class = trim($branch_class);
                                                $selected = (isset($class) && $branch_class == $class) ? "selected" : "";
                                                echo "<option value='$branch_class' $selected>$branch_class</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <select class="form-control select2" name="subject_id" id="subject_name">
                                            <option value="">Default (All Subjects)</option>
                                            <?php
                                            if (isset($class) && $class != '') {
                                                echo dropdown_where('subject', 'id', 'subject_name', ['branch_id' => $_SESSION['branch_id'] ?? 1], $subject_id ?? '');
                                            } else {
                                                echo dropdown_where('subject', 'id', 'subject_name', ['branch_id' => $_SESSION['branch_id'] ?? 1], $subject_id ?? '');
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name='status' required>
                                            <option value='ACTIVE' <?php if (isset($status) && $status == 'ACTIVE')
                                                echo 'selected'; ?>>ACTIVE</option>
                                            <option value='HIDE' <?php if (isset($status) && $status == 'HIDE')
                                                echo 'selected'; ?>>HIDE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-blue mt-3 mb-2"
                                style="border-bottom: 2px solid #ddd; padding-bottom: 5px;">Marks Configuration</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NB</label>
                                        <input type="text" class="form-control" name="nb"
                                            value="<?php echo $nb ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>MA</label>
                                        <input type="text" class="form-control" name="ma"
                                            value="<?php echo $ma ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Marks Obtained (MO)</label>
                                        <input type="text" class="form-control" name="mo"
                                            value="<?php echo $mo ?? ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-info" id='update_btn'>Save Max Marks</button>
                                <a href="max_marks" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                <hr style="margin: 30px 0;">

                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id='example1'>
                                <thead>
                                    <tr class="bg-gray-light">
                                        <th>Exam</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>NB</th>
                                        <th>MA</th>
                                        <th>MO</th>
                                        <th>Status</th>
                                        <th>Operation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = get_all('max_marks', '*', ['branch_id' => $_SESSION['branch_id'] ?? 1]);
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $row_id = $row['id'];
                                            if (empty($row['class']) && empty($row['exam_id'])) {
                                                continue;
                                            }

                                            $sub_name = 'Default (All Subjects)';
                                            if (!empty($row['subject_id'])) {
                                                $sub_val = get_data('subject', $row['subject_id'], 'subject_name')['data'];
                                                if (!empty($sub_val)) {
                                                    $sub_name = htmlspecialchars($sub_val);
                                                }
                                            }

                                            $exam_name = '';
                                            if (!empty($row['exam_id'])) {
                                                $exam_val = get_data('exam_list', $row['exam_id'], 'exam_name')['data'];
                                                if (!empty($exam_val)) {
                                                    $exam_name = htmlspecialchars($exam_val);
                                                }
                                            }

                                            echo "<tr>";
                                            echo "<td>" . $exam_name . "</td>";
                                            echo "<td><strong>" . htmlspecialchars($row['class']) . "</strong></td>";
                                            echo "<td>" . $sub_name . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nb'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['ma'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['mo'] ?? '') . "</td>";

                                            if ($row['status'] == 'ACTIVE')
                                                echo "<td><span class='badge bg-green'>Active</span></td>";
                                            else
                                                echo "<td><span class='badge bg-red'>Hide</span></td>";

                                            $link = encode('id=' . $row['id']);
                                            echo "<td>
                                                    <a href='max_marks?link=$link' class='btn btn-xs btn-info'><i class='fa fa-edit'></i></a>
                                                    <a href='system/master_process.php?task=delete_max_marks&id={$row['id']}' class='btn btn-xs btn-danger' onclick=\"return confirm('Are you sure to delete this record?');\"><i class='fa fa-trash'></i></a>
                                                  </td>";
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

<script>
    $(document).ready(function () {
        $('#update_frm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function (data) {
                    var res = JSON.parse(data);
                    if (res.status == 'success') {
                        alert('Data saved successfully!');
                        window.location.href = 'max_marks';
                    } else {
                        alert('Error: ' + res.msg);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    });
</script>

<?php require_once('system/footer2.php'); ?>