<?php
require_once('system/function.php');

// ✅ ERROR DISPLAY
error_reporting(E_ALL);
ini_set('display_errors', 1);

// echo "<pre>";
// print_r($_POST);

if (isset($_POST['promote'])) {

    $processed = 0;

    if (!empty($_POST['student_id'])) {

        foreach ($_POST['student_id'] as $id => $student_id) {

            // ✅ ONLY PROCESS IF CHECKBOX IS CHECKED
            if (!isset($_POST['PID'.$id])) {
                continue; // skip unselected students
            }

            // 🔥 CLEAN ID
            $student_id = trim($student_id);
            if (empty($student_id)) continue;

            // 🔥 GET DATA
            $student_admission = $_POST['student_admission'][$id] ?? '';
            $reason            = $_POST['reason'][$id] ?? '';
            $status            = $_POST['status'][$id] ?? '';
            $remark            = $_POST['remark'][$id] ?? '';
            $promote_class     = $_POST['promote_class'][$id] ?? '';
            $final_dues        = $_POST['final_dues'][$id] ?? '';
            $new_session       = $_POST['new_session'] ?? date('Y');
            $created_at        = date('Y-m-d H:i:s');

            // 🔒 BASIC ESCAPE
            $student_id        = addslashes($student_id);
            $student_admission = addslashes($student_admission);
            $reason            = addslashes($reason);
            $status            = addslashes($status);
            $remark            = addslashes($remark);

            // ✅ CHECK EXIST
            $check = direct_sql("SELECT id FROM promotion_status WHERE student_id='$student_id'");

            // ✅ DATA ARRAY
            $udata = array(
                'student_id'        => $student_id,
                'student_admission' => $student_admission,
                'status'            => $status,
                'reason'            => $reason,
                'remark'            => $remark,
                'created_at'        => $created_at
            );

            // ✅ INSERT / UPDATE
            if (!empty($check['data'])) {

                $result = update_data('promotion_status', $udata, $student_id, 'student_id');

            } else {

                $result = insert_data('promotion_status', $udata);
            }

            // 🔥 LOG ALWAYS
            insert_data('promotion_status_log', $udata);

            // DEBUG
            if ($result) {
                echo "✅ Updated ID: $student_id<br>";
                $processed++;
            } else {
                echo "❌ Error for ID: $student_id<br>";
            }
        }
    }

    if ($processed == 0) {
        echo "❌ No student selected!";
    }

    echo "<script>
        alert('Promotion Completed');
        window.location.href = 'promotion_status.php';
    </script>";
    exit;
}
?>