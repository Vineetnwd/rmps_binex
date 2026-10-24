<?php require_once("system/header.php");	?>
<?php require_once("system/menu.php");
$filter['status'] = 'ACTIVE';
$student_class = $student_section = '';
if (isset($_POST['student_class']) and $_POST['student_class'] != '') {
	$filter['student_class'] = $student_class = $_POST['student_class'];
}
if (isset($_POST['student_section']) and $_POST['student_section'] != '') {
	$filter['student_section'] = $student_section = $_POST['student_section'];
}
?>
<title> <?php echo $inst_name; ?> : Student Monthly Report </title>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

	body {
		color: #000;
		padding: 0px;
		font-family: 'Roboto', sans-serif;
		font-size: 12;
		margin: auto;
	}

	.inner_box {
		margin: 10px auto;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Month Wise Student Payment </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Fee</a></li>
			<li class="breadcrumb-item active"> Month Wise Payment</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Month Wise Payment Report</h3>

				<div class="box-tools pull-right">
					<form method='post'>
						<select name='student_class' required>
							<?php dropdown($class_list, $student_class); ?>
						</select>
						<select name='student_section'>
							<?php dropdown($section_list, $student_section); ?>
						</select>
						<button class='btn btn-orange'> Show </button>
					</form>
				</div>
			</div>
			<div class='box-body'>
				<div class='table-responsive'>
					<table cellspacing='0' rules='all' border='1'>
						<thead>
							<tr class='bg-gray'>
								<td colspan='18' align='center'> <?= $inst_name  ?> : All Student Month Wise Payment Report</td>
							</tr>
							<tr class='op-thead'>
								<td>Sr. No.</td>
								<td>Student Name</td>
								<td>Admission No.</td>
								<td>Current Dues</td>
								<?php foreach ($month_list as $month) {
									echo "<td class='p-2'> $month </td>";
								}
								?>
								<td>Total Paid</td>
							</tr>
						</thead>
						<tbody>
							<?php
							extract($_POST);

							$res = get_all('student', '*', $filter, ' student_class, student_section, student_roll ');
							$i = 1;
							$tcollection = 0;
							if ($res['count'] > 0) {
								foreach ($res['data'] as $student) {
									$student_id = $student['id'];

									$student = get_data('student', $student_id)['data'];
									$student_fee  = get_data('student_fee', $student_id, null, 'student_id')['data'];

									$row_span = 1;
									$ttype = ($student['student_type'] == 'GENERAL') ? '' : " <b>[" . $student['student_type'] . "]</b>";
									$bg_color = ($student['student_type'] == 'GENERAL') ? '' : "#cdf293";

									echo "<tr bgcolor='$bg_color'>";
									echo "<td rowspan='$row_span' > $i </td>";
									echo "<td rowspan='$row_span' > {$student['student_name']} </td>";
									echo "<td rowspan='$row_span' >" . $student['student_admission'] . "<br><small>" . $student['student_class'] . "-" . $student['student_section'] . $ttype . "</small></td>";


									echo "<td bgcolor='lightyellow'> {$student_fee['current_dues']} </td>";
									$all_rcpt = get_all('receipt', '*', ['student_id' => $student_id, 'status' => 'PAID']);
									$tct = 0;
									$tpaid = 0;
									foreach ((array)$all_rcpt['data'] as $rcpt) {
										$paid_date  = $rcpt['paid_date'];
										$paid_amount  = $rcpt['paid_amount'];
										$mode  = $rcpt['payment_mode'][0];
										$tpaid += $paid_amount;
										$ct  = count(explode(",", $rcpt['paid_month']));
										$tct += $ct;
										echo "<td colspan='$ct' align='center' ><small> " . date('d-M-Y', strtotime($paid_date)) . "<br> [" . $mode . "] " . $paid_amount . "</small></td>";
									}
									$empty = 13 - $tct;
									if ($empty > 0) {
										echo "<td colspan='$empty' ></td>";
									}
									echo "<td> $tpaid </td>";


									echo "</tr>";

									$i++;
									$tcollection = $tcollection + $tpaid;
								}
							}
							?>

							<tr class='bg-gray'>
								<td colspan='17'> Total </td>
								<td><?= $tcollection ?></td>
							</tr>

						</tbody>

					</table>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once("system/footer.php");	?>