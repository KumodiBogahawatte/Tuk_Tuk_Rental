document.addEventListener('DOMContentLoaded', function () {
    const backPhone = document.querySelector('.back-phone');
    const frontPhone = document.querySelector('.front-phone');
    const phoneStack = document.querySelector('.phone-stack');
  
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          backPhone.classList.add('visible');
          frontPhone.classList.add('visible');
        }
      });
    }, { threshold: 0.3 });
  
    observer.observe(phoneStack);
  });  


window.addEventListener("scroll", function () {
    const navbar = document.querySelector(".navbar");
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });

  //thumbnail click to update main image
  document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.getElementById("mainImage");
    const thumbnails = document.querySelectorAll(".thumb-img");

    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", function () {
        const newSrc = this.getAttribute("src");
        mainImage.setAttribute("src", newSrc);
      });
    });
  });

  function changeImage(thumbnail) {
    // Get the source of the clicked thumbnail
    var newSrc = thumbnail.src;
    
    // Set the main image's src to the clicked thumbnail's src
    document.getElementById('mainImage').src = newSrc;
    
    // Optionally, add the 'active' class to highlight the selected thumbnail
    var thumbs = document.querySelectorAll('.thumb-img');
    thumbs.forEach(function(thumb) {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');
}


// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Initialize accordions
document.addEventListener('DOMContentLoaded', function() {
    // Ensure accordions work properly
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


//Dark and light mode toggle
//   document.addEventListener('DOMContentLoaded', function () {
//     const toggleBtn = document.getElementById('themeToggle');
//     const themeIcon = document.getElementById('themeIcon');

//     // Load saved preference
//     const savedTheme = localStorage.getItem('theme');
//     if (savedTheme === 'dark') {
//         document.body.classList.add('dark-mode');
//         themeIcon.classList.replace('fa-moon', 'fa-sun');
//     }

//     toggleBtn.addEventListener('click', () => {
//         document.body.classList.toggle('dark-mode');
//         const isDark = document.body.classList.contains('dark-mode');

//         // Toggle icon
//         themeIcon.classList.toggle('fa-sun', isDark);
//         themeIcon.classList.toggle('fa-moon', !isDark);

//         // Save preference
//         localStorage.setItem('theme', isDark ? 'dark' : 'light');
//     });
// });
  
