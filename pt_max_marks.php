<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');

if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
}
else{	
	$fee = insert_row('pt_max_marks');
	$id = $fee['id'];
}

if($id!='')
{
  $res = get_data('pt_max_marks',$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
  }
}

// Fetch dynamic classes from branch configuration
$branch_data = get_data('branch_list', $_SESSION['branch_id'] ?? 1, null, 'branch_id')['data'];
$branch_classes = explode(",", $branch_data['class_list']);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1> PT Maximum Marks Setting </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#settings">Admin Settings</a></li>
            <li class="breadcrumb-item active">PT Max Marks</li>
        </ol>
    </section>

    <section class="content">
        <div class='box box-default'>
            <div class="box-header with-border">
                <h3 class="box-title">Manage PT Max Marks Settings</h3>
                <div class="box-tools pull-right">
                    <div class='float-right'><a class='fa fa-plus text-orange btn btn-xs btn-outline-warning' href='pt_max_marks' title='Add New Config'> Add New Settings</a></div> 
                </div>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form action='update_pt_max_marks' method='post' id='update_frm' enctype='multipart/form-data'>
                            <input class="form-control" value='<?php echo $id; ?>' name='id' type='hidden'>
                            <input type="hidden" name="branch_id" value="<?php echo $_SESSION['branch_id'] ?? 1; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Class <?= imp() ?></label>
                                        <select class="form-control select2" name="class_category" id="class_name" required>
                                            <option value="">Select Class</option>
                                            <?php
                                            foreach ($branch_classes as $branch_class) {
                                                $branch_class = trim($branch_class);
                                                $selected = (isset($class_category) && $branch_class == $class_category) ? "selected" : "";
                                                echo "<option value='$branch_class' $selected>$branch_class</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <select class="form-control select2" name="subject_id" id="subject_name">
                                            <option value="">Default (All Subjects)</option>
                                            <?php
                                            if (isset($class_category) && $class_category != '') {
                                                echo dropdown_where('subject', 'id', 'subject_name', ['branch_id' => $_SESSION['branch_id'] ?? 1], $subject_id ?? '');
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Subject Type</label>
                                        <select class="form-control" name='subject_type' required>
                                            <option value='Main Subjects' <?php if(isset($subject_type) && $subject_type == 'Main Subjects') echo 'selected'; ?>>Main Subjects</option>
                                            <option value='Additional Subjects' <?php if(isset($subject_type) && $subject_type == 'Additional Subjects') echo 'selected'; ?>>Additional Subjects</option>
                                            <option value='Alternate/Additional' <?php if(isset($subject_type) && $subject_type == 'Alternate/Additional') echo 'selected'; ?>>Alternate/Additional</option>
                                            <option value='None' <?php if(isset($subject_type) && $subject_type == 'None') echo 'selected'; ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name='status' required>
                                            <option value='ACTIVE' <?php if(isset($status) && $status == 'ACTIVE') echo 'selected'; ?>>ACTIVE</option>
                                            <option value='HIDE' <?php if(isset($status) && $status == 'HIDE') echo 'selected'; ?>>HIDE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-blue mt-3 mb-2" style="border-bottom: 2px solid #ddd; padding-bottom: 5px;">Main Subjects Configuration</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>SEA-I Max Marks (Main)</label>
                                        <input type="text" class="form-control calc-trigger" name="sea_max" placeholder="e.g. 10, 5, or -" value="<?php echo $sea_max ?? '-'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>MA-I Max Marks (Main)</label>
                                        <input type="text" class="form-control calc-trigger" name="ma_max" placeholder="e.g. 10, 5, or -" value="<?php echo $ma_max ?? '-'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PT-I Max Marks (Main)</label>
                                        <input type="text" class="form-control calc-trigger" name="pt_max" placeholder="e.g. 30, 40, or -" value="<?php echo $pt_max ?? '-'; ?>">
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-orange mt-3 mb-2" style="border-bottom: 2px solid #ddd; padding-bottom: 5px;">Additional Subjects Configuration</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Marks Obtained Max (Add.)</label>
                                        <input type="text" class="form-control calc-trigger" name="pt_add_max" placeholder="e.g. 30, 40, or -" value="<?php echo $pt_add_max ?? '-'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Project Max (Add.)</label>
                                        <input type="text" class="form-control calc-trigger" name="sea_add_max" placeholder="e.g. 10, or -" value="<?php echo $sea_add_max ?? '-'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Max Marks</label>
                                        <input type="text" class="form-control" id="total_max" name="total_max" placeholder="Automatically calculated or entered" value="<?php echo $total_max ?? '-'; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <input type="button" class="btn btn-info" value='Save Max Marks' id='update_btn' >
                                <a href="pt_max_marks" class="btn btn-default">Cancel</a>
                            </div>
                        </form> 
                    </div>
                </div>

                <hr style="margin: 30px 0;">

                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id='example1'>
                                <thead>
                                    <tr class="bg-gray-light">
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Subject Type</th>
                                        <th>SEA-I (Main)</th>
                                        <th>MA-I (Main)</th>
                                        <th>PT-I (Main)</th>
                                        <th>Marks Obtained (Add.)</th>
                                        <th>Project (Add.)</th>
                                        <th>Total Marks</th>
                                        <th>Status</th>
                                        <th>Operation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = get_all('pt_max_marks', '*', ['branch_id'=>$_SESSION['branch_id'] ?? 1]);
                                    if($res['count']>0)
                                    {
                                        foreach($res['data'] as $row)
                                        {
                                            $row_id = $row['id'];
                                            // Skip completely blank rows that could be left over from aborted insert_row actions
                                            if (empty($row['class_category']) && empty($row['subject_type'])) {
                                                continue;
                                            }
                                            
                                            $sub_name = 'Default (All Subjects)';
                                            if (!empty($row['subject_id'])) {
                                                $sub_val = get_data('subject', $row['subject_id'], 'subject_name')['data'];
                                                if (!empty($sub_val)) {
                                                    $sub_name = htmlspecialchars($sub_val);
                                                }
                                            }
                                            
                                            echo "<tr>";
                                            echo "<td><strong>" . htmlspecialchars($row['class_category']) . "</strong></td>";
                                            echo "<td><span class='badge bg-purple'>" . $sub_name . "</span></td>";
                                            echo "<td>" . htmlspecialchars($row['subject_type']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['sea_max']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['ma_max']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['pt_max']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['pt_add_max']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['sea_add_max']) . "</td>";
                                            echo "<td><span class='badge bg-navy'>" . htmlspecialchars($row['total_max']) . "</span></td>";
                                            echo "<td>";
                                            if ($row['status'] == 'ACTIVE') {
                                                echo "<span class='badge bg-success'>ACTIVE</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>HIDDEN</span>";
                                            }
                                            echo "</td>";
                                            ?>
                                            <td>
                                                <a href='pt_max_marks?link=<?php echo encode('id='.$row_id); ?>' class='fa fa-edit btn btn-info btn-xs' title="Edit"></a>
                                                <span class='delete_btn btn btn-danger btn-xs' data-table='pt_max_marks' data-id='<?php echo $row_id;?>' data-pkey='id' title="Delete"><i class='fa fa-trash'></i></span>
                                            </td>
                                            </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php');?>

<script>
$(document).ready(function() {
    function autoCalculateTotal() {
        var subType = $('select[name="subject_type"]').val();
        var total = 0;
        var hasValidSum = false;

        if (subType === 'Main Subjects' || subType === 'Alternate/Additional') {
            var sea = parseFloat($('input[name="sea_max"]').val());
            var ma = parseFloat($('input[name="ma_max"]').val());
            var pt = parseFloat($('input[name="pt_max"]').val());

            if (!isNaN(sea)) { total += sea; hasValidSum = true; }
            if (!isNaN(ma)) { total += ma; hasValidSum = true; }
            if (!isNaN(pt)) { total += pt; hasValidSum = true; }
        } else if (subType === 'Additional Subjects') {
            var ptAdd = parseFloat($('input[name="pt_add_max"]').val());
            var seaAdd = parseFloat($('input[name="sea_add_max"]').val());

            if (!isNaN(ptAdd)) { total += ptAdd; hasValidSum = true; }
            if (!isNaN(seaAdd)) { total += seaAdd; hasValidSum = true; }
        }

        if (hasValidSum) {
            $('#total_max').val(total);
        }
    }

    $('.calc-trigger').on('input change', function() {
        autoCalculateTotal();
    });

    $('select[name="subject_type"]').on('change', function() {
        autoCalculateTotal();
    });

    // Dynamic subject loading
    $('#class_name').change(function() {
        let className = $(this).val();
        let branchId = <?= $_SESSION['branch_id'] ?? 1 ?>;
        let selectedSubId = "<?= $subject_id ?? '' ?>";

        if (className != "") {
            $.ajax({
                url: 'system/master_process?task=get_subjects',
                type: 'POST',
                data: {
                    class_name: className,
                    branch_id: branchId
                },
                success: function(response) {
                    $('#subject_name').html(response);
                    // Add back default option at the top
                    $('#subject_name').prepend('<option value="">Default (All Subjects)</option>');
                    if (selectedSubId != "") {
                        $('#subject_name').val(selectedSubId);
                    } else {
                        $('#subject_name').val("");
                    }
                },
                error: function() {
                    alert("Failed to load subjects");
                }
            });
        } else {
            $('#subject_name').html('<option value="">Default (All Subjects)</option>');
        }
    });

    // Trigger change if we are in Edit mode with predefined class
    <?php if (isset($class_category) && $class_category != '') { ?>
    $('#class_name').trigger('change');
    <?php } ?>
});
</script>
