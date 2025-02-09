<?php
include "./Database/connection.php";
include "phpqrcode/qrlib.php";  

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $start_time = microtime(true);

    $member_id = intval($_POST['member_id']);
    $ticket_type = mysqli_real_escape_string($conn, $_POST['ticket_type']);
    $timestamp = date("Y-m-d");

    $stmt_member = mysqli_prepare($conn, "SELECT member_name FROM member WHERE member_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_member, "i", $member_id);
    mysqli_stmt_execute($stmt_member);
    mysqli_stmt_bind_result($stmt_member, $member_name);
    mysqli_stmt_fetch($stmt_member);
    mysqli_stmt_close($stmt_member);

    if (!$member_name) {
        die("Error: Member not found.");
    }

    mysqli_begin_transaction($conn);  
    $stmt = mysqli_prepare($conn, "INSERT INTO ticket (member_id, is_scan, created_at, updated_at, ticket_type) 
                                   VALUES (?, 0, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isss", $member_id, $timestamp, $timestamp, $ticket_type);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_rollback($conn);
        die("Error: Could not create ticket.");
    }

    $ticket_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $qr_number = abs(crc32($member_id . $ticket_type . time()) % 1000000000) + rand(100000, 999999);
    $qr_data = "Member Name: $member_name\nTicket Type: $ticket_type\nTicket ID: $ticket_id\nQR Number: $qr_number";

    $qrPath = 'qrcodes/';
    $qrFile = $qrPath . "QR_" . $qr_number . ".png";
    
    switch (strtolower($ticket_type)) {
        case 'individual':
            $backgroundPath = 'images/1.png';
            break;
        case 'couple':
            $backgroundPath = 'images/2.png';
            break;
        case 'child':
            $backgroundPath = 'images/3.png';
            break;
    }
    
    $finalImagePath = "images/ticketQR/ticket_$qr_number.png";

    if (!is_dir('images/ticketQR')) {
        mkdir('images/ticketQR', 0775, true);
    }

    QRcode::png($qr_data, $qrFile, 'L', 5, 1);

    if (file_exists($backgroundPath)) {
        $background = imagecreatefrompng($backgroundPath);
        $qrCode = imagecreatefrompng($qrFile);

        $qrSize = 250;
        $resizedQR = imagescale($qrCode, $qrSize, $qrSize);
        imagecopy($background, $resizedQR, 200, 200, 0, 0, $qrSize, $qrSize);

        imagepng($background, $finalImagePath);
        imagedestroy($background);
        imagedestroy($qrCode);
        imagedestroy($resizedQR);
    } else {
        mysqli_rollback($conn);
        die("Error: Background image not found.");
    }

    // Update Ticket with QR Info
    $ticket_url = "images/ticketQR/ticket_$qr_number.png";
    $stmt_update = mysqli_prepare($conn, "UPDATE ticket SET qr_number = ?, ticket_url = ? WHERE ticket_id = ?");
    mysqli_stmt_bind_param($stmt_update, "isi", $qr_number, $ticket_url, $ticket_id);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    // Commit Transaction and Redirect
    mysqli_commit($conn);

    // Debugging: Measure execution time
    $execution_time = microtime(true) - $start_time;
    error_log("Ticket Generation Time: " . number_format($execution_time, 4) . " seconds");

    header("Location: dashboard.php?message=Ticket Created Successfully with QR Code");
    exit();
} else {
    header("Location: create_ticket.php");
    exit();
}
?>
