<?php
require_once('system/header.php');
require_once('system/menu.php');

if(isset($_GET['link']) and $_GET['link']!='') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {	
	$fee = insert_row('project');
	$id = $fee['id'];
}

if($id!='') {
  $res = get_data('project', $id);
  if($res['count'] > 0 && $res['status'] == 'success') {
	  extract($res['data']);
  }
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Projects</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Website Management</li>
            <li class="breadcrumb-item active">Manage Projects</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Manage Projects Settings</h3>
                        <div class="box-tools pull-right">
                            <div class='float-right'><a class='fa fa-plus text-orange btn btn-xs btn-outline-warning' href='manage_project' title='Add New Config'> Add New Project</a></div> 
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action='system/master_process.php?task=update_project' method='post' id='update_frm' enctype='multipart/form-data'>
                                    <input class="form-control" value='<?php echo $id; ?>' name='id' type='hidden'>
                                    <input type="hidden" name="branch_id" value="<?php echo $_SESSION['branch_id'] ?? 1; ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Project Name</label>
                                                <input type="text" class="form-control" name="project_name" required value="<?php echo htmlspecialchars($project_name ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date ?? ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date ?? ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Project Status</label>
                                                <select class="form-control" name='status' required>
                                                    <option value='PENDING' <?php if(isset($status) && $status == 'PENDING') echo 'selected'; ?>>PENDING</option>
                                                    <option value='ONGOING' <?php if(isset($status) && $status == 'ONGOING') echo 'selected'; ?>>ONGOING</option>
                                                    <option value='COMPLETED' <?php if(isset($status) && $status == 'COMPLETED') echo 'selected'; ?>>COMPLETED</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Visibility Status</label>
                                                <select class="form-control" name='status_flag' required>
                                                    <option value='ACTIVE' <?php if(isset($status_flag) && $status_flag == 'ACTIVE') echo 'selected'; ?>>ACTIVE</option>
                                                    <option value='HIDE' <?php if(isset($status_flag) && $status_flag == 'HIDE') echo 'selected'; ?>>HIDE</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-info" id='update_btn'>Save Project</button>
                                        <a href="manage_project" class="btn btn-default">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class='op-thead'>
                                                <th>Project Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Visibility</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $res = get_all('project', '*', ['branch_id' => $_SESSION['branch_id'] ?? 1]);
                                            if($res['count'] > 0) {
                                                foreach($res['data'] as $row) {
                                                    if(empty($row['project_name'])) continue;
                                                    
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['project_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                                                    
                                                    // Status badge
                                                    if($row['status'] == 'COMPLETED')
                                                        echo "<td><span class='badge bg-green'>Completed</span></td>";
                                                    else if($row['status'] == 'ONGOING')
                                                        echo "<td><span class='badge bg-blue'>Ongoing</span></td>";
                                                    else
                                                        echo "<td><span class='badge bg-yellow'>Pending</span></td>";
                                                        
                                                    // Visibility badge
                                                    if($row['status_flag'] == 'ACTIVE')
                                                        echo "<td><span class='badge bg-green'>Active</span></td>";
                                                    else
                                                        echo "<td><span class='badge bg-red'>Hide</span></td>";
                                                        
                                                    $link = encode('id='.$row['id']);
                                                    echo "<td>
                                                            <a href='manage_project?link=$link' class='btn btn-xs btn-info'><i class='fa fa-edit'></i></a>
                                                          </td>";
                                                    echo "</tr>";
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
            </div>
        </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>
