<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Bulk Marks Import</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#fee">Exam</a></li>
            <li class="breadcrumb-item active">Marks Import</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Marks Upload</h3>
				<a href="marks_entry" class="btn text-danger btn-md float-right">
                            <i class="fa fa-download"></i> Download CSV from Exam -> Marks Entry
                        </a>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <!-- Alert for AJAX Response -->
                <div id="ajax-response" class="alert" style="display: none;"></div>
				
                <div class="row">
                    <div class="col-lg-3 offset-lg-2">
                        <div class="form-group text-right">
                            <label class="control-label" for="inputSuccess">Update Marks</label>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="form-group has-success">
                                <input type="file" id="marks-file" name="file" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label class="control-label">&nbsp;</label>
                            <button id="upload-btn" class="btn btn-success btn-md">Upload Marks</button>
                        </div>
                    </div>

                
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.getElementById('upload-btn').addEventListener('click', function () {
        const fileInput = document.getElementById('marks-file');
        const formData = new FormData();

        // Append the file and hidden fields
        if (fileInput.files.length > 0) {
            formData.append('file', fileInput.files[0]);
            formData.append('table', 'exam');
            formData.append('pkey', 'student_admission');
            formData.append('remove', 'name,roll,class');

            // AJAX Request
            fetch('system/master_process?task=marks_upload', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    const responseDiv = document.getElementById('ajax-response');
                    responseDiv.style.display = 'block';

                    if (data.status='success') {
                        responseDiv.className = 'alert alert-success';
                        responseDiv.innerHTML = `${data.new} New record(s) added and ${data.change} Updated. ${data.msg}`;
                    } else {
                        responseDiv.className = 'alert alert-danger';
                        responseDiv.innerHTML = data.msg || 'An error occurred. Please try again.';
                    }
                })
                .catch(error => {
                    const responseDiv = document.getElementById('ajax-response');
                    responseDiv.style.display = 'block';
                    responseDiv.className = 'alert alert-danger';
                    responseDiv.innerHTML = 'An unexpected error occurred.';
                });
        } else {
            alert('Please select a file to upload.');
        }
    });
</script>
<?php require_once('system/footer.php'); ?>
