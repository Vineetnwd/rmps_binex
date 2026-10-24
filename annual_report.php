<?php require_once("function.php");	?>
<title> <?php echo $inst_name; ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
body{
	color:#000;
	padding:0px;
	margin:0px;
	font-family: 'Roboto', sans-serif;
}
.inner_box{
    margin:10px auto;
}

</style>
<center><h2> <?= $inst_name?> : Annual Student Summary Report</h2></center>
<?php
extract($_POST);

$res = get_all('student', '*',['status'=>'ACTIVE'],' student_class, student_section, student_roll');
$i=1;
$tc=0;
$td=0;
if($res['count']>0)
{
	foreach($res['data'] as $student)
	{
	$student_id = $student['id'];
	$student_admission = $student['student_admission'];
	$finaldues = finaldues($student_id);
	$td +=$finaldues;
	?>
            <div class='inner_box'>
                
        	    <table cellpadding='2px' cellspacing='0' class='content' rules='all' border='1' width='700px' bgcolor='#f6f6f6' align='center'>
	               
	               
	                 <tr>
	                    <td> <h4> Sr. No. : <?= $i ?></h4>  </td>
	                    <td bgcolor='lightgreen' > Adm No. : <b><?php echo $student['student_admission']; ?></b></b> </td>
	                
	                  
	                    <td bgcolor='lightpink' colspan='2'> Name : <b> <?php echo $student['student_name']; ?></b>  </td>
	                </tr>
	               
	                <tr>
	                    <td> Mobile No. </td>
	                    <td> <?php echo $student['student_mobile']; ?>  </td>
	                
	                    <td> Father's Name  </td>
	                    <td> <?php echo $student['student_father']; ?>  </td>
	                </tr>
	                 <tr>
	                    <td> Class  </td>
	                    <td> <?php echo $student['student_class']."-".$student['student_section']; ?>  </td>
	                
	                    <td> Roll  </td>
	                    <td> <?php echo $student['student_roll']; ?>  </td>
	                </tr>
	               
	            </table>
	            <table cellpadding='2px' cellspacing='0' class='content' rules='all' border='1' width='700px' align='center'>
	              
	                <tr bgcolor='lightblue'>
	                    <td> Receipt No.</td>
	                    <td> Date Time </td>
	                    <td> Total </td>
	                    <td> Paid </td>
	                    <td> Mode </td>
	                    <td> Received By </td>
	                </tr>
	            
	            <?php 
	            
	            $res2 = get_all('receipt', '*',['student_id'=>$student_id,'status'=>'PAID']);
	            $stotal= $spaid =0;
	            foreach((array)$res2['data'] as $rcpt)
	            {
	                $stotal +=$rcpt['total'];
	                $spaid +=$rcpt['paid_amount'];
	                
	                // Create a DateTime object with the UTC timezone
                    $utc_datetime = new DateTime($rcpt['updated_at'], new DateTimeZone('UTC'));
                    
                    // Convert UTC to IST
                    $utc_datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                    
                    // Format the IST time
                    $ist_time = $utc_datetime->format('d M Y h:i A');

	               echo "<tr>";
	               
	               echo "<td>". $rcpt['id']."</td>";
	               echo "<td>". $ist_time ."</td>";
	               echo "<td>". $rcpt['total']."</td>";
	               echo "<td>". $rcpt['paid_amount']."</td>";
	               echo "<td>". $rcpt['payment_mode']."</td>";
	               echo "<td>". get_data('user',$rcpt['created_by'],'user_name')['data']."</td>";
	               echo "</tr>";
	            }
	            $tc +=$spaid;
	             if( $finaldues >0)
	               {
	                   $dues= $stotal-$spaid;
	                echo "<tr bgcolor='lightpink'><td colspan='2'> Total </td> <td>$stotal </td> <td> $spaid </td><td colspan='2' style='background:red;color:#fff'> Dues:  $finaldues </td>  </tr>";
	               }
	               else{
	             echo "<tr bgcolor='yellow'><td colspan='2'> Total </td> <td>$stotal </td> <td>$spaid </td><td colspan='2'></td></tr>";
	               }
	            ?>
	            </table>
	            </center>
			</div>
<?php $i++; } } ?>

<table cellpadding='2px' cellspacing='0' class='content' rules='all' border='1' width='700px' align='center'>
	              
	                <tr >
	                    <td> Total Collection.</td>
	                    <td bgcolor='lightgreen'> <h3><?= $tc ." </h3><br> (". amount_in_word($tc)?>) </td>
	                    <td> Total Dues </td>
	                    <td bgcolor='lightpink'> <h3><?= $td. "</h3><br> (". amount_in_word($td) ?>)  </td>
	                   
	                </tr>
</table>
			