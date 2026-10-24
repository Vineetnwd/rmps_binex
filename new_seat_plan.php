<?php 
require_once("system/header.php");
require_once("system/menu.php");

/*
|--------------------------------------------------------------------------
| SETTINGS
|--------------------------------------------------------------------------
*/

$total_rooms = 25;

/*
|--------------------------------------------------------------------------
| GET STUDENTS
|--------------------------------------------------------------------------
*/

function get_student($student_class)
{
    $sql = "SELECT
                student_admission,
                student_name,
                student_class,
                student_section,
                student_roll
            FROM student
            WHERE student_class = '$student_class'
            AND student_roll <> 0
            AND status = 'ACTIVE'
            AND branch_id = '{$_SESSION['branch_id']}'
            AND NOT (
                student_class = 'X'
                AND student_section = 'D'
            )
            ORDER BY student_section, student_roll
            ";

    return direct_sql($sql);
}

$class_order = array(
    'II',
    'IV',
    'III',
    'V',
    'VII',
    'VI',
    'IX',
    'VIII',
    'X'
);



$class_students = array();
$class_indexes  = array();

foreach($class_order as $cls)
{
    $res = get_student($cls);

    $class_students[$cls] = isset($res['data'])
        ? $res['data']
        : array();

    $class_indexes[$cls] = 0;
}

/*
|--------------------------------------------------------------------------
| GROUP POINTERS
|--------------------------------------------------------------------------
*/

$current_group = array(
    1 => 0,
    2 => 1,
    3 => 2
);

/*
|--------------------------------------------------------------------------
| GET NEXT STUDENT
|--------------------------------------------------------------------------
*/

function get_next_student(
    $group_no,
    &$current_group,
    $class_order,
    &$class_students,
    &$class_indexes
)
{
    $used_indexes = array();

    for($i = 1; $i < $group_no; $i++)
    {
        $used_indexes[] = $current_group[$i];
    }

    while(isset($class_order[$current_group[$group_no]]))
    {
        if(in_array($current_group[$group_no], $used_indexes))
        {
            $current_group[$group_no]++;
            continue;
        }

        $cls = $class_order[$current_group[$group_no]];

        if(isset($class_students[$cls][$class_indexes[$cls]]))
        {
            $student = $class_students[$cls][$class_indexes[$cls]];

            $class_indexes[$cls]++;

            return $student;
        }

        $current_group[$group_no]++;
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| RESET FOR PDF
|--------------------------------------------------------------------------
*/

function reset_group_data($class_order)
{
    $indexes = array();

    foreach($class_order as $cls)
    {
        $indexes[$cls] = 0;
    }

    return array(
        'group' => array(
            1 => 0,
            2 => 1,
            3 => 2
        ),
        'indexes' => $indexes
    );
}

?>

<div class="content-wrapper">

<section class="content-header">

    <h1>
        Dynamic Exam Seating Plan
    </h1>

    <div class="pull-right">

        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i>
            Print
        </button>

        <button onclick="downloadAttendancePDF()" class="btn btn-danger">
            <i class="fa fa-file-pdf-o"></i>
            Attendance PDF
        </button>

    </div>

</section>

<section class="content">

<style>

@page{
    size:A4 landscape;
    margin:0mm;
}

body{
    background:#fff;
    font-family:Arial, sans-serif;
    margin:0;
    padding:0;
}

.page{
    width:282mm;
    height:195mm;
    overflow:hidden;
    background:#fff;
    margin:0 auto;
    border:1px solid #000;
    padding:2px;
    box-sizing:border-box;
    page-break-after:always;
}

.room-header{
    text-align:center;
    margin-bottom:4px;
    position:relative;
    border: 1px solid #000;
}

.room-title{
    font-size:40px;
    font-weight:900;
    letter-spacing:1px;
    line-height:40px;
}

.date-line{
    position:absolute;
    right:5px;
    top:5px;
    width:120px;
    border-bottom:1px solid #000;
    text-align: left;
}

.seating-wrapper{
    display:flex;
    gap:6px;
    margin-bottom:5px;
}

.seating-section{
    flex:1;
}

.seating-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.seating-table th,
.seating-table td{
    border:1px solid #000;
    text-align:center;
    vertical-align:middle;
}

.seating-table th{
    padding:2px;
    font-size:10px;
    background:#f5f5f5;
    font-weight:bold;
}

.row-no{
    width:25px;
    font-size:10px;
    font-weight:bold;
}

.student-box{
    height:60px;
    font-size:11px;
    line-height:11px;
    padding:1px;
    font-weight:bold;
    overflow:hidden;
    word-wrap:break-word;
}

.bottom-label td{
    padding:2px;
    font-size:9px;
    font-weight:bold;
}

.bottom-section{
    border:1px solid #000;
    padding:5px;
    display:flex;
    justify-content:space-between;
    gap:10px;
}

.summary-box{
    width:35%;
}

.summary-row{
    display:flex;
    align-items:center;
    margin-top:40px;
}

.summary-row span{
    width:100px;
    font-size:13px;
    font-weight:bold;
}

.summary-line{
    flex:1;
    border-bottom:1px solid #000;
    /*height:14px;*/
    text-align: center;
    font-size:15px;
    font-weight: 900;
}

.report-box{
    width:60%;
}

.report-title{
    font-size:10px;
    margin-bottom:2px;
    text-align:right;
    font-weight:bold;
}

.report-table{
    width:100%;
    border-collapse:collapse;
}

.report-table td{
    border:1px solid #000;
    padding:10px;
    text-align:center;
    font-size:15px;
    font-weight:bold;
}

.signatures{
    margin-top:50px;
    display:flex;
    justify-content:space-between;
    padding:0 10px;
}

.signature-box{
    width:180px;
    border-top:1px solid #000;
    text-align:center;
    padding-top:2px;
    font-size:10px;
    font-weight:bold;
}

@media print{

    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header{
        display:none !important;
    }

    .content-wrapper{
        margin-left:0 !important;
    }

    body{
        background:#fff !important;
    }

    .page{
        margin-top:6mm !important;
    }

    .page:first-of-type{
        margin-top:0 !important;
    }

}

</style>

<?php

$layout = array(
    'Entry Side'  => range(1,5),
    'Middle'      => range(6,11),
    'Window Side' => range(12,17)
);


/*
|--------------------------------------------------------------------------
| ROOM SUMMARY DATA
|--------------------------------------------------------------------------
*/

$room_summary = array();

$summary_reset = reset_group_data($class_order);

$current_group_summary = $summary_reset['group'];
$class_indexes_summary = $summary_reset['indexes'];

for($room_no = 1; $room_no <= $total_rooms; $room_no++)
{
    $room_students = array();

    for($r = 1; $r <= 17; $r++)
    {
        for($g = 1; $g <= 3; $g++)
        {
            $student = get_next_student(
                $g,
                $current_group_summary,
                $class_order,
                $class_students,
                $class_indexes_summary
            );

            if($student)
            {
                $room_students[] = $student;
            }
        }
    }

    $class_list = array();
    $class_count = array();

    foreach($room_students as $stu)
    {
        $class_name = $stu['student_class'] . "-" . $stu['student_section'];

        if(!in_array($class_name, $class_list))
        {
            $class_list[] = $class_name;
        }

        if(!isset($class_count[$class_name]))
        {
            $class_count[$class_name] = 0;
        }

        $class_count[$class_name]++;
    }

    $room_summary[$room_no] = array(
        'total_students' => count($room_students),
        'classes'        => $class_list,
        'class_count'    => $class_count
    );
}

for($room = 1; $room <= $total_rooms; $room++)
{
?>

<div class="page">

    <div class="room-header">

        <div class="room-title">
            ROOM NUMBER - <?php echo str_pad($room,2,'0',STR_PAD_LEFT); ?>
        </div>

        <div class="date-line">Date : </div>

    </div>

    <div class="seating-wrapper">

<?php

foreach($layout as $title => $rows)
{
?>

<div class="seating-section">

<table class="seating-table">

<tr>

    <th class="row-no">
        Row
    </th>

    <th colspan="3">
        <?php echo $title; ?>
    </th>

</tr>

<?php

foreach($rows as $bench)
{
?>

<tr>

    <td class="row-no">
        <?php echo $bench; ?>
    </td>

<?php

for($g = 1; $g <= 3; $g++)
{
    $student = get_next_student(
        $g,
        $current_group,
        $class_order,
        $class_students,
        $class_indexes
    );

    echo "<td class='student-box'>";

    if($student)
    {
        echo $student['student_name'];
        echo "<br>";

        echo $student['student_class']
            ."-"
            .$student['student_section'];

        echo " [".$student['student_roll']."]";
    }

    echo "</td>";
}

?>

</tr>

<?php
}
?>

<tr class="bottom-label">

    <td></td>

    <td>LEFT</td>
    <td>MIDDLE</td>
    <td>RIGHT</td>

</tr>

</table>

</div>

<?php
}
?>

    </div>

    <div class="bottom-section">

        <div class="summary-box">

            <div class="summary-row">
                <span>Total Student</span>
                <div class="summary-line">
                    <?php echo $room_summary[$room]['total_students']; ?>
                </div>
            </div>

            <div class="summary-row">
                <span>Total Present</span>
                <div class="summary-line"></div>
            </div>

            <div class="summary-row">
                <span>Total Absent</span>
                <div class="summary-line"></div>
            </div>

        </div>

        <div class="report-box">

            <div class="report-title">
                Class Wise Report
            </div>

            <table class="report-table">

                <tr>
                    <td>CLASSES</td>
            
                    <?php
                    foreach($room_summary[$room]['classes'] as $cls)
                    {
                        echo "<td>".$cls."</td>";
                    }
                    ?>
                </tr>
            
                <tr>
                    <td>PRESENT</td>
            
                    <?php
                    foreach($room_summary[$room]['classes'] as $cls)
                    {
                        echo "<td></td>";
                    }
                    ?>
                </tr>
            
                <tr>
                    <td>ABSENT</td>
            
                    <?php
                    foreach($room_summary[$room]['classes'] as $cls)
                    {
                        echo "<td></td>";
                    }
                    ?>
                </tr>
            
                <tr>
                    <td>TOTAL</td>
            
                    <?php
                    foreach($room_summary[$room]['classes'] as $cls)
                    {
                        echo "<td>".$room_summary[$room]['class_count'][$cls]."</td>";
                    }
                    ?>
                </tr>
            
            </table>

        </div>

    </div>

    <div class="signatures">

        <div class="signature-box">
            Invigilator's Signature
        </div>

        <div class="signature-box">
            Examination Incharge
        </div>

    </div>

</div>

<?php
}
?>

</section>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>

function downloadAttendancePDF()
{
    var printWindow = window.open('', '', 'width=1200,height=900');

    var html = `

    <html>

    <head>

        <title>
            Roomwise Attendance
        </title>

        <style>

        @page{
            size:A4 portrait;
            margin:8mm;
        }

        body{
            margin:0;
            padding:0;
            font-family:Arial;
            background:#fff;
        }

        .pdf-page{
            width:100%;
            min-height:100vh;
            page-break-after:always;
            page-break-inside:avoid;
            box-sizing:border-box;
            overflow:hidden;
        }

        .school{
            text-align:center;
            margin-bottom:10px;
        }

        .school-name{
            font-size:26px;
            font-weight:900;
        }

        .school-sub{
            font-size:11px;
            margin-top:2px;
        }

        .title{
            display:inline-block;
            margin-top:8px;
            border:2px solid #000;
            padding:5px 18px;
            font-size:18px;
            font-weight:bold;
        }

        .top{
            display:flex;
            justify-content:space-between;
            margin-top:12px;
            margin-bottom:10px;
            font-size:12px;
            font-weight:bold;
        }

        .invigilator{
            margin-bottom:10px;
            font-size:12px;
            font-weight:bold;
        }

        table{
            width:100%;
            border-collapse:collapse;
            page-break-inside:auto;
        }
        
        tr{
            page-break-inside:avoid !important;
            page-break-after:auto;
        }
        
        td{
            page-break-inside:avoid !important;
        }
        
        th{
            page-break-inside:avoid !important;
        }

        th, td{
            border:1px solid #000;
            padding:6px 4px;
            font-size:10px;
            line-height:13px;
            height:36px;
            vertical-align:middle;
        }

        th{
            background:#efefef;
            text-align:center;
            font-weight:bold;
        }

        .row-number{
            text-align:center;
            font-size:16px;
            font-weight:bold;
            min-height:78px;
        }

        .signatures{
            margin-top:25px;
            display:flex;
            justify-content:space-between;
        }

        .sign{
            width:220px;
            border-top:1px solid #000;
            text-align:center;
            padding-top:5px;
            font-size:11px;
            font-weight:bold;
        }

        </style>

    </head>

    <body>

    `;

<?php

$pdf_reset = reset_group_data($class_order);

$current_group_pdf = $pdf_reset['group'];
$class_indexes_pdf = $pdf_reset['indexes'];

for($room = 1; $room <= $total_rooms; $room++)
{
?>

    html += `

    <div class="pdf-page">

        <div class="school">

            <div class="school-name">
                R M PUBLIC SCHOOL
            </div>

            <div class="school-sub">
                AFFILIATED TO CBSE, NEW DELHI
            </div>

            <div class="title">
                ROOMWISE ATTENDANCE
            </div>

        </div>

        <div class="top">

            <div>
                ROOM NO :
                <?php echo str_pad($room,2,'0',STR_PAD_LEFT); ?>
            </div>
            
            <div>
    
                INVIGILATOR :
                _______________________________
    
            </div>

            <div>
                DATE : ____________
            </div>

        </div>


        <table>

            <tr>

                <th width="6%">
                    ROW
                </th>

                <th width="8%">
                    ROLL
                </th>

                <th width="12%">
                    ADM NO
                </th>

                <th width="10%">
                    CLASS
                </th>

                <th width="39%">
                    STUDENT NAME
                </th>

                <th width="25%">
                    SIGNATURE
                </th>

            </tr>

<?php

for($row = 1; $row <= 17; $row++)
{
    for($g = 1; $g <= 3; $g++)
    {
        $student = get_next_student(
            $g,
            $current_group_pdf,
            $class_order,
            $class_students,
            $class_indexes_pdf
        );

?>

            <tr>

<?php
if($g == 1)
{
?>

                <td rowspan="3" class="row-number">
                    <?php echo $row; ?>
                </td>

<?php
}
?>

                <td>
                    <?php echo ($student) ? $student['student_roll'] : ''; ?>
                </td>

                <td>
                    <?php echo ($student) ? $student['student_admission'] : ''; ?>
                </td>

                <td>
                    <?php
                
                    if($student)
                    {
                        echo $student['student_class']
                            ." - ".
                            $student['student_section'];
                    }
                
                    ?>
                </td>

                <td>
                    <?php echo ($student) ? $student['student_name'] : ''; ?>
                </td>

                <td></td>

            </tr>

<?php
    }
}
?>

        </table>

        <div class="signatures">

            <div class="sign">
                INVIGILATOR'S SIGNATURE
            </div>

            <div class="sign">
                EXAMINATION INCHARGE
            </div>

        </div>

    </div>

    `;

<?php
}
?>

    html += `

    </body>

    </html>

    `;

    printWindow.document.open();

    printWindow.document.write(html);

    printWindow.document.close();

    printWindow.focus();

    setTimeout(function(){

        printWindow.print();

    }, 1000);
}

</script>
<?php require_once("system/footer.php"); ?>