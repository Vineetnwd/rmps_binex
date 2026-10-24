<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
//    ini_set('display_errors', 1);
//    ini_set('display_startup_errors', 1);
//    error_reporting(E_ALL);
if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('tabulation');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('tabulation', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}

$can_edit = get_data('employee',get_data('user',$user_id,'user_name')['data'],'marks_entry','emp_code')['data'];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Tabulation Marks </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Tabulation Marks</li>
        </ol>
    </section>
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
		    <div>
              <h3 class="box-title">Marks Entry Sheet (Download CSV)</h3>
    
    		  <div class="box-tools pull-right">
    		    <kbd>Double  Click</kbd> on marks to Edit and <kbd>Enter</kbd> to Save
		      </div>
	      </div>
        </div>
		<div class="box-body">
           <?php
        //   $emp_info = get_data('employee',strtoupper($_SESSION['user_name']),null,'emp_code')['data'];
            $emp_code = strtoupper(remove_space($_SESSION['user_name']));
            $ssql = "SELECT * FROM employee WHERE emp_code = '$emp_code'";
            $emp_info = direct_sql($ssql)['data']['0'];
            $subjects = get_all('teacher_subject','*',['teacher_id'=>$emp_info['id']])['data'];
if (!empty($subjects)) {
?>
    <?php foreach ($subjects as $row): 
        $class_name = $row['class_name'];
        $section_name = $row['section_name'];
        $subject_id = $row['subject_name'];
        $exam_id = $row['exam_id']??'';

        $subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
        $exam_data = get_data('exam_list', $exam_id)??[];
        $exam_name = $exam_data['data']['exam_name'] ?? '';
        if(!isset($_REQUEST['exam_id']) && $_REQUEST['exam_id']==''){
    ?>
        <form action="" method="post">
                <div class="card p-1 bg-light border rounded" style='margin-bottom: 0.2rem'>
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong>Class:</strong> <?= htmlspecialchars($class_name) ?>
                            <input type="hidden" name="student_class" value="<?= htmlspecialchars($class_name) ?>">
                        </div>
                        <div class="col-md-2">
                            <strong>Section:</strong> <?= htmlspecialchars($section_name) ?>
                            <input type="hidden" name="student_section" value="<?= htmlspecialchars($section_name) ?>">
                        </div>
                        <div class="col-md-3">
                            <strong>Subject:</strong> <?= htmlspecialchars($subject_name) ?>
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
                        </div>
                        <div class="col-md-3">
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
    <?php } endforeach; ?>

<?php } else { ?>
    <div class="alert alert-warning">No assigned subjects found in <code>teacher_subject</code> table.</div>
<?php } ?>
    	<div class="table-responsive">
    		
            <?php 
    		if (isset($_REQUEST['exam_id']) and isset($_REQUEST['subject_id']))
    		{
    			$subject_name = get_data('subject',$_REQUEST['subject_id'],'subject_column')['data'];
    		?> 
    		<div class="bg-light border p-2 rounded mb-3">
                <i class="fa fa-users text-primary"></i> <strong>Class:</strong> <?= $_REQUEST['student_class'] ?> &nbsp;
                <i class="fa fa-users text-success"></i> <strong>Section:</strong> <?= $_REQUEST['student_section'] ?> &nbsp;
                <i class="fa fa-book text-warning"></i> <strong>Subject:</strong> <?= $subject_name ?> &nbsp;
                <i class="fa fa-pencil text-danger"></i> <strong>Exam:</strong> <?= $_REQUEST['exam_id'] ?>
            </div>

    		<table id="example" class="table table-bordered table-striped">
    					<thead class='op-thead'>
    						<!--<th> Exam ID </th>-->
    						<th> Adm. No. </th>
    						<!--<th> Class </th>-->
    						<th> Roll </th>
    						<th> Name </th>
    						<?php 
    						if($_REQUEST['exam_id']=='UNIT TEST 1'){
    						    $exam_id = 'ut1';
    						echo "<th> UT 1 </th>";
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 2'){
    						    $exam_id = 'ut3';
    						echo "<th> UT 2 </th>";
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 3'){
    						    $exam_id = 'ut3';
    						echo "<th> UT 3 </th>";    					
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 4'){
    						    $exam_id = 'ut4';
    						echo "<th> UT 4 </th>";
    						}elseif($_REQUEST['exam_id'] == 'TERM 1' ){
    						    $exam_id = 'TERM 1';
    						    if($_REQUEST['student_class']=='IX' || $_REQUEST['student_class']=='X'){
            						echo "<th> MA (5)</th>";
            						echo "<th> Portfolio (5)</th>";
            						echo "<th> SE (5)</th>";
    						        echo "<th> Mid Term (80)</th>";
    						    }else{
            						echo "<th> GA (20)</th>";
            						echo "<th> NB (5)</th>";
            						echo "<th> SEA (5)</th>";
        						    if($_REQUEST['student_class']=='I' || $_REQUEST['student_class']=='II'){
        						        echo "<th> Mid Term (60)</th>";
        						    }else{
        						        echo "<th> Mid Term (80)</th>";
        						    }
    						    }
    						}elseif($_REQUEST['exam_id'] == 'TERM 2'){
    						    $exam_id = 'TERM 2';
    						    if($_REQUEST['student_class']=='IX' || $_REQUEST['student_class']=='X'){
            						echo "<th> MA (5)</th>";
            						echo "<th> Portfolio (5)</th>";
            						echo "<th> SE (5)</th>";
        						    if($_REQUEST['subject_id']=='46'){
    						        echo "<th> Mid Term (50)</th>";
        						    }else{
    						        echo "<th> Mid Term (80)</th>";
        						    }
    						    }else{
            						echo "<th> GA (20)</th>";
            						echo "<th> NB (5)</th>";
            						echo "<th> SEA (5)</th>";
        						    if($_REQUEST['student_class']=='I' || $_REQUEST['student_class']=='II'){
        						        echo "<th> Mid Term (60)</th>";
        						    }else{
        						        echo "<th> Mid Term (80)</th>";
        						    }
    						    }
    						}
    						?>
    						
    					</tr>
    					</th>
                       
                        <tbody>
    					<?php
    						$res = get_all('student', '*', ['student_class'=>$_REQUEST['student_class'], 'student_section'=>$_REQUEST['student_section'],'status' =>'ACTIVE'],'student_roll asc');
    						foreach($res['data'] as $row)
    						{   
    						    $r_term = $_REQUEST['term'];
    						    if ($r_term == '') {
                                    if ($_REQUEST['exam_id'] == 'UNIT TEST 1' || $_REQUEST['exam_id'] == 'UNIT TEST 2') {
                                        $r_term = 'TERM 1';
                                    } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 3' || $_REQUEST['exam_id'] == 'UNIT TEST 4') {
                                        $r_term = 'TERM 2';
                                    }else{
                                        $r_term = $_REQUEST['exam_id'];
                                    }
                                }

    						$student_admission=	$row['student_admission'];
    						$student_id=	$row['id'];
    						$sid =	$row['id'];
    						$tabulation = get_all('tabulation','*',['student_id'=>$student_id,'student_admission'=>$student_admission,'term'=>$r_term,'subject'=>$_REQUEST['subject_id'],'branch_id'=>$branch_id])['data'][0];
    				// 		echo "<pre>";
    				// 		print_r($tabulation);
    						$_REQUEST['subject_id'];
    						$ut1 = $tabulation['ut1']??0;
    						$ut2 = $tabulation['ut2']??0;
    						$ut3 = $tabulation['ut3']??0;
    						$ut4 = $tabulation['ut4']??0;
    						$ma = $tabulation['ma']??0;
    						$portfolio = $tabulation['portfolio']??0;
    						$se = $tabulation['se']??0;
    						$mid_term = $tabulation['mid_term']??0;
    						
    						$ga = $tabulation['ga']??0;
    						$nb = $tabulation['nb']??0;
    						$sea = $tabulation['sea']??0;
    						
    						$ga2 = $tabulation['ga']??0;
    						$nb2 = $tabulation['nb']??0;
    						$sea2 = $tabulation['sea']??0;
    				// 		echo "<tr><td>".$_REQUEST['exam_id']."</td>";
    						echo "<td>".$student_admission."</td>";
    				// 		echo "<td>".$row['student_class'].'-'.$row['student_section']."</td>";
    						echo "<td>".$row['student_roll']."</td>";
    						echo "<td class='st_name'>".$row['student_name']."</td>";
    						if($_REQUEST['exam_id']=='UNIT TEST 1'){
    						echo "<td>
    							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ut1' data-col_name='ut1' data-max='$max' >". $ut1 ."</span></td>";
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 2'){
    						echo "<td>
    							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ut2' data-col_name='ut2' data-max='$max' >". $ut2 ."</span></td>";
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 3'){
    						echo "<td>
    							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ut3' data-col_name='ut3' data-max='$max' >". $ut3 ."</span></td>";
    						}elseif($_REQUEST['exam_id']=='UNIT TEST 4'){
    						echo "<td>
    							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ut4' data-col_name='ut4' data-max='$max' >". $ut4 ."</span></td>";
    						}elseif($_REQUEST['exam_id']=='TERM 1'){
    						    if($_REQUEST['student_class']=='IX' || $_REQUEST['student_class']=='X'){
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ma' data-col_name='ma' data-max='5' >". $ma ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'portfolio' data-col_name='portfolio' data-max='5' >". $portfolio ."</span></td>";
            							
        						    if($_REQUEST['subject_id']=='46'){
            						echo "<td>
        							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'se' data-col_name='se' data-max='10' >". $se ."</span></td>";
            						echo "<td>
            						
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'mid_term' data-col_name='mid_term' data-max='50' >". $mid_term ."</span></td>";
        						    }else{
            						echo "<td>
        							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'se' data-col_name='se' data-max='5' >". $se ."</span></td>";
            						echo "<td>
            						
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'mid_term' data-col_name='mid_term' data-max='80' >". $mid_term ."</span></td>";
        						    }
    						    }else{
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ga' data-col_name='ga' data-max='20' >". $ga ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'nb' data-col_name='nb' data-max='5' >". $nb ."</span></td>";
            						echo "<td>
        							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'sea' data-col_name='sea' data-max='5' >". $sea ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'mid_term' data-col_name='mid_term' data-max='80' >". $mid_term ."</span></td>";
    						    }
    						}elseif($_REQUEST['exam_id']='TERM 2'){
    						    if($_REQUEST['student_class']=='IX' || $_REQUEST['student_class']=='X'){
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ma' data-col_name='ma' data-max='5' >". $ma ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'portfolio' data-col_name='portfolio' data-max='5' >". $portfolio ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'se' data-col_name='se' data-max='5' >". $se ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'mid_term' data-col_name='mid_term' data-max='80' >". $mid_term ."</span></td>";
    						    }else{
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'ga' data-col_name='ga' data-max='20' >". json_encode($tabulation) ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'nb' data-col_name='nb' data-max='5' >". $nb ."</span></td>";
            						echo "<td>
        							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'sea' data-col_name='sea' data-max='5' >". $sea ."</span></td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 2' data-subject='{$_REQUEST['subject_id']}' data-exam_id= 'mid_term' data-col_name='mid_term' data-max='80' >". $mid_term ."</span></td>";
    						    }
    						}
    						echo "</tr> ";
    						}
    					?>
                         </tr> 
    				
                        </tbody>
                    </table>
    		<?php } ?>			
            </div> 
        </div>
    </section>
</div>
<?php require_once('system/footer.php'); ?>
<script>
    function reset() {
        $("#update_frm").reset();
    }
</script>
<script>
$(document).ready(function () {
    function toggleUTFields() {
        var term = $('#term').val();
        if (term === 'TERM 1') {
            $('#ut1').closest('.form-group').show();
            $('#ut2').closest('.form-group').show();
            $('#ut3').closest('.form-group').hide();
            $('#ut4').closest('.form-group').hide();
            $('#mid_term').closest('.form-group').show();
        } else if (term === 'TERM 2') {
            $('#ut1').closest('.form-group').hide();
            $('#ut2').closest('.form-group').hide();
            $('#ut3').closest('.form-group').show();
            $('#ut4').closest('.form-group').show();
            $('#annual').closest('.form-group').show();
        } else {
            $('#ut1, #ut2, #ut3, #ut4, #mid_term, #annual').closest('.form-group').hide();
        }
    }

    toggleUTFields();

    $('#term').on('change', function () {
        toggleUTFields();
    });
});
</script>
<script>
$(document).ready(function () {
    function toggleClassBasedFields() {
        let selectedClass = $('#class_name').val()?.toUpperCase() || "";

        // Normalize class input
        const middleClasses = ['III', 'IV', 'V', 'VI', 'VII', 'VIII'];
        const highClasses = ['IX', 'X'];

        $('#ga, #nb, #sea, #ma, #portfolio, #se').closest('.form-group').hide();

        if (middleClasses.includes(selectedClass)) {
            $('#ga, #nb, #sea').closest('.form-group').show();
        } else if (highClasses.includes(selectedClass)) {
            $('#ma, #portfolio, #se').closest('.form-group').show();
        }
    }

    toggleClassBasedFields();

    $('#class_name').on('change', function () {
        toggleClassBasedFields();
    });
});
</script>
		
<script>
function selsub(sel)
	{
		console.log(sel);
		$.ajax({
			type:'post',
			data:{'class_name':sel},
			url:'system/master_process.php?task=select_subject',
			success:function(data)
			{
				//console.log(data);
				$("#subject_list").html(data);
			}
		})
	}
	
$(document).ready(function () {
//   $(document).on('dblclick', '.marks-tab', function () {
//     let $span = $(this);
//     let currentValue = $span.text().trim();
//     let max = parseInt($span.data('max')) || 80;
//     $span.data('previousValue', currentValue);
//     let input = $('<input>', {
//       type: 'number',
//       value: currentValue,
//       max: max,
//       min: 0,
//       class: 'marks-input',
//       css: {
//         width: '60px',
//         padding: '4px'
//       }
//     });
//     $span.html(input);
//     input.focus();
//   });
  
var canEdit = "<?= $can_edit ?>";
console.log(canEdit);
if (canEdit === "YES") {
    $('.marks-tab').each(function () {
        let $span = $(this);
        let currentValue = $.trim($span.text());
        let max = parseInt($span.data('max'), 10) || 80;

        let input = $('<input>', {
            type: 'number',
            val: currentValue,
            min: 0,
            max: max,
            class: 'marks-input'
        }).css({
            width: '60px',
            padding: '4px'
        });

        $span.html(input);
    });
}

  $(document).on('blur', '.marks-input', function (e) {
    // if (e.which === 13) {
      let $input = $(this);
      let newValue = $input.val().trim();
      let $span = $input.closest('.marks-tab');
      let prevValue = $span.data('previousValue');
      let studentAdmission = $span.data('student_admission');
      let studentId = $span.data('student_id');
      let examId = $span.data('exam_id');
      let subject = $span.data('subject');
      let colName = $span.data('col_name');
      let term = $span.data('term');
      let max = parseInt($span.data('max')) || 100;

      if (parseFloat(newValue) > max) {
        alert("Marks can't be greater than " + max);
        $span.text(prevValue);
        return;
      }
      $input.prop('disabled', true);
      $.ajax({
        url: 'system/master_process.php?task=tabulation_single_marks',
        type: 'POST',
        dataType: 'json',
        data: {
          student_admission: studentAdmission,
          exam_id: examId,
          student_id: studentId,
          col_name: colName,
          subject: subject,
          term: term,
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
        // error: function (xhr, status, error) {
        //   console.log("AJAX Error:", status, error);
        //   console.log("Response:", xhr.responseText);
        // //   alert('AJAX error occurred. Try again!');
        //   $span.text(prevValue);
        // }
      });
    // }
  });

//   $(document).on('blur', '.marks-input', function () {
//     let $span = $(this).closest('.marks-tab');
//     let prevValue = $span.data('previousValue');
//     $span.text(prevValue);
//   });
});

</script>