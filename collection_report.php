<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
    $str='';
    if(isset($_GET['txn_mode']) and $_GET['txn_mode'] !=''){
        
        $pmode =$_GET['txn_mode'];
        
        $str= "and payment_mode ='$pmode'";
    }
$from_date = isset($_REQUEST['from_date']) ?$_REQUEST['from_date']:$today;
$to_date = isset($_REQUEST['to_date']) ?$_REQUEST['to_date']:$today;
   
$date_limit = date('Y-m-d',strtotime('-31 days', strtotime($today))); // Subtract 7 days from today

if($user_type=='DBA')
{
    echo "<script> window.location='dashboard' </script>";
}

?>
<script> 
    document.title ="Collection Report of <?php echo $_POST['from_date']; ?> to <?php echo $_POST['to_date']; ?>";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Collection Report</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Fee</a></li>
        <li class="breadcrumb-item active">Collection Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     <div class="row">
	        <?php 
	         
	        echo simple_widget (total_collection($from_date, $to_date), 'Total', 'fa fa-inr' , 'warning');
	        echo simple_widget (total_collection($from_date, $to_date, 'Cash'), 'Cash', 'fa fa-credit-card' , 'success');
	        echo simple_widget (total_collection($from_date, $to_date, 'Bank'), 'Bank', 'fa fa-bank' , 'info');
	        echo simple_widget (total_collection($from_date, $to_date,'CANCEL'), 'CANCEL', 'fa fa-close' , 'danger');
	       
	        ?>
			
		</div>
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <!-- <h3 class="box-title">Add and Update Transport Area </h3>-->
		  <form method='post'>
			<div class='row'>
			
				
				<div class="col-lg-3 text-right">
				
				<?php if($user_type =='ACCOUNT'){ ?>
				    	<input type='date' value='<?php echo $from_date;?>' name='from_date'  placeholder='from date' max='<?= $today ?>'>
				<?php }else{ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date'  ' max='<?= $today ?>'>
					
				<?php } ?>
				</div>
				<div class="col-lg-3 text-center">
				
					<input type='date' value='<?php echo $to_date;?>' name='to_date' max='<?= $today ?>' >
				</div>
				<div class="col-lg-2">
					<!--<label>Receipt Type</label>-->
					<select name='status' >
						<option value='PAID'> APPROVED</option>
						<option value='CANCEL'> CANCELED</option>
					</select>
				</div>
					<div class="col-lg-2">
					<!--<label>Receipt Type</label>-->
					<select name='collected_by' >
					    <option value=''> Collected By</option>
					    <?php dropdown_list('user','id','user_name',@$_POST['collected_by']); ?>
					</select>
				</div>
				<div class="col-lg-2">
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
						if(isset($_POST['collected_by']) and $_POST['collected_by'] !='')
						{
						$collected_by =$_POST['collected_by'];
						}
				?>					
			                <div class="table-responsive">
                              <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead >
                                        <tr class='op-thead'>
                                            <th>Receipt Id</th>
                                            <th>Adm No.</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Roll No.</th>
                                            <th>Invoice date</th>
                                            <th>Paid date</th>
                                            <th>Mode</th>
											<th width='80px'>Payment For</th>
                                            <th>Total</th>
                                            <th>Discount</th>
                                            <th>Paid Amount</th>
                                            <th>Received By</th>
                                            <?php if($status =='CANCEL')
                                            {
                                            echo "<th>Cancel By</th>
                                            <th>Cancel At</th>
                                            <th>Cancel Remarks</th>";
                                            }else{
                                            echo "<th>#</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$total=0;
									$cash =0;
									$bank=0;
									$online=0;
									$all_discount=0;
									if(trim($collected_by) !='')
									{
									$query ="select * from receipt where paid_date between '$from_date' and '$to_date' and status ='$status' $str and created_by ='$collected_by' order by id desc";
									}
									else{
									$query ="select * from receipt where paid_date between '$from_date' and '$to_date' and status ='$status' $str order by paid_date desc";
									}
									//echo $query;
									$res =direct_sql($query);
									
									foreach($res['data'] as $row)
									{
									$rid = $row['id'];
									$sid =$row['student_id'];
									$student =get_data('student',$sid)['data'];
									$paid_month =remove_space($row['paid_month']);
									
									$total =$total +$row['paid_amount'];
									if( $row['payment_mode'] =='CASH')
									{
									    $cash =$cash + $row['paid_amount'];
									}
										if( $row['payment_mode'] =='BANK')
									{
									    $bank =$bank + $row['paid_amount'];
									}
										if( $row['payment_mode'] =='ONLINE')
									{
									    $online =$online + $row['paid_amount'];
									}
									$all_discount =$all_discount +$row['discount'];
											echo"<tr class='odd gradeX'>";
								// 		if($paid_month=='annual_fee'){
								// 			echo "<td> 
								// 			<a href='annual_receipt?receipt_id=$rid' title='Annual Receipt' target='_blank' class='>". $rid."</a> </td>";
								// 		}
								// 		else{
										
								// 		}
										
										echo "<td> 
											<a href='receipt?receipt_id=$rid' title='Monthly Receipt' target='_blank' class='badge bg-primary'>". $rid."</a> </td>";
										echo "<td> ". $student['student_admission'] ."</td>";
											echo "<td> ".$student['student_name']."</td>";
											echo "<td> ". $student['student_class'] ."-". $student['student_section'] ."</td>";
											echo "<td> ".$student['student_roll'] ."</td>";
								
											echo "<td> ". $row['paid_date']."</td>";
										    //echo "<td> ". date('d-M-y',strtotime($row['updated_at'])) ."</td>";
										    echo "<td> ". date('Y-m-d h:i',strtotime($row['updated_at'])) ."</td>";
											echo "<td align='right'> ". show_status($row['payment_mode']) ."</td>";
											echo "<td> ". ucwords(str_replace(',', ', ',$row['paid_month'])) ."</td>";
											echo "<td> ". $row['total'] ."</td>";
											echo "<td align='right'> ". $row['discount'] ."</td>";
											echo "<td align='right'> ". $row['paid_amount'] ."</td>";
											echo "<td align='right'> ". get_data('user',$row['created_by'],'user_name')['data']."</td>";
										
										if($status =='CANCEL')
                                            {
                                            echo "<td>".get_data('user',$row['cancel_by'],'user_name')['data']."</td>
                                            <td>".$row['cancel_at']."</td>
                                            <td>" .$row['cancel_remarks']."</td>";
                                            } else{
                                        ?>
										<td width='80px'>
										    <?php if($user_type =='ADMIN' or $user_type =='DEV'){ ?>
										<button data-id='<?php echo $rid; ?>' Value='Cancel' title='Cancel Receipt' class='cancelreceipt btn btn-danger btn-sm'>
									<i class="fa fa-times-circle-o" aria-hidden="true"></i></button>
										<?php }
										else if($row['paid_date']==$today){ ?>
										  	<button data-id='<?php echo $rid; ?>' Value='Cancel' title='Cancel Receipt' class='cancelreceipt btn btn-danger btn-sm'>
									<i class="fa fa-times-circle-o" aria-hidden="true"></i></button> 
									
										<?php } ?>
										<?php
										echo btn_view('receipt', $rid,$student['student_name'] ); 
                                        }
											echo "</td></tr>";
									}
								
								    
									
									?>
                                   	</tbody>
                                   	<tfoot>
										<tr class='bg-gray'> 
										    <th> </th>
										    <th>CASH </th>
										    <th><?php echo $cash ; ?> </th>
										    <th>BANK</th>
										    <th><?php echo $bank ; ?></th>
										    <th>Online</th>
										    <th><?php echo $online ; ?></th>
										    <th></th>
										    <th>Total</th>
										    
									
											
										
											<th align='right'><b> <?php echo $total; ?> </b></th>
											<th align='right'><b> <?php echo $all_discount; ?> </b></th>
										<?php
										if($status =='CANCEL')
										{
										//	echo "<th colspan='5'></th>";
											echo "<th></th><th></th><th></th><th></th><th></th>";
										}
										else{
											//echo "<th colspan='3'></th>";
												echo "<th></th><th></th><th></th>";
										}
										?>	
										</tr> 
									</tfoot>
                                </table>
								<?php } ?>
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       