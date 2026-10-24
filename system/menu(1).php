<style>
	/* ===== SIDEBAR BACKGROUND ===== */
	.main-sidebar {
		background: #0f172a;
		/* modern dark blue */
	}

	/* ===== MENU TEXT STYLE ===== */
	.sidebar-menu li a {
		color: #ffffff !important;
		font-weight: 400;
		font-size: 13px;
		letter-spacing: 0.3px;
		padding: 10px 15px;
		transition: all 0.3s ease;
	}

	/* ===== HOVER EFFECT (GLOW) ===== */
	.sidebar-menu li a:hover {
		background: linear-gradient(90deg, #1d4ed8, #3b82f6);
		color: #ffffff !important;
		box-shadow: 0 0 12px rgba(59, 130, 246, 0.8);
		transform: translateX(5px);
		border-radius: 6px;
	}

	/* ===== ACTIVE MENU ===== */
	.sidebar-menu li.active>a {
		background: linear-gradient(90deg, #2563eb, #60a5fa);
		color: #ffffff !important;
		font-size: 14px;
		font-weight: 400;
		box-shadow: 0 0 14px rgba(96, 165, 250, 0.9);
		border-radius: 6px;
	}

	/* ===== TREEVIEW MENU ===== */
	.treeview-menu {
		background: #020617;
		padding-left: 10px;
	}

	.treeview-menu li a {
		font-size: 12px;
		opacity: 0.95;
	}

	/* ===== TREEVIEW HOVER ===== */
	.treeview-menu li a:hover {
		background: linear-gradient(90deg, #06b6d4, #3b82f6);
		box-shadow: 0 0 10px rgba(6, 182, 212, 0.7);
	}

	/* ===== USER PANEL TEXT ===== */
	.user-panel .info p,
	.user-panel .info a {
		color: #ffffff;
		font-weight: 600;
	}

	/* ===== SEARCH BOX ===== */
	.sidebar-form input {
		background: #1e293b;
		border: none;
		color: #fff;
	}

	.sidebar-form input::placeholder {
		color: #cbd5f5;
	}

	.sidebar-form .btn {
		background: #3b82f6;
		color: #fff;
	}

	.sidebar-footer a:hover {
		color: #3b82f6;
		transform: scale(1.2);
	}
</style>
<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="image float-left">
				<img src="<?= $base_url ?>/assets/images/user2-160x160.jpg" class="rounded-circle" alt="User Image">
			</div>
			<div class="info float-left">
				<p>Welcome <?php echo $user_name; ?></p> <a href="#"><i
						class="fa fa-circle text-success"></i><?php echo $user_type; ?></a>
			</div>
			<?php if ($user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'ACCOUNT') { ?>
				<form action="collect_fee" method="get" class="sidebar-form">
					<!--<input name="student_class" type='hidden'>-->
					<input name="search_by" type='hidden' value='student_all'>
					<div class="input-group">
						<input type="text" name="search_text" class="form-control" id='search_text'
							placeholder="Student Name"> <span class="input-group-btn"> <button type="submit" name="search"
								id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i> </button> </span>
					</div>
				</form>
			<?php } ?>
		</div>
		<ul class="sidebar-menu" data-widget="tree" id='nav'>
			<?php if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'PRO') { ?>
				<li>
					<a href="dashboard"> <img src="assets/svg/dashboard.svg" width='28' /> Dashboard</a>
				</li>
			<?php } else if ($user_type == 'ACCOUNT') { ?>
					<li>
						<a href="acc_dash"> <img src="assets/svg/dashboard.svg" width='28' /> Dashboard</a>
					</li>
			<?php } else if ($user_type == 'TEACHER') { ?>
						<li>
							<a href="teacher_dash"> <img src="assets/svg/dashboard.svg" width='28' /> Dashboard</a>
						</li>
			<?php } ?>


			<?php if ($user_type == 'Account' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'ACCOUNT') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/wallet.svg" width='28' /> <span>Fee Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="collect_fee" accesskey='c'>
								<akey>C</akey>ollect Fee
							</a>
						</li>
						<li> <a href="collection_report" accesskey='s'>
								<akey>S</akey>how Collection
							</a>
						</li>
						<li> <a href="generate_demand" accesskey='d'>
								<akey>D</akey>emand Print
							</a>
						</li>
						<li> <a href="route_demand" accesskey='r'>
								<akey>R</akey>oute wise Demand
							</a>
						</li>
						<li> <a href="class_wise_ledger"> Class Wise Ledger</a>
						</li>



					</ul>
				</li>

				<li class="treeview">
					<a href="#"><img src="assets/svg/bus.svg" width='28' /> <span>Transport Managment</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="add_area" accesskey='a'>
								<akey>A</akey>dd Area
							</a>
						</li>
						<li> <a href="add_trip"> Add Trip </a>
						</li>
						<li> <a href="area_wise_report"> Area Wise Report </a>
						</li>
						<li> <a href="transport_card"> Generate Transport Card</a>
						</li>
					</ul>
				</li>
			<?php } ?>


			<?php if ($user_type == 'TEACHER'):
				$emp = get_data('employee', strtoupper($_SESSION['user_name']), null, 'emp_code')['data'];
				?>
	
				<!-- ====== TEACHER: Academics ====== -->
				<li class="treeview">
					<a href="#">
						<img src="assets/svg/paper.svg" width='26' />
						<span>Academics</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<?php if (($emp['designation'] ?? '') == 'NTT'): ?>
							<li><a href="add_pre_marks"><i class="fa fa-star fa-fw"></i> Montessori Tabulation</a></li>
						<?php else: ?>
							<li><a href="add_tabulation_marks"><i class="fa fa-pencil fa-fw"></i> Marks Tabulation</a></li>
						<?php endif; ?>
						<li><a href="student_performance"><i class="fa fa-bar-chart fa-fw"></i> Student Performance</a></li>
						<li><a href="syllabus_tracker"><i class="fa fa-list-alt fa-fw"></i> Syllabus Tracker</a></li>
					</ul>
				</li>

				<!-- ====== TEACHER: UT Tests ====== -->
				<li class="treeview">
					<a href="#">
						<img src="assets/svg/print.svg" width='26' />
						<span>UT Tests</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<?php if (($emp['designation'] ?? '') != 'NTT'): ?>
							<li><a href="ut_marks_entry"><i class="fa fa-edit fa-fw"></i> UT Marks Entry</a></li>
						<?php endif; ?>
						<li><a href="ut_test_calendar_report"><i class="fa fa-calendar fa-fw"></i> UT Calendar Planner</a>
						</li>
						<li><a href="teacher_ut_report"><i class="fa fa-file-text-o fa-fw"></i> UT Report Card</a></li>
						<li><a href="class_ut_report"><i class="fa fa-table fa-fw"></i> Class-wise UT Report</a></li>
					</ul>
				</li>

				<!-- ====== TEACHER: Homework & Planning ====== -->
				<li class="treeview">
					<a href="#">
						<img src="assets/svg/education.svg" width='26' />
						<span>Homework & Planning</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="home_work"><i class="fa fa-book fa-fw"></i> Manage Homework</a></li>
						<li><a href="homework_generator"><i class="fa fa-send-o fa-fw"></i> Homework Generator</a></li>
					</ul>
				</li>

				<!-- ====== TEACHER: Attendance ====== -->
				<li class="treeview">
					<a href="#">
						<img src="assets/svg/att_calendar.svg" width='26' />
						<span>Attendance</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="teacher_st_att"><i class="fa fa-check-square-o fa-fw"></i> Student Attendance</a></li>
						<li><a href="daily_attendance"><i class="fa fa-calendar-check-o fa-fw"></i> Daily Attendance</a>
						</li>
						<li><a href="student_monthwise_att_report"><i class="fa fa-calendar fa-fw"></i> Monthwise Report</a>
						</li>
						<li><a href="attendance_report"><i class="fa fa-bar-chart fa-fw"></i> Attendance Report</a></li>
						<li><a href="daily_attendance_report"><i class="fa fa-bar-chart fa-fw"></i> Daily Attendance
								Report</a></li>
						<li><a href="emp_own_att"><i class="fa fa-user-circle fa-fw"></i> My Attendance</a></li>
					</ul>
				</li>

				<!-- ====== TEACHER: Daily Work ====== -->
				<li class="treeview">
					<a href="#">
						<img src="assets/svg/education.svg" width='26' />
						<span>Daily Work</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="daily_logbook"><i class="fa fa-book fa-fw"></i> Daily Log Book</a></li>
						<li><a href="daily_routine"><i class="fa fa-calendar fa-fw"></i> Class Routine</a></li>
						<li><a href="leave_apply"><i class="fa fa-calendar-minus-o fa-fw"></i> Apply Leave</a></li>
						<li><a href="reports"><i class="fa fa-file-text-o fa-fw"></i> Reports</a></li>
					</ul>
				</li>

			<?php endif; ?>

			<?php if ($user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'DBA'): ?>

				<!-- ====== ADMIN: UT & Reports ====== -->
				<li class="treeview">
					<a href="#"> <img src="assets/svg/print.svg" width='28' /> <span>UT &amp; Reports</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="ut_marks_entry">UT Marks Entry</a></li>
						<li><a href="student_performance">Student Performance</a></li>
						<li><a href="syllabus_tracker">Syllabus Tracker</a></li>
						<li><a href="teacher_ut_report">Teacher-wise UT Report</a></li>
						<li><a href="class_ut_report">Class-wise UT Report</a></li>
						<li><a href="teacher_performance">Teacher Performance</a></li>
					</ul>
				</li>

				<!-- ====== ADMIN: Teacher Attendance ====== -->
				<li class="treeview">
					<a href="#"> <img src="assets/svg/att_calendar.svg" width='28' /> <span>Teacher Attendance</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="daily_attendance">Daily Attendance</a></li>
						<li><a href="attendance_report">Attendance Report</a></li>
						<li><a href="daily_attendance_report">Daily Attendance Report</a></li>
					</ul>
				</li>

				<!-- ====== ADMIN: Teacher Daily Work ====== -->
				<li class="treeview">
					<a href="#"> <img src="assets/svg/education.svg" width='28' /> <span>Teacher Daily Work</span>
						<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
					</a>
					<ul class="treeview-menu">
						<li><a href="daily_logbook">Daily Log Book</a></li>
						<li><a href="homework_generator">Homework Generator</a></li>
						<li><a href="daily_routine">Class Routine</a></li>
						<li><a href="reports">Reports</a></li>
					</ul>
				</li>

			<?php endif; ?>

			<?php if ($user_type == 'PRO') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/student.svg" width='28' /> <span>Student Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="new_student_update">Update Student Photo</a>
					</ul>
				</li>

			<?php }
			if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'ACCOUNT') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/faq.svg" width='28' /> <span>Enquiry Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="add_enquiry">Add Enquiry</a></li>
						<li><a href="manage_enquiry">Manage Enquiry </a></li>
						<li><a href="form_collection">Form Sale Report </a></li>
						<li><a href="application_list">Online Application List </a></li>
						<li><a href="manage_admission_phases">Manage Admission Phases <span
									class='badge bg-success'>New</span></a></li>
						<li><a href="application_collection_report">Application Report </a></li>
						<li><a href="scholorship_app">Scholorship Application </a></li>
						<li><a href="scholorship_seat_manage">Scholorship Seating</a></li>
						<li><a href="scholorship_report">Scholorship Report </a></li>
						<?php if (($user_type == 'DBA' && $_SESSION['user_name'] == 'pratima') || $_SESSION['user_type'] == 'DEV') { ?>
							<li><a href="scholor_marks">Scholorship Marks Update </a></li>
						<?php } ?>
					</ul>
				</li>

			<?php }
			if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'ACCOUNT') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/reception.svg" width='28' /> <span>Reception</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> <span
								class='badge bg-success'>new</span></span>
					</a>
					<ul class="treeview-menu">

						<li><a href="st_gate_pass">Student Gate Pass<span class='badge bg-danger'>new</span></a>
						</li>
						<li><a href="manage_gate_pass">Manage Gate Pass<span class='badge bg-danger'>new</span></a>
						</li>

					</ul>
				</li>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/student.svg" width='28' /> <span>Student Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="add_student">Add Student</a>
						</li>
						<li><a href="manage_student">Manage Student </a>
						</li>
						<li><a href="student_status">Student Report</a>
						</li>
						<li><a href="st_remark_export">Student Export (Remark)<span class='badge bg-danger'>new</span></a>
						</li>
						<?php if ($user_type == 'DEV' or $user_type == 'ADMIN' or $user_type == 'DBA') { ?>
							<li><a href="new_student_update">Update Student Photo</a>
							</li>
							<li><a href="fly_student">Add FLY Student <span class='badge bg-danger'>new</span></a>
							</li>
						<?php } ?>

					</ul>
				</li>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/att_calendar.svg" width='28' /> <span>Att Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">

						<li><a href="student_att">Student Attendance</a>
						</li>
						<li><a href="student_monthwise_att_report">Monthwise Attendance </a>
						</li>
						<li><a href="att_summary">Student Attendance Report</a>
						</li>
						<li><a href="daily_attendance_report">Daily Attendance Report</a>
						</li>

					</ul>
				</li>
				<?php if ($user_type == 'DEV') { ?>
					<li class="treeview">
						<a href="#"> <img src="assets/svg/report.svg" width='28' /> <span>CBSE RESULT <span
									class='badge bg-success'>New</span></span> <span class="pull-right-container"> <i
									class="fa fa-angle-left pull-right"></i> </span>
						</a>
						<ul class="treeview-menu">
							<li><a href="cbse_upload_result">Upload Result </a>
							</li>
							<li><a href="cbse_view_result">View Result </a>
							</li>

						</ul>
					</li>
				<?php } ?>

			<?php }
			if ($user_type == 'ACCOUNT') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/emp.svg" width='28' /> <span>Employee Mgmt.</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">

						<!--<li><a href="manage_recruitment">Manage Recruitment</a>-->
						<!--</li>-->
						<!--<li><a href="manage_employee">Manage Employee</a>-->
						<!--</li>-->
						<!--<li><a href="manage_salary">Manage Salary </a>-->
						<!--</li>-->
						<!--<li><a href="pay_salary">Pay Salary </a>-->
						<!--</li>-->
						<li><a href="employee_attendance">Employee Att.</a>
						</li>
						<!--<li><a href="leave_req">Leave Details</a>-->
						<!--</li>-->
						<li><a href="emp_att_report">Employee Att. Report</a>
						</li>

					</ul>
				</li>

			<?php }
			if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/emp.svg" width='28' /> <span>Employee Mgmt.</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">

						<li><a href="manage_recruitment">Manage Recruitment</a>
						</li>
						<li><a href="manage_employee">Manage Employee</a>
						</li>
						<li><a href="manage_salary">Manage Salary </a>
						</li>
						<li><a href="pay_salary">Pay Salary </a>
						</li>
						<li><a href="employee_attendance">Employee Att.</a>
						</li>
						<li><a href="leave_req">Leave Details</a>
						</li>
						<li><a href="emp_att_report">Employee Att. Report</a>
						</li>
						<li><a href="manage_teacher_subject">Manage Teacher Subject</a>
						</li>

					</ul>
				</li>
			<?php } ?>





			<?php if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'Account') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/education.svg" width='28' /> <span>Inventory Mgmt.</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<?php if ($user_type == 'Account' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
							<li> <a href="collect_fee">Sell Item</a></li>
							<li> <a href="item_details">Stock Report</a></li>
							<li> <a href="manage_distribute_item">Sell Report</a></li>
						<?php } ?>
						<?php if ($user_type == 'ADMIN' or $user_type == 'DEV') { ?>
							<li> <a href="manage_vendor">Add/Manage Vendor</a></li>
							<li><a href="item_cat">Item Category</a></li>
							<li> <a href="manage_item">Add/Manage Item</a></li>
							<li> <a href="purchase_item">Purchase Item</a></li>
							<li> <a href="purchase_report">Purchase Report</a></li>
						<?php } ?>
					</ul>
				</li>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/education.svg" width='28' /> <span>Sports Mgmt.</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="#">Add Event</a></li>
						<li> <a href="#">Manage Events</a></li>
						<li> <a href="#">Create Participants</a></li>
						<li> <a href="#">Manage Participants</a></li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV' or $user_type == 'Account') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/print.svg" width='28' /> <span>Exam Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> <span
								class='badge bg-success'>new</span></span>
					</a>
					<ul class="treeview-menu">

						<li> <a href="admit_card">Exam Time Table </a>
						</li>
						<li> <a href="print_admit_card"> Print Admit Card </a>
						</li>
						<li> <a href="exam_seat_manage">Seating Plan</a>
						</li>
						<li> <a href="new_seat_plan">New Seating Plan<span class='badge bg-danger'>new</span></a>
						</li>
						<li> <a href="marks_entry"> Marks Entry </a>
						</li>
						<li> <a href="co_marks_entry"> Co Marks Entry <span class='badge bg-danger'>New</span></a>
						</li>
						<li> <a href="marks_upload"> Bulk Marks Upload </a>
						</li>
						<li> <a href="consolidated_marks"> Consolidated Marks <span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="generate_report_card"> PT Report Card<span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<!--<a href="generate_report_card"> Print Report Card<span class='badge bg-danger'>new</span></a>-->
							<a href="report"> Print Report Card<span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="manage_sub_subject"> Sub Subjects<span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="manage_sub_papers"> Sub Papers<span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="tabulation_report"> Tabulation Report Card <span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="tabulation_class_report"> Tabulation Class Report <span
									class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="pre_report">Pre Report Card<span class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="pre_tabulation_report">Pre Tabulation Report <span
									class='badge bg-danger'>new</span></a>
						</li>
						<li>
							<a href="add_student_att">Student Att Enrty <span class='badge bg-danger'>new</span></a>
						</li>
						<li> <a href="promotion_status">Promotion Status </a>
						</li>
						<li> <a href="promotion_list">Promotion List </a>
						</li>
					</ul>
				</li>
				<li class="treeview">
					<a href="#"> <img src="assets/icon/notes.png" width='28' /> <span>Exam Analysis</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> <span
								class='badge bg-success'>new</span></span>
					</a>
					<ul class="treeview-menu">

						<li> <a href="subwise_analysis">Subject Wise I to VIII </a></li>
						<li> <a href="secondry_analysis">Subject Wise IX & X </a></li>
						<li> <a href="class_sub_report">Analysis Report </a></li>
						<li> <a href="class_report">Class Analysis Report </a></li>
						<li> <a href="unit_test_report">Unit Test Report Card</a></li>
						<li> <a href="teacher_ut_report">Teacher-wise UT Report</a></li>
						<li> <a href="class_ut_report">Class-wise UT Report</a></li>
						<li> <a href="pre_ut_report">Pre UT Report Card</a></li>
						<li> <a href="ut_test_calendar_report">UT Calendar Planner</a></li>
					</ul>
				</li>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/web.svg" width='28' /> <span>Website Management</span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">

						<li> <a href="add_to_gallery">Manage Gallery </a>
						</li>
						<li> <a href="notice"> Manage Notice </a>
						</li>
						<li> <a href="manage_hiring">Job Post </a>
						</li>
					</ul>
				</li>
			<?php } ?>

			<?php if ($user_type == 'Librarian' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
				<li class="treeview">
					<a href="#"> <img src="assets/svg/library.svg" width='28' /> <span>Library Managment </span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="book_cat"> Book Category </a>
						</li>
						<li> <a href="book_pub"> Book Publisher </a>
						</li>
						<li> <a href="manage_book"> Manage Book </a>
						</li>
						<li> <a href="issue_book"> Issue A Book </a>
						</li>
						<li> <a href="book_return"> Return Book </a>
						</li>
						<li> <a href="return_report"> Return Report </a>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == 'ACCOUNT' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
				<li class="treeview">
					<a href="#"><img src="assets/svg/account.svg" width='28' /> <span>Accounts </span> <span
							class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="acc_dash">Account Dashbord </a></li>
						<li> <a href="exp_head">Expense Head </a>
						</li>
						<li>
							<a href="manage_account"> Manage Transaction</a>
						</li>

						<li>
							<a href="date_wise_report"> Date Wise Report</a>
						</li>
						<li>
							<a href="daily_collection_report"> All Date Report <span class='badge bg-success'>new</span>
							</a>
						</li>
						<?php if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
							<li>
								<a href="head_wise_report"> Head Wise Report</a>
							</li>
							<li>
								<a href="closing_report"> Daily Closing Report</a>
							</li>
						<?php } ?>
					</ul>
				</li>


				<li class="treeview">
					<a href="#"><img src="assets/icon/notes.png" width='28' /> <span>Report </span><span
							class='badge bg-success'>new</span> <span class="pull-right-container"> <i
								class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="report_overall_summary">Overall Summary </a></li>
						<li> <a href="scholarship_report">Scholarship Students </a></li>
						<li> <a href="report_defaulter_student.php"> Defaulter Student </a></li>
						<li> <a href="report_class_wise_fee_collection "> Class-wise Fee Collection </a></li>
						<li> <a href="report_monthly_fee_collection"> Monthly Fee Collection</a></li>
						<li> <a href="report_student_receipt"> Student Receipt </a></li>
						<li> <a href="report_mode_of_payment"> Mode of Payment Report</a></li>
						<li> <a href="report_concession_discount_report"> Discount Report</a></li>
						<li> <a href="collection_report"> Fee Receipt Report</a></li>
						<li> <a href="report_outstanding_dues_report"> Outstanding Dues Report</a></li>
						<li> <a href="report_individual.php"> Individual Fee Report</a></li>
						<?php if ($user_type == 'DEV') { ?>
							<li> <a href="student_payment_status"> Update Fee Status </a></li>
						<?php } ?>
					</ul>
				</li>

			<?php } ?>
			<?php if ($user_type == 'DBA' or $user_type == 'ADMIN' or $user_type == 'DEV') { ?>
				<li class="treeview">
					<a href="#"> <img src="https://img.icons8.com/color/96/certificate.png" width='28' />
						<span>Certification </span> <span class="pull-right-container"> <i
								class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
				</li>
				<li> <a href="generate_idcard"> Identity Card </a>
				</li>
				<li> <a href="create_certificate"> Create Certificate </a>
				</li>
				</li>
			</ul>
			</li>
			<li class="treeview">
				<a href="#"> <img src="https://img.icons8.com/color/96/communication.png" width='28' /> <span>Communication
					</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
				</a>
				<ul class="treeview-menu">
					<li> <a href="send_sms"> Send SMS </a>
					</li>
					<li> <a href="bulk_sms"> Bulk WhatsApp <span class='badge bg-danger'> New </span> </a>
					</li>
					<li> <a href="sms_template"> SMS Template </a>
					</li>
					<li> <a href="sms_status"> SMS Status Report <span class='badge badge-success'>New</span></a>
					</li>
				</ul>
			</li>
		<?php } ?>
		<?php if ($user_type == 'ADMIN' or $user_type == 'DEV') { ?>
			<li class="treeview">
				<a href="#"> <img src="assets/svg/setting.svg" width='28' /> <span>Settings</span> <span
						class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
				</a>
				<ul class="treeview-menu">

					<li> <a href="add_fee"> Create Fee </a>
					</li>
					<li> <a href="update_fee"> Set Fee Amount </a>
					</li>
					<li> <a href="subject_setting"> Manage Subject </a>
					</li>
					<li> <a href="exam_list">Manage Exams <span class='badge bg-success'>New</span> </a>
					</li>
					<li> <a href="manage_ut_tests">Manage UT Tests <span class='badge bg-success'>New</span> </a>
					</li>
					<li> <a href="marks_setting">Set Exam Marks <span class='badge bg-success'>New</span> </a>
					</li>

					<li> <a href="bulk_import">Bulk Import </a>
					</li>
					<!-- <li>
						<a href="admin_txn"> Admin Transaction</a>
					</li>

					<li>
						<a href="admin_account"> Transaction Report</a>
					</li> -->

					<li> <a href="add_branch"> Manage Branch <span class='badge bg-success'>New</span></a>
					</li>
					<li> <a href="add_user"> Manage User </a>
					</li>
					<li> <a href="fee_test" accesskey='r'> <span class='badge badge-success'>R</span> Recheck </a>
					</li>
					<li>
						<?php // echo $current_session;
							if ($session_list[$db_name] == $current_session) { ?>

						<li> <a href="promote_student">Promote Student </a>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
		</ul>
	</section>
	<div class="sidebar-footer">
		<a href="#" class="link" data-toggle="tooltip" title="" data-original-title="Settings" id='appinfo'><i
				class="fa fa-cog fa-spin"></i></a>
		<a href="support" class="link" data-toggle="tooltip" title="" data-original-title="Help & Support"><i
				class="fa fa-life-ring" aria-hidden="true"></i></i></a>
		<a href="#" class="link" data-toggle="tooltip" title="" onclick="logout()"><i class="fa fa-power-off"></i></a>
	</div>
</aside>