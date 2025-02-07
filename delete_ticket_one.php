<?php
include "./Database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('No ticket ID provided!'); window.location='allTicket.php';</script>";
    exit();
}

$ticket_id = intval($_GET['id']);

// Delete the ticket
$deleteQuery = "DELETE FROM ticket WHERE ticket_id = ?";
$stmt = mysqli_prepare($conn, $deleteQuery);
mysqli_stmt_bind_param($stmt, "i", $ticket_id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    echo "<script>alert('Ticket deleted successfully!'); window.location='allTicket.php';</script>";
} else {
    echo "<script>alert('Error deleting ticket!'); window.location='allTicket.php';</script>";
}

mysqli_close($conn);
?>
