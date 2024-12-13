<?php 
include 'conn.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize response
$response = array('status' => '', 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'sendOtp') {
        $email = $_POST['email'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid email format.';
            echo json_encode($response);
            exit;
        }

        // Check if email already exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Email already exists.';
            echo json_encode($response);
            exit;
        }

        // Generate OTP
        $otp = rand(100000, 999999);  // Generate a 6-digit OTP
        $_SESSION['otp'] = $otp;       // Store OTP in session

        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'plantbazaar21@gmail.com';   // Your email
            $mail->Password = 'izyf vnfq tkwi rkjt';            // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('plantbazaar21@gmail.com', 'PlantBazaar');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is <b>$otp</b>";

            $mail->send();
            $response['status'] = 'success';
            $response['message'] = 'OTP sent to your email.';
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = "Error: {$mail->ErrorInfo}";
        }
        echo json_encode($response);
        exit;
    }
}



?>