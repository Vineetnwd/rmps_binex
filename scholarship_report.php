<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

if($user_type !='ADMIN' and $user_type !='DEV')
{
    echo "<script> window.location='dashboard' </script>";
}
?>
<script> 
    document.title ="Scholarship Report  ";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1>Discount Report </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#report">Report</a></li>
        <li class="breadcrumb-item active">Scholarship Report </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
						<h3 class="box-title"> Scholarship Report </h3>
						<!--<div class="box-tools pull-right">-->
						<!--	<form method="post">-->
						<!--	    <input type ='date' class='from-control' name='from_date' value='<?= $from_date ?>'>-->
						<!--	    <input type ='date' class='from-control' name='to_date' value='<?= $to_date ?>'>-->
						<!--		<button class='btn btn-orange'> Generate </button>-->
						<!--	</form>-->
						<!--</div>-->
					</div>
        <!-- /.box-header -->
	    <div class="box-body">
		 			
			                <div class="table-responsive">
                              <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead >
                                        <tr class='op-thead'>
                                            <th>Sr. No. </th>
                                            <th>Adm. No. </th>
                                            <th>Name </th>
                                            <th>Father </th>
                                            <th>Class-Section </th>
                                            <th>Roll </th>
                                            <th>Scholorship </th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									

                                   $sql="SELECT * 
FROM 
    student
WHERE
    scholarship>0
AND status='ACTIVE' 
ORDER BY 
    scholarship DESC";

$res  = direct_sql($sql);
$i =1;
foreach((array)$res['data'] as $row)
{
    $branch_name =get_data('branch_list', $fee['branch_id'],'inst_name')['data'];
    $cashier =get_data('user', $fee['created_by'],'user_name')['data'];
    $student =get_data('student', $fee['student_id'])['data'];
    $tdiscount +=$fee['discount'];
    echo "<tr>";
    echo "<td>" . $i. "</td>";
    echo "<td>" . $row['student_admission']. "</td>";
    echo "<td>" . $row['student_name']. "</td>";
    echo "<td>" . $row['student_father']. "</td>";
    echo "<td>" . $row['student_class']."-". $row['student_section']. "</td>";
    echo "<td>" . $row['student_roll']. "</td>";
    echo "<td>" . $row['scholarship']. "</td>";
    echo "</tr>";
$i++;
}
									?>
                                   	</tbody>
                                   	<!--<tfoot>-->
                                   	<!--    <tr class="bg-secondary text-light">-->
                                   	<!--        <td colspan="8"> Total discount</td>-->
                                   	<!--        <td colspan="8" >  </td>-->
                                   	<!--    </tr>-->
                                   	<!--</tfoot>-->
                                   
                                </table>
					
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       