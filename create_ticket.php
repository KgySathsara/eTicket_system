<?php
include "./Database/connection.php";

if (!isset($_GET['member_id'])) {
    die("Invalid request. No member selected.");
}

$member_id = intval($_GET['member_id']);

// Fetch Member Name
$memberQuery = "SELECT member_name FROM member WHERE member_id = ?";
$stmt = mysqli_prepare($conn, $memberQuery);
mysqli_stmt_bind_param($stmt, "i", $member_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$member = mysqli_fetch_assoc($result);

if (!$member) {
    die("Member not found.");
}

$member_name = htmlspecialchars($member['member_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket</title>

    <!-- CSS Libraries -->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background: white; color: black; }
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

        .main-content { margin-left: 200px; padding: 20px; }
        .dashboard-container { background: rgba(255, 255, 255, 0.2); padding: 40px; border-radius: 10px; box-shadow: 0px 10px 30px rgba(149, 24, 24, 0.3); color: black; }
        .card { border-radius: 10px; padding: 20px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }
        .footer { color: gray; text-align: center; padding: 15px 0; position: fixed; width: 100%; bottom: 0; }
        .header { display: flex;  justify-content: space-between; align-items: center; background: rgba(53, 53, 239, 0.2); padding: 15px; border-radius: 8px;}
        .header h3 { color: black;}
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Admin Panel</h3>
    <ul>
        <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
        <li><a href="add_member.php"><i class="fa fa-user-plus"></i> Add Member</a></li>
        <li><a href="allTicket.php" class="active"><i class="fa fa-ticket"></i> Tickets</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="dashboard-container mt-4">
        <h2 class="text-center mb-4"><i class="fa fa-ticket"></i> Create Ticket</h2>
        
        <form action="process_ticket.php" method="POST">
            <div class="form-group">
                <label>Member Name:</label>
                <input type="text" class="form-control" value="<?php echo $member_name; ?>" readonly>
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
            </div>

            <div class="form-group">
                <label for="ticket_type">Select Ticket Type:</label>
                <select name="ticket_type" id="ticket_type" class="form-control" required>
                    <option value="">-- Select Ticket Type --</option>
                    <option value="Individual">Individual</option>
                    <option value="Couple">Couple</option>
                    <option value="Child">Child</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Create Ticket</button>
            <a href="dashboard.php" class="btn btn-secondary btn-block"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
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
