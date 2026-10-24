<?php require_once('system/function.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($inst_name) ?$full_name :' Bine ' ?> : Digital Backbone of School</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #333;
        background-color: #f7f9fc;
        background-image: url('assets/images/back.png');
        background-position:50% 65%;
        opacity:0.9;
    }
    .wrapper {
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: 800px;
      margin: 20px;
      background: #ffffff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    @media (min-width: 768px) {
      .wrapper {
        flex-direction: row;
      }
    }
    .left-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 30px;
      background: linear-gradient(135deg, #1f2940, #004c76);
      color: #fff;
      text-align: center;
    }
    .left-section img {
      width: 150px;
      margin-bottom: 20px;
    }
    .left-section h1 {
      font-size: 1.8rem;
      margin-bottom: 10px;
    }
    .left-section p {
      font-size: 1rem;
      line-height: 1.5;
      max-width: 300px;
    }
    .right-section {
      flex: 1;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    h2 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #004c76;
    }
    .form-group {
      margin-bottom: 15px;
      position: relative;
    }
    .form-group input {
      width: 100%;
      padding: 12px 15px;
      font-size: 1rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      background: #f9f9f9;
      color: #333;
      outline: none;
      transition: all 0.3s;
    }
    .form-group input:focus {
      border-color: #131b2e;
      box-shadow: 0 0 5px rgba(76, 132, 255, 0.5);
    }
    .btn {
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      color: #fff;
      background: #c20a09;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #004c76;
    }
    .forgot-link {
      display: block;
      margin-top: 10px;
      text-align: center;
      font-size: 0.9rem;
      color: #4c84ff;
      text-decoration: none;
    }
    .forgot-link:hover {
      text-decoration: underline;
    }
    .credit{
        font-size:10px;
        color:#1f2940;
        text-align:center;
        margin-top:20px;
    }
    @media (max-width: 768px) {
      .left-section {
        padding: 20px;
      }
      .right-section {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="left-section">
      <img src="assets/images/logo.png" alt="Logo" >
      <h1><?= $full_name?> </h1>
      <p>Digital backbone of your Schools.</p>
    </div>
    <div class="right-section">
        <br>
      <center><h2>Login to Your Account</h2></center>
      <form id="login_frm">
        <div class="form-group">
          <input type="text" placeholder="Username" name="user_name" required>
        </div>
        <div class="form-group">
          <input type="password" placeholder="Password" name="user_pass" required>
        </div>
        <button type="button" id="login_btn" class="btn">SIGN IN</button>
      </form>
      <a href="javascript:void(0)" id="forget_password" class="forgot-link">Forgot Password?</a>
       <div class='credit'> BineX 5.0 | Planted By <a href='https://offerplant.com' target='_blank'>OfferPlant</a> </div>
    </div>
   
  </div>


	<!-- jQuery 3 -->
	<script src="<?= $base_url ?>/assets/vendor_components/jquery-3.3.1/jquery-3.3.1.js"></script>

	<script src="<?= $base_url ?>/assets/vendor_components/popper/dist/popper.min.js"></script>
	<!-- Bootstrap 4.1.3--	-->
	<script src="<?= $base_url ?>/assets/vendor_components/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?= $base_url ?>/assets/js/jquery.validate.min.js"></script>
	<script src="<?= $base_url ?>/assets/js/bootbox.all.js"></script>
	<script src="<?= $base_url ?>/assets/js/notify.min.js"></script>
	<script src="<?= $base_url ?>/assets/js/op.js"></script>
    <script>
        $(document).ready(function() {
          $(window).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              $("#login_btn").trigger('click');
            }
          });
        });
    </script>
</body>
</html>
