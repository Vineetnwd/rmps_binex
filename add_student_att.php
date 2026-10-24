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
    $emp = insert_row('attendance');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('attendance', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Students Attendance Update </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Students Attendance Update</li>
        </ol>
    </section>
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
		    <div>
              <h3 class="box-title">Attendance Sheet (Download CSV)</h3>
    
    		  <div class="box-tools pull-right">
    		    <kbd>Double  Click</kbd> on value to Edit and <kbd>Enter</kbd> to Save
		      </div>
	      </div>
        </div>
		<div class="box-body">
            <form action="" method="post">
                <div class="card p-1 bg-light border rounded" style='margin-bottom: 0.2rem'>
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong>Class:</strong> <?= htmlspecialchars($class_name) ?>
                            <select class="form-select" name="student_class" required>
					            <?php dropdown($class_list,$class_name); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <strong>Section:</strong> <?= htmlspecialchars($section_name) ?>
                            <select class="form-select" name="student_section" required>
						        <?php dropdown($section_list,$section_name); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <strong>Exam:</strong> <?= htmlspecialchars($exam_name) ?>
                            <select class="form-select" name="exam_id" required>
                                <?= dropdown_list('exam_list', 'exam_name', 'exam_name', @$_REQUEST['exam_id']); ?>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-dark w-100">Show Details</button>
                        </div>
                    </div>
                </div>
            </form>

    	<div class="table-responsive">
    		
            <?php 
    		if (isset($_REQUEST['exam_id']))
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
    						<th> Adm. No. </th>
    						<th> Roll </th>
    						<th> Name </th>
    						<th> Present Days </th>
    					</tr>
    					</th>
                       
                        <tbody>
    					<?php
    						$res = get_all('student', '*', ['student_class'=>$_REQUEST['student_class'], 'student_section'=>$_REQUEST['student_section'],'status' =>'ACTIVE'],'student_roll asc');
    						foreach($res['data'] as $row)
    						{   
    						  //  $r_term = $_REQUEST['term'];

    						$student_admission=	$row['student_admission'];
    						$student_id=	$row['id'];
    						$sid =	$row['id'];
    						$tabulation = get_all('attendance','*',['student_id'=>$student_id,'student_admission'=>$student_admission,'term'=>'TERM 1','branch_id'=>$branch_id])['data'][0];
    						print_r($r_term);
    						$present_days = $tabulation['present_days']??0;
    						echo "<td>".$student_admission."</td>";
    						echo "<td>".$row['student_roll']."</td>";
    						echo "<td class='st_name'>".$row['student_name']."</td>";
            						echo "<td>
            							<span class='marks-tab' data-student_id= '$sid' data-student_admission= '$student_admission' data-term='TERM 1' data-col_name='present_days'  >". $present_days ."</span></td>";
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
$(document).ready(function () {
  $('.marks-tab').each(function () {
    let $span = $(this);
    let currentValue = $span.text().trim();
    let max = parseInt($span.data('max')) || 80;

    let input = $('<input>', {
      type: 'number',
      value: currentValue,
      max: max,
      min: 0,
      class: 'marks-input',
      css: {
        width: '60px',
        padding: '4px'
      }
    });

    $span.html(input);
  });
  
  $(document).on('blur', '.marks-input', function (e) {
    // if (e.which === 13) {
      let $input = $(this);
      let newValue = $input.val().trim();
      let $span = $input.closest('.marks-tab');
      let studentAdmission = $span.data('student_admission');
      let studentId = $span.data('student_id');
      let colName = $span.data('col_name');
      let term = $span.data('term');

      $input.prop('disabled', true);
      $.ajax({
        url: 'system/master_process.php?task=add_student_att',
        type: 'POST',
        dataType: 'json',
        data: {
          student_admission: studentAdmission,
          student_id: studentId,
          col_name: colName,
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
      });
  });
});

</script>