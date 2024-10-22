<?php 
session_start();
include "../conn.php"; // Include your database connection

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$reported_user = $_GET['user_id'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User</title>
    <link rel="stylesheet" href="report.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Report User</h1>
        <form action="report.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="reported_user_id" value="<?php echo $reported_user;?>">
            <label for="report_reason">Reason for Report:</label>
            <select name="report_reason" id="report_reason" required>
                <option value="Prohibited item">Prohibited item</option>
                <option value="Scam">Scam</option>
                <option value="Counterfeit">Counterfeit</option>
                <option value="Offensive">Offensive messages/images</option>
                <option value="Other">Other</option>
            </select>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description" placeholder="Further elaborate on your selected reason"></textarea>
            
            <label for="proof_img_1">Proof Image 1:</label>
            <input type="file" name="proof_img_1" accept="image/*">
            <label for="proof_img_2">Proof Image 2:</label>
            <input type="file" name="proof_img_2" accept="image/*">
            <label for="proof_img_3">Proof Image 3:</label>
            <input type="file" name="proof_img_3" accept="image/*">
            <label for="proof_img_4">Proof Image 4:</label>
            <input type="file" name="proof_img_4" accept="image/*">
            <label for="proof_img_5">Proof Image 5:</label>
            <input type="file" name="proof_img_5" accept="image/*">
            <label for="proof_img_6">Proof Image 6:</label>
            <input type="file" name="proof_img_6" accept="image/*">
            
            <button type="submit">Submit Report</button>
        </form>
        <a href="index.php" class="back-btn">Back to Messages</a>
    </div>
</body>
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    var formData = new FormData(this); // Create form data object from the form

    // Send the AJAX request
    fetch('ajax/report_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show a success message
            Swal.fire({
                icon: 'success',
                title: 'Report Submitted',
                text: data.message,
                showConfirmButton: true
            })
            .then((result) => {
                if(result.isConfirmed){
                    // Redirect or refresh after a few seconds if needed
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    })
                }
            });

            // Hide the form
            document.querySelector('form').style.display = 'none';
        } else {
            // Show an error message
            Swal.fire({
                icon: 'error',
                title: data.title,
                text: data.message,
                showConfirmButton: true,
                confirmButtonText: 'Close'
            }).then((result) => {
                if(result.isConfirmed){
                    // Redirect or refresh after a few seconds if needed
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    })
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Submission Failed',
            text: 'There was an error submitting your report. Please try again later.',
            showConfirmButton: true
        }).then((result) => {
                if(result.isConfirmed){
                    // Redirect or refresh after a few seconds if needed
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    })
                }
        });
    });
});

</script>
</html>
