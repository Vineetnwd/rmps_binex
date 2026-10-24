<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$table_name = 'account_txn';
$to_date = (isset($_REQUEST['to_date']))?$_REQUEST['to_date']:$today;
$from_date = (isset($_REQUEST['from_date']))?$_REQUEST['from_date']:$today;
$sel_account_name = get_data('account_head', $_POST['account_id'],'account_name')['data'];

//$sql0 ="create VIEW IF NOT EXISTS exp_details  AS SELECT account_head.account_type AS account_type, account_txn.id AS id, account_txn.account_id AS account_id, account_txn.txn_date AS txn_date, account_txn.txn_type AS txn_type, account_txn.txn_amount AS txn_amount, account_txn.txn_mode AS txn_mode, account_txn.txn_remarks AS txn_remarks, account_txn.status AS status, account_txn.created_by AS created_by, account_txn.created_at AS created_at, account_txn.updated_by AS updated_by, account_txn.updated_at AS updated_at, account_txn.cancel_at AS cancel_at, account_txn.cancel_by AS cancel_by, account_txn.cancel_remarks AS cancel_remarks FROM (account_txn join account_head) WHERE account_txn.account_id = account_head.id ";
?>

<script> 
   document.title ="<?php echo $inst_name; ?> : Head Wise Report of <?= $_POST['account_type'];?> <?= $sel_account_name ;?> From <?php echo date('d-M-Y',strtotime($_POST['from_date'])); ?> to <?php echo date('d-M-Y',strtotime($_POST['to_date'])); ?> ";
</script>
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Head Wise Expense Details </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Enquiry</a></li>
        <li class="breadcrumb-item active"> Account Txn Report</li>
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
			
				
				<div class="col-lg-2 text-right">
					<!--<label>From Date </label>-->
			
					<input type='date' value='<?php echo $from_date;?>' name='from_date' >
				
				</div>
				<div class="col-lg-2 text-center">
					<!--<label>To Date </label>-->
					<input type='date' value='<?php echo $to_date;?>' name='to_date' >
				</div>
				
				<div class="col-lg-2 text-center">
					<!--<label>Expense Type </label>-->
						<select name='account_type' id ='account_type'>
							<?php dropdown($account_head_list, $_POST['account_type']); ?>
						</select>
				</div>
				<div class="col-lg-4 text-center">
			    		<select name='account_id' id='account_id' >
			    		    <?php dropdown_list('account_head','id','account_name', $_POST['account_id']); ?>
						 </select>
				
				</div>
				<div class="col-lg-2 text-center">
						<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
				</div>
			
			</div>
			</form>
        </div>
        <!-- /.box-header -->
	    <div class="box-body">
		 		<?php if(isset($_POST['submit']) && isset($_POST['account_id']) )
									{
						$fromdate =$_POST['from_date'];
						$todate =$_POST['to_date'];
						$account_type =$_POST['account_type'];
						$account_id =$_POST['account_id'];
						$status =$_POST['status'];
				?>					
			                <div class="table-responsive">
                              <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead >
                                        <tr class='op-thead'>
                                            <th>Account Name</th>
                                            <th>Txn Date</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$total=0;
									$q='';
									if($_POST['account_id'] <>'')
									{
									   $q=" and account_id='$account_id' ";
									}
									$sql = "SELECT * FROM `exp_details` where  txn_date between '$fromdate' and '$todate' and account_type ='$account_type' and status ='ACTIVE' $q order by txn_date";
									
								
									$res= direct_sql($sql);
								    foreach($res['data'] as $row)
									{
									$rid = $row['id'];
								    $account = get_data('account_head', $row['account_id'])['data'];
								    
									$total =$total +$row['txn_amount'];
								
									echo"<tr class='odd gradeX'>";
									echo "<td> ".$account['account_name']."</td>";
								//	echo "<td> ". date('d-M-Y',strtotime($row['txn_date'])) ."</td>";
									echo "<td> ". $row['txn_date'] ."</td>";
									echo "<td align='right'> ". $row['txn_amount'] ."</td>";
									echo "<td align='right'> ". $row['txn_remarks'] ."</td>";
									echo "<td align='right'> ". $row['status'] ."</td>";
										
									echo "</tr>";
									}
								
								
									
									?>
                                      
                                    </tbody>
									<tfoot>
										<tr class='op-thead'> 
											<th colspan='2' align='right'> Total </td>
											<th align='right'><b> <?php echo $total; ?> </b></th>
											<th colspan='2'></th>
										</tr> 
									</tfoot>
                                </table>
								<?php } ?>
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>   

<script>
     $(document).on('click','#account_type',function(){
		 
		  var acc_type = $(this).val();
		  	$.ajax({
        	type: "GET",
        	url: "system/master_process.php?task=get_account",
        	data:'account_type='+acc_type,
        	success: function(data){
        		console.log(data);
        		$("#account_id").html("<option value=''></option>"+data);
        	}
        	});
		  
		});
</script>