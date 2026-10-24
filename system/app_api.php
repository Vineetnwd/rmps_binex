<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once('function.php');
$token =$api_key =$_REQUEST['token'];
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if($token ==$api_key) 
{
	if(isset($_GET['task']))
	{
    $task =$_REQUEST['task'];
    //$user_id = $_REQUEST['req_by'];
	switch($task)
		{
		    case "app_version" :
		        $res = array("app_version"=>"1.0.0", "app_name"=>"com.offerplant.rmpschool");
		        echo json_encode($res);
		       
		        break;
		    
			case "insert_row":
				// Recive table_name 
				extract($_REQUEST);
				$res = insert_row($table_name);
				echo json_encode($res);
				break;
			
			case "insert_data":
				// Recive table_name , post_data
				extract($_REQUEST);
				$ArrayData = json_decode($post_data,true);
				$res = insert_data( $table_name, $ArrayData);
				echo json_encode($res);
				break;			
			
			case "update_data":
				// Recive table_name , post_data
				extract($_REQUEST);
				$ArrayData = json_decode($post_data,true);
				$res = update_data( $table_name, $ArrayData, $id, $pkey='id' );
				echo json_encode($res);
				break;	
				
			case "delete_data":
				// Recive table_name , id, pkey
				extract($_REQUEST);
				$res = delete_data( $table_name, $id ,$pkey='id');
				echo json_encode($res);
				break;			
			
			case "delete_multi_data":
				// Recive table_name , post_data
				extract($_REQUEST);
				$ArrayData = json_decode($post_data,true);
				$res = delete_multi_data( $table_name, $whereArr);
				echo json_encode($res);
				break;			
			
			case "get_all":
				// Recive table_name , , post_data , orderby
				extract($_REQUEST);
				$res = get_all( $table_name, $column_list, $whereArr , $orderby );
				echo json_encode($res);
				break;	
			
		
			case "get_data":
				// Recive table_name , id, field_name , pkey
				extract($_REQUEST);
				$res = get_data($table_name, $id , $field_name, $pkey);
				echo json_encode($res);
				break;	
			
			case "get_multi_data": 
				// Recive table_name , post_data, orderby
				extract($_REQUEST);
				//print_r($_REQUEST);
				$res = get_multi_data( $table_name, $whereArr , $orderby );
				echo json_encode($res);
				break;	
				
			case "dropdown":
				// Recive list_name of Array 
				extract($_REQUEST);
				echo json_encode($$list_name);
				break;	
			// Actual Bine APP API 
			
				
			case "get_student_by_mobile":
				$mobile =$data['mobile'];
				$sql ="select id, student_name, student_photo, student_admission,student_class, student_roll, student_section, student_mobile from student where student_mobile='$mobile' and status ='ACTIVE'";
				$res =direct_sql($sql);
				if($res['status'] =='success' and $res['count']>0)
				{
    				$res['otp'] = $otp = rand(100000,999999);
        	        $res['msg'] = $msg = "Thanks for interest in  $inst_name  Your App Login OTP is $otp ";
        	        //sendsms($student_mobile, $msg);
				}
				echo json_encode($res);
				break;
				
		
		    
    		
    		case "get_result":
    		    extract($_REQUEST);
    		    $set_list = get_all('answer','*',array('student_id'=>$student_id,'status'=>'PUBLISH'));
    		    if($set_list['count']>0)
    		    {
        		    foreach($set_list['data'] as $set)
        		    {
        		        
        		       $res[] = set_result($student_id, $set['set_id']); 
        		    }
        		     echo json_encode($res);
    		    }
    		    else{
    		         echo json_encode($set_list);
    		    }
    		    break;
    		 
    	
			case "get_receipt" :
			    $student_id = $data['student_id'];
			    $sql ="SELECT id as ID , paid_date as Date, total, paid_amount as paid , current_dues as dues FROM `receipt` WHERE status ='PAID' and student_id ='$student_id'";
			    $res =direct_sql($sql);
			    unset($res['sql']);
			    echo json_encode($res);
			    break;
    	    
		
			case "get_hw" :
			    $student_id = $data['student_id'];
			    $hw_date =$data['hw_date']??$today;
    		    $student = get_data('student', $student_id)['data'];
    		    $hw_class = $student['student_class'];
    		    $hw_section = $student['student_section'];
    		   	$res = get_all('home_work','*',array('class'=>$hw_class, 'section'=> $hw_section, 'hw_date'=>$hw_date));
    		   	unset($res['sql']);
    		   	echo json_encode($res);
				break;
				
			case "get_notice":
    		       $sql="SELECT id,  date_format(notice_date, '%d %b %Y') as notice_date, notice_title, notice_details, notice_attachment, status  FROM notice where status not in ('AUTO','DELETED') order by id desc";
    		    $res =direct_sql($sql);
    		    unset($res['sql']);
    		    echo json_encode($res);
    			break;
    	
				
    		case "get_dues":
				$student_id = $data['student_id'];
				$res['prev_dues'] = $total = get_data('student_fee', $student_id, 'current_dues','student_id')['data'];
                $res['month_list'] = $marr = duesmonthcount($student_id)['list'];
                foreach((array)$marr as $mnth)
                {
                $fee = monthly_fee($student_id, remove_space($mnth));
                $res[$mnth] = $fee['fee'];
                $total= $total + $fee['fee']['total'];
                }
                $res['total'] = $total;
				echo json_encode($res);
				break;	
			
			
			
			case "get_profile":
			    $student_id = $data['student_id'];
			    $sql ="SELECT id, student_admission, student_name, student_mother, student_father, date_of_birth, student_sex, student_mobile, student_email, student_photo, student_class, student_section, student_roll, student_type,student_address1, student_address2, pin_code, house_name FROM student WHERE id ='$student_id'";
			    $res =direct_sql($sql);
    		    unset($res['sql']);
    		    echo json_encode($res);
				break;	
			
			case "student_att":
			    $student_id = $data['student_id'];
			    if(!empty($month))
			    {
			        $res =student_att_list($student_id, $month);
			    }else{
			        $res =student_att_list($student_id);
			    }
			 	echo json_encode($res);
				break;		
			
			case "get_admit_card" :
			    $student_id = $data['student_id'];
			    $student = get_data('student', $student_id)['data'];
    		    $student_class = $student['student_class'];
    		    $branch_id = $student['branch_id'];
    		    $exam_list =  direct_sql_api("select * from exam_list where branch_id ='$branch_id' and status not in ('AUTO','DELETED')");
    		    foreach($exam_list['data'] as $exam)
    		    {
    		         $sql ="SELECT exam_date, subject.subject_name, sitting_name, start_time, end_time FROM `admit_card` left join subject on admit_card.subject_id = subject.id where exam_id ='{$exam['id']}' and admit_card.student_class ='$student_class' and admit_card.branch_id ='$branch_id' order by exam_date, start_time";
    		        $res[$exam['exam_name']]['plan'] = direct_sql_api($sql)['data'];
    		       
    		    }
    		   	echo json_encode($res);
				break;
			
			
			default:
			    echo" No Task Selected or Invalid Task";
		}
	}
}
else{
	die("Not Authorised");
}


?>