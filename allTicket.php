<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
}

// Fetch all tickets with member details
$ticketQuery = "SELECT 
                    t.ticket_id, 
                    t.qr_number, 
                    t.is_scan, 
                    t.ticket_url, 
                    t.created_at, 
                    t.updated_at, 
                    t.ticket_type, 
                    m.member_name, 
                    m.member_mobile_number
                FROM ticket t
                JOIN member m ON t.member_id = m.member_id
                ORDER BY t.created_at DESC";

$ticketResult = mysqli_query($conn, $ticketQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tickets</title>

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
        <h2 class="text-center"><i class="fa fa-ticket"></i> All Tickets</h2>

        <!-- Ticket Table -->
        <table class="table table-hover mt-4">
            <thead class="table-dark ">
                <tr>
                    <th style="text-align: center;">#</th>
                    <th style="background-color: #007bff; color: white; text-align: center;">Ticket ID</th>
                    <th style="text-align: center;">Attend</th>
                    <th style="text-align: center;">Member Name</th>
                    <th style="text-align: center;">Mobile Number</th>
                    <th style="text-align: center;">QR Number</th>
                    <th style="text-align: center;">Ticket Type</th>
                    <th style="text-align: center;">Updated At</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $serial_no = 1; 
                    while ($row = mysqli_fetch_assoc($ticketResult)) { 
                    ?>
                    <tr style="text-align: center;">
                        <td><?php echo $serial_no++; ?></td>
                        <td style="text-align: center;"><?php echo htmlspecialchars($row['ticket_id']); ?></td>
                        <td>
                            <?php echo ($row['is_scan'] == 1) ? "<span class='badge badge-success'>Yes</span>" : "<span class='badge badge-danger'>No</span>"; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_mobile_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['qr_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['ticket_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                        <td>
                            <a href="<?php echo $row['ticket_url']; ?>" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="download_ticket.php?file=<?php echo basename($row['ticket_url']); ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="edit_ticket.php?id=<?php echo $row['ticket_id']; ?>" 
                               class="btn btn-warning btn-sm" >
                               <i class="fa fa-edit"></i>
                            </a>
                            <a href="delete_ticket_one.php?id=<?php echo $row['ticket_id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this ticket?');">
                               <i class="fa fa-trash"></i>
                            </a>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
<footer class="footer">
    <p>Powered by <a href="https://www.eblix.com.au/" target="_blank">eBlix Technology</a></p>
</footer>
</html>
