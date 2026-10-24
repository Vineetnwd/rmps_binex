<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $student = decode($_GET['link']);
    $id = $student['id'];
    $isedit = 'yes';
} else {

    $student = insert_row('career');
    $isedit = 'no';
    $id = $student['id'];
}

if ($id != '') {
	$res = get_data('career', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
		if ($student_admission == '') {
			$student_admission = $student['id'];
		}
	}
}
?>
<style>
    @media(min-width:70px){
        #uploadcv{
                margin-top:-120px;
                margin-left:535px;
        }
    }
    .checkbox{
        margin-left:10px
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> New Hiring</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Hiring</a></li>
            <li class="breadcrumb-item active">New Hiring</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Hiring Details </h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id='update_frm' action='update_hiring'>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Job Title <?= imp() ?></label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <input type='hidden' name='isedit' value='<?= $isedit; ?>' />                                    
                                    <input class="form-control border-warning" type="text" value='<?php echo $hiring_title; ?>' name="job_title" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Job Post</label>
                                <div class="col-sm-8">
                                <input class="form-control border-warning" type="text" value='<?php echo $job_post; ?>' name="job_post" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Salary</label>
                                <div class="col-sm-8">
                                    <input class="form-control border-warning" type="text" value='<?php echo $salary; ?>' name="salary" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Qualification </label>
                                <div class="col-sm-8">
                                    <input type="text" name="qualification" class="form-control" value="<?= $qualification ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Experiense <?= imp() ?></label>
                                <div class="col-sm-8">
                                    <input class="form-control border-warning" type="text" value='<?php echo $experiense; ?>' name="experiense" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Applicable Gender <?= imp() ?></label>
                                <div class="col-sm-8">
                                        <?php 
                                        $gen = get_data('career',$id,'gender')['data'];
                                            $checkboxes = [
                                                ['name' => 'gender', 'c_name' => 'MALE', 'value' => 'MALE'],
                                                ['name' => 'gender', 'c_name' => 'FEMALE', 'value' => 'FEMALE']
                                            ];                                            
                                            create_checks($checkboxes, $gen);                                            
                                        ?>
                                        
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label"> Language</label>
                                <div class="col-sm-8"> 
                                        <?php 
                                         $lang = get_data('career',$id,'language')['data'];
                                            $language_li = [
                                                ['name' => 'language', 'c_name' => 'HINDI', 'value' => 'HINDI'],
                                                ['name' => 'language', 'c_name' => 'ENGLISH', 'value' => 'ENGLISH']
                                            ];                                            
                                            create_checks($language_li, $lang);                                            
                                        ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Process <?= imp() ?></label>
                                <div class="col-sm-8">
                                        <?php 
                                         $proc = get_data('career',$id,'process')['data'];                                            
                                            $process_li = [
                                                ['name' => 'process', 'c_name'=>'ONLINE' , 'value' => 'ONLINE'],
                                                ['name' => 'process', 'c_name'=>'OFFLINE', 'value' => 'OFFLINE']
                                            ];                                            
                                            create_checks($process_li, $proc);                                            
                                        ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                <input class="form-control" type="email" value='<?php echo $email; ?>'name="email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label"> Remarks (if Any)</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" rows="2" name='remark'><?php echo $remark; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
<?php require_once('system/footer.php'); ?>