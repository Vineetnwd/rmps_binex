<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
 //print_r($_POST);
 extract($_POST);
?>
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Demand List of <?php echo $student_class ." - " . $student_section; ?></h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#transport">Fee</a></li>
        <li class="breadcrumb-item active">Demand List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Demand List</h3>
		  <div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		  </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
		 	
			
                            <div class="table-responsive">
                               <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead class='op-thead'>
	<th> Adm No. </th>
	<th> Class/Sec </th>
	<th> Roll No. </th>
	<th> Name </th>
	<th> Father's Name </th>
	<th> Address </th>
	<th> Mobile No. </th>
	<th> Route </th>
	<th> Till Month</th>

	<th> Prev. Session Dues </th>
	<th> Prev. Dues </th>
	<th> Annual Fee </th>
	<th> Tuition Fee </th>
	<th> Transport Fee </th>
	<th> Hostel Fee </th>
	<th> Total </th>
	<th> Remarks</th>
	</thead>
	<tbody>
<?php
//$all_data = get_all('student','*', array('student_class'=>$student_class,'student_section'=>$student_section, 'status'=>'ACTIVE'));
$student_type = "'" . implode("','", $student_type) . "'";

if($student_admission <>'')
{
    $sql = " select * from student where finance_type='NORMAL' and status ='ACTIVE' and student_admission in ($student_admission) and branch_id ='$branch_id'";
    
    //$sql = " select * from student where student_class ='$student_class' and student_section ='$student_section' and finance_type='NORMAL' and status ='ACTIVE' and student_roll in ($student_roll) and student_type in($student_type) ";
}
else if($student_class =='' and $student_section =='')
{
    $sql = " select * from student where finance_type='NORMAL' and status ='ACTIVE' and branch_id ='$branch_id'";
    
}
else{
     $sql ="select * from student where student_type in($student_type) and  finance_type='NORMAL' and student_class = '$student_class' and student_section ='$student_section'  and  status ='ACTIVE' and branch_id ='{$_SESSION['branch_id']}'";

}


$all_data = direct_sql($sql);

if($all_data['count']>0)
foreach($all_data['data'] as $student)
{
	
$student_id = $student['id'];
$student = get_data('student', $student_id)['data'];
$student_name =$student['student_name'];
$student_class =$student['student_class'];
$student_mobile =$student['student_mobile'];
//$annual_fee = monthly_fee($student_id, 'admission_month')['fee']['total'];
$dues_details = nmonth_fee($student_id, $fee_month);
$prev_dues = get_data('student_fee',$student_id,'current_dues','student_id')['data'];
$prev_session_dues = intval(get_data('student_fee', $student_id, 'prev_session_dues', 'student_id')['data'])??0;
$total = $dues_details['total'] +$prev_dues+$prev_session_dues;
if($student['area_id']!=''){
    $area_name = get_data('transport_area',$student['area_id'],'area_name')['data'];
}else{
    $area_name='-';
}
?>


		<tr class='odd gradeX'>
			
			<td ><?php echo $student['student_admission']; ?></td>
			<td ><?php echo $student['student_class']."-".$student['student_section']; ?></td>
			<td ><?php echo $student['student_roll']; ?></td>
			<td ><?php echo $student['student_name']; ?></td>
			<td ><?php echo $student['student_father']; ?></td>
			<td ><?php echo $student['student_address1']; ?></td>
			<td ><?php echo $student['student_mobile'] .", <br>". $student['father_mobile']; ?></td>
			<td ><?php echo $area_name ?></td>
			<td ><?php echo implode(', ',$fee_month); ?></td>
			<td ><?php echo $prev_session_dues; ?></td>
			<td ><?php echo $prev_dues; ?></td>
			<td ><?php echo get_annual_fee($student_id) ?></td>
			<td> <?php echo $dues_details['tuition_fee']; ?> </td>
			<td> <?php echo $dues_details['transport_fee']; ?> </td>
			<td> <?php echo $dues_details['hostel_fee']; ?> </td>
			<td> <?php echo $total; ?> </td>
			<td> <?php echo $remarks; ?> </td>
			
		
		</tr>
<?php

        if($_POST['send_sms'] =="yes")
        {
            $mlist = implode(',',$fee_month);
            $sms = "Dear Parent Kindly pay Rs. $total as the tuition fee of class $student_class of the month $mlist \nRegards $inst_name OFFAPP";
            $te_id='1207166936506605381';
            send_msg($student_mobile, $sms, $te_id);
        }
	
 }?>
</tbody>
</table>

 </div>
                            
                      
            </div>

</div>
</div>
   <?php require_once('system/footer2.php'); ?>