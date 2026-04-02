/**
 * Al Amad School - Main JS
 * Handles: scroll-reveal, counter animations, smooth scroll, active nav
 */

// ── Smooth Scroll ──────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ── Scroll Reveal ──────────────────────────────────────────
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(el => {
        if (el.isIntersecting) {
            el.target.classList.add('visible');
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal, .reveal-right').forEach(el => revealObserver.observe(el));

// ── Stats Counter Animation ────────────────────────────────
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = +el.getAttribute('data-target');
            const duration = 1800;
            const step = target / (duration / 16);
            let current = 0;
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    el.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    el.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
            counterObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-num').forEach(el => counterObserver.observe(el));

// ── Active Nav Link ────────────────────────────────────────
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        if (window.scrollY >= section.offsetTop - 100) {
            current = section.getAttribute('id');
        }
    });
    navLinks.forEach(a => {
        a.classList.remove('active');
        if (a.getAttribute('href') === `#${current}`) a.classList.add('active');
    });
});

// ── Mobile Menu Toggle ──────────────────────────────────────
const mobileToggle = document.querySelector('.mobile-toggle');
const navLinksContainer = document.querySelector('.nav-links');

if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        mobileToggle.classList.toggle('active');
        navLinksContainer.classList.toggle('active');
    });

    // Close menu when clicking a link
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileToggle.classList.remove('active');
            navLinksContainer.classList.remove('active');
        });
    });
}
