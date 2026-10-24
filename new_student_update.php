<?php
require_once('system/header.php');
require_once('system/menu.php');

extract(post_clean($_GET));

$student_class = $student_class ?? '';
$student_section = $student_section ?? '';
$status = $status ?? 'ACTIVE';
$filters = [];

if (!empty($_GET['link'])) {
	$data = decode($_GET['link']);
	$student_class = $data['student_class'] ?? $student_class;
	$student_section = $data['student_section'] ?? $student_section;
}

if (!empty($_GET['status']))
	$status = $_GET['status'];
if ($status != '')
	$filters['status'] = $status;
if (!empty($_GET['student_class'])) {
	$student_class = $_GET['student_class'];
	$filters['student_class'] = $student_class;
}
if (!empty($_GET['student_section'])) {
	$student_section = $_GET['student_section'];
	$filters['student_section'] = $student_section;
}

if (!empty($filters)) {
	$res = get_all('student', '*', $filters);
} else {
	$branch_id = (int) $_SESSION['branch_id'];
	$res = direct_sql("SELECT * FROM student WHERE status NOT IN ('AUTO','DELETED','BLOCKED') AND branch_id='{$branch_id}' LIMIT 50");
}

if (isset($_POST['task']) && $_POST['task'] == 'new_roll') {
	$roll_message = ($student_class != '' && $student_section != '')
		? new_roll_no($student_class, $student_section) . ' Student Roll Updated'
		: 'Please select Class and Section first.';
}
?>

<div class="content-wrapper">
	<section class="content-header">
		<h1>Manage Student</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Student</li>
			<li class="breadcrumb-item active">Manage Student</li>
		</ol>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-12">
				<div class="box">

					<div class="box-header with-border">

						<form method="post">
							<input type="hidden" name="task" value="new_roll">
							<h3 class="box-title">
								Manage Student
								<button type="submit" class="btn btn-success btn-sm">ASSIGN NEW ROLL</button>
								<?php if (!empty($roll_message)) { ?>
									<span style="margin-left:10px;color:#28a745;font-size:14px;">
										<?= htmlspecialchars($roll_message) ?>
									</span>
								<?php } ?>
							</h3>
						</form>

						<div class="box-tools pull-right">
							<form method="get">

								<select name="student_class">
									<?php dropdown($class_list, $student_class); ?>
								</select>

								<select name="student_section">
									<?php dropdown($section_list, $student_section); ?>
								</select>

								<select name="status">
									<?php dropdown($status_list, $status); ?>
								</select>

								<button type="submit" class="btn btn-orange">Show</button>

							</form>
						</div>

					</div>

					<div class="box-body">
						<div class="table-responsive">

							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr class="op-thead">
										<th>Photo</th>
										<th>Adm. No.</th>
										<th>Name</th>
										<th>Father's Name</th>
										<th>Class - Section</th>
										<th>Roll</th>
										<th>Mobile No</th>
										<th>House Name</th>
										<th>Urdu</th>
										<th>Height</th>
										<th>Weight</th>
										<th class="text-right">Action</th>
									</tr>
								</thead>

								<tbody>

									<?php if (!empty($res['count'])) {
										foreach ($res['data'] as $row) {
											$id = (int) $row['id'];
											?>

											<tr>

												<td>
													<img src="<?= $base_url . 'upload/' . $row['student_photo'] ?>"
														style="height:50px;width:50px;border-radius:50%;object-fit:cover;">
												</td>

												<td><?= htmlspecialchars($row['student_admission'] ?? '') ?></td>

												<td><?= htmlspecialchars($row['student_name'] ?? '') ?></td>

												<td><?= htmlspecialchars($row['student_father'] ?? '') ?></td>

												<td>
													<?= htmlspecialchars($row['student_class'] ?? '') ?> -
													<?= htmlspecialchars($row['student_section'] ?? '') ?>
												</td>

												<td><?= htmlspecialchars($row['student_roll'] ?? '') ?></td>

												<td><span
														class="mobile"><?= htmlspecialchars($row['student_mobile'] ?? '') ?></span>
												</td>

												<td><?= htmlspecialchars($row['house_name'] ?? '') ?></td>

												<td class="text-center">
													<input type="checkbox" class="student-health" data-id="<?= $id ?>"
														data-field="urdu" <?= (($row['urdu'] ?? '') == '1') ? 'checked' : '' ?>>
												</td>

												<td>
													<input type="text" class="form-control student-health" data-id="<?= $id ?>"
														data-field="height"
														value="<?= htmlspecialchars($row['height'] ?? '') ?>"
														style="width:70px;">
												</td>

												<td>
													<input type="text" class="form-control student-health" data-id="<?= $id ?>"
														data-field="weight"
														value="<?= htmlspecialchars($row['weight'] ?? '') ?>"
														style="width:70px;">
												</td>

												<td class="text-right">

													<?= btn_view('student', $id, $row['student_name']); ?>

													<?php if (($row['status'] ?? '') == 'ACTIVE') {
														echo btn_edit('update_student', $id);
													} ?>

													<?php if (
														isset($user_type) &&
														($user_type == 'ADMIN' || $user_type == 'DEV') &&
														($row['status'] ?? '') != 'ACTIVE'
													) {
														echo btn_edit('update_student', $id);
													} ?>

												</td>

											</tr>

										<?php }
									} ?>

								</tbody>
							</table>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
	$(function () {
		var timers = {};

		function save(el) {
			var $el = $(el),
				id = $el.data('id'),
				field = $el.data('field'),
				value = $el.is(':checkbox') ? ($el.prop('checked') ? '1' : '0') : $.trim($el.val()),
				key = id + '_' + field;

			clearTimeout(timers[key]);

			$.ajax({
				url: 'system/master_process.php?task=update_student_health',
				type: 'POST',
				dataType: 'json',
				data: { id: id, field: field, value: value },
				success: function (r) {
					if (r.status == 'success') {
						$el.css('background', '#d4edda');
						setTimeout(function () { $el.css('background', ''); }, 700);
					} else {
						$el.css('background', '#f8d7da');
					}
				},
				error: function () {
					$el.css('background', '#f8d7da');
				}
			});
		}

		$('#example').on('change', '.student-health[type="checkbox"]', function () {
			save(this);
		});

		$('#example').on('input', '.student-health:not([type="checkbox"])', function () {
			var el = this,
				key = $(el).data('id') + '_' + $(el).data('field');

			clearTimeout(timers[key]);

			timers[key] = setTimeout(function () {
				save(el);
			}, 500);
		});

		$('#example').on('blur', '.student-health:not([type="checkbox"])', function () {
			save(this);
		});
	});
</script>