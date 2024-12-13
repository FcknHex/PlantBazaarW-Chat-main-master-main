<?php
include 'conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}
?>
  <?php
    include 'nav.php';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="editprofile.css">
    <!-- SweetAlert Library -->
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
    <div class="sidebar">
        <nav>
            <ul>
                <li><a href="editprofile.php">Edit Profile</a></li>
                <li><a href="changepassword.php">Change Password</a></li>
            </ul>
        </nav>
    </div>
    <div class="form-container">
    <form action="" method="post" id="changePasswordForm">
        <label for="password">Enter your current password:</label>
        <input type="password" id="password" name="password" required>
        <p id="error-current"></p>

        <label for="newPassword">Enter your new password: </label>
        <input type="password" id="newPassword" name="newPassword" required>
        <p class="note1">Password must be at least 8 characters long and include at least one number and one special character.</p>
        <p id="error-new"></p>

        <label for="confirmPassword">Confirm your new password: </label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <p id="error-confirm"></p>

        <input type="submit" name="submit" id="submit" value="Change Password">
    </form>
    </div>
    </div>
</body>
<script>

let attempts = 0; // Track the number of failed attempts
const maxAttempts = 3; // Maximum allowed attempts
const baseCooldownTime = 30; // Cooldown time in seconds

// Check for stored attempts and timestamp
if (localStorage.getItem('failedAttempts')) {
    attempts = parseInt(localStorage.getItem('failedAttempts'), 10);
}
if (localStorage.getItem('cooldownEndTime')) {
    const cooldownEndTime = parseInt(localStorage.getItem('cooldownEndTime'), 10);
    const currentTime = Math.floor(Date.now() / 1000); // Current time in seconds

    // If cooldown has not ended, disable the form inputs
    if (currentTime < cooldownEndTime) {
        disableFormInputs(cooldownEndTime - currentTime);
    } else {
        // Reset attempts if cooldown has ended
        localStorage.removeItem('failedAttempts');
        localStorage.removeItem('cooldownEndTime');
    }
}

   $('#changePasswordForm').on('submit', function(e) {
       e.preventDefault(); // Prevent default form submission

       // Get form data
       var currentPassword = $('#password').val();
       var newPassword = $('#newPassword').val();
       var confirmPassword = $('#confirmPassword').val();

       // Validate the current password
       if (currentPassword === '') {
           $('#error-current').text('Please enter your current password.');
           return;
       }
       // Send AJAX request
       $.ajax({
           url: 'Ajax/change_password.php',
           type: 'POST',
           data: { currentPassword: currentPassword, newPassword: newPassword,confirmPassword: confirmPassword },
           dataType: 'json',
           success: function(response) {
               console.log(response);
               // Handle success response
                if (response.status === 'success') {
                attempts = 0;
                localStorage.removeItem('failedAttempts');
                Swal.fire({
                    title: "Password Changed",
                    text: "Your password has been changed successfully.",
                    icon: "success",
                    button: "Ok",
                    timer: 3000
                }).then(function() {
                    window.location.href="editprofile";
                });
               }else if (response.status === 'failed-current') {
                   // Password change failed, display error message
                   attempts ++;
                   localStorage.setItem('failedAttempts', attempts);
                   handleFailedAttempt(response);

                   $('#error-current').text(response.message);
                   $('#password').addClass('error');
               }else if (response.status === 'failed-new') {
                   // Password change failed, display error message
                   $('#error-new').text(response.message);
                   $('#newPassword').addClass('error');
               }else if (response.status === 'failed-confirm') {
                   // Password change failed, display error message
                   $('#error-confirm').text(response.message);
                   $('#confirmPassword').addClass('error');
               } else {
                   // Password change failed, display error message
                   $('#error-current').text(response.message);
               }
           },error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:", textStatus, errorThrown); // Log any AJAX errors
            console.log(jqXHR.responseText); // Log the full response for debugging
        }
       });
   });

   function handleFailedAttempt(response) {
    attempts++; // Increment failed attempts
    const cooldownTime = baseCooldownTime * attempts;
    if (attempts >= maxAttempts) {
        const currentTime = Math.floor(Date.now() / 1000); // Current time in seconds
        const cooldownEndTime = currentTime + cooldownTime; // Calculate end time
        localStorage.setItem('cooldownEndTime', cooldownEndTime); // Store end time
        disableFormInputs(cooldownTime); // Disable inputs and start cooldown
    } else {
        if (response.status === 'failed-current') {
            $('#error-current').text(response.message);
            $('#password').addClass('error');
        } else if (response.status === 'failed-new') {
            $('#error-new').text(response.message);
            $('#newPassword').addClass('error');
        } else if (response.status === 'failed-confirm') {
            $('#error-confirm').text(response.message);
            $('#confirmPassword').addClass('error');
        } else {
            $('#error-current').text(response.message);
        }
    }
}

function disableFormInputs(timeLeft) {
    $('#changePasswordForm input').prop('disabled', true); // Disable inputs
    $('#submit').prop('disabled', true);
    $('#error-current').text(`Too many failed attempts. Please wait ${timeLeft} seconds before trying again.`);
    startCooldown(timeLeft);
}

function startCooldown(timeLeft) {
    const timer = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timer);
            $('#changePasswordForm input').prop('disabled', false); // Re-enable inputs
            $('#submit').prop('disabled', false);
            $('#error-current').text(''); // Clear error message
            localStorage.removeItem('cooldownEndTime'); // Clear cooldown end time
        } else {
            $('#error-current').text(`Too many failed attempts. Please wait ${timeLeft--} seconds.`);
        }
    }, 1000);
}

   document.getElementById('password').addEventListener('input', function() {
       $('#error-current').text('');
       $('#password').removeClass('error');
   });

   document.getElementById('newPassword').addEventListener('input', function() {
       $('#error-new').text('');
       $('#newPassword').removeClass('error');
   });

   document.getElementById('confirmPassword').addEventListener('input', function() {
       $('#error-confirm').text('');
       $('#confirmPassword').removeClass('error');
   });
</script>
</html>
