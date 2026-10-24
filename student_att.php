<style>
.student-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #3b82f6;
}    
</style>
<?php require_once('system/header.php'); ?>
<?php
require_once('system/menu.php');
if (isset($_REQUEST['att_date'])) {
    $att_date = $_REQUEST['att_date'];
} else {
    $att_date = date('Y-m-d');
}
?>
<style>
    [type=checkbox]+label{
        margin-bottom:-10px !important;
        margin-top:-9px !important;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Student Attendance </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="row ">
                    <div class="col-md-4">
                        <h6><span class="text-danger">Month</span> : <?php echo  date('M') ?> <span class="text-danger"> Year</span> : <?php echo date("Y"); ?></b>
                        [<?php if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
                                extract($_REQUEST);
                                echo $class . "-" . $section;
                            } ?>]
                        </h6>
                    </div>
                    <div class="col-md-8 float-right text-right">
                       
                            <form>
                                <select name="class" id="class" required>
                                    <?php dropdown($class_list, $class); ?>
                                </select>
                                <select name="section" id="section" required>
                                    <?php dropdown($section_list, $section); ?>
                                </select>
                                <input type='date' name='att_date' value='<?php echo $att_date; ?>' id='att_date' max='<?php echo date('Y-m-d'); ?>'>
                                <button class="btn btn-warning" type="submit">Show</button>
                            </form>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                     <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-stripped">
                                <thead>
                                <tr>
                                    <td colspan='3'></td>
                                    <td align='center'>
                                <?php
                                    check_all('selectall','chk','','Select All')
                                ?> 
                                </td>
                                <td>
                                    <button data-status ='P'  class='att_btn btn bg-white border-success' ><i class="fa fa-check-circle" style="color: green;"></i> Present Mark As Present </button>
                                </td> 
                                <td>
                                    <button data-status ='A'   class='att_btn btn bg-white border-danger' ><i class="fa fa-times-circle" style="color: red;"></i> Mark As Absent </button>
                                </td> 
                                <!-- <td>
                                    <button data-status ='L'  class='att_btn btn bg-white border-primary' ><i class="fa fa-suitcase" style="color: blue;"></i> Marks As Leave </button>
                                </td>     -->
                                </tr>
                                    <tr class='op-thead'>
                                        <th>#</th>
                                        <th>Sl. No.</th>
                                        <th>Photo</th>
                                        <th>Student Name</th>
                                        <th>Roll No.</th>
                                        <th>Admission No.</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <!--<th>Action</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class']) and isset($_REQUEST['section']) and isset($_REQUEST['att_date'])) {
                                        extract($_REQUEST);
                                        $res = get_all('student', '*', array('student_class' => $class, 'student_section' => $section,'status'=>'ACTIVE') ,'student_roll');
                                    ?>
                                        <form action='system/master_process?task=make_att' method='post' id='att_frm'>
                                        <?php
                                       
                                        if (date('D', strtotime($att_date)) == 'Sun') {
                                            echo "<script> alert('Selected Date is sunday'); </script>";
                                        }
                                        if ($res['count'] > 0) {
                                            //print_r($res);
                                            foreach ($res['data'] as $row) {
                                                $id = $row['id'];
                                                $stu_class = $row['student_class'];
                                                $stu_section = $row['student_section'];
                                                echo "<tr>";
                                                echo "<td>";
                                                $tbl_name = "student_att";
                                                $col_name = 'd_' . date('j', strtotime($att_date));
                                                $mvalue = remove_space(date('M_Y', strtotime($att_date)));
                                                $post = array('att_month' => $mvalue, 'student_id' => $id);
                                                
                                                $sql = "SELECT * FROM student_att WHERE student_id = '$id' AND att_month LIKE '$mvalue' ";
                                                $stu_att = direct_sql($sql);
                                                if ($stu_att['count'] == 0) {
                                                  $res2 = insert_data($tbl_name, $post);
                                                }
                                                
                                                if($att_date ===$today){
                                                    if ($stu_att['data'][0][$col_name] == 'P') {
                                                        att_check("sel_$id",$id,'chk','checked');
                                                        // echo "<input type='checkbox' data-class='$stu_class' data-section='$stu_section' value ='$id' name='sel_id[]' class='chk' checked>";
                                                
                                                    } else{
                                                        att_check("sel_$id",$id,'chk','');
                                                        //  echo "<input type='checkbox' data-class='$stu_class' data-section='$stu_section' value ='$id' name='sel_id[]' class='chk'>";
                                                    }                                               
                                                }
                                                else{
                                                    if ($stu_att['data'][0][$col_name] == 'P') {
                                                        echo "<span class='badge bg-success p-2'>P</span>";
                                                    } else if ($stu_att['data'][0][$col_name] == "A") {
                                                        echo "<span class='badge bg-danger p-2'>A</span>";
                                                    }
                                                }
                                                echo "</td>";
                                                echo "<td>" . $i . "</td>";
                                                $photo = $row['student_photo'];
                                                
                                                // if($photo==''){
                                                    if($_SESSION['branch_id']==1){
                                                        $folder_path = $base_url.'student_photo/Bishambharpur/Photo/';
                                                    }else if($_SESSION['branch_id']==2){
                                                        $folder_path = $base_url.'student_photo/Barauli/Photo/';
                                                    }else{
                                                        $folder_path = $base_url.'student_photo/Barharia/Photo/';
                                                    }
                                                // echo $base_url;
                                                    $st_photo = $folder_path.$row['student_admission'].'.jpg';
                                                // }else{
                                                //     $st_photo = $base_url.'upload/'.$row['student_photo'];
                                                // }
                                                if (file_exists($st_photo)) {
                                                    $st_photo = "assets/images/user.jpg";
                                                }
                                                
                                                echo "<td>
                                                        <img src='$st_photo' class='student-photo'>
                                                      </td>";
                                                
                                                echo "<td>" . $row['student_name'] . "</td>";
                                                echo "<td>" . $row['student_roll'] . "</td>";
                                                echo "<td>" . $row['student_admission'] . "</td>";
                                                echo "<td>" . $stu_class . "</td>";
                                                echo "<td>" . $stu_section . "</td>";
                                                echo "</tr>";
                                                $i++;
                                            }
                                        }
                                    }
                                        ?>
                                        </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>
<script>
    //=========SELECT ALL CHECK BOX WITH SAME NAME=======//
    function selectAll(source) {
        checkboxes = document.getElementsByName('sel_id[]');
        for (var i in checkboxes)
            checkboxes[i].checked = source.checked;
    }
</script>

<?php if (isset($_REQUEST['class'])) { ?>
<script>
  $(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
    $('#example1').DataTable({
      "pageLength": 100
    });
  });
</script>
<?php } ?>
