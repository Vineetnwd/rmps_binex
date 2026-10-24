<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('recruitment');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('recruitment', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
        $doc_path =  $base_url.'system/upload/'.$resume;
    }
}
?>
<style>
    .uploadForm{
        @media(min-width:700px){
            margin-left:52%;
            margin-top:-130px;
            z-index:500;
        }
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Add Employee </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title">Add Recruitment </h1>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <form action='update_employee' id='update_frm' method='post' name="emp_frm" enctype='multipart/form-data'>

                            <div class="card">
                                <div class="bg-gray p-5 text-black">
                                    <b class="text-uppercase">Personal Details</b>
                                </div>
                                <div class="card-body">
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <input type='hidden' value='<?php echo $isedit; ?>' name='isedit'>
                                    <input type='hidden' value='<?php echo $resume; ?>' name='resume' id='resume'>
                                    <div class="form-row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label>Employee code <?= imp() ?></label>
                                                <input type="text" class="form-control" required name='emp_code' value='<?php echo $emp_code; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Name <?= imp() ?></label>
                                                <input type="text" class="form-control" required name='name' value='<?php echo $name; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Gender <?= imp() ?> </label>
                                                <select name="sex" id="sex" class="form-control" required>
                                                    <?php dropdown($gender_list, $sex); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="date" class="form-control" name='dob' value='<?php echo $dob; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Full Address</label>
                                                <textarea name="address" id="address" rows="6" class="form-control" style="height: 35px; resize: vertical;"><?php echo $address; ?></textarea>
                                            </div>

                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label>Mobile <?= imp() ?> </label>
                                                <input class="form-control" type='tel' name='mobile' value='<?php echo $mobile; ?>' maxlength="10" minlength="10" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control" type='email' name='email' value='<?php echo $email; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Aadhar No.</label>
                                                <input class="form-control" type='text' name='adhar' value='<?php echo $adhar; ?>' maxlength="12" minlength="12">
                                            </div>
                                            <div class="form-group">
                                                <label>Marital status <?= imp() ?> </label>
                                                <select name="marital_status" id="marital_status" class="form-control" required>
                                                    <?php dropdown($marital_status_list, $marital_status); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="bg-gray p-5 text-black">
                                        <b class="text-uppercase">Employment Details</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <label>Qualification</label>
                                                    <input class="form-control" type='text' name='qualification' value='<?php echo $qualification; ?>'>
                                                </div>
                                                <div class="form-group">
                                                    <label>Teaching Experience</label>
                                                    <input type="text" class="form-control" required name='experience' value='<?php echo $experience; ?>'>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject(s) Applied For</label>
                                                    <input type="text" class="form-control" required name='subject' value='<?php echo $esubject; ?>'>
                                                </div>
                                                <div class="form-group">
                                                    <label>Preferred Class </label>
                                                    <select name="class_preferred" id="class_preferred" class="form-control" required>
                                                        <?php dropdown($standard_list, $class_preferred); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <label>Currently Employeed  <?= imp() ?> </label>
                                                    <select name="employed" id="employed" class="form-control" required>
                                                        <?php dropdown($allow_status, $employed); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject(s) Applied For</label>
                                                    <input type="text" class="form-control" required name='employer' value='<?php echo $employer; ?>'>
                                                </div>
                                                <div class="form-group">
                                                    <label>Notice Period</label>
                                                    <input type="text" class="form-control" required name='notice_period' value='<?php echo $notice_period; ?>'>
                                                </div>
                                                <div class="form-group">
                                                    <label>Expected Salary(in <i class="fa fa-inr fa-fw"></i> )</label>
                                                    <input class="form-control" type='number' name='ex_salary' value='<?php echo $ex_salary; ?>' min="1000" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="bg-gray p-5 text-black">
                                        <b class="text-uppercase">Other Details</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                        <div class="col-lg-6 col-sm-12">

                                            <!--<div class="form-group">-->
                                            <!--    <div class="col-sm-12 text-center">-->
                                            <!--        <div id='display'>-->
                                                        <!--<img src='upload/-->
                                                        <?php 
                                                        // if ($photo == "") {
                                                        //                         echo "no_image.jpg";
                                                        //                     } else {
                                                        //                         echo $photo;
                                                        //                     }
                                                                            ?>
                                                                            <!--' width='150px' height='160px' id='result'>-->
                                            <!--        </div>-->
                                            <!--        <input type='hidden' name='photo' id='targetimg' class='form-control' readonly value='<?php echo $photo; ?>'>-->
                                            <!--        <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE PHOTO </span>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                                <div class="form-group">
                                                    <label>Status </label>
                                                    <select name='status' class='form-control'>
                                                        <?php dropdown($status_list, $status); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6">

                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                        <form id='uploadForm' class='uploadForm' enctype='multipart/form-data'>
                            <div class='form-group'>
                                <label>Upload Photograph (Max 50 KB) </label>
                                <input type='file' class='form-control w-500' name='uploaddoc' id='uploaddoc' accept=".pdf, .doc, .docx, .jpg, .jpeg, .png">
                                <br><small> Only Pdf, Docx, Jpg and Png image upto 100KB. </small>
                            </div>
                            <!--<div id="download">-->
                            <?php if($isedit == 'yes'){ ?>
                                <a href='<?= $doc_path ?>' class='fa fa-download btn btn-dark btn-xs' download></i> Download</a>
                            <?php  } else { ?>
                                <a href='' id='download' class='fa fa-download btn btn-dark btn-xs' style='display:none'  download ></i> Download</a>
                            <?php } ?>
                            <!--</div>-->
                        </form>
                    </div>

                </div>
            </div>
        </div>
</div>
</div>
</section>
</div>

<?php require_once('system/footer2.php'); ?>
<script>
    function reset() {
        $("#update_frm").reset();
    }
</script>

<script>
    $(document).on('change', '#uploaddoc', function () {
        $("#uploadForm").submit();
    });
    
        $("#uploadForm").on('submit',(function(e){
    	e.preventDefault();
	if($('#update_btn').length!=0)
			{
				$("#insert_btn").attr("disabled", true);
			}
    	$.ajax({
    	url: "system/master_process?task=upload_doc",
    	type: "POST",
    	data:  new FormData(this),
    	contentType: false,
    	cache: false,
    	processData:false,
    	success: function(data){
    		var obj = JSON.parse(data);
    		console.log(data);
    		$("#resume").val(obj.id);
    		$.notify(obj.msg,obj.status);
		    $("#display").html("<img src='system/upload/"+obj.id +"' width='140px' height='160px' >");
		    $("#download").attr("href", "system/upload/" + obj.id);
		    $("#download").css("display", "inline-block");
		    $("#update_btn").attr("disabled", false);
    	},
    	error: function(){} 	        
    	});
    }));

</script>