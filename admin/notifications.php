<?php
date_default_timezone_set('Asia/Colombo');
if (!isset($pdo)) { 
    include_once '../config/db_connect.php'; 
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($pdo)) {
    die("Database connection not established. Please check your configuration.");
}

// Set MySQL session timezone to Asia/Colombo
try {
    $pdo->exec("SET time_zone = '+05:30'");
} catch (PDOException $e) {
    echo "Error setting MySQL timezone: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error marking notifications as read: " . $e->getMessage();
}

try {
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching notifications: " . $e->getMessage();
    $notifications = [];
}

function human_time_diff($created_at) {
    $current_time = new DateTime('now', new DateTimeZone('Asia/Colombo'));
    $notification_time = new DateTime($created_at, new DateTimeZone('Asia/Colombo'));
    $diff = $current_time->getTimestamp() - $notification_time->getTimestamp();
    
    if ($diff < 60) return 'just now';
    elseif ($diff < 3600) return round($diff / 60) . ' minute' . ($diff / 60 > 1 ? 's' : '') . ' ago';
    elseif ($diff < 86400) return round($diff / 3600) . ' hour' . ($diff / 3600 > 1 ? 's' : '') . ' ago';
    elseif ($diff < 604800) return round($diff / 86400) . ' day' . ($diff / 86400 > 1 ? 's' : '') . ' ago';
    elseif ($diff < 2592000) return round($diff / 604800) . ' week' . ($diff / 604800 > 1 ? 's' : '') . ' ago';
    elseif ($diff < 31536000) return round($diff / 2592000) . ' month' . ($diff / 2592000 > 1 ? 's' : '') . ' ago';
    else return round($diff / 31536000) . ' year' . ($diff / 31536000 > 1 ? 's' : '') . ' ago';
}

// Debug: Output current server time to verify
// Remove this after testing
echo "<!-- Debug: Current server time (Asia/Colombo): " . date('Y-m-d H:i:s') . " -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications - TukTuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../admin/css/notification.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .container {
            max-width: 1200px;
            padding: 20px;
        }
        .page-header {
            background: none;
            padding: 0;
            margin-bottom: 20px;
        }
        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1f36;
        }
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            color: #6c757d;
            border-color: #6c757d;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #ffffff;
        }
        .no-notifications {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 40px;
            text-align: center;
        }
        .no-notifications i {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .no-notifications h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1f36;
        }
        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.25rem;
            }
            .no-notifications {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-bell me-2"></i>Notifications</h2>
            <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
        </div>
    </div>
    <?php if (count($notifications) > 0): ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notification): 
                $typeClass = 'notification-' . $notification['type'];
                $typeColor = $notification['type'] === 'contact_message' ? '#198754' : 
                            ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? '#dc3545' : '#6c757d');
                $typeIcon = $notification['type'] === 'contact_message' ? 'fa-message' : 
                           ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? 'fa-calendar-check' : 'fa-cog');
            ?>
                <div class="card notification-item <?php echo $typeClass; ?> mb-3 <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                    <?php if (!$notification['is_read']): ?>
                        <span class="badge bg-danger notification-badge">New</span>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="notification-content">
                                <div>
                                    <span class="notification-type" style="background-color: <?php echo $typeColor; ?>; color: #ffffff;">
                                        <i class="fas <?php echo $typeIcon; ?> me-1"></i>
                                        <?php echo ucfirst($notification['type']); ?>
                                    </span>
                                    <span class="notification-date">
                                        <i class="far fa-clock me-1"></i>
                                        <?php 
                                            $date = new DateTime($notification['created_at'], new DateTimeZone('Asia/Colombo'));
                                            echo $date->format('d M Y, H:i'); 
                                        ?>
                                    </span>
                                </div>
                                <p class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <div class="notification-footer">
                                    <small class="text-muted"><?php echo human_time_diff($notification['created_at']); ?></small>
                                    <div class="notification-actions">
                                        <a href="view_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-primary btn-view">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Debug: Output raw and converted created_at -->
                        <!-- Remove this after testing -->
                        <?php
                            $debug_date = new DateTime($notification['created_at'], new DateTimeZone('Asia/Colombo'));
                            $debug_utc = new DateTime($notification['created_at'], new DateTimeZone('Asia/Colombo'));
                            $debug_utc->setTimezone(new DateTimeZone('UTC'));
                            echo "<!-- Debug: Raw created_at (from DB, Asia/Colombo): {$notification['created_at']}, Displayed (Asia/Colombo): " . $debug_date->format('Y-m-d H:i:s') . ", UTC equivalent: " . $debug_utc->format('Y-m-d H:i:s') . " -->";
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-notifications">
            <i class="fas fa-bell-slash"></i>
            <h4>No Notifications</h4>
            <p class="text-muted">You don't have any notifications at the moment.<br>Notifications will appear here when you receive new messages or bookings.</p>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>