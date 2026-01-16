let currentLanguage = "ka";

fetch('translations.json')
    .then(res => res.json())
    .then(data => {
        const lang = localStorage.getItem('lang') || 'ka'; 
        const texts = data[lang];

        document.title = texts.title;

        document.querySelector('.logo a p').textContent = texts.headerLogoText;
        document.querySelector('nav ul li:nth-child(1) a').textContent = texts.navbar.home;
        document.querySelector('nav ul li:nth-child(2) a').textContent = texts.navbar.about;
        document.querySelector('nav ul li:nth-child(3) a').textContent = texts.navbar.register;
        document.querySelector('nav ul li:nth-child(4) a').textContent = texts.navbar.login;
        document.querySelector('.search input').placeholder = texts.navbar.searchPlaceholder;
        document.querySelector('.search button').textContent = texts.navbar.searchButton;

        document.getElementById('PCs').textContent = texts.products.PCsHeading;

        document.querySelector('footer p').textContent = texts.footerText;
    })
    .catch(err => console.error('Error loading language JSON:', err));
		
async function loadTranslations() {
  try {
    const response = await fetch("translations.json");
    if (!response.ok) throw new Error("Error loading translations");
    const translations = await response.json();
    applyTranslations(translations);
  } catch (error) {
    console.error("Error loading translations:", error);
  }
}

function applyTranslations(translations) {
  const langData = translations[currentLanguage];

  document.title = langData.title;

  document.querySelector(".logo p").textContent = langData.headerLogoText;

  const navItems = document.querySelectorAll("nav ul li a");
  navItems[0].textContent = langData.navbar.home;
  navItems[1].textContent = langData.navbar.about;
  navItems[2].textContent = langData.navbar.register;
  navItems[3].textContent = langData.navbar.login;

  const searchInput = document.querySelector(".search input");
  searchInput.placeholder = langData.navbar.searchPlaceholder;
  const searchButton = document.querySelector(".search button");
  searchButton.textContent = langData.navbar.searchButton;

  document.getElementById("PCs").textContent = langData.products.PCsHeading;
  const quantityLabels = document.querySelectorAll(".qyt label");
  quantityLabels.forEach(label => (label.textContent = langData.products.quantityLabel));
  const addToCartButtons = document.querySelectorAll(".productbtns button:first-child");
  addToCartButtons.forEach(button => (button.textContent = langData.products.addToCartButton));
  const buyButtons = document.querySelectorAll(".productbtns button:last-child");
  buyButtons.forEach(button => (button.textContent = langData.products.buyButton));

  document.querySelector("footer p").textContent = langData.footerText;
}


document.getElementById("languageSelector").addEventListener("change", (e) => {
  currentLanguage = e.target.value; 
  loadTranslations();
});

loadTranslations();

