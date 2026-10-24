<?php
require_once('system/header.php');
require_once('system/menu.php');

$exam_name = $student_class = $student_section = null;
if (isset($_REQUEST['exam_id'])) {
    $exam_id = $_REQUEST['exam_id'];
}
if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}
?>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #tbl_a,
        #tbl_a * {
            visibility: visible;
        }

        #tbl_a {
            page-break-inside: avoid;
            position: absolute;
            left: 0;
            top: 20;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1> Tabulation Report Sheet </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Tabulation Report Sheet</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Tabulation Report Sheet</h3>
                <div class="box-tools pull-right">
                    <i class='fa fa-file-excel-o btn btn-orange text-light btn-sm' onclick='exportxls2()' title='Export to Excel'></i>
                    <i class='fa fa-file-pdf-o btn btn-dark text-light btn-sm' onclick='exportpdf()' title='Export to PDF'></i>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" name='student_class' required onChange="this.form.submit()">
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
                                <select class="form-control" name="exam_id" required>
                                    <option value="" disabled <?= (!isset($exam_id) || $exam_id == '') ? 'selected' : '' ?>>Select Term</option>
                                    <option value="TERM 1" <?= ($exam_id == 'TERM 1') ? 'selected' : '' ?>>TERM 1</option>
                                    <option value="TERM 2" <?= ($exam_id == 'TERM 2') ? 'selected' : '' ?>>TERM 2</option>
                                    <option value="both" <?= ($exam_id == 'both') ? 'selected' : '' ?>>Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>&nbsp;</label>
                            <input type="submit" class="btn btn-primary btn-block" value="Show Details">
                        </div>
                    </div>
                </form>
            </div>

            <?php
            if (!empty($student_class) && !empty($student_section) && !empty($exam_id)) {
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

                // merge subjects if class I - VIII
                $merged_subjects = [];
                if (in_array($student_class, ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'])) {
                    $english_done = $hindi_done = $urdu_done = false;
                    foreach ($subjects as $sub) {
                        $sub_id = is_array($sub) ? $sub['id'] : $sub;
                        $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];

                        if (!$english_done && (stripos($sub_name, 'English Literature') !== false || stripos($sub_name, 'English Grammar') !== false)) {
                            $merged_subjects[] = ['id' => 'english', 'subject_name' => 'English'];
                            $english_done = true;
                        } elseif (!$hindi_done && (stripos($sub_name, 'Hindi') !== false)) {
                            $merged_subjects[] = ['id' => 'hindi', 'subject_name' => 'Hindi'];
                            $hindi_done = true;
                        } elseif (!$urdu_done && (stripos($sub_name, 'Urdu') !== false)) {
                            $merged_subjects[] = ['id' => 'urdu', 'subject_name' => 'Sanskrit / Urdu'];
                            $urdu_done = true;
                        } elseif (stripos($sub_name, 'English') === false && stripos($sub_name, 'Hindi') === false && stripos($sub_name, 'Urdu') === false) {
                            $merged_subjects[] = ['id' => $sub_id, 'subject_name' => $sub_name];
                        }
                    }
                } else {
                    $merged_subjects = [];
                    foreach ($subjects as $sub) {
                        $sub_id = is_array($sub) ? $sub['id'] : $sub;
                        $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
                        $merged_subjects[] = ['id' => $sub_id, 'subject_name' => $sub_name];
                    }
                }
            ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive" id="tbl_a">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <td colspan="<?= ($exam_id == 'both') ? '23' : '11'; ?>" class="bg-success text-white text-center" style="font-weight: bold; font-size: 16px;">
                                            Tabulation Report - Class: <?= $student_class ?> &nbsp;&nbsp;&nbsp; Section: <?= $student_section ?>
                                        </td>
                                    </tr>
                                    <?php
                                    foreach ((array)$students as $stu):
                                        $gt1 = 0; // reset grand total for each student
                                    ?>
                                        <tr>
                                            <!--<th class="text-center bg-info"></th>-->
                                            <?php if ($exam_id == 'both' || $exam_id == 'TERM 1'): ?>
                                                <th colspan="11" class="text-left bg-dark text-light">
                                                    <strong class='text-light'><?//= $stu['id'] ?> Roll <?= $stu['student_roll'] . " - " . $stu['student_name'] . " (" . $stu['student_class'] . " - " . $stu['student_section'] . ")" ?></strong> TERM 1
                                                </th>
                                            <?php endif; ?>
                                            <?php if ($exam_id == 'both' || $exam_id == 'TERM 2'): ?>
                                                <th colspan="11" class="text-center bg-warning">
                                                    <strong class='text-light'>Roll <?= $stu['student_roll'] . " - " . $stu['student_name'] . " (" . $stu['student_class'] . " - " . $stu['student_section'] . ")" ?></strong> TERM 2
                                                </th>
                                            <?php endif; ?>
                                            <?php if ($exam_id == 'both'): ?>
                                                <th colspan="2" class="text-center bg-primary">Final</th>
                                            <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <th>Subject</th>
                                            <?php
                                            if ($exam_id == 'both' || $exam_id == 'TERM 1'):
                                                if ($student_class == 'IX' || $student_class == 'X') { ?>
                                                    <th>UT1</th>
                                                    <th>UT2</th>
                                                    <th>Best 5</th>
                                                    <th>MA</th>
                                                    <th>Portfolio</th>
                                                    <th>SE</th>
                                                    <th>Pre Mid Term</th>
                                                    <th>Mid Term</th>
                                                    <th>Term 1 Total</th>
                                                    <th>Grade</th>
                                                <?php } else { ?>
                                                    <th>UT1</th>
                                                    <th>UT2</th>
                                                    <th>Best 30</th>
                                                    <th>GA</th>
                                                    <th>PA</th>
                                                    <th>NB</th>
                                                    <th>SEA</th>
                                                    <th>Mid Term</th>
                                                    <th>Term 1 Total</th>
                                                    <th>Grade</th>
                                                <?php }
                                            endif;
                                            if ($exam_id == 'both' || $exam_id == 'TERM 2'):
                                                if ($student_class == 'IX' || $student_class == 'X') { ?>
                                                    <th>UT3</th>
                                                    <th>UT4</th>
                                                    <th>Best 5</th>
                                                    <th>MA</th>
                                                    <th>Portfolio</th>
                                                    <th>SE</th>
                                                    <th>Pre Mid Term</th>
                                                    <th>Mid Term</th>
                                                    <th>Term 2 Total</th>
                                                    <th>Grade</th>
                                                <?php } else { ?>
                                                    <th>UT3</th>
                                                    <th>UT4</th>
                                                    <th>Best 30</th>
                                                    <th>GA</th>
                                                    <th>PA</th>
                                                    <th>NB</th>
                                                    <th>SEA</th>
                                                    <th>Mid Term</th>
                                                    <th>Term 2 Total</th>
                                                    <th>Grade</th>
                                                <?php }
                                            endif;
                                            if ($exam_id == 'both'): ?>
                                                <th>Grand Total</th>
                                                <th>Grade</th>
                                            <?php endif; ?>
                                        </tr>
                                </thead>
                                <tbody>
                                <?php
                                        foreach ((array)$merged_subjects as $sub):
                                            $sub_id = $sub['id'];
                                            $subject_name = $sub['subject_name'];
                                            echo "<tr><td>{$subject_name} </td>";//{$sub_id}

                                            // get TERM 1 marks
                                            if ($exam_id == 'both' || $exam_id == 'TERM 1') {
                                                $ut1 = $ut2 = $ma1 = $portfolio1 = $se1 = $mid1 = $ga1 = $pa1 = $nb1 = $sea1 = $mut1 = $t1t = $prem = 0;
                                                $grade1 = '';
                                            
                                                // --- TERM 1 DATA FETCH ---
                                                if (in_array($sub_id, ['english', 'hindi'])) {
                                                    // Fetch all matching subjects (for language merging)
                                                    $term1_data = direct_sql("SELECT * FROM tabulation WHERE student_id='{$stu['id']}' AND term='TERM 1' AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')", 'get', false)['data'] ?? [];
                                                    
                                                        // echo "<pre>";
                                                        // print_r($term1_data);
                                                        // echo "<hr>";
                                                    foreach ($term1_data as $row) {
                                                        $ut1 += $row['ut1'] / 2;
                                                        $ut2 += $row['ut2'] / 2;
                                                        $ma1 += $row['ma'];
                                                        $portfolio1 += $row['portfolio'];
                                                        $se1 = $row['se'];
                                                        $mid1 = $row['mid_term'];
                                                        $ga1 = $row['ga'];
                                                        $nb1 = $row['nb'];
                                                        $sea1= $row['sea'];
                                                    }
                                            
                                                    $mut1 = max($ut1, $ut2);
                                                    $pa1 = ($ga1 + $mut1) / 5;
                                                }else if (in_array($sub_id, [ 'urdu'])) {
                                                    // Fetch all matching subjects (for language merging)
                                                    $term1_data = direct_sql("SELECT * FROM tabulation WHERE student_id='{$stu['id']}' AND term='TERM 1' AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')", 'get', false)['data'] ?? [];
                                                    
                                                        // echo "<pre>";
                                                        // print_r($term1_data);
                                                        // echo "<hr>";
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
                                                    // Normal single-subject fetch
                                                    $term1 = direct_sql(
                                                        "SELECT * FROM tabulation WHERE student_id='{$stu['id']}' 
                                                         AND subject='{$sub_id}' AND term='TERM 1'",
                                                        'get',
                                                        false
                                                    )['data'][0] ?? null;
                                            
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
                                            
                                                // --- CLASS-WISE CALCULATIONS ---
                                                if ($student_class == 'IX' || $student_class == 'X') {
                                                    if ($sub_id != '46') {
                                                        $mut1 = max($ut1, $ut2) / 10;
                                                    }
                                            
                                                    $prem = $mut1 + $ma1 + $portfolio1 + $se1;
                                                    $t1t = $mid1 + $prem;
                                                    $grade1 = grade($t1t);
                                                    $gt1 += $t1t;
                                                    $subject_totals[$sub_id] = $t1t;
                                            
                                                    echo "<td>{$ut1}</td><td>{$ut2}</td><td>{$mut1}</td><td>{$ma1}</td>
                                                          <td>{$portfolio1}</td><td>{$se1}</td><td>{$prem}</td>
                                                          <td>{$mid1}</td><td>{$t1t}</td><td>{$grade1}</td>";
                                            
                                                } elseif ($student_class == 'I' || $student_class == 'II') {
                                                    $nmid1 = number_format($mid1 * (4 / 3), 2);
                                                    $t1t = $pa1 + $nb1 + $sea1 + $nmid1;
                                                    $gt1 += $t1t;
                                                    $grade1 = grade($t1t);
                                            
                                                    echo "<td>{$ut1}</td><td>{$ut2}</td><td>{$mut1}</td><td>{$ga1}</td>
                                                          <td>{$pa1}</td><td>{$nb1}</td><td>{$sea1}</td>
                                                          <td>{$nmid1}</td><td>{$t1t}</td><td>{$grade1}</td>";
                                            
                                                }else {
                                                    $t1t = $pa1 + $nb1 + $sea1 + $mid1;
                                                    $gt1 += $t1t;
                                                    $grade1 = grade($t1t);
                                            
                                                    echo "<td>{$ut1}</td><td>{$ut2}</td><td>{$mut1}</td><td>{$ga1}</td>
                                                          <td>{$pa1}</td><td>{$nb1}</td><td>{$sea1}</td>
                                                          <td>{$mid1}</td><td>{$t1t}</td><td>{$grade1}</td>";
                                                }
                                            }


                                            // get TERM 2 marks
                                            if ($exam_id == 'both' || $exam_id == 'TERM 2') {
                                                if (in_array($sub_id, ['english', 'hindi', 'urdu'])) {
                                                    $term2_data = direct_sql("SELECT * FROM tabulation WHERE student_id='{$stu['id']}' AND term='TERM 2' AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')", 'get', false)['data'] ?? [];
                                                    $ut3 = $ut4 = $ma2 = $portfolio2 = $se2 = $mid2 = $ga2 = $pa2 = $nb2 = $sea2 = 0;
                                                    foreach ($term2_data as $row) {
                                                        $ut3 += $row['ut3'];
                                                        $ut4 += $row['ut4'];
                                                        $ma2 += $row['ma'];
                                                        $portfolio2 += $row['portfolio'];
                                                        $se2 += $row['se'];
                                                        $mid2 += $row['mid_term'];
                                                        $ga2 += $row['ga'];
                                                        $pa2 += $row['pa'];
                                                        $nb2 += $row['nb'];
                                                        $sea2 += $row['sea'];
                                                    }
                                                } else {
                                                    $term2 = direct_sql("SELECT * FROM tabulation WHERE student_id='{$stu['id']}' AND subject='{$sub_id}' AND term='TERM 2'", 'get', false)['data'][0] ?? null;
                                                    if ($term2) {
                                                        $ut3 = $term2['ut3'];
                                                        $ut4 = $term2['ut4'];
                                                        $ma2 = $term2['ma'];
                                                        $portfolio2 = $term2['portfolio'];
                                                        $se2 = $term2['se'];
                                                        $mid2 = $term2['mid_term'];
                                                        $ga2 = $term2['ga'];
                                                        $pa2 = $term2['pa'];
                                                        $nb2 = $term2['nb'];
                                                        $sea2 = $term2['sea'];
                                                    }
                                                }
                                                echo "<td>{$ut3}</td><td>{$ut4}</td><td>--</td><td>{$ma2}</td><td>{$portfolio2}</td><td>{$se2}</td><td>--</td><td>{$mid2}</td><td>--</td><td>NA</td>";
                                            }

                                            if ($exam_id == 'both') {
                                                $grand_total = intval($mid1) + intval($mid2);
                                                echo "<td>{$grand_total}</td><td>NA</td>";
                                            }
                                            echo "</tr>";
                                        endforeach;


                                        if ($exam_id == 'both' || $exam_id == 'TERM 1') {
                                            if ($student_class == 'IX' || $student_class == 'X') {
                                                if (!empty($subject_totals)) {
                                                    $main_subject = 41; // compulsory subject id
                                                    $tp5 = 0;

                                                    // ensure subject 41 exists
                                                    $tp5 += $subject_totals[$main_subject] ?? 0;

                                                    // exclude 41, sort others by marks descending
                                                    $other_subjects = $subject_totals;
                                                    unset($other_subjects[$main_subject]);
                                                    arsort($other_subjects);

                                                    // take top 4
                                                    $top4 = array_slice($other_subjects, 0, 4);
                                                    $tp5 += array_sum($top4);
                                                }

                                                $per = number_format($tp5 / 5, 2);
                                                echo "<tr><td colspan='4'><strong>Total (TERM 1): {$gt1}</strong></td><td colspan='4'><strong>Total (ENG + TOP 4): {$tp5}</strong></td><td colspan='2'><strong>Percentage : {$per}%</strong></td><td><strong>Grade: " . grade_on_marks($tp5, 5) . "</strong></td></tr>";
                                            } else if($student_class=='VI' || $student_class=='VII' || $student_class=='VIII') {
                                                $per = number_format($gt1 / 8, 2);
                                                echo "<tr><td colspan='6'>.</td><td colspan='2'><strong>Total (TERM 1): {$gt1}</strong></td><td colspan='2'><strong>Percentage : {$per}%</strong></td><td><strong>Grade: " . grade_on_marks($gt1, 5) . "</strong></td></tr>";
                                            } else {
                                                $per = number_format($gt1 / 7, 2);
                                                echo "<tr><td colspan='6'>.</td><td colspan='2'><strong>Total (TERM 1): {$gt1}</strong></td><td colspan='2'><strong>Percentage : {$per}%</strong></td><td><strong>Grade: " . grade_on_marks($gt1, 7) . "</strong></td></tr>";
                                            }
                                        } else if ($exam_id == 'both' || $exam_id == 'TERM 2') {
                                            echo "<tr><td colspan='8' >.</td><td colspan='2'><strong>Total (TERM 2): {$gt2}</strong><td><strong>Grade: " . grade_on_marks($gt2, 7) . "</strong></td></tr>";
                                        } else if ($exam_id == 'both') {
                                            echo "<tr><td '><strong>Total (ANNUAL): {($gt1+$gt2)}</strong><td><strong>Grade: " . grade_on_marks($gt1 + $gt2, 14) . "</strong></td></tr>";
                                        }
                                    endforeach;
                                ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    function exportxls2() {
        var tab = document.getElementById('tbl_a').getElementsByTagName('table')[0];
        var tab_text = "<table border='1px'><tr>";
        for (var j = 0; j < tab.rows.length; j++) {
            tab_text += tab.rows[j].innerHTML + "</tr>";
        }
        tab_text += "</table>";
        tab_text = tab_text.replace(/<img[^>]*>/gi, "");
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");
        var blob = new Blob(["\ufeff" + tab_text], {
            type: 'application/vnd.ms-excel'
        });
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = 'Tabulation_Report.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>


<!-- PDF Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    function exportpdf() {
        const {
            jsPDF
        } = window.jspdf;

        // Create PDF in A4 portrait orientation
        const doc = new jsPDF({
            orientation: "portrait",
            unit: "pt",
            format: "a4"
            // format: [609.45, 894.90]
        });

        // Get parameters safely
        const urlParams = new URLSearchParams(window.location.search);
        const studentClass = urlParams.get('student_class') || '';
        const studentSection = urlParams.get('student_section') || '';
        const titleText = `Tabulation Report - Class: <?= $student_class ?>  Section: <?= $student_section ?>`;

        // Add title
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text(titleText, 40, 40);

        // Convert table to PDF
        doc.autoTable({
            html: '#tbl_a table', // actual table inside div
            startY: 60,
            theme: 'grid',
            tableWidth: 'auto',
            headStyles: {
                fillColor: [0, 51, 102], // dark blue header
                textColor: [255, 255, 255],
                lineColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'left',
                valign: 'middle'
            },
            bodyStyles: {
                lineColor: [0, 0, 0],
                lineWidth: 0.25,
                fontSize: 7, // smaller for portrait
                cellPadding: 3,
                valign: 'middle'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            styles: {
                overflow: 'linebreak',
                cellWidth: 'wrap',
                halign: 'left'
            },
            columnStyles: {
                0: { cellWidth: 140 }, // column index 0 = 150px wide
                // if you want all columns limited to 150px:
                // 1: { cellWidth: 150 },
                // 2: { cellWidth: 150 },
                // etc...
            },
            didParseCell: function(data) {
                const val = data.cell.text[0];
                if (val === "0" || val === "AB" || val === "" || val === "0.00") {
                    data.cell.text = ["AB"]; // ✅ Replace the displayed value with "AB"
                    data.cell.styles.fillColor = [255, 230, 230]; // ✅ Light red background
                }
            },

            didDrawPage: function(data) {
                const pageCount = doc.internal.getNumberOfPages();
                const pageCurrent = doc.internal.getCurrentPageInfo().pageNumber;
                doc.setFontSize(9);
                doc.setTextColor(100);
                doc.text(`Page ${pageCurrent}`, doc.internal.pageSize.width - 80, doc.internal.pageSize.height - 20);
                // doc.text(`Page ${pageCurrent} of ${pageCount}`, doc.internal.pageSize.width - 80, doc.internal.pageSize.height - 20);
            },
            margin: {
                top: 60,
                left: 20,
                right: 20,
                bottom: 30
            }
        });

        // Save with proper filename
        doc.save(`Tabulation_Report_<?= $student_class . "_" . $student_section; ?>.pdf`);
    }
</script>