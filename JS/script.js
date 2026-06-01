// Menu burger toggle
const menuBurger = document.querySelector('.menu-burger');
const navMenu = document.querySelector('.nav-menu');
const menuOverlay = document.querySelector('.menu-overlay');
const body = document.body;

function toggleMenu() {
    menuBurger.classList.toggle('active');
    navMenu.classList.toggle('active');
    menuOverlay.classList.toggle('active');
    body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
}

function closeMenu() {
    menuBurger.classList.remove('active');
    navMenu.classList.remove('active');
    menuOverlay.classList.remove('active');
    body.style.overflow = '';
}

menuBurger.addEventListener('click', toggleMenu);

// Fermer le menu lors du clic sur un lien
const navLinks = document.querySelectorAll('.nav-menu a');
navLinks.forEach(link => {
    link.addEventListener('click', closeMenu);
});

// Fermer le menu si on clique sur l'overlay
menuOverlay.addEventListener('click', closeMenu);
