<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "./Database/connection.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (empty($data['ticket_id']) || empty($data['qr_number'])) {
        echo json_encode(["status" => "error", "message" => "Missing ticket_id or qr_number"]);
        exit();
    }

    $ticket_id = intval($data['ticket_id']);
    $qr_number = intval($data['qr_number']);

    $sql = "SELECT is_scan FROM ticket WHERE ticket_id = ? AND qr_number = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $qr_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $ticket = mysqli_fetch_array($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        if ($ticket) {
            if ($ticket['is_scan'] == 1) {
                echo json_encode([
                    "status" => "error",
                    "message" => "This ticket has already been scanned!",
                    "ticket_id" => $ticket_id
                ]);

            } else {

                $update_sql = "UPDATE ticket SET is_scan = 1, updated_at = NOW() WHERE ticket_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);

                if ($update_stmt) {
                    mysqli_stmt_bind_param($update_stmt, "i", $ticket_id);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);

                    echo json_encode([
                        "status" => "success",
                        "message" => "Ticket Verified Successfully!",
                        "qr_number" => $qr_number,
                        "ticket_id" => $ticket_id
                    ]);

                } else {
                    echo json_encode(["status" => "error", "message" => "Database update error"]);
                }
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid ticket or QR code"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database query error"]);
    }
}

mysqli_close($conn);

?>

