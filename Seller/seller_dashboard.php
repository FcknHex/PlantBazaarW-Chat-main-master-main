<?php
include '../conn.php';
session_start();

// Check if a user is logged in
$isLoggedIn = isset($_SESSION['email']) && !empty($_SESSION['email']);

$profilePic = ''; // Placeholder for the profile picture
$isSeller = false; // Flag to check if the user is a seller

if ($isLoggedIn) {
    $email = $_SESSION['email'];

    // Query to get the profile picture from the database
    $query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
        $userId = $user['id'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
    }


    // Query to check if the user is a seller
    $sellerQuery = "SELECT seller_id FROM sellers WHERE user_id = '$userId'";
    $sellerResult = mysqli_query($conn, $sellerQuery);

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $isSeller = true; // User is a seller
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="seller_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src= "notif.js"></script>
    <title>Seller Dashboard</title>
</head>
<body>
<?php
include 'nav.php';
?>
<!-- Start of Main Content  -->
<div class="main-content">
    <h1>Welcome to Your Seller Dashboard</h1>
    <button id="viewSoldHistoryButton">View Sold Listings History</button>
    <div class="product-list">
        <h2>Your Listed Plants</h2>
        <div class="card-container">
            <!-- Products will be dynamically inserted here -->
        </div>
    </div>
    
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Plant</h2>
            <form id="addProductForm"  enctype="multipart/form-data">
                <label for="plantname">Plant Name:</label>
                <input type="text" id="plantname" name="plantname" required>

                <label for="plantsize">Plant Size:</label>
                <br>
                <select name="plantsize" id="plantsize" required>
                    <option value="" disabled selected>Select Size</option>
                    <option value="Seedling">Seedling</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Adult">Adult</option>
                </select>
                <br>
            
                <label for="plantdetails">Description (optional) :</label>
                <textarea name="plantdetails" id="plantdetails" cols="30" rows="10"></textarea>
                <br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required min="0" step="0.01">

                <label for="plantcategories">Category:<span class="required"></label>
                <br>
                <select name="plantcategories" id="plantcategories" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Outdoor">Outdoor Plant</option>
                    <option value="Indoor">Indoor Plants</option>
                    <option value="Flowers">Flowers</option>
                    <option value="Leaves">Leaves</option>
                    <option value="Bushes">Bushes</option>
                    <option value="Trees">Trees</option>
                    <option value="Climbers">Climbers Plant</option>
                    <option value="Grasses">Grasses</option>
                    <option value="Succulent">Succulent Plant</option>
                    <option value="Cacti">Cacti Plant</option>
                    <option value="Aquatic">Aquatic Plant</option>
                </select>
                <br>

                <label for="Location">Location:</label>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Region <span class="text-danger">*</span></label>
                <select name="region" class="region" id="region"></select>
                <input type="hidden" class="region-text" name="region" id="region-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Province *</label>
                <select name="province" class="province" id="province"></select>
                <input type="hidden" class="province-text" name="province" id="province-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">City / Municipality *</label>
                <select name="city" class="city" id="city"></select>
                <input type="hidden" class="city-text" name="city" id="city-text" required>
                </div>
                <div class="col-sm-6 mb-3">
                <label class="form-label">Barangay *</label>
                <select name="barangay" class="barangay" id="barangay"></select>
                <input type="hidden" class="barangay-text" name="barangay" id="barangay-text" required>
                </div>
                <div class="col-md-6 mb-3">
                <label for="street-text" class="form-label">Street (Optional)</label>
                <input type="text" class="street-text" name="street" id="street-text">
                </div>

                <div class="image-upload-container">
                <div class="image-upload-column">
                    <label for="img1">1st Image:</label>
                    <input type="file" id="img1" name="img1" accept="image/*" required>
                    <img id="img1Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
                <div class="image-upload-column">
                    <label for="img2">2nd Image:</label>
                    <input type="file" id="img2" name="img2" accept="image/*">
                    <img id="img2Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
                <div class="image-upload-column">
                    <label for="img3">3rd Image:</label>
                    <input type="file" id="img3" name="img3" accept="image/*">
                    <img id="img3Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
                </div>
            </div>


                <button type="submit">Add Product</button>
            </form>
            <div id="message"></div>
        </div>
    </div>
    <!-- <a href="add_product.php" id="openModalLink" class="add-product-btn">+ Add New Plant</a> -->
    <button type="button" id="openModalBtn1">Add New Plant</button>
        <!-- End of Main Content -->
         
    <!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Plant</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editPlantId" name="plantid"> <!-- Hidden field to hold plant ID -->
            <label for="editplantname">Plant Name:</label>
            <input type="text" id="editplantname" name="editplantname" value="<?php echo $row['plantname']; ?>" required>

            <label for="editPlantSize">Plant Size:</label>
                <br>
                <select name="editPlantSize" id="editPlantSize" required>
                    <option value="" disabled selected>Select Size</option>
                    <option value="Seedling">Seedling</option>
                    <option value="Juvenile">Juvenile</option>
                    <option value="Adult">Adult</option>
                </select>
                <br>

                <label for="editplantdetails">Description (optional) :</label>
                <textarea name="editplantdetails" id="editplantdetails" cols="30" rows="10"></textarea>
                <br>

            <label for="editPlantCategories">Category:<span class="required"></label>
                <br>
                <select name="editPlantcategories" id="editPlantcategories" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Outdoor">Outdoor Plant</option>
                    <option value="Indoor">Indoor Plants</option>
                    <option value="Flowers">Flowers</option>
                    <option value="Leaves">Leaves</option>
                    <option value="Bushes">Bushes</option>
                    <option value="Trees">Trees</option>
                    <option value="Climbers">Climbers Plant</option>
                    <option value="Grasses">Grasses</option>
                    <option value="Succulent">Succulent Plant</option>
                    <option value="Cacti">Cacti Plant</option>
                    <option value="Aquatic">Aquatic Plant</option>
                </select>
                <br>

            <label for="editLocation">Location:</label>
            <div class="col-sm-6 mb-3">
        <label class="form-label">Region <span style="color:red;">*</span></label>
        <select name="editregion" class="region" id="region1"></select>
        <input type="text" class="editregion" name="editregion" id="region-text1" required disabled>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">Province *</label>
            <select name="editprovince" class="province" id="province1"></select>
            <input type="text" class="editprovince" name="editprovince" id="province-text1" required disabled>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">City / Municipality *</label>
            <select name="editcity"  class="city" id="city1"></select>
            <input type="text" class="editcity" name="editcity" id="city-text1" required disabled>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label">Barangay *</label>
            <select name="editbarangay" class="barangay" id="barangay1"></select>
            <input type="text" class="editbarangay" name="editbarangay" id="barangay-text1" required disabled>
        </div>
        <div class="col-md-6 mb-3">
            <label for="street-text" class="form-label">Street (Optional)</label>
            <input type="text" class="editstreet" name="editstreet" id="street-text1">
        </div>

            <label for="editPrice">Price:</label>
            <input type="number" id="editPrice" name="editPrice" required min="0" step="0.01" required>
            
            <div class="image-upload-container">

            <div class="image-upload-column">
            <label for="editImg1">1st Image:</label>
            <input type="file" id="editImg1" name="img1" accept="image/*">
            <img id="editImg1Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg2">2nd Image:</label>
            <input type="file" id="editImg2" name="img2" accept="image/*">
            <img id="editImg2Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg3">3rd Image:</label>
            <input type="file" id="editImg3" name="img3" accept="image/*">
            <img id="editImg3Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            </div>
            <button type="submit">Update Product</button>
        </form>
        <div id="editMessage"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete this listing?</p>
        <button id="confirmDeleteButton">Yes, Delete</button>
        <button id="cancelDeleteButton">Cancel</button>
    </div>
</div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="ph-address-selector.js"></script>
    <script>

  // Add event listeners to image inputs to preview images
document.getElementById('editImg1').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg1Preview').src = event.target.result;
      document.getElementById('editImg1Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg2').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg2Preview').src = event.target.result;
      document.getElementById('editImg2Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg3').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg3Preview').src = event.target.result;
      document.getElementById('editImg3Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('img1').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img1Preview').src = event.target.result;
      document.getElementById('img1Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image1_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img1').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});

document.getElementById('img2').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img2Preview').src = event.target.result;
      document.getElementById('img2Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image2_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img2').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});

document.getElementById('img3').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('img3Preview').src = event.target.result;
      document.getElementById('img3Label').innerHTML = file.name; // Display the file name next to the input field

      // Rename the image
      const newFileName = 'image3_' + Date.now() + '.' + file.name.split('.').pop();
      document.getElementById('img3').files[0].name = newFileName;
    };
    reader.readAsDataURL(file);
});
   

$(document).ready(function() {

let currentpage = 1;
const productsPerPage = 4 ;
let currentView = 'available'; // Track current view (available or sold)


// Handle "View Sold Listings History" button click
$('#viewSoldHistoryButton').on('click', function() {
    if (currentView === 'available') {
        currentView = 'sold-history';
        $(this).text('View Available Listings'); // Change button text to switch back to available listings
        fetchProducts(1, 'sold-history'); // Fetch sold products
    } else {
        currentView = 'available';
        $(this).text('View Sold Listings History'); // Change button text back to sold listings
        fetchProducts(1, 'available'); // Fetch available products
    }
    
     // Clear or reset pagination whenever switching views
     $('.pagination').empty();
});


function fetchProducts(page = 1, viewType='available') {
    
    // 0 is temporarily set for available listings, will be changed to 1 when we fixed the adding of plants that sets the listing to 1 as default

    let status = viewType === 'sold-history' ? 2 : 1; // 0 for available, 2 for sold history
    
    $.ajax({
        url: 'fetch_listed_plants.php',
        type: 'GET',
        dataType: 'json', // Specify the expected data type as JSON
        data: {page: page,
            listing_status: status
        },
        success: function(data) {
            const productContainer = $('.card-container');
            productContainer.empty();

            if (data.products.length === 0) {
                productContainer.append(`<p>No products found for ${viewType === 'sold-history' ? 'sold' : 'available'} listings.</p>`);
                $('.pagination').empty();
                return;
            }

            data.products.forEach(function(product) {
            const card = $('<div>').addClass('card');
            const imgSrc = '../Products/' + product.seller_email + '/' + product.img1;
                if (product.img1 === '') {
                    if (product.img2 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img2;
                    } else if (product.img3 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img3;
                    } else {
                    imgSrc = '../plant-bazaar.jpg' // Display a default image if all images are empty
                    }
                }
            card.append($('<img>').attr('src', imgSrc).attr('alt', product.plantname));
            card.append($('<h1>').addClass('card-title').text(product.plantname));
            card.append($('<p>').text('Price: ₱' + product.price));
            card.append($('<p>').text('Category: ' + product.plantcategories));
            


             if (viewType === 'available') {
            // Create a container for the buttons
            const buttonContainer = $('<div>').addClass('button-container');
             // For available listings, add edit and delete buttons
            // Create the Edit button
            const editButton = $('<button>')
                .addClass('edit-button') // Add a class for CSS styling and event handling
                .data('plantid', product.plantid) // Store the plant ID in a data attribute
                .text('Edit Listing') // Set the button text
                .css({
                    backgroundColor: '#4CAF50', // Green background
                    color: 'white', // White text
                    border: 'none', // No border
                    padding: '10px 15px', // Padding for the button
                    textAlign: 'center', // Center text
                    fontSize: '16px', // Font size
                    margin: '4px 2px', // Margin around the button
                    cursor: 'pointer', // Pointer cursor on hover
                    borderRadius: '5px' // Rounded corners
                });
            // Create the Delete button
            const deleteButton = $('<button>')
                .addClass('delete-button') // Add a class for CSS styling
                .data('plantid', product.plantid) // Store the plant ID in a data attribute
                .text('Delete Listing') // Set the button text
                 .css({
                            backgroundColor: '#f44336', // Red background
                            color: 'white', // White text
                            border: 'none', // No border
                            padding: '10px 15px', // Padding for the button
                            textAlign: 'center', // Center text
                            fontSize: '16px', // Font size
                            margin: '4px 2px', // Margin around the button
                            cursor: 'pointer', // Pointer cursor on hover
                            borderRadius: '5px' // Rounded corners
                        });
            const markAsSoldButton = $('<button>')
                .addClass('mark-sold-button') // Add a class for CSS styling
                .data('plantid', product.plantid) // Store the plant ID in a data attribute
                .text('Mark as Sold') // Set the button text
                .css({
                            backgroundColor: 'black', // Red background
                            color: '#f8f8f8', // White text
                            border: '1px solid #ccc', // No border
                            padding: '10px 15px', // Padding for the button
                            textAlign: 'center', // Center text
                            fontSize: '16px', // Font size
                            margin: '4px 2px', // Margin around the button
                            cursor: 'pointer', // Pointer cursor on hover
                            borderRadius: '5px' // Rounded corners
                        });

                    // Append buttons to the button container
                    buttonContainer.append(editButton);
                    buttonContainer.append(deleteButton);
                    buttonContainer.append(markAsSoldButton);
                    card.append(buttonContainer);
                    } else {
                    }
            // Append the buttonContainer to your product card
            // Example: $('#productCard').append(buttonContainer);

                productContainer.append(card);
            });

                        setupPagination(data.total, viewType, page);
                    },
                    error: function(xhr, status, error) {
                        console.error("Request failed:", error);
                    }
                });
    }

    function setupPagination(totalProducts, viewType, currentPage) {
        const paginationContainer = $('.pagination');
        paginationContainer.empty(); // Clear existing pagination

        const totalPages = Math.ceil(totalProducts / productsPerPage);

        if (totalPages <= 1) {
        return;
        }
        // Create pagination buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = $('<button>')
                .text(i)
                .attr('data-page', i)
                .addClass(i === currentpage ? 'active' : '');

            pageButton.on('click', function() {
                const page = $(this).data('page');
                fetchProducts(page, viewType);
                
            });

            paginationContainer.append(pageButton);
        }
    }

    fetchProducts(1, 'available');

    $('.main-content').append('<div class="pagination"></div>');


    function loadAddProductForm() {
            $.ajax({
                url: 'add_product.php', // Load the add product form
                type: 'GET',
                success: function(data) {
                    $('.main-content').html(data); // Replace content with the add product form
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                }
            });
        }
});
$(document).ready(function() {
    // Get the modal
    var modal = $('#addProductModal');
    
    // Get the button that opens the modal
    var btn = $('#openModalBtn1');
    
    // Get the <span> element that closes the modal
    var span = $('.close');
    
    // When the user clicks the button, open the modal 
    btn.on('click', function(e) {
        e.preventDefault(); // Prevent default button behavior (stop navigating)
        modal.show(); // Show the modal
    });
    
    // When the user clicks on <span> (x), close the modal
    span.on('click', function() {
        modal.hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    // Submit form via AJAX
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object from the form

        $.ajax({
            url: 'add_product.php', // URL to the PHP script that will handle the form submission
            type: 'POST', // POST request
            data: formData,
            contentType: false, // Tell jQuery not to set contentType
            processData: false, // Tell jQuery not to process the data
            success: function(response) {
                console.log(response);
                $('#message').html(response); // Display the response message
                $('#addProductForm')[0].reset(); // Reset the form
                modal.hide(); // Hide the modal after successful submission
                setTimeout(function() {
                    location.reload();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
                $('#message').html('<p style="color: red;">An error occurred while adding the product.</p>');
            }
        });
    });
});

$(document).ready(function() {
        // Get the modal
        var modal = $('#addProductModal');
        
        // Get the button that opens the modal
        var btn = $('#openModalBtn1');
        
        // Get the <span> element that closes the modal
        var span = $('.close');
        
        // When the user clicks the button, open the modal 
        btn.on('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            modal.show();
        });
        
        // When the user clicks on <span> (x), close the modal
        span.on('click', function() {
            modal.hide();
        });

        // When the user clicks anywhere outside of the modal, close it
        $(window).on('click', function(event) {
            if ($(event.target).is(modal)) {
                modal.hide();
            }
        });
    });

    $(document).on('click', '.edit-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    // Fetch the existing data for this plant and populate the modal fields
    console.log('plantId:', plantId);
    $.ajax({
  url: 'fetch_listed_products.php', // URL to fetch product details
  type: 'GET',
  dataType: 'json',
  data: { plantid: plantId }, // Pass the plant ID
  success: function(data) {
    console.log(data);
    // Populate the modal fields with fetched data
    $('#editPlantId').prop('value', data.plantid);
    $('#editplantname').prop('value', data.plantname);
    $('#editPrice').prop('value', data.price);
    $('#region-text1').prop('value', data.region);
    $('#province-text1').prop('value', data.province);
    $('#city-text1').prop('value', data.city);
    $('#barangay-text1').prop('value', data.barangay);
    $('.editstreet').prop('value', data.street);



    const regionName = data.region;
    const provinceName = data.province;
    const cityName = data.city;
    const barangayName = data.barangay;

    $('#editplantdetails').prop('value', data.details);
    $('#editPlantcategories').prop('value', data.plantcategories);
    $('#editPlantSize').prop('value', data.plantSize);
    $('#editPlantColor').prop('value', data.plantColor);
    $('#editLocation').prop('value', data.location);
    $('#editImg1Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img1).attr('alt', data.plantname).text(data.img1);
    $('#editImg2Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img2).attr('alt', data.plantname).text(data.img2);
    $('#editImg3Preview').attr('src', '../Products/' + '<?php echo $_SESSION['email'] ?>' + '/' + data.img3).attr('alt', data.plantname).text(data.img3);
    // $('#editImg1Label').text(data.img1);
    // $('#editImg2Label').text(data.img2);
    // $('#editImg3Label').text(data.img3);


    // Open the edit modal
    $('#editProductModal').show();

    loadRegionDropdown(regionName, provinceName, cityName, barangayName);
  },
  error: function() {
    alert('Error fetching product data.');
  }
});
});

function loadRegionDropdown(regionName, selectedProvince, selectedCity, selectedBarangay) {
    $.getJSON('ph-json/region.json', function(regionData) {
        const regionDropdown = $('#region1');
        regionDropdown.empty();
        regionDropdown.append('<option value="" disabled>Select Region</option>');

        $.each(regionData, function(index, entry) {
            const isSelected = entry.region_name === regionName ? 'selected' : '';
            regionDropdown.append(`<option value="${entry.region_name}" ${isSelected}>${entry.region_name}</option>`);
        });

        // Update input text based on selected region
        $('#region-text1').val(regionName);

        // Trigger the province dropdown load after region is set
        loadProvinceDropdown(regionName, selectedProvince, selectedCity, selectedBarangay);
    });

    // Update the region input text when dropdown changes
    $('#region1').on('change', function() {
        const selectedRegion = $(this).find('option:selected').text();
        $('#region-text1').val(selectedRegion);
        $('#province-text1').val("");
        $('#city-text1').val("");
        $('#barangay-text1').val("");

        $('#barangay1').val("");
        $('#province1').val("");
        $('#city1').val("");

        loadProvinceDropdown(selectedRegion, '', '', ''); // Reload the provinces based on the new region
    });
}

function loadProvinceDropdown(regionName, selectedProvince, selectedCity, selectedBarangay) {
    $.getJSON('ph-json/region.json', function(regionData) {
        const selectedRegion = regionData.find(entry => entry.region_name === regionName);

        if (!selectedRegion) {
            console.warn('No matching region found for:', regionName);
            return;
        }
    // Update the province input text based on selected value
    $('#province-text1').val(selectedProvince);
        const regionCode = selectedRegion.region_code;

        $.getJSON('ph-json/province.json', function(provinceData) {
            const provinceDropdown = $('#province1');
            provinceDropdown.empty();
            provinceDropdown.append('<option value="" disabled>Select Province</option>');

            // Filter provinces by region code
            const filteredProvinces = provinceData.filter(province => province.region_code === regionCode);

            $.each(filteredProvinces, function(index, province) {
                const isSelected = province.province_name === selectedProvince ? 'selected' : '';
                provinceDropdown.append(`<option value="${province.province_name}" ${isSelected}>${province.province_name}</option>`);
            });

            // Update the province input text based on selected value
            $('#province-text1').val(selectedProvince);

            // Load the cities based on the selected province
            loadCityDropdown(selectedProvince, selectedCity, selectedBarangay);
        });

        // Update the province input text when dropdown changes
        $('#province1').on('change', function() {
            const selectedProvince = $(this).find('option:selected').text();
            $('#province-text1').val(selectedProvince);
            loadCityDropdown(selectedProvince, '', ''); // Reload the cities based on the new province
        });
    });
}

function loadCityDropdown(provinceName, selectedCity, selectedBarangay) {
    $.getJSON('ph-json/province.json', function(provinceData) {
        // Find the selected province's code
        const selectedProvince = provinceData.find(province => province.province_name === provinceName);
        if (!selectedProvince) {
            console.warn('No matching province found for:', provinceName);
            return;
        }
        const provinceCode = selectedProvince.province_code;

        // Fetch city data and filter by the province code
        $.getJSON('ph-json/city.json', function(cityData) {
            const cityDropdown = $('#city1');
            cityDropdown.empty();
            cityDropdown.append('<option value="" disabled>Select City</option>');

            const filteredCities = cityData.filter(city => city.province_code === provinceCode);
            if (filteredCities.length === 0) {
                console.warn('No cities found for province:', provinceName);
            }

            // Populate city dropdown
            $.each(filteredCities, function(index, city) {
                const isSelected = city.city_name === selectedCity ? 'selected' : '';
                cityDropdown.append(`<option value="${city.city_name}" ${isSelected}>${city.city_name}</option>`);
            });

            // Set the city text input
            $('#city-text1').val(selectedCity);

            // Load barangays based on selected city
            loadBarangayDropdown(selectedCity, selectedBarangay);
        });

        // Update city text when dropdown changes
        $('#city1').on('change', function() {
            const selectedCity = $(this).find('option:selected').text();
            $('#city-text1').val(selectedCity);
            loadBarangayDropdown(selectedCity, '');  // Reload barangays
        });
    });
}

function loadBarangayDropdown(cityName, selectedBarangay) {
    $.getJSON('ph-json/city.json', function(cityData) {
        // Find the selected city's code
        const selectedCity = cityData.find(city => city.city_name === cityName);
        if (!selectedCity) {
            console.warn('No matching city found for:', cityName);
            return;
        }
        const cityCode = selectedCity.city_code;

        // Fetch barangay data and filter by city code
        $.getJSON('ph-json/barangay.json', function(barangayData) {
            const barangayDropdown = $('#barangay1');
            barangayDropdown.empty();
            barangayDropdown.append('<option value="" disabled>Select Barangay</option>');

            const filteredBarangays = barangayData.filter(barangay => barangay.city_code === cityCode);
            if (filteredBarangays.length === 0) {
                console.warn('No barangays found for city:', cityName);
            }

            // Populate barangay dropdown
            $.each(filteredBarangays, function(index, barangay) {
                const isSelected = barangay.brgy_name === selectedBarangay ? 'selected' : '';
                barangayDropdown.append(`<option value="${barangay.brgy_name}" ${isSelected}>${barangay.brgy_name}</option>`);
            });

            // Set the barangay text input
            $('#barangay-text1').val(selectedBarangay);
        });

        // Update barangay text when dropdown changes
        $('#barangay1').on('change', function() {
            const selectedBarangay = $(this).find('option:selected').text();
            $('#barangay-text1').val(selectedBarangay);
        });
    });
}


// Handle the delete button click
$(document).on('click', '.delete-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    $('#deleteConfirmationModal').show(); // Open the delete confirmation modal
    // Set up the confirm button in the modal
    $('#confirmDeleteButton').off('click').on('click', function() {
        // Redirect to delete script
        window.location.href = 'delete_product.php?plantid=' + plantId;
    });$('#cancelDeleteButton').off('click').on('click', function() {
        // Close the modal
        $('#deleteConfirmationModal').hide();
    });
});

// Close modals when clicking on the close button
$('.close').on('click', function() {
    $('#editProductModal').hide();
    $('#deleteConfirmationModal').hide();
});

// Close the modals when clicking outside of them
$(window).on('click', function(event) {
    if ($(event.target).is('#editProductModal')) {
        $('#editProductModal').hide();
    } else if ($(event.target).is('#deleteConfirmationModal')) {
        $('#deleteConfirmationModal').hide();
    }
});

// Handle the form submission for updating the product
$('#editProductForm').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    // Get dropdown values
    var selectedRegion = $('#region1').val();
    var selectedProvince = $('#province1').val();
    var selectedCity = $('#city1').val();
    var selectedBarangay = $('#barangay1').val();

    // Get hidden text values
    var regionText = $('#region-text1').val();
    var provinceText = $('#province-text1').val();
    var cityText = $('#city-text1').val();
    var barangayText = $('#barangay-text1').val();

    // Compare dropdown values with hidden text fields
    if (selectedRegion !== regionText || selectedProvince !== provinceText || selectedCity !== cityText || selectedBarangay !== barangayText) {
        Swal.fire({
            icon: 'warning',
            title: 'Error',
            text: 'The dropdown values and the corresponding hidden text fields do not match.',
            confirmButtonText: 'OK'
        });
        return; // Stop form submission
    }
    var formData = new FormData(this); // Create FormData object from the form
Swal.fire({
    icon: 'warning',
    title: 'Are you sure you want to update this product?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    reverseButtons: true
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
        url: 'edit_product.php', // URL to the PHP script that will handle the form submission
        type: 'POST',
        data: formData,
        contentType: false, // Tell jQuery not to set contentType
        processData: false, // Tell jQuery not to process the data
        success: function(response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Product updated successfully',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                $('#editProductForm')[0].reset(); // Reset the form
                $('#editProductModal').hide(); // Hide the modal after successful submission
                location.reload();
            });
            
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status + " " + error);
        }
    });
    }
});
  
});

$(document).on('click', '.mark-sold-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
Swal.fire({
    icon: 'warning',
    title: 'Are you sure you want to mark this product as sold?',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    reverseButtons: true
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
        url: 'mark_as_sold.php', // Create this PHP file
        type: 'POST',
        data: { plantid: plantId },
        success: function(response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Product marked as sold',
                showConfirmButton: false,
                timer: 3000
            })
            setTimeout(function() {
                window.location.reload(); // Refresh the page or handle as needed
            }, 2000);
        },
        error: function() {
            alert('Error marking product as sold.');
        }
    });
    }
})
});
   



// Logout AJAX
$(document).on('click', '#logoutLink', function(event) {
        event.preventDefault();

        $.ajax({
            url: '../Ajax/logout.php', // Path to your logout.php file
            type: 'POST',
            success: function(response) {
                if (response.trim() === "success") {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Successfully Logged out',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    // Reload page after 3 seconds
                    setTimeout(function() {
                    window.location.href = '../index.php';
                    }, 3000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Logout Failed',
                        text: 'Please try again.',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + status + " - " + error);
                Swal.fire({
                icon: "error",
                title: "Error",
                text: "An unexpected error occurred. Please try again later."
            });
            }
        });
    });


    
    </script>
</body>
</html>