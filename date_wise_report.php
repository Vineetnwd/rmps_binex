<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$table_name = 'account_txn';
$from_date = (isset($_REQUEST['from_date']))?$_REQUEST['from_date']:$today;
$to_date = (isset($_REQUEST['to_date']))?$_REQUEST['to_date']:$today;

$date_limit = date('Y-m-d',strtotime('-7 days', strtotime($today))); // Subtract 7 days from today
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
     <h1> Date wise Balance Report </h1>
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
			
				
				<div class="col-lg-4 text-right"> 
					<label>From Date </label>
				<?php if($user_type !='ADMIN'){ ?>
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
	       <?php  if(isset($_POST['from_date']) && isset($_POST['to_date'])){
	       extract($_POST);
	       ?>
		 	    <div class='row'>
		 	        <div class='col-md-6'>
		 	            <h2> Collection (Cash & Bank)</h2>
		 	                <table class='tbl table'>
					<?php 
					$income = all_income($from_date, $to_date);
					foreach($income as $ed => $i_amt){
					?>
				        <tr><td><?php echo add_space($ed); ?> </td>
				            <td align='right'> <?php echo $i_amt;?></td>
				        </tr>
					<?php } ?>
					   
					</table>
		 	        </div>
		 	        
		 	         <div class='col-md-6'>
		 	            <h2> Payment /Expense </h2>
		 	               <table class='tbl table'>
					<?php 
					$payment = all_exp($from_date, $to_date);
					foreach($payment as $hd => $e_amt){
					?>
				        <tr><td><?php echo add_space($hd); ?> </td>
				            <td align='right'> <?php echo $e_amt;?></td>
				        </tr>
					<?php } ?>
					</table>
		 	        </div>
		 	    </div>
		 	
            </div>
            <div class='box-footer text-center'>
               <h2> 
               <button class='btn btn-success text-light'>Bank Collection : <?php echo all_income($from_date, $to_date ,'bank')['total']; ?>  
               </button>
               
               <button class='btn btn-danger text-light'>Online Collection : <?php echo all_income($from_date, $to_date ,'Online')['total']; ?>  
               </button>
               
               
               <button class='btn btn-dark text-light'>Balance Amount : <?php echo $income['total'] - $payment['total'];?> 
               </button>
            
             
            
            </div>
            <?php } ?> 
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       