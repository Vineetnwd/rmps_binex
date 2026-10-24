<?php 
$uploadDir = 'upload/';
$maxFileSize = 2 * 1024 * 1024; // 2MB limit
$allowedTypes = ['image/jpeg', 'image/png'];

if (!isset($_FILES['image'])) {
    http_response_code(400);
    echo "No file uploaded!";
    exit;
}

$file = $_FILES['image'];

// Check upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo "Upload error code: " . $file['error'];
    exit;
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo "File size exceeds 2MB limit.";
    exit;
}

// Validate file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo "Only JPG and PNG images are allowed.";
    exit;
}

// Sanitize file name
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'img_' . time() . '_' . rand(1000,9999) . '.' . $ext;
$targetPath = $uploadDir . basename($filename);

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Move file
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo "Upload successful: $targetPath";
} else {
    http_response_code(500);
    echo "Failed to move uploaded file.";
}
?>

