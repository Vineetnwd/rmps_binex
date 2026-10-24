<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

if($user_type !='ADMIN' and $user_type !='DEV')
{
    echo "<script> window.location='dashboard' </script>";
}
$from_date  = $_REQUEST['from_date'] ??$today;
$to_date  = $_REQUEST['to_date'] ??$today;
?>
<script> 
    document.title ="Discount Report  ";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1>Discount Report </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#report">Report</a></li>
        <li class="breadcrumb-item active">Discount Report </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
						<h3 class="box-title"> Discount Report </h3>
						<div class="box-tools pull-right">
							<form method="post">
							    <input type ='date' class='from-control' name='from_date' value='<?= $from_date ?>'>
							    <input type ='date' class='from-control' name='to_date' value='<?= $to_date ?>'>
								<button class='btn btn-orange'> Generate </button>
							</form>
						</div>
					</div>
        <!-- /.box-header -->
	    <div class="box-body">
		 			
			                <div class="table-responsive">
                              <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead >
                                        <tr class='op-thead'>
                                            <th>Txn Date </th>
                                            <th>Receipt No. </th>
                                            <th>Cashier </th>
                                            <th>Admission No </th>
                                            <th>Student Name </th>
                                            <th>Class-Section </th>
                                            <th>Total Amount </th>
                                            <th>Paid Amount </th>
                                            <th>Discount </th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									

                                   $sql="SELECT * 
FROM 
    receipt
WHERE
    paid_date  between '$from_date' and '$to_date'
AND discount >0 
ORDER BY 
    paid_date DESC";

$fees  = direct_sql($sql);
$tdiscount =0;
foreach((array)$fees['data'] as $fee)
{
    $branch_name =get_data('branch_list', $fee['branch_id'],'inst_name')['data'];
    $cashier =get_data('user', $fee['created_by'],'user_name')['data'];
    $student =get_data('student', $fee['student_id'])['data'];
    $tdiscount +=$fee['discount'];
    echo "<tr>";
    echo "<td>". date('d-M-Y', strtotime($fee['paid_date'])). "</td>";
    echo "<td>" . $fee['id']. "</td>";
    echo "<td>" . $cashier. "</td>";
    echo "<td>" . $student['student_admission']. "</td>";
    echo "<td>" . $student['student_name']. "</td>";
    echo "<td>" . $student['student_class']."-". $student['student_section']. "</td>";
    echo "<td>" . $fee['total']. "</td>";
    echo "<td>" . $fee['paid_amount']. "</td>";
    echo "<td>" . $fee['discount']. "</td>";
    echo "</tr>";

}
									?>
                                   	</tbody>
                                   	<tfoot>
                                   	    <tr class="bg-secondary text-light">
                                   	        <td colspan="8"> Total discount</td>
                                   	        <td colspan="8" > <?= $tdiscount ?> </td>
                                   	    </tr>
                                   	</tfoot>
                                   
                                </table>
					
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       