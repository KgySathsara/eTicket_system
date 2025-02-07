<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
}

// Check if member ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No Member ID provided!'); window.location='dashboard.php';</script>";
    die();
}

$member_id = $_GET['id'];

// Fetch existing member details
$sql = "SELECT member_id, member_name, member_mobile_number, member_email FROM member WHERE member_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $member = mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    if (!$member) {
        echo "<script>alert('Invalid Member ID!'); window.location='dashboard.php';</script>";
        die();
    }
} else {
    echo "<script>alert('Database error!');</script>";
    die();
}

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_member'])) {
    $member_name = trim($_POST['member_name']);
    $member_mobile = trim($_POST['member_mobile']);
    $member_email = trim($_POST['member_email']);

    if (empty($member_name) || empty($member_mobile) || empty($member_email)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        $sql_update = "UPDATE member SET member_name = ?, member_mobile_number = ?, member_email = ?, updated_at = NOW() WHERE member_id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);

        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssi", $member_name, $member_mobile, $member_email, $member_id);

            if (mysqli_stmt_execute($stmt_update)) {
                echo "<script>alert('Member updated successfully!'); window.location='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error updating member! Please try again.');</script>";
            }
            mysqli_stmt_close($stmt_update);
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
    <title>Edit Member</title>
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

        .member {
            color: gray;
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
            <h3 class="member text-center"><i class="fa fa-edit"></i> Edit Member</h3>

            <form method="POST" action="" class="member">
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user"></i> Member Name</label>
                    <input type="text" class="form-control" name="member_name" value="<?php echo htmlspecialchars($member['member_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-phone"></i> Mobile Number</label>
                    <input type="text" class="form-control" name="member_mobile" value="<?php echo htmlspecialchars($member['member_mobile_number']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-envelope"></i> Email Address</label>
                    <input type="email" class="form-control" name="member_email" value="<?php echo htmlspecialchars($member['member_email']); ?>" required>
                </div>

                <button type="submit" name="update_member" class="btn btn-primary w-100"><i class="fa fa-save"></i> Update Member</button>
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
