<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
$table_name = 'branch_list';
if (isset($_GET['link']) && $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
    $isedit = 'yes';
} else {
    $branch = insert_row($table_name);
    $isedit = 'no';
    $id = $branch['id'];
}

if ($id != '') {
    $res = get_data($table_name, $id);
    if ($res['count'] > 0 && $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>

<style>
     .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .form-group label {
            font-weight: 500;
            color: #555;
        }
</style>
<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>New Branch</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Branches</a></li>
            <li class="breadcrumb-item active">New Branch</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Branch Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class="fa fa-save"></i> Save</button>
                </div>
            </div>

            <div class="box-body">
        <form id="update_frm" action="update_branch" enctype="multipart/form-data">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <div class="form-section-title">Basic Information</div>
                    <div class="form-group row">
                        <label for="full_name" class="col-sm-4 col-form-label required">Full Name</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <input type="hidden" name="isedit" value="<?= $isedit; ?>">
                            <input type="text" name="full_name" value="<?= $full_name; ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inst_name" class="col-sm-4 col-form-label required">Institute Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="inst_name" value="<?= $inst_name; ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="school_code" class="col-sm-4 col-form-label">School Code</label>
                        <div class="col-sm-8">
                            <input type="text" name="school_code" value="<?= $school_code; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="aff_no" class="col-sm-4 col-form-label">Affiliation Number</label>
                        <div class="col-sm-8">
                            <input type="text" name="aff_no" value="<?= $aff_no; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inst_contact" class="col-sm-4 col-form-label required">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="tel" name="inst_contact" value="<?= $inst_contact; ?>" class="form-control" pattern="[6789][0-9]{9}" required maxlength="10" minlength="10">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inst_url" class="col-sm-4 col-form-label">Website URL</label>
                        <div class="col-sm-8">
                            <input type="url" name="inst_url" value="<?= $inst_url; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inst_address1" class="col-sm-4 col-form-label required">Address Line 1</label>
                        <div class="col-sm-8">
                            <textarea name="inst_address1" class="form-control" rows="3" required><?= $inst_address1; ?></textarea>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-lg-6">
                    <div class="form-section-title">Additional Details</div>
                    <div class="form-group row">
                        <label for="inst_address2" class="col-sm-4 col-form-label">Address Line 2</label>
                        <div class="col-sm-8">
                            <textarea name="inst_address2" class="form-control" rows="3"><?= $inst_address2; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inst_email" class="col-sm-4 col-form-label">Email Address</label>
                        <div class="col-sm-8">
                            <input type="email" name="inst_email" value="<?= $inst_email; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="class_list" class="col-sm-4 col-form-label">Allowed Classes</label>
                        <div class="col-sm-8">
                            <input type="text" name="class_list" value="<?= $class_list; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="section_list" class="col-sm-4 col-form-label">Allowed Sections</label>
                        <div class="col-sm-8">
                            <input type="text" name="section_list" value="<?= $section_list; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="permission_list" class="col-sm-4 col-form-label">Permission List</label>
                        <div class="col-sm-8">
                            <input type="text" name="permission_list" value="<?= $permission_list; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select name="status" class="form-control">
                                <?= dropdown($status_list, $status); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="receipt_size" class="col-sm-4 col-form-label">Receipt Size (<?= $receipt_size ?>)</label>
                        <div class="col-sm-8">
                            <select name="receipt_size" class="form-control">
                                <?= dropdown_with_key($receipt_size_list, $receipt_size); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- File Upload Section -->
            <div class="form-section-title">Upload Institute Logos</div>
            <div id="progressBarContainer" style="display:none;">
              <progress id="uploadProgress" value="0" max="100"></progress>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="inst_logo" class="col-form-label">School Logo</label>
                        <input type="file" class="form-control-file" data-preview="previewLogo" data-input="inst_logo">
                        <input type="hidden" name="inst_logo" value='<?=$inst_logo ?>'>
                        <div id="previewLogo" style="display:flex;">
                            <?= (!empty($inst_logo)) ? "<img src='upload/$inst_logo' alt='previewLogo'  height='40px'/>":"" ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="principal_sign" class="col-form-label"  >Principal Sign</label>
                        <input type="file" class="form-control-file" data-preview="previewPsing" data-input="principal_sign">
                        <input type="hidden" name="principal_sign" value='<?=$principal_sign ?>' >
                        <div id="previewPsing" style="display:flex;">
                            <?= (!empty($principal_sign)) ? "<img src='upload/$principal_sign' alt='Principal Sign'  height='40px'/>":"" ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exam_controller_sign" class="col-form-label">Exam Controller Sign</label>
                        <input type="file" class="form-control-file" data-preview="previewExam" data-input="exam_controller_sign">
                        <input type="hidden" name="exam_controller_sign"  value='<?=$exam_controller_sign ?>' >
                        <div id="previewExam" style="display:flex;">
                            <?= (!empty($exam_controller_sign)) ? "<img src='upload/$exam_controller_sign' alt='Exam Contoller'  height='40px'/>":"" ?>
                        </div>
                    </div>
                </div>
            </div>
          
        </form>
    
            </div>
            <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>School Code</th>
                                <th>Status</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = fetch_all($table_name,'*',['status'=>'AUTO,DELETED'],['sort'=>'inst_name','condition_type'=>' NOT IN ']);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    echo "<tr>";
                                    echo "<td>" . $row['inst_name'] . "</td>";
                                    echo "<td>" . $row['inst_address1'] . "</td>";
                                    echo "<td>" . $row['inst_contact'] . "</td>";
                                    echo "<td>" . $row['school_code'] . "</td>";
                                    echo "<td>" . show_status($row['status']) . "</td>";
                                    
                                    ?>
                                    <td>
                                        <?= btn_view($table_name, $id,'Branch Details') ?>
                                        <a href='add_branch.php?link=<?php echo encode('id=' . $id); ?>' class='editBtn fa fa-edit btn btn-info btn-xs'></a>
                                        <span class='delete_btn btn btn-danger btn-sm' data-table='<?= $table_name ?>' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
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
    </section>
</div>
<?php require_once('system/footer.php'); ?>

<script>
    document.querySelectorAll("input[type='file']").forEach(input => {
      input.addEventListener("change", function () {
        const files = this.files;
        const previewId = this.dataset.preview;
        const inputName = this.dataset.input;
    
        const previewArea = document.getElementById(previewId);
        const hiddenInput = document.querySelector(`input[name='${inputName}']`);
        const progressBar = document.getElementById('uploadProgress');
        const progressContainer = document.getElementById('progressBarContainer');
    
        previewArea.innerHTML = '';
        progressBar.value = 0;
        progressContainer.style.display = 'block';
    
        const formData = new FormData();
        Array.from(files).forEach(file => formData.append('files[]', file));
    
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'system/upload.php', true);
    
        xhr.upload.onprogress = function (e) {
          if (e.lengthComputable) {
            progressBar.value = (e.loaded / e.total) * 100;
          }
        };
    
        xhr.onload = function () {
          progressContainer.style.display = 'none';
          if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const paths = [];
    
            response.files.forEach(file => {
              const div = document.createElement('div');
              div.style.margin = '5px';
    
              if (file.type.startsWith('image/')) {
                div.innerHTML = `<img src="upload/${file.url}" height="60"><br><a href="${file.url}" target="_blank">Download</a>`;
              } else {
                div.innerHTML = `<a href="upload/${file.url}" target="_blank">${file.name}</a>`;
              }
    
              previewArea.appendChild(div);
              paths.push(file.url);
            });
    
            hiddenInput.value = this.multiple ? paths.join(',') : paths[0];
          } else {
            previewArea.innerHTML = 'Upload failed.';
          }
        };
    
        xhr.send(formData);
      });
    });
</script>
