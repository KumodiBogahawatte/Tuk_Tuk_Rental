<!-- Main landing page -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Reservstion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>
    <section class="container my-5">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Your Reservation Details</h4>
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <th>Pick-Up Location</th>
                <td>Maharagama</td>
              </tr>
              <tr>
                <th>Rental Date</th>
                <td>2025-05-01</td>
              </tr>
              <tr>
                <th>Rental Time</th>
                <td>10:00 AM</td>
              </tr>
              <tr>
                <th>Return Location</th>
                <td>Homagama</td>
              </tr>
              <tr>
                <th>Return Date</th>
                <td>2025-05-02</td>
              </tr>
              <tr>
                <th>Return Time</th>
                <td>04:00 PM</td>
              </tr>
            </tbody>
          </table>
          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary me-md-2" type="button">Confim Booking</button>
          </div>
        </div>
      </div>
    </section>


    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- GSAP (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>

    <!-- Your JS File -->
    <script src="../assets/js/index.js"></script>
    <!-- AOS Library JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    flatpickr(".date-input", {
        dateFormat: "d/m/Y", // or "Y-m-d" etc.
    });
    </script>

  </body>
</html>