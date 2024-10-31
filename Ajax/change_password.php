
<?php
include '../conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$email = $_SESSION['email'];
$query = "SELECT id, password FROM users WHERE email = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $user = $result->fetch_assoc();
    $password = $user['password'];
    $userId = $user['id'];
} else {
    echo json_encode(['status' => 'failed', 'message' => 'Error retrieving user data.']);
    exit;
}

    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate the current password
    if (!password_verify($currentPassword, $password)) {
    echo json_encode(['status' => 'failed-current', 'message' => 'Current password is incorrect.']);
    exit;
    }
    
    // Validate current password to new password
    if ($currentPassword === $newPassword) {
        echo json_encode(['status' => 'failed-new', 'message' => 'Your new password must be different from your current password.']);
        exit;
    }

    // Validate the new password
    if (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^a-zA-Z\d]/', $newPassword)) {
        echo json_encode(['status' => 'failed-new', 'message' => 'Your new password must be at least 8 characters long and include at least one number and one special character.']);
        exit;
    }
    
    // Validate the confirmation password
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'failed-confirm', 'message' => 'The confirmation password does not match.']);
        exit;
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $hashedPassword, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failed', 'message' => 'Error updating password.']);
    }
    $stmt->close();
    }
    else {
        echo json_encode(['status' => 'failed', 'message' => 'Invalid request.']);
    }

    ?>