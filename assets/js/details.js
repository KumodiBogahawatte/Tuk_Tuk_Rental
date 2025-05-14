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

// Function to fetch Sri Lanka cities
async function fetchSriLankaCities() {
    const pickupSelect = document.getElementById('pickup_location');
    const returnSelect = document.getElementById('return_location');
    
    if (!pickupSelect || !returnSelect) {
        console.log('Select elements not found');
        return;
    }
    
    try {
        const response = await fetch('https://secure.geonames.org/searchJSON?country=LK&featureClass=P&maxRows=1000&username=tuktukrental');
        const data = await response.json();
        
        if (data.geonames && data.geonames.length > 0) {
            const cities = [...new Set(data.geonames.map(city => city.name))].sort();
            cities.forEach(city => {
                pickupSelect.add(new Option(city, city));
                returnSelect.add(new Option(city, city));
            });
            console.log('Cities loaded from API:', cities.length);
        } else {
            throw new Error('No cities found in API response');
        }
    } catch (error) {
        console.log('Error fetching cities, using fallback list:', error);
        // Fallback cities if API fails
        const fallbackCities = [
            'Colombo', 'Kandy', 'Galle', 'Jaffna', 'Negombo', 
            'Kurunegala', 'Anuradhapura', 'Matara', 'Ratnapura', 
            'Badulla', 'Moratuwa', 'Kalutara', 'Batticaloa', 
            'Trincomalee', 'Matale', 'Gampaha', 'Kegalle', 
            'Polonnaruwa', 'Hambantota', 'Ampara'
        ];
        
        fallbackCities.forEach(city => {
            pickupSelect.add(new Option(city, city));
            returnSelect.add(new Option(city, city));
        });
        console.log('Fallback cities loaded:', fallbackCities.length);
    }
}