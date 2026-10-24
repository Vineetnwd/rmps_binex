<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$table_name = 'account_txn';
$from_date =(isset($_REQUEST['from_date']))?$_REQUEST['from_date']:$today;
$to_date =(isset($_REQUEST['to_date']))?$_REQUEST['to_date']:$today;

//$date_limit = date('Y-m-d',strtotime('-7 days', strtotime($today))); // Subtract 7 days from today
?>
<style>
.tbl tr:last-child
{
  background:#ffbf36;
}
</style>
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Daily Closing Report </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Report</a></li>
        <li class="breadcrumb-item active"> Account </li>
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
					<label>From Date </label>
				<?php if($user_type !='Admin'){ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date' min='<?= $date_limit;?>' max='<?= $today ?>'>
				<?php }else{ ?>
					<input type='date' value='<?php echo $from_date;?>' name='from_date' max='<?= $today ?>' >
				<?php } ?>
				</div>
				<div class="col-lg-4 text-center">
					<label>To Date </label>
					<input type='date' value='<?php echo $to_date;?>' name='to_date' max='<?= $today ?>'>
				</div>
				
				<div class="col-lg-2 text-center">
						<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
				</div>
			
			</div>
			</form>
        </div>
        <!-- /.box-header -->
	    <div class="box-body">
	          <?php  if(isset($_POST['from_date']) && isset($_POST['to_date'])){ ?>
	        <table class='table table-bordered' id='example'>
	            <thead>
	            <tr class='op-thead'>
	                <th>Closing Date</th>
	                <th>Opening Balance</th>
	                <th>Cash Collection</th>
	                <th>Bank Collection</th>
	                <th>Cash Expense </th>
	                <th>Bank Expense </th>
	                <th>Closing Balance </th>
	            </tr>
	       </thead>
	       <tbody>
	     <?php
	      
	      $sql ="select * from closing_report where closing_date between '$from_date' and '$to_date' order by closing_date desc ";
	      $res = direct_sql($sql);
	      
	      foreach((array)$res['data'] as $row)
	      {
	          echo "<tr>";
	          //echo "<td>".date('d-M-Y', strtotime($row['closing_date'])) ."</td>";
	          echo "<td>".$row['closing_date']."</td>";
	          echo "<td align='right'>".$row['opening_balance'] ."</td>";
	          echo "<td align='right'>".$row['inc_cash'] ."</td>";
	          echo "<td align='right'>".$row['inc_bank'] ."</td>";
	          echo "<td align='right'>".$row['exp_cash'] ."</td>";
	          echo "<td align='right'>".$row['exp_bank'] ."</td>";
	          echo "<td align='right'>".$row['closing_balance'] ."</td>";
	          echo"</tr>";
	       ?>
		 	    
            <?php } ?> 
            </tbody>
             </table>
             <?php } ?>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       