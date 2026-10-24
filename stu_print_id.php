<?php require_once("system/function.php"); ?>
<title> <?php echo $inst_name; ?></title>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

	@font-face {
		font-family: 'Geometr415 Blk BT';
		src: local('Geometr415 Blk BT'), local('Arial Black');
	}

	@page {
		size: A4 portrait;
		margin: 3mm;
	}

	body {
		margin: 0;
		padding: 0;
		font-family: 'Calibri', sans-serif;
		background: #fff;
	}

	.page-container {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		/*gap: 4mm;*/
		width: 100%;
		/*padding-top: 2mm;*/
	}

	.idcard {
		width: 88mm;
		height: 58mm;
		background: url('assets/images/id_card.png') no-repeat;
		background-size: 100% 100%;
		/*margin-bottom: 2mm;*/
		position: relative;
		page-break-inside: avoid;
		box-sizing: border-box;
		overflow: hidden;
	}

	/* Absolute Values */
	.val-name {
		position: absolute;
		top: 62px;
		left: 4px;
		width: 220px;
		text-align: center;
		font-family: 'Aptos Black', 'Arial Black', sans-serif;
		font-size: 10.5pt;
		color: #8c1010;
		text-transform: uppercase;
	}

	.val-class {
		position: absolute;
		top: 83px;
		left: 4px;
		width: 220px;
		text-align: center;
		font-family: 'Arial Black', sans-serif;
		font-size: 9pt;
		color: #d11212;
	}

	/* Values precisely measured from background image (979×614px → 333×219px CSS, scale=0.34) */
	.val-father {
		position: absolute;
		top: 101px;
		left: 80px;
		/* "Father's Name:" ends ~x=235 → 235×0.34=80px */
		font-family: 'Calibri', sans-serif;
		font-size: 8pt;
		font-weight: bold;
		color: #8c1010;
		width: 150px;
	}

	.val-adm {
		position: absolute;
		top: 118px;
		left: 57px;
		font-family: 'Calibri', sans-serif;
		font-size: 8pt;
		font-weight: bold;
		color: #d11212;
	}

	.val-roll {
		position: absolute;
		top: 118px;
		left: 143px;
		/* "Roll No. :" ends ~x=420 → 143px */
		font-family: 'Calibri', sans-serif;
		font-size: 8pt;
		font-weight: bold;
		color: #d11212;
	}

	.val-addr1 {
		position: absolute;
		top: 135px;
		left: 15px;
		text-indent: 39px;  /* first line: 14+39=53px, aligns after "Address :" label */
		font-family: 'Calibri', sans-serif;
		font-size: 8pt;
		font-weight: bold;
		color: #8c1010;
		width: 200px;
		line-height: 1;
		word-break: break-word;
	}

	/* addr2 no longer needed separately; hidden by default */
	.val-addr2 {
		display: none;
	}

	.val-blood {
		position: absolute;
		top: 163px;
		left: 78px;
		/* "Blood Group :" ends ~x=230 → 78px */
		font-family: 'Calibri', sans-serif;
		font-size: 8pt;
		font-weight: bold;
		color: #d11212;
	}

	.val-mno {
		position: absolute;
		top: 179px;
		left: 100px;
		/* "M.No.:" ends ~x=210 → 71px */
		font-family: 'Geometr415 Blk BT', 'Arial Black', sans-serif;
		font-size: 10pt;
		color: #d11212;
	}

	.val-bus {
		position: absolute;
		top: 197px;
		left: 120px;
		/* "Bus Stop:" ends ~x=235 → 80px */
		font-family: 'Calibri', sans-serif;
		font-size: 10px;
		font-weight: bold;
		color: #8c1010;
	}

	/* Photo circle precisely measured from background image (979×614px):
	   Left edge: x≈680 → 680/979×333 = 231px from left → right = 333-328 = 5px
	   Top edge:  y≈165 → 165/614×219 = 59px from top
	   Width:    (965-680)/979×333 = 97px
	   Height:   (455-165)/614×219 = 103px  */
	.photo-container {
		position: absolute;
		top: 34px;
		right: -23px;
		width: 147px;
		height: 149px;
		border-radius: 50%;
		border: none;
		overflow: hidden;
		z-index: 5;
	}

	.photo-container img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	/* Signature positioned over the script text in background image
	   Signature text center: ~x=740,y=530 → right=(979-740)/979×333=81px→right:30px, bottom=(614-530)/614×219=30px */
	.val-sign {
		position: absolute;
		bottom: 28px;
		right: 30px;
		height: 22px;
		z-index: 5;
	}

	@media print {
		#printbtn {
			display: none;
		}
	}
</style>

<?php
extract($_POST);
if (isset($_POST['student_adm']) and $_POST['student_adm'] != '') {
	$sql = "select * from student where student_admission in ($student_adm) and status ='ACTIVE'";
} else {
	$student_type = "'" . implode("','", $student_type) . "'";
	$sql = "select * from student where student_type in($student_type) and student_class = '$student_class' and student_section ='$student_section' and status ='ACTIVE'";
}
$res = direct_sql($sql);

if ($res['count'] > 0) {
	echo "<div class='page-container'>";
	foreach ($res['data'] as $student) {
		$student_id = $student['id'];
		$student_admission = $student['student_admission'];

		$addr1 = $student['student_address1'];
		$addr2 = $student['student_address2'];

		if (trim(strtolower($addr1)) == trim(strtolower($addr2)) || trim(strtolower($addr2)) == 'null' || empty(trim($addr2))) {
			$addr2 = '';
		}

		$principal_sign = 'assets/images/sign.jpg'; // default or fetch from DB if needed
		?>
		<div class='idcard'>
			<div class="val-name"><?php echo $student['student_name']; ?></div>
			<div class="val-class"><?php echo $student['student_class']; ?> 		<?php echo $student['student_section']; ?></div>

			<div class="val-father"><?php echo $student['student_father']; ?></div>
			<div class="val-adm"><?php echo $student['student_admission']; ?></div>
			<div class="val-roll"><?php echo $student['student_roll']; ?></div>
			<div class="val-addr1"><?php 
				$full_addr = trim($addr1);
				if (!empty($addr2)) $full_addr .= ', ' . trim($addr2);
				echo $full_addr; 
			?></div>
			<?php
			$bg = trim($student['student_bloodgroup']);
			if (!empty($bg) && strtolower($bg) !== 'null') {
				echo '<div class="val-blood">' . $bg . '</div>';
			}
			?>

			<div class="val-mno"><?php echo $student['student_mobile']; ?></div>
			<?php

			$area_id = trim($student['area_id']);
			$bus = get_data('transport_area', $area_id, 'area_name')['data'];
			if (!empty($bus) && strtolower($bus) !== 'null') {
				echo '<div class="val-bus">' . $bus . '</div>';
			}
			?>

			<div class="photo-container">
				<img src='upload/<?php echo $student['student_photo']; ?>' alt='Photo' />
			</div>

		</div>

		<?php
	}
	echo "</div>";
} else {
	echo "No Student Found";
}
?>