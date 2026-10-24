<?php
require_once('system/function.php');

$sql = "CREATE TABLE IF NOT EXISTS `max_marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `pt1` varchar(10) DEFAULT NULL,
  `pt2` varchar(10) DEFAULT NULL,
  `nb` varchar(10) DEFAULT NULL,
  `ma` varchar(10) DEFAULT NULL,
  `hy` varchar(10) DEFAULT NULL,
  `an` varchar(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($con, $sql)) {
    echo "Table max_marks created successfully.\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}
?>
