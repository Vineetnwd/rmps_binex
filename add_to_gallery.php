<?php require_once('system/header.php');
require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $student = decode($_GET['link']);
    $id = $student['id'];
    $isedit = 'yes';
} else {

    $student = insert_row('gallery');
    $isedit = 'no';
    $id = $student['id'];
}
if ($id != '') {
	$res = get_data('gallery', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
		if ($student_admission == '') {
			$student_admission = $student['id'];
		}
	}
}
?>
   <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> New Gallery</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Website Management</a></li>
			<li class="breadcrumb-item active">Manage Gallery</li>
		</ol>
	</section>

	<section class="content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="box box-default">
                    <div class="box-header with-border">
                    <h4 class="mb-sm-0 font-size-18">Add an Image for Web Gallery</h4>
    
                        <div class="box-tools pull-right">
                        </div>
                    </div>                      
    
                    <div class="box mb-4">
                        <div class="box-body">
                                    <div class='row'>
                                        <div class='col-lg-4'>
                                        <form action ='update_gallery' method ='post' id='insert_frm' enctype='multipart/form-data'>
                                
                                        
                                        <div class="form-group mb-3">
                                            <label>Image Title</label>
                                            
                                            <input class="form-control" value='<?= $image_title ?>' name='image_title'  required  >
                                            <input class="form-control" type='hidden' value='ACTIVE' name='status'  required  >
                                            <input class="form-control" type='hidden' value='<?= $id ?>' name='id'  required  >
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label>Image Status</label>
                                            <select class="form-control" name="image_status" required>
                                                <option value="SLIDER" <?php echo ($image_status == 'SLIDER') ? 'selected' : ''; ?>>SLIDER</option>
                                                <option value="GALLERY" <?php echo ($image_status == 'GALLERY') ? 'selected' : ''; ?>>GALLERY</option>
                                                <option value="HIDE" <?php echo ($image_status == 'HIDE') ? 'selected' : ''; ?>>HIDE</option>
                                            </select>
                                        <input class="form-control"  name='image_url' type='hidden' id='targetimg' required>
                                        </div>
                                        
                                        </form>     
                                        <form id='uploadForm' enctype= 'multipart/form-data'>
                                            <div id='display'></div>
                                            <div class="form-group">
                                                <label>Upload Photograph (Max 50 KB) </label>
                                                <input type='file' class="form-control" name='uploadimg' id='uploadimg' accept='image'>
                                                <br><small> Only Jpg and Png image upto 50KB. </small>
                                            <?php
                                                if($image_url != ''){
                                               echo  "<div><img src='$base_url/system/upload/$image_url'  style='width:100px;'></div>";
                                                }
                                            ?>
                                            </div>
                                            
                                        <button class="btn btn-danger" id='insert_btn'>Upload Image</button>
                                        </form>
                                        </div>
                                        
                            
                                        
                                    <div class="col-lg-8">
                                                
                                                <!--    Basic Table  -->
                                    <table id="data_tbl" class="table table-hover nowrap" cellspacing="0" width="100%">
                                                    <thead >
                                                        <tr>
                                                            
                                                        
                                                            <th>Image Title</th>
                                                        <!--   <th>event Details</th>-->
                                                            <th>Show Image </th>
                                                            <th>Status</th>
                                                            <th>Operation</th>
                                                            
                                                            
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $query ="select * from gallery where status='ACTIVE' order by id desc";
                                                    $res = direct_sql($query);
                                                    foreach((array) $res['data'] as $row)
                                                    {
                                                        
                                                        // print_r($row);
                                                            echo "<tr>";
                                                            $image_id=$row['id'];
                                                        //  echo "<td> ". date('d M',strtotime($row['image_date'])) ."</td>";
                                                            echo "<td> ". $row['image_title'] ."</td>";
                                                        $path="system/upload/". $row['image_url'];
                                                        echo "<td> <img src='$path' alt='$path' height='40' width='40'></td>";
                                                            echo "<td> ". $row['image_status'] ."</td>";
                                                    ?>
                                                        
                                                            
                                                        <td align='right'>
                                                        <?php echo btn_edit('add_to_gallery',$image_id); ?>
                                                        <?php echo btn_delete('gallery',$image_id); ?>
                                                    <!--<span class='delete_btn' data-table='gallery' data-id='<?php echo $image_id; ?>' data-pkey='image_id' title='Detete Image Permanently'> <i class='fa fa-trash'></i> </span>-->
                                                    </td>
                                                            </tr>
                                                    <?php
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
<!-- End Page-content -->
<?php require_once('system/footer2.php'); ?>
