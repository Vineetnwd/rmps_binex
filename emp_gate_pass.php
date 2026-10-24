<?php
include_once('./system/op_lib.php');
include('fpdf/fpdf.php');

$gid = $_GET['id']; 
$gate_pass = get_data('leave_details',$gid)['data'];
$emp = get_data('employee',$gate_pass['emp_id'])['data'];
// Create instance of FPDF - A5 landscape
$pdf = new FPDF('L', 'mm', 'A5');
$pdf->AddPage();

// Add border
$pdf->SetLineWidth(0.8); 
$pdf->Rect(5, 5, 200, 122, 'D');
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
$pdf->Cell(190, 8, 'Gate Pass (Employee)', 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);
$pdf->Ln(1);

$pdf->Cell(50, 7, 'Date:', 1, 0);
$pdf->Cell(90, 7, date('d-M-Y', strtotime($gate_pass['from_date'])), 1, 0);
$pdf->Cell(50, 7, "", 'TR', 1, 'C');

$pdf->Cell(50, 7, 'Name of Employee:', 1, 0);
$pdf->Cell(90, 7, $emp['name'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

// $pdf->Cell(50, 7, "Class:", 1, 0);
// $pdf->Cell(90, 7, $emp['student_class'].' - '.$emp['student_roll'], 1, 0);
// $pdf->Cell(50, 7, '', 'R', 1);

// $pdf->Cell(50, 7, 'Escorted By:', 1, 0);
// $pdf->Cell(90, 7, $gate_pass['escorted_by'], 1, 0);
// $pdf->Cell(50, 7, '', 'R', 1);

// $pdf->Cell(50, 7, 'Relation:', 1, 0);
// $pdf->Cell(90, 7, $gate_pass['relation'], 1, 0);
// $pdf->Cell(50, 7, '', 'BR', 1);

$pdf->Cell(50, 7, 'Time In:', 1, 0);
$pdf->Cell(90, 7, $gate_pass['in_time'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

$pdf->Cell(50, 7, 'Time Out:', 1, 0);
$pdf->Cell(90, 7, $gate_pass['out_time'], 1, 0);
$pdf->Cell(50, 7, '', 'R', 1);

$pdf->Cell(50, 7, 'Cause:', 1, 0);
$pdf->Cell(90, 7, $gate_pass['leave_cause'], 1, 0);
$pdf->Cell(50, 7, '', 'BR', 1);

$pdf->Cell(50, 7, 'Remark:', 1, 0);
$pdf->Cell(140, 7, $gate_pass['remarks'], 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 14, 'Employee Signature  :', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(140, 14, $gate_pass['escort_mobile'], 1, 1);

// Signature boxes
$pdf->Cell(95, 18, '', 1, 0);
$pdf->Cell(95, 18, '', 1, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, 'Reception', 1, 0, 'C');
$pdf->Cell(95, 6, 'Principal', 1, 1, 'C');

// Output PDF
$pdf->Output('I', 'Gate Pass.pdf');
?>
