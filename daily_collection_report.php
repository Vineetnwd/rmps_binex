<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
    $str='';
    if(isset($_GET['txn_mode']) and $_GET['txn_mode'] !=''){
        
        $pmode =$_GET['txn_mode'];
        
        $str= "and payment_mode ='$pmode'";
    }
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

$date_limit = date('Y-m-d',strtotime('-7 days', strtotime($today))); // Subtract 7 days from today

?>
<script> 
    document.title ="Collection Report of <?php echo $_POST['from_date']; ?> to <?php echo $_POST['to_date']; ?>";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Day Wise Collection Report</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Fee</a></li>
        <li class="breadcrumb-item active">Day Wise Collection Report</li>
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
			
				
				<div class="col-lg-4 text-right">
				
				<?php if($user_type !='ADMIN'){ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date' min='<?= $date_limit;?>' max='<?= $today ?>'>
				<?php }else{ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date' placeholder='from date' max='<?= $today ?>' >
				<?php } ?>
				</div>
				<div class="col-lg-4 text-center">
				
					<input type='date' value='<?php echo $to_date;?>' name='to_date' max='<?= $today ?>' >
				</div>
				
			
				<div class="col-lg-4">
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
                                      
                                        <tr>
                                            
                                            <th  class='bg-dark text-light'>Invoice date</th>
                                            <th class='bg-success text-light'>Cash</th>
                                            <th class='bg-success text-light'>Bank</th>
                                            <th class='bg-success text-light'>Online</th>
										    <th class='bg-success text-light'>Total</th>
										    <th  class='bg-danger text-light'>Cash</th>
                                            <th  class='bg-danger text-light'>Bank</th>
                                            <th  class='bg-danger text-light'>Total</th>
										    <th  class='bg-dark text-light'>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$total=0;
									$cash =0;
									$bank=0;
									$online=0;
									
									$e_total=0;
									$e_cash =0;
									$e_bank=0;
								
									$query ="select paid_date from receipt where paid_date between '$fromdate' and '$todate' and status ='PAID' group by paid_date order by paid_date";
								
								
								
									$res = mysqli_query( $con,$query) or die(" Default Error : ".mysqli_error($con));
									while($row =mysqli_fetch_array($res))
									{
									$rowtotal=0;
									$pdate =$row['paid_date'];
									$cash +=  all_income($pdate, $pdate , 'CASH')['total'];
									$bank +=  all_income($pdate, $pdate , 'BANK')['total'];
									$online +=  all_income($pdate, $pdate , 'ONLINE')['total'];
									$rowtotal = all_income($pdate, $pdate , 'CASH')['total'] + all_income($pdate, $pdate , 'BANK')['total'] + all_income($pdate, $pdate , 'ONLINE')['total'];
									
									$e_cash +=  all_exp($pdate, $pdate , 'CASH')['total'];
									$e_bank +=  all_exp($pdate, $pdate , 'BANK')['total'];
									
									$e_rowtotal = all_exp($pdate, $pdate , 'CASH')['total'] + all_exp($pdate, $pdate , 'BANK')['total'];
									
									
									$total +=$rowtotal;
									$e_total +=$e_rowtotal;
									$balance = $rowtotal -$e_rowtotal;
									//print_r(all_income($pdate, $pdate , 'CASH'));
								            echo "<tr>";
								            echo "<td align='left'>" . $pdate. "</td>";
								            echo "<td align='right'>" .all_income($pdate, $pdate , 'CASH')['total']. "</td>";
								            echo "<td align='right'>" .all_income($pdate, $pdate , 'BANK')['total']. "</td>";
								            echo "<td align='right'>" .all_income($pdate, $pdate , 'ONLINE')['total']. "</td>";
								            echo "<td align='right'>" .$rowtotal. "</td>";
								             echo "<td align='right'>" .all_exp($pdate, $pdate , 'CASH')['total']. "</td>";
								            echo "<td align='right'>" .all_exp($pdate, $pdate , 'BANK')['total']. "</td>";
								            echo "<td align='right'>" .$e_rowtotal. "</td>";
								            echo "<td align='right'>" .$balance. "</td>";
											echo "</tr>";
									}
								
								    
									
									?>
                                   	</tbody>
                                   	<tfoot>
										<tr bgcolor='#f5f5f5'> 
										    <th  class='bg-dark text-light'> TOTAL </th>
										    
										    <th align='right' class='bg-success text-light'><?php echo $cash ; ?> </th>
										   
										    <th align='right' class='bg-success text-light'><?php echo $bank ; ?></th>
										   
										    <th align='right' class='bg-success text-light'><?php echo $online ; ?></th>
										   
										
											<th align='right' class='bg-success text-light'><b> <?php echo $total; ?> </b></th>
											
											 <th align='right' class='bg-danger text-light'><?php echo $e_cash ; ?> </th>
										   
										    <th align='right' class='bg-danger text-light'><?php echo $e_bank ; ?></th>
										   
										    <th align='right'  class='bg-danger text-light'><?php echo $e_total; ?></th>
									         <th align='right' class='bg-dark text-light'><?php echo $total-$e_total; ?></th>
										</tr> 
									</tfoot>
                                </table>
								<?php } ?>
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       