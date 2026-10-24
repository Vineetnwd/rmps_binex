<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('system/function.php');
require('fpdf/fpdf.php');
closing_update();

$txn_date = ($_REQUEST['txn_date'])?$_REQUEST['txn_date']:$today;

// $total  = get_all('student',)['count'];
$active  = get_all('student',['id'],['status'=>"ACTIVE"])['count'];
$inactive  = get_all('student',['id'],['status'=>"INACTIVE"])['count'];
$blocked = get_all('student',['id'],['status'=>"BLOCK"])['count'];
$total = $active+$inactive+$blocked;

$boys  = get_all('student',['id'],['student_sex'=>"MALE",'status'=>"ACTIVE"])['count'];
$girls  = get_all('student',['id'],['student_sex'=>"FEMALE",'status'=>"ACTIVE"])['count'];

$transport  = get_all('student',['id'],['student_type'=>"TRANSPORT",'status'=>"ACTIVE"])['count'];
$hostel  = get_all('student',['id'],['student_type'=>"HOSTEL",'status'=>"ACTIVE"])['count'];

$cash= intval(get_all('receipt', ['sum(paid_amount) as total '] ,['paid_date'=>$txn_date,'payment_mode'=>'CASH','status'=>'PAID'])['data'][0]['total']);
$bank = intval(get_all('receipt', ['sum(paid_amount) as total '] ,['paid_date'=>$txn_date,'payment_mode'=>'BANK','status'=>'PAID'])['data'][0]['total']);
$cancel = intval(get_all('receipt', ['sum(paid_amount) as total '] ,['paid_date'=>$txn_date,'status'=>'CANCEL'])['data'][0]['total']);

$rcount= intval(get_all('receipt', ['id'] ,['paid_date'=>$txn_date,'payment_mode'=>'CASH','status'=>'PAID'])['count']);
$ccount= intval(get_all('receipt', ['id'] ,['paid_date'=>$txn_date,'payment_mode'=>'CASH','status'=>'CANCEL'])['count']);


$exp_cash = intval(get_all('account_txn', ['sum(txn_amount) as total '] ,['txn_date'=>$txn_date,'txn_mode'=>'CASH','status'=>'ACTIVE'])['data'][0]['total']);
$exp_bank = intval(get_all('account_txn', ['sum(txn_amount) as total '] ,['txn_date'=>$txn_date,'txn_mode'=>'BANK','status'=>'ACTIVE'])['data'][0]['total']);
$exp_cancel = intval(get_all('account_txn', ['sum(txn_amount) as total '] ,['txn_date'=>$txn_date,'status'=>'CANCEL'])['data'][0]['total']);

$exp_count = intval(get_all('account_txn', ['id'] ,['txn_date'=>$txn_date,'status'=>'ACTIVE'])['count']);
$exp_ccount = intval(get_all('account_txn',['id'],['txn_date'=>$txn_date,'status'=>'CANCEL'])['count']);


// $teacher = intval(get_all('user',['id'],['user_type'=>"Teacher",'status'=>'ACTIVE'])['count']);
$emps = get_all('employee','*',['status'=>'ACTIVE']);
$new_admission = intval(get_all('student',['id'],['date_of_admission'=>$txn_date,'status'=>'ACTIVE','branch_id'=>$_SESSION['branch_id']])['count']);
$new_enquiry = intval(get_all('enquiry',['id'],['updated_at'=>$txn_date,'status'=>'ACTIVE'])['count']);
// Closing Report
$closing = get_data('closing_report',$txn_date,null, 'closing_date')['data'];
       
       
// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Set font
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(255, 191, 0); // Backgound color
$pdf->Rect(0, 0, 210, 310, 'F');
$pdf->SetFillColor(255, 255, 255); // White Area 
$pdf->Rect(8, 8, 195, 280, 'F');
// Title
// Set fill color (RGB values)

$pdf->SetTextColor(255, 191, 0); // Red color
// Draw a cell with full page width and 5mm height to fill a line
$branch =  get_data('branch_list', $_SESSION['branch_id'])['data'];

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 6, $branch['full_name'] .", ". $branch['inst_name'], 0, 1, 'C');
$pdf->SetTextColor(30,30,30); // Text color
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 4, $branch['inst_address1'].", ".$branch['inst_address2'], 0, 1, 'C');
$pdf->Cell(0, 4, 'Daily Summary Report | Date : '.  date('d-M-Y', strtotime($txn_date)), '0', '1', 'C', true);

// Line break
$pdf->Ln(6);

// $pdf->SetFont('Arial', 'B', 14);
// $pdf->SetFillColor(144, 238, 144); // Light Green Area 
// $pdf->Cell(100, 8, 'Opening Balance : Rs.' .$closing['opening_balance'], 0, 0, 'C', true);
// $pdf->Cell(0, 8, 'Closing Balance : Rs.' .$closing['closing_balance'], 0, 1, 'C', true);

$pdf->SetFillColor(255, 255, 255); // White Area 
$pdf->SetTextColor(0,0,0); // Text color
// Student Statics 
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Student Statics', 0, 1, 'C', true);

$pdf->Cell(25, 8, 'Total', 1, 0, 'C');
$pdf->Cell(25, 8, 'Active' , 1, 0, 'C');
$pdf->Cell(25, 8, 'Inactive', 1, 0, 'C');
$pdf->Cell(25, 8, 'Blocked', 1, 0, 'C');
$pdf->Cell(25, 8, 'Boys', 1, 0, 'C');
$pdf->Cell(25, 8, 'Girls', 1, 0, 'C');
$pdf->Cell(25, 8, 'Transport', 1, 0, 'C');
$pdf->Cell(0, 8, 'Hostel', 1, 1, 'C');

$pdf->Cell(25, 8, $total, 1, 0, 'C');
$pdf->Cell(25, 8, $active, 1, 0, 'C');
$pdf->Cell(25, 8, $inactive, 1, 0, 'C');
$pdf->Cell(25, 8,  $blocked, 1, 0, 'C');
$pdf->Cell(25, 8, $boys , 1, 0, 'C');
$pdf->Cell(25, 8, $girls, 1, 0, 'C');
$pdf->Cell(25, 8, $transport, 1, 0, 'C');
$pdf->Cell(0, 8, $hostel, 1, 1, 'C');


//$pdf->Image('images/summary.png',30,90,150,0);
// Line break
$pdf->Ln(3);

// Collection Analysis
$pdf->Cell(0, 6, 'Collection Analysis', 0, 1, 'C', true);

$pdf->Cell(35, 8, 'Branch Name', 1, 0, 'C');
$pdf->Cell(30, 8, 'Cashier', 1, 0, 'C');
$pdf->Cell(30, 8, 'Total', 1, 0, 'C');
$pdf->Cell(30, 8, 'Cash', 1, 0, 'C');
$pdf->Cell(30, 8, 'Bank', 1, 0, 'C');
$pdf->Cell(22, 8, 'Cancel', 1, 0, 'C');
$pdf->Cell(13, 8, 'Count', 1, 1, 'C');



$sql="SELECT 
	branch_id,
    created_by, 
    SUM(paid_amount) AS Total,
    SUM(CASE WHEN payment_mode = 'CASH' AND status = 'PAID' THEN paid_amount ELSE 0 END) AS Cash,
    SUM(CASE WHEN payment_mode = 'BANK' AND status = 'PAID' THEN paid_amount ELSE 0 END) AS Bank,
    SUM(CASE WHEN status = 'CANCEL' THEN paid_amount ELSE 0 END) AS Cancel,
    COUNT(*) AS Ct
FROM receipt
where paid_date ='$txn_date'
GROUP BY branch_id,created_by";

$fees  = direct_sql($sql);
// $gtotal =0;
$ctotal = 0;
$btotal = 0;
foreach ((array)$fees['data'] as $fee) {
    $branch_name = get_data('branch_list', $fee['branch_id'], 'inst_name')['data'];
    $cashier = get_data('user', $fee['created_by'], 'user_name')['data'];

    $ctotal += $fee['Cash'];
    $btotal += $fee['Bank'];

    $pdf->Cell(35, 8, $branch_name, 1, 0, 'C');
    $pdf->Cell(30, 8, $cashier, 1, 0, 'C');
    $pdf->Cell(30, 8, $fee['Total'], 1, 0, 'C');
    $pdf->Cell(30, 8, $fee['Cash'], 1, 0, 'C');
    $pdf->Cell(30, 8, $fee['Bank'], 1, 0, 'C');
    $pdf->Cell(22, 8, $fee['Cancel'], 1, 0, 'C');
    $pdf->Cell(13, 8, $fee['Ct'], 1, 1, 'C');
}

$pdf->SetTextColor(250,0,0); // Text color
$pdf->SetFont('Arial', 'B', 10);
$gtotal = $ctotal+$btotal;
$pdf->Cell(0, 10, 'Total Collection of the day : '. $gtotal.' ( '. amount_in_word($gtotal).')', 0, 1, 'C', true);
// Line break
$pdf->Ln(6);

$pdf->SetTextColor(0,0,0); // Text color
// Student Statics 
$pdf->SetFont('Arial', 'B', 10);

// Expense Analysis
// $pdf->Cell(0, 6, 'Expense Analysis', 0, 1, 'C', true);
// $pdf->Cell(35, 8, 'Total (Rs.)', 1, 0, 'C');
// $pdf->Cell(35, 8, 'Cash (Rs.)', 1, 0, 'C');
// $pdf->Cell(35, 8, 'Bank (Rs.)', 1, 0, 'C');
// $pdf->Cell(35, 8, 'Cancel (Rs.)', 1, 0, 'C');
// $pdf->Cell(20, 8, 'Count', 1, 0, 'C');
// $pdf->Cell(0, 8, 'Count(Cancel)', 1, 1, 'C');
// $pdf->Cell(35, 8, ($exp_cash+$exp_bank) , 1, 0, 'C');
// $pdf->Cell(35, 8, $exp_cash, 1, 0, 'C');
// $pdf->Cell(35, 8, $exp_bank, 1, 0, 'C');
// $pdf->Cell(35, 8, $exp_cancel, 1, 0, 'C');
// $pdf->Cell(20, 8, $exp_count, 1, 0, 'C');
// $pdf->Cell(0, 8,  $exp_ccount, 1, 1, 'C');


// Line break
// $pdf->Ln(6);



// Attendance Analysis

$pdf->Cell(0, 6, 'Attendance Analysis (student - A/TS)', 0, 1, 'C', true);
// $pdf->Cell(20, 8, 'Class' , 1, 0, 'C');
// $class_list = explode(",", get_data('branch_list', $_SESSION['branch_id'], 'class_list')['data']);
// foreach($class_list as $cls)
// {
// $pdf->Cell(11, 8, $cls , 1, 0, 'C');
// }
// $pdf->Ln(8);
// $pdf->Cell(20, 8, 'Stength' , 1, 0, 'C');
// foreach(array_filter($class_list) as $cls)
// {
//     $ct = get_all('student',['id'],['student_class'=>$cls,'status'=>'ACTIVE'])['count'];
//     $pdf->Cell(11, 8, $ct , 1, 0, 'C');
// }



$class_list = explode(",", get_data('branch_list', $_SESSION['branch_id'], 'class_list')['data']);
$section_list = explode(",", get_data('branch_list', $_SESSION['branch_id'], 'section_list')['data']);

$pdf->Cell(15, 8, 'Section', 1, 0, 'C');
foreach (array_filter($class_list) as $cls) {
    $pdf->Cell(11.7, 8, $cls, 1, 0, 'C');
}
$pdf->Ln();

foreach (array_filter($section_list) as $section) {
    $pdf->Cell(15, 8, $section, 1, 0, 'C'); // Section name

    foreach (array_filter($class_list) as $class) {
        $ct = studentcount($class, $section) ?? 0;
        $att = today_att_status($class, $section, 'A'); 
        $pdf->Cell(11.7, 8,  $att . '/' . $ct , 1, 0, 'C');
    }

    $pdf->Ln(); 
}




// Line break
$pdf->Ln(7);



// Other Analysis

$pdf->Cell(0, 6, 'Lead & Followup', 0, 1, 'C', true);
$pdf->Cell(35, 8, 'New Enquiry', 1, 0, 'C');
$pdf->Cell(35, 8, 'New Visitor', 1, 0, 'C');
$pdf->Cell(35, 8, 'New Admission', 1, 0, 'C');
$pdf->Cell(35, 8, 'Contact Form', 1, 0, 'C');
$pdf->Cell(0, 8, 'New Notice', 1, 1, 'C');
$pdf->Cell(35, 8, $new_enquiry, 1, 0, 'C');
$pdf->Cell(35, 8, '', 1, 0, 'C');
$pdf->Cell(35, 8, $new_admission, 1, 0, 'C');
$pdf->Cell(35, 8, '', 1, 0, 'C');
$pdf->Cell(0, 8, '', 1, 1, 'C');



// Line break
$pdf->Ln(6);


$sql9 ="SELECT student_class, count(id) as ct FROM `student` where date(created_at) ='$txn_date' and student_class !='' group by student_class";

$nstudents = direct_sql($sql9);
$cellWidth = ($nstudents['count']>0)? (190/count($nstudents['data'])):20;

if($nstudents['count']>0)
{
    $pdf->Cell(0, 12, 'New Admission Analysis', 0, 1, 'C', true);   
    
    foreach((array)$nstudents['data'] as $sclass)
    {
      $pdf->Cell($cellWidth, 8, $sclass['student_class'] , 1, 0, 'C');  
    }
    $pdf->Cell(0, 8, '' , 0, 1, 'C'); 
    
    foreach((array)$nstudents['data']  as $sct)
    {
      $pdf->Cell($cellWidth, 8, $sct['ct'] , 1, 0, 'C');  
    }
    $pdf->Cell(0, 8, '' , 0, 1, 'C'); 

}
// Expense Analysis
// Line break
$pdf->Ln(6);

$pdf->Cell(0, 6, 'Attendance (Employee)', 0, 1, 'C', true);

$pdf->Cell(35, 8, 'Total', 1, 0, 'C');
$pdf->Cell(35, 8, 'Today Present', 1, 0, 'C');
$pdf->Cell(35, 8, 'Late Present', 1, 0, 'C');
$pdf->Cell(35, 8, 'Today Absent', 1, 0, 'C');
$pdf->Cell(0, 8, 'On Leave', 1, 1, 'C');
   if(absent_emp(date('j',strtotime($txn_date)))!=''){
       $t_present = count(present_emp(date('j',strtotime($txn_date)))) ?? 0;
       $t_absent = count(absent_emp(date('j',strtotime($txn_date)))) ?? 0;
       $t_leave = count(leave_emp(date('j',strtotime($txn_date)))) ?? 0;
        $pdf->Cell(35, 8, ($emps['count'] > 0 ? $emps['count'] : ''), 1, 0, 'C');
        $pdf->Cell(35, 8, $t_present, 1, 0, 'C');
        $pdf->Cell(35, 8, '', 1, 0, 'C');
        $pdf->Cell(35, 8, $t_absent, 1, 0, 'C');
        $pdf->Cell(0, 8, $t_leave, 1, 1, 'C');
        $absent_emp = implode(", ", absent_emp(date('j',strtotime($txn_date))));
        $leave_emp = implode(", ", leave_emp(date('j',strtotime($txn_date))));
        $pdf->MultiCell(0, 8, 'Absent Employees : '.$absent_emp, 1, 'L');
        $pdf->MultiCell(0, 8, 'On Leave Employees : '.$leave_emp, 1, 'L');
   }
        
// Line break
$pdf->Ln(60);
$pdf->SetFont('Arial', '', 7);

$pdf->SetXY(20, 280);
$pdf->Cell(0, 8, "Disclaimer :in case of 0(Zero) or Blank Data it's means Data Not Updated. ", 0, 1, 'C');
$pdf->Cell(0, 8, 'This Report is Generate from BINE 5.0 : Digital Backbone of School | Planted by OfferPlant', 0, 1, 'C');
// Output the PDF
$pdf->Output();
?>
