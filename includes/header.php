<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Al Amad Private School — A modern school management platform for students, parents, and teachers in Tunisia.">
    <title>Al Amad School - Excellence in Education</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme','light');</script>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <!-- LOGO with real image -->
            <a href="index.php" class="logo" aria-label="Al Amad School Home">
                <img src="assets/img/logo.png" alt="Al Amad School Logo"
                     style="width:42px; height:42px; object-fit:contain; border-radius:10px;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span class="logo-icon" style="display:none;">A</span>
                <span class="logo-text">Al Amad <small>School</small></span>
            </a>

            <ul class="nav-links" id="nav-links">
                <li><a href="#features"><i class="fas fa-th-large"></i> Features</a></li>
                <li><a href="#about"><i class="fas fa-school"></i> About Us</a></li>
                <li><a href="#register"><i class="fas fa-user-plus"></i> Enroll</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>

            <div class="auth-buttons">
                <button id="theme-toggle" class="btn btn-secondary btn-icon" title="Toggle Light/Dark Mode">
                    <i class="fas fa-circle-half-stroke"></i>
                </button>
                <a href="auth/login.php" class="btn btn-outline" id="nav-login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="#register" class="btn btn-primary" id="nav-enroll-btn">
                    <i class="fas fa-user-plus"></i> Enroll Now
                </a>
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <i class="fas fa-bars" id="mobileIcon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile nav drawer -->
    <div class="mobile-nav" id="mobileNav">
        <a href="#features"><i class="fas fa-th-large"></i> Features</a>
        <a href="#about"><i class="fas fa-school"></i> About Us</a>
        <a href="#register"><i class="fas fa-user-plus"></i> Enroll</a>
        <a href="#contact"><i class="fas fa-envelope"></i> Contact</a>
        <a href="auth/login.php" class="btn btn-primary" style="margin-top:.5rem;"><i class="fas fa-sign-in-alt"></i> Login</a>
    </div>

<style>
/* ── Navbar scroll effect ── */
.navbar.scrolled {
    box-shadow: 0 2px 20px rgba(0,0,0,.12);
}
.nav-links a {
    display: flex; align-items: center; gap: 6px;
    font-size: .9rem;
}
.nav-links a:hover { color: var(--primary); }

/* ── Mobile nav drawer ── */
.mobile-nav {
    display: none;
    flex-direction: column;
    gap: .5rem;
    padding: 1rem 1.5rem;
    background: var(--card-bg);
    border-bottom: 1px solid var(--glass-border);
    position: sticky; top: 80px; z-index: 999;
}
.mobile-nav.open { display: flex; }
.mobile-nav a {
    padding: .75rem 1rem;
    border-radius: 12px;
    color: var(--text);
    text-decoration: none;
    font-weight: 600;
    display: flex; align-items: center; gap: 8px;
    transition: background .2s;
}
.mobile-nav a:hover { background: var(--bg); color: var(--primary); }

/* Back-to-top button */
#back-to-top {
    position: fixed;
    bottom: 2rem; right: 2rem;
    width: 44px; height: 44px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
    opacity: 0; pointer-events: none;
    transition: opacity .3s, transform .3s;
    z-index: 1000;
}
#back-to-top.visible { opacity: 1; pointer-events: auto; }
#back-to-top:hover { transform: translateY(-3px); }
</style>
<script src="assets/js/theme-switcher.js" defer></script>
<script>
// Navbar scroll effect
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20);
    document.getElementById('back-to-top').classList.toggle('visible', window.scrollY > 400);
});
// Mobile nav toggle
document.getElementById('mobileToggle').addEventListener('click', function () {
    const nav = document.getElementById('mobileNav');
    const icon = document.getElementById('mobileIcon');
    const open = nav.classList.toggle('open');
    icon.className = open ? 'fas fa-times' : 'fas fa-bars';
    this.setAttribute('aria-expanded', open);
});
// Close mobile nav on link click
document.querySelectorAll('.mobile-nav a').forEach(a => {
    a.addEventListener('click', () => {
        document.getElementById('mobileNav').classList.remove('open');
        document.getElementById('mobileIcon').className = 'fas fa-bars';
    });
});
// Smooth anchor scroll with offset for sticky nav
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        const offset = 90; // navbar height
        window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
    });
});
</script>
