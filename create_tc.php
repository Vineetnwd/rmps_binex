<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['student_admission'])) {
    $adm = $_GET['student_admission'];
    $sid = $_GET['student_id'];
    $branch_id = $_SESSION['branch_id'];
     $new_tc_no = generate_tc_no();
    $res = insert_data('tbl_tc', array('student_admission' => $adm,'tc_no'=>$new_tc_no,'student_id'=>$sid,'branch_id'=>$branch_id, 'doa_certificate' => date('Y-m-d'), 'doi_certificate' => date('Y-m-d')));
    // $stu_data = get_data('student', $adm, null, 'student_admission')['data'];
    $stu_data = get_all('student','*',['student_admission'=>$adm,'branch_id'=>$branch_id])['data']['0'];
    $data = get_data('tbl_tc', $adm, null, 'student_admission')['data'];
}
function get_sub_by_class($cls) {
    $cls = strtoupper(trim($cls));

    $group1 = ['NUR', 'LKG', 'UKG'];
    $group2 = ['I', 'II', 'III', 'IV'];
    $group3 = ['V', 'VI', 'VII', 'VIII', 'IX', 'X'];

    if (in_array($cls, $group1)) {
        return ['ENG', 'HIN', 'MAT', 'ARTS'];
    } elseif (in_array($cls, $group2)) {
        return ['ENG', 'HIN', 'MAT', 'EVS', 'AI'];
    } elseif (in_array($cls, $group3)) {
        return ['ENG', 'HIN', 'MAT', 'SCI', 'SST', 'AI'];
    }

    return [];
}
function next_class($cur_class, $promote, $class_list) {
    if (strtoupper($promote) !== 'YES') {
        return $cur_class;
    }

    $index = array_search(strtoupper($cur_class), $class_list);

    // If class found and not last
    if ($index !== false && isset($class_list[$index + 1])) {
        return $class_list[$index + 1];
    }

    // If last class (like XII) or not found
    return $cur_class;
}

function class_to_text($class) {
    $map = [
        'NUR' => 'Nursery',
        'LKG' => 'Lower Kindergarten',
        'UKG' => 'Upper Kindergarten',
        'I'   => 'ONE',
        'II'  => 'TWO',
        'III' => 'THREE',
        'IV'  => 'FOUR',
        'V'   => 'FIVE',
        'VI'  => 'SIX',
        'VII' => 'SeveSEVENnth',
        'VIII'=> 'EIGHT',
        'IX'  => 'NINE',
        'X'   => 'TEN',
        'XI'  => 'ELEVEN',
        'XII' => 'TWELVE'
    ];

    return $map[strtoupper($class)] ?? $class;
}

$promote_class = !empty($data['promotion_higher_class']) ? $data['promotion_higher_class'] : 'YES';
$subjects = get_sub_by_class($stu_data['student_class']);
$fee_paid = get_last_paid_month($stu_data['id']);
$st_att = get_data('attendance',$stu['id'],'present_days','student_id')['data'];
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transfer Certificate</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#transport">Certificate</a></li>
            <li class="breadcrumb-item active">Transfer Certificate</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Transfer Certificate</h3>

                <div class="box-tools pull-right">
                    <div style='float:right'>
                        <form>
                            <input type='text' name='student_admission' placeholder='Enter Admission No. '>
                            <button class='btn btn-danger btn-sm'>Search </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-4">
                        <form action='create_tc' method='post' enctype='multipart/form-data' target='_blank' id="update_frm">

                            <div class="form-group">
                                <label>CBSE Admission No.</label>
                                <input class="form-control" name='cbse_admission_no' value='<?php echo $data['cbse_admission_no']; ?>' required>
                            </div>
                            <div class="form-group">
                                <label>Student Id</label>
                                <input class="form-control" name='id' type='hidden' value='<?php echo $data['id']; ?>' required>
                                <input class="form-control" name='student_admission' value='<?php echo $adm; ?>' required readonly>
                            </div>

                            <div class="form-group">
                                <label>Enter Student Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_name']; ?>' name='student_name' readonly>

                            </div>
                            <div class="form-group">
                                <label>Enter Mother's Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_mother']; ?>' name='student_mother'>
                            </div>
                            <div class="form-group">
                                <label>Enter Father's Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_father']; ?>' name='student_father'>
                            </div>
                            
                            <div class="form-group">
                                <label>Serial No. : </label>
                                <?php
                                    if($data['tc_no']==''){
                                        $tc_no = generate_tc_no();
                                    }else{
                                        $tc_no=$data['tc_no'];
                                    }
                                ?>
                                <input class="form-control" name='tc_no' value='<?php echo $tc_no; ?>' >
                            </div>
                            
                            <div class="form-group">
                                <label>Whether the candidate belongs to Sechduled Caste or Sechduled Tribe</label>
                                <input class="form-control" name='sc_st' value='<?php echo !empty($data['sc_st']) ? $data['sc_st'] : 'N/A'; ?>' >
                            </div>
                            
                            
                            <!--<div class="form-group">-->
                            <!--    <label>Admission No.</label>-->
                            <!--    <input class="form-control" value='<?php echo $data['student_admission']; ?>' name='student_admission' >-->
                            <!--</div>-->
                            <div class="form-group">
                                <label>Date of First admission in school with class</label>
                                <select name='d_first' class='form-control' required>
                                    <?php dropdown($class_list, $data['d_first']); ?>
                                </select>
                                
                                <div class="form-group">
                                <input class="form-control" name='d_first_date' value='<?php echo !empty($data['d_first']) ? $data['d_first'] : $stu_data['date_of_admission']; ?>' type='date'>
                                 </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Date of Birth (in Christion era) according to Admission Register</label>
                                <input class="form-control" name='d_birth' type='date' value='<?php echo date('Y-m-d', strtotime($stu_data['date_of_birth'])); ?>' readonly>

                                <input class="form-control mt-2" name='dob_text' type='text' value="<?= dob_to_words(date('Y-m-d', strtotime($stu_data['date_of_birth']))) ?>" placeholder='DOB in Text'>
                            </div>
                            
                            <div class="form-group">
                                <label>Class in which the student last studied</label>
                                <select name='last_class' class='form-control' required>
                                    <?php dropdown($class_list, $stu_data['student_class']); ?>
                                </select>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>School/ Board Annual examination last taken with result</label>
                            <input class="form-control" name='last_result' value='<?php echo $data['last_result']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Whether failed, if so once/ twice in the higher class</label>
                            <input class="form-control" name='higher_class' value='<?php echo !empty($data['higher_class']) ? $data['higher_class'] : 'NO';?>'>
                        </div>
                        <div class="form-group">
                            <label>Subject Studies</label>
                            <input class="form-control" name='subject_studies' value='<?php echo implode(', ', $subjects);//$data['subject_studies']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Whether qualified for promotion to the heigher class</label>
                            <input class="form-control" name='promotion_higher_class' value='<?= $promote_class ?>' >
                        </div>
                        <div class="form-group">
                            <label>If yes, class in figure</label>
                            <?php
                                $st_class_list = ['NUR','LKG','UKG','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                                $hc_value = !empty($data['higher_class_fig']) 
                                    ? $data['higher_class_fig'] 
                                    : (!empty($stu_data['student_class']) 
                                        ? next_class($stu_data['student_class'], $promote_class, $st_class_list) 
                                        : 'N/A');
                            ?>

                            <input class="form-control" name="higher_class_fig" value="<?= htmlspecialchars($hc_value, ENT_QUOTES); ?>">
                            <input class="form-control" name='higher_class_word' value='<?= class_to_text($hc_value); ?>' placeholder="Class In Words">
                        </div>
                        <div class="form-group">
                            <label>Month upto which the (student has paid) school dues paid</label>
                            <input class="form-control" name='dues_paid' value='<?php echo $data['dues_paid'] ?? $fee_paid['month'].' '.$fee_paid['year']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Any fee consession availed of : if so, the nature of such consession</label>
                            <input class="form-control" name='consession' value='<?php echo !empty($data['consession']) ? $data['consession'] : 'NO';?>'>
                        </div>
                        <div class="form-group">
                            <label>Total number of working days</label>
                            <input class="form-control" name='working_day' value='<?php echo $data['working_day']; ?>'>
                        </div>
                        
                         <div class="form-group">
                            <label>Student PEN No. (New)</label>
                            <input class="form-control" name='pen_no' value='<?php echo $data['pen_no']; ?>'>
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        
                         <div class="form-group">
                            <label>Student APAR ID (New)</label>
                            <input class="form-control" name='apaar_id' value='<?php echo $data['apaar_id']; ?>'>
                        </div>
                         <div class="form-group">
                            <label>Registration No. of the candidate (in case of class IX to XII)</label>
                            <input class="form-control" name='cbsereg_no' value='<?php echo !empty($stu_data['cbsereg_no']) ? $stu_data['cbsereg_no'] : 'N/A';?>'>
                        </div>
                         <div class="form-group">
                            <label>Board Issued Roll Number (in case of class IX to XII</label>
                            <input class="form-control" name='cbseroll_no' value='<?php echo !empty($stu_data['cbsereg_no']) ? $stu_data['cbseroll_no'] : 'N/A';?>'>
                        </div>
                        <div class="form-group">
                            <label>Total number of working day present</label>
                            <input class="form-control" name='total_present' value='<?php echo $data['total_present'] ?? $st_att; ?>'>
                        </div>

                        <div class="form-group">
                            <label>Whether NCC Cadet/ Boy Scout/ Girl Guide (Details my be Given)</label>
                            <input class="form-control" name='ncc' value='<?php echo !empty($data['ncc']) ? $data['ncc'] : 'N/A';?>'>
                        </div>


                        <div class="form-group">
                            <label>Game Played or Extra Curicular Activities in which the student usually took part (mention achievment level therein) </label>
                            <input class="form-control" name='game' value='<?php echo !empty($data['game']) ? $data['game'] : 'N/A';?>'>
                        </div>

                        <div class="form-group">
                            <label>General Conduct</label>
                            <input class="form-control" name='conduct' value='<?php echo !empty($data['conduct']) ? $data['conduct'] : 'GOOD';?>'>
                        </div>
                        <div class="form-group">
                            <?php
                                if($data['doa_certificate']==''){
                                    $doa = $today;
                                }else{
                                    $doa = $data['doa_certificate'];
                                }
                            ?>
                            <label>Date of application for certificate</label>
                            <input class="form-control" name='doa_certificate' type='date' value='<?php echo date('Y-m-d', strtotime($doa)); ?>'>
                        </div>

                        <div class="form-group">
                            <?php
                                if($data['doi_certificate']==''){
                                    $doi = $today;
                                }else{
                                    $doi = $data['doi_certificate'];
                                }
                            ?>
                            <label>Date of issue of certificate</label>
                            <input class="form-control" name='doi_certificate' type='date' value='<?php echo date('Y-m-d', strtotime($doi)); ?>'>
                        </div>

                        <div class="form-group">
                            <label>Reason of leaving school</label>
                            <input class="form-control" name='reason_leaving' value='<?php echo $data['reason_leaving']; ?>'>
                        </div>
                        
                       

                        <div class="form-group">
                            <label>Any other remarks</label>
                            <input class="form-control" name='other_remarks' value='<?php echo $data['other_remarks'] ?? 'N/A'; ?>'>
                        </div>


                        </form>
                        <input id="update_btn" class="btn btn-primary" value=' Generate & Print '>

                    </div>

                    <!-- /.row -->

                </div>
            </div>
        </div>
</div>

<?php require_once('system/footer.php'); ?>