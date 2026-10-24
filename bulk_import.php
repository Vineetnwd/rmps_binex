<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<div class="content-wrapper">
    <!-- Page Header -->
    <section class="content-header">
        <h1>Bulk Import</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#extra">Extra</a></li>
            <li class="breadcrumb-item active">Bulk Import</li>
        </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
        <!-- Action Box -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Bulk Actions</h3>
            </div>
            <div class="box-body">
                <!-- Notification -->
                <?php if (isset($_GET['res'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php
                    $info = json_decode($_GET['res'], true);
                    echo $info['new'] . " new record(s) added and " . $info['change'] . " updated.";
                    ?>
                </div>
                <?php } ?>

                <!-- Bulk Import Forms -->
                <div class="bulk-actions">
                    <div class="row action-row">
                        <!-- Student Data Import -->
                        <div class="col-lg-6">
                            <form action="system/master_process?task=bulk_import" method="post" enctype="multipart/form-data" class="import-form">
                                <label for="studentFile">Select CSV (Student File)</label>
                                <input type="hidden" name="table" value="student">
                                <input type="hidden" name="pkey" value="student_admission">
                                <div class="input-group">
                                    <input type="file" name="file" id="studentFile" class="form-control" required>
                                    <button type="submit" class="btn btn-success">Import Student Data</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 text-right">
                            <a href="system/master_process.php?task=bulk_export&table=student&status=ACTIVE" class="btn btn-dark">
                                <i class="fa fa-download"></i> Download Student Template
                            </a>
                        </div>
                    </div>
                    
                    <hr>

                    <!-- Teacher Data Import -->
                    <div class="row action-row">
                        <div class="col-lg-6">
                            <form action="system/master_process?task=bulk_import_teacher" method="post" enctype="multipart/form-data" class="import-form">
                                <label for="teacherFile">Select CSV (Teacher File)</label>
                                <input type="hidden" name="table" value="employee">
                                <input type="hidden" name="pkey" value="emp_code">
                                <div class="input-group">
                                    <input type="file" name="file" id="teacherFile" class="form-control" required>
                                    <button type="submit" class="btn btn-success">Import Teacher Data</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 text-right">
                            <a href="system/master_process.php?task=bulk_export&table=employee&status=ACTIVE" class="btn btn-dark">
                                <i class="fa fa-download"></i> Download Teacher Template
                            </a>
                        </div>
                    </div>

                    <hr>

                    <!-- Transport Area Import -->
                    <div class="row action-row">
                        <div class="col-lg-6">
                            <form action="system/master_process?task=bulk_import" method="post" enctype="multipart/form-data" class="import-form">
                                <label for="areaFile">Select CSV (Transport Area)</label>
                                <input type="hidden" name="table" value="transport_area">
                                <input type="hidden" name="pkey" value="id">
                                <div class="input-group">
                                    <input type="file" name="file" id="areaFile" class="form-control" required>
                                    <button type="submit" class="btn btn-success">Import Area List</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 text-right">
                            <a href="system/master_process.php?task=bulk_export&table=transport_area" class="btn btn-dark">
                                <i class="fa fa-download"></i> Download Transport Template
                            </a>
                        </div>
                    </div>

                    <hr>

                    <!-- Update Dues -->
                    <div class="row action-row">
                        <div class="col-lg-6">
                            <form action="system/master_process?task=bulk_import" method="post" enctype="multipart/form-data" class="import-form">
                                <label for="duesFile">Update Previous Dues</label>
                                <input type="hidden" name="table" value="student_fee">
                                <input type="hidden" name="pkey" value="student_admission">
                                <div class="input-group">
                                    <input type="file" name="file" id="duesFile" class="form-control" required>
                                    <button type="submit" class="btn btn-success">Update Dues</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 text-right">
                            <a href="system/master_process.php?task=bulk_export&table=student_fee" class="btn btn-dark">
                                <i class="fa fa-download"></i> Download Fee Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer.php'); ?>
