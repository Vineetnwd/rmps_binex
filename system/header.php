<?php require_once('function.php');
$_POST = post_clean($_POST);
$_GET = post_clean($_GET);
if (!isset($_SESSION['initiated']))
     {
      echo "<script> window.location = '{$base_url}system/master_process?task=logout' </script>";
     }
else{                     
  $user_id =$_SESSION['user_id'];
  $udata =get_data('user',$user_id)['data'];
  $user_name =$udata['user_name'];
  $user_type =$udata['user_type'];
  $branch_id =$udata['branch_id'];
  session_regenerate_id();
}

if($user_type=='Teacher')
{
    if(in_array(basename($_SERVER['REQUEST_URI']) ,['teacher_marks_entry','co_marks_entry']))
    {
        
    }
}

closing_update();
$b_config = branch_config();
extract($b_config);
//End of Brnach Config 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name=  "apple-mobile-web-app-status-bar" content="#ffbf36">
    <meta name="theme-color" content="#ffc000">
    <link rel="manifest" href="<?= $base_url ?>/system/manifest.json">
    <link rel="icon" href="<?= $base_url ?>/assets/images/favicon.ico">
    <title><?php echo $inst_name; ?> </title>
    
	<!-- Bootstrap 4.1.3-->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/vendor_components/bootstrap/css/bootstrap.css">
	
	<!-- Bootstrap-extend-->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/css/bootstrap-extend.css">
	
	<!-- font awesome -->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/vendor_components/font-awesome/css/font-awesome.min.css">
	
	<!-- ionicons -->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/vendor_components/Ionicons/css/ionicons.min.css">
	
	<!-- theme style -->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/css/master_style.css">
	
	<!-- Minimal-art Admin skins. choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="<?= $base_url ?>/assets/css/skins/_all-skins.css">
	
	<!-- jvectormap --
	<link rel="stylesheet" href="assets/vendor_components/jvectormap/jquery-jvectormap.css">
	
	<!-- Morris charts --
	<link rel="stylesheet" href="assets/vendor_components/morris.js/morris.css">
    <!-- OfferPlant Custom CSS -->
    <!-- OfferPlant Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/bine.css">
    <?//php if($user_type=='TEACHER'){ ?>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/teacher_premium.css">
    <?//php } ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

     
    <style>
    .session-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 600;
        font-size: 15px;
        color: #333;
    }
    
    .session-label i {
        color: #000; /* you can change color */
        font-size: 19px;
    }
    </style> 
  </head>

<body class="sidebar-mini skin-yellow">
<div class="wrapper sidebar-mini skin-dask">

  <header class="main-header">
    <!-- Logo -->
    <a href="dashboard" class="logo bg-dark text-light d-none d-md-flex">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="<?= $base_url ?>/assets/images/logo.png"  alt=""></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><?php echo $inst_name; ?></span>
    </a>
    <!-- Header Navbar-->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
       <span style='font-size:20px;border-radius:6px;' class='bg-dark text-yellow p-2 px-4'><?= $full_name ?></span>
      <div class="navbar-custom-menu">
          
        <ul class="nav navbar-nav">
           
            <li class="session-label mr-2">
                <i class="fa fa-university"></i>
                <?=get_data('branch_list',$_SESSION['branch_id'],'inst_name')['data']?>
            </li>
            <li class="session-label">
                <i class="fa fa-calendar"></i>
                <?=$session_list[$db_name]?>
            </li>
        <?php if(strtoupper($user_type)=='ADMIN' or strtoupper($user_type)=='DEV') {?> 
		  <!-- Tasks-->
          <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <kbd><i class="fa fa-desktop"></i></kbd>
            </a>
            <ul class="dropdown-menu scale-up">
              <li class="header">Hot Link & ShortCut</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu inner-content-div">
                  
                  <li><!-- Task item -->
                    <a href="add_student">
                      <h3>
                        Dashboard
                        <small class="pull-right badge bg-dark text-light">F1</small>
                      </h3>
                    </a>
                  </li>
                  
                  <li><!-- Task item -->
                    <a href="manage_student">
                      <h3>
                        Manage Student 
                        <small class="pull-right badge bg-dark text-light">F2</small>
                      </h3>
                    </a>
                  </li>
                  
                  <li><!-- Task item -->
                    <a href="collection_report">
                      <h3>
                        Collection Report  
                        <small class="pull-right badge bg-dark text-light">F3</small>
                      </h3>
                    </a>
                  </li>
                  <li><!-- Task item -->
                    <a href="collect_fee">
                      <h3>
                        Collect Fee 
                        <small class="pull-right badge bg-dark text-light">F4</small>
                      </h3>
                    </a>
                  </li>
                  <!-- end task item -->
                 <li><!-- Task item -->
                    <a href="collect_fee">
                      <h3>
                        Send SMS 
                        <small class="pull-right badge bg-dark text-light">F5</small>
                      </h3>
                    </a>
                  </li>
                  <!-- end task item -->
                    <li><!-- Task item -->
                    <a href="collect_fee">
                      <h3>
                        Save & Update  
                        <small class="pull-right badge bg-dark text-light">Ctrl + S</small>
                      </h3>
                    </a>
                  </li>
                  
                  <li><!-- Task item -->
                    <a href="collect_fee">
                      <h3>
                        Search Student  
                        <small class="pull-right badge bg-dark text-light">Ctrl + F</small>
                      </h3>
                    </a>
                  </li>
                 
                  <!-- end task item -->
                </ul>
              </li>
              <!--<li class="footer">-->
              <!--  <a href="#">View all tasks</a>-->
              <!--</li>-->
            </ul>
          </li>
		<?php } ?>  
		<?php if(isset($_SESSION['prev_user_id'])){ 
		    $prev_user = get_data('user',$_SESSION['prev_user_id'])['data'];
		?>
          <li class="dropdown user user-menu">
            <a class="login_as dropdown-toggle" data-toggle="dropdown" title='Back to Previous Account' data-uid='<?= $prev_user['user_name']?>' data-code='<?= $prev_user['user_pass']?>'  >
              <img src="<?= $base_url ?>/assets/images/login.png" class="user-image rounded-circle" alt="User Image">
            </a>
            </li>
        <?php } ?>
          <!-- User Account-->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?= $base_url ?>/assets/images/user2-160x160.jpg" class="user-image rounded-circle" alt="User Image">
            </a>
           <ul class="dropdown-menu scale-up">
              <!-- User image -->
              <li class="user-header">
                <img src="<?= $base_url ?>/assets/images/user2-160x160.jpg" class="float-left rounded-circle" alt="User Image">

                <p>
                  <?php echo $udata['full_name']; ?>
                  <small class="mb-5"><?php echo $udata['user_email']; ?></small>
                  <small class="mb-5"><?php echo $udata['user_mobile']; ?></small>
                  <small class="mb-5"><?php echo date('d M Y h:i A', strtotime($udata['last_login'])); ?></small>
                 
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row no-gutters">
                  <div class="col-12 text-left">
                    <a href="#"><i class="ion ion-person"></i> My Profile</a>
                  </div>
                  <div class="col-12 text-left">
                    <a href="#"><i class="ion ion-email-unread"></i> Inbox</a>
                  </div>
                  <div class="col-12 text-left">
                    <a href="#"><i class="ion ion-settings"></i> Setting</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="change_password" class="btn btn-block btn-primary"><i class="ion ion-locked"></i> Change Password</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-block btn-danger" onclick='logout()'><i class="ion ion-power"></i> Log Out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Messages-->
        </ul>
      </div>
    </nav>
  </header>

