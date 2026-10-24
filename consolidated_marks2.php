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
        <h1> Consolidated Marks </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#exam">Exam</a></li>
            <li class="breadcrumb-item active">Consolidated Marks</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Consolidated Marks Sheet</h3>
                <div class="box-tools pull-right">
                    <i class='fa fa-file-excel-o btn btn-orange text-light btn-sm' onclick='exportxls()'
                        title='Export to Excel'> </i>

                </div>
            </div>
            <div class="box-body">
                <form action='' method='post'>
                    <div class='row'>

                        <div class="col-lg-3 col-offset-lg-2">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" name='student_class' required
                                    onChange="selsub(this.value)">
                                    <?php dropdown($class_list,$student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">

                            <div class="form-group">
                                <label>Select Section</label>
                                <select class="form-control" name='student_section' required>
                                    <?php dropdown($section_list,$student_section); ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-lg-3">

                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name='exam_id' required>
                                    <?= dropdown_list('exam_list','id','exam_name', @$exam_id); ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-lg-3">
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
						//extract($_POST);
						 $subject_list =subject_list($student_class);
						 $sl =5;
						 $cols = 3+(count($subject_list)*$sl);
						?>

                        </div>
                        <div class='table-responsive'>
                            <table id="data_tbl" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <td colspan='<?php echo $cols;?>' align='center'>
                                            <b> <?php echo $exam_name ." ".$current_session; ?></b><br>
                                            <?php echo "Marks Details of ".$student_class. "-". $student_section; ?>
                                        </td>
                                    </tr>

                                    <tr class='op-thead'>
                                        <th rowspan='2' valign='middle'> Roll No. </th>
                                        <th rowspan='2'> Name </th>
                                        <?php foreach($subject_list as $subject_id)
										{
											$ct = count($marks_type_list)+1;
                                            
											echo "<th colspan='$ct'><center>". get_data('subject',$subject_id,'subject_name')['data']."</center></th>";
										}
										?>

                                        <th rowspan='2'> Grand Total </th>
                                    </tr>
                                    <tr class='bg-yellow text-dark'>
                                        <?php foreach($subject_list as $subject_id)
										{
                                          	foreach($marks_type_list as $mstype)
											{
                                                $max = max_marks($exam_id,$subject_id,$mstype); 
												echo "<th>". $mstype. "(". $subject_id .")</th>";
											}
											echo "<th> Total </th>";
										} 
										?>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
	
		$sql = "SELECT exam.*, student.id as student_id, student.student_admission, student.student_name, student.student_class,student.student_section, student.student_roll FROM exam, student WHERE student.student_admission = exam.student_admission and student.status ='ACTIVE' and student.student_class='$student_class' and student.student_section='$student_section' and exam_id ='$exam_id' order by student.student_roll;";
		$subject =remove_space($subject);
		$res = direct_sql($sql, 'get', false);
		foreach((array)$res['data'] as $row)
		{
		$total=0;
		
		$id = $row['id'];
		$student_id = $row['student_id'];
		$student_admission = $row['student_admission'];
		//print_r($marks);
		
		
		echo "<tr class='default'>";
		
		echo "<td>".$row['student_roll'] ."</td>";
		echo "<td>". $row['student_name'] ."</td>";
		foreach($subject_list as $subject_id)
			{
			$subject =get_data('subject',$subject_id,'subject_column')['data'];
			$subtotal=0;
			$marks = get_marks($student_admission,$exam_id, $subject);
			$total =$total + $marks['total'];
            
			foreach($marks_type_list as $mstype)
			{
                
				echo "<td>".$marks[remove_space($mstype)]. "</td>";
			}
			
			echo "<td bgcolor='lightgreen'>".$marks['total']."</td>";
			}
			
			echo "<td>" .$total ."</td>";
		echo "</tr> ";
		}
	?>
                                    </tr>
                                </tbody>
                            </table>
                            <?php } ?>

                        </div>
                    </div>


                </div>
            </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>