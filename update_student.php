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
	      $sql ="SELECT max(student_admission) as sl FROM student where status not in('DELETED','AUTO','REMOVED')";
	      $resadm = direct_sql($sql);
	      $student_admission = intval($resadm['data'][0]['sl']) +1;
	  }
  }
}
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
		 	<form id='update_frm' action='update_student_photo'>
          <div class="row">
			
            <div class="col-lg-3">
            	<div class="form-group row">
				 
				  <label for="example-text-input" class="col-sm-4 col-form-label">Name</label>
				  <div class="col-sm-8">
					 <input type='hidden' name='id' value='<?php echo $id;?>' />
					<input class="form-control border-warning" type="text"  value='<?php echo $student_name;?>' name="student_name" required readonly>
				  </div>
				</div>
			</div>	
				
			<div class="col-lg-5">
				<div class="form-group row">
					<label for="example-text-input" class="col-sm-4 col-form-label">House Name</label>
					<div class="col-sm-8">
						<select name='house_name' id='house_name' class='form-control'>
							<?php dropdown($house_list, $house_name); ?>
						</select>
					</div>
				</div>
				
			</div>
			<div class="col-lg-4">
            	
				<div class="form-group row">
				  <div class="col-sm-12 text-center">
					<div id='display'><img src='upload/<?php echo $student_photo;?>' width='150px' height='160px' id='result'></div>
				  	<input type='hidden' name='student_photo' id='targetimg' class='form-control' readonly value='<?php echo $student_photo;?>'>
					 <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE  PHOTO </span>		
				  </div>
				</div>
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
				<h4> Upload Image <span style='float:right' id='hbtn'>X</span></h4><hr>
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
    $('#hbtn').on('click', function () {
        $('.modal').css('display','none');
    });

</script>