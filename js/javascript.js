// ===== Mobile Hamburger Menu =====
function toggleMenu() {
  const menu = document.getElementById('mobile-menu') || document.getElementById('mobileMenu');
  if (menu) {
    menu.classList.toggle('open');
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.getElementById('hamburger');
  if (hamburger) {
    hamburger.addEventListener('click', toggleMenu);
  }

  // Close the mobile menu automatically when a link inside it is tapped
  const menu = document.getElementById('mobile-menu') || document.getElementById('mobileMenu');
  if (menu) {
    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menu.classList.remove('open');
      });
    });
  }
});