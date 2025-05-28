// Phone Stack Animation
document.addEventListener('DOMContentLoaded', function () {
    const backPhone = document.querySelector('.back-phone');
    const frontPhone = document.querySelector('.front-phone');
    const phoneStack = document.querySelector('.phone-stack');
  
    if (backPhone && frontPhone && phoneStack) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    backPhone.classList.add('visible');
                    frontPhone.classList.add('visible');
                }
            });
        }, { threshold: 0.3 });
    
        observer.observe(phoneStack);
    }
});  

// Navbar Scroll Effect
window.addEventListener("scroll", function () {
    const navbar = document.querySelector(".navbar");
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Initialize accordions
document.addEventListener('DOMContentLoaded', function() {
    var accordionItems = document.querySelectorAll('.accordion-button');
    accordionItems.forEach(function(item) {
        item.addEventListener('click', function() {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            var target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                if (expanded) {
                    target.classList.remove('show');
                } else {
                    target.classList.add('show');
                }
            }
        });
    });
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

// Initialize Date and Time Pickers
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr(".date-input", {
        dateFormat: "d/m/Y",
        minDate: "today",
        disableMobile: "true"
    });

    // Initialize time pickers
    flatpickr(".time-input", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
        disableMobile: "true"
    });
});

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
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

// Index page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize booking form validation
    const bookingForm = document.querySelector('form[action="availability.php"]');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const pickupDate = this.querySelector('[name="pickup_date"]').value;
            const returnDate = this.querySelector('[name="return_date"]').value;
            
            if (new Date(returnDate) < new Date(pickupDate)) {
                e.preventDefault();
                alert('Return date cannot be earlier than pickup date');
            }
        });
    }

    // Fetch and populate locations
    fetchSriLankaCities();
    
    // Initialize phone stack animation
    const phoneStack = document.querySelector('.phone-stack');
    if (phoneStack) {
        gsap.from('.back-phone', {
            y: 50,
            opacity: 0,
            duration: 1,
            ease: 'power2.out'
        });
        
        gsap.from('.front-phone', {
            y: 30,
            opacity: 0,
            duration: 1,
            delay: 0.3,
            ease: 'power2.out'
        });
    }
});

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

// Smooth Scroll
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Show or hide the button
window.addEventListener("scroll", function () {
    const topButton = document.querySelector(".back-to-top");
    if (window.scrollY > 300) {
    topButton.style.display = "block";
    } else {
    topButton.style.display = "none";
    }
});

// Smooth scroll to top
document.querySelector(".back-to-top").addEventListener("click", function (e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
});
