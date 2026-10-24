<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('system/header.php');?>
<?php require_once('system/menu.php');

    $emp_id = $sid =$_GET['emp_id'];
    $employee = get_data('employee',$emp_id,null)['data'];
    extract($employee); 
    
    $emp = insert_row('staff_gate_pass');
    $id = $emp['id'];
    $isedit = 'no';

if ($id != '') {
    $res = get_data('staff_gate_pass', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
if($escort_date==''){
    $escort_date=date('d-m-Y',strtotime($today));
}

?>
<style>
    /*.uploadForm{*/
    /*    @media(min-width:700px){*/
    /*        margin-left:52%;*/
    /*        margin-top:-100px;*/
    /*        z-index:500;*/
    /*    }*/
    /*}*/
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> <?= $name.' ('.$emp_code .' - '. $emp_type.') ' ?> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Staff Gate Pass</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title">Escorts Details </h1>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <form action='update_staff_gate_pass' id='update_frm' method='post' name="staff_gate_pass_frm" enctype='multipart/form-data'>

                            <div class="card">
                                <div class="bg-gray p-5 text-black">
                                    <!--<b class="text-uppercase">Escorts Details</b>-->
                                </div>
                                <div class="card-body">
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <input type='hidden' value='<?php echo $isedit; ?>' name='isedit'>
                                    <input type='hidden' value='<?php echo $sid; ?>' name='student_id' id='student_id'>
                                    <div class="form-row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label>Date <?= imp() ?></label>
                                                <input type="date" class="form-control" required name='escort_date' value='<?php echo $escort_date; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label for="mobile">Mobile No. </label>
                                                <input type="tel" class="form-control" name='escort_mobile'  value='<?php echo $mobile; ?>' readonly="">
                                            </div>

                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            
                                            <div class="form-group">
                                                <label>Time Out</label>
                                                <input type="time" class="form-control" name='time_out' value='<?php echo $time_out; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Time In</label>
                                                <input type="time" class="form-control" name='time_in' value='<?php echo $time_in; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Reason</label>
                                                <input type="text" class="form-control" name='reason' value='<?php echo $reason; ?>'>
                                            </div>
                                        </div>
                                        
                                            <div class="form-group">
                                                <div class="col-sm-12 text-center">
                                                    <div id='display'>
                                                        <img src='upload/<?php if ($photo == "") {
                                                                                echo "no_image.jpg";
                                                                            } else {
                                                                                echo $photo;
                                                                            } ?>' width='150px' height='160px' id='result'>
                                                    </div>
                                                    <input type='hidden' name='photo' id='targetimg' class='form-control' readonly value='<?php echo $photo; ?>'>
                                                    <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE PHOTO </span>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                        </form>
                        <!--<form id='uploadForm' class='uploadForm' enctype='multipart/form-data'>-->
                        <!--    <div class='form-group'>-->
                        <!--        <label>Upload Photograph (Max 50 KB) </label>-->
                        <!--        <input type='file' class='form-control w-500' name='uploaddoc' id='uploaddoc' accept=".pdf, .doc, .docx, .jpg, .jpeg, .png">-->
                        <!--        <br><small> Only Pdf, Docx, Jpg and Png image upto 100KB. </small>-->
                        <!--    </div>-->
                        <!--    <div id="download">-->
                        <!--    <?php if($isedit == 'yes'){ ?>-->
                        <!--        <a href='<?= $doc_path ?>' class='fa fa-download btn btn-dark btn-xs' download></i> Download</a>-->
                        <!--    <?php  } else { ?>-->
                        <!--        <a href='' id='download' class='fa fa-download btn btn-dark btn-xs' style='display:none'  download ></i> Download</a>-->
                        <!--    <?php } ?>-->
                        <!--    </div>-->
                        <!--</form>-->
                    </div>

                </div>
            </div>
</div>
</div>
</section>
</div>

<div class='modal' id='uploadmodal'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-body'>
                <h4 style='display: inline;'> Upload Image </h4>
                <span id='btn_hide' style='float:right;font-size:15px;font-weight:bold'>X</span>
                <hr>
                <form id='uploadgpForm' enctype='multipart/form-data'>
                    <div class='form-group'>
                        <label>Upload Photograph (Max 100 KB)</label>
                        <input type='file' name='uploadgpimg' id='uploadgpimg' accept='image'>
                        <br><small> Only Jpg and Png image upto 100KB. </small>
                    </div>
                </form>
                <div id="display"></div>
                <div id="my_camera"></div>
                <form>
                    <input type=button value="Take Snapshot" onclick="take_snapshot2()">
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once('system/footer2.php'); ?>
<script>
    //===========UPLOAD IMAGES ==============//
$('#btn_hide').click(function(){
    $('#uploadmodal').css('display','none'); 
});
$('#uploadgpimg').change(function (){
	$("#uploadgpForm").submit();
});

$("#uploadgpForm").on('submit',(function(e){
	e.preventDefault();
	if($('#update_btn').length!=0)
			{
				$("#insert_btn").attr("disabled", true);
			}
	$.ajax({
	url: "system/master_process?task=upload_gatepass",
	type: "POST",
	data:  new FormData(this),
	contentType: false,
	cache: false,
	processData:false,
	success: function(data){
		console.log(data);
		//alert(data);
		var obj = JSON.parse(data);
		$("#targetimg").val(obj.id);
		$("#display").html("<img src='system/gatepass/"+obj.id +"' width='140px' height='160px' >");
		$.notify(obj.msg,obj.status);
		$("#update_btn").attr("disabled", false);
		$("#uploadmodal").hide();
	},
	error: function(){
		
	} 	        
	});
}));
</script>
