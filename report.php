<?php
require_once('system/header.php');
require_once('system/menu.php');

$exam_id = $student_class = $student_section = null;
if (isset($_REQUEST['exam_id']))
    $exam_id = $_REQUEST['exam_id'];
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];

function fmt_val($v)
{
    if ($v === null || $v === '' || $v === '0' || $v === 0 || $v === '0.00' || $v === 0.0)
        return 'AB';
    if (!is_numeric($v))
        return htmlspecialchars($v);
    if (intval($v) == $v)
        return intval($v);
    return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
}

$branch_id = $_SESSION['branch_id'] ?? 1;
$principal = get_data('branch_list', $branch_id)['data'] ?? '';
$principal = $principal ? $base_url . 'upload/' . $principal['principal_sign'] : 'assets/images/new_sign.png';

$class_list = array_column(direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY student_class ASC")['data'] ?? [], 'student_class');
$section_list = array_column(direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY student_section ASC")['data'] ?? [], 'student_section');
?>
<style>
    /* ====================================================
       GLOBAL BACKGROUND & OUTER MARGIN (PURE WHITE)
       ==================================================== */
    html,
    body {
        background: #ffffff !important;
        background-color: #ffffff !important;
        margin: 0;
        padding: 0;
    }

    .wrapper,
    .content-wrapper,
    section.content,
    .content,
    .box,
    .box-default,
    .box-body {
        background: #ffffff !important;
        background-color: #ffffff !important;
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    .content-wrapper {
        text-transform: uppercase !important;
        font-size: 12px !important;
        font-weight: 400 !important;
    }

    /* Remove AdminLTE pseudo-elements causing extra space on Page 1 */
    .wrapper::before,
    .wrapper::after,
    .content-wrapper::before,
    .content-wrapper::after,
    .content::before,
    .content::after,
    .box::before,
    .box::after {
        display: none !important;
        content: none !important;
    }

    /* ── REPORT CARD: fixed A4 height, flex column ─────────────────────────── */
    .report-card {
        /*height: 297mm;*/
        width: 100%;
        margin: 0 auto 20px auto;
        padding: 10px 20px 5px 20px;
        box-sizing: border-box;
        background: #ffffff !important;
        border: 1px solid #cfcfcf;
        border-radius: 0 !important;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        page-break-after: always;
        break-after: page;
        page-break-inside: avoid;
        break-inside: avoid;
        display: flex;
        flex-direction: column;
        height: auto;
        min-height: calc(100vh - 12mm);
        box-sizing: border-box;

    }

    /* ── HEADERS ────────────────────────────────────────────────────────────── */
    .rc-header {
        background: #CC0000;
        color: #fff;
        text-align: center;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 3px 5px;
        font-size: 16px;
        flex-shrink: 0;
    }

    .rc-header2 {
        background: #FFCC00;
        color: #000;
        text-align: center;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 3px 5px;
        font-size: 12px;
        border-bottom: 2px solid #CC0000;
        flex-shrink: 0;
    }

    /* ── STUDENT INFO TABLE ─────────────────────────────────────────────────── */
    .stu-info-table {
        width: 100%;
        border-collapse: collapse;
        flex-shrink: 0;
    }

    .stu-info-table td {
        border: 1px solid #333;
        padding: 4px 5px;
        font-weight: 700;
        font-size: 14px;
        vertical-align: middle;
    }

    .stu-info-table td.label-cell {
        background: #C5E0B4;
        width: 20%;
    }

    .stu-photo-cell {
        width: 15%;
        border: 1px solid #CC0000 !important;
        background: #CC0000;
        padding: 4px;
        text-align: center;
        vertical-align: middle;
    }

    .stu-photo-circle {
        width: 90px;
        height: 120px;
        /*border-radius: 50%;*/
        background: #fff;
        /*border: 2px solid #000;*/
        display: inline-block;
        object-fit: cover;
    }

    /* ── MARKS TABLE ─────────────────────────────────────────────────────────── */
    .marks-table {
        width: 100%;
        border-collapse: collapse;
        /*margin-top: 3px;*/
        border: 1px solid #000;
        table-layout: fixed;
        flex-shrink: 0;
    }

    .marks-table th,
    .marks-table td {
        border: 1px solid #000;
        padding: 3px 4px;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        vertical-align: middle;
    }

    .marks-table th.subj-head {
        background: #FFCC00;
        text-align: left;
    }

    .marks-table th.mark-head {
        background: #A9D18E;
    }

    .marks-table td.subj-name {
        text-align: left;
        font-weight: 600;
    }

    /* ── TOTALS BAR ─────────────────────────────────────────────────────────── */
    .totals-bar {
        background: #1F4E79;
        color: #fff;
        display: flex;
        font-weight: 700;
        margin-top: 3px;
        padding: 4px 8px;
        border: 1px solid #000;
        font-size: 11px;
        flex-shrink: 0;
    }

    .totals-bar div {
        flex: 1;
        border-right: 1px solid #fff;
        padding: 1px 6px;
    }

    .totals-bar div:last-child {
        border-right: none;
    }

    .mid-section {
        display: flex;
        width: 100%;
        align-items: stretch;
        flex: 1 1 auto;
        min-height: 0;
        margin-top: 3px;
        overflow: hidden;
    }

    .mid-left {
        width: 49%;
        flex: 0 0 49%;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .mid-chart {
        width: 49%;
        flex: 0 0 49%;
        max-width: 49%;
        min-width: 0;
        min-height: 0;
        margin-left: 2%;
        border: 1px solid #000;
        overflow: hidden;
        box-sizing: border-box;
    }

    .mid-chart>div {
        width: 100% !important;
        height: 100% !important;
        min-width: 0 !important;
        min-height: 0 !important;
    }

    .mid-chart svg {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .co-sch-table,
    .remarks-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .co-sch-table tr,
    .remarks-table tr {
        height: auto;
    }

    .co-sch-table td,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 12px;
        font-weight: 600;
        vertical-align: middle;
        box-sizing: border-box;
    }

    /* ── HEALTH & ATTENDANCE ────────────────────────────────────────────────── */
    .health-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-top: 4px;
        text-align: center;
        flex-shrink: 0;
    }

    .health-table td {
        padding: 3px 5px;
        border: 1px solid #000;
        font-size: 11px;
        font-weight: 700;
    }

    .health-table .hh {
        background: #1F4E79;
        color: #fff;
    }

    .att-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-top: 4px;
        flex: 1 1 auto;
        min-height: 0;
    }

    .att-table td {
        padding: 4px 8px;
        font-size: 20px;
        font-weight: 800;
        border-bottom: 1px solid #000;
    }

    /* ── GRADING SCALE ──────────────────────────────────────────────────────── */
    .grading-scale {
        background: #CC0000;
        color: #fff;
        font-size: 10px;
        text-align: center;
        padding: 3px 5px;
        margin-top: 3px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* ── REMARKS TABLE ──────────────────────────────────────────────────────── */
    .remarks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
        border: 1px solid #000;
        flex-shrink: 0;
        table-layout: fixed;
    }

    .remarks-table th,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 12px;
        font-weight: 600;
        vertical-align: middle;
    }

    .remarks-table th {
        background: #FFCC00;
        text-align: left;
    }

    .remarks-table td.subj-name {
        width: 30%;
    }

    /* ── SIGNATURES ─────────────────────────────────────────────────────────── */
    .signatures-box {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding: 0 10px 5px;
        width: 100%;
        box-sizing: border-box;
        flex-shrink: 0;
        margin-top: auto;
    }

    .sign-space {
        height: 50px;
        position: relative;
    }

    .sign-line {
        border-top: 1px dotted #000;
        width: 180px;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        padding-top: 4px;
    }

    .co-sch-table td,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 12px;
        font-weight: 600;
        vertical-align: middle;
        box-sizing: border-box;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
            /* Identical uniform outer margin for every single page */
        }

        *,
        *::before,
        *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html,
        body {
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
        }

        /* Neutralize all layout wrappers so Page 1 has zero extra offsets or gray background */
        .wrapper,
        .content-wrapper,
        .right-side,
        section.content,
        .content,
        .box,
        .box-default,
        .box-body {
            margin: 0 !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            margin-right: 0 !important;
            padding: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border: none !important;
            border-top: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            width: 100% !important;
            min-height: 0 !important;
            left: 0 !important;
            overflow: visible !important;
            float: none !important;
            position: static !important;
        }

        .wrapper::before,
        .wrapper::after,
        .content-wrapper::before,
        .content-wrapper::after,
        .content::before,
        .content::after,
        .box::before,
        .box::after,
        .box-body::before,
        .box-body::after {
            display: none !important;
            content: none !important;
            height: 0 !important;
            width: 0 !important;
            border: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .main-header,
        .main-sidebar,
        .left-side,
        .control-sidebar,
        .main-footer,
        .content-header,
        .box-header,
        .no_print,
        .no-print,
        .btn,
        form {
            display: none !important;
        }

        .report-card {
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 10px 20px 5px 20px !important;
            border: 1px solid #cfcfcf !important;
            border-right: 1px solid #cfcfcf !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            overflow: visible !important;
            page-break-after: always !important;
            break-after: page !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            box-sizing: border-box !important;
            height: auto !important;
            min-height: calc(100vh - 2mm) !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        .report-card:last-child {
            page-break-after: auto !important;
            break-after: auto !important;
        }

        .mid-section {
            flex: none !important;
            min-height: 0 !important;
            overflow: visible !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header no_print">
        <h1>Nursery Marksheet</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Report Cards</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border no_print">
                <h3 class="box-title">Print Report Cards</h3>
            </div>
            <div class="box-body no_print">
                <form action="" method="post" class="form-inline">
                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Class&nbsp;</label>
                        <select class="form-control" name="student_class"
                            required><?php dropdown($class_list, $student_class); ?></select>
                    </div>
                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Section&nbsp;</label>
                        <select class="form-control" name="student_section"
                            required><?php dropdown($section_list, $student_section); ?></select>
                    </div>
                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Exam&nbsp;</label>
                        <select class="form-control" name="exam_id" required>
                            <option value="" disabled <?= (!isset($exam_id) || $exam_id == '') ? 'selected' : '' ?>>Select
                                Exam</option>
                            <?= dropdown_list('exam_list', 'id', 'exam_name', $exam_id) ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Show & Print">
                    </div>
                </form>
            </div>

            <?php if (!empty($student_class) && !empty($student_section) && !empty($exam_id)):

                /* ── Students ─────────────────────────────────────────── */
                $students = direct_sql("SELECT * FROM student WHERE student_class='$student_class' AND student_section='$student_section' AND branch_id='{$_SESSION['branch_id']}' AND status IN ('ACTIVE','PROMOTED')")['data'];
                usort($students, fn($a, $b) => (int) $a['student_roll'] - (int) $b['student_roll']);

                /* ── Exam ─────────────────────────────────────────────── */
                $exam_name = strtoupper(get_data('exam_list', $exam_id)['data']['exam_name'] ?? '');

                /* ── Subjects ─────────────────────────────────────────── */
                $subjects = direct_sql("SELECT * FROM subject WHERE FIND_IN_SET('$student_class',student_class)>0 AND subject_type='Scholastic' AND is_extra='NO' AND IFNULL(show_in_report,'YES')='YES' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY subject_order ASC")['data'] ?? [];

                /* ── Additional Subjects ──────────────────────────────── */
                $additional_subjects = direct_sql("SELECT * FROM subject WHERE FIND_IN_SET('$student_class',student_class)>0 AND is_extra='YES' AND IFNULL(show_in_report,'YES')='YES' AND status='ACTIVE' AND (branch_id='{$_SESSION['branch_id']}' OR branch_id='0' OR branch_id IS NULL) ORDER BY subject_order ASC")['data'] ?? [];

                /* ── Treat Urdu as Additional Subject for LKG & UKG ── */
                $is_lkg_ukg = in_array(strtoupper(trim($student_class)), ['LKG', 'UKG']);
                if ($is_lkg_ukg) {
                    $existing_add_ids = array_column((array) $additional_subjects, 'id');
                    foreach ((array) $subjects as $k => $sub) {
                        if (stripos($sub['subject_name'] ?? '', 'urdu') !== false) {
                            if (!in_array($sub['id'], $existing_add_ids)) {
                                $additional_subjects[] = $sub;
                            }
                            unset($subjects[$k]);
                        }
                    }
                    $subjects = array_values($subjects);
                }

                $sub_count = count($subjects);

                /* ── Dynamic row height based on subject count ────────── */
                $row_h = max(16, min(52, (int) (210 / max($sub_count + count($additional_subjects), 1))));

                /* ── Dynamic Max Marks (Loaded only from max_marks, fallback to default course values) ── */
                $mm_rows = direct_sql("SELECT subject_id, nb, ma, mo FROM max_marks WHERE class='$student_class' AND exam_id='$exam_id' AND (branch_id='{$_SESSION['branch_id']}' OR branch_id='0' OR branch_id IS NULL) AND (status='ACTIVE' OR status IS NULL OR status='') ORDER BY id DESC")['data'] ?? [];

                $mm_by_sub = [];
                $mm_class_default = null;
                foreach ($mm_rows as $row) {
                    $sid = trim($row['subject_id'] ?? '');
                    if ($sid !== '' && $sid !== '0') {
                        if (!isset($mm_by_sub[$sid])) {
                            $mm_by_sub[$sid] = $row;
                        }
                    } else {
                        if ($mm_class_default === null) {
                            $mm_class_default = $row;
                        }
                    }
                }

                $get_sub_max_marks = function ($sub_id = null) use ($mm_by_sub, $mm_class_default) {
                    $entry = null;
                    if ($sub_id !== null && isset($mm_by_sub[$sub_id])) {
                        $entry = $mm_by_sub[$sub_id];
                    } elseif ($mm_class_default !== null) {
                        $entry = $mm_class_default;
                    }

                    // Default course values
                    $pt = 10;
                    $ma = 15;
                    $sea = 15; // nb in max_marks
                    $hye = 60; // mo in max_marks
            
                    if ($entry) {
                        if (isset($entry['pt']) && is_numeric($entry['pt'])) {
                            $pt = (int) $entry['pt'];
                        }
                        if (isset($entry['ma']) && is_numeric($entry['ma'])) {
                            $ma = (int) $entry['ma'];
                        }
                        if (isset($entry['nb']) && is_numeric($entry['nb'])) {
                            $sea = (int) $entry['nb'];
                        }
                        if (isset($entry['mo']) && is_numeric($entry['mo'])) {
                            $hye = (int) $entry['mo'];
                        }
                    }

                    $total = $pt + $ma + $sea + $hye;
                    if ($total <= 0)
                        $total = 100;

                    return [
                        'pt' => $pt,
                        'ma' => $ma,
                        'sea' => $sea,
                        'hye' => $hye,
                        'total' => $total
                    ];
                };

                // Main subject header max marks
                $main_header_max = $get_sub_max_marks(null);
                $main_pt_max = $main_header_max['pt'];
                $main_ma_max = $main_header_max['ma'];
                $main_sea_max = $main_header_max['sea'];
                $main_hye_max = $main_header_max['hye'];
                $main_total_max = $main_header_max['total'];

                // Additional subject header max marks
                $first_add_id = !empty($additional_subjects) ? ($additional_subjects[0]['id'] ?? null) : null;
                $add_header_max = $get_sub_max_marks($first_add_id);
                $add_pt_max = $add_header_max['pt'];
                $add_ma_max = $add_header_max['ma'];
                $add_sea_max = $add_header_max['sea'];
                $add_hye_max = $add_header_max['hye'];
                $add_total_max = $add_header_max['total'] > 0 ? $add_header_max['total'] : 100;

                /* ── Branch header image ──────────────────────────────── */
                $branch_img = [1 => 'assets/images/b1.JPG', 2 => 'assets/images/b2.JPG', 4 => 'assets/images/b3.JPG'];
                $img_src = $branch_img[$_SESSION['branch_id']] ?? '';

                /* ── Batch Pre-load: Tabulation Marks (Current Exam & PT-1) ── */
                $pt_exam_res = direct_sql("SELECT id FROM exam_list WHERE (exam_name LIKE '%PERIODIC%1%' OR exam_name LIKE '%PERIODIC%I%' OR exam_name LIKE '%PT%1%' OR exam_name LIKE '%PT-1%' OR exam_name LIKE '%PT 1%') AND (branch_id = '{$_SESSION['branch_id']}' OR branch_id = '0' OR branch_id IS NULL) ORDER BY id DESC LIMIT 1");
                $pt_exam_id = $pt_exam_res['data'][0]['id'] ?? '';

                $pt_marks_map = [];
                $tab_marks_map = [];

                if (!empty($students)) {
                    $stu_id_list = implode(',', array_map('intval', array_column($students, 'id')));
                    $stu_adm_list = "'" . implode("','", array_map(function ($s) {
                        return addslashes($s['student_admission'] ?? '');
                    }, $students)) . "'";

                    // 1. Batch load current exam tabulation rows
                    $current_tab_rows = direct_sql("SELECT student_id, student_admission, subject, pt, ma, sea, nb, se, mo FROM tabulation WHERE exam_id='$exam_id' AND (student_id IN ($stu_id_list) OR student_admission IN ($stu_adm_list))")['data'] ?? [];
                    foreach ($current_tab_rows as $crow) {
                        $sid = $crow['student_id'] ?? '';
                        $sadm = $crow['student_admission'] ?? '';
                        $sub_id = $crow['subject'] ?? '';
                        if ($sid !== '') {
                            $tab_marks_map[$sid][$sub_id] = $crow;
                        }
                        if ($sadm !== '') {
                            $tab_marks_map[$sadm][$sub_id] = $crow;
                        }
                    }

                    // 2. Batch load PT-1 exam rows
                    if (!empty($pt_exam_id)) {
                        $pt_rows = direct_sql("SELECT student_id, student_admission, subject, pt, ma, sea, nb, se, mo FROM tabulation WHERE exam_id='$pt_exam_id' AND (student_id IN ($stu_id_list) OR student_admission IN ($stu_adm_list))")['data'] ?? [];
                        foreach ($pt_rows as $prow) {
                            $psid = $prow['student_id'] ?? '';
                            $padm = $prow['student_admission'] ?? '';
                            $psub = $prow['subject'] ?? '';

                            // Calculate total marks scored in the PT exam
                            $pt_tot = 0;
                            $has_pt_val = false;
                            foreach (['pt', 'ma', 'sea', 'nb', 'se', 'mo'] as $c) {
                                if (isset($prow[$c]) && $prow[$c] !== '' && is_numeric($prow[$c])) {
                                    $pt_tot += (float) $prow[$c];
                                    $has_pt_val = true;
                                }
                            }

                            if ($has_pt_val) {
                                $pval = round($pt_tot / 5);
                                if ($psid !== '') {
                                    $pt_marks_map[$psid][$psub] = $pval;
                                }
                                if ($padm !== '') {
                                    $pt_marks_map[$padm][$psub] = $pval;
                                }
                            }
                        }
                    }
                }

                // Helper to resolve marks for a student & subject from memory
                $get_student_subject_marks = function ($stu, $sub_id) use (&$tab_marks_map, &$pt_marks_map) {
                    $sid = $stu['id'] ?? '';
                    $sadm = $stu['student_admission'] ?? '';

                    $tab = $tab_marks_map[$sid][$sub_id] ?? ($tab_marks_map[$sadm][$sub_id] ?? []);

                    $raw_pt = $pt_marks_map[$sid][$sub_id] ?? ($pt_marks_map[$sadm][$sub_id] ?? null);
                    if ($raw_pt !== null) {
                        $pt = $raw_pt;
                    } else {
                        $tab_pt = $tab['pt'] ?? '';
                        $pt = (is_numeric($tab_pt) && $tab_pt !== '') ? round((float) $tab_pt / 5) : $tab_pt;
                    }

                    $ma = $tab['ma'] ?? '';
                    $sea = (isset($tab['nb']) && $tab['nb'] !== '') ? $tab['nb'] : ((isset($tab['sea']) && $tab['sea'] !== '') ? $tab['sea'] : ($tab['se'] ?? ''));
                    $mo = $tab['mo'] ?? '';

                    $total = (is_numeric($pt) ? (float) $pt : 0) + (is_numeric($ma) ? (float) $ma : 0) + (is_numeric($sea) ? (float) $sea : 0) + (is_numeric($mo) ? (float) $mo : 0);

                    return [
                        'pt' => $pt,
                        'ma' => $ma,
                        'sea' => $sea,
                        'mo' => $mo,
                        'total' => $total,
                        'has_any' => (isset($tab['ma']) && $tab['ma'] !== '') || ($sea !== '') || (isset($tab['mo']) && $tab['mo'] !== '') || ($pt !== '')
                    ];
                };

                /* ── Rank calculation ─────────────────────────────────── */
                $student_totals = [];
                foreach ((array) $students as $stu) {
                    $gt = 0;
                    $s_urdu = !empty($stu['urdu']) && $stu['urdu'] == 1;
                    foreach ((array) $subjects as $sub) {
                        $n = strtolower($sub['subject_name']);
                        if ($s_urdu && stripos($n, 'sanskrit') !== false && stripos($n, 'urdu') === false)
                            continue;
                        if (!$s_urdu && stripos($n, 'urdu') !== false && stripos($n, 'sanskrit') === false)
                            continue;

                        $m = $get_student_subject_marks($stu, $sub['id']);
                        $gt += $m['total'];
                    }
                    $student_totals[$stu['id']] = $gt;
                }
                arsort($student_totals);
                $ranks = [];
                $r = $rr = 1;
                $prev = null;
                foreach ($student_totals as $sid => $tot) {
                    if ($prev !== null && $tot < $prev)
                        $r = $rr;
                    $ranks[$sid] = $r;
                    $prev = $tot;
                    $rr++;
                }
                ?>
                <script src="https://www.gstatic.com/charts/loader.js"></script>
                <script>google.charts.load('current', { packages: ['corechart'] });</script>

                <?php foreach ((array) $students as $stu):
                    $stu_id = $stu['id'];
                    $bid = $_SESSION['branch_id'];
                    $img_url = $base_url . 'student_photo/' . ($bid == 1 ? 'Bishambharpur' : ($bid == 2 ? 'Barauli' : 'Barharia')) . '/Photo/';
                    // $photo = $stu['student_photo'] ? $img_url . $stu['student_photo'] : 'assets/images/blank_person.png';
                    $photo = !empty($stu['student_photo']) ? $base_url . 'upload/' . $stu['student_photo'] : 'assets/images/blank_person.png';

                    /* Per-student urdu/sanskrit filter */
                    $show_urdu = !empty($stu['urdu']) && $stu['urdu'] == 1;
                    $stu_subjects = array_filter((array) $subjects, function ($sub) use ($show_urdu) {
                        $n = strtolower($sub['subject_name']);
                        if ($show_urdu && stripos($n, 'sanskrit') !== false && stripos($n, 'urdu') === false)
                            return false;
                        if (!$show_urdu && stripos($n, 'urdu') !== false && stripos($n, 'sanskrit') === false)
                            return false;
                        return true;
                    });
                    $stu_add_subjects = array_filter((array) $additional_subjects, function ($sub) use ($show_urdu, $is_lkg_ukg, $stu, $get_student_subject_marks) {
                        $n = strtolower($sub['subject_name']);
                        if ($show_urdu && stripos($n, 'sanskrit') !== false && stripos($n, 'urdu') === false)
                            return false;
                        if (!$show_urdu && stripos($n, 'urdu') !== false && stripos($n, 'sanskrit') === false) {
                            if ($is_lkg_ukg && $get_student_subject_marks($stu, $sub['id'])['has_any']) {
                                return true;
                            }
                            return false;
                        }
                        return true;
                    });
                    $max_total = 0;
                    foreach ($stu_subjects as $sub) {
                        $max_total += $get_sub_max_marks($sub['id'])['total'];
                    }
                    if ($max_total <= 0) {
                        $max_total = count($stu_subjects) * 100;
                    }
                    $total_subj_count = count($stu_subjects) + count($stu_add_subjects);
                    $row_h = max(18, min(45, (int) (250 / max($total_subj_count, 1))));
                    ?>
                    <div class="report-card">

                        <?php if ($img_src): ?>
                            <div style="text-align:center;margin-bottom:4px;flex-shrink:0;">
                                <img src="<?= $img_src ?>" height="160px" width="100%" alt="Branch">
                            </div>
                        <?php endif; ?>

                        <div class="rc-header">STUDENT ACHIEVEMENT REPORT</div>
                        <div class="rc-header2"><?= $exam_name . ', ' . $current_session ?></div>

                        <!-- Student Info -->
                        <table class="stu-info-table">
                            <tr>
                                <td rowspan="4" class="stu-photo-cell"><img src="<?= $photo ?>" class="stu-photo-circle"></td>
                                <td class="label-cell">NAME</td>
                                <td colspan="2"><?= $stu['student_name'] ?></td>
                                <td class="label-cell">CLASS</td>
                                <td><?= $stu['student_class'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">MOTHER'S NAME</td>
                                <td colspan="2"><?= $stu['student_mother'] ?? '' ?></td>
                                <td class="label-cell">SECTION</td>
                                <td><?= $stu['student_section'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">FATHER'S NAME</td>
                                <td colspan="2"><?= $stu['student_father'] ?? '' ?></td>
                                <td class="label-cell">STUDENT ID</td>
                                <td><?= $stu['student_admission'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">DATE OF BIRTH</td>
                                <td colspan="2">
                                    <?= (!empty($stu['date_of_birth']) && $stu['date_of_birth'] != '0000-00-00') ? date('d-m-Y', strtotime($stu['date_of_birth'])) : '' ?>
                                </td>
                                <td class="label-cell">ROLL NUMBER</td>
                                <td><?= $stu['student_roll'] ?></td>
                            </tr>
                        </table>

                        <div class="rc-header2" style="background:#CC0000;color:#fff;border:none;padding:3px;font-size:13px;">
                            Scholastic Area
                        </div>

                        <!-- Marks Table -->
                        <?php
                        $grand_total = 0;
                        $chartData = [['Subject', 'OBTAINED MARKS', 'TOTAL MARKS']];
                        ?>
                        <table class="marks-table">
                            <thead>
                                <tr>
                                    <th class="subj-head" style="width:25%;">SUBJECTS</th>
                                    <th class="mark-head">PT-1 (<?= $main_pt_max ?>)</th>
                                    <th class="mark-head">MA (<?= $main_ma_max ?>)</th>
                                    <th class="mark-head">SEA (<?= $main_sea_max ?>)</th>
                                    <th class="mark-head">HYE (<?= $main_hye_max ?>)</th>
                                    <th class="mark-head">TOTAL (<?= $main_total_max ?>)</th>
                                    <th class="mark-head">GRADE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stu_subjects as $sub):
                                    $m = $get_student_subject_marks($stu, $sub['id']);
                                    $pt = $m['pt'];
                                    $ma = $m['ma'];
                                    $sea = $m['sea'];
                                    $mo = $m['mo'];
                                    $total = $m['total'];
                                    $grand_total += $total;
                                    $sub_mm = $get_sub_max_marks($sub['id']);
                                    $sub_total_max_val = $sub_mm['total'] > 0 ? $sub_mm['total'] : 100;
                                    $grd = grade(round(($total / $sub_total_max_val) * 100, 2));
                                    $sn = strtoupper(substr($sub['subject_name'], 0, 3));
                                    $chartData[] = [$sn, (float) $total, max(0, $sub_total_max_val - (float) $total)];
                                    ?>
                                    <tr style="height:<?= $row_h ?>px;">
                                        <?php if ($sub['subject_name'] == 'Artificial Intelligence' && in_array($stu['student_class'], ['IX', 'X'])) { ?>

                                            <td class="subj-name"><?= $sub['subject_name'] . ' (Skill Subject)' ?></td>

                                        <?php } else { ?>
                                            <td class="subj-name"><?= $sub['subject_name'] ?></td>
                                        <?php } ?>
                                        <td><?= fmt_val($pt) ?></td>
                                        <td><?= fmt_val($ma) ?></td>
                                        <td><?= fmt_val($sea) ?></td>
                                        <td><?= fmt_val($mo) ?></td>
                                        <td><?= $total ?></td>
                                        <td><?= $grd ?></td>
                                    </tr>
                                <?php endforeach;
                                $percentage = $max_total > 0 ? round(($grand_total / $max_total) * 100, 2) : 0;
                                $overall_grade = grade($percentage);
                                $student_rank = $ranks[$stu_id] ?? '-';
                                ?>
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <div class="totals-bar">
                            <div>TOTAL = <?= $grand_total ?></div>
                            <div>PERCENTAGE = <?= $percentage ?>%</div>
                            <div>GRADE = <?= $overall_grade ?></div>
                            <div>RANK = <?= $student_rank ?></div>
                        </div>

                        <!-- Additional Subjects -->
                        <?php if (!empty($stu_add_subjects)): ?>
                            <table class="marks-table" style="margin-top:3px;">
                                <thead>
                                    <tr>
                                        <th class="subj-head" style="width:25%;">Additional SUBJECTS</th>
                                        <th class="mark-head">PT-1 (<?= $add_pt_max ?>)</th>
                                        <th class="mark-head">MA (<?= $add_ma_max ?>)</th>
                                        <th class="mark-head">SEA (<?= $add_sea_max ?>)</th>
                                        <th class="mark-head">HYE (<?= $add_hye_max ?>)</th>
                                        <th class="mark-head">TOTAL (<?= $add_total_max ?>)</th>
                                        <th class="mark-head">GRADE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stu_add_subjects as $asub):
                                        $am = $get_student_subject_marks($stu, $asub['id']);
                                        $apt = $am['pt'];
                                        $ama = $am['ma'];
                                        $asea = $am['sea'];
                                        $ahye = $am['mo'];
                                        $atotal = $am['total'];
                                        $asub_mm = $get_sub_max_marks($asub['id']);
                                        $asub_total_max_val = $asub_mm['total'] > 0 ? $asub_mm['total'] : 100;
                                        $agrd = $am['has_any'] ? grade(round(($atotal / $asub_total_max_val) * 100, 2)) : 'AB';
                                        $atotal_disp = $am['has_any'] ? $atotal : 'AB';
                                        ?>
                                        <tr style="height:<?= $row_h ?>px;">
                                            <td class="subj-name"><?= $asub['subject_name'] ?></td>
                                            <td><?= fmt_val($apt) ?></td>
                                            <td><?= fmt_val($ama) ?></td>
                                            <td><?= fmt_val($asea) ?></td>
                                            <td><?= fmt_val($ahye) ?></td>
                                            <td><?= $atotal_disp ?></td>
                                            <td><?= $agrd ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php
                        $co_subjects = (array) co_subject_list($student_class);
                        $co_count = count($co_subjects);
                        $co_row_h = max(18, min(45, (int) (180 / max($co_count, 1))));
                        $mid_h = (($co_count + 1) * $co_row_h) + 104;
                        $remark_count = count($stu_subjects);
                        $remark_row_h = max(18, min(45, (int) (210 / max($remark_count, 1))));
                        ?>
                        <div class="mid-section" style="min-height:<?= $mid_h ?>px;">
                            <div class="mid-left">
                                <table class="co-sch-table">
                                    <thead>
                                        <tr style="height:<?= $co_row_h ?>px;">
                                            <th colspan="2"
                                                style="background:#CC0000;color:#fff;border:1px solid #000;padding:3px;font-size:13px; font-weight: 700;text-align:center">
                                                CO-SCHOLASTIC ASSESSMENT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($co_subjects as $co_sub):
                                            $gr = direct_sql("SELECT grade FROM co_scholastic_grades WHERE exam_id='$exam_id' AND student_id='$stu_id' AND subject_id='{$co_sub['id']}' LIMIT 1")['data'][0]['grade'] ?? 'NA';
                                            ?>
                                            <tr style="height:<?= $co_row_h ?>px;">
                                                <td><?= $co_sub['subject_name'] ?></td>
                                                <td style='text-align:center'><?= $gr ? $gr : 'NA' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php
                                $raw_height = trim((string) ($stu['height'] ?? ''));
                                $raw_weight = trim((string) ($stu['weight'] ?? ''));
                                $clean_h = preg_replace('/[^0-9.]/', '', $raw_height);
                                $clean_w = preg_replace('/[^0-9.]/', '', $raw_weight);
                                $stu_height = is_numeric($clean_h) ? (float) $clean_h : 0;
                                $stu_weight = is_numeric($clean_w) ? (float) $clean_w : 0;
                                $bmi_val = '-';
                                if ($stu_height > 0 && $stu_weight > 0) {
                                    $bmi_calc = ($stu_weight * 10000) / ($stu_height * $stu_height);
                                    $bmi_val = number_format($bmi_calc, 2);
                                }
                                $height_display = ($stu_height > 0) ? ($stu_height . ' cm') : '-';
                                $weight_display = ($stu_weight > 0) ? ($stu_weight . ' kg') : '-';

                                $att_data = calculate_student_attendance($stu_id, $exam_id, $student_class, $_SESSION['branch_id']);
                                $present_days = $att_data['present_days'] ?? 0;
                                $total_open_days = $att_data['total_open_days'] ?? 0;
                                $att_display = ($total_open_days > 0) ? ($present_days . ' / ' . $total_open_days) : '- / -';
                                ?>

                                <table class="health-table">
                                    <tr>
                                        <td class="hh">HEIGHT</td>
                                        <td class="hh">WEIGHT</td>
                                        <td class="hh">BODY MASS INDEX</td>
                                    </tr>
                                    <tr>
                                        <td><?= $height_display ?></td>
                                        <td><?= $weight_display ?></td>
                                        <td><?= $bmi_val ?></td>
                                    </tr>
                                </table>

                                <table class="att-table">
                                    <tr>
                                        <td style="width:50%;">ATTENDANCE</td>
                                        <td style="width:50%;"><?= $att_display ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mid-chart" style="height:<?= $mid_h ?>px;">
                                <div id="chart_div_<?= $stu_id ?>" style="width:100%;height:<?= $mid_h - 5 ?>px;"></div>
                            </div>
                            <script>
                                google.charts.setOnLoadCallback(function () {
                                    var chartDiv = document.getElementById('chart_div_<?= $stu_id ?>');
                                    if (!chartDiv) return;
                                    var data = google.visualization.arrayToDataTable(<?= json_encode($chartData, JSON_NUMERIC_CHECK) ?>);

                                    function drawChart_<?= $stu_id ?>() {
                                        var midSec = chartDiv.closest('.mid-section');
                                        var midLeft = midSec ? midSec.querySelector('.mid-left') : null;
                                        var measuredH = (midLeft && midLeft.offsetHeight > 50) ? (midLeft.offsetHeight - 2) : <?= $mid_h - 2 ?>;
                                        var targetH = Math.max(measuredH, <?= $mid_h - 2 ?>);
                                        var parentW = chartDiv.parentElement ? chartDiv.parentElement.clientWidth : 0;
                                        var targetW = parentW > 50 ? parentW : (chartDiv.clientWidth > 50 ? chartDiv.clientWidth : 340);

                                        var options = {
                                            title: 'GRAPHICAL REPRESENTATION OF RESULT',
                                            titleTextStyle: { fontSize: 11, bold: true, color: '#555' },
                                            isStacked: true,
                                            width: targetW,
                                            height: targetH,
                                            chartArea: { left: 45, top: 25, width: '70%', height: '74%' },
                                            vAxis: { viewWindow: { min: 0, max: 100 }, ticks: [0, 50, 100], format: "#'%'", textStyle: { color: '#555', fontSize: 9 }, gridlines: { color: '#e0e0e0' } },
                                            hAxis: { textStyle: { color: '#333', fontSize: 8 }, slantedText: false, maxAlternation: 1 },
                                            legend: { position: 'bottom', textStyle: { color: '#555', fontSize: 9 } },
                                            colors: ['#175b7b', '#b90000'],
                                            bar: { groupWidth: '50%' }
                                        };
                                        var chart = new google.visualization.ColumnChart(chartDiv);
                                        chart.draw(data, options);
                                    }

                                    drawChart_<?= $stu_id ?>();
                                    window.addEventListener('resize', drawChart_<?= $stu_id ?>);
                                });
                            </script>
                        </div>

                        <!-- Grading Scale -->
                        <div class="grading-scale">
                            8-Point Grading Scale: A1 - Outstanding | A2 - Excellent | B1 - Very Good | B2 - Good | C1 -
                            Satisfactory | C2 - Average | D - Needs Improvement | E - Unsatisfactory<br>
                            PT-Periodic Test | MA- Multiple Assessment | SEA- Subject Enrichment Assessment | HYE- Half Yearly
                            Examination
                        </div>

                        <!-- Remarks -->
                        <table class="remarks-table">
                            <thead>
                                <tr>
                                    <th class="subj-name">SUBJECTS</th>
                                    <th>REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stu_subjects as $sub):
                                    $m = $get_student_subject_marks($stu, $sub['id']);
                                    $rmk = get_remarks($student_class, $sub['id'], grade($m['total']), $sub['subject_name']);
                                    if (!empty($rmk)): ?>
                                        <tr style="height:<?= $remark_row_h ?>px;">
                                            <td class="subj-name" style="height:<?= $remark_row_h ?>px;">
                                                <?= $sub['subject_name'] ?>
                                            </td>
                                            <td style="height:<?= $remark_row_h ?>px;">
                                                <?= $rmk ?>
                                            </td>
                                        </tr>
                                    <?php endif; endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Signatures -->
                        <div class="signatures-box">
                            <div>
                                <div class="sign-space"></div>
                                <div class="sign-line">SIGNATURE OF CLASS TEACHER</div>
                            </div>
                            <div>
                                <div class="sign-space"></div>
                                <div class="sign-line">SIGNATURE OF PARENT</div>
                            </div>
                            <div>
                                <div class="sign-space" style="position:relative;">
                                    <img src="<?= $principal ?>" height="40"
                                        style="position:absolute;bottom:5px;left:50%;transform:translateX(-50%);">
                                </div>
                                <div class="sign-line">SIGNATURE OF PRINCIPAL</div>
                            </div>
                        </div>

                    </div><!-- /.report-card -->
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>
</div><?php require_once('system/footer.php'); ?>