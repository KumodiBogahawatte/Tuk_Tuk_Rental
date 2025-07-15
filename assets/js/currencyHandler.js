document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency-select');
    let currentExchangeRate = parseFloat(document.querySelector('.vehicle-price')?.getAttribute('data-rate')) || 323.50;
    
    // Pickup charges data (from your document)
    const pickupCharges = {
        'Airport': 0, 'Colombo': 0, 'Negombo': 0, 'Wadduwa office': 0, 'Benthota': 0,
        'Hikkaduwa': 40, 'Galle': 40, 'Unawatuna': 40, 'Weligama': 50, 'Mirissa': 50,
        'Ahangama': 45, 'Matara': 50, 'Tangalle': 50, 'Hiriketiya/Dickwella': 50,
        'Tissa': 60, 'Kataragama': 60, 'Kandy': 50, 'Nuwara-Eliya': 60, 'Ella': 40,
        'Haputale': 50, 'Sigiriya': 60, 'Batticaloa': 60, 'Trincomalee/Nilaweli': 60,
        'Arugam-bay': 60, 'Anuradhapura/Polonnaruwa': 50, 'Kalpitiya': 45, 'Jaffna': 100
    };

    // Duration-based discounts
    const durationDiscounts = [
        { min: 1, max: 4, discount: 30 },
        { min: 5, max: 7, discount: 25 },
        { min: 8, max: 30, discount: 20 },
        { min: 31, max: 60, discount: 16 },
        { min: 61, max: Infinity, discount: 12 }
    ];

    function formatPrice(amount, currency) {
        const formatted = parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currency === 'USD' ? `$${formatted}` : `Rs. ${formatted}`;
    }

    function convertPrice(usdAmount, toCurrency) {
        if (toCurrency === 'USD') {
            return usdAmount;
        }
        return usdAmount * currentExchangeRate;
    }

    function updatePrices(currency) {
        // Update vehicle prices
        document.querySelectorAll('.vehicle-price').forEach(priceElement => {
            const basePrice = parseFloat(priceElement.dataset.price);
            const currencySpan = priceElement.querySelector('.currency');
            const amountSpan = priceElement.querySelector('.amount');
            
            if (!isNaN(basePrice)) {
                const convertedPrice = convertPrice(basePrice, currency);
                currencySpan.textContent = currency;
                amountSpan.textContent = parseFloat(convertedPrice).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
            }
        });

        // Update location datalist prices
        updateDatalistPrices(currency);
        
        // Update any displayed pickup charges
        updatePickupCharges(currency);
    }

    function updateDatalistPrices(currency) {
        document.querySelectorAll('#pickup_locations option, #return_locations option').forEach(option => {
            const basePrice = parseFloat(option.getAttribute('data-price')) || 0;
            const locationName = option.value;
            
            // Add pickup charge if applicable
            const pickupCharge = pickupCharges[locationName] || 0;
            const totalPrice = basePrice + pickupCharge;
            
            const convertedPrice = convertPrice(totalPrice, currency);
            const formattedPrice = formatPrice(convertedPrice, currency);
            
            option.textContent = `${locationName} (${formattedPrice})`;
        });
    }

    function updatePickupCharges(currency) {
        document.querySelectorAll('.pickup-charge').forEach(element => {
            const usdCharge = parseFloat(element.dataset.charge);
            const convertedCharge = convertPrice(usdCharge, currency);
            element.textContent = formatPrice(convertedCharge, currency);
        });
    }

    function calculateDurationDiscount(days) {
        const discount = durationDiscounts.find(d => days >= d.min && days <= d.max);
        return discount ? discount.discount : 0;
    }

    function calculateTotalPrice(vehiclePrice, pickupLocation, returnLocation, days, currency) {
        const pickupCharge = pickupCharges[pickupLocation] || 0;
        const returnCharge = pickupCharges[returnLocation] || 0;
        const durationDiscount = calculateDurationDiscount(days);
        
        const baseTotal = (vehiclePrice * days) + pickupCharge + returnCharge - durationDiscount;
        return convertPrice(baseTotal, currency);
    }

    // Enhanced booking form with price calculation
    function setupBookingForm() {
        const form = document.getElementById('booking-form');
        const pickupLocationInput = document.getElementById('pickup_location');
        const returnLocationInput = document.getElementById('return_location');
        const pickupDateInput = document.querySelector('input[name="pickup_date"]');
        const returnDateInput = document.querySelector('input[name="return_date"]');
        
        function updateBookingPrice() {
            const pickupLocation = pickupLocationInput.value;
            const returnLocation = returnLocationInput.value;
            const pickupDate = new Date(pickupDateInput.value);
            const returnDate = new Date(returnDateInput.value);
            
            if (pickupDate && returnDate && pickupDate < returnDate) {
                const days = Math.ceil((returnDate - pickupDate) / (1000 * 60 * 60 * 24));
                const currency = currencySelect ? currencySelect.value : 'LKR';
                
                // Show estimated price (you can add this element to your form)
                const priceDisplay = document.getElementById('estimated-price');
                if (priceDisplay) {
                    const vehiclePrice = 50; // Default price or get from selected vehicle
                    const total = calculateTotalPrice(vehiclePrice, pickupLocation, returnLocation, days, currency);
                    priceDisplay.textContent = `Estimated Total: ${formatPrice(total, currency)}`;
                }
            }
        }

        if (form) {
            [pickupLocationInput, returnLocationInput, pickupDateInput, returnDateInput].forEach(input => {
                if (input) {
                    input.addEventListener('change', updateBookingPrice);
                }
            });
        }
    }

    // Handle currency changes
    if (currencySelect) {
        currencySelect.addEventListener('change', function() {
            const currency = this.value;
            updatePrices(currency);
            updateLocationInputsWithPrice(currency);
            localStorage.setItem('preferredCurrency', currency);
        });

        // Load saved preference
        const savedCurrency = localStorage.getItem('preferredCurrency');
        if (savedCurrency) {
            currencySelect.value = savedCurrency;
            updatePrices(savedCurrency);
        }
    }

    // Update location inputs with price when changed
    function updateLocationInputsWithPrice(currency) {
        ['pickup_location', 'return_location'].forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                const locationName = input.value.replace(/\s+\(.*?\)$/, '').trim();
                if (locationName && pickupCharges.hasOwnProperty(locationName)) {
                    const pickupCharge = pickupCharges[locationName];
                    const convertedCharge = convertPrice(pickupCharge, currency);
                    const formattedPrice = formatPrice(convertedCharge, currency);
                    input.value = `${locationName} (${formattedPrice})`;
                }
            }
        });
    }

    // Initialize
    const initialCurrency = currencySelect ? currencySelect.value : 'LKR';
    updatePrices(initialCurrency);
    setupBookingForm();
    
    // Auto-update exchange rates every hour
    setInterval(function() {
        fetch('/api/exchange-rate.php')
            .then(response => response.json())
            .then(data => {
                if (data.rate) {
                    currentExchangeRate = data.rate;
                    updatePrices(currencySelect ? currencySelect.value : 'LKR');
                }
            })
            .catch(error => console.log('Exchange rate update failed:', error));
    }, 3600000); // 1 hour
});