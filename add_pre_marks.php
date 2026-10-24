<?php 
require_once('system/header.php'); 
require_once('system/menu.php');

// Detect edit or new tabulation entry
// if (isset($_GET['link']) && $_GET['link'] != '') {
//     $data = decode($_GET['link']);
//     $id = $data['id'];
//     $isedit = 'yes';
// } else {
//     $emp = insert_row('tabulation');
//     $id = $emp['id'];
//     $isedit = 'no';
// }

// if ($id != '') {
//     $res = get_data('tabulation', $id);
//     if ($res['count'] > 0 && $res['status'] == 'success') {
//         extract($res['data']);
//     }
// }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1> Tabulation Marks </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Tabulation Marks</li>
        </ol>
    </section>

    <section class="content">  
      <div class="box box-default">
        <div class="box-header with-border">
            <div>
              <h3 class="box-title">Marks Entry Sheet (Download CSV)</h3>
              <div class="box-tools pull-right">
                <kbd>Double Click</kbd> on marks to Edit and <kbd>Enter</kbd> to Save
              </div>
            </div>
        </div>

        <div class="box-body">
        <?php
            $emp_code = strtoupper(remove_space($_SESSION['user_name']));
            $ssql = "SELECT * FROM employee WHERE emp_code = '$emp_code'";
            $emp_info = direct_sql($ssql)['data']['0'];
            $subjects = get_all('teacher_subject','*',['teacher_id'=>$emp_info['id']])['data'];

            if (!empty($subjects)) {
                foreach ($subjects as $row): 
                    $class_name   = $row['class_name'];
                    $section_name = $row['section_name'];
                    $subject_id   = $row['subject_name'];
                    $exam_id      = $row['exam_id'] ?? '';

                    $subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
                    $exam_data    = get_data('exam_list', $exam_id) ?? [];
                    $exam_name    = $exam_data['data']['exam_name'] ?? '';

                    if (!isset($_REQUEST['exam_id']) || $_REQUEST['exam_id']==''){
        ?>
            <form action="" method="post">
                <div class="card p-1 bg-light border rounded" style="margin-bottom:0.2rem">
                    <div class="row align-items-center">
                        <div class="col-md-1">
                            <strong>Class:</strong> <?= htmlspecialchars($class_name) ?>
                            <input type="hidden" name="student_class" value="<?= htmlspecialchars($class_name) ?>">
                        </div>
                        <div class="col-md-1">
                            <strong>Section:</strong> <?= htmlspecialchars($section_name) ?>
                            <input type="hidden" name="student_section" value="<?= htmlspecialchars($section_name) ?>">
                        </div>
                        <div class="col-md-3">
                            <strong>Subject:</strong> <?= htmlspecialchars($subject_name) ?>
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
                        </div>
                        <div class="col-md-3">
                            <strong>Sub Subject:</strong> 
                            <select class="form-select" name="sub_subject_id" required>
                                <?= dropdown_where('pre_sub_subject', 'id', 'sub_subject', ['subject_id'=>$subject_id]); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <strong>Exam:</strong> <?= htmlspecialchars($exam_name) ?>
                            <select class="form-select" name="exam_id" required>
                                <?= dropdown_list('exam_list', 'exam_name', 'exam_name', $_REQUEST['exam_id']); ?>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-dark w-100">Show Details</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php } endforeach; } else { ?>
            <div class="alert alert-warning">No assigned subjects found in <code>teacher_subject</code> table.</div>
        <?php } ?>

        <div class="table-responsive">
        <?php 
        if (isset($_REQUEST['exam_id']) && isset($_REQUEST['subject_id'])) {
            $subject_name = get_data('subject',$_REQUEST['subject_id'],'subject_column')['data'];
            $sub_subject_name = get_data('pre_sub_subject',$_REQUEST['sub_subject_id'],'sub_subject')['data'];
        ?> 
            <div class="bg-light border p-2 rounded mb-3">
                <i class="fa fa-users text-primary"></i> <strong>Class:</strong> <?= $_REQUEST['student_class'] ?> &nbsp;
                <i class="fa fa-users text-success"></i> <strong>Section:</strong> <?= $_REQUEST['student_section'] ?> &nbsp;
                <i class="fa fa-book text-warning"></i> <strong>Subject:</strong> <?= $subject_name ?> &nbsp;
                <i class="fa fa-book text-dark"></i> <strong>Sub Subject:</strong> <?= $sub_subject_name ?> &nbsp;
                <i class="fa fa-pencil text-danger"></i> <strong>Exam:</strong> <?= $_REQUEST['exam_id'] ?>
            </div>

            <table id="example" class="table table-bordered table-striped">
                <thead class='op-thead'>
                    <tr>
                        <th>Adm. No.</th>
                        <th>Roll</th>
                        <th>Name</th>
                        <?php 
                        // if($_REQUEST['exam_id']=='UNIT TEST 1'){
                        //     $exam_id = 'ut1';
                        //     echo "<th>UT 1</th>";
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 2'){
                        //     $exam_id = 'ut2';
                        //     echo "<th>UT 2</th>";
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 3'){
                        //     $exam_id = 'ut3';
                        //     echo "<th>UT 3</th>";                    
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 4'){
                        //     $exam_id = 'ut4';
                        //     echo "<th>UT 4</th>";
                        // }else
                        // if($_REQUEST['exam_id'] == 'TERM 1' || $_REQUEST['exam_id'] == 'TERM 2'){
                        
                        
                        if($_REQUEST['exam_id']){
                            $papers = get_all('pre_papers','*',['sub_subject_id'=>$_REQUEST['sub_subject_id']]);
                            foreach ($papers['data'] as $paper){
                                if($_REQUEST['exam_id']=='TERM 1' || $_REQUEST['exam_id']=='TERM 2'){
                                $max_m = $paper['max_marks'] ?? 50;
                                }else{
                                 $max_m = 30; 
                                }
                                echo "<th>".$paper['paper_name']." (".$max_m.")</th>";
                            }
                        }
                        if($_REQUEST['exam_id']=='TERM 1' || $_REQUEST['exam_id']=='TERM 2'){
                        $max_m = $paper['max_marks'] ?? 50;
                        }else{
                         $max_m = 30; 
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $res = get_all('student','*',[
                        'student_class'=>$_REQUEST['student_class'],
                        'student_section'=>$_REQUEST['student_section'],
                        'status'=>'ACTIVE'
                    ],'student_roll asc');

                    foreach($res['data'] as $row){   
                        $r_term = $_REQUEST['term'] ?? '';
                        if ($r_term == '') {
                            // if ($_REQUEST['exam_id'] == 'UNIT TEST 1' || $_REQUEST['exam_id'] == 'UNIT TEST 2') {
                            //     $r_term = 'TERM 1';
                            // } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 3' || $_REQUEST['exam_id'] == 'UNIT TEST 4') {
                            //     $r_term = 'TERM 2';
                            // } else {
                                $r_term = $_REQUEST['exam_id'];
                            // }
                        }

                        $student_admission = $row['student_admission'];
                        $student_id = $row['id'];
                        $sid = $row['id'];

                        $tabulation = get_all('tabulation','*',[
                            'student_id'=>$student_id,
                            'student_admission'=>$student_admission,
                            'term'=>$r_term,
                            'subject'=>$_REQUEST['subject_id'],
                            'branch_id'=>$branch_id
                        ])['data'][0];

                        // $ut1 = $tabulation['ut1']??0;
                        // $ut2 = $tabulation['ut2']??0;
                        // $ut3 = $tabulation['ut3']??0;
                        // $ut4 = $tabulation['ut4']??0;

                        echo "<tr>";
                        echo "<td>".$student_admission."</td>";
                        echo "<td>".$row['student_roll']."</td>";
                        echo "<td class='st_name'>".$row['student_name']."</td>";

                        // if($_REQUEST['exam_id']=='UNIT TEST 1'){
                        //     echo "<td><span class='marks-tab' data-student_id='$sid' data-student_admission='$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id='ut1' data-col_name='ut1' data-max='20'>{$ut1}</span></td>";
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 2'){
                        //     echo "<td><span class='marks-tab' data-student_id='$sid' data-student_admission='$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id='ut2' data-col_name='ut2' data-max='20'>{$ut2}</span></td>";
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 3'){
                        //     echo "<td><span class='marks-tab' data-student_id='$sid' data-student_admission='$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id='ut3' data-col_name='ut3' data-max='20'>{$ut3}</span></td>";
                        // }elseif($_REQUEST['exam_id']=='UNIT TEST 4'){
                        //     echo "<td><span class='marks-tab' data-student_id='$sid' data-student_admission='$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id='ut4' data-col_name='ut4' data-max='20'>{$ut4}</span></td>";
                        // }elseif($_REQUEST['exam_id']=='TERM 1' || $_REQUEST['exam_id']=='TERM 2'){
                            // print_r($_REQUEST);
                            foreach ($papers['data'] as $paper){
                                $paperMarks = get_all('pre_marks','*',[
                                    'student_id'=>$student_id,
                                    'paper_id'=>$paper['id'],
                                    'term'=>$r_term,
                                    'subject_id'=>$_REQUEST['subject_id'],
                                    'sub_subject_id'=>$_REQUEST['sub_subject_id'],
                                    'branch_id'=>$branch_id
                                ])['data'][0]['obtained'] ?? '';
                                $obtained = $paperMarks !== '' ? $paperMarks : 'NA';

                                echo "<td><span class='marks-tab' data-student_id='{$sid}' data-student_admission='{$student_admission}' data-term='{$r_term}' data-subject='{$_REQUEST['subject_id']}' data-sub_subject='{$_REQUEST['sub_subject_id']}' data-paper_id='{$paper['id']}' data-col_name='obtained' data-max='{$max_m}'>{$obtained}</span></td>";
                            }
                        // }
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
        <?php } ?>          
        </div>
      </div>
    </section>
</div>
<?php require_once('system/footer.php'); ?>

<script>
$(document).ready(function () {
  // Double click to edit
//   $(document).on('dblclick', '.marks-tab', function () {
      
  $('.marks-tab').each(function () {
    let $span = $(this);
    if ($span.find('input').length > 0) return;

    let currentValue = $span.text().trim();
    let max = parseInt($span.data('max')) || 80;

    let input = $('<input>', {
      type: 'number',
      value: currentValue === 'NA' ? '' : currentValue,
      max: max,
      min: 0,
      class: 'marks-input',
      css: { width: '60px', padding: '4px' }
    });

    $span.data('previousValue', currentValue);
    $span.empty().append(input);
    input.focus();
  });
  

  
  // Save on blur or Enter
  $(document).on('blur keypress', '.marks-input', function (e) {
    if (e.type === 'keypress' && e.which !== 13) return;

    let $input = $(this);
    let newValue = $input.val().trim();
    let $span = $input.closest('.marks-tab');
    let prevValue = $span.data('previousValue');
    let studentAdmission = $span.data('student_admission');
    let studentId = $span.data('student_id');
    let examId = $span.data('exam_id');
    let subject_id = $span.data('subject');
    let colName = $span.data('col_name');
    let term = $span.data('term');
    let paperId = $span.data('paper_id');
    let subSubject = $span.data('sub_subject');
    let max = parseInt($span.data('max')) || 100;

    if (newValue === '' || isNaN(newValue)) newValue = 'NA';
    if (newValue !== 'NA' && parseFloat(newValue) > max) {
      alert("Marks can't be greater than " + max);
      $span.text(prevValue);
      return;
    }

    $.ajax({
      url: 'system/master_process.php?task=tabulation_pre_marks',
      type: 'POST',
      dataType: 'json',
      data: {
        student_admission: studentAdmission,
        exam_id: examId,
        student_id: studentId,
        col_name: colName,
        subject_id: subject_id,
        term: term,
        paper_id: paperId,
        sub_subject_id: subSubject,
        value: newValue
      },
      success: function (response) {
        if (response.status === 'success') {
          $span.text(newValue);
        } else {
          console.log('Error updating marks. Try again!');
          $span.text(prevValue);
        }
      }
    });
  });
});
</script>
