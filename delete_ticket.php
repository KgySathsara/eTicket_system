<?php
include "./Database/connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('No member ID provided!'); window.location='dashboard.php';</script>";
    exit();
}

$member_id = intval($_GET['id']);

// Step 1: Delete all tickets for this member
$deleteTicketsQuery = "DELETE FROM ticket WHERE member_id = ?";
$stmt_tickets = mysqli_prepare($conn, $deleteTicketsQuery);
mysqli_stmt_bind_param($stmt_tickets, "i", $member_id);
mysqli_stmt_execute($stmt_tickets);
mysqli_stmt_close($stmt_tickets);

// Step 2: Delete the member
$deleteMemberQuery = "DELETE FROM member WHERE member_id = ?";
$stmt_member = mysqli_prepare($conn, $deleteMemberQuery);
mysqli_stmt_bind_param($stmt_member, "i", $member_id);

if (mysqli_stmt_execute($stmt_member)) {
    mysqli_stmt_close($stmt_member);
    echo "<script>alert('Member and all associated tickets deleted successfully!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Error deleting member!'); window.location='dashboard.php';</script>";
}

mysqli_close($conn);
?>
