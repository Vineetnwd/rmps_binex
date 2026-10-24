<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

if($user_type !='ADMIN' and $user_type !='DEV')
{
    echo "<script> window.location='dashboard' </script>";
}

?>
<script> 
    document.title ="Outstanding Dues Report";
</script>						
    
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1>Outstanding Dues Report</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#report">Report</a></li>
        <li class="breadcrumb-item active">Outstanding Dues Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
            Outstanding Dues Report
		  
        </div>
        <!-- /.box-header -->
	    <div class="box-body">
		 			
			                <div class="table-responsive">
                              <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
                                    <thead >
                                        <tr class='op-thead'>
                                            <th>Class Name </th>
                                            <th>Strength </th>
                                            <th>Target </th>
                                            <th>Collection </th>
                                            <th class='bg-danger text-light'>Outstanding Amount</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									    
								        foreach($class_list as $student_class)
								        {
								            
								            $month= strtolower(date('F'));
								            $strength =studentcount($student_class);
								            
								            $target = fee_target_by_month($month, $student_class);
								            
								            $collection = fee_collection_by_month($month, $student_class);
								            $per = ($target>0)? round((($collection/$target)*100),2):0;
								            $outstanding = $target - $collection;
								            echo "<tr>";
								            echo "<td>" . add_space($student_class) ."</td>";
								            echo "<td>" .$strength ."</td>";
								            echo "<td>" .$target ."</td>";
								            echo "<td>" .$collection ."</td>";
								            echo "<td class='bg-danger text-light'> ". $outstanding ." </td>";
								            echo "</tr>";
								        }
									?>
                                   	</tbody>
                                   
                                </table>
					
                            </div>
            </div>
	  </section>
</div>
<?php require_once('system/footer2.php');?>       