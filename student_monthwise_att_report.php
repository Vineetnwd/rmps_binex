<?php require_once('system/header.php');
require_once('system/menu.php');

if (isset($_REQUEST['month'])) {
    $month =$_REQUEST['month'];
    $att_month =  remove_space(date('M_Y', strtotime($month)));
    
}
$class =(isset($_REQUEST['student_class']))?$_REQUEST['student_class']:'';
$section =(isset($_REQUEST['student_section']))?$_REQUEST['stduent_section']:'';
//$monthNumber =(isset($_REQUEST['month']))?date("m", strtotime($_REQUEST['month'])):'';
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Student Attendance Report</h1>
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
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-danger">Month</span> : <?=  add_space($att_month); ?> </b>
                        [<?php if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
                                extract($_REQUEST);
                                echo $class . "-" . $section;
                            } ?>] &nbsp; <button class="btn btn-success btn-sm border-rounded" onclick="exportxls()">Export</button>
                    </div>
                    <div class="col-md-6 float-end">
                     
                                <form>
                                    <select name="class" id="class" required>
                                    <?php
                                        if ($_SESSION['user_type'] == 'TEACHER') {
                                            $emp = get_data('employee', $_SESSION['user_name'], null, 'emp_code')['data'];
                                            $emp_cls = isset($emp['allow_class']) ? $emp['allow_class'] : '';
                                        }
                                        // Populate dropdown for all classes (if $emp_cls is set)
                                        if (!empty($emp_cls)) {
                                            $classSections = explode(',', $emp_cls);
                                            $classes = [];
                                            foreach ($classSections as $item) {
                                                $class = explode('-', $item)[0];
                                                if (!in_array($class, $classes)) {
                                                    $classes[] = $class;
                                                    echo "<option value='$class'>$class</option>";
                                                }
                                            }
                                        } else {
                                            dropdown($class_list,  @$_REQUEST['class']); // Default dropdown if not TEACHER
                                        }
                                    ?>
                                </select> 
                                    </select>
                                    <select name="section" id="section" required>
                                    <option value="" selected>Section</option>
                                        <?= dropdown($section_list, @$_REQUEST['section']); ?>
                                    </select>
                                    <input type='month' name='month' value='<?= date('Y-m', strtotime($_REQUEST['month'])); ?>' >
                                    <button class="btn btn-warning btn-md" type="submit">Show</button>
                                </form>
                         
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table rules='all' border='1' align="center" id='data_tbl'>
                                <thead>
                                    <tr>
                                        <th colspan="<?= (date("t", strtotime($month)) +5) ?>">
                                            <center><big><?= $full_name .' || Class : '. $class .' Section : '. $section .' Month :' .$month ?></big></center>
                                        </th>
                                    </tr>
                                    <tr class='bg-gray'>
                                        <th  class='text-center p-2 font-weight-bold' >Admission No.</th>
                                        <th  class='text-center p-2 font-weight-bold'>Student Name</th>
                                        <th  class='text-center p-2 font-weight-bold'>Class</th>
                                        <th  class='text-center p-2 font-weight-bold'>Sec</th>
                                        <?php 
                                        $last_day = date("t", strtotime($month));
                                       
                                        for ($i = 1; $i <= $last_day; $i++) {
                                           $date = $month."-".$i;
                                           $day =date('D',strtotime($date));
                                           $cls =($day =='Sun') ? 'bg-gray':'';
                                           echo "<th class='text-center p-2 font-weight-bold  $cls'>" . $i . "<br> ".$day. "</th>";
                                           
                                        }
                                        ?>
                                        <th class='text-center p-2 font-weight-bold bg-danger text-light '  >Total Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class']) and isset($_REQUEST['section']) and isset($_REQUEST['month'])) {
                                        extract($_REQUEST);
                                      $sql ="SELECT  student.student_name, student.student_admission, student.student_class, student.student_section, student.student_roll, student_category, student.status , student_att.* FROM `student_att` left join student on student.id = student_att.student_id where student_class ='$class' and student_section ='$section' and att_month='$att_month' and student.status='ACTIVE'"; 
		                        	$res = direct_sql($sql);
                                        
                                    ?>
                                    <?php
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                $student_id = $row['id'];
                                                $stu_adm = $row['student_admission'];
                                                $stu_class = $row['student_class'];
                                                $stu_section = $row['student_section'];
                                                $p_count =0;
                                                echo "<tr>";
                                                echo "<td>" . $stu_adm . "</td>";
                                                echo "<td>" . $row['student_name'] . "</td>";
                                                echo "<td align='center'>" . $stu_class . "</td>";
                                                echo "<td align='center'>" . $stu_section . "</td>";
                                               
                                               
                                                for ($i = 1; $i <= $last_day; $i++) {
                                                    $date = $month."-".$i;
                                                    $day =date('D',strtotime($date));
                                                    $cls =($day =='Sun') ? 'bg-gray':'';
                                                    
                                                    //$att_status = att_status($student_id,$att_month,$i);
                                                    $att_status = $row['d_'.$i];
                                                    $att_cls = ($att_status =='P')? 'badge bg-success':'badge bg-danger';
                                                    if ($att_status =='P') $p_count ++;
                                                    echo "<th class='text-center p-2 font-weight-bold  $cls'> <span class='$att_cls'>" .$att_status. "</span></th>";
                                                    
                                                 }
                                                    echo "<td class='text-center p-2 font-weight-bold bg-danger text-light ' >" . $p_count . "</td>";
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
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
        }
    }
    $("#export").click(function() {
        $("#example1").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Worksheet Name",
            filename: "Monthwise_Student_Attendance_Report <?php echo $class . "-" . $section;?> ", //do not include extension
            fileext: ".xls", // file extension
            exclude_img: true,
            exclude_links: false,
            exclude_inputs: true
        });
    });
</script>
<script>
    // Pass PHP variable to JavaScript
    const classSections = <?php echo isset($emp_cls) ? json_encode(explode(',', $emp_cls)) : '[]'; ?>;

    // Populate sections dynamically based on selected class
    function populateSections(selectedClass) {
        const sectionDropdown = document.getElementById("section");
        sectionDropdown.innerHTML = "<option value='' disabled selected>Select Section</option>";
        classSections.forEach(item => {
            const [className, section] = item.split("-");
            if (className === selectedClass) {
                const option = document.createElement("option");
                option.value = section;
                option.textContent = section;
                sectionDropdown.appendChild(option);
            }
        });
    }

    // Populate classes on page load if necessary
    document.addEventListener("DOMContentLoaded", () => {
        if (document.getElementById("class").value) {
          //  populateSections(document.getElementById("class").value);
        }
    });
</script>