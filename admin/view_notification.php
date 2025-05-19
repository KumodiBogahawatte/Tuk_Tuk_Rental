<?php
include_once '../config/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    die("Notification ID is required.");
}

$notification_id = intval($_GET['id']);

// Fetch the notification details
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->execute([$notification_id]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notification) {
        die("Notification not found.");
    }
    
    // Mark this notification as read
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$notification_id]);
    
} catch (PDOException $e) {
    die("Error fetching notification: " . $e->getMessage());
}

// If the notification is related to a contact message, fetch the message details
$contact_message = null;
if ($notification['type'] === 'contact_message') {
    try {
        // Extract message ID from notification message
        preg_match('/\d+/', $notification['message'], $matches);
        $contact_id = $matches[0] ?? null;
        
        if ($contact_id) {
            $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([intval($contact_id)]);
            $contact_message = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching contact message: " . $e->getMessage();
    }
}

// Check if this is a reservation notification
$reservation = null;
if ($notification['type'] === 'reservation' || $notification['type'] === 'booking') {
    try {
        // Extract reservation ID from notification message
        preg_match('/\d+/', $notification['message'], $matches);
        $reservation_id = $matches[0] ?? null;
        
        if ($reservation_id) {
            $stmt = $pdo->prepare("
                SELECT r.*, v.brand, v.model 
                FROM reservations r
                JOIN vehicles v ON r.vehicle_id = v.id
                WHERE r.id = ?
            ");
            $stmt->execute([intval($reservation_id)]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching reservation: " . $e->getMessage();
    }
}
?>

<?php include 'admin_nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notification - TukTuk Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../admin/css/view_notification.css">
</head>
<body>
    
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-bell me-2 text-primary"></i> Notification Details</h2>
            <a href="notifications.php" class="btn btn-outline-secondary btn-back">
                <i class="fas fa-arrow-left me-2"></i> Back to Notifications
            </a>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card notification-card mb-4 <?php echo 'notification-' . $notification['type']; ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-<?php echo $notification['type'] === 'contact_message' ? 'success' : ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? 'danger' : 'primary'); ?> badge-notification">
                        <?php 
                        $icon = '';
                        switch($notification['type']) {
                            case 'contact_message':
                                $icon = 'fa-message';
                                break;
                            case 'reservation':
                            case 'booking':
                                $icon = 'fa-calendar-check';
                                break;
                            default:
                                $icon = 'fa-info-circle';
                        }
                        ?>
                        <i class="fas <?php echo $icon; ?> me-1"></i>
                        <?php echo ucfirst($notification['type']); ?>
                    </span>
                </div>
                <div class="notification-timestamp">
                    <i class="far fa-clock me-1"></i>
                    <?php echo date('d M Y, H:i', strtotime($notification['created_at'])); ?>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    <?php 
                    $title = '';
                    switch($notification['type']) {
                        case 'contact_message':
                            $title = 'New Contact Message';
                            break;
                        case 'reservation':
                        case 'booking':
                            $title = 'New Reservation Request';
                            break;
                        default:
                            $title = 'System Notification';
                    }
                    echo $title;
                    ?>
                </h5>
                <p class="card-text"><?php echo htmlspecialchars($notification['message']); ?></p>
            </div>
        </div>

        <?php if ($contact_message): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-message me-2"></i> Contact Message Details</h4>
                </div>
                <div class="card-body">
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-user me-2"></i> From:</div>
                        <div class="col-md-9"><?php echo htmlspecialchars($contact_message['name']); ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-envelope me-2"></i> Email:</div>
                        <div class="col-md-9">
                            <a href="mailto:<?php echo htmlspecialchars($contact_message['email']); ?>">
                                <?php echo htmlspecialchars($contact_message['email']); ?>
                            </a>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-clock me-2"></i> Sent At:</div>
                        <div class="col-md-9"><?php echo date('d M Y, H:i', strtotime($contact_message['created_at'])); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-comment me-2"></i> Message:</div>
                        <div class="col-md-9">
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($contact_message['message'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="mailto:<?php echo htmlspecialchars($contact_message['email']); ?>" class="btn btn-primary">
                        <i class="fas fa-reply me-2"></i> Reply to Message
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($reservation): ?>
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Reservation Details</h4>
                </div>
                <div class="card-body">
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-car me-2"></i> Vehicle:</div>
                        <div class="col-md-9"><?php echo htmlspecialchars($reservation['brand'] . ' ' . $reservation['model']); ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-user me-2"></i> Customer:</div>
                        <div class="col-md-9"><?php echo htmlspecialchars($reservation['customer_name']); ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-envelope me-2"></i> Email:</div>
                        <div class="col-md-9">
                            <a href="mailto:<?php echo htmlspecialchars($reservation['customer_email']); ?>">
                                <?php echo htmlspecialchars($reservation['customer_email']); ?>
                            </a>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-phone me-2"></i> Phone:</div>
                        <div class="col-md-9"><?php echo htmlspecialchars($reservation['customer_phone']); ?></div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Pickup:</div>
                        <div class="col-md-9">
                            <?php echo htmlspecialchars($reservation['pickup_location']); ?> on 
                            <?php echo date('d M Y', strtotime($reservation['pickup_date'])); ?> at
                            <?php echo date('H:i', strtotime($reservation['pickup_time'])); ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Return:</div>
                        <div class="col-md-9">
                            <?php echo htmlspecialchars($reservation['return_location']); ?> on 
                            <?php echo date('d M Y', strtotime($reservation['return_date'])); ?> at
                            <?php echo date('H:i', strtotime($reservation['return_time'])); ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-3 fw-bold"><i class="fas fa-tag me-2"></i> Status:</div>
                        <div class="col-md-9">
                            <span class="badge bg-<?php 
                                echo $reservation['status'] === 'confirmed' ? 'success' : 
                                    ($reservation['status'] === 'cancelled' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($reservation['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <a href="reservations.php?id=<?php echo $reservation['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Manage Reservation
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($reservation['customer_email']); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i> Contact Customer
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!$contact_message && !$reservation && $notification['type'] !== 'system'): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Detailed information for this notification type is not available.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>