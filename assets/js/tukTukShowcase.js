class CleanTukTukShowcase {
  constructor() {
    this.currentIndex = 0;
    this.isPlaying = true;
    this.autoPlayInterval = null;
    this.duration = 4000;
    
    this.slides = [
      {
        badge: "Most Popular",
        title: "Regular Tuk Tuk",
        subtitle: "The most popular four-stroke Bajaj tuk-tuk in Sri Lanka. Experience classic comfort with modern reliability for your perfect journey.",
        price: "18",
        features: [
          { icon: "⚙️", text: "Manual Transmission" },
          { icon: "⛽", text: "Petrol Engine" },
          { icon: "👥", text: "3 Passengers" },
          { icon: "🎨", text: "Classic Design" }
        ],
        image: "../assets/images/Vehicles/Bajaj_RE-yellow-removebg-preview.png",
        className: "slide-1"
      },
      {
        badge: "Eco-Friendly",
        title: "Electric Tuk Tuk",
        subtitle: "Experience the future of travel with our silent, emission-free electric tuk-tuk. Automatic transmission with 150km range.",
        price: "18",
        features: [
          { icon: "🔋", text: "Electric Motor" },
          { icon: "🔄", text: "Auto Transmission" },
          { icon: "📏", text: "150km Range" },
          { icon: "🌿", text: "Zero Emissions" }
        ],
        image: "../assets/images/Vehicles/Bajaj_RE-blue-removebg-preview.png",
        className: "slide-2"
      },
      {
        badge: "Open Air",
        title: "Convertible Tuk Tuk",
        subtitle: "Feel the tropical breeze and enjoy panoramic views with our removable roof tuk-tuk. Perfect for sightseeing adventures.",
        price: "16",
        features: [
          { icon: "🏠", text: "Removable Roof" },
          { icon: "👀", text: "Panoramic Views" },
          { icon: "💨", text: "Fresh Air" },
          { icon: "📸", text: "Photo Perfect" }
        ],
        image: "../assets/images/Vehicles/Bajaj_RE-removebg-preview.png",
        className: "slide-3"
      }
    ];

    this.init();
  }

  init() {
    this.render();
    this.bindEvents();
    this.startAutoPlay();
    this.animateCurrentSlide();
  }

  render() {
    const container = document.querySelector('.tuktuk-showcase');
    if (!container) return;

    container.innerHTML = `
      ${this.slides.map((slide, index) => `
        <div class="showcase-slide ${slide.className} ${index === 0 ? 'active' : ''}" data-index="${index}">
          <div class="showcase-container">
            <div class="showcase-content">
              <div class="showcase-left">
                <div class="showcase-badge">${slide.badge}</div>
                <h2 class="showcase-title">${slide.title}</h2>
                <p class="showcase-subtitle">${slide.subtitle}</p>
                
                <div class="showcase-price">
                  <span class="price-label">Starting from</span>
                  <span class="price-value">$${slide.price}</span>
                  <span class="price-period">/ day</span>
                </div>
                
                <div class="showcase-features">
                  ${slide.features.map(feature => `
                    <div class="feature-item">
                      <div class="feature-icon">${feature.icon}</div>
                      <span>${feature.text}</span>
                    </div>
                  `).join('')}
                </div>
                
                <div class="showcase-cta">
                  <a href="#" class="cta-button cta-primary">
                    Book Now
                    <i class="fas fa-arrow-right"></i>
                  </a>
                  <a href="#" class="cta-button cta-secondary">
                    Learn More
                  </a>
                </div>
              </div>
              
              <div class="showcase-right">
                <div class="image-container">
                  <img src="${slide.image}" alt="${slide.title}" class="showcase-image">
                </div>
              </div>
            </div>
          </div>
        </div>
      `).join('')}
      
      <div class="showcase-navigation">
        <div class="nav-indicators">
          ${this.slides.map((_, index) => `
            <div class="nav-indicator ${index === 0 ? 'active' : ''}" data-index="${index}"></div>
          `).join('')}
        </div>
        
        <div class="nav-controls">
          <button class="nav-btn" id="prevBtn">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="nav-btn" id="playPauseBtn">
            <i class="fas fa-pause"></i>
          </button>
          <button class="nav-btn" id="nextBtn">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    `;
  }

  bindEvents() {
    document.querySelectorAll('.nav-indicator').forEach((indicator, index) => {
      indicator.addEventListener('click', () => this.goToSlide(index));
    });

    document.getElementById('prevBtn')?.addEventListener('click', () => this.previousSlide());
    document.getElementById('nextBtn')?.addEventListener('click', () => this.nextSlide());
    document.getElementById('playPauseBtn')?.addEventListener('click', () => this.togglePlayPause());

    const container = document.querySelector('.tuktuk-showcase');
    container?.addEventListener('mouseenter', () => this.pauseAutoPlay());
    container?.addEventListener('mouseleave', () => this.resumeAutoPlay());
  }

  animateCurrentSlide() {
    const currentSlide = document.querySelector(`.showcase-slide[data-index="${this.currentIndex}"]`);
    if (!currentSlide) return;

    const elements = currentSlide.querySelectorAll('.showcase-badge, .showcase-title, .showcase-subtitle, .showcase-price, .showcase-features, .showcase-cta, .showcase-image');
    
    elements.forEach(el => el.classList.remove('animate'));
    
    setTimeout(() => {
      elements.forEach(el => el.classList.add('animate'));
    }, 50);
  }

  goToSlide(index) {
    if (index === this.currentIndex) return;

    document.querySelectorAll('.showcase-slide').forEach(slide => {
      slide.classList.remove('active');
    });

    document.querySelector(`.showcase-slide[data-index="${index}"]`).classList.add('active');

    this.currentIndex = index;
    this.updateIndicators();
    this.animateCurrentSlide();
    this.restartAutoPlay();
  }

  nextSlide() {
    const nextIndex = (this.currentIndex + 1) % this.slides.length;
    this.goToSlide(nextIndex);
  }

  previousSlide() {
    const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
    this.goToSlide(prevIndex);
  }

  updateIndicators() {
    document.querySelectorAll('.nav-indicator').forEach((indicator, index) => {
      indicator.classList.toggle('active', index === this.currentIndex);
    });
  }

  startAutoPlay() {
    if (this.isPlaying) {
      this.autoPlayInterval = setInterval(() => this.nextSlide(), this.duration);
    }
  }

  stopAutoPlay() {
    if (this.autoPlayInterval) {
      clearInterval(this.autoPlayInterval);
      this.autoPlayInterval = null;
    }
  }

  restartAutoPlay() {
    this.stopAutoPlay();
    this.startAutoPlay();
  }

  pauseAutoPlay() {
    this.isPlaying = false;
    this.stopAutoPlay();
    this.updatePlayButton();
  }

  resumeAutoPlay() {
    this.isPlaying = true;
    this.startAutoPlay();
    this.updatePlayButton();
  }

  togglePlayPause() {
    this.isPlaying ? this.pauseAutoPlay() : this.resumeAutoPlay();
  }

  updatePlayButton() {
    const btn = document.getElementById('playPauseBtn');
    const icon = btn?.querySelector('i');
    if (icon) {
      icon.className = this.isPlaying ? 'fas fa-pause' : 'fas fa-play';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new CleanTukTukShowcase();
});