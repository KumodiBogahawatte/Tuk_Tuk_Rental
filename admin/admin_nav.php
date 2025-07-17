<?php
// Ensure we have a database connection
if (!isset($pdo)) {
    include_once '../config/db_connect.php';
}

// Fetch unread notifications count
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE is_read = 0");
    $stmt->execute();
    $row = $stmt->fetch();
    $unread_count = $row['unread_count'] ?? 0;
} catch (PDOException $e) {
    $unread_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 12px 0;
        }
        .navbar-brand {
            font-weight: 600;
            color: #1a1f36;
            font-size: 1.25rem;
        }
        .navbar-brand:hover {
            color: #0d6efd;
        }
        .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 8px 16px;
            transition: color 0.2s ease;
        }
        .nav-link:hover {
            color: #0d6efd;
        }
        .nav-item.active .nav-link {
            color: #0d6efd;
            font-weight: 600;
        }
        .btn-logout {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #ffffff;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        .notification-bell {
            position: relative;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
            padding: 3px 6px;
            border-radius: 50%;
            background-color: #dc3545;
            color: #ffffff;
        }
        @media (max-width: 991.98px) {
            .navbar-nav {
                margin-top: 10px;
            }
            .nav-item {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">TukTuk Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'active' : '' ?>" href="vehicles.php">Vehicles</a></li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?> notification-bell" href="notifications.php">
                        <i class="fas fa-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="notification-badge"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-logout ms-2" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
</body>
</html>