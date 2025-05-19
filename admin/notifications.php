<?php
// Include database connection
if (!isset($pdo)) { 
    include_once '../config/db_connect.php'; 
}

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if database connection is established
if (!isset($pdo)) {
    die("Database connection not established. Please check your configuration.");
}

// Mark notifications as read when the page is loaded
try {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error marking notifications as read: " . $e->getMessage();
}

// Fetch all notifications with proper error handling
try {
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching notifications: " . $e->getMessage();
    $notifications = [];
}
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
</head>
<body>
  <?php include 'admin_nav.php'; ?>

  <div class="page-header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-bell text-primary me-2"></i> Notifications</h2>
        <div>
          <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="container mb-5">    
    <?php if (count($notifications) > 0): ?>
      <div class="notification-list">
        <?php foreach ($notifications as $notification): 
          // Determine notification class based on type
          $typeClass = 'notification-' . $notification['type'];
          $typeColor = '';
          $typeIcon = '';
          
          switch($notification['type']) {
            case 'contact_message':
              $typeColor = 'success';
              $typeIcon = 'fa-message';
              break;
            case 'reservation':
            case 'booking':
              $typeColor = 'danger';
              $typeIcon = 'fa-calendar-check';
              break;
            case 'system':
              $typeColor = 'secondary';
              $typeIcon = 'fa-cog';
              break;
            default:
              $typeColor = 'primary';
              $typeIcon = 'fa-info-circle';
          }
        ?>
          <div class="card notification-item <?php echo $typeClass; ?> mb-3 <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
            <?php if (!$notification['is_read']): ?>
              <span class="badge bg-danger notification-badge">New</span>
            <?php endif; ?>
            
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="notification-content">
                  <div>
                    <span class="notification-type bg-<?php echo $typeColor; ?> text-white">
                      <i class="fas <?php echo $typeIcon; ?> me-1"></i>
                      <?php echo ucfirst($notification['type']); ?>
                    </span>
                    
                    <span class="notification-date">
                      <i class="far fa-clock me-1"></i>
                      <?php echo date('d M Y, H:i', strtotime($notification['created_at'])); ?>
                    </span>
                  </div>
                  
                  <p class="notification-message">
                    <?php echo htmlspecialchars($notification['message']); ?>
                  </p>
                  
                  <div class="notification-footer">
                    <small class="text-muted">
                      <?php echo human_time_diff(strtotime($notification['created_at'])); ?>
                    </small>
                    <div class="notification-actions">
                      <a href="view_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-primary btn-view">
                        <i class="fas fa-eye me-1"></i> View Details
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-notifications">
        <i class="fas fa-bell-slash"></i>
        <h4>No Notifications</h4>
        <p class="text-muted">
          You don't have any notifications at the moment.<br>
          Notifications will appear here when you receive new messages or bookings.
        </p>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Function to convert timestamp to relative time
    function timeAgo(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const seconds = Math.floor((now - date) / 1000);
      
      let interval = Math.floor(seconds / 31536000);
      if (interval >= 1) {
        return interval + " year" + (interval === 1 ? "" : "s") + " ago";
      }
      
      interval = Math.floor(seconds / 2592000);
      if (interval >= 1) {
        return interval + " month" + (interval === 1 ? "" : "s") + " ago";
      }
      
      interval = Math.floor(seconds / 86400);
      if (interval >= 1) {
        return interval + " day" + (interval === 1 ? "" : "s") + " ago";
      }
      
      interval = Math.floor(seconds / 3600);
      if (interval >= 1) {
        return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
      }
      
      interval = Math.floor(seconds / 60);
      if (interval >= 1) {
        return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
      }
      
      return "just now";
    }

    // Apply the time ago function to all notification dates
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.notification-footer small').forEach(function(element) {
        const timestamp = element.closest('.notification-item').querySelector('.notification-date').textContent.trim();
        if (timestamp) {
          element.textContent = timeAgo(timestamp);
        }
      });
    });
  </script>
</body>
</html>

<?php
// Function to display a human-readable time difference
function human_time_diff($timestamp) {
    $current_time = time();
    $diff = $current_time - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = round($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = round($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = round($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = round($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}
?>