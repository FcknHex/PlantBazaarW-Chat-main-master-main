<?php
include '../conn.php'; // Include your database connection

// Fetch regions from the database
$query = "SELECT id, name FROM regions ORDER BY name ASC";
$result = mysqli_query($conn, $query);

$regions = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $regions[] = $row;
    }
}

// Return regions as JSON
header('Content-Type: application/json');
echo json_encode($regions);
?>
