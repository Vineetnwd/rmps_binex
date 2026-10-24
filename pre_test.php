<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('system/header.php');
require_once('system/menu.php');

$exam_id = $student_class = $student_section = null;
if (isset($_REQUEST['exam_id'])) {
    $exam_id = $_REQUEST['exam_id'];
}
if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}

function fmt_val($v) {
    // Format numeric values; convert empty/zero-like to AB
    if ($v === null || $v === '' || $v === '0' || $v === 0 || $v === '0.00' || $v === 0.0) return 'AB';
    if (!is_numeric($v)) return htmlspecialchars($v);
    // show up to 2 decimals only if needed
    if (intval($v) == $v) return intval($v);
    return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
}
$principal = get_data('branch_list',$branch_id)['data'];
if($principal!=''){
    $principal = $base_url.'upload/'.$principal['principal_sign'];
}else{
    $principal = 'assets/images/new_sign.png';
}

$mi = 1;

$att = [
    'I' => 113,
    'II' => 113,
    'III' => 113,
    'IV' => 113,
    'V' => 113,
    'VI' => 113,
    'VII' => 113,
    'VIII' => 113,
    'IX' => 132,
    'X' => 132
];

?>
<style>
    body { font-family: "Helvetica", Arial, sans-serif; color:#222; background: #fff; }
    .content-wrapper{text-transform: uppercase !important;font-size:14px!important;font-weight:400!important;}
    .report-card { min-height: 297mm !important; margin: 0px; border: 1px solid #cfcfcf;background:#fff; box-shadow: none; }
    .rc-header { background: #525252; color: #fff; text-align:center; font-weight:700; letter-spacing:0.6px; }
    .rc-header2 { background: #ccc; color: #000; padding:8px 12px; text-align:center; font-weight:700; letter-spacing:0.6px; }
    .rc-top { display:flex; padding:10px; border:1px solid #e0e0e0; margin-top:10px; align-items:center; gap:10px; }
    .stu-left, .stu-right { width: 34%; }
    .stu-center { width: 32%; text-align:left; }
    .stu-left p, .stu-right p { margin:6px 2px; text-transform: uppercase;font-size:14 px;font-weight:600 }
    .info-label { color:#555; font-size:12px; display:inline-block; width:110px; }
    .info-value { font-weight:700; text-transform: uppercase;font-size:14px;font-weight:600 display:inline-block; }

    .stu-photo { width:88px; height:128px; border-radius:6px; border:1px solid #ddd; object-fit:cover; }
    /*table{width:100% !important;};*/
    table.sch-table { width:100%; border-collapse: collapse; text-transform: uppercase;font-size:14px;font-weight:600 }
    table.sch-table th, table.sch-table td { border:1px solid #cfcfcf; padding:1px 6px; vertical-align:middle; text-align:center; font-size:12px;text-transform: uppercase;font-weight:400}
    table.sch-table th.subject { text-align:left; padding-left:10px; background:lightyellow; font-weight:700; }
    table.sch-table td.subject { text-align:left; padding-left:10px; }
    .term1-head { background:#f7fbff; }
    .term2-head { background:#fff7ec; }
    .final-head { background:#eefaf1; }

    .rc-bottom { display:flex; gap:12px; margin-top:12px; }
    .co-scholastic { width:38%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }
    .attendance { width:26%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }
    .performance { width:36%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }

    .signatures { display:flex; justify-content:space-between; margin-top:18px; align-items:flex-end; }
    .sign-box { width:30%; text-align:center; text-transform: uppercase;font-size:14px;font-weight:600 }
    .sign-line { border-top:1px solid #444; margin-top:10px; padding-top:6px; }
    /*.report-card{*/
    /*    transform: scale(0.85) !important;*/
    /*    transform-origin: top left;*/
    /*}*/
   @media print {
    @page {
        size: a4 portrait;
        margin: 0;           
    }

    body {
        margin: 0;
        padding: 0;
    }
     /*table{width: 210mm !important;};*/
   .report-card {
        page-break-after: always;
        /*width: 100%;*/
        /*margin: 0px;*/
        /*padding: 0px;*/
        /*border: none;*/
        /*box-sizing: border-box;*/
        /*width: 210mm;*/
        /*min-height: 297mm;*/
        /*transform: scale(0.85);*/
        /*transform-origin: top left; */
    }

    /*.report-card {*/
    /*    width: 210mm;*/
    /*    min-height: 297mm;*/
    /*    margin: 0 auto;*/
    /*    page-break-after: always;*/
    /*  }*/


    .no_print {
        display: none !important;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Tabulation / Report Cards</h1>
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
                        <select class="form-control" name="student_class" required>
                            <?//php dropdown($class_list, $student_class); ?>
                                <option value="" disabled <?= (!isset($student_class) || $student_class == '') ? 'selected' : '' ?>>Select Class</option>
                                <option value="NUR" <?= ($student_class == 'NUR') ? 'selected' : '' ?>>NUR</option>
                                <option value="LKG" <?= ($student_class == 'LKG') ? 'selected' : '' ?>>LKG</option>
                                <option value="UKG" <?= ($student_class == 'UKG') ? 'selected' : '' ?>>UKG</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Section&nbsp;</label>
                        <select class="form-control" name="student_section" required>
                            <?php dropdown($section_list, $student_section); ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Exam&nbsp;</label>
                        <select class="form-control" name="exam_id" required>
                            <option value="" disabled <?= (!isset($exam_id) || $exam_id == '') ? 'selected' : '' ?>>Select Term</option>
                            <option value="TERM 1" <?= ($exam_id == 'TERM 1') ? 'selected' : '' ?>>TERM 1</option>
                            <option value="TERM 2" <?= ($exam_id == 'TERM 2') ? 'selected' : '' ?>>TERM 2</option>
                            <option value="both" <?= ($exam_id == 'both') ? 'selected' : '' ?>>Both (Annual)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Show & Print">
                    </div>
                </form>
                <!--<button id="downloadPDF">Export PDF</button>-->
            </div>
            <?php
            if($student_class=='NUR' || $student_class == 'LKG' || $student_class=='UKG'){
                if (!empty($student_class) && !empty($student_section) && !empty($exam_id)) {
                    // fetch students
                    $students = get_all('student', '*', [
                        'student_class' => $student_class,
                        'student_section' => $student_section,
                        'branch_id' => $_SESSION['branch_id'],
                        'status' => 'ACTIVE'
                    ])['data'];
                    usort($students, function ($a, $b) {
                        return (int)$a['student_roll'] - (int)$b['student_roll'];
                    });
                    $subjects = subject_list($student_class);
                    $merged_subjects = [];
                    if (in_array($student_class, ['I','II','III','IV','V','VI','VII','VIII'])) {
                        $english_done = $hindi_done = $urdu_done = false;
                        foreach ($subjects as $sub) {
                            $sub_id = is_array($sub) ? $sub['id'] : $sub;
                            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
                            if (!$english_done && (stripos($sub_name,'English') !== false)) {
                                $merged_subjects[] = ['id'=>'english','subject_name'=>'English']; $english_done = true;
                            } elseif (!$hindi_done && (stripos($sub_name,'Hindi') !== false)) {
                                $merged_subjects[] = ['id'=>'hindi','subject_name'=>'Hindi']; $hindi_done = true;
                            } elseif (!$urdu_done && (stripos($sub_name,'Urdu') !== false)) {
                                $merged_subjects[] = ['id'=>'urdu','subject_name'=>'Sanskrit / Urdu']; $urdu_done = true;
                            } elseif (stripos($sub_name,'English')===false && stripos($sub_name,'Hindi')===false && stripos($sub_name,'Urdu')===false) {
                                $merged_subjects[] = ['id'=>$sub_id, 'subject_name'=>$sub_name];
                            }
                        }
                    } else {
                        foreach ($subjects as $sub) {
                            $sub_id = is_array($sub) ? $sub['id'] : $sub;
                            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
                            $merged_subjects[] = ['id'=>$sub_id, 'subject_name'=>$sub_name];
                        }
                    }
    
                    // Loop students
                    foreach ((array)$students as $stu):
                    ?>    
                    <div style="text-align:center; margin-bottom:10pxmargin-top:5px;min-height:170px">
                            <?php
                            $branch_images = [
                                1 => 'assets/images/b1.JPG',
                                2 => 'assets/images/b2.JPG',
                                4 => 'assets/images/b3.JPG',
                            ];
                            
                            if($_SESSION['branch_id']==1){
                                $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
                            }else if($_SESSION['branch_id']==2){
                                $img_url = $base_url.'student_photo/Barauli/Photo/';
                            }else{
                                $img_url = $base_url.'student_photo/Barharia/Photo/';
                            }
                        
                            $img_src = $branch_images[$_SESSION['branch_id']];
                        
                            if ($img_src) {
                                echo "<img src='{$img_src}' height='170px' width='100% alt='Branch Logo'>";
                            }
                            ?>
                        </div>
    
                            <div class="rc-header" style='font-size:20px;padding:0px'>MY ACHIEVEMENT REPORT</div>
                            <div class="rc-header2" style='padding:0px'>TERM 1 EXAMINATION 2025-2026</div>
    
                           <table style="width:100%; border-collapse:collapse; font-family:'Segoe UI', Arial, sans-serif; border:1px solid #000; margin-bottom:8px;">
                             Header Row 
                            <tr style="background:#f2f2f2;">
                                <td colspan="3" style="font-size:14px; widtht:50%; color:#000; text-align:center; border:1px solid #000; padding:0px;">
                                    STUDENT PROFILE
                                </td>
                                <td rowspan="5" style="width:110px; widtht:40%; text-align:center; border:1px solid #000; vertical-align:middle; background:#fafafa;">
                                    <?php $photo = $stu['photo'] ?? 'system/images/avatar_placeholder.png'; ?>
                                    <img src="<?= htmlspecialchars($img_url . $stu['student_admission']) ?>.jpg"
                                         alt="<?= htmlspecialchars($stu['student_admission']) ?>.jpg"
                                         style="width:95px; height:120px; border:1px solid #ccc; border-radius:8px; object-fit:cover; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                                </td>
                            </tr>
                        
                             Row 1 
                            <tr>
                                <td style="width:50%; border:1px solid #000; padding-left:8px;">
                                    <strong>Name:</strong> <?= htmlspecialchars($stu['student_name']) ?>
                                </td>
                                <td style="width:40%; border:1px solid #000; padding-left:8px;">
                                    <strong>Class:</strong> <?= htmlspecialchars($stu['student_class']) . ' - ' . htmlspecialchars($stu['student_section']) ?>
                                </td>
                            </tr>
                        
                             Row 2 
                            <tr>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Father's Name:</strong> <?= htmlspecialchars($stu['student_father'] ?? '') ?>
                                </td>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Roll No.:</strong> <?= htmlspecialchars($stu['student_roll']) ?>
                                </td>
                            </tr>
                        
                             Row 3 
                            <tr>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Mother's Name:</strong> <?= htmlspecialchars($stu['student_mother'] ?? '') ?>
                                </td>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Admission No.:</strong> <?= htmlspecialchars($stu['student_admission'] ?? '') ?>
                                </td>
                            </tr>
                        
                             Row 4 
                            <tr>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Date of Birth:</strong> <?= date('d-m-Y', strtotime($stu['date_of_birth'] ?? '')) ?>
                                </td>
                                <td style="border:1px solid #000; padding-left:8px;">
                                    <strong>Contact:</strong> <?= htmlspecialchars($stu['student_mobile'] ?? '') ?>
                                </td>
                            </tr>
                        </table>
    
                            
                            <div style="flex:1; border:1px solid #ccc; border-radius:5px; padding:10px;margin-right:5px">
                                <div style="background:#525252; font-size:16px; font-weight:700; color:#fff; padding:0px;padding-left:5px;">
                                    GRAPHICAL REPRESENTATION OF RESULT
                                </div>
                            
                                <?php
                                $score = [];
                            
                                foreach ((array)$merged_subjects as $sub) {
                                    $sub_id = $sub['id'];
                                    $subject_name = $sub['subject_name'];
                            
                                    $ut1 = $ut2 = $ma1 = $portfolio1 = $se1 = $mid1 = $ga1 = $pa1 = $nb1 = $sea1 = 0;
                                    $mut1 = $t1t = 0;
                            
                                    // Handle TERM 1
                                    if ($exam_id === 'both' || $exam_id === 'TERM 1') {
                            
                                        if (in_array($sub_id, ['english', 'hindi', 'urdu'])) {
                                            $term1_data = direct_sql("
                                                SELECT * FROM tabulation 
                                                WHERE student_id='{$stu['id']}' 
                                                AND term='TERM 1' 
                                                AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')
                                            ", 'get', false)['data'] ?? [];
                            
                                            foreach ($term1_data as $row) {
                                                $ut1 += $row['ut1'] / 2;
                                                $ut2 += $row['ut2'] / 2;
                                                $ma1 += $row['ma'];
                                                $portfolio1 += $row['portfolio'];
                                                $se1 += $row['se'];
                                                $mid1 += $row['mid_term'];
                                                $ga1 += $row['ga'];
                                                $nb1 += $row['nb'];
                                                $sea1 += $row['sea'];
                                            }
                            
                                            $mut1 = max($ut1, $ut2);
                                            $pa1 = ($ga1 + $mut1) / 5;
                            
                                        } else {
                                            $term1 = direct_sql("
                                                SELECT * FROM tabulation 
                                                WHERE student_id='{$stu['id']}' 
                                                AND subject='{$sub_id}' 
                                                AND term='TERM 1'
                                            ", 'get', false)['data'][0] ?? null;
                            
                                            if ($term1) {
                                                $ut1 = $term1['ut1'];
                                                $ut2 = $term1['ut2'];
                                                $ma1 = $term1['ma'];
                                                $portfolio1 = $term1['portfolio'];
                                                $se1 = $term1['se'];
                                                $mid1 = $term1['mid_term'];
                                                $ga1 = $term1['ga'];
                                                $nb1 = $term1['nb'];
                                                $sea1 = $term1['sea'];
                                                $mut1 = max($ut1, $ut2);
                                                $pa1 = ($ga1 + $mut1) / 5;
                                            }
                                        }
                            
                                        // Total marks calculation
                                        // $t1t = ($student_class === 'IX' || $student_class === 'X') 
                                        //     ? $mid1 + $mut1 + $ma1 + $portfolio1 + $se1 
                                        //     : $pa1 + $nb1 + $sea1 + $mid1;
                                        if ($student_class === 'IX' || $student_class === 'X') {
                                            if ($sub_id != '46') {
                                                $mut1 = max($ut1, $ut2) / 10;
                                            }
                                            // $t1t = $mid1 + $mut1 + $ma1 + $portfolio1 + $se1;
                                            $prem = $mut1 + $ma1 + $portfolio1 + $se1;
                                            $t1t = $mid1 + $prem;
                                        } 
                                        else if ($student_class === 'I' || $student_class === 'II') {
                                            $nmid1 = number_format($mid1 * (4 / 3), 2);
                                            $t1t = $pa1 + $nb1 + $sea1 + $nmid1;
                                        } 
                                        else {
                                            $t1t = $pa1 + $nb1 + $sea1 + $mid1;
                                        }
                            
                                        $score[$subject_name] = (float)$t1t; // exact number
                                    }
                            
                                    // Handle TERM 2
                                    if ($exam_id === 'both' || $exam_id === 'TERM 2') {
                            
                                        $ut1 = $ut2 = $ma1 = $portfolio1 = $se1 = $mid1 = $ga1 = $pa1 = $nb1 = $sea1 = 0;
                                        $mut1 = $t1t = 0;
                            
                                        if (in_array($sub_id, ['english', 'hindi', 'urdu'])) {
                                            $term2_data = direct_sql("
                                                SELECT * FROM tabulation 
                                                WHERE student_id='{$stu['id']}' 
                                                AND term='TERM 2' 
                                                AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')
                                            ", 'get', false)['data'] ?? [];
                            
                                            foreach ($term2_data as $row) {
                                                $ut1 += $row['ut1'] / 2;
                                                $ut2 += $row['ut2'] / 2;
                                                $ma1 += $row['ma'];
                                                $portfolio1 += $row['portfolio'];
                                                $se1 += $row['se'];
                                                $mid1 += $row['mid_term'];
                                                $ga1 += $row['ga'];
                                                $nb1 += $row['nb'];
                                                $sea1 += $row['sea'];
                                            }
                            
                                            $mut1 = max($ut1, $ut2);
                                            $pa1 = ($ga1 + $mut1) / 5;
                            
                                        } else {
                                            $term2 = direct_sql("
                                                SELECT * FROM tabulation 
                                                WHERE student_id='{$stu['id']}' 
                                                AND subject='{$sub_id}' 
                                                AND term='TERM 2'
                                            ", 'get', false)['data'][0] ?? null;
                            
                                            if ($term2) {
                                                $ut1 = $term2['ut1'];
                                                $ut2 = $term2['ut2'];
                                                $ma1 = $term2['ma'];
                                                $portfolio1 = $term2['portfolio'];
                                                $se1 = $term2['se'];
                                                $mid1 = $term2['mid_term'];
                                                $ga1 = $term2['ga'];
                                                $nb1 = $term2['nb'];
                                                $sea1 = $term2['sea'];
                                                $mut1 = max($ut1, $ut2);
                                                $pa1 = ($ga1 + $mut1) / 5;
                                            }
                                        }
                            
                                        // $t1t = ($student_class === 'IX' || $student_class === 'X') 
                                        //     ? $mid1 + $mut1 + $ma1 + $portfolio1 + $se1 
                                        //     : $pa1 + $nb1 + $sea1 + $mid1;
                            
                                        $score[$subject_name] = (float)$t1t;
                                    }
                                }
                            
                                // Prepare chart data
                                $chartData = [['Subject', 'Marks', ['role' => 'style'], ['role' => 'annotation']]];
                                foreach ($score as $subject => $marks) {
                                    $chartData[] = [$subject, $marks, 'color:#1b9e77', $marks];
                                }
                                ?>
                            
                                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                <script type="text/javascript">
                                    google.charts.load('current', { packages: ['corechart'] });
                                    google.charts.setOnLoadCallback(drawChart);
                            
                                    function drawChart() {
                                        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData, JSON_NUMERIC_CHECK); ?>);
                            
                                        var options = {
                                            title: 'Student Performance',
                                            chartArea: { width: '70%', height: '70%' },
                                            hAxis: { 
                                                title: 'Subjects',
                                                slantedText: false,       
                                                textStyle: {
                                                    fontSize: 8           
                                                }
                                            },
                                            vAxis: { 
                                                title: 'Marks',
                                                viewWindow: { min: 0, max: 100 },
                                                ticks: [0,20,40,60,80,100],
                                                minorGridlines: { count: 0 }, 
                                                baselineColor: '#000',         
                                                gridlines: { color: '#ccc' },
                                            },
                                            legend: 'none',
                                            annotations: {
                                                alwaysOutside: true,
                                                textStyle: { fontSize: 12, color: '#000', bold: true }
                                            },
                                            bar: { groupWidth: '60%' }
                                        };
    
                            
                                        var chart = new google.visualization.ColumnChart(
                                            document.getElementById('columnchart_values_<?php echo $stu['student_admission']; ?>')
                                        );
                                        chart.draw(data, options);
                                    }
                                </script>
                            
                                <div id="columnchart_values_<?php echo $stu['student_admission']; ?>" style="width: 950px; height: 270px;"></div>
                            </div>
    
                            <div class="rc-header">SCHOLASTIC AREAS</div>
                             Scholastic table 
                             
                            <table class="sch-table" aria-label="Scholastic">
                                <thead>
                                    <tr bgcolor="lightyellow">
                                        <th class="subject"></th>
    
                                        <?php if ($exam_id == 'TERM 1' || $exam_id == 'both'): ?>
                                            <th class="term1-head" style=" background-color:lightyellow" colspan="8">TERM 1</th>
                                        <?php endif; ?>
    
                                        <?php if ($exam_id == 'TERM 2' || $exam_id == 'both'): ?>
                                            <th class="term2-head" colspan="<?= ($student_class == 'IX' || $student_class == 'X') ? 9 : 9; ?>">TERM 2</th>
                                        <?php endif; ?>
    
                                        <?php if ($exam_id == 'both'): ?>
                                            <th class="final-head">Final</th>
                                            <th class="final-head">Grade</th>
                                        <?php endif; ?>
                                    </tr>
                                    <tr  style="background-color:lightyellow" >
                                        <th class="subject text-left">Subject</th>
                                        <?php 
                                         if ($exam_id == 'both' || $exam_id == 'TERM 1'): ?>
                                                    <th>Pre Mid (30)</th>
                                                    <th>Oral Skill (20)</th>
                                                    <th>Mid Term (50)</th>
                                                    <th>Total (100)</th>
                                                    <th>Grade</th>
                                                <?php 
                                            endif; ?>
    
                                        <?php if ($exam_id == 'TERM 2' || $exam_id == 'both'): ?>
                                            <th>UT3</th><th>UT4</th><th>Best</th><th>MA/GA</th><th>PA/Portfolio</th><th>NB</th><th>SEA/SE</th><th>Mid</th><th>Total</th>
                                        <?php endif; ?>
    
                                        <?php if ($exam_id == 'both'): ?>
                                            <th></th><th></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                        
                        
                    <?php
                        $gt1 = $gt2 = 0;
                        foreach ($merged_subjects as $sub){
                            $sub_subjects = direct_sql("SELECT * FROM pre_sub_subject WHERE subject_id='{$sub['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                            if(!$sub_subjects) $sub_subjects=[['id'=>0,'sub_subject'=>$sub['subject_name']]];
                                    $ut1 = get_subject_oral_total($stu['id'], 'UNIT TEST 1',$sub['id'], $branch_id);
                                    $ut2 = get_subject_oral_total($stu['id'], 'UNIT TEST 2',$sub['id'], $branch_id);
                                    echo "<br>";
                                    $best_ut = $sub['subject_name'].' = '.max($ut1, $ut2);
                                    $combined = [];
                                        foreach ($best_ut as $subject => $marks) {
                                            $base = str_replace(' WS', '', $subject);
                                            if (!isset($combined[$base])) {
                                                $combined[$base] = 0;
                                            }
                                            $combined[$base] += $marks;
                                        }
                                        $best_ut = $combined; 
                                        print_r($best_ut);
                                    echo "<br>";
                        }
                    endforeach; 
                ?>
                
                                </tbody>
                                <tfoot>
                                    
                                </tfoot>
                                </table>
                        	<div style="width:100%; font-family:Arial, sans-serif; margin-top:2px; display:flex; gap:20px;">
                                 Left Side: Co-Scholastic & Attendance 
                                <div style="flex:0 0 29%;">
                                    <table style="width:100%; border-collapse:collapse; border:1px solid #ccc; border-radius:5px; overflow:hidden;">
                                        <tr style="background-color:#0b3b5c; color:#fff; font-size:14px; text-align:center;">
                                            <th colspan="2" style="padding:10px;">Co-Scholastic Areas</th>
                                        </tr>
                                        <tr style="background-color:#eef5fc;">
                                            <td style="padding:1px 2px;font-size:11px">Discipline & Moral Values</td>
                                            <td style="padding:1px 2px;font-size:11px text-align:left;">A</td>
                                        </tr>
                                        <tr style="background-color:#eef5fc;">
                                            <td style="padding:1px 2px;font-size:11px">Art Education</td>
                                            <td style="padding:1px 2px;font-size:11px text-align:left;">A</td>
                                        </tr>
                                        <tr style="background-color:#eef5fc;">
                                            <td style="padding:1px 2px;font-size:11px">Work Education</td>
                                            <td style="padding:1px 2px;font-size:11px text-align:left;">A</td>
                                        </tr>
                                        <tr style="background-color:#eef5fc;">
                                            <td style="padding:1px 2px;font-size:11px">Health & Physical Education</td>
                                            <td style="padding:1px 2px;font-size:11px text-align:left;">A</td>
                                        </tr>
                                        <tr style="background-color:#eef5fc;">
                                            <td style="padding:1px 2px;font-size:11px">Attendance</td>
                                            <td style="padding:1px 2px;font-size:11px text-align:left;">
                                                <div style="font-size:16px; font-weight:700; color:#2d8cf0;">
                                                    <?php
                                                    $st_att = get_data('attendance',$stu['id'],'present_days','student_id')['data'];
                                                    
                                                    if (isset($att[$student_class])) {
                                                        $t_att = $att[$student_class];
                                                    } else {
                                                        $t_att = 0; 
                                                    }
                                                    ?>
                                                    <?= intval($st_att ?? 0) ?> / <?= intval($t_att) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                 Right Side: Performance & Remarks 
                                <div style="flex:0 0 69%;">
                                    <div style="width:100%; margin-top:1px; font-family:Arial, sans-serif;border-collapse:collapse; border-radius:5px; overflow:hidden;">
                                        <table style="width:100%; border-collapse: collapse; text-align:center; font-size:14px;">
                                            <thead>
                                                <tr style="background-color:#0b3b5c; color:#fff;">
                                                    <th style="padding:8px; border:1px solid #cfcfcf;">Subject</th>
                                                    <th style="padding:8px; border:1px solid #cfcfcf;">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach ((array)$merged_subjects as $sub){
                                                        $sub_rem = get_remarks($student_class, $sub['id'], $grade1, $sub['id']);
                                                        // print_r($sub_rem);
                                                        if($sub_rem != ''){
                                                ?>
                                                <tr style="background-color:#f7fbff;">
                                                    <td style="padding:2px 3px; border:1px solid #cfcfcf; text-align:left;font-size:11px">
                                                        <?= $sub['subject_name'] ?>
                                                    </td>
                                                    <td style="padding:2px 3px; border:1px solid #cfcfcf; text-align:left;font-size:11px">
                                                        <?= $sub_rem; ?>
                                                    </td>
                                                </tr>
                                                <?php } } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <p style="text-align:center; margin-bottom:1px; margin-bottom:4px; color:#0b3b5c;font-size:14px;font-weight:600;background-color:#0b3b5c; color:#fff;">
                                8 Point Grading Scale
                            </p>
                            <table style="width:100%; border-collapse: collapse; text-align:center; font-size:14px;">
                                <thead>
                                    <tr style="background-color:#0b3b5c; color:#fff;">
                                        <th style="padding:1px 2px; border:1px solid #cfcfcf;">Marks Range</th>
                                        <th style="padding:1px 2px; border:1px solid #cfcfcf;">Grade</th>
                                        <th style="padding:1px 2px; border:1px solid #cfcfcf;">Marks Range</th>
                                        <th style="padding:1px 2px; border:1px solid #cfcfcf;">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background-color:#f7fbff;">
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">91 – 100</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">A1</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">81 – 90.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">A2</td>
                                    </tr>
                                    <tr style="background-color:#f7fbff;">
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">71 - 80.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">B1</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">61 - 70.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">B2</td>
                                    </tr>
                                    <tr style="background-color:#f7fbff;">
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">51 - 60.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">C1</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">41 - 50.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">C2</td>
                                    </tr>
                                    <tr style="background-color:#f7fbff;">
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">33 - 40.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">D</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">0 - 32.99</td>
                                        <td style="padding:1px 2px; border:1px solid #cfcfcf;">E (NEEDS IMPROVEMENT)</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="signatures">
                                <div class="sign-box"><div class="sign-line">Class Teacher</div></div>
                                <div class="sign-box"><img src='<?= $principal ?>' width='180px' height='50px'><div class="sign-line">Principal</div></div>
                            </div>
                        </div>
                       
            <?php
                } 
            }
            ?>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>