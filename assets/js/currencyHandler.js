// Global object to store pricing data and exchange rate
window.tuktukPricing = {
    exchangeRate: 1, // Default LKR to USD rate (will be fetched)
    currencySymbol: 'LKR',
    pickupCharges: {}, // Fetched from API
    extras: {},       // Fetched from API
    durationDiscounts: {}, // Fetched from API
};

document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency-select');
    
    // Function to format prices
    function formatPrice(amount, currency) {
        if (isNaN(amount) || amount === null) return '';
        const formatted = parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currency === 'USD' ? `$${formatted}` : `Rs. ${formatted}`;
    }

    // Function to convert USD to current selected currency
    function convertUsdToCurrent(usdAmount) {
        if (window.tuktukPricing.currencySymbol === 'USD') {
            return usdAmount;
        }
        return usdAmount * window.tuktukPricing.exchangeRate;
    }

    // Main function to update all prices on the page
    function updateAllPricesOnPage() {
        const currentCurrency = window.tuktukPricing.currencySymbol;

        // Update elements with data-price-usd attributes
        document.querySelectorAll('[data-price-usd]').forEach(element => {
            const usdPrice = parseFloat(element.dataset.priceUsd);
            if (!isNaN(usdPrice)) {
                const convertedPrice = convertUsdToCurrent(usdPrice);
                // Check if the element contains a currency span and an amount span
                const currencySpan = element.querySelector('.currency');
                const amountSpan = element.querySelector('.amount');

                if (currencySpan && amountSpan) {
                    currencySpan.textContent = currentCurrency;
                    amountSpan.textContent = parseFloat(convertedPrice).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    });
                } else {
                    // For single span elements, just update text content directly
                    element.textContent = formatPrice(convertedPrice, currentCurrency);
                }
            }
        });

        // Update location datalist options
        document.querySelectorAll('#pickup_locations option, #return_locations option').forEach(option => {
            const locationName = option.value;
            const usdCharge = window.tuktukPricing.pickupCharges[locationName] || 0;
            const convertedCharge = convertUsdToCurrent(usdCharge);
            option.textContent = `${locationName} (${formatPrice(convertedCharge, currentCurrency)})`;
        });
    }

    // Fetch initial exchange rate and pricing data
    async function fetchPricingData() {
        try {
            // Fetch exchange rate
            const rateResponse = await fetch('/api/exchange-rate.php');
            const rateData = await rateResponse.json();
            if (rateData.rate) {
                window.tuktukPricing.exchangeRate = parseFloat(rateData.rate);
            } else {
                console.error("Failed to fetch exchange rate:", rateData.error);
            }

            // Fetch all pricing data from a new API endpoint (you'll need to create this)
            const pricingResponse = await fetch('/api/pricing-data.php'); // <-- NEW API ENDPOINT
            const pricingData = await pricingResponse.json();

            if (pricingData.success) {
                // Map array of objects to object for easier lookup
                pricingData.pickupCharges.forEach(item => {
                    window.tuktukPricing.pickupCharges[item.location_name] = parseFloat(item.charge_usd);
                });
                pricingData.extras.forEach(item => {
                    window.tuktukPricing.extras[item.id] = { name: item.name, price_usd: parseFloat(item.price_usd) };
                });
                pricingData.durationDiscounts = pricingData.durationDiscounts.map(item => ({
                    min_days: parseInt(item.min_days),
                    max_days: item.max_days ? parseInt(item.max_days) : Infinity,
                    discount_usd: parseFloat(item.discount_usd)
                }));
            } else {
                console.error("Failed to fetch pricing data:", pricingData.error);
            }

            // After fetching all data, update prices on the page
            updateAllPricesOnPage();

        } catch (error) {
            console.error("Error fetching pricing data:", error);
            // Use fallback values if API fails
            // (You might want to hardcode some fallback rates/charges if the API is critical)
        }
    }

    // Initialize currency and fetch data
    if (currencySelect) {
        const savedCurrency = localStorage.getItem('preferredCurrency');
        if (savedCurrency) {
            currencySelect.value = savedCurrency;
            window.tuktukPricing.currencySymbol = savedCurrency;
        } else {
            window.tuktukPricing.currencySymbol = currencySelect.value;
        }

        currencySelect.addEventListener('change', function() {
            window.tuktukPricing.currencySymbol = this.value;
            localStorage.setItem('preferredCurrency', this.value);
            updateAllPricesOnPage();
        });
    } else {
        // If no currency select, default to LKR
        window.tuktukPricing.currencySymbol = 'LKR';
    }

    // Call fetch data on page load
    fetchPricingData();

    // Auto-update exchange rates and pricing data every hour
    setInterval(fetchPricingData, 3600000); // 1 hour

    // Expose utility functions globally for other scripts (like details.js)
    window.tuktukUtils = {
        formatPrice: formatPrice,
        convertUsdToCurrent: convertUsdToCurrent,
        getPricingData: () => window.tuktukPricing // Allow other scripts to read pricing data
    };
});