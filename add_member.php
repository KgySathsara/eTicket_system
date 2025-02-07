<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_member'])) {
    $member_name = trim($_POST['member_name']);
    $member_mobile = trim($_POST['member_mobile']);
    $member_email = trim($_POST['member_email']);

    if (empty($member_name) || empty($member_mobile) || empty($member_email)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);

        // Insert Member Data
        $sql_member = "INSERT INTO member (member_name, member_mobile_number, member_email, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt_member = mysqli_prepare($conn, $sql_member);

        if ($stmt_member) {
            mysqli_stmt_bind_param($stmt_member, "sss", $member_name, $member_mobile, $member_email);
            if (mysqli_stmt_execute($stmt_member)) {
                mysqli_commit($conn);
                echo "<script>alert('Member added successfully!'); window.location.href='dashboard.php';</script>";
            } else {
                mysqli_rollback($conn);
                echo "<script>alert('Error adding member. Please try again.');</script>";
            }
            mysqli_stmt_close($stmt_member);
        } else {
            echo "<script>alert('Database error!');</script>";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member</title>
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: white;
            color: black;
        }

        .sidebar {
            width: 200px;
            height: 100vh;
            background: rgba(43, 3, 26, 0.8);
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            color: white;
        }

        .sidebar h3 {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 12px;
            text-align: left;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }


        .main-content {
            color: #333;
            margin-left: 200px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(53, 53, 239, 0.2);
            padding: 15px;
            border-radius: 8px;
        }

        .header h3 {
            color: black;
        }

        .dashboard-container {
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(149, 24, 24, 0.3);
            color: white;
        }

        .footer {
            color: gray;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer a {
            color: rgb(76, 123, 188);
            text-decoration: none;
        }

        .footer a:hover {
            color: rgb(77, 70, 35);
            text-decoration: underline;
        }

        .member{
            color: black;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Admin Panel</h3>
            <ul>
                <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
                <li><a href="add_member.php"><i class="fa fa-user-plus"></i> Add Member</a></li>
                <li><a href="allTicket.php"><i class="fa fa-ticket"></i> Tickets</a></li>
                <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="dashboard-container mt-4">
            <h3 class=" member text-center"><i class="fa fa-user-plus"></i> Add New Member</h3>

            <form method="POST" action="add_member.php" class="member">
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user"></i> Member Name</label>
                    <input type="text" class="form-control" name="member_name" placeholder="Enter member name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-phone"></i> Mobile Number</label>
                    <input type="text" class="form-control" name="member_mobile" placeholder="Enter mobile number" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-envelope"></i> Email Address</label>
                    <input type="email" class="form-control" name="member_email" placeholder="Enter email address" required>
                </div>

                <button type="submit" name="add_member" class="btn btn-primary w-100"><i class="fa fa-save"></i> Submit</button>
                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
            </form>
        </div>
    </div>

</body>
<footer class="footer">
    <div class="container text-center">
        <p>Powered by <a href="https://www.eblix.com.au/" target="_blank">eBlix Technology</a></p>
    </div>
</footer>
</html>
