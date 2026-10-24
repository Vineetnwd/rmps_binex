<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$exam_id = $student_class = $student_section =$subject =null;
if(isset($_REQUEST['exam_id']))
{
	$exam_id = $_REQUEST['exam_id'];
	$exam_name = get_data('exam_list',$exam_id,'exam_name')['data'];
}
if(isset($_REQUEST['student_class']))
{
	$student_class = $_REQUEST['student_class'];
}
if(isset($_REQUEST['student_section']))
{
	$student_section = $_REQUEST['student_section'];
}
if(isset($_REQUEST['subject_id']))
{
	$subject_id = $_REQUEST['subject_id'];
	$subject_name = get_data('subject',$subject_id,'subject_name')['data'];
}
?>
<style>
@media print {
  body * {
    visibility: hidden;
  }
  #tbl_a, #tbl_a * {
    visibility: visible;
  }
  #tbl_a {
    page-break-inside: avoid;
    position: absolute;
    left: 0;
    top: 20;
  }
  input[type='text']{
      border:solid 1px #ddd;
  }

}
</style>
<title> <?php echo $inst_name . " " .$exam_name . " ". $subject_name ." ". $student_class; ?> </title>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Marks Entry </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Exam</a></li>
        <li class="breadcrumb-item active">Marks Entry</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
          <h3 class="box-title">Marks Entry Sheet (Download CSV)</h3>

		  <div class="box-tools pull-right">
		  <kbd>Double  Click</kbd> on marks to Edit and <kbd>Enter</kbd> to Save
		  </div>
        </div>
		<div class="box-body">
            <form action ='' method='post'>
            <div class="row">
                 <!--  page header -->
				<div class="col-md-2">
					<div class="form-group">
						<label>Select Class</label>
						<select class="form-control" name='student_class' required onChange='selsub(this.value)'>
							<?php dropdown($class_list,$student_class); ?>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					
					<div class="form-group">
						<label>Enter Section</label>
						<select class="form-control" name='student_section' required>
							<?php dropdown($section_list,$student_section); ?>
						</select>
					</div>
				
				</div>
				<div class="col-md-3">
					
					<div class="form-group">
						<label>Select Exam</label>
						<select class="form-control" name='exam_id' required>
							<?php dropdown_list('exam_list','id','exam_name', $exam_id); ?>
						</select>
					</div>
				
				</div>
                <div class="col-md-3">
					
					<div class="form-group">
						<label>Select Subject</label>
						<select class="form-control" name='subject_id' required id='subject_list'>
							<?php dropdown_list('subject','id','subject_name', $subject_id); ?>
						</select>
					</div>
				
				</div>
				
				<div class="col-md-2">
					<label>&nbsp; </label>
					<input type="submit" class="btn btn-dark text-light btn-block" value='Show Details' >
				</div>
			</form>
			</div>
           
					<div class="table-responsive">
						
                        <?php 
						if (isset($_REQUEST['exam_id']) and isset($_REQUEST['subject_id']))
						{
							$subject_name = get_data('subject',$_REQUEST['subject_id'],'subject_column')['data'];
						?> 
						<table id="example" class="table table-bordered table-striped">
									<thead class='op-thead'>
										
										
										<th> Exam ID </th>
										<th> Student Admission </th>
										<th> Class </th>
										<th> Roll </th>
										<th> Name </th>
										<?php 
										foreach ($marks_type_list as $mstype)
										{
											$max = max_marks($exam_id, $subject_id,$mstype);
											echo "<th>". add_space( $subject_name." ".$mstype). "</th>";
										}
										?>
										
									</tr>
									</th>
                                   
                                    <tbody>
									<?php
										$res = get_all('student', '*', array('student_class'=>$student_class, 'student_section'=>$student_section,'status' =>'ACTIVE'));
										foreach($res['data'] as $row)
										{
										$student_admission=	$row['student_admission'];
										$marks = get_marks($student_admission,$exam_id, $subject_name);
										echo "<tr><td>".$exam_id."</td>";
										echo "<td>".$student_admission."</td>";
										echo "<td>".$row['student_class'].'-'.$row['student_section']."</td>";
										echo "<td>".$row['student_roll']."</td>";
										echo "<td>".$row['student_name']."</td>";

										foreach ($marks_type_list as $mstype)
										{
											$col_name = remove_space($subject_name."_".$mstype);
											$max = max_marks($exam_id, $subject_id,$mstype);
											echo "<td>
											<span class='marks-tab' data-student_admission= '$student_admission' data-exam_id= '$exam_id' data-col_name='$col_name' data-max='$max' >". $marks[remove_space($mstype)] ."</span></td>";
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
	<?php require_once('system/footer2.php'); ?>		
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

	$(document).ready(function() {
  // Handle double-click to make the field editable
  $(document).on('dblclick', '.marks-tab', function() {
    let currentElement = $(this);
    let currentValue = currentElement.text().trim();
    let max = currentElement.data('max');

    // Replace text with an input box
    let input = $('<input>', {
      type: 'number',
      value: currentValue,
	  max: max,
	  min:0,
      class: 'marks-input',
      css: {
        width: '50px',
		padding:'5px',
      }
    });

    currentElement.html(input);
    input.focus();
  });

  // Handle "Enter" keypress to save the data via AJAX
  $(document).on('keypress', '.marks-input', function(e) {
    if (e.which === 13) { // Enter key
      let inputElement = $(this);
      let newValue = inputElement.val().trim();
      let parentSpan = inputElement.closest('.marks-tab');

      // Gather data attributes
      let studentAdmission = parentSpan.data('student_admission');
      let examId = parentSpan.data('exam_id');
      let colName = parentSpan.data('col_name');
      let max = parentSpan.data('max');
	
	  if(parseInt(newValue)>parseInt(max))
	  {
		alert("Marks can't be greater than " + max);
		parentSpan.text(parentSpan.data('previousValue'));
	  }
	  else{
      // AJAX call to update the value
      $.ajax({
        url: 'system/master_process.php?task=single_marks_update', // Change to your actual URL
        type: 'POST',
		dataType:'json',
        data: {
          student_admission: studentAdmission,
          exam_id: examId,
          col_name: colName,
          value: newValue
        },
        success: function(response) {
          if (response.status=='success') {
			parentSpan.text(newValue); // Update the value in the span
          } else {
            alert('Error updating marks. Try again!');
            parentSpan.text(parentSpan.data('previousValue')); // Reset value
          }
        },
        error: function() {
          alert('AJAX call failed. Try again!');
          parentSpan.text(parentSpan.data('previousValue')); // Reset value
        }
      });

      // Set the previous value in case of failure
      parentSpan.data('previousValue', parentSpan.text());
	}
    }
  });

  // Handle input blur (optional) to reset if focus is lost
  $(document).on('blur', '.marks-input', function() {
    let inputElement = $(this);
    let parentSpan = inputElement.closest('.marks-tab');
    parentSpan.text(parentSpan.data('previousValue'));
  });
});

</script>


