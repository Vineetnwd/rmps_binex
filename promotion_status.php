<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('system/header.php');
require_once('system/menu.php');
?>

<style>
input[type="checkbox"] {
    opacity: 1 !important;
    position: static !important;
}
</style>

<div class="content-wrapper">

    <section class="content-header">
        <h1>Promote Student to New Session</h1>
    </section>

    <section class="content">
        <div class="box">

            <!-- FILTER -->
            <div class="box-header with-border">
                <form method="get" style="float:right;">
                    <select name="student_class">
                        <?php dropdown($class_list, $_GET['student_class'] ?? '') ?>
                    </select>

                    <select name="student_section" onchange="this.form.submit()">
                        <?php dropdown($section_list, $_GET['student_section'] ?? '') ?>
                    </select>
                </form>
            </div>

            <div class="box-body">
                <div class="table-responsive">

<form action="promotion_status_update.php" method="post">

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Adm No</th>
    <th>Class</th>
    <th>Roll</th>
    <th>Name</th>
    <th>Status</th>
    <th>Current Dues</th>
    <th>Final Dues</th>
    <th>Reason</th>
    <th>Promotion</th>
    <th>Remark</th>
    <th>Select</th>
</tr>
</thead>

<tbody>

<?php
if (!empty($_GET['student_class']) && !empty($_GET['student_section'])) {

    $student_class   = trim($_GET['student_class']);
    $student_section = trim($_GET['student_section']);

    // ✅ MAIN FIX: EXCLUDE PROMOTED STUDENTS
    $sql = "
    SELECT s.*
    FROM student s
    LEFT JOIN promotion_status ps 
        ON ps.student_id = s.id
        AND ps.id = (
            SELECT MAX(id) 
            FROM promotion_status 
            WHERE student_id = s.id
        )
    WHERE s.student_class='$student_class'
    AND s.student_section='$student_section'
    AND s.status='PROMOTED'
    AND s.branch_id = '{$_SESSION['branch_id']}'
    AND (ps.status IS NULL OR ps.status != 'PROMOTED')
    ";

    $res = direct_sql($sql);

    $i = 1;

    if (!empty($res['data'])) {
    foreach ($res['data'] as $row) {

        $id  = $row['id'];
        $adm = $row['student_admission'];

        // Next class
        $cid = array_search($row['student_class'], $class_list);
        $promote_class = $class_list[$cid + 1] ?? $row['student_class'];

        // Fees
        $finaldues = finaldues($id);
        $current_dues = get_data('student_fee',$id,'current_dues','student_id')['data'] ?? 0;

        // Grades
        $grades = get_final_subject_grades($id, $row['student_class']);

        $e_count = 0;
        $grade_text = '';

        if(!empty($grades)){
            foreach($grades as $sub => $grade){
                $grade_text .= "$sub:$grade, ";
                if(strtoupper($grade) == 'E'){
                    $e_count++;
                }
            }
        }

        // Logic
        $reason = [];

        if($finaldues > 0){
            $reason[] = "Fee Due";
        }

        if($e_count >= 2){
            $reason[] = "E in $e_count Subjects";
        }

        $default_status = empty($reason) ? 'PROMOTED' : 'WITHHELD';
        $reason_text = implode(', ', $reason);

        // Link
        $promote_link = 'student_id='.$id.
                        '&student_admission='.$adm.
                        '&final_dues='.$finaldues.
                        '&promote_class='.$promote_class;

        echo "<tr>";

        echo "<td>$i</td>";
        echo "<td>$adm</td>";
        echo "<td>{$row['student_class']}-{$row['student_section']}</td>";
        echo "<td>{$row['student_roll']}</td>";
        echo "<td title='$grade_text'>{$row['student_name']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td align='right'>$current_dues</td>";
        echo "<td class='text-danger' align='right'>$finaldues</td>";
        echo "<td class='text-danger'>$reason_text</td>";

        // Hidden
        echo "<input type='hidden' name='student_id[$id]' value='$id'>";
        echo "<input type='hidden' name='student_admission[$id]' value='$adm'>";
        echo "<input type='hidden' name='reason[$id]' value='$reason_text'>";

        // Dropdown
        echo "<td>
            <select name='status[$id]' class='form-control status-dropdown' data-id='$id'>
                <option value='PROMOTED' ".($default_status=='PROMOTED'?'selected':'').">PROMOTED</option>
                <option value='WITHHELD' ".($default_status=='WITHHELD'?'selected':'').">WITHHELD</option>
                <option value='CONDITIONALLY PROMOTED'>CONDITIONALLY PROMOTED</option>
            </select>
        </td>";

        // Remark
        echo "<td>
            <input type='text' name='remark[$id]' id='remark_$id'
            class='form-control remark-box'
            style='display:none;'>
        </td>";

        // Checkbox
        echo "<td>
            <input type='checkbox' class='student-checkbox'
                   name='PID$id'
                   value='".encode($promote_link)."'>
        </td>";

        echo "</tr>";

        $i++;
    }
    } else {
        echo "<tr><td colspan='12' class='text-center text-danger'>No students found</td></tr>";
    }
}
?>

<!-- FOOTER -->
<tr>
<td colspan="8"></td>

<td colspan="2" align="right">
    <input type="checkbox" id="selectall">
</td>

<td colspan="2">
    <input type="submit" name="promote" value="Update Selected" class="btn btn-success btn-sm">

    <button type="button" onclick="printSelected()" class="btn btn-primary btn-sm">
        Print
    </button>
</td>
</tr>

</tbody>
</table>

</form>

                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
$(function(){

    $('.status-dropdown').change(function(){
        let id = $(this).data('id');
        let val = $(this).val();

        if(val === 'CONDITIONALLY PROMOTED'){
            $('#remark_'+id).show().attr('required', true);
        } else {
            $('#remark_'+id).hide().val('').attr('required', false);
        }
    });

    $('#selectall').change(function(){
        $('.student-checkbox').prop('checked', this.checked);
    });

});

function printSelected(){
    let ids = [];

    $('.student-checkbox:checked').each(function(){
        let id = $(this).attr('name').replace('PID','');
        ids.push(id);
    });

    if(ids.length === 0){
        alert("Select at least one student");
        return;
    }

    window.open('promotion_card.php?student_ids=' + ids.join(','), '_blank');
}
</script>