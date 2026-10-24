<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$table = 'admit_card';
$student_class = $_GET['student_class'];
$exam_id = $_GET['exam_id'];
?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Marks Setting </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Exam</a></li>
        <li class="breadcrumb-item active">Marks Setting</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
          <h3 class="box-title">Marks Setting</h3>

		 <div class="box-tools pull-right">
		            <form>
		          
						<select name='exam_id' required >
						   <?php dropdown_list('exam_list','id','exam_name', $exam_id);?>
						</select>
				  
						<select name='student_class' required >
						   <?php dropdown($class_list,$student_class);?>
						</select>
                        <button class='btn btn-dark'> Show Subjects </button>
			        </form>
		  </div>
        </div>
				<div class="box-body">
				    <p class='text-danger'> You Can Set Marks Scheme for Each Exam and Class Separately
				     <button class='btn btn-success float-right' id='update_btn'> SAVE</button> 
				    </p>
				       
                        <table class='table table-bordered'>
                            <thead>
                            <tr class='op-thead'>
                                <th> Subject Name  </th>
								<?php
								foreach($marks_type_list as $mstype)
								{
									echo "<th> $mstype </th>";
								}
								?>
                                <th> Full marks  </th>
                                <th> Passing   </th>
                            </tr>
                            </thead> 
                            <tbody>

                          
				    	<form action ='update_marks_scheme' method ='post' id='update_frm'>
				    	    <input class="form-control"  type='hidden' name='exam_id' required  value='<?php echo $exam_id; ?>' >
				    	    <input class="form-control"  type='hidden' name='student_class' required  value='<?php echo $student_class; ?>' >
				    	    <?php
				    	    if(isset($_GET['student_class']))
				    	    {
				    	    $stu_class = $_GET['student_class'];
				    	    $sub_list = subject_list($stu_class);
				    	    
				    	    foreach($sub_list as $sub_id){
				    	        
				    	    $sub_name = get_data('subject',$sub_id,'subject_name')['data'];
				    	    
				    	    $find = get_all('admit_card', '*', array('exam_id'=>$exam_id, 'student_class'=>$stu_class, 'subject_id'=> $sub_id))['data'][0];
				    	    
				    	    ?>
				    	        <tr>
							   <td>
                                    <input class="form-control"  type='hidden'  name='subject_id[]' required  value='<?php echo $sub_id; ?>' >
                                    <?php echo $sub_name; ?>
								</td>
								
								<?php
								foreach($marks_type_list as $mstype)
								{
									$col_name = 'marks_'.remove_space($mstype).'[]';
									$mvalue = $find['marks_'.remove_space($mstype)];
									
									?>
									<td><input class="form-control"  type='number' name='<?= $col_name?>' value='<?= $mvalue ?>' ></td>
								<?php }	?>

								<td><input class="form-control marks_total"  type='number' name='marks_total[]'  value='<?php echo $find['marks_total']; ?>' readonly></td>
                                <td><input class="form-control marks_passing"  type='number' name='marks_passing[]'  value='<?php echo $find['marks_passing']; ?>'></td>
								</tr>
						    
						   <?php  } } ?>     
						        
						</form>
                        </tbody>
                        </table>
				</div>
		</div>
	</section>
</div>
<?php require_once('system/footer.php'); ?>


<script>
    // Function to calculate total for a specific row
    function calculateTotal(row) {
        let total = 0;

        // Select all input fields before the marks_total field
        const inputs = row.querySelectorAll('input[type="number"]:not(.marks_total):not([name="marks_passing[]"])');
        
        inputs.forEach(input => {
            total += parseFloat(input.value) || 0; // Add the value or 0 if empty
        });

        // Update the marks_total field
        row.querySelector('.marks_total').value = total;
    }

    // Add event listeners to dynamically calculate total
    document.querySelectorAll('tbody tr').forEach(row => {
        row.querySelectorAll('input[type="number"]:not(.marks_total):not([name="marks_passing[]"])').forEach(input => {
            input.addEventListener('input', () => calculateTotal(row));
        });
    });
</script>
