<?php

if($_SERVER['CONTENT_TYPE'] == 'application/json' || strpos($_SERVER['CONTENT_TYPE'],'application/json') !== false){

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if(json_last_error() === JSON_ERROR_NONE){
        $_REQUEST = array_merge($_REQUEST, $data);
    }
}
header('Content-Type: application/pdf');

require_once("system/function.php");
require_once("fpdf/fpdf.php");

$_REQUEST = post_clean($_REQUEST);
extract($_REQUEST);

$branch_info = get_data('branch_list', $_SESSION['branch_id'])['data'];
$student = get_data("student", $student_admission,null,'student_admission')['data'];

if(!$student){
    die("Student Not Found");
}

$branch_images = [
    1 => 'assets/images/b1.JPG',
    2 => 'assets/images/b2.JPG',
    4 => 'assets/images/b3.JPG',
];

// $branch_id = $_SESSION['branch_id'];

if($branch_id == 1){
    $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
}
else if($branch_id == 2){
    $img_url = $base_url.'student_photo/Barauli/Photo/';
}
else{
    $img_url = $base_url.'student_photo/Barharia/Photo/';
}

$img_src = $base_url.$branch_images[$branch_id];

extract($student);

class AdmitCardPDF extends FPDF
{
    function Header()
    {
        global $img_src;

        if(!file_exists($img_src)){
            $this->Image($img_src,10,10,187,40);
        }
        
        $this->Ln(40);
    }
}

$pdf = new AdmitCardPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);


/* =====================
   EXAM TITLE
===================== */

$exam_name = get_data('exam_list',$exam_id,'exam_name')['data'];

$pdf->SetFillColor(210,210,210);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,$exam_name." - ".date('Y')." (HALL TICKET)",1,1,'C',true);

$pdf->Ln(3);


/* =====================
   STUDENT DETAILS
===================== */

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,6,'Admission No :',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,$student_admission,0,0);

// Photo
// if($student_photo != ''){
    $pdf->Image($img_url.$student_admission.'.jpg',160,60,30,35);
// }

$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,6,'Class & Section :',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,$student_class.' '.$student_section,0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,6,'Roll No :',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,$student_roll,0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,6,'Name :',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,$student_name,0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,6,"Father's Name :",0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,$student_father,0,1);

$pdf->Ln(5);


/* =====================
   TIME TABLE HEADER
===================== */

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,7,"Exam Time Table For Class - ".$student_class,1,1,'C');

$pdf->SetFont('Arial','B',10);
$pdf->Cell(35,7,'Date',1);
$pdf->Cell(75,7,'Subject',1);
$pdf->Cell(40,7,'Start Time',1);
$pdf->Cell(40,7,'End Time',1);
$pdf->Ln();


/* =====================
   FETCH TIME TABLE
===================== */

$sql1 = "SELECT DISTINCT exam_date 
         FROM admit_card 
         WHERE exam_id='$exam_id'
         AND student_class='$student_class'
         AND exam_date!='0000-00-00'
         ORDER BY exam_date";

$res = direct_sql($sql1)['data'];

foreach($res as $row)
{
    $edate = $row['exam_date'];

    $routine = direct_sql("
        SELECT * FROM admit_card 
        WHERE student_class='$student_class'
        AND exam_date='$edate'
        ORDER BY start_time
    ")['data'];

    foreach($routine as $r)
    {
        if($r['subject_id']!=''){
            $subject = get_data('subject',$r['subject_id'],'subject_name')['data'];
        } else {
            $subject = strtoupper($r['subject_name']);
        }

        if($subject!='')
        {
            $pdf->Cell(35,7,date('d-M-Y',strtotime($edate)),1);
            $pdf->Cell(75,7,$subject,1);
            $pdf->Cell(40,7,date('h:i A',strtotime($r['start_time'])),1);
            $pdf->Cell(40,7,date('h:i A',strtotime($r['end_time'])),1);
            $pdf->Ln();
        }
    }
}

$pdf->Ln(5);


/* =====================
   INSTRUCTIONS
===================== */

$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,6,'Instructions :',0,1);

$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,5," Hall Ticket is mandatory for examinations.");

$pdf->Ln(15);


/* =====================
   SIGNATURE
===================== */

if($branch_info['exam_controller_sign']!=''){
    $pdf->Image($base_url.'upload/'.$branch_info['exam_controller_sign'],150,$pdf->GetY(),40);
}

$pdf->Ln(15);
$pdf->SetX(150);
$pdf->Cell(40,6,'Controller of Examination',0,1,'C');


/* =====================
   OUTPUT PDF
===================== */

$pdf->Output("","Admit_Card_".$student_admission.".pdf");
?>
