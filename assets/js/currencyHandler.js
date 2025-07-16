document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency-select');
    const vehiclePrices = document.querySelectorAll('.vehicle-price');
    function updatePrices(currency) {
        document.querySelectorAll('.vehicle-price').forEach(priceElement => {
            const basePrice = parseFloat(priceElement.dataset.price);
            const rate = parseFloat(priceElement.dataset.rate);
            const currencySpan = priceElement.querySelector('.currency');
            const amountSpan = priceElement.querySelector('.amount');
            if (currency === 'USD') {
                currencySpan.textContent = 'USD';
                amountSpan.textContent = isNaN(basePrice) ? '' : basePrice.toLocaleString('en-US', {minimumFractionDigits: 2});
            } else {
                currencySpan.textContent = 'LKR';
                amountSpan.textContent = isNaN(basePrice) ? '' : (basePrice * rate).toLocaleString('en-US', {minimumFractionDigits: 2});
            }
        });
    }

    // Handle currency changes
    if (currencySelect) {
        currencySelect.addEventListener('change', function() {
            const currency = this.value;
            updatePrices(currency);
            // Get USD rate from any .vehicle-price element or from a global variable
            const usdRate = parseFloat(document.querySelector('.vehicle-price')?.getAttribute('data-rate')) || 0;
            updateDatalistPrices(currency, usdRate);
            updateLocationInputsWithPrice(currency, usdRate);
            localStorage.setItem('preferredCurrency', currency);
        });

        // Load saved preference on page load
        const savedCurrency = localStorage.getItem('preferredCurrency');
        if (savedCurrency) {
            currencySelect.value = savedCurrency;
            updatePrices(savedCurrency);
        }

        // Always sync datalist prices on load
        const currency = currencySelect.value;
        const usdRate = parseFloat(document.querySelector('.vehicle-price')?.getAttribute('data-rate')) || 0;
        updateDatalistPrices(currency, usdRate);
        updateLocationInputsWithPrice(currency, usdRate);
    }
});

function updateDatalistPrices(currency, usdRate) {
    document.querySelectorAll('#pickup_locations option, #return_locations option').forEach(option => {
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        if (currency === 'USD') {
            option.textContent = `${option.value} (USD ${price.toLocaleString('en-US', {minimumFractionDigits: 2})})`;
        } else {
            option.textContent = `${option.value} (LKR ${(price * usdRate).toLocaleString('en-US', {minimumFractionDigits: 2})})`;
        }
    });
}

function updateLocationInputsWithPrice(currency, usdRate) {
    [
        {inputId: 'pickup_location', datalistId: 'pickup_locations'},
        {inputId: 'return_location', datalistId: 'return_locations'}
    ].forEach(({inputId, datalistId}) => {
        const input = document.getElementById(inputId);
        const datalist = document.getElementById(datalistId);
        if (!input || !datalist) return;
        const val = input.value.replace(/\s+\(.*?\)$/, '').trim();
        datalist.querySelectorAll('option').forEach(option => {
            if (option.value === val) {
                // Compose new price text based on updated datalist option
                const priceText = option.textContent.match(/\((.*?)\)$/);
                if (priceText) {
                    input.value = `${val} ${priceText[0]}`;
                }
            }
        });
    });
}