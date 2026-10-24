<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
//    ini_set('display_errors', 1);
//    ini_set('display_startup_errors', 1);
//    error_reporting(E_ALL);

if (isset($_GET['link']) && $_GET['link'] != '') {
    $data   = decode($_GET['link']);
    $id     = $data['id'];
    $isedit = 'yes';
} else {
    $emp    = insert_row('tabulation');
    $id     = $emp['id'];
    $isedit = 'no';
}

if ($id != '') {
    $res = get_data('tabulation', $id);
    if ($res['count'] > 0 && $res['status'] == 'success') {
        extract($res['data']);
    }
}

// Permission: can this user edit marks?
$can_edit = get_data(
    'employee',
    get_data('user', $user_id, 'user_name')['data'],
    'marks_entry',
    'emp_code'
)['data'];

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Tabulation Marks </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Tabulation Marks</li>
        </ol>
    </section>

    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div>
                    <h3 class="box-title">Marks Entry Sheet (Download CSV)</h3>
                    <div class="box-tools pull-right">
                        <?php if ($can_edit == 'YES') { ?>
                            <kbd>Marks Editable</kbd> – Change & click outside to Save
                        <?php } else { ?>
                            <kbd>View Only</kbd> – You don’t have marks entry permission
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <?php
                // Get teacher info
                $emp_code = strtoupper(remove_space($_SESSION['user_name']));
                $ssql     = "SELECT * FROM employee WHERE emp_code = '$emp_code'";
                $emp_info = direct_sql($ssql)['data'][0] ?? [];

                $subjects = !empty($emp_info)
                    ? (get_all('teacher_subject', '*', ['teacher_id' => $emp_info['id']])['data'] ?? [])
                    : [];

                if (!empty($subjects)) {
                    ?>
                    <!-- Single Exam selection (top) -->
                    <form method="post">
                        <div style="background: #fef3cd; padding: 25px; border-radius: 12px; margin-bottom: 25px; border-left: 6px solid #dc3545; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
                            <div style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 25px;">
                                <div style="flex: 1; min-width: 220px;">
                                    <label style="color: #dc3545; font-weight: 700; font-size: 14px; display: block; margin-bottom: 10px;">
                                        🎯 SELECT EXAM
                                    </label>
                                    <select style="width: 100%; border: 2px solid #dc3545; border-radius: 8px; background: #ccc; color: #000 !important; " 
                                            name="exam_id" 
                                            id="main_exam_select" 
                                            required>
                                        <?= dropdown_list('exam_list', 'exam_name', 'exam_name', $_REQUEST['exam_id'] ?? '') ?>
                                    </select>
                                </div>
                    
                                <div>
                                    <button type="submit" style="background: #dc3545; color: #ffd700; padding: 12px 30px; border: none; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);">
                                        ✅ Apply Exam
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php
                    if (!isset($_REQUEST['subject_id'])) { 
                    // ----- Subject list (per teacher subject) -----
                    foreach ($subjects as $row):
                        
                        $class_name   = $row['class_name'];
                        $section_name = $row['section_name'];
                        $subject_id   = $row['subject_name'];
                        $exam_id      = $row['exam_id'] ?? '';

                        $subject_full_name = get_data('subject', $subject_id, 'subject_name')['data']; // e.g. "Hindi Grammar"
                        ?>
                        <form action="" method="post" class="subject-form">
                            <div class="card p-1 bg-light border rounded teacher-row"
                                 style="margin-bottom: 0.2rem;"
                                 data-subject-name="<?= htmlspecialchars($subject_full_name) ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <strong>Class:</strong> <?= htmlspecialchars($class_name) ?>
                                        <input type="hidden" name="student_class"
                                               value="<?= htmlspecialchars($class_name) ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Section:</strong> <?= htmlspecialchars($section_name) ?>
                                        <input type="hidden" name="student_section"
                                               value="<?= htmlspecialchars($section_name) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Subject:</strong>
                                        <span class="subject-name"><?= htmlspecialchars($subject_full_name) ?></span>
                                        <input type="hidden" name="subject_id"
                                               value="<?= htmlspecialchars($subject_id) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Exam:</strong>
                                        <input type="text" name="exam_id"
                                               value="<?= htmlspecialchars($_REQUEST['exam_id'] ?? '') ?>" style="border: none;background:#f8f9fa"
                                               readonly>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="submit" class="btn btn-dark w-100">Show Details</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php
                    endforeach;
                    }
                } else {
                    ?>
                    <div class="alert alert-warning">
                        No assigned subjects found in <code>teacher_subject</code> table.
                    </div>
                <?php } ?>

                <div class="table-responsive">
                    <?php
                    // ------------------ Marks table part ------------------
                    if (isset($_REQUEST['exam_id']) && isset($_REQUEST['subject_id'])) {

                        $subject_name = get_data('subject', $_REQUEST['subject_id'], 'subject_column')['data'];
                        ?>
                        <div class="bg-light border p-2 rounded mb-3">
                            <i class="fa fa-users text-primary"></i>
                            <strong>Class:</strong> <?= $_REQUEST['student_class'] ?> &nbsp;
                            <i class="fa fa-users text-success"></i>
                            <strong>Section:</strong> <?= $_REQUEST['student_section'] ?> &nbsp;
                            <i class="fa fa-book text-warning"></i>
                            <strong>Subject:</strong> <?= $subject_name ?> &nbsp;
                            <i class="fa fa-pencil text-danger"></i>
                            <strong>Exam:</strong> <?= $_REQUEST['exam_id'] ?>
                        </div>

                        <table id="example" class="table table-bordered table-striped">
                            <thead class="op-thead">
                            <tr>
                                <th>Adm. No.</th>
                                <th>Roll</th>
                                <th>Name</th>
                                <?php
                                // ----- Header as in your logic -----
                                if ($_REQUEST['exam_id'] == 'UNIT TEST 1') {
                                    $exam_id_for_table = 'ut1';
                                    echo "<th> UT 1 </th>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 2') {
                                    $exam_id_for_table = 'ut2';
                                    echo "<th> UT 2 </th>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 3') {
                                    $exam_id_for_table = 'ut3';
                                    echo "<th> UT 3 </th>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 4') {
                                    $exam_id_for_table = 'ut4';
                                    echo "<th> UT 4 </th>";
                                } elseif ($_REQUEST['exam_id'] == 'TERM 1') {
                                    $exam_id_for_table = 'TERM 1';
                                    if ($_REQUEST['student_class'] == 'IX' || $_REQUEST['student_class'] == 'X') {
                                        echo "<th> MA (5)</th>";
                                        echo "<th> Portfolio (5)</th>";
                                        echo "<th> SE (5)</th>";
                                        echo "<th> Mid Term (80)</th>";
                                    } else {
                                        echo "<th> GA (20)</th>";
                                        echo "<th> NB (5)</th>";
                                        echo "<th> SEA (5)</th>";
                                        if ($_REQUEST['student_class'] == 'I' || $_REQUEST['student_class'] == 'II') {
                                            echo "<th> Mid Term (60)</th>";
                                        } else {
                                            echo "<th> Mid Term (80)</th>";
                                        }
                                    }
                                } elseif ($_REQUEST['exam_id'] == 'TERM 2') { // FIXED: == not =
                                    $exam_id_for_table = 'TERM 2';
                                    if ($_REQUEST['student_class'] == 'IX' || $_REQUEST['student_class'] == 'X') {
                                        echo "<th> MA (5)</th>";
                                        echo "<th> Portfolio (5)</th>";
                                        
                                        if ($_REQUEST['subject_id'] == '46') {
                                            echo "<th> SE (10)</th>";
                                            echo "<th> Annual (50)</th>";
                                        } else {
                                            echo "<th> SE (5)</th>";
                                            echo "<th> Annual (80)</th>";
                                        }
                                    } else {
                                        echo "<th> GA (20)</th>";
                                        echo "<th> NB (5)</th>";
                                        echo "<th> SEA (5)</th>";
                                        if ($_REQUEST['student_class'] == 'I' || $_REQUEST['student_class'] == 'II') {
                                            echo "<th> Annual (60)</th>";
                                        } else {
                                            echo "<th> Annual (80)</th>";
                                        }
                                    }
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // $res_students = get_all(
                            //     'student',
                            //     '*',
                            //     [
                            //         'student_class'   => $_REQUEST['student_class'],
                            //         'student_section' => $_REQUEST['student_section'],
                            //         'status'          => 'ACTIVE'
                            //     ],
                            //     'student_roll asc'
                            // );
                            
							$res_students = direct_sql("SELECT * FROM student WHERE student_class='{$_REQUEST['student_class']}' AND student_section='{$_REQUEST['student_section']}' AND status IN ('ACTIVE','PROMOTED') ORDER BY student_roll ASC");

                            foreach ($res_students['data'] as $srow) {

                                $r_term = $_REQUEST['term'] ?? '';
                                if ($r_term == '') {
                                    if ($_REQUEST['exam_id'] == 'UNIT TEST 1' || $_REQUEST['exam_id'] == 'UNIT TEST 2') {
                                        $r_term = 'TERM 1';
                                    } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 3' || $_REQUEST['exam_id'] == 'UNIT TEST 4') {
                                        $r_term = 'TERM 2';
                                    } else {
                                        $r_term = $_REQUEST['exam_id'];
                                    }
                                }

                                $student_admission = $srow['student_admission'];
                                $sid               = $srow['id'];

                                $tabulation = get_all(
                                    'tabulation',
                                    '*',
                                    [
                                        'student_id'        => $sid,
                                        'student_admission' => $student_admission,
                                        'term'              => $r_term,
                                        'subject'           => $_REQUEST['subject_id'],
                                        'branch_id'         => $branch_id
                                    ]
                                )['data'][0] ?? [];

                                $ut1       = $tabulation['ut1'] ?? 0;
                                $ut2       = $tabulation['ut2'] ?? 0;
                                $ut3       = $tabulation['ut3'] ?? 0;
                                $ut4       = $tabulation['ut4'] ?? 0;
                                $ma        = $tabulation['ma'] ?? 0;
                                $portfolio = $tabulation['portfolio'] ?? 0;
                                $se        = $tabulation['se'] ?? 0;
                                $mid_term  = $tabulation['mid_term'] ?? 0;
                                $ga        = $tabulation['ga'] ?? 0;
                                $nb        = $tabulation['nb'] ?? 0;
                                $sea       = $tabulation['sea'] ?? 0;

                                echo "<tr>";
                                echo "<td>{$student_admission}</td>";
                                echo "<td>{$srow['student_roll']}</td>";
                                echo "<td class='st_name'>{$srow['student_name']}</td>";

                                // Main cell outputs with data-max as in your code
                                if ($_REQUEST['exam_id'] == 'UNIT TEST 1') {
                                    echo "<td>
                                        <span class='marks-tab'
                                            data-student_id='$sid'
                                            data-student_admission='$student_admission'
                                            data-term='TERM 1'
                                            data-subject='{$_REQUEST['subject_id']}'
                                            data-exam_id='ut1'
                                            data-col_name='ut1'
                                            data-max='80'>{$ut1}</span></td>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 2') {
                                    echo "<td>
                                        <span class='marks-tab'
                                            data-student_id='$sid'
                                            data-student_admission='$student_admission'
                                            data-term='TERM 1'
                                            data-subject='{$_REQUEST['subject_id']}'
                                            data-exam_id='ut2'
                                            data-col_name='ut2'
                                            data-max='80'>{$ut2}</span></td>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 3') {
                                    echo "<td>
                                        <span class='marks-tab'
                                            data-student_id='$sid'
                                            data-student_admission='$student_admission'
                                            data-term='TERM 2'
                                            data-subject='{$_REQUEST['subject_id']}'
                                            data-exam_id='ut3'
                                            data-col_name='ut3'
                                            data-max='80'>{$ut3}</span></td>";
                                } elseif ($_REQUEST['exam_id'] == 'UNIT TEST 4') {
                                    echo "<td>
                                        <span class='marks-tab'
                                            data-student_id='$sid'
                                            data-student_admission='$student_admission'
                                            data-term='TERM 2'
                                            data-subject='{$_REQUEST['subject_id']}'
                                            data-exam_id='ut4'
                                            data-col_name='ut4'
                                            data-max='80'>{$ut4}</span></td>";
                                } elseif ($_REQUEST['exam_id'] == 'TERM 1') {

                                    if ($_REQUEST['student_class'] == 'IX' || $_REQUEST['student_class'] == 'X') {

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='ma'
                                                data-col_name='ma'
                                                data-max='5'>{$ma}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='portfolio'
                                                data-col_name='portfolio'
                                                data-max='5'>{$portfolio}</span></td>";

                                        if ($_REQUEST['subject_id'] == '46') {

                                            echo "<td>
                                                <span class='marks-tab'
                                                    data-student_id='$sid'
                                                    data-student_admission='$student_admission'
                                                    data-term='TERM 1'
                                                    data-subject='{$_REQUEST['subject_id']}'
                                                    data-exam_id='se'
                                                    data-col_name='se'
                                                    data-max='10'>{$se}</span></td>";

                                            echo "<td>
                                                <span class='marks-tab'
                                                    data-student_id='$sid'
                                                    data-student_admission='$student_admission'
                                                    data-term='TERM 1'
                                                    data-subject='{$_REQUEST['subject_id']}'
                                                    data-exam_id='mid_term'
                                                    data-col_name='mid_term'
                                                    data-max='50'>{$mid_term}</span></td>";

                                        } else {

                                            echo "<td>
                                                <span class='marks-tab'
                                                    data-student_id='$sid'
                                                    data-student_admission='$student_admission'
                                                    data-term='TERM 1'
                                                    data-subject='{$_REQUEST['subject_id']}'
                                                    data-exam_id='se'
                                                    data-col_name='se'
                                                    data-max='5'>{$se}</span></td>";

                                            echo "<td>
                                                <span class='marks-tab'
                                                    data-student_id='$sid'
                                                    data-student_admission='$student_admission'
                                                    data-term='TERM 1'
                                                    data-subject='{$_REQUEST['subject_id']}'
                                                    data-exam_id='mid_term'
                                                    data-col_name='mid_term'
                                                    data-max='80'>{$mid_term}</span></td>";
                                        }

                                    } else {

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='ga'
                                                data-col_name='ga'
                                                data-max='20'>{$ga}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='nb'
                                                data-col_name='nb'
                                                data-max='5'>{$nb}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='sea'
                                                data-col_name='sea'
                                                data-max='5'>{$sea}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 1'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='mid_term'
                                                data-col_name='mid_term'
                                                data-max='80'>{$mid_term}</span></td>";
                                    }

                                } elseif ($_REQUEST['exam_id'] == 'TERM 2') {

                                    if ($_REQUEST['student_class'] == 'IX' || $_REQUEST['student_class'] == 'X') {

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='ma'
                                                data-col_name='ma'
                                                data-max='5'>{$ma}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='portfolio'
                                                data-col_name='portfolio'
                                                data-max='5'>{$portfolio}</span></td>";

                                        if ($_REQUEST['subject_id'] == '46') {

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='se'
                                                data-col_name='se'
                                                data-max='10'>{$se}</span></td>";
                                        }else {
                                            echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='se'
                                                data-col_name='se'
                                                data-max='5'>{$se}</span></td>";
                                        }

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='mid_term'
                                                data-col_name='mid_term'
                                                data-max='80'>{$mid_term}</span></td>";

                                    } else {

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='ga'
                                                data-col_name='ga'
                                                data-max='20'>{$ga}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='nb'
                                                data-col_name='nb'
                                                data-max='5'>{$nb}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='sea'
                                                data-col_name='sea'
                                                data-max='5'>{$sea}</span></td>";

                                        echo "<td>
                                            <span class='marks-tab'
                                                data-student_id='$sid'
                                                data-student_admission='$student_admission'
                                                data-term='TERM 2'
                                                data-subject='{$_REQUEST['subject_id']}'
                                                data-exam_id='mid_term'
                                                data-col_name='mid_term'
                                                data-max='80'>{$mid_term}</span></td>";
                                    }
                                }

                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    } // end if marks table
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer.php'); ?>

<script>
    function reset() {
        $("#update_frm").reset();
    }
</script>

<!-- Class / term based field toggle – unchanged from your original -->
<script>
$(document).ready(function () {
    function toggleUTFields() {
        var term = $('#term').val();
        if (term === 'TERM 1') {
            $('#ut1').closest('.form-group').show();
            $('#ut2').closest('.form-group').show();
            $('#ut3').closest('.form-group').hide();
            $('#ut4').closest('.form-group').hide();
            $('#mid_term').closest('.form-group').show();
        } else if (term === 'TERM 2') {
            $('#ut1').closest('.form-group').hide();
            $('#ut2').closest('.form-group').hide();
            $('#ut3').closest('.form-group').show();
            $('#ut4').closest('.form-group').show();
            $('#annual').closest('.form-group').show();
        } else {
            $('#ut1, #ut2, #ut3, #ut4, #mid_term, #annual').closest('.form-group').hide();
        }
    }

    toggleUTFields();

    $('#term').on('change', function () {
        toggleUTFields();
    });
});
</script>

<script>
$(document).ready(function () {
    function toggleClassBasedFields() {
        let selectedClass = $('#class_name').val()?.toUpperCase() || "";

        const middleClasses = ['III', 'IV', 'V', 'VI', 'VII', 'VIII'];
        const highClasses   = ['IX', 'X'];

        $('#ga, #nb, #sea, #ma, #portfolio, #se').closest('.form-group').hide();

        if (middleClasses.includes(selectedClass)) {
            $('#ga, #nb, #sea').closest('.form-group').show();
        } else if (highClasses.includes(selectedClass)) {
            $('#ma, #portfolio, #se').closest('.form-group').show();
        }
    }

    toggleClassBasedFields();

    $('#class_name').on('change', function () {
        toggleClassBasedFields();
    });
});
</script>

<!-- Subject selection AJAX (your old function, kept) -->
<script>
function selsub(sel) {
    $.ajax({
        type: 'post',
        data: {'class_name': sel},
        url: 'system/master_process.php?task=select_subject',
        success: function (data) {
            $("#subject_list").html(data);
        }
    })
}
</script>

<!-- NEW: Dynamic filter for language subjects AND marks-edit logic -->
<script>
$(document).ready(function () {

    // ------------ LANGUAGE SUBJECT FILTER (UNIT TEST vs TERM) ------------

    function isLanguageSubject(name) {
        let n = name.toLowerCase();
        return n.includes('hindi') || n.includes('english') || n.includes('sanskrit') || n.includes('urdu');
    }

    function isGrammarOrLiterature(name) {
        let n = name.toLowerCase();
        return n.includes('grammar') || n.includes('literature');
    }

    // function applySubjectFilter() {
    //     let examVal = $('#main_exam_select').val() || '';

    //     $('.teacher-row').each(function () {
    //         let $row        = $(this);
    //         let subjectName = $.trim($row.data('subject-name') || $row.find('.subject-name').text());

    //         let lang    = isLanguageSubject(subjectName);
    //         let gramLit = isGrammarOrLiterature(subjectName);

    //         if (!lang) {
    //             // Non-language subjects always visible
    //             $row.show();
    //             return;
    //         }

    //         let isUnit = examVal.indexOf('UNIT TEST') !== -1;
    //         let isTerm = (examVal === 'TERM 1' || examVal === 'TERM 2');

    //         // No exam selected -> show all
    //         if (!isUnit && !isTerm) {
    //             $row.show();
    //             return;
    //         }

    //         if (isUnit) {
    //             // UNIT TEST -> only Grammar/Literature rows of language
    //             if (gramLit) {
    //                 $row.show();
    //             } else {
    //                 $row.hide();
    //             }
    //             return;
    //         }

    //         if (isTerm) {
    //             // TERM 1 / TERM 2 -> only main language (no Grammar/Lit)
    //             if (!gramLit) {
    //                 $row.show();
    //             } else {
    //                 $row.hide();
    //             }
    //             return;
    //         }

    //         // Fallback
    //         $row.show();
    //     });
    // }
    
    function applySubjectFilter() {
    let examVal = $('#main_exam_select').val() || '';

    $('.teacher-row').each(function () {
        let $row = $(this);
        let subjectName = $.trim($row.data('subject-name') || $row.find('.subject-name').text());

        // 🚀 FIX: Don't filter IX/X at all
        let className = $row.find('input[name="student_class"]').val()?.trim().toUpperCase();
        if (className === "IX" || className === "X") {
            $row.show();
            return;
        }

        let lang    = isLanguageSubject(subjectName);
        let gramLit = isGrammarOrLiterature(subjectName);

        if (!lang) {
            $row.show();
            return;
        }

        let isUnit = examVal.indexOf('UNIT TEST') !== -1;
        let isTerm = (examVal === 'TERM 1' || examVal === 'TERM 2');

        if (!isUnit && !isTerm) {
            $row.show();
            return;
        }

        if (isUnit) {
            if (gramLit) $row.show();
            else $row.hide();
            return;
        }

        if (isTerm) {
            if (!gramLit) $row.show();
            else $row.hide();
            return;
        }

        $row.show();
    });
}


    // Apply on load and when top exam selection changes
    applySubjectFilter();

    $('#main_exam_select').on('change', function () {
        applySubjectFilter();
    });

    // ------------ MARKS EDIT PERMISSION ------------

    var canEdit = "<?= $can_edit ?>";
    console.log("Marks Entry Permission:", canEdit);

    if (canEdit === "YES") {

        // Convert spans to inputs on page load
        $('.marks-tab').each(function () {
            let $span        = $(this);
            let currentValue = $.trim($span.text());
            if (currentValue === '') currentValue = 0;

            let max = parseInt($span.data('max'), 10);
            if (isNaN(max)) max = 80;

            $span.data('previousValue', currentValue);

            let input = $('<input>', {
                type: 'number',
                class: 'marks-input',
                min: 0,
                max: max
            }).css({
                width: '60px',
                padding: '4px'
            });

            input.val(currentValue);
            $span.empty().append(input);
        });

        // Auto-save on blur
        $(document).on('blur', '.marks-input', function () {
            let $input   = $(this);
            let $span    = $input.closest('.marks-tab');
            let newValue = $.trim($input.val());
            if (newValue === '') newValue = 0;

            let prevValue = $span.data('previousValue');
            let max       = parseFloat($span.data('max')) || 100;

            if (parseFloat(newValue) > max) {
                alert("Marks can't be greater than " + max);
                $input.val(prevValue);
                return;
            }

            let studentAdmission = $span.data('student_admission');
            let studentId        = $span.data('student_id');
            let examId           = $span.data('exam_id');
            let subject          = $span.data('subject');
            let colName          = $span.data('col_name');
            let term             = $span.data('term');

            $input.prop('disabled', true);

            $.ajax({
                url: 'system/master_process.php?task=tabulation_single_marks',
                type: 'POST',
                dataType: 'json',
                data: {
                    student_admission: studentAdmission,
                    exam_id: examId,
                    student_id: studentId,
                    col_name: colName,
                    subject: subject,
                    term: term,
                    value: newValue
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $span.data('previousValue', newValue);
                        $input.val(newValue);
                    } else {
                        console.log('Error updating marks. Try again!');
                        $input.val(prevValue);
                    }
                    $input.prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    $input.val(prevValue);
                    $input.prop('disabled', false);
                }
            });
        });

    } else {
        // VIEW ONLY MODE
        $('.marks-tab').css({
            'font-weight': 'bold',
            'color': '#000'
        });
    }
});

$(document).on('submit', '.subject-form', function () {
    $('#subject_list_container').addClass('d-none');
});

</script>
