<?php
require_once('system/function.php');
$data = decode($_GET['link']);

$student_id = $data['id'];
$res = get_data('student', $student_id);

if ($res['count'] > 0) {
	extract($res['data']);
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Student Details</title>
		<style>
			@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

			body {
				font-family: 'Roboto', sans-serif;
				background-color: #f4f4f9;
				margin: 0;
				padding: 0;
			}

			.container {
				width: 90%;
				max-width: 960px;
				margin: 20px auto;
				background: #fff;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
				border-radius: 8px;
				overflow: hidden;
			}

			.header {
				background-color: #fd7e14;
				color: #fff;
				text-align: center;
				padding: 20px;
			}

			.header img {
				height: 100px;
			}

			.header h1 {
				font-size: 28px;
				margin: 10px 0;
			}

			.header p {
				margin: 5px 0;
				font-size: 14px;
			}

			table {
				width: 100%;
				border-collapse: collapse;
				margin: 20px 0;
			}

			th,
			td {
				padding: 12px 10px;
				text-align: left;
				font-size: 14px;
				border: 1px solid #ddd;
			}

			th {
				background-color: #f9f9f9;
				font-weight: 500;
			}

			tr:nth-child(even) {
				background-color: #f9f9f9;
			}

			tr:hover {
				background-color: #f1f1f1;
			}

			.image-cell {
				text-align: center;
			}

			.image-cell img {
				height: 150px;
				border-radius: 8px;
				border: 2px solid #ddd;
			}

			.print-btn {
				display: block;
				width: 150px;
				margin: 20px auto;
				padding: 10px 15px;
				background-color: #28a745;
				color: #fff;
				border: none;
				border-radius: 5px;
				font-size: 16px;
				cursor: pointer;
				text-align: center;
			}

			.print-btn:hover {
				background-color: #218838;
			}
			
			   .declaration {
            margin: 20px auto;
            width: 950px;
            font-size: 14px;
            line-height: 1.8;
        }
        .declaration p{
         padding:10px;   
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding: 0 20px;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }
		</style>
	</head>

	<body>
		<div class="container">
			<div class="header">
			    <div class="image-cell">
				<img src="assets/images/logo.png" alt="School Logo" align='left'>
				</div>
				<div class="image-cell">
					<img src="upload/<?php echo $student_photo; ?>" alt="Student Photo" align='right'>
				</div>
				<h1><?php echo $full_name; ?></h1>
				<p><strong>(Affiliated to CBSE, New Delhi upto 10+2)</strong></p>
				<p>Affiliation No.: <?php echo $aff_no; ?> | School Code: <?php echo $school_code; ?></p>
				<p><?php echo $inst_address1 . ', ' . $inst_address2; ?></p>
				<p>Contact No.: <?php echo $inst_contact; ?> | Email: <?php echo $inst_email; ?></p>
				<p>Website: <?php echo $inst_url; ?></p>
			</div>

			<table>
			    <tr>
					<th colspan="4"><center><h2>Registration Cum Admission Form</h2></center></th>
				</tr>
				<tr>
					<th colspan="4">Student Information</th>
				</tr>
				<tr>
					<td>Student Name</td>
					<td colspan="3"><b><?php echo strtoupper($student_name); ?></b></td>
				</tr>
				<tr>
					<td>Class</td>
					<td><?php echo $student_class; ?></td>
					<td>Section</td>
					<td><?php echo $student_section; ?></td>
				</tr>
				<tr>
					<td>Roll No.</td>
					<td><?php echo $student_roll; ?></td>
					<td>Admission No.</td>
					<td><?php echo $student_admission; ?></td>
				</tr>
				<tr>
					<td>Date of Birth</td>
					<td><?php echo date('d-M-Y', strtotime($date_of_birth)); ?></td>
					<td>Gender</td>
					<td><?php echo $student_sex; ?></td>
				</tr>
			
				<tr>
					<th colspan="4">Family Information</th>
				</tr>
				<tr>
					<td>Father's Name</td>
					<td><?php echo $student_father; ?></td>
					<td>Mother's Name</td>
					<td><?php echo $student_mother; ?></td>
				</tr>
				<tr>
					<td>Father's Mobile</td>
					<td><?php echo $father_mobile; ?></td>
					<td>Mother's Mobile</td>
					<td><?php echo $mother_mobile; ?></td>
				</tr>
				<tr>
					<th colspan="4">Contact Details</th>
				</tr>
				<tr>
					<td>Mobile No.</td>
					<td colspan="3"><?php echo $student_mobile; ?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td colspan="3"><?php echo $student_email; ?></td>
				</tr>
			</table>
              <div class="declaration">
        <h3>Declaration</h3>
        <p>
            I, <b><?php echo $student_name; ?></b>, hereby declare that the information provided above is true and correct to the best of my knowledge and belief. I also understand that any discrepancy found in the provided details may lead to cancellation of my admission.
        </p>
        <p>
            I agree to abide by the rules and regulations of the institution and understand that any violation of these rules may result in disciplinary actions as deemed necessary by the school authorities.
        </p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            Student's Signature
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            Parent's Signature
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            Authorized Signatory
        </div>
    </div>

  
			<button class="print-btn" onclick="window.print()">Print</button>
		</div>
	</body>

	</html>
<?php
}
?>
