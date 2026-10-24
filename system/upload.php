<?php
header('Content-Type: application/json');

$uploadDir = "../upload/";
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'pdf' => 'application/pdf',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'doc' => 'application/msword'
];

$response = ['files' => [], 'errors' => []];

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
    $originalName = basename($_FILES['files']['name'][$index]);
    $fileSize = $_FILES['files']['size'][$index];
    $fileType = $_FILES['files']['type'][$index];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Validate extension and type
    if (!array_key_exists($ext, $allowedTypes) || $allowedTypes[$ext] !== $fileType) {
        $response['errors'][] = "$originalName: Invalid file type.";
        continue;
    }

    // Validate file size
    if ($fileSize > $maxSize) {
        $response['errors'][] = "$originalName: File is too large (max 5MB).";
        continue;
    }

    // Move file
    $safeName = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $originalName);
    $targetPath = $uploadDir . $safeName;

    if (move_uploaded_file($tmpName, $targetPath)) {
        $response['files'][] = [
            'name' => $originalName,
            'url' => $safeName,
            'type' => $fileType
        ];
    } else {
        $response['errors'][] = "$originalName: Upload failed.";
    }
}

echo json_encode($response);
