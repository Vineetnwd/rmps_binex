<?php
require_once __DIR__ . '/mpdf/vendor/autoload.php';
require_once('system/function.php');
$mpdf = new \Mpdf\Mpdf();
$rid = $_REQUEST['receipt_id'];

if(get_data('receipt',$rid)['count'] >0)
{
$receipt = get_data('receipt',$rid)['data'];
$student = get_data('student',$receipt['student_id'])['data'];
if($_SESSION['user_id'] <>'' or $student['student_mobile'] ==$_GET['mobile']){
?>
$mpdf->WriteHTML("
<title> Receipt No.  <?php echo $rid; ?> </title>

<script type="text/javascript" src="js/towords.js"></script>
<style>
body{
	font-family:calibri,arial, time new roman;
	font-size:10px;
	padding:0px;
	margin:0px;
}
.cancel, .CANCEL{
    text-decoration: line-through;
    color:red;
}
td{
   font-weight:300; 
	font-size:14px;
	padding:5px 8px;
}
td .head{
	font-size:11px;
}
.btn{
	border:solid 1px #ddd;
	padding:4px;
	margin:10px;
	background:#f5f5f5;
	text-decoration:none;
	color:#222;
	text-transform:uppercase;
	font-weight:800;
}
.no-print{
    padding:15px 50px;
    width:450px;
}
.name{
	display:inline;
	font-size:16px;
	line-height:24px;
	font-weight:800;
}
@media print {
         .btn{display:none;}
      }	
@media print{@page {size: landscape}}
</style>");

<?php if( $_SESSION['user_id'] <>'' ) {?>
	<div class='no-print'>
			<a href='collect_fee' class='btn' accesskey ='n' > New Payment (Use Alt+N) </a>
			<a href='' class='btn' onClick='window.print()'>  PRINT (Use Ctrl +P) </a>
	</div>
	<table  border='1'  rules ='all' cellpadding='5px' width='450px' align='right'>
<?php } else{ ?> 
<br> <br>
<table  border='1'  rules ='all' cellpadding='5px' width='70%' align='center'>
<?php } ?>  
 
	<thead>
		<tr>
			<td colspan='5' align='center' class='head'> 
				<img align='left' src='images/logo.png' height='80px'>
				<span style='font-size:18px;font-weight:600;'> <?php echo $full_name; ?> </span><br>
				(Affiliated to CBSE, New Delhi upto 10+2) <br>
				<b>Affiliation No. : <?php echo $aff_no;?>  School No. :  <?php echo $school_code;?></b><br>
				<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
				Contact No.: <?php echo $inst_contact; ?> <br> Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>
			</td>
		</tr>
		<tr bgcolor='#f5f5f5'>
			<td colspan='3'> Receipt No. : <b class='<?php echo $receipt['status']; ?>'><?php echo $rid . "-".   $receipt['status']; ?> </b></td>
			<td align='right' colspan='2'><?php echo date('d-M-Y h:i A', strtotime($receipt['created_at'])); ?></td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:center'>
			<b> Parents Copy : Fee Details of <?php echo add_space(str_replace(',',', ',$receipt['paid_month']));?></b>
			</td>
		</tr>
	</thead>
	<tbody >
		<tr>
			<td colspan='2'> Name </td> <td colspan='3'><?php echo strtoupper($student['student_name']); ?></td>
		</tr>
		<tr>
			<td colspan='2'> Student ID </td><td colspan='3'><?php echo strtoupper($student['student_admission']); ?></td>
			
		</tr>
		
		<tr>
			<td colspan='2'> Father's Name </td> <td colspan='3'><?php echo strtoupper($student['student_father']); ?></td>
		</tr>
		<tr>
			<td> Class </td> <td colspan='3'><?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?> (<?php echo $student['student_type']; ?>)</td>
			<td>Roll No. : <?php echo $student['student_roll']; ?></td>
		</tr>
		<tr>
			<td colspan='5' height='140px' valign='top'>
			<table width='100%' border='0' rules='none'>
			<?php if(get_data('receipt',$rid,'previous_dues')['data']!=0){ ?>
				<tr>
					<td> Previous Dues </td> <td align='right'><?php echo get_data('receipt',$rid,'previous_dues')['data']; ?></td>
				</tr>
			<?php }?>	
				<?php 
				// ----------USER DEFINED FEE ---------//
				// $sql1 ="select * from fee_head where status ='ACTIVE'";
				// $res1 =mysqli_query($con,$sql1);
				//while($row1 = mysqli_fetch_array($res1)){
				$res = get_all('fee_head','*',array('status'=>'ACTIVE'));
				//print_r($res);
				if($res['status']=='success')
				foreach($res['data'] as $row1)
				{
				$col_name =remove_space($row1['fee_name']);
				$amount =get_data('receipt',$rid,$col_name)['data'];
				
				if ($amount >0) {?>
				<tr>
					
					<td> <?php echo $row1['fee_name']; ?> </td>
					<td align='right' ><?php echo $amount; ?></td>
				</tr>
				<?php } }		?>
			<?php if($receipt['other_fee']!=0){ ?>
				<tr>
					<td> Miscellaneous Fee </td> <td align='right'><?php echo $receipt['other_fee']; ?></td>
				</tr>
			<?php }?>	
			</table>
			</td>
		</tr>
		<?php if($receipt['remarks'] !='') { ?>
		<tr><td colspan='5' align='center'>Remarks: <?php echo $receipt['remarks']; ?>
		<?php }?>
		</td></tr>
		<?php if($receipt['discount'] <>0) { ?>
		<tr>
			<td colspan='4' align='right'> Discount (-) </td> <td align='right'><?php echo $receipt['discount']; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan='4' align='right'> Total </td> <td align='right'><?php echo $receipt['total']; ?></td>
		</tr>
		
		<tr bgcolor='#d6d6d6'>
			<td colspan='4' align='right' ><b> Paid Amount (In <?php echo $paid =$receipt['payment_mode']; ?>) </b> </td> <td align='right'><b><big><?php echo $paid = $receipt['paid_amount']; ?></big></b></td>
		</tr>
		
		<tr>
			<td colspan='5' style='text-transform:capitalize;' >
			<script> var words =toWords(<?php echo $paid;?>); document.write("<div class='t'><b>In Words : </b>"+words+" rupees only </div>"); </script>
			</td>
		</tr>
		<tr style='color:red'>
			<td colspan='4'> Current Dues </td> <td align='right'><?php echo $receipt['current_dues']; ?></td>
		</tr>
		
		<tr>
		<td colspan='5'>
		
			<small>
			<b>Instructions :-</b><br>
			Please pay before 10th of every month. </br>
			Always update your mobile no. to receive notification. <br> 
			This receipt is auto generated by computer only for information purpose </br>
			
		</td>
	</tr>
	<tr>
		<td colspan='2'> Issued by : <?php echo get_data('user',get_data('receipt',$rid,'created_by')['data'],'user_name')['data']; ?>
		</td >
		<td colspan='3' align='right'>
		Authorised Signatory 
		</td>
	</tr>
	
	
	</table>
    <br>

<?php } } ?>

$mpdf->WriteHTML("<!DOCTYPE html>
<html>
<head>
    <title>Complex HTML Page with Chart</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }

        nav {
            background-color: #444;
            color: white;
            padding: 10px;
        }

        section {
            padding: 10px;
        }

        article {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
</head>
<body>
    <header>
        <h1>Welcome to Our Website</h1>
    </header>
    <nav>
        <ul>
            <li><a href='#'>Home</a></li>
            <li><a href='#'>About Us</a></li>
            <li><a href='#'>Services</a></li>
            <li><a href='#'>Contact Us</a></li>
        </ul>
    </nav>
    <section>
        <article>
            <h2>Article 1</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod eros ut odio feugiat, vitae dictum nisi tristique.</p>
        </article>
        <article>
            <h2>Article 2</h2>
            <p>Donec tincidunt quam ut mi feugiat, non condimentum dolor ultricies. Nullam vehicula justo ac dolor luctus, ac congue neque scelerisque.</p>
        </article>
        <canvas id='myChart' width='400' height='200'></canvas>
    </section>
    <footer>
        <p>&copy; 2024 Our Website. All rights reserved.</p>
    </footer>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Sales',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>");
$mpdf->Output();
