// Guidebook functionality
let currentPage = 1;
const totalPages = 6;

function openGuidebook() {
    const modal = document.getElementById('guidebook-modal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeGuidebook() {
    const modal = document.getElementById('guidebook-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        const currentPageElement = document.getElementById(page-$,{currentPage});
        if (currentPageElement) {
            currentPageElement.classList.remove('active');
        }
        
        currentPage++;
        
        const nextPageElement = document.getElementById(page-$,{currentPage});
        if (nextPageElement) {
            nextPageElement.classList.add('active');
            nextPageElement.classList.add('page-flip');
            
            setTimeout(() => {
                nextPageElement.classList.remove('page-flip');
            }, 600);
        }
        
        updateNavigation();
    }
}

function previousPage() {
    if (currentPage > 1) {
        const currentPageElement = document.getElementById(page-$,{currentPage});
        if (currentPageElement) {
            currentPageElement.classList.remove('active');
        }
        
        currentPage--;
        
        const prevPageElement = document.getElementById(page-$,{currentPage});
        if (prevPageElement) {
            prevPageElement.classList.add('active');
            prevPageElement.classList.add('page-flip');
            
            setTimeout(() => {
                prevPageElement.classList.remove('page-flip');
            }, 600);
        }
        
        updateNavigation();
    }
}

function updateNavigation() {
    const currentPageSpan = document.getElementById('current-page');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
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

// Toggle dropdown functionality
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) return;
    
    const button = dropdown.previousElementSibling;
    if (!button) return;
    
    const icon = button.querySelector('i');
    
    dropdown.classList.toggle('show');
    
    if (icon) {
        if (dropdown.classList.contains('show')) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize guidebook trigger
    const guidebookTrigger = document.querySelector('.guidebook-trigger');
    if (guidebookTrigger) {
        guidebookTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            openGuidebook();
        });
    }
    
    // Close guidebook when clicking outside
    const guidebookModal = document.getElementById('guidebook-modal');
    if (guidebookModal) {
        guidebookModal.addEventListener('click', function(e) {
            if (e.target === guidebookModal) {
                closeGuidebook();
            }
        });
    }
    
    // Close button functionality
    const closeBtn = document.querySelector('.close-book');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeGuidebook();
        });
    }
    
    // Navigation buttons
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            nextPage();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            previousPage();
        });
    }
    
    // Initialize navigation
    updateNavigation();
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    field.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
                } else {
                    field.style.borderColor = '#ddd';
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
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Scroll animations
    function animateOnScroll() {
        const elements = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card, .step-item');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    }
    
    // Initialize animations
    const animatedElements = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card, .step-item');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Run animation on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Staggered loading animation for cards
    const cards = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Handle keyboard navigation for guidebook
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('guidebook-modal');
    if (modal && modal.style.display === 'block') {
        switch(e.key) {
            case 'Escape':
                closeGuidebook();
                break;
            case 'ArrowRight':
                nextPage();
                break;
            case 'ArrowLeft':
                previousPage();
                break;
        }
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    // Recalculate animations on resize
    const elements = document.querySelectorAll('.facility-card, .requirement-card, .insurance-card, .kit-card, .step-item');
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
});


// Guidebook Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('guidebook-modal');
    const triggerBtn = document.querySelector('.guidebook-trigger');
    const closeBtn = document.getElementById('close-guidebook');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const currentPageSpan = document.getElementById('current-page');
    const totalPagesSpan = document.getElementById('total-pages');
    
    let currentPage = 1;
    const totalPages = 6;
    
    // Initialize
    function init() {
        totalPagesSpan.textContent = totalPages;
        currentPageSpan.textContent = currentPage;
        updatePageVisibility();
        updateNavButtons();
    }
    
    // Open modal
    function openModal() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Close modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Update page visibility
    function updatePageVisibility() {
        const pages = document.querySelectorAll('.page-spread');
        pages.forEach((page, index) => {
            if (index + 1 === currentPage) {
                page.classList.add('active');
            } else {
                page.classList.remove('active');
            }
        });
    }
    
    // Update navigation buttons
    function updateNavButtons() {
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }
    
    // Go to next page
    function nextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            currentPageSpan.textContent = currentPage;
            updatePageVisibility();
            updateNavButtons();
            
            // Add page flip animation
            const currentPageSpread = document.querySelector('.page-spread.active');
            if (currentPageSpread) {
                currentPageSpread.classList.add('page-flip');
                setTimeout(() => {
                    currentPageSpread.classList.remove('page-flip');
                }, 600);
            }
        }
    }
    
    // Go to previous page
    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            currentPageSpan.textContent = currentPage;
            updatePageVisibility();
            updateNavButtons();
            
            // Add page flip animation
            const currentPageSpread = document.querySelector('.page-spread.active');
            if (currentPageSpread) {
                currentPageSpread.classList.add('page-flip');
                setTimeout(() => {
                    currentPageSpread.classList.remove('page-flip');
                }, 600);
            }
        }
    }
    
    // Event listeners
    if (triggerBtn) {
        triggerBtn.addEventListener('click', openModal);
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', prevPage);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', nextPage);
    }
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (modal.style.display === 'block') {
            if (e.key === 'ArrowLeft') {
                prevPage();
            } else if (e.key === 'ArrowRight') {
                nextPage();
            }
        }
    });
    
    // Initialize the guidebook
    init();
});