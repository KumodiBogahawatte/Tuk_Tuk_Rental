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
// Enhanced Navbar Functionality - Fixed sticky behavior
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;
    
    // Ensure navbar is properly positioned
    navbar.style.position = 'sticky';
    navbar.style.top = '0';
    navbar.style.zIndex = '1030';
    
    // Improved scroll effect with throttling for better performance
    let ticking = false;
    
    function updateNavbar() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    });
    
    // Detect background brightness and adapt navbar
    function adaptNavbarToBackground() {
        const hero = document.querySelector('.hero, .banner, .bg-image, [style*="background"]');
        
        if (hero) {
            const heroStyles = window.getComputedStyle(hero);
            const bgColor = heroStyles.backgroundColor;
            const bgImage = heroStyles.backgroundImage;
            
            // If there's a background image, assume it might be colorful
            if (bgImage && bgImage !== 'none') {
                navbar.classList.add('glass-effect');
                // You can add more sophisticated color detection here
                return;
            }
            
            // Simple brightness detection for solid colors
            if (bgColor && bgColor !== 'rgba(0, 0, 0, 0)') {
                const rgb = bgColor.match(/\d+/g);
                if (rgb) {
                    const brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
                    
                    if (brightness < 128) {
                        navbar.classList.add('dark-variant');
                        navbar.classList.remove('bright-variant');
                    } else {
                        navbar.classList.add('bright-variant');
                        navbar.classList.remove('dark-variant');
                    }
                }
            }
        }
    }
    
    // Call adaptation function
    adaptNavbarToBackground();
    
    // Re-adapt on window resize
    window.addEventListener('resize', adaptNavbarToBackground);
    
    // Enhanced mobile menu functionality
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            // Add animation classes
            setTimeout(() => {
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.style.animation = 'slideDown 0.3s ease-out forwards';
                }
            }, 10);
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = navbar.contains(event.target);
        const isNavOpen = navbarCollapse && navbarCollapse.classList.contains('show');
        
        if (!isClickInsideNav && isNavOpen) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: true
            });
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('.nav-link[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - navbar.offsetHeight;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
});

// Additional utility functions
function setNavbarVariant(variant) {
    const navbar = document.querySelector('.navbar');
    
    // Remove all variant classes
    navbar.classList.remove('glass-effect', 'dark-variant', 'bright-variant');
    
    // Add the specified variant
    if (variant) {
        navbar.classList.add(variant);
    }
}

// Usage examples:
// setNavbarVariant('dark-variant') - for dark backgrounds
// setNavbarVariant('bright-variant') - for bright backgrounds  
// setNavbarVariant('glass-effect') - for colorful/image backgrounds

// CSS animations for mobile menu
const style = document.createElement('style');
style.textContent = `
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}
`;
document.head.appendChild(style);

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
        mirror: false,
        anchorPlacement: 'top-bottom',
        offset: 120,
        delay: 0,
    });
});

// Refresh AOS on dynamic content changes
document.addEventListener('DOMContentLoaded', function() {
    AOS.refresh();
});

// Optional: Refresh AOS when images are loaded
window.addEventListener('load', function() {
    AOS.refresh();
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

// Back to Top Button
document.addEventListener('DOMContentLoaded', function() {
  const backToTopButton = document.querySelector('.back-to-top');
  
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      backToTopButton.classList.add('visible');
    } else {
      backToTopButton.classList.remove('visible');
    }
  });
  
  backToTopButton.addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});

//Show Price in the Input
document.addEventListener('DOMContentLoaded', function() {
    function setupLocationInputWithPrice(inputId, datalistId) {
        const input = document.getElementById(inputId);
        const datalist = document.getElementById(datalistId);

        input.addEventListener('change', function() {
            const val = input.value.trim();
            let found = false;
            datalist.querySelectorAll('option').forEach(option => {
                if (option.value === val) {
                    // Extract price and currency from option text
                    const priceText = option.textContent.match(/\((.*?)\)$/);
                    if (priceText) {
                        input.value = `${val} ${priceText[0]}`;
                    }
                    found = true;
                }
            });
            if (!found) {
                // If not found, keep only the typed value
                input.value = val;
            }
        });

        // Optional: Remove price when editing
        input.addEventListener('input', function() {
            // Remove price part if user starts editing
            const val = input.value;
            input.value = val.replace(/\s+\(.*?\)$/, '');
        });
    }

    setupLocationInputWithPrice('pickup_location', 'pickup_locations');
    setupLocationInputWithPrice('return_location', 'return_locations');
});

// Enhanced counter animation with controlled speed
document.addEventListener('DOMContentLoaded', function() {
    const startCountingWhenVisible = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                const speed = +counter.getAttribute('data-speed') || 200; // Default speed if not specified
                const duration = target / speed * 1000; // Calculate duration in ms
                
                animateCounter(counter, target, duration);
                observer.unobserve(counter);
            }
        });
    };

    const animateCounter = (element, target, duration) => {
        const start = 0;
        const startTime = performance.now();
        
        const updateCounter = (currentTime) => {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);
            const currentValue = Math.floor(progress * target);
            
            element.textContent = currentValue.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        };
        
        requestAnimationFrame(updateCounter);
    };

    const observer = new IntersectionObserver(startCountingWhenVisible, {
        threshold: 0.5 // Start when 50% of the element is visible
    });

    document.querySelectorAll('.counter').forEach(counter => {
        observer.observe(counter);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('booking-form');
    if (!form) return;

    // Helper to get allowed locations from a datalist
    function getAllowedLocations(listId) {
        return Array.from(document.querySelectorAll(`#${listId} option`)).map(opt => opt.value.trim().toLowerCase());
    }

    // Helper to extract only the location name (before any price or parentheses)
    function extractLocationName(val) {
        return val.split(' (')[0].trim().toLowerCase();
    }

    const allowedPickupLocations = getAllowedLocations('pickup_locations');
    const allowedReturnLocations = getAllowedLocations('return_locations');

    form.addEventListener('submit', function(e) {
        const pickupLocation = form.querySelector('[name="pickup_location"]');
        const returnLocation = form.querySelector('[name="return_location"]');
        const pickupDate = form.querySelector('[name="pickup_date"]');
        const pickupTime = form.querySelector('[name="pickup_time"]');
        const returnDate = form.querySelector('[name="return_date"]');
        const returnTime = form.querySelector('[name="return_time"]');

        let message = '';
        if (!pickupLocation.value.trim()) {
            message = 'Please select a pick-up location.';
        } else if (!allowedPickupLocations.includes(extractLocationName(pickupLocation.value))) {
            message = 'Please select a valid pick-up location from the list.';
        } else if (!pickupDate.value.trim()) {
            message = 'Please select a pick-up date.';
        } else if (!pickupTime.value.trim()) {
            message = 'Please select a pick-up time.';
        } else if (!returnLocation.value.trim()) {
            message = 'Please select a return location.';
        } else if (!allowedReturnLocations.includes(extractLocationName(returnLocation.value))) {
            message = 'Please select a valid return location from the list.';
        } else if (!returnDate.value.trim()) {
            message = 'Please select a return date.';
        } else if (!returnTime.value.trim()) {
            message = 'Please select a return time.';
        }

        if (message) {
            e.preventDefault();
            alert(message);
        }
    });
});