<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Generate Admit card </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Exam</a></li>
        <li class="breadcrumb-item active">Admit card</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
          <h3 class="box-title">Generate Admit Card</h3>

		 <div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		  </div>
        </div>
				<div class="box-body">
				    	<form action ='print_admit.php' method ='post' target='otpl' id='reminder_frm'>
						<div class='row justify-content-center'>
						
						<div class='col-md-4'>
						        
						        
				    
								<div class="form-group">
										<label>Select Class </label>
										<select class="form-control" name='student_class' >
										   <?php dropdown($class_list);?>
										</select>
								</div>
								<div class="form-group">
										<label>Select Section </label>
										<select class="form-control" name='student_section' >
										   <?php dropdown($section_list);?>
										</select>
								</div>
						</div>
						<div class='col-md-4'>
									<div class="form-group">
                                            <label> Admission No (1102,1403,1404...)  </label>
                                            <input type='text' class="form-control"  name='student_admission' placeholder='1102,1403,1404' >
								</div>
								 <div class="form-group">
            						<label>Exam </label>
            						<select name='exam_id'  class="form-control"  required >
            						   <?php dropdown_list('exam_list','id','exam_name');?>
            						</select>
            				    </div>	
								<div class="form-group">

									 <input type="submit" class="btn btn-orange btn-block " value=' Create Admit Card ' name='submit'>
									
								</div>
						
						</div>
						</div>
						</form>
						
					
				</div>
		</div>
	</section>
</div>
<?php require_once('system/footer.php'); ?>