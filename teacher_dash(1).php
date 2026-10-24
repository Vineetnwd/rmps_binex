<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
if ($user_type == 'ACCOUNT') {
	echo "<script> window.location ='acc_dash'  </script>";
}
if ($user_type == 'STAFF') {
	echo "<script> window.location ='studyplant'  </script>";
}
?>
<style>
.dashboard-menu {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* ✅ exactly 3 per row */
    gap: 20px;
    padding: 20px;
}

@media (max-width: 768px) {
    .dashboard-menu {
        grid-template-columns: repeat(2, 1fr); /* 2 per row on tablets */
    }
}

@media (max-width: 480px) {
    .dashboard-menu {
        grid-template-columns: 1fr; /* 1 per row on phones */
    }
}


.dashboard-item {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: #333;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.dashboard-item:hover {
    background: #ffe57f;
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.icon {
    font-size: 28px;
    color: #f9a825;
    margin-bottom: 10px;
    display: block;
}

.icon-img {
    width: 32px;
    height: 32px;
    margin-bottom: 10px;
}

.dashboard-item span {
    display: block;
    font-size: 14px;
    font-weight: 600;
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Dashboard
			<?php if ($user_type == 'ADMIN' or $user_type == 'DEV') { ?>
				<small>
					<a class="btn btn-dark" href="acc_dash"> Account </a>
					<a class="btn btn-success" target="_blank" href="summary.php?txn_date=<?=$today?>"> Daily Summary </a>
				</small>
			<?php } ?>
		</h1>
		<!--<ol class="breadcrumb">-->
		<!--	<form action="system/master_process.php?task=switch_branch" method="post" role="form" id="branch_form">-->
		<!--		<input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">-->
		<!--		<select name="branch_id" class="form-control float-left" id="branch_select">-->
		<!--			<?= dropdown_with_key(branch_list(), $_SESSION['branch_id']); ?>-->
		<!--		</select>-->
		<!--	</form>-->

		<!--	<form action="system/master_process.php?task=change_session" id="session_form">-->
		<!--		<select name="session_year" class="form-control float-left" id="session_select">-->
		<!--			<?php dropdown_with_key($session_list, $db_name); ?>-->
		<!--		</select>-->
		<!--	</form>-->
		<!--</ol>-->
	</section><!-- 📁 Dashboard Menu -->
<div class="dashboard-menu">
    
	<?php 
	    $emp = get_data('employee',strtoupper($_SESSION['user_name']),null,'emp_code')['data'];
	    if($emp['designation']=='NTT'){
	?>
        <a href="add_pre_marks" class="dashboard-item">
            <i class="fa fa-pencil icon"></i>
            <span>Montessory Tabulation</span>
        </a>
    <?php } else { ?>
        <a href="add_tabulation_marks" class="dashboard-item">
            <i class="fa fa-pencil icon"></i>
           <span>Marks Tabulation</span>
        </a>
    <?php } ?>
    <a href="teacher_st_att" class="dashboard-item">
        <img src="assets/svg/att_calendar.svg" class="icon-img" />
        <span>Student Attendance</span>
    </a>
    
    <a href="student_monthwise_att_report" class="dashboard-item">
        <img src="assets/svg/att_calendar.svg" class="icon-img" />
        <span>Monthwise Attendance</span>
    </a>
    
    <a href="emp_own_att" class="dashboard-item">
        <img src="assets/svg/att_calendar.svg" class="icon-img" />
        <span>Own Att. Report</span>
    </a>
    
    <a href="leave_apply" class="dashboard-item">
        <img src="assets/svg/emp.svg" class="icon-img" />
        <span>Leave Apply</span>
    </a>
    
    <a href="home_work" class="dashboard-item">
        <img src="assets/svg/education.svg" class="icon-img" />
        <span>Manage Homework</span>
    </a>
</div>

</div>
<!-- /.content-wrapper -->

<?php require_once('system/footer2.php'); ?>