<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$exam_name = $student_class = $student_section =$subject =null;
if(isset($_REQUEST['exam_name']))
{
	$exam_name = $_REQUEST['exam_name'];
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
	$subject_name = get_data('subject',$_REQUEST['subject_id'],'subject_name')['data'];
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
<title> <?php echo $inst_name . " " .$exam_list[$exam_name] . " ". $subject_name ." ". $student_class; ?> </title>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Marks Entry (Teachers) </h1>
     <?php print_r($exam_list) ?>
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
          <h3 class="box-title">Online Marks Entry </h3>

		  <div class="box-tools pull-right">
		  </div>
        </div>
		<div class="box-body">
            <form action ='' method='post'>
            <div class="row">
                 <!--  page header -->
				<!--<div class="col-lg-2 col-offset-lg-2">-->
				<!--	<div class="form-group">-->
				<!--		<label>Select Session</label>-->
				<!--		<select class="form-control" name='session_year' required>-->
				<!--			<?php dropdown($session_list,$current_session); ?>-->
				<!--		</select>-->
				<!--	</div>-->
				<!--</div>-->
				<div class="col-lg-2 col-offset-lg-2">
					<div class="form-group">
						<label>Select Class</label>
						<select class="form-control" name='student_class' required onChange='selsub(this.value)'>
						    <?php if($user_type == 'TEACHER'){ 
						    $emp = get_data('employee',$_SESSION['user_name'],null,'emp_code')['data'];
						    $emp_cls = $emp['allow_class'];
						    ?>
							<?php// dropdown_where($class_list,$student_class); ?>
							<?php } else { ?>
							<?php dropdown($class_list,$student_class); ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-lg-2">
					
					<div class="form-group">
						<label>Enter Section</label>
						<select class="form-control" name='student_section' required>
							<?php dropdown($section_list,$student_section); ?>
						</select>
					</div>
				
				</div>
				<div class="col-lg-3">
					
					<div class="form-group">
						<label>Select Exam</label>
						<select class="form-control" name='exam_name' required>
							<?php// dropdown_with_key($exam_list,$exam_name); ?>
							<?php dropdown_list('exam_list','id','exam_name', $exam_id); ?>
						</select>
					</div>
				</div>
                <div class="col-lg-3">
					<div class="form-group">
						<label>Select Subject</label>
						<select class="form-control" name='subject_id' required id='subject_list'>
					        <?php  
                                $emp = get_data('employee', $_SESSION['user_name'], null, 'emp_code')['data'];
                                $emp_subs = explode(',', $emp['allow_subject']);
                                $i=0;
                                foreach ($emp_subs as $emp_sub) {
                                   echo $subject_data = get_data('subject', $emp_sub[$i], 'subject_name')['data']."<br>";
                                    if (!empty($subject_data)) {
                                        echo "<option value='{$emp_sub}'>{$subject_data}</option>";
                                    }
                                }$i+1;
                            ?>
                        </select>

					</div>
				
				</div>
				
				<div class="col-lg-2">
					<label>&nbsp; </label>
					<input type="submit" class="btn btn-primary btn-block" value='Show List' >
				</div>
			</form>
			</div>
           
					<div class="table-responsive">
						
                        <?php 
						if (isset($_REQUEST['exam_name']) and isset($_REQUEST['subject_id']))
						{
							$subject_name = get_data('subject',$_REQUEST['subject_id'],'subject_column')['data'];
							$sub_data = get_data('subject',$_REQUEST['subject_id'])['data'];
						
							
						    if($exam_name =='PT2' or $exam_name =='PT1')
    						 {
    						     $nb_fm = $sub_data['nb_fm'];
    						     $se_fm = $sub_data['se_fm'];
    						     $mo_fm = $sub_data['mo_fm'];
    						 }
    						 else
    						 {
    						     $nb_fm = $sub_data['nb_annual'];
    						     $se_fm = $sub_data['se_annual'];
    						     $mo_fm = $sub_data['mo_annual'];
    						 }
						
						?> 
						<table id="example2" class="table table-bordered table-striped">
						    
									<thead>
									<tr>
									    <td colspan='9' align='center' class='bg-primary text-light text-bold'>
									        <?= $_REQUEST['exam_name']. ' of '. $student_class.'-'. $student_section .' for '.$subject_name; ?> 
									    </td>
									</tr>
									<tr>
										<!--<th> Exam Name </th>-->
										<!--<th> Session Year</th>-->
										<th> Adm No. </th>
										<!--<th> Student Id </th>-->
										<!--<th> Class </th>-->
										<th> Roll </th>
										<th> Name </th>
										<!--<th> <?php echo $subject_name; ?> PT</th>-->
										<th> <?php echo add_space($subject_name); ?> NB</th>
										<th> <?php echo add_space($subject_name); ?> SE</th>
										<th> <?php echo add_space($subject_name); ?> MO</th>
										
									</tr>
								
									</th>
                                   
                                    <tbody>
                                    <form action='system/master_process?task=teacher_marks_entry' method='post' enctype='multipart/form-data'>
									 <input type='hidden' name='subject' value='<?php echo $subject_name;?>'>
									 <input type='hidden' name='exam_name' value='<?php echo $exam_name;?>'>
									 <input type='hidden' name='session_year' value='<?php echo $session_list[$db_name];?>'>
									<?php
										$res = get_all('student', '*', array('student_class'=>$student_class, 'student_section'=>$student_section,'status' =>'ACTIVE'),'student_roll');
										foreach($res['data'] as $row)
										{
										    $sid= $row['id'];
										    $student_admission = $row['student_admission'];
										$marks = get_marks($student_admission,$exam_name, $subject_name);
										
											$mo =$subject_name."_mo[]";
                                    		$se =$subject_name."_se[]";
                                    		$nb =$subject_name."_nb[]";
                                    		
                                    		
										if ($marks['mo'] > 0)
                                		{
                                			echo "<tr bgcolor='lightgreen'>";
                                		}
                                		else{
                                			echo "<tr class='default'>";
                                		}
								        echo "<td>
								            <input type='hidden'  name='student_id[]'  value='$sid' >
								            <input type='hidden'  name='student_admission[]'  value='$student_admission'>
								            $student_admission
								            </td>";
							
									
									    echo "<td>".$row['student_roll']."</td>";
										echo "<td>".$row['student_name']."</td>";
										
										if($exam_name)
										
									    echo "<td><input type='number' name='$nb' value='".$marks['nb']."' max='".$nb_fm."' min='0'></td>";
                                		echo "<td><input type='number' name='$se' value='".$marks['se']."' max='".$se_fm."' min='0'></td>";
                                		echo "<td><input type='number' name='$mo'  value='".$marks['mo']."' max ='".$mo_fm."' min='0'></td>";
		
										echo "</tr> ";
										}
									?>
                                   
                                     <tr>
                                        <th colspan='6' >
                                            <center>
                                                <input type='submit' value='Save Marks' class='btn btn-success btn-md'>
                                            </center>
                                        </th>
                                     </tr>
									</form>
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
			url:'master_process?task=select_subject',
			success:function(data)
			{
				//console.log(data);
				$("#subject_list").html(data);
			}
		})
	}
	
	
</script>


 <script>
        // Pass PHP variable to JavaScript
        const classSections = <?php echo json_encode(explode(',', $emp_cls)); ?>;

        // Function to populate the first dropdown
        function populateClasses() {
            const classDropdown = document.querySelector("select[name='student_class']");
            classDropdown.innerHTML = "<option value='' disabled selected>Select Class</option>";
            classSections.forEach(item => {
                const [className] = item.split("-");
                if (!Array.from(classDropdown.options).some(opt => opt.value === className)) {
                    const option = document.createElement("option");
                    option.value = className;
                    option.textContent = className;
                    classDropdown.appendChild(option);
                }
            });
        }

        // Function to populate the second dropdown based on the selected class
        function selsub(selectedClass) {
            const sectionDropdown = document.querySelector("select[name='student_section']");
            sectionDropdown.innerHTML = "<option value='' disabled selected>Select Section</option>";
            classSections.forEach(item => {
                const [className, section] = item.split("-");
                if (className === selectedClass) {
                    const option = document.createElement("option");
                    option.value = section;
                    option.textContent = section;
                    sectionDropdown.appendChild(option);
                }
            });
        }

        // Initialize the first dropdown on page load
        window.onload = populateClasses;
    </script>
