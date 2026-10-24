<?php require_once('system/header.php');?>
<?php require_once('system/menu.php');?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
	  <h1> CBSE Upload Result</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#transport">CBSE</a></li>
        <li class="breadcrumb-item active">Upload Result</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content" >
     
     <!-- Basic Forms -->
      <div class="box box-default" >
        <div class="box-header with-border">
          <h3 class="box-title">Upload Result (.txt File) </h3>
			
          <div class="box-tools pull-right">
	
		    <form method="post" onsubmit="return confirm('Are you sure to delete all records?')">
            <button name="delete_all" class="btn btn-danger">🗑️ Delete All Records</button>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body" style='background:url("assets/images/results.jpeg") 40% 30%;opacity:0.75;min-height:700px'>
            <?php
            
            $pdo = new PDO("mysql:host=localhost;dbname=$db_name", "$db_user", "$db_password");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Clean table
if (isset($_POST['delete_all'])) {
    $pdo->exec("DELETE FROM cbse_result");
    $msg = "All cbse_results records deleted successfully!";
}

// Handle upload
if (isset($_POST['upload']) && isset($_FILES['file']['tmp_name'])) {
    $filePath = $_FILES['file']['tmp_name'];
    $inserted = parseAndInsert($filePath, $pdo);
    $msg = "$inserted students result inserted successfully.";
}

// Parser + Inserter
// function parseAndInsert($filePath, $pdo) {
//     $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     $inserted = 0;

//     for ($i = 0; $i < count($lines); $i++) {
//         if (preg_match('/^\d{8}/', $lines[$i])) {
//             $line1 = $lines[$i];
//             $line2 = $lines[$i + 1] ?? '';

//             preg_match('/^(\d{8})\s+([MF])\s+(.+?)\s+((?:\d{3}\s+)+)(PASS|COMP|ABST)/', $line1, $m1);
//             if (!$m1) continue;

//             $roll     = trim($m1[1]);
//             $gender   = trim($m1[2]);
//             $name     = trim($m1[3]);
//             $subjects = preg_split('/\s+/', trim($m1[4]));
//             $result   = trim($m1[5]);

//             preg_match_all('/\d{2,3} [A-Z]\d|AB E/', $line2, $m2);
//             $marks = $m2[0];

//             $parsedMarks = [];
//             foreach ($marks as $mark) {
//                 if ($mark === 'AB E') {
//                     $parsedMarks[] = ['marks' => 'AB', 'grade' => 'E'];
//                 } else {
//                     [$mk, $gr] = explode(' ', $mark);
//                     $parsedMarks[] = ['marks' => $mk, 'grade' => $gr];
//                 }
//             }

//             $stmt = $pdo->prepare("INSERT IGNORE INTO cbse_result 
//                 (roll_no, name, gender, result, subjects, marks) 
//                 VALUES (?, ?, ?, ?, ?, ?)");
//             $stmt->execute([
//                 $roll, $name, $gender, $result,
//                 json_encode($subjects),
//                 json_encode($parsedMarks)
//             ]);
//             $inserted++;
//             $i++;
//         }
//     }
//     return $inserted;
// }

function parseAndInsert($filePath, $pdo) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $inserted = 0;

    for ($i = 0; $i < count($lines); $i++) {
        $line1 = trim($lines[$i]);
        $line2 = trim($lines[$i + 1] ?? '');

        // Skip if the line doesn't start with a roll number
        if (!preg_match('/^\d{8}/', $line1)) continue;

        // Match the line using a flexible pattern
        if (!preg_match('/^(\d{8})\s+([MF])\s+(.+?)\s+((?:\d{3}\s+)+)[A-Z0-9\s]+\s+(PASS|COMP|ABST)/', $line1, $m1)) continue;

        $roll   = $m1[1];
        $gender = $m1[2];
        $name   = trim($m1[3]);
        $subjectStr = trim($m1[4]);
        $result = $m1[5];

        // Dynamically extract subject codes into an array
        $subjects = preg_split('/\s+/', $subjectStr);

        // Parse marks and grades from the second line
        preg_match_all('/(\d{2,3}|AB)\s+([A-Z]\d)/', $line2, $m2, PREG_SET_ORDER);
        $marks = [];
        foreach ($m2 as $match) {
            $marks[] = [
                'marks' => $match[1],
                'grade' => $match[2]
            ];
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT IGNORE INTO cbse_result 
            (roll_no, name, gender, result, subjects, marks) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $roll,
            $name,
            $gender,
            $result,
            json_encode($subjects),
            json_encode($marks)
        ]);

        $inserted++;
        $i++; // Skip the next line, as it's already processed
    }

    return $inserted;
}

?>


<div class="container py-5">
    <div class="card shadow p-4 my-4" style="opacity:0.85;" >
        <h3 class="mb-4 text-center">📂 CBSE Gazette Uploader</h3>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="mb-4">
            <div class='row'>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                    <input type="file" name="file" class="form-control" required accept=".txt">
                </div>
                <div class="col-md-3">
                <button name="upload" class="btn btn-success">Upload & Insert</button>
                </div>
            </div>
        </form>

       
    </div>
</div>
		
		</div>
    </section>
</div>		
<?php require_once('system/footer2.php');?>
