document.addEventListener('DOMContentLoaded', function() {
    var navbar = document.querySelector('.main-navbar-gov');
    
    function checkScroll() {
        if (window.scrollY > 40) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
    
    window.addEventListener('scroll', checkScroll);
    checkScroll(); // Check on load
});
