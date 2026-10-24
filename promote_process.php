<?php
require_once('system/function.php');
// if (isset($_POST['promote'])) {
//   unset($_POST['promote']);
//   $new_session = '2026-2027';
//   $all_list = post_clean($_POST);
//   foreach ($all_list  as $list) {
//     $data = decode(xss_clean($list));
//     //print_r($data);
//     extract($data);
    
//     $r =promote_student($student_admission, $promote_class, $final_dues=0);
//     print_r($r);
//   }
// } 
// echo "<script> window.location ='http://rmpublicschool.org.in/multi/promote_student.php' </script>";

 if (isset($_POST['promote'])) {
  unset($_POST['promote']);
  $new_db = 'u443617320_rmps_26';
  $new_session = '2026-2027';
  $all_list = post_clean($_POST);
  $branch_id = $_SESSION['branch_id'];
  print_r($all_list);
  foreach ($all_list  as $list) {
  
    $data = decode(xss_clean($list));
    extract($data);
    
    $con = mysqli_connect('localhost', 'u443617320_rmps_26', '@User2001', 'u443617320_rmps_26') or die("Unable to Connect, Check the Connection Parameter. " . mysqli_error($con));
    
        //   /* ======= IF COPY SUCCESS UPDATE REQUIRED INFORMATION TO NEW DB ======*/
      $udata = array('student_class' => $promote_class, 'base_dues' => $final_dues, 'student_session' => $new_session, 'created_at' => $created_at, 'status' => 'ACTIVE','admission_type'=>'OLD');
      $res2  = update_multi_data($new_db . '.student', $udata, ['student_admission'=>$student_admission,'branch_id'=>$branch_id]);
        print_r($res2);
        
       echo $sql ="UPDATE `student_fee` SET `prev_session_dues` = '$final_dues' WHERE student_admission = '$student_admission' and branch_id ='$branch_id'";
    //   $res4 = direct_sql($sql,'set');
        // print_r($res4);
        $result = mysqli_query($con, $sql);
        
        if ($result) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . mysqli_error($con);
        }
        //   /* ======= MARK THE STUDENT AS PROMOTED IN OLD DATABSE ======*/
    $con = mysqli_connect('localhost',$db_name,'@User2001',$db_name) or die("connection Error");
    // $res5 = update_data('student', array('status' => 'PROMOTED'),$student_admission,'student_admission'); 
    $sql5 ="UPDATE `student` SET `status` = 'PROMOTED' WHERE student_admission = '$student_admission' and branch_id ='$branch_id'";
      $res5 = mysqli_query($con, $sql5);
        
        if ($res5) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . mysqli_error($con);
        }
     
  }
} 
echo "<script> window.location ='promote_student.php' </script>";
?>