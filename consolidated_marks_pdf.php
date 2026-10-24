<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('fpdf/fpdf.php');
require_once('system/function.php'); 

$blist = get_data('branch_list',$branch_id)['data'];

class PDF extends FPDF {
    function Header() {
        global $full_name;
        global $blist;
        
        $this->Image('https://rmpublicschool.org/assets/img/logo.png', 10, 8, 155,30);
        $this->Image('assets/images/cbse.png', 165, 8, 0,35);
        
        $this->Cell(0, 25, '', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $blist['inst_address1'].$blist['inst_address2'], 0, 1, 'C');
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'CONSOLIDATED MARKSHEET', 0, 1, 'C');
        $this->Ln(4);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'Generated on ' . date('d-m-Y'), 0, 0, 'L');
        $this->Cell(0, 5, 'Page ' . $this->PageNo().'/{nb}', 0, 0, 'R');
    }
}

$pdf = new PDF('P', 'mm', 'A4'); 
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10); 

// GET POST DATA
$exam_id = $_POST['exam_id'] ?? '';
$student_class = $_POST['student_class'] ?? '';
$student_section = $_POST['student_section'] ?? '';

// Fetch students
$sql = "SELECT *
        FROM student 
        WHERE student_class = '$student_class' 
          AND student_section = '$student_section' 
          AND status = 'ACTIVE'
          AND branch_id = $branch_id
        ORDER BY student_roll";
$students = direct_sql($sql, 'get', false)['data'] ?? [];

// Get subjects
$subjects = subject_list($student_class);

// Loop each student
foreach ($students as $st) {
    $pdf->AddPage();
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(200, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->Cell(0, 8, "Student's Profile", 1, 1, 'C', true);

    $pdf->SetTextColor(0);
    // Student Info Table
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 8, 'Exam:', 1, 0, 'L');
    $pdf->Cell(30, 8, $exam_id, 1, 0, 'L');
    $pdf->Cell(35, 8, 'Student Name:', 1, 0, 'L');
    $pdf->Cell(65, 8, $st['student_name'], 1, 1, 'L');
    
    $pdf->Cell(30, 8, 'Class:', 1, 0, 'L');
    $pdf->Cell(30, 8, $student_class.' - '.$st['student_section'], 1, 0, 'L');
    $pdf->Cell(35, 8, 'Roll No:', 1, 0, 'L');
    $pdf->Cell(65, 8, $st['student_roll'], 1, 1, 'L');
    
    $pdf->Cell(30, 8, 'Student Mother:', 1, 0, 'L');
    $pdf->Cell(65, 8, $st['student_mother'], 1, 0, 'L');
    $pdf->Cell(30, 8, 'D.O.B:', 1, 0, 'L');
    $pdf->Cell(35, 8, date('d - m - Y', strtotime($st['date_of_birth'])), 1, 1, 'L');
    
    $pdf->Cell(30, 8, 'Student Father:', 1, 0, 'L');
    $pdf->Cell(65, 8, $st['student_father'], 1, 0, 'L');
    $pdf->Cell(30, 8, 'House:', 1, 0, 'L');
    $pdf->Cell(35, 8, $st['house_name'], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 8, 'Student Address:', 1, 0, 'L');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, $st['student_address2'], 1, 0, 'L');

    // Student Photo
    if (!empty($st['student_photo']) && file_exists('upload/'.$st['student_photo'])) {
        $pdf->Image('upload/'.$st['student_photo'], 172, 62, 25, 30);
    }else{
        $pdf->Image('upload/no_image.jpg', 172, 62, 25, 30);
    }

    $pdf->Ln(15);

    // Chart
    $chart_path = "temp/chart_{$st['student_admission']}.png";
    if (file_exists($chart_path)) {
        $pdf->Image($chart_path, 15, $pdf->GetY(), 80, 50);
        $pdf->Ln(55);
    }

    // Marks Table
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(200, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->Cell(100, 8, 'Subject', 1, 0, 'C', true);
    $pdf->Cell(0, 8, 'Marks', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0);
    $total = 0;
    foreach ($subjects as $subject_id) {
        $subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
        $marks = get_tabulation_marks($st['student_admission'], 'TERM 1', $subject_id, ['ut1','ut2','mid_term']);
        $pdf->Cell(100, 8, $subject_name, 1);
        $pdf->Cell(0, 8, $marks['total'], 1, 1, 'C');
        $total += $marks['total'];
    }

    // Total
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(100, 8, 'Total', 1);
    $pdf->Cell(0, 8, $total, 1, 1, 'C');

    $pdf->Ln(5);

    // // Remarks & Attendance
    // $pdf->SetFont('Arial', 'B', 10);
    // $pdf->Cell(0, 8, 'Remarks & Attendance', 1, 1, 'C', true);
    // $pdf->SetFont('Arial', '', 9);
    // $pdf->MultiCell(0, 6, "General remarks here...\nAttendance: 95%", 1);
}

$pdf->Output('I', 'Class '.$student_class.' - '.$student_section.'.pdf');
