<?php require_once('system/header.php'); ?>
<?php
require_once('system/menu.php');
if (isset($_GET['att_date'])) {
    $category = $_REQUEST['category'];
    $att_date = $_GET['att_date'];
} else {
    $att_date = date('Y-m-d');
}
?>
<style>
    [type=checkbox]+label{
        margin-bottom:-10px !important;
        margin-top:-10px !important;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Employee Attendance </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-9">
                        <b><span class="text-danger">Month</span> : <?php echo  date('M') ?> <span class="text-danger"> Year</span> : <?php echo date("Y"); ?></b> [<?php echo $category; ?>]
                    </div>
                    <div class="col-md-3 text-right">
                        
                                <form>
                                    <input type='date' name='att_date' value='<?php echo $att_date; ?>' id='att_date' max='<?php echo date('Y-m-d'); ?>' required>
                                   
                                    <button class="btn btn-dark ">Show</button>
                                </form>
                       
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-end" >
                            <!-- <span class='btn btn-border border-primary ' style="float:right"> -->
                                <!-- <input type="checkbox" id="selectall" onclick="selectAll(this)" /> Check All -->
                                <?php // check_all('selectall','chk','','Check All'); ?>
                            <!-- </span>&nbsp; -->
                            <!-- <button id='present_btn' class='btn btn-success btn-sm' title='Present All Checked Data' style="float:right; margin-right:8px;">Present</button> &nbsp; -->
                            <!-- <button id='abs_btn' class='btn btn-danger btn-sm' title='Absent All Checked Data' style="float:right; margin-right:8px;">Absent</button> &nbsp; -->
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                    <td colspan='3'></td>
                                    <td align='center'>
                                <?php
                                    check_all('selectall','chk','','Select All')
                                ?> 
                                </td>
                                <td>
                                    <button data-status ='P'  class='emp_att_btn btn bg-white border-success' ><i class="fa fa-check-circle" style="color: green;"></i> Present Mark As Present </button>
                                </td> 
                                <td>
                                    <button data-status ='A'   class='emp_att_btn btn bg-white border-danger' ><i class="fa fa-times-circle" style="color: red;"></i> Mark As Absent </button>
                                </td> 
                                <!-- <td>
                                    <button data-status ='L'  class='emp_att_btn btn bg-white border-primary' ><i class="fa fa-suitcase" style="color: blue;"></i> Marks As Leave </button>
                                </td>     -->
                                </tr>
                                    <tr>
                                        <th>#</th>
                                        <!--<th>Code</th>-->
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Designation</th>
                                        <th>Action</th>
                                        <th>Leave</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;           

                                        //$res = get_all('employee', '*', array('e_category' => $category));
                                        $res = get_all('employee','*', array('status'=>'ACTIVE')); 
                                        ?>
                                        <form action='system/master_process.php?task=make_emp_att' method='post' id='att_frm'>
                                        <?php
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                $id = $row['id'];
                                                $l_info = get_all('leave_details','*',['emp_id'=>$id],'id desc limit 1')['data']['0'];
                                                // $emp_type = $row['e_category'];
                                                echo "<tr>";
                                                echo "<td>" . $i . "</td>";
                                                //echo "<td>" . $row['e_code'] . "</td>";
                                                echo "<td>" . $row['name'] . "</td>";
                                                echo "<td>" . $row['emp_type'] . "</td>";
                                                echo "<td>" . $row['designation'] . "</td>";
                                                echo "<td width='185'>";
                                                $tbl_name = "employee_att";
                                                $col_name = 'd_' . date('j', strtotime($att_date));
                                                if (date('D', strtotime($att_date)) == 'Sun') {
                                                    echo "<script> alert('Selected Date is sunday');</script>";
                                                }
                                                $mvalue = remove_space(date('M_Y', strtotime($att_date)));
                                                $post = array('att_month' => $mvalue, 'emp_id' => $id);
                                                $sql = "SELECT * from $tbl_name where emp_id = $id and att_month like '$mvalue' ";
                                                $emp_att = direct_sql($sql);
                                                if ($emp_att['count'] == 0) {
                                                    insert_data($tbl_name, $post);
                                                }
                                                if ($emp_att['data'][0][$col_name] == 'P') {                                                   
                                                    echo "<span class='badge bg-success p-2'>P</span>";
                                                } else if ($emp_att['data'][0][$col_name] == 'A') {
                                                    echo "<span class='badge bg-danger p-2'>A</span>";
                                                } else if ($emp_att['data'][0][$col_name] == 'L') {
                                                    echo "<span class='badge bg-warning p-2'>L</span>";
                                                } else if ($emp_att['data'][0][$col_name] == 'H') {
                                                    echo "<span class='badge bg-primary p-2'>H</span>";
                                                } else {
                                                    // echo "<input data-emp='$emp_type' type='checkbox' value ='$id' name='sel_id[]' class='chk'>&nbsp;";
                                                    att_check("sel_$id",$id,'chk');                                                   
                                                }
                                                echo "</td>";
                                                echo "<td>";
                                                    if ($emp_att['data'][0][$col_name] != 'L') {
                                                        echo "<span><a data-id='$id' class='btn btn-border border-danger text-danger' data-target='#addLeave' data-toggle='modal' id='add_leave_btn'>Add Leave</a></span>";
                                                        };
                                                    if ($emp_att['data'][0][$col_name] == 'P') {
                                                         echo "<button type='button' data-id='$id' class='btn btn-info' data-toggle='modal' data-target='#halfDay' id='add_half_day' style='margin-left:10px'>Half Day</button>";
                                                        }else if($emp_att['data'][0][$col_name] == 'H'){
                                                        echo "<span><a href='emp_gate_pass.php?id=".$l_info['id']."' class='btn btn-border border-primary text-success' style='margin-left:10px'>Download</a></span>";
                                                        };
                                                    "</td>";
                                                echo "</tr>";
                                                $i++;
                                            }
                                        }
                                    
                                        ?>
                                        </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal for Leave Starts-->
<div class="modal fade" id="addLeave" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog w-90" role="document">
        <div class="modal-content">
            <!--<div class="modal-body"> -->
            <div class="card rounded">
                <div class="card-header">
                    <h3 class="text-center">Add Leave</h3>
                </div>
                <div class="card-body">
                    <?php
                    // $leave = insert_row("leave_details");
                    $lid = $leave['id'];
                    ?>
                    <form action="leave_details" id="insert_frm" method="POST">
                        <!-- <div class="form-group">
                            <label for="leave_type">Leave Type</label>
                            <select name="leave_type" id="leave_type" class="form-control" required>
                                <option value="">--Select Leave Type--</option>
                                <?php dropdown($leave_type_list, $leave_type); ?>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="leave_cause">Leave Cause (upto 100 words)</label>
                            <input type="hidden" name="id" value="<?php echo $lid; ?>">
                            <input type="hidden" name="emp_id" id="emp_id">
                            <textarea type="text" name="leave_cause" id="leave_cause" class="form-control" required></textarea>
                        </div>
                         <div class="form-group">
                            <input type="hidden" name="leave_app" id="leave">
                        </div> 
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <?php dropdown($status_list, $status); ?>
                            </select>
                        </div>
                    </form>
                     <form id="uploadLeaveApp" enctype="multipart/form-data">
                        <div id="displayLeaveApp"></div>
                        <label class="form-label">Upload Application</label>
                        <input type="file" class="form-control" name="uploaddoc" id="leave_app">
                    </form>
                </div>
                <!-- </div>
            </div> -->
                <div class="card-footer p-2 text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insert_btn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="halfDay" tabindex="-1" role="dialog" aria-labelledby="halfDayTitle" aria-hidden="true">
    <div class="modal-dialog w-90" role="document">
        <div class="modal-content">
            <div class="card rounded">
                <div class="card-header">
                    <h3 class="text-center">Mark Half Day</h3>
                </div>
                <div class="card-body">
                    <form action="half_day_leave" id="insert_form" method="POST">
                        <div class="form-group">
                            <label for="from_date">Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="leave_cause">Leave Cause (upto 100 words)</label>
                            <input type="hidden" name="id" value="<?php echo $hid; ?>">
                            <input type="hidden" name="emp_id" id="h_emp_id">
                            <textarea type="text" name="leave_cause" id="leave_cause" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="remarks">In Time</label>
                            <input type="time" name="in_time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="remarks">Out Time</label>
                            <input type="time" name="out_time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <?php dropdown($status_list, $status); ?>
                            </select>
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
                                <input type='hidden' name='photo' id='targetimg' class='form-control' readonly>
                                <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE PHOTO </span>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
                <div class="card-footer p-2 text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insert_button">Save changes</button>
                </div>
            </div>
        </div>
    </div>

<div class='modal' id='uploadmodal'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-body'>
                <h4> Upload Image </h4>
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
<!-- Modal Ends -->

<?php require_once('system/footer2.php'); ?>
<script>
    //========== SELECT ALL CHECK BOX WITH PRESENT =======//

// Select/Deselect all checkboxes
function selectAll(source) {
    const checkboxes = document.getElementsByName('sel_id[]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });
}
$(document).on('click', '#add_leave_btn', function () {
    let emp_id = $(this).data("id");
    $("#emp_id").val(emp_id);
});

$(document).on('click', '#add_half_day', function () {
    let emp_id = $(this).data("id");
    $("#h_emp_id").val(emp_id);
});


    //========================UPLOAD LEAVE APPLICATION=======================
    $('#leave_app').change(function() {
        $("#uploadLeaveApp").submit();
    });

    $("#uploadLeaveApp").on('submit', (function(e) {
        e.preventDefault();
        $.ajax({
            url: "system/master_process?task=upload_doc",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                var obj = JSON.parse(data);
                $("#leave").val(obj.id);
                // $("#displayLeaveApp").html("<img src='system/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
                var fileUrl = "system/upload/" + obj.id;
                var extension = obj.id.split('.').pop().toLowerCase();
                var html = "";
                
                if (["jpg", "jpeg", "png", "gif"].includes(extension)) {
                    html = "<img src='" + fileUrl + "' width='100px' height='100px' class='img-thumbnail'>";
                } else if (extension === "pdf") {
                    html = "<iframe src='" + fileUrl + "' width='100px' height='100px' style='border:1px solid #ccc;'></iframe>";
                } else {
                    html = "<a href='" + fileUrl + "' target='_blank'>Download File</a>";
                }
                $("#displayLeaveApp").html(html);

                $.notify(obj.msg, obj.status);
            },
            error: function() {}
        });
    }));


$(document).on('click', "#insert_button", function(event){
		var task= $("#insert_form").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data  =$("#insert_form").serialize();
		$.ajax({
			'type':'POST',
			'url':'system/master_process.php?task='+task,
			'data':data,
			success: function(data){
				console.log(data);
				//alert(data);
			var obj = JSON.parse(data);
			$('#insert_form')[0].reset();
			$("#insert_button").html("Save Details");
			$("#insert_button").removeAttr("disabled");
			if(obj.url!= null)
				{
					bootbox.alert(obj.msg, function(){ 
						window.location.replace(obj.url);
					});	
				}
				else{
					$.notify(obj.msg, obj.status);
				}
			}

		});
});

</script>