<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Access-Control-Allow-Methods: GET, POST"); //, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once('function.php');
$_POST = post_clean($_POST);
$_GET = post_clean($_GET);

if (isset($_GET['task'])) {
	$task = xss_clean($_GET['task']);
	$user_id = $_SESSION['user_id'];
	if ($_SESSION['user_name'] == 'guest') {
		if ($task == 'verify_login' or $task == 'logout') {

		} else {
			$res['msg'] = 'Sorry ! Dont Have permission';
			$res['status'] = 'error';
			echo json_encode($res);
			exit();
		}
	}

	switch ($task) {


		case "change_session":
			if (isset($_POST['session_year']) and $_POST['session_year'] != '') {

				$_SESSION['db_name'] = $_SESSION['db_user'] = $_POST['session_year'];
				$db_name = $db_user = $_POST['session_year'];
				$db_password = '@User2001';
				$con = mysqli_connect($host_name, $db_user, $db_password, $db_name) or die("Unable to Connect, Check the Connection Parameter. " . mysqli_error($con));
				$res = update_data($db_name . '.user', array('token' => $token), $user_id);
			}
			echo "<script> window.location='../dashboard' </script>";
			break;

		case "verify_login": // Delete Any Data From Table
			extract($_POST);
			$res = fetch_all('user', '*', array('user_name' => $user_name, 'user_pass' => md5($user_pass)));
			if ($res['count'] == 1) {
				if (!isset($_SESSION['initiated'])) {
					session_regenerate_id();
					$_SESSION['initiated'] = TRUE;
					$_SESSION['bine_token'] = md5(uniqid(rand(), TRUE));
					$_SESSION['token_time'] = time();
					$_SESSION['user_agent'] = 'bine_' . $_SERVER['HTTP_USER_AGENT'];
				}
				$_SESSION['user_id'] = $user_id = $res['data'][0]['id'];
				$_SESSION['user_name'] = $res['data'][0]['user_name'];
				$_SESSION['user_type'] = $res['data'][0]['user_type'];
				$_SESSION['branch_id'] = $res['data'][0]['branch_id'] ?? 1;
				update_data('user', array('token' => $token, 'status' => 'ACTIVE'), $user_id);
				$res['url'] = 'dashboard';
				//setcookie('bine_token', $token, time() + 600, '/', $inst_url);
				//void session_set_cookie_params ( int $lifetime [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]] )
				//session_set_cookie_params( 60, '/' , $inst_name , true , true );
				session_regenerate_id();
			} else {
				$res['url'] = 'login';
			}
			echo json_encode($res);
			break;

		case "login_as": // Login to Another Account
			extract($_POST);
			$_SESSION['prev_user_id'] = $_SESSION['user_id'];
			$res = fetch_all('user', '*', array('user_name' => $user_name, 'user_pass' => $user_pass));

			if ($res['count'] == 1) {
				if (!isset($_SESSION['initiated'])) {
					session_regenerate_id();
					$_SESSION['initiated'] = TRUE;
					$_SESSION['bine_token'] = md5(uniqid(rand(), TRUE));
					$_SESSION['token_time'] = time();
					$_SESSION['user_agent'] = 'bine_' . $_SERVER['HTTP_USER_AGENT'];
				}
				$_SESSION['user_id'] = $user_id = $res['data'][0]['id'];
				$_SESSION['user_name'] = $res['data'][0]['user_name'];
				$_SESSION['user_type'] = $res['data'][0]['user_type'];
				$_SESSION['branch_id'] = $res['data'][0]['branch_id'] ?? 1;
				update_data('user', array('token' => $token, 'status' => 'ACTIVE'), $user_id);
				$res['url'] = 'dashboard';
				session_regenerate_id();
			} else {
				$res['url'] = 'login';
			}
			echo json_encode($res);
			break;

		case "change_password": // Change Password of Logged in User
			$current_pass = md5($_POST['current_password']);
			$new_password = md5($_POST['new_password']);
			$where = array('id' => $user_id, 'user_pass' => $current_pass);
			$res = update_multi_data('user', array('user_pass' => $new_password), $where);
			echo json_encode($res);
			break;

		case "close_invoice":
			// print_r($_POST);
			extract($_POST);
			// $total = $_POST['total'];
			if ($total <= 0) {
				$res['msg'] = "Total must be greater than 0";
				$res['status'] = "error";
			} else {
				$_POST['status'] = "CLOSED";
				$res = update_data("invoice", $_POST, $id);
				if ($res['status'] == "success") {
					$link = encode("student_id=$student_id&inv_id=" . $_SESSION['inv_id']);
					unset($_SESSION['inv_id']);
					unset($_SESSION['class']);
					unset($_SESSION['section']);
					unset($_SESSION['inv_no']);
					unset($_SESSION['txn_date']);
					unset($_SESSION['student_id']);
					$res['msg'] = "Successfully Invoice is Closed";
					$res['url'] = "distribute_item";
					$pre_dues = get_data('invoice', $student_id, 'dues')['data'];
					$cur_dues = $pre_dues + $dues;
					update_data('student', ['item_dues' => $cur_dues], $student_id)['data'];
				}

				$res['url'] = "distribute_item_receipt?link=$link";
			}
			echo json_encode($res);
			break;
		case "cancel_invoice_receipt":
			$stu_id = $_POST['student_id'];
			$inv_id = $_POST['inv_id'];
			$post['status'] = 'CANCELLED';
			$res = update_multi_data('distribute_item', $post, array('student_id' => $stu_id, 'inv_id' => $inv_id));
			if ($res['status'] == 'error') {
				$_SESSION['msg'] = "Sorry! Something went wrong";
			} else {
				$_SESSION['msg'] = "Successfully! Invoice is cancelled";
				update_data('invoice', $post, $inv_id);
				$get_item = get_all('distribute_item', '*', array('student_id' => $stu_id, 'inv_id' => $inv_id));
				if ($get_item['count'] > 0) {
					foreach ($get_item['data'] as $item) {
						$item_id = $item['item_id'];
						$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
						$distribute_stock = $item['qty'];
						$update_stock['current_stock'] = $cur_stock + $distribute_stock;
						update_data('inventory_item', $update_stock, $item_id);
					}
				}
				$res['msg'] = ($res['status'] == 'success') ? 'Invoice Cancelled Successfully' : $res['msg'];
				$res['url'] = "manage_distribute_item";
			}
			echo json_encode($res);
			break;

		case "master_delete": // Delete Any Data From Table 
			extract($_POST);
			if ($_SESSION['user_type'] == 'ADMIN' or $_SESSION['user_type'] == 'DEV') {
				$searchdata = get_data($table, $id);
				if ($searchdata['count'] > 0) {
					if ($table == 'employee') {
						$emp_code = strtolower($searchdata['data']['emp_code'] ?? '');
						if ($emp_code) {
							delete_data('user', $emp_code, 'user_name');
						}
					}
					if ($table == 'ut_tests') {
						$ut_number = $searchdata['data']['ut_number'] ?? '';
						$branch_id = $searchdata['data']['branch_id'] ?? '';
						if ($ut_number !== '' && $branch_id !== '') {
							$del_all_q = "DELETE FROM ut_tests WHERE ut_number = '" . mysqli_real_escape_string($con, $ut_number) . "' AND branch_id = '" . mysqli_real_escape_string($con, $branch_id) . "'";
							direct_sql($del_all_q, 'set');
						}
					}
					$res = delete_data($table, $id, $pkey);
				}
			} else if ($table == 'distribute_item') {
				$searchdata = get_data($table, $id);
				if ($searchdata['count'] > 0) {

					$res = delete_data($table, $id, $pkey);
				}
			} else {
				$res = array('msg' => "Don't  have permission", 'status' => 'error');
			}
			echo json_encode($res);
			break;

		case "restore": // Delete Any Data From Table 
			extract($_POST);
			if ($_SESSION['user_type'] == 'ADMIN' or $_SESSION['user_type'] == 'DEV') {
				$rdata = get_data('rbin', $id);
				if ($rdata['count'] > 0) {
					$arrdata = json_decode($rdata['data']);

					$idata = insert_data($arrdata['table_name'], $arrdata['deleted_name']);
					if ($idata['id'] != 0) {
						$res = update_data('rbin', $id, array('status' => 'restored'));
					}
				}
			} else {
				$res = array('msg' => "Don't  have permission", 'status' => 'error');
			}
			echo json_encode($res);
			break;

		case "add_template": // To add Template
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = insert_data('sms_template', $_POST);
			echo json_encode($res);
			break;
		case "add_category":
			extract($_POST);
			$cat_name_upper = $_POST['cat_name'];
			unset($_POST['cat_name']);
			$_POST['cat_name'] = strtoupper($cat_name_upper);
			$get_category = get_all("emp_cat", '*', array("cat_name" => $cat_name));
			if ($get_category['count'] > 0) {
				$res['msg'] = "Category Already Exist,Please Create New Category";
				$res['status'] = "error";
			} else {
				$res = update_data('emp_cat', $_POST, $_POST['id']);
				$res['url'] = $_SERVER['HTTP_REFERER'];
			}
			echo json_encode($res);
			break;
		case "send_sms": // SMS any TIME
			extract($_POST);
			//  	$templateid = get_data('sms_template',$template_id, 'template_id')['data'];
			//  	$ctype = get_data('sms_template',$template_id, 'content_type')['data'];

			$mobile_arr = explode(",", $mobile);
			foreach ($mobile_arr as $mob) {
				send_sms($mob, $sms); // for Whatsapp
			}

			$res['msg'] = 'WhatsApp Sechduled Successfully';
			$res['status'] = 'success';
			$res['url'] = 'send_sms';

			echo json_encode($res);
			break;

		case "group_sms": // SMS SMS any TIME
			//print_r($_POST);
			$template_id = $_POST['template_id'];
			$templateid = get_data('sms_template', $template_id, 'template_id')['data'];
			$ctype = get_data('sms_template', $template_id, 'content_type')['data'];
			$msg_no = $_POST['msg_no'];
			$section = $_POST['section'];
			$sms = $_POST['msg_text'];
			$unicode = $_POST['unicode'];

			if ($section != '') {
				$mobile = get_all('student', array('student_whatsapp'), array('status' => 'ACTIVE', 'student_class' => $msg_no, 'student_section' => $section))['data'];
			} else if ($section == '') {
				$mobile = get_all('student', array('student_whatsapp'), array('status' => 'ACTIVE', 'student_class' => $msg_no))['data'];
			} else {
				// 	if($msg_no =='ALL_STUDENT')
				// 	{
				// 		$mobile =get_all('student',array('student_whatsapp'),array('status'=>'ACTIVE'))['data'];
				// 	}
				if ($msg_no == 'ALL_STAFF') {
					$mobile = get_all('employee', array('e_mobile'), array('status' => 'ACTIVE'))['data'];
				} else {
					$mobile = get_all('student', array('student_whatsapp'), array('student_class' => $msg_no))['data'];
				}
			}
			$i = 1;
			foreach ($mobile as $mob) {
				send_sms($mob['student_whatsapp'], $sms); // for Whatsapp

				$res['msg'] = $i . 'WhatsApp Sechduled Successfully';
				$res['status'] = 'success';
				// $res['url'] ='send_sms';
				$i++;
			}

			echo json_encode($res);
			break;


		case "product_info": // ITEM INFO 18 AUG
		case "get_item": // ITEM INFO 18 AUG
			extract($_POST);
			$res = get_data('inventory_item', $product_id)['data'];
			echo json_encode($res);
			break;

		case "update_item":
			extract($_POST);
			unset($_POST['isedit']);
			if ($isedit == 'no') {
				$_POST['current_stock'] = $_POST['opening_stock'];
			}
			$res = update_data('inventory_item', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'manage_item';
			}
			echo json_encode($res);
			break;

		case "purchase_item":
			extract($_POST);

			$old_stock = get_data('inventory_item', $name, 'current_stock')['data'];
			$new_stock = $old_stock + $quantity;

			$res = update_data('purchase_item', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'purchase_item';
			}
			update_data('inventory_item', ['current_stock' => $new_stock], $name)['data'];
			echo json_encode($res);
			break;

		case "distribute_item":
			extract($_POST);

			$pre_dues = get_data('invoice', $student_id, 'dues')['data'];
			$cur_dues = $pre_dues + $dues;

			$res = update_data('student', $_POST, $_POST['student_id']);
			if ($res['status'] == "success") {
				$res['url'] = 'distribute_item';
			}

			break;

		case "update_item_cat":
			$res = update_data('item_cat', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'add_item';
			}
			echo json_encode($res);
			$notice = "Account Head Added by {$_SESSION['user_name']}";
			break;

		case "update_vendor":
			extract($_POST);
			unset($_POST['isedit']);
			$res = update_data('vendor', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'manage_vendor';
			}
			echo json_encode($res);
			break;

		case "update_vendor_cat":
			$res = update_data('vendor_cat', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'add_vendor';
			}
			echo json_encode($res);
			break;

		case "check_qty":
			extract($_POST);
			$aq = get_data('inventory_item', $item_id, 'current_stock')['data'];
			$rate = get_data('inventory_item', $item_id, 'rate')['data'];
			if ($aq >= $qty) {
				$res['qty'] = $q;
				$res['rate'] = $rate;
				$res['status'] = "success";
				$res['msg'] = "Available in stock";
			} else {
				$res['status'] = "error";
				$res['msg'] = "Sorry only $aq quantity available";
			}
			echo json_encode($res);
			break;

		case "get_item_dues":
			extract($_POST);
			$dues = get_data('student', $student_id, 'item_dues');
			echo json_encode($dues);
			break;

		case "update_distribute_item":
			extract($_POST);
			$inv_no = ($_POST['inv_no'] == '') ? date('ymd' . $inv_id) : $_POST['inv_no'];
			$_SESSION['class'] = $student_class;
			$_SESSION['section'] = $student_section;
			$_SESSION['inv_no'] = $inv_no;
			$_SESSION['inv_id'] = $inv_id;
			$_SESSION['txn_date'] = $date;
			$_SESSION['student_id'] = $student_id;
			unset($_POST['student_class']);
			unset($_POST['student_section']);
			$_POST['status'] = "ACTIVE";
			$res = update_data('distribute_item', $_POST, $id);
			if ($res['status'] == 'success') {
				$res['url'] = $_SERVER['HTTP_REFERER'];
				$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
				$rem_stock['current_stock'] = $cur_stock - $qty;
				update_data('inventory_item', $rem_stock, $item_id);
			}
			echo json_encode($res);
			break;


		case "add_category":
			extract($_POST);
			$cat_name_upper = $_POST['cat_name'];
			unset($_POST['cat_name']);
			$_POST['cat_name'] = strtoupper($cat_name_upper);
			$get_category = get_all("emp_cat", '*', array("cat_name" => $cat_name));
			if ($get_category['count'] > 0) {
				$res['msg'] = "Category Already Exist,Please Create New Category";
				$res['status'] = "error";
			} else {
				$res = update_data('emp_cat', $_POST, $_POST['id']);
				$res['url'] = $_SERVER['HTTP_REFERER'];
			}
			echo json_encode($res);
			break;

		case "close_invoice":
			// print_r($_POST);
			extract($_POST);
			// $total = $_POST['total'];
			if ($total <= 0) {
				$res['msg'] = "Total must be greater than 0";
				$res['status'] = "error";
			} else {
				$_POST['status'] = "CLOSED";
				$res = update_data("invoice", $_POST, $id);
				if ($res['status'] == "success") {
					unset($_SESSION['inv_id']);
					unset($_SESSION['class']);
					unset($_SESSION['section']);
					unset($_SESSION['inv_no']);
					unset($_SESSION['txn_date']);
					unset($_SESSION['student_id']);
					$res['msg'] = "Successfully Invoice is Closed";
					$res['url'] = "distribute_item";
					$pre_dues = get_data('invoice', $student_id, 'dues')['data'];
					$cur_dues = $pre_dues + $dues;
					$dres = update_data('student', ['item_dues' => $cur_dues], $student_id);
					if ($dres['status'] == 'success') {
						$link = encode("student_id=$student_id&inv_id={$_SESSION['inv_id']}");
					}
				}
			}
			$res['url'] = "distribute_item_receipt?link=$link";
			echo json_encode($res);
			break;

		case "cancel_invoice_receipt":
			$stu_id = $_POST['student_id'];
			$inv_id = $_POST['inv_id'];
			$post['status'] = 'CANCELLED';
			$res = update_multi_data('distribute_item', $post, array('student_id' => $stu_id, 'inv_id' => $inv_id));
			if ($res['status'] == 'error') {
				$_SESSION['msg'] = "Sorry! Something went wrong";
			} else {
				$_SESSION['msg'] = "Successfully! Invoice is cancelled";
				update_data('invoice', $post, $inv_id);
				$get_item = get_all('distribute_item', '*', array('student_id' => $stu_id, 'inv_id' => $inv_id));
				if ($get_item['count'] > 0) {
					foreach ($get_item['data'] as $item) {
						$item_id = $item['item_id'];
						$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
						$distribute_stock = $item['qty'];
						$update_stock['current_stock'] = $cur_stock + $distribute_stock;
						update_data('inventory_item', $update_stock, $item_id);
					}
				}
				$res['url'] = "manage_distribute_item";
			}
			echo json_encode($res);
			break;

		case "cancel_entry_receipt":
			$id = $_POST['id'];
			$post['status'] = 'CANCELLED';
			$res = update_multi_data('purchase_item', $post, array('id' => $id));
			if ($res['status'] == 'error') {
				$_SESSION['msg'] = "Sorry! Something went wrong";
			} else {
				$_SESSION['msg'] = "Successfully! Entry is cancelled";
				update_data('purchase_item', $post, $id);
				$get_item = get_all('purchase_item', '*', array('id' => $id));
				if ($get_item['count'] > 0) {
					foreach ($get_item['data'] as $item) {
						$item_id = $item['name'];
						$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
						$distribute_stock = $item['quantity'];
						$update_stock['current_stock'] = $cur_stock - $distribute_stock;
						update_data('inventory_item', $update_stock, $item_id);
					}
				}
				$res['url'] = "purchase_report";
			}
			echo json_encode($res);
			break;
		case "get_student":
			extract($_POST);
			$res = get_all('student', '*', array('student_class' => $stu_class, 'student_section' => $stu_section));
			$stu_list = "<option value=''>--Select Student--</option>";
			if ($res['count'] > 0) {
				foreach ($res['data'] as $row) {
					$id = $row['id'];
					$stu_list .= "<option value='$id'>" . $row['student_name'] . " (" . $row['student_admission'] . ") </option>";
				}
			}
			echo $stu_list;
			break;
		case "bulk_wa":
			$class = $_POST['student_class'];
			$mobiles = get_student_mobile($class);

			$sms = $_POST['msg_text'];
			$title = $_POST['title'];
			foreach ($mobiles as $mob) {
				$ires = insert_data('wa_sms', array('status' => 'PENDING', 'mobile' => $mob, 'msg' => $sms, 'title' => $title));
			}
			echo "<script> window.location ='" . $_SERVER['HTTP_REFERER'] . "' </script>";
			break;


		case "master_block": // BLOCK Any Data From Table 
			extract($_POST);
			//print_r($_POST);
			if ($table == 'employee') {
				$searchdata = get_data($table, $id, null, $pkey);
				if ($searchdata['count'] > 0) {
					$emp_code = strtolower($searchdata['data']['emp_code'] ?? '');
					if ($emp_code) {
						update_data('user', array('status' => 'BLOCK'), $emp_code, 'user_name');
					}
				}
			}
			$bdata = array('status' => 'BLOCK');
			$res = update_data($table, $bdata, $id, $pkey);
			echo json_encode($res);
			break;

		case "block_user": // BLOCK Any Data From Table 
			extract($_POST);
			//print_r($_POST);
			$bdata = array('status' => $data_status);
			$res2 = update_data('center_details', $bdata, $id, 'center_code');
			$res = update_data('user', $bdata, $id, 'user_name');
			$res['msg'] = 'User and Center ' . $data_status . ' Successfully';
			$res['url'] = 'show_user';
			echo json_encode($res);
			break;

		// 			case "update_status" : // Update Status Data From Table 
		// 				extract($_POST);
		// 				$st = $_POST['data_status'];
		// 				$sid =$_POST['sid'];
		// 				$bdata =array('status'=>$st);
		// 				foreach($sid as $id)
		// 				{
		// 					if($st=='DELETE')
		// 					{
		// 						$res = delete_data('student',$id,'student_id');
		// 					}
		// 					else{
		// 						$res = update_data('student',$bdata,$id,'student_id');
		// 						if($st =='VERIFIED')
		// 						{
		// 						$data = get_data('student',$id,null,'student_id')['data'];
		// 						$course =courseinfo($id,'course_code');

		// 						$sms ="Hello ". $data['student_name'] ." Your registration for course ". $course ." in accepted. Now you can check your admission status. \n Regards \n ".$inst_name. $inst_url;
		// 						$default_sms($data['student_mobile'],$sms);
		// 						}
		// 					}

		// 				}
		// 				echo json_encode($res);
		// 				break;


		case "logout":
			$rtype = 'direct';
			extract($_POST);
			if ($_SESSION['bine_token'] != '') {
				$user_id = $_SESSION['user_id'];
				$user_type = $_SESSION['user_type'];
				$result = update_data('user', array('token' => '', 'status' => 'LOGOUT'), $user_id);
				echo json_encode($result);
				if ($result['status'] == 'success') {
					unset($_SESSION['user_name']);
					unset($_SESSION['user_type']);
					unset($_SESSION['user_id']);
					unset($_SESSION['bine_token']);
					session_destroy();
					$url = $base_url . 'login';
				} else {
					$url = $base_url . 'dashboard';
				}
			} else {
				$url = $base_url . 'login';
			}
			if ($rtype == 'AJAX') {
				$result['url'] = $url;
				json_encode($result);
			} else {
				echo "<script> window.location ='{$base_url}login' </script>";
			}
			break;

		case "forget_password":
			$user_name = $_POST['user_name'];
			$sql = "select * from user where user_name ='$user_name' and status not in ('AUTO','DELETED')";
			$res = direct_sql($sql);
			//print_r($res);
			if ($res['count'] > 0) {
				$id = $res['data'][0]['id'];
				$user_type = $res['data'][0]['user_type'];
				$email = $res['data'][0]['user_email'];
				$mobile = $res['data'][0]['user_mobile'];
				$name = $res['data'][0]['full_name'];

				$np = rnd_str(6);
				$up = array('password' => md5($np));
				$res = update_data($user_type, $up, $id, 'id');
				$sms = "Dear " . $name . " Your new password is " . $np . " kindly change after login " . $inst_name;
				rtf_mail($email, "Password Recover of $inst_name ", $sms, $noreply_email);
				//bulk_sms($mobile,$sms);
				$data['id'] = $id;
				$data['status'] = 'success';
				$data['msg'] = "New Password Successfully Send to $email";
			} else {
				$data['id'] = 0;
				$data['status'] = 'error';
				$data['msg'] = 'No any user exist with this ID. Try Again';
			}
			echo json_encode($data);
			break;

		case "get_dist":
			$state_code = $_GET['state_code'];
			$res = fetch_all('district', '*', array('state_code' => $state_code), ['sort' => 'name']);
			foreach ($res['data'] as $data) {
				$id = $data['id'];
				echo "<option value='" . $data['code'] . "'>" . $data['name'] . "</option>";
			}
			break;

		case "get_account":
			$account_type = $_REQUEST['account_type'];
			$res = get_all('account_head', '*', array('account_type' => $account_type, 'status' => 'ACTIVE'));
			print_r($account_type);
			foreach ($res['data'] as $data) {
				$id = $data['id'];
				echo "<option value='" . $data['id'] . "'>" . $data['account_name'] . "</option>";
			}
			break;


		case "upload":
			$result = upload_img('uploadimg', 'rand', '../upload');
			echo json_encode($result);
			break;
		/*============= STANDARD TASK END ===============*/

		case "upload_gatepass":
			$result = upload_doc('uploadgpimg', 'rand', 'gatepass');
			echo json_encode($result);
			break;

		case "upload_doc":
			$result = upload_doc('uploaddoc', 'rand', 'upload');
			echo json_encode($result);
			break;
		/*============= STANDARD TASK END ===============*/

		case "update_student":
			//print_r($_POST);
			extract($_POST);

			$app_data = direct_sql("SELECT * FROM admission WHERE app_no = '{$_POST['app_no']}'")['data']['0'];

			if (isset($app_data['id']) && $app_data['id'] != '') {

				$res2 = update_data('admission', ['adm_status' => 'ADMITTED'], $app_data['id']);
			}
			// print_r($res2);
			unset($_POST['app_no']);
			$res = update_data('student', $_POST, $_POST['id']);
			//$res['url'] ='add_student?link='.encode('student_name='.$student_name.'&id='.$id);

			$stu_new = array('student_id' => $_POST['id'], 'student_admission' => $_POST['student_admission']);
			$findstudent = get_data('student_fee', $id, null, 'student_id');
			//print_r($findstudent);
			if ($findstudent['count'] == 0) {
				if ($base_dues <> 0) {
					$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				}
				$res0 = insert_data('student_fee', $stu_new);
				//print_r($res0);
			} else {
				$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				//update_data('student_fee',$stu_new,$id,'student_id');
			}
			$res['url'] = 'manage_student';
			echo json_encode($res);
			break;


		case "update_fly_student":
			//print_r($_POST);
			extract($_POST);

			$app_data = direct_sql("SELECT * FROM admission WHERE app_no = '{$_POST['app_no']}'")['data']['0'];

			if (isset($app_data['id']) && $app_data['id'] != '') {

				$res2 = update_data('admission', ['adm_status' => 'ADMITTED'], $app_data['id']);
			}
			// print_r($res2);
			unset($_POST['app_no']);
			$res = update_data('student', $_POST, $_POST['id']);

			$stu_new = array('student_id' => $_POST['id'], 'student_admission' => $_POST['student_admission']);
			$findstudent = get_data('student_fee', $id, null, 'student_id');
			if ($findstudent['count'] == 0) {
				if ($base_dues <> 0) {
					$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				}
				$res0 = insert_data('student_fee', $stu_new);
			} else {
				$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
			}
			$res['url'] = 'fly_student';
			echo json_encode($res);
			break;

		case "update_student_photo":
			//print_r($_POST);
			extract($_POST);

			$app_data = direct_sql("SELECT * FROM admission WHERE app_no = '{$_POST['app_no']}'")['data']['0'];

			if (isset($app_data['id']) && $app_data['id'] != '') {

				$res2 = update_data('admission', ['adm_status' => 'ADMITTED'], $app_data['id']);
			}
			// print_r($res2);
			unset($_POST['app_no']);
			$res = update_data('student', $_POST, $_POST['id']);
			//$res['url'] ='add_student?link='.encode('student_name='.$student_name.'&id='.$id);

			$stu_new = array('student_id' => $_POST['id'], 'student_admission' => $_POST['student_admission']);
			$findstudent = get_data('student_fee', $id, null, 'student_id');
			//print_r($findstudent);
			if ($findstudent['count'] == 0) {
				if ($base_dues <> 0) {
					$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				}
				$res0 = insert_data('student_fee', $stu_new);
				//print_r($res0);
			} else {
				$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				//update_data('student_fee',$stu_new,$id,'student_id');
			}
			$res['url'] = 'new_student_update';
			echo json_encode($res);
			break;


		case "update_fee":
			//print_r($_POST);
			extract($_POST);
			$col_name = remove_space($fee_name);
			if (isset($_POST['fee_month'])) {
				$_POST['fee_month'] = implode(',', $_POST['fee_month']);
			}
			if (isset($_POST['student_class'])) {
				$_POST['student_class'] = implode(',', $_POST['student_class']);
			}
			if (isset($_POST['finance_type'])) {
				$_POST['finance_type'] = implode(',', $_POST['finance_type']);
			}
			if (isset($_POST['admission_type'])) {
				$_POST['admission_type'] = implode(',', $_POST['admission_type']);
			}
			if (isset($_POST['student_type'])) {
				$_POST['student_type'] = implode(',', $_POST['student_type']);
			}
			$res = update_data('fee_head', $_POST, $id);
			add_column('receipt', $col_name, 'float(12,2)', ' 0.00');
			if ($fee_type == 'STUDENT') {
				add_column('student_fee', $col_name, 'float(12,2)', ' 0.00');
				if ($fee_type == 'FIXED') {
					direct_sql("update student_fee set $col_name ='$fee_amount'", 'set');
				}
			} else {
				add_column('fee_details', remove_space($fee_name), 'float(12,2)', '0.00');
				if ($fee_type == 'FIXED') {
					direct_sql("update fee_details set $col_name ='$fee_amount'", 'set');
				}
			}
			$res['url'] = 'add_fee';
			echo json_encode($res);
			break;

		case "delete_fee":
			if ($_SESSION['user_type'] == 'ADMIN' or $_SESSION['user_type'] == 'DEV') {
				$id = $_POST['id'];
				$col_name = remove_space(get_data('fee_head', $id, 'fee_name')['data']);
				$fee_type = get_data('fee_head', $id, 'fee_type')['data'];
				if ($fee_type == 'STUDENT') {
					remove_column('student_fee', $col_name);
				} else {
					remove_column('fee_details', $col_name);
				}
				remove_column('receipt', $col_name);
				$res = delete_data('fee_head', $id);
			} else {
				$res = array('msg' => "Don't  have permission", 'status' => 'error');
			}
			echo json_encode($res);
			break;

		case "set_fee_amount":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('fee_details', $_POST, $id);
			$res['url'] = 'update_fee';
			echo json_encode($res);
			break;

		case "get_month_fee":
			extract($_POST);
			$res = monthly_fee($student_id, $month_name);
			$res['url'] = 'update_fee';
			echo json_encode($res);
			break;

		case "nmonth_fee":
			extract($_POST);
			// print_r($_POST);
			$res = nmonth_fee($student_id, $month_list);
			echo json_encode($res);
			break;

		case "online_fee":
			extract($_POST);
			unset($_POST['send_sms']);
			$current_dues = $total - $paid_amount;
			$student = get_data('student', $student_id)['data'];
			$student_name = $student['student_name'];
			$student_mobile = $student['student_mobile'];
			$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
			$res = insert_data('receipt', $_POST);

			if ($res['status'] == 'success') {
				$rid = $res['id'];
				foreach (explode(',', $paid_month) as $month) {
					$old_value = get_data('student_fee', $student_id, $month)['data'];
					if ($old_value != null) {
						$rid = $old_value . "," . $rid;
					}
					$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
				}
			}
			$res['url'] = 'receipt?receipt_id=' . $res['id'];
			if ($send_sms == 'yes') {
				$sms = "Dear $student_name \nRs. $paid_amount received successfully against $paid_month Your current due is $current_dues. \nRegards $inst_name OFFAPP";
				$te_id = '1207166936512475761';
				send_sms($student_mobile, $sms);
			}

			echo json_encode($res);
			break;

		// 		case "pay_fee":
// 			extract($_POST);
// 			unset($_POST['send_sms']);

		// 			if($prev_session_dues >0)
// 			{
// 			  $new_prev_session_dues =  $prev_session_dues -$paid_amount;
// 			  $_POST['current_dues'] = sprintf("%.2f", $new_prev_session_dues);
// 			  //$_POST['previous_dues'] =$prev_session_dues;
// 			  $res = insert_data('receipt', $_POST);
// 			  $old_value = get_data('student_fee', $student_id, 'other_month', 'student_id')['data'];

		//     		  $rid = ($old_value != null ) ? $old_value.$res['id']:$res['id'] ; 

		//     		  $res2 = update_data('student_fee', array('other_month' => $rid, 'prev_session_dues' => $new_prev_session_dues), $student_id, 'student_id');
// 			}
// 			else{
//     			$current_dues = $total - $paid_amount;
//     			$student = get_data('student', $student_id)['data'];
//     			$student_name = $student['student_name'];
//     			$student_mobile = $student['student_mobile'];
//     			$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
//     			$res = insert_data('receipt', $_POST);

		//     			if ($res['status'] == 'success') {
//     				$rid = $res['id'];
//     				foreach (explode(',', $paid_month) as $month) {
//     					$old_value = get_data('student_fee', $student_id, $month, 'student_id')['data'];
//     					if ($old_value != null and $month == 'other_month') {
//     						$rid = $old_value . "," . $rid;
//     					}
//     					$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
//     				}
//     				$res2 = update_data('student', array('allow_discount' => 'NO'), $student_id);
//     			}
//         	}
// 			$receipt_size = get_data('branch_list',$_SESSION['branch_id'],'receipt_size')['data'];
// 			$receipt_page = ($receipt_size==2)?'receipt2':'receipt';
// 			$res['url'] = $receipt_page.'?receipt_id=' . $res['id'];
// 			if ($send_sms == 'yes') {

		// 				$sms = "Dear $student_name \nRs. $paid_amount received successfully against $paid_month Your current due is $current_dues. \nRegards $inst_name OFFAPP";
// 				$te_id = '1207166936512475761';
// 				//send_sms($student_mobile, $sms);
// 			}
// 			echo json_encode($res);
// 			break;


		case "pay_fee":
			if ($_SESSION['user_id'] != '' or $_SESSION['user_id'] != 0) {
				extract($_POST);
				unset($_POST['send_sms']);
				$current_dues = $total - $paid_amount;
				$student = get_data('student', $student_id)['data'];
				$student_name = $student['student_name'];
				$student_mobile = $student['student_mobile'];
				$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
				$res = insert_data('receipt', $_POST);

				if ($res['status'] == 'success') {
					$rid = $res['id'];
					foreach (explode(',', $paid_month) as $month) {
						$old_value = get_data('student_fee', $student_id, $month, 'student_id')['data'];
						if ($old_value != null and $month == 'other_month') {
							$rid = $old_value . "," . $rid;
						}
						$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues, 'prev_session_dues' => '0.00'), $student_id, 'student_id');
					}
					//$res2 = update_data('student', array('allow_discount' => 'NO'), $student_id);
				}
				$res['url'] = 'receipt?receipt_id=' . $res['id'];
				if ($send_sms == 'yes') {

					$sms = "Dear $student_name \nRs. $paid_amount received successfully against $paid_month Your current due is $current_dues. \nRegards $inst_name OFFAPP";
					$te_id = '1207166936512475761';
					//send_sms($student_mobile, $sms);
				}

			} else {
				$res['status'] = 'error';
				$res['msg'] = 'Invalid user';

			}
			echo json_encode($res);
			break;

		// case "pay_fee":

		//   extract($_POST);
		//     unset($_POST['send_sms']);

		//     // 📌 GET STUDENT DETAILS
		//     $student = get_data('student', $student_id)['data'];
		//     $student_name = $student['student_name'];
		//     $student_mobile = $student['student_mobile'];

		//     // 📌 GET EXISTING FEE DATA
		//     $fee = get_data('student_fee', $student_id)['data'];

		//     $prev_session_dues = (float)$fee['prev_session_dues'];
		//     $old_current_dues  = (float)$fee['current_dues'];

		//     $paid_amount = (float)$paid_amount;
		//     $total       = (float)$total;

		//     // 🧠 STEP 1: ADD NEW TOTAL TO CURRENT DUES
		//     $current_dues = $old_current_dues + $total;

		//     // 🧠 STEP 2: APPLY PAYMENT (FIRST PREVIOUS, THEN CURRENT)
		//     $remaining_amount = $paid_amount;

		//     // 🔴 CLEAR PREVIOUS DUES
		//     if ($prev_session_dues > 0) {
		//         if ($remaining_amount >= $prev_session_dues) {
		//             $remaining_amount -= $prev_session_dues;
		//             $new_prev_session_dues = 0;
		//         } else {
		//             $new_prev_session_dues = $prev_session_dues - $remaining_amount;
		//             $remaining_amount = 0;
		//         }
		//     } else {
		//         $new_prev_session_dues = 0;
		//     }

		//     // 🟢 CLEAR CURRENT DUES
		//     if ($remaining_amount > 0) {
		//         if ($remaining_amount >= $current_dues) {
		//             $current_dues = 0;
		//         } else {
		//             $current_dues -= $remaining_amount;
		//         }
		//     }

		//     // 🧾 FORMAT VALUES
		//     $current_dues = sprintf("%.2f", $current_dues);
		//     $new_prev_session_dues = sprintf("%.2f", $new_prev_session_dues);

		//     $_POST['current_dues'] = $current_dues;

		//     // 📄 INSERT RECEIPT
		//     $res = insert_data('receipt', $_POST);

		//     if ($res['status'] == 'success') {

		//         $rid = $res['id'];

		//         // 🔁 UPDATE MONTH-WISE RECEIPT MAPPING
		//         foreach (explode(',', $paid_month) as $month) {

		//             $old_value = get_data('student_fee', $student_id, $month, 'student_id')['data'];

		//             if (!empty($old_value)) {
		//                 $rid_final = $old_value . "," . $rid;
		//             } else {
		//                 $rid_final = $rid;
		//             }

		//             update_data('student_fee', [
		//                 $month => $rid_final
		//             ], $student_id, 'student_id');
		//         }

		//         // 🔄 UPDATE FINAL DUES
		//         update_data('student_fee', [
		//             'current_dues'      => $current_dues,
		//             'prev_session_dues' => $new_prev_session_dues
		//         ], $student_id, 'student_id');

		//         // 🚫 DISABLE DISCOUNT AFTER PAYMENT
		//         update_data('student', [
		//             'allow_discount' => 'NO'
		//         ], $student_id);
		//     }

		//     // 📄 RECEIPT PAGE DECISION
		//     $receipt_size = get_data('branch_list', $_SESSION['branch_id'], 'receipt_size')['data'];
		//     $receipt_page = ($receipt_size == 2) ? 'receipt2' : 'receipt';

		//     $res['url'] = $receipt_page . '?receipt_id=' . $res['id'];

		//     // 📩 OPTIONAL SMS
		//     if ($send_sms == 'yes') {
		//         $sms = "Dear $student_name \nRs. $paid_amount received successfully. Current Due: $current_dues \nRegards $inst_name";
		//         // send_sms($student_mobile, $sms);
		//     }

		//     echo json_encode($res);
		//     break;


		case "cancel_receipt":
			extract($_POST);
			//print_r($_POST);
			$rid = $_POST['receipt_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');
			$i = 1;
			$student_id = get_data('receipt', $rid, 'student_id')['data'];
			$paid_month = get_data('receipt', $rid, 'paid_month')['data'];
			$prev_session_dues = get_data('receipt', $rid, 'prev_session_dues')['data'];

			if ($prev_session_dues > 0) {
				$curr_session_dues = get_data('student_fee', 'prev_session_dues', $student_id, 'student_id')['data'];
				$new_session_dues = $curr_session_dues + $prev_session_dues;
				update_data('student_fee', ['prev_session_dues' => $new_session_dues], $student_id, 'student_id');
			}
			$new_dues = get_data('receipt', $rid, 'previous_dues')['data'];
			$month_list = explode(",", $paid_month);
			foreach ($month_list as $colname) {
				$sql2 = "update student_fee set $colname = null, current_dues ='$new_dues' where student_id ='$student_id'";
				$res = mysqli_query($con, $sql2) or die("Update Student Month Error : " . mysqli_error($con));
				$i = $i + 1;
			}
			if ($i > 1) {
				$cancel_data = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
				$res2 = update_data('receipt', $cancel_data, $rid);
				if ($res2['status'] == 'success') {
					$res2['msg'] = "Receipt No. $rid Cancelled Successfully";
				}
				echo json_encode($res2);
			}
			break;

		case "cancel_acctxn":
			extract($_POST);
			//print_r($_POST);
			$txn_id = $_POST['txn_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');

			$cancel_data = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
			$res = update_data('account_txn', $cancel_data, $txn_id);
			if ($res['status'] == 'success') {
				$res['msg'] = "Txn No. $txn_id Cancelled Successfully";
			}
			echo json_encode($res);
			break;


		case "update_area":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('transport_area', $_POST, $id);
			$res['url'] = 'add_area';
			echo json_encode($res);
			break;

		case "update_trip":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('trip_details', $_POST, $id);
			$res['url'] = 'add_trip';
			echo json_encode($res);
			break;

		case "update_holiday":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('holiday', $_POST, $id);
			echo json_encode($res);
			break;

		case "bulk_import":
			extract($_POST);
			echo "<pre>";
			$res = csv_import($table, $pkey);
			print_r($res);
			//echo "<script> window.location='bulk_import?res=".json_encode($res)."' </script>";
			break;

		case "bulk_export":
			if ($_SESSION['user_type'] == 'ADMIN' or $_SESSION['user_type'] == 'DEV') {
				$status = $_GET['status'];
				csv_export($_REQUEST['table']);
			}
			break;

		case "create_tc":

			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			extract($_POST);
			// 			print_r($_POST);
			$tc_st = direct_sql("SELECT * FROM tbl_tc WHERE student_admission='$student_admission' AND branch_id='{$_SESSION['branch_id']}'");

			if ($tc_st['count'] == 1) {
				$res = update_data('tbl_tc', $_POST, $student_admission, 'student_admission');
			} else {
				$res = insert_data('tbl_tc', $_POST);
			}
			$link = encode('student_admission=' . $_POST['student_admission']);
			// 			print_r($res);
			if ($res['status'] == 'success') {
				$cres = update_data('student', array('status' => 'BLOCK'), $_POST['student_admission'], 'student_admission');
				$url = 'print_tc?link=' . $link;
				// print_r($cres);
				// echo "<script> window.location='$base_url.$url' </script>";
				$res['url'] = $url;
				echo json_encode($res);
			}
			break;


		case "update_user":
			$_POST['allowed_branch'] = implode(",", $_POST['allowed_branch']);
			$_POST['user_name'] = remove_space($_POST['user_name']);
			if (isset($_POST['user_pass']) and $_POST['user_pass'] <> '') {
				$_POST['user_pass'] = md5($_POST['user_pass']);
			} else {
				unset($_POST['user_pass']);
			}
			$res = update_data('user', $_POST, $_POST['id']);
			$res['url'] = 'add_user';
			echo json_encode($res);
			break;


		case "update_subject":
			extract($_POST);
			$sub = remove_space($subject_name);
			$_POST['subject_column'] = $sub;
			$_POST['status'] = 'ACTIVE';
			$_POST['student_class'] = implode(',', $_POST['student_class']);
			$res = update_data('subject', $_POST, $id);

			if ($res['status'] == 'success' and $subject_type == 'Scholastic') {
				foreach ($marks_type_list as $mstype) {
					$col_name = $sub . '_' . remove_space($mstype);
					add_column('exam', $col_name, 'int', '0');
				}
			} else {
				add_column('exam', $sub, 'varchar(25)');
			}
			$res['url'] = 'subject_setting';
			echo json_encode($res);
			break;


		case "delete_subject":
			if ($_SESSION['user_type'] == 'ADMIN' or $_SESSION['user_type'] == 'DEV' or $_SESSION['user_type'] == 'DBA') {
				extract($_POST);
				$sub_id = $_POST['id'];
				$sub = get_data('subject', $sub_id, 'subject_column')['data'];
				$sub_type = get_data('subject', $sub_id, 'subject_type')['data'];
				if ($sub_type == 'Scholastic') {
					foreach ($marks_type_list as $mstype) {
						$col_name = $sub . '_' . remove_space($mstype);
						remove_column('exam', $col_name, 'int', '0');
					}
				} else {
					remove_column('exam', $sub);
				}
				$res = delete_data('subject', $sub_id);
				echo json_encode($res);
			}
			break;

		// 		case "select_subject":
// 			extract($_POST);
// 			$scls = xss_clean($class_name);
// 			$sql = "select * from subject where find_in_set('$scls',student_class) and subject_type='Scholastic'";
// 			$sub = direct_sql($sql);
// 			foreach ($sub['data'] as $sub) {
// 				echo "<option value='" . $sub['id'] . "'>" . $sub['subject_name'] . "</option>";
// 			}
// 			break;

		case "select_subject":
			extract($_POST);
			$scls = xss_clean($class_name);
			$user_id = $_SESSION['user_id'];
			$branch_id = get_data('user', $user_id, 'branch_id')['data'] ?? $_SESSION['branch_id'] ?? 1;
			// 			$sql = "select * from subject where find_in_set('$scls', student_class) and status='ACTIVE' and branch_id='$branch_id' order by subject_order, subject_name";
			$sql = "select * from subject where find_in_set('$scls', student_class) and status='ACTIVE' and branch_id='$branch_id' and ifnull(show_in_report, 'YES') = 'YES' order by subject_order, subject_name";

			$sub = direct_sql($sql);
			echo "<option value=''>Select Subject</option>";
			foreach ($sub['data'] as $sub) {
				echo "<option value='" . $sub['id'] . "'>" . $sub['subject_name'] . "</option>";
			}
			break;

		case "select_sub_subject":
			extract($_POST);
			$sql = "select * from sub_subject where subject_id ='$subject_id'";
			$sub = direct_sql($sql);
			foreach ($sub['data'] as $sub) {
				echo "<option value='" . $sub['id'] . "'>" . $sub['sub_subject'] . "</option>";
			}
			break;

		case "marks_entry":
			echo "<pre>";
			// 			print_r($_POST);
			extract($_POST);
			$ct = count($_POST['student_id']);
			for ($i = 0; $i < $ct; $i++) {
				$student_id = $_POST['student_id'][$i];
				$student_admission = $_POST['student_admission'][$i];
				//$exam_name=$_POST['exam_name'];
				$subject = remove_space($_POST['subject']);
				$se = $subject . "_se";
				$mo = $subject . "_mo";
				$nb = $subject . "_nb";
				$adata = array('student_admission' => $student_admission, 'student_id' => $student_id, 'exam_name' => $exam_name, 'session_year' => $session_year);
				insert_data('exam', $adata);

				$data = array($se => $_POST[$se][$i], $mo => $_POST[$mo][$i], $nb => $_POST[$nb][$i], 'student_id' => $_POST['student_id'][$i], 'status' => 'updated');
				//print_r($data);
				$where = array('student_admission' => $student_admission, 'exam_name' => $exam_name);
				$res = update_multi_data('exam', $data, $where);
			}
			break;


		/*=== BINE MODULE ===*/
		case "teacher_marks_entry":
			echo "<pre>";
			//print_r($_POST);
			extract($_POST);
			$ct = count($_POST['student_id']);
			for ($i = 0; $i < $ct; $i++) {
				$student_id = $_POST['student_id'][$i];
				$student_admission = $_POST['student_admission'][$i];
				$exam_name = $_POST['exam_name'];
				$subject_col = explode(' ', $subject);
				$subject = remove_space($subject_col[0]);

				$se = $subject . "_se";
				$mo = $subject . "_mo";
				$nb = $subject . "_nb";
				$adata = array('student_admission' => $student_admission, 'student_id' => $student_id, 'exam_id' => $exam_name, 'session_year' => $session_year);

				$idata = insert_data('exam', $adata);

				$data = array($se => $_POST[$se][$i], $mo => $_POST[$mo][$i], $nb => $_POST[$nb][$i], 'student_id' => $_POST['student_id'][$i], 'status' => 'updated');
				$where = array('student_admission' => $student_admission, 'exam_id' => $exam_name);
				$res = update_multi_data('exam', $data, $where);
			}
			echo "<script> window.location ='../teacher_marks_entry'; </script>";
			break;


		case "co_marks_entry":
			extract($_POST);
			$ct = count($_POST['student_admission']);
			for ($i = 0; $i < $ct; $i++) {
				$student_admission = $_POST['student_admission'][$i];

				$where = array('student_admission' => $student_admission, 'exam_id' => $exam_id);
				$search = get_all('exam', '*', $where);
				$subject_list = co_subject_list($student_class);
				foreach ($subject_list as $subject) {
					$data[$subject['subject_column']] = $_POST[$subject['subject_column']][$i];
				}
				$data['student_admission'] = $_POST['student_admission'][$i];
				$data['attendance'] = $_POST['attendance'][$i];
				$data['remarks'] = $_POST['remarks'][$i];
				$data['status'] = 'updated';

				if ($search['count'] == 0) {
					insert_data('exam', $where);
					$res = update_multi_data('exam', $data, $where);
				} else {
					$res = update_multi_data('exam', $data, $where);
				}
			}
			$res['url'] = 'co_marks_entry';
			echo json_encode($res);
			break;

		case "marks_upload":
			extract($_POST);
			//print_r($_POST);
			$res = marks_import($table, ['student_admission', 'exam_id'], $remove);
			echo json_encode($res);
			break;



		case "update_enquiry":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = 'ACTIVE';
			$_POST['app_no'] = $app_no = 'E' . date('ym') . str_pad($id, 4, "0", STR_PAD_LEFT);
			$res = update_data('enquiry', $_POST, $_POST['id']);
			$res['url'] = 'manage_enquiry';
			$sms = "Dear $student_name, \nYour Application for Admission in class $student_class submitted successfully with application no is $app_no \nRegards \n$inst_name OFFAPP";
			$te_id = '1207166936482715380';
			send_sms($student_mobile, $sms);
			echo json_encode($res);
			break;

		case "update_material":
			$_POST['status'] = 'ACTIVE';
			$res = update_data('study_material', $_POST, $_POST['id']);
			$res['url'] = 'add_study_material';
			echo json_encode($res);
			break;

		// case "save_photo":
		//     $baseFromJavascript = $_POST['student_photo']; 
		//     $base_to_php = explode(',', $baseFromJavascript);
		//     $imageData = base64_decode($base_to_php[1]);
		//     $sourceImage = imagecreatefromstring($imageData);
		//     if ($sourceImage === false) {
		//         $res['msg'] = "Invalid image data.";
		//         $res['status'] = 'error';
		//         echo json_encode($res);
		//         break;
		//     }
		//     $file_name = date('ymdhis') . "_" . rnd_str(5) . ".jpg"; 
		//     $dir = '../upload/';
		//     $filepath = $dir . $file_name;
		//     if (!is_dir($dir)) {
		//         mkdir($dir, 0777, true);
		//     }
		//     $tempPath = tempnam(sys_get_temp_dir(), 'img_');
		//     $quality = 85; 
		//     do {
		//         imagejpeg($sourceImage, $tempPath, $quality);
		//         $fileSize = filesize($tempPath);
		//         $quality -= 5; 
		//     } while ($fileSize > 120 * 1024 && $quality > 10); 
		//     copy($tempPath, $filepath);
		//     unlink($tempPath); 
		//     imagedestroy($sourceImage);
		//     $res['msg'] = "The file " . $file_name . " has been uploaded.";
		//     $res['id'] = $file_name;
		//     $res['status'] = 'success';
		//     echo json_encode($res);
		//     break;

		case "save_photo":
			$baseFromJavascript = $_POST['student_photo'];
			$base_to_php = explode(',', $baseFromJavascript);
			$imageData = base64_decode($base_to_php[1]);
			$sourceImage = imagecreatefromstring($imageData);

			if ($sourceImage === false) {
				$res['msg'] = "Invalid image data.";
				$res['status'] = 'error';
				echo json_encode($res);
				break;
			}

			// Optional: Resize image if it's too large (e.g., max 600x600)
			$maxWidth = 600;
			$maxHeight = 600;
			$width = imagesx($sourceImage);
			$height = imagesy($sourceImage);

			if ($width > $maxWidth || $height > $maxHeight) {
				$scale = min($maxWidth / $width, $maxHeight / $height);
				$newWidth = (int) ($width * $scale);
				$newHeight = (int) ($height * $scale);

				$resizedImage = imagecreatetruecolor($newWidth, $newHeight);
				imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
				imagedestroy($sourceImage);
				$sourceImage = $resizedImage;
			}

			$file_name = date('ymdhis') . "_" . rnd_str(5) . ".jpg";
			$dir = '../upload/';
			$filepath = $dir . $file_name;

			if (!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}

			$tempPath = tempnam(sys_get_temp_dir(), 'img_');
			$targetSize = 100 * 1024; // 100 KB
			$quality = 90;

			// Compress image to under 100 KB
			do {
				imagejpeg($sourceImage, $tempPath, $quality);
				$fileSize = filesize($tempPath);
				$quality -= 5;
			} while ($fileSize > $targetSize && $quality > 10);

			copy($tempPath, $filepath);
			unlink($tempPath);
			imagedestroy($sourceImage);

			$res['msg'] = "The file " . $file_name . " has been uploaded.";
			$res['id'] = $file_name;
			$res['status'] = 'success';
			echo json_encode($res);
			break;

		case "save_gp_photo":
			$baseFromJavascript = $_POST['student_photo'];
			$base_to_php = explode(',', $baseFromJavascript);
			$imageData = base64_decode($base_to_php[1]);
			$sourceImage = imagecreatefromstring($imageData);
			if ($sourceImage === false) {
				$res['msg'] = "Invalid image data.";
				$res['status'] = 'error';
				echo json_encode($res);
				break;
			}
			$file_name = date('ymdhis') . "_" . rnd_str(5) . ".jpg";
			$dir = '../system/gatepass/';
			$filepath = $dir . $file_name;
			if (!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}
			$tempPath = tempnam(sys_get_temp_dir(), 'img_');
			$quality = 85;
			do {
				imagejpeg($sourceImage, $tempPath, $quality);
				$fileSize = filesize($tempPath);
				$quality -= 5;
			} while ($fileSize > 120 * 1024 && $quality > 10);
			copy($tempPath, $filepath);
			unlink($tempPath);
			imagedestroy($sourceImage);
			$res['msg'] = "The file " . $file_name . " has been uploaded.";
			$res['id'] = $file_name;
			$res['status'] = 'success';
			echo json_encode($res);
			break;



		/*---------------LIBRARY MANAGMENT ----------------*/


		case "update_book_cat":
			$res = update_data('book_cat', $_POST, $_POST['id']);
			$res['url'] = 'book_cat';
			echo json_encode($res);
			break;

		case "update_book_pub":
			$res = update_data('book_pub', $_POST, $_POST['id']);
			$res['url'] = 'book_cat';
			echo json_encode($res);
			break;

		case "update_book":
			$res = update_data('book_list', $_POST, $_POST['id']);
			$res['url'] = 'manage_book';
			echo json_encode($res);
			break;

		case "search_book":
			extract($_POST);
			$sql = "select accession_no, book_name, book_no from book_list where cat_id = '$cat_id' and book_name like '%$search_value%' or accession_no like '%$search_value%' or book_no like '%$search_value%'";
			$res = direct_sql($sql);
			echo json_encode($res);
			break;

		case "issue_book":
			extract($_POST);
			$_POST['issue_date'] = $today = date('Y-m-d');
			$_POST['issue_by'] = $user_id;
			$_POST['status'] = 'ISSUED';
			$_POST['valid_till'] = date("Y-m-d", strtotime($today . " +$max_day_allow day"));
			$res = insert_data('book_txn', $_POST);
			update_data('book_list', array('status' => 'ISSUED'), $book_id);
			$res['url'] = 'issue_book';
			echo json_encode($res);
			break;

		case "return_book":
			extract($_POST);
			$_POST['status'] = 'RETURN';
			if ($book_fine !== '') {
				$_POST['fine_date'] = date('Y-m-d');
			}
			$res = update_data('book_txn', $_POST, $id);
			update_data('book_list', array('status' => 'AVAILABLE'), $book_id);
			$res['url'] = 'book_return';
			echo json_encode($res);
			break;

		/*---------------Account MANAGMENT ----------------*/


		case "update_account_head":
			$res = update_data('account_head', $_POST, $_POST['id']);
			$res['url'] = 'exp_head';
			echo json_encode($res);
			break;

		case "exp_entry":
			$res = insert_data('account_txn', $_POST);
			$res['url'] = 'exp_head';
			echo json_encode($res);
			break;

		case "exp_update":
			extract($_POST);
			$res = update_data('account_txn', $_POST, $id);
			$res['url'] = 'manage_account';
			echo json_encode($res);
			break;

		// 		case "update_admit_card":
// 			extract($_POST);
// 			//print_r($_POST);
// 			$ct = count($subject_id);
// 			for ($i = 0; $i < $ct; $i++) {

		// 				$find = get_all('admit_card', '*', array('exam_id' => $exam_id, 'student_class' => $student_class, 'subject_id' => $subject_id[$i]));

		// 				$data['exam_id'] = $exam_id;
// 				$data['student_class'] = $student_class;
// 				$data['subject_id'] = $subject_id[$i];
// 				$data['exam_date'] = $exam_date[$i];
// 				$data['start_time'] = $start_time[$i];
// 				$data['end_time'] = $end_time[$i];
// 				$data['sitting_name'] = $sitting_name[$i];

		// 				if ($find['count'] == 0) {
// 					$res = insert_data('admit_card', $data);
// 				} else {
// 					$res = update_multi_data('admit_card', $data, array('exam_id' => $exam_id, 'student_class' => $student_class, 'subject_id' => $subject_id[$i]));
// 				}
// 			}
// 			$res['url'] = "admit_card?student_class=$student_class&exam_id=$exam_id";
// 			echo json_encode($res);
// 			break;

		// case "update_admit_card":
		//     extract($_POST);

		//     $ct = count($subject_name);
		//     for ($i = 0; $i < $ct; $i++) {

		//         $sub_id = $subject_id[$i] ?? '';
		//         $sub_name = trim($subject_name[$i]);

		//         $data = array(
		//             'exam_id'       => $exam_id,
		//             'student_class' => $student_class,
		//             'subject_name'  => $sub_name,
		//             'exam_date'     => $exam_date[$i],
		//             'start_time'    => $start_time[$i],
		//             'end_time'      => $end_time[$i],
		//             'sitting_name'  => $sitting_name[$i]
		//         );

		//         // Only include subject_id if it exists (from DB subjects)
		//         if (!empty($sub_id)) {
		//             $data['subject_id'] = $sub_id;
		//         }

		//         // Build condition based on availability
		//         $condition = array(
		//             'exam_id'       => $exam_id,
		//             'student_class' => $student_class
		//         );
		//         if (!empty($sub_id)) {
		//             $condition['subject_id'] = $sub_id;
		//         } else {
		//             $condition['subject_name'] = $sub_name;
		//         }

		//         // Check if record exists
		//         $find = get_all('admit_card', '*', $condition);

		//         if ($find['count'] == 0) {
		//             $res = insert_data('admit_card', $data);
		//         } else {
		//             $res = update_multi_data('admit_card', $data, $condition);
		//         }
		//     }

		//     $res['url'] = "admit_card?student_class=$student_class&exam_id=$exam_id";
		//     echo json_encode($res);
		//     break;

		case "update_admit_card":

			extract($_POST);
			$branch_id = $_SESSION['branch_id'];
			$query = "DELETE FROM admit_card WHERE exam_id='$exam_id' AND student_class='$student_class'";
			mysqli_query($con, $query);
			if (!empty($subject_name)) {
				for ($i = 0; $i < count($subject_name); $i++) {
					$sub_id = trim($subject_id[$i] ?? '');
					$sub_name = trim($subject_name[$i]);
					if ($sub_name == '') {
						continue;
					}
					$exam_date1 = $exam_date[$i] ?? '';
					$start_time1 = $start_time[$i] ?? '';
					$end_time1 = $end_time[$i] ?? '';
					$sitting_name1 = $sitting_name[$i] ?? '';
					if ($sub_id == '') {
						$sub_id = 'NULL';
					} else {
						$sub_id = "'$sub_id'";
					}

					$query = "INSERT INTO admit_card ( exam_id,student_class,subject_id,subject_name,exam_date,start_time,end_time,sitting_name,branch_id)
                        VALUES ('$exam_id','$student_class',$sub_id,'$sub_name','$exam_date1','$start_time1','$end_time1','$sitting_name1',$branch_id)";
					mysqli_query($con, $query);
				}
			}
			echo json_encode(['status' => 'success', 'msg' => 'Updated Successfully', 'url' => "admit_card?student_class=$student_class&exam_id=$exam_id"]);

			break;


		case "admin_txn":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('admin_txn', $_POST, $_POST['id']);
			$res['url'] = 'admin_txn';
			echo json_encode($res);
			break;

		case "cancel_admin_txn":
			extract($_POST);
			//print_r($_POST);
			$txn_id = $_POST['txn_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');

			$cancel_data = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
			$res = update_data('admin_txn', $cancel_data, $txn_id);
			if ($res['status'] == 'success') {
				$res['msg'] = "Txn No. $txn_id Cancelled Successfully";
			}
			echo json_encode($res);
			break;

		case "update_holiday":
			$res = update_data('holiday', $_POST, $_POST['id']);
			$res['url'] = 'add_holiday';
			echo json_encode($res);
			break;

		case "last_roll":
			extract($_REQUEST);
			$res = last_roll($student_class, $student_section);
			echo json_encode($res);
			break;

		case "sl":
			extract($_REQUEST);
			$sl = short_url($url);
			echo $sl;
			break;

		case "update_exam_list":
			extract($_POST);
			unset($_POST['isedit']); // Remove 'isedit' from the POST data to avoid issues during updates
			$search = get_all('exam_list', '*', ['exam_name' => $exam_name]);
			if ($search['count'] == 0) {
				$res = update_data('exam_list', $_POST, $id);
				$res['url'] = 'exam_list';
			} else if ($search['count'] == 1) {
				if ($search['data'][0]['id'] == $id) {
					$res = update_data('exam_list', $_POST, $id);
					$res['url'] = 'exam_list';
				} else {
					$res['status'] = 'error';
					$res['msg'] = "Exam name cannot be duplicate during edit.";
				}
			} else {
				// Update the record
				$res['status'] = 'error';
				$res['msg'] = "Exam name cannot be duplicate.";
			}
			echo json_encode($res);
			break;

		case "update_marks_scheme":
			extract($_POST);
			//print_r($_POST);
			$ct = count($subject_id);
			$new = $update = 0;
			for ($i = 0; $i < $ct; $i++) {

				$find = get_all('admit_card', '*', array('exam_id' => $exam_id, 'student_class' => $student_class, 'subject_id' => $subject_id[$i]));
				$data['exam_id'] = $exam_id;
				$data['student_class'] = $student_class;
				$data['subject_id'] = $subject_id[$i];
				foreach ($marks_type_list as $mstype) {
					$col_name = 'marks_' . remove_space($mstype);
					$data[$col_name] = $$col_name[$i];
				}
				$data['marks_total'] = $marks_total[$i];
				$data['marks_passing'] = $marks_passing[$i];
				if ($find['count'] == 0) {
					$res = insert_data('admit_card', $data);
				} else {
					$res = update_multi_data('admit_card', $data, array('exam_id' => $exam_id, 'student_class' => $student_class, 'subject_id' => $subject_id[$i]));
				}
			}
			$res['url'] = "marks_setting?student_class=$student_class&exam_id=$exam_id";
			echo json_encode($res);
			break;

		case "single_marks_update":
			extract($_REQUEST);
			$where = ['student_admission' => $student_admission, 'exam_id' => $exam_id];
			$student_id = get_data('student', $student_admission, 'id', 'student_admission')['data'];
			$search = get_all('exam', '*', $where);
			if ($search['count'] > 0) {
				$res = update_multi_data('exam', [$col_name => $value, 'student_id' => $student_id], $where);
			} else {
				$res = insert_data('exam', ['student_admission' => $student_admission, 'student_id' => $student_id, 'exam_id' => $exam_id, $col_name => $value]);
			}
			echo json_encode($res);
			break;

		case "tabulation_single_marks":
			extract($_POST);
			$where = ['term' => $term, 'student_admission' => $student_admission, 'subject' => $subject, 'student_id' => $student_id, 'branch_id' => $_SESSION['branch_id']];
			// 			$student_id = get_data('student', $student_admission, 'id', 'student_admission')['data'];
			$search = get_all('tabulation', ['id'], $where);
			if ($search['count'] > 0) {
				$res = update_multi_data('tabulation', [$col_name => $value, 'student_id' => $student_id], $where);
			} else {
				$res = insert_data('tabulation', ['student_id' => $student_id, 'subject' => $subject, 'student_admission' => $student_admission, 'term' => $term, $col_name => $value, 'branch_id' => $_SESSION['branch_id']]);
			}
			echo json_encode($res);
			break;

		case "update_branch":
			extract($_POST);
			unset($_POST['isedit']);
			$res = update_data('branch_list', $_POST, $id);
			$classes = $_POST['class_list'];
			$cls_arr = explode(",", $classes);
			foreach ($cls_arr as $class) {
				$sql = "SELECT * FROM fee_details WHERE student_class = '$class' and branch_id = '{$_POST['id']}'";
				$search = direct_sql($sql);
				if ($search['count'] < 1) {
					$res2 = insert_data('fee_details', ['student_class' => $class, 'branch_id' => $_POST['id'], 'status' => 'ACTIVE']);
				}
			}
			$res['url'] = 'add_branch';
			echo json_encode($res);
			break;

		case "switch_branch":
			extract($_POST);
			$res = update_data('user', ['branch_id' => $branch_id], $user_id);
			if ($res['status'] == 'success') {
				$_SESSION['branch_id'] = $branch_id;
			}
			$res['url'] = 'add_branch';
			echo json_encode($res);
			break;


		case "make_att":
			// print_r($_POST);
			$tbl_name = 'student_att'; //removespace(date('F_Y',strtotime($att_date)));
			$checked_ids = $_POST['checked_ids'];
			$unchecked_ids = $_POST['unchecked_ids'];
			$att_date = $_POST['att_date'];
			$att_status = $_POST['att_status'];
			$reverse_status = ($_POST['att_status'] == 'A') ? 'P' : 'A';
			$m_value = remove_space(date('M_Y', strtotime($att_date)));
			$col_name = 'd_' . date('j', strtotime($att_date));
			foreach ($checked_ids as $stu_id) {
				$post = array($col_name => $att_status, 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('student_id' => $stu_id, 'att_month' => $m_value));
			}
			foreach ($unchecked_ids as $stu_id) {
				$post = array($col_name => $reverse_status, 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('student_id' => $stu_id, 'att_month' => $m_value));
			}

			$res['status'] = 'success';
			break;

		case "update_hiring":
			// print_r($_POST);
			$tbl_name = 'career'; //removespace(date('F_Y',strtotime($att_date)));
			$gender = $_POST['gender'];
			$gender_str = implode(',', $gender);
			$language = $_POST['language'];
			$language_str = implode(',', $language);
			$process = $_POST['process'];
			$process_str = implode(',', $process);
			$post = array('hiring_title' => $_POST['job_title'], 'job_post' => $_POST['job_post'], 'salary' => $_POST['salary'], 'qualification' => $_POST['qualification'], 'experiense' => $_POST['experiense'], 'gender' => $gender_str, 'language' => $language_str, 'process' => $process_str, 'email' => $_POST['email'], 'remark' => $_POST['remark'], 'status' => 'ACTIVE');
			$res = update_data($tbl_name, $post, $_POST['id']);
			$res['url'] = 'manage_hiring';
			$res['status'] = 'success';
			echo json_encode($res);
			break;

		case "update_notice":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = $_POST['status'] ?? 'ACTIVE';
			$res = update_data('notice', $_POST, $_POST['id']);
			// $res =insert_data('notice',$_POST);
			$res['url'] = 'notice';
			echo json_encode($res);
			break;

		case "update_gallery":
			extract($_POST);
			unset($_POST['isedit']);
			// $_POST['image'] = upload_img('image')['id'];
			$res = update_data('gallery', $_POST, $id);
			$res['url'] = 'add_to_gallery';
			echo json_encode($res);
			break;

		case "update_employee":
			extract($_POST);
			// 			$user = fetch_all('user','*',[])
			$user_name = strtolower($_POST['emp_code']);
			$user_pass = md5($_POST['password']);
			$emp_status = $_POST['status'] ?? 'ACTIVE';
			$user_res = get_data('user', $user_name, null, 'user_name');
			if ($user_res['count'] > 0) {
				$res2 = update_data('user', ['user_type' => 'TEACHER', 'user_pass' => $user_pass, 'user_name' => $user_name, 'full_name' => $_POST['name'], 'user_email' => $_POST['email'], 'status' => $emp_status, 'branch_id' => $_SESSION['branch_id'], 'allowed_branch' => $_SESSION['branch_id']], $user_res['data']['id']);
			} else {
				$res2 = insert_data('user', ['user_type' => 'TEACHER', 'user_pass' => $user_pass, 'user_name' => $user_name, 'full_name' => $_POST['name'], 'user_email' => $_POST['email'], 'status' => $emp_status, 'branch_id' => $_SESSION['branch_id'], 'allowed_branch' => $_SESSION['branch_id']]);
			}
			unset($_POST['isedit']);
			unset($_POST['password']);
			$_POST['allow_class'] = (isset($_POST['allow_class'])) ? implode(",", $_POST['allow_class']) : '';
			$_POST['allow_subject'] = (isset($_POST['allow_subject'])) ? implode(",", $_POST['allow_subject']) : '';
			$res = update_data('employee', $_POST, $id);
			$res['url'] = 'manage_employee';
			echo json_encode($res);
			break;

		case "update_teacher_subject":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('teacher_subject', $_POST, $id);
			$res['url'] = 'manage_teacher_subject';
			echo json_encode($res);
			break;

		case "leave_details":
			extract($_POST);
			$tbl_name = 'employee_att';
			// $checked_ids = $_POST['checked_ids'];
			$emp_id = $_POST['emp_id'];
			$att_frm_date = $_POST['from_date'];
			$att_to_date = $_POST['to_date'];
			$leave_status = 'L';
			$start = strtotime($att_frm_date);
			$end = strtotime($att_to_date);
			if ($start > $end) {
				$res['status'] = 'error';
				$res['message'] = 'Invalid date range';
				echo json_encode($res);
				break;
			}
			for ($date = $start; $date <= $end; $date = strtotime('+1 day', $date)) {
				$day = date('j', $date);
				$month_key = strtolower(date('M_Y', $date));
				$col_name = 'd_' . $day;
				// foreach ($checked_ids as $emp_id) {
				$post = array($col_name => $leave_status, 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array(
					'emp_id' => $emp_id,
					'att_month' => $month_key
				));
				// }
			}
			$branch_id = intval($_SESSION['branch_id']);
			unset($_POST['isedit']);
			unset($_POST['id']);
			$res = insert_data('leave_details', $_POST);
			$res['url'] = 'employee_attendance';
			echo json_encode($res);
			break;


		case "leave_apply":
			extract($_POST);
			unset($_POST['isedit']);
			unset($_POST['id']);
			$_POST['status'] = 'PENDING';
			$res = insert_data('leave_details', $_POST);
			$res['url'] = 'leave_apply';
			echo json_encode($res);
			break;

		case "half_day_leave":
			extract($_POST);
			$tbl_name = 'employee_att';
			$emp_id = $_POST['emp_id'];
			$att_frm_date = $_POST['from_date'];
			$leave_status = 'H';
			$start = strtotime($att_frm_date);
			$day = date('j', $date);
			$month_key = strtolower(date('M_Y', $date));
			$col_name = 'd_' . $day;
			$post = array($col_name => $leave_status, 'status' => 'ACTIVE');
			$res = update_multi_data($tbl_name, $post, array(
				'emp_id' => $emp_id,
				'att_month' => $month_key
			));
			$branch_id = intval($_SESSION['branch_id']);
			unset($_POST['isedit']);
			unset($_POST['id']);
			$res = insert_data('leave_details', $_POST);
			$res['url'] = 'employee_attendance';
			echo json_encode($res);
			break;

		case "make_emp_att":
			// print_r($_POST);
			$tbl_name = 'employee_att'; //removespace(date('F_Y',strtotime($att_date)));
			$checked_ids = $_POST['checked_ids'];
			$unchecked_ids = $_POST['unchecked_ids'];
			$att_date = $_POST['att_date'];
			$att_status = $_POST['att_status'];
			$reverse_status = ($_POST['att_status'] == 'A') ? 'P' : 'A';
			$m_value = remove_space(date('M_Y', strtotime($att_date)));
			$col_name = 'd_' . date('j', strtotime($att_date));
			foreach ($checked_ids as $emp_id) {
				$post = array($col_name => $att_status, 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('emp_id' => $emp_id, 'att_month' => $m_value));
			}
			foreach ($unchecked_ids as $emp_id) {
				$post = array($col_name => $reverse_status, 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('emp_id' => $emp_id, 'att_month' => $m_value));
			}
			$res['status'] = 'success';
			echo json_encode($res);
			break;

		case "update_salary":
			unset($_POST['example1_length']);
			unset($_POST['isedit']);
			unset($_POST['id']);
			for ($i = 0; $i < count($_POST['emp_id']); $i++) {
				$data = [
					'month' => $_POST['month'],
					'branch_id' => $_POST['branch_id'],
					'basic_salary' => $_POST['salary'][$i],
					'total_present' => $_POST['total_present'][$i],
					'total_absent' => $_POST['total_absent'][$i],
					'total_leave' => $_POST['total_leave'][$i],
					'payable_amount' => $_POST['payable_amount'][$i],
					'total_present' => $_POST['total_present'][$i],
					'emp_id' => $_POST['emp_id'][$i],
				];
				$search = get_all('salary', '*', ['month' => $_POST['month'], 'emp_id' => $_POST['emp_id'][$i], 'branch_id' => $_POST['branch_id']]);
				if ($search['count'] == 0) {
					$res = insert_data('salary', $data);
				} else {
					$res = update_data('salary', $data, $search['data'][0]['id']);
				}
			}
			$res['url'] = 'add_salary';
			echo json_encode($res);
			break;

		case "update_home_work":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = 'SHOW';
			$res = update_data('home_work', $_POST, $_POST['id']);
			$res['url'] = 'home_work';
			echo json_encode($res);
			break;

		case "update_scholorship":
			$_POST = json_decode(file_get_contents('php://input'), true);
			$_POST['branch_id'] = isset($_SESSION['branch_id']) ?? '1';
			$_POST['otp'] = $otp = rand(100000, 999999);
			$_POST['app_no'] = $app_no = date('ymdhis') . rand(100, 999);
			extract($_POST);
			//print_r($_POST);
			$template = file_get_contents('message.html');
			$template = str_replace('{{OTP}}', $otp, $template);
			$message = str_replace('{{name}}', $name, $template);
			if ($_POST['name'] != '' or $_POST['email'] != '' or $_POST['mobile'] != '') {
				$res = insert_data('scholorship', $_POST);
			}
			rtf_mail($email, "Thanks $name for Interest in RM Public School - Scholarship Contest ", $message, $name);
			$res['otp'] = $otp;
			$res['app_no'] = $app_no;
			echo json_encode($res);
			break;

		case "verify_otp":
			$_POST = json_decode(file_get_contents('php://input'), true);
			extract($_POST);
			$res['status'] = 'error';
			$res['status'] = 'OTP Not Matched';
			$sotp = get_data('scholorship', $app_no, 'otp', 'app_no')['data'];
			if ($sotp == $otp) {
				$res['status'] = 'success';
				$res['msg'] = 'OTP Verified Successfully';
			}
			echo json_encode($res);
			break;

		case "update_scholor_data":
			$_POST = json_decode(file_get_contents('php://input'), true);
			$_POST['status'] = isset($_POST['status']) ?? 'ACTIVE';
			extract($_POST);
			$res = update_data('scholorship', ['payment_status' => $payment_status], $app_no, 'app_no');
			echo json_encode($res);
			break;

		case "update_adm_payment":
			extract($_POST);
			$_POST['pay_status'] = 'PAID';
			$_POST['payment_received_by'] = $_SESSION['user_id'];
			$res = update_data('admission', $_POST, $_POST['id']);
			$res['url'] = 'application_list';
			//  print_r($res);
			echo json_encode($res);
			break;

		case "update_scholor_payment":
			extract($_POST);
			$stinfo = get_data('scholorship', $_POST['id'])['data'];
			if (!$stinfo) {
				echo json_encode(['status' => 'error', 'message' => 'Student not found']);
				break;
			}
			$st_class = $stinfo['student_class'];
			$roman_to_int = [
				'I' => '01',
				'II' => '02',
				'III' => '03',
				'IV' => '04',
				'V' => '05',
				'VI' => '06',
				'VII' => '07',
				'VIII' => '08',
				'IX' => '09',
				'X' => '10',
				'XI' => '11',
				'XII' => '12'
			];
			if (!isset($roman_to_int[$st_class])) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid student class']);
				break;
			}
			$st_class_num = $roman_to_int[$st_class];
			$last_roll = direct_sql("SELECT student_roll FROM scholorship WHERE student_class='$st_class' ORDER BY student_roll DESC LIMIT 1")['data'][0]['student_roll'] ?? null;
			if ($last_roll) {
				$lr = intval(substr($last_roll, -3));
			} else {
				$lr = 0;
			}
			$new_roll = '2025' . $st_class_num . str_pad($lr + 1, 3, "0", STR_PAD_LEFT);

			$_POST['student_roll'] = $new_roll;
			$_POST['payment_status'] = 'PAID';
			$_POST['payment_received_by'] = $_SESSION['user_id'] ?? 'Unknown';
			$res = update_data('scholorship', $_POST, $_POST['id']);
			$res['url'] = 'scholorship_app';
			echo json_encode($res);
			break;


		case "update_scholorship_ad":
			$_POST = array_map('strtoupper', $_POST);
			$_POST['branch_id'] = '1';
			$_POST['otp'] = $otp = rand(100000, 999999);
			$_POST['app_no'] = $app_no = date('ymdhis') . rand(100, 999);
			$_POST['status'] = isset($_POST['status']) ? $_POST['status'] : 'ACTIVE';
			unset($_POST['isedit']);

			extract($_POST);
			// 			print_r($_POST);
			$template = file_get_contents('message.html');
			$template = str_replace('{{OTP}}', $otp, $template);
			$message = str_replace('{{name}}', $name, $template);
			if ($_POST['name'] != '' or $_POST['email'] != '' or $_POST['mobile'] != '') {
				$res = update_data('scholorship', $_POST, $id);
			}
			rtf_mail($email, "Thanks $name for Interest in RM Public School - Scholarship Contest ", $message, $name);
			$res['msg'] = 'Your Application has been added with Application no ' . $app_no;
			$res['url'] = 'scholorship_app';
			echo json_encode($res);
			break;

		case "update_recruitment":

			$_POST['app_no'] = $app_no = date('ymdhis') . rand(100, 999);
			extract($_POST);
			// 			print_r($_POST);
			$res = insert_data('recruitment', $_POST);
			$res['msg'] = "Dear $name ! Your Application Submitted Successfully. Your Application No is $app_no";
			echo json_encode($res);
			break;

		case "update_scholor_marks":
			// 			unset($_POST['isedit']);
// 			unset($_POST['id']);
			$res = update_data('scholorship', $_POST, $_POST['id']);
			$res['url'] = 'scholor_marks';
			echo json_encode($res);
			header("Location: ../scholor_marks");
			exit();
			break;

		case "update_gate_pass":
			unset($_POST['isedit']);
			$_POST['status'] = 'ACTIVE';
			extract($_POST);
			$res = update_data('gate_pass', $_POST, $_POST['id']);
			$res['url'] = 'manage_gate_pass';
			echo json_encode($res);
			break;

		case "get_subjects":
			$class = trim($_POST['class_name'] ?? '');
			$branch = intval($_POST['branch_id'] ?? 0);

			$options = "<option value=''>Select Subject</option>";

			if ($class && $branch) {
				$class = addslashes($class);
				// $query = "SELECT id, subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch";
				$query = "SELECT id, subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch AND ifnull(show_in_report, 'YES') = 'YES'";
				$res = direct_sql($query);

				if ($res['status'] === 'success' && !empty($res['data'])) {
					foreach ($res['data'] as $row) {
						$options .= "<option value='{$row['id']}'>{$row['subject_name']}</option>";
					}
				} else {
					$options .= "<option value=''>No subjects found</option>";
				}
			}

			echo $options;
			break;


		case "get_sub_subjects":
			// $sql = "SELECT id,subject_name FROM subject WHERE (FIND_IN_SET('NUR', student_class) OR FIND_IN_SET('LKG', student_class) OR FIND_IN_SET('UKG', student_class)) AND subject_type='Scholastic' AND branch_id='{$_SESSION['branch_id']}'";;
			$sql = "SELECT id,subject_name FROM subject WHERE (FIND_IN_SET('NUR', student_class) OR FIND_IN_SET('LKG', student_class) OR FIND_IN_SET('UKG', student_class)) AND subject_type='Scholastic' AND branch_id='{$_SESSION['branch_id']}' AND ifnull(show_in_report, 'YES') = 'YES'";
			$sub = direct_sql($sql);
			// print_r($sub);
			foreach ($sub['data'] as $s) {
				echo "<option value='" . $s['id'] . "'>" . $s['subject_name'] . "</option>";
			}
			break;


		case "get_sub_subjects_br":
			// $sql = "SELECT id,subject_name FROM subject WHERE  subject_type='Scholastic' AND branch_id='{$_SESSION['branch_id']}'";
			$sql = "SELECT id,subject_name FROM subject WHERE  subject_type='Scholastic' AND branch_id='{$_SESSION['branch_id']}' AND ifnull(show_in_report, 'YES') = 'YES'";
			$sub = direct_sql($sql);
			// print_r($sub);
			foreach ($sub['data'] as $s) {
				echo "<option value='" . $s['id'] . "'>" . $s['subject_name'] . "</option>";
			}
			break;



		case "update_sub_subject":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('pre_sub_subject', $_POST, $id);
			$res['url'] = 'manage_sub_subject';
			echo json_encode($res);
			break;

		case "update_pre_papers":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('pre_papers', $_POST, $id);
			$res['url'] = 'manage_sub_papers';
			echo json_encode($res);
			break;

		case "tabulation_pre_marks":
			extract($_POST);

			$branch_id = $_SESSION['branch_id'];
			$where = ['term' => $term, 'student_admission' => $student_admission, 'subject_id' => $subject_id, 'student_id' => $student_id, 'sub_subject_id' => $sub_subject_id, 'paper_id' => $paper_id, 'branch_id' => $branch_id];

			if (!empty($sub_subject)) {
				$where['sub_subject'] = $sub_subject;
			}
			if (!empty($paper_id)) {
				$where['paper_id'] = $paper_id;
			}
			$search = get_all('pre_marks', ['id'], $where);

			if ($search['count'] > 0) {
				$res = update_multi_data('pre_marks', [$col_name => $value], $where);
			} else {
				$insertData = ['student_id' => $student_id, 'student_admission' => $student_admission, 'subject_id' => $subject_id, 'sub_subject_id' => $sub_subject_id, 'term' => $term, 'branch_id' => $branch_id, $col_name => $value];

				if (!empty($sub_subject)) {
					$insertData['sub_subject'] = $sub_subject;
				}
				if (!empty($paper_id)) {
					$insertData['paper_id'] = $paper_id;
				}

				$res = insert_data('pre_marks', $insertData);
			}
			echo json_encode($res);
			break;

		case "add_student_att":
			extract($_POST);
			$where = ['term' => $term, 'student_admission' => $student_admission, 'student_id' => $student_id, 'branch_id' => $_SESSION['branch_id']];
			$search = get_all('attendance', ['id'], $where);
			if ($search['count'] > 0) {
				$res = update_multi_data('attendance', [$col_name => $value, 'student_admission' => $student_admission, 'student_id' => $student_id], $where);
			} else {
				$res = insert_data('attendance', ['student_id' => $student_id, 'student_admission' => $student_admission, 'term' => $term, $col_name => $value]);
			}
			echo json_encode($res);
			break;

		case "dues_fee":
			extract($_POST);
			$_POST['student_id'] = 'ACTIVE';

			$res = finaldues($student_id);
			echo json_encode($res);
			break;

		case "auto_house":
			$class = $_POST['student_class'];
			$houses = $house_list;
			$counts = [];
			foreach ($houses as $house) {
				$sql = "SELECT COUNT(*) as total FROM student 
                                  WHERE student_class='$class' 
                                  AND house_name='$house' 
                                  AND status='ACTIVE'";
				$res = direct_sql($sql);
				$counts[$house] = $res['data'][0]['total'];
			}
			$min_house = array_keys($counts, min($counts))[0];
			echo $min_house;
			break;

		case "save_ut_marks":
			extract($_POST);
			$check = get_all('ut_marks', '*', ['ut_id' => $ut_id, 'student_id' => $student_id]);
			if ($check['count'] > 0) {
				$res = update_multi_data('ut_marks', ['marks' => $marks, 'entry_time' => date('Y-m-d H:i:s'), 'submitted_by' => $_SESSION['user_id']], ['ut_id' => $ut_id, 'student_id' => $student_id]);
			} else {
				$res = insert_data('ut_marks', ['ut_id' => $ut_id, 'student_id' => $student_id, 'marks' => $marks, 'entry_time' => date('Y-m-d H:i:s'), 'submitted_by' => $_SESSION['user_id']]);
			}
			echo json_encode($res);
			break;

		case "update_logbook":
			extract($_POST);
			$res = update_data('logbook_period', [$field => $value], $id);

			// If subject is changed, automatically update teacher and return their name
			if ($field == 'subject') {
				// Get period info to know class/section
				$p_info = get_data('logbook_period', $id);
				$log_id = $p_info['data']['logbook_id'];
				$log_info = get_data('logbook', $log_id);
				$class = $log_info['data']['student_class'];
				$section = $log_info['data']['student_section'];

				// Look up teacher from timetable (source of truth)
				$log_date = $log_info['data']['date'] ?? date('Y-m-d');
				$day_name = date('l', strtotime($log_date));
				$period_no = $p_info['data']['period_no'] ?? 0;

				// First try: find teacher for this exact subject, class, section, day, and period in timetable
				$t_query = "SELECT t.teacher_id, e.name as teacher_name FROM timetable t 
						   JOIN employee e ON t.teacher_id = e.id
						   WHERE t.subject = '$value' 
						   AND t.student_class = '$class' 
						   AND t.student_section = '$section' 
						   AND t.day = '$day_name'
						   AND t.status = 'ACTIVE'
						   AND t.branch_id = '$branch_id'
						   LIMIT 1";
				$t_res = direct_sql($t_query, 'get', false);

				// Fallback: search across all days if no match on specific day
				if ($t_res['count'] == 0) {
					$t_query = "SELECT t.teacher_id, e.name as teacher_name FROM timetable t 
							   JOIN employee e ON t.teacher_id = e.id
							   WHERE t.subject = '$value' 
							   AND t.student_class = '$class' 
							   AND t.student_section = '$section' 
							   AND t.status = 'ACTIVE'
							   AND t.branch_id = '$branch_id'
							   LIMIT 1";
					$t_res = direct_sql($t_query, 'get', false);
				}

				if ($t_res['count'] > 0) {
					$teacher_id = $t_res['data'][0]['teacher_id'];
					$teacher_name = $t_res['data'][0]['teacher_name'];
					update_data('logbook_period', ['teacher_id' => $teacher_id], $id);
					$res['teacher_name'] = $teacher_name;
				} else {
					$res['teacher_name'] = 'Not Assigned';
				}
			}
			echo json_encode($res);
			break;




		case "signoff_logbook":
			extract($_POST);
			$res = update_data('logbook_period', ['status' => 'DONE'], $id);
			echo json_encode($res);
			break;

		case "add_syllabus":
			$student_class = $_POST['student_class'] ?? '';
			$subject = $_POST['subject'] ?? '';
			$chapter = $_POST['chapter'] ?? '';
			$topics = $_POST['topics'] ?? [];
			$planned_dates = $_POST['planned_dates'] ?? [];

			if (is_array($topics)) {
				foreach ($topics as $index => $topic) {
					if (!empty(trim($topic))) {
						$date = $planned_dates[$index] ?? date('Y-m-d');
						insert_data('syllabus', [
							'student_class' => $student_class,
							'subject' => $subject,
							'chapter' => $chapter,
							'topic' => trim($topic),
							'planned_date' => $date
						]);
					}
				}
			}
			header("Location: " . $_SERVER['HTTP_REFERER']);
			break;

		case "update_syllabus_progress":
			extract($_POST);
			$check = get_all('syllabus_progress', '*', ['syllabus_id' => $syllabus_id, 'student_section' => $student_section]);
			if ($check['count'] > 0) {
				update_multi_data('syllabus_progress', ['status' => $status, 'date_taught' => $date_taught, 'ut_covered' => $ut_covered], ['syllabus_id' => $syllabus_id, 'student_section' => $student_section]);
			} else {
				insert_data('syllabus_progress', ['syllabus_id' => $syllabus_id, 'student_section' => $student_section, 'status' => $status, 'date_taught' => $date_taught, 'ut_covered' => $ut_covered]);
			}
			header("Location: " . $url);
			break;

		case "save_single_attendance":
			extract($_POST);
			$m_value = remove_space(date('M_Y', strtotime($att_date)));
			$day_num = date('j', strtotime($att_date));
			$col_name = 'd_' . $day_num;

			if ($status == 'C') {
				// Clear attendance (set to NULL)
				$sql = "UPDATE student_att SET $col_name = NULL WHERE student_id = '$student_id' AND att_month = '$m_value'";
				$res = mysqli_query($con, $sql);
				echo json_encode(['status' => 'success', 'count' => mysqli_affected_rows($con)]);
			} else {
				$res = update_multi_data('student_att', [$col_name => $status], ['student_id' => $student_id, 'att_month' => $m_value]);
				echo json_encode($res);
			}
			break;


		case "clear_all_attendance":
			extract($_POST);
			$m_value = remove_space(date('M_Y', strtotime($att_date)));
			$day_num = date('j', strtotime($att_date));
			$col_name = 'd_' . $day_num;
			// For bulk clear, update all student_ids for this month to NULL for the given day
			if (!empty($student_ids)) {
				$stu_id_list = implode("','", $student_ids);
				$sql = "UPDATE student_att SET $col_name = NULL WHERE att_month = '$m_value' AND student_id IN ('$stu_id_list')";
				mysqli_query($con, $sql);
			}
			echo json_encode(['status' => 'success']);
			break;


		case "mark_all_present":
			extract($_POST);
			$m_value = remove_space(date('M_Y', strtotime($att_date)));
			$day_num = date('j', strtotime($att_date));
			$col_name = 'd_' . $day_num;
			foreach ($student_ids as $stu_id) {
				update_multi_data('student_att', [$col_name => 'P'], ['student_id' => $stu_id, 'att_month' => $m_value]);
			}
			echo json_encode(['status' => 'success']);
			break;

		case "copy_prev_attendance":
			extract($_POST);
			$curr_ts = strtotime($att_date);
			$prev_date = date('Y-m-d', strtotime('-1 day', $curr_ts));
			if (date('D', strtotime($prev_date)) == 'Sun') {
				$prev_date = date('Y-m-d', strtotime('-2 day', $curr_ts));
			}

			$prev_m = remove_space(date('M_Y', strtotime($prev_date)));
			$prev_d_num = 'd_' . date('j', strtotime($prev_date));

			$curr_m = remove_space(date('M_Y', strtotime($att_date)));
			$curr_d_num = 'd_' . date('j', strtotime($att_date));

			$students = get_all('student', 'id', ['student_class' => $class_name, 'student_section' => $section, 'status' => 'ACTIVE']);
			foreach ($students['data'] as $s) {
				$stu_id = $s['id'];
				$prev_att = direct_sql("SELECT $prev_d_num as status FROM student_att WHERE student_id = '$stu_id' AND att_month = '$prev_m'");
				if ($prev_att['count'] > 0) {
					$status = $prev_att['data'][0]['status'];
					if (!empty($status)) {
						update_multi_data('student_att', [$curr_d_num => $status], ['student_id' => $stu_id, 'att_month' => $curr_m]);
					}
				}
			}
			echo json_encode(['status' => 'success']);
			break;

		case "get_teacher_subjects":
			$class = trim($_POST['student_class'] ?? '');
			$section = trim($_POST['student_section'] ?? '');
			$branch = intval($_SESSION['branch_id'] ?? 1);
			$user_type = $_SESSION['user_type'] ?? '';
			$selected_teacher_id = intval($_POST['teacher_id'] ?? 0);

			$options = "<option value=''>Select Subject</option>";

			if ($class && $section) {
				$class = addslashes($class);
				$section = addslashes($section);

				if (strtoupper($user_type) == 'TEACHER') {
					$emp = get_teacher_employee();
					$emp_id = $emp['id'] ?? 0;
				} else {
					$emp_id = $selected_teacher_id;
				}

				if ($emp_id > 0) {
					// Fetch subjects from timetable (daily routine) — the source teacher actually filled
					$query = "SELECT DISTINCT subject FROM timetable 
					          WHERE teacher_id = '$emp_id' 
					          AND student_class = '$class' 
					          AND student_section = '$section' 
					          AND status = 'ACTIVE'
					          AND branch_id = $branch
					          ORDER BY subject ASC";
				} else {
					// For Admin/General, show all subjects for this class
					// 	$query = "SELECT subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch";
					$query = "SELECT subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch AND ifnull(show_in_report, 'YES') = 'YES'";
				}

				$res = direct_sql($query);

				if ($res['status'] === 'success' && !empty($res['data'])) {
					foreach ($res['data'] as $row) {
						// timetable uses `subject`; subject table uses `subject_name`
						$subj = $row['subject'] ?? $row['subject_name'] ?? '';
						if ($subj) {
							// Resolve subject name if it's an ID
							if (is_numeric($subj)) {
								$sub_info = get_data('subject', $subj);
								$subj_name = ($sub_info['count'] > 0) ? $sub_info['data']['subject_name'] : $subj;
							} else {
								$subj_name = $subj;
							}
							$options .= "<option value='" . htmlspecialchars($subj) . "'>" . htmlspecialchars($subj_name) . "</option>";
						}
					}
				}
			}
			echo $options;
			break;

		case "update_timetable":
			extract($_POST);
			// Expects: student_class, student_section, day, period, subject, teacher_id
			$branch_id = $_SESSION['branch_id'] ?? 1;
			$user_id = $_SESSION['user_id'] ?? 0;
			$user_type = $_SESSION['user_type'] ?? '';

			if ($user_type == 'TEACHER') {
				$emp = get_teacher_employee();
				$teacher_id = $emp['id'] ?? null;
			}

			// ENFORCE: A teacher can only teach a single class during any given Day and Period!
			$conflict_q = "SELECT student_class, student_section FROM timetable 
			               WHERE teacher_id = '$teacher_id' 
			               AND day = '$day' 
			               AND period = '$period' 
			               AND status = 'ACTIVE' 
			               AND branch_id = '$branch_id'
			               AND NOT (student_class = '$student_class' AND student_section = '$student_section')
			               LIMIT 1";
			$conflict_res = direct_sql($conflict_q);
			if ($conflict_res['count'] > 0) {
				$c_class = $conflict_res['data'][0]['student_class'];
				$c_section = $conflict_res['data'][0]['student_section'];
				echo json_encode([
					'status' => 'error',
					'msg' => 'Conflict! This teacher is already scheduled to teach Class ' . $c_class . '-' . $c_section . ' on ' . $day . ', Period ' . $period . '.'
				]);
				break;
			}

			$timetable_id = $_POST['timetable_id'] ?? '';

			// Get subject name of the new assignment
			$sub_name_q = "SELECT subject_name FROM subject WHERE id = '$subject' OR subject_name = '$subject' LIMIT 1";
			$sub_name_res = direct_sql($sub_name_q, 'get', false);
			$subject_name = $sub_name_res['count'] > 0 ? strtolower($sub_name_res['data'][0]['subject_name']) : strtolower($subject);
			$is_optional_lang = (strpos($subject_name, 'sanskrit') !== false || strpos($subject_name, 'urdu') !== false);

			// ENFORCE: A class-section can only have a single scheduled subject/teacher during any given Day and Period!
			// Exception: Up to two teachers for Sanskrit and Urdu
			$class_conflict_q = "SELECT t.id, t.teacher_id, t.subject, e.name as teacher_name 
			                     FROM timetable t 
			                     LEFT JOIN employee e ON t.teacher_id = e.id
			                     WHERE t.student_class = '$student_class' 
			                     AND t.student_section = '$student_section' 
			                     AND t.day = '$day' 
			                     AND t.period = '$period' 
			                     AND t.status = 'ACTIVE' 
			                     AND t.branch_id = '$branch_id'";
			if (!empty($timetable_id)) {
				$class_conflict_q .= " AND t.id != '$timetable_id'";
			}
			$class_conflict_res = direct_sql($class_conflict_q, 'get', false);

			if ($class_conflict_res['count'] > 0) {
				$conflict = false;
				$c_teacher = '';
				$c_subject = '';

				if ($is_optional_lang && $class_conflict_res['count'] == 1) {
					// Check if the already assigned subject is also Sanskrit or Urdu
					$c_sub_val = $class_conflict_res['data'][0]['subject'];
					$c_sub_name_q = "SELECT subject_name FROM subject WHERE id = '$c_sub_val' OR subject_name = '$c_sub_val' LIMIT 1";
					$c_sub_name_res = direct_sql($c_sub_name_q, 'get', false);
					$c_subject_name = $c_sub_name_res['count'] > 0 ? strtolower($c_sub_name_res['data'][0]['subject_name']) : strtolower($c_sub_val);

					$c_is_optional_lang = (strpos($c_subject_name, 'sanskrit') !== false || strpos($c_subject_name, 'urdu') !== false);

					if (!$c_is_optional_lang) {
						$conflict = true;
						$c_teacher = $class_conflict_res['data'][0]['teacher_name'] ?: 'another teacher';
						$c_subject = $c_sub_val;
					} else {
						// Check if same teacher is already assigned
						if ($class_conflict_res['data'][0]['teacher_id'] == $teacher_id) {
							$conflict = true;
							$c_teacher = $class_conflict_res['data'][0]['teacher_name'];
							$c_subject = $c_sub_val;
						}
					}
				} else {
					$conflict = true;
					$c_teacher = $class_conflict_res['data'][0]['teacher_name'] ?: 'another teacher';
					$c_subject = $class_conflict_res['data'][0]['subject'];
				}

				if ($conflict) {
					echo json_encode([
						'status' => 'error',
						'msg' => 'Conflict! This class period is already scheduled for ' . $c_subject . ' with ' . $c_teacher . '.'
					]);
					break;
				}
			}

			$data = [
				'student_class' => $student_class,
				'student_section' => $student_section,
				'day' => $day,
				'period' => $period,
				'subject' => $subject,
				'teacher_id' => $teacher_id,
				'status' => 'ACTIVE',
				'branch_id' => $branch_id
			];

			if (!empty($timetable_id)) {
				$data['updated_by'] = $user_id;
				$res = update_data('timetable', $data, $timetable_id);
			} else {
				$data['created_by'] = $user_id;
				$res = insert_data('timetable', $data);
			}
			echo json_encode($res);
			break;

		// 		case "clear_timetable_period":
// 			extract($_POST);
// 			// Expects: student_class, student_section, day, period, timetable_id (optional)
// 			$branch_id = $_SESSION['branch_id'] ?? 1;
// 			$user_type = $_SESSION['user_type'] ?? '';
// 			$timetable_id = $_POST['timetable_id'] ?? '';

		// 			if ($user_type == 'TEACHER') {
// 				$emp = get_teacher_employee();
// 				$teacher_id = $emp['id'] ?? null;

		// 				if (!empty($timetable_id)) {
// 					$check_q = "SELECT teacher_id FROM timetable WHERE id='$timetable_id' AND branch_id='$branch_id' LIMIT 1";
// 				} else {
// 					$check_q = "SELECT teacher_id FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND day='$day' AND period='$period' AND branch_id='$branch_id' LIMIT 1";
// 				}
// 				$check_res = direct_sql($check_q);
// 				if ($check_res['count'] > 0 && $check_res['data'][0]['teacher_id'] != $teacher_id) {
// 					echo json_encode(['status' => 'error', 'msg' => 'You can only clear your own scheduled periods.']);
// 					break;
// 				}
// 			}

		// 			if (!empty($timetable_id)) {
// 				$query = "DELETE FROM timetable WHERE id='$timetable_id' AND branch_id='$branch_id'";
// 			} else {
// 				$query = "DELETE FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND day='$day' AND period='$period' AND branch_id='$branch_id'";
// 			}
// 			$res = direct_sql($query);

		// 			echo json_encode(['status' => 'success', 'msg' => 'Period cleared successfully']);
// 			break;

		case "clear_timetable_period":
			extract($_POST);
			// Expects: student_class, student_section, day, period, timetable_id (optional)
			$branch_id = $_SESSION['branch_id'] ?? 1;
			$user_type = $_SESSION['user_type'] ?? '';
			$timetable_id = $_POST['timetable_id'] ?? '';

			if ($user_type == 'TEACHER') {
				$emp = get_teacher_employee();
				$teacher_id = $emp['id'] ?? null;

				if (!empty($timetable_id)) {
					$check_q = "SELECT teacher_id FROM timetable WHERE id='$timetable_id' AND branch_id='$branch_id' LIMIT 1";
				} else {
					$check_q = "SELECT teacher_id FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND day='$day' AND period='$period' AND branch_id='$branch_id' LIMIT 1";
				}
				$check_res = direct_sql($check_q);
				if ($check_res['count'] > 0 && $check_res['data'][0]['teacher_id'] != $teacher_id) {
					echo json_encode(['status' => 'error', 'msg' => 'You can only clear your own scheduled periods.']);
					break;
				}
			}

			if (!empty($timetable_id)) {
				$query = "DELETE FROM timetable WHERE id='$timetable_id' AND branch_id='$branch_id'";
			} else {
				$query = "DELETE FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND day='$day' AND period='$period' AND branch_id='$branch_id'";
			}
			$res = direct_sql($query, 'set');

			echo json_encode(['status' => 'success', 'msg' => 'Period cleared successfully']);
			break;


		case "add_logbook_period":
			extract($_POST);
			// Expects: logbook_id, period_no, subject, teacher_id, topics, homework
			$branch_id = $_SESSION['branch_id'] ?? 1;

			$data = [
				'logbook_id' => $logbook_id,
				'period_no' => $period_no,
				'subject' => $subject,
				'teacher_id' => $teacher_id,
				'topics' => $topics ?? '',
				'homework' => $homework ?? '',
				'status' => 'PENDING',
				'branch_id' => $branch_id
			];

			$check = get_all('logbook_period', ['id'], ['logbook_id' => $logbook_id, 'period_no' => $period_no]);
			if ($check['count'] > 0) {
				echo json_encode(['status' => 'error', 'msg' => 'Period ' . $period_no . ' already exists in this logbook.']);
				break;
			}

			$res = insert_data('logbook_period', $data);
			echo json_encode($res);
			break;

		case "save_class_teacher":
			extract($_POST);
			// Expects: teacher_id, class_teacher
			if (empty($teacher_id)) {
				echo json_encode(['status' => 'error', 'msg' => 'Teacher ID not found. Please log out and log in again.']);
				break;
			}

			$teacher_id_esc = mysqli_real_escape_string($con, $teacher_id);
			if ($class_teacher === '') {
				$query = "UPDATE employee SET class_teacher = NULL WHERE id = '$teacher_id_esc'";
				$q_res = mysqli_query($con, $query);
				if ($q_res) {
					$res = ['status' => 'success', 'msg' => 'Assignment deleted successfully'];
				} else {
					$res = ['status' => 'error', 'msg' => 'Database error: ' . mysqli_error($con)];
				}
			} else {
				$class_teacher_esc = mysqli_real_escape_string($con, $class_teacher);
				$query = "UPDATE employee SET class_teacher = '$class_teacher_esc' WHERE id = '$teacher_id_esc'";
				$q_res = mysqli_query($con, $query);
				if ($q_res) {
					$res = ['status' => 'success', 'msg' => 'Assignment updated successfully'];
				} else {
					$res = ['status' => 'error', 'msg' => 'Database error: ' . mysqli_error($con)];
				}
			}
			echo json_encode($res);
			break;

		case "get_class_ut_tests":
			$branch = intval($_SESSION['branch_id'] ?? 1);

			$options = "<option value=''>Select UT</option>";

			$query = "SELECT ut_number, date, max_marks FROM ut_tests 
			          WHERE (student_class = '' OR student_class IS NULL)
			          AND (student_section = '' OR student_section IS NULL)
			          AND (subject = '' OR subject IS NULL)
			          AND status = 'ACTIVE' 
			          AND branch_id = $branch 
			          ORDER BY ut_number ASC";
			$res = direct_sql($query);

			if ($res['status'] === 'success' && !empty($res['data'])) {
				foreach ($res['data'] as $row) {
					$ut_num = $row['ut_number'];
					$ut_date = date('d-M-Y', strtotime($row['date']));
					$max_m = number_format($row['max_marks'], 0);
					$disp = "UT Test $ut_num (Max: $max_m, Date: $ut_date)";
					$options .= "<option value='$ut_num'>$disp</option>";
				}
			}
			echo $options;
			break;

		case "update_ut_test":
			$class_subject_arr = $_POST['class_subject'] ?? null;
			unset($_POST['class_subject']);
			unset($_POST['isedit']);
			unset($_POST['id']);

			extract($_POST);

			$branch_id = $_SESSION['branch_id'] ?? 1;
			$_POST['status'] = $_POST['status'] ?? 'ACTIVE';
			$_POST['student_class'] = ''; // Enforce empty class for global scheduled records
			$_POST['student_section'] = ''; // Enforce empty section for global scheduled records
			$_POST['subject'] = ''; // Enforce empty subject for global scheduled records

			$res = update_data('ut_tests', $_POST, $id);

			// Handle class-wise subjects if provided
			if (isset($class_subject_arr) && is_array($class_subject_arr)) {
				$ut_num_esc = mysqli_real_escape_string($con, $ut_number);
				$date_esc = mysqli_real_escape_string($con, $date);
				$status_esc = mysqli_real_escape_string($con, $_POST['status']);
				$max_marks_esc = mysqli_real_escape_string($con, $max_marks ?? 20);

				foreach ($class_subject_arr as $cls_name => $sub_name) {
					$cls_name_esc = mysqli_real_escape_string($con, $cls_name);
					$sub_name_esc = mysqli_real_escape_string($con, $sub_name);

					// Check if there is an existing record for this class under this UT number
					$chk_q = "SELECT id FROM ut_tests 
					          WHERE ut_number = '$ut_num_esc' 
					          AND student_class = '$cls_name_esc' 
					          AND (student_section = '' OR student_section IS NULL) 
					          AND branch_id = '$branch_id' LIMIT 1";
					$chk_res = direct_sql($chk_q);

					if (!empty($sub_name)) {
						if ($chk_res['count'] > 0) {
							$row_id = $chk_res['data'][0]['id'];
							$up_q = "UPDATE ut_tests SET 
							         subject = '$sub_name_esc', 
							         date = '$date_esc', 
							         status = '$status_esc', 
							         max_marks = '$max_marks_esc'
							         WHERE id = '$row_id'";
							direct_sql($up_q, 'set');
						} else {
							$ins_q = "INSERT INTO ut_tests (ut_number, student_class, student_section, subject, date, status, max_marks, branch_id) 
							          VALUES ('$ut_num_esc', '$cls_name_esc', '', '$sub_name_esc', '$date_esc', '$status_esc', '$max_marks_esc', '$branch_id')";
							direct_sql($ins_q, 'set');
						}
					} else {
						// Subject is empty, delete the record if it exists
						if ($chk_res['count'] > 0) {
							$row_id = $chk_res['data'][0]['id'];
							$del_q = "DELETE FROM ut_tests WHERE id = '$row_id'";
							direct_sql($del_q, 'set');
						}
					}
				}
			} else {
				// If class_subject array is not passed (e.g. simple date/max marks update), sync all existing class records
				if ($id > 0) {
					$orig_res = get_data('ut_tests', $id);
					if ($orig_res['count'] > 0 && $orig_res['status'] == 'success') {
						$orig = $orig_res['data'];
						$up_q = "UPDATE ut_tests SET 
						         max_marks = '" . mysqli_real_escape_string($con, $max_marks) . "', 
						         date = '" . mysqli_real_escape_string($con, $date) . "',
						         status = '" . mysqli_real_escape_string($con, $_POST['status']) . "'
						         WHERE ut_number = '{$orig['ut_number']}'
						         AND student_class != '' AND student_class IS NOT NULL
						         AND branch_id = '$branch_id'";
						direct_sql($up_q, 'set');
					}
				}
			}

			$res['url'] = 'manage_ut_tests';
			echo json_encode($res);
			break;

		case "get_students_by_class":
			$cls = trim($_GET['class_name'] ?? '');
			$sec = trim($_GET['section_name'] ?? '');
			$branch = intval($_SESSION['branch_id'] ?? 1);
			$res = ['status' => 'error', 'data' => []];

			if ($cls && $sec) {
				$cls = addslashes($cls);
				$sec = addslashes($sec);
				$q = direct_sql("SELECT id, name, roll_no FROM student WHERE student_class = '$cls' AND student_section = '$sec' AND status = 'ACTIVE' AND branch_id = $branch ORDER BY name ASC");
				if ($q['status'] === 'success') {
					$res['status'] = 'success';
					$res['data'] = $q['data'];
				}
			}
			header('Content-Type: application/json');
			echo json_encode($res);
			break;

		case "update_admission_phase":
			extract($_POST);
			unset($_POST['isedit']);
			$_POST['branch_id'] = $_SESSION['branch_id'] ?? 1;
			$_POST['status'] = $_POST['status'] ?? 'ACTIVE';

			if ($id > 0) {
				$res = update_data('admission_phase', $_POST, $id);
			} else {
				$res = insert_data('admission_phase', $_POST);
			}
			$res['url'] = 'manage_admission_phases';
			echo json_encode($res);
			break;

		case "get_booked_class_sections":
			$day = trim($_POST['day'] ?? '');
			$period = trim($_POST['period'] ?? '');
			$branch_id = $_SESSION['branch_id'] ?? 1;

			if (!$day || !$period) {
				echo json_encode([]);
				break;
			}

			$day_esc = mysqli_real_escape_string($con, $day);
			$period_esc = mysqli_real_escape_string($con, $period);
			$branch_id_esc = mysqli_real_escape_string($con, $branch_id);

			$q = "SELECT t.student_class, t.student_section, t.subject, t.teacher_id, e.name as teacher_name 
			      FROM timetable t 
			      LEFT JOIN employee e ON t.teacher_id = e.id 
			      WHERE t.day = '$day_esc' 
			      AND t.period = '$period_esc' 
			      AND t.status = 'ACTIVE' 
			      AND t.branch_id = '$branch_id_esc'";

			$res = direct_sql($q);
			$booked = [];
			if ($res['status'] === 'success' && !empty($res['data'])) {
				foreach ($res['data'] as $row) {
					$booked[] = [
						'class' => $row['student_class'],
						'section' => $row['student_section'],
						'subject' => $row['subject'],
						'teacher_id' => $row['teacher_id'],
						'teacher' => $row['teacher_name'] ?? 'Unknown'
					];
				}
			}
			echo json_encode($booked);
			break;

		// 		case "save_exam_tabulation_marks":
// 			extract($_POST);
// 			$user_id = $_SESSION['user_id'];
// 			$branch_id = get_data('user', $user_id, 'branch_id')['data'] ?? $_SESSION['branch_id'] ?? 1;


		// 			if (strtoupper($_SESSION['user_type'] ?? '') == 'TEACHER') {
// 				$emp = get_teacher_employee();
// 				$teacher_id = $emp['id'] ?? 0;

		// 				// Verify if this teacher is assigned to this subject, class, and section
// 				$student = get_data('student', $student_id);
// 				if ($student['count'] > 0) {
// 					$class = $student['data']['student_class'];
// 					$section = $student['data']['student_section'];

		// 					$check_assignment = get_all('teacher_subject', 'id', [
// 						'teacher_id' => $teacher_id,
// 						'class_name' => $class,
// 						'section_name' => $section,
// 						'subject_name' => $subject,
// 						'status' => 'ACTIVE',
// 						'branch_id' => $branch_id
// 					]);

		// 					if ($check_assignment['count'] == 0) {
// 						echo json_encode(['status' => 'error', 'msg' => 'You do not have permission to enter marks for this subject and class/section.']);
// 						break;
// 					}
// 				} else {
// 					echo json_encode(['status' => 'error', 'msg' => 'Student not found.']);
// 					break;
// 				}
// 			}

		// 			// Resolve exam name to infer the term if not present
// 			$exam_name = get_data('exam_list', $exam_id, 'exam_name')['data'] ?? '';
// 			$term = '';
// 			if ($exam_name == 'UNIT TEST 1' || $exam_name == 'UNIT TEST 2') {
// 				$term = 'TERM 1';
// 			} elseif ($exam_name == 'UNIT TEST 3' || $exam_name == 'UNIT TEST 4') {
// 				$term = 'TERM 2';
// 			} else {
// 				$term = $exam_name;
// 			}

		// 			$where = [
// 				'student_id' => $student_id,
// 				'student_admission' => $student_admission,
// 				'exam_id' => $exam_id,
// 				'subject' => $subject,
// 				'branch_id' => $branch_id
// 			];

		// 			// Backend dynamic max marks validation
// 			$student_data = get_data('student', $student_id)['data'] ?? [];
// 			$student_class = $student_data['student_class'] ?? '';
// 			if ($student_class != '' && $value !== '') {
// 				$max_allowed = get_pt_max_mark($student_class, $subject, $col_name, $branch_id);
// 				if (is_numeric($max_allowed)) {
// 					if ((float)$value > (float)$max_allowed) {
// 						echo json_encode(['status' => 'error', 'msg' => "Invalid marks! Maximum allowed marks for this column is $max_allowed."]);
// 						break;
// 					}
// 				}
// 			}

		// 			$search = get_all('tabulation', ['id'], $where);
// 			if ($search['count'] > 0) {
// 				$res = update_multi_data('tabulation', [$col_name => $value, 'term' => $term], $where);
// 			} else {
// 				$res = insert_data('tabulation', [
// 					'student_id' => $student_id,
// 					'student_admission' => $student_admission,
// 					'exam_id' => $exam_id,
// 					'term' => $term,
// 					'subject' => $subject,
// 					$col_name => $value,
// 					'branch_id' => $branch_id,
// 					'status' => 'ACTIVE'
// 				]);
// 			}
// 			echo json_encode($res);
// 			break;

		case "save_exam_tabulation_marks":
			extract($_POST);
			$user_id = $_SESSION['user_id'];
			$branch_id = get_data('user', $user_id, 'branch_id')['data'] ?? $_SESSION['branch_id'] ?? 1;

			if (strtoupper($_SESSION['user_type'] ?? '') == 'TEACHER') {
				$emp = get_teacher_employee();
				$teacher_id = $emp['id'] ?? 0;

				// Verify if this teacher is assigned to this subject, class, and section
				$student = get_data('student', $student_id);
				if ($student['count'] > 0) {
					$class = $student['data']['student_class'];
					$section = $student['data']['student_section'];

					$class_esc = mysqli_real_escape_string($con, (string) $class);
					$section_esc = mysqli_real_escape_string($con, (string) $section);
					$subject_esc = mysqli_real_escape_string($con, (string) $subject);

					$check_q = "SELECT t.id FROM timetable t 
					            JOIN subject s ON (t.subject = s.subject_name OR t.subject = CONVERT(s.id, CHAR)) 
					            WHERE t.teacher_id = '$teacher_id' 
					            AND t.student_class = '$class_esc' 
					            AND t.student_section = '$section_esc' 
					            AND s.id = '$subject_esc'
					            AND t.status = 'ACTIVE' 
					            AND t.branch_id = '$branch_id' 
					            LIMIT 1";
					$check_assignment = direct_sql($check_q, 'get', false);

					if ($check_assignment['count'] == 0) {
						echo json_encode(['status' => 'error', 'msg' => 'You do not have permission to enter marks for this subject and class/section.']);
						break;
					}
				} else {
					echo json_encode(['status' => 'error', 'msg' => 'Student not found.']);
					break;
				}
			}

			// Resolve exam name to infer the term if not present
			$exam_name = get_data('exam_list', $exam_id, 'exam_name')['data'] ?? '';
			$term = '';
			if ($exam_name == 'UNIT TEST 1' || $exam_name == 'UNIT TEST 2') {
				$term = 'TERM 1';
			} elseif ($exam_name == 'UNIT TEST 3' || $exam_name == 'UNIT TEST 4') {
				$term = 'TERM 2';
			} else {
				$term = $exam_name;
			}

			$where = [
				'student_id' => $student_id,
				'student_admission' => $student_admission,
				'exam_id' => $exam_id,
				'subject' => $subject,
				'branch_id' => $branch_id
			];

			// Backend dynamic max marks validation
			$student_data = get_data('student', $student_id)['data'] ?? [];
			$student_class = $student_data['student_class'] ?? '';

			$exam_name_lower = strtolower(get_data('exam_list', $exam_id, 'exam_name')['data'] ?? '');
			$is_periodic = (strpos($exam_name_lower, 'periodic') !== false || strpos($exam_name_lower, 'pt') !== false);

			if ($student_class != '' && $value !== '') {
				if (!$is_periodic && in_array($col_name, ['nb', 'ma', 'mo'])) {
					$mm_res = direct_sql("SELECT nb, ma, mo FROM max_marks WHERE class='$student_class' AND exam_id='$exam_id' AND (subject_id='$subject' OR subject_id='' OR subject_id IS NULL) AND branch_id='$branch_id' ORDER BY subject_id DESC LIMIT 1");
					$max_allowed = ($mm_res['count'] > 0) ? $mm_res['data'][0][$col_name] : '-';
				} else {
					$max_allowed = get_pt_max_mark($student_class, $subject, $col_name, $branch_id);
				}

				if (is_numeric($max_allowed)) {
					if ((float) $value > (float) $max_allowed) {
						echo json_encode(['status' => 'error', 'msg' => "Invalid marks! Maximum allowed marks for this column is $max_allowed."]);
						break;
					}
				}
			}

			$search = get_all('tabulation', ['id'], $where);
			if ($search['count'] > 0) {
				$res = update_multi_data('tabulation', [$col_name => $value, 'term' => $term], $where);
			} else {
				$res = insert_data('tabulation', [
					'student_id' => $student_id,
					'student_admission' => $student_admission,
					'exam_id' => $exam_id,
					'term' => $term,
					'subject' => $subject,
					$col_name => $value,
					'branch_id' => $branch_id,
					'status' => 'ACTIVE'
				]);
			}
			echo json_encode($res);
			break;

		case "bulk_import_teacher":
			// Allowed mime types
			$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

			$change = $new = 0;
			if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$csvFile = fopen($_FILES['file']['tmp_name'], 'r');
					$col_list = array_map('trim', fgetcsv($csvFile));
					while (($line = fgetcsv($csvFile)) !== FALSE) {
						$all_data = array_combine($col_list, $line);

						$emp_code = $all_data['emp_code'] ?? '';
						if (empty($emp_code))
							continue;

						$user_name = strtolower($emp_code);
						$password = $all_data['password'] ?? '123456';
						$user_pass = md5($password);
						$full_name = $all_data['name'] ?? '';
						$email = $all_data['email'] ?? '';
						$mobile = $all_data['mobile'] ?? '';

						$user_res = get_data('user', $user_name, null, 'user_name');
						if ($user_res['count'] > 0) {
							update_data('user', ['user_type' => 'TEACHER', 'user_pass' => $user_pass, 'user_name' => $user_name, 'full_name' => $full_name, 'user_email' => $email, 'user_mobile' => $mobile, 'status' => 'ACTIVE', 'branch_id' => $_SESSION['branch_id'], 'allowed_branch' => $_SESSION['branch_id']], $user_res['data']['id']);
						} else {
							insert_data('user', ['user_type' => 'TEACHER', 'user_pass' => $user_pass, 'user_name' => $user_name, 'full_name' => $full_name, 'user_email' => $email, 'user_mobile' => $mobile, 'status' => 'ACTIVE', 'branch_id' => $_SESSION['branch_id'], 'allowed_branch' => $_SESSION['branch_id']]);
						}

						unset($all_data['password']);
						$all_data['branch_id'] = $_SESSION['branch_id'];

						$search_result = get_data('employee', $emp_code, null, 'emp_code');
						if ($search_result['count'] < 1) {
							$res = insert_data('employee', $all_data);
							if ($res['id'] != 0) {
								$new++;
							}
						} else {
							$res = update_data('employee', $all_data, $emp_code, 'emp_code');
							if ($res['status'] == 'success') {
								$change++;
							}
						}
					}
					$res = array('status' => 'success', 'change' => $change, 'new' => $new, 'msg' => " $new New Data and $change change found and updated.");
				}
			} else {
				$res = array('status' => 'error', 'change' => $change, 'new' => $new, 'msg' => 'Please upload a valid CSV file.');
			}
			echo "<script> window.location='../bulk_import?res=" . json_encode($res) . "' </script>";
			break;

		case "update_remark":
			extract($_POST);
			if (isset($_POST['subject']) && $_POST['subject'] != '') {
				$sub_id = $_POST['subject'];
				$sub_name = get_data('subject', $sub_id, 'subject_name')['data'];
				$_POST['subject_name'] = $sub_name;
			}
			$res = update_data('remark', $_POST, $id);
			$res['url'] = 'remark';
			echo json_encode($res);
			break;


		case "get_exam_entry_sections":
			$class = trim($_POST['student_class'] ?? '');
			$branch = intval($_SESSION['branch_id'] ?? 1);
			$user_type = $_SESSION['user_type'] ?? '';

			$options = "<option value=''>Select Section</option>";

			if ($class) {
				$class = addslashes($class);

				if (strtoupper($user_type) == 'TEACHER') {
					$emp = get_teacher_employee();
					$emp_id = $emp['id'] ?? 0;

					$query = "SELECT DISTINCT student_section as section_name FROM timetable 
					          WHERE teacher_id = '$emp_id' 
					          AND student_class = '$class' 
					          AND status = 'ACTIVE' 
					          AND branch_id = $branch 
					          ORDER BY student_section ASC";
				} else {
					// For Admin/General, show all active sections in the branch
					$branch_data = get_data('branch_list', $branch, null, 'branch_id')['data'] ?? [];
					$sections = explode(",", $branch_data['section_list'] ?? '');
					foreach ($sections as $section) {
						$sec = trim($section);
						if ($sec) {
							$options .= "<option value='" . htmlspecialchars($sec) . "'>" . htmlspecialchars($sec) . "</option>";
						}
					}
					echo $options;
					break;
				}

				$res = direct_sql($query);

				if ($res['status'] === 'success' && !empty($res['data'])) {
					foreach ($res['data'] as $row) {
						$sec = $row['section_name'];
						$options .= "<option value='" . htmlspecialchars($sec) . "'>" . htmlspecialchars($sec) . "</option>";
					}
				}
			}
			echo $options;
			break;

		case "get_exam_entry_subjects":
			$class = trim($_POST['student_class'] ?? '');
			$section = trim($_POST['student_section'] ?? '');
			$branch = intval($_SESSION['branch_id']);
			$user_type = $_SESSION['user_type'] ?? '';

			$options = "<option value=''>Select Subject</option>";

			if ($class && $section) {
				$class = addslashes($class);
				$section = addslashes($section);

				if (strtoupper($user_type) == 'TEACHER') {
					$emp = get_teacher_employee();
					$emp_id = $emp['id'] ?? 0;

					$query = "SELECT DISTINCT s.id, s.subject_name FROM timetable t 
					          JOIN subject s ON (t.subject = s.subject_name OR t.subject = CONVERT(s.id, CHAR)) 
					          WHERE t.teacher_id = '$emp_id' 
					          AND t.student_class = '$class' 
					          AND t.student_section = '$section' 
					          AND t.status = 'ACTIVE' 
					          AND s.status = 'ACTIVE'
					          AND s.branch_id = $branch 
					          AND t.branch_id = $branch 
					          AND ifnull(s.show_in_report, 'YES') = 'YES'
					          ORDER BY s.subject_name ASC";
					//AND FIND_IN_SET('$class', s.student_class)
				} else {
					// For Admin/General, show all subjects for this class
					// 	$query = "SELECT id, subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch ORDER BY subject_order, subject_name ASC";
					$query = "SELECT id, subject_name FROM subject WHERE FIND_IN_SET('$class', student_class) AND branch_id = $branch AND ifnull(show_in_report, 'YES') = 'YES' ORDER BY subject_order, subject_name ASC";

				}

				$res = direct_sql($query, 'get', false);

				if ($res['status'] === 'success' && !empty($res['data'])) {
					foreach ($res['data'] as $row) {
						$sub_id = $row['id'];
						$subj_name = $row['subject_name'];
						$options .= "<option value='" . htmlspecialchars((string) $sub_id) . "'>" . htmlspecialchars((string) $subj_name) . "</option>";
					}
				}
			}
			echo $options;
			break;

		case "update_pt_max_marks":
			extract($_POST);
			$res = update_data('pt_max_marks', $_POST, $id);
			$res['url'] = 'pt_max_marks';
			echo json_encode($res);
			break;

		case "update_max_marks":
			extract($_POST);
			$res = update_data('max_marks', $_POST, $id);
			$res['url'] = 'max_marks';
			echo json_encode($res);
			break;

		case "delete_max_marks":
			$id = $_GET['id'];
			$res = delete_data('max_marks', $id);
			echo "<script>alert('Data deleted successfully.'); window.location.href='../max_marks';</script>";
			break;

		case "save_co_scholastic_grades":
			$exam_id = $_POST['exam_id'] ?? '';
			$student_ids = $_POST['student_ids'] ?? [];
			$grades = $_POST['grades'] ?? [];
			$branch_id = $_SESSION['branch_id'] ?? 1;
			$user_id = $_SESSION['user_id'] ?? 0;
			$datetime = date('Y-m-d H:i:s');

			if (empty($exam_id) || empty($student_ids)) {
				echo json_encode(['status' => 'error', 'msg' => 'Missing data']);
				break;
			}

			$success_count = 0;
			foreach ($student_ids as $student_id) {
				if (isset($grades[$student_id]) && is_array($grades[$student_id])) {
					foreach ($grades[$student_id] as $subject_id => $grade) {
						if (empty($grade))
							continue; // skip empty grades

						$check_sql = "SELECT id FROM co_scholastic_grades WHERE exam_id='$exam_id' AND student_id='$student_id' AND subject_id='$subject_id'";
						$check_res = direct_sql($check_sql);

						if ($check_res['count'] > 0) {
							$record_id = $check_res['data'][0]['id'];
							$update_data = [
								'grade' => $grade,
								'updated_at' => $datetime,
								'updated_by' => $user_id
							];
							update_data('co_scholastic_grades', $update_data, $record_id);
						} else {
							$insert_data = [
								'exam_id' => $exam_id,
								'student_id' => $student_id,
								'subject_id' => $subject_id,
								'grade' => $grade,
								'branch_id' => $branch_id,
								'created_at' => $datetime,
								'created_by' => $user_id,
								'status' => 'ACTIVE'
							];
							insert_data('co_scholastic_grades', $insert_data);
						}
						$success_count++;
					}
				}
			}

			echo json_encode(['status' => 'success', 'msg' => "Saved $success_count grades"]);
			break;

		case "toggle_show_in_report":
			$id = $_POST['id'];
			$show_in_report = $_POST['show_in_report'];
			$res = update_data('subject', ['show_in_report' => $show_in_report], $id);
			echo json_encode($res);
			break;

		case "update_student_health":
			$id = (int) ($_POST['id'] ?? 0);
			$field = trim($_POST['field'] ?? '');
			$value = $_POST['value'] ?? '';
			$allowed = ['urdu', 'height', 'weight'];
			if ($id <= 0 || !in_array($field, $allowed, true)) {
				echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
				break;
			}
			if ($field == 'urdu') {
				$value = $value == '1' ? '1' : '0';
			} else {
				$value = trim((string) $value);
				if ($value !== '' && !is_numeric($value)) {
					echo json_encode(['status' => 'error', 'msg' => 'Invalid value']);
					break;
				}
			}
			$res = update_data('student', [$field => $value], $id);
			echo json_encode(['status' => $res ? 'success' : 'error', 'msg' => $res ? 'Updated' : 'Update failed']);
			break;

		default:
			echo "<script> alert('Invalid Action'); window.location ='" . $_SERVER['HTTP_REFERER'] . "' </script>";
	}
}
