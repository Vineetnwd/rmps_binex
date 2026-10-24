<?php
require_once('system/header.php');
require_once('system/menu.php');

extract($_GET);

$branch_id = $_SESSION['branch_id'] ?? 1;
$assigned_classes = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
$class_list = array_column($assigned_classes['data'] ?? [], 'student_class');

$assigned_sections = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
$section_list = array_column($assigned_sections['data'] ?? [], 'student_section');
?>
<style>
    th {
        background-color: #000 !important;
        color: #fff !important;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Consolidated Sheet</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Marks Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Generate Report</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" onclick="exportexcel()" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="exportpdf()" title="Export to PDF">
                        Export to PDF
                    </button>
                </div>
            </div>

            <div class="box-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label>Class</label>
                        <select name="student_class" class="form-control" required>
                            <?php dropdown($class_list ?? [], $_GET['student_class'] ?? ''); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Section</label>
                        <select name="student_section" class="form-control" required>
                            <?php dropdown($section_list ?? [], $_GET['student_section'] ?? ''); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Exam</label>
                        <select name="exam_id" class="form-control" required>
                            <?= dropdown_list('exam_list', 'id', 'exam_name', $_GET['exam_id'] ?? '') ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Sort By</label>
                        <select name="sort_by" class="form-control">
                            <option value="roll" <?= ($_GET['sort_by'] ?? '') == 'roll' ? 'selected' : '' ?>>Roll-wise
                            </option>
                            <option value="rank" <?= ($_GET['sort_by'] ?? '') == 'rank' ? 'selected' : '' ?>>Rank-wise
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <label style="display:block;">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Show Report</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        if (!empty($_GET['student_class']) && !empty($_GET['student_section']) && !empty($_GET['exam_id'])) {

            $student_class = $_GET['student_class'];
            $student_section = $_GET['student_section'];
            $exam_id = $_GET['exam_id'];

            // ✅ Get exam name and type
            $exam_data = get_data('exam_list', $exam_id);
            $exam_name = $exam_data['data']['exam_name'] ?? '';
            $exam_name_lower = strtolower($exam_name);
            $is_periodic = (strpos($exam_name_lower, 'periodic') !== false || strpos($exam_name_lower, 'pt') !== false);

            // ✅ Select subjects based on class
            $subjects = direct_sql("
        SELECT * FROM subject 
        WHERE FIND_IN_SET('$student_class', student_class) > 0
        AND status='ACTIVE' 
        AND (show_in_report = 'YES' OR show_in_report IS NULL)
        AND subject_type = 'Scholastic'
        AND branch_id='$branch_id'
        ORDER BY subject_order, subject_name
    ")['data'];

            // ✅ Filter subjects based on exam type
            $filtered_subjects = [];
            foreach ($subjects as $sub) {
                $sname = strtoupper(trim($sub['subject_name']));

                if (in_array($exam_name, ['UNIT TEST 1', 'UNIT TEST 2', 'UNIT TEST 3', 'UNIT TEST 4'])) {
                    if (
                        in_array($sname, [
                            'ENGLISH LITERATURE',
                            'ENGLISH GRAMMAR',
                            'HINDI LITERATURE',
                            'HINDI GRAMMAR',
                            'SANSKRIT LITERATURE',
                            'URDU LITERATURE',
                            'SANSKRIT GRAMMAR',
                            'URDU GRAMMAR'
                        ]) ||
                        !preg_match('/ENGLISH$|HINDI$|SANSKRIT \/ URDU$/i', $sname)
                    ) {
                        $filtered_subjects[] = $sub;
                    }

                } elseif (in_array($exam_name, ['TERM 1', 'TERM 2'])) {
                    if (
                        in_array($sname, ['ENGLISH', 'HINDI', 'SANSKRIT / URDU']) ||
                        (!preg_match('/LITERATURE$|GRAMMAR$/i', $sname))
                    ) {
                        $filtered_subjects[] = $sub;
                    }
                } else {
                    $filtered_subjects[] = $sub;
                }
            }
            $subjects = $filtered_subjects;

            // ✅ Build subject configurations (columns, max marks, language eligibility)
            $subject_configs = [];
            foreach ($subjects as $sub) {
                $sub_id = $sub['id'];
                $sname = $sub['subject_name'];
                $is_extra = (($sub['is_extra'] ?? 'NO') == 'YES');
                $is_both_lang = (stripos($sname, 'urdu') !== false && stripos($sname, 'sanskrit') !== false);
                $is_urdu_subject = (!$is_both_lang && stripos($sname, 'urdu') !== false);
                $is_sanskrit_subject = (!$is_both_lang && stripos($sname, 'sanskrit') !== false);

                $columns = [];
                if ($is_periodic) {
                    $sea_max = get_pt_max_mark($student_class, $sub_id, 'sea', $branch_id);
                    $ma_max = get_pt_max_mark($student_class, $sub_id, 'ma', $branch_id);
                    $pt_max = get_pt_max_mark($student_class, $sub_id, 'pt', $branch_id);

                    if ($sea_max !== '-' && $sea_max !== '' && $sea_max !== null) {
                        $columns[] = ['key' => 'sea', 'title' => 'SEA', 'max' => $sea_max];
                    }
                    if ($ma_max !== '-' && $ma_max !== '' && $ma_max !== null) {
                        $columns[] = ['key' => 'ma', 'title' => 'MA', 'max' => $ma_max];
                    }
                    if ($pt_max !== '-' && $pt_max !== '' && $pt_max !== null) {
                        $columns[] = ['key' => 'pt', 'title' => 'PT', 'max' => $pt_max];
                    }
                    if (empty($columns)) {
                        $columns[] = ['key' => 'sea', 'title' => 'SEA', 'max' => '-'];
                        $columns[] = ['key' => 'ma', 'title' => 'MA', 'max' => '-'];
                        $columns[] = ['key' => 'pt', 'title' => 'PT', 'max' => '-'];
                    }
                } else {
                    // Regular Exams (Term 1, Term 2, Half Yearly, Annual, etc.)
                    $nb_max = '-';
                    $ma_max = '-';
                    $mo_max = '-';
                    $mm_res = direct_sql("SELECT nb, ma, mo FROM max_marks WHERE class='$student_class' AND exam_id='$exam_id' AND (subject_id='$sub_id' OR subject_id='' OR subject_id IS NULL) AND branch_id='$branch_id' ORDER BY subject_id DESC LIMIT 1");
                    if ($mm_res['count'] > 0) {
                        $nb_max = $mm_res['data'][0]['nb'] ?? '-';
                        $ma_max = $mm_res['data'][0]['ma'] ?? '-';
                        $mo_max = $mm_res['data'][0]['mo'] ?? '-';
                    }

                    if ($nb_max !== '-' && $nb_max !== '' && $nb_max !== null) {
                        $columns[] = ['key' => 'nb', 'title' => 'NB', 'max' => $nb_max];
                    }
                    if ($ma_max !== '-' && $ma_max !== '' && $ma_max !== null) {
                        $columns[] = ['key' => 'ma', 'title' => 'MA', 'max' => $ma_max];
                    }
                    if ($mo_max !== '-' && $mo_max !== '' && $mo_max !== null) {
                        $columns[] = ['key' => 'mo', 'title' => 'MO', 'max' => $mo_max];
                    }
                    if (empty($columns)) {
                        $columns[] = ['key' => 'nb', 'title' => 'NB', 'max' => '-'];
                        $columns[] = ['key' => 'ma', 'title' => 'MA', 'max' => '-'];
                        $columns[] = ['key' => 'mo', 'title' => 'MO', 'max' => '-'];
                    }
                }

                $sub_total_max = 0;
                foreach ($columns as $col) {
                    if (is_numeric($col['max'])) {
                        $sub_total_max += (float) $col['max'];
                    }
                }

                $colspan = count($columns) + 1; // +1 for Total column
        
                $subject_configs[$sub_id] = [
                    'sub' => $sub,
                    'is_extra' => $is_extra,
                    'is_both_lang' => $is_both_lang,
                    'is_urdu_subject' => $is_urdu_subject,
                    'is_sanskrit_subject' => $is_sanskrit_subject,
                    'columns' => $columns,
                    'sub_total_max' => $sub_total_max,
                    'colspan' => $colspan
                ];
            }

            // ✅ Get students
            $students = get_all('student', '*', [
                'student_class' => $student_class,
                'student_section' => $student_section,
                'status' => 'ACTIVE',
                'branch_id' => $branch_id
            ], 'student_roll ASC')['data'];

            // Fetch all tabulations for this exam and class (including nb, mo)
            $all_tabs_res = direct_sql("
        SELECT t.student_id, t.subject, t.sea, t.ma, t.pt, t.nb, t.mo 
        FROM tabulation t 
        JOIN student s ON t.student_id = s.id 
        WHERE t.exam_id='$exam_id' 
          AND t.branch_id='$branch_id' 
          AND s.student_class='$student_class' 
          AND s.student_section='$student_section'
    ");
            $tabulations = [];
            if (isset($all_tabs_res['data']) && !empty($all_tabs_res['data'])) {
                foreach ($all_tabs_res['data'] as $row) {
                    $tabulations[$row['student_id']][$row['subject']] = $row;
                }
            }

            // Calculate Grand Total for each student and rank them
            $student_stats = [];
            foreach ($students as $stu) {
                $grand_total = 0;
                $student_max_total = 0;
                $student_urdu = $stu['urdu'] ?? 0;
                $is_urdu_student = (!empty($student_urdu) && ($student_urdu == '1' || $student_urdu === 1));

                foreach ($subjects as $sub) {
                    $cfg = $subject_configs[$sub['id']];

                    // Language eligibility check
                    if ($cfg['is_urdu_subject'] && !$is_urdu_student) {
                        continue;
                    }
                    if ($cfg['is_sanskrit_subject'] && $is_urdu_student) {
                        continue;
                    }

                    $tab = $tabulations[$stu['id']][$sub['id']] ?? [];

                    $has_marks = false;
                    $sub_obtained = 0;
                    foreach ($cfg['columns'] as $col) {
                        $val = $tab[$col['key']] ?? '';
                        if (is_numeric($val)) {
                            $sub_obtained += (float) $val;
                            $has_marks = true;
                        }
                    }

                    $grand_total += $sub_obtained;
                    $student_max_total += $cfg['sub_total_max'];
                }
                $percentage = ($student_max_total > 0) ? round(($grand_total / $student_max_total) * 100, 2) : 0;

                $student_stats[$stu['id']] = [
                    'grand_total' => $grand_total,
                    'student_max_total' => $student_max_total,
                    'percentage' => $percentage,
                    'grade' => grade($percentage)
                ];
            }

            // Sort to determine ranks
            $totals = [];
            foreach ($student_stats as $id => $stat) {
                $totals[$id] = $stat['grand_total'];
            }
            arsort($totals);

            $rank = 1;
            $real_rank = 1;
            $prev_total = null;
            foreach ($totals as $id => $total) {
                if ($prev_total !== null && $total < $prev_total) {
                    $rank = $real_rank;
                }
                $student_stats[$id]['rank'] = $rank;
                $prev_total = $total;
                $real_rank++;
            }

            // Perform sorting based on selection
            if (($_GET['sort_by'] ?? 'roll') == 'rank') {
                usort($students, function ($a, $b) use ($student_stats) {
                    $rankA = $student_stats[$a['id']]['rank'] ?? 9999;
                    $rankB = $student_stats[$b['id']]['rank'] ?? 9999;
                    if ($rankA === $rankB) {
                        return (int) $a['student_roll'] <=> (int) $b['student_roll']; // tie-breaker: roll number
                    }
                    return $rankA <=> $rankB;
                });
            }
            ?>

            <div class="box box-default mt-3">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">
                        <?= strtoupper($exam_name) ?> - CLASS <?= $student_class ?> (<?= $student_section ?>)
                    </h3>
                </div>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped text-center" id="tbl_a">
                        <thead>
                            <tr>
                                <th rowspan="2">Roll</th>
                                <th rowspan="2">Adm. No.</th>
                                <th rowspan="2">Student Name</th>
                                <?php
                                foreach ($subjects as $sub) {
                                    $cfg = $subject_configs[$sub['id']];
                                    $heading_title = htmlspecialchars($sub['subject_name']);
                                    if ($cfg['sub_total_max'] > 0) {
                                        $heading_title .= " ({$cfg['sub_total_max']})";
                                    }
                                    echo "<th colspan='{$cfg['colspan']}'>$heading_title</th>";
                                }
                                ?>
                                <th rowspan="2">Grand Total</th>
                                <th rowspan="2">Percentage</th>
                                <th rowspan="2">Grade</th>
                                <th rowspan="2">Rank</th>
                            </tr>
                            <tr>
                                <?php
                                foreach ($subjects as $sub) {
                                    $cfg = $subject_configs[$sub['id']];
                                    foreach ($cfg['columns'] as $col) {
                                        echo "<th>{$col['title']} ({$col['max']})</th>";
                                    }
                                    echo "<th>Total" . ($cfg['sub_total_max'] > 0 ? " ({$cfg['sub_total_max']})" : "") . "</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($students as $stu) {
                                echo "<tr>";
                                echo "<td>{$stu['student_roll']}</td>";
                                echo "<td>{$stu['student_admission']}</td>";
                                echo "<td class='text-start'>{$stu['student_name']}</td>";

                                $student_urdu = $stu['urdu'] ?? 0;
                                $is_urdu_student = (!empty($student_urdu) && ($student_urdu == '1' || $student_urdu === 1));

                                foreach ($subjects as $sub) {
                                    $cfg = $subject_configs[$sub['id']];
                                    $tab = $tabulations[$stu['id']][$sub['id']] ?? [];

                                    // Check if this subject is eligible for this student
                                    $is_eligible = true;
                                    if ($cfg['is_urdu_subject'] && !$is_urdu_student) {
                                        $is_eligible = false;
                                    } elseif ($cfg['is_sanskrit_subject'] && $is_urdu_student) {
                                        $is_eligible = false;
                                    }

                                    if (!$is_eligible) {
                                        // Not opted: display neutral '-'
                                        foreach ($cfg['columns'] as $col) {
                                            echo "<td style='background-color:#f8fafc; color:#94a3b8;'>-</td>";
                                        }
                                        echo "<td style='background-color:#f8fafc; color:#94a3b8;'>-</td>";
                                        continue;
                                    }

                                    $sub_obtained = 0;
                                    $has_any_num = false;

                                    foreach ($cfg['columns'] as $col) {
                                        $val = $tab[$col['key']] ?? '';
                                        if (is_numeric($val)) {
                                            $sub_obtained += (float) $val;
                                            $has_any_num = true;
                                            echo "<td>$val</td>";
                                        } else {
                                            $display = (empty($val) && $val !== '0' && $val !== 0) ? "AB" : $val;
                                            $bg = ($display === "AB") ? "style='background-color:#f8d7da'" : "";
                                            echo "<td $bg>$display</td>";
                                        }
                                    }

                                    // Total column for this subject
                                    if ($has_any_num) {
                                        echo "<td><b>$sub_obtained</b></td>";
                                    } else {
                                        echo "<td style='background-color:#f8d7da'>AB</td>";
                                    }
                                }

                                // Output the 4 summary columns
                                $stats = $student_stats[$stu['id']];
                                echo "<td><b>{$stats['grand_total']}</b></td>";
                                echo "<td>{$stats['percentage']}%</td>";
                                echo "<td>{$stats['grade']}</td>";
                                echo "<td>{$stats['rank']}</td>";

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php } ?>
    </section>
</div>

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        form,
        .btn,
        .breadcrumb,
        .content-header {
            display: none !important;
        }

        table {
            width: 100vw;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 3px;
            word-wrap: break-word;
        }

        th {
            text-align: center;
            vertical-align: middle !important;
            font-weight: bold;
        }

        td {
            vertical-align: middle !important;
        }
    }
</style>

<?php require_once('system/footer.php'); ?>

<!-- Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportexcel() {
        var table = document.getElementById("tbl_a");
        if (!table) { alert("No data to export!"); return; }
        var wb = XLSX.utils.table_to_book(table, { sheet: "Consolidated Marks" });
        XLSX.writeFile(wb, "Consolidated_Marks.xlsx");
    }
</script>

<!-- PDF Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
    function exportpdf() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: "landscape",
            unit: "pt",
            format: "legal"
        });

        doc.text("Consolidated Sheet <?= $_GET['student_class'] . ' - ' . $_GET['student_section'] ?>", 40, 40); // title

        doc.autoTable({
            html: '#tbl_a',
            startY: 60, // space for title
            theme: 'grid',
            headStyles: {
                fillColor: [0, 0, 0],       // black background
                textColor: [255, 255, 255], // white text
                lineColor: [255, 255, 255], // white border
                lineWidth: 0.5
            },
            bodyStyles: {
                lineColor: [0, 0, 0], // black border for td
                lineWidth: 0.2,
                fontSize: 10,
                cellPadding: 3
            },
            didParseCell: function (data) {
                // Highlight "AB" cells with light red background
                if (data.cell.text[0] === "AB") {
                    data.cell.styles.fillColor = [248, 215, 218]; // light red
                }
            }
        });

        doc.save('Consolidated_<?= $_GET['student_class'] . '_' . $_GET['student_section'] ?>.pdf');
    }
</script>