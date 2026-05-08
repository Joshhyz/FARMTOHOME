<?php
$cookieLifetime = 60 * 60 * 24 * 30;
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: registration.php?mode=login');
    exit();
}

require_once 'database.php';

$currentUserId = (int) $_SESSION['user_id'];
$currentUserName = $_SESSION['user_name'] ?? 'User';
$currentUserRole = strtolower(trim($_SESSION['user_role'] ?? 'buyer'));
$currentUserType = ($currentUserRole === 'farmer' || $currentUserRole === 'seller') ? 'Farmer' : 'Buyer';

// Create messages table if it doesn't exist.
$tableSql = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $tableSql);

$columnCheck = mysqli_query($conn, "SHOW COLUMNS FROM messages LIKE 'is_read'");
if ($columnCheck && mysqli_num_rows($columnCheck) === 0) {
    mysqli_query($conn, "ALTER TABLE messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0");
}

$activeChatUserId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
$messageError = '';
$messageSuccess = '';

$unreadCount = 0;
$unreadResult = mysqli_query($conn, "SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = $currentUserId AND is_read = 0");
if ($unreadResult) {
    $unreadCount = (int) mysqli_fetch_assoc($unreadResult)['unread_count'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $activeChatUserId = isset($_POST['receiver_id']) ? (int) $_POST['receiver_id'] : 0;
    $messageText = trim($_POST['message'] ?? '');

    if ($activeChatUserId <= 0) {
        $messageError = 'Please select a conversation or open a seller from the marketplace.';
    } elseif ($messageText === '') {
        $messageError = 'Type a message before sending.';
    } else {
        $safeMessage = mysqli_real_escape_string($conn, $messageText);
        $insertSql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($currentUserId, $activeChatUserId, '$safeMessage')";
        if (mysqli_query($conn, $insertSql)) {
            $messageSuccess = 'Message sent.';
        } else {
            $messageError = 'Unable to send the message right now.';
        }
    }
}

$conversations = [];
$conversationSql = "SELECT u.id, u.full_name, u.role, MAX(m.created_at) AS last_at,
    SUM(CASE WHEN m.sender_id = u.id AND m.receiver_id = $currentUserId AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
    FROM users u
    JOIN messages m ON (u.id = m.sender_id AND m.receiver_id = $currentUserId) OR (u.id = m.receiver_id AND m.sender_id = $currentUserId)
    GROUP BY u.id
    ORDER BY last_at DESC";
$conversationResult = mysqli_query($conn, $conversationSql);
if ($conversationResult) {
    while ($row = mysqli_fetch_assoc($conversationResult)) {
        if ((int) $row['id'] !== $currentUserId) {
            $conversations[] = $row;
        }
    }
}

$activeChatUser = null;
$chatMessages = [];

if ($activeChatUserId > 0) {
    $userQuery = mysqli_query($conn, "SELECT id, full_name, role FROM users WHERE id = $activeChatUserId LIMIT 1");
    if ($userQuery && mysqli_num_rows($userQuery) > 0) {
        $activeChatUser = mysqli_fetch_assoc($userQuery);

        mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE sender_id = $activeChatUserId AND receiver_id = $currentUserId AND is_read = 0");

        $chatSql = "SELECT m.*, u.full_name AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = $currentUserId AND m.receiver_id = $activeChatUserId)
               OR (m.sender_id = $activeChatUserId AND m.receiver_id = $currentUserId)
            ORDER BY m.created_at ASC";
        $chatResult = mysqli_query($conn, $chatSql);
        if ($chatResult) {
            while ($row = mysqli_fetch_assoc($chatResult)) {
                $chatMessages[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page">
<header class="dashboard-topbar">
    <div class="dashboard-brand">
        <img src="images/logo.png" alt="FarmToHome Logo">
        FarmToHome
    </div>
    <div class="dashboard-topbar-right">
        <span class="dashboard-role-label"><?php echo htmlspecialchars($currentUserType); ?> Role</span>
        <a href="Message.php" class="dashboard-notification">
            Messages
            <?php if ($unreadCount > 0): ?>
                <span class="notification-badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
        </a>
        <div class="dashboard-user">Hi, <?php echo htmlspecialchars($currentUserName); ?></div>
        <a href="logout.php" class="dashboard-logout">Logout</a>
    </div>
</header>
<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-title">Messages</div>
        <div class="sidebar-description">Chat with buyers and sellers directly.</div>
        <div class="sidebar-menu">
            <a class="sidebar-item" href="dashboard.php">
                <span>Dashboard</span>
            </a>
            <a class="sidebar-item active" href="Message.php">
                <span>Messages</span>
            </a>
            <a class="sidebar-item" href="marketplace.php">
                <span>Browse Products</span>
            </a>
        </div>
    </aside>
    <main class="dashboard-main">
        <section class="chat-panel">
            <div class="chat-header">
                <h2>Messages</h2>
                <p>Start a chat by clicking "Message Seller" on a product, or continue an existing conversation.</p>
            </div>
            <div class="messages-grid">
                <div class="conversations-card">
                    <div class="conversations-heading">Conversations</div>
                    <ul class="conversation-list">
                        <?php if (count($conversations) > 0): ?>
                            <?php foreach ($conversations as $conversation): ?>
                                <li class="conversation-card <?php echo ($activeChatUser && (int) $activeChatUser['id'] === (int) $conversation['id']) ? 'active' : ''; ?>">
                                    <a href="Message.php?user_id=<?php echo (int) $conversation['id']; ?>">
                                        <div class="conversation-meta">
                                            <div class="conversation-name"><?php echo htmlspecialchars($conversation['full_name']); ?></div>
                                            <div class="conversation-role"><?php echo htmlspecialchars(ucfirst($conversation['role'])); ?></div>
                                        </div>
                                        <?php if ((int) $conversation['unread_count'] > 0): ?>
                                            <span class="conversation-badge"><?php echo (int) $conversation['unread_count']; ?></span>
                                        <?php else: ?>
                                            <span class="conversation-badge">Chat</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="conversation-card">
                                <div class="conversation-meta">
                                    <div class="conversation-name">No conversations yet</div>
                                    <div class="conversation-role">Message a seller from the marketplace.</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="chat-card">
                    <?php if ($activeChatUser): ?>
                        <div class="chat-header">
                            <h2><?php echo htmlspecialchars($activeChatUser['full_name']); ?></h2>
                            <p><?php echo htmlspecialchars(ucfirst($activeChatUser['role'])); ?></p>
                        </div>
                        <div class="chat-body">
                            <?php if (count($chatMessages) > 0): ?>
                                <?php foreach ($chatMessages as $message): ?>
                                    <div class="message-bubble <?php echo ((int) $message['sender_id'] === $currentUserId) ? 'sent' : 'received'; ?>">
                                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                        <div class="message-meta">
                                            <?php echo ((int) $message['sender_id'] === $currentUserId) ? 'You' : htmlspecialchars($message['sender_name']); ?> · <?php echo htmlspecialchars(date('M j, Y h:i A', strtotime($message['created_at']))); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="chat-empty">No messages yet. Send the first message to begin the chat.</div>
                            <?php endif; ?>
                        </div>
                        <div class="chat-input-area">
                            <?php if ($messageError): ?>
                                <div class="error-message"><?php echo htmlspecialchars($messageError); ?></div>
                            <?php elseif ($messageSuccess): ?>
                                <div class="success-message"><?php echo htmlspecialchars($messageSuccess); ?></div>
                            <?php endif; ?>
                            <form class="chat-form" method="POST">
                                <input type="hidden" name="receiver_id" value="<?php echo (int) $activeChatUser['id']; ?>">
                                <textarea name="message" placeholder="Type your message..." required></textarea>
                                <button type="submit" name="send_message">Send</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="chat-empty">Select a conversation or click "Message Seller" on a product to start chatting.</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
