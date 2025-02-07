<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
}

// Fetch Member and Ticket Count
$query = "SELECT 
            (SELECT COUNT(*) FROM member) AS member_count, 
            (SELECT COUNT(*) FROM ticket) AS ticket_count";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$memberCount = $row['member_count'];
$ticketCount = $row['ticket_count'];

// Fetch Member Data with Ticket Details
$memberQuery = "SELECT 
                m.member_id, 
                m.member_name, 
                m.member_mobile_number, 
                COALESCE(GROUP_CONCAT(t.ticket_type SEPARATOR ', '), 'No Ticket') AS tickets,
                MAX(t.updated_at) AS last_updated  -- Get the most recent update time
                FROM member m 
                LEFT JOIN ticket t ON m.member_id = t.member_id
                GROUP BY m.member_id, m.member_name, m.member_mobile_number
                ORDER BY last_updated DESC";  // Order by last updated date (most recent first)


$memberResult = mysqli_query($conn, $memberQuery);

// Function to Fetch Tickets for Each Member
function getTickets($member_id, $conn) {
    $ticketQuery = "SELECT ticket_type FROM ticket WHERE member_id = ?";
    $stmt = mysqli_prepare($conn, $ticketQuery);
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $tickets = [];
    while ($ticket = mysqli_fetch_assoc($result)) {
        $tickets[] = htmlspecialchars($ticket['ticket_type']);
    }

    return $tickets;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>

    <!-- CSS Libraries -->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
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
        .dashboard-container { background: rgba(255, 255, 255, 0.2); padding: 30px; border-radius: 10px; box-shadow: 0px 10px 30px rgba(149, 24, 24, 0.3); color: black; }
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
    <div class="dashboard-container mt-2">
        <h2><i class='fa fa-home'></i> Member Dashboard</h2>

        <!-- Add Member Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="add_member.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add Member</a>
        </div>

        <!-- Card UI for Member & Ticket Counts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light text-dark text-center p-4">
                    <i class="fa fa-users text-primary fa-3x"></i> <!-- Increased icon size -->
                    <h4>Total Members</h4>
                    <h2><?php echo $memberCount; ?></h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-light text-dark text-center p-4">
                    <i class="fa fa-ticket text-danger fa-3x"></i> <!-- Increased icon size -->
                    <h4>Total Tickets</h4>
                    <h2><?php echo $ticketCount; ?></h2>
                </div>
            </div>
        </div>

        <!-- Member Table -->
        <table class="table table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Member No</th>
                    <th>Member Name</th>
                    <th>Mobile Number</th>
                    <th>Tickets</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($memberResult)) { 
                    $tickets = getTickets($row['member_id'], $conn);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_mobile_number']); ?></td>
                        <td>
                            <?php 
                            if (empty($tickets)) {
                                echo "<span class='badge badge-danger'>No Ticket</span>";
                            } else {
                                foreach ($tickets as $ticket) {
                                    echo "<span class='badge badge-info'>$ticket</span> ";
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <a href="create_ticket.php?member_id=<?php echo $row['member_id']; ?>" 
                                class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i>
                            </a>
                            <a href='edit_member.php?id=<?php echo $row['member_id']; ?>' class='btn btn-warning btn-sm'>
                                <i class='fa fa-edit'></i>
                            </a>
                            <a href='delete_ticket.php?id=<?php echo $row['member_id']; ?>' 
                                class='btn btn-danger btn-sm' 
                                onclick="return confirm('Are you sure you want to delete this member?');">
                                <i class='fa fa-trash'></i>
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
