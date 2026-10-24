<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');
$result_type_list = ['PASS','ABST','COMP'];

?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
	  <h1> View CBSE Results </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#transport">CBSE</a></li>
        <li class="breadcrumb-item active">View Result</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">View/Search/Sort/Export Result </h3>
			
          <div class="box-tools pull-right">
              <form>
    	        <select name='result_type' onchange="submit()">
    	            <?= dropdown($result_type_list , @$_GET['result_type']) ?>
    	        </select>
	        </form>
		   
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
          
		<?php
// DB connection

$conn = new mysqli($host_name, $db_user, $db_password, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get all records
if(isset($_GET['result_type']) and $_GET['result_type'] !='')
{
    $result_type =$_GET['result_type']??'PASS';
    $sql = "SELECT * FROM cbse_result WHERE result = '$result_type'";
}
else{
   $sql = "SELECT * FROM cbse_result"; 
}

$result = $conn->query($sql);

$allSubjects = [];
$students = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $subjects = json_decode($row['subjects'], true);
        $marks = json_decode($row['marks'], true);
        
        
$assoc = [];

foreach ($subjects as $index => $key) {
    if (isset($marks[$index])) {
        $assoc[$key] = $marks[$index]['marks'];
    } else {
        $assoc[$key] = null; // or set to some default value
    }
}
        if (is_array($subjects)) {
            $i=0;
            foreach ($subjects as $sub) {
                $allSubjects[$sub] = $marks[$i]['marks'];
            }
        }

        $students[] = [
            'roll_no' => $row['roll_no'],
            'name' => $row['name'],
            'gender' => $row['gender'],
            'result' => $row['result'],
            'marks' => $assoc
        ];
    }
}

$subjectHeaders = array_keys($allSubjects);
sort($subjectHeaders); // Optional: alphabetically sort subjects
?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id='example'>
            <thead class="table-dark">
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <?php foreach ($subjectHeaders as $sub): ?>
                    <th><?= htmlspecialchars($sub) ?></th>
                    <?php endforeach; ?>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['roll_no']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['gender']) ?></td>
                        <?php foreach ($subjectHeaders as $sub): ?>
                            <td><?= isset($student['marks'][$sub]) ? htmlspecialchars($student['marks'][$sub]) : '-' ?></td>
                        <?php endforeach; ?>
                        <td><?= htmlspecialchars($student['result']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr><td colspan="<?= 4 + count($subjectHeaders) ?>" class="text-center">No data found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

		</div>
    </section>
</div>		
<?php require_once('system/footer2.php');?>
