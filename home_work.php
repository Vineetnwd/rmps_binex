<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $home_work = decode($_GET['link']);
    $id = $home_work['id'];
    $isedit = 'yes';
} else {
    $home_work = insert_row('home_work');
    $isedit = 'no';
    $id = $home_work['id'];
}

if ($id != '') {
    $res = get_data('home_work', $id);
    if ($res['count'] > 0) {
        extract($res['data']);
    }
}
// $classVal = isset($home_work['class']) ? $home_work['class'] : '';
// $sectionVal = isset($home_work['section']) ? $home_work['section'] : '';
$classVal = isset($class) ? $class : '';
$sectionVal = isset($section) ? $section : '';

$emp_cls = '';

if ($_SESSION['user_type'] === 'TEACHER') {
    $emp = get_data('employee', $_SESSION['user_name'], null, 'emp_code')['data'];
    $emp_cls = isset($emp['allow_class']) ? $emp['allow_class'] : '';
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1> New Home Work</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Home Work</a></li>
            <li class="breadcrumb-item active">New Home Work</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Home Work Details </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <form action='update_home_work' id='insert_frm' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Home Work Date</label>
                                <input class="form-control" value='<?php echo date('Y-m-d'); ?>' min='<?php echo date('Y-m-d'); ?>' name='hw_date' type='date'>
                                <input value='<?= $id ?>' name='id' type='hidden'>
                            </div>
                            <div class="form-group">
                                <label>Class</label>
                                <select class="form-control form-select" name="class" id="class" required onchange="populateSections(this.value)">
                                    <option value="" disabled <?= $classVal === '' ? 'selected' : '' ?>>Select Class</option>
                                    <?php
                                    if (!empty($emp_cls)) {
                                        $classSections = explode(',', $emp_cls);
                                        $classes = [];
                                        foreach ($classSections as $item) {
                                            $class = explode('-', $item)[0];
                                            if (!in_array($class, $classes)) {
                                                $classes[] = $class;
                                                $selected = ($class === $classVal) ? 'selected' : '';
                                                echo "<option value='$class' $selected>$class</option>";
                                            }
                                        }
                                    } else {
                                        dropdown($class_list, $classVal);
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Section</label>
                                <select class="form-control form-select" name="section" id="section" required>
                                   <?= dropdown($section_list, @$section) ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Home Work Title</label>
                                <input class="form-control" value='<?= $hw_title ?>' name='hw_title' required>
                            </div>
                            <div class="form-group">
                                <label>Details of Home Work</label>
                                <textarea class="form-control" name="hw_details" required><?= $hw_details ?></textarea>
                            </div>
                            <div class="checkbox">
                                <label></label>
                                <input class="form-control" type='hidden' name='hw_attachment' id='targetimg'>
                            </div>
                        </form>
                        <form id='uploadForm' enctype='multipart/form-data'>
                            <div class="form-group mb-3">
                                <label>Home Work Attachment </label>
                                <input type='file' name='uploadimg' id='uploadimg' accept='image' class='form-control'>
                            </div>
                            <div id='display'></div>
                        </form>
                        <div class="box-tool mt-4 bg-white">
                            <button class="btn btn-danger" id='insert_btn'>Assign Home Work</button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <table id="data_tbl" class="table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Class - Section</th>
                                    <th>Attachment </th>
                                    <th>Operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "select * from home_work where status != 'AUTO' order by id desc";
                                $res = direct_sql($query);
                                foreach ((array) $res['data'] as $row) {
                                    echo "<tr>";
                                    $home_work_id = $row['id'];
                                    echo "<td> " . date('d M', strtotime($row['hw_date'])) . "</td>";
                                    echo "<td> " . $row['hw_title'] . "</td>";
                                    echo "<td>". $row['class'].' - '.$row['section'] ."</td>";
                                    if ($row['hw_attachment'] == "") {
                                        echo "<td> NO ATTACHMENT </td>";
                                    } else {
                                        echo "<td> <a href='system/upload/" . $row['hw_attachment'] . "' target='new' download> Download </a></td>";
                                    }
                                ?>
                                    <td align='right'>
                                        <?php 
                                        $link = encode("id=" . $home_work_id);
                                        echo "<a href='home_work?link=$link' class=' btn btn-info btn-xs text-light m-1' title='Edit Information '> <i class='fa fa-edit'></i> </a>";
                                        echo "<button class='delete_btn btn btn-danger btn-xs' data-table='home_work' data-id='$home_work_id' data-pkey='id' title='Detete This Permanently'  > <i class='fa fa-trash'></i> </button> ";
                                        ?>
                                    </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>
<script>
const classSections = <?php echo isset($emp_cls) ? json_encode(explode(',', $emp_cls)) : '[]'; ?>;
const selectedSection = "<?= $sectionVal ?>";

function populateSections(selectedClass) {
    const sectionDropdown = document.getElementById("section");
    sectionDropdown.innerHTML = "<option value='' disabled>Select Section</option>";

    classSections.forEach(item => {
        const [className, section] = item.split("-");
        if (className === selectedClass && section) {
            const option = document.createElement("option");
            option.value = section;
            option.textContent = section;
            if (section === selectedSection) {
                option.selected = true;
            }
            sectionDropdown.appendChild(option);
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const classSelect = document.getElementById("class");
    if (classSelect && classSelect.value) {
        populateSections(classSelect.value);
    }
});
</script>
