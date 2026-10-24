<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if($user_type=='DBA')
{
    echo "<script> window.location='dashboard' </script>";
}

?>
<style>
.tbl tr:last-child
{
  background:gray;
  color:#fff;
}
h4{
    font-size:22px;
    font-weight:800;
}
</style>
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Account
        <?php if($user_type =='Admin') { ?>
        <small><a href='dashboard'>( Dashboard)</a></small>
        <?php } ?>
        <a class="btn btn-success" target="_blank" href="summary.php?txn_date=<?=$today?>"> Daily Summary </a>
      </h1>
      <ol class="breadcrumb">

    	<form action="system/master_process.php?task=switch_branch" method="post" role="form" id="branch_form">
			<input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
			<select name="branch_id" class="form-control float-left" id="branch_select">
				<?= dropdown_with_key(branch_list(), $_SESSION['branch_id']); ?>
			</select>
		</form>
			
        <form  action='system/master_process.php?task=change_session' method='post' role="form">
                    
         <select name='session_year' class='form-control float-left' onchange='submit()'>
              <?php dropdown_with_key($session_list, $db_name); ?>
          </select>
        </form>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
         
         <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-blue">
            <div class="inner">
              <h4><?= $opening_bal = account_balance(); ?></h4>

              <p>Opening Balance </p>
            </div>
            <div class="icon">
              <i class="fa fa-inr"></i>
            </div>
            <a href="collection_report?txn_mode=Cash" class="small-box-footer">Cash <i class="fa fa-arrow-right"></i></a>
          </div>
        </div> 
          
         <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h4><?php 
			    echo $cash_coll = intval(all_income('','','CASH')['total']); 
                
                ?></h4>

              <p>Cash Collection</p>
            </div>
            <div class="icon">
              <i class="fa fa-money"></i>
            </div>
            <a href="collection_report?txn_mode=Cash" class="small-box-footer">Cash <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
       
       
     
        <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h4><?= $cash_exp = all_exp('','','CASH')['total']??0; ?></h4>

              <p>Cash Expense</p>
            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="manage_account" if ='' class="small-box-footer">Expense  <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              
              <h4><?php echo (($cash_coll + total_sale('CASH')) -$cash_exp); ?></h4>

              <p>Cash in Hand </p>
            </div>
            <div class="icon">
             <i class="fa fa-thumbs-up"></i>
            </div>
            <a href="date_wise_report" class="small-box-footer">Balance <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
        
         <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-blue">
            <div class="inner">
              <h4><?php 
               echo $total_sale = total_sale()??0;
              ?></h4>

              <p>Stationary Sale </p>
            </div>
            <div class="icon">
              <i class="fa fa-book"></i>
            </div>
            <a href="manage_distribute_item" class="small-box-footer">Cash <?= total_sale('CASH')??0 ?> | Bank <?= total_sale('BANK')??0 ?> <i class="fa fa-arrow-right"></i> </a>
          </div>
        </div>
        
         <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h4><?php 
             
               echo $bank_coll = intval(all_income('','','BANK')['total']); 
              ?></h4>

              <p>Bank Collection </p>
            </div>
            <div class="icon">
              <i class="fa fa-bank"></i>
            </div>
            <a href="collection_report?txn_mode=Bank" class="small-box-footer">Bank  <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
        
        <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h4><?= $bank_exp = all_exp('','','BANK')['total']??0; ?></h4>

              <p>Bank Expense</p>
            </div>
            <div class="icon">
              <i class="fa fa-credit-card"></i>
            </div>
            <a href="manage_account" if ='' class="small-box-footer">Expense  <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
                <?php
              $closing_balance =  ($opening_balance + $cash_coll + $bank_coll + $total_sale ) -($cash_exp + $bank_exp);
              ?>
              <h4><?= $closing_balance ?></h4>

              <p>Closing Balance</p>
            </div>
            <div class="icon">
              <i class="fa fa-inr"></i>
            </div>
            <a href="date_wise_report" class="small-box-footer">Balance <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
        
        </div>
    
    
        
        <div class="row">
        
		<div class="col-xl-12 connectedSortable">
			<div class="box box-info">
				<div class="box-header">
				  <h3 class="box-title">Quick Access</h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<div class="box-body">
				    <div class='row'>
						   
						    
						   <div class='col-md-2 text-center p-2'>
						        <a href='collect_fee' title='Collect Fee'>
						        <img src='assets/icon/printer.png' height='50px' />
						        <br> Pay Fee
						        </a>
						    </div>
						    
						    <div class='col-md-2 text-center p-2'>
						        <a href='generate_demand' title='Demand Slip'>
						        <img src='assets/icon/calculation.png' height='50px' />
						        <br> Create Demand
						        </a>
						    </div>
						  
						  
						    <div class='col-md-2 text-center p-2'>
						         <a href='collection_report' title='Collection Report'>
						        <img src='assets/icon/calc.png' height='50px' />
						        <br> Collection Report</a>
						    </div>
						    
						    
						     <div class='col-md-2 text-center p-2'>
						         <a href='collect_fee' title='Distribute Item '>
						        <img src='assets/icon/pencil.png' height='50px' />
						        <br>   Distribute Item </a>
						    </div>
						     <div class='col-md-2 text-center p-2'>
						         <a href='manage_account' title='Collection Report'>
						        <img src='assets/icon/ereader.png' height='50px' />
						        <br> Expense Report</a>
						    </div>
						  
						    
						     <div class='col-md-2 text-center p-2'>
						         <span id='exp_entry'title='Add Expense'>
						        <img src='assets/icon/writing.png' height='50px' />
						        <br> Expense Entry  </a>
						    </div>
						   
					</div>
				</div>
				<!-- /.box-body -->
            </div>
          <!-- /.box -->
      	</div>         
        <!-- /.col -->        
       </div>  
       
       
       
		<div class="row">

			<div class="col-xl-12 connectedSortable">
				<div class="box box-info">
					<div class="box-header">
						<h3 class="box-title">Student Analysis & Absent Student</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body" style='overflow-x:scroll'>
						<table id="example1" class="table table-bordered table-striped">
							<tr class='bg-dark text-light'>
								<th> Section / Class</th>
								<?php
								foreach (array_filter((array)$class_list) as $class) {
									echo "<th>" . $class . " </th>";
								}
								?>
							</tr>
							<?php
							foreach (array_filter($section_list) as $section) {
								echo "<tr><th>" . $section . "</th>";

								foreach (array_filter($class_list) as $class) {
									$link = encode('student_class=' . $class . '&student_section=' . $section);
                                    $cmonth =date('Y-m');
									$att = today_att_status($class, $section,'A');
									$ct = studentcount($class,$section)??0 ;
									echo "<th><a href='manage_student?link=$link' title='Total Strength'>" . $ct . "</a> 
									<a href='student_monthwise_att_report?class=$class&section=$section&month=$cmonth' title='Absent Student' class='badge badge-danger text-light'>" . $att . "</a></th>";
									//echo "<th><a href='student_status?link=$link'>" . studentcount($class, $section) . "</a></th>";
								}
								echo "</tr>";
							}
							?>
							<tr class='bg-primary text-light'>
								<th> Total </th>
								<?php
								foreach (array_filter($class_list) as $class) {
								    $ct = studentcount($class)??0 ;
									echo "<th><a href='manage_student?student_class=$class&student_section='>" . $ct . "</a></th>";
								}
								?>
							</tr>
							<tr>
								<th> New Admission </th>
								<?php
								foreach (array_filter($class_list) as $class) {
									echo "<th><a href='manage_student?student_class=$class&student_section=' class='text-success text-bold'> + " . new_student_count($class) . "</a></th>";
									//echo "<th><a href='student_status?student_class=$class&student_section='>" . studentcount($class) . "</a></th>";
								}
								?>
							</tr>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
      <!-- /.row -->		
      <div class="row">
		  
		 <div class="col-xl-4 connectedSortable">	
          <!-- PRODUCT LIST -->
			  <div class="box box-primary">
				<div class="box-header with-border">
				  <h3 class="box-title">Last 3 Invoice </h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
				  <ul class="products-list product-list-in-box">
					<?php  $respay = get_multi_data('receipt', array('paid_date'=>$today),'order by id desc limit 3'); 
					
					if($respay['count']>0) {
					foreach($respay['data'] as $pd){
					?>
					<li class="item">
					  <div class="product-img">
					  <?php $img=  get_data('student',$pd['student_id'],'student_photo')['data']??'no_image.jpg'; ?>
						<img src="upload/<?php echo $img; ?>" alt="Student Image">
					  </div>
					  <div class="product-info">
						<a href="javascript:void(0)" class="product-title"> <?php echo get_data('student',$pd['student_id'],'student_name')['data']; ?> <span
							class="label bg-yellow pull-right"><?php echo $pd['paid_amount']; ?> </span></a>
						<span class="product-description">
							<?php echo $pd['paid_month']; ?> 
						</span>
						<span class="product-description">
						At	<?php echo date('h:i A', strtotime($pd['created_at'])); ?> 
						</span>
					  </div>
					</li>
					<?php } } ?>
				  </ul>
				</div>
				<!-- /.box-body -->
				<div class="box-footer text-center">
				  <a href="collection_report" class="uppercase">View All Collection</a>
				</div>
				<!-- /.box-footer -->
			  </div>           
		 </div>  
		 
		 <div class="col-xl-4 connectedSortable">	
          <!-- PRODUCT LIST -->
			  <div class="box box-primary">
				<div class="box-header with-border">
				  <h3 class="box-title">Today Payment</h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
				  <ul class="products-list product-list-in-box">
				      <table class='tbl table'>
					<?php 
					$payment = all_exp($today, $today);
					foreach($payment as $hd => $e_amt){
					?>
				        <tr><td><?php echo add_space($hd); ?> </td>
				            <td align='right'> <?php echo $e_amt;?></td>
				        </tr>
					<?php } ?>
					</table>
				  </ul>
				</div>
				<!-- /.box-body -->
				<div class="box-footer text-center">
				  <a href="manage_account" class="uppercase">View All Collection</a>
				</div>
				<!-- /.box-footer -->
			  </div>           
		 </div> 
		 
		 <div class="col-xl-4 connectedSortable">	
          <!-- PRODUCT LIST -->
			  <div class="box box-primary">
				<div class="box-header with-border">
				  <h3 class="box-title">Today Collection</h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
				  <ul2 class="products-list product-list-in-box">
				      <table class='tbl table'>
					<?php 
					$income = all_income($today, $today);
					foreach($income as $ed => $i_amt){
					?>
				        <tr><td><?php echo add_space($ed); ?> </td>
				            <td align='right'> <?php echo $i_amt;?></td>
				        </tr>
					<?php } ?>
					</table>
				  </ul>
				</div>
				<!-- /.box-body -->
				<div class="box-footer text-center">
				  <a href="collection_report" class="uppercase">View All Payment</a>
				</div>
				<!-- /.box-footer -->
			  </div>           
		 </div> 
       
      </div>
      <!-- /.row -->	
	  
	  
        <div class="row">
            <div class="col-xl-12 connectedSortable">
            	<div class="box box-info">
				<div class="box-header">
				  <h3 class="box-title">Revanue of the Month </h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<div class="box-body">
            	
            	    <div id="income_exp_graph" style="width: 100%; height: 250px;"></div>
            	 </div>
                </div>
            </div>
        </div>
      
	</section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php require_once('system/footer.php'); ?>
  <div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id='appmodal'>
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">  Expense Entry </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<form action ='exp_entry' method ='post' id='insert_frm'	enctype='multipart/form-data'>
					    
					    <div class="form-group">
								<label>Expense Type </label>
								<select id='account_type' class='form-control'>
									<?php dropdown($account_head_list, $account_type); ?>
								</select>
						</div>		
					
						<div class="form-group">
							<label> Account Name </label>
							<select name='account_id' class="form-control" id='account_id'>
							</select>
						</div>
						
					
						<div class="form-group">
							<label>Txn Date</label>
							<input class="form-control"  type='date' value ='<?php echo date('Y-m-d'); ?>' name='txn_date' readonly required>
						</div>	
						<div class="form-group">
							<label>Txn Amount</label>
							<input class="form-control"  type='number' value ='' name='txn_amount' required autofocus>
						</div>
						
						<div class="form-group">
							<label>Txn Mode</label>
							<select name ='txn_mode' class='form-control'>
							<?php dropdown($txn_mode_list); ?>
							</select>
						</div>
																
						<div class="form-group">
							<label>Remarks </label>
							<input class="form-control" placeholder="Details of Transaction" name='txn_remarks' required>
						</div>
					</form>
					
					<button class="btn btn-success" id='insert_btn'>Save Txn </button>
				
                </div>
            </div>
        </div>
    </div>

	<script>
        $(document).on('click','#exp_entry',function(e){
		  e.preventDefault();
		  $('#appmodal').modal('show');
		  var txn_type = $(this).attr("data-txn");
		  $("#account_id").val($(this).attr("data-id"));
		  $("#account_name").val($(this).attr("data-account"));
		  $("#txn_type").val(txn_type);
		  $("#exampleModalCenterTitle").html("Expense "+txn_type);
		  
		});
		
		 $(document).on('click','#account_type',function(){
		 
		  var acc_type = $(this).val();
		  	$.ajax({
        	type: "GET",
        	url: "system/master_process.php?task=get_account",
        	data:'account_type='+acc_type,
        	success: function(data){
        		console.log(data);
        		$("#account_id").html(data);
        	}
        	});
		  
		});
	</script>	
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      
      <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(income_exp_graph);
    //  console.log(<?php echo income_exp_graph();?> );
      function income_exp_graph() {
        var data = google.visualization.arrayToDataTable(<?php echo income_exp_graph();?> );

        var options = {
          title: 'Income Vs Expence',
        };

        var chart = new google.visualization.LineChart(document.getElementById('income_exp_graph'));

        chart.draw(data, options);
      }
    </script>

<script>
	// Auto-submit form via AJAX for branch change
	$('#branch_select').on('change', function() {
		let form = $('#branch_form');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function(response) {
				console.log('Branch changed successfully:', response);
				location.reload();
				// Optionally handle the server response here
			},
			error: function(xhr, status, error) {
				console.error('Error changing branch:', error);
			}
		});
	});

	// Auto-submit form via AJAX for session change
	$('#session_select').on('change', function() {
		let form = $('#session_form');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function(response) {
				console.log('Session changed successfully:', response);
				location.reload();
				// Optionally handle the server response here
			},
			error: function(xhr, status, error) {
				console.error('Error changing session:', error);
			}
		});
	});
</script>
