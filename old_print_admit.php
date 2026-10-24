<?php 
require_once("system/function.php");
	$_REQUEST =post_clean($_REQUEST);
	//print_r($_REQUEST);
	$branch_info =get_data('branch_list', $_SESSION['branch_id'])['data'];
	extract($_REQUEST);
    if($student_admission <>'')
    {
        $sql = "select * from student where finance_type='NORMAL' and status ='ACTIVE' and branch_id ='{$_SESSION['branch_id']}' and student_admission in ($student_admission)";
    }
    else{
    	$sql = "select * from student where student_class ='$student_class'  and branch_id ='{$_SESSION['branch_id']}' and student_section ='$student_section' and status ='ACTIVE'";
    }
    $res = direct_sql($sql)['data'];
	foreach($res as $row)
	{
		$student_list[] = $row['id'];
	}
?>


<style>
body{
	color:#000;
	padding:0px;
	margin:0px;
	font-family:calibri;
}
td{
    font-size:12px;
}
.DivIdToPrint{
     margin:6px;
}

@media print {
  #printbtn {
    display: none;
  }
  @page {size:landscape}
   #DivIdToPrint{
        page-break-inside: avoid;
        margin:8px;
        padding:6px;
        width:48%;
      
        float:right;
    }
}

</style>

<title> <?php echo "Admit Card of $student_class" ; ?> </title>
<?php foreach($student_list as $sid) { 

   $student = get_data("student",$sid)['data'];
   $student_class =$student['student_class'];
   extract($student);
   ?>
 <table border='0' class='DivIdToPrint mt-4' cellpadding='0' rules='all' id='DivIdToPrint' align='center'>
	
	<tr height='85px'>
		
			<td align='center' class='header'> 
			<img src='assets/images/logo.png' height='80px' align='left'>
			<span style='font-size:30px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'> <?php echo $branch_info['full_name']; ?> </span><br>
			<?php echo $branch_info['inst_address1']; ?>, <?php echo $branch_info['inst_address2']; ?> |
			<br>
			Contact : <?php echo $branch_info['inst_contact']; ?> | Website : <?php echo $branch_info['inst_url']; ?>
			</td>
		
	</tr>
	<tr height='30px' bgcolor='#d5d5d5'>
		<td><center><b> <?= get_data('exam_list',$exam_id,'exam_name')['data']. " - ". date('Y'); ?> (HALL TICKET)</b> </center></td>
	</tr>
	<tr>
	    <td style='text-align:left;padding:10px;vertical-align:top;'>
	
            
            <table class='table' width='100%' cellpadding='2' border='1' rules='rows' >		
            <tr><td>Admission No.: </td>
                <td><?= $student['student_admission']; ?> </td>
                <td rowspan='6' colspan='2' align='right'>
            	<img src='upload/<?= $student['student_photo']; ?>' alt='Student Photo Here' style='border : solid 1px #ddd;margin-left:50px;' width='120px' height='150px'/>
            	</td>
            </tr>
            
            <tr><td>Class & Section : </td><td><?= $student['student_class']. $student['student_section']; ?></td></tr>
            
            <tr><td>Roll No. : </td><td><?= $student['student_roll']; ?></td></tr>
            
            <tr><td>Name : </td><td><?= $student['student_name']; ?> </td></tr>
            
            <tr><td>Father's Name :</td><td> <?= $student['student_father']; ?> </td></tr>
            
           <tr><td> Address : </td><td><?= $student['student_address1']; ?> </td></tr>
</table>
<table class='table' width='100%' cellpadding='2' border='0' rules='rows' >		
                <tr class='bg-dark text-light text-center'>
                    <th colspan='6'> Exam Time Table for Class <?php echo $student_class; ?> </th>
                </tr>
              
                
                <tr>
                    <th> Exam Date </th>
                    <th> Subject Name </th>
                    <th> In Time  </th>
                    <th> Out Time  </th>
                    <th> Full Marks  </th>
                    <th> Pass Marks  </th>
                </tr>
          
            <?php
            
            $sql1 = "select distinct(exam_date) as edate from admit_card where exam_id='$exam_id' and student_class ='$student_class' and exam_date >= '$today' order by exam_date "; 
            $res = direct_sql($sql1)['data'];
            
            
            foreach($res as $row1)
            {
                $edate = $row1['edate'];
                echo "<tr><td>" . date('d-M-Y', strtotime($edate)). "</td><td colspan='5'>";
                echo "<table width='100%' rules='none' border='0'>";
                
                $sql = "select * from admit_card where exam_id = '$exam_id' and student_class ='$student_class' and exam_date ='$edate' order by start_time ";
                
                $routine = direct_sql($sql)['data'];
            
        
              
                foreach($routine as $row)
                {
                   // $subject = $row['subject_id'];
                    $subject = get_data('subject', $row['subject_id'],'subject_name')['data'];
                    echo "<tr>";
                    echo "<td width='160px'>" .$subject ."</td>";
                    echo "<td width='80px'>" .date('h:i A',strtotime($row['start_time'])) ."</tdwidth=>";
                    echo "<td width='100px'>" .date('h:i A',strtotime($row['end_time'])) ."</td>";
                    echo "<td width='100px'>" .$row['marks_total'] ."</td>";
                    echo "<td>" .$row['marks_passing'] ."</td>";
                    echo "</tr>";
                    
                }
                echo "</td></table></tr>";
            }
	        ?>
	          </table>
	   
		</td>
	</tr>
	
	<tr>
	   
	    <td>
	        <b> Instructions :</b>
	        <ul>
	        
	        <li>	Check the examination schedule carefully.</li>
            <li>	Report in time to examination hall,  to avoid any delay in examination. Reporting time is half an hour before the exam time.</li>
            <li>	Bring your Student ID Card and Exam Hall ticket issued by School Administration. Without Hall ticket entry may be restricted.</li>
            <li>	Do not bring any unauthorized material (e.g. electronic gadgets, written notes, dictionaries, and sticky notes).</li>
            <li>	Read the questions carefully before start writing. </li>


	        </ul>
	    </td>
	</tr>
	<tr>
		<td valign='bottom' height='80px'>
		   
			 <div style='float:right;bottom:10px;margin-right:15px;'> 
			 <img src ='upload/<?= $branch_info['exam_controller_sign'] ?>' align='right' height='40px'> <br>
			 Examination in Charge 
			 </div>
		</td>
	</tr>
</table>

<?php } ?>
			