<?php
// Enhanced CurrencyService with database integration
class CurrencyService {
    private $pdo;
    private $apiKey = '33b97733263d6274fe556c08';
    private $baseUrl = 'https://v6.exchangerate-api.com/v6/';
    private $cacheDuration = 3600; // 1 hour cache

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getExchangeRate($from = 'USD', $to = 'LKR') {
        $cacheFile = $this->getCacheFilePath($from, $to);
        
        // Check cache first
        if ($this->isCacheValid($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            return $cached['rate'];
        }

        // Try to fetch from database cache
        $dbRate = $this->getFromDatabase($from, $to);
        if ($dbRate && $this->isDatabaseRateValid($dbRate)) {
            $this->cacheRate($cacheFile, $dbRate['rate']);
            return $dbRate['rate'];
        }

        // Fetch new rate from API
        try {
            $rate = $this->fetchFromApi($from, $to);
            $this->cacheRate($cacheFile, $rate);
            $this->saveToDatabase($from, $to, $rate);
            return $rate;
        } catch (Exception $e) {
            error_log("Exchange rate error: " . $e->getMessage());
            return $this->getFallbackRate($from, $to);
        }
    }

    private function fetchFromApi($from, $to) {
        $url = "{$this->baseUrl}{$this->apiKey}/pair/{$from}/{$to}";
        $response = @file_get_contents($url);
        
        if ($response === false) {
            throw new Exception('Failed to fetch exchange rate');
        }

        $data = json_decode($response, true);
        
        if (isset($data['conversion_rate'])) {
            return $data['conversion_rate'];
        }
        
        throw new Exception('Invalid API response');
    }

    private function getFromDatabase($from, $to) {
        $stmt = $this->pdo->prepare("
            SELECT rate, updated_at 
            FROM exchange_rates 
            WHERE from_currency = ? AND to_currency = ? 
            ORDER BY updated_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function isDatabaseRateValid($rateData) {
        $lastUpdate = strtotime($rateData['updated_at']);
        return (time() - $lastUpdate) < $this->cacheDuration;
    }

    private function saveToDatabase($from, $to, $rate) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO exchange_rates (from_currency, to_currency, rate, updated_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                rate = VALUES(rate), 
                updated_at = VALUES(updated_at)
            ");
            $stmt->execute([$from, $to, $rate]);
        } catch (Exception $e) {
            error_log("Database save error: " . $e->getMessage());
        }
    }

    private function getCacheFilePath($from, $to) {
        $cacheDir = __DIR__ . '/../cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        return $cacheDir . "rate_{$from}_{$to}.json";
    }

    private function isCacheValid($cacheFile) {
        return file_exists($cacheFile) && 
               (time() - filemtime($cacheFile) < $this->cacheDuration);
    }

    private function cacheRate($cacheFile, $rate) {
        file_put_contents($cacheFile, json_encode([
            'rate' => $rate,
            'timestamp' => time()
        ]));
    }

    private function getFallbackRate($from, $to) {
        // Updated fallback rates based on recent values
        $fallbackRates = [
            'USD_LKR' => 323.50,
            'LKR_USD' => 0.0031
        ];
        
        return $fallbackRates["{$from}_{$to}"] ?? null;
    }

    // Helper method to convert USD to LKR
    public function convertUsdToLkr($usdAmount) {
        $rate = $this->getExchangeRate('USD', 'LKR');
        return $usdAmount * $rate;
    }

    // Helper method to format price for display
    public function formatPrice($usdPrice, $currency = 'LKR') {
        if ($currency === 'USD') {
            return [
                'currency' => 'USD',
                'amount' => number_format($usdPrice, 2),
                'symbol' => '$'
            ];
        } else {
            $lkrPrice = $this->convertUsdToLkr($usdPrice);
            return [
                'currency' => 'LKR',
                'amount' => number_format($lkrPrice, 2),
                'symbol' => 'Rs.'
            ];
        }
    }
}

// Database migration for exchange rates table
/*
CREATE TABLE exchange_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_currency VARCHAR(3) NOT NULL,
    to_currency VARCHAR(3) NOT NULL,
    rate DECIMAL(10, 6) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_pair (from_currency, to_currency)
);
*/

// Usage example in your index.php
class PriceDisplayHelper {
    private $currencyService;

    public function __construct($currencyService) {
        $this->currencyService = $currencyService;
    }

    public function displayVehiclePrice($vehicle, $preferredCurrency = 'LKR') {
        $usdPrice = $vehicle['usd_price'];
        $priceData = $this->currencyService->formatPrice($usdPrice, $preferredCurrency);
        
        return [
            'display' => "{$priceData['symbol']} {$priceData['amount']}",
            'currency' => $priceData['currency'],
            'amount' => $priceData['amount'],
            'usd_base' => $usdPrice
        ];
    }

    public function displayLocationPrice($location, $preferredCurrency = 'LKR') {
        $usdPrice = $location['usd_price'];
        $priceData = $this->currencyService->formatPrice($usdPrice, $preferredCurrency);
        
        return "{$location['name']} ({$priceData['currency']} {$priceData['amount']})";
    }
}
?>