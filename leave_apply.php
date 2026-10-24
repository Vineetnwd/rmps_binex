<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); 
if($user_type=='TEACHER'){
?>


<div class="content-wrapper">
  <section class="content-header">
    <h1>Own Attendance Report</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item active">Own Attendance Report</li>
    </ol>
  </section>

  <section class="content">
    <div class="box box-default">
      <?php 
        //   print_r($_SESSION);
          $emp_info = get_data('employee',strtoupper($_SESSION['user_name']),null,'emp_code')['data'];
          $emp_leaves = get_all('leave_details','*',['emp_id'=>$emp_info['id']]);
      ?>
      <div class="box-body">
        <div class="calendar-container row" style='margin-left: 0px;'>
          <!-- Calendar -->
          
            <div class="card rounded col-md-4" style='border:1px solid #ddd'>
                <div class="card-header">
                    <h3 class="text-center">Apply Leave</h3>
                </div>
                <div class="card-body">
                    <?php
                    // $leave = insert_row("leave_details");
                    $lid = $leave['id'];
                    ?>
                    <form action="leave_apply" id="insert_frm" method="POST">
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
                            <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp_info['id']; ?>">
                            <textarea type="text" name="leave_cause" id="leave_cause" class="form-control" required></textarea>
                        </div>
                        <!-- <div class="form-group">
                            <input type="hidden" name="leave_app" id="leave">
                        </div> -->
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label for="status">Status</label>-->
                        <!--    <select name="status" id="status" class="form-control" required>-->
                                <?//php dropdown($status_list, $status); ?>
                        <!--    </select>-->
                        <!--</div>-->
                    </form>
                    <!-- <form id="uploadLeaveApp" enctype="multipart/form-data">
                        <div id="displayLeaveApp"></div>
                        <label class="form-label">Upload Application</label>
                        <input type="file" class="form-control" name="leave_app" id="leave_app">
                    </form> -->
                </div>
                <!-- </div>
            </div> -->
                <div class="card-footer p-2 text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insert_btn">Save changes</button>
                </div>
            </div>
            <div class='col-md-8'>
                <table id="data_tbl" class="table table-hover" cellspacing="0" width="100%">
                    <tr style='background:#eee'>
                        <th>#</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Cause</th>
                        <th>Status</th>
                    </tr>
                <?php
                $i=1;
                    foreach($emp_leaves['data'] as $emp_leave){
                ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?= $emp_leave['from_date'] ?></td>
                        <td><?= $emp_leave['to_date'] ?></td>
                        <td><?= $emp_leave['leave_cause'] ?></td>
                        <td><?= $emp_leave['status'] ?></td>
                    </tr>
                <?php
                $i++;
                    }
                ?>
                </table>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
    }
require_once('system/footer2.php'); ?>
