<?php 
include "./Database/connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Page</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="css/util.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
</head>
    <style>
        .footer {
        color: gray;
        text-align: center;
        padding: 15px 0;
        position: fixed;
        width: 100%;
        bottom: 0;
        }

        .footer a {
            color: #ffeb3b;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer a:hover {
            color: #ffd600;
            text-decoration: underline;
        }
    </style>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields!');</script>";
    } else {
        $sql = "SELECT * FROM `user` WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

            // print_r($user["password"]);
            // echo '<br>';
            // print_r($password);
            // echo '<br>';
            // echo ($user["password"] === $password);
            // echo (password_verify($password, password_hash(123456789, PASSWORD_BCRYPT)));
            // die();

            if ($user) {
                if (password_verify($password, password_hash($user["password"], PASSWORD_BCRYPT))) {
                    session_start();
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: dashboard.php"); 
                    die();
                } else {
                    echo "<script>alert('Incorrect Password!');</script>";
                }
            } else {
                echo "<script>alert('User not found!');</script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Database error!');</script>";
        }
    }
    mysqli_close($conn);
}
?>

<div class="limiter">
    <div class="container-login100 bg-info">
        <div class="wrap-login100">
            <form class="login100-form validate-form"  method="POST" action="index.php">

                <span class="login100-form-title p-b-26">Welcome To Login</span>
                <span class="login100-form-title p-b-20"> <i class="zmdi zmdi-font"></i></span>

                <div class="wrap-input100 validate-input"data-validate="Enter Email">
                    <input class="input100" type="text" name="email">
                    <span class="focus-input100" data-placeholder="Email"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <span class="btn-show-pass"><i class="zmdi zmdi-eye"></i></span>
                    <input class="input100" type="password" name="password">
                    <span class="focus-input100" data-placeholder="Password"></span>
                </div>

                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" name="login"> Login </button>
                    </div>
                </div>

                <div class="text-center ">
                    <span class="txt1">Don't have an account?</span>
                    <a class="txt2" href="#">Sign Up</a>
                </div>

            </form>
        </div>
    </div>
</div>


<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
</body>
    <footer class="footer">
        <div class="container text-center">
            <p>Powered by <a href="https://www.eblix.com.au/" target="_blank">eBlix Technology</a></p>
        </div>
    </footer>
</html>
