<?php
session_start();
include "../conn.php"; // Include your database connection

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Logged-in user ID
    $reported_user_id = $_POST['reported_user_id'];
    $report_reason = $_POST['report_reason'];
    $description = $_POST['description'];

    // Handle file uploads for proof images
    $proof_images = array_fill(1, 6, null); // Initialize an array with 6 null values

    for ($i = 1; $i <= 6; $i++) {
        if (!empty($_FILES["proof_img_$i"]['name'])) {
            $target_dir = "uploads/proof_images/";

            // Sanitize the file name and add a unique ID to prevent overwriting
            $file_name = basename($_FILES["proof_img_$i"]["name"]);
            $file_name = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $file_name); // Replace unsafe characters
            $unique_name = uniqid() . "_" . $file_name; // Add a unique ID to avoid overwriting

            $target_file = $target_dir . $unique_name;

            // Check if the directory exists, if not, create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["proof_img_$i"]["tmp_name"], $target_file)) {
                $proof_images[$i] = $unique_name; // Save the uploaded file path
            }
        }
    }

    // Insert report into the database
    $sql = "INSERT INTO reports (reporting_user, reported_user, report_reason, description, proof_img_1, proof_img_2, proof_img_3, proof_img_4, proof_img_5, proof_img_6, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);

    // Prepare variables for bind_param. Replace empty file paths with NULL
    for ($i = 1; $i <= 6; $i++) {
        $proof_images[$i] = empty($proof_images[$i]) ? null : $proof_images[$i];
    }

    // Bind parameters: provide NULL where no image was uploaded
    $stmt->bind_param(
        "iissssssss",
        $user_id,
        $reported_user_id,
        $report_reason,
        $description,
        $proof_images[1],
        $proof_images[2],
        $proof_images[3],
        $proof_images[4],
        $proof_images[5],
        $proof_images[6]
    );

    // Check if the user has already submitted a report for the same user
    $reportsQuery = "SELECT COUNT(*) as report_count FROM reports WHERE reporting_user = ? AND reported_user = ?";
    $stmtCheck = $conn->prepare($reportsQuery);
    $stmtCheck->bind_param("ii", $user_id, $reported_user_id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result()->fetch_assoc();

    if ($resultCheck['report_count'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Wait for the admin to review your report', 'title' => 'You already reported this user.']);
        exit();
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting the report: ' . $stmt->error, 'title' => 'Submission Failed']);
    }
}
?>
