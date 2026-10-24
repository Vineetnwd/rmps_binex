<?php 
include('system/function.php');

$param = decode($_GET['link']);

$table = $param['table'];
$id = $param['id'];

$res = get_data($table, $id);
if($res['status'] == 'success') {
    $data = $res['data'];
    unset($data['id'], $data['created_by'], $data['created_at'], $data['updated_at'], $data['updated_by'], $data['category'], $data['publisher_name']);
}
if($_SESSION['branch_id']==1){
    $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
}else if($_SESSION['branch_id']==2){
    $img_url = $base_url.'student_photo/Barauli/Photo/';
}else{
    $img_url = $base_url.'student_photo/Barharia/Photo/';
}

?>
    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        @media print {
            body { display: none; }
        }
        #view_data .content {
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
        }
        #view_data .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-weight: bold;
        }
        /* General row styles for alternating colors */
        .table tbody tr:nth-child(odd) {
            background-color: #d9d9d9; /* Light gray for odd rows */
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #ffffff; /* White for even rows */
        }
        
        /* Optional: Add hover effect */
        .table tbody tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }


    </style>
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
    <div id='view_data'>
    <div class="content bg-light border rounded shadow-sm">
        <?php 
            $info = "<div class='table-responsive'><table cellpadding='5' rules='rows' bordercolor='gray' width='100%'>";
            $info .= "<thead><tr class='bg-gray'><th>Field</th><th>Details</th></tr></thead><tbody>";

            foreach ($data as $key => $value) {
                if ($key == 'photo' or $key == 'student_photo'  ) {
                    $display_key = add_space($key);
                    if($value!=''){
                        $display_val = "<img src='upload/".$value."' width='100px' class='img-thumbnail'>";
                    }else{
                        $photo = $img_url . $data['student_admission'] . ".jpg";
                        $display_val = "<img src='".$photo.".jpg' width='100px' class='img-thumbnail'>";
                    }
                } elseif ($key == 'student_id') {
                    $display_key = 'Student Name';
                    $display_val = get_data('student', $value, 'student_name')['data'];
                } elseif ($key == 'area_id') {
                    $display_key = 'Area Name';
                    $display_val = get_data('transport_area', $value, 'area_name')['data'];
                }
                elseif ($key == 'trip_id') {
                    $display_key = 'Trip Name';
                    $display_val = get_data('trip_details', $value, 'trip_name')['data'];
                }
                elseif ($key == 'cat_id') {
                    $display_key = 'Category Name';
                    $display_val = get_data('book_cat', $value, 'cat_name')['data'];
                } elseif ($key == 'pub_id') {
                    $display_key = 'Publisher Name';
                    $display_val = get_data('book_pub', $value, 'pub_name')['data'];
                } elseif ($key == 'issue_by') {
                    $display_key = 'Issued By';
                    $display_val = get_data('user', $value, 'full_name')['data'];
                } elseif (strpos($key, 'date') !== false && $value != null) {
                    $display_key = add_space($key);
                    $display_val = date('d-M-Y', strtotime($value));
                } elseif ($key == 'valid_till') {
                    $display_key = 'Valid Till';
                    $display_val = date('d-M-Y', strtotime($value));
                } else {
                    $display_key = add_space($key);
                    $display_val = wordwrap($value, 55, '<br>', true);
                }

                $info .= "<tr><td><b>".$display_key."</b></td><td> : ".$display_val."</td></tr>";
            }
            $info .= "</tbody></table></div>";
            
            echo $info;
        ?>
    </div>
</div>