<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$exam_name = $student_class = $student_section =$subject =null;
if(isset($_REQUEST['exam_id']))
{
	$exam_id = $_REQUEST['exam_id'];
	$exam_name = get_data('exam_list',$exam_id,'exam_name')['data'];
}
if(isset($_REQUEST['student_class']))
{
	$student_class = $_REQUEST['student_class'];
	$subject_list = co_subject_list($student_class);
	
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
<title> <?php echo $inst_name . " " .$exam_list[$exam_name] . " ". $subject_name ." ". $student_class; ?> </title>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Marks Entry (Co-Scholostic) </h1>
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
                    <button id='update_btn' class='btn btn-success btn-md'>Save Marks</button>
                </div>
            </div>
            <div class="box-body">
                <form action='' method='post'>
                    <div class="row">
                        <div class="col-lg-3 col-offset-lg-2">
                            <div class="form-group">
                                <label>Select Class</label>
                                <!--<select class="form-control" name='student_class' required-->
                                <!--    onChange='selsub(this.value)'>-->
                                <!--    <?php dropdown($class_list,$student_class); ?>-->
                                <!--</select>-->
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
                        <div class="col-lg-3">

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
                                <select class="form-control" name='exam_id' required>
                                    <option name=''> Choose A Exam </option>
                                    <?php dropdown_list('exam_list','id','exam_name', @$exam_id); ?>
                                </select>
                            </div>

                        </div>


                        <div class="col-lg-2">
                            <label>&nbsp; </label>
                            <input type="submit" class="btn btn-dark btn-block" value='Show List'>
                        </div>
                </form>
            </div>

            <div class="table-responsive">

                <?php 
						if (isset($_REQUEST['exam_id']))
						{
							
						
						?>
                <table id="example2" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <td colspan='9' align='center' class='bg-yellow text-dark text-bold'>
                                Enter Marks for <?= $exam_name. ' of '. $student_class.'-'. $student_section ; ?>
                            </td>
                        </tr>
                        <tr class='op-thead'>
                            <th> Adm No. </th>
                            <th> Roll </th>
                            <th> Name </th>
                            <?php
										foreach((array)$subject_list as $subject)
										{
											echo "<th>". $subject['subject_name']. "</th>";
										}
										?>
                            <th> Attendance </th>
                            <th> Remarks </th>

                        </tr>

                        </th>

                    <tbody>
                        <form action='co_marks_entry' method='post' enctype='multipart/form-data' id='update_frm'>
                            <input type='hidden' name='exam_id' value='<?php echo $exam_id;?>'>
                            <input type='hidden' name='student_class' value='<?php echo $student_class;?>'>
                            <?php
										$res = get_all('student', '*', array('student_class'=>$student_class, 'student_section'=>$student_section,'status' =>'ACTIVE'),'student_roll');
										
									    foreach($res['data'] as $row)
										{
										    $sid= $row['id'];
										    $student_admission = $row['student_admission'];
										    
									        $marks  = co_marks($student_admission,$exam_id);
									        
								        echo "<td>
								            <input type='hidden'  name='student_id[]'  value='$sid' >
								            <input type='hidden'  name='student_admission[]'  value='$student_admission'>
								            $student_admission
								            </td>";
							
									
									    echo "<td>".$row['student_roll']."</td>";
										echo "<td>".$row['student_name']."</td>";

										foreach((array)$subject_list as $subject)
										{
										$name =$subject['subject_column'].'[]';
									    echo "<td><select name='{$name}'>";
									    dropdown($grade_list, $marks[$subject['subject_column']]);
									    echo "</select></td>";
										}

									    echo "<td><input type='text'  name='attendance[]'  value='{$marks['attendance']}' placeholder ='88/117' style='width:80px'> </td>";
									 
									    echo "<td><select name='remarks[]' >";
									    dropdown($remarks_list, $marks['remarks']);
									    echo "</select></td>";
                                	
										echo "</tr> ";
										}	
									?>

                            <tr>
                                <th colspan='8'>
                                    <center>

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
function selsub(sel) {
    console.log(sel);
    $.ajax({
        type: 'post',
        data: {
            'class_name': sel
        },
        url: 'master_process?task=select_subject',
        success: function(data) {
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