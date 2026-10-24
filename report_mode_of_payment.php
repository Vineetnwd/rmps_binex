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
    document.title ="Mode Wise Payment Details ";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1>Mode Wise Payment Details</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#report">Report</a></li>
        <li class="breadcrumb-item active">Collection Summary</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
						<h3 class="box-title"> Mode Wise Report </h3>
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
                                            <th>Billed By </th>
                                            <th class='bg-danger text-light'>Total Receipt </th>
                                            <th>Cancel Receipt </th>
                                            <th>Total Amount </th>
                                            <th>Cash Amount </th>
                                            <th>Bank Amount </th>
                                            <th>Cancel Amount </th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									

                                   $sql="SELECT 
    paid_date,
    created_by,
    
    COUNT(*) AS total_receipt_count,
    SUM(CASE WHEN status = 'CANCEL' THEN 1 ELSE 0 END) AS cancel_receipt_count,
    
    SUM(paid_amount) AS Total,
    SUM(CASE WHEN payment_mode = 'CASH' AND status = 'PAID' THEN paid_amount ELSE 0 END) AS Cash,
    SUM(CASE WHEN payment_mode = 'BANK' AND status = 'PAID' THEN paid_amount ELSE 0 END) AS Bank,
    SUM(CASE WHEN status = 'CANCEL' THEN paid_amount ELSE 0 END) AS Cancel_Amount

FROM 
    receipt
WHERE
    paid_date  between '$from_date' and '$to_date'
GROUP BY 
    paid_date, branch_id, created_by
ORDER BY 
    paid_date DESC";

$fees  = direct_sql($sql);
// $gtotal =0;
$ctotal = 0;
$btotal = 0;
foreach((array)$fees['data'] as $fee)
{
    $branch_name =get_data('branch_list', $fee['branch_id'],'inst_name')['data'];
    $cashier =get_data('user', $fee['created_by'],'user_name')['data'];
    // $gtotal +=$fee['Total'];
    $ctotal += $fee['Cash'];
    $btotal =+ $fee['Bank'];
    echo "<tr>";
    echo "<td>". date('d-M-Y', strtotime($fee['paid_date'])). "</td>";
    echo "<td>" . $cashier. "</td>";
    echo "<td>" . $fee['total_receipt_count']. "</td>";
    echo "<td>" . $fee['cancel_receipt_count']. "</td>";
    echo "<td class='bg-danger text-light'>" . $fee['Total']. "</td>";
    echo "<td>" . $fee['Cash']. "</td>";
    echo "<td>" . $fee['Bank']. "</td>";
    echo "<td>" . $fee['Cancel_Amount']. "</td>";
    echo "</tr>";

}
									?>
                                   	</tbody>
                                   
                                </table>
					
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       