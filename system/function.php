<?php
require_once('op_lib.php');
if(isset($_SESSION['db_name']))
{
	$db_name =$_SESSION['db_name'];
}

function branch_config()
{
    global $con;
    $sql="select * from branch_list where id ='{$_SESSION['branch_id']}'";
    $res = direct_sql($sql);
    $data =$res['data'][0];
    foreach ( $data as $key=>$value ) {
		 // Check if the input string is in JSON object format
        $isObject = is_object(json_decode($value));
        // Check if the input string is in JSON array format
        $isArray = is_array(json_decode($value));
        // Check if the input string is a comma-separated value
        $isCSV = strpos($value, ',') !== false;

		    if ($isObject || $isArray) {
                // If it's a JSON object or array, directly decode it
                $value = json_decode($value, true);
            } elseif ($isCSV) {
                // If it's a comma-separated value, explode and trim the values
                $csvArray = explode(',', $value);
                $value = array_map('trim', $csvArray);
            }
		$vardata[$key] = $value;
	}
	return $vardata;
}

function studentcount($class, $section=null)
    {
    	if($section ==null)
    	{
    		$res = get_all('student','*',array('status'=>'ACTIVE','student_class'=>$class));
    	}
    	else{
    		$res = get_all('student','*',array('status'=>'ACTIVE','student_class'=>$class,'student_section'=>$section));
    	}
    	return $res['count'];
    }
    
function new_student_count($class, $section=null)
    {
    	if($section ==null)
    	{
    		$res = get_all('student','*',array('status'=>'ACTIVE','admission_type'=>'NEW','student_class'=>$class));
    	}
    	else{
    		$res = get_all('student','*',array('status'=>'ACTIVE','admission_type'=>'NEW','student_class'=>$class,'student_section'=>$section));
    	}
    	return $res['count'];
    }
    

function last_id($table, $col_name)
  {
   
    $sql ="SELECT max(cast($col_name as int)) as sl_no FROM $table where status not in('DELETED','AUTO','REMOVED')";
    $res = direct_sql($sql);
    return $res['data'][0]['sl_no'];
  }
  
function get_fee($student_id, $fee_id)
		{
		global $con;
		$fee=0.00;
		$student = get_data('student',$student_id,null)['data'];
		$fee = get_data('fee_head',$fee_id)['data'];
		$col_name = remove_space($fee['fee_name']);

       
		
			if($col_name =='transport_fee')
				{
					if($student['student_type'] =='TRANSPORT') 
						{
							//$fee = get_data('transport_area',$student['area_id'],'area_fee')['data'];
							$fee = get_transport($student_id,'area_fee');
						} 
					else {$fee = 0;}
				}
		else if ($col_name =='previous_dues'){$fee=0;} 
		else if ($col_name =='hostel_fee') 
				{
					if($student['student_type'] =='HOSTELER') 
						{
							//$fee = get_data('fee_details',$student['student_class'],$col_name,'student_class')['data'];
                            $fee  =  get_all('fee_details','*',['branch_id' =>$_SESSION['branch_id'],'student_class'=>$student['student_class']])['data'][0][$col_name];

						} 
					else {$fee = 0;}
					
				}
		else if($fee['fee_type'] =='STUDENT')
				{
					$fee = get_data('student_fee',$student_id,$col_name,'student_id')['data'];
				}
		else if ($col_name =='tuition_fee')
			{
			    $fee  =  get_all('fee_details','*',['branch_id' =>$_SESSION['branch_id'],'student_class'=>$student['student_class']])['data'][0][$col_name];

                $fee = $fee -($fee*$student['scholarship']/100);
			}
			else{
					//$fee = get_data('fee_details',$student['student_class'],$col_name,'student_class')['data'];
                    $fee  =  get_all('fee_details','*',['branch_id' =>$_SESSION['branch_id'],'student_class'=>$student['student_class']])['data'][0][$col_name];
				}
		return $fee;
		}
		

function get_fee_by_name($student_id, $col_name)
		{
		global $con;
		$fee=0.00;
		$student = get_data('student',$student_id,null)['data'];
	//	$fee = get_data('fee_head',$fee_id)['data'];
	//	$col_name = remove_space($fee['fee_name']);
		
			if($col_name =='transport_fee')
				{
					if($student['student_type'] =='TRANSPORT') 
						{
							$fee = get_data('transport_area',$student['area_id'],'area_fee')['data'];
						} 
					else {$fee = 0;}
				}
		else if ($col_name =='previous_dues'){$fee=0;} 
		else if ($col_name =='hostel_fee') 
				{
					if($student['student_type'] =='HOSTELER') 
						{
							$fee = get_data('fee_details',$student['student_class'],$col_name,'student_class')['data'];
						} 
					else {$fee = 0;}
					
				}
		else if ($col_name =='tuition_fee')
			{
			    $fee  =  get_all('fee_details','*',['branch_id' =>$_SESSION['branch_id'],'student_class'=>$student['student_class']])['data'][0][$col_name];

                $fee = $fee -($fee*$student['scholarship']/100);
			}
		else if($fee['fee_type'] =='STUDENT')
				{
					$fee = get_data('student_fee',$student_id,$col_name,'student_id')['data'];
				}
			else{
					$fee = get_data('fee_details',$student['student_class'],$col_name,'student_class')['data'];
				}
		return $fee;
		}

function monthly_fee($student_id, $month_name)
	{
		global $con;
        $branch_id = $_SESSION['branch_id']??1;
		$all_fee =[];
		//$fee['total']=0;
		$student = get_data('student',$student_id,null)['data'];
		extract($student);
		$sql ="select * from fee_head where find_in_set ('$finance_type', finance_type) and branch_id='$branch_id' and  find_in_set( '$admission_type',admission_type) and find_in_set('$month_name', fee_month) and find_in_set('$student_class', student_class) and find_in_set('$student_type', student_type) and status='ACTIVE'";
		
		//$sql ="select * from fee_head where finance_type ='NORMAL' and find_in_set( '$admission_type',admission_type) and find_in_set('$month_name', fee_month) and find_in_set('$student_type', student_type) and status='ACTIVE'";
		$res = direct_sql($sql);
		
		if($res['count']>0)
		{
			$total =0;
			foreach($res['data'] as $row)
			{
				$fee['id'] = $row['id'];
				$fee['name'] =$row['fee_name'];
				$fee['col_name']=$col_name = remove_space($row['fee_name']);
				$fee['amount'] = $fee_amount= get_fee($student_id,$row['id']);
				$total =$total +$fee['amount'];
				$all_fee['fee'][$col_name] =$fee_amount;
			}
			$all_fee['fee']['total']=$total;
			$all_fee['student_id']=$student_id;
			$all_fee['admission'] =$student['student_admission'];
			$all_fee['month']=$month_name;
			
		}
        else{
            $all_fee['fee']['total']=0;
			$all_fee['student_id']=$student_id;
			$all_fee['admission'] =0;
			$all_fee['month']=$month_name;
        }
		return $all_fee;
	}

function nmonth_fee_all($sid, $marr)
{
	$all = array();
	foreach($marr as $month)
		{
		    if(remove_space($month) <> 'previous_dues')
		    {
    			if(get_data('student_fee',$sid, remove_space($month),'student_id')['data'] == NULL)
    			{   
    			    $all[$month] =monthly_fee($sid,$month)['fee'];
    			}
    		}
		}
	return array_add($all);
}

function create_check($name, $value =null, $class='fee-month' , $checked=null )
{
	$id = remove_space($name);
	$check = "<span class='checkbox'><input type='checkbox' name ='$id' id='$id' value='$value' class='$class' $checked ><label for='$id'>$name</label></span>";
	echo $check;
}

// function create_checks($checkboxes, $class = 'fee-month') {
//     foreach ($checkboxes as $checkbox) {
//         // Ensure proper fallbacks and values for attributes
//         $name = $checkbox['name'] ?? 'checkbox';
//         $value = $checkbox['value'] ?? null;
//         $c_name = $checkbox['c_name'] ?? $name; // Use c_name if available, otherwise fallback to name
//         $checked = !empty($checkbox['checked']) ? 'checked' : null;
        
//         // Sanitize c_name for use in id attribute (e.g., no spaces)
//         $id = remove_space($c_name); // Ensure remove_space function is defined to remove spaces
        
//         // Output the checkbox and label HTML
//         echo "<span class='checkbox'>
//                 <input type='checkbox' name='{$name}[]' id='{$id}' value='{$value}' class='{$class}' {$checked}>
//                 <label for='{$id}'>{$c_name}</label>
//               </span>";
//     }
// }

function create_checks($checkboxes, $chk, $class = 'fee-month') {
    foreach ($checkboxes as $checkbox) {
        // Ensure proper fallbacks and values for attributes
        $name = $checkbox['name'] ?? 'checkbox';
        $value = $checkbox['value'] ?? null;
        $c_name = $checkbox['c_name'] ?? $name; // Use c_name if available, otherwise fallback to name
        
        // Check if this checkbox should be checked based on $chk
        $checked = (strpos($chk, $value) !== false) ? 'checked' : null;

        // Sanitize c_name for use in id attribute (e.g., no spaces)
        $id = remove_space($c_name); // Ensure remove_space function is defined to remove spaces
        
        // Output the checkbox and label HTML
        echo "<span class='checkbox'>
                <input type='checkbox' name='{$name}[]' id='{$id}' value='{$value}' class='{$class}' {$checked}>
                <label for='{$id}'>{$c_name}</label>
              </span>";
    }
}



function emp_salary_check($id, $month, $checked = null) {
    $checkedAttribute = $checked ? 'checked' : '';
    $uniqueId = $id;
    
    $check = "
    <div class='checkbox'>
        <input 
            data-month='$month' 
            type='checkbox' 
            value='$id' 
            name='sel_id[]' 
            id='$uniqueId' 
            class='chk' 
            $checkedAttribute 
        >
        <label for='$uniqueId'></label>
    </div>";
    
    echo $check;
}

function att_check($name, $value =null, $class='fee-month' , $checked=null )
{
	$id = remove_space($name);
	$check = "<div class='checkbox'><input type='checkbox' name ='$id' id='$id' value='$value' class='$class' $checked ><label for='$id'></label></div>";
	echo $check;
}


function check_all($name, $class = 'chk', $selectAllClass = 'select-all', $labelText = 'Select All') 
{
    $id = remove_space($name);
    $checkbox = "<div class='checkbox'>
                    <input type='checkbox' id='$id' class='$selectAllClass btn btn-sm text-center' data-target-class='$class'>
                    <label for='$id'>$labelText</label>
                 </div>
                 <script>
                    // JavaScript to handle Select All functionality
                    document.addEventListener('DOMContentLoaded', function () {
                        const selectAll = document.querySelector('#$id');
                        if (selectAll) {
                            selectAll.addEventListener('change', function () {
                                const targetClass = selectAll.getAttribute('data-target-class');
                                const checkboxes = document.querySelectorAll('.' + targetClass);
                                checkboxes.forEach(function (checkbox) {
                                    checkbox.checked = selectAll.checked;
                                });
                            });
                        }
                    });
                 </script>";
    echo $checkbox;
}


function array_add(array $all): array
{
    $total = [];

    // Iterate over each associative array
    foreach ($all as $row) {
        foreach ($row as $key => $value) {
            // Ensure the key exists in the total and sum up the values
            $total[$key] = ($total[$key] ?? 0) + (is_numeric($value) ? $value : 0);
        }
    }

    // Ensure all possible keys across the arrays are present in the result
    foreach ($all as $row) {
        foreach (array_keys($row) as $key) {
            $total[$key] = $total[$key] ?? 0;
        }
    }

    return $total;
}

	

function nmonth_fee($sid, $marr)
	{
		//$marr = explode(',',$mlist);
		$all = array();
		foreach($marr as $month)
		{
		    if(remove_space($month) <> 'previous_dues')
		    {
    			if(get_data('student_fee',$sid, remove_space($month),'student_id')['data']==null)
    			{
    			$all[$month] =monthly_fee($sid,$month)['fee'];
    			}
    		}
		}
		return array_add($all);
	}

function get_marks($student_admission, $exam_id, $subject)
{
	global $con;
    global $marks_type_list;
	$sql ="select * from exam where student_admission  ='$student_admission' and exam_id = '$exam_id'  ";
	$res = direct_sql($sql);
		if ($res['count'] ==1)
		{
		$row =$res['data'][0];
        $total =0;
        foreach ($marks_type_list as $mstype)
        {
            $marks =$row[$subject.'_'.remove_space($mstype)];
            $total += $marks;
		    $data[remove_space($mstype)] = $marks;
        }		
		$data['total'] = $total;
		}
		else{
            foreach ($marks_type_list as $mstype)
            {
                $data[remove_space($mstype)] = 0;
            }
		$data['total']   = 0;
		}
	return $data;
}



function marks_import($table, $pkey = 'id', $remove = null) {
    // Allowed MIME types
    $csvMimes = [
        'text/csv', 'application/csv', 'text/plain',
        'application/vnd.ms-excel', 'application/octet-stream'
    ];
    
    $change = $new = 0;
    $res = [];

    // Validate uploaded file
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($fileExtension !== 'csv') {
            return ['status' => 'error', 'msg' => 'Invalid file type. Only CSV files are allowed.'];
        }

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $tmpName = $_FILES['file']['tmp_name'];
            $csvFile = fopen($tmpName, 'r');
            
            if ($csvFile) {
                // Read column headers and validate
                $col_list = array_map('remove_space', fgetcsv($csvFile));
                // Handle composite keys
                $pkeys = is_array($pkey) ? $pkey : explode(',', $pkey);
                foreach ($pkeys as $key) {
                    if (!in_array($key, $col_list)) {
                        return ['status' => 'error', 'msg' => "Missing required key column '$key' in CSV."];
                    }
                }

                // Process rows
                while (($line = fgetcsv($csvFile)) !== false) {
                    $all_data = array_combine($col_list, $line);

                    // Remove unwanted columns
                    if ($remove) {
                        foreach (explode(',', $remove) as $rcol) {
                            unset($all_data[$rcol]);
                        }
                    }

                    // Create composite key condition
                    $search = [];
                    foreach ($pkeys as $key) {
                        $search[$key] = trim($all_data[$key]);
                    }

                    // Check for duplicate entries
                    $search_result = get_all($table, '*', $search);

                    if ($search_result['count'] == 0) {
                        // Insert new record
                        $insert_res = insert_data($table, $all_data);
                        if (!empty($insert_res['id'])) {
                            $new++;
                        }
                    } else {
                        // Update existing record
                        $update_res = update_multi_data($table, $all_data, $search);
                        if ($update_res['status'] === 'success') {
                            $change++;
                        }
                    }
                }

                fclose($csvFile);

                // Return success response
                $res = [
                    'status' => 'success',
                    'change' => $change,
                    'new' => $new,
                    'msg' => 'Data has been imported successfully.'
                ];
            } else {
                $res = ['status' => 'error', 'msg' => 'Unable to open the CSV file.'];
            }
        } else {
            $res = ['status' => 'error', 'msg' => 'File upload failed.'];
        }
    } else {
        $res = ['status' => 'error', 'msg' => 'Please upload a valid CSV file.'];
    }

    return $res;
}

	
function pre_grade($per)
{
	if($per<=100 && $per >=90)
	{
		$grade ='A+';
	}
	elseif($per<89.99 && $per >=80)
	{
		$grade ='A';
	}
	elseif($per<79.99 && $per >=70)
	{
		$grade ='B+';
	}
	elseif($per<69.99 && $per >=60)
	{
		$grade ='B';
	}
	elseif($per<59.99 && $per >=50)
	{
		$grade ='C';
	}
	elseif($per >=40)
	{
		$grade ='D';
	}
	else{
		$grade ='E';
	}
	 //A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32)
	return $grade;
}


function pre_grade_marks($total_marks, $subject_count)
{
    $max_total = $subject_count * 100; // maximum possible marks
    $percent = ($total_marks / $max_total) * 100; // calculate percentage
    
    if ($percent >= 90 && $percent <= 100) {
        $grade = 'A+';
    } elseif ($percent >= 80) {
        $grade = 'A';
    } elseif ($percent >= 70) {
        $grade = 'B+';
    } elseif ($percent >= 60) {
        $grade = 'B';
    } elseif ($percent >= 50) {
        $grade = 'C';
    } elseif ($percent >= 40) {
        $grade = 'D';
    } else {
        $grade = 'E';
    } 
    return $grade;
}
	
function grade($per)
{
	if($per >=90)
	{
		$grade ='A1';
	}
	elseif($per >=80)
	{
		$grade ='A';
	}
	elseif($per >=70)
	{
		$grade ='B1';
	}
	elseif($per >=60)
	{
		$grade ='B';
	}
	elseif($per >=50)
	{
		$grade ='C';
	}
	elseif($per >=40)
	{
		$grade ='D';
	}
	else{
		$grade ='E';
	}
	 //A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32)
	return $grade;
}

function grade_on_marks($total_marks, $subject_count)
{
    $max_total = $subject_count * 100; // maximum possible marks
    $percent = ($total_marks / $max_total) * 100; // calculate percentage
    
    if ($percent >= 91 && $percent <= 100) {
        $grade = 'A1';
    } elseif ($percent >= 81) {
        $grade = 'A2';
    } elseif ($percent >= 71) {
        $grade = 'B1';
    } elseif ($percent >= 61) {
        $grade = 'B2';
    } elseif ($percent >= 51) {
        $grade = 'C1';
    } elseif ($percent >= 41) {
        $grade = 'C2';
    } elseif ($percent >= 33) {
        $grade = 'D';
    } else {
        $grade = 'E';
    }
    return $grade;
}

function duesmonthcount($student_id)
	{
	    global $db_name;
	    global $session_list;
	    global $current_session;
	    global $fee_month; // Month Allowed to Collect Fee
		$ct =0;
		$list =[];
	//	if($db_name=='u443617320_rmps_26'){
	        $cmonth =date('F');  
	//	}else{
		//    $cmonth ='March';   
		//}
		$prev_dues = get_data('student_fee', $student_id, 'current_dues','student_id')['data'];
		
		if($prev_dues <>0)
		{
		$list[] ='Previous Dues';
		}
		
		$student =get_data('student',$student_id)['data'];
		
		unset($fee_month[0]); // Remove Admission Month from List
		foreach($fee_month as $month)
		{
			$x = get_data('student_fee',$student_id,remove_space($month),'student_id')['data'];
			if($x=='' || $x ==null)
			{
			$ct++;
			$list[] = $month;
			//$list =$list.", ".$month;
			}
			if($month==$cmonth)
			{
			    break;
			}
		}
		$res['count'] =$ct;
		$res['list']=$list;
		return $res;
	}
	
	
function finaldues($student_id)
    {
        $total = get_data('student_fee', $student_id, 'current_dues','student_id')['data'];
        $marr = duesmonthcount($student_id)['list'];
        foreach((array)$marr as $mnth)
        {
        $res = monthly_fee($student_id, remove_space($mnth));
        $total= $total +$res['fee']['total'];
        }
        return $total;
    }

//echo finaldues(20);

function all_income($from_date ='', $to_date ='' , $mode ='')
 {
     global $today;
     global $user_type;
     global $user_id;
     
     $branch_id = $_SESSION['branch_id'];
     if($from_date =='')
     {
        $from_date =$today;
     }
      if($to_date =='')
     {
        $to_date =$today;
     }
      if($mode <> '')
     {
        $mode = " and payment_mode ='$mode'";
     }
    $res = get_all('fee_head','*',array('status'=>'ACTIVE','branch_id'=>$branch_id))['data'];
    $gt=0;
    //print_r($res);
    $res[]['fee_name']='paid_amount';
    foreach($res as $row)
    {
        $fee_name = remove_space($row['fee_name']);
        if($user_type=='ACCOUNT')
        {
        $sql = "select sum($fee_name) as total from receipt  where status ='PAID' and branch_id = '$branch_id' and created_by ='$user_id' and paid_date between '$from_date' and '$to_date' " .$mode ;
        }else{
          $sql = "select sum($fee_name) as total from receipt  where status ='PAID' and branch_id = '$branch_id' and paid_date between '$from_date' and '$to_date' " .$mode ;    
        }
        $gt = $gt+direct_sql($sql)['data'][0]['total'];
        $total[$fee_name] = direct_sql($sql)['data'][0]['total'];
        
    }
        $a2['total'] = $total['paid_amount']; // Last Position
        $diff = $gt- $total['paid_amount'];
        $a1['previous_dues'] =$total['paid_amount'] - $diff; //Calculate Unmateched amount
        unset($total['paid_amount']); // To Remove from All Fee List
        $total = $a1 + $total +$a2;
    return $total;
 }
 
 
   function all_exp($from_date ='', $to_date ='' , $mode ='')
 {
     global $today;
     global $user_type;
     global $user_id;
     $branch_id = (isset($_SESSION['branch_id']) and $_SESSION['branch_id'] !='')?$_SESSION['branch_id']:'0';
     if($from_date =='')
     {
        $from_date =$today;
     }
      if($to_date =='')
     {
        $to_date =$today;
     }
      if($mode <> '')
     {
        $mode = " and txn_mode ='$mode'";
     }
        //$fee_name = remove_space($row['fee_name']);
        //$sql = "SELECT account_id, sum(txn_amount) as total FROM `account_txn` where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode group by account_id ";
      if($user_type=='ACCOUNT')
      {
        $sql= "select account_type, sum(txn_amount) as total from exp_details where status = 'ACTIVE' and branch_id = $branch_id and txn_date BETWEEN '$from_date' and '$to_date' and created_by ='$user_id'  $mode GROUP by account_type"; 
      }else{
         $sql= "select account_type, sum(txn_amount) as total from exp_details where status = 'ACTIVE' and branch_id = $branch_id and txn_date BETWEEN '$from_date' and '$to_date' $mode GROUP by account_type"; 
      }
      
        $res = direct_sql($sql);
        $gt =0;
        $total=[];
        if($res['count']>0)
        {
            foreach((array)$res['data'] as $row)
            {
                $acc_type = $row['account_type'];
                $gt = $gt+$row['total'];
                $total[$acc_type] = $row['total'];
            }
            $total['total'] = $gt;
        }
        return $total;
 }
 
 function admin_income($from_date ='', $to_date ='' , $mode ='')
 {
     global $today;
     if($from_date =='')
     {
        $from_date =$today;
     }
      if($to_date =='')
     {
        $to_date =$today;
     }
      if($mode <> '')
     {
        $mode = " and payment_mode ='$mode'";
     }
    $res = get_all('fee_head','*',array('status'=>'ACTIVE'))['data'];
    $gt=0;
    //print_r($res);
    $res[]['fee_name']='paid_amount';
    foreach($res as $row)
    {
        $fee_name = remove_space($row['fee_name']);
        
        $sql = "select sum($fee_name) as total from receipt  where status ='PAID' and paid_date between '$from_date' and '$to_date' " .$mode ;
        $gt = $gt+direct_sql($sql)['data'][0]['total'];
        $total[$fee_name] = direct_sql($sql)['data'][0]['total'];
        
    }
        $a2['total'] = $total['paid_amount']; // Last Position
        $diff = $gt- $total['paid_amount'];
        $a1['previous_dues'] =$total['paid_amount'] - $diff; //Calculate Unmateched amount
        unset($total['paid_amount']); // To Remove from All Fee List
        $total = $a1 + $total +$a2;
    return $total;
 }
 
 
   function admin_exp($from_date ='', $to_date ='' , $mode ='')
 {
     global $today;
     if($from_date =='')
     {
        $from_date =$today;
     }
      if($to_date =='')
     {
        $to_date =$today;
     }
      if($mode <> '')
     {
        $mode = " and txn_mode ='$mode'";
     }
        //$fee_name = remove_space($row['fee_name']);
        //$sql = "SELECT account_id, sum(txn_amount) as total FROM `account_txn` where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode group by account_id ";
        
        $sql= "select account_type, sum(txn_amount) as total from exp_details where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode GROUP by account_type"; 
        
        $res = direct_sql($sql);
        $gt =0;
        foreach($res['data'] as $row)
        {
            $acc_type = $row['account_type'];
            $gt = $gt+$row['total'];
            $total[$acc_type] = $row['total'];
        }
        $total['total'] = $gt;
        return $total;
 }
 
 function last_roll( $student_class, $student_section)
 {
    $roll_no =1;
     $sql ="select max(student_roll) as student_roll from  student where status ='ACTIVE' and  student_class ='$student_class' and student_section ='$student_section' ";
     $res = direct_sql($sql);
     if($res['count'] ==1)
     {
        $roll_no =  $res['data'][0]['student_roll'];
         
     }
     return $roll_no +1; 
 }
 
 function monthly_collection_graph()
    {
        global $db_name;
        $cmonth = date('m');
        $sql = "SELECT student_class, count(id) as ct FROM $db_name.payment_book where month(paid_date) ='$cmonth' group by student_class ";
        $result = direct_sql($sql);
        $rows = array();
        $table[] = array("Class","Paid","Total");
       
        foreach($result['data'] as $row)
        {
            $data =array();
            $data[] =$row['student_class'];
            $data[] =intval($row['ct']);
            $data[] =get_all('student', '*', array('student_class'=>$row['student_class']))['count'];
           
            $table[] =$data;
        }
        $jsonTable = json_encode($table);
        return $jsonTable;
    }
    
function new_admission_graph()
    {
        $cmonth = date('m');
        $cyear = date('Y');
       // $sql = "SELECT student_class, count(id) as ct FROM student where month(created_at) ='$cmonth' and year(created_at) ='$cyear' and student_class <>'' group by month(created_at), student_class";
        $sql = "SELECT student_class, count(id) as ct FROM student where admission_type ='NEW' and year(created_at) ='$cyear' and student_class <>'' group by month(created_at), student_class";
        $result = direct_sql($sql);
        $rows = array();
        $table[] = array("Class","Admission");
       
        foreach($result['data'] as $row)
        {
            $data =array();
            $data[] =$row['student_class'];
            $data[] =intval($row['ct']);
            $table[] =$data;
        }
        $jsonTable = json_encode($table);
        return $jsonTable;
    }
    
function income_exp_graph()
    {
        $td = date('d');
        
        $rows = array();
        $table[] = array("Date","Income","Expence","Balance");
       
        for($i=1; $i<=$td; $i++)
        {
            $dt = date('Y-m-'.$i);
            $data =array();
            $data[] =date($i.' M');
            $data[] =intval(all_income($dt, $dt)['total']);
            $data[] =intval(all_exp($dt, $dt)['total']);
            $data[] =intval(all_income($dt, $dt)['total'])-intval(all_exp($dt, $dt)['total']);
            $table[] =$data;
        }
        $jsonTable = json_encode($table);
        return $jsonTable;
    }

function short_url($long_url, $keyword ='')
    {
        if($keyword !='')
        {
            $keyword = "&keyword=".remove_space($keyword);
        }
        $timestamp = time();
        $signature = hash('sha512', $timestamp . 'lskfhz7yajzbtwpsh6j9cdnuhaadklwk' );
        
        //$api_url ="https://sl.morg.in/yourls-api.php?timestamp=$timestamp&signature=$signature&hash=sha512&action=shorturl&format=json&url=". $long_url.$keyword;
        $api_url ="https://sl.morg.in/yourls-api.php?username=offerplant&password=Plant!2017&action=shorturl&format=json&url=". $long_url.$keyword;
        
        $res = api_call($api_url);
        
        $data = json_decode($res,true);
        return $data['shorturl'];
        //return $data;
    }
    
function short($in, $size=30 )
    {
        return $out = strlen($in) > $size ? substr($in,0,$size)."..." : $in;
    }
    
function mask($text, $cr=2)
{
    return substr($text, 0, $cr) . '****' . substr($text, -$cr);
}


function update_payment($student_id, $amount) // for online payment 
    {
        global $inst_name;
         $student = get_data('student', $student_id)['data'];
         $rdata['student_admission'] =$student['student_admission'];
         $rdata['student_id'] =$student['id'];
         $rdata['previous_dues'] = get_data('student_fee', $student_id,'current_dues','student_id')['data'];
         $dues_month = duesmonthcount($student_id);
         $paid_month = duesmonthcount($student_id)['list'];
         $all_fee = nmonth_fee($student_id,$dues_month['list']);
         foreach($all_fee as $key=>$value)
    		{
    		    $rdata[$key] =intval($value);	
    		}
    	 $total_dues = finaldues($student_id);
    	 $current_dues = $total_dues -$amount;
         $rdata['paid_month'] =$paid_month_list = implode(',',$dues_month['list']);
         $rdata['paid_amount'] = $amount;
         $rdata['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
         $rdata['paid_date'] =date('Y-m-d');
         $rdata['payment_mode'] ='ONLINE';
         $rdata['status'] ='PAID';
         
    	$res = insert_data('receipt', $rdata);
    	
    	print_r($rdata);
    	if($res['status']=='success')
    				{
    					$rid = $res['id'];
    					foreach($paid_month as $month)
    					{
    						if(remove_space($month)<>'previous_dues'){
        						$old_value = get_data('student_fee',$student_id,$month)['data'];
        						if($old_value!=null)
        						{
        							$rid =$old_value.",".$rid;	
        						}
        						$res2 = update_data('student_fee',array($month=>$rid,'current_dues'=>$current_dues),$student_id,'student_id');
        						print_r($res2);
    						}
    					}
    				}
            update_data('student_fee',array('current_dues' =>$current_dues),$student_id,'student_id');
    	
    	$sms = "Dear {$student['student_name']} \nRs. {$amount} with RID #$rid received successfully against $paid_month_list Your current due is $current_dues. \nRegards $inst_name Via Bine";
        $te_id = '1507163688594295451';
        send_msg($student['student_mobile'], $sms, $te_id);
        return $res['id'];
    }


function subject_list($class_name,$branch_id=null)
{
    if ($branch_id === null) {
        $branch_id = $_SESSION['branch_id'] ?? '';
    }
	$scls = xss_clean($class_name);
	//$list =array();
// 	$sql = "select * from subject where find_in_set('$scls',student_class) and  subject_type ='Scholastic'  and status ='ACTIVE' and branch_id='$branch_id' and is_extra ='NO' ";
	$sql = "select * from subject where find_in_set('$scls',student_class) and  subject_type ='Scholastic'  and status ='ACTIVE' and branch_id='$branch_id' and is_extra ='NO' and ifnull(show_in_report, 'YES') = 'YES' ";
    $sub = direct_sql($sql);
	foreach ($sub['data'] as $sub) {
		$list[] = $sub['id'];
	}
	return $list;
}


function extra_list($class_name)
{
	$scls = xss_clean($class_name);
	//$list =array();
// 	$sql = "select * from subject where find_in_set('$scls',student_class) and status ='ACTIVE' and is_extra ='YES' ";
	$sql = "select * from subject where find_in_set('$scls',student_class) and status ='ACTIVE' and is_extra ='YES' and ifnull(show_in_report, 'YES') = 'YES' ";
    $sub = direct_sql($sql);
	foreach ($sub['data'] as $sub) {
		$list[] = $sub['id'];
	}
	return $list;
}

function total_collection($from_date='', $to_date='', $mode='')
{
    global $today;
    $branch_id = $branch_id??$_SESSION['branch_id'];
    $amount =0;
    $from_date = ($from_date!=='')?$from_date:$today;
    $to_date = ($to_date !=='')?$to_date:$today;
    if($mode=='CANCEL')
    {
        $sql = "select sum(paid_amount) as total from receipt  where status ='CANCEL' and branch_id ='$branch_id' and paid_date between '$from_date' and '$to_date' " ;
    }else{
        $mode = ($mode !='') ? " and payment_mode ='$mode'" :$mode;
    $sql = "select sum(paid_amount) as total from receipt  where status ='PAID' and branch_id ='$branch_id'  and paid_date between '$from_date' and '$to_date' " .$mode ;
    }
    $res = direct_sql($sql);
    if($res['count']>0)
    {
        $amount = $res['data'][0]['total'];
    }
	return $amount;
}


function wa_send_old($number, $message, $media_url='' ) {
	global $instance_id;
	global $access_token;
    $api_url = 'https://wa.pinkcarpet.in/api/send';

    insert_data('wa_sms',['mobile'=>'91'.$number, 'msg' =>$message,'status'=>'PENDING']);
    
    if(strlen($number)==10)
    {
        // Prepare the data for the POST request
        $data = array(
            'number' => '91'.$number,
            'type' => 'media',
            'message' => $message,
           
            'instance_id' => $instance_id,
            'access_token' => $access_token
        );
    
    	if (!filter_var($media_url, FILTER_VALIDATE_URL) === false) {
         
          $data['media_url'] = $media_url;
        } 
        // Encode the data in JSON format
        $json_data = json_encode($data);
    
        // Initialize cURL session
        $ch = curl_init($api_url);
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        
         // Set timeout to a low value so the request doesn't wait for long
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Set the timeout in seconds
        
        // Execute the request asynchronously
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
    
        
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json',
        //     'Content-Length: ' . strlen($json_data)
        // ));
    
        // Execute the cURL session and get the response
        echo $response = curl_exec($ch);
    
        // Close cURL session
        curl_close($ch);
        
    }
}



function wa_send($mobile, $msg, $file_url='', $file_name ='' ){
   	global $instance_id;
	global $access_token;
    
    if(strlen($mobile)==10) 
    {
    $msg = urlencode($msg);
    $api_url="https://wa.pinkcarpet.in/api/send?number=91{$mobile}&type=media&message=$msg&media_url=$file_url&filename=$file_name&instance_id=$instance_id&access_token=$access_token"; 
    //$api_url="https://wa.pinkcarpet.in/api/send?number=919431426600&type=media&message=$msg&media_url=$file_url&filename=$file_name&instance_id=$instance_id&access_token=$access_token"; 
    $ch = curl_init($api_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $response = curl_exec($ch);
    }
}


function isValidIndianMobileNumber($number) {
    $number = preg_replace('/[^0-9]/', '', $number);
    
    if (strlen($number) !== 10) {
        return false;
    }
    
    $validPrefixes = ['9', '8', '7', '6'];
    $firstDigit = substr($number, 0, 1);
    
    if (!in_array($firstDigit, $validPrefixes)) {
        return false;
    }
    
    return true;
}

function get_student_mobile( $class ='ALL')
{
    if($class=='NA')
    {
    $list1 = create_list('student','student_mobile');
    $list2 = create_list('student','student_whatsapp');
    }
    else{
      $list1 = create_list('student','student_mobile',['student_class'=>$class]);
    $list2 = create_list('student','student_whatsapp',['student_class'=>$class]);   
    }

    $flist = array_unique(array_merge($list1, $list2));
    
    foreach ($flist as $number) {
        if (isValidIndianMobileNumber($number)) {
            $validContacts[] = $number;
        }
    }
    
    return $validContacts;
}


function new_roll_no($student_class, $student_section, $order_by =' student_name ') {
    global $branch_id;
    
    $res = get_all('student','*',['student_class'=>$student_class,'student_section'=>$student_section,'status'=>'ACTIVE'],'student_name ASC ');
    $ct=0;
    $serial = 1;
    foreach ($res['data'] as $row) {
        $res2 = update_data('student', ['student_roll'=>$serial], $row['id']);
        if($res2['status']=='success')
        {
            $ct++;
        }
        $serial++;
    }
    return $ct;
}

function total_sale($mode='')
    {
        global $today;
        $total =0;
        $branch_id = $_SESSION['branch_id'];
        if($mode=='')
        {
         $total= intval(get_all('invoice', ['sum(payment) as total '] ,['txn_date'=>$today,'status'=>'CLOSED','branch_id'=>$branch_id])['data'][0]['total']);
        }
        else{
        $total= intval(get_all('invoice', ['sum(payment) as total '] ,['txn_date'=>$today,'payment_mode'=>$mode,'status'=>'CLOSED','branch_id'=>$branch_id])['data'][0]['total']);
        }   
        return $total;
    }
    
function closing_report($txn_date)
    {
        // Get Opeing Balance 
        get_all('closing_report','*',null,' id dsec limit 1');
    }

// Closing Report

function get_dates_between($start, $end){
    $dates = array();
    $current = strtotime($start);
    $end = strtotime($end);

    while ($current <= $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    return $dates;
}

function daily_closing($txn_date)
{
// Get Opeing Balance 
    $sql ="select * from closing_report where closing_date < '$txn_date' order by closing_date desc limit 1 ";
    $res =direct_sql($sql);
   // $res =   get_all('closing_report','*',null,' id desc limit 1');
     
     if($res['count']>0)
     {
        $data['closing_date'] = $txn_date;
        $data['opening_balance'] = $opening_balance =$res['data'][0]['closing_balance'];
        
        //Inventory Report
       $cash_sale= intval(get_all('invoice', ['sum(payment) as total '] ,['txn_date'=>$txn_date,'payment_mode'=>'CASH','status'=>'CLOSED'])['data'][0]['total']);
       $bank_sale= intval(get_all('invoice', ['sum(payment) as total '] ,['txn_date'=>$txn_date,'payment_mode'=>'BANK','status'=>'CLOSED'])['data'][0]['total']);
       
       
        // Cash Collection 
        $cash= intval(get_all('receipt', ['sum(paid_amount) as total '] ,['paid_date'=>$txn_date,'payment_mode'=>'CASH','status'=>'PAID'])['data'][0]['total']);
        $bank = intval(get_all('receipt', ['sum(paid_amount) as total '] ,['paid_date'=>$txn_date,'payment_mode'=>'BANK','status'=>'PAID'])['data'][0]['total']);
        $data['exp_cash']  =$exp_cash = intval(get_all('account_txn', ['sum(txn_amount) as total '] ,['txn_date'=>$txn_date,'txn_mode'=>'CASH','status'=>'ACTIVE'])['data'][0]['total']);
        $data['exp_bank']  = intval(get_all('account_txn', ['sum(txn_amount) as total '] ,['txn_date'=>$txn_date,'txn_mode'=>'BANK','status'=>'ACTIVE'])['data'][0]['total']);
       
        $data['inc_cash']  =$cash +$cash_sale;
        $data['inc_bank']  =$bank +$bank_sale;
        
        $data['closing_balance'] = ($opening_balance + $cash + $cash_sale) - $exp_cash;
        $search = get_all('closing_report','*',['closing_date'=>$txn_date]);
        if($search['count']>0)
        {
            update_data('closing_report',$data,$txn_date,'closing_date');
        }
        else{
            insert_data('closing_report',$data);
        }
     }
}

function closing_update()
{
    global $today;
    global $db_name;
    global $CONFIG;
    if($_SESSION['db_name'] ==$CONFIG['db_name'])
    {
        $res =   get_all('closing_report','*',null,' id desc limit 1');
        $start = $res['data'][0]['closing_date'];
        $dates = get_dates_between($start, $today);
        foreach($dates as $tdate)
        {
             daily_closing($tdate);
        }
    }
}

function account_balance($type='opening_balance', $date ='')
{
    global $today;
    global $db_name;
    global $CONFIG;
    $branch_id = $branch_id??$_SESSION['branch_id'];
    $bal =0;
    if($_SESSION['db_name'] ==$CONFIG['db_name'] or $db_name == $CONFIG['db_name'] )
    {
        if($date =='')
        {
        //    $res= get_data('closing_report',$today, null, 'closing_date');
           $res= get_all('closing_report','*',['closing_date'=>$today]);
        }
        else{
        //    $res  = get_data('closing_report',$date, null, 'closing_date'); 
           $res= get_all('closing_report','*',['closing_date'=>$date]);
        }
        if($res['count']>0)
        {
            $bal = $res['data']['0'][$type];
        }
    }
    return intval($bal);
}

function total_sale_count($item_id)
{
    $total = 0;
    $sql ="select sum(qty) as total from distribute_item where item_id='$item_id' and status ='ACTIVE' ";
    $res = direct_sql($sql);
    if( $res['count']>0)
    {
        $total  = $res['data'][0]['total'];
    }
    return $total;
}


function total_purchase_count($item_id)
{
    $total = 0;
    $sql ="select sum(quantity) as total from purchase_item where name='$item_id'";
    $res = direct_sql($sql);
    if( $res['count']>0)
    {
        $total  = $res['data'][0]['total'];
    }
    return $total;
}
//2024 Updates

function simple_widget ($ct, $title, $icon='fa fa-user', $color='info' )
    {
        $str ='<div class="col-xl-3 col-md-6 col">
			  <div class="info-box">
				<span class="info-box-icon bg-'.$color.'"><i class="'.$icon.'"></i></span>
				<div class="info-box-content">
				  <span class="info-box-number">'.$ct.'<small></small></span>
				  <span class="info-box-text">'.$title.'</span>
				</div>
			  </div>
			</div>';
			
		return $str;
    }
    
function area_list()
    {
        
        $res =get_all('transport_area');
        foreach($res['data'] as $row)
        {
            $ct = get_all('student','*',['area_id'=>$row['id'],'status'=>'ACTIVE'])['count'];
            if($ct>0)
            {
            $data[$row['id']] ="<span class='badge bg-orange'> $ct </span> ". $row['area_name'] .$row['id'] ." [₹".$row['area_fee']."]";
            }
        }
        return $data;
    }

function co_subject_list($student_class='')
{
    if($student_class =='')
    {
    // $sql = "select * from subject where status ='ACTIVE' and subject_type ='Non Scholastic' ";
    $sql = "select * from subject where status ='ACTIVE' and subject_type ='Non Scholastic' and ifnull(show_in_report, 'YES') = 'YES' ";
    }
    else{
    // $sql = "select * from subject where find_in_set('$student_class',student_class) and  status ='ACTIVE' and subject_type ='Non Scholastic' ";
	$sql = "select * from subject where find_in_set('$student_class',student_class) and  status ='ACTIVE' and subject_type ='Non Scholastic' and ifnull(show_in_report, 'YES') = 'YES' ";
    }
    $res = direct_sql($sql);
    return $res['data'];
}

function co_marks($student_admission, $exam_id)
{
    $sql = "select * from exam where student_admission='$student_admission' and exam_id ='$exam_id'";
    $res = direct_sql($sql);
    if($res['count']>0)
    {
    return $res['data'][0];
    }
}

function max_marks($exam_id,$subject_id, $mstype ='')
{
    $max =0;
    $res = get_all('admit_card','*',['exam_id'=>$exam_id, 'subject_id'=>$subject_id]);
    if($res['count']>0)
    {
        $max =$res['data'][0];
    
        if($mstype <>'')
        {
        $max = $max['marks_'.remove_space($mstype)];
        }
    }
    return $max;
}


function add_branch_id()
{
    global $con;
    $query = "SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'";
    $result = $con->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableName = $row['table_name'];

            // Check if branch_id column already exists
            $checkColumnQuery = "SELECT COUNT(*) AS column_exists 
                                FROM information_schema.columns 
                                WHERE table_schema = DATABASE() 
                                AND table_name = '$tableName' 
                                AND column_name = 'branch_id'";
            $columnResult = $con->query($checkColumnQuery);
            $columnData = $columnResult->fetch_assoc();

            if ($columnData['column_exists'] == 0) {
                // Alter table to add branch_id column if it doesn't exist
                $alterQuery = "ALTER TABLE `$tableName` ADD COLUMN `branch_id` INT NULL";
                if ($con->query($alterQuery) === TRUE) {
                    echo "Added branch_id column to table $tableName successfully.<br>";
                } else {
                    echo "Error adding branch_id column to table $tableName: " . $con->error . "<br>";
                }
            } else {
                echo "Column branch_id already exists in table $tableName.<br>";
            }
        }
    } else {
        echo "No tables found in the database.";
    }
    // Close the connection
    $con->close();
}

function branch_list()
    {
        if($_SESSION['user_type'] == 'DEV'){
            $branches_arr = fetch_all('branch_list', 'id',['status'=>'AUTO,DELETED'],['condition_type'=>' NOT IN '])['data'];
        } else {
            $branches = get_data('user', $_SESSION['user_id'],'allowed_branch')['data'];
            $branches_arr = explode(",",$branches);
        }    
        foreach($branches_arr as $bid)
        {
            if($_SESSION['user_type'] == 'DEV'){
                $b_id = $bid['id'];           
                $data[$b_id] = get_data('branch_list',$b_id,'inst_name')['data'];
            } else {
                $data[$bid] = get_data('branch_list',$bid,'inst_name')['data'];
            }          
        }
        return $data;
    }
    
function show_dropdown($tablename, $value, $list, $selected = null, $list2 = null)
{
	global $con;
	$i = 0;
	$query = "select * from $tablename where status not in('AUTO','BLOCK','DELETED') order by $list";
	$res = mysqli_query($con, $query) or die(" Creating Drop down Error : " . mysqli_error($con));
	while ($row = mysqli_fetch_array($res)) {
		$key = $row[$value];
		$show = $row[$list];
		$col2 = '';
		if ($list2 <> null) {
			$col2 = "[ " . $row[$list2] . " ]";
		}

	?>
		<option value='<?php echo $key; ?>' <?php if ($key == $selected) echo "selected"; ?>><?php echo $show . " " . $col2; ?></option>
	<?php
	}
}

//get present in a month overall
function get_total_present_in_month($table_name)
{
	global $att_month_list;
	foreach ($att_month_list as $month) {
		$count = 0;
		$m_split = strtolower(substr($month, 0, 3));
		$y =  date('Y');
		$d_m = $m_split . "_" . $y;
		$sql =  "SELECT * from $table_name where att_month like '$d_m' and branch_id ='{$_SESSION['branch_id']}' ";
		$student = direct_sql($sql);
		if ($student['count'] > 0) {
			foreach ($student['data'] as $stu_row) {
				for ($i = 1; $i <= 31; $i++) {
					$day = "d_" . $i;
					if ($stu_row[$day] ==  "P") {
						$count++;
					}
				}
			}
		}
		echo "<td><b>" . $count . "</b></td>";
	}
}

//get present in a month class and sectionwise
function get_present_in_month($class, $section)
{
	global $att_month_list;
	foreach ($att_month_list as $month) {
		$count = 0;
		$month_split = strtolower(substr($month, 0, 3));
		$year = date('Y');
		$att_month = $month_split . "_" . $year;
		$mvalue = remove_space($att_month);
		$sql ="SELECT  student.student_name, student.student_admission, student.student_class, student.student_section, student.student_roll, student_category, student.status , student_att.* FROM `student_att` left join student on student.id = student_att.student_id where student_class ='$class' and student_section ='$section' and att_month='$mvalue'"; 
			$res = direct_sql($sql);
				foreach ((array)$res['data'] as $att_data) {
					for ($i = 1; $i < 31; $i++) {
						$day = "d_" . $i;
						if ($att_data[$day] == "P") {
							$count++;
						}
					}
				}
		$str = ($count>0)?"<a href='student_monthwise_att_report?class=$class&section=$section&month=$month' target='_blank'>" . $count . "</a>":" - ";
		echo  "<td>$str</td>";
	}
}

		
function checklist_with_id($name, $array_list, $selected=null, $height='160px' )
		{
			$selected = explode(',',$selected);
			echo "<div style='overflow-y:auto;height:$height'>";
			?>
			<span class='btn btn-sm btn-info float-right'  onclick="selectcheck('<?php echo $name;?>')" ><i class='fa fa-check'></i></span><hr>
			<?php
			foreach(array_filter($array_list) as $id=>$list)
			{
				$checked =null;
				$x = array_search(trim($id), array_map('trim', $selected));
				
				if( $x >=-1){ $checked ='checked';}
				?>
				<div class="checkbox">
					  <input type="checkbox" value="<?php echo $id;?>" id="Checkbox_<?php echo $list;?>" <?php echo $checked; ?> name='<?php echo $name.'[]';?>'> 
					  <label for="Checkbox_<?php echo $list;?>"><?php echo $list?></label>
				</div>
				<?php
			}
			echo "</div>";
		}
		


function create_list_with_id($table_name, $field,  $whereArr =null)
{
global $con;
$cols = array();
 

if($whereArr != null)
{
    foreach($whereArr as $key=>$value) 
        {
            $newvalue = preg_replace('/[^A-Za-z.@,:+0-9\-_]/', ' ', $value);
            $where[] = "$key = '$newvalue'";
        }
    $sql = "select distinct($field),id from ". $table_name ." WHERE " .implode('and ', $where);
        
}
else{
    $sql = "select distinct($field),id from ". $table_name;
}

$res = mysqli_query($con,$sql) or die(" Error IN create List : ".mysqli_error($con));
    if (mysqli_num_rows($res)>=1)
    {
        while($row =mysqli_fetch_assoc($res))
        {
            $id = $row['id'];
            $list[$id] =$row[$field];
        }
    }
    else{
        return null;
    }
return $list;
}


		
function active_class_list()
{
    $res = direct_sql('SELECT student_class, student_section FROM `student` group by student_class, student_section');
    
    foreach($res['data'] as $row)
    {
          if($row['student_class'] !='' and $row['student_section'] !='' )
          {
           $all_class_list[] = $row['student_class'] ."-".$row['student_section'];
          }
    }
    return array_filter($all_class_list);
}


function get_present_emp($id)
{
	global $att_month_list;
	foreach ($att_month_list as $att_month) {
		$count = 0;
		$m_split = strtolower(substr($att_month, 0, 3));
		$y =  date('Y');
		$month = $m_split . "_" . $y;
		$mvalue = remove_space($month);
		$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
		$get_emp = direct_sql($sql);
		if ($get_emp['count'] > 0) {
			foreach ($get_emp['data'] as $row) {
				for ($i = 1; $i <= 31; $i++) {
					$day = "d_" . $i;
					if ($row[$day] ==  "P") {
						$count++;
					}
				}
			}
		}
		echo "<td><b>" . $count . "</b></td>";
	}
}

//Employee Attendance Strength
function get_emp_pre_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "P") {
					$count++;
				}
			}
		}
	}
    return $count;
}
function get_emp_abs_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "A") {
					$count++;
				}
			}
		}
	}
    return $count;
}
function get_emp_leave_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "L") {
					$count++;
				}
			}
		}
	}
    return $count;
}

// STUDENT ATT DETAILS 
function att_status ($student_id, $month_name, $dt)
{
    $status ='';
    $res  = get_all('student_att','*',['student_id'=>$student_id,'att_month'=>remove_space($month_name),'branch_id'=>$_SESSION['branch_id']]);
    if($res['count']>0)
    {
        $data = $res['data'][0];
        $status=  $data['d_'.$dt];
    }
    return $status;
}



function today_att_status($stu_class, $stu_section ,$status ='P' )
{
    $att_month = strtolower(date('M'))."_".date('Y');
    $att_date ="d_".date('j');
    // $sql ="SELECT student_class, student_section, count(student_id) as count FROM `student_att`, student where student_att.student_id = student.id and student_att.branch_id='{$_SESSION['branch_id']}' and att_month ='$att_month' and $att_date ='$status' and student_class ='$stu_class' and student_section ='$stu_section' group by student_class, student_section";
$sql = "SELECT 
            student.student_class, 
            student.student_section, 
            COUNT(student_att.student_id) AS count
        FROM student_att
        INNER JOIN student 
            ON student_att.student_id = student.id
        WHERE student_att.branch_id = '{$_SESSION['branch_id']}'
            AND student_att.att_month = '$att_month'
            AND student_att.$att_date = '$status'
            AND student.student_class = '$stu_class'
            AND student.student_section = '$stu_section'
        GROUP BY student.student_class, student.student_section";
    $res = direct_sql($sql,'get',false);
  return $res['data'][0]['count'];
}
    
function present_emp($att_dt)
{
    $att_dt = $att_dt ?? date('j');
    $att_month = strtolower(date('M')) . "_" . date('Y');
    $att_date = "d_" . intval($att_dt);
    $branch_id = intval($_SESSION['branch_id']);

    $sql = "SELECT emp_id FROM employee_att WHERE att_month = '$att_month' AND `$att_date` = 'P' AND branch_id = '$branch_id'";
    $res = direct_sql($sql, 'get', false);
    $absentees = [];

    if (!empty($res['data'])) {
        foreach ($res['data'] as $row) {
            $emp = get_data('employee', $row['emp_id'], 'name');
            if (!empty($emp['data'])) {
                $absentees[] = $emp['data'];
            }
        }
    }
    return $absentees;
}    
 
function absent_emp($att_dt)
{
    $att_dt = $att_dt ?? date('j');
    $att_month = strtolower(date('M')) . "_" . date('Y');
    $att_date = "d_" . intval($att_dt);
    $branch_id = intval($_SESSION['branch_id']);

    $sql = "SELECT emp_id FROM employee_att WHERE att_month = '$att_month' AND `$att_date` = 'A' AND branch_id = '$branch_id'";
    $res = direct_sql($sql, 'get', false);
    $absentees = [];

    if (!empty($res['data'])) {
        foreach ($res['data'] as $row) {
            $emp = get_data('employee', $row['emp_id'], 'name');
            if (!empty($emp['data'])) {
                $absentees[] = $emp['data'];
            }
        }
    }
    return $absentees;
}    

function leave_emp($att_dt)
{
    $att_dt = $att_dt ?? date('j');
    $att_month = strtolower(date('M')) . "_" . date('Y');
    $att_date = "d_" . intval($att_dt);
    $branch_id = intval($_SESSION['branch_id']);

    $sql = "SELECT emp_id FROM employee_att WHERE att_month = '$att_month' AND `$att_date` = 'L' AND branch_id = '$branch_id'";
    $res = direct_sql($sql, 'get', false);
    $absentees = [];

    if (!empty($res['data'])) {
        foreach ($res['data'] as $row) {
            $emp = get_data('employee', $row['emp_id'], 'name');
            if (!empty($emp['data'])) {
                $absentees[] = $emp['data'];
            }
        }
    }
    return $absentees;
}



function monthly_avg_att($class, $section='')
{
    $mvalue = remove_space(date('M'))."_".date('Y');
    
    if($section==''){
            $get_stu = get_all('student', '*', array('student_class' => $class));
    }
    else{
    $get_stu = get_all('student', '*', array('student_class' => $class, 'student_section' => $section));
    }
    
    if ($get_stu['count'] > 0) {
        foreach ($get_stu['data'] as $row) {
            $id = $row['id'];
            $sql = "SELECT * from student_att where student_id = $id and att_month like '$mvalue' ";
            $res = direct_sql($sql);
            if ($res['count'] >  0) {
                foreach ($res['data'] as $att_data) {
                    for ($i = 1; $i < 31; $i++) {
                        $day = "d_" . $i;
                        if ($att_data[$day] == "P") {
                            $count++;
                        }
                    }
                }
            }
        }
    }
return $x= round($count/date('d'));
}

// Phase Select 

// function get_phase($app_no)
// {
// $created_at  = get_data('admission', $app_no, 'created_at', 'app_no')['data'];

// if(strtotime($created_at)<=strtotime('2026-01-19'))
// 	{
// 	    $exam_date = "18 Jan 2026";
// 	    $phase ='1st Phase';
// 	}
// 	else if(strtotime($created_at)>strtotime('2026-01-19') and strtotime($created_at)<=strtotime('2026-02-15'))
// 	{
// 	    $exam_date = "15 Feb 2026";
// 	    $phase ='2nd Phase';
// 	}
// 	else if(strtotime($created_at)>strtotime('2026-02-15') and strtotime($created_at)<strtotime('2026-03-08'))
// 	{
// 	    $exam_date = "08 March 2026";
// 	    $phase ='3rd Phase';
// 	}
// 	else if(strtotime($created_at)>strtotime('2026-03-08') and strtotime($created_at)<=strtotime('2026-03-29'))
// 	{
// 	    $exam_date = "29 March 2026";
// 	    $phase ='4th Phase';
// 	}
// // 	else if(strtotime($created_at)>strtotime('2025-03-23') and strtotime($created_at)<=strtotime('2025-04-02'))
// // 	{
// // 	    $exam_date = "02 April 2025";
// // 	    $phase ='5th Phase';
// // 	}
// // 	else if(strtotime($created_at)>strtotime('2025-04-02') and strtotime($created_at)<=strtotime('2025-04-30'))
// // 	{
// // 	    $exam_date = "30 April 2025";
// // 	    $phase ='Last Phase';
// // 	}
// 	else 
// 	{
// 	    $exam_date = "Sorry General Admission Date Expired. Only Management Quota Admission Open";
// 	    $phase ='No General Admission Phase Available ';
// 	}
	
// 	$res['exam_date'] = $exam_date;
// 	$res['phase'] = $phase;
	
// 	return $res;
// }

function get_phase($app_no)
{
    $created_at = get_data('admission', $app_no, 'created_at', 'app_no')['data'];
    $branch_id = $_SESSION['branch_id'] ?? 1;

    // Default response if no phase matches or date is expired
    $exam_date = "Sorry General Admission Date Expired. Only Management Quota Admission Open";
    $phase = 'No General Admission Phase Available ';

    if (!empty($created_at)) {
        $sql = "SELECT phase_name, exam_date FROM admission_phase 
                WHERE status = 'ACTIVE' 
                AND branch_id = '$branch_id' 
                AND DATE('$created_at') <= end_date 
                ORDER BY end_date ASC LIMIT 1";
        
        $res_db = direct_sql($sql);
        if ($res_db['count'] > 0) {
            $phase_data = $res_db['data'][0];
            $phase = $phase_data['phase_name'];
            $exam_date = $phase_data['exam_date'];
        }
    }

    $res['exam_date'] = $exam_date;
    $res['phase'] = $phase;

    return $res;
}


// New Bine Updates 


	
function student_paid($sid)
	{
	    $total =0;
	    $sql ="select sum(paid_amount) as total from receipt where student_id ='$sid' and status ='PAID' ";
	    $res = direct_sql($sql);
	    if($res['count']>0)
	    {
	    $total = $res['data'][0]['total'];
	    }
	    return $total;
	}

function promote_student($student_admission, $new_class, $prev_dues=0)
{
    global $current_session;
    $res =null;
    $search = get_data('student',$student_admission,null,'student_admission');
   
    if($search['count']>0)
    {
        $student_id = $search['data']['id'];
    
    $sres =update_data('student',['student_class'=>$new_class,'student_session' =>$current_session,'admission_type'=>'OLD','base_dues'=>$prev_dues,'status'=>'ACTIVE'], $student_admission,'student_admission');
        if($sres['status']=='success')
        {
        $res  = update_data('student_fee',['prev_session_dues'=>$prev_dues,'current_dues' =>0, 'student_admission'=>$student_admission], $student_id,'student_id');
        }
    }
    return $res ;
}


function get_transport ($student_id,$col_name='area_name')
    {
        $value =null;
        $student = get_data('student',$student_id)['data'];
        
        $res =  fetch_all('transport_area', '*', ['branch_id'=>$student['branch_id'], 'id' => $student['area_id']]);
        
        if($res['count']>0)
        {
            $data =$res['data'][0];
            $value =$data[$col_name];
        }
        return $value;
    }
 
 function student_target($student_id)
    {
        global $month_list;
        $total =0;
        $current_dues = get_data('student_fee', $student_id,'current_dues')['data'];
        update_data('student_target',['current_dues' =>$current_dues],$student_id,'student_id');
        foreach ($month_list as $month)
        {
            
            $mfee = monthly_fee($student_id, $month)['fee']['total'];
            $total +=$mfee;
            $res1 = update_data('student_target', [$month=>$mfee], $row['id'], 'student_id');
        }
        return $total;
    }
 
function fee_target_by_month($month, $class = null, $section = null) {
    global $con, $branch_id;

    $allowed_months = ['admission_month','april','may','june','july','august','september','october','november','december','january','february','march'];
    $month = strtolower($month);

    if (!in_array($month, $allowed_months)) {
        return 0;
    }

    // Escape for safety
    $branch_id = mysqli_real_escape_string($con, $branch_id);
    $sql = "SELECT SUM($month) AS total FROM student_target WHERE branch_id = '$branch_id' AND status = 'ACTIVE'";

    if (!empty($class)) {
        $class = mysqli_real_escape_string($con, $class);
        $sql .= " AND student_class = '$class'";
    }

    if (!empty($section)) {
        $section = mysqli_real_escape_string($con, $section);
        $sql .= " AND student_section = '$section'";
    }

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
}

function fee_collection_by_month($month, $class = null, $section = null) {
    global $con, $branch_id;

    $allowed_months = ['admission_month','april','may','june','july','august','september','october','november','december','january','february','march'];
    $month = ucfirst(strtolower($month)); // Ensure 'April' not 'april'

    if (!in_array(strtolower($month), $allowed_months)) {
        return 0;
    }

    // Escape inputs
    $branch_id = mysqli_real_escape_string($con, $branch_id);
    $month = mysqli_real_escape_string($con, $month);
    $start_date = date('Y-04-01');
    $sql = "
        SELECT SUM(receipt.paid_amount) AS total
        FROM receipt
        JOIN student ON receipt.student_id = student.id
        WHERE receipt.status = 'PAID'
        AND paid_date >= '$start_date'
        AND student.branch_id = '$branch_id'
        AND MONTHNAME(receipt.paid_date) = '$month'
    ";

    if (!empty($class)) {
        $class = mysqli_real_escape_string($con, $class);
        $sql .= " AND student.student_class = '$class'";
    }

    if (!empty($section)) {
        $section = mysqli_real_escape_string($con, $section);
        $sql .= " AND student.student_section = '$section'";
    }

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
}


function last_dues($student_id)
    {
        $last_dues =0 ;
        $sql ="select * from receipt where student_id ='$student_id' and status ='PAID' order by id desc limit 1";
        $res  = direct_sql($sql);
        if($res['count']>0)
        {
            $last_dues = $res['data'][0]['current_dues'];
        }
        return $last_dues;
    }

// function monthly_target($month, $stu_class, $stu_section='A')
//     {
//         $target=0;
//         $students  = get_all('student',['id'],['status'=>'ACTIVE','student_class'=>$stu_class,'student_section'=>$stu_section]);
        
//         foreach((array)$students['data'] as $student)
//         {
//             $mfee = monthly_fee($student['id'], $month)['fee']['total'];
            
//             $target +=$mfee;   
//         }
//         return $target;
//     }


 
 function last_adm()
 {
    $roll_no =1;
     $sql ="select student_admission from student where student_admission!='' order by student_admission desc limit 1 ";
     $res = direct_sql($sql);
     if($res['count'] ==1)
     {
        $roll_no =  $res['data'][0]['student_admission'];
         
     }
     return $roll_no +1; 
 }
 
function student_att_list($student_id, $month='')
{
	    $month = ($month=='')? date('F') : $month;
		$att_month = remove_space(date('M_Y', strtotime($month))) ;
        $plist[$month] =[];
		$sql = "SELECT * from student_att where student_id = '$student_id' and att_month like '$att_month' ";
		$res = direct_sql($sql);
		if ($res['count'] >  0) {
			foreach ($res['data'] as $att_data) {
				for ($i = 1; $i < 31; $i++) {
					$day = "d_" . $i;
					
					if ($att_data[$day] == "P") {
					    $plist[$month][] = 	$i;
					}
				}
			}
		}
	    
	return $plist;
}



function get_annual_fee($student_id)
		{
		$student = get_data('student',$student_id,null)['data'];
		
		//$annual_fee = get_data('fee_details',$student['student_class'],'annual_fee','student_class')['data'];
		//$exam_fee = get_data('fee_details',$student['student_class'],'exam_fee','student_class')['data'];
		
		$annual_fee = monthly_fee($student_id,'admission_month')['fee']['total'];
		
		$astatus  = get_data('student_fee', $student_id, 'admission_month','student_id')['data'];
		
    		if(empty($astatus))
    		{
    		    $fee = $annual_fee;
    		}
    		else{
    		    $fee =0;
    		}

		return $fee;
		}
 
function determine_term_and_types($exam_id) {
    switch ($exam_id) {
        case 'UNIT TEST 1': return ['TERM 1', ['ut1']];
        case 'UNIT TEST 2': return ['TERM 1', ['ut2']];
        case 'TERM 1': return ['TERM 1', ['ut1', 'ut2', 'mid_term']];
        case 'UNIT TEST 3': return ['TERM 2', ['ut3']];
        case 'UNIT TEST 4': return ['TERM 2', ['ut4']];
        case 'TERM 2': return ['TERM 2', ['ut3', 'ut4', 'annual']];
        default: return [$exam_id, []];  // fallback
    }
}

function get_tabulation_marks($student_admission, $term, $subject_id, $marks_type_list)
{
    $data = [];
    $student_admission = addslashes($student_admission);
    $term = addslashes($term);
    $subject_id = (int)$subject_id;

    $sql = "SELECT * FROM tabulation WHERE student_admission = '$student_admission' AND term = '$term' AND subject = $subject_id LIMIT 1 ";
    $res = direct_sql($sql);

    if ($res['count'] == 1) {
        $row = $res['data'][0];
        $total = 0;
        foreach ($marks_type_list as $mstype) {
            $marks = isset($row[$mstype]) ? (int)$row[$mstype] : 0;
            $data[$mstype] = $marks;
            $total += $marks;
        }
        $data['total'] = $total;
    } else {
        foreach ($marks_type_list as $mstype) {
            $data[$mstype] = 0;
        }
        $data['total'] = 0;
    }
    return $data;
}
 

function get_marks_by_sid($student_id, $exam_name, $subject, $student_class)
{
	global $con;
	$sql ="select * from tabulation where student_id  ='$student_id' and term like '$exam_name' and subject = $subject ";
	$res = direct_sql($sql);
	if($student_class=='I' || $student_class=='II'){
// 	print_r($res);
	if ($res['count'] ==1)
		{
		$row =$res['data'][0];
		    $data['ut1'] = $row['ut1'];
		    $data['ut2'] = $row['ut2'];
		    $data['ga']	 = $row['ga'];
		    $mid1 = $data['mid_term']	 = $row['mid_term'];
		    $data['nb']	 = $row['nb'];
		    $data['sea'] = $row['sea'];
		    $data['pa'] = ($data['ga'] + max($data['ut1'], $data['ut2']))/5;
		    $nmid1 = number_format($mid1 * (4 / 3), 2);
		}
	    
	}
// 		if ($res['count'] ==1)
// 		{
// 		$row =$res['data'][0];
// 		$data['pt']	 = $row[$subject.'_pt'];
// 		$data['mo']	 = $row[$subject.'_mo'];		
// 		$data['se']	 = $row[$subject.'_se'];		
// 		$data['nb']	 = $row[$subject.'_nb'];
// 		$data['total'] =$data['nb']+$data['se']+$data['mo'] +$data['pt'];
// 		}
// 		else{
// 		$data['pt']	 = 0;		
// 		$data['mo']	 = 0;		
// 		$data['se']	 = 0;		
// 		$data['nb']	 = 0;
// 		$data['total'] = 0;
// 		}
	return $data;
}

function get_co_marks_by_sid($student_id, $exam_name, $subject)
{
	global $con;
	$sql ="select * from co_scholastic where student_id  ='$student_id' and exam_name like '$exam_name' ";
	$res = direct_sql($sql);
// 	print_r($res);
	$data =NULL;
		if ($res['count'] ==1)
		{
		$data =$res['data'][0][$subject];
		}
	return $data;
}

  
function get_category($class) {
    $junior = ["NUR", "LKG", "UKG", "I", "II", "III", "IV", "V"];
    $senior = ["VI", "VII", "VIII", "IX", "X", "XI", "XII"];

    if (in_array($class, $junior)) {
        return "JUNIOR";
    } elseif (in_array($class, $senior)) {
        return "SENIOR";
    } else {
        return "UNKNOWN"; // If class is not found in both lists
    }
} 

    
function get_remarks($student_class, $subject_id, $grade, $subject_name = '')
{
    $remarks = '';
    $student_type = get_category($student_class);

    if (!empty($subject_id) && is_numeric($subject_id)) {
        $subject_id = intval($subject_id); 
        $res = get_all('remark', '*', ['subject' => $subject_id, 'branch_id' => 1]);//'class_cat' => $student_type, 
        if ($res['count'] > 0) {
            $remarks = $res['data'][0][$grade];
        }
    } else {
        if (!empty($subject_name)) {
            $res = get_all('remark', '*', ['subject_name' => $subject_name, 'branch_id' => 1]);//'class_cat' => $student_type, 
            if ($res['count'] > 0) {
                $remarks = $res['data'][0][$grade];
            }
        }
    }

    return $remarks;
}

function chart_graph($student_id) {
    // Get student class
    $student = get_data('student', $student_id)['data'];
    $student_class = $student['class'];

    // Fetch all subjects for that class
    $subjects = subject_list($student_class);
    $merged_subjects = [];

    // Merge English/Hindi/Urdu for primary classes
    if (in_array($student_class, ['I','II','III','IV','V','VI','VII','VIII'])) {
        $english_done = $hindi_done = $urdu_done = false;
        foreach ($subjects as $sub) {
            $sub_id = is_array($sub) ? $sub['id'] : $sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];

            if (!$english_done && stripos($sub_name, 'English') !== false) {
                $merged_subjects[] = ['id' => 'english', 'subject_name' => 'English'];
                $english_done = true;
            } elseif (!$hindi_done && stripos($sub_name, 'Hindi') !== false) {
                $merged_subjects[] = ['id' => 'hindi', 'subject_name' => 'Hindi'];
                $hindi_done = true;
            } elseif (!$urdu_done && stripos($sub_name, 'Urdu') !== false) {
                $merged_subjects[] = ['id' => 'urdu', 'subject_name' => 'Sanskrit / Urdu'];
                $urdu_done = true;
            } elseif (
                stripos($sub_name, 'English') === false &&
                stripos($sub_name, 'Hindi') === false &&
                stripos($sub_name, 'Urdu') === false
            ) {
                $merged_subjects[] = ['id' => $sub_id, 'subject_name' => $sub_name];
            }
        }
    } else {
        foreach ($subjects as $sub) {
            $sub_id = is_array($sub) ? $sub['id'] : $sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
            $merged_subjects[] = ['id' => $sub_id, 'subject_name' => $sub_name];
        }
    }

    // --- BUILD SCORES ---
    $score = [];
    foreach ((array)$merged_subjects as $sub) {
        $sub_id = $sub['id'];
        $subject_name = $sub['subject_name'];

        // Reset all term vars
        $ut1 = $ut2 = $ma1 = $portfolio1 = $se1 = $mid1 = $ga1 = $nb1 = $sea1 = 0;

        // Fetch marks for TERM 1
        if (in_array($sub_id, ['english', 'hindi', 'urdu'])) {
            $term1_data = direct_sql(
                "SELECT * FROM tabulation 
                 WHERE student_id='{$student_id}' 
                 AND term='TERM 1' 
                 AND subject IN (SELECT id FROM subject WHERE subject_name LIKE '%{$subject_name}%')",
                'get',
                false
            )['data'] ?? [];

            foreach ($term1_data as $row) {
                $ut1 += $row['ut1'] / 2;
                $ut2 += $row['ut2'] / 2;
                $ma1 += $row['ma'];
                $portfolio1 += $row['portfolio'];
                $se1 += $row['se'];
                $mid1 += $row['mid_term'];
                $ga1 += $row['ga'];
                $nb1 += $row['nb'];
                $sea1 += $row['sea'];
            }
        } else {
            $term1 = direct_sql(
                "SELECT * FROM tabulation 
                 WHERE student_id='{$student_id}' 
                 AND subject='{$sub_id}' 
                 AND term='TERM 1'",
                'get',
                false
            )['data'][0] ?? null;

            if ($term1) {
                $ut1 = $term1['ut1'];
                $ut2 = $term1['ut2'];
                $ma1 = $term1['ma'];
                $portfolio1 = $term1['portfolio'];
                $se1 = $term1['se'];
                $mid1 = $term1['mid_term'];
                $ga1 = $term1['ga'];
                $nb1 = $term1['nb'];
                $sea1 = $term1['sea'];
            }
        }

        $mut1 = max($ut1, $ut2);
        $pa1 = ($ga1 + $mut1) / 5;

        // Class-based total logic
        if (in_array($student_class, ['IX', 'X'])) {
            if ($sub_id != '46') {
                $mut1 = max($ut1, $ut2) / 10;
            }
            $prem = $mut1 + $ma1 + $portfolio1 + $se1;
            $t1t = $mid1 + $prem;
        } elseif (in_array($student_class, ['I', 'II'])) {
            $nmid1 = number_format($mid1 * (4 / 3), 2);
            $t1t = $pa1 + $nb1 + $sea1 + $nmid1;
        } else {
            $t1t = $pa1 + $nb1 + $sea1 + $mid1;
        }

        // Store marks
        $score[$subject_name] = round($t1t, 2);
    }

    // --- Prepare Google Chart data ---
    $chartData = [['Subject', 'Marks']];
    foreach ($score as $subject => $marks) {
        $chartData[] = [$subject, (float)$marks];
    }

    return $chartData;
}


function get_subject_total($student_id, $term, $subject_id, $branch_id) {
    $total = 0;

    // Handle merged subjects like english/hindi/urdu
    if (!is_numeric($subject_id)) {
        $like = ucfirst(strtolower($subject_id));
        $query = "SELECT id FROM subject 
                  WHERE subject_name LIKE '%" . addslashes($like) . "%'
                  AND branch_id='{$branch_id}' 
                  AND status='ACTIVE'";
        $subs = direct_sql($query)['data'] ?? [];
        if (empty($subs)) return 0;
        $subject_ids = array_column($subs, 'id');
    } else {
        $subject_ids = [$subject_id];
    }

    foreach ($subject_ids as $sid) {
        $ss_res = direct_sql("SELECT id FROM pre_sub_subject 
                              WHERE subject_id='{$sid}' 
                              AND branch_id='{$branch_id}' 
                              AND status='ACTIVE'");
        $sub_subjects = $ss_res['data'] ?? [];

        if (empty($sub_subjects)) {
            $sum = direct_sql("
                SELECT SUM(CAST(obtained AS UNSIGNED)) AS total
                FROM pre_marks
                WHERE student_id='{$student_id}' 
                  AND subject_id='{$sid}'
                  AND term='" . addslashes($term) . "'
                  AND branch_id='{$branch_id}'
            ")['data'][0]['total'] ?? 0;
            $total += intval($sum);
            continue;
        }
        foreach ($sub_subjects as $ss) {
            $ss_id = $ss['id'];
            // Fetch papers
            $p_res = direct_sql("SELECT id FROM pre_papers 
                                 WHERE sub_subject_id='{$ss_id}' 
                                 AND branch_id='{$branch_id}' 
                                 AND status='ACTIVE'");
            $papers = $p_res['data'] ?? [];

            if (!empty($papers)) {
                foreach ($papers as $p) {
                    $p_id = $p['id'];
                    $sum = direct_sql("
                        SELECT SUM(CAST(obtained AS UNSIGNED)) AS total
                        FROM pre_marks
                        WHERE student_id='{$student_id}'
                          AND subject_id='{$sid}'
                          AND sub_subject_id='{$ss_id}'
                          AND paper_id='{$p_id}'
                          AND term='" . addslashes($term) . "'
                          AND branch_id='{$branch_id}'
                    ")['data'][0]['total'] ?? 0;
                    $total += intval($sum);
                }
            } else {
                $sum = direct_sql("
                    SELECT SUM(CAST(obtained AS UNSIGNED)) AS total
                    FROM pre_marks
                    WHERE student_id='{$student_id}'
                      AND subject_id='{$sid}'
                      AND sub_subject_id='{$ss_id}'
                      AND term='" . addslashes($term) . "'
                      AND branch_id='{$branch_id}'
                ")['data'][0]['total'] ?? 0;
                $total += intval($sum);
            }
        }
    }

    return intval($total);
}

	
function preduesmonths($student_id)
{
    $ct = 0;
    $prev_dues = get_data('student_fee', $student_id, 'base_dues', 'student_id')['data'];
    
    $list = [];
    if ($prev_dues != 0) {
        $list[] = 'Previous Dues';
    }

    global $att_month_list; // Month Allowed to Collect Fee
    $fee_month = $att_month_list;
    $currentMonth = date('F');
    $months_till_last = [];

    foreach ($fee_month as $month) {
        $month_clean = trim(ucwords($month)); 
        if ($month_clean == $currentMonth) {
            break;
        }
        $months_till_last[] = $month_clean;

        $x = get_data('student_fee', $student_id, remove_space($month_clean), 'student_id')['data'];
        if ($x == '' || $x == null) {
            $ct++;
            $list[] = $month_clean;
        }
    }

    // Determine first due month (fdm)
    if (isset($list[0]) && $list[0] == 'Previous Dues') {
        $fdm_index = 1; // skip Previous Dues
    } else {
        $fdm_index = 0;
    }

    $fdm = isset($list[$fdm_index]) ? $list[$fdm_index] : null;

    // Set start_date based on fdm
    $currentYear = date('Y');
    if ($fdm) {
        $start_date = new DateTime("$fdm 1, $currentYear");
        // If fdm month is later than current month, assume previous year
        if (date('n') < date('n', strtotime($fdm))) {
            $start_date = new DateTime("$fdm 1, " . ($currentYear - 1));
        }
    } else {
        $start_date = new DateTime("first day of this month"); // fallback
    }

    $res['count'] = $ct;
    $res['list'] = $list;
    $res['months_till_last'] = $months_till_last;
    $res['fdm'] = $fdm;
    $res['start_date'] = $start_date->format('Y-m-d');

    return $res;
}


function predues($student_id)
{
    $total = get_data('student_fee', $student_id, 'base','student_id')['data'];
    
    $marr = preduesmonths($student_id)['list'];;
    foreach($marr as $mnth)
    {
    $res = monthly_fee($student_id, remove_space($mnth));
    $total= $total +$res['fee']['total'];
    }
    
    return $total;
}


function get_final_subject_grades($student_id, $student_class) {

    $subjects = subject_list($student_class);
    $final_grades = [];

    // Merge subjects (same logic as your report card)
    $merged_subjects = [];

    if (in_array($student_class, ['I','II','III','IV','V','VI','VII','VIII'])) {

        $english_done = $hindi_done = $urdu_done = false;

        foreach ($subjects as $sub) {

            $sub_id = is_array($sub) ? $sub['id'] : $sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];

            if (!$english_done && stripos($sub_name,'English') !== false) {
                $merged_subjects[] = ['id'=>'english','subject_name'=>'English'];
                $english_done = true;

            } elseif (!$hindi_done && stripos($sub_name,'Hindi') !== false) {
                $merged_subjects[] = ['id'=>'hindi','subject_name'=>'Hindi'];
                $hindi_done = true;

            } elseif (!$urdu_done && stripos($sub_name,'Urdu') !== false) {
                $merged_subjects[] = ['id'=>'urdu','subject_name'=>'Sanskrit / Urdu'];
                $urdu_done = true;

            } elseif (
                stripos($sub_name,'English')===false &&
                stripos($sub_name,'Hindi')===false &&
                stripos($sub_name,'Urdu')===false
            ) {
                $merged_subjects[] = ['id'=>$sub_id,'subject_name'=>$sub_name];
            }
        }

    } else {

        foreach ($subjects as $sub) {
            $sub_id = is_array($sub) ? $sub['id'] : $sub;
            $sub_name = get_data('subject', $sub_id, 'subject_name')['data'];

            $merged_subjects[] = ['id'=>$sub_id,'subject_name'=>$sub_name];
        }
    }

    // LOOP SUBJECTS
    foreach ($merged_subjects as $sub) {

        $sub_id = $sub['id'];
        $subject_name = $sub['subject_name'];

        // -------- TERM 1 --------
        $t1 = direct_sql("
            SELECT * FROM tabulation 
            WHERE student_id='$student_id'
            AND term='TERM 1'
            AND subject IN (
                SELECT id FROM subject 
                WHERE subject_name LIKE '%$subject_name%'
            )
        ")['data'] ?? [];

        $t1_total = 0;

        foreach ($t1 as $row) {
            if ($student_class == 'IX' || $student_class == 'X') {
                $internal = max($row['ut1'],$row['ut2'])/10 + $row['ma'] + $row['portfolio'] + $row['se'];
                $t1_total += $row['mid_term'] + $internal;
            } else {
                $pa = ($row['ga'] + max($row['ut1'],$row['ut2'])) / 5;
                $t1_total += $pa + $row['nb'] + $row['sea'] + $row['mid_term'];
            }
        }

        // -------- TERM 2 --------
        $t2 = direct_sql("
            SELECT * FROM tabulation 
            WHERE student_id='$student_id'
            AND term='TERM 2'
            AND subject IN (
                SELECT id FROM subject 
                WHERE subject_name LIKE '%$subject_name%'
            )
        ")['data'] ?? [];

        $t2_total = 0;

        foreach ($t2 as $row) {
            if ($student_class == 'IX' || $student_class == 'X') {
                $internal = max($row['ut3'],$row['ut4'])/16 + $row['ma'] + $row['portfolio'] + $row['se'];
                $t2_total += $row['mid_term'] + $internal;
            } else {
                $pa = ($row['ga'] + max($row['ut3'],$row['ut4'])) / 5;
                $t2_total += $pa + $row['nb'] + $row['sea'] + $row['mid_term'];
            }
        }

        // -------- FINAL --------
        $final = ($t1_total + $t2_total) / 2;

        // -------- GRADE --------
        $grade = grade($final);

        $final_grades[$subject_name] = $grade;
    }

    return $final_grades;
}

function distribute_student($class = 'II', $sections = ['A','B','C']) {
    global $con;
    global $branch_id;
    
    // 1. Fetch students (class-wise)
    $sql = "SELECT id, student_name FROM student 
            WHERE student_class = '$class'  and branch_id ='$branch_id' and status ='ACTIVE'
            ORDER BY TRIM(student_name) ASC";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        die("Query Failed: " . mysqli_error($con));
    }

    $students = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $students[] = $row;
    }

    // 2. Prepare counters
    $sectionCount = count($sections);
    $rollCounters = array_fill_keys($sections, 1);

    // 3. Loop + Assign + Update
    foreach ($students as $index => $student) {

        $section = $sections[$index % $sectionCount];
        $roll = $rollCounters[$section];

        // Update query
        $update = "UPDATE student 
                   SET student_section = '$section', 
                       student_roll = '$roll' 
                   WHERE id = '".$student['id']."'";

        if (!mysqli_query($con, $update)) {
            echo "Error updating ID ".$student['id']." : ".mysqli_error($con)."<br>";
        }

        $rollCounters[$section]++;
    }

    echo "✅ Distribution & Roll Update Completed";
}

function generate_tc_no() {
    global $session_list, $db_name;

    $session = substr($session_list[$db_name], 2);

    $res = direct_sql("
        SELECT tc_no 
        FROM tbl_tc 
        WHERE status = 'ACTIVE' 
        AND student_name != '' 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $last_number = 0;

    if (!empty($res['data'][0]['tc_no'])) {
        $parts = explode('/', $res['data'][0]['tc_no']);
        $last_number = (int) end($parts);
    }
    $new_number = $last_number + 1;
    $formatted_number = str_pad($new_number, 4, '0', STR_PAD_LEFT);

    return "RMPS/TC-$session/$formatted_number";
}

function numberToWords($num) {
    $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
        17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
    ];

    $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty',
        5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy',
        8 => 'Eighty', 9 => 'Ninety'
    ];

    if ($num < 20) return $ones[$num];
    if ($num < 100) {
        return $tens[floor($num / 10)] . ' ' . $ones[$num % 10];
    }
    if ($num < 1000) {
        return $ones[floor($num / 100)] . ' Hundred ' . numberToWords($num % 100);
    }
    if ($num < 100000) {
        return numberToWords(floor($num / 1000)) . ' Thousand ' . numberToWords($num % 1000);
    }
    return '';
}

function dayToOrdinal($day) {
    $ordinals = [
        1=>'First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth',
        'Eleventh','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth',
        'Eighteenth','Nineteenth','Twentieth','Twenty First','Twenty Second','Twenty Third',
        'Twenty Fourth','Twenty Fifth','Twenty Sixth','Twenty Seventh','Twenty Eighth',
        'Twenty Ninth','Thirtieth','Thirty First'
    ];
    return $ordinals[$day];
}

function dob_to_words($date) {
    $timestamp = strtotime($date);

    $day = (int)date('d', $timestamp);
    $month = date('F', $timestamp);
    $year = (int)date('Y', $timestamp);

    $dayWord = dayToOrdinal($day);
    $yearWord = numberToWords($year);

    return "$dayWord $month $yearWord";
}

function get_last_paid_month($sid) {
    global $month_list;

    $sf = get_data('student_fee', $sid, null, 'student_id');

    if ($sf['count'] == 0 || empty($sf['data'])) {
        return null;
    }

    $data = $sf['data'];
    $last_paid = null;

    foreach ($month_list as $month) {
        if ($month == 'Admission_month') continue;
        $key = strtolower($month);
        if (isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null) {
            $last_paid = $month;
        }
    }
    if (!$last_paid) return null;
    $base_year = !empty($data['updated_at']) 
        ? date('Y', strtotime($data['updated_at'])) 
        : date('Y');
    $jan_to_mar = ['January','February','March'];

    if (in_array($last_paid, $jan_to_mar)) {
        $year = $base_year + 1;
    } else {
        $year = $base_year;
    }

    return [
        'month' => $last_paid,
        'year'  => $year
    ];
}


function get_teacher_employee() {
    global $con;
    $user_name = $_SESSION['user_name'] ?? '';
    if (empty($user_name)) return null;
    $user_name_esc = mysqli_real_escape_string($con, $user_name);
    $sql = "SELECT * FROM employee WHERE LOWER(emp_code) = LOWER('$user_name_esc') AND status='ACTIVE' LIMIT 1";
    $res = mysqli_query($con, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}



function get_pt_max_mark($student_class, $subject_id, $col_name, $branch_id) {
    global $con;
    
    $class_esc = mysqli_real_escape_string($con, $student_class);
    $sub_id = (int)$subject_id;
    $branch_esc = mysqli_real_escape_string($con, $branch_id);
    
    // First, try to get specific subject config
    $sql = "SELECT * FROM pt_max_marks WHERE class_category = '$class_esc' AND subject_id = $sub_id AND status = 'ACTIVE' AND (branch_id = '$branch_esc' OR branch_id = '' OR branch_id IS NULL) ORDER BY branch_id DESC LIMIT 1";
    $res = direct_sql($sql);
    
    if ($res['count'] == 0) {
        // Next, try to get default (All Subjects) config for that class and subject type (Main or Additional)
        // Determine subject type
        $sub_data = get_data('subject', $sub_id, 'is_extra')['data'] ?? 'NO';
        $sub_type = ($sub_data == 'YES') ? 'Additional Subjects' : 'Main Subjects';
        
        $sql = "SELECT * FROM pt_max_marks WHERE class_category = '$class_esc' AND (subject_type = '$sub_type' OR subject_type = 'Alternate/Additional') AND (subject_id IS NULL OR subject_id = 0) AND status = 'ACTIVE' AND (branch_id = '$branch_esc' OR branch_id = '' OR branch_id IS NULL) ORDER BY branch_id DESC LIMIT 1";
        $res = direct_sql($sql);
    }
    
    if ($res['count'] > 0) {
        $row = $res['data'][0];
        // Determine which column max to return
        $sub_data = get_data('subject', $sub_id, 'is_extra')['data'] ?? 'NO';
        $val = '-';
        if ($sub_data == 'YES') {
            if ($col_name == 'sea') {
                $val = $row['sea_add_max'];
            } elseif ($col_name == 'pt') {
                $val = $row['pt_add_max'];
            }
        } else {
            if ($col_name == 'sea') {
                $val = $row['sea_max'];
            } elseif ($col_name == 'ma') {
                $val = $row['ma_max'];
            } elseif ($col_name == 'pt') {
                $val = $row['pt_max'];
            }
        }
        
        if ($val != '-' && trim((string)$val) != '') {
            return $val;
        }
    }
    
    // Default fallback if no configuration is found in the db or if db value is '-'
    if ($col_name == 'sea') {
        return 10;
    } elseif ($col_name == 'ma') {
        return 10;
    } elseif ($col_name == 'pt') {
        return 30;
    }
    
    return '-';
}




function calculate_student_attendance($student_id, $exam_id, $student_class, $branch_id) {
    static $open_days_cache = [];
    $cache_key = "{$exam_id}_{$student_class}_{$branch_id}";

    if (!isset($open_days_cache[$cache_key])) {
        // 1. Get Exam Start Date (1st date of selected exam)
        $exam_start_date_res = direct_sql("SELECT MIN(exam_date) as start_date FROM admit_card WHERE exam_id = '$exam_id' AND student_class = '$student_class' AND branch_id = '$branch_id' AND exam_date != '0000-00-00'");
        $exam_start_date = $exam_start_date_res['data'][0]['start_date'] ?? null;

        if (!$exam_start_date) {
            $exam_start_date_res = direct_sql("SELECT MIN(exam_date) as start_date FROM admit_card WHERE exam_id = '$exam_id' AND student_class = '$student_class' AND exam_date != '0000-00-00'");
            $exam_start_date = $exam_start_date_res['data'][0]['start_date'] ?? null;
        }
        if (!$exam_start_date) {
            $exam_start_date_res = direct_sql("SELECT MIN(exam_date) as start_date FROM admit_card WHERE exam_id = '$exam_id' AND exam_date != '0000-00-00'");
            $exam_start_date = $exam_start_date_res['data'][0]['start_date'] ?? null;
        }
        if (!$exam_start_date) {
            $exam_list_res = direct_sql("SELECT start_date FROM exam_list WHERE id = '$exam_id' AND start_date != '0000-00-00' AND start_date IS NOT NULL");
            $exam_start_date = $exam_list_res['data'][0]['start_date'] ?? null;
        }

        if (!$exam_start_date) {
            $open_days_cache[$cache_key] = false;
        } else {
            // 2. Determine session start date (April 1st)
            $exam_timestamp = strtotime($exam_start_date);
            $exam_year = (int)date('Y', $exam_timestamp);
            $exam_month = (int)date('n', $exam_timestamp);

            // If exam is Jan-March, session started previous year
            $session_start_year = ($exam_month <= 3) ? $exam_year - 1 : $exam_year;
            $start_date = "$session_start_year-04-01";

            // 3. Fetch Holidays between start date and exam start date
            $holidays = [];
            $h_res = direct_sql("SELECT holiday_date FROM holiday WHERE status='ACTIVE' AND (branch_id='$branch_id' OR branch_id='0' OR branch_id IS NULL) AND holiday_date BETWEEN '$start_date' AND '$exam_start_date'");
            if (isset($h_res['data'])) {
                foreach ($h_res['data'] as $h) {
                    $holidays[] = $h['holiday_date'];
                }
            }

            $open_days = [];
            $current_ts = strtotime($start_date);
            $end_ts = $exam_timestamp;

            // 4. Calculate day by day open days
            while ($current_ts <= $end_ts) {
                $date_str = date('Y-m-d', $current_ts);
                $day_of_week = date('w', $current_ts); // 0 is Sunday

                if ($day_of_week != 0 && !in_array($date_str, $holidays)) {
                    $open_days[] = [
                        'month_key' => strtolower(date('M_Y', $current_ts)),
                        'day_num' => date('j', $current_ts)
                    ];
                }
                $current_ts = strtotime('+1 day', $current_ts);
            }

            $open_days_cache[$cache_key] = [
                'exam_start_date' => $exam_start_date,
                'total_open_days' => count($open_days),
                'open_days' => $open_days
            ];
        }
    }

    if ($open_days_cache[$cache_key] === false) {
        return ['present_days' => 0, 'total_open_days' => 0];
    }

    $cached = $open_days_cache[$cache_key];
    $total_open_days = $cached['total_open_days'];
    $open_days = $cached['open_days'];

    // 5. Fetch Student Attendance Records
    $att_res = direct_sql("SELECT * FROM student_att WHERE student_id = '$student_id' AND (branch_id = '$branch_id' OR branch_id = '0' OR branch_id IS NULL)");
    if (empty($att_res['data'])) {
        $att_res = direct_sql("SELECT * FROM student_att WHERE student_id = '$student_id'");
    }

    $student_attendance = [];
    if (isset($att_res['data'])) {
        foreach ($att_res['data'] as $row) {
            $student_attendance[$row['att_month']] = $row;
        }
    }

    $present_days = 0;
    foreach ($open_days as $od) {
        $m_k = $od['month_key'];
        $d_n = $od['day_num'];

        if (isset($student_attendance[$m_k])) {
            $status = strtoupper($student_attendance[$m_k]['d_' . $d_n] ?? '');
            if ($status == 'P') {
                $present_days++;
            }
        }
    }

    return [
        'present_days' => $present_days,
        'total_open_days' => $total_open_days
    ];
}

?>