<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "./Database/connection.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing  or password"]);
    exit();
}

$email = trim($data['email']);
$password = trim($data['password']);

$sql = "SELECT * FROM `user` WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($user) {
        if (password_verify($password, password_hash($user["password"], PASSWORD_BCRYPT))) {
            //$_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user_id" => $user['user_id']
            ]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect Password!"]);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found!"]);
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["status" => "error", "message" => "Database error!"]);
    exit();
}

mysqli_close($conn);
?>