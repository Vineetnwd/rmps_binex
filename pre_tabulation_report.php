<?php 
require_once('system/header.php'); 
require_once('system/menu.php');  

$exam_name = $student_class = $student_section = null;  
if (isset($_REQUEST['exam_id'])) { $exam_id = $_REQUEST['exam_id']; } 
if (isset($_REQUEST['student_class'])) { $student_class = $_REQUEST['student_class']; } 
if (isset($_REQUEST['student_section'])) { $student_section = $_REQUEST['student_section']; } 
?>  

<style>
th{
    background-color: #000 !important;
    color:#fff !important;
    font-size:14px;
    font-weight:600;
}
@media print {
    body * { visibility: hidden; }
    #tbl_a, #tbl_a * { visibility: visible; }
    #tbl_a { page-break-inside: avoid; position: absolute; left: 0; top: 20; }
}
th, td { text-align: center; vertical-align: middle !important; font-size: 12px; }
th{ background-color: #000; color: #fff;padding:0px; }
th.subject-th { background-color: #2E86C1; }
th.subsubject-th { background-color: #5DADE2; color: #000; }
th.paper-th { background-color: #85C1E9; color: #000; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Tabulation Report Sheet</h1>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Tabulation Report Sheet</h3>
                <div class="box-tools pull-right">
                    <i class='fa fa-file-excel-o btn btn-dark text-light btn-sm' onclick='exportxls2()' title='Export to EXCEL'></i>
                    <i class='fa fa-file-pdf-o btn btn-danger text-light btn-sm ml-2' onclick='exportpdf()' title='Export to PDF'></i>
                </div>
            </div>
            <div class="box-body">
                <div class='row'>
                    <form action="" method="post" class="form-inline" style='width:100%'>
                        <div class="form-group mr-2 col-md-3">
                            <label>Class:</label>
                            <select class="form-control" name='student_class' required>
                                <?//php dropdown($class_list, $student_class); ?>
                                <option value="" disabled <?= (!isset($student_class) || $student_class == '') ? 'selected' : '' ?>>Select Class</option>
                                <option value="NUR" <?= ($student_class == 'NUR') ? 'selected' : '' ?>>NUR</option>
                                <option value="LKG" <?= ($student_class == 'LKG') ? 'selected' : '' ?>>LKG</option>
                                <option value="UKG" <?= ($student_class == 'UKG') ? 'selected' : '' ?>>UKG</option>
                            </select>
                        </div>
                        <div class="form-group mr-2 col-md-3">
                            <label>Section:</label>
                            <select class="form-control" name='student_section' required>
                                <?php dropdown($section_list, $student_section); ?>
                            </select>
                        </div>
                        <div class="form-group mr-2 col-md-3">
                            <label>Exam:</label>
                            <select class="form-control" name="exam_id" required>
                                <option value="" disabled <?= (!isset($exam_id) || $exam_id == '') ? 'selected' : '' ?>>Select Term</option>
                                <option value="UNIT TEST 1" <?= ($exam_id == 'UNIT TEST 1') ? 'selected' : '' ?>>UNIT TEST 1</option>
                                <option value="UNIT TEST 2" <?= ($exam_id == 'UNIT TEST 2') ? 'selected' : '' ?>>UNIT TEST 2</option>
                                <option value="TERM 1" <?= ($exam_id == 'TERM 1') ? 'selected' : '' ?>>TERM 1</option>
                                <option value="TERM 2" <?= ($exam_id == 'TERM 2') ? 'selected' : '' ?>>TERM 2</option>
                                <option value="both" <?= ($exam_id == 'both') ? 'selected' : '' ?>>Both</option>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Show">
                    </form>
                </div>
            </div>

<?php
if (!empty($student_class) && !empty($student_section) && !empty($exam_id)) {
    $students = get_all('student','*', [
        'student_class' => $student_class, 
        'student_section' => $student_section,
        'branch_id'=>$_SESSION['branch_id'],
        'status'=>'ACTIVE'
    ])['data'];
    usort($students, fn($a,$b)=> (int)$a['student_roll']-(int)$b['student_roll']);
    
    $subjects = subject_list($student_class);

    // Merge subjects (English/Hindi/Urdu) for lower classes
    $merged_subjects = [];
    if (in_array($student_class, ['I','II','III','IV','V','VI','VII','VIII'])) {
        $english_done = $hindi_done = $urdu_done = false;
        foreach ($subjects as $sub) {
            $sub_id = is_array($sub)? $sub['id']:$sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
            if (!$english_done && stripos($sub_name,'English')!==false){ $merged_subjects[]=['id'=>'english','subject_name'=>'English']; $english_done=true;}
            elseif (!$hindi_done && stripos($sub_name,'Hindi')!==false){ $merged_subjects[]=['id'=>'hindi','subject_name'=>'Hindi']; $hindi_done=true;}
            elseif (!$urdu_done && stripos($sub_name,'Urdu')!==false){ $merged_subjects[]=['id'=>'urdu','subject_name'=>'Sanskrit / Urdu']; $urdu_done=true;}
            elseif(stripos($sub_name,'English')===false && stripos($sub_name,'Hindi')===false && stripos($sub_name,'Urdu')===false){ $merged_subjects[]=['id'=>$sub_id,'subject_name'=>$sub_name]; }
        }
    } else {
        foreach ($subjects as $sub) {
            $sub_id = is_array($sub)? $sub['id']:$sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
            $merged_subjects[]=['id'=>$sub_id,'subject_name'=>$sub_name];
        }
    }
?>


<div class="table-responsive" id="tbl_a">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th rowspan="3">Roll</th>
                <th rowspan="3">Student Name</th>
                <?php 
                // First header row: Subject
                foreach ($merged_subjects as $sub){
                    $sub_subjects = direct_sql("SELECT * FROM pre_sub_subject WHERE subject_id='{$sub['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                    if(!$sub_subjects) $sub_subjects=[['id'=>0,'sub_subject'=>$sub['subject_name']]];
                    $colspan_subject = 0;
                    foreach($sub_subjects as $ss){
                        $paper_res = direct_sql("SELECT * FROM pre_papers WHERE sub_subject_id='{$ss['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'");
                        $papers = $paper_res['data'];
                        $colspan_subject += max(1,$paper_res['count']);
                    }
                    echo "<th class='subject-th' colspan='{$colspan_subject}'>{$sub['subject_name']}</th>";
                }
                ?>
                <!--<th rowspan="3">Total</th>-->
                <!--<th rowspan="3">Grade</th>-->
            </tr>
            <tr>
                <?php 
                // Second header row: Sub-subject
                foreach ($merged_subjects as $sub){
                    $sub_subjects = direct_sql("SELECT * FROM pre_sub_subject WHERE subject_id='{$sub['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                    if(!$sub_subjects) $sub_subjects=[['id'=>0,'sub_subject'=>$sub['subject_name']]];
                    foreach($sub_subjects as $ss){
                        $papers = direct_sql("SELECT * FROM pre_papers WHERE sub_subject_id='{$ss['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                        if(!$papers) $papers=[['id'=>0,'paper_name'=>'Main Paper']];
                        echo "<th class='subsubject-th' colspan='".count($papers)."'>{$ss['sub_subject']}</th>";
                    }
                }
                ?>
            </tr>
            <tr>
                <?php 
                // Third header row: Paper
                foreach ($merged_subjects as $sub){
                    $sub_subjects = direct_sql("SELECT * FROM pre_sub_subject WHERE subject_id='{$sub['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                    if(!$sub_subjects) $sub_subjects=[['id'=>0,'sub_subject'=>$sub['subject_name']]];
                    foreach($sub_subjects as $ss){
                        $papers = direct_sql("SELECT * FROM pre_papers WHERE sub_subject_id='{$ss['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                        if(!$papers) $papers=[['id'=>0,'paper_name'=>'Main Paper']];
                        foreach($papers as $p){
                            echo "<th class='paper-th' style='writing-mode: vertical-rl; transform: rotate(180deg); vertical-align: bottom; white-space: nowrap;'>{$p['paper_name']}</th>";
                        }
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($students as $stu): 
            $total_marks=0;
        ?>
            <tr>
                <td><?= $stu['student_roll'] ?></td>
                <td><?= $stu['student_name'] ?></td>
                <?php 
                foreach ($merged_subjects as $sub){
                    $sub_subjects = direct_sql("SELECT * FROM pre_sub_subject WHERE subject_id='{$sub['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                    if(!$sub_subjects) $sub_subjects=[['id'=>0,'sub_subject'=>$sub['subject_name']]];
                    foreach($sub_subjects as $ss){
                        $papers = direct_sql("SELECT * FROM pre_papers WHERE sub_subject_id='{$ss['id']}' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}'")['data'];
                        if(!$papers) $papers=[['id'=>0,'paper_name'=>'Main Paper','max_marks'=>100]];
                        foreach($papers as $p){
                            $marks = direct_sql("SELECT obtained FROM pre_marks WHERE student_id='{$stu['id']}' AND term='$exam_id' AND subject_id='{$sub['id']}' AND sub_subject_id='{$ss['id']}' AND paper_id='{$p['id']}' AND branch_id='{$_SESSION['branch_id']}'", 'get', false)['data'][0]['obtained'] ?? 0;
                            $total_marks += intval($marks);
                            $display = ($marks==0) ? 'AB' : $marks;
                            echo "<td>{$display}</td>";
                        }
                    }
                }
                ?>
                <!--<td><?//= $total_marks ?></td>-->
                <!--<td><?//= grade_on_marks($total_marks,100) ?></td>-->
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>
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
    var blob = new Blob(["\ufeff" + tab_text], { type: 'application/vnd.ms-excel' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = 'Tabulation_Report.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
function exportpdf() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'pt', 'legal'); // landscape, points, legal size

    // Title
    doc.setFontSize(16);
    doc.text("Tabulation Report", 40, 30);

    // Clone table for PDF
    const table = document.querySelector('#tbl_a table').cloneNode(true);

    // Shorten vertical headers (for compact view)
    table.querySelectorAll('th.paper-th').forEach(th => {
        th.dataset.fullText = th.innerText;
        if (th.innerText.length > 4) th.innerText = th.innerText.slice(0, 3);
    });

    // Generate PDF Table
    doc.autoTable({
        html: table,
        startY: 50,
        theme: 'grid',
        headStyles: { 
            fillColor: [0, 0, 0], 
            textColor: 255, 
            fontSize: 10, 
            halign: 'center', 
            valign: 'middle',
            cellPadding: { top: 12, bottom: 12 }, // Increase top/bottom padding
            minCellHeight: 40, // Force header height (approx 100%)
            lineWidth: 0.1,
        },
        bodyStyles: { 
            fontSize: 8, 
            cellPadding: 3, 
            valign: 'middle' 
        },
        styles: { 
            font: 'helvetica', 
            lineColor: [0,0,0],
            lineWidth: 0.1,
            overflow: 'linebreak',
            minCellHeight: 20,
            halign: 'center', 
            valign: 'middle' 
        },
        columnStyles: {
            0: { halign: 'center' },
            1: { halign: 'left' }
        },
        pageBreak: 'auto',
        didParseCell: function (data) {
            // Highlight AB / 0 cells
            const val = data.cell.text[0];
            if (val === 'AB' || val === '0') {
                data.cell.styles.fillColor = [255, 230, 230];
            }
        },
        willDrawCell: function (data) {
            if (data.row.section === 'head' && data.cell.raw.dataset?.fullText) {
                data.cell.text = [data.cell.raw.dataset.fullText];
            }
        }
    });

    // Save the PDF
    doc.save('Tabulation_Report.pdf');
}
</script>
