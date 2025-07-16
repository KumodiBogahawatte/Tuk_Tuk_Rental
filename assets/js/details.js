// Enhanced thumbnail interaction
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumb-img');
    const mainImage = document.getElementById('mainImage');
    
    if (!mainImage || !thumbnails.length) return;

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Smooth transition for main image
            mainImage.style.opacity = '0';
            setTimeout(() => {
                mainImage.src = this.src;
                mainImage.style.opacity = '1';
            }, 300);
        });
    });
});

// Function to change main image when clicking thumbnails
function changeImage(img) {
    const mainImage = document.getElementById('mainImage');
    mainImage.style.opacity = '0';
    setTimeout(() => {
        mainImage.src = img.src;
        mainImage.style.opacity = '1';
    }, 300);
    
    // Update active state of thumbnails
    document.querySelectorAll('.thumb-img').forEach(thumb => {
        thumb.classList.remove('active');
    });
    img.classList.add('active');
}

// Function to open reservation modal
function openReservationModal() {
    const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
    modal.show();
}

// Initialize date and time pickers
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr(".date-input", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: "true"
    });

    // Initialize time pickers
    flatpickr(".time-input", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        disableMobile: "true"
    });

    // Fetch and populate locations
    fetchSriLankaCities();
});

// Location Selection Handlers
document.addEventListener('DOMContentLoaded', function() {
    const pickupLocation = document.getElementById('pickup_location');
    const returnLocation = document.getElementById('return_location');

    if (pickupLocation) {
        pickupLocation.addEventListener('change', function() {
            console.log("Pickup location selected:", this.value);
        });
    }

    if (returnLocation) {
        returnLocation.addEventListener('change', function() {
            console.log("Return location selected:", this.value);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Make sure tuktukUtils is loaded from currencyHandler.js
    if (typeof window.tuktukUtils === 'undefined') {
        console.error('tuktukUtils not loaded. Ensure currencyHandler.js is loaded before details.js');
        return;
    }

    const { formatPrice, convertUsdToCurrent, getPricingData } = window.tuktukUtils;

    // Enhanced thumbnail interaction (Existing code - no change needed for pricing)
    const thumbnails = document.querySelectorAll('.thumb-img');
    const mainImage = document.getElementById('mainImage');
    
    if (mainImage && thumbnails.length) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.src = this.src;
                    mainImage.style.opacity = '1';
                }, 300);
            });
        });
    }

    // Function to open reservation modal (Existing code - no change needed for pricing)
    window.openReservationModal = function() { // Expose globally for onclick
        const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
        modal.show();
    };

    // Initialize date and time pickers (Existing code - no change needed for pricing)
    flatpickr(".date-input", {
        dateFormat: "d/m/Y",
        minDate: "today",
        disableMobile: "true"
    });
    flatpickr(".time-input", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        disableMobile: "true"
    });

    // Pricing calculation and display within the modal
    const reservationForm = document.getElementById('reservationForm');
    const pickupLocationInput = document.getElementById('pickup_location');
    const returnLocationInput = document.getElementById('return_location');
    const pickupDateInput = reservationForm.querySelector('input[name="pickup_date"]');
    const returnDateInput = reservationForm.querySelector('input[name="return_date"]');
    const pickupTimeInput = reservationForm.querySelector('input[name="pickup_time"]');
    const returnTimeInput = reservationForm.querySelector('input[name="return_time"]');
    const extraCheckboxes = reservationForm.querySelectorAll('.extra-checkbox');

    // Function to calculate rental days
    function calculateRentalDays(pickupDateStr, returnDateStr) {
        const pickupDate = parseDateString(pickupDateStr);
        const returnDate = parseDateString(returnDateStr);

        if (!pickupDate || !returnDate || pickupDate > returnDate) {
            return 0;
        }
        const diffTime = Math.abs(returnDate.getTime() - pickupDate.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays + 1; // Include both start and end day
    }

    function parseDateString(dateStr) {
        const parts = dateStr.split('/');
        // Ensure month is 0-indexed for Date constructor
        if (parts.length === 3) {
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }
        return null;
    }

    // Function to calculate duration discount
    function getDurationDiscountUSD(rentalDays) {
        const durationDiscounts = getPricingData().durationDiscounts;
        const discountRule = durationDiscounts.find(rule =>
            rentalDays >= rule.min_days && (rule.max_days === Infinity || rentalDays <= rule.max_days)
        );
        return discountRule ? discountRule.discount_usd : 0;
    }

    // Function to dynamically update costs
    function updateCostDisplays() {
        const pricingData = getPricingData();
        const currentCurrency = pricingData.currencySymbol;
        
        // Update vehicle daily price
        document.querySelectorAll('.vehicle-price').forEach(element => {
            const usdPrice = parseFloat(element.dataset.priceUsd || phpVehicleDailyRate);
            element.querySelector('.currency').textContent = currentCurrency;
            element.querySelector('.amount').textContent = formatPrice(convertUsdToCurrent(usdPrice), currentCurrency).replace(/[^0-9.,]/g, '');
        });

        // Update license fee display
        document.querySelectorAll('.license-fee-display').forEach(element => {
            const usdPrice = parseFloat(element.dataset.priceUsd || phpLicenseFee);
            element.textContent = formatPrice(convertUsdToCurrent(usdPrice), currentCurrency);
        });

        // Update extra prices
        document.querySelectorAll('.extra-price-display').forEach(element => {
            const usdPrice = parseFloat(element.dataset.priceUsd);
            element.textContent = `(${formatPrice(convertUsdToCurrent(usdPrice), currentCurrency)})`;
        });

        // Update location input prices (if they are displayed there)
        const pickupLocationName = pickupLocationInput.value;
        const returnLocationName = returnLocationInput.value;

        const pickupChargeUSD = pricingData.pickupCharges[pickupLocationName] || 0;
        const returnChargeUSD = pricingData.pickupCharges[returnLocationName] || 0;

        document.getElementById('pickup-location-info').textContent = pickupLocationName ? `(${formatPrice(convertUsdToCurrent(pickupChargeUSD), currentCurrency)})` : '';
        document.getElementById('return-location-info').textContent = returnLocationName ? `(${formatPrice(convertUsdToCurrent(returnChargeUSD), currentCurrency)})` : '';
    }

    // Event listeners for form fields that affect pricing
    if (reservationForm) {
        [pickupLocationInput, returnLocationInput, pickupDateInput, returnDateInput,
         pickupTimeInput, returnTimeInput].forEach(input => {
            input.addEventListener('change', updateCostDisplays);
        });

        extraCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCostDisplays);
        });
    }

    // Initial update when modal is opened (or on page load if not a modal)
    document.getElementById('reservationModal').addEventListener('shown.bs.modal', updateCostDisplays);

    // Also update on currency change from main handler
    document.addEventListener('currencyUpdated', updateCostDisplays);

    // Initial update on page load for general prices outside modal
    updateCostDisplays();

    // Datalist input handling
    function setupLocationInput(inputId, datalistId) {
        const input = document.getElementById(inputId);
        const datalist = document.getElementById(datalistId);

        input.addEventListener('input', function() {
            // Remove any existing price info if user types
            const cleanValue = this.value.split(' (')[0];
            this.value = cleanValue;
            updateCostDisplays(); // Re-evaluate on input
        });

        input.addEventListener('change', function() {
            // Re-add price info if a valid selection is made
            const locationName = this.value;
            const pricingData = getPricingData();
            if (pricingData.pickupCharges[locationName] !== undefined) {
                const usdCharge = pricingData.pickupCharges[locationName];
                this.value = `${locationName} (${formatPrice(convertUsdToCurrent(usdCharge), pricingData.currencySymbol)})`;
            }
            updateCostDisplays();
        });
    }

    setupLocationInput('pickup_location', 'pickup_locations');
    setupLocationInput('return_location', 'return_locations');

    // Populate datalists dynamically (if not already done by PHP)
    // This part assumes phpLocations is passed from PHP
    if (typeof phpLocations !== 'undefined' && phpLocations.length > 0) {
        const pickupDatalist = document.getElementById('pickup_locations');
        const returnDatalist = document.getElementById('return_locations');

        phpLocations.forEach(loc => {
            const option = document.createElement('option');
            option.value = loc.name;
            // The currencyHandler.js updateAllPricesOnPage will correctly format this
            pickupDatalist.appendChild(option);
            returnDatalist.appendChild(option.cloneNode(true));
        });
        updateCostDisplays(); // Ensure prices are displayed after populating
    }
});