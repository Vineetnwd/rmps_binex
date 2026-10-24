<?php
require_once('system/function.php');
require_once('fpdf/fpdf.php'); // your FPDF path



//----------------------
// 1. GET POST VALUES
//----------------------
$student_class  = $_POST['student_class'] ?? '';
$student_section = $_POST['student_section'] ?? '';
$subject = $_POST['subject'] ?? '';
$subject_name = $_POST['subject_name'] ?? '';
$term = $_POST['term'] ?? '';

if (!$student_class || !$student_section || !$subject || !$term) {
    die("Missing required parameters");
}

$student_class  = mysqli_real_escape_string($con, $student_class);
$student_section = mysqli_real_escape_string($con, $student_section);
$subject = mysqli_real_escape_string($con, $subject);
$term = mysqli_real_escape_string($con, $term);

//----------------------
// 2. TOTAL STUDENTS
//----------------------
$total_sql = "SELECT COUNT(*) AS total_students FROM student
              WHERE student_class='$student_class'
              AND student_section='$student_section'
              AND status='ACTIVE'";
$resTotal = direct_sql($total_sql);
$total_students = $resTotal['data'][0]['total_students'] ?? 0;

//----------------------
// 3. MAIN QUERY (same as your DomPDF)
//----------------------
$sql = "SELECT
    total.total_students,
    
    -- UT1 Statistics (Full marks: 50)
    -- Pass = 33% of 50 = 16.5, 40% = 20, 50% = 25, 70% = 35, 80% = 40, 90% = 45
    COUNT(t.ut1) AS ut1_appeared,
    ROUND((COUNT(t.ut1) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_appeared_percent,
    COUNT(CASE WHEN t.ut1 >= 16.5 THEN 1 END) AS ut1_passed,
    ROUND((COUNT(CASE WHEN t.ut1 >= 16.5 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_passed_percent,
    COUNT(CASE WHEN t.ut1 < 16.5 THEN 1 END) AS ut1_failed,
    ROUND((COUNT(CASE WHEN t.ut1 < 16.5 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_failed_percent,
    COUNT(CASE WHEN t.ut1 >= 45 THEN 1 END) AS ut1_90_plus,
    ROUND((COUNT(CASE WHEN t.ut1 >= 45 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_90_plus_percent,
    COUNT(CASE WHEN t.ut1 >= 40 AND t.ut1 < 45 THEN 1 END) AS ut1_80_89,
    ROUND((COUNT(CASE WHEN t.ut1 >= 40 AND t.ut1 < 45 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_80_89_percent,
    COUNT(CASE WHEN t.ut1 >= 35 AND t.ut1 < 40 THEN 1 END) AS ut1_70_79,
    ROUND((COUNT(CASE WHEN t.ut1 >= 35 AND t.ut1 < 40 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_70_79_percent,
    COUNT(CASE WHEN t.ut1 >= 25 AND t.ut1 < 35 THEN 1 END) AS ut1_50_69,
    ROUND((COUNT(CASE WHEN t.ut1 >= 25 AND t.ut1 < 35 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_50_69_percent,
    COUNT(CASE WHEN t.ut1 >= 20 AND t.ut1 < 25 THEN 1 END) AS ut1_40_49,
    ROUND((COUNT(CASE WHEN t.ut1 >= 20 AND t.ut1 < 25 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_40_49_percent,
    COUNT(CASE WHEN t.ut1 >= 16.5 AND t.ut1 < 20 THEN 1 END) AS ut1_33_39,
    ROUND((COUNT(CASE WHEN t.ut1 >= 16.5 AND t.ut1 < 20 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut1_33_39_percent,

    -- UT2 Statistics (Full marks: 50)
    COUNT(t.ut2) AS ut2_appeared,
    ROUND((COUNT(t.ut2) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_appeared_percent,
    COUNT(CASE WHEN t.ut2 >= 16.5 THEN 1 END) AS ut2_passed,
    ROUND((COUNT(CASE WHEN t.ut2 >= 16.5 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_passed_percent,
    COUNT(CASE WHEN t.ut2 < 16.5 THEN 1 END) AS ut2_failed,
    ROUND((COUNT(CASE WHEN t.ut2 < 16.5 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_failed_percent,
    COUNT(CASE WHEN t.ut2 >= 45 THEN 1 END) AS ut2_90_plus,
    ROUND((COUNT(CASE WHEN t.ut2 >= 45 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_90_plus_percent,
    COUNT(CASE WHEN t.ut2 >= 40 AND t.ut2 < 45 THEN 1 END) AS ut2_80_89,
    ROUND((COUNT(CASE WHEN t.ut2 >= 40 AND t.ut2 < 45 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_80_89_percent,
    COUNT(CASE WHEN t.ut2 >= 35 AND t.ut2 < 40 THEN 1 END) AS ut2_70_79,
    ROUND((COUNT(CASE WHEN t.ut2 >= 35 AND t.ut2 < 40 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_70_79_percent,
    COUNT(CASE WHEN t.ut2 >= 25 AND t.ut2 < 35 THEN 1 END) AS ut2_50_69,
    ROUND((COUNT(CASE WHEN t.ut2 >= 25 AND t.ut2 < 35 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_50_69_percent,
    COUNT(CASE WHEN t.ut2 >= 20 AND t.ut2 < 25 THEN 1 END) AS ut2_40_49,
    ROUND((COUNT(CASE WHEN t.ut2 >= 20 AND t.ut2 < 25 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_40_49_percent,
    COUNT(CASE WHEN t.ut2 >= 16.5 AND t.ut2 < 20 THEN 1 END) AS ut2_33_39,
    ROUND((COUNT(CASE WHEN t.ut2 >= 16.5 AND t.ut2 < 20 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS ut2_33_39_percent,

    -- MTE Statistics (Full marks: 80)
    -- Pass = 33% of 80 = 26.4, 40% = 32, 50% = 40, 70% = 56, 80% = 64, 90% = 72
    COUNT(t.mid_term) AS mte_appeared,
    ROUND((COUNT(t.mid_term) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_appeared_percent,
    COUNT(CASE WHEN t.mid_term >= 26.4 THEN 1 END) AS mte_passed,
    ROUND((COUNT(CASE WHEN t.mid_term >= 26.4 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_passed_percent,
    COUNT(CASE WHEN t.mid_term < 26.4 THEN 1 END) AS mte_failed,
    ROUND((COUNT(CASE WHEN t.mid_term < 26.4 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_failed_percent,
    COUNT(CASE WHEN t.mid_term >= 72 THEN 1 END) AS mte_90_plus,
    ROUND((COUNT(CASE WHEN t.mid_term >= 72 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_90_plus_percent,
    COUNT(CASE WHEN t.mid_term >= 64 AND t.mid_term < 72 THEN 1 END) AS mte_80_89,
    ROUND((COUNT(CASE WHEN t.mid_term >= 64 AND t.mid_term < 72 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_80_89_percent,
    COUNT(CASE WHEN t.mid_term >= 56 AND t.mid_term < 64 THEN 1 END) AS mte_70_79,
    ROUND((COUNT(CASE WHEN t.mid_term >= 56 AND t.mid_term < 64 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_70_79_percent,
    COUNT(CASE WHEN t.mid_term >= 40 AND t.mid_term < 56 THEN 1 END) AS mte_50_69,
    ROUND((COUNT(CASE WHEN t.mid_term >= 40 AND t.mid_term < 56 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_50_69_percent,
    COUNT(CASE WHEN t.mid_term >= 32 AND t.mid_term < 40 THEN 1 END) AS mte_40_49,
    ROUND((COUNT(CASE WHEN t.mid_term >= 32 AND t.mid_term < 40 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_40_49_percent,
    COUNT(CASE WHEN t.mid_term >= 26.4 AND t.mid_term < 32 THEN 1 END) AS mte_33_39,
    ROUND((COUNT(CASE WHEN t.mid_term >= 26.4 AND t.mid_term < 32 THEN 1 END) / NULLIF(total.total_students, 0)) * 100, 2) AS mte_33_39_percent

FROM tabulation t
JOIN student s ON s.id = t.student_id
CROSS JOIN (
    SELECT COUNT(*) AS total_students
    FROM student 
    WHERE student_class = '$student_class'
      AND student_section = '$student_section'
      AND status = 'ACTIVE'
) AS total
WHERE 
    s.student_class = '$student_class'
    AND s.student_section = '$student_section'
    AND s.status = 'ACTIVE'
    AND t.term = '$term'
    AND t.subject = '$subject'";

$res = direct_sql($sql);
$stats = $res['data'][0] ?? [];

//---------------------------
// 4. REPORT ROWS
//---------------------------
$report_rows = [
    [1, 'Total Students in Class', $total_students, 100, $total_students, 100, $total_students, 100],
    [2, 'No. of Students Appeared', 
        $stats['ut1_appeared'] ?? 0, $stats['ut1_appeared_percent'] ?? 0, 
        $stats['ut2_appeared'] ?? 0, $stats['ut2_appeared_percent'] ?? 0,
        $stats['mte_appeared'] ?? 0, $stats['mte_appeared_percent'] ?? 0
    ],
    [3, 'No. of Students Passed (>=33%)', 
        $stats['ut1_passed'] ?? 0, $stats['ut1_passed_percent'] ?? 0, 
        $stats['ut2_passed'] ?? 0, $stats['ut2_passed_percent'] ?? 0,
        $stats['mte_passed'] ?? 0, $stats['mte_passed_percent'] ?? 0
    ],
    [4, 'No. of Students Failed (<33%)', 
        $stats['ut1_failed'] ?? 0, $stats['ut1_failed_percent'] ?? 0, 
        $stats['ut2_failed'] ?? 0, $stats['ut2_failed_percent'] ?? 0,
        $stats['mte_failed'] ?? 0, $stats['mte_failed_percent'] ?? 0
    ],
    [5, 'Scored 90% and above',
        $stats['ut1_90_plus'] ?? 0, $stats['ut1_90_plus_percent'] ?? 0,
        $stats['ut2_90_plus'] ?? 0, $stats['ut2_90_plus_percent'] ?? 0,
        $stats['mte_90_plus'] ?? 0, $stats['mte_90_plus_percent'] ?? 0
    ],
    [6, 'Scored 80% - 89.99%',
        $stats['ut1_80_89'] ?? 0, $stats['ut1_80_89_percent'] ?? 0,
        $stats['ut2_80_89'] ?? 0, $stats['ut2_80_89_percent'] ?? 0,
        $stats['mte_80_89'] ?? 0, $stats['mte_80_89_percent'] ?? 0
    ],
    [7, 'Scored 70% - 79.99%',
        $stats['ut1_70_79'] ?? 0, $stats['ut1_70_79_percent'] ?? 0,
        $stats['ut2_70_79'] ?? 0, $stats['ut2_70_79_percent'] ?? 0,
        $stats['mte_70_79'] ?? 0, $stats['mte_70_79_percent'] ?? 0
    ],
    [8, 'Scored 50% - 69.99%',
        $stats['ut1_50_69'] ?? 0, $stats['ut1_50_69_percent'] ?? 0,
        $stats['ut2_50_69'] ?? 0, $stats['ut2_50_69_percent'] ?? 0,
        $stats['mte_50_69'] ?? 0, $stats['mte_50_69_percent'] ?? 0
    ],
    [9, 'Scored 40% - 49.99%',
        $stats['ut1_40_49'] ?? 0, $stats['ut1_40_49_percent'] ?? 0,
        $stats['ut2_40_49'] ?? 0, $stats['ut2_40_49_percent'] ?? 0,
        $stats['mte_40_49'] ?? 0, $stats['mte_40_49_percent'] ?? 0
    ],
    [10, 'Scored 33% - 39.99%',
        $stats['ut1_33_39'] ?? 0, $stats['ut1_33_39_percent'] ?? 0,
        $stats['ut2_33_39'] ?? 0, $stats['ut2_33_39_percent'] ?? 0,
        $stats['mte_33_39'] ?? 0, $stats['mte_33_39_percent'] ?? 0
    ],
];

//--------------------------------
// 5. CREATE PDF WITH FPDF
//--------------------------------
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->SetFillColor(255, 0, 0);

// Header
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0,10,'R M PUBLIC SCHOOL, BISHAMBHRAPUR, GOPALGANJ',0,1,'C',true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','',13);
$pdf->Cell(0,8,'Pro-forma for Comparative Study / Result Analysis',0,1,'C');

$pdf->Ln(5);

// Info Box
$pdf->SetFont('Arial','',10);
$pdf->Cell(60,6,"Class: $student_class",0,0,'L');
$pdf->Cell(60,6,"Section: $student_section",0,0,'L');
$pdf->Cell(60,6,"Subject: $subject_name",0,1,'L');

$pdf->Cell(60,6,"Term: $term",0,0,'L');
$pdf->Cell(60,6,"Total Students: $total_students",0,0,'L');
$pdf->Cell(60,6,"Generated: ".date('d-M-Y H:i A'),0,1,'L');

$pdf->Ln(2);

// Full Marks Info
$pdf->SetFont('Arial','I',9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0,5,"Full Marks: UT-1 = 50 | UT-2 = 50 | MTE = 80 | Pass Marks (33%): UT = 16.5 | MTE = 26.4",0,1,'C');
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(3);

//-------------------------------
// TABLE HEADER
//-------------------------------
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(52, 58, 64); // Dark gray
$pdf->SetTextColor(255, 255, 255);

$w = [12, 60, 20, 20, 20, 20, 20, 20]; // widths

$pdf->Cell($w[0],10,'Sr.',1,0,'C',true);
$pdf->Cell($w[1],10,'Particulars',1,0,'C',true);
$pdf->Cell($w[2]+$w[3],10,'UT-1 (50)',1,0,'C',true);
$pdf->Cell($w[4]+$w[5],10,'UT-2 (50)',1,0,'C',true);
$pdf->Cell($w[6]+$w[7],10,'MTE (80)',1,1,'C',true);

$pdf->Cell($w[0],8,'',1,0,'C',true);
$pdf->Cell($w[1],8,'',1,0,'C',true);
$pdf->Cell($w[2],8,'No.',1,0,'C',true);
$pdf->Cell($w[3],8,'%',1,0,'C',true);
$pdf->Cell($w[4],8,'No.',1,0,'C',true);
$pdf->Cell($w[5],8,'%',1,0,'C',true);
$pdf->Cell($w[6],8,'No.',1,0,'C',true);
$pdf->Cell($w[7],8,'%',1,1,'C',true);

$pdf->SetTextColor(0, 0, 0);

//-------------------------------
// TABLE BODY
//-------------------------------
$pdf->SetFont('Arial','',9);

// Row colors based on type
$row_colors = [
    1 => [233, 236, 239],  // Total - gray
    2 => [255, 255, 255],  // Appeared - white
    3 => [212, 237, 218],  // Passed - green
    4 => [248, 215, 218],  // Failed - red
    5 => [212, 237, 218],  // 90%+ - green
    6 => [204, 229, 255],  // 80-89% - blue
    7 => [204, 229, 255],  // 70-79% - blue
    8 => [255, 243, 205],  // 50-69% - yellow
    9 => [255, 243, 205],  // 40-49% - yellow
    10 => [248, 215, 218], // 33-39% - red
];

foreach($report_rows as $row){
    $sr = $row[0];
    
    // Set fill color based on row
    if(isset($row_colors[$sr])){
        $pdf->SetFillColor($row_colors[$sr][0], $row_colors[$sr][1], $row_colors[$sr][2]);
        $fill = true;
    } else {
        $fill = false;
    }
    
    // Bold for first row
    if($sr == 1){
        $pdf->SetFont('Arial','B',9);
    } else {
        $pdf->SetFont('Arial','',9);
    }
    
    $pdf->Cell($w[0],7,$row[0],1,0,'C',$fill);
    $pdf->Cell($w[1],7,$row[1],1,0,'L',$fill);
    $pdf->Cell($w[2],7,$row[2],1,0,'C',$fill);
    $pdf->Cell($w[3],7,$row[3].'%',1,0,'C',$fill);
    $pdf->Cell($w[4],7,$row[4],1,0,'C',$fill);
    $pdf->Cell($w[5],7,$row[5].'%',1,0,'C',$fill);
    $pdf->Cell($w[6],7,$row[6],1,0,'C',$fill);
    $pdf->Cell($w[7],7,$row[7].'%',1,1,'C',$fill);
}

// Marks Conversion Reference
$pdf->Ln(5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(0,5,'Marks Conversion Reference:',0,1,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,4,'UT (50 marks): 90%=45, 80%=40, 70%=35, 50%=25, 40%=20, 33%=16.5',0,1,'L');
$pdf->Cell(0,4,'MTE (80 marks): 90%=72, 80%=64, 70%=56, 50%=40, 40%=32, 33%=26.4',0,1,'L');

// Footer
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);

// Create a clickable link cell
$pdf->SetTextColor(255,0,0);
$pdf->Cell(0,5, "Generated by $dev_by | ".date('d-M-Y H:i:s A'), 0, 1, 'C', false, $dev_url);

// Reset text color
$pdf->SetTextColor(0,0,0);

//-------------------------------
// OUTPUT PDF
//-------------------------------
$filename = "Exam_Analysis_{$subject_name}_{$student_class}_{$student_section}_".date('Y-m-d').".pdf";
$pdf->Output('D', $filename);
exit;
?>
