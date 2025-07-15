// Enhanced Navbar Functionality
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;
    
    // Scroll effect
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
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

