<?php
include_once('./system/op_lib.php');
include('fpdf/fpdf.php');

$gid = $_GET['id']; 
$gate_pass = get_data('gate_pass',$gid)['data'];
$student = get_data('student', $gate_pass['student_id'])['data'];

// Create instance of FPDF - A5 landscape
$pdf = new FPDF('L', 'mm', 'A5');
$pdf->AddPage();

// Add border
$pdf->SetLineWidth(0.8); 
$pdf->Rect(5, 5, 200, 137, 'D');
$pdf->SetLineWidth(0.2); 

// Header Image
$pdf->Image('assets/images/b1.JPG', 8, 8, 190, 25);

// Student Photo
$escortimg = "system/gatepass/" . $gate_pass['photo'];
if(file_exists($escortimg)){
    $pdf->Image($escortimg, 150, 41, 50, 35, 'JPG');
}

$pdf->SetAutoPageBreak(false);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(204, 204, 204); 
$pdf->Cell(0, 22, '', 0, 1); // move below image
$pdf->Cell(.5, 8, '', 0, 0);
$pdf->Cell(190, 8, 'Gate Pass (Student)', 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);
$pdf->Ln(1);

$pdf->Cell(50, 7, 'Date:', 1, 0);
$pdf->Cell(90, 7, date('d-M-Y', strtotime($gate_pass['escort_date'])), 1, 0);
$pdf->Cell(50, 7, "", 'TR', 1, 'C');

$pdf->Cell(50, 7, 'Name of Student:', 1, 0);
$pdf->Cell(90, 7, $student['student_name'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

$pdf->Cell(50, 7, "Class:", 1, 0);
$pdf->Cell(90, 7, $student['student_class'].' - '.$student['student_roll'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

$pdf->Cell(50, 7, 'Escorted By:', 1, 0);
$pdf->Cell(90, 7, $gate_pass['escorted_by'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

$pdf->Cell(50, 7, 'Relation:', 1, 0);
$pdf->Cell(90, 7, $gate_pass['relation'], 1, 0);
$pdf->Cell(50, 7, '', 'BR', 1);

$pdf->Cell(50, 7, 'Time In:', 1, 0);
$pdf->Cell(140, 7, $gate_pass['time_in'], 1, 1);

$pdf->Cell(50, 7, 'Time Out:', 1, 0);
$pdf->Cell(140, 7, $gate_pass['time_out'], 1, 1);

$pdf->Cell(50, 7, 'Reason:', 1, 0);
$pdf->Cell(140, 7, $gate_pass['reason'], 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 14, 'Signature & Contact :', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(140, 14, $gate_pass['escort_mobile'], 1, 1);

// Signature boxes
$pdf->Cell(50, 18, '', 1, 0);
$pdf->Cell(47, 18, '', 1, 0);
$pdf->Cell(47, 18, '', 1, 0);
$pdf->Cell(46, 18, '', 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, 'Admin', 1, 0, 'C');
$pdf->Cell(47, 6, 'Transport', 1, 0, 'C');
$pdf->Cell(47, 6, 'Class Teacher', 1, 0, 'C');
$pdf->Cell(46, 6, 'Principal', 1, 1, 'C');

// Output PDF
$pdf->Output('I', 'Gate Pass.pdf');
?>
