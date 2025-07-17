<?php
ob_start(); // Start output buffering
date_default_timezone_set('Asia/Colombo'); // Set PHP timezone to Asia/Colombo
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors for debugging

include_once '../config/db_connect.php';
require_once 'admin_auth.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Notification ID is required.");
}

$notification_id = intval($_GET['id']);
$error_message = '';
$success_message = '';

try {
    // Set MySQL session timezone to Asia/Colombo
    $pdo->exec("SET time_zone = '+05:30'");
} catch (PDOException $e) {
    $error_message = "Error setting MySQL timezone: " . $e->getMessage();
}

try {
    // Fetch the notification details
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->execute([$notification_id]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notification) {
        die("Notification not found.");
    }

    // Mark notification as read
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$notification_id]);
    $success_message = "Notification marked as read.";
} catch (PDOException $e) {
    $error_message = "Error fetching notification: " . $e->getMessage();
    $notification = null;
}

// Fetch contact message details if applicable
$contact_message = null;
if ($notification && $notification['type'] === 'contact_message') {
    try {
        preg_match('/\d+/', $notification['message'], $matches);
        $contact_id = $matches[0] ?? null;

        if ($contact_id) {
            $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([intval($contact_id)]);
            $contact_message = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($contact_message && !filter_var($contact_message['email'], FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid email address for contact message.";
                $contact_message = null;
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching contact message: " . $e->getMessage();
    }
}

// Fetch reservation details if applicable
$reservation = null;
if ($notification && ($notification['type'] === 'reservation' || $notification['type'] === 'booking')) {
    try {
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
            if ($reservation && !filter_var($reservation['customer_email'], FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid customer email for reservation.";
                $reservation = null;
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching reservation: " . $e->getMessage();
    }
}

/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    $to_email = $_POST['to_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';

    if (filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';
            $mail->Password = 'your-app-password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'TukTuk Rental Team');
            $mail->addAddress($to_email);
            $mail->addReplyTo('your-email@gmail.com', 'TukTuk Rental Team');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br(htmlspecialchars($body));
            $mail->AltBody = strip_tags($body);

            $mail->send();
            $success_message = "Email sent successfully to $to_email!";
        } catch (Exception $e) {
            $error_message = "Failed to send email: " . $mail->ErrorInfo;
        }
    } else {
        $error_message = "Invalid email address provided.";
    }
}
*/
?>

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
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .container {
            max-width: 1200px;
            padding: 20px;
        }
        .notification-card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #0d6efd;
        }
        .card-header {
            background: none;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .badge-notification {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .notification-timestamp {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .detail-row {
            margin-bottom: 10px;
        }
        .message-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .btn-back, .btn-primary, .btn-outline-primary {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .btn-primary:hover, .btn-outline-primary:hover {
            filter: brightness(90%);
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
        }
        .email-form {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        @media (max-width: 768px) {
            h2 {
                font-size: 1.5rem;
            }
            .card-title {
                font-size: 1.1rem;
            }
            .notification-timestamp {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell me-2 text-primary"></i> Notification Details</h2>
        <a href="notifications.php" class="btn btn-outline-secondary btn-back">
            <i class="fas fa-arrow-left me-2"></i> Back to Notifications
        </a>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($notification): ?>
        <div class="card notification-card mb-4 <?php echo 'notification-' . htmlspecialchars($notification['type']); ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-<?php echo $notification['type'] === 'contact_message' ? 'success' : ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? 'danger' : 'primary'); ?> badge-notification">
                        <?php 
                        $icon = $notification['type'] === 'contact_message' ? 'fa-message' : ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? 'fa-calendar-check' : 'fa-info-circle');
                        ?>
                        <i class="fas <?php echo $icon; ?> me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($notification['type'])); ?>
                    </span>
                </div>
                <div class="notification-timestamp">
                    <i class="far fa-clock me-1"></i>
                    <?php 
                        $date = new DateTime($notification['created_at'], new DateTimeZone('Asia/Colombo'));
                        echo $date->format('d M Y, H:i'); 
                    ?>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    <?php 
                    $title = $notification['type'] === 'contact_message' ? 'New Contact Message' : 
                             ($notification['type'] === 'reservation' || $notification['type'] === 'booking' ? 'New Reservation Request' : 'System Notification');
                    echo htmlspecialchars($title);
                    ?>
                </h5>
                <p class="card-text"><?php echo htmlspecialchars($notification['message']); ?></p>
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
    <?php endif; ?>

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
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($contact_message['email']); ?>&su=Re:%20Your%20Contact%20Message&body=Dear%20<?php echo urlencode($contact_message['name']); ?>,%0A%0AThank%20you%20for%20your%20message.%0A%0A[Your%20response%20here]%0A%0ABest%20regards,%0ATukTuk%20Rental%20Team"
                           target="_blank" class="email-link">
                            <?php echo htmlspecialchars($contact_message['email']); ?>
                        </a>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-3 fw-bold"><i class="fas fa-clock me-2"></i> Sent At:</div>
                    <div class="col-md-9">
                        <?php 
                            $date = new DateTime($contact_message['created_at'], new DateTimeZone('Asia/Colombo'));
                            echo $date->format('d M Y, H:i'); 
                        ?>
                    </div>
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
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($contact_message['email']); ?>&su=Re:%20Your%20Contact%20Message&body=Dear%20<?php echo urlencode($contact_message['name']); ?>,%0A%0AThank%20you%20for%20your%20message.%0A%0A[Your%20response%20here]%0A%0ABest%20regards,%0ATukTuk%20Rental%20Team"
                   target="_blank" class="btn btn-primary email-link me-2">
                    <i class="fas fa-reply me-2"></i> Reply to Message
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#emailModal" 
                        data-email="<?php echo htmlspecialchars($contact_message['email']); ?>" 
                        data-subject="Re: Your Contact Message"
                        data-body="Dear <?php echo htmlspecialchars($contact_message['name']); ?>,

Thank you for your message.

[Your response here]

Best regards,
TukTuk Rental Team">
                    <i class="fas fa-envelope-open-text me-2"></i> Manual Email
                </button>
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
                        <a href="mailto:<?php echo htmlspecialchars($reservation['customer_email']); ?>?subject=Re:%20Your%20Reservation%20(ID:%20<?php echo $reservation['id']; ?>)&body=Dear%20<?php echo urlencode($reservation['customer_name']); ?>,%0D%0A%0D%0AThank%20you%20for%20your%20reservation%20for%20<?php echo urlencode($reservation['brand'] . ' ' . $reservation['model']); ?>.%0D%0A%0D%0A[Your%20response%20here]%0D%0A%0D%0ABest%20regards,%0D%0ATukTuk%20Rental%20Team"
                           class="email-link">
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
                        <?php 
                            $pickup_date = new DateTime($reservation['pickup_date'] . ' ' . $reservation['pickup_time'], new DateTimeZone('Asia/Colombo'));
                            echo $pickup_date->format('d M Y, H:i'); 
                        ?>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Return:</div>
                    <div class="col-md-9">
                        <?php echo htmlspecialchars($reservation['return_location']); ?> on 
                        <?php 
                            $return_date = new DateTime($reservation['return_date'] . ' ' . $reservation['return_time'], new DateTimeZone('Asia/Colombo'));
                            echo $return_date->format('d M Y, H:i'); 
                        ?>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-3 fw-bold"><i class="fas fa-tag me-2"></i> Status:</div>
                    <div class="col-md-9">
                        <span class="badge bg-<?php 
                            echo $reservation['status'] === 'confirmed' ? 'success' : 
                                 ($reservation['status'] === 'cancelled' ? 'danger' : 'warning'); 
                        ?>">
                            <?php echo ucfirst(htmlspecialchars($reservation['status'])); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="reservations.php?id=<?php echo $reservation['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Manage Reservation
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($reservation['customer_email']); ?>?subject=Re:%20Your%20Reservation%20(ID:%20<?php echo $reservation['id']; ?>)&body=Dear%20<?php echo urlencode($reservation['customer_name']); ?>,%0D%0A%0D%0AThank%20you%20for%20your%20reservation%20for%20<?php echo urlencode($reservation['brand'] . ' ' . $reservation['model']); ?>.%0D%0A%0D%0A[Your%20response%20here]%0D%0A%0D%0ABest%20regards,%0D%0ATukTuk%20Rental%20Team"
                       class="btn btn-outline-primary email-link me-2">
                        <i class="fas fa-envelope me-2"></i> Contact Customer
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#emailModal" 
                            data-email="<?php echo htmlspecialchars($reservation['customer_email']); ?>" 
                            data-subject="Re: Your Reservation (ID: <?php echo $reservation['id']; ?>)"
                            data-body="Dear <?php echo htmlspecialchars($reservation['customer_name']); ?>,

Thank you for your reservation for <?php echo htmlspecialchars($reservation['brand'] . ' ' . $reservation['model']); ?>.

[Your response here]

Best regards,
TukTuk Rental Team">
                        <i class="fas fa-envelope-open-text me-2"></i> Manual Email
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$contact_message && !$reservation && $notification && $notification['type'] !== 'system'): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Detailed information for this notification type is not available.
        </div>
    <?php endif; ?>

    <!-- Modal for Manual Email -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Compose Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" class="email-form" action="https://mail.google.com/mail/?view=cm&fs=1" target="_blank">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="to_email" class="form-label">To:</label>
                            <input type="email" class="form-control" id="to_email" name="to" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject:</label>
                            <input type="text" class="form-control" id="subject" name="su" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label">Message:</label>
                            <textarea class="form-control" id="body" name="body" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Open in Gmail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Populate modal fields when opened
    document.addEventListener('DOMContentLoaded', function() {
        const emailModal = document.getElementById('emailModal');
        emailModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const email = button.getAttribute('data-email');
            const subject = button.getAttribute('data-subject');
            const body = button.getAttribute('data-body').replace(/\n/g, '\n');

            const modal = this;
            modal.querySelector('#to_email').value = email;
            modal.querySelector('#subject').value = subject;
            modal.querySelector('#body').value = body;
        });
    });
</script>
</body>
</html>
<?php
// Debug: Output current server time to verify
// Remove this after testing
echo "<!-- Debug: Current server time (Asia/Colombo): " . date('Y-m-d H:i:s') . " -->";
ob_end_flush();
?>