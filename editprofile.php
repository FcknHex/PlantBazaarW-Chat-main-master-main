<?php
include 'conn.php';
session_start();

// Start output buffering
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

// Retrieve the user's data from the database
$email = $_SESSION['email'];
$query = "SELECT id, firstname, lastname, phonenumber, region, city, proflePicture FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $userId = $user['id'];
    $profilePicture = $user['proflePicture'];
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $phonenumber = $user['phonenumber'];
    $region = $user['region'];
    $city = $user['city'];
} else {
    echo 'Error retrieving user data';
    exit;
}

$updated = false;

// Handle form submission
if (isset($_POST['submit'])) {
    $newFirstname = $_POST['firstname'];
    $newLastname = $_POST['lastname'];
    $newPhoneNumber = $_POST['phonenumber'];
    $newRegion = $_POST['region'];
    $newCity = $_POST['city'];

    // Check if any data has changed
    if ($newFirstname != $firstname || $newLastname != $lastname || $newPhoneNumber != $phonenumber || $newRegion != $region || $newCity != $city) {
        // Update the user's data in the database
        $query = "UPDATE users SET firstname = ?, lastname = ?, phonenumber = ?, region = ?, city = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $newFirstname, $newLastname, $newPhoneNumber, $newRegion, $newCity, $userId);

        if ($stmt->execute()) {
            // Set updated flag to true
            $updated = true;
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

ob_end_flush();
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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="profile-picture">Profile Picture:</label>
        <div class="profile-picture-upload">
        <img src="ProfilePictures/<?php echo $profilePicture;?>" alt="Profile Picture" id="preview-image">
        <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
        <button type="button" id="change-profile-pic">Change Profile Picture</button>
        </div>
        <label for="firstname">Firstname:</label>
        <input type="text" id="firstname" name="firstname" pattern="[a-zA-Z\s]+" value="<?php echo $firstname; ?>"><br><br>
        <label for="lastname">Lastname:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>"><br><br>
        <label for="phonenumber">Phone Number:</label>
        <input type="tel" id="phonenumber" name="phonenumber" maxlength="10" pattern="[9][0-9]{9}" title="Please enter a valid phone number" value="<?php echo $phonenumber; ?>">
        <p class="note">Format: 9XXXXXXXX</p>
        <br><br>
        <label for="address">Address:</label>
        <!-- Address Section -->
        <label for="region">Region:</label>
        <select id="region" name="region">
            <option value="" disabled selected>Select Region</option>
        </select><br><br>

        <label for="city">City/Municipality:</label>
        <select id="city" name="city">
            <option value="" disabled selected>Select City/Municipality</option>
        </select><br><br>

        <input type="submit" name="submit" id="submit" value="Update Profile" disabled>
    </form>
    </div>
    </div>
    <!-- Check if the profile was updated, then trigger SweetAlert -->
    <?php if ($updated): ?>
        <script>
            Swal.fire({
                title: "Profile Updated!",
                text: "Your profile has been updated successfully",
                icon: "success",
                button: "Ok",
                timer: 3000
            }).then(function() {
                window.location.href = "index.php";
            });
        </script>
    <?php endif; ?>
</body>
<script src="ph-address.js"></script>
<script>
    const profilePictureInput = document.getElementById('profile-picture');
    const previewImage = document.getElementById('preview-image');
    const regionSelect = document.getElementById("region");
    const citySelect = document.getElementById("city");

    profilePictureInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
         // Change profile picture button functionality
    document.getElementById('change-profile-pic').addEventListener('click', function() {
        document.getElementById('profile-picture').click();
    });

       // Limit phone number to 11 digits
        document.getElementById('phonenumber').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
         // Ensure the first digit is not zero
         if (e.target.value.startsWith('0')) {
            e.target.value = e.target.value.slice(1);
        }
    });

    const userRegion = "<?php echo $region; ?>";
    const userCity = "<?php echo $city; ?>";

    // Populate regions
    Object.keys(philippinesData).forEach(region => {
        const option = document.createElement("option");
        option.value = region;
        option.textContent = region;
        if (region === userRegion) {
            option.selected = true;
        }
        regionSelect.appendChild(option);
    });

    // // Populate cities based on selected region
    // regionSelect.addEventListener("change", function() {
    //     const selectedRegion = this.value;
    //     const cities = philippinesData[selectedRegion] || [];

    //     // Clear existing cities
    //     citySelect.innerHTML = '<option value="" disabled selected>Select City/Municipality</option>';

    //     // Populate city dropdown
    //     cities.forEach(city => {
    //         const option = document.createElement("option");
    //         option.value = city;
    //         option.textContent = city;
    //         citySelect.appendChild(option);
    //     });
    // });

    // Populate cities based on selected region
    function loadCities(selectedRegion) {
        const cities = philippinesData[selectedRegion] || [];
        citySelect.innerHTML = '<option value="" disabled>Select City/Municipality</option>';

        cities.forEach(city => {
            const option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            if (city === userCity) {
                option.selected = true;
            }
            citySelect.appendChild(option);
        });
    }

    // Load cities if region is already selected
    if (userRegion) {
        loadCities(userRegion);
    }

    regionSelect.addEventListener("change", function() {
        loadCities(this.value);
    });
   
//    enable update profile button when any input value changes
const inputs = document.querySelectorAll("input");
const button = document.getElementById("submit");

inputs.forEach(input => {
    input.addEventListener("input", function() {
        const isAnyInputFilled = Array.from(inputs).some(input => input.value.trim() !== '');
        button.disabled =!isAnyInputFilled;
    });
})
</script>
</html>
