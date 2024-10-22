<?php
session_start();
$current_user_id = $_SESSION['user_id'];
include "../conn.php";

// Fetch the latest message per conversation and count unseen messages for each chat user, including the recipient's name
$sql = "
    SELECT 
        m1.message, 
        m1.sender_id, 
        m1.receiver_id, 
        m1.timestamp, 
        m1.status,
        unseen.unseen_messages,
        CASE 
            WHEN m1.sender_id = $current_user_id THEN m1.receiver_id
            ELSE m1.sender_id
        END AS chat_user,
        users.firstname AS recipient_firstname,
        users.lastname AS recipient_lastname,
        users.proflePicture AS recipient_proflePicture
    FROM messages m1
    INNER JOIN (
        SELECT 
            CASE 
                WHEN sender_id = $current_user_id THEN receiver_id
                ELSE sender_id
            END AS chat_user,
            MAX(timestamp) AS latest_message
        FROM messages
        WHERE sender_id = $current_user_id OR receiver_id = $current_user_id
        GROUP BY chat_user
    ) m2 ON (m1.sender_id = m2.chat_user OR m1.receiver_id = m2.chat_user)
        AND m1.timestamp = m2.latest_message
    LEFT JOIN (
        SELECT 
            CASE 
                WHEN sender_id = $current_user_id THEN receiver_id
                ELSE sender_id
            END AS chat_user,
            COUNT(*) AS unseen_messages
        FROM messages
        WHERE status = 0
        AND receiver_id = $current_user_id
        GROUP BY chat_user
    ) unseen ON unseen.chat_user = m2.chat_user
    LEFT JOIN users ON users.id = CASE 
                                    WHEN m1.sender_id = $current_user_id THEN m1.receiver_id
                                    ELSE m1.sender_id
                                  END
    ORDER BY m1.timestamp DESC
";

$result = mysqli_query($conn, $sql);
$userMessages = [];
while ($row = mysqli_fetch_assoc($result)) {
    
    $isNewMessage = ($row['status'] == 0) && ($row['receiver_id'] == $current_user_id);
    $unseenCount = isset($row['unseen_messages']) ? $row['unseen_messages'] : 0;

    // Determine the other user's ID (chat partner)
    $chatUserId = ($row['sender_id'] == $current_user_id) ? $row['receiver_id'] : $row['sender_id'];
    
    $recipientName = $row['recipient_firstname'] . ' ' . $row['recipient_lastname'];

    $profilePicture = $row['recipient_proflePicture'];

    $messageText = $isNewMessage 
        ? "New Message (" . $unseenCount . "): " . htmlspecialchars($row['message'])
        : htmlspecialchars($row['message']);
    
    $notification = htmlspecialchars($row['message']);

    $userMessages[] = [
        'user_id' => $chatUserId, // Chat partner's ID
        'recipient_name' => $recipientName, // Recipient's full name
        'profile_picture' => $profilePicture, // Recipient's profile picture path
        'message' => $messageText,
        'timestamp' => date("h:i a", strtotime($row['timestamp'])),
        'notification' => $notification,
        'unseen_count' => $unseenCount,
        'sender_id' => $row['sender_id'],
        'recipient_id' => $row['receiver_id']
    ];
}

// Output the message data as JSON
echo json_encode($userMessages);
?>
