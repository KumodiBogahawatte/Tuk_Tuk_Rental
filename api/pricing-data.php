<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';
require_once '../service/CurrencyService.php'; // Required for ReservationService constructor
require_once '../service/ReservationService.php';

$currencyService = new CurrencyService($pdo); // Still needed
$reservationService = new ReservationService($pdo, $currencyService); // Using the service

try {
    $pickupCharges = $pdo->query("SELECT location_name, charge_usd FROM pickup_charges")->fetchAll(PDO::FETCH_ASSOC);
    $extras = $reservationService->getAllExtras();
    $durationDiscounts = $pdo->query("SELECT min_days, max_days, discount_usd FROM duration_pricing")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'pickupCharges' => $pickupCharges,
        'extras' => $extras,
        'durationDiscounts' => $durationDiscounts
    ]);

} catch (Exception $e) {
    error_log("Error fetching pricing data: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve pricing data.']);
}
?>