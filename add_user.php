<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
	$isedit ='yes';
}
else{	
	$fee =insert_row('user');
	$id = $fee['id'];
	$isedit ='no';
}

if($id!='')
{
  $res = get_data('user',$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
  }
}
$branch_id = $_SESSION['branch_id'];

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Manage User</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">User</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add & Manage User</h3>
                <div class="box-tools pull-right">
                   <?php if ($_SESSION['user_type'] == 'DEV' or $_SESSION['user_type'] == 'ADMIN') { ?>
                                    <button class="btn btn-dark btn-block" id='update_btn'>Save  </button>
					<?php } else { ?>
						<button class="btn btn-border border-danger btn-block" disabled>Don't Have Permission</button>
					<?php } ?>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <!-- Collapsible Form -->
                <div class="row mb-4">
                    <div class="col-12">  
                            <form action='update_user' id='update_frm' method='post' enctype='multipart/form-data'>
                                <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                <div class="row">
									
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input class="form-control" required name='full_name' value='<?php echo $full_name; ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>User Type</label>
                                            <select name='user_type' class='form-control' required>
                                                <?php dropdown($user_type_list, $user_type); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input class="form-control" type="text" id="username" name="user_name" pattern="[a-zA-Z0-9_]{4,16}" title="Username must be 4-16 characters, letters, numbers, and underscores only." value='<?php echo $user_name; ?>' required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input class="form-control" <?= ($isedit=='no')?'required':'' ?> name='user_pass' value=''>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <input class="form-control" required name='user_mobile' value='<?php echo $user_mobile; ?>' pattern="[6789][0-9]{9}" title="Please enter a valid 10-digit Indian mobile number.">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control" type='email' name='user_email' value='<?php echo $user_email; ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name='status' class='form-control' required>
                                                <?php dropdown($status_list, $status); ?>
                                            </select>
                                        </div>
                                    </div>
									<div class="col-md-4">
                                        <div class="form-group">
                                            <label>Default Branch</label>
                                            <select name='branch_id' class='form-control' required>
                                                <?= dropdown_with_key(branch_list(), $branch_id); ?>
                                            </select>
                                        </div>
                                    </div>
									<div class="col-md-4">
                                        <div class="form-group">
                                            <label>Allow Branch</label>
											<?php
											$branch_list = branch_list(); //create_list('branch_list','inst_name',['status'=>'ACTIVE'],'id');
											echo check_list('allowed_branch', $branch_list , $allowed_branch); 
											?>											
                                        </div>
                                    </div>
                                </div>                              
                            </form>                      
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th>Branch</th>
                                <th>User Type</th>
                                <th>Username</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($_SESSION['user_type']=='ADMIN' )
                            {
                                $a_branch = get_data('user',$_SESSION['user_id'],'allowed_branch')['data'];
                                $sql = "SELECT * FROM user WHERE status NOT IN ('AUTO','DELETED') AND branch_id IN ($a_branch)";
                                $res = direct_sql($sql);
                                // $res = fetch_all('user','*',['status'=>'AUTO,DELETED'],['condition_type'=>' NOT IN ']);
                            }else if($_SESSION['user_type']=='DEV'){
                                $res = fetch_all('user','*',['status'=>'AUTO,DELETED'],['condition_type'=>' NOT IN ']);
                            }
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    echo "<tr>";
                                    echo "<td>" . $branch_list[$row['branch_id']] . "</td>";
                                    echo "<td>" . $row['user_type'] . "</td>";
                                    echo "<td>" . $row['user_name'] . "</td>";
                                    echo "<td>" . $row['user_mobile'] . "</td>";
                                    echo "<td>" . $row['user_email'] . "</td>";
                                    ?>
                                    <td>
                                        <button class='login_as btn btn-warning btn-xs' data-uid='<?= $row['user_name']?>' data-code='<?= $row['user_pass']?>' ><i class='fa fa-key fa-rotate-90'></i></button>
                                        <a href='add_user.php?link=<?php echo encode('id=' . $id); ?>' class='editBtn fa fa-edit btn btn-info btn-xs'></a>
                                        <span class='delete_btn btn btn-danger btn-sm' data-table='user' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                    </td>
                                    <?php
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>


			
<?php require_once('system/footer2.php');?>

