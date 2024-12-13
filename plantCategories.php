<?php
include 'conn.php';
session_start();

// Check if a user is logged in
$isLoggedIn = isset($_SESSION['email']) && !empty($_SESSION['email']);
$profilePic = ''; // Placeholder for the profile picture
$email = null;
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

    // If no profile picture is available, use a default image
    if (empty($profilePic)) {
        $profilePic = 'ProfilePictures/Default-Profile-Picture.png';  // Path to a default profile picture
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="plantcategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lcv5mUqAAAAABNZ9eLdrYxpn8OWSacrmhefh9I3"></script>
    <script src="notif.js"></script>
    <title>Document</title>
</head>
<body>
    <?php include 'nav.php'; ?>
<div class="container">
    <!-- Categories Container -->
    <button id="openCategoriesModal" class="categories-modal-btn">Filter Categories</button>

    <!-- Categories Container for Desktop -->
    <div class="categories-container">
        <!-- Plant Type -->
        <div class="plant-type">
            <h3>Plant Type</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-type-items">
                <label><input type="checkbox" class="category-checkbox" value="Outdoor"> Outdoor Plant</label>
                <label><input type="checkbox" class="category-checkbox" value="Indoor"> Indoor Plant</label>
                <label><input type="checkbox" class="category-checkbox" value="Flowers"> Flowers</label>
                <label><input type="checkbox" class="category-checkbox" value="Leaves"> Leaves</label>
                <label><input type="checkbox" class="category-checkbox" value="Bushes"> Bushes</label>
                <label><input type="checkbox" class="category-checkbox" value="Trees"> Trees</label>
                <label><input type="checkbox" class="category-checkbox" value="Climbers"> Climbers</label>
                <label><input type="checkbox" class="category-checkbox" value="Grasses"> Grasses</label>
                <label><input type="checkbox" class="category-checkbox" value="Succulent"> Succulent</label>
                <label><input type="checkbox" class="category-checkbox" value="Cacti"> Cacti</label>
                <label><input type="checkbox" class="category-checkbox" value="Aquatic"> Aquatic</label>
            </div>
        </div>

     <!-- Plant Size -->
        <div class="plant-size">
            <h3>Filter by Size</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-size-items">
                <label><input type="checkbox" class="size-checkbox" value="Seedling"> Seedlings</label>
                <label><input type="checkbox" class="size-checkbox" value="Juvenile"> Juvenile</label>
                <label><input type="checkbox" class="size-checkbox" value="Adult"> Adult</label>
            </div>
        </div>

     <!-- Plant Location -->
     <div class="plant-location">
            <h3>Filter by Location</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div id="locationCheckboxes"></div> <!-- Dynamic Location Checkboxes -->
        </div>
    </div>

    <!-- Modal for Categories (visible on mobile view only) -->
    <div id="categoriesModal" class="categories-modal">
        <div class="categories-modal-content">
            <button id="closeCategoriesModal" class="close-modal-btn">&times;</button>

            <!-- Copy of categories for the mobile view modal -->
            <div class="plant-type">
                <h3>Plant Type</h3>
                <button class="clear-all">Clear All</button>
                <div class="plant-type-items">
                    <label><input type="checkbox" class="category-checkbox" value="Outdoor"> Outdoor Plant</label>
                    <label><input type="checkbox" class="category-checkbox" value="Indoor"> Indoor Plant</label>
                    <label><input type="checkbox" class="category-checkbox" value="Flowers"> Flowers</label>
                    <label><input type="checkbox" class="category-checkbox" value="Leaves"> Leaves</label>
                    <label><input type="checkbox" class="category-checkbox" value="Bushes"> Bushes</label>
                    <label><input type="checkbox" class="category-checkbox" value="Trees"> Trees</label>
                    <label><input type="checkbox" class="category-checkbox" value="Climbers"> Climbers</label>
                    <label><input type="checkbox" class="category-checkbox" value="Grasses"> Grasses</label>
                    <label><input type="checkbox" class="category-checkbox" value="Succulent"> Succulent</label>
                    <label><input type="checkbox" class="category-checkbox" value="Cacti"> Cacti</label>
                    <label><input type="checkbox" class="category-checkbox" value="Aquatic"> Aquatic</label>
                </div>
            </div>
            <!-- Plant Size -->
        <div class="plant-size">
            <h3>Filter by Size</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div class="plant-size-items">
                <label><input type="checkbox" class="size-checkbox" value="Seedlings"> Seedlings</label>
                <label><input type="checkbox" class="size-checkbox" value="Juvenile"> Juvenile</label>
                <label><input type="checkbox" class="size-checkbox" value="Adult"> Adult</label>
            </div>
        </div>

        <!-- Plant Location -->
     <div class="plant-location">
            <h3>Filter by Location</h3>
            <button class="clear-all">Clear All</button> <!-- Clear All Button -->
            <div id="locationCheckboxesMobile"></div> <!-- Dynamic Location Checkboxes -->
        </div>

</div>
</div>
        


<div class="listed-plants">
    <div class="sort-container">
        <h1>Listed Plants</h1>
        <select id="sortPrice" class="sort-price-dropdown">
            <option value="">Sort by Price</option>
            <option value="low">Lowest to Highest</option>
            <option value="high">Highest to Lowest</option>
        </select>
    </div>
    <div class="search-bar-container">
    <input type="text" id="searchBar" placeholder="Search...">
    <button id="clearSearch" class="clear-search-btn" style="display: none;">&times;</button>
</div>

    <div class="newly-contents" id="newly-contents">
        <!-- Products will be loaded dynamically -->
    </div>
    <div id="pagination-container"></div> <!-- Pagination -->
</div>
</div>
<?php include 'footer.php';?>
<script src="script.js"></script>
<script>
$(document).ready(function () {
    const plantsPerPage = 6; // Set number of plants per page
    let currentPage = 1;
    let allPlants = []; // Store all plants for pagination

// Variables for logged-in state and user email
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const userEmail = <?php echo json_encode($email); ?>;
    // Fetch Newly Listed Plants via AJAX
    function fetchPlants() {
        $.ajax({
            url: 'Ajax/fetch_categories.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                allPlants = response; // Store response for pagination
                if (!allPlants.length) {
                    $('#newly-contents').html("<p>No plants available at the moment.</p>");
                    return;
                }
                loadPlants(currentPage); // Initial load with pagination
                setupCheckboxes(allPlants); // Setup filters based on fetched plants
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load plants. Please try again.'
                });
            }
        });
    }

    function setupCheckboxes(plants) {
        let locations = [...new Set(plants.map(p => p.city))]; // Unique location names
        let sizes = [...new Set(plants.map(p => p.plantSize))]; // Unique plant sizes
        let types = [...new Set(plants.map(p => p.plantcategories))]; // Unique plant categories

        // Create location checkboxes dynamically
        let locationCheckboxesHtml = locations.map(location =>
            `<label>
                <input type="checkbox" class="location-checkbox" value="${location}">
                ${location}
            </label><br>`).join('');

        $('#locationCheckboxes').html(locationCheckboxesHtml);
        $('#locationCheckboxesMobile').html(locationCheckboxesHtml);

        // Handle checkbox change events for filtering
        $('.location-checkbox, .size-checkbox, .category-checkbox').on('change', filterPlants);
    }

    function loadPlants(page = 1) {
        let totalPages = Math.ceil(allPlants.length / plantsPerPage);
        let paginatedPlants = allPlants.slice((page - 1) * plantsPerPage, page * plantsPerPage);
        displayPlants(paginatedPlants);
        renderPagination(totalPages, page);
    }

    function displayPlants(plantsToDisplay) {
    let contentHtml = plantsToDisplay.map(product => {
        // Check if the user is logged in and if the seller is not the logged-in user
        let chatButtonHtml = '';

        // Conditionally display the chat button only if logged in and the user is not the seller
        if (isLoggedIn && userEmail !== product.seller_email) {
                chatButtonHtml = `<button class="chat-seller" data-email="${product.seller_email}">Chat Seller</button>`;
            }

        return `<div class="plant-item" data-location="${product.city}" data-category="${product.plantcategories}" data-size="${product.plantSize}" data-price="${product.price}">
            <div class="plant-image">
                <img src="Products/${product.seller_email}/${product.img1}" alt="${product.plantname}" onerror="this.onerror=null; this.src='placeholder.jpg';">
            </div>
            <p>${product.plantname}</p>
            <p>Price: ₱${product.price}</p>
            <p>Category: ${product.plantcategories}</p>
            <p>Size: ${product.plantSize}</p>
            <div class="plant-item-buttons">
                <button class="view-details" data-id="${product.plantid}" data-email="${product.seller_email}">View more details</button>
                ${chatButtonHtml} <!-- Conditionally render chat button -->
            </div>
        </div>`;
    }).join('');
    $('#newly-contents').html(contentHtml);
}

    function renderPagination(totalPages, current) {
        let paginationHtml = '';

        if (totalPages <= 1) {
            return;
        }
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<button class="page-link" data-page="${i}">${i}</button>`;
        }
        $('#pagination-container').html(paginationHtml);

        $('.page-link').on('click', function () {
            currentPage = $(this).data('page');
            loadPlants(currentPage);
        });
    }

    function filterPlants() {
        let searchTerm = $('#searchBar').val().toLowerCase();
        let selectedLocations = $('.location-checkbox:checked').map(function () {
            return $(this).val();
        }).get();
        let selectedSizes = $('.size-checkbox:checked').map(function () {
            return $(this).val();
        }).get();
        let selectedTypes = $('.category-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        // Filter plants based on selected checkboxes and search term
        let filteredPlants = allPlants.filter(function (plant) {
            let plantLocation = plant.city;
            let plantSize = plant.plantSize;
            let plantType = plant.plantcategories;
            let plantName = plant.plantname.toLowerCase(); // Lowercase for comparison

            let matchesType = selectedTypes.length === 0 || selectedTypes.includes(plantType);
            let matchesLocation = selectedLocations.length === 0 || selectedLocations.includes(plantLocation);
            let matchesSize = selectedSizes.length === 0 || selectedSizes.includes(plantSize);
            let matchesSearchTerm = plantName.includes(searchTerm); // Check if plant name includes search term

            return matchesType && matchesLocation && matchesSize && matchesSearchTerm; // Ensure all criteria match
        });

        // Reset current page and load filtered plants
        currentPage = 1;
        loadFilteredPlants(filteredPlants);
    }

    function loadFilteredPlants(filteredPlants) {
        let totalPages = Math.ceil(filteredPlants.length / plantsPerPage);
        let paginatedPlants = filteredPlants.slice((currentPage - 1) * plantsPerPage, currentPage * plantsPerPage);
        displayPlants(paginatedPlants);
        renderPagination(totalPages, currentPage);
    }

    // Clear All Button Logic
    $('.clear-all').on('click', function () {
        $(this).closest('.plant-type').find('input[type="checkbox"]').prop('checked', false);
        $(this).closest('.plant-size').find('input[type="checkbox"]').prop('checked', false);
        $(this).closest('.plant-location').find('input[type="checkbox"]').prop('checked', false);
        currentPage = 1; // Reset to first page
        filterPlants(); // Reapply filters to maintain filtering state
    });

    // Sorting Functionality
    $('#sortPrice').on('change', function() {
        let sortValue = $(this).val();
        let sortedPlants = [...allPlants]; // Create a copy of the plants array for sorting

        if (sortValue === 'low') {
            sortedPlants.sort((a, b) => a.price - b.price);
        } else if (sortValue === 'high') {
            sortedPlants.sort((a, b) => b.price - a.price);
        }

        loadFilteredPlants(sortedPlants); // Load sorted plants
    });

    // Search Bar Logic
    $('#searchBar').on('input', function () {
        filterPlants(); // Filter plants on input change
    });

    // Initialize the fetch process
    fetchPlants();

    
});
// Open modal for categories on mobile
$('#openCategoriesModal').on('click', function() {
        $('#categoriesModal').addClass('show');
    });

    $('#closeCategoriesModal').on('click', function() {
        $('#categoriesModal').removeClass('show');
    });
// Save filter state before navigating to the details page
$(document).on('click', '.view-details', function () {
                    let plantId = $(this).data('id');
                    let sellerEmail = $(this).data('email');
                    window.location.href = `viewdetails.php?plantId=${plantId}&sellerEmail=${sellerEmail}`;
                });
</script>
 
</body>
</html>