const modal = document.getElementById('gallery-modal');
const modalImg = document.getElementById('gallery-modal-img');
const closeBtn = document.querySelector('.gallery-modal-close');
const leftArrow = document.querySelector('.gallery-modal-arrow-left');
const rightArrow = document.querySelector('.gallery-modal-arrow-right');
const galleryItems = Array.from(document.querySelectorAll('.gallery-masonry-item img'));

let currentIndex = 0;

function openModal(index) {
  currentIndex = index;
  modal.classList.add('open');
  modalImg.src = galleryItems[currentIndex].src;
  modalImg.alt = galleryItems[currentIndex].alt;
}

function closeModal() {
  modal.classList.remove('open');
  modalImg.src = '';
}

function showPrev() {
  currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
  modalImg.src = galleryItems[currentIndex].src;
  modalImg.alt = galleryItems[currentIndex].alt;
}

function showNext() {
  currentIndex = (currentIndex + 1) % galleryItems.length;
  modalImg.src = galleryItems[currentIndex].src;
  modalImg.alt = galleryItems[currentIndex].alt;
}

galleryItems.forEach((img, idx) => {
  img.addEventListener('click', () => openModal(idx));
});

closeBtn.addEventListener('click', closeModal);

modal.addEventListener('click', function(e) {
  if (e.target === modal) closeModal();
});

leftArrow.addEventListener('click', function(e) {
  e.stopPropagation();
  showPrev();
});
rightArrow.addEventListener('click', function(e) {
  e.stopPropagation();
  showNext();
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
  if (!modal.classList.contains('open')) return;
  if (e.key === 'ArrowLeft') showPrev();
  if (e.key === 'ArrowRight') showNext();
  if (e.key === 'Escape') closeModal();
});

// Loader for each image
function setupImageLoader(img) {
  img.addEventListener('load', function() {
    img.style.display = 'block';
    var loader = img.previousElementSibling;
    if (loader && loader.classList.contains('img-loader')) {
      loader.style.display = 'none';
    }
  });
  if (img.complete) {
    img.dispatchEvent(new Event('load'));
  }
}
document.querySelectorAll('.gallery-masonry-item img').forEach(setupImageLoader);

// Show More functionality
const showMoreBtn = document.getElementById('show-more-btn');
const masonry = document.getElementById('gallery-masonry');
const showMoreLoader = document.getElementById('show-more-loader');
let imagesToShow = window.galleryImages || [];
let batchSize = 6;

if (showMoreBtn) {
  showMoreBtn.addEventListener('click', function() {
    showMoreLoader.classList.remove('d-none');
    setTimeout(() => { // Simulate loading
      for (let i = 0; i < batchSize && imagesToShow.length > 0; i++) {
        const imgSrc = imagesToShow.shift();
        const item = document.createElement('div');
        item.className = 'gallery-masonry-item';
        item.innerHTML = `<div class="img-loader"></div><img src="${imgSrc}" alt="Gallery Photo" style="display:none;">`;
        masonry.appendChild(item);
        setupImageLoader(item.querySelector('img'));
      }
      showMoreLoader.classList.add('d-none');
      if (imagesToShow.length === 0) {
        showMoreBtn.style.display = 'none';
      }
    }, 800); // Loader animation duration
  });
}