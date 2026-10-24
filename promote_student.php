<?php require_once('system/header.php');?>
<?php require_once('system/menu.php'); 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);



?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Promote Student to New Session 
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item">Student</li>
        <li class="breadcrumb-item active">Promote Student</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
                    <!-- Advanced Tables -->
                    <div class="box">
                          <div class="box-header with-border">
                            Promote Student 
                            <div style='float:right;'>
                            <form action='promote_student.php' action='get'>
                              Fi<akey>l</akey>ter By 
                                    <select name="student_class" >
                                               <?php dropdown($class_list,$_GET['student_class'])?>
                                    </select>
                                     <select name="student_section" onchange='submit()' accesskey='l' >
                                               <?php dropdown($section_list,$_GET['student_section'])?>
                                    </select>
            				</form>
                           </div>
                        </div>
                        <div class="box-body">
                            
                           
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" >
                                    <thead>
                                        <tr>
                                            
											<th>ID</th>
											<th>Adm No.</th>
											<th>Class </th>
											<th>Roll No.</th>
											<th>Student Name</th>
											<th>Area/Fare</th>
											<th>Student Type </th>
											<th>Status </th>
											<th>Current Dues </th>
											<th>Final Dues </th>
											<th>Promote to </th>
											<th>Operation.</th>
                                        </tr>
                                    </thead>
									
                                    <tbody>
										<form action='promote_process.php' method='post'>
										<?php 
										if (isset($_GET['student_class']) and isset($_GET['student_class']))
										{
											$student_class=trim($_GET['student_class']);
											$student_section=trim($_GET['student_section']);
											
										$sql ="select * from student where student_class = '$student_class' and student_section = '$student_section' and status ='ACTIVE' ";
										$res = direct_sql($sql);
									
							        	$i=1;
										foreach($res['data'] as $row )
										{
											
										$id =$row['id'];
										$status = $row['status'];
										$dues_list ='';
										
										if(duesmonthcount($id)['count']>0)
										{
										    //$dues_list  = implode(duesmonthcount($id)['list'],',');
										}
										
										echo"<tr class='odd gradeX'>";
										$cid = array_search($row['student_class'], $class_list);
										$promote_class =$class_list[$cid+1];
										$finaldues = finaldues($id);
										$adm =$row['student_admission'];
										
										$promote_link='student_id='.$id.'&student_admission='.$adm.'&final_dues='.$finaldues.'&promote_class='.$promote_class;
										//echo"<td><a href='print_application.php?student_id=$stu_id' target='_blank'>".$row['student_name']."</a></td>";
										echo"<td>".$i."</td>";
										echo"<td>".$row['student_admission']."</td>";
										echo"<td>".$row['student_class']."-".$row['student_section']."</td>";
										echo"<td>".$row['student_roll']."</td>";
										echo"<td>".$row['student_name']."</td>";
										echo"<td align='right'>";
										if(get_data('student',$id,'student_type')['data']=='TRANSPORT')
										{
										 echo get_data('transport_area',$row['area_id'],'area_fee')['data'];
										}
										echo "</td>";
										
									
										echo"<td>".$row['student_type']."</td>";
										echo"<td>".$row['status']."</td>";
										echo"<td align='right'>".get_data('student_fee',$id,'current_dues','student_id')['data']."</td>";
										
										
										echo"<td class='text-right text-danger' title='".$dues_list."'>".$finaldues."</td>";
										//echo"<td><a title='". finaldues($id)['month']."' data-toggle='tooltip' data-placement='top' >".$finaldues."</a></td>";
										echo"<td>". $promote_class."</td>";
										
										
                                        echo"<td width='105'>";
									    create_check('PID'.$id, encode($promote_link));
									
									//	echo"<input type='checkbox' name ='link[]' value='".encode($promote_link)."'>";
									//echo "<a href='promote_process.php?link=".encode($promote_link)."'  title='Promote to Next Class ' ><button class='btn btn-warning btn-xs' >Promote </button></a>";
										
										
										echo "</td></tr>";
										$i =$i+1;
										}
										
                                       ?>
                                     </tr> 
									 <tr> 
										<td align='right' colspan='6'> 
										
										 <?php	create_check('SelectAll', encode($promote_link));
										?>
										</td>
									<td align='left' colspan='6'> 	
										<input type='submit' value=' Promote Selected ' class='btn btn-sm btn-success' name='promote'>
										</td>
									 </tr>
									 <?php } ?>
									 </form>
                                    </tbody>
									
									
                               	  </table>
				</div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<?php require_once('system/footer2.php'); ?>

<script>
   $(document).ready(function() {
          $('#selectall').change(function() {
              if(this.checked) {
                $(".fee-month").prop('checked',true);
              }
              else{
                  $(".fee-month").prop('checked',false); 
              }
    });
});
</script>
