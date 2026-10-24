<?php 
require_once('system/header.php');?>
<?php require_once('system/menu.php');
$table_name ='transport_area';
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
}
else{	
// 	$fee =insert_row('transport_area');
// 	$id = $fee['id'];
// 	$result = get_multi_data($table_name, array('created_by' => $user_id, 'status' => 'AUTO','branch_id'=>$_SESSION['branch_id']), ' order by id desc limit 1');
    $result = get_multi_data($table_name, [ 'status' => 'AUTO','branch_id'=>$_SESSION['branch_id']], ' order by id desc limit 1');
	if ($result['count'] ==0) {
		$result = insert_data($table_name, array('status' => 'AUTO', 'created_at' => $current_date_time,'branch_id'=>$_SESSION['branch_id']));
		$id = $result['id'];
	} else {
		$id = $result['data'][0]['id'];
	}
}

if($id!='')
{
  $res = direct_sql("SELECT * FROM transport_area WHERE id='$id' AND branch_id='$branch_id'");//get_data('transport_area',$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']['0']);
  }
}
?>

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Transport Area</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#transport">Transport</a></li>
        <li class="breadcrumb-item active">Transport Area</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Add and Update Transport Area <?php // echo $user_id; ?> </h3>

          <div class="box-tools pull-right">
			 <a class='fa fa-plus btn btn-info btn-sm' href='add_area' title='Add Fee Head'> </a>
			 
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
		 	
				<div class='row'>
					<div class="col-lg-3 col-sm-6">
											<form action ='update_area' id='update_frm' method ='post' enctype='multipart/form-data'>
												<input type='hidden' value='<?php echo $id; ?>' name='id'>
												<div class="form-group">
															<label> Area List*</label>
															<input class="form-control" required name='area_name' value='<?php echo $area_name; ?>'>
															
															
												</div>
												<div class="form-group">
															<label>Transportation Fee* (in Rs.)</label>
															<input class="form-control"  name='area_fee' type='number' value='<?php echo $area_fee; ?>'>
															
												</div>
												<div class="form-group">
															
												<!--<input name='update_all' value='true' type='checkbox'> Apply this change to all student related to this area-->
															
												</div>
												</form>
												<button  class="btn btn-primary" id='update_btn' > Save </button>
												
		
              
					</div>
					
                <div class="col-lg-9">
							<div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead >
                                        <tr class='op-thead'>	
                                            <th> # </th>
                                            <th> Area Name</th>
                                            <th> Tranportion Fee </th>
                                            <th> Student Count </th>
											<th> Operation </td>
                                        </tr>
                                    </thead>
                                    <tbody>
									
									<?php
									$query ="select * from transport_area where status='ACTIVE' order by area_name";
									$i=1;
									$res = direct_sql($query);
									foreach((array)$res['data'] as $row)
									{
										$id =$row['id'];
										$ct  = get_all('student','*',['area_id'=>$id,'student_type'=>'TRANSPORT','branch_id'=>$branch_id])['count'];
										echo "<tr><td>";
										echo "<input type='hidden' value='".$id."'  name='id'>". $id ."</td>";
										echo "<td>".$row['area_name'] ."</td>";	
										echo "<td>".$row['area_fee'] ."</td>";	
										echo "<td><a href='area_wise_report.php?area_id=$id' class='badge bg-dark text-light'>".$ct."</a></td>";	
										
										?>
										<td>
										    <?php echo btn_edit('add_area', $id);?>
										    <?php echo btn_delete($table_name, $id);?>
										</td>
										<?php
										$i++;
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


			
<?php require_once('system/footer2.php');?>
            

       