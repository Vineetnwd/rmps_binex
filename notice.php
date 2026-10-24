<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $notice = decode($_GET['link']);
    $id = $notice['id'];
    $isedit = 'yes';
} else {

    $notice = insert_row('notice');
    $isedit = 'no';
    $id = $notice['id'];
}

if ($id != '') {
    $res = get_data('notice', $id);
    if ($res['count'] > 0) {
        extract($res['data']);
    }
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> New Notice</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Notice</a></li>
            <li class="breadcrumb-item active">New Notice</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Notice Details </h3>
                <div class="box-tools pull-right">
                    <!-- <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button> -->
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <!-- <form id='insert_frm' action='update_notice'> -->
                <div class="row">
                    <div class="col-md-4">
                        <!-- Form Elements -->
                        <form action='update_notice' id='insert_frm' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Notice Date</label>
                                <input class="form-control" value='<?php echo date('Y-m-d'); ?>' max='<?php echo date('Y-m-d'); ?>' name='notice_date' type='date'>
                                <input value='<?= $id ?>' name='id' type='hidden'>
                            </div>
                            <div class="form-group">
                                <label>Notice Title</label>

                                <input class="form-control" value='<?= $notice_title ?>' name='notice_title' required>
                            </div>

                            <div class="form-group">
                                <label>Details of Notice</label>
                                <!-- <textarea name='notice_details' id='summernote' required></textarea> -->
                                <textarea class="form-control" name="notice_details" required><?= $notice_details ?></textarea>
                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label>Notice Status</label>-->
                            <!--    <select class="form-control form-select" name="status" required>-->
                            <!--        <option value="SHOW" <?= $status === 'SHOW' ? 'selected' : '' ?>>SHOW</option>-->
                            <!--        <option value="HIDE" <?= $status === 'HIDE' ? 'selected' : '' ?>>HIDE</option>-->
                            <!--    </select>-->

                            <!--</div>-->
                            <div class="checkbox">
                                <label>
                                    <!--<input type="checkbox" value="yes" name='checksms'> Send Email to All Clients-->
                                </label>
                                <input class="form-control" type='hidden' name='notice_attachment' id='targetimg'>
                            </div>
                        </form>
                        <form id='uploadForm' enctype='multipart/form-data'>
                            <div class="form-group mb-3">
                                <label>Notice Attachment </label>
                                <input type='file' name='uploadimg' id='uploadimg' accept='image' class='form-control'>
                            </div>
                            <div id='display'></div>
                        </form>
                        <div class="box-tool mt-4 bg-white">
                            <button class="btn btn-danger" id='insert_btn'>Publish Notice</button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <!--    Basic Table  -->
                        <table id="data_tbl" class="table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Notice Date</th>
                                    <th>Notice Title</th>
                                    <!--   <th>Notice Details</th>-->
                                    <th>Attachment </th>
                                    <!--<th>Status</th>-->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "select * from notice where status != 'AUTO' order by id desc";
                                $res = direct_sql($query);
                                foreach ((array) $res['data'] as $row) {
                                    echo "<tr>";
                                    $notice_id = $row['id'];
                                    echo "<td> " . date('d M', strtotime($row['notice_date'])) . "</td>";
                                    echo "<td> " . $row['notice_title'] . "</td>";
                                    //echo "<td>". $row['notice_details'] ."</td>";
                                    if ($row['notice_attachment'] == "") {
                                        echo "<td> NO ATTACHMENT </td>";
                                    } else {
                                        echo "<td> <a href='system/upload/" . $row['notice_attachment'] . "' target='new' download class='badge bg-primary'> Download </a></td>";
                                    }
                                    // echo "<td> " . $row['status'] . "</td>";
                                ?>
                                    <td align='right'>
                                        <?php echo btn_edit('notice', $notice_id); ?>
                                        <?php echo btn_delete('notice', $notice_id); ?>
                                    </td>
                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
                <!-- </form> -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
    </section>
</div>



<!-- End Page-content -->
<?php require_once('system/footer2.php'); ?>

