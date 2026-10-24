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
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
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
              <form  style='display:inline'>
                 <input type='month' name="att_month" value='<?= $sel_month?>' required>
                <button class="btn btn-orange"> Show </button>
                <button class="btn btn-success" id='insert_btn' title='Save Data' style="margin-right:8px;"> Save <i class='fa fa-save'></i>  </button>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
  <div class="table-responsive">
    <form action="pay_salary" method="post" id="insert_frm">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Emp. Code</th>
            <th>Name</th>
            <th>Emp. Type</th>
            <th>Payable Amount</th>
            <th>Pay Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $res = get_all('salary', '*', ['month' => $sel_month, 'branch_id' => $_SESSION['branch_id']]);
          if ($res['count'] > 0) { 
            foreach ($res['data'] as $row) {  
              $emp_data = get_data('employee', $row['emp_id'], null)['data']; 
              if ($sel_month != '') {
                $balance = $emp_data['balance'] + $row['payable_amount'];
          ?>
            <tr>
              <td><?php echo $row['emp_id']; ?></td>
              <td><?php echo $emp_data['name']; ?></td>
              <td><?php echo $emp_data['emp_type']; ?></td>
              <td><?php echo $balance; ?></td>
              <td>
                <input 
                  type="text" 
                  name="paid_amount[]" 
                  value="<?php echo $balance; ?>" 
                  style="width: 60px;"
                >
              </td>
              <input type="hidden" name="month[]" value="<?= $sel_month; ?>">
              <input type="hidden" name="id[]" value="<?= $row['id']; ?>">
              <input type="hidden" name="emp_id[]" value="<?= $row['emp_id']; ?>">
              <input type="hidden" name="balance[]" value="<?= $balance; ?>">
            </tr>
          <?php 
              }
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
    //=========SELECT ALL CHECK BOX WITH SAME NAME=======//
    function selectAll(source) {
        checkboxes = document.getElementsByName('sel_id[]');
        for (var i in checkboxes)
            checkboxes[i].checked = source.checked;
    }
</script>