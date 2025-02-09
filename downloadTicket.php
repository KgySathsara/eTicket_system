<?php
include "./Database/connection.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log debug information
$log_file = "debug_log.txt";  // Log file
$debug_message = date("Y-m-d H:i:s") . " - Request received. Data: " . json_encode($_GET) . "\n";
file_put_contents($log_file, $debug_message, FILE_APPEND);

// Check if 'file' parameter exists
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Error: No file specified.");
}

// Sanitize file input
$file = basename($_GET['file']);  // Prevent directory traversal
$file_path = "images/ticketQR/" . $file;

// Log file path check
file_put_contents($log_file, "Checking file path: " . $file_path . "\n", FILE_APPEND);

// Verify if file exists
if (!file_exists($file_path)) {
    die("Error: File not found. Checked Path: " . $file_path);
}

// Log file found success
file_put_contents($log_file, "File found. Proceeding with download.\n", FILE_APPEND);

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Read file and exit
readfile($file_path);
exit;
?>
