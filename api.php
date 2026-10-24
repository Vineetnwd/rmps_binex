<?php
// ====== BASIC HEADERS ======
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include required functions
require_once('system/function.php');

// ====== GET TASK ======
$task = $_GET['task'] ?? $_REQUEST['task'] ?? '';

// ====== UNIVERSAL REQUEST DATA PARSER ======
$data = [];

/*
This supports ALL request types:
✔ JSON body  (React login, dashboard, etc.)
✔ form-urlencoded POST (OTP, forms)
✔ GET params
✔ multipart upload
*/

$contentType = $_SERVER["CONTENT_TYPE"] ?? '';

if (stripos($contentType, 'application/json') !== false) {

    // JSON BODY
    $json = file_get_contents('php://input');
    $decoded = json_decode($json, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $data = $decoded;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid JSON format"
        ]);
        exit;
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // FORM POST
    $data = $_POST;

} else {

    // GET REQUEST
    $data = $_GET;
}

// ====== OPTIONAL GLOBALS ======
$student_id = $data['student_id'] ?? '';

$branch_id = '';
if ($student_id != '') {
    $branch = fetch_all('student', ['branch_id'], ['id'=>$student_id])['data'] ?? [];
    $branch_id = $branch[0]['branch_id'] ?? '';
}

// ====== PHONEPE CONFIG ======
define('PHONEPE_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('PHONEPE_SALT_KEY', 'YOUR_SALT_KEY');
define('PHONEPE_SALT_INDEX', 1);
define('PHONEPE_ENV', 'PRODUCTION');

define('PHONEPE_BASE_URL',
    PHONEPE_ENV === 'PRODUCTION'
        ? 'https://api.phonepe.com/apis/hermes'
        : 'https://api-preprod.phonepe.com/apis/pg-sandbox'
);

// ====== START SWITCH ======
if ($task != '') {

    switch ($task)
		{
		    
		    case "login":
		        extract($data);
		        $res  = fetch_all('user',['id','full_name','user_type','status','branch_id','allowed_branch'],['user_name'=>$user_name, 'user_pass'=>md5($user_pass)]);
		        echo json_encode($res);
		        break;
		        
		    case "allowed_branch":
		        extract($data);
		        $sql = "SELECT id,inst_name FROM branch_list WHERE id IN ($allowed_branch)";
		        $res = direct_sql_api($sql)['data'];
		        echo json_encode($res);
		        break;
		        
		    case "search_student":
                $search_text = isset($data['search_text']) ? trim($data['search_text']) : '';
                if ($search_text !== '') {
                    $search_text = mysqli_real_escape_string($con, $search_text);
                    $sql = "
                        SELECT id, student_name, student_class, student_section, student_father, student_mobile, status FROM student
                        WHERE student_name LIKE '%$search_text%'
                           OR student_mobile LIKE '%$search_text%'
                           OR student_admission LIKE '%$search_text%'
                           AND status = 'ACTIVE' AND branch_id='{$data['branch_id']}'
                    ";
                    $res = direct_sql_api($sql);
                    // unset($res['sql']);
                    echo json_encode($res);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Missing or empty search_text parameter"
                    ]);
                }
                break;
            
                case "student_fee":
                extract($data);
                $_SESSION['branch_id'] = $branch_id;
                $month_list;
                $prev_dues =intval(get_data('student_fee',  $student_id,'current_dues', 'student_id')['data']);
                if($prev_dues <>0)
                {
                $all_fee['previous_dues'] = intval(get_data('student_fee',  $student_id,'current_dues', 'student_id')['data']);
                }
                foreach($month_list as $month)
                {
                    $fee = monthly_fee($student_id, $month);
                    $rid  = get_data('student_fee',  $student_id, remove_space($month) , 'student_id')['data'];
                    unset($fee['student_id']);
                    unset($fee['admission']);
                    unset($fee['month']);
                    $fee['fee']['status'] = ($rid === null || $rid === '') ? 'UNPAID' : 'PAID';
                    $all_fee[$month] = $fee['fee'];
                }
                echo json_encode($all_fee);
                break;
                 
            case "pay_fee":
    //             extract($data);
    //             // Month_list, totalt, Pad_amount, discount, data, payment_mode , remarks 
                
    //             $rdata = nmonth_fee_all($student_id, $months);
    //             $rdata['previous_dues'] =get_data('student_fee',$student_id,'current_dues', 'student_id')['data'];
    //             $rdata['paid_month'] =implode(",",$months);
    //             $rdata['student_id'] =$student_id;
    //             $rdata['student_admission'] = get_data('student',$student_id,'student_admission')['data'];
    //             $rdata['total'] =$total;
    //             $rdata['paid_amount'] =$paid_amount;
    //             $rdata['current_dues'] =$total -$paid_amount;
    //             $rdata['remarks'] =$remarks;
    //             $rdata['other_fee'] =$misc_fee;
    //             $rdata['discount'] =$discount;
    //             $rdata['paid_date'] = $payment_date;
    //             $rdata['created_by'] = $created_by;
    //             $rdata['payment_mode'] =($payment_mode=='cash')?"CASH":"BANK";
                
               
    //             $res = insert_data_bid('receipt',$rdata,$branch_id);
			
				// if($res['status']=='success')
				// {
				// 	$rid = $res['id'];
				// 	foreach($months as $month)
				// 	{
				// 		$old_value = get_data('student_fee',$student_id,remove_space($month),'student_id')['data'];
				// 		if($old_value!=null and $month=='other_month')
				// 		{
				// 			$rid =$old_value.",".$rid;	
				// 		}
				// 		$res2 = update_data('student_fee',array($month=>$rid,'current_dues'=>$current_dues),$student_id,'student_id');
				// 	}
				// }
				// echo json_encode($res);
                break;
            
            
            case "get_receipt": 
                extract($data);
                $receipt_info =  get_data('receipt', $receipt_id);
               
                if($receipt_info['count']>0)
                {
                    $student = get_data('student', $receipt_info['data']['student_id'])['data'];
                     
                    $receipt['student_name'] = $student['student_name'];
                    $receipt['admission_no'] = $student['student_admission'];
                    $receipt['student_class'] = $student['student_class'] . " " .$student['student_section'];
                    $receipt['father_name'] = $student['student_father'] ;
                    $receipt['paid_month'] = $receipt_info['data']['paid_month'];
                    $receipt['payment_date'] = $receipt_info['data']['paid_date'];
                    $receipt['receipt_id'] = $receipt_info['data']['id'];
                    $receipt['total'] = $receipt_info['data']['total'];
                    $receipt['paid_amount'] = $receipt_info['data']['paid_amount'];
                    $receipt['discount'] = $receipt_info['data']['discount'];
                    $receipt['misc_fee'] = $receipt_info['data']['other_fee'];
                    $receipt['remarks'] = $receipt_info['data']['remarks'];
                    $receipt['current_dues'] = $receipt_info['data']['current_dues'];
                    $receipt['fee_details'] =nmonth_fee_all($student['id'], explode(",", $receipt_info['data']['paid_month']));
                echo json_encode($receipt);
                }
                break;
            
            case "collection_report": 
                $from_date =$data['from_date']??$today;
                $to_date =$data['to_date']??$today;
                $sql ="select receipt.id as receipt_id, student.student_admission as admission_no, student.student_name, receipt.paid_amount, receipt.paid_date from receipt left join student on receipt.student_id =student.id where paid_date between '$from_date' and '$to_date' and receipt.status ='PAID' and receipt.branch_id='{$data['branch_id']}'";
                $res  =direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
                break;
            
            case "student_list";
                extract($data);
                $res  = fetch_all('student',['id','student_name','student_roll', 'student_admission'],['student_class'=>$student_class,'student_section'=>$student_section,'branch_id'=>$branch_id]);
                echo json_encode($res);
                break;
		    
		    case "make_att";
                extract($data);
                $att_month = strtolower(date('M_Y', strtotime($att_date)));
                $date =date('j', strtotime($att_date));
                $col_name ='d_'.$date;
                $p=$a =0;
                foreach($student_list as $student_id=>$status)
                {
                    $search = get_all('student_att','*',['student_id'=>$student_id, 'att_month'=>$att_month]);
                    ($status == 'P') ? $p++ : $a++;
                    if($search['count']>0)
                    {
                        $res = update_data('student_att', [$col_name =>$status], $search['data'][0]['id']);
                    }
                    else{
                        insert_data_bid('student_att', ['student_id'=>$student_id, 'att_month'=>$att_month, $col_name =>$status],$branch_id);
                    }
                }
                $res['present'] =$p;
                $res['absent'] =$a;
                echo json_encode($res);
                break;
		    
		    
		    case "dues_list":
		      //  print_r($data);
		        $sql ="select id, student_admission, student_mobile, student_name from student where student_class ='{$data['student_class']}' and student_section ='{$data['student_section']}' and branch_id='{$data['branch_id']}'";
		        $search = direct_sql($sql);
		      //  print_r($search);
		        foreach($search['data'] as $row)
		        {
		            $student_id = $row['id'];
		            $row['previous_dues'] = get_data('student_fee',$student_id,'current_dues', 'student_id')['data'];
		            $months = explode(",",$data['months']);
                    $row['fee']  = nmonth_fee_all($student_id, $months);
                    $res[] = $row;
		        }
		        echo json_encode($res);
                break;
            
            case "subject_list":
                extract($data);
                $hw_class = ($data['hw_class'] != '') ? $data['hw_class'] : $hdata['student_class'];
		        $sql  ="SELECT id, subject_name, status FROM subject where FIND_IN_SET( '$hw_class', student_class) and branch_id='{$branch_id}'";
		        $res = direct_sql($sql);
		        unset($res['sql']);
		        echo json_encode($res);
                break;
               
            case "home_work":
		        extract($data);
		        $hdata['hw_date'] =$today;
		        $hdata['hw_class'] =$hw_class;
		        $hdata['hw_section'] =$hw_section;
		        $hdata['subject_id'] =$subject_id;
		        $hdata['hw_text'] = $hw_text;
		        $hdata['hw_file'] =$hw_file;
		        $search =  fetch_all('homework', '*',['hw_date'=>$hw_date,'hw_class'=> $hw_date, 'subject_id' => $subject_id,'branch_id'=>$branch_id]);
		        if($search['count']>0)
		        {
		          //$res  = update_data('homework',$hdata, $search['data'][0]['id']);  
		          $res['msg'] ='Homework Already Assigned';
		          $res['status'] ='error';
		        }else{
		            $res  = insert_data_bid('homework',$hdata,$branch_id);
		        }
		        echo json_encode($res);
                break;
             
            case "class_list":
                $sql ="select student_class, student_section , count(id) as total from student where status ='ACTIVE' and branch_id='{$data['branch_id']}' group by student_class, student_section  ";
                $res =direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
                break;
            
            case "upload":
                
                // Directory to save uploaded files
                $target_dir = "homework/";
                
                // Create directory if not exists
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $response = [];
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                
                    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === 0) {
                        $file_name = basename($_FILES["file"]["name"]);
                        $target_file = $target_dir . $file_name;
                        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                        $file_size = $_FILES["file"]["size"];
                
                        // Allowed file types
                        $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];
                
                        // Validate file type
                        if (!in_array($file_type, $allowed_types)) {
                            $response = [
                                "success" => false,
                                "error" => "Invalid file type. Only JPG, PNG, GIF, and PDF are allowed."
                            ];
                            echo json_encode($response);
                            exit;
                        }
                
                        // Validate file size (max 5 MB)
                        if ($file_size > 5 * 1024 * 1024) {
                            $response = [
                                "success" => false,
                                "error" => "File too large. Maximum allowed size is 5 MB."
                            ];
                            echo json_encode($response);
                            exit;
                        }
                
                        // Move uploaded file
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                            $response = [
                                "success" => true,
                                "message" => "File uploaded successfully.",
                                "file_name" => $file_name,
                                "file_path" => $target_file,
                                "file_type" => $file_type,
                                "file_size" => round($file_size / 1024, 2) . " KB"
                            ];
                        } else {
                            $response = [
                                "success" => false,
                                "error" => "Failed to move uploaded file."
                            ];
                        }
                    } else {
                        $response = [
                            "success" => false,
                            "error" => "No file uploaded or upload error.",
                            "details" => $_FILES["file"]["error"] ?? null
                        ];
                    }
                } else {
                    $response = [
                        "success" => false,
                        "error" => "Invalid request method. Use POST."
                    ];
                }
                
                // Return JSON response
                echo json_encode($response, JSON_PRETTY_PRINT);
                break;
            
            // case "dashboard" :
            //     $user = get_data('user', $data['user_id'])['data'];
            //     //Total ACTIVE Student
            //     $total = get_all('student',['id'],['status'=>'ACTIVE'])['count'];
            //     // Totday Present
            //     $att_month = date("M-Y");
            //     $att_date = 'd_'.date("j");
            //     $sql ="SELECT count(id) as present FROM `student_att` where $att_date ='P' and att_month ='$att_month'";
            //     $att = direct_sql($sql);
            //     $absent  = $total - $att['data'][0]['present'];
                
            //     // total Collection Today
            //     $coll_sql ="SELECT sum(paid_amount) as total FROM `receipt` where paid_date ='$today' and status ='PAID'";
            //     $coll = direct_sql($coll_sql);
            //     $collection = $coll['data'][0]['total'] ?? 0;
            //     echo  json_encode(
            //         [
            //             'student'=> $total,
            //             'absent'=> $absent,
            //             'collection'=> $collection,
            //             'full_name'=> $user['full_name'],
            //             'user_type'=> $user['user_type'],
            //         ]);
            //     break;
            
            case "dashboard":
                $user = get_data('user', $data['user_id'])['data'];
                if($user['user_type']=='ADMIN' or $user['user_type']=='DEV'){
                    if(isset($data['branch_id']) && $data['branch_id']!=''){
                        $branch_id = $data['branch_id'];
                    }else{
                        $branch_id = $user['branch_id'];
                    }
                    $allowed_branch = $user['allowed_branch'];
                    $total = fetch_all('student', ['id'], ['status' => 'ACTIVE','academic_type'=>'REGULAR','branch_id'=>$branch_id])['count'];
                    $att_month = date("M-Y");
                    $att_date = 'd_' . date("j");
                    $sql = "SELECT COUNT(id) AS present FROM student_att WHERE $att_date = 'P' AND att_month = '$att_month' AND branch_id='$branch_id'";
                    $att = direct_sql_api($sql);
                    $absent = $total - ($att['data'][0]['present'] ?? 0);
                    $coll_sql = "SELECT SUM(paid_amount) AS total FROM receipt WHERE paid_date = '$today' AND status = 'PAID'  AND branch_id='$branch_id'";
                    $coll = direct_sql_api($coll_sql);
                    $collection = $coll['data'][0]['total'] ?? 0;
                    $recent_receipt_sql = "SELECT id, student_id, paid_amount, paid_date FROM receipt WHERE status='PAID'  AND branch_id='$branch_id' ORDER BY id DESC LIMIT 5";
                    $recent_receipts = direct_sql_api($recent_receipt_sql);
                    $recent_student_sql = "SELECT id, student_name, created_at FROM student WHERE branch_id='$branch_id' ORDER BY id DESC LIMIT 5";
                    $recent_students = direct_sql_api($recent_student_sql);
                    echo json_encode([
                        'branch_id'       => $branch_id,
                        'allowed_branch'  => $allowed_branch,
                        'student'         => $total,
                        'absent'          => $absent,
                        'collection'      => $collection,
                        'full_name'       => $user['full_name'],
                        'user_type'       => $user['user_type'],
                        'recent_receipts' => $recent_receipts['data'] ?? [],
                        'recent_students' => $recent_students['data'] ?? []
                    ]);
		        }else{
		            echo json_encode(['status'=> 'error','msg'=> 'You are Not allowed']);
		        }
                break;
                
                case "teacher_dash":
                    $user = get_data('user', $data['user_id'])['data'];
                    if ($user['user_type'] == 'TEACHER') {
                        $emp = get_data('employee', $user['user_name'], null, 'emp_code')['data'];
                        if (isset($data['branch_id']) && $data['branch_id'] != '') {
                            $branch_id = $data['branch_id'];
                        } else {
                            $branch_id = $user['branch_id'];
                        }
                        $allowed_classes = direct_sql_api("
                            SELECT id, class_name, section_name 
                            FROM teacher_subject 
                            WHERE teacher_id='{$emp['id']}'
                        ")['data'];
                        $att_date  = 'd_' . date("j");
                        $today     = date("Y-m-d");
                        $att_month = date("m");
                        $total = 0;
                        $present_total = 0;
                        foreach ($allowed_classes as $allowed_class) {
                            $class   = $allowed_class['class_name'];
                            $section = $allowed_class['section_name'];
                            $total_sql = "
                                SELECT COUNT(id) AS total 
                                FROM student 
                                WHERE student_class='$class'
                                AND student_section='$section'
                                AND branch_id='$branch_id'
                                AND status='ACTIVE'
                            ";
                            $total_res   = direct_sql_api($total_sql);
                            $class_total = $total_res['data'][0]['total'] ?? 0;
                    
                            $total += $class_total;
                            $att_sql = "
                                SELECT COUNT(sa.id) AS present
                                FROM student_att sa
                                JOIN student s ON s.id = sa.student_id
                                WHERE s.student_class='$class'
                                AND s.student_section='$section'
                                AND sa.$att_date='P'
                                AND sa.att_month='$att_month'
                                AND sa.branch_id='$branch_id'
                            ";
                            $att_res = direct_sql_api($att_sql);
                            $present_total += ($att_res['data'][0]['present'] ?? 0);
                        }
                        $absent = $total - $present_total;
                        $coll_sql = "
                            SELECT SUM(paid_amount) AS total 
                            FROM receipt 
                            WHERE paid_date='$today' 
                            AND status='PAID'  
                            AND branch_id='$branch_id'
                        ";
                        $coll = direct_sql_api($coll_sql);
                        $collection = $coll['data'][0]['total'] ?? 0;
                        $recent_receipt_sql = "
                            SELECT id, student_id, paid_amount, paid_date 
                            FROM receipt 
                            WHERE status='PAID'  
                            AND branch_id='$branch_id' 
                            ORDER BY id DESC 
                            LIMIT 5
                        ";
                        $recent_receipts = direct_sql_api($recent_receipt_sql);
                        $recent_student_sql = "
                            SELECT id, student_name,student_class,student_section, created_at 
                            FROM student 
                            WHERE branch_id='$branch_id' 
                            ORDER BY id DESC 
                            LIMIT 5
                        ";
                        $recent_students = direct_sql_api($recent_student_sql);
                        echo json_encode([
                            'branch_id'       => $branch_id,
                            'allowed_classes' => $allowed_classes,
                            'class_teacher'   => $emp['class_teacher'],
                            'user_id'         => $user['id'],
                            'emp_id'          => $emp['id'],
                            'student'         => $total,
                            'present'         => $present_total,
                            'absent'          => $absent,
                            'collection'      => $collection,
                            'full_name'       => $emp['full_name'],
                            'user_type'       => $user['user_type'],
                            'recent_receipts' => $recent_receipts['data'] ?? [],
                            'recent_students' => $recent_students['data'] ?? []
                        ]);
                    
                    } else {
                    
                        echo json_encode([
                            'status'=> 'error',
                            'msg'=> 'You are Not allowed'
                        ]);
                    }
                    
                    break;


                
		    case "notice" :
                extract($data);
		        $data['notice_date'] = $data['notice_date'] ??$today;
		        $data['status']='ACTIVE';
		        $res = insert_data_bid("notice", $data,$branch_id);
		        unset($res['sql']);
                echo json_encode($res);
                break;
            
            case "leave_applied" :
                $res = direct_sql("SELECT id,status,student_id,from_date,to_date,cause FROM student_leave WHERE status NOT IN ('AUTO','DELETED') and branch_id='{$data['branch_id']}'");
		        unset($res['sql']);
                echo json_encode($res);
                break;
                
            case "leave_update" :
                extract($data);
                $res = update_data('student_leave',$data,$id);
                echo json_encode($res);
                break;
                
            case "complaints" :
                extract($data);
                if($user_type=='ADMIN'){
                    $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.status NOT IN ('AUTO','DELETED')";
                    $res = direct_sql($sql);
                }else if($user_type=='ACCOUNT'){
                    $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.complaint_to='ACCOUNT' AND complaints.status NOT IN ('AUTO','DELETED')";
                    $res = direct_sql($sql);
                }else{
                    $res = null;
                    $res['status'] = 'error';
                    $res['msg'] = 'You Are Not Authorised';
                }
    			unset($res['sql']);
                echo json_encode($res);
                break;
                
            case "update_complaints" :
                extract($data);
                $res = update_data('complaints',$data,$id);
                echo json_encode($res);
                break;
                
    		case "send_otp":
    			extract($data);
    			$res =fetch_all('student', ['id'], ['student_mobile'=>$student_mobile,'status'=>'ACTIVE']);//,'date_of_birth'=>$date_of_birth
        			if($res['status'] =='success' and $res['count']>0)
        			{
        				$res['otp'] = $otp = 9999;// rand(1000,9999);
            	        $res['msg'] = $msg = "Thanks for interest in  $inst_name  Your App Login OTP is $otp ";
            	        update_data('student',['otp'=>$otp],$res['data']['0']['id']);
            	        //sendsms($student_mobile, $msg);
        			}else{
        			    $res = null;
    				    $res['status']='error';
    				    $res['msg'] = 'No Data Found';
        			}
    			unset($res['sql']);
    			echo json_encode($res);
    			break;
                
    		case "get_otp_old":
    			extract($data);
    			$res = get_all('student', ['id', 'otp','student_admission','student_name', 'student_class', 'student_section', 'student_roll','student_type','student_photo','student_sex','student_mobile','branch_id'], ['student_mobile'=>$student_mobile,'status'=>'ACTIVE']);
        			if($res['count']>0)
        			{
        				if($otp==$res['data']['0']['otp']){
    			         //   $res = $res['data'];
    			            $res['msg'] = "OTP VERIFIED";
    			            $res['current_dues'] = get_data('student_fee', $res['data']['0']['student_admission'], 'current_dues', 'student_admission')['data'];
        				}else{
        				    $res = null;
        				    $res['status']='error';
        				    $res['msg'] = 'OTP Mismatched';
        				}
        			}else{
        			    $res = null;
    				    $res['status']='error';
    				    $res['msg'] = 'OTP Mismatched';
        			}
    			unset($res['sql']);
    			echo json_encode($res);
    			break;
    			
    		case "get_otp":
                    extract($data);
                
                    // Fetch all students with their current dues and total paid amount
                    $sql = "
                        SELECT 
                            s.id,
                            s.otp,
                            s.student_admission,
                            s.student_name,
                            s.student_class,
                            s.student_section,
                            s.student_roll,
                            s.student_type,
                            s.student_photo,
                            s.student_sex,
                            s.student_mobile,
                            s.branch_id,
                            f.current_dues,
                            COALESCE(SUM(r.paid_amount), 0) AS total_paid
                        FROM student s
                        LEFT JOIN student_fee f ON s.student_admission = f.student_admission
                        LEFT JOIN receipt r ON s.student_admission = r.student_admission
                        WHERE s.student_mobile = '$student_mobile' AND s.status = 'ACTIVE'
                        GROUP BY 
                            s.id, s.otp, s.student_admission, s.student_name, s.student_class, 
                            s.student_section, s.student_roll, s.student_type, s.student_photo, 
                            s.student_sex, s.student_mobile, f.current_dues
                    ";
                
                    $res = direct_sql_api($sql);
                    if($branch_id == 1){
                        $img_url = 'student_photo/Bishambharpur/Photo/';
                    }
                    else if($branch_id == 2){
                        $img_url = 'student_photo/Barauli/Photo/';
                    }
                    else{
                        $img_url = 'student_photo/Barharia/Photo/';
                    }
                    
                    $res['data']['0']['student_photo'] = $img_url.$res['data']['0']['student_admission'].'jpg';
 
                    if ($res['count'] > 0) {
                        $res['notices'] = get_all('notice',['id','notice_date','notice_title','notice_details','notice_attachment'],['status'=>'ACTIVE'],'id desc limit 3')['data'];
                        $res = $res;
                    } else {
                        $res = [
                            'status' => 'error',
                            'msg' => 'No records found'
                        ];
                    }
                
                    echo json_encode($res);
                    break;

    			
            case "get_attendance" : 
                extract($data); 
                $res = fetch_all('student_att','*',['student_id'=>$student_id])['data']; 
                echo json_encode($res); 
                break;
                
			    
			case "get_homework" :
			 //   print_r($data);
			    extract($data);
			    $sinfo = get_data('student',$student_id)['data'];
			    $res= fetch_all('homework',['id', 'hw_date', 'hw_class', 'hw_section', 'subject_id', 'hw_text', 'hw_file'],['hw_class'=>$sinfo['student_class'],'hw_section'=>$sinfo['student_section'],'hw_date'=>$hw_date]);
		        $res = $res['data'];
			    echo json_encode($res);
			    break;
			    
			case "get_notice" :
			    $sql = "SELECT id,notice_date,notice_title,notice_details,notice_attachment FROM notice WHERE status='ACTIVE' AND branch_id='$branch_id'";
			    $res= direct_sql($sql)['data'];
			    echo json_encode($res);
			    break;
			    
			    
		    case "send_complaint" :
		        $data['complaint_date'] = $data['complaint_date'] ??$today;
		        $res = insert_data_bid("complaints", $data,$data['branch_id']);
		        unset($res['sql']);
                echo json_encode($res);
                break;
                
            case "send_review" :
			    extract($data);
		        $review = get_data("review",$student_id,null,'student_id');
                if($review['count']>0){
                    $res = update_data("review",$data,$student_id,'student_id');
                }else{
		            $res = insert_data_bid("review", $data,$data['branch_id']);
                }
		        unset($res['sql']);
		        if($res['status']=='success'){
		            $res['msg'] == 'Review Submitted';
		        }
                echo json_encode($res);
                break;
                
            case "get_review" :
			    extract($data);
		        $res = get_data("review",$student_id,null,'student_id')['data'];
                echo json_encode($res);
                break;
                
            case "get_student_profile":
    extract($data);
    // $student_id and $branch_id are already set by extract($data)
    
    $sql = "
        SELECT 
            s.*,
            f.current_dues,
            COALESCE(SUM(r.paid_amount), 0) AS total_paid
        FROM student s
        LEFT JOIN student_fee f ON s.student_admission = f.student_admission
        LEFT JOIN receipt r ON s.student_admission = r.student_admission
        WHERE s.id = '$student_id' AND s.status = 'ACTIVE' AND s.branch_id = '$branch_id'
        GROUP BY 
            s.id, s.otp, s.student_admission, s.student_name, s.student_class, 
            s.student_section, s.student_roll, s.student_type, s.student_photo, 
            s.student_sex, s.student_mobile, f.current_dues
    ";
    $res = direct_sql_api($sql);

    if ($branch_id == 1) {
        $img_url = 'student_photo/Bishambharpur/Photo/';
    } else if ($branch_id == 2) {
        $img_url = 'student_photo/Barauli/Photo/';
    } else {
        $img_url = 'student_photo/Barharia/Photo/';
    }

    $res['data']['0']['student_photo'] = $img_url . $res['data']['0']['student_admission'] . 'jpg';

    if ($res['count'] > 0) {
        $res = $res['data'];
    } else {
        $res = [];
        $res['status'] = 'error';
        $res['msg'] = 'No data found';
    }
    echo json_encode($res);
    break;

                
            case "help_and_support" :
			    extract($data);
			    $res = get_data('branch_list',$branch_id)['data'];
			    echo json_encode($res);
                break;
                
            case "student_leave_apply" :
			    extract($data);
			    $data['status']='PENDING';
		        $res = insert_data_bid("student_leave", $data,$branch_id);
		        unset($res['sql']);
			    echo json_encode($res);
                break;
                
            case "holiday_list" :
                $res = get_all('holiday',['id','holiday_name','holiday_date'],['status'=>'ACTIVE']);
		        unset($res['sql']);
			    echo json_encode($res);
                break;
                
            case "payment_history" :
                extract($data);
                $fee_head = get_all('fee_head', '*', ['status' => 'ACTIVE']);
                $fee_columns = [];
                
                foreach ($fee_head['data'] as $fee) {
                    $fee_columns[] = remove_space($fee['fee_name']);
                }
                $default_columns = [
                    'id AS receipt_id',
                    'paid_date',
                    'status',
                    'paid_month',
                    'other_fee',
                    'total',
                    'paid_amount',
                    'current_dues',
                    'remarks'
                ];
                
                $all_columns = array_merge($default_columns, $fee_columns);
                $columns_str = implode(", ", $all_columns);
                $sql = "SELECT $columns_str FROM receipt WHERE student_id = '$student_id' ORDER BY paid_date DESC";
                $res = direct_sql($sql);
		        unset($res['sql']);
			    echo json_encode($res);
                break;
                
            case "get_exam":
                extract($data);
                $exam_list = fetch_all('exam_list',['id','exam_name'],['status'=>'ACTIVE','branch_id'=>$branch_id])['data'];
                // print_r($exam_list);
                echo json_encode($exam_list);
                break;
                
          case "get_marks":
                extract($data);
            
                $student_id = $data['student_id'];
                $branch = fetch_all('student','*',['id'=>$student_id])['data'];
                $branch_id = $branch['0']['branch_id'];
                
                // Fetch student info
                $studentData = fetch_all('student','*',['id'=>$student_id]);
                if ($studentData['data'] < 1) {
                    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
                    exit;
                }
                $student = $studentData['data'];
            
                // Get list of subjects for the student's class
                $subjects = subject_list($student['student_class'],$branch_id);
                $exam_name = fetch_all('exam_list',['exam_name'],['id'=>$exam_id])['data']['0']['exam_name'];
                print_r($exam_name);
                if($exam_name=='UNIT TEST 1' or $exam_name=='UNIT TEST 2'){
                    $term = 'TERM 1';
                    if($exam_name=='UNIT TEST 1'){
                        $ut = 'ut1';
                    }else if($exam_name=='UNIT TEST 2'){
                        $ut = 'ut2';
                    }
                }else if($exam_name=='UNIT TEST 3' or $exam_name=='UNIT TEST 4'){
                    $term = 'TERM 2';
                    if($exam_name=='UNIT TEST 3'){
                        $ut = 'ut3';
                    }else if($exam_name=='UNIT TEST 4'){
                        $ut = 'ut4';
                    }
                }
                if($exam_name=='UNIT TEST 1' or $exam_name=='UNIT TEST 2' or $exam_name=='UNIT TEST 3' or $exam_name=='UNIT TEST 4'){
                    // $examResults = fetch_all('tabulation', [$ut,'subject'], ['student_id' => $sid, 'exam_id' => $exam_id]);
                    $examResults = direct_sql_api("SELECT * FROM tabulation WHERE exam_id='$exam_id' AND student_id='$sid'");
                    // 'tabulation', [$ut,'subject'], ['student_id' => $sid, 'exam_id' => $exam_id]);
                    echo $sql = "SELECT 
                        t.id,
                        t.subject,
                        t.$ut,
                        s.subject_name
                    FROM tabulation t 
                    INNER JOIN subject s ON tabulation.subject=subject.id
                    WHERE t.student_id='$sid' AND exam_id='$exam_id' AND t.branch_id='$branch_id'";
                    $res = direct_sql_api($sql);
                }else{
                    $examResults = fetch_all('tabulation', '*', ['student_id' => $sid, 'exam_id' => $exam_id]);
                }
                $examData = !empty($examResults['data']) ? $examResults['data'][0] : [];
            
                // Initialize totals
                $totalMarks = 0;
                $responseData = [];
            
                foreach ($subjects as $subject_id) {
                    // Get subject info
                    $subjectInfo = get_data('subject', $subject_id);
                    $subjectName = $subjectInfo['data']['subject_name'] ?? 'Unknown';
                    $subjectCol  = $subjectInfo['data']['subject_column'] ?? '';
            
                    // Get marks
                    $marks = get_marks($student['student_admission'], $exam_name, $subjectCol);
                    // $pt = $marks['pt'] ?? 0;
                    $nb = $marks['nb'] ?? 0;
                    $se = $marks['se'] ?? 0;
                    $mo = $marks['mo'] ?? 0;
            
                    $total = $nb + $se + $mo;
                    $totalMarks += $total;
            
                    // Build subject record
                    $responseData[] = [
                        'subject_id'   => $subject_id,
                        'subject_name' => $subjectName,
                        'marks' => [
                            'nb' => $nb,
                            'se' => $se,
                            'mo' => $mo,
                            'total' => $total
                        ]
                    ];
                }
            
                // ✅ Handle Co-Scholastic Areas
                $coScholasticData = [];
                if (!empty($co_scholastic_list)) {
                    foreach ($co_scholastic_list as $co) {
                        $key = remove_space($co);
                        $coScholasticData[] = [
                            'area' => $co,
                            'grade' => $co_marks[$key] ?? 'N/A'
                        ];
                    }
                }
                // Final response
                echo json_encode([
                    'status' => 'success',
                    'student' => [
                        'id' => $sid,
                        'name' => $student['student_name'] ?? '',
                        'class' => $student['student_class'] ?? ''
                    ],
                    'exam_name' => $exam_name,
                    'subjects' => $responseData,
                    'co_scholastic' => $coScholasticData, // ✅ Added section
                    'grand_total' => $totalMarks
                ]);
                break;

			    
			case "st_complaints" :
                extract($data);
                    $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.student_id='$student_id' AND complaints.status NOT IN ('AUTO','DELETED') AND complaints.branch_id='$branch_id'";
                    $res = direct_sql($sql);
    			unset($res['sql']);
                echo json_encode($res);
                break;
                
			case "st_leave_applications" :
                extract($data);
                    $sql = "SELECT id,status,student_id,from_date,to_date,cause 
                    FROM student_leave
                    WHERE student_id='$student_id' AND student_leave.status NOT IN ('AUTO','DELETED') AND branch_id='$branch_id'";
                    $res = direct_sql($sql);
    			unset($res['sql']);
                echo json_encode($res);
                break;
                
		        
		    case 'initiate_phonepe_payment':
                $input = json_decode(file_get_contents('php://input'), true);
                
                $student_id = $input['student_id'] ?? '';
                $sinfo = get_data('student',$student_id);
                $student_name = $sinfo['student_name'] ?? '';
                $student_mobile = $sinfo['student_mobile'] ?? '';
                $amount = $input['amount'] ?? 0;
                $transaction_id = $input['transaction_id'] ?? '';
                $selected_months = $input['selected_months'] ?? [];
                $payment_method = $input['payment_method'] ?? 'PhonePe';
                
                // Validate inputs
                if (empty($student_id) || empty($amount) || empty($transaction_id)) {
                    echo json_encode(['error' => 'Missing required fields']);
                    exit;
                }
                
                // Convert selected months to JSON string
                $months_json = json_encode($selected_months);
                
                // ✅ INSERT into phonepe_transactions table (matching your table structure)
                $sql = "INSERT INTO phonepe_transactions (
                    merchant_transaction_id,
                    transaction_id,
                    student_id,
                    amount,
                    months,
                    status,
                    payment_data,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())";
                
                $stmt = $con->prepare($sql);
                
                // Store payment request data as JSON
                $payment_data = json_encode([
                    'student_name' => $student_name,
                    'payment_method' => $payment_method,
                    'selected_months' => $selected_months
                ]);
                
                $stmt->bind_param(
                    "sssdss",
                    $transaction_id,           // merchant_transaction_id
                    $transaction_id,           // transaction_id (same for now)
                    $student_id,              // student_id
                    $amount,                  // amount
                    $months_json,             // months (JSON array)
                    $payment_data             // payment_data (JSON object)
                );
                
                if (!$stmt->execute()) {
                    echo json_encode(['error' => 'Error in Inserting Data: ' . $stmt->error]);
                    exit;
                }
                
                $payment_id = $con->insert_id;
                
                // ✅ Now initiate PhonePe payment
                // PhonePe API configuration
                $merchant_id = "YOUR_MERCHANT_ID";
                $salt_key = "YOUR_SALT_KEY";
                $salt_index = "1";
                
                // PhonePe payment request
                $phonepe_data = [
                    'merchantId' => $merchant_id,
                    'merchantTransactionId' => $transaction_id,
                    'merchantUserId' => 'MUID' . $student_id,
                    'amount' => $amount * 100, // Convert to paise
                    'redirectUrl' => 'https://dpsmushkipur.com/bine/api.php?action=phonepe_callback',
                    'redirectMode' => 'POST',
                    'callbackUrl' => 'https://dpsmushkipur.com/bine/api.php?action=phonepe_callback',
                    'mobileNumber' => $student_mobile, // Get from student if available
                    'paymentInstrument' => [
                        'type' => 'PAY_PAGE'
                    ]
                ];
                
                // Encode payload
                $json_encode = json_encode($phonepe_data);
                $payload = base64_encode($json_encode);
                
                // Generate checksum
                $checksum_string = $payload . '/pg/v1/pay' . $salt_key;
                $checksum = hash('sha256', $checksum_string) . '###' . $salt_index;
                
                // Make API call to PhonePe
                $url = 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay'; // Use production URL for live
                
                $headers = [
                    'Content-Type: application/json',
                    'X-VERIFY: ' . $checksum
                ];
                
                $request_data = [
                    'request' => $payload
                ];
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                $response_data = json_decode($response, true);
                
                // Update payment_response in database
                $update_sql = "UPDATE phonepe_transactions SET payment_response = ? WHERE id = ?";
                $update_stmt = $con->prepare($update_sql);
                $update_stmt->bind_param("si", $response, $payment_id);
                $update_stmt->execute();
                
                if ($http_code == 200 && $response_data['success'] === true) {
                    $payment_url = $response_data['data']['instrumentResponse']['redirectInfo']['url'];
                    
                    echo json_encode([
                        'success' => true,
                        'payment_url' => $payment_url,
                        'merchant_transaction_id' => $transaction_id,
                        'payment_id' => $payment_id
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => $response_data['message'] ?? 'PhonePe payment initiation failed'
                    ]);
                }
                
                break;
            
            case "check_phonepe_payment":
                extract($data);
                
                // PhonePe Configuration
                $merchant_id = "SU2509191800322669510083";
                $salt_key = "5c5a0b98-69ee-4edc-a248-cc23a74b8862";
                $salt_index = 1;
                
                // Get transaction details from database
                $transaction = get_data('phonepe_transactions', $merchant_transaction_id, 'merchant_transaction_id', 'merchant_transaction_id');
                
                if($transaction['count'] > 0) {
                    $txn_data = $transaction['data'];
                    
                    // Check payment status with PhonePe
                    $status_url = "https://api.phonepe.com/apis/hermes/pg/v1/status/" . $merchant_id . "/" . $merchant_transaction_id;
                    
                    // Generate X-VERIFY for status check
                    $string_to_hash = '/pg/v1/status/' . $merchant_id . '/' . $merchant_transaction_id . $salt_key;
                    $sha256 = hash('sha256', $string_to_hash);
                    $x_verify = $sha256 . '###' . $salt_index;
                    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $status_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'X-VERIFY: ' . $x_verify,
                            'X-MERCHANT-ID: ' . $merchant_id,
                            'accept: application/json'
                        ),
                    ));
                    
                    $response = curl_exec($curl);
                    curl_close($curl);
                    
                    $response_data = json_decode($response, true);
                    
                    if($response_data['success'] == true && $response_data['code'] == 'PAYMENT_SUCCESS') {
                        // Payment successful - create receipt
                        $student_id = $txn_data['student_id'];
                        $months = explode(",", $txn_data['months']);
                        $total_amount = $txn_data['amount'];
                        
                        // Prepare receipt data similar to admin pay_fee
                        $rdata = nmonth_fee_all($student_id, $months);
                        $rdata['previous_dues'] = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
                        $rdata['paid_month'] = implode(",", $months);
                        $rdata['student_id'] = $student_id;
                        $rdata['student_admission'] = get_data('student', $student_id, 'student_admission')['data'];
                        $rdata['total'] = $total_amount;
                        $rdata['paid_amount'] = $total_amount;
                        $rdata['current_dues'] = 0; // Fully paid
                        $rdata['remarks'] = "Online Payment via PhonePe - TxnID: " . $merchant_transaction_id;
                        $rdata['other_fee'] = 0;
                        $rdata['discount'] = 0;
                        $rdata['paid_date'] = date('Y-m-d');
                        $rdata['created_by'] = $student_id; // Student made payment
                        $rdata['payment_mode'] = "ONLINE";
                        $rdata['phonepe_txn_id'] = $merchant_transaction_id;
                        
                        $res = insert_data_bid('receipt', $rdata,$branch_id);
                        
                        if($res['status'] == 'success') {
                            $rid = $res['id'];
                            $current_dues = 0;
                            
                            // Update student_fee table
                            foreach($months as $month) {
                                $old_value = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
                                if($old_value != null && $month == 'other_month') {
                                    $rid = $old_value . "," . $rid;
                                }
                                $res2 = update_data('student_fee', array(remove_space($month) => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
                            }
                            
                            // Update transaction status
                            update_data('phonepe_transactions', array(
                                'status' => 'COMPLETED',
                                'receipt_id' => $rid,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'payment_response' => json_encode($response_data)
                            ), $merchant_transaction_id, 'merchant_transaction_id');
                            
                            echo json_encode(array(
                                'status' => 'COMPLETED',
                                'receipt_id' => $rid,
                                'message' => 'Payment successful'
                            ));
                        }
                    } else if($response_data['code'] == 'PAYMENT_PENDING') {
                        echo json_encode(array(
                            'status' => 'PENDING',
                            'message' => 'Payment is still pending'
                        ));
                    } else {
                        // Update transaction as failed
                        update_data('phonepe_transactions', array(
                            'status' => 'FAILED',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'payment_response' => json_encode($response_data)
                        ), $merchant_transaction_id, 'merchant_transaction_id');
                        
                        echo json_encode(array(
                            'status' => 'FAILED',
                            'message' => 'Payment failed'
                        ));
                    }
                } else {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Transaction not found'
                    ));
                }
                break;
                
            case "admit_card":
                extract($data);
                $st_class = get_data('student',$student_id,'student_class','id')['data'];
                $sql = "SELECT ac.*, 
                        s.subject_name
                    FROM admit_card ac
                    JOIN subject s ON ac.subject_id = s.id
                    WHERE exam_id = '$exam_id' AND ac.student_class = '$st_class' AND ac.status='ACTIVE'AND ac.exam_date!='0000-00-00' AND ac.branch_id ='$branch_id'";
                $res = direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
                break;
                
            case "live_classes":
                extract($data);
            
                $sinfo = get_data('student', $student_id)['data'];
                if (!$sinfo) {
                    return [
                        "status" => "error",
                        "message" => "Student not found"
                    ];
                }
                $student_class   = $sinfo['student_class'];
                $student_section = $sinfo['student_section'];
                $branch_id       = $sinfo['branch_id'];
                $sql = "SELECT lc.*, s.subject_name
                        FROM live_classes lc
                        JOIN subject s ON lc.subject = s.id 
                        WHERE lc.student_class = '$student_class'
                            AND lc.student_section = '$student_section'
                            AND lc.branch_id = '$branch_id'
                            AND lc.status = 'ACTIVE'";
                $res = direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
                break;
                
                
            case "assigned_subject" :
                extract($data);
                $sql = "SELECT ts.id,
                            ts.class_name,
                            ts.section_name,
                            ts.subject_name as subject_id,
                            s.subject_name 
                    FROM teacher_subject ts 
                    LEFT JOIN subject s ON ts.subject_name = s.id
                    WHERE ts.status = 'ACTIVE' 
                    AND ts.teacher_id='$emp_id'
                    AND ts.branch_id = '$branch_id'";

                $res = direct_sql_api($sql);
                unset($res['sql']);
                echo json_encode($res);
                break;
                
            case "marks_permission":
                extract($data);
                $res = get_data('employee',$emp_id,'marks_entry');
                $res['can_edit']= $res['data'];
                unset($res['data']);
                echo json_encode($res);
                break;
                
            case "student_marks":
                $student_class   = $data['student_class'] ?? '';
                $student_section = $data['student_section'] ?? '';
                $subject_id      = $data['subject_id'] ?? '';
                $exam_id         = $data['exam_id'] ?? '';
                $branch_id       = $data['branch_id'] ?? '';
                if (empty($student_class) || empty($student_section) || empty($subject_id) || empty($exam_id)) {
                    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
                    exit;
                }
                // Determine term from exam_id
                if ($exam_id == 'UNIT TEST 1' || $exam_id == 'UNIT TEST 2' || $exam_id == 'TERM 1') {
                    $term = 'TERM 1';
                } else {
                    $term = 'TERM 2';
                }
                // Determine which columns to show based on exam and class
                $columns = [];
                $max_marks = [];
                if ($exam_id == 'UNIT TEST 1') {
                    $columns = ['ut1'];
                    $max_marks = ['ut1' => 80];
                } elseif ($exam_id == 'UNIT TEST 2') {
                    $columns = ['ut2'];
                    $max_marks = ['ut2' => 80];
                } elseif ($exam_id == 'UNIT TEST 3') {
                    $columns = ['ut3'];
                    $max_marks = ['ut3' => 80];
                } elseif ($exam_id == 'UNIT TEST 4') {
                    $columns = ['ut4'];
                    $max_marks = ['ut4' => 80];
                } elseif ($exam_id == 'TERM 1' || $exam_id == 'TERM 2') {
                    if ($student_class == 'IX' || $student_class == 'X') {
                        $columns = ['ma', 'portfolio', 'se', 'mid_term'];
                        if ($subject_id == '46') {
                            $max_marks = ['ma' => 5, 'portfolio' => 5, 'se' => 10, 'mid_term' => 50];
                        } else {
                            $max_marks = ['ma' => 5, 'portfolio' => 5, 'se' => 5, 'mid_term' => 80];
                        }
                    } else {
                        $columns = ['ga', 'nb', 'sea', 'mid_term'];
                        if ($student_class == 'I' || $student_class == 'II') {
                            $max_marks = ['ga' => 20, 'nb' => 5, 'sea' => 5, 'mid_term' => 60];
                        } else {
                            $max_marks = ['ga' => 20, 'nb' => 5, 'sea' => 5, 'mid_term' => 80];
                        }
                    }
                }
                // Column labels for display
                $column_labels = [
                    'ut1' => 'UT 1', 'ut2' => 'UT 2', 'ut3' => 'UT 3', 'ut4' => 'UT 4',
                    'ma' => 'MA', 'portfolio' => 'Portfolio', 'se' => 'SE',
                    'ga' => 'GA', 'nb' => 'NB', 'sea' => 'SEA', 'mid_term' => 'Mid Term'
                ];
                // Get students
                $res_students = fetch_all('student', '*', [
                    'student_class'   => $student_class,
                    'student_section' => $student_section,
                    'status'          => 'ACTIVE'
                ]);
                $students_data = [];
                if ($res_students['status'] == 'success' && !empty($res_students['data'])) {
                    foreach ($res_students['data'] as $srow) {
                        $sid = $srow['id'];
                        $student_admission = $srow['student_admission'];
                        // Get tabulation marks
                        $tabulation = fetch_all('tabulation', '*', [
                            'student_id'        => $sid,
                            'student_admission' => $student_admission,
                            'term'              => $term,
                            'subject'           => $subject_id,
                            'branch_id'         => $branch_id
                        ])['data'][0] ?? [];
                        $marks = [];
                        foreach ($columns as $col) {
                            $marks[$col] = $tabulation[$col] ?? 0;
                        }
                        $students_data[] = [
                            'student_id'        => $sid,
                            'student_admission' => $student_admission,
                            'student_roll'      => $srow['student_roll'],
                            'student_name'      => $srow['student_name'],
                            'marks'             => $marks
                        ];
                    }
                }
                echo json_encode([
                    "status"        => "success",
                    "count"         => count($students_data),
                    "term"          => $term,
                    "columns"       => $columns,
                    "column_labels" => $column_labels,
                    "max_marks"     => $max_marks,
                    "data"          => $students_data
                ], JSON_PRETTY_PRINT);
                break;

            case "save_marks":
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
                $student_id        = isset($data['student_id']) ? trim($data['student_id']) : '';
                $student_admission = isset($data['student_admission']) ? trim($data['student_admission']) : '';
                $term_val          = isset($data['term']) ? trim($data['term']) : '';
                $subject           = isset($data['subject']) ? trim($data['subject']) : '';
                $col_name          = isset($data['col_name']) ? trim($data['col_name']) : '';
                $value             = isset($data['value']) ? floatval($data['value']) : 0;
                $branch_id         = isset($data['branch_id']) ? trim($data['branch_id']) : '';
            
                // Required validation
                if ($student_id == '' || $student_admission == '' || $term_val == '' || $subject == '' || $col_name == '') {
                    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
                    exit;
                }
            
                // Allowed column names (SQL Injection Protection)
                $allowed_cols = ['ut1','ut2','ut3','ut4','ma','portfolio','se','ga','nb','sea','mid_term'];
            
                if (!in_array($col_name, $allowed_cols)) {
                    echo json_encode(["status" => "error", "message" => "Invalid column name"]);
                    exit;
                }
            
                // Check existing record
                $existing = fetch_all('tabulation', '*', [
                    'student_id'        => $student_id,
                    'student_admission' => $student_admission,
                    'term'              => $term_val,
                    'subject'           => $subject,
                    'branch_id'         => $branch_id
                ]);
            
                if (!empty($existing['data'])) {
            
                    // UPDATE
                    $tab_id = $existing['data'][0]['id'];
            
                    $update_result = update_data_bid(
                        'tabulation',
                        [$col_name => $value],
                        $tab_id,
                        'id',
                        $branch_id
                    );
            
                    if ($update_result) {
                        echo json_encode(["status" => "success", "message" => "Marks updated successfully"]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Failed to update marks"]);
                    }
            
                } else {
            
                    // INSERT
                    $insert_data = [
                        'student_id'        => $student_id,
                        'student_admission' => $student_admission,
                        'term'              => $term_val,
                        'subject'           => $subject,
                        'branch_id'         => $branch_id,
                        $col_name           => $value
                    ];
            
                    $insert_result = insert_data_bid('tabulation', $insert_data);
            
                    if ($insert_result) {
                        echo json_encode(["status" => "success", "message" => "Marks saved successfully"]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Failed to save marks"]);
                    }
                }
            
            break;
            
        case "get_teacher_homework" :
            extract($data);
            $res = fetch_all('homework','*',['created_by'=>$created_by,'branch_id'=>$branch_id]);
            echo json_encode($res);
            break;
            
        
        case "teacher_leave_apply" :
		    extract($data);
		    $data['status']='PENDING';
	        $res = insert_data_bid("leave_details", $data,$branch_id);
	        unset($res['sql']);
		    echo json_encode($res);
            break;
		
		case "get_teacher_leaves" :
            extract($data);
                $sql = "SELECT ld.id,ld.status,ld.emp_id,ld.from_date,ld.to_date,ld.leave_cause,ld.leave_app,e.name 
                FROM leave_details ld
                JOIN employee e ON ld.emp_id = e.id
                WHERE ld.emp_id='$emp_id' AND ld.status NOT IN ('AUTO','DELETED') AND ld.branch_id='$branch_id'";
                $res = direct_sql($sql);
			unset($res['sql']);
            echo json_encode($res);
            break;
                
        case "emp_leave_update" :
            extract($data);
            $res = update_data('leave_details',$data,$id);
            echo json_encode($res);
            break;
            
         
        case "get_teacher_attendance":
            extract($data);
            $res = fetch_all('employee_att', '*', [
                'emp_id' => $emp_id,
                'att_month' => $att_month,
                'branch_id' => $branch_id
            ]);
            
            if ($res['status'] == 'success' && !empty($res['data'])) {
                 $res['data'] = $res['data'][0];
            } else {
                 $res['data'] = null;
            }
            
            echo json_encode($res);
            break;

        case "mark_self_attendance":
            extract($data);
            $day_col = "d_" . intval($day);
            $existing = fetch_all('employee_att', 'id', [
                'emp_id' => $emp_id,
                'att_month' => $att_month,
                'branch_id' => $branch_id
            ]);
            
            if ($existing['status'] == 'success' && !empty($existing['data'])) {
                $att_id = $existing['data'][0]['id'];
                $update_data = [$day_col => $status];
                $res = update_data('employee_att', $update_data, $att_id);
            } else {
                $insert_data = [
                    'emp_id' => $emp_id,
                    'att_month' => $att_month,
                    $day_col => $status,
                    'branch_id' => $branch_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 'ACTIVE'
                ];
                $res = insert_data_bid('employee_att', $insert_data, $branch_id);
            }
            
            echo json_encode($res);
            break;
            
        case "get_all_teacher_attendance":
            extract($data);
            if (!$branch_id || !$att_month) {
                echo json_encode(["status" => "error", "message" => "Missing branch_id or att_month"]);
                break;
            }
            $sql = "SELECT e.id as emp_id, e.name, ea.id as att_id, ea.att_month, 
                    ea.d_1, ea.d_2, ea.d_3, ea.d_4, ea.d_5, ea.d_6, ea.d_7, ea.d_8, ea.d_9, ea.d_10,
                    ea.d_11, ea.d_12, ea.d_13, ea.d_14, ea.d_15, ea.d_16, ea.d_17, ea.d_18, ea.d_19, ea.d_20,
                    ea.d_21, ea.d_22, ea.d_23, ea.d_24, ea.d_25, ea.d_26, ea.d_27, ea.d_28, ea.d_29, ea.d_30, ea.d_31
                    FROM employee e 
                    LEFT JOIN employee_att ea ON e.id = ea.emp_id AND ea.att_month = '$att_month'
                    WHERE e.branch_id = '$branch_id' AND e.status = 'Active' 
                    ORDER BY e.name ASC";
            
            $res = direct_sql($sql);
            if ($res['status'] == 'success' && !empty($res['data'])) {
                foreach ($res['data'] as &$row) {
                    $present = 0;
                    $absent = 0;
                    $leave = 0;
                    
                    for ($i = 1; $i <= 31; $i++) {
                        $d = $row["d_$i"] ?? null;
                        if ($d == 'P') $present++;
                        elseif ($d == 'A') $absent++;
                        elseif ($d == 'L') $leave++;
                    }
                    
                    $row['summary'] = [
                        'present' => $present,
                        'absent' => $absent,
                        'leave' => $leave,
                        'total' => $present + $absent + $leave // Days marked
                    ];
                }
            }
            
            echo json_encode($res);
            break;

        case "get_all_teacher_leaves":
            extract($data);
            if (!$branch_id) {
                echo json_encode(["status" => "error", "message" => "Missing branch_id"]);
                break;
            }
            $sql = "SELECT ld.*, e.name, e.designation, (DATEDIFF(ld.to_date, ld.from_date) + 1) as total_days 
                    FROM leave_details ld 
                    JOIN employee e ON ld.emp_id = e.id 
                    WHERE ld.branch_id = '$branch_id' 
                    ORDER BY ld.id DESC";
            
            $res = direct_sql($sql);
            echo json_encode($res);
            break;

        case "update_teacher_leave_status":
            extract($data);
            if (!$id || !$status) {
                echo json_encode(["status" => "error", "message" => "Missing id or status"]);
                break;
            }
            
            $update_data = ['status' => $status];
            $res = update_data_bid('leave_details', $update_data, $id, 'id', $branch_id);
            if ($res['status'] == 'success' && $status == 'ACTIVE') {
                $sql_leave = "SELECT * FROM leave_details WHERE id = '$id'";
                $leave_res = direct_sql($sql_leave);
                
                if ($leave_res['status'] == 'success' && !empty($leave_res['data'])) {
                    $leave_row = $leave_res['data'][0];
                    $emp_id = $leave_row['emp_id'];
                    $from_date = $leave_row['from_date'];
                    $to_date = $leave_row['to_date'];
                    
                    $start = new DateTime($from_date);
                    $end = new DateTime($to_date);
                    $end->modify('+1 day'); // Include end date
                    
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
                    
                    foreach ($period as $dt) {
                        $att_month = strtolower($dt->format('M_Y')); // e.g. feb_2026
                        $day_num = intval($dt->format('d'));
                        $day_col = "d_" . $day_num;
                        $check_sql = "SELECT id FROM employee_att WHERE emp_id='$emp_id' AND att_month='$att_month'";
                        $check_res = direct_sql($check_sql);
                        
                        if ($check_res['status'] == 'success' && !empty($check_res['data'])) {
                            $att_id = $check_res['data'][0]['id'];
                            $up_att = [$day_col => 'L'];
                            update_data('employee_att', $up_att, $att_id);
                        } else {
                            $ins_att = [
                                'emp_id' => $emp_id,
                                'att_month' => $att_month,
                                'branch_id' => $branch_id,
                                $day_col => 'L'
                            ];
                            insert_data('employee_att', $ins_att);
                        }
                    }
                }
            }
            
            echo json_encode($res);
            break;
            
            
            
			default:
			    echo" No Task Selected or Invalid Task";
		}
	}


?>