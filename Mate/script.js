let currentIndex = 0;

function moveSlide(direction) {
  const slides = document.querySelector('.custom-slides');
  const totalSlides = slides.children.length;

  currentIndex = (currentIndex + direction + totalSlides) % totalSlides;

  const slideWidth = slides.children[0].clientWidth;
  slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}

window.addEventListener('resize', () => {
  const slides = document.querySelector('.custom-slides');
  const slideWidth = slides.children[0].clientWidth;
  slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
});
