<?php
// Since this is a public page, we don't require system/menu.php or system/header.php which are for admin
// But we need DB connection. Let's assume process.php or similar handles DB if this is a standalone root file.
// Or we can include the standard header if it's acceptable. Let's see how index.php does it.
// Assuming we have basic DB access or include process.php:
require_once('process.php'); // General DB connection and functions
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Projects</title>
    <!-- Use basic Bootstrap for the public page -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .project-card { margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; border-radius: 8px; }
        .status-badge { font-size: 0.9em; padding: 6px 10px; }
        .page-header { background: #343a40; color: white; padding: 40px 0; margin-bottom: 40px; text-align: center; }
    </style>
</head>
<body>

<div class="page-header">
    <h1>Our Projects</h1>
    <p>Discover the latest projects and their current status</p>
</div>

<div class="container">
    <div class="row">
        <?php
        // Fetch all active projects
        $res = get_all('project', '*', ['status_flag' => 'ACTIVE', 'branch_id' => 1]); // Assuming branch 1 for public
        if(isset($res['count']) && $res['count'] > 0) {
            foreach($res['data'] as $row) {
                if(empty($row['project_name'])) continue;
                
                $status_class = 'badge-warning';
                if($row['status'] == 'COMPLETED') $status_class = 'badge-success';
                else if($row['status'] == 'ONGOING') $status_class = 'badge-primary';
                
                echo '<div class="col-md-6 col-lg-4">';
                echo '  <div class="card project-card">';
                echo '    <div class="card-body">';
                echo '      <h5 class="card-title">'.htmlspecialchars($row['project_name']).'</h5>';
                echo '      <h6 class="card-subtitle mb-2 text-muted">'.htmlspecialchars($row['start_date']).' to '.htmlspecialchars($row['end_date']).'</h6>';
                echo '      <p class="card-text">'.htmlspecialchars($row['description']).'</p>';
                echo '      <span class="badge '.$status_class.' status-badge">'.htmlspecialchars($row['status']).'</span>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No active projects found.</div></div>';
        }
        ?>
    </div>
</div>

</body>
</html>
