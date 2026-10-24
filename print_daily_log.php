<?php
require_once('system/function.php');
if (!isset($_SESSION['initiated'])) {
    header("Location: system/master_process.php?task=logout");
    exit;
}

$user_id = $_SESSION['user_id'];
$udata = get_data('user', $user_id)['data'];
$user_name = $udata['user_name'];
$user_type = $udata['user_type'];
$branch_id = $udata['branch_id'];

$b_config = branch_config();
extract($b_config);

$student_class = $_GET['student_class'] ?? '';
$student_section = $_GET['student_section'] ?? '';
$log_date = $_GET['log_date'] ?? date('Y-m-d');
$day_name = date('l', strtotime($log_date));

// Fetch Class Teacher Name
$ct_esc = mysqli_real_escape_string($con, $student_class . "-" . $student_section);
$ct_emp = direct_sql("SELECT name FROM employee WHERE class_teacher LIKE '$ct_esc%' AND status = 'ACTIVE' AND branch_id = '$branch_id' LIMIT 1");
$class_teacher_name = ($ct_emp['count'] > 0) ? $ct_emp['data'][0]['name'] : '';

// Fetch Class Strength
$strength_res = direct_sql("SELECT COUNT(*) as total FROM student WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE' AND branch_id = '$branch_id'");
$total_strength = ($strength_res['count'] > 0) ? $strength_res['data'][0]['total'] : 0;

// Fetch Attendance Counts
$day_num = date('j', strtotime($log_date));
$m_value = remove_space(date('M_Y', strtotime($log_date)));

$p_res_att = direct_sql("SELECT COUNT(*) as count FROM student_att sa JOIN student s ON sa.student_id = s.id WHERE s.student_class = '$student_class' AND s.student_section = '$student_section' AND s.status = 'ACTIVE' AND sa.att_month = '$m_value' AND sa.d_$day_num = 'P' AND sa.branch_id = '$branch_id'");
$present_count = ($p_res_att['count'] > 0) ? $p_res_att['data'][0]['count'] : 0;

$a_res_att = direct_sql("SELECT COUNT(*) as count FROM student_att sa JOIN student s ON sa.student_id = s.id WHERE s.student_class = '$student_class' AND s.student_section = '$student_section' AND s.status = 'ACTIVE' AND sa.att_month = '$m_value' AND sa.d_$day_num = 'A' AND sa.branch_id = '$branch_id'");
$absent_count = ($a_res_att['count'] > 0) ? $a_res_att['data'][0]['count'] : 0;

// Fetch Logbook ID
$log_sql = "SELECT id FROM logbook WHERE date='$log_date' AND student_class='$student_class' AND student_section='$student_section' LIMIT 1";
$log_res = direct_sql($log_sql);
$log_id = ($log_res['count'] > 0) ? $log_res['data'][0]['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Log - <?= htmlspecialchars($student_class) ?>-<?= htmlspecialchars($student_section) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@400;700&family=Outfit:wght@400;600;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }

        .log-sheet {
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
            background: #fff;
        }

        .header-container {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            position: relative;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .affiliation-info {
            font-size: 10px;
            font-weight: 600;
            margin: 4px 0;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        .logo-wrapper {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .logo-wrapper img {
            height: 70px;
            width: auto;
        }

        .title-box {
            display: inline-block;
            border: 2px double #000;
            border-radius: 4px;
            padding: 4px 20px;
            font-weight: 800;
            font-size: 16px;
            margin-top: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .meta-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .outline-box {
            border: 1.5px solid #000;
            padding: 4px 15px;
            min-width: 80px;
            text-align: center;
            font-family: 'Kalam', cursive;
            font-size: 16px;
            color: #0b132b;
            font-weight: 700;
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 12px;
            font-weight: 600;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .handwritten-val {
            font-family: 'Kalam', cursive;
            font-size: 16px;
            font-weight: 700;
            color: #1e3a8a;
            border-bottom: 1px dashed #555;
            padding: 0 8px;
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        .log-table th,
        .log-table td {
            border: 1.5px solid #000;
            padding: 8px 10px;
            vertical-align: middle;
            text-align: left;
        }

        .log-table th {
            background-color: #f8fafc;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .log-table td.center {
            text-align: center;
        }

        .font-handwritten {
            font-family: 'Kalam', cursive;
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1.4;
        }

        .signature-font {
            font-family: 'Kalam', cursive;
            font-size: 14px;
            font-weight: 700;
            font-style: italic;
            color: #1e293b;
            text-decoration: underline;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 8mm;
            }

            body {
                padding: 0;
                margin: 0;
                overflow: hidden;
            }

            .log-sheet {
                border: 2px solid #000 !important;
                max-width: 100% !important;
                width: 100% !important;
                height: 138mm !important; /* Half height of A4 portrait with a safety margin */
                max-height: 138mm !important;
                padding: 10px !important;
                box-sizing: border-box !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .no-print {
                display: none !important;
            }

            /* Compact elements for half-height log book */
            .header-container {
                padding: 4px !important;
                margin-bottom: 6px !important;
                border-width: 1.5px !important;
            }

            .school-name {
                font-size: 18px !important;
            }

            .affiliation-info {
                font-size: 8px !important;
                margin: 2px 0 !important;
            }

            .logo-wrapper {
                left: 10px !important;
            }

            .logo-wrapper img {
                height: 40px !important;
            }

            .title-box {
                font-size: 11px !important;
                padding: 2px 12px !important;
                margin-top: 4px !important;
                border-width: 1.5px !important;
            }

            .meta-row {
                margin-bottom: 6px !important;
                font-size: 11px !important;
            }

            .outline-box {
                font-size: 12px !important;
                padding: 2px 8px !important;
                min-width: 60px !important;
                border-width: 1px !important;
            }

            .info-row {
                margin-bottom: 6px !important;
                padding-bottom: 4px !important;
                gap: 8px !important;
                font-size: 11px !important;
                border-bottom-width: 1px !important;
            }

            .handwritten-val {
                font-size: 12px !important;
                padding: 0 4px !important;
            }

            .log-table th,
            .log-table td {
                padding: 4px 6px !important;
                font-size: 11px !important;
                border-width: 1px !important;
            }

            .log-table th {
                font-size: 9.5px !important;
            }

            .font-handwritten {
                font-size: 11px !important;
                line-height: 1.2 !important;
            }

            .signature-font {
                font-size: 10px !important;
            }
        }

        .print-btn-bar {
            max-width: 900px;
            margin: 0 auto 15px auto;
            text-align: right;
        }

        .btn-print {
            background-color: #f59e0b;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background-color: #d97706;
        }
    </style>
</head>

<body>

    <div class="print-btn-bar no-print">
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Print / Save Daily Log</button>
    </div>

    <div class="log-sheet">
        <div class="header-container">
            <div class="logo-wrapper">
                <img src="assets/images/logo.png"
                    onerror="this.src='https://rmpublicschool.org/binex/assets/images/logo.png'" alt="RMPS LOGO">
            </div>
            <h1 class="school-name">R M Public School</h1>
            <div class="affiliation-info">Affiliated to CBSE, New Delhi, Up to 10+2, Affiliation No.-330874</div>
            <div class="affiliation-info" style="margin-top: 0;">CBSE School Code: 65899, ISO 9001:2015 Certified</div>
            <div class="title-box">Daily Log</div>
        </div>

        <div class="meta-row">
            <div class="meta-group">
                <span>DATE :</span>
                <div class="outline-box"><?= date('d/m/Y', strtotime($log_date)) ?></div>
            </div>
            <div class="meta-group">
                <span>DAY :</span>
                <div class="outline-box"><?= htmlspecialchars($day_name) ?></div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-item">
                <span>CLASS TEACHER :</span>
                <span class="handwritten-val"><?= htmlspecialchars($class_teacher_name ?: 'Sahana Yasmin') ?></span>
            </div>
            <div class="info-item">
                <span>CLASS :</span>
                <span
                    class="handwritten-val"><?= htmlspecialchars($student_class) ?>-<?= htmlspecialchars($student_section) ?></span>
            </div>
            <div class="info-item">
                <span>STRENGTH :</span>
                <span class="handwritten-val"><?= $total_strength ?></span>
            </div>
            <div class="info-item">
                <span>PRESENT :</span>
                <span class="handwritten-val"><?= $present_count ?></span>
            </div>
            <div class="info-item">
                <span>ABSENT :</span>
                <span class="handwritten-val"><?= $absent_count ?></span>
            </div>
        </div>

        <table class="log-table">
            <thead>
                <tr>
                    <th width="8%">Period No.</th>
                    <th width="15%">Subject</th>
                    <th width="18%">Subject Teacher</th>
                    <th width="28%">Topics Taught / Activity</th>
                    <th width="18%">Homework</th>
                    <th width="13%">Signature</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($p = 1; $p <= 8; $p++) {
                    $p_res = get_all('logbook_period', '*', ['logbook_id' => $log_id, 'period_no' => $p]);

                    $subject = "";
                    $teacher_id_period = 0;
                    $topics = "";
                    $homework = "";

                    if ($p_res['count'] > 0) {
                        $pd = $p_res['data'][0];
                        $subject_val = $pd['subject'];
                        $teacher_id_period = $pd['teacher_id'];
                        $topics = $pd['topics'];
                        $homework = $pd['homework'];
                    } else {
                        // Timetable fallback
                        $tt = get_all('timetable', '*', ['student_class' => $student_class, 'student_section' => $student_section, 'day' => $day_name, 'period' => $p]);
                        if ($tt['count'] > 0) {
                            $subject_val = $tt['data'][0]['subject'];
                            $teacher_id_period = $tt['data'][0]['teacher_id'];
                        }
                    }

                    // Resolve Subject Name
                    $subject_name = '';
                    if (!empty($subject_val)) {
                        $sub_info = get_data('subject', $subject_val);
                        $subject_name = ($sub_info['count'] > 0) ? $sub_info['data']['subject_name'] : $subject_val;
                    }

                    // Resolve Teacher Name
                    $teacher_name = '';
                    if ($teacher_id_period) {
                        $t_info = get_data('employee', $teacher_id_period, 'name');
                        $teacher_name = ($t_info['status'] == 'success') ? $t_info['data'] : '';
                    }
                    // Fallback: look up timetable with JOIN if teacher_name still empty
                    if (empty($teacher_name) && !empty($student_class) && !empty($student_section)) {
                        $tt_teacher = direct_sql("SELECT t.teacher_id, e.name as teacher_name 
                                                  FROM timetable t 
                                                  JOIN employee e ON t.teacher_id = e.id 
                                                  WHERE t.student_class='$student_class' 
                                                  AND t.student_section='$student_section' 
                                                  AND t.day='$day_name' 
                                                  AND t.period='$p' 
                                                  AND t.status='ACTIVE' 
                                                  AND t.branch_id='$branch_id' LIMIT 1", 'get', false);
                        if ($tt_teacher['count'] > 0) {
                            $teacher_name = $tt_teacher['data'][0]['teacher_name'];
                        }
                    }
                    ?>
                    <tr>
                        <td class="center font-handwritten" style="font-size: 14px; font-weight: bold;"><?= $p ?></td>
                        <td class="font-handwritten"><?= htmlspecialchars($subject_name) ?></td>
                        <td class="font-handwritten"><?= htmlspecialchars($teacher_name) ?></td>
                        <td class="font-handwritten"><?= nl2br(htmlspecialchars($topics)) ?></td>
                        <td class="font-handwritten"><?= nl2br(htmlspecialchars($homework)) ?></td>
                        <td class="center signature-font"><?= htmlspecialchars($teacher_name) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>