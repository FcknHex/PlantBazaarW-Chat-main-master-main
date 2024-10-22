<?php
include '../conn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Google reCAPTCHA secret key
    $secretKey = "6LfL8mUqAAAAANpODH758b9EVgK3A5k7dJdd5q4h";
    
    $captcha = $_POST['g-recaptcha-response'];

    // Verify the reCAPTCHA response with Google
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        echo json_encode(['success' => false, 'message' => 'CAPTCHA validation failed.']);
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if($user['password'] == $password) {
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(array('success' => true, 'message' => 'Successfully logged in'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Invalid password'));
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'Email not found'));
    }
}
?>