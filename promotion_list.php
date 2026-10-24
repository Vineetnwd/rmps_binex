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
        <h1>Promotion Status List</h1>
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

<form method="post">

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Adm No</th>
    <th>Name</th>
    <th>Class</th>
    <th>Status</th>
    <th>Reason</th>
    <th>Remark</th>
    <th>Date</th>
    <th>Select</th>
</tr>
</thead>

<tbody>

<?php

$student_class   = $_GET['student_class'] ?? '';
$student_section = $_GET['student_section'] ?? '';

// ✅ FINAL SAFE QUERY
$sql = "
SELECT * FROM (

    SELECT 
        ps.id,
        ps.student_id,
        ps.status,
        ps.reason,
        ps.remark,
        ps.created_at,

        s.student_name,
        s.student_class,
        s.student_section,
        s.student_admission,
        s.branch_id

    FROM promotion_status ps
    LEFT JOIN student s 
        ON s.id = ps.student_id

) AS temp

WHERE temp.branch_id = '".addslashes($branch_id)."'
";

// ✅ FILTER
if(!empty($student_class)){
    $sql .= " AND temp.student_class = '".addslashes($student_class)."' ";
}

if(!empty($student_section)){
    $sql .= " AND temp.student_section = '".addslashes($student_section)."' ";
}

// ✅ FIXED ORDER BY
$sql .= " ORDER BY temp.id DESC ";

$res = direct_sql($sql);

$i = 1;

if(!empty($res['data'])){
foreach($res['data'] as $row){

    $id  = $row['student_id'];

    echo "<tr>";

    echo "<td>".$i."</td>";
    echo "<td>".$row['student_admission']."</td>";
    echo "<td>".$row['student_name']."</td>";
    echo "<td>".$row['student_class']."-".$row['student_section']."</td>";
    echo "<td>".$row['status']."</td>";
    echo "<td>".$row['reason']."</td>";
    echo "<td>".$row['remark']."</td>";
    echo "<td>".date('d-m-Y', strtotime($row['created_at']))."</td>";

    echo "<td>
        <input type='checkbox' class='student-checkbox'
               value='".$id."'>
    </td>";

    echo "</tr>";

    $i++;
}
} else {
    echo "<tr><td colspan='9' class='text-center text-danger'>No Records Found</td></tr>";
}
?>

<tr>
<td colspan="6"></td>

<td colspan="2" align="right">
    <input type="checkbox" id="selectall">
</td>

<td>
    <button type="button" onclick="printSelected()" class="btn btn-primary btn-sm">
        Print Selected
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

    $('#selectall').change(function(){
        $('.student-checkbox').prop('checked', this.checked);
    });

});

function printSelected(){

    let ids = [];

    $('.student-checkbox:checked').each(function(){
        ids.push($(this).val());
    });

    if(ids.length === 0){
        alert("Select at least one student");
        return;
    }

    window.open('promotion_card.php?student_ids=' + ids.join(','), '_blank');
}
</script>