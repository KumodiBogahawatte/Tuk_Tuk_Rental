// Global variables
var currentPage = 1;
var totalPages = 6;
var isFlipping = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeGuidebook();
    initializeDropdowns();
    initializeAnimations();
    initializeFormValidation();
});

// Initialize guidebook functionality
function initializeGuidebook() {
    var modal = document.getElementById('guidebook-modal');
    var triggerBtn = document.querySelector('.guidebook-trigger');
    var closeBtn = document.getElementById('close-guidebook');
    var prevBtn = document.getElementById('prev-page');
    var nextBtn = document.getElementById('next-page');
    var currentPageSpan = document.getElementById('current-page');
    var totalPagesSpan = document.getElementById('total-pages');
    
    // Set initial values
    if (currentPageSpan) currentPageSpan.textContent = currentPage;
    if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
    
    // Open book
    if (triggerBtn) {
        triggerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openBook();
        });
    }
    
    // Close book
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeBook();
        });
    }
    
    // Close when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeBook();
            }
        });
    }
    
    // Navigation buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            previousPage();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            nextPage();
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (modal && modal.style.display === 'block') {
            switch(e.key) {
                case 'Escape':
                    closeBook();
                    break;
                case 'ArrowLeft':
                    previousPage();
                    break;
                case 'ArrowRight':
                    nextPage();
                    break;
            }
        }
    });
    
    updateNavigation();
}

// Open book function
function openBook() {
    var modal = document.getElementById('guidebook-modal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        var book = document.querySelector('.book');
        if (book) {
            book.style.transform = 'rotateY(0deg) scale(1)';
            book.style.opacity = '1';
        }
    }
}

// Close book function
function closeBook() {
    var modal = document.getElementById('guidebook-modal');
    var book = document.querySelector('.book');
    
    if (book) {
        book.style.transform = 'rotateY(-15deg) scale(0.9)';
        book.style.opacity = '0';
    }
    
    setTimeout(function() {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }, 300);
}

// Next page function
function nextPage() {
    if (currentPage < totalPages && !isFlipping) {
        flipPage(currentPage + 1);
    }
}

// Previous page function
function previousPage() {
    if (currentPage > 1 && !isFlipping) {
        flipPage(currentPage - 1);
    }
}

// Flip page function
function flipPage(targetPage) {
    isFlipping = true;
    
    var currentSpread = document.getElementById('page-' + currentPage);
    var targetSpread = document.getElementById('page-' + targetPage);
    
    if (currentSpread) {
        currentSpread.classList.remove('active');
        
        // Add flip animation
        var pages = currentSpread.querySelectorAll('.page');
        pages.forEach(function(page, index) {
            setTimeout(function() {
                page.classList.add('page-flip');
            }, index * 100);
        });
    }
    
    // Show target page after flip animation
    setTimeout(function() {
        if (targetSpread) {
            targetSpread.classList.add('active');
        }
        currentPage = targetPage;
        updateNavigation();
        
        // Remove flip class
        if (currentSpread) {
            var pages = currentSpread.querySelectorAll('.page');
            pages.forEach(function(page) {
                page.classList.remove('page-flip');
            });
        }
        
        isFlipping = false;
    }, 400);
}

// Update navigation buttons
function updateNavigation() {
    var currentPageSpan = document.getElementById('current-page');
    var prevBtn = document.getElementById('prev-page');
    var nextBtn = document.getElementById('next-page');
    
    if (currentPageSpan) {
        currentPageSpan.textContent = currentPage;
    }
    
    if (prevBtn) {
        prevBtn.disabled = currentPage === 1;
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentPage === totalPages;
    }
}

// Initialize dropdown functionality
function initializeDropdowns() {
    // This function is called from HTML onclick
    window.toggleDropdown = function(dropdownId) {
        var dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        
        var button = dropdown.previousElementSibling;
        if (!button) return;
        
        var icon = button.querySelector('i');
        
        dropdown.classList.toggle('show');
        
        if (icon) {
            if (dropdown.classList.contains('show')) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }
    };
}

// Initialize animations
function initializeAnimations() {
    // Set up scroll animations
    var animatedElements = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card, .step-item');
    
    animatedElements.forEach(function(element) {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Run animations
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Staggered loading animation for cards
    var cards = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card');
    cards.forEach(function(card, index) {
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Animate on scroll function
function animateOnScroll() {
    var elements = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card, .step-item');
    
    elements.forEach(function(element) {
        var rect = element.getBoundingClientRect();
        var isVisible = rect.top < window.innerHeight && rect.bottom > 0;
        
        if (isVisible) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
}

// Initialize form validation
function initializeFormValidation() {
    var forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var requiredFields = form.querySelectorAll('[required]');
            var isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc2626';
                    field.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
                } else {
                    field.style.borderColor = '#e5e7eb';
                    field.style.boxShadow = 'none';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
        });
    });
}

// Handle window resize
window.addEventListener('resize', function() {
    animateOnScroll();
});

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    var anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('href');
            var target = document.querySelector(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Test function to check if JS is loading
console.log('How It Works JavaScript loaded successfully!');