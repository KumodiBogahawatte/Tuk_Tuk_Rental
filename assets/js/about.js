// Horizontal drag-to-scroll
const gallery = document.getElementById('gallery-scroll');
let isDown = false, startX, scrollLeft;

if (gallery) {
  gallery.addEventListener('mousedown', (e) => {
    isDown = true;
    startX = e.pageX - gallery.offsetLeft;
    scrollLeft = gallery.scrollLeft;
    gallery.style.cursor = 'grabbing';
  });
  gallery.addEventListener('mouseleave', () => {
    isDown = false;
    gallery.style.cursor = 'grab';
  });
  gallery.addEventListener('mouseup', () => {
    isDown = false;
    gallery.style.cursor = 'grab';
  });
  gallery.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - gallery.offsetLeft;
    const walk = (x - startX) * 1.5;
    gallery.scrollLeft = scrollLeft - walk;
  });
  // Set initial cursor
  gallery.style.cursor = 'grab';
}

// Animate images on scroll into view
const galleryCells = document.querySelectorAll('.gallery-cell');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.2 });

galleryCells.forEach(cell => observer.observe(cell));

// Auto-scroll animation
function autoScrollGallery() {
  if (!gallery) return;
  let maxScroll = gallery.scrollWidth - gallery.clientWidth;
  let direction = 1;
  setInterval(() => {
    if (gallery.scrollLeft >= maxScroll) direction = -1;
    if (gallery.scrollLeft <= 0) direction = 1;
    gallery.scrollLeft += direction * 6; // Adjust speed here
  }, 16); // ~60fps
}
autoScrollGallery();

document.addEventListener('DOMContentLoaded', function() {
  const galleryCells = document.querySelectorAll('.gallery-cell');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.2 });

  galleryCells.forEach(cell => observer.observe(cell));
});