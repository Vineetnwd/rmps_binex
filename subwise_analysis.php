<?php 
require_once('system/header.php');
require_once('system/menu.php');

if($user_type != 'ADMIN' && $user_type != 'DEV') {
    echo "<script> window.location='dashboard' </script>";
    exit;
}

// Get form values
$student_class = $_REQUEST['student_class'] ?? '';
$student_section = $_REQUEST['student_section'] ?? '';
$subject = $_REQUEST['subject'] ?? '';
$term = $_REQUEST['term'] ?? '';
$action = $_REQUEST['action'] ?? '';

// Get subject name for display
$subject_name = '';
if (!empty($subject)) {
    $subject_query = direct_sql("SELECT subject_name FROM subject WHERE id = '$subject' LIMIT 1");
    if (!empty($subject_query['data'][0])) {
        $subject_name = $subject_query['data'][0]['subject_name'];
    }
}
?>

<script> 
    document.title = "Subject Wise Report";
</script>

<style>
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .report-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .report-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 15px;
    }
    .report-info-item {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 5px;
    }
    .report-info-item strong {
        display: block;
        font-size: 12px;
        opacity: 0.8;
    }
    .report-info-item span {
        font-size: 16px;
        font-weight: 600;
    }
    .op-thead {
        background: #343a40;
        color: white;
    }
    .btn-download {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-download:hover {
        opacity: 0.9;
        color: white;
    }
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .filter-form select {
        min-width: 150px;
        padding: 8px 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    .grade-excellent { background-color: #d4edda !important; }
    .grade-good { background-color: #cce5ff !important; }
    .grade-average { background-color: #fff3cd !important; }
    .grade-poor { background-color: #f8d7da !important; }
    .total-row { background-color: #e9ecef !important; font-weight: bold; }
    
    @media print {
        .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1><i class="fa fa-bar-chart"></i> Subject Wise Report</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Exam Analysis</a></li>
            <li class="breadcrumb-item active">Subject Wise Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Filter Box -->
        <div class="box box-default no-print">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Filter Options</h3>
            </div>
            <div class="box-body">
                <form method="post" class="filter-form" id="reportForm">
                    <div class='row'>
                        <div class='col-3'>
                            <select name='student_class' id='student_class' class='form-control' required>
                                <option value='' disabled <?= empty($student_class) ? 'selected' : '' ?>>Select Class</option>
                                <option value='I' <?= $student_class == 'I' ? 'selected' : '' ?>>I</option>
                                <option value='II' <?= $student_class == 'II' ? 'selected' : '' ?>>II</option>
                                <option value='III' <?= $student_class == 'III' ? 'selected' : '' ?>>III</option>
                                <option value='IV' <?= $student_class == 'IV' ? 'selected' : '' ?>>IV</option>
                                <option value='V' <?= $student_class == 'V' ? 'selected' : '' ?>>V</option>
                                <option value='VI' <?= $student_class == 'VI' ? 'selected' : '' ?>>VI</option>
                                <option value='VII' <?= $student_class == 'VII' ? 'selected' : '' ?>>VII</option>
                                <option value='VIII' <?= $student_class == 'VIII' ? 'selected' : '' ?>>VIII</option>
                                <option value='IX' <?= $student_class == 'IX' ? 'selected' : '' ?>>IX</option>
                                <option value='X' <?= $student_class == 'X' ? 'selected' : '' ?>>X</option>
                            </select>
                        </div> 
                        <div class='col-3'>
                            <select name='student_section' id='student_section' class='form-control' required>
                                <?= dropdown($section_list, $student_section) ?>
                            </select>
                        </div> 
                        <div class='col-3'>
                            <select name='subject' id='subject' class='form-control' required>
                                <option value='' disabled <?= empty($subject) ? 'selected' : '' ?>>Select Subject</option>
                                <?= dropdown_where_fis('subject', 'id', 'subject_name', ['student_class' => 'I,II,III,IV,V,VI,VII,VIII'], $subject) ?>
                            </select>
                        </div> 
                        <div class='col-3'>
                            <select name='term' id='term' class='form-control' required>
                                <option value='' disabled <?= empty($term) ? 'selected' : '' ?>>Select Term</option>
                                <option value='TERM 1' <?= $term == 'TERM 1' ? 'selected' : '' ?>>Term 1</option>
                                <!--<option value='TERM 2' <?= $term == 'TERM 2' ? 'selected' : '' ?>>Term 2</option>-->
                            </select>
                        </div> 
                        <div class='col-12 mt-2'>
                            <input type="hidden" name="action" id="formAction" value="generate">
                            <button type="submit" class='btn btn-primary'>
                                <i class="fa fa-search"></i> Generate Report
                            </button>
                            
                            <?php if (!empty($_POST) && $action != 'download_pdf'): ?>
                                <button type="button" class='btn btn-dark' onclick="downloadPDF()">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF
                                </button>
                                <button type="button" class='btn btn-info' onclick="window.print()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            <?php endif; ?>
                        </div> 
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Process form submission
        if (!empty($_POST) && !empty($student_class) && !empty($student_section) && !empty($subject) && !empty($term)):
            
            // Sanitize inputs
            $student_class = mysqli_real_escape_string($con, $student_class);
            $student_section = mysqli_real_escape_string($con, $student_section);
            $subject = mysqli_real_escape_string($con, $subject);
            $term = mysqli_real_escape_string($con, $term);
            
            // Get total students count
            $total_sql = "SELECT COUNT(*) AS total_students FROM student 
                          WHERE student_class = '$student_class' 
                          AND student_section = '$student_section' 
                          AND status = 'ACTIVE'";
            $total_result = direct_sql($total_sql);
            $total_students = $total_result['data'][0]['total_students'] ?? 0;
            
            // Build SQL query based on class
            $sql = "SELECT
                total.total_students,
            
                /* ================= UT1 STATISTICS (30 MARKS) ================= */
                COUNT(t.ut1) AS ut1_appeared,
                ROUND((COUNT(t.ut1) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_appeared_percent,
            
                COUNT(CASE WHEN t.ut1 >= 9.9 THEN 1 END) AS ut1_passed,
                ROUND((COUNT(CASE WHEN t.ut1 >= 9.9 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_passed_percent,
            
                COUNT(CASE WHEN t.ut1 < 9.9 THEN 1 END) AS ut1_failed,
                ROUND((COUNT(CASE WHEN t.ut1 < 9.9 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_failed_percent,
            
                COUNT(CASE WHEN t.ut1 >= 27 THEN 1 END) AS ut1_90_plus,
                ROUND((COUNT(CASE WHEN t.ut1 >= 27 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_90_plus_percent,
            
                COUNT(CASE WHEN t.ut1 >= 24 AND t.ut1 < 27 THEN 1 END) AS ut1_80_89,
                ROUND((COUNT(CASE WHEN t.ut1 >= 24 AND t.ut1 < 27 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_80_89_percent,
            
                COUNT(CASE WHEN t.ut1 >= 21 AND t.ut1 < 24 THEN 1 END) AS ut1_70_79,
                ROUND((COUNT(CASE WHEN t.ut1 >= 21 AND t.ut1 < 24 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_70_79_percent,
            
                COUNT(CASE WHEN t.ut1 >= 15 AND t.ut1 < 21 THEN 1 END) AS ut1_50_69,
                ROUND((COUNT(CASE WHEN t.ut1 >= 15 AND t.ut1 < 21 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_50_69_percent,
            
                COUNT(CASE WHEN t.ut1 >= 12 AND t.ut1 < 15 THEN 1 END) AS ut1_40_49,
                ROUND((COUNT(CASE WHEN t.ut1 >= 12 AND t.ut1 < 15 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_40_49_percent,
            
                COUNT(CASE WHEN t.ut1 >= 9.9 AND t.ut1 < 12 THEN 1 END) AS ut1_33_39,
                ROUND((COUNT(CASE WHEN t.ut1 >= 9.9 AND t.ut1 < 12 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_33_39_percent,
            
            
                /* ================= UT2 STATISTICS (30 MARKS) ================= */
                COUNT(t.ut2) AS ut2_appeared,
                ROUND((COUNT(t.ut2) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_appeared_percent,
            
                COUNT(CASE WHEN t.ut2 >= 9.9 THEN 1 END) AS ut2_passed,
                ROUND((COUNT(CASE WHEN t.ut2 >= 9.9 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_passed_percent,
            
                COUNT(CASE WHEN t.ut2 < 9.9 THEN 1 END) AS ut2_failed,
                ROUND((COUNT(CASE WHEN t.ut2 < 9.9 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_failed_percent,
            
                COUNT(CASE WHEN t.ut2 >= 27 THEN 1 END) AS ut2_90_plus,
                ROUND((COUNT(CASE WHEN t.ut2 >= 27 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_90_plus_percent,
            
                COUNT(CASE WHEN t.ut2 >= 24 AND t.ut2 < 27 THEN 1 END) AS ut2_80_89,
                ROUND((COUNT(CASE WHEN t.ut2 >= 24 AND t.ut2 < 27 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_80_89_percent,
            
                COUNT(CASE WHEN t.ut2 >= 21 AND t.ut2 < 24 THEN 1 END) AS ut2_70_79,
                ROUND((COUNT(CASE WHEN t.ut2 >= 21 AND t.ut2 < 24 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_70_79_percent,
            
                COUNT(CASE WHEN t.ut2 >= 15 AND t.ut2 < 21 THEN 1 END) AS ut2_50_69,
                ROUND((COUNT(CASE WHEN t.ut2 >= 15 AND t.ut2 < 21 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_50_69_percent,
            
                COUNT(CASE WHEN t.ut2 >= 12 AND t.ut2 < 15 THEN 1 END) AS ut2_40_49,
                ROUND((COUNT(CASE WHEN t.ut2 >= 12 AND t.ut2 < 15 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_40_49_percent,
            
                COUNT(CASE WHEN t.ut2 >= 9.9 AND t.ut2 < 12 THEN 1 END) AS ut2_33_39,
                ROUND((COUNT(CASE WHEN t.ut2 >= 9.9 AND t.ut2 < 12 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_33_39_percent";
            
            // Add MTE statistics based on class (Class I & II have 60 marks, others have 80 marks)
            if ($student_class != 'I' && $student_class != 'II') {
                // MTE (80 MARKS)
                $sql .= ",
                COUNT(t.mid_term) AS mte_appeared,
                ROUND((COUNT(t.mid_term) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_appeared_percent,
            
                COUNT(CASE WHEN t.mid_term >= 26.4 THEN 1 END) AS mte_passed,
                ROUND((COUNT(CASE WHEN t.mid_term >= 26.4 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_passed_percent,
            
                COUNT(CASE WHEN t.mid_term < 26.4 THEN 1 END) AS mte_failed,
                ROUND((COUNT(CASE WHEN t.mid_term < 26.4 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_failed_percent,
            
                COUNT(CASE WHEN t.mid_term >= 72 THEN 1 END) AS mte_90_plus,
                ROUND((COUNT(CASE WHEN t.mid_term >= 72 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_90_plus_percent,
            
                COUNT(CASE WHEN t.mid_term >= 64 AND t.mid_term < 72 THEN 1 END) AS mte_80_89,
                ROUND((COUNT(CASE WHEN t.mid_term >= 64 AND t.mid_term < 72 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_80_89_percent,
            
                COUNT(CASE WHEN t.mid_term >= 56 AND t.mid_term < 64 THEN 1 END) AS mte_70_79,
                ROUND((COUNT(CASE WHEN t.mid_term >= 56 AND t.mid_term < 64 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_70_79_percent,
            
                COUNT(CASE WHEN t.mid_term >= 40 AND t.mid_term < 56 THEN 1 END) AS mte_50_69,
                ROUND((COUNT(CASE WHEN t.mid_term >= 40 AND t.mid_term < 56 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_50_69_percent,
            
                COUNT(CASE WHEN t.mid_term >= 32 AND t.mid_term < 40 THEN 1 END) AS mte_40_49,
                ROUND((COUNT(CASE WHEN t.mid_term >= 32 AND t.mid_term < 40 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_40_49_percent,
            
                COUNT(CASE WHEN t.mid_term >= 26.4 AND t.mid_term < 32 THEN 1 END) AS mte_33_39,
                ROUND((COUNT(CASE WHEN t.mid_term >= 26.4 AND t.mid_term < 32 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_33_39_percent";
            } else {
                // MTE (60 MARKS) for Class I & II
                $sql .= ",
                COUNT(t.mid_term) AS mte_appeared,
                ROUND((COUNT(t.mid_term) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_appeared_percent,
            
                COUNT(CASE WHEN t.mid_term >= 19.8 THEN 1 END) AS mte_passed,
                ROUND((COUNT(CASE WHEN t.mid_term >= 19.8 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_passed_percent,
            
                COUNT(CASE WHEN t.mid_term < 19.8 THEN 1 END) AS mte_failed,
                ROUND((COUNT(CASE WHEN t.mid_term < 19.8 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_failed_percent,
            
                COUNT(CASE WHEN t.mid_term >= 54 THEN 1 END) AS mte_90_plus,
                ROUND((COUNT(CASE WHEN t.mid_term >= 54 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_90_plus_percent,
            
                COUNT(CASE WHEN t.mid_term >= 48 AND t.mid_term < 54 THEN 1 END) AS mte_80_89,
                ROUND((COUNT(CASE WHEN t.mid_term >= 48 AND t.mid_term < 54 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_80_89_percent,
            
                COUNT(CASE WHEN t.mid_term >= 42 AND t.mid_term < 48 THEN 1 END) AS mte_70_79,
                ROUND((COUNT(CASE WHEN t.mid_term >= 42 AND t.mid_term < 48 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_70_79_percent,
            
                COUNT(CASE WHEN t.mid_term >= 30 AND t.mid_term < 42 THEN 1 END) AS mte_50_69,
                ROUND((COUNT(CASE WHEN t.mid_term >= 30 AND t.mid_term < 42 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_50_69_percent,
            
                COUNT(CASE WHEN t.mid_term >= 24 AND t.mid_term < 30 THEN 1 END) AS mte_40_49,
                ROUND((COUNT(CASE WHEN t.mid_term >= 24 AND t.mid_term < 30 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_40_49_percent,
            
                COUNT(CASE WHEN t.mid_term >= 19.8 AND t.mid_term < 24 THEN 1 END) AS mte_33_39,
                ROUND((COUNT(CASE WHEN t.mid_term >= 19.8 AND t.mid_term < 24 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_33_39_percent";
            }
            
            $sql .= "
            FROM tabulation t
            JOIN student s ON s.id = t.student_id
            CROSS JOIN (
                SELECT COUNT(*) AS total_students
                FROM student
                WHERE student_class = '$student_class'
                  AND student_section = '$student_section'
                  AND status = 'ACTIVE'
            ) AS total
            WHERE
                s.student_class = '$student_class'
                AND s.student_section = '$student_section'
                AND s.status = 'ACTIVE'
                AND t.term = '$term'
                AND t.subject = '$subject'
            ";

            $result = direct_sql($sql);
            $stats = $result['data'][0] ?? [];
            
            // Define report rows
            $report_rows = [
                ['sr' => 1, 'label' => 'Total Students in Class', 'ut1' => $total_students, 'ut1_p' => '100', 'ut2' => $total_students, 'ut2_p' => '100', 'mte' => $total_students, 'mte_p' => '100', 'class' => 'total-row'],
                ['sr' => 2, 'label' => 'No. of Students Appeared', 'ut1' => $stats['ut1_appeared'] ?? 0, 'ut1_p' => $stats['ut1_appeared_percent'] ?? 0, 'ut2' => $stats['ut2_appeared'] ?? 0, 'ut2_p' => $stats['ut2_appeared_percent'] ?? 0, 'mte' => $stats['mte_appeared'] ?? 0, 'mte_p' => $stats['mte_appeared_percent'] ?? 0, 'class' => ''],
                ['sr' => 3, 'label' => 'No. of Students Passed (≥33%)', 'ut1' => $stats['ut1_passed'] ?? 0, 'ut1_p' => $stats['ut1_passed_percent'] ?? 0, 'ut2' => $stats['ut2_passed'] ?? 0, 'ut2_p' => $stats['ut2_passed_percent'] ?? 0, 'mte' => $stats['mte_passed'] ?? 0, 'mte_p' => $stats['mte_passed_percent'] ?? 0, 'class' => 'grade-excellent'],
                ['sr' => 4, 'label' => 'No. of Students Failed (<33%)', 'ut1' => $stats['ut1_failed'] ?? 0, 'ut1_p' => $stats['ut1_failed_percent'] ?? 0, 'ut2' => $stats['ut2_failed'] ?? 0, 'ut2_p' => $stats['ut2_failed_percent'] ?? 0, 'mte' => $stats['mte_failed'] ?? 0, 'mte_p' => $stats['mte_failed_percent'] ?? 0, 'class' => 'grade-poor'],
                ['sr' => 5, 'label' => 'Scored 90% and above', 'ut1' => $stats['ut1_90_plus'] ?? 0, 'ut1_p' => $stats['ut1_90_plus_percent'] ?? 0, 'ut2' => $stats['ut2_90_plus'] ?? 0, 'ut2_p' => $stats['ut2_90_plus_percent'] ?? 0, 'mte' => $stats['mte_90_plus'] ?? 0, 'mte_p' => $stats['mte_90_plus_percent'] ?? 0, 'class' => 'grade-excellent'],
                ['sr' => 6, 'label' => 'Scored 80% - 89.99%', 'ut1' => $stats['ut1_80_89'] ?? 0, 'ut1_p' => $stats['ut1_80_89_percent'] ?? 0, 'ut2' => $stats['ut2_80_89'] ?? 0, 'ut2_p' => $stats['ut2_80_89_percent'] ?? 0, 'mte' => $stats['mte_80_89'] ?? 0, 'mte_p' => $stats['mte_80_89_percent'] ?? 0, 'class' => 'grade-good'],
                ['sr' => 7, 'label' => 'Scored 70% - 79.99%', 'ut1' => $stats['ut1_70_79'] ?? 0, 'ut1_p' => $stats['ut1_70_79_percent'] ?? 0, 'ut2' => $stats['ut2_70_79'] ?? 0, 'ut2_p' => $stats['ut2_70_79_percent'] ?? 0, 'mte' => $stats['mte_70_79'] ?? 0, 'mte_p' => $stats['mte_70_79_percent'] ?? 0, 'class' => 'grade-good'],
                ['sr' => 8, 'label' => 'Scored 50% - 69.99%', 'ut1' => $stats['ut1_50_69'] ?? 0, 'ut1_p' => $stats['ut1_50_69_percent'] ?? 0, 'ut2' => $stats['ut2_50_69'] ?? 0, 'ut2_p' => $stats['ut2_50_69_percent'] ?? 0, 'mte' => $stats['mte_50_69'] ?? 0, 'mte_p' => $stats['mte_50_69_percent'] ?? 0, 'class' => 'grade-average'],
                ['sr' => 9, 'label' => 'Scored 40% - 49.99%', 'ut1' => $stats['ut1_40_49'] ?? 0, 'ut1_p' => $stats['ut1_40_49_percent'] ?? 0, 'ut2' => $stats['ut2_40_49'] ?? 0, 'ut2_p' => $stats['ut2_40_49_percent'] ?? 0, 'mte' => $stats['mte_40_49'] ?? 0, 'mte_p' => $stats['mte_40_49_percent'] ?? 0, 'class' => 'grade-average'],
                ['sr' => 10, 'label' => 'Scored 33% - 39.99%', 'ut1' => $stats['ut1_33_39'] ?? 0, 'ut1_p' => $stats['ut1_33_39_percent'] ?? 0, 'ut2' => $stats['ut2_33_39'] ?? 0, 'ut2_p' => $stats['ut2_33_39_percent'] ?? 0, 'mte' => $stats['mte_33_39'] ?? 0, 'mte_p' => $stats['mte_33_39_percent'] ?? 0, 'class' => 'grade-poor'],
            ];
        ?>
        
        <!-- Report Box -->
        <div class="box box-primary" id="reportContent">
            <div class="box-header">
                <!-- Report Header with Class, Section, Subject, Term -->
                <div class="report-header">
                    <h4><i class="fa fa-file-text"></i> Subject Wise Analysis Report</h4>
                    <div class="report-info">
                        <div class="report-info-item">
                            <strong>Class</strong>
                            <span><?= htmlspecialchars($student_class) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Section</strong>
                            <span><?= htmlspecialchars($student_section) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Subject</strong>
                            <span><?= htmlspecialchars($subject_name) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Term</strong>
                            <span><?= htmlspecialchars($term) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Total Students</strong>
                            <span><?= $total_students ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Generated On</strong>
                            <span><?= date('d-M-Y H:i A') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <!-- Full Marks Info -->
                <div class="alert alert-info no-print">
                    <i class="fa fa-info-circle"></i>
                    <strong>Full Marks:</strong>
                    UT-1 = 30 | UT-2 = 30 |
                    MTE = <?= ($student_class == 'I' || $student_class == 'II') ? '60' : '80' ?> |
                    <strong>Pass Marks (33%):</strong>
                    UT = 9.9 |
                    MTE = <?= ($student_class == 'I' || $student_class == 'II') ? '19.8' : '26.4' ?>
                </div>

                
                <div class="table-responsive">
                    <table id="reportTable" class="table table-bordered table-hover display nowrap margin-top-10">
                        <thead>
                            <tr class='op-thead'>
                                <th rowspan="2" class='text-center' style="vertical-align: middle;">Sr. No.</th>
                                <th rowspan="2" class='text-center' style="vertical-align: middle;">Particulars</th>
                                <th colspan="2" class='text-center'>UT-1 (30)</th>
                                <th colspan="2" class='text-center'>UT-2 (30)</th>
                                <?php if($student_class == 'I' || $student_class == 'II'): ?>
                                    <th colspan="2" class='text-center'>MTE (60)</th>
                                <?php else: ?>
                                    <th colspan="2" class='text-center'>MTE (80)</th>
                                <?php endif; ?>
                            </tr>
                            <tr class='op-thead'>
                                <th class='text-center'>No.</th>
                                <th class='text-center'>%</th>
                                <th class='text-center'>No.</th>
                                <th class='text-center'>%</th>
                                <th class='text-center'>No.</th>
                                <th class='text-center'>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_rows as $row): ?>
                            <tr class="<?= $row['class'] ?>">
                                <td class='text-center'><?= $row['sr'] ?></td>
                                <td><?= $row['label'] ?></td>
                                <td class='text-center'><?= $row['ut1'] ?></td>
                                <td class='text-center'><?= $row['ut1_p'] ?>%</td>
                                <td class='text-center'><?= $row['ut2'] ?></td>
                                <td class='text-center'><?= $row['ut2_p'] ?>%</td>
                                <td class='text-center'><?= $row['mte'] ?></td>
                                <td class='text-center'><?= $row['mte_p'] ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Summary Cards -->
                <div class="row margin-top-20">
                    <div class="col-md-4">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">
                                    Overall Pass Rate (MTE <?= ($student_class == 'I' || $student_class == 'II') ? '60' : '80' ?>)
                                </span>
                                <span class="info-box-number"><?= $stats['mte_passed_percent'] ?? 0 ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-star"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Distinction (90%+) in MTE</span>
                                <span class="info-box-number"><?= $stats['mte_90_plus'] ?? 0 ?> Students</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box grade-poor">
                            <span class="info-box-icon"><i class="fa fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Failed in MTE</span>
                                <span class="info-box-number"><?= $stats['mte_failed'] ?? 0 ?> Students</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
        
    </section>
</div>

<!-- Hidden form for PDF download -->
<form id="pdfForm" method="post" action="download_subject_report_pdf.php" target="_blank" style="display:none;">
    <input type="hidden" name="student_class" value="<?= htmlspecialchars($student_class) ?>">
    <input type="hidden" name="student_section" value="<?= htmlspecialchars($student_section) ?>">
    <input type="hidden" name="subject" value="<?= htmlspecialchars($subject) ?>">
    <input type="hidden" name="subject_name" value="<?= htmlspecialchars($subject_name) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
</form>

<script>
function downloadPDF() {
    document.getElementById('pdfForm').submit();
}

// Dynamic subject loading based on class
$(document).ready(function() {
    $('#student_class').change(function() {
        var classVal = $(this).val();
        // You can add AJAX here to load subjects based on class
    });
});
</script>

<?php require_once('system/footer2.php'); ?>