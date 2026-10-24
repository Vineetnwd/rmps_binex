<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('system/header.php');
require_once('system/menu.php');

// Get inputs safely
$exam_id = $_REQUEST['exam_id'] ?? null;
$student_class = $_REQUEST['student_class'] ?? null;
$student_section = $_REQUEST['student_section'] ?? null;



// If exam_id is selected, get term and mark types
if ($exam_id) {
    list($term, $marks_type_list) = determine_term_and_types($exam_id);
} else {
    $term = null;
    $marks_type_list = [];
}

?>

<style>
@media print {
    body * { visibility: hidden; }
    #tbl_a, #tbl_a * { visibility: visible; }
    #tbl_a { page-break-inside: avoid; position: absolute; left: 0; top: 20px; }
    input[type='text'] { border: solid 1px #ddd; }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Consolidated Marks</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#exam">Exam</a></li>
            <li class="active">Consolidated Marks</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Consolidated Marks Sheet</h3>
                <div class="box-tools pull-right">
                    <!--<i class='fa fa-file-excel-o btn btn-orange text-light btn-sm' onclick='exportxls()' title='Export to Excel'></i>-->
                    <button class="btn btn-success btn-sm" onclick="exportexcel()" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="exportpdf()" title="Export to PDF">
                        Export to PDF
                    </button>
                </div>
            </div>

            <div class="box-body">
                <form action='' method='post'>
                    <div class='row'>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" name='student_class' required onChange="selsub(this.value)">
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Section</label>
                                <select class="form-control" name='student_section' required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name='exam_id' required>
                                    <?= dropdown_list('exam_list', 'exam_name', 'exam_name', @$exam_id); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label>&nbsp; </label>
                            <input type="submit" class="btn btn-primary btn-block" value='Show Details'>
                        </div>
                    </div>
                </form>

                <?php if ($exam_id && $student_class && $student_section): ?>
                    <?php
                    $subject_list = subject_list($student_class);
                    $is_single_ut = count($marks_type_list) === 1;

                    // Calculate total columns based on single UT or term exam
                    $subject_columns = count($subject_list) * ($is_single_ut ? 1 : (count($marks_type_list) + 1));
                    $total_columns = 2 + $subject_columns + 1; // Roll No, Name + subjects + Grand Total
                    ?>

                    <div class='table-responsive'>
                        <table class="table table-bordered table-striped" id="tbl_a">
                            <thead>
                                <tr>
                                    <th colspan='<?= $total_columns ?>' class='text-center'>
                                        <b><?= htmlspecialchars($exam_id) . ' - ' . htmlspecialchars($current_session) ?></b><br>
                                        <?= "Marks Details of " . htmlspecialchars($student_class) . " - " . htmlspecialchars($student_section) ?>
                                    </th>
                                </tr>
                                <tr class='op-thead'>
                                    <th rowspan='2'>Roll No.</th>
                                    <th rowspan='2'>Name</th>
                                    <?php foreach ($subject_list as $subject_id): ?>
                                        <th colspan='<?= $is_single_ut ? 1 : (count($marks_type_list) + 1) ?>' class='text-center'>
                                            <?= htmlspecialchars(get_data('subject', $subject_id, 'subject_name')['data']) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th rowspan='2'>Grand Total</th>
                                </tr>
                                <tr>
                                    <?php foreach ($subject_list as $subject_id): ?>
                                        <?php if ($is_single_ut): ?>
                                            <th><?= strtoupper(htmlspecialchars($marks_type_list[0])) ?></th>
                                        <?php else: ?>
                                            <?php foreach ($marks_type_list as $type): ?>
                                                <th><?= strtoupper(htmlspecialchars($type)) ?></th>
                                            <?php endforeach; ?>
                                            <th>Total</th>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // $students = direct_sql(
                                //     "SELECT student_admission, student_name, student_roll FROM student 
                                //      WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE' 
                                //      ORDER BY student_roll", 
                                //     'get', false
                                // );
                                $term_safe = addslashes($term);
                                $class_safe = addslashes($student_class);
                                $section_safe = addslashes($student_section);
                                
                                $students = direct_sql("
                                    SELECT s.student_admission, s.student_name, s.student_roll
                                    FROM student s
                                    JOIN tabulation t ON t.student_admission = s.student_admission AND t.term = '$term_safe'
                                    WHERE s.student_class = '$class_safe'
                                      AND s.student_section = '$section_safe'
                                      AND s.status = 'ACTIVE'
                                      AND s.branch_id = $branch_id
                                    GROUP BY s.student_admission
                                    ORDER BY s.student_roll
                                ", 'get', false);


                                foreach ((array)$students['data'] as $student):
                                    $student_admission = $student['student_admission'];
                                    $student_name = $student['student_name'];
                                    $student_roll = $student['student_roll'];
                                    $grand_total = 0;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student_roll) ?></td>
                                        <td><?= htmlspecialchars($student_name) ?></td>

                                        <?php foreach ($subject_list as $subject_id):
                                            // $subject_column = get_data('subject', $subject_id, 'subject_column')['data'];
                                            // print_r($subject_id);
                                            $marks = get_tabulation_marks($student_admission, $term, $subject_id, $marks_type_list);
                                            $subject_total = 0;
                                        ?>

                                            <?php if ($is_single_ut):
                                                $val = $marks[$marks_type_list[0]] ?? 0;
                                                $grand_total += $val;
                                            ?>
                                                <td><?= htmlspecialchars($val) ?></td>
                                            <?php else: ?>
                                                <?php foreach ($marks_type_list as $type):
                                                    $val = $marks[$type] ?? 0;
                                                    $subject_total += $val;
                                                ?>
                                                    <td><?= htmlspecialchars($val) ?></td>
                                                <?php endforeach; ?>
                                                <td bgcolor='lightgreen'><?= htmlspecialchars($subject_total) ?></td>
                                                <?php $grand_total += $subject_total; ?>
                                            <?php endif; ?>

                                        <?php endforeach; ?>

                                        <td><b><?= htmlspecialchars($grand_total) ?></b></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportexcel() {
    var table = document.getElementById("tbl_a");
    if (!table) {
        alert("No data to export!");
        return;
    }

    var wb = XLSX.utils.table_to_book(table, { sheet: "Consolidated Marks" });
    XLSX.writeFile(wb, "Consolidated_Marks.xlsx");
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
function exportpdf() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: "landscape" });

    doc.text("Tabulation Report", 14, 10);

    doc.autoTable({
        html: '#tbl_a', // Replace with your table ID
        startY: 20,
        theme: 'grid'
    });

    doc.save('tabulation.pdf');
}
</script>
