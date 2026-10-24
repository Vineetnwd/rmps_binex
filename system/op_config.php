<?php 
session_start();
ini_set('display_errors', 1);
$CONFIG['token'] = session_id();
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

date_default_timezone_set("Asia/Kolkata");
$CONFIG['current_date_time'] = date('Y-m-d H:i:s');
//$CONFIG['today'] = '2022-04-03'; 
$CONFIG['today'] =date('Y-m-d'); 
error_reporting(E_ERROR | E_PARSE);
/*-------Some Basic Details (Global Variables) ---------*/

$CONFIG['full_name'] ="R M Public School";
$CONFIG['inst_name'] ="RMPS";
$CONFIG['school_code'] ="65899";
$CONFIG['aff_no'] ="330874";
$CONFIG['aff_to'] ="Central Board of Secondary Education";
$CONFIG['inst_managed_by'] ="Sajawal Siksha Sansthan";
$CONFIG['inst_address1'] ="RMPS Campus, Bishambhrapur, Manjhagarh,  ";
$CONFIG['inst_address2'] ="Gopalganj, Bihar -841427";
$CONFIG['inst_contact'] ="9771637167";
$CONFIG['inst_email'] ="info@rmpublicschool.org";
$CONFIG['inst_logo'] ="images/logo.png";
$CONFIG['white_logo'] ="images/logo.png";
$CONFIG['banner'] ="images/banner.jpg";
$CONFIG['inst_url'] ="https://rmpublicschool.org";
$CONFIG['inst_type'] ="Institute";
$CONFIG['sender_id'] ="OFFAPP"; // AIRTEL STGHSB
$CONFIG['noreply_email'] ="noreply@sghs.morg.in";
$CONFIG['sms_auth_key'] ="370782AXr3d0jEz61bb1223P1"; //sms.morg.in MORGSMS 
/*---------Social Link ----------*/

$CONFIG['facebook'] ='https://facebook/OfferPlant';
$CONFIG['twitter'] ='https://twitter/OfferPlant';
$CONFIG['linkedin'] ='https://linkedin/company/OfferPlant';
$CONFIG['youtube'] ='https://youtube/OfferPlant';
$CONFIG['pinterest'] ='https://pinterest/OfferPlant';
$CONFIG['instagram'] ='https://instagram/OfferPlant';


$CONFIG['app_name'] ='BineX 5.0';
$CONFIG['dev_company'] ="OfferPlant Technologies Private Limited";
$CONFIG['dev_by'] ="OfferPlant";
$CONFIG['dev_url'] ="https://offerplant.com";
$CONFIG['dev_email'] ="ask@offerplant";
$CONFIG['dev_contact'] ="9431426600";
$CONFIG['api_key'] = '71c2ee1aa984518ccb3454640a12cf93';
$CONFIG['default_sms'] = 'send_sms';

// Auto-detect environment based on the server hostname
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // Local Configuration
    $CONFIG['host_name'] = 'localhost';
    $CONFIG['db_user'] = 'root';
    $CONFIG['db_password'] = '';
    $CONFIG['db_name'] = 'binex';
    $CONFIG['base_url'] = $protocol . $_SERVER['HTTP_HOST'] . '/binex';
} else {
    // Live Configuration
    $CONFIG['host_name'] = 'localhost';
    $CONFIG['db_user'] = 'u443617320_rmps_26';
    $CONFIG['db_password'] = '@User2001';
    $CONFIG['db_name'] = 'u443617320_rmps_26'; // Live database name
    $CONFIG['base_url'] = 'https://rmpublicschool.org/binex/';
    // $CONFIG['base_url'] = $_SERVER['HTTP_HOST'].'/binex/';
}


// DROP TABLE IF EXISTS exp_details; create VIEW exp_details  AS SELECT account_head.account_type AS account_type, account_txn.id AS id, account_txn.branch_id, account_txn.account_id AS account_id, account_txn.txn_date AS txn_date, account_txn.txn_type AS txn_type, account_txn.txn_amount AS txn_amount, account_txn.txn_mode AS txn_mode, account_txn.txn_remarks AS txn_remarks, account_txn.status AS status, account_txn.created_by AS created_by, account_txn.created_at AS created_at, account_txn.updated_by AS updated_by, account_txn.updated_at AS updated_at, account_txn.cancel_at AS cancel_at, account_txn.cancel_by AS cancel_by, account_txn.cancel_remarks AS cancel_remarks FROM (account_txn join account_head) WHERE account_txn.account_id = account_head.id ;

// DROP TABLE IF EXISTS payment_book; CREATE VIEW payment_book  AS SELECT student.student_name AS student_name, student.student_class AS student_class, student.student_section AS student_section, student.student_roll AS student_roll, student.student_type AS student_type, receipt.id AS id, receipt.receipt_no AS receipt_no, receipt.branch_id, receipt.student_id AS student_id, receipt.student_admission AS student_admission, receipt.created_at AS created_at, receipt.urid AS urid, receipt.paid_date AS paid_date, receipt.paid_month AS paid_month, receipt.previous_dues AS previous_dues, receipt.discount AS discount, receipt.transport_fee AS transport_fee, receipt.hostel_fee AS hostel_fee, receipt.other_fee AS other_fee, receipt.total AS total, receipt.paid_amount AS paid_amount, receipt.current_dues AS current_dues, receipt.payment_mode AS payment_mode, receipt.remarks AS remarks, receipt.status AS status, receipt.cancel_at AS cancel_at, receipt.cancel_by AS cancel_by, receipt.cancel_remarks AS cancel_remarks, receipt.created_by AS created_by, receipt.admission_fee AS admission_fee, receipt.tuition_fee AS tuition_fee, receipt.updated_by AS updated_by, receipt.updated_at AS updated_at, receipt.stationary_fee AS stationary_fee, receipt.school_development_fee AS school_development_fee FROM (student join receipt) WHERE receipt.student_id = student.id;

$CONFIG['month_list'] =$CONFIG['fee_month'] = array ('Admission_month','April','May','June','July','August','September','October','November','December','January','February','March');
$CONFIG['att_month_list'] =array ('April','May','June','July','August','September','October','November','December','January','February','March');

//$CONFIG['class_list'] =array('NUR','LKG','UKG','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
//$CONFIG['section_list'] =array('A','B');
$CONFIG['gender_list'] =array('MALE','FEMALE','OTHER');
$CONFIG['marital_status_list'] =array('SINGLE','MARRIED','DIVORCED');
$CONFIG['standard_list'] =array('PRIMARY','MIDDLE','SECONDARY','SENIOR SECONDARY');
$CONFIG['status_list'] =array('ACTIVE','BLOCK','INACTIVE','PROMOTED');
$CONFIG['religion_list'] =array('HINDU','MUSLIM','SHIKH','CHRISTIAN');
$CONFIG['caste_list'] =array('General','SC','ST','OBC');
$CONFIG['student_type_list'] =array('GENERAL','TRANSPORT','HOSTELER');
$CONFIG['bloodgroup_list'] =array('A+','B+','O+','AB+','A-','B-','O-','AB-');
$CONFIG['admission_list'] =array('NEW','OLD');
$CONFIG['academic_list'] =array('REGULAR','DISTANCE');
$CONFIG['finance_list'] =array('NORMAL','BPL','WARD','FREE');
$CONFIG['house_list'] =array('HOPE','PEACE','FAITH','COURAGE');
$CONFIG['scholarship_list'] =array(0,50,75,100);

$CONFIG['e_type_list'] =array('PGT','TGT','PRT','Teaching','Non Teaching','Administration','Counselor','Librarian');
$CONFIG['department_list'] =array('Administration','IT','Language','Science','Maths','SST','Non-Teaching');
$CONFIG['qualification_list'] = array('No Formal Education','Primary Education','Secondary Education or High School','Vocational Qualification','Bachelor\'s degree','Master\'s degree','Doctorate or higher');
$CONFIG['occupation_list'] = array('Unemployed','Private Job','Govt. Job','Self Employed','Professional','Farmer','Student','House Wife');
$CONFIG['income_list'] = array('No Income', 'Less Than 1,00,000', '1,00,001 to 3,00,000','3,00,001 to 10,00,000','10,00,001 to 25,00,000','25,00,001 or above');
$CONFIG['account_list'] =array('Tuition','Hostel','Transport','Director Cash','School A/c','Staff Salery','Office Expense','Miscellenous');
$CONFIG['user_type_list'] =array('ADMIN','ACCOUNT','DBA','PRO','EMP');
$CONFIG['co_grade_list'] =array('A','B','C');
$CONFIG['current_session'] ='2026-27';
$CONFIG['adm_suffix'] ='/2022-2023';
$CONFIG['fee_mode_list'] =array('Monthly','Annual','OneTime');
$CONFIG['fee_nature_list'] =array('CLASS'=>'Based On Class','STUDENT'=>'Based On Student','FIXED'=>'Fixed For All');
$CONFIG['subject_category_list'] =array('Scholastic','Non Scholastic');
$CONFIG['smc_list'] =array('PDF','YOUTUBE','GOOGLE MEET','RICH TEXT','OTHER');
/*-------End of Basic Details ---------*/

$CONFIG['session_list'] =array('u443617320_rmps_25'=>'2025-26','u443617320_rmps_26'=>'2026-27');

$CONFIG['source_list']      = array('FRIENDS', 'NEWSPAPER', 'POSTER', 'LOUDSPEAKER', 'GOOGLE SEARCH', 'SOCIAL MEDIA', 'CALL/MSG','WEBSITE');


/* LIBRARY CONFIGRATION */ 
$CONFIG['book_status'] =array('AVAILABLE','ISSUED','MISSING','REMOVED');
$CONFIG['book_txn_status'] =array('ISSUED','RETURN','MISSED');
$CONFIG['fine_per_day'] =1;
$CONFIG['max_book_allow'] =3;
$CONFIG['max_day_allow'] =15;

/*INVENTROY CONFIGRATION*/
$CONFIG['item_status'] = array('IN STOCK', 'OUT OF STOCK', 'REMOVED');
$CONFIG['payment_mode_list'] = array('CASH','BANK');
$CONFIG['gst_percent_list'] = array(0, 5, 12, 18, 28);


/* Account Configuration */ 

$CONFIG['account_head_list'] =array('SALERY','INFRASTRUCTURE','ENERGY','RENT', 'TRANSPORT', 'HOSTEL', 'STATIONARY','LIBRARY','LABORATORY', 'ACTIVITY', 'DAILY EXPENSES','MISCELLANEOUS','PRINCIPAL', 'BANK DIPOSIT');
$CONFIG['txn_mode_list'] =array('CASH','BANK','UNPAID','ONLINE');
$CONFIG['payment_status_list'] =array('ACTIVE'=>'PENDING','SUCCESS'=>'SUCCESS');
$CONFIG['allow_status'] =array('YES','NO');
$CONFIG['extra_subject'] =array('NO','YES');
$CONFIG['sitting_list'] =array('1st','2nd');
$CONFIG['search_by_list'] =array('student_admission'=>'Admission No','student_roll'=>'Roll No.','student_name'=>'Name','student_mobile'=>'Mobile No.','student_father'=>'Fathers name');

// Exam Setting 
$CONFIG['grade_list'] =array('A1','A2','B1','B2','C1','C2');
$CONFIG['marks_type_list'] =array('WRITTEN','VIVA');//'NB','SE',
$CONFIG['receipt_list'] =array('SCHOOL','PARENT');//'NB','SE',

$CONFIG['remarks_list'] = [
    "",
    "Outstanding performance!",
    "Great effort and progress!",
    "Good work, keep it up!",
    "Shows potential, focus on improvement.",
    "Needs individual support to excel.",
    "Aim for more consistency in attendance."
];


$CONFIG['receipt_size_list'] =array('2'=>'2 in A4 Page','4'=>'4 in A4 Page');

// Official WHATSAPP SETTING 
$wa_vendor ='04f81880-54aa-4413-a4b6-b51c669d26ff';
$wa_token ='14vw3hWTaCH8hJiKgPuPmnWmek5wUBVTLF0zQhHlN4lJdaYw2w3BxdXoFGBwSAIv';
$wa_owner ='9934860158';
// Default Branch ID
$branch_id =1;
sort($CONFIG['account_head_list']);
extract($CONFIG);

?>