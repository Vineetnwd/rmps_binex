<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once('./system/op_lib.php');
include('fpdf/fpdf.php');

// STUDENT DATA 
// $data = decode($_GET['app_no']);
$app_no = $_GET['app_no']; 

$student = get_data('scholorship', $app_no, null, 'app_no')['data'];
// Sample data (replace with actual data from your form)
$name = $student['name'] ?? 'John Doe';
$fatherName = $student['father_name'] ?? 'Richard Roe';
$category = $student['category'] ?? 'Junior';
$studentClass = $student['student_class'] ?? '8';
$mobile = $student['mobile'] ?? '9876543210';
$email = $student['email'] ?? 'john.doe@example.com';
$dob = $student['date_of_birth'] ?? '2005-01-01';
$appNo = $student['app_no'] ?? 'APP123456';
$amount = $student['amount'] ?? '1000';
$doa = $student['created_at'] ?? '00-00-0000';
$payment_status = $student['payment_status'] ?? 'UNPAID';
$current_school = $student['current_school'] ?? 'R M Public School';

$qr_value= "Name : ". $name.', Application No. : '.$appNo .', Category : '.$category.', Payment : '.$payment_status;
$qr = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=$qr_value";


// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage('P', 'A4');
$pdf->SetLineWidth(1); 
$pdf->Rect(10, 10, 190, 277, 'D');
$pdf->Image('assets/images/b1.JPG','13',13,'180','35','JPG');
// $pdf->Image($qr,'172',260,'20','20','PNG');

// Disable automatic page breaks
$pdf->SetAutoPageBreak(false);

$pdf->Cell(0, 40, '', 0, 1);
$pdf->SetFillColor(204, 204, 204); 
// $pdf->SetTextColor(255, 0, 0);
// Add title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(.5, 10, '', 0, 0);
$pdf->Cell(188.8, 10, 'Scholorship Application Receipt', 0, 1, 'C',true);
$pdf->SetTextColor(0, 0, 0); 
$pdf->Ln(0.5);

$pdf->Cell(105, 5, '', 'R', 1);

// Add application details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(45, 10, 'Application No:', 0, 0);
$pdf->Cell(60, 10, $appNo, 'R', 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45, 10, 'Important Dates:-', 0, 0);
$pdf->Cell(50, 10, '', 0, 1);
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(45, 10, 'Name:', 0, 0);
$pdf->Cell(60, 10, $name, 'R', 0);
$pdf->Cell(4, 10, '', 0, 0);
$pdf->Cell(90, 10, 'Application Start: 20-DEC-2024 ', 0, 1);

$pdf->Cell(45, 10, "Father's Name:", 0, 0);
$pdf->Cell(60, 10, $fatherName, 'R', 0);
$pdf->Cell(3, 10, '', 0, 0);
$pdf->Cell(90, 10, ' Last Date of Application: 01-MAR-2025 ', 0, 1,'L');

$pdf->Cell(45, 10, 'Category:', 0, 0);
$pdf->Cell(60, 10, $category, 'R', 0);
$pdf->Cell(4, 10, '', 0, 0);
$pdf->Cell(90, 10, 'Date of Written Test: 02-MAR-2025', 0, 1,'L');

$pdf->Cell(45, 10, 'Class:', 0, 0);
$pdf->Cell(60, 10, $studentClass, 'R', 0);
$pdf->Cell(4, 10, '', 0, 0);
$pdf->Cell(90, 10, 'Result of Written Test: 05-MAR-2025', 0, 1,'L');

$pdf->Cell(45, 10, 'Mobile:', 0, 0);
$pdf->Cell(60, 10, $mobile, 'R', 0);
$pdf->Cell(4, 10, '', 0, 0);
$pdf->Cell(90, 10, 'Date of Personal Interview: 09-MAR-2025', 0, 1,'L');


$pdf->Cell(45, 10, 'Date of Birth:', 0, 0);
$pdf->Cell(60, 10, date('d-m-Y', strtotime($dob)), 'R', 0);

$pdf->Cell(4, 10, '', 0, 0);
$pdf->Cell(90, 10, 'Date of Final Result: 10-MAR-2025', 0, 1,'L');

$pdf->Cell(45, 10, 'Current School:', 0, 0);
$pdf->Cell(60, 10, $current_school, 'R', 1);

$pdf->Cell(45, 10, 'Date of Apply:', 0, 0);
$pdf->Cell(60, 10, date('d-m-Y', strtotime($doa)), 'R', 1);

$pdf->Cell(45, 10, 'Email:', 0, 0);
$pdf->Cell(60, 10, $email, 'R', 1);

$pdf->Cell(45, 10, 'Payment Status', 'B', 0);
$pdf->Cell(60, 10, $payment_status, 'BR', 0);
$pdf->Cell(85, 10, '', 'B', 1);

$objectives = [
    "Identify Talented Students: Recognize students with outstanding academic and extracurricular potential.",
    "Provide Financial Aid: Offer scholarships to students who exhibit merit and need financial support.",
    "Promote Inclusivity: Encourage diverse participation from all socio-economic backgrounds.",
    "Enhance School Reputation: Uphold RMPS's commitment to excellence and quality education."
];

$pdf->SetFont('Arial', 'B', 14);  // Slightly smaller font
$pdf->SetFillColor(204, 204, 204);
$pdf->Cell(.5, 10, '', 0, 0);
$pdf->Cell(188.8, 10, 'Objectives :-', 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 10);  // Slightly smaller font
$pdf->SetTextColor(0, 0, 0); 
$pdf->MultiCell(0, 5, implode("\n", $objectives));

$pdf->SetFont('Arial', 'B', 14); 
$pdf->SetFillColor(204, 204, 204); 
$pdf->Cell(.5, 10, '', 0, 0);
$pdf->Cell(188.8, 10, 'Examination Details :-', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0); 

$pdf->SetFont('Arial', '', 10);  // Slightly smaller font
$pdf->SetTextColor(0, 0, 0); 

// Phase 1 details
$pdf->SetFont('Arial', 'B', 12); 
$pdf->MultiCell(0, 7, "Phase 1: Written Test");
$pdf->SetFont('Arial', 'B', 10); 
$pdf->MultiCell(0, 5, "     Subjects: ");
$pdf->SetFont('Arial', '', 10); 
$pdf->MultiCell(0, 5, "             Mathematics, EVS, English, Hindi & Mental Ability for Classes I to V (Junior); ");
$pdf->MultiCell(0, 5, "             Mathematics, Science, English, Social Studies, Hindi & Mental Ability for Classes VI to IX (Senior).");
$pdf->SetFont('Arial', 'B', 10); 
$pdf->MultiCell(0, 5, "     Marking Scheme:");
$pdf->SetFont('Arial', '', 10); 
$pdf->MultiCell(0, 5, "             Junior - 15 Questions 15 Minutes for Each Subjects. 15x5= 75 Questions 75 minutes");
$pdf->MultiCell(0, 5, "             Senior - 20 Questions 20 Minutes for Each Subjects. 20x6= 120 Questions 120 minutes");

// Phase 2 details
$pdf->Ln(5);  // Add space between phases
$pdf->SetFont('Arial', 'B', 14); 
$pdf->MultiCell(0, 5, "Phase 2: Interview");
$pdf->SetFont('Arial', '', 10); 
$pdf->MultiCell(0, 5, "     Personal interview to assess the student's interests, aspirations, and extracurricular achievements.");

$pdf->Ln(10);  // Add space after the examination section

// $pdf->Cell(50, 10, 'Amount Paid:', 0, 0);
// $pdf->Cell(0, 10, 'Rs. ' . $amount, 0, 1);

// Add footer
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 9);  // Slightly smaller font
if ($payment_status == 'UNPAID') {
    $pdf->Cell(0, 10, 'Please Carry this Application Reciept for paying registration fee of 1000/- on the Cash Counter', 0, 1, 'L');
}
$pdf->Cell(0, 10, '---------------Thank you for your application----------------', 0, 1, 'C');
// $pdf->Cell(0, 10, 'Please keep this receipt for future reference.', 0, 1, 'L');

// Output PDF
$pdf->Output('I', 'Application_Receipt.pdf');
?>
