<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = $log_date = null;
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];
if (isset($_REQUEST['log_date']))
    $log_date = $_REQUEST['log_date'];
else
    $log_date = date('Y-m-d');

// Fetch assigned classes/sections for Teachers
if ($user_type == 'TEACHER') {
    $emp = get_teacher_employee();
    $teacher_id = $emp['id'] ?? null;

    if (!empty($emp['class_teacher'])) {
        $parts = explode('-', $emp['class_teacher']);
        $ct_class = trim($parts[0]);
        $ct_section = isset($parts[1]) ? trim($parts[1]) : '';
        $class_list = [$ct_class];
        $section_list = [$ct_section];
    } else {
        $class_list = [];
        $section_list = [];
    }
} else {
    // For Admin/Dev, fetch all classes and sections
    if (empty($class_list)) {
        $assigned_classes = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
        $class_list = array_column($assigned_classes['data'] ?? [], 'student_class');
    }
    if (empty($section_list)) {
        $assigned_sections = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
        $section_list = array_column($assigned_sections['data'] ?? [], 'student_section');
    }
}

?>
<title> <?php echo $inst_name; ?> - Homework Generator </title>
<style>
    @media print {
        /* Hide all page layout elements */
        .main-header, .main-sidebar, .content-header, .box, .no-print, header, sidebar, footer {
            display: none !important;
        }
        
        /* Reset margins/paddings on parents of #print-area to ensure it starts at top-left */
        html, body, .wrapper, .content-wrapper, .content {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            width: 100% !important;
            height: 100% !important;
            overflow: visible !important;
            position: static !important;
        }
        
        /* Make only .print-page and its children visible */
        body * {
            visibility: hidden;
        }
        
        .print-page, .print-page * {
            visibility: visible;
        }

        .print-page {
            page-break-after: always !important;
            height: 100vh !important;
            box-sizing: border-box !important;
            position: relative !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .print-page:last-child {
            page-break-after: avoid !important;
        }

        #print-area {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        .grid-6 {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            grid-template-rows: repeat(3, 1fr) !important;
            gap: 10px !important;
            width: 100% !important;
            height: 98vh !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .grid-4 {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            grid-template-rows: repeat(2, 1fr) !important;
            gap: 15px !important;
            width: 100% !important;
            height: 98vh !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        
        body {
            overflow: hidden;
        }
    }

    /* Screen layout preview grids */
    .print-page {
        margin-bottom: 30px;
        background: #fff;
        border: 1.5px dashed #cbd5e1;
        padding: 15px;
        border-radius: 8px;
        position: relative;
    }

    .grid-6 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(3, 1fr);
        gap: 15px;
        width: 100%;
        box-sizing: border-box;
    }

    .grid-4 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        gap: 20px;
        box-sizing: border-box;
    }

    .hw-slip {
        border: 2px solid #000 !important;
        background: #fff !important;
        border-radius: 6px !important;
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        padding: 15px !important;
        height: 100% !important;
        overflow: hidden !important;
    }

    .slip-compact {
        padding: 8px !important;
        font-size: 11px !important;
    }

    .slip-compact .hw-header-v2 {
        margin-bottom: 5px !important;
    }

    .slip-compact .hw-header-v2 h3 {
        font-size: 12px !important;
    }

    .slip-compact .hw-table-v2 th,
    .slip-compact .hw-table-v2 td {
        padding: 3px 5px !important;
        font-size: 10px !important;
    }

    .slip-large {
        padding: 18px !important;
        font-size: 13.5px !important;
    }

    .slip-large .hw-header-v2 {
        margin-bottom: 12px !important;
    }

    .slip-large .hw-header-v2 h3 {
        font-size: 15px !important;
    }

    .slip-large .hw-table-v2 th,
    .slip-large .hw-table-v2 td {
        padding: 6px 10px !important;
        font-size: 12px !important;
    }

    .hw-header-v2 {
        text-align: center !important;
    }

    .hw-header-v2 h3 {
        margin: 0 0 2px 0 !important;
        font-weight: 800 !important;
        color: #000 !important;
        text-transform: uppercase !important;
    }

    .hw-table-v2 {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1.5px solid #000 !important;
        margin-top: 3px !important;
    }

    .hw-table-v2 th,
    .hw-table-v2 td {
        border: 1.5px solid #000 !important;
        text-align: left !important;
        vertical-align: top !important;
    }

    .hw-table-v2 th {
        background-color: #df9d73 !important;
        color: #000 !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
    }

    .hw-footer-v2 {
        margin-top: 8px !important;
        display: flex !important;
        justify-content: space-between !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #000 !important;
    }
    
    .slip-compact .hw-footer-v2 {
        font-size: 8.5px !important;
        margin-top: 5px !important;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> Homework Generator <small>Module 4</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Homework Generator</li>
        </ol>
    </section>

    <section class="content">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="m-0 font-weight-bold text-dark">Homework Generator</h4>
                <p class="text-muted mb-0">Generate and print homework slips for students</p>
            </div>
        </div>

        <div class="box box-primary premium-card">
            <div class="box-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Class</label>
                                <select class="form-control form-control-premium" name="student_class" id="class_select"
                                    required>
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Section</label>
                                <select class="form-control form-control-premium" name="student_section" required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subject (Optional)</label>
                                <select class="form-control form-control-premium" name="subject" id="subject_select">
                                    <option value="">All Subjects</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="log_date" class="form-control form-control-premium"
                                    value="<?= $log_date ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-premium btn-premium-primary btn-block">Generate
                                Slips</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($student_class && $student_section && $log_date): ?>
            <div class="row mb-3 no-print">
                <div class="col-md-12 text-right">
                    <button class="btn btn-danger" onclick="window.print()"><i class="fa fa-print"></i> Print Homework
                        Slips</button>
                </div>
            </div>

            <?php
            // Fetch logbook
            $log_sql = "SELECT * FROM logbook WHERE date='$log_date' AND student_class='$student_class' AND student_section='$student_section'";
            $log_res = direct_sql($log_sql);

            if ($log_res['count'] > 0) {
                $log_id = $log_res['data'][0]['id'];

                // Fetch homework periods
                $hw_data = [];
                $target_subject = $_REQUEST['subject'] ?? '';

                for ($p = 1; $p <= 8; $p++) {
                    $p_res = get_all('logbook_period', '*', ['logbook_id' => $log_id, 'period_no' => $p]);
                    if ($p_res['count'] > 0 && !empty($p_res['data'][0]['homework'])) {
                        $subj_id = $p_res['data'][0]['subject'];

                        // Fetch subject name
                        $sub_info = get_data('subject', $subj_id);
                        $subj_name = ($sub_info['count'] > 0) ? $sub_info['data']['subject_name'] : $subj_id;

                        // If a specific subject is selected, filter by it
                        if ($target_subject != '' && $subj_id != $target_subject) {
                            continue;
                        }

                        $hw_data[] = [
                            'period' => $p,
                            'subject' => $subj_name,
                            'homework' => $p_res['data'][0]['homework']
                        ];
                    }
                }

                if (count($hw_data) > 0) {
                    $c_upper = strtoupper($student_class);
                    $is_junior = in_array($c_upper, ['NUR', 'LKG', 'UKG', 'NURSERY']);
                    $chunk_size = $is_junior ? 6 : 4;
                    $grid_class = $is_junior ? 'grid-6' : 'grid-4';
                    $slip_class = $is_junior ? 'slip-compact' : 'slip-large';

                    if (!function_exists('get_ordinal')) {
                        function get_ordinal($number) {
                            $ends = array('th','st','nd','rd','th','th','th','th','th','th');
                            if ((($number % 100) >= 11) && (($number % 100) <= 13))
                                return $number. 'th';
                            else
                                return $number. $ends[$number % 10];
                        }
                    }

                    // Fetch active students for this class and section
                    $students_q = "SELECT student_name, student_roll FROM student WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY student_roll ASC";
                    $students_res = direct_sql($students_q);
                    $students_list = $students_res['data'] ?? [];
                    if (empty($students_list)) {
                        $students_list = [
                            ['student_name' => '____________________', 'student_roll' => '____']
                        ];
                    }

                    // Group students into page chunks (6 for junior, 4 for senior)
                    $student_chunks = array_chunk($students_list, $chunk_size);
                    ?>
                    <?php foreach ($student_chunks as $chunk): ?>
                        <div class="print-page">
                            <div id="print-area" class="<?= $grid_class ?>">
                                <?php foreach ($chunk as $student): ?>
                                    <div class="hw-slip <?= $slip_class ?>">
                                        <div class="hw-header-v2">
                                            <h3>R M PUBLIC SCHOOL,</h3>
                                            <div style="font-size: 1.05em; font-weight: 700;"><?= $is_junior ? 'Montessori ' : '' ?><?= htmlspecialchars($inst_name) ?></div>
                                            <div style="font-size: 0.9em; font-weight: 800; letter-spacing: 0.5px; margin-top: 2px;">HOME WORK</div>
                                            <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 0.85em; margin-top: 4px; border-bottom: 1.5px solid #000; padding-bottom: 2px;">
                                                <span><?= htmlspecialchars($student_class) ?>-<?= htmlspecialchars($student_section) ?> | Roll: <?= htmlspecialchars($student['student_roll']) ?></span>
                                                <span><?= date('d.m.Y', strtotime($log_date)) ?></span>
                                            </div>
                                            <div style="font-weight: 800; font-size: 0.85em; text-align: left; margin-top: 2px; padding-bottom: 2px; border-bottom: 1.5px solid #000;">
                                                Name: <span style="text-transform: uppercase;"><?= htmlspecialchars($student['student_name']) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                            <table class="hw-table-v2">
                                                <thead>
                                                    <tr>
                                                        <th width="15%" style="text-align: center;">Pd.</th>
                                                        <th width="25%">Subject</th>
                                                        <th width="60%">Homework Assigned</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($hw_data as $hw): ?>
                                                        <tr>
                                                            <td style="text-align: center; font-weight: bold;"><?= get_ordinal($hw['period']) ?></td>
                                                            <td style="font-weight: 700;"><?= htmlspecialchars($hw['subject']) ?></td>
                                                            <td><?= nl2br(htmlspecialchars($hw['homework'])) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="hw-footer-v2">
                                            <span>Please check and sign.</span>
                                            <span>Parent Signature: __________________</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php
                } else {
                    echo "<div class='alert alert-warning no-print'>No homework found in the Log Book for this date. Ensure teachers have entered homework and signed off.</div>";
                }
            } else {
                echo "<div class='alert alert-danger no-print'>No Log Book found for this Class & Section on " . date('d M Y', strtotime($log_date)) . ".</div>";
            }
            ?>
        <?php endif; ?>
    </section>
</div>

<script>
    $(document).ready(function () {
        function loadSubjects() {
            let stu_class = $('#class_select').val();
            let stu_section = $('select[name="student_section"]').val();
            let current_subject = "<?= $_REQUEST['subject'] ?? '' ?>";

            if (stu_class && stu_section) {
                $.ajax({
                    url: 'system/master_process.php?task=get_teacher_subjects',
                    type: 'POST',
                    data: { student_class: stu_class, student_section: stu_section },
                    success: function (res) {
                        $('#subject_select').html('<option value="">All Subjects</option>' + res);
                        if (current_subject) {
                            $('#subject_select').val(current_subject);
                        }
                    }
                });
            }
        }

        $('#class_select, select[name="student_section"]').on('change', loadSubjects);
        loadSubjects();
    });
</script>

<?php require_once('system/footer2.php'); ?>