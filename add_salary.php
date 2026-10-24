<?php 
require_once('system/header.php');
require_once('system/menu.php'); 

extract(post_clean($_GET));
$table_name = 'salary';

if (isset($_GET['att_month'])) {
    $sel_month = $_GET['att_month'];
}

if (isset($_GET['link']) && $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
} else {
    $item = insert_row($table_name);  
    $id = $item['id'];
}

if (!isset($payment_date)) {
    $payment_date = date("Y-m-d");
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Salary</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item">Employee management</li>
      <li class="breadcrumb-item active">Manage Salary</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
  <div class="row">
    <div class="col-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Manage Salary</h3>
          <div class="box-tools pull-right">
          <?php if (! isset($sel_month) and  $sel_month== '') { ?>
            <form style="display: inline;">
              <input type='month' name="att_month" value='<?= $sel_month?>' required>
              <button class="btn btn-orange">Show</button>
            </form>
            <?php } else { ?>
            <span class="btn btn-success" id="insert_btn" style="display: inline;">Save</span>
            <?php } ?>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <form id="insert_frm" action="update_salary" method="post">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Emp. Code</th>
                    <th>Name</th>
                    <th>Emp. Type</th>
                    <th>Balance</th>
                    <th>Salary</th>
                    <?php if ($sel_month != '') { ?>
                      <th>Absent</th>
                      <th>Leave</th>
                      <th>Present</th>
                      <th>Amount</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $res = get_all('employee', '*', ['status' => 'ACTIVE','branch_id'=> $_SESSION['branch_id']]);
                    if ($res['count'] > 0) {
                      foreach ($res['data'] as $row) { 
                  ?>
                    <tr>
                      <td><?php echo $row['emp_code']; ?></td>
                      <td><?php echo $row['name']; ?></td>
                      <td><?php echo $row['emp_type']; ?></td>
                      <td>
                        <input 
                          type="text" 
                          name="balance[]" 
                          value="<?php echo $row['balance']; ?>" 
                          style="width: 60px; color: #ccc;" 
                          readonly
                        >
                      </td>
                      <td>
                        <input 
                          type="text" 
                          name="salary[]" 
                          value="<?php echo $row['salary']; ?>" 
                          style="width: 60px; color: #ccc;" 
                          readonly
                        >
                      </td>
                      <?php if ($sel_month != '') { ?>
                        <td>
                          <input 
                            type="text" 
                            class='text-danger'
                            name="total_absent[]" 
                            value="<?php echo get_emp_abs_attendance($row['id'], $sel_month); ?>" 
                            style="width: 60px; color: #ccc;" 
                            readonly
                          >
                        </td>
                        <td>
                          <input 
                            type="text" 
                            name="total_leave[]" 
                            value="<?php echo get_emp_leave_attendance($row['id'], $sel_month); ?>" 
                            style="width: 60px; color: #ccc;" 
                            readonly
                          >
                        </td>
                        <td>
                          <input 
                            type="number" 
                            min=0
                            name="total_present[]" 
                            value="<?= date('t', strtotime($sel_month)); ?>" 
                            max="<?= date('t', strtotime($sel_month)); ?>" 
                            style="width: 60px;" 
                            
                          >
                        </td>
                        <td>
                          <input 
                            type="text" 
                            name="payable_amount[]" 
                            value="<?php echo $row['salary']+$row['balance']; ?>" 
                            style="width: 60px;"
                          >
                        </td>
                        <input type="hidden" name="month" value="<?= $sel_month; ?>">
                        <input type="hidden" name="branch_id" value="<?= $row['branch_id']; ?>">
                        <input type="hidden" name="emp_id[]" value="<?= $row['id']; ?>">
                      <?php } ?>
                    </tr>
                  <?php
                      } 
                    }
                  ?>
                </tbody>
              </table>
            </form>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php require_once('system/footer2.php'); ?>

<script>
$(document).ready(function () {
  $('input[name="total_present[]"]').on('input', function () {
    const $row = $(this).closest('tr'); // Get the current row
    const $salaryInput = $row.find('input[name="salary[]"]');
    const $absentInput = $row.find('input[name="total_absent[]"]');
    const $amountInput = $row.find('input[name="payable_amount[]"]');
    const selMonth = $('input[name="att_month"]').val(); // Get selected month

    // Calculate total days in the month dynamically
    const totalDaysInMonth = selMonth ? new Date(selMonth.split("-")[0], selMonth.split("-")[1], 0).getDate() : 30;

    // Parse present days
    let totalPresent = parseInt($(this).val(), 10);
    if (isNaN(totalPresent) || totalPresent < 0 || totalPresent > totalDaysInMonth) {
      totalPresent = Math.max(0, Math.min(totalPresent, totalDaysInMonth));
      $(this).val(totalPresent); // Correct the input if invalid
    }

    // Parse salary
    const salary = parseFloat($salaryInput.val() || 0);
    const totalAbsent = totalDaysInMonth - totalPresent;

    // Update absent input value
    $absentInput.val(totalAbsent);

    // Calculate payable amount
    const payableAmount = (salary / totalDaysInMonth) * totalPresent;
    $amountInput.val(payableAmount.toFixed(2));
  });
});


$(document).ready(function () {
    $('input[name="total_present[]"]').on('input', function () {
        updatePAmount(this);
    });
});

</script>
