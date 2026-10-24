<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
if ($user_type == 'ACCOUNT') {
	echo "<script> window.location ='acc_dash'  </script>";
}
if ($user_type == 'STAFF') {
	echo "<script> window.location ='studyplant'  </script>";
}
if ($user_type == 'TEACHER') {
	echo "<script> window.location ='teacher_dash'  </script>";
}
if ($user_type == 'ECONTROLLER') {
	echo "<script> window.location ='econtroller_dash'  </script>";
}
?>
<style>
    .main-tab {
    display: block;
    background: transparent;
    color: #222 !important;
    padding: 15px;
    border-radius: 10px;
    transition: 0.3s;
}

.tab-icon {
    height: 45px;
    margin-bottom: 8px;
}

.main-tab:hover {
    background: #f1f5f9;
    transform: translateY(-5px);
}
.birthday-card {
    background: #fff;
    border-radius: 12px;
    padding: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.birthday-card:hover {
    transform: translateY(-5px);
}

.bday-img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 3px solid #ff4081;
    object-fit: cover;
}

.bday-name {
    font-weight: 600;
    margin-top: 5px;
    color:#000;
}

.bg-pink {
    background: linear-gradient(135deg, #ff758c, #ff7eb3);
    color: #fff;
}

/* Wing Wise Attendance & Strength Widget */
.wing-dashboard-wrapper {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.wing-card {
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.wing-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

.wing-card-header {
    border-radius: 12px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.wing-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.wing-header-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wing-header-title {
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.3px;
    margin: 0;
}

.wing-header-badge {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wing-header-junior {
    background: #E8F4FD;
}

.wing-header-senior {
    background: #EAF8EE;
}

.wing-header-total {
    background: #F0EEFD;
}

.wing-metrics-row {
    display: flex;
    gap: 10px;
}

.wing-metric-box {
    flex: 1;
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 12px;
    padding: 12px 6px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    min-height: 105px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.wing-metric-box:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

.wing-metric-label {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 6px;
    white-space: nowrap;
}

.wing-metric-label.label-strength {
    color: #1e40af;
}

.wing-metric-label.label-present {
    color: #16a34a;
}

.wing-metric-label.label-absent {
    color: #dc2626;
}

.wing-metric-value-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin: auto 0;
}

.wing-metric-number {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}

.wing-metric-badge {
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    padding: 3px 8px;
    margin-top: 6px;
    display: inline-block;
    letter-spacing: 0.2px;
}

.wing-badge-present {
    background: #DCFCE7;
    color: #15803D;
}

.wing-badge-absent {
    background: #FFE4E6;
    color: #E11D48;
}

@media (max-width: 991px) {
    .wing-metrics-row {
        gap: 6px;
    }
    .wing-metric-number {
        font-size: 18px;
    }
    .wing-header-title {
        font-size: 17px;
    }
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
		<?php if($user_type !='PRO'){ ?>
		<ol class="breadcrumb">
			<form action="system/master_process.php?task=switch_branch" method="post" role="form" id="branch_form">
				<input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
				<select name="branch_id" class="form-control float-left" id="branch_select">
					<?= dropdown_with_key(branch_list(), $_SESSION['branch_id']); ?>
				</select>
			</form>

			<form action="system/master_process.php?task=change_session" id="session_form">
				<select name="session_year" class="form-control float-left" id="session_select">
					<?php dropdown_with_key($session_list, $db_name); ?>
				</select>
			</form>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<div class="callout callout-info">
			<h4>Thanks for Choosing <?= $app_name ?></h4>
			<p>Welcome to the Bine Dashboard! Track student progress, manage attendance, view upcoming events, and stay updated with important notifications—all in one place.</p>
		</div>
		<?php if($user_type != 'TEACHER' && $user_type != 'PRO'){ ?>
		<div class="row">
			<div class="col-xl-12 connectedSortable">
			    <?php
                    $today = date('m-d');
                    
                    // STUDENTS
                    $students_bday = direct_sql("
                        SELECT id, student_name, student_photo, student_admission, student_class, student_section, date_of_birth 
                        FROM student 
                        WHERE DATE_FORMAT(date_of_birth, '%m-%d') = '$today' 
                        AND status='ACTIVE'
                    ");
                    
                    // STAFF
                    $staff_bday = direct_sql("
                        SELECT id, name, photo, dob 
                        FROM employee 
                        WHERE DATE_FORMAT(dob, '%m-%d') = '$today' 
                        AND status='ACTIVE'
                    ");
                    ?>
                    
                    <div class="box box-solid bg-pink">
                        <div class="box-header">
                            <h3 class="box-title text-dark">🎂 Today's Birthdays</h3>
                        </div>
                    
                        <div class="box-body">
                            <div class="row">
                    
                                <?php
                                foreach ($students_bday['data'] as $row) {
                    
                                    // PHOTO LOGIC (same as before)
                                    $sphoto = $row['student_photo'];
                                    $img = "assets/images/user.jpg";
                    
                                    if (!file_exists($sphoto)) {
                                        $img = $base_url."upload/".$sphoto;
                                    }
                    
                                    echo "
                                    <div class='col-md-2 col-6 text-center mb-3'>
                                        <div class='birthday-card'>
                                            <img src='$img' class='bday-img'>
                                            <div class='bday-name'>{$row['student_name']}</div>
                                            <small class='text-dark'>{$row['student_class']}-{$row['student_section']}</small>
                                        </div>
                                        <a href='birthday_card.php?id=".$row['id']."&type=student' target='_blank' class='btn btn-xs btn-primary mt-2 w-100'>Card</a>
                                    </div>";
                                }
                    
                                foreach ($staff_bday['data'] as $row) {
                    
                                    $img = "assets/images/user.jpg";
                    
                                    if (file_exists($row['photo'])) {
                                        $img = $base_url."upload/".$row['photo'];
                                    }
                    
                                    echo "
                                    <div class='col-md-2 col-6 text-center mb-3'>
                                        <div class='birthday-card bg-yellow'>
                                            <img src='$img' class='bday-img'>
                                            <div class='bday-name'>{$row['name']}</div>
                                            <small class='text-dark'>Staff</small>
                                        </div>
                                        <a href='birthday_card.php?id=".$row['id']."&type=employee' target='_blank' class='btn btn-xs btn-primary mt-2 w-100'>Card</a>
                                    </div>";
                                }
                                ?>
                    
                            </div>
                        </div>
                    </div>
			    <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">Main Modules</h3>
                    </div>
                
                    <div class="box-body">
                        <div class="row text-center">
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="manage_student" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/students.png" class="tab-icon">
                                    <br> Students
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="add_student" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/add-user-group-man-man.png" class="tab-icon">
                                    <br> Admission
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="student_att" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/conference-call.png" class="tab-icon">
                                    <br> Student Attendance
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="student_monthwise_att_report" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/time-card.png" class="tab-icon">
                                    <br> Attendance Report
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="manage_employee" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/conference-call.png" class="tab-icon">
                                    <br> Staff Management
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="employee_attendance" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/time-card.png" class="tab-icon">
                                    <br> Staff Attendance
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="leave_apply" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/document.png" class="tab-icon">
                                    <br> Leave Apply
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="st_gate_pass" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/exit.png" class="tab-icon">
                                    <br> Gate Pass
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="manage_gate_pass" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/report-card.png" class="tab-icon">
                                    <br> Gate Pass Report
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="collection_report" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/sales-performance.png" class="tab-icon">
                                    <br> Fee Report
                                </a>
                            </div>
                            
                            <div class="col-md-2 col-6 p-3">
                                <a href="report" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/test-passed.png" class="tab-icon">
                                    <br> Report Card
                                </a>
                            </div>
                
                            <div class="col-md-2 col-6 p-3">
                                <a href="issue_book" class="main-tab">
                                    <img src="https://img.icons8.com/color/96/library.png" class="tab-icon">
                                    <br> Library
                                </a>
                            </div>
                
                        </div>
                    </div>
                </div>
				<div class="box box-info">
					<div class="box-header">
						<h4 class="box-title">Quick Access</h4>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class='row text-light'>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='add_student' title='New Admission'>
									<img src='assets/svg/new_admission.svg' height='50px' />
									<br> New Admission
								</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collect_fee' title='Collect Fee'>
									<img src='assets/svg/pay.svg' height='50px' />
									<br> Pay Fee
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='generate_demand' title='Demand Slip'>
									<img src='assets/svg/receipt.svg' height='50px' />
									<br> Create Demand
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='send_sms' title='Send SMS'>
									<img src='assets/svg/sms.svg' height='50px' />
									<br> Send SMS
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collect_fee' title='Student Ledger'>
									<img src='assets/svg/account.svg' height='50px' />
									<br> Student Ledger</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collection_report' title='Collection Report'>
									<img src='assets/svg/collection.svg' height='50px' />
									<br> Collection Report</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='create_certificate' title=' Create Certificate'>
									<img src='assets/svg/certificate.svg' height='50px' />
									<br> Certificate</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='add_area' title=' Transport Area '>
									<img src='assets/svg/bus.svg' height='50px' />
									<br> Transport Area </a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='manage_account' title='Collection Report'>
									<img src='assets/svg/report.svg' height='50px' />
									<br> Expense Report</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='issue_book' title=' Issue A Book'>
									<img src='assets/svg/library.svg' height='50px' />
									<br> Issue A Book </a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='book_return' title=' Book Return'>
									<img src='assets/svg/lesson.svg' height='50px' />
									<br> Book Return</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='date_wise_report' title='Date with Report'>
									<img src='assets/svg/settings.svg' height='50px' />
									<br> Day Book </a>
							</div>

						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<div class="col-xl-3 col-lg-6 col-12">
			</div>
			<!-- /.col -->
		</div>
		<?php } ?>
		<!-- /.row -->

		<div class="row">
			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-blue">
					<div class="inner">
						<h3><?php echo count_all('student', array('status' => 'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></h3>

						<p>Student</p>
					</div>
					<div class="icon">
						<img src="assets/svg/student.svg" width='60' />
					</div>
				    <?php if($user_type != 'TEACHER' && $user_type != 'PRO'){ ?>
					<a href="add_student" class="small-box-footer">New Registration <i class="fa fa-arrow-right"></i></a>
					<?php } ?>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-green">
					<div class="inner">
						<h3><?php echo count_all('employee', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Employee </p>
					</div>
					<div class="icon">
						<img src="assets/svg/staff.svg" width='60' />
					</div>
				    <?php if($user_type != 'TEACHER' && $user_type != 'PRO'){ ?>
					<a href="manage_salary" class="small-box-footer">Pay Salary <i class="fa fa-arrow-right"></i></a>
					<?php } ?>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo count_all('transport_area', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Transport Area</p>
					</div>
					<div class="icon">
						<img src="assets/svg/bus.svg" width='60' />
					</div>
				    <?php if($user_type != 'TEACHER' && $user_type != 'PRO'){ ?>
					<a href="add_area" class="small-box-footer">Add New Area <i class="fa fa-arrow-right"></i></a>
					<?php } ?>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-orange">
					<div class="inner">
						<h3><?php echo count_all('enquiry', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Enquiry </p>
					</div>
					<div class="icon">
						<img src="assets/svg/door.svg" width='60' />
					</div>
				    <?php if($user_type != 'TEACHER' && $user_type != 'PRO'){ ?>
					<a href="add_enquiry" class="small-box-footer">New Enquiry <i class="fa fa-arrow-right"></i></a>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class='row'>
		<!--	<div class="col-xl-3 col-md-6 col-12">-->
		<!--		<div class="info-box">-->
		<!--			<span class="info-box-icon push-bottom">-->
		<!--				<img src='assets/svg/hostel.svg' height='45' /> </span>-->

		<!--			<div class="info-box-content">-->
		<!--				<span class="info-box-text">Hostel</span>-->
		<!--				<span class="info-box-number"></span>-->

		<!--				<div class="progress">-->
		<!--					<div class="progress-bar bg-orange" style="width: 45%"></div>-->
		<!--				</div>-->
		<!--				<span class="progress-description text-muted" style='font-size:15px'>-->
		<!--				    <?php if($user_type == 'TEACHER' ){ ?>-->
		<!--					    <?php echo get_all('student', '*', array('student_type' => 'HOSTELER','status'=>'ACTIVE'))['count']; ?> Hostel-->
		<!--					<?php } else { ?>-->
		<!--					    <a href='student_status?status=&student_type=HOSTELER'><?php echo get_all('student', '*', array('student_type' => 'HOSTELER'))['count']; ?> Hostel</a>-->
		<!--					<?php } ?>-->
							<!--<a href='student_status?status=INACTIVE&student_type='><?php echo get_all('student', '*', array('status' => 'INACTIVE'))['count']; ?> Inactive </a>-->
		<!--				</span>-->
		<!--			</div>-->
					<!-- /.info-box-content -->
			<!--	</div>-->
				<!-- /.info-box -->
			<!--</div>-->
			<!-- /.col -->
			<div class="col-xl-4 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom ">
						<img src='assets/svg/bus.svg' height='45' />
					</span>

					<div class="info-box-content">
						<span class="info-box-text">Transport </span>


						<div class="progress">
							<div class="progress-bar bg-orange" style="width: 40%"></div>
						</div>
						<span class="progress-description text-muted">
							<span class="info-box-number" style='font-size:15px'>
						    <?php if($user_type == 'TEACHER'){ ?>
							    <?php echo count_all('student', array('student_type' => 'TRANSPORT','status'=>'ACTIVE'))['count']; ?> Area
							<?php } else { ?>
							    <a href='add_area'><?php echo count_all('student', array('student_type' => 'TRANSPORT','status'=>'ACTIVE'))['count']; ?></a>
							    <?php } ?>
						    </span>
						</span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-xl-4 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom ">
						<img src='assets/svg/girl.svg' height='45' />
					</span>

					<div class="info-box-content">
						<span class="info-box-text">Girls</span>


						<div class="progress">
							<div class="progress-bar bg-orange" style="width: 85%"></div>
						</div>
						<span class="info-box-number" style='font-size:15px'><?php echo count_all('student', array('student_sex' => 'FEMALE','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-xl-4 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom">
						<img src='assets/svg/boy.svg' height='45' />
					</span>

					<div class="info-box-content">
						<span class="info-box-text">Boys</span>

						<div class="progress">
							<div class="progress-bar bg-orange" style="width: 50%"></div>
						</div>
						<span class="progress-description text-muted">
							<span class="info-box-number" style='font-size:15px'><?php echo count_all('student', array('student_sex' => 'MALE','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></span>
						</span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
		</div>
		
		
		<div class="five-col-row">
            <div class="five-col">
                <div class="info-box">
                    <span class="info-box-icon push-bottom" style='height: 50px;width: 50px;text-align: center;font-size: 50px;line-height: 50px;'>
                        <img src='assets/svg/hostel.svg' height='30' />
                    </span>
                    <div class="info-box-content" style='margin-left: 60px;'>
                        <span class="info-box-text">PEACE</span>
                        <span class="info-box-number"></span>
                        <div class="progress">
                            <div class="progress-bar bg-orange" style="width: 45%"></div>
                        </div>
                        <span class="progress-description text-muted" style='font-size:15px'>
                            <a href='student_status?status=ACTIVE&house_name=PEACE'><?= count_all('student',  array('house_name' => 'PEACE','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></a>
                        </span>
                    </div>
                </div>
            </div>
        
            <div class="five-col">
                <div class="info-box">
                    <span class="info-box-icon push-bottom" style='height: 50px;width: 50px;text-align: center;font-size: 50px;line-height: 50px;'>
                        <img src='assets/svg/hostel.svg' height='30' />
                    </span>
                    <div class="info-box-content" style='margin-left: 60px;'>
                        <span class="info-box-text">COURAGE</span>
                        <div class="progress">
                            <div class="progress-bar bg-orange" style="width: 40%"></div>
                        </div>
                        <span class="progress-description text-muted">
                            <span class="info-box-number" style='font-size:15px'>
                            <a href='student_status?status=ACTIVE&house_name=COURAGE'><?= count_all('student', array('house_name' => 'COURAGE','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></a>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        
            <div class="five-col">
                <div class="info-box">
                    <span class="info-box-icon push-bottom" style='height: 50px;width: 50px;text-align: center;font-size: 50px;line-height: 50px;'>
                        <img src='assets/svg/hostel.svg' height='30' />
                    </span>
                    <div class="info-box-content" style='margin-left: 60px;'>
                        <span class="info-box-text">HOPE</span>
                        <div class="progress">
                            <div class="progress-bar bg-orange" style="width: 85%"></div>
                        </div>
                            <span class="info-box-number" style='font-size:15px'>
                            <a href='student_status?status=ACTIVE&house_name=HOPE'><?= count_all('student', array('house_name' => 'HOPE','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></a>
                            </span>
                    </div>
                </div>
            </div>
        
            <div class="five-col">
                <div class="info-box">
                    <span class="info-box-icon push-bottom" style='height: 50px;width: 50px;text-align: center;font-size: 50px;line-height: 50px;'>
                        <img src='assets/svg/hostel.svg' height='30' />
                    </span>
                    <div class="info-box-content" style='margin-left: 60px;'>
                        <span class="info-box-text">FAITH</span>
                        <div class="progress">
                            <div class="progress-bar bg-orange" style="width: 50%"></div>
                        </div>
                        <span class="progress-description text-muted"  style='font-size:15px'>
                            <a href='student_status?status=ACTIVE&house_name=FAITH'><?= count_all('student', array('house_name' => 'FAITH','status'=>'ACTIVE','academic_type'=>'REGULAR'))['count']; ?></a>
                            
                        </span>
                    </div>
                </div>
            </div>
        
            <!-- 👇 Add a fifth card as per your data requirement -->
            <div class="five-col">
                <div class="info-box">
                    <span class="info-box-icon push-bottom" style='height: 50px;width: 50px;text-align: center;font-size: 50px;line-height: 50px;'>
                        <img src='assets/svg/hostel.svg' height='30' />
                    </span>
                    <div class="info-box-content" style='margin-left: 60px;'>
                        <span class="info-box-text">House Not Assigned</span>
                        <div class="progress">
                            <div class="progress-bar bg-orange" style="width: 100%"></div>
                        </div>
                        <span class="info-box-number" style='font-size:15px'>
                            
                            <a href='student_status?status=ACTIVE&house_name=NULL'><?php echo direct_sql("SELECT * FROM `student` WHERE status='ACTIVE' AND house_name is null")['count']; ?></a>
                            </span>
                    </div>
                </div>
            </div>
        </div>





		<div class="row">

			<div class="col-xl-12 connectedSortable">
				<div class="box box-info">
					<div class="box-header">
						<h3 class="box-title">Student Analysis & Absent Student</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body" style='overflow-x:scroll'>
						<table id="example1" class="table table-bordered table-striped">
							<tr class='bg-dark text-light'>
								<th> Section / Class</th>
								<?php
								foreach (array_filter((array)$class_list) as $class) {
									echo "<th>" . $class . " </th>";
								}
								?>
							</tr>
							<?php
							foreach (array_filter($section_list) as $section) {
								echo "<tr><th>" . $section . "</th>";

								foreach (array_filter($class_list) as $class) {
									$link = encode('student_class=' . $class . '&student_section=' . $section);
                                    $cmonth =date('Y-m');
									$att = today_att_status($class, $section,'A');
									$ct = studentcount($class,$section)??0 ;
									echo "<th><a href='manage_student?link=$link' title='Total Strength'>" . $ct . "</a> 
									<a href='student_monthwise_att_report?class=$class&section=$section&month=$cmonth' title='Absent Student' class='badge badge-danger text-light'>" . $att . "</a></th>";
									//echo "<th><a href='student_status?link=$link'>" . studentcount($class, $section) . "</a></th>";
								}
								echo "</tr>";
							}
							?>
							<tr class='bg-primary text-light'>
								<th> Total </th>
								<?php
								foreach (array_filter($class_list) as $class) {
								    $ct = studentcount($class)??0 ;
									echo "<th><a href='manage_student?student_class=$class&student_section='>" . $ct . "</a></th>";
								}
								?>
							</tr>
							<tr>
								<th> New Admission ( <span class='text-success text-bold'>
								    <?= count_all('student',['admission_type'=>'NEW','branch_id'=>$_SESSION['branch_id'],'status'=>'ACTIVE'])['count']; ?>
								    
								    </span> )</th>
								<?php
								foreach (array_filter($class_list) as $class) {
									echo "<th><a href='manage_student?student_class=$class&student_section=' class='text-success text-bold'> + " . new_student_count($class) . "</a></th>";
									//echo "<th><a href='student_status?student_class=$class&student_section='>" . studentcount($class) . "</a></th>";
								}
								?>
							</tr>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- Branch Wise Wing Attendance & Strength (Junior Wing, Senior Wing, Total Strength) -->
		<?php
		$current_branch_id = intval($_SESSION['branch_id'] ?? 1);
		$current_att_m1 = strtolower(date('M_Y'));
		$current_att_m2 = date('M_Y');
		$current_att_m3 = strtolower(date('M')) . "_" . date('Y');
		$current_att_col = "d_" . intval(date('j'));

		// Query active regular students and today's attendance for current branch
		$wing_sql = "
			SELECT 
				s.id,
				s.student_class,
				sa.{$current_att_col} AS att_today
			FROM student s
			LEFT JOIN student_att sa 
				ON sa.student_id = s.id 
				AND (sa.att_month = '{$current_att_m1}' OR sa.att_month = '{$current_att_m2}' OR sa.att_month = '{$current_att_m3}' OR sa.att_month LIKE '" . date('M') . "%')
				AND sa.branch_id = '{$current_branch_id}'
			WHERE s.branch_id = '{$current_branch_id}'
			  AND s.status = 'ACTIVE'
			  AND (s.academic_type = 'REGULAR' OR s.academic_type IS NULL OR s.academic_type = '')
		";
		$wing_res = direct_sql($wing_sql, 'get', false);

		$jw_total = 0; $jw_present = 0; $jw_absent = 0;
		$sw_total = 0; $sw_present = 0; $sw_absent = 0;
		$tot_total = 0; $tot_present = 0; $tot_absent = 0;

		$junior_match_list = [
			'NUR', 'NURSERY', 'LKG', 'UKG', 'PRENUR', 'PRENURSERY', 
			'PREP', 'PLAY', 'PLAYGROUP', 'KG', 'KG1', 'KG2', 'LKG1', 'UKG1', 'PG', 'MONT', 'MONTESSORI'
		];
		$senior_match_list = [
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
			'1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH',
			'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'
		];

		if (!empty($wing_res['data'])) {
			foreach ($wing_res['data'] as $stu) {
				$raw_cls = trim($stu['student_class'] ?? '');
				$clean_cls = strtoupper(trim(str_replace(['CLASS', 'STD', '.', '-', '_', ' '], '', $raw_cls)));
				$att = strtoupper(trim($stu['att_today'] ?? ''));

				// Overall total
				$tot_total++;
				if ($att === 'P') {
					$tot_present++;
				} elseif ($att === 'A') {
					$tot_absent++;
				}

				// Check Junior Wing: Nur to UKG
				if (in_array($clean_cls, $junior_match_list) || preg_match('/^(NUR|LKG|UKG|PRE|PLAY|KG|MONT)/i', $clean_cls)) {
					$jw_total++;
					if ($att === 'P') {
						$jw_present++;
					} elseif ($att === 'A') {
						$jw_absent++;
					}
				} 
				// Check Senior Wing: 1 to 10
				elseif (in_array($clean_cls, $senior_match_list) || 
						preg_match('/^(10|[1-9])(ST|ND|RD|TH)?$/i', $clean_cls) || 
						preg_match('/^(X|IX|VIII|VII|VI|V|IV|III|II|I)$/i', $clean_cls) ||
						(is_numeric($clean_cls) && intval($clean_cls) >= 1 && intval($clean_cls) <= 10)) {
					$sw_total++;
					if ($att === 'P') {
						$sw_present++;
					} elseif ($att === 'A') {
						$sw_absent++;
					}
				}
			}
		}

		// If attendance was marked (present > 0), but no 'A' was recorded, absent = total - present
		if ($jw_present > 0 && $jw_absent == 0) {
			$jw_absent = max(0, $jw_total - $jw_present);
		}
		if ($sw_present > 0 && $sw_absent == 0) {
			$sw_absent = max(0, $sw_total - $sw_present);
		}
		if ($tot_present > 0 && $tot_absent == 0) {
			$tot_absent = max(0, $tot_total - $tot_present);
		}

		// Calculate Percentages
		$jw_p_pct = ($jw_total > 0) ? number_format(($jw_present / $jw_total) * 100, 2) . '%' : '0.00%';
		$jw_a_pct = ($jw_total > 0) ? number_format(($jw_absent / $jw_total) * 100, 2) . '%' : '0.00%';

		$sw_p_pct = ($sw_total > 0) ? number_format(($sw_present / $sw_total) * 100, 2) . '%' : '0.00%';
		$sw_a_pct = ($sw_total > 0) ? number_format(($sw_absent / $sw_total) * 100, 2) . '%' : '0.00%';

		$tot_p_pct = ($tot_total > 0) ? number_format(($tot_present / $tot_total) * 100, 2) . '%' : '0.00%';
		$tot_a_pct = ($tot_total > 0) ? number_format(($tot_absent / $tot_total) * 100, 2) . '%' : '0.00%';
		?>

		<div class="row wing-dashboard-row mb-4">
			<div class="col-12">
				<div class="wing-dashboard-wrapper">
					<div class="row">
						<!-- Junior Wing Card -->
						<div class="col-xl-4 col-lg-4 col-md-12 mb-3 mb-xl-0">
							<div class="wing-card">
								<div class="wing-card-header wing-header-junior">
									<div class="wing-header-left">
										<div class="wing-header-icon">
											<svg width="36" height="36" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="17" cy="22" r="10" fill="#FFDFC4"/>
												<path d="M8 19C8 13 12 9 18 9C22 9 25 11 26 15C24 15 21 17 20 20C18 20 16 19 13 20C11 21 9 22 8 19Z" fill="#8B4513"/>
												<circle cx="14.5" cy="22" r="1.5" fill="#222"/>
												<circle cx="19.5" cy="22" r="1.5" fill="#222"/>
												<path d="M15.5 25.5C16 26.5 18 26.5 18.5 25.5" stroke="#C06030" stroke-width="1.2" stroke-linecap="round"/>
												<path d="M8 38C8 33 12 30 17 30C22 30 26 33 26 38" fill="#3B82F6"/>
												<circle cx="31" cy="23" r="10" fill="#FFE3D1"/>
												<path d="M22 20C23 14 27 10 33 10C37 10 40 12 41 16C39 16 36 17 35 20C33 20 31 19 28 20C26 21 24 22 22 20Z" fill="#D97706"/>
												<circle cx="21" cy="18" r="3.5" fill="#D97706"/>
												<circle cx="41" cy="18" r="3.5" fill="#D97706"/>
												<circle cx="28.5" cy="23" r="1.5" fill="#222"/>
												<circle cx="33.5" cy="23" r="1.5" fill="#222"/>
												<path d="M29.5 26.5C30 27.5 32 27.5 32.5 26.5" stroke="#C06030" stroke-width="1.2" stroke-linecap="round"/>
												<path d="M23 38C23 33 26 30 31 30C36 30 39 33 39 38" fill="#EC4899"/>
											</svg>
										</div>
										<h4 class="wing-header-title">Junior Wing</h4>
									</div>
									<div class="wing-header-badge">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M3 21h18"/>
											<path d="M5 21V9l7-5 7 5v12"/>
											<path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
											<circle cx="12" cy="10" r="1"/>
											<path d="M12 4V2"/>
										</svg>
									</div>
								</div>
								<div class="wing-metrics-row">
									<a href="manage_student" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-strength">Total Strength</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-users" style="color: #2563eb; font-size: 20px;"></i>
										</div>
										<span class="wing-metric-number"><?= $jw_total ?></span>
									</a>
									<a href="student_att" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-present">Present</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #16a34a; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $jw_present ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-present"><?= $jw_p_pct ?></span>
									</a>
									<a href="student_monthwise_att_report" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-absent">Absent</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #dc2626; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $jw_absent ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-absent"><?= $jw_a_pct ?></span>
									</a>
								</div>
							</div>
						</div>

						<!-- Senior Wing Card -->
						<div class="col-xl-4 col-lg-4 col-md-12 mb-3 mb-xl-0">
							<div class="wing-card">
								<div class="wing-card-header wing-header-senior">
									<div class="wing-header-left">
										<div class="wing-header-icon">
											<svg width="36" height="36" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="24" cy="26" r="10" fill="#FFDFC4"/>
												<circle cx="20.5" cy="26" r="1.5" fill="#222"/>
												<circle cx="27.5" cy="26" r="1.5" fill="#222"/>
												<path d="M22 29.5C23 30.5 25 30.5 26 29.5" stroke="#C06030" stroke-width="1.2" stroke-linecap="round"/>
												<path d="M14 24C14 20 17 18 24 18C31 18 34 20 34 24" fill="#2B2D42"/>
												<path d="M14 40C14 34 18 32 24 32C30 32 34 34 34 40" fill="#1E293B"/>
												<path d="M21 33L24 37L27 33" stroke="#F59E0B" stroke-width="1.5"/>
												<polygon points="24,9 39,15 24,21 9,15" fill="#1E293B"/>
												<rect x="18" y="18" width="12" height="3" fill="#1E293B"/>
												<path d="M36 16V23" stroke="#F59E0B" stroke-width="1.8"/>
												<circle cx="36" cy="24" r="1.8" fill="#F59E0B"/>
											</svg>
										</div>
										<h4 class="wing-header-title">Senior Wing</h4>
									</div>
									<div class="wing-header-badge">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M3 21h18"/>
											<path d="M5 21V9l7-5 7 5v12"/>
											<path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>
											<circle cx="12" cy="10" r="1"/>
											<path d="M12 4V2"/>
										</svg>
									</div>
								</div>
								<div class="wing-metrics-row">
									<a href="manage_student" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-strength">Total Strength</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-users" style="color: #2563eb; font-size: 20px;"></i>
										</div>
										<span class="wing-metric-number"><?= $sw_total ?></span>
									</a>
									<a href="student_att" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-present">Present</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #16a34a; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $sw_present ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-present"><?= $sw_p_pct ?></span>
									</a>
									<a href="student_monthwise_att_report" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-absent">Absent</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #dc2626; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $sw_absent ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-absent"><?= $sw_a_pct ?></span>
									</a>
								</div>
							</div>
						</div>

						<!-- Total Strength Card -->
						<div class="col-xl-4 col-lg-4 col-md-12">
							<div class="wing-card">
								<div class="wing-card-header wing-header-total">
									<div class="wing-header-left">
										<div class="wing-header-icon">
											<svg width="32" height="32" viewBox="0 0 24 24" fill="#312e81">
												<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
											</svg>
										</div>
										<h4 class="wing-header-title">Total Strength</h4>
									</div>
									<div class="wing-header-badge">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="#0284c7">
											<path d="M4 19h4V12H4v7zm6 0h4V7h-4v12zm6 0h4V3h-4v16z"/>
										</svg>
									</div>
								</div>
								<div class="wing-metrics-row">
									<a href="manage_student" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-strength">Total Strength</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-users" style="color: #4338ca; font-size: 20px;"></i>
										</div>
										<span class="wing-metric-number"><?= $tot_total ?></span>
									</a>
									<a href="student_att" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-present">Present</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #16a34a; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $tot_present ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-present"><?= $tot_p_pct ?></span>
									</a>
									<a href="student_monthwise_att_report" class="wing-metric-box text-decoration-none">
										<span class="wing-metric-label label-absent">Absent</span>
										<div class="wing-metric-value-row">
											<i class="fa fa-user" style="color: #dc2626; font-size: 16px;"></i>
											<span class="wing-metric-number"><?= $tot_absent ?></span>
										</div>
										<span class="wing-metric-badge wing-badge-absent"><?= $tot_a_pct ?></span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-8 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Collection of Month & Avg Attendance</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div id="payment_chart" style="width: 100%; height: 250px;"></div>
					</div>
					<!-- /.box-body -->

				</div>
			</div>
			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Admission of Month</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div id="admission_chart" style="width: 100%; height: 250px;"></div>
					</div>
					<!-- /.box-body -->

				</div>
			</div>

		</div>

		<div class="row">

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Last 3 Payments </h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul class="products-list product-list-in-box">
							<?php 
							$today = date('Y-m-d');
							$respay = get_multi_data('receipt', array('paid_date' => $today, 'status' => 'PAID'), 'order by id desc limit 3');
                            // print_r($respay);
							if ($respay['count'] > 0) {
								foreach ($respay['data'] as $pd) {
							?>
									<li class="item">
										<div class="product-img">
											<?php
											$rid = $pd['id'];
											$img =  get_data('student', $pd['student_id'], 'student_photo')['data']; ?>
											<img src="upload/<?php echo $img; ?>" alt="Student Image">
										</div>
										<div class="product-info">
											<a href="receipt.php?receipt_id=<?php echo  $rid; ?>" target='_blank' class="product-title"> <?php echo get_data('student', $pd['student_id'], 'student_name')['data']; ?> <span class="label bg-orange pull-right"><?php echo $pd['paid_amount']; ?> </span></a>
											<span class="product-description">
												<?php echo $pd['paid_month']; ?>
											</span>
											<span class="product-description">
												At <?php echo date('h:i A', strtotime($pd['created_at'])); ?>
											</span>
										</div>
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="collection_report" class="uppercase">View All Collection</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Today Payment</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul class="products-list product-list-in-box">
							<table class='tbl table'>
								<?php
								$payment = all_exp($today, $today);
								foreach ($payment as $hd => $e_amt) {
								?>
									<tr>
										<td><?php echo add_space($hd); ?> </td>
										<td align='right'> <?php echo $e_amt; ?></td>
									</tr>
								<?php } ?>
							</table>
						</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="manage_account" class="uppercase">View All Payments</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Today Collection</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul2 class="products-list product-list-in-box">
							<table class='tbl table'>
								<?php
								$income = all_income($today, $today);
								foreach ($income as $ed => $i_amt) {
									if ($i_amt <> '') {
								?>
										<tr>
											<td><?php echo add_space($ed); ?> </td>
											<td align='right'> <?php echo $i_amt; ?></td>
										</tr>
								<?php }
								} ?>
							</table>
							</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="collection_report" class="uppercase">View All Collection</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

		</div>
		<!-- /.row -->
    <?php } ?>


	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('system/footer2.php'); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
	window.addEventListener('load', () => {
		registerSW();
	});

	// Register the Service Worker
	async function registerSW() {
		if ('serviceWorker' in navigator) {
			try {
				await navigator
					.serviceWorker
					.register('sw.js');
			} catch (e) {
				console.log('SW registration failed');
			}
		}
	}
</script>

<script type="text/javascript">
	google.charts.load('current', {
		'packages': ['corechart']
	});
	google.charts.setOnLoadCallback(paymentChart);
	google.charts.setOnLoadCallback(admissionChart);

	function paymentChart() {
		var data = google.visualization.arrayToDataTable(<?php echo monthly_collection_graph(); ?>);

		var options = {
			title: 'Total Vs Paid',
			chartArea: {
				left: 30,
				top: 20,
				width: "80%",
				height: "80%"
			}
		};

		var chart = new google.visualization.LineChart(document.getElementById('payment_chart'));

		chart.draw(data, options);
	}

	function admissionChart() {
		var data = google.visualization.arrayToDataTable(<?php echo new_admission_graph(); ?>);

		var options = {
			title: 'Class Vs New Admission',
			//chartArea:{left:30,top:20,width:"70%",height:"80%"}
		};

		var chart = new google.visualization.BarChart(document.getElementById('admission_chart'));

		chart.draw(data, options);
	}
</script>

<script>
	// Auto-submit form via AJAX for branch change
	$('#branch_select').on('change', function() {
		let form = $('#branch_form');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function(response) {
				console.log('Branch changed successfully:', response);
				location.reload();
				// Optionally handle the server response here
			},
			error: function(xhr, status, error) {
				console.error('Error changing branch:', error);
			}
		});
	});

	// Auto-submit form via AJAX for session change
	$('#session_select').on('change', function() {
		let form = $('#session_form');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function(response) {
				console.log('Session changed successfully:', response);
				location.reload();
				// Optionally handle the server response here
			},
			error: function(xhr, status, error) {
				console.error('Error changing session:', error);
			}
		});
	});
</script>