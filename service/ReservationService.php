<?php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/CurrencyService.php';

class ReservationService {
    private $pdo;
    private $currencyService;

    public function __construct(PDO $pdo, CurrencyService $currencyService) {
        $this->pdo = $pdo;
        $this->currencyService = $currencyService;
    }

    // --- Data Fetching Methods ---

    public function getVehicleById(int $vehicleId) {
        $stmt = $this->pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt->execute([$vehicleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPickupChargeByLocation(string $locationName) {
        $stmt = $this->pdo->prepare("SELECT charge_usd FROM pickup_charges WHERE location_name = ?");
        $stmt->execute([$locationName]);
        return $stmt->fetchColumn() ?: 0.00;
    }

    public function getAllExtras() {
        $stmt = $this->pdo->query("SELECT id, name, price_usd, description FROM extras WHERE is_active = TRUE");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExtraById(int $extraId) {
        $stmt = $this->pdo->prepare("SELECT id, name, price_usd FROM extras WHERE id = ? AND is_active = TRUE");
        $stmt->execute([$extraId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDurationDiscount(int $rentalDays) {
        $stmt = $this->pdo->prepare("
            SELECT discount_usd 
            FROM duration_pricing 
            WHERE min_days <= ? AND (max_days >= ? OR max_days IS NULL)
            ORDER BY min_days DESC LIMIT 1
        ");
        $stmt->execute([$rentalDays, $rentalDays]);
        return $stmt->fetchColumn() ?: 0.00;
    }

    public function getPaymentMethods() {
        $stmt = $this->pdo->query("SELECT * FROM payment_methods WHERE is_active = TRUE");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Calculation Methods (All prices are in USD for internal calculation) ---

    /**
     * Calculates the total cost breakdown for a reservation.
     * All calculations are done in USD internally for accuracy.
     *
     * @param array $vehicle Vehicle data from DB (must have 'price_per_day', 'license_fee_usd')
     * @param string $pickupLocationName Name of the pickup location
     * @param string $returnLocationName Name of the return location
     * @param int $rentalDays Number of rental days
     * @param array $selectedExtraIds Array of extra IDs selected by the user
     * @param string $pickupTime Time of pickup (e.g., "18:30")
     * @param string $returnTime Time of return (e.g., "05:00")
     * @return array Calculated pricing breakdown (all values in USD)
     */
    public function calculatePricing(
        array $vehicle,
        string $pickupLocationName,
        string $returnLocationName,
        int $rentalDays,
        array $selectedExtraIds = [],
        string $pickupTime = '12:00', // Default if not provided
        string $returnTime = '12:00'  // Default if not provided
    ): array {
        $dailyRateUSD = (float)$vehicle['price_per_day'];
        $licenseFeeUSD = (float)$vehicle['license_fee_usd'];

        // 1. Base Rental Cost
        $baseRentalCost = $dailyRateUSD * $rentalDays;

        // 2. Pickup and Return Charges
        $pickupChargeUSD = $this->getPickupChargeByLocation($pickupLocationName);
        $returnChargeUSD = $this->getPickupChargeByLocation($returnLocationName);
        $locationFeesUSD = $pickupChargeUSD + $returnChargeUSD;

        // 3. Duration Discount
        $durationDiscountUSD = $this->getDurationDiscount($rentalDays);
        $rentalAfterDiscount = $baseRentalCost - $durationDiscountUSD;
        if ($rentalAfterDiscount < 0) $rentalAfterDiscount = 0; // Prevent negative rental cost

        // 4. Extras Cost
        $extrasCostUSD = 0;
        $extrasDetails = [];
        foreach ($selectedExtraIds as $extraId) {
            $extra = $this->getExtraById($extraId);
            if ($extra) {
                $extrasCostUSD += (float)$extra['price_usd'];
                $extrasDetails[] = ['name' => $extra['name'], 'price_usd' => (float)$extra['price_usd']];
            }
        }

        // 5. Night Time Charges (Flat fee, assuming per rental, or per day, adjust as needed)
        // From document: "Night time charges 6.00 pm to 6.00 am"
        // This suggests a flat fee if any part of the rental duration falls within this night window.
        // For simplicity, let's assume it's applied if EITHER pickup OR return falls in the night window.
        // A more complex logic would involve iterating through each day of rental.
        $nightChargeUSD = 0;
        $nightTimeExtra = $this->getExtraByName('Night time charges'); // Assuming this extra exists
        if ($nightTimeExtra) {
            $pickupHour = (int)substr($pickupTime, 0, 2);
            $returnHour = (int)substr($returnTime, 0, 2);

            // Check if pickup is between 6 PM (18) and 6 AM (6)
            $isPickupNight = ($pickupHour >= 18 || $pickupHour < 6);
            // Check if return is between 6 PM (18) and 6 AM (6)
            $isReturnNight = ($returnHour >= 18 || $returnHour < 6);

            // Apply night charge if any part of the rental involves night hours.
            // A more robust solution might count how many nights are actually involved.
            // For now, if the rental includes any part that triggers the night clause, apply it once.
            if ($isPickupNight || $isReturnNight) {
                $nightChargeUSD = (float)$nightTimeExtra['price_usd'];
            }
        }


        // 6. Deposit (Flat rate, usually not part of the 'total price' for payment but noted)
        $depositUSD = 5000 / $this->currencyService->getExchangeRate('LKR', 'USD'); // Assuming 5000 LKR deposit

        // Calculate Subtotal (excluding deposit)
        $subtotalUSD = $rentalAfterDiscount + $locationFeesUSD + $extrasCostUSD + $nightChargeUSD + $licenseFeeUSD;

        // Total amount due (Subtotal + Deposit)
        $totalAmountDueUSD = $subtotalUSD + $depositUSD;


        return [
            'daily_rate_usd' => $dailyRateUSD,
            'rental_days' => $rentalDays,
            'base_rental_cost_usd' => $baseRentalCost,
            'duration_discount_usd' => $durationDiscountUSD,
            'rental_after_discount_usd' => $rentalAfterDiscount,
            'pickup_charge_usd' => $pickupChargeUSD,
            'return_charge_usd' => $returnChargeUSD,
            'location_fees_usd' => $locationFeesUSD,
            'license_fee_usd' => $licenseFeeUSD,
            'extras_cost_usd' => $extrasCostUSD,
            'extras_details' => $extrasDetails,
            'night_charge_usd' => $nightChargeUSD,
            'subtotal_usd' => $subtotalUSD, // Total before deposit
            'deposit_usd' => $depositUSD,
            'total_amount_due_usd' => $totalAmountDueUSD, // Subtotal + Deposit
        ];
    }

    /**
     * Helper to get an extra by name (e.g., "Night time charges")
     * @param string $name
     * @return array|false
     */
    public function getExtraByName(string $name) {
        $stmt = $this->pdo->prepare("SELECT id, name, price_usd FROM extras WHERE name = ? AND is_active = TRUE");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}