<style>
.student-photo-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3b82f6;
}
.box-title {
    background: #f1f5f9;
    padding: 10px;
    border-radius: 8px;
}
</style>
<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('system/header.php');?>
<?php require_once('system/menu.php');
$student_id =$_GET['student_id'];
$student = get_data('student',$student_id,null)['data'];
$prev_dues = get_data('student_fee',$student_id,'current_dues','student_id')['data'];
//print_r($prev_dues);
$last_dues = last_dues($student_id);
$prev_session_dues = get_all('student_fee','*', ['student_id'=>$student_id,'branch_id'=>$branch_id])['data'][0]['prev_session_dues'];

if(intval($last_dues) != intval($prev_dues) and $prev_session_dues ==0 )
{
    update_data('student_fee',['current_dues'=>$last_dues],$student_id,'student_id');
    $prev_dues =$last_dues;
}

//$prev_session_dues = get_data('student_fee',$student['student_admission'],'prev_session_dues','student_admission')['data'];
$allow_discount = ($student['allow_discount']=='YES')?'':'readonly';

if($user_type=='DBA')
{
    echo "<script> window.location='dashboard.php' </script>";
}

?>
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Fee Collection <span class='badge bg-danger'><?php echo $student['student_admission'] ; ?></span> </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#fee">Fee</a></li>
        <li class="breadcrumb-item active">Collect Fee</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
     <div class="alert alert-info alert-dismissible fade show" role="alert">
        <!--<strong>Attention! </strong> Please clear all previous dues/previous session  dues before generating the invoice for the <b>new month/new session</b>.-->
        
    </div>
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
   <!--       <h3 class="box-title"><?php echo $student['student_name']; ?><b> [<?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?></b>] <?php echo $student['student_type']; ?>-->
			<!--</h3>-->
			<?php
            $photo = $student['student_photo'];
            // $st_photo = "assets/images/user.jpg";
            
            if ($_SESSION['branch_id'] == 1) {
                $folder = "Bishambharpur";
            } elseif ($_SESSION['branch_id'] == 2) {
                $folder = "Barauli";
            } else {
                $folder = "Barharia";
            }
            // if (!empty($photo)) {
            //     $web_path    = $base_url . "upload/" . $photo;
            
            //     if (file_exists($server_path)) {
            //         $st_photo = $web_path;
            //     }
            // } else {
                $web_path    = $base_url . "student_photo/$folder/Photo/" . $student['student_admission'] . ".jpg";
            
                // if (file_exists($server_path)) {
                    $st_photo = $web_path;
                // }
            // }
            ?>
            
            <h3 class="box-title" style="display:flex;align-items:center;gap:15px;">
            
                <img src="<?= $st_photo ?>" class="student-photo-lg">
            
                <div>
                    <?= $student['student_name']; ?>  
                    <br>
                    <small>
                        [<?= $student['student_class']; ?>-<?= $student['student_section']; ?>]
                        <?= $student['student_type']; ?>
                    </small>
                </div>
            
            </h3>

          <div class="box-tools pull-right">
			 <a href='collect_fee.php'  class='btn btn-success btn-sm'  > Press Alt+C For New</a>
			 <a href='student_ledger.php?student_id=<?php echo $student_id;?>&action=show' title='Pay Fee ' class='btn btn-info btn-sm' name='Pay_fee'>Ledger </a>
			 
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
           
			<div class="row">
				
					<div class="col-lg-3" id='month_list'>		
							<b> Months List </b>
							<ul class='list-group'>
							<?php 						
								foreach($month_list as $month)
								{
									$value =monthly_fee($student_id,$month)['total'];									
									$pst = get_data('student_fee',$student_id, remove_space($month),'student_id')['data'];
									$pst = end(explode(",", $pst));
									$is_disable = '';//($prev_session_dues>0)?'disabled':'';
									if($pst =='')
									{
										//echo "<li class='list-group-item' >";
										create_check($month, remove_space($month), 'fee-month' ,$is_disable); 
										//echo "</li>";
									}
									else{
										//create_check($month, remove_space($month), 'fee-month', 'disabled' );
										echo "<li class='list-group-item' >". $month . "<a href='receipt?receipt_id=$pst' target='_blank' class='badge badge-success text-light float-right'> $pst </a></li>";
									}
								}
							?>
							</ul>
					</div>
					<div class="col-lg-3" style='overflow-y:auto;height:460px'>
					<form  id='pay_frm' action='pay_fee'> 
						<?php 
							$fee_list = get_all('fee_head','*',array('status'=>'ACTIVE','branch_id'=>$branch_id));
							if($fee_list['count']>0)
							{
								foreach($fee_list['data'] as $fee_info)
								{
								    // print_r($fee_list);
									$col_name = remove_space($fee_info['fee_name']);
									echo '<div class="form-group m-0"><label>'.ucwords($fee_info['fee_name']).'</label>';
									echo "<input type='number' name='$col_name' id='$col_name' class='form-control fee-value' placeholder='".$fee_info['fee_name']."' value='0' readonly ></div>";
									
								}
							}
						?>
					</div>
					
					<div class="col-lg-3">
						
						<div class="form-group">
                            <label>Date of Payment </label>
                             <input class="form-control" value='<?php echo $student['student_admission'];?>' name='student_admission'  type='hidden'>
                             <input class="form-control" value='<?php echo $student_id;?>' name='student_id'  id='student_id' type='hidden'>
                             <input class="form-control" value='other_month' id='fee_month' name='paid_month' type='hidden' required>
                            <input class="form-control" value='<?php echo date('Y-m-d');?>' max='<?php echo date('Y-m-d');?>' min='<?php echo date('Y-m-d');?>' name='paid_date' type='date' readonly>
                        </div>
                        <?php 
                            if($prev_dues>0){
                                $psd_cls = 'd-none';
                            }
                        ?>
                        <div class="form-group has-error <?= $psd_cls?>">
							<label class="control-label" for="inputerror" >Prev Session Dues</label>   
                            <input class="form-control text-right" value='<?php echo $prev_session_dues;?>' name='prev_session_dues' type='text' readonly 
                            id="prev_session_dues1" >
                        </div>
                        
						<div class="form-group">
							<label>Previous Dues</label>   
                            <input class="form-control text-right" value='<?php echo $prev_dues;?>' name='previous_dues' type='text' readonly id="previous_dues" >
                        </div>
						
						<div class="form-group">
							<label>Miscellaneous Fee</label>   
                            <input class="form-control text-right" value='0' name='other_fee' type='number' id="other_fee" >
                        </div>
						<div class="form-group has-error">
							<label class="control-label" for="inputError">Discount (<?= $student['allow_discount'] ?>)</label>
							
							<input type="number" class='form-control' name='discount' id='discount' value='0' <?= $allow_discount ?>  required>
							
						</div>
						
					</div>	
			
					<div class="col-lg-3">
						<div class="form-group">
                          <label>Net Payable</label>   
                           <input class="form-control text-right" name='total' type='hidden' id="final_dues" required readonly value='<?php echo finaldues($student_id); ?>'>
                           
                            <input class="form-control text-right" name='total' type='text' id="total" required readonly value='<?php echo finaldues($student_id); ?>'>
                        </div>
						<div class="form-group has-success">
                            <label class="control-label" for="inputSuccess">Amount to be Paid</label>
                            <input type="number" class="form-control" name='paid_amount' id='paid_amount' required autofocus>
                        </div>
						<div class="form-group">
							<label>Payment Mode</label>
							<select class="form-control" name='payment_mode' required>
								<?= dropdown($payment_mode_list,'CASH') ?>
							</select>
						</div>
						<div class="form-group">
                            <label>Remarks (Payment Details)</label>
                            <input class="form-control" name='remarks' required type='text' >
                        </div>
						
						<!--<div class="checkbox text-sm">-->
						<!--	<input type="checkbox" id="send_sms" value='yes' checked name='send_sms' >-->
						<!--	<label for="send_sms">Send SMS </label>                  -->
						<!--</div>-->
					</form>
				
						<button class="btn btn-success" id='pay_btn'><i class='fa fa-save'></i> Pay Now </button>
				
				</div>
						
					</div>
                </div>
			</div>
        </section>
        </div>
<?php require_once('system/footer.php'); ?>

<script>
    
	async function addall()
	{
		var sum = monthly_fee = 0;
		$(".fee-value").each(function(){
			monthly_fee = monthly_fee + parseInt($(this).val());
		});
		var prev_dues =parseInt($("#previous_dues").val()) ||0;
		var prev_session_dues =parseInt($("#prev_session_dues1").val()) ||0;
		var other_fee =parseInt($("#other_fee").val());
		var discount =parseInt($("#discount").val());
// 		sum= (sum+ monthly_fee + prev_dues+other_fee +prev_session_dues )-discount;
    	if(prev_dues > 0){
            sum = monthly_fee + prev_dues + other_fee - discount;
        } else {
            sum = monthly_fee + prev_dues + other_fee + prev_session_dues - discount;
        }
		
		$("#total").val(parseFloat(sum)); 
	}
	
	$("#total, #paid_amount, #previous_dues, #other_fee, #discount, #prev_session_dues1").on('keyup',function(){
	    //var final_dues  = $("#final_dues").val();
	    //var total  = $("#total").val();
		 addall();
	});
    // Auto Select Till Month 
    
    // $(document).ready(function(){
        
        
    //     const allmonth = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    //     const d = new Date();
    //     var cm = allmonth[d.getMonth()].toLowerCase();
    //     //console.log(cm);
    //     var ml = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december','january', 'february', 'march'];
    //     //var ml = ['november', 'december','january', 'february', 'march'];
        
    //     for(var i=0; i<10; i++)
    //     {
    //       $("#"+ml[i]).click();   
    //       if(ml[i] ==cm) 
    //       {
    //          break;
    //       }
    //     }
        
    //     // ml.forEach(function (item, index){
    //     //   $("#"+item).click();
    //     // });
    // })  
    
    
   function autoclick(){
        const allmonth = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        const d = new Date();
        var cm = allmonth[d.getMonth()].toLowerCase();
        var ml = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december','january', 'february', 'march'];
         for(var i=0; i<12; i++)
        {
           $("#"+ml[i]).click();   
           if(ml[i] ==cm) 
           {
             break;
           }
        }
       $("#pay_btn").attr("disabled",false); 
       $("#pay_btn").html("Make Payment"); 
    } 
    
    
   //setTimeout(autoclick, 1500);
    
    
</script>

