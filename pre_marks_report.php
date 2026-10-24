<?php require_once('system/header.php');
require_once('system/menu.php');
$exam_name = $student_class = $student_section =$subject =null;
if(isset($_REQUEST['exam_id']))
{
	$exam_id = $_REQUEST['exam_id'];
}
if(isset($_REQUEST['student_class']))
{
	$student_class = $_REQUEST['student_class'];
}
if(isset($_REQUEST['student_section']))
{
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
    input[type='text'] {
        border: solid 1px #ddd;
    }

}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Tabulation Report Sheet </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#exam">Exam</a></li>
            <li class="breadcrumb-item active">Tabulation Report Sheet</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Tabulation Report Sheet</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-orange text-light btn-sm" onclick="exportxls()" title="Export to Excel">
                        Export to Excel
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="exportpdf()" title="Export to PDF">
                        Export to PDF
                    </button>
                </div>
            </div>
            <div class="box-body">
                <form action='' method='post'>
                    <div class='row'>

                        <div class="col-lg-2 col-offset-lg-2">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" name='student_class' required
                                    onChange="selsub(this.value)">
                                    <option value='' selected disabled>Select Class</option>
                                    <option value='NUR'>NUR</option>
                                    <option value='LKG'>LKG</option>
                                    <option value='UKG'>UKG</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Select Section</label>
                                <select class="form-control" name='student_section' required>
                                    <?php dropdown($section_list,$student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Subject</label>
                                <select class="form-control" name='subject'id="subjectSelect" required>
                                    <?= dropdown_list('subject','id','subject_name',$_REQUEST['subject']); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">

                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name="exam_id" required>
                                    <option value="" disabled <?= (!isset($_REQUEST['exam_id']) || $_REQUEST['exam_id'] == '') ? 'selected' : '' ?>>Select Term</option>
                                    <?= dropdown_list('exam_list', 'exam_name', 'exam_name', $_REQUEST['exam_id']); ?>
                                    <option value="both" <?= (isset($_REQUEST['exam_id']) && $_REQUEST['exam_id'] == 'both') ? 'selected' : '' ?>>ANNUAL</option>
                                    
                                </select>

                            </div>

                        </div>
                        <div class="col-lg-2">
                            <label>&nbsp; </label>
                            <input type="submit" class="btn btn-primary btn-block" value='Show Details'>
                        </div>
                </form>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <?php 
						if (isset($_REQUEST['exam_id']) and isset($_REQUEST['student_class']))
						{
						 $subject_list =subject_list($student_class);
						 $sl =5;
						 $cols = 3+(count($subject_list)*$sl);
						?>

                        </div>
                        <div class='table-responsive'>
                            <table id="data_tbl" class="table table-bordered table-striped tabulation_report">
                            <thead>
                                <tr>
                                    <td colspan='<?php echo $cols; ?>' align='center'>
                                        <b><?php echo $exam_name . " " . $current_session; ?></b><br>
                                        <?php echo "Marks Details of " . $student_class . " - " . $student_section; ?>
                                    </td>
                                </tr>
                        
                                <tr class='op-thead'>
                                    <th style='min-width:125px'>Class </th>
                                    <th style='min-width:125px'>Name</th>
                                    <?php
                                    $exam_id = strtoupper(trim($_REQUEST['exam_id']));
                        
                                    if ($exam_id == 'TERM 1') {
                                            echo "
                                                <th style='min-width:125px'>UT 1 (30)</th>
                                                <th style='min-width:125px'>UT 2 (30)</th>
                                                <th style='min-width:125px'>Best Out Of 30</th>
                                                <th style='min-width:125px'>GA (20)</th>
                                                <th style='min-width:125px'>PA (10)</th>
                                                <th style='min-width:125px'>NB (5)</th>
                                                <th style='min-width:125px'>SEA (5)</th>
                                                <th style='min-width:125px'>Mid Term (80)</th>
                                                <th style='min-width:155px'>Term 1 Total (100)</th>
                                                <th style='min-width:125px'>Grade</th>";
                                        
                                    } elseif ($exam_id == 'TERM 2') {
                                        
                                            echo "
                                                <th style='min-width:125px'>UT - 3</th>
                                                <th style='min-width:125px'>UT - 4</th>
                                                <th style='min-width:125px'>Best Out Of 30</th>
                                                <th style='min-width:125px'>GA</th>
                                                <th style='min-width:125px'>PA</th>
                                                <th style='min-width:125px'>NB</th>
                                                <th style='min-width:125px'>SEA<th>
                                                <th style='min-width:125px'>Mid Term</th>
                                                <th style='min-width:155px'>Second Term Total</th>
                                                <th style='min-width:125px'>Grade</th>";
                                        
                                    } elseif ($exam_id == 'BOTH') {
                                            echo "
                                                <th style='min-width:125px'>UT - 1</th>
                                                <th style='min-width:125px'>UT - 2</th>
                                                <th style='min-width:125px'>Best Out Of 30</th>
                                                <th style='min-width:125px'>GA</th>
                                                <th style='min-width:125px'>PA</th>
                                                <th style='min-width:125px'>NB</th>
                                                <th style='min-width:125px'>SEA</th>
                                                <th style='min-width:125px'>Mid Term</th>
                                                <th style='min-width:155px'>First Term Total</th>
                                                <th style='min-width:125px'>Grade</th>
                            
                                                <th style='min-width:125px'>UT - 3</th>
                                                <th style='min-width:125px'>UT - 4</th>
                                                <th style='min-width:125px'>Best Out Of 30</th>
                                                <th style='min-width:125px'>GA</th>
                                                <th style='min-width:125px'>PA</th>
                                                <th style='min-width:125px'>NB</th>
                                                <th style='min-width:125px'>SEA</th>
                                                <th style='min-width:125px'>Mid Term</th>
                                                <th style='min-width:155px'>Second Term Total</th>
                                                <th style='min-width:125px'>Grade</th>
                            
                                                <th style='min-width:125px'>Grand Total</th>
                                                <th style='min-width:125px'>Grade</th>";
                                        
                                    } elseif (in_array($exam_id, ['UNIT TEST 1', 'UNIT TEST 2', 'UNIT TEST 3', 'UNIT TEST 4'])) {
                                        echo "
                                            <th style='min-width:125px'>$exam_id ( ". get_data('subject',$_REQUEST['subject'],'subject_name')['data']." )</th>";
                                    }
                                    ?>
                                </tr>
                                <tr class='bg-yellow text-dark'></tr>
                            </thead>
                        
                            <tbody>
                                <?php
                                $subject = remove_space($_REQUEST['subject']);
                                $students = direct_sql("SELECT * FROM student WHERE student_class = '$student_class' AND student_section = '$student_section' AND status='ACTIVE' AND branch_id = $branch_id ORDER BY student_roll", 'get', false)['data'];
                                
                                foreach ((array)$students as $stu_info) {
                                    $student_id = $stu_info['id'];
                                    $tab_data = direct_sql("SELECT * FROM pre_marks WHERE student_id = $student_id AND subject_id = '{$subject}'", 'get', false)['data'];
                                    
                                // echo "<pre>";
                                // print_R($tab_data);
                                    // Initialize variables
                                    $row1 = $row2 = null;
                                    $t1_total = $t2_total = 0;
                                
                                    // Separate TERM 1 and TERM 2 data
                                    foreach ((array)$tab_data as $tab) {
                                        if ($tab['term'] == 'TERM 1') $row1 = $tab;
                                        elseif ($tab['term'] == 'TERM 2') $row2 = $tab;
                                    }
                                
                                    echo "<tr>";
                                    echo "<td>{$stu_info['student_class']} - {$stu_info['student_section']} - {$stu_info['student_roll']}</td>";
                                    echo "<td>{$stu_info['student_name']}</td>";
                                
                                    if ($exam_id == 'TERM 1') {
                                        
                                        if($student_class=='IX' || $student_class =='X'){
                                            if ($row1) {
                                                echo "<td><center>{$row1['ut1']}</center></td>";
                                                echo "<td><center>{$row1['ut2']}</center></td>";
                                                $best_ut_t1 = max($row1['ut1'], $row1['ut2'])/10;
                                                echo "<td><center>{$best_ut_t1}</center></td>";
                                                echo "<td><center>{$row1['ma']}</center></td>";
                                                echo "<td><center>{$row1['portfolio']}</center></td>";
                                                echo "<td><center>{$row1['se']}</center></td>";
                                                $pre_midt1 = $best_ut_t1 + $row1['ma'] + $row1['portfolio'] + $row1['se'];
                                                echo "<td><center>{$pre_midt1}</center></td>";
                                                echo "<td><center>{$row1['mid_term']}</center></td>";
                                                $t1_total = $pre_midt1 + $row1['mid_term'];
                                                echo "<td><center>{$t1_total}</center></td>";
                                                echo "<td><center>". grade($t1_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                            
                                        }else{
                                            if ($row1) {
                                                echo "<td><center>{$row1['ut1']}</center></td>";
                                                echo "<td><center>{$row1['ut2']}</center></td>";
                                                
                                                $term1_ut = max($row1['ut1'], $row1['ut2']);
                                                echo "<td><center>{$term1_ut}</center></td>";
                                                echo "<td><center>{$row1['ga']}</center></td>";
                                                
                                                $pa = ($term1_ut+$row1['ga'])/5;
                                                echo "<td><center>{$pa}</center></td>";
                                                echo "<td><center>{$row1['nb']}</center></td>";
                                                echo "<td><center>{$row1['sea']}</center></td>";
                                                
                                                echo "<td><center>{$row1['mid_term']}</center></td>";
                                                $t1_total = $pa + $row1['nb']+$row1['sea']+$row1['mid_term'];
                                                echo "<td><center>{$t1_total}</center></td>";
                                                echo "<td><center>". grade($t1_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                        }
                                
                                    } elseif ($exam_id == 'TERM 2') {
                                        
                                        if($student_class=='IX' || $student_class =='X'){    
                                            if ($row2) {
                                                echo "<td><center>{$row2['ut3']}</center></td>";
                                                echo "<td><center>{$row2['ut4']}</center></td>";
                                                
                                                $best_ut_t2 = max($row2['ut3'], $row1['ut4'])/10;
                                                echo "<td><center>{$row2['ma']}</center></td>";
                                                echo "<td><center>{$row2['portfolio']}</center></td>";
                                                echo "<td><center>{$row2['se']}</center></td>";
                                                $pre_midt2 = $best_ut_t2 + $row2['ma'] + $row2['portfolio'] + $row2['se'];
                                                echo "<td><center>{$pre_midt2}</center></td>";
                                                echo "<td><center>{$row2['mid_term']}</center></td>";
                                                $t2_total = $pre_midt2 + $row2['mid_term'];
                                                echo "<td><center>{$t2_total}</center></td>";
                                                echo "<td><center>". grade($t2_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                        }else{
                                            if ($row2) {
                                                echo "<td><center>{$row2['ut3']}</center></td>";
                                                echo "<td><center>{$row2['ut4']}</center></td>";
                                                
                                                $term2_ut = max($row1['ut3'], $row1['ut4']); 
                                                echo "<td><center>{$term2_ut}</center></td>";
                                                echo "<td><center>{$row2['ga']}</center></td>";
                                                
                                                $pa2 = ($term2_ut+$row2['ga'])/5;
                                                echo "<td><center>{$pa2}</center></td>";
                                                echo "<td><center>{$row2['nb']}</center></td>";
                                                echo "<td><center>{$row2['sea']}</center></td>";
                                                echo "<td><center>{$row2['mid_term']}</center></td>";
                                                
                                                $t2_total = $pa2 + $row2['nb']+$row2['sea']+$row2['mid_term'];
                                                echo "<td><center>{$t2_total}</center></td>";
                                                echo "<td><center>". grade($t2_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                        }
                                
                                    } elseif ($exam_id == 'BOTH') {
                                        // TERM 1
                                         if($student_class=='IX' || $student_class =='X'){
                                            if ($row1) {
                                                echo "<td><center>{$row1['ut1']}</center></td>";
                                                echo "<td><center>{$row1['ut2']}</center></td>";
                                                $best_ut_t1 = max($row1['ut1'], $row1['ut2'])/10;
                                                echo "<td><center>{$best_ut_t1}</center></td>";
                                                echo "<td><center>{$row1['ma']}</center></td>";
                                                echo "<td><center>{$row1['portfolio']}</center></td>";
                                                echo "<td><center>{$row1['se']}</center></td>";
                                                $pre_midt1 = $best_ut_t1 + $row1['ma'] + $row1['portfolio'] + $row1['se'];
                                                echo "<td><center>{$pre_midt1}</center></td>";
                                                echo "<td><center>{$row1['mid_term']}</center></td>";
                                                $t1_total = $pre_midt1 + $row1['mid_term'];
                                                echo "<td><center>{$t1_total}</center></td>";
                                                echo "<td><center>". grade($t1_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                            
                                            if ($row2) {
                                                echo "<td><center>{$row2['ut3']}</center></td>";
                                                echo "<td><center>{$row2['ut4']}</center></td>";
                                                
                                                $best_ut_t2 = max($row2['ut3'], $row1['ut4'])/10;
                                                echo "<td><center>{$row2['ma']}</center></td>";
                                                echo "<td><center>{$row2['portfolio']}</center></td>";
                                                echo "<td><center>{$row2['se']}</center></td>";
                                                $pre_midt2 = $best_ut_t2 + $row2['ma'] + $row2['portfolio'] + $row2['se'];
                                                echo "<td><center>{$pre_midt2}</center></td>";
                                                echo "<td><center>{$row2['mid_term']}</center></td>";
                                                $t2_total = $pre_midt2 + $row2['mid_term'];
                                                echo "<td><center>{$t2_total}</center></td>";
                                                echo "<td><center>". grade($t2_total)."</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                            
                                        }else{
                                            if ($row1) {
                                                echo "<td><center>{$row1['ut1']}</center></td>";
                                                echo "<td><center>{$row1['ut2']}</center></td>";
                                                
                                                $term1_ut = max($row1['ut1'], $row1['ut2']);
                                                echo "<td><center>{$term1_ut}</center></td>";
                                                echo "<td><center>{$row1['ga']}</center></td>";
                                                
                                                $pa = ($term1_ut+$row1['ga'])/5;
                                                echo "<td><center>{$pa}</center></td>";
                                                echo "<td><center>{$row1['nb']}</center></td>";
                                                echo "<td><center>{$row1['sea']}</center></td>";
                                                
                                                echo "<td><center>{$row1['mid_term']}</center></td>";
                                                $t1_total = $pa + $row1['nb']+$row1['sea']+$row1['mid_term'];
                                                echo "<td><center>{$t1_total}</center></td>";
                                                echo "<td><center>Grade</center></td>";
                                            } else {
                                                echo str_repeat("<td><center>NA</center></td>", 10);
                                            }
                                            
                                        // TERM 2
                                        if ($row2) {
                                            echo "<td><center>{$row2['ut3']}</center></td>";
                                            echo "<td><center>{$row2['ut4']}</center></td>";
                                            
                                            $term2_ut = max($row1['ut3'], $row1['ut4']); 
                                            echo "<td><center>{$term2_ut}</center></td>";
                                            echo "<td><center>{$row2['ga']}</center></td>";
                                            
                                            $pa2 = ($term2_ut+$row2['ga'])/5;
                                            echo "<td><center>{$pa2}</center></td>";
                                            echo "<td><center>{$row2['nb']}</center></td>";
                                            echo "<td><center>{$row2['sea']}</center></td>";
                                            echo "<td><center>{$row2['mid_term']}</center></td>";
                                            
                                            $t2_total = $pa2 + $row2['nb']+$row2['sea']+$row2['mid_term'];
                                            echo "<td><center>{$t2_total}</center></td>";
                                            echo "<td><center>Grade</center></td>";
                                        } else {
                                            echo str_repeat("<td><center>NA</center></td>", 10);
                                        }
                                    }
                                
                                
                                        // Grand Total
                                        $grand_total = $t1_total + $t2_total;
                                        echo "<td><center>" . ($grand_total > 0 ? $grand_total : 'NA') . "</center></td>";
                                        echo "<td><center>". grade($grand_total)."</center></td>";
                                
                                    } elseif (in_array($exam_id, ['UNIT TEST 1', 'UNIT TEST 2', 'UNIT TEST 3', 'UNIT TEST 4'])) {
                                        $ut_key = 'ut' . substr($exam_id, -1);
                                        $ut_mark = 'NA';
                                        foreach ((array)$tab_data as $row) {
                                            if (isset($row[$ut_key])) {
                                                $ut_mark = $row[$ut_key];
                                                break;
                                            }
                                        }
                                        echo "<td><center>{$ut_mark}</center></td>";
                                    }
                                
                                    echo "</tr>";
                                }
                                ?>
                                </tbody>

                        </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>
<script>
    function selsub(selectedClass) {
        const branchId = "<?= $_SESSION['branch_id']; ?>"; // Embed PHP variable

        $.ajax({
            url: 'system/master_process.php?task=get_subjects', // Change to your target PHP script
            type: 'POST',
            data: {
                class_name: selectedClass,
                branch_id: branchId
            },
            success: function(response) {
                console.log("Response from server:", response);
                $('#subjectSelect').html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
function exportpdf() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: "landscape" });

    doc.text("Tabulation Report", 14, 10); 

    const table = document.querySelector('.tabulation_report');
    if (!table) {
        alert("Table not found!");
        return;
    }
    const colCount = table.querySelectorAll('th').length;
    const columnStyles = {};
    for (let i = 0; i < colCount; i++) {
        columnStyles[i] = { cellWidth: 24 }; 
    }

    // Generate PDF
    doc.autoTable({
        html: table,
        startY: 20,
        theme: 'grid',
        columnStyles: columnStyles,
        headStyles: { fillColor: [0, 0, 0] }, 
        styles: { fontSize: 10 }
    });

    // Save PDF
    doc.save('tabulation.pdf');
}
</script>
