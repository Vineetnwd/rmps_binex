<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Gate Pass</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Reception</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <!--<h3 class="box-title"> </h3>-->
                <!--<div class="box-tools pull-right">-->
                <!--    <a class='btn btn-success btn-sm' title='New Employee' href='add_recruitment'> <i class='fa fa-plus'>  </i> Add New</a>-->
                <!--</div>-->
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th> # </th>
                                <th> Student Name </th>
                                <th> Class </th>
                                <th> Date</th>
                                <th> Escorted By</th>
                                <th> Time Out</th>
                                <th> Operation </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $i = 1;
                            $sql = "SELECT * from gate_pass where status NOT IN('AUTO','BLOCK') order by id desc";
                            $res = direct_sql($sql);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    $sinfo = get_data('student',$row['student_id'])['data'];
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . $sinfo['student_name'] . "</td>";
                                    echo "<td>" . $sinfo['student_class'].' - '. $sinfo['student_roll'] . "</td>";
                                    echo "<td>" . date('d-M-Y',strtotime($row['escort_date'])) . "</td>";
                                    echo "<td>" . $row['escorted_by'] . "</td>";
                                    echo "<td>" . $row['time_out'] . "</td>";
                            ?>
                                    <td>
                                        <?= btn_view('gate_pass', $id, $row['name']) ?>
                                        <!--<a href='add_recruitment.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>-->
                                        <!--<span class='delete_btn btn btn-danger btn-xs' data-table='recruitment' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>-->
                                        <a href='pdf_gate_pass.php?id=<?=$row['id']?>' class='fa fa-download btn btn-dark btn-xs' target='_blank'></i></a>
                                    </td>
                            <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>