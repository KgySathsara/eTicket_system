<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if a ticket ID is provided
if (!isset($_GET['id'])) {
    echo "<script>alert('No ticket ID provided!'); window.location='allTicket.php';</script>";
    exit();
}

$ticket_id = intval($_GET['id']); // Secure against SQL injection

// Fetch ticket details
$sql = "SELECT ticket_type, is_scan FROM ticket WHERE ticket_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $ticket_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Ticket not found!'); window.location='allTicket.php';</script>";
    exit();
}

$ticket = mysqli_fetch_assoc($result);

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_type = in_array($_POST['ticket_type'], ['couple', 'individual', 'child']) ? $_POST['ticket_type'] : 'individual';
    $is_scan = isset($_POST['is_scan']) ? 1 : 0;

    $updateQuery = "UPDATE ticket SET ticket_type = ?, is_scan = ?, updated_at = NOW() WHERE ticket_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sii", $ticket_type, $is_scan, $ticket_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Ticket updated successfully!'); window.location='allTicket.php';</script>";
    } else {
        echo "<script>alert('Error updating ticket!');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket</title>

    <!-- CSS Libraries -->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">

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

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <div class="dashboard-container">
        <h2><i class="fa fa-edit"></i> Edit Ticket</h2>

        <form method="POST" class="mt-4">
            <div class="form-group">
                <label>Ticket Type</label>
                <select name="ticket_type" class="form-control" required>
                    <option value="couple" <?php echo ($ticket['ticket_type'] == 'couple') ? 'selected' : ''; ?>>Couple</option>
                    <option value="individual" <?php echo ($ticket['ticket_type'] == 'individual') ? 'selected' : ''; ?>>Individual</option>
                    <option value="child" <?php echo ($ticket['ticket_type'] == 'child') ? 'selected' : ''; ?>>Child</option>
                </select>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" name="is_scan" class="form-check-input" <?php echo $ticket['is_scan'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Scanned</label>
            </div>

            <button type="submit" class="btn btn-primary">Update Ticket</button>
            <a href="allTicket.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
<footer class="footer">
    <p>Powered by <a href="https://www.eblix.com.au/" target="_blank">eBlix Technology</a></p>
</footer>
</html>
