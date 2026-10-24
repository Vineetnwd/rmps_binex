<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
	  <h1> Bulk WhatsApp</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#transport">Extra</a></li>
        <li class="breadcrumb-item active">Send SMS</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Type SMS and Send (English & Hindi) </h3>
			
          <div class="box-tools pull-right">
			<span id='msg_count' class='badge badge-success p-1' ></span>
			<a href='https://chrome.google.com/webstore/detail/google-input-tools/mclkkofklkfljcocdinagocijmpgbhab?hl=en-US&utm_source=chrome-ntp-launcher' target='_blank' class='badge badge-danger p-2' > <i class='fa fa-language'></i> Click to use Google Hindi Input </a>
			 
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
			<div class='row'>
			    <div class='alert alert-success mx-2'>
			        All Message will Be Sechduled and Send Automatically. its takes 1-2 Hour to complete the process. You dont have to do anything you can shut down your pc also. 
			    </div>
				<div class='col-lg-4'>
									<form action='master_process.php?task=bulk_wa' method='post'>
									  
									    <div class="form-group">
                                            <label>Select Class </label>
                                            <select class="form-control" name='student_class' id ='msg_no' required >
										
											 <option value='ALL'>All Student</option>
											 <?php dropdown($class_list) ?>
											 <!--<option value='ALL_STAFF'>All Staff</option>-->
                                            </select>
										</div>
									      <label>Campaign Title </label>
									    <input type='text' name='title' value='' required  class="form-control">
										<div class="form-group">
                                            <label>Whatsapp Message </label>
                                        
                                        <textarea class="form-control msg_box" rows="4" name='msg_text' required name='msg_text' ></textarea>
                                        </div>
										<div class="form-group">
										<button class="btn btn-sm btn-success " name='group_sms' type='submit' > SEND SMS TO ALL </button>
									    </div>
									</form>
										
								
									
				</div>
				
				<div class="col-lg-8">
					
									<form action='#' method='post' id='sms_frm'>
										<div class="form-group">
                                            <label>Enter Mobile Nos. </label>
                                            <textarea class="form-control" rows="2" id='mobile2'><?php if(isset($_REQUEST['mobiles'])){ echo $_REQUEST['mobiles'];} ?></textarea>
											<p>Use Enter, Comma or space between two number </p>
										</div>
									
										
										<div class="form-group">
                                            <label>Message (160 character Per SMS) </label>
                                        <textarea class="form-control msg_box" rows="4" id='msg_text2' required></textarea>
                                        </div>
                                        
                                        
                                        
									
									</form>
									<div class="form-group">
										<button class="btn btn-sm btn-primary " id='send_sms'> SEND SMS TO NUMBER </button>
									</div>
									
						
                </div>
            </div>
		</div>
    </section>
</div>		
<?php require_once('system/footer2.php');?>
