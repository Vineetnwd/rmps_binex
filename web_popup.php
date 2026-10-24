<?php require_once('temp/sidebar.php'); 
$table ='popup';
    $d = get_all($table,'*',['type'=>'web']);
    if ($d['count'] > 0) {
        $arr = decode($_GET['link']);
        $data  = get_data($table, $d['data'][0]['id'])['data'];
        $isedit = 'yes';
        extract($data);
    } else {
        $res  = insert_row($table);
        $id = $res['id'];
        $isedit = 'no';
        $data  = get_data($table, $id)['data'];
        extract($data);
    }
?>
   <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                     <!-- <div class="content p-4"> -->
                         <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Add an Image for Public Website</h4>
                                    <div class="page-title-right">
                              
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
            
    <div class="card mb-4">
        <div class="card-body">
                    <div class='row'>
                        <div class='col-lg-4'></div>
                        <div class='col-lg-4'>
                        <form action ='add_to_popup' method ='post' id='insert_frm' enctype='multipart/form-data'>
                
                        
                        <div class="form-group mb-3">
                            <label>Image Title</label>
                            
                            <input class="form-control" value='<?= $image_title ?>' name='image_title' required  >
                            <input class="form-control" type='hidden' value='ACTIVE' name='status'  required  >
                        </div>
                        
                        <div class="form-group mb-3">
                                            <label>Image Status</label>
                                            <select class="form-select" name='image_status' required>
                                                <?php if($image_status =='SHOW'){ 
                                                    $sslt = 'selected';
                                                    $hslt = '';
                                                 }else{ 
                                                     $hslt = 'selected';
                                                     $sslt = '';
                                                 }
                                                 ?>
                                                <option value='SHOW' <?= $sslt ?>>SHOW</option>
                                                <option value='HIDE' <?= $hslt ?>>HIDE</option>
                                            </select>
                         <input class="form-control"  name='image_url' type='hidden' id='targetimg' required>
                         <input class="form-control"  value='<?= $image_url ?>' type='hidden' id='imgp' required>
                         <input class="form-control"  name='id' type='hidden' value="<?= $id ?>" required>
                        </div>
                        <input type='hidden' name='url' value='web_popup'>
                        <input type='hidden' name='type' value='web'>
                        </form>     
                         <form id='uploadForm' enctype= 'multipart/form-data'>
                            <div id='display'></div>
                            <div class="form-group">
                                <label>Upload Photograph (Max 50 KB) </label>
                                <input type='file' class="form-control" name='uploadimg' id='uploadimg' accept='image'>
                                <br><small> Only Jpg and Png image upto 50KB. </small>
                            </div>
                        </form>
                         <button class="btn btn-danger" id='insert_btn'>Upload Image</button>
                        </div>
                        
                        </div>
            </div>
        </div>
    </div>
<!-- End Page-content -->
<?php require_once('temp/footer.php'); ?>
<script>
        const img = $('#imgp').val();
        if(img){
        $("#display").html("<img src='temp/upload/"+img +"' width='100px' height='100px' class='img-thumbnail'>");
        }else{
            
        }
</script>
