<?php
include_once('./system/function.php');
include('fpdf/fpdf.php');

// ✅ GET MULTIPLE IDS
$ids = [];

if(isset($_GET['student_ids'])){
    $ids = explode(',', $_GET['student_ids']);
} elseif(isset($_GET['student_id'])){
    $ids[] = $_GET['student_id']; // fallback (single)
}

$pdf = new FPDF('L', 'mm', 'A5');

foreach($ids as $student_id){

    $student_id = trim($student_id);
    if(empty($student_id)) continue;

    $student = get_data('student', $student_id)['data'];
    $promotion_status = get_data('promotion_status', $student_id,null,'student_id')['data'];

    if(empty($student)) continue;

    // 🔥 DATA
    $finaldues = finaldues($student_id);
    $grades = get_final_subject_grades($student_id, $student['student_class']);

    $e_count = 0;

    if(!empty($grades)){
        foreach($grades as $g){
            if(strtoupper($g) == 'E'){
                $e_count++;
            }
        }
    }

    // 🔥 STATUS
    $status = 'PROMOTED';
    if($finaldues > 0 || $e_count >= 2){
        $status = 'WITHHELD';
    }

    // ✅ NEW PAGE PER STUDENT
    $pdf->AddPage();

    // Border
    $pdf->SetLineWidth(0.2);

    // Header
    $branch_images = [
        1 => 'assets/images/b1.JPG',
        2 => 'assets/images/b2.JPG',
        4 => 'assets/images/b3.JPG',
    ];
    $pdf->Image($img_src = $branch_images[$_SESSION['branch_id']], 8, 8, 190, 25);
    
   // ✅ DEFINE SERVER PATH ONLY (NO URL)
    if($_SESSION['branch_id']==1){
        $folder_path = $_SERVER['DOCUMENT_ROOT'].'/binex/student_photo/Bishambharpur/Photo/';
    }else if($_SESSION['branch_id']==2){
        $folder_path = $_SERVER['DOCUMENT_ROOT'].'/binex/student_photo/Barauli/Photo/';
    }else{
        $folder_path = $_SERVER['DOCUMENT_ROOT'].'/binex/student_photo/Barharia/Photo/';
    }
    
    // ✅ FILE NAME
    $file_name = $student['student_admission'].'.jpg';
    
    // ✅ FULL LOCAL PATH
    $photo_file = $folder_path . $file_name;
    
    // ✅ DEFAULT IMAGE (LOCAL PATH)
    $default_image = $_SERVER['DOCUMENT_ROOT'].'/binex/assets/images/user.jpg';
    
    // ✅ CHECK + LOAD
    if (!empty($file_name) && file_exists($photo_file)) {
        $pdf->Image($photo_file, 170, 40, 20, 23, 'JPG'); // ✅ LOCAL PATH
    } else {
        $pdf->Image($default_image, 170, 40, 20, 23, 'PNG');
    }
        
    $principal = get_data('branch_list',$branch_id)['data'];
    if($principal!=''){
        $principal = $base_url.'upload/'.$principal['principal_sign'];
    }else{
        $principal = 'assets/images/new_sign.png';
    }
    $pdf->Image($principal, 148, 110, 35, 20, 'PNG');
    
    $pdf->SetAutoPageBreak(false);
    $pdf->Ln(22);

    // Title
    $pdf->SetFont('Arial','B',13);
    $pdf->SetFillColor(204,204,204);
    $pdf->Cell(190,8,'PROMOTION CARD (Academic Session 2025-2026)',0,1,'C',true);

    $pdf->SetFont('Arial','',11);
    $pdf->Ln(2);

    // TEXT
    $pdf->MultiCell(140,6,
    "This is to certify that Master / Miss ".$student['student_name']."
S/o Mr. ".$student['student_father']." a student of Grade ".$student['student_class'].' - '.$student['student_section']."
has successfully completed the academic requirements of 2025-2026."
    );

    $pdf->Ln(3);

    // Fee Status
    $pdf->Cell(50,7,'Fee Status:',1,0);
    $pdf->Cell(140,7, ($finaldues > 0 ? 'Pending' : 'Cleared'),1,1);

    // Dues
    $pdf->Cell(50,7,'Final Dues:',1,0);
    $pdf->Cell(140,7,$finaldues,1,1);

    // Status
    $pdf->Cell(50,7,'Promotion Status:',1,0);
    $pdf->SetFont('Arial','B',11);
    if($promotion_status['status']=='CONDITIONALLY PROMOTED'){
        $pdf->Cell(140,7,$promotion_status['status'].' ('.$promotion_status['remark'].')',1,1);
    }
    else if($promotion_status['status']=='PROMOTED'){
        $pdf->Cell(140,7,'PROMOTED TO NEXT CLASS',1,1);
    }else{
        $pdf->Cell(140,7,$promotion_status['status'],1,1);
    }

    $pdf->SetFont('Arial','',11);
    $pdf->Ln(3);

    // Note
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(190,5,
    "Admission to the next higher class is valid only after clearance of all pending dues."
    );

    $pdf->Ln(10);

    // Footer Section
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(190,6,'For School Use',1,1,'C');

    $pdf->SetFont('Arial','B',10);

    $pdf->Ln(15);
    $pdf->Cell(65,10,'Class Teacher',0,0,'C');
    $pdf->Cell(60,10,'Exam In-charge',0,0,'C');
    $pdf->Cell(65,10,'Principal',0,1,'C');

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(190,6,'------------------------------------------------------------------------------------------------------------------------------------------------------',0,1,'C');
}

// ✅ OUTPUT ONE PDF (ALL STUDENTS)
$pdf->Output('I', 'Promotion_Cards.pdf');
?>