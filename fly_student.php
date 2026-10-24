<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); 

if(isset($_GET['link']) and $_GET['link']!='')
{
	$student = decode($_GET['link']);
	$id = $student['id'];
}
else{
	$student =insert_row('student'); 
	//print_r($student);
	$id =$student['id'];
}

if($id!='')
{
  $res = get_data('student',$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
	  if($student_admission=='')
	  {
	   //   $sql ="SELECT max(student_admission) as sl FROM student where status not in('DELETED','AUTO','REMOVED')";
	   //   $resadm = direct_sql($sql);
	   //   $student_admission = intval($resadm['data'][0]['sl']) +1;
	  }
  }
}

$admission_type = 'NEW';
$academic_type = 'DISTANCE';
$status = 'ACTIVE';
?>
 
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Student Details</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#">Student</a></li>
        <li class="breadcrumb-item active">Add Student</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Student Details </h3>

          <div class="box-tools pull-right">
			 <button class="btn btn-success" id='update_btn' ><i class='fa fa-save'></i> Save</button>
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
		 	<form id='update_frm' action='update_fly_student'>
        <div class="row">
			
            <div class="col-lg-6">
            	<div class="form-group row">
				 
				  <label for="example-text-input" class="col-sm-4 col-form-label">Name</label>
				  <div class="col-sm-8">
					 <input type='hidden' name='id' value='<?php echo $id;?>' />
					<input class="form-control border-warning" type="text"  value='<?php echo $student_name;?>' name="student_name" required>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Father's Name</label>
				  <div class="col-sm-8">
					<input class="form-control" type="text"  value='<?php echo $student_father;?>' name='student_father' required>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Mother's Name</label>
				  <div class="col-sm-8">
					<input class="form-control" type="text"  value='<?php echo $student_mother;?>' name='student_mother'>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Date of Birth</label>
				  <div class="col-sm-8">
					<input class="form-control" type="date"  value='<?php echo $date_of_birth;?>' name='date_of_birth' id='example-date-input'>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Gender</label>
				  <div class="col-sm-8">
					<select name='student_sex' class='form-control' required>
						<?php dropdown($gender_list,$student_sex); ?>
					</select>
				  </div>
				</div>
			
			</div>
			<div class="col-lg-6">
            	
				<div class="form-group row">
				  <div class="col-sm-12 text-center">
					<div id='display'><img src='upload/<?php echo $student_photo;?>' width='150px' height='160px' id='result'></div>
				  	<input type='hidden' name='student_photo' id='targetimg' class='form-control' readonly value='<?php echo $student_photo;?>'>
					 <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE  PHOTO </span>		
				  </div>
				</div>
				
				
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Mobile No.</label>
				  <div class="col-sm-8">
					<input class="form-control" type="number"  value='<?php echo $student_mobile;?>' pattern="[6789][0-9]{9}" name="student_mobile" required>
				  </div>
				</div>
				<?php if($user_type =='ADMIN' or $user_type =='DEV') { ?>	
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Select Branch </label>
				 <div class="col-sm-8">
					<select name='branch_id' class='form-control' required>
					  <?= dropdown_with_key(branch_list(),$branch_id); ?>
					</select>
				  </div>
				</div>
				<?php } else{ ?>
				<input type='hidden' name='branch_id' value='<?= $branch_id ?>' />
			    <?php } ?> 
			</div>
		
        </div>
            <!-- /.col -->
			
          <h3 class="box-title bg-gray p-2">Admission Details</h3>
		 
          <div class="row">
			
            <div class="col-lg-6">
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Session </label>
				  <div class="col-sm-8">
					<select name='student_session' class='form-control' required>
					  <?php dropdown($session_list,$student_session); ?>
					</select>
				  </div>
				</div>
				
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Date of Admission</label>
				  <div class="col-sm-8">
					<input class="form-control" type="date"  value='<?php echo $date_of_admission;?>' name='date_of_admission'>
				  </div>
				</div>
				
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Admission No.*</label>
				  <div class="col-sm-8">
					<input class="form-control border-warning" type="text"  value='<?php echo $student_admission;?>' name='student_admission' required>
				  </div>
				</div>
				
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Class & Section</label>
				  <div class="col-sm-4">
					<select name='student_class' id='student_class' class='form-control' required>
					  <?php dropdown($class_list,$student_class); ?>
					</select>
				  </div>
				  <div class="col-sm-4">
					<select name='student_section'  id='student_section' class='form-control' required>
						<?php dropdown($section_list,$student_section); ?>
					</select>
				  </div>
				</div>
			</div>	
				
			<div class="col-lg-6">
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Class Roll No.</label>
				  <div class="col-sm-8">
					<input class="form-control" type="number" name='student_roll' id='student_roll'  value='<?php echo $student_roll;?>'>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Admission Type </label>
				  <div class="col-sm-8">
					<select name='admission_type' class='form-control' required>
						<?php dropdown($admission_list,$admission_type); ?>
					</select>
				  </div>
				</div>
			
				<div class="form-group row">
				  <label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
				  <div class="col-sm-8">
					<select name='status' class='form-control' required>
					  <?php dropdown($status_list,$status); ?>
					</select>
				  </div>
				</div>
				<?php 
                    if (
                        $user_type === 'ADMIN' || 
                        $user_type === 'DEV' || 
                        ($user_type === 'DBA' && $user_name === 'pratima')
                    ) {  
                    ?>

			        
        				<div class="form-group row">
        				  <label for="example-text-input" class="col-sm-4 col-form-label">Academic Type </label>
        				  <div class="col-sm-8">
        					<select name='academic_type' class='form-control' required>
        					  <?php dropdown($academic_list,$academic_type); ?>
        					</select>
        				  </div>
        				</div>
				
			    <?php } ?>
			</div>
			
            </div>
			
          </div>
          <!-- /.row -->
		
        </div>
        <!-- /.box-body -->
		 </form>
    </section>
  </div>

 <div class='modal' id='uploadmodal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-body'>
				<h4> Upload Image </h4><hr>
				<form id='uploadForm' enctype='multipart/form-data'>
					<div class='form-group'>
						<label>Upload Photograph (Max 100 KB)</label>
						<input type='file' name='uploadimg' id='uploadimg' accept='image'>
						<br><small> Only Jpg and Png image upto 100KB. </small>
					</div>
				</form>
				<div id="my_camera"></div>
				<form>
            		<input type=button value="Take Snapshot" onClick="take_snapshot()">
            	</form>
			</div>
		</div>
	</div>
</div>
<?php require_once('system/footer2.php'); ?>

<script>
 $(document).ready(function() { 
  var x = $('#student_type').val();
 
 
  if(x=='TRANSPORT')
  {
	  $('#area_id').css("display","block");
	  $("#area_text").attr("required","required");

	  
  }
  else{
	  $('#area_id').css("display","none");
	   $('#area_text').removeAttr("required");
	  }  
	    
	    
	    
 $('#student_type').on('change', function() {
  
      var a=  $('#student_type').val();
      
      if(a=='TRANSPORT')
      {
    	  $('#area_id').css("display","block");
    	   $("#area_text").attr("required","required");
      }
      else{
    	  $('#area_id').css("display","none");
    	    $('#area_text').removeAttr("required");
      }
});

        $("#base_dues").on('blur',function(){
            var bd1 = $("#base_dues").val();
            var bd2 = $("#base_dues2").val();
            if(bd1 !=bd2)
            {
               	bootbox.confirm({
        		message: "You you really want to Change Base Dues ?",
        		buttons: 
        		{
        			confirm: {
        				label: '<i class="fa fa-check"></i> Yes',
        				className: 'btn-success'
        			},
        			cancel: {
        				label: '<i class="fa fa-times"></i> No',
        				className: 'btn-danger'
        			}
        		}, 
        		callback: function (result) {
        		if(result==false)
                {
                    $("#base_dues").val($("#base_dues2").val());
                }
        		}
        		});
            }   
        });
});
 
 
 $("#student_section, #student_class").on('change', function(){
		var student_class = $("#student_class").val(); 
		var student_section = $("#student_section").val(); 
    		$.ajax({
    			method:'post',
    			url:'master_process.php?task=last_roll',
    			data:{
    			student_section: student_section,
    			student_class:student_class
    		  	},
    	  		success:function(res){
    		       $("#student_roll").val(res);
    			}	
    		});
	});
</script>