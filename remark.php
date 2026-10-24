<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
}
else{	
	$fee =insert_row('remark');
	$id = $fee['id'];
}

if($id!='')
{
  $res = get_data('remark',$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
  }
}
?>
<div class="content-wrapper">
<section class="content">
	<div class='box box-default'>
			<div class="box-header with-border">
			  <h3 class="box-title">Manage Remarks</h3>
			  <div class="box-tools pull-right">
				 <div class='float-right'><a class='fa fa-plus text-orange' href='remark' title='Add Remark'></a></div> 
			  </div>
			</div>
		 <div class="box-body">
				<div class="row">
					<div class="col-lg-12">
						<form action ='update_remark' method ='post' id='update_frm' enctype='multipart/form-data'>
						<input class="form-control" value='<?php echo $id; ?>' name='id' type='hidden'>
						
						<div class="row">
                            <!--<div class="col-md-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label>Class Category</label>-->
                            <!--        <select class="form-control" name='class_cat' required>-->
                            <!--            <option value='JUNIOR' <?php if(isset($class_cat) && $class_cat == 'JUNIOR') echo 'selected'; ?>>JUNIOR</option>-->
                            <!--            <option value='SENIOR' <?php if(isset($class_cat) && $class_cat == 'SENIOR') echo 'selected'; ?>>SENIOR</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <?php 
                                        echo create_dropdown('subject', [
                                            'data_source' => 'subject', 
                                            'display' => 'subject_name', 
                                            'value' => 'id',
                                            'selected' => $subject ?? '',
                                            'conditions' => ['branch_id' => $_SESSION['branch_id'] ?? 1]
                                        ], 'form-control');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name='status' required>
                                        <option value='ACTIVE' <?php if(isset($status) && $status == 'ACTIVE') echo 'selected'; ?>>ACTIVE</option>
                                        <option value='HIDE' <?php if(isset($status) && $status == 'HIDE') echo 'selected'; ?>>HIDE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>A1 Remark</label>
                                    <textarea class="form-control" name="A1" rows="2"><?php echo $A1 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>A2 Remark</label>
                                    <textarea class="form-control" name="A2" rows="2"><?php echo $A2 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>B1 Remark</label>
                                    <textarea class="form-control" name="B1" rows="2"><?php echo $B1 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>B2 Remark</label>
                                    <textarea class="form-control" name="B2" rows="2"><?php echo $B2 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>C1 Remark</label>
                                    <textarea class="form-control" name="C1" rows="2"><?php echo $C1 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>C2 Remark</label>
                                    <textarea class="form-control" name="C2" rows="2"><?php echo $C2 ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>D Remark</label>
                                    <textarea class="form-control" name="D" rows="2"><?php echo $D ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>E Remark</label>
                                    <textarea class="form-control" name="E" rows="2"><?php echo $E ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>

						<input type="submit" class="btn btn-info" value='Save Remark' id='update_btn' >
						</form>	
					</div>
                </div>

                <hr>

                <div class="row mt-4">
					<div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table" id='example1'>
                                    <thead >
                                        <tr>
                                            <!--<th>Class Cat</th>-->
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>A1</th>
                                            <th>A2</th>
                                            <th>B1</th>
                                            <th>B2</th>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$res = get_all('remark', '*', ['branch_id'=>$_SESSION['branch_id'],'status'=>'ACTIVE']);
									if($res['count']>0)
									{
									foreach($res['data'] as $row)
									{
										echo "<tr>";
										$id = $row['id'];
								// 		echo "<td> " . $row['class_cat'] . "</td>";
										echo "<td> " . $row['subject_name'] . "</td>";
										echo "<td> " . $row['status'] . "</td>";
                                        echo "<td> " . substr($row['A1'], 0, 30) . "...</td>";
                                        echo "<td> " . substr($row['A2'], 0, 30) . "...</td>";
                                        echo "<td> " . substr($row['B1'], 0, 30) . "...</td>";
                                        echo "<td> " . substr($row['B2'], 0, 30) . "...</td>";
									?>
											<td align='right'>
											<a href='remark?link=<?php echo encode('id='.$id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
										    <span class='delete_btn btn btn-danger btn-sm' data-table='remark' data-id='<?php echo $id;?>' data-pkey='id'><i class='fa fa-trash'></i></span>
											</tr>
									<?php } }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
        </div>
        </div>
    </section>
</div>
<?php require_once('system/footer2.php');?>
