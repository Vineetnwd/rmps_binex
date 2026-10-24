<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

$sel_branch = ($_POST['sel_branch'])??$branch_id;
$search_by = ($_POST['search_by'])??'student_admission';
$student_class = ($_POST['student_class'])??'';
$search_text = ($_POST['search_text'])??'';
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1>Gate Pass</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#fee">Reception</a></li>
        <li class="breadcrumb-item active">Student Gate Pass</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Student Gate Pass</h3>

    <!--      <div class="box-tools pull-right">-->
			 <!--<a class='fa fa-table btn btn-info btn-sm' href='add_area' title='Show Student'> </a>-->
    <!--      </div>-->
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
			
			<div class='row'>
            <div class="col-lg-12">
					<hr>
                    <!-- Advanced Tables -->
							<?php 
							
								$sql = "select * from employee where status not in ('AUTO','BLOCK') and branch_id ='{$_SESSION['branch_id']}'";
									
							?>
             
                            <div class="table-responsive">
                                <table rules='all' border='1' width='100%' cellpadding='5'>
                                    <thead>
                                        <tr class='op-thead'>
                                            <th>Emp Code</th>
											<th>Emp Name</th>
											<th>Emp Type </th>
											<th>Mobile No </th>
											<th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php 
									//echo $sql;
									$res = direct_sql($sql);
									if($res['count']>0)
									{
										foreach($res['data'] as $row)
										{
										$id =$row['id'];
										$student_admission =$row['student_admission'];
										$status = $row['status'];
										echo"<tr class='odd gradeX'>";
										echo"<td>".$row['emp_code']."</td>";
										echo"<td>".$row['name']."</td>";
										echo"<td>".$row['emp_type']."</td>";
										echo"<td>".$row['mobile']."</td>";
										echo"<td width='145'>";
									    echo "<a href='create_emp_gatepass.php?emp_id=$id' title='Gate Pass' class='btn btn-success btn-sm' name='gate_pass'>Gate Pass </a>";
										echo "</td></tr>";
										}
										
									}
									else{
										echo "<tr><td colspan='10'> 
										<div class='text-danger text-center'>No Record Found </div> </td></tr>";
									}
							
                                       ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
             </div>                           
</section>
</div>
<?php require_once('system/footer.php'); ?>
<script>
                $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $("#st_search").trigger("click");
                }
            });
</script>