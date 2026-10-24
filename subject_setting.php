<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('subject');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('subject', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Subject Setting</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Subject</a></li>
			<li class="breadcrumb-item active">Add Subject</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Subject Details</h3>

				<div class="box-tools pull-right">
					<a class='btn btn-info' href='subject_setting' title='Add Subject'><i class='fa fa-plus'></i> Add
						Subject </a>
					<button class="btn btn-success" id='update_btn'> Submit </button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<div class='row'>
					<div class='col-lg-2'></div>
					<div class='col-lg-4'>
						<form action='update_subject' id='update_frm' enctype='multipart/form-data'>
							<div class="form-group">
								<label>Subject Name</label>
								<input class="form-control" type='hidden' name='id' value='<?php echo $id; ?>' required>
								<input class="form-control" name='subject_name' value='<?php echo $subject_name; ?>'
									required>
							</div>
							<div class="form-group">
								<label>Subject Code</label>
								<input class="form-control" name='subject_code' value='<?php echo $subject_code; ?>'>
							</div>
							<div class="form-group">
								<label>Display Order (1,2..)</label>
								<input class="form-control" type='number' name='subject_order'
									value='<?php echo $subject_order; ?>' required min='0'>
							</div>
							<div class="form-group">
								<label>Subject Category</label>
								<select class="form-control" name='subject_type' required>
									<?php echo dropdown($subject_category_list, $subject_type); ?>'
								</select>
							</div>
							<div class="form-group">
								<label>Show in Report</label>

								<select class="form-control" name='show_in_report' required>
									<option value="YES" <?php if (!isset($show_in_report) || $show_in_report == 'YES')
										echo 'selected'; ?>>YES</option>
									<option value="NO" <?php if (isset($show_in_report) && $show_in_report == 'NO')
										echo 'selected'; ?>>NO</option>
								</select>
							</div>
							<div class="form-group">
								<label> Extra Subject </label>

								<select class="form-control" name='is_extra' required>
									<?php echo dropdown($extra_subject, $is_extra); ?>
								</select>
							</div>

					</div>
					<div class="col-lg-4 col-sm-6">
						<div class="form-group">
							<label> Class ( Multiple)</label>
							<?php check_list('student_class', $class_list, $student_class, '270px'); ?>
						</div>
						</form>
					</div>
				</div>
				<hr>
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>

							<tr class='op-thead'>
								<th> Serial No. </th>
								<th> Subject Type </th>
								<th> Subject Name </th>
								<th> Subject Code </th>
								<th> Is Extra </th>
								<th> Show in Report </th>
								<th> Class</th>
								<th> Action </td>
							</tr>
						</thead>
						<tbody>

							<?php

							if ($_SESSION['user_type'] == 'DEV') {
								//	$res = fetch_all('subject','*',['status'=>'AUTO,DELETED'],['sort'=>'subject_name','condition_type'=>' NOT IN ']);
								$res = get_all('subject', '*', ['status' => 'ACTIVE', 'branch_id' => $branch_id]);
							} else {
								$res = get_all('subject', '*', ['status' => 'ACTIVE', 'branch_id' => $branch_id]);
							}
							//echo $res['sql'];
							foreach ($res['data'] as $row) {
								$ord = $row['subject_order'];
								$id = $row['id'];
								echo "<tr><td>" . $row['subject_order'] . "</td>";
								echo "<td>" . $row['subject_type'] . "</td>";
								echo "<td>" . $row['subject_name'] . "</td>";
								echo "<td>" . $row['subject_code'] . "</td>";
								echo "<td>" . $row['is_extra'] . "</td>";
								
								$current_show = $row['show_in_report'] ?? 'YES';
								$icon_class = $current_show == 'YES' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger';
								echo "<td><i class='fa fa-2x $icon_class toggle_show_in_report' data-id='$id' data-val='$current_show' style='cursor:pointer;' title='Toggle Show in Report'></i></td>";
								
								echo "<td>" . $row['student_class'] . "</td>";
								?>
								<td>
									<a href='subject_setting.php?link=<?php echo encode('id=' . $id); ?>'
										class='fa fa-edit btn btn-info btn-xs'></a>
									<span class='delete_subject btn btn-danger btn-sm' data-table='subject'
										data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
								</td>
								</tr>
								<?php

							}
							?>


						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('system/footer2.php'); ?>
<script>
$(document).ready(function() {
    $(document).on('click', '.toggle_show_in_report', function() {
        var btn = $(this);
        var id = btn.data('id');
        var current_val = btn.data('val');
        var new_val = current_val == 'YES' ? 'NO' : 'YES';
        
        $.ajax({
            url: 'system/master_process.php?task=toggle_show_in_report',
            type: 'POST',
            data: {id: id, show_in_report: new_val},
            success: function(res) {
                var data = JSON.parse(res);
                if(data.status == 'success') {
                    btn.data('val', new_val);
                    if(new_val == 'YES') {
                        btn.removeClass('fa-toggle-off text-danger').addClass('fa-toggle-on text-success');
                    } else {
                        btn.removeClass('fa-toggle-on text-success').addClass('fa-toggle-off text-danger');
                    }
                } else {
                    alert("Error updating status");
                }
            },
            error: function() {
                alert("Server error occurred");
            }
        });
    });
});
</script>