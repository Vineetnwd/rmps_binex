<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
extract($_POST);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Exam Seat Planning</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#fee">Exam</a></li>
      <li class="breadcrumb-item active">Seat Planning </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Basic Forms -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Your Seat is ready </h3>'
        <div class="box-tools pull-right">
         <button onclick='exportxls()' class='btn btn-primary'>Export</button>
        </div>
      </div>
      <!-- /.box-header -->

<div class="box-body">
<?php

function get_student($student_class)
{
    // $sql = "select app_no, name, student_class, student_roll from scholorship where student_class ='$student_class' and student_roll <>0 and payment_status ='PAID' order by student_roll";
    // $res = direct_sql($sql);
    $res2 = fetch_all('scholorship','*',['student_class'=>$student_class,'payment_status'=>'PAID'],[],'student_roll');
    return $res2;
}


$vi_all = get_student($class_1);
$vii_all = get_student($class_2);
$viii_all = get_student($class_3);
$ct = max(array($vi_all['count'],$vii_all['count'],$viii_all['count']));

$k=1;
?>
<style>
    table,td, th {
        text-align:center;
        padding:10px;
    }
    h3{
        display:inline;
    }
@media print {
  .footer {page-break-after: always;}
}
</style>

<div class="table-responsive">
    <table id="data_tbl" class="table table-bordered table-striped">
<!--<table rules='all' width='600px' align='center' cellpadding='5px' id='data_tbl'>-->
    
<thead>
    <tr bgcolor='#d6d6d6'><td>Bench No.</td><td> Col 1</td><td> Col2 </td><td> Col 3</td></tr>
    <tr><td colspan='4' bgcolor='#d5d5d5'><center> <h3>ROOM NO. <?php echo $k=1;?></h3> </td>
    </tr>
</thead>
<tbody>
<?php
$j=1;
for ($i=0; $i<$ct; $i++)
{
    
    // $sc_code = 
   
    echo "<tr>";
    
    echo "<td>". $j ."</td>";
    echo "<td>";
    if($vi_all['data'][$i]['name']<>'')
    {
        // print_r($vi_all);
    echo $vi_all['data'][$i]['name'];
    echo "<br>". $vi_all['data'][$i]['student_class'];
    echo "-" .$vi_all['data'][$i]['student_section'];
    echo $vi_all['data'][$i]['student_roll'];
    $sc_code = get_data('branch_list',$vi_all['data'][$i]['branch_id'],'school_code')['data'];
        if($sc_code !='BSP'){
            echo "-" . $sc_code;
        }
    }
    echo "</td>";
    
    echo "<td>";
    if($vii_all['data'][$i]['name']<>'')
    {
    echo $vii_all['data'][$i]['name'];
    echo "<br>". $vii_all['data'][$i]['student_class'];
    echo "-" .$vii_all['data'][$i]['student_section'];
    echo $vii_all['data'][$i]['student_roll'];
    $sc_code = get_data('branch_list',$vi_all['data'][$i]['branch_id'],'school_code')['data'];
        if($sc_code !='BSP'){
            echo "-" . $sc_code;
        }
    }
    echo "</td>";
    
    echo "<td>";
    if($viii_all['data'][$i]['name']<>'')
    {
    echo $viii_all['data'][$i]['name'];
    echo "<br>". $viii_all['data'][$i]['student_class'];
    echo "-" .$viii_all['data'][$i]['student_section'];
    echo $viii_all['data'][$i]['student_roll'];
    $sc_code = get_data('branch_list',$vi_all['data'][$i]['branch_id'],'school_code')['data'];
        if($sc_code !='BSP'){
            echo "-" . $sc_code;
        }
    }
    echo "</td>";
    
    echo "</tr>";
    $j++;
    if(($i+1)%$total_bench==0 and $i<>0)
    {
        $k++;
        $j=1;
        echo "<tr class='footer'><td colspan='4' bgcolor='#d9d5d5'><center> <h3> ROOM NO. ". $k . "</h3> </td></tr>";
        
    }
   
}
?>
</tbody>
</table>
	</div>
        </div>
    </div>
  </section>
</div>
<?php require_once('system/footer.php'); ?>