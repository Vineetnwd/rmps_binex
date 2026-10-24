<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// Build conditions
$conditions = [];
$conditions['status'] = 'ACTIVE';
$conditions['branch_id'] = $branch_id ?? $_SESSION['branch_id'];

// Fetch all ACTIVE TC records first
$res = get_all('tbl_tc', '*', $conditions,'id DESC');
// Filter rows where admission or name is blank/null
$filtered = [];

if($res['count'] > 0){
    foreach($res['data'] as $row){

        if(
            !empty(trim($row['student_admission'])) &&
            !empty(trim($row['student_name']))
        ){
            $filtered[] = $row;
        }
    }
}
?>

<div class="content-wrapper">

<section class="content-header">
    <h1>TC Report</h1>
</section>

<section class="content">
<div class="row">
<div class="col-12">

<div class="box">
<div class="box-header with-border">
    <h3 class="box-title">Transfer Certificate Students</h3>
</div>

<div class="box-body">
<div class="table-responsive">

<table id="example" class="table table-bordered table-hover display nowrap">
<thead>
<tr class='op-thead'>
    <th>SN</th>
    <th>ID</th>
    <th>Adm No</th>
    <th>Student Name</th>
    <th>Father Name</th>
    <th>Mother Name</th>
    <th>DOB</th>
    <th>Last Class</th>
    <th>Higher Class</th>
    <th>Issue Date</th>
    <th>Issued By</th>
    <th>Remarks</th>
</tr>
</thead>

<tbody>

<?php
if(!empty($filtered)){
    $i=1;
    foreach($filtered as $row){
        $link = encode('student_admission='.$row['student_admission']);
        echo "<tr>";

        echo "<td>$i</td>";
        echo "<td>{$row['id']}</td>";
        echo "<td><a href='print_tc?link=$link' target='_blank' class='btn btn-info btn-sm' >{$row['student_admission']}</a></td>";
        echo "<td>{$row['student_name']}</td>";
        echo "<td>{$row['student_father']}</td>";
        echo "<td>{$row['student_mother']}</td>";

        echo "<td>";
        if(!empty($row['d_birth'])){
            echo date('d-M-Y', strtotime($row['d_birth']));
        }
        echo "</td>";

        echo "<td>{$row['last_class']}</td>";
        echo "<td>{$row['higher_class']}</td>";
        echo "<td>".date('Y-m-d', strtotime($row['doi_certificate']))."</td>";
        echo "<td>".get_data('user',$row['created_by'],'user_name')['data']."</td>";
        echo "<td>{$row['other_remarks']}</td>";

        echo "</tr>";
        $i++;
    }
}
?>

</tbody>
</table>

</div>
</div>

</div>
</div>
</div>
</section>

</div>

<?php require_once('system/footer2.php'); ?>