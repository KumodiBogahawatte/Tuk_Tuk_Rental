<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">TukTuk Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
        <li class="nav-item"><a class="nav-link" href="vehicles.php">Vehicle Management</a></li>
        <li class="nav-item"><a class="nav-link position-relative" href="notifications.php"><i class="fas fa-bell"></i>
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
            // Silently handle the error and set count to 0
            $unread_count = 0;
        }
        ?>
        <?php if ($unread_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count; ?>
              </span>
            <?php endif; ?>
        </a></li>
        <li class="nav-item">
          <a class="btn btn-danger ms-2" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>