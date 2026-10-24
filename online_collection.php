<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
    $str= "and payment_mode ='ONLINE'";
    
    if(isset($_REQUEST['from_date']))
{
    $from_date = $_REQUEST['from_date'];
}
else{
    $from_date = $today;
}

if(isset($_REQUEST['to_date']))
{
    $to_date = $_REQUEST['to_date'];
}
else{
    $to_date = $today;
}

if($user_type=='DBA')
{
    echo "<script> window.location='dashboard.php' <script>";
}
?>
								

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Online Collection Report</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Fee</a></li>
        <li class="breadcrumb-item active">Online Collection</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <!-- <h3 class="box-title">Add and Update Transport Area </h3>-->
		  <form method='post'>
			<div class='row'>
			
				
				<div class="col-lg-3 text-right">
				
				<?php if($user_type !='Admin'){ ?>
					<input type='date' value='<?php $from_date;?>' name='from_date' min ='<?php //echo date('Y-m-d'); ?>'>
				<?php }else{ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date' placehplder='from date'>
				<?php } ?>
				</div>
				<div class="col-lg-3 text-center">
				
					<input type='date' value='<?php echo $to_date;?>' name='to_date' min ='<?php echo date('Y-m-d', strtotime('-6 months')); ?>'>
				</div>
				<div class="col-lg-3">
					<!--<label>Receipt Type</label>-->
					<select name='status' >
						<!--<option value='SUCCESS'> SUCCESS</option>-->
						<!--<option value='ACTIVE'> PENDING</option>-->
						<!--<option value='FAILED'> FAILED</option>-->
						<?php dropdown_with_key($payment_status_list, $_POST['status']); ?>
					</select>
				</div>
				
				<div class="col-lg-3">
						<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
				</div>
			
			</div>
			</form>
        </div>
        <!-- /.box-header -->
	    <div class="box-body">
		 		<?php 
		 		
		 		if(isset($_POST['submit']) && isset($_POST['from_date']) )
									{
						$fromdate =$_POST['from_date'];
						$todate =$_POST['to_date'];
						$status =$_POST['status'];
					
				?>					
			          
				<div class="table-responsive">
				  <table id="example" class="table table-bordered table-striped">
                                    <thead >
                                        <tr>
                                            <th>#</th>
                                            <th>Receipt Id</th>
                                            <th>Adm No.</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Roll No.</th>
                                            <th>Paid Date</th>
                                            <th>Mode</th>
											<th width='80px'>Payment For</th>
                                            <th>Paid Amount</th>
                                            <th>Txn Fee</th>
                                            <th>Tax</th>
                                            <th>Order Id</th>
                                            <th>Txn Id</th>
                                            <th>Action</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$ct=1;
									$total=0;
								    $all_discount=0;
									
									$query ="select * from online_payment where txn_date between '$fromdate' and '$todate' and status ='$status' $str order by txn_date desc";
									$res = mysqli_query( $con,$query) or die(" Default Error : ".mysqli_error($con));
									while($row =mysqli_fetch_array($res))
									{
									$rid = $row['receipt_id'];
									$sid =$row['student_id'];
									$student =get_data('student',$sid)['data'];
									$paid_month =remove_space($row['paid_month']);
									$oid = $row['order_id'];
									$total =$total +$row['total_amount'];
								
									$all_discount =$all_discount +$row['discount'];
										echo"<tr class='odd gradeX'>";
										echo "<td> ". $ct ."</td>";
										echo "<td> 
											<a href='receipt.php?receipt_id=$rid' title='Monthly Receipt' target='_blank'>". $rid."</a> </td>";
									
										echo "<td> ". $student['student_admission'] ."</td>";
										echo "<td> ".$student['student_name']."</td>";
										echo "<td> ". $student['student_class'] ."-". $student['student_section'] ."</td>";
										echo "<td> ".$student['student_roll'] ."</td>";
										echo "<td> ". $row['txn_date']."</td>";
											
										echo "<td align='right'> ". $row['payment_mode'] ."</td>";
										echo "<td> ". ucwords(str_replace(',', ', ',$row['payment_month'])) ."</td>";
										echo "<td> ". $row['payment_amount'] ."</td>";
										echo "<td> ". $row['txn_fee'] ."</td>";
										echo "<td> ". $row['txn_tax'] ."</td>";
										echo "<td align='right'><a href='pay/recheck.php?order_id=$oid' target='blank' class='badge badge-success btn-sm' title='ReCheck Transaction'>  ". $row['order_id'] . "</a>"."</td>";
										echo "<td align='right'> ". $row['txn_id'] ."</td>";
										
										
										if($status =='CANCEL')
                                            {
                                            echo "<td>".get_data('user',$row['cancel_by'],'user_name')['data']."</td>
                                            <td>".$row['cancel_at']."</td>
                                            <td>" .$row['cancel_remarks']."</td>";
                                            } else{
                                        ?>
										<td width='80px'>
										<button data-id='<?php echo $rid; ?>' Value='Cancel' title='Cancel Receipt' class='cancelreceipt btn btn-danger btn-sm'>
									<i class="fa fa-times-circle-o" aria-hidden="true"></i>
									</button>
										
										<?php
										echo btn_view('receipt', $rid,$student['student_name'] ); 
                                        }
                                       
										echo "</td></tr>";
										$ct++;
									}
								
								    
									
									?>
                                   	</tbody>
        
                                </table>
								
                            </div>
                            <?php } ?>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       