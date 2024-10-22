// Store already notified message IDs
let notifiedMessageIds = [];

// Function to fetch new messages
function fetchLatestMessages() {
    $.ajax({
        url: "chat_upgrade/ajax/fetch_latest_message.php", // Adjust the URL as necessary
        method: "GET",
        dataType: "json",
        success: function(response) {
            response.forEach(function(message) {
                // Check if the message is unseen and if the user hasn't been notified already
                if (message.unseen_count > 0 && !notifiedMessageIds.includes(message.id)) {
                    showNotification(message);
                     // Avoid sending duplicate notifications
                }notifiedMessageIds.push(message.id);
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error fetching messages: " + errorThrown);
        }
    });
}

// Function to display browser notification
function showNotification(message) {
    if (Notification.permission === "granted") {
        new Notification("New Message from " + message.recipient_name, {
            body: message.message,
            icon: 'ProfilePictures/'+message.profile_picture // Optional icon path
        });
    }
}

// Request permission for browser notifications if not already granted
function requestNotificationPermission() {
    if (Notification.permission !== "granted") {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                console.log("Notification permission granted.");
            } else {
                console.log("Notification permission denied.");
            }
        });
    }
}

// Start polling for new messages every 5 seconds
setInterval(fetchLatestMessages, 5000);

// Request permission on page load
requestNotificationPermission();
