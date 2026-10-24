<?php
require_once('system/function.php');

$sql = "CREATE TABLE IF NOT EXISTS `co_scholastic_grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($con, $sql)) {
    echo "Table co_scholastic_grades created successfully.\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}
?>
