<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php

$table = 'admit_card';

$student_class = $_GET['student_class'] ?? '';
$exam_id       = $_GET['exam_id'] ?? '';

?>

<div class="content-wrapper">

    <section class="content-header">

        <h1>Generate Admit Card</h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="#transport">Exam</a>
            </li>

            <li class="breadcrumb-item active">
                Admit Card
            </li>
        </ol>

    </section>

    <section class="content">

        <div class="box box-default">

            <div class="box-header with-border">

                <h3 class="box-title">
                    Generate Admit Card
                </h3>

                <div class="box-tools pull-right">

                    <form>

                        <select name='exam_id' required>
                            <?php dropdown_list('exam_list','id','exam_name',$exam_id); ?>
                        </select>

                        <select name='student_class' required>
                            <?php dropdown($class_list,$student_class); ?>
                        </select>

                        <button class='btn btn-dark'>
                            Show Subjects
                        </button>

                    </form>

                </div>

            </div>

            <div class="box-body">

                <p class='text-danger'>

                    Set Past Date (expired) if Subject not Applicable

                    <button
                        class='btn btn-success float-right'
                        id='update_btn'
                        type="submit"
                        form="update_frm">

                        SAVE

                    </button>

                </p>

                <div class='row bg-dark text-light p-2 mb-3'>

                    <div class="col-3">
                        <label>Subject Name</label>
                    </div>

                    <div class="col-3">
                        <label>Exam Date</label>
                    </div>

                    <div class="col-2">
                        <label>Start Time</label>
                    </div>

                    <div class="col-2">
                        <label>End Time</label>
                    </div>

                    <div class="col-2">
                        <label>Sitting Name</label>
                    </div>

                </div>

                <form
                    action="update_admit_card"
                    method="post"
                    id="update_frm">

                    <input
                        type="hidden"
                        name="exam_id"
                        value="<?php echo $exam_id; ?>">

                    <input
                        type="hidden"
                        name="student_class"
                        value="<?php echo $student_class; ?>">

                    <div id="subject_container">

<?php

if($student_class!=''){

    // =========================
    // CLASS SUBJECTS
    // =========================

    $sub_list = subject_list($student_class);

    if(!is_array($sub_list)){
        $sub_list = [];
    }

    $shown_subjects = [];

    foreach($sub_list as $sub_id){

        $sub_name = get_data(
            'subject',
            $sub_id,
            'subject_name'
        )['data'] ?? '';

        $query = "
            SELECT *
            FROM admit_card
            WHERE exam_id='$exam_id'
            AND student_class='$student_class'
            AND subject_id='$sub_id'
            LIMIT 1
        ";

        $run  = mysqli_query($con,$query);
        $find = mysqli_fetch_assoc($run);

        $shown_subjects[] = trim($sub_name);

?>

    <div class='row m-0 subject-row mb-2'>

        <div class="form-group col-3">

            <input
                type="hidden"
                name="subject_id[]"
                value="<?php echo $sub_id; ?>">

            <input
                type="text"
                class="form-control"
                name="subject_name[]"
                value="<?php echo $sub_name; ?>"
                required>

        </div>

        <div class="form-group col-3">

            <input
                class="form-control"
                type="date"
                name="exam_date[]"
                value="<?php echo $find['exam_date'] ?? ''; ?>">

        </div>

        <div class="form-group col-2">

            <input
                class="form-control"
                type="time"
                name="start_time[]"
                value="<?php echo $find['start_time'] ?? ''; ?>">

        </div>

        <div class="form-group col-2">

            <input
                class="form-control"
                type="time"
                name="end_time[]"
                value="<?php echo $find['end_time'] ?? ''; ?>">

        </div>

        <div class="form-group col-2 d-flex">

            <select
                name="sitting_name[]"
                class="form-control mr-2">

                <?php dropdown($sitting_list,$find['sitting_name'] ?? ''); ?>

            </select>

            <button
                type="button"
                class="btn btn-danger btn-sm remove-row">

                X

            </button>

        </div>

    </div>

<?php
    }

    // =========================
    // MANUAL SUBJECTS
    // =========================

    $query = "
        SELECT *
        FROM admit_card
        WHERE exam_id='$exam_id'
        AND student_class='$student_class'
    ";

    $run = mysqli_query($con,$query);

    while($manual = mysqli_fetch_assoc($run)){

        $manual_name = trim($manual['subject_name'] ?? '');
        $manual_sub_id = trim($manual['subject_id'] ?? '');

        if(
            ($manual_sub_id=='' || $manual_sub_id=='0')
            &&
            !in_array($manual_name,$shown_subjects)
        ){
?>

    <div class='row m-0 subject-row mb-2'>

        <div class="form-group col-3">

            <input
                type="hidden"
                name="subject_id[]"
                value="">

            <input
                type="text"
                class="form-control"
                name="subject_name[]"
                value="<?php echo $manual_name; ?>"
                required>

        </div>

        <div class="form-group col-3">

            <input
                class="form-control"
                type="date"
                name="exam_date[]"
                value="<?php echo $manual['exam_date'] ?? ''; ?>">

        </div>

        <div class="form-group col-2">

            <input
                class="form-control"
                type="time"
                name="start_time[]"
                value="<?php echo $manual['start_time'] ?? ''; ?>">

        </div>

        <div class="form-group col-2">

            <input
                class="form-control"
                type="time"
                name="end_time[]"
                value="<?php echo $manual['end_time'] ?? ''; ?>">

        </div>

        <div class="form-group col-2 d-flex">

            <select
                name="sitting_name[]"
                class="form-control mr-2">

                <?php dropdown($sitting_list,$manual['sitting_name'] ?? ''); ?>

            </select>

            <button
                type="button"
                class="btn btn-danger btn-sm remove-row">

                X

            </button>

        </div>

    </div>

<?php
        }
    }
}
?>

                <!--    </div>

                    <button
                        type="button"
                        id="add_subject"
                        class="btn btn-primary mt-3">

                        + Add Subject

                    </button>

                </form>

            </div>

        </div>

    </section>

</div> -->

<?php require_once('system/footer.php'); ?>

<script>

function subjectRowHTML(){

    return `
        <div class="row m-0 subject-row mb-2">

            <div class="form-group col-3">

                <input
                    type="hidden"
                    name="subject_id[]"
                    value="">

                <input
                    class="form-control"
                    type="text"
                    name="subject_name[]"
                    placeholder="Enter Subject Name"
                    required>

            </div>

            <div class="form-group col-3">

                <input
                    class="form-control"
                    type="date"
                    name="exam_date[]">

            </div>

            <div class="form-group col-2">

                <input
                    class="form-control"
                    type="time"
                    name="start_time[]">

            </div>

            <div class="form-group col-2">

                <input
                    class="form-control"
                    type="time"
                    name="end_time[]">

            </div>

            <div class="form-group col-2 d-flex">

                <select
                    name="sitting_name[]"
                    class="form-control mr-2">

                    <?php dropdown($sitting_list,''); ?>

                </select>

                <button
                    type="button"
                    class="btn btn-danger btn-sm remove-row">

                    X

                </button>

            </div>

        </div>
    `;
}

document.getElementById('add_subject').addEventListener('click',function(){

    document
        .getElementById('subject_container')
        .insertAdjacentHTML(
            'beforeend',
            subjectRowHTML()
        );

});

document.addEventListener('click',function(e){

    if(e.target.classList.contains('remove-row')){

        e.target
            .closest('.subject-row')
            .remove();
    }
});

</script>