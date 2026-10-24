<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $emp = insert_row('employee');
    $id = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('employee', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
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
                <h1 class="box-title">Add Employee </h1>
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
                                    <div class="form-row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Employee Code <?= imp() ?></label>
                                                <input type="text" class="form-control" required name='emp_code' value='<?php echo $emp_code; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Employee name <?= imp() ?></label>
                                                <input type="text" class="form-control" required name='name' value='<?php echo $name; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Employee Type &nbsp;<a data-toggle="modal" data-target="#addCategory" title="Create New Employee type" style="cursor:pointer"><span class="badge badge-warning">+</span></a></label>
                                                <select name="emp_type" id="category" class="form-control" required>
                                                    <?php
                                                    $sql = "SELECT * FROM emp_cat WHERE cat_name NOT IN ('DRIVER') ";
                                                    $emp_category = direct_sql($sql)['data'];
                                                    foreach ($emp_category as $data) {
                                                        extract($data);
                                                        $cat_list[$cat_name] = $cat_name;
                                                    }
                                                    dropdown($cat_list, $emp_type); ?>
                                                </select>
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
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Blood Group</label>
                                                <select name="blood_group" id="blood_group" class="form-control">
                                                    <?php dropdown($bloodgroup_list, $blood_group); ?>
                                                </select>
                                            </div>
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
                                                <label>Allow Marks Entry</label>
                                                <select name="marks_entry" id="marks_entry" class="form-control">
                                                    <option value="NO"  <?php if($row['marks_entry'] == 'NO')  echo 'selected'; ?>>NO</option>
                                                    <option value="YES" <?php if($row['marks_entry'] == 'YES') echo 'selected'; ?>>YES</option>
                                                </select>

                                            </div>
                                          
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
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
                                            
                                            <div class="form-group">
                                                    <label for="">Full Address</label>
                                                    <textarea name="address" id="address" rows="6" class="form-control"><?php echo $address; ?></textarea>
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
                                            <div class="col-lg-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Qualification</label>
                                                    <input class="form-control" type='text' name='qualification' value='<?php echo $qualification; ?>'>
                                                </div>
                                                
                                                <div class="form-group">
                                                <label>Designation <?= imp() ?> </label>
                                                <input class="form-control" required name='designation' value='<?php echo $designation; ?>'>
                                            </div>

                                                <div class="form-group">
                                                    <label>Joining Date</label>
                                                    <input type="date" class="form-control" required name='doj' value='<?php echo $doj; ?>'>
                                                </div>

                                                <div class="form-group">
                                                    <label>Salary(in <i class="fa fa-inr fa-fw"></i> )</label>
                                                    <input class="form-control" type='number' name='salary' value='<?php echo $salary; ?>' min="1000" placeholder="0.00">
                                                </div>
                                                <?php if ($isedit == 'no') { ?>
                                                    <div class="form-group">
                                                        <label>Balance Amount</label>
                                                        <input class="form-control" type='number' name='balance' value='<?php echo $balance; ?>' placeholder="0.00">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-lg-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Allow Subject </label>
                                                    <?php
                                                    $all_subject_list = create_list_with_id('subject', 'subject_name');
                                                    checklist_with_id('allow_subject', $all_subject_list, $allow_subject, '165px'); ?>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Allow Classes </label>
                                                    <?php check_list('allow_class', active_class_list(), $allow_class, '165px'); ?>
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
                                            <div class="col-lg-6 col-sm-6">

                                            <div class="form-group">
                                                    <label>User Password</label>
                                                    <input class="form-control" name='password' value='' placeholder="">
                                                    <p class='text-info'> Leave It Blank if don't want to change</p>
                                                </div>


                                            </div>
                                            <div class="col-lg-6 col-sm-6">

                                              
                                                <div class="form-group">
                                                    <label>Status </label>
                                                    <select name='status' class='form-control'>
                                                        <?php dropdown($status_list, $status); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        </div>
    </section>

<!-- Modal for adding Category  -->
<!--<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">-->
<!--    <div class="modal-dialog" role="document">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>-->
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                    <span aria-hidden="true">&times;</span>-->
<!--                </button>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <form action="add_category" id="cat_frm">-->
<!--                    <div class="form-group">-->
<!--                        <label for="form-label">Category</label>-->
                        <?php
// <!--                        $cat = insert_row("emp_cat");
// <!--                        $id = $cat['id'];
                        ?>
<!--                        <input type="hidden" name="id" value="<?php echo $id; ?>" required>-->
<!--                        <input type="text" class="form-control" name="cat_name" required>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <label for="form-label">Status</label>-->
<!--                        <select name="status" id="status" required class="form-control">-->
<!--                            <?php dropdown($status_list, $status); ?>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                </form>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
<!--                <button type="button" class="btn btn-primary" id="cat_btn">Save</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Modal for photograph upload  -->
<div class='modal' id='uploadmodal'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-body'>
                <button type="button" class="close" onclick="$('#uploadmodal').hide();" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4> Upload Image </h4>
                <hr>
                <form id='uploadForm' enctype='multipart/form-data'>
                    <div class='form-group'>
                        <label>Upload Photograph (Max 100 KB)</label>
                        <input type='file' name='uploadimg' id='uploadimg' accept='image'>
                        <br><small> Only Jpg and Png image upto 100KB. </small>
                    </div>
                </form>
                <div id="display"></div>
                <div id="my_camera"></div>
                <form>
                    <input type=button value="Take Snapshot" onclick="take_snapshot()">
                </form>
            </div>
        </div>
    </div>
</div>

</div>
<?php require_once('system/footer2.php'); ?>
<script>
    function reset() {
        $("#update_frm").reset();
    }
</script>