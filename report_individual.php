<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

if($user_type=='DBA')
{
    echo "<script> window.location='dashboard.php' </script>";
}
$sel_branch = ($_POST['sel_branch'])??$branch_id;
$search_by = ($_POST['search_by'])??'student_admission';
$student_class = ($_POST['student_class'])??'';
$search_text = ($_POST['search_text'])??'';
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Individual Fee Report </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#fee">Fee</a></li>
        <li class="breadcrumb-item active">Collect fee</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Individual Fee Report </h3>

          <div class="box-tools pull-right">
		
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
		 	<form action ='' method='post'>
			<div class='row'>
				<div class="col-lg-4 col-offset-lg-2"></div>
				
               
					<div class="form-group">
						
						<div class="form-group has-success">
							<label class="control-label" for="inputSuccess">Enter Admission No. </label>
							<input type="text" class='form-control' name='search_text' placeholder="Enter Admission No." required autofocus>
						</div>
					</div>
			
				    <div class="col-lg-2">
					<div class="form-group">
						<label  class="control-label" >&nbsp; Alt +S to Search </label>
						<input type="submit" id='st_search' class='btn btn-success' value='Search Student' name='search' accesskey='s'>
					</div>
				    </div>
				
			</div>
			</form>
			
			<div class='row'>
          
				<div class='col-md-2'></div>
				<div class='col-md-8'>
                    <!-- Advanced Tables -->
							<?php
							if (isset($_REQUEST['search']))
							{
							$search =  get_data('student', $_REQUEST['search_text'], null ,'student_admission');
    							if($search['count']==0)
    							{
    							 echo "<center><div class='alert alert-info'> Invalid Admission No </div></center> ";
    							}
							else{
							$student_id = $search['data']['id'];
                            $student =  $search['data'];
                        	$finaldues = finaldues($student_id);
                        	$td +=$finaldues;
	?>
            
                
        	    <table  class='table table-bordered'>
	               
	               
	                 <tr>
	                    <td> <h4> Sr. No. : <?= $i ?></h4>  </td>
	                    <td bgcolor='lightgreen' > Adm No. : <b><?php echo $student['student_admission']; ?></b></b> </td>
	                
	                  
	                    <td bgcolor='lightpink' colspan='2'> Name : <b> <?php echo $student['student_name']; ?></b>  </td>
	                </tr>
	               
	                <tr>
	                    <td> Mobile No. </td>
	                    <td> <?php echo $student['student_mobile']; ?>  </td>
	                
	                    <td> Father's Name  </td>
	                    <td> <?php echo $student['student_father']; ?>  </td>
	                </tr>
	                 <tr>
	                    <td> Class  </td>
	                    <td> <?php echo $student['student_class']."-".$student['student_section']; ?>  </td>
	                
	                    <td> Roll  </td>
	                    <td> <?php echo $student['student_roll']; ?>  </td>
	                </tr>
	               
	            </table>
	            <table class='table table-bordered'>
	              
	                <tr bgcolor='lightblue'>
	                    <td> Receipt No.</td>
	                    <td> Date Time </td>
	                    <td> Total </td>
	                    <td> Paid </td>
	                    <td> Mode </td>
	                    <td> Received By </td>
	                </tr>
	            
	            <?php 
	            
	            $res2 = get_all('receipt', '*',['student_id'=>$student_id,'status'=>'PAID']);
	            $stotal= $spaid =0;
	            foreach((array)$res2['data'] as $rcpt)
	            {
	                $stotal +=$rcpt['total'];
	                $spaid +=$rcpt['paid_amount'];
	                
	                // Create a DateTime object with the UTC timezone
                    $utc_datetime = new DateTime($rcpt['updated_at'], new DateTimeZone('UTC'));
                    
                    // Convert UTC to IST
                    $utc_datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                    
                    // Format the IST time
                    $ist_time = $utc_datetime->format('d M Y h:i A');

	               echo "<tr>";
	               
	               echo "<td>". $rcpt['id']."</td>";
	               echo "<td>". $ist_time ."</td>";
	               echo "<td>". $rcpt['total']."</td>";
	               echo "<td>". $rcpt['paid_amount']."</td>";
	               echo "<td>". $rcpt['payment_mode']."</td>";
	               echo "<td>". get_data('user',$rcpt['created_by'],'user_name')['data']."</td>";
	               echo "</tr>";
	            }
	            $tc +=$spaid;
	             if( $finaldues >0)
	               {
	                   $dues= $stotal-$spaid;
	                echo "<tr bgcolor='lightpink'><td colspan='2'> Total </td> <td>$stotal </td> <td> $spaid </td><td colspan='2' style='background:red;color:#fff'> Dues:  $finaldues </td>  </tr>";
	               }
	               else{
	             echo "<tr bgcolor='yellow'><td colspan='2'> Total </td> <td>$stotal </td> <td>$spaid </td><td colspan='2'></td></tr>";
	               }
	            ?>
	            </table>
	            </center>
			</div>
						<?php	} }
                                       ?>
             </div>                    
                    </div>
             </div>                           
</section>
</div>
<?php require_once('system/footer.php'); ?>
<script>
                $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $("#st_search").trigger("click");
                }
            });
</script>