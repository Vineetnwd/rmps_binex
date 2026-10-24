<?php
include_once('system/op_config.php');

$mysqli = new mysqli("localhost", $db_user, $db_password, $db_name);

$columns = [
    'id', 'student_name', 'student_father', 'student_mobile', 
    'student_class', 'student_section', 'student_roll', 
    'student_email', 'date_of_birth', 'status'
];

$limit  = $_GET['length'];
$offset = $_GET['start'];
$search = $_GET['search']['value'];
$order_col_index = $_GET['order'][0]['column'];
$order_col = $columns[$order_col_index];
$order_dir = $_GET['order'][0]['dir'];

// Search filter
$search_sql = "";
if (!empty($search)) {
    $search_terms = array_map(function($col) use ($mysqli, $search) {
        return "$col LIKE '%" . $mysqli->real_escape_string($search) . "%'";
    }, $columns);
    $search_sql = "WHERE " . implode(" OR ", $search_terms);
}

// Total records
$totalResult = $mysqli->query("SELECT COUNT(*) AS count FROM student");
$totalRow = $totalResult->fetch_assoc()['count'];

// Filtered records
$filteredResult = $mysqli->query("SELECT COUNT(*) AS count FROM student $search_sql");
$filteredRow = $filteredResult->fetch_assoc()['count'];

// Fetch records
$query = "SELECT " . implode(",", $columns) . " FROM student $search_sql ORDER BY $order_col $order_dir LIMIT $limit OFFSET $offset";
$dataResult = $mysqli->query($query);

$data = [];
while($row = $dataResult->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalRow,
    "recordsFiltered" => $filteredRow,
    "data" => $data
]);
