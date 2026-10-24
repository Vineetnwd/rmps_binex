<?php 
require_once("system/function.php");
	$_REQUEST =post_clean($_REQUEST);
	//print_r($_REQUEST);
	$branch_info =get_data('branch_list', $_SESSION['branch_id'])['data'];
	extract($_REQUEST);
    if($student_admission <>'')
    {
        $sql = "select * from student where finance_type='NORMAL' and status ='ACTIVE' and branch_id ='{$_SESSION['branch_id']}' and student_admission in ($student_admission) order by student_roll asc";
    }
    else{
    	$sql = "select * from student where student_class ='$student_class'  and branch_id ='{$_SESSION['branch_id']}' and student_section ='$student_section' and status ='ACTIVE' order by student_roll asc";
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
td{font-size:10px;padding:2px;font-family:calibri,arial;font-weight:600;}
th{font-size:12px;padding:2px;font-family:calibri,arial;font-weight:800;}

/*.DivIdToPrint{*/
/*     margin:8px;*/
/*     width:49%;*/
/*}*/


/*.idcard{width:750px; height:550px; background:url('assets/img/idcardpng') no-repeat; text-align:center; float:left; margin:6px; page-break-after:always;position:relative;}*/
/*.photo{position:absolute;margin:auto;top:10px;left:10px;margin-right:50px;z-index:0;border:solid 0px #ddd;border-radius:100%;}*/
/*.qr{position:absolute;width:55px; height:55px;margin:auto;top:255px;right:18px;z-index:0;border:solid 0px #000;border-radius:5px;}*/

@media print {
  #printbtn {
    display: none;
  }
  @page {size:portrait}
   .DivIdToPrint{
        page-break-inside: avoid;
        padding:6px;
        width:48%;
        margin:auto;
        margin-right:12px;
        margin-bottom:25px;
    }
}

</style>

<title> <?php echo "Admit Card of $student_class" ; ?> </title>
<?php foreach($student_list as $sid) { 

   $student = get_data("student",$sid)['data'];
   extract($student);
   ?>
 <table border='1' class='DivIdToPrint mt-4' cellpadding='0' cellspacing='0' rules='all' id='DivIdToPrint' align='left'>
	
	<tr height='85px'>
		
			<td align='center' class='header'> 
			<img src='assets/images/logo.png' height='60px' align='left'>
			<span style='font-size:30px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'> <?php echo $full_name; ?> </span><br>
			<b>(Affiliated to CBSE, New Delhi upto 10+2) <br>
			<b>Affiliation No. : <?php echo $aff_no;?>  School No. :  <?php echo $school_code;?></b><br>
			<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> |
			Contact : <?php echo $inst_contact; ?>
			<br>
			Email : <?php echo $inst_email; ?> | Website : <?php echo $inst_url; ?>
			</td>
		
	</tr>
	<tr height='30px' bgcolor='#d5d5d5'>
		<td><center><b> <?php 
		$student_class = get_data("student",$sid,'student_class')['data'];
// 		echo $exam_name = get_all('admit_card', '*', array('student_class'=> $student_class))['data'][0]['exam_name']. " ". date('Y');
		//echo $exam_name = $_POST['exam_name']. " ". date('Y');
		echo get_data('exam_list',$exam_id,'exam_name')['data']. " - ". date('Y'); 
		
		?> (HALL TICKET)</b> </center></td>
	</tr>
	<tr>
	    <td style='text-align:left;padding:px;vertical-align:top;'>
	
            
            <table class='table' width='100%' cellpadding='0' border='0' rules='all' >		
            <tr><td width='120px'>Admission No.: </td><td colspan='2'><?php echo get_data("student",$sid,'student_admission')['data']; ?> </td>
                <td rowspan='5' >
            	<img src='upload/<?php echo $student_photo ?>' align='right' alt='Student Photo Here' style='border : solid 1px #ddd' width='80px' height='100px'/>
            	</td>
            </tr>
            
            <tr><td>Class & Section : </td><td colspan='2'><?php echo $student_class ?> <?php echo $student_section ?> </td></tr>
            
            <tr><td>Roll No. : </td><td colspan='2'><?php echo $student_roll ?> </td></tr>
            
            <tr><td>Name : </td><td colspan='2'><?php echo $student_name ?> </td></tr>
            
            <tr><td>Father's Name :</td><td colspan='2'> <?php echo $student_father ?> </td></tr>
            
           <!--<tr><td> Address : </td><td colspan='2'><?php echo $student_address1 ?> </td></tr>-->

                <tr class='bg-dark text-light text-center'>
                    <th colspan='4'> Exam Time Table For Class - <?php echo $student_class; ?> </th>
                </tr>
                <tr align='center' >
                    <th> Date </th>
                    <th width='35%'> Subject </th>
                    <th> Start Time </th>
                    <th> End Time </th>
                   
                </tr>
          
            <?php
            
            $sql1 = "select distinct(exam_date) as edate from admit_card where exam_id='$exam_id' and student_class ='$student_class' and exam_date!='0000-00-00' and exam_date >= '$today' order by exam_date "; 
            $res = direct_sql($sql1)['data'];
            
            
            foreach($res as $row1)
            {
                $edate = $row1['edate'];
                echo "<tr><td>" . date('d-M-Y', strtotime($edate)). "</td><td colspan='3'> <table width='100%' rules='none' border='0'>";
                
                $sql = "select * from admit_card where student_class ='$student_class' and exam_date ='$edate' order by start_time ";
                $routine = direct_sql($sql)['data'];
            
          //  $routine = get_all('admit_card', '*', array('student_class'=> $student_class),'')['data'];
                foreach($routine as $row)
                {
                    if($row['subject_id']!=''){
                        $subject = get_data('subject', $row['subject_id'],'subject_name')['data'];
                    }else{
                        $subject = strtoupper($row['subject_name']);
                    }
                    if($subject!=''){
                        echo "<tr>";
                        echo "<td width='55%'>" .$subject ."</td>";
                        echo "<td>" . date('h:i A',strtotime($row['start_time'])) ."</td>";
                        echo "<td>" . date('h:i A',strtotime($row['end_time'])) ."</td>";
                        echo "</tr>";
                    }    
                }
                
                echo "</td></table></tr>";
            }
	        ?>
	          </table>
	   </td>
	</tr>
		
	
	<tr align= 'left'>
	    <td>
	        <b> Instructions :</b>
	    <!--    <ul>-->
	    <!--    <li>-->
	    <!--        Candidates should note that an authenticated Admit Card is an important document without which the candidate will not be permitted to appear for further selection-->
	    <!--    </li>-->
	    <!--    <li>Cell phones, calculators, watch calculators, alarm clocks, digital watches with built in calculators/ memory or any electronic or smart devices are not be allowed in the exam hall.</li>-->
	    <!--    <li>-->
	            Hall Ticket is mandatory for examinations.
	    <!--    </li>-->
	    <!--    </ul>-->
	    <!--</td>-->
	    
	    <!--<td>-->
	    <!--    <b> Instructions :</b>-->
	    <!--    <ul>-->
	    <!--        <li>Arrive in time to avoid any delay in examination. Report  half an hour before the exam time.</li>-->
	    <!--    <li>-->
	    <!--        Bring your Student ID Card and Exam Hall ticket issued by School Administration. Without hall ticket entry not allowed.  -->
	    <!--    </li>-->
	    <!--    <li>Do not bring any unauthorised material (e.g. electronics gadgets, written notes, notes in dictionaries, paper, and sticky tape eraser). Pencil cases and glasses cases must not be taken to your desks. These will be checked and confiscated. </li>-->
	    <!--    <li>-->
	    <!--        Read the questions carefully before start writing. All the best for your exam.-->
	    <!--    </li>-->
	    <!--    </ul>-->
	    <!--</td>-->
	<!--</tr>-->
	
	<tr>
		<td valign='bottom'>
		   
			 <div style='float:right;bottom:10px;margin-right:15px;'>  
			 <img src ='upload/<?= $branch_info['exam_controller_sign'] ?>' align='right' height='25px'> <br>
			 Controller of Examination 
			 </div>
		</td>
	</tr>
</table>

<?php } ?>
			