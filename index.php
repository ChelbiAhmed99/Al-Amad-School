<?php include 'includes/header.php'; ?>

<!-- ══════════════════════════════════════════════
     PRIMARY SCHOOL THEME OVERRIDE
     Bright, warm, child-friendly homepage design
════════════════════════════════════════════════ -->
<style>
    /* ── Color Palette — Primary School ── */
    :root {
        --ps-orange: #FF7B54;
        /* warm coral-orange */
        --ps-blue: #4ECDC4;
        /* sky teal          */
        --ps-yellow: #FFD166;
        /* sunny yellow      */
        --ps-green: #06D6A0;
        /* fresh green       */
        --ps-purple: #a29bfe;
        /* soft lavender     */
        --ps-red: #ef476f;
        /* rosy red          */
        --ps-dark: #2d3436;
        /* text dark         */
        --ps-mid: #636e72;
        /* text muted        */
        --ps-light: #f8f9ff;
        /* page bg           */
        --ps-white: #ffffff;
    }

    /* ── Light mode (default) ── */
    :root,
    [data-theme="light"] {
        --primary: var(--ps-orange);
        --secondary: var(--ps-blue);
        --accent: var(--ps-yellow);
        --bg: var(--ps-light);
        --card-bg: var(--ps-white);
        --text: var(--ps-dark);
        --text-muted: var(--ps-mid);
        --glass-border: rgba(0, 0, 0, .07);
    }

    /* ── Dark mode ── */
    [data-theme="dark"] {
        --primary: var(--ps-orange);
        --secondary: var(--ps-blue);
        --accent: var(--ps-yellow);
        --bg: #1a1512;
        --card-bg: #251e19;
        --text: #f0ebe8;
        --text-muted: #a09590;
        --glass-border: rgba(255, 255, 255, .07);
    }

    [data-theme="dark"] body {
        background: #1a1512;
        color: #f0ebe8;
    }

    [data-theme="dark"] .navbar {
        background: #251e19;
        border-bottom-color: var(--ps-orange);
    }

    [data-theme="dark"] .hero {
        background: linear-gradient(150deg, #1f1711 0%, #111e1c 40%, #1a1a12 100%);
    }

    [data-theme="dark"] .hero::after {
        background: #1a1512;
    }

    [data-theme="dark"] .stats-band {
        background: linear-gradient(135deg, #b85530, #c87a40);
    }

    [data-theme="dark"] .features {
        background: #1a1512;
    }

    [data-theme="dark"] .feature-card {
        background: #251e19;
        border-color: rgba(255, 255, 255, .06);
    }

    [data-theme="dark"] .feature-card h3 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .section-title {
        color: #f0ebe8;
    }

    [data-theme="dark"] .about-section {
        background: #251e19;
    }

    [data-theme="dark"] .values-section {
        background: #1f1711;
    }

    [data-theme="dark"] .value-card {
        background: #251e19;
        border-color: rgba(255, 255, 255, .06);
    }

    [data-theme="dark"] .value-card h3 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .gallery-section {
        background: #141c1b;
    }

    [data-theme="dark"] .enrollment-section {
        background: #251e19;
    }

    [data-theme="dark"] .step-card {
        background: #2e2318;
        border-color: rgba(255, 123, 84, .15);
    }

    [data-theme="dark"] .step-card h4 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .contact-section {
        background: #1a1512;
    }

    [data-theme="dark"] .contact-form-card {
        background: #251e19;
        border-color: rgba(255, 123, 84, .15);
    }

    [data-theme="dark"] .contact-form-card h3 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .form-group input,
    [data-theme="dark"] .form-group select {
        background: #1a1512;
        border-color: rgba(255, 255, 255, .1);
        color: #f0ebe8;
    }

    [data-theme="dark"] .contact-info-card {
        background: #251e19;
        border-color: rgba(255, 255, 255, .06);
    }

    [data-theme="dark"] .contact-info-card h4 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .hero-badge-float {
        background: #251e19;
    }

    [data-theme="dark"] .logo-text {
        color: var(--ps-orange) !important;
    }

    [data-theme="dark"] .nav-links a {
        color: #f0ebe8;
    }

    [data-theme="dark"] .about-text h2 {
        color: #f0ebe8;
    }

    [data-theme="dark"] .mobile-nav {
        background: #251e19;
    }

    [data-theme="dark"] .mobile-nav a {
        color: #f0ebe8;
    }

    body {
        background: var(--ps-light);
        color: var(--ps-dark);
    }

    /* ── Navbar ── */
    .navbar {
        background: var(--ps-white);
        border-bottom: 3px solid var(--ps-orange);
        box-shadow: 0 4px 20px rgba(255, 123, 84, .12);
    }

    .logo-text {
        color: var(--ps-orange) !important;
        font-weight: 900;
    }

    .nav-links a {
        color: var(--ps-dark);
        font-weight: 700;
    }

    .nav-links a:hover {
        color: var(--ps-orange);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--ps-orange), #ff9f7f) !important;
        box-shadow: 0 4px 16px rgba(255, 123, 84, .35) !important;
        border-radius: 50px !important;
        color: white !important;
    }

    .btn-outline {
        border: 2px solid var(--ps-orange) !important;
        color: var(--ps-orange) !important;
        border-radius: 50px !important;
        background: transparent !important;
    }

    .btn-outline:hover {
        background: var(--ps-orange) !important;
        color: white !important;
    }

    /* ── HERO ── */
    .hero {
        background: linear-gradient(150deg, #fff8f5 0%, #e8fdf8 40%, #fffbf0 100%);
        padding: 6rem 0 4rem;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 10% 20%, rgba(255, 123, 84, .08) 0%, transparent 50%),
            radial-gradient(circle at 90% 80%, rgba(78, 205, 196, .08) 0%, transparent 50%);
    }

    /* Decorative shapes */
    .hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 80px;
        background: var(--ps-light);
        clip-path: ellipse(55% 100% at 50% 100%);
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: rgba(255, 123, 84, .12);
        color: var(--ps-orange);
        border: 1.5px solid rgba(255, 123, 84, .25);
        border-radius: 50px;
        padding: .5rem 1.2rem;
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    .hero h1 {
        font-size: clamp(2rem, 4.5vw, 3.2rem);
        font-weight: 900;
        color: var(--ps-dark);
        line-height: 1.15;
        margin-bottom: 1.25rem;
    }

    .hero h1 span {
        background: linear-gradient(90deg, var(--ps-orange), var(--ps-yellow));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero>.container>.hero-layout>.hero-content>p {
        font-size: 1.05rem;
        color: var(--ps-mid);
        line-height: 1.7;
        max-width: 480px;
    }

    .hero-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin: 2rem 0 1.75rem;
    }

    .btn-lg {
        padding: .95rem 2rem;
        font-size: 1rem;
        font-weight: 800;
    }

    .btn-ghost {
        background: rgba(255, 123, 84, .08) !important;
        border: 2px solid rgba(255, 123, 84, .2) !important;
        color: var(--ps-orange) !important;
        border-radius: 50px !important;
    }

    .btn-ghost:hover {
        background: rgba(255, 123, 84, .15) !important;
        border-color: var(--ps-orange) !important;
    }

    .hero-trust {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        font-size: .83rem;
        font-weight: 700;
        color: var(--ps-mid);
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    /* Hero visual orbs */
    .hero-img-container {
        position: relative;
    }

    .hero-logo-ring {
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 123, 84, .1), rgba(78, 205, 196, .1));
        border: 3px dashed rgba(255, 209, 102, .5);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: rotateSlow 20s linear infinite;
        position: relative;
    }

    .hero-logo-ring img {
        width: 160px;
        height: 160px;
        object-fit: contain;
        filter: drop-shadow(0 10px 25px rgba(255, 123, 84, .25));
        animation: rotateSlow 20s linear infinite reverse;
    }

    .hero-badge-float {
        position: absolute;
        background: white;
        border-radius: 20px;
        padding: .7rem 1.1rem;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .1);
        font-weight: 800;
        animation: floatBadge 4s ease-in-out infinite;
        border: 2px solid rgba(0, 0, 0, .05);
    }

    .hero-badge-float.top {
        top: -15px;
        right: -30px;
        animation-delay: 0s;
        color: var(--ps-orange);
    }

    .hero-badge-float.bot {
        bottom: -15px;
        left: -30px;
        animation-delay: 1.2s;
        color: var(--ps-blue);
    }

    .hbf-label {
        font-size: .65rem;
        color: var(--ps-mid);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .hbf-val {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1.1;
    }

    .decor-emoji {
        position: absolute;
        font-size: 2rem;
        opacity: .5;
        animation: floatBadge 5s ease-in-out infinite;
    }

    .decor-emoji.e1 {
        top: 0;
        left: -40px;
        animation-delay: .5s;
    }

    .decor-emoji.e2 {
        bottom: 20px;
        right: -40px;
        animation-delay: 2s;
    }

    @keyframes rotateSlow {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes floatBadge {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* ── FEATURES SECTION ── */
    .features {
        background: var(--ps-light);
        padding: 5rem 0;
    }

    .section-label {
        display: inline-block;
        background: rgba(255, 123, 84, .12);
        color: var(--ps-orange);
        border-radius: 50px;
        padding: .35rem 1rem;
        font-size: .75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1rem;
    }

    .section-title {
        font-size: clamp(1.6rem, 3vw, 2.4rem);
        font-weight: 900;
        color: var(--ps-dark);
        line-height: 1.2;
        margin-bottom: .75rem;
    }

    .section-sub {
        color: var(--ps-mid);
        font-size: .95rem;
        line-height: 1.7;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-top: 3rem;
    }

    .feature-card {
        background: white;
        border-radius: 24px;
        padding: 2rem 1.75rem;
        border: 2px solid rgba(0, 0, 0, .04);
        box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        transition: transform .3s cubic-bezier(.175, .885, .32, 1.275), box-shadow .3s;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        opacity: 0;
        transition: opacity .3s;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, .1);
    }

    .feature-card:hover::before {
        opacity: 1;
    }

    .feature-card:nth-child(1)::before {
        background: linear-gradient(90deg, var(--ps-orange), #ffb347);
    }

    .feature-card:nth-child(2)::before {
        background: linear-gradient(90deg, var(--ps-blue), #00b4d8);
    }

    .feature-card:nth-child(3)::before {
        background: linear-gradient(90deg, var(--ps-yellow), #f4a261);
    }

    .feature-card:nth-child(4)::before {
        background: linear-gradient(90deg, var(--ps-purple), #c77dff);
    }

    .feature-card:nth-child(5)::before {
        background: linear-gradient(90deg, var(--ps-green), #52b788);
    }

    .feature-card:nth-child(6)::before {
        background: linear-gradient(90deg, var(--ps-red), #f77f00);
    }

    .feature-icon {
        width: 110px;
        height: 110px;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.75rem;
        background: white;
        padding: 5px;
        border: 2px solid rgba(0, 0, 0, .05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        overflow: hidden;
        transition: transform .3s cubic-bezier(.175, .885, .32, 1.275);
    }

    .feature-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 17px;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(-3deg);
        box-shadow: 0 12px 32px rgba(255, 123, 84, .15);
        border-color: var(--ps-orange);
    }

    .feature-card h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--ps-dark);
        margin-bottom: .6rem;
    }

    .feature-card p {
        font-size: .88rem;
        color: var(--ps-mid);
        line-height: 1.65;
    }

    .feature-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--ps-orange);
        font-weight: 700;
        font-size: .85rem;
        text-decoration: none;
        margin-top: 1rem;
        transition: gap .2s;
    }

    .feature-link:hover {
        gap: 8px;
    }

    /* ── STATS BAND ── */
    .stats-band {
        background: linear-gradient(135deg, var(--ps-orange), #ff9f7f);
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
    }

    .stats-band::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='20' fill='none' stroke='rgba(255,255,255,.08)' stroke-width='2'/%3E%3C/svg%3E");
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 2rem;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .stat-item {
        color: white;
    }

    .stat-num {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1;
        display: block;
        margin-bottom: .3rem;
    }

    .stat-lbl {
        font-size: .85rem;
        font-weight: 700;
        opacity: .85;
    }

    /* ── ABOUT SECTION ── */
    .about-section {
        padding: 5rem 0;
        background: white;
    }

    .about-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .about-img-main {
        border-radius: 28px;
        overflow: hidden;
        height: 320px;
        background: linear-gradient(135deg, var(--ps-orange), var(--ps-yellow));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 6rem;
        box-shadow: 0 20px 50px rgba(255, 123, 84, .2);
        border: 4px solid rgba(255, 209, 102, .4);
    }

    .about-img-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .about-img-sub {
        margin-top: 1rem;
        border-radius: 20px;
        overflow: hidden;
        height: 160px;
        background: linear-gradient(135deg, var(--ps-blue), var(--ps-green));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        box-shadow: 0 10px 30px rgba(78, 205, 196, .2);
        border: 4px solid rgba(78, 205, 196, .3);
    }

    .about-img-sub img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .about-experience {
        margin-top: 1rem;
        background: var(--ps-orange);
        border-radius: 20px;
        padding: 1.25rem;
        color: white;
        text-align: center;
        box-shadow: 0 8px 24px rgba(255, 123, 84, .3);
    }

    .about-experience strong {
        font-size: 2rem;
        font-weight: 900;
        display: block;
    }

    .about-experience span {
        font-size: .8rem;
        font-weight: 700;
        opacity: .85;
    }

    .about-text h2 {
        font-size: clamp(1.5rem, 2.5vw, 2.2rem);
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .about-text h2 span {
        color: var(--ps-orange);
    }

    .about-text p {
        color: var(--ps-mid);
        line-height: 1.75;
        margin-bottom: 1rem;
        font-size: .95rem;
    }

    .about-bullets {
        margin: 1.5rem 0;
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .bullet {
        display: flex;
        align-items: center;
        gap: .7rem;
        font-weight: 700;
        font-size: .9rem;
        color: var(--ps-dark);
    }

    /* ── VALUES ── */
    .values-section {
        padding: 5rem 0;
        background: #fff8f5;
    }

    .values-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .values-text h2 {
        font-size: clamp(1.5rem, 2.5vw, 2.2rem);
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 1rem;
    }

    .values-text h2 span {
        color: var(--ps-orange);
    }

    .values-text p {
        color: var(--ps-mid);
        line-height: 1.7;
        font-size: .95rem;
    }

    .values-cards {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .value-card {
        background: white;
        border-radius: 20px;
        padding: 1.4rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
        border: 2px solid rgba(0, 0, 0, .04);
        transition: transform .2s, box-shadow .2s;
    }

    .value-card:hover {
        transform: translateX(6px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, .08);
    }

    .value-num {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--ps-orange);
        min-width: 48px;
        text-align: center;
        opacity: .25;
        font-style: italic;
    }

    .value-card h3 {
        font-size: .95rem;
        font-weight: 800;
        color: var(--ps-dark);
        margin-bottom: .3rem;
    }

    .value-card p {
        font-size: .83rem;
        color: var(--ps-mid);
        line-height: 1.6;
        margin: 0;
    }

    /* ── ENROLLMENT STEPS ── */
    .enrollment-section {
        padding: 5rem 0;
        background: white;
    }

    .steps-row {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin: 3rem 0 2rem;
    }

    .step-card {
        flex: 1;
        min-width: 180px;
        background: var(--ps-light);
        border-radius: 24px;
        padding: 2rem 1.5rem;
        text-align: center;
        border: 2px solid rgba(255, 123, 84, .12);
        transition: transform .3s, box-shadow .2s;
        position: relative;
    }

    .step-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(255, 123, 84, .12);
    }

    .step-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--ps-orange), var(--ps-yellow));
        color: white;
        font-size: 1.2rem;
        font-weight: 900;
        margin-bottom: 1rem;
        box-shadow: 0 6px 18px rgba(255, 123, 84, .3);
    }

    .step-card h4 {
        font-size: .95rem;
        font-weight: 800;
        color: var(--ps-dark);
        margin-bottom: .5rem;
    }

    .step-card p {
        font-size: .83rem;
        color: var(--ps-mid);
        line-height: 1.6;
    }

    .step-arrow {
        font-size: 2rem;
        color: rgba(255, 123, 84, .35);
        font-weight: 900;
        flex-shrink: 0;
    }

    .steps-cta {
        text-align: center;
        margin-top: 1.5rem;
    }

    /* ── GALLERY ── */
    .gallery-section {
        padding: 5rem 0;
        background: #f0fdfb;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-top: 2.5rem;
    }

    .gallery-item {
        border-radius: 24px;
        overflow: hidden;
        aspect-ratio: 4/3;
        box-shadow: 0 6px 24px rgba(0, 0, 0, .08);
        background: linear-gradient(135deg, var(--ps-blue), var(--ps-green));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        transition: transform .3s;
    }

    .gallery-item:hover {
        transform: scale(1.03);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gallery-item:nth-child(1) {
        background: linear-gradient(135deg, #ffecd2, #fcb69f);
    }

    .gallery-item:nth-child(2) {
        background: linear-gradient(135deg, #a8edea, #fed6e3);
    }

    .gallery-item:nth-child(3) {
        background: linear-gradient(135deg, #ffecd2, #d4fc79);
    }

    /* ── REGISTER (CONTACT) ── */
    .contact-section {
        padding: 5rem 0;
        background: var(--ps-light);
    }

    .contact-split {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 3rem;
        margin-top: 3rem;
        align-items: start;
    }

    .contact-form-card {
        background: white;
        border-radius: 28px;
        padding: 2.5rem;
        border: 2px solid rgba(255, 123, 84, .12);
        box-shadow: 0 10px 40px rgba(255, 123, 84, .1);
        position: relative;
        overflow: hidden;
    }

    .contact-form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--ps-orange), var(--ps-yellow), var(--ps-blue));
    }

    .contact-form-card h3 {
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--ps-dark);
        margin-bottom: 1.75rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: .4rem;
        margin-bottom: 1rem;
    }

    .form-group label {
        font-size: .78rem;
        font-weight: 800;
        color: var(--ps-mid);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .form-group label i {
        color: var(--ps-orange);
        margin-right: 4px;
    }

    .form-group input,
    .form-group select {
        padding: .85rem 1rem;
        border-radius: 14px;
        border: 2px solid rgba(0, 0, 0, .07);
        background: var(--ps-light);
        font-family: 'Outfit', sans-serif;
        font-size: .93rem;
        color: var(--ps-dark);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--ps-orange);
        box-shadow: 0 0 0 4px rgba(255, 123, 84, .12);
    }

    .btn-full {
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--ps-orange), #ff9f7f);
        color: white;
        font-family: 'Outfit', sans-serif;
        font-size: .95rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        box-shadow: 0 6px 20px rgba(255, 123, 84, .3);
        transition: transform .2s, box-shadow .2s;
        margin-top: .5rem;
    }

    .btn-full:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(255, 123, 84, .4);
    }

    .contact-info-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .contact-info-card {
        background: white;
        border-radius: 20px;
        padding: 1.3rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
        border: 2px solid rgba(0, 0, 0, .04);
        transition: transform .2s;
    }

    .contact-info-card:hover {
        transform: translateX(4px);
    }

    .contact-icon-wrapper {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .contact-info-card h4 {
        font-size: .9rem;
        font-weight: 800;
        color: var(--ps-dark);
        margin-bottom: .2rem;
    }

    .contact-info-card p {
        font-size: .82rem;
        color: var(--ps-mid);
        line-height: 1.5;
        margin: 0;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {

        .about-layout,
        .values-layout,
        .contact-split {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .steps-row {
            flex-direction: column;
        }

        .step-arrow {
            transform: rotate(90deg);
        }

        .hero-layout {
            grid-template-columns: 1fr !important;
        }

        .hero-visual {
            display: none;
        }
    }

    @media (max-width: 600px) {

        .features-grid,
        .gallery-grid,
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }

    /* ── Reveal animations ── */
    .reveal {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity .7s, transform .7s;
    }

    .reveal.visible {
        opacity: 1;
        transform: none;
    }

    .reveal-right {
        opacity: 0;
        transform: translateX(32px);
        transition: opacity .7s, transform .7s;
    }

    .reveal-right.visible {
        opacity: 1;
        transform: none;
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 123, 84, .3);
        border-radius: 8px;
    }
</style>

<!-- Back to top -->
<button id="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<main>
    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="hero">
        <div class="container hero-layout"
            style="display:grid;grid-template-columns:1.1fr 1fr;gap:3rem;align-items:center;">
            <div class="hero-content reveal">
                <div class="hero-badge">
                    <i class="fas fa-school"></i> Al Amad Private School — Tunisia
                </div>
                <h1>A Happy Place to<br><span>Learn & Grow 🌟</span></h1>
                <p>A warm, nurturing environment where every child discovers their potential. Our modern platform keeps
                    parents and teachers connected every step of the way.</p>
                <div class="hero-actions">
                    <a href="auth/login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Access Portal
                    </a>
                    <a href="#register" class="btn btn-ghost btn-lg">
                        <i class="fas fa-star"></i> Enroll Your Child
                    </a>
                </div>
                <div class="hero-trust">
                    <div class="trust-item"><i class="fas fa-heart" style="color:var(--ps-red)"></i> Caring Teachers
                    </div>
                    <div class="trust-item"><i class="fas fa-shield-alt" style="color:var(--ps-green)"></i> Safe Campus
                    </div>
                    <div class="trust-item"><i class="fas fa-star" style="color:var(--ps-yellow)"></i> Top Results</div>
                </div>
            </div>
            <div class="hero-visual reveal-right">
                <div class="hero-img-container"
                    style="display:flex;align-items:center;justify-content:center;position:relative;">
                    <div class="decor-emoji e1">📚</div>
                    <div class="decor-emoji e2">✏️</div>
                    <div class="hero-logo-ring">
                        <div style="font-size:5.5rem;line-height:1;animation:rotateSlow 20s linear infinite reverse;">🏫
                        </div>
                    </div>
                    <div class="hero-badge-float top">
                        <div class="hbf-label">Students</div>
                        <div class="hbf-val" style="color:var(--ps-orange)">500+</div>
                    </div>
                    <div class="hero-badge-float bot">
                        <div class="hbf-label">Satisfaction</div>
                        <div class="hbf-val" style="color:var(--ps-blue)">98%</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ STATS ═══════════════════ -->
    <div class="stats-band">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-num">500+</span>
                    <span class="stat-lbl">🎒 Happy Students</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">40+</span>
                    <span class="stat-lbl">👩‍🏫 Expert Teachers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">15+</span>
                    <span class="stat-lbl">⭐ Years of Excellence</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">98%</span>
                    <span class="stat-lbl">❤️ Parent Satisfaction</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ FEATURES ═══════════════════ -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header-block reveal" style="text-align:center;max-width:560px;margin:0 auto;">
                <span class="section-label">Platform</span>
                <h2 class="section-title">Everything your school needs</h2>
                <p class="section-sub">One powerful platform connecting parents, students, and teachers — every single
                    day.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-grades.png" alt="Grades tracking">
                    </div>
                    <h3>🌟 Grade Tracking</h3>
                    <p>Parents and students see live grades, subject averages, and academic progress at any time.</p>
                    <a href="auth/login.php" class="feature-link">View Grades →</a>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-schedule.png" alt="Timetables">
                    </div>
                    <h3>📅 Timetables</h3>
                    <p>Always up-to-date class schedules, exam dates, and extracurricular activities.</p>
                    <a href="auth/login.php" class="feature-link">See Schedule →</a>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-payments.png" alt="Easy payments">
                    </div>
                    <h3>💳 Easy Payments</h3>
                    <p>Secure monthly or annual tuition payments with instant digital receipts.</p>
                    <a href="#register" class="feature-link">Pay Fees →</a>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-messages.png" alt="Messaging">
                    </div>
                    <h3>💬 Messaging</h3>
                    <p>Direct messaging between parents, teachers, and the administration in real time.</p>
                    <a href="auth/login.php" class="feature-link">Open Messages →</a>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-attendance.png" alt="Attendance">
                    </div>
                    <h3>✅ Attendance</h3>
                    <p>Automated daily attendance with instant alerts to parents for any absence.</p>
                    <a href="auth/login.php" class="feature-link">Track Now →</a>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <img src="assets/img/features/feature-notices.png" alt="Announcements">
                    </div>
                    <h3>📢 Announcements</h3>
                    <p>School news, homework assignments, and event notices — always in reach.</p>
                    <a href="auth/login.php" class="feature-link">See Notices →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ ABOUT ═══════════════════ -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-layout">
                <div class="about-visual reveal">
                    <div class="about-img-main">
                        <img src="assets/img/school-art.jpg" alt="Students" onerror="this.parentElement.innerHTML='🎨'">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                        <div class="about-img-sub" style="margin-top:0;">
                            <img src="assets/img/school-reading.jpg" alt="Reading"
                                onerror="this.parentElement.innerHTML='📖'">
                        </div>
                        <div class="about-experience">
                            <strong>15+</strong>
                            <span>Years of<br>Excellence</span>
                        </div>
                    </div>
                </div>
                <div class="about-text reveal-right">
                    <span class="section-label">About Us</span>
                    <h2>Where <span>learning</span><br>becomes an adventure</h2>
                    <p>Founded with a vision to nurture the next generation of leaders, Al Amad School blends warm
                        Tunisian educational values with modern, tech-driven learning methodologies.</p>
                    <p>Our dedicated educators focus on academic success, character building, critical thinking, and
                        creativity — ensuring every child reaches their true potential.</p>
                    <div class="about-bullets">
                        <div class="bullet"><i class="fas fa-check-circle" style="color:var(--ps-green)"></i> Expert &
                            Caring Teaching Staff</div>
                        <div class="bullet"><i class="fas fa-check-circle" style="color:var(--ps-blue)"></i>
                            State-of-the-Art Facilities</div>
                        <div class="bullet"><i class="fas fa-check-circle" style="color:var(--ps-orange)"></i>
                            Comprehensive Curriculum</div>
                        <div class="bullet"><i class="fas fa-check-circle" style="color:var(--ps-yellow)"></i> Safe &
                            Nurturing Environment</div>
                    </div>
                    <a href="#register" class="btn btn-primary"><i class="fas fa-star"></i> Enroll Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ VALUES ═══════════════════ -->
    <section id="values" class="values-section">
        <div class="container">
            <div class="values-layout">
                <div class="values-text reveal">
                    <span class="section-label">Our Mission</span>
                    <h2>Built on <span>core values</span></h2>
                    <p>Al Amad School fosters a culture where every student thrives academically and morally, guided by
                        three foundational pillars of excellence.</p>
                </div>
                <div class="values-cards">
                    <div class="value-card reveal">
                        <div class="value-num">01</div>
                        <div>
                            <h3>⭐ Excellence</h3>
                            <p>Striving for the highest academic standards and positive character development every day.
                            </p>
                        </div>
                    </div>
                    <div class="value-card reveal">
                        <div class="value-num" style="color:var(--ps-blue)">02</div>
                        <div>
                            <h3>💡 Innovation</h3>
                            <p>Embracing modern technology and creative thinking in every classroom and lesson.</p>
                        </div>
                    </div>
                    <div class="value-card reveal">
                        <div class="value-num" style="color:var(--ps-green)">03</div>
                        <div>
                            <h3>❤️ Integrity</h3>
                            <p>Fostering honesty, respect, and global citizenship from the very first day of school.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ GALLERY ═══════════════════ -->
    <section id="gallery" class="gallery-section">
        <div class="container">
            <div class="section-header-block reveal" style="text-align:center;max-width:500px;margin:0 auto;">
                <span class="section-label">Campus Life</span>
                <h2 class="section-title">Our students in action 🎉</h2>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item reveal">
                    <img src="assets/img/school-art.jpg" alt="Art class"
                        onerror="this.outerHTML='<div class=\'gallery-item reveal visible\' style=\'font-size:4rem;\'>🎨</div>'">
                </div>
                <div class="gallery-item reveal">
                    <img src="assets/img/school-reading.jpg" alt="Reading"
                        onerror="this.outerHTML='<div class=\'gallery-item reveal visible\' style=\'font-size:4rem;\'>📖</div>'">
                </div>
                <div class="gallery-item reveal">
                    <img src="assets/img/school-classroom.jpg" alt="Classroom"
                        onerror="this.outerHTML='<div class=\'gallery-item reveal visible\' style=\'font-size:4rem;\'>🏫</div>'">
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ ENROLLMENT STEPS ═══════════════════ -->
    <section id="enrollment" class="enrollment-section">
        <div class="container">
            <div class="section-header-block reveal" style="text-align:center;max-width:520px;margin:0 auto;">
                <span class="section-label">Get Started</span>
                <h2 class="section-title">Join Al Amad in 3 easy steps</h2>
            </div>
            <div class="steps-row">
                <div class="step-card reveal">
                    <div class="step-num">1</div>
                    <h4><i class="fas fa-edit" style="color:var(--ps-orange)"></i> Fill the Form</h4>
                    <p>Complete the simple enrollment form below with your child's details and payment preference.</p>
                </div>
                <div class="step-arrow">→</div>
                <div class="step-card reveal">
                    <div class="step-num" style="background:linear-gradient(135deg,var(--ps-blue),var(--ps-green))">2
                    </div>
                    <h4><i class="fas fa-check-circle" style="color:var(--ps-blue)"></i> We Review</h4>
                    <p>Our admissions team reviews your application and contacts you to confirm everything.</p>
                </div>
                <div class="step-arrow">→</div>
                <div class="step-card reveal">
                    <div class="step-num" style="background:linear-gradient(135deg,var(--ps-green),var(--ps-blue))">3
                    </div>
                    <h4><i class="fas fa-id-card" style="color:var(--ps-green)"></i> Welcome Aboard!</h4>
                    <p>Receive your parent portal credentials and start tracking your child's progress.</p>
                </div>
            </div>
            <div class="steps-cta reveal">
                <a href="#register" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Start Enrollment
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTACT SECTION ═══════════════════ -->
    <section id="contact" class="contact-section" style="background: white; padding-bottom: 3rem;">
        <div class="container">
            <div class="section-header-block reveal"
                style="text-align:center;max-width:560px;margin:0 auto; margin-bottom: 3rem;">
                <span class="section-label">Connect</span>
                <h2 class="section-title">Get in Touch 📞</h2>
                <p class="section-sub">Have questions or need assistance? Our friendly team is ready to help you!</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
                <!-- Visit -->
                <div class="contact-info-card reveal"
                    style="padding: 2.5rem; flex-direction: column; text-align: center; gap: 1.5rem;">
                    <div class="contact-icon-wrapper"
                        style="background:var(--ps-orange); color:white; width: 70px; height: 70px; font-size: 2rem; margin: 0 auto; border-radius: 20px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.2rem; margin-bottom: .5rem;">Our Campus 📍</h4>
                        <p style="font-size: .95rem; line-height: 1.6;">Al Amad Private School<br>Rue de l'Éducation,
                            Tunis 1000, Tunisia</p>
                    </div>
                </div>

                <!-- Email -->
                <div class="contact-info-card reveal"
                    style="padding: 2.5rem; flex-direction: column; text-align: center; gap: 1.5rem; transition-delay: .1s;">
                    <div class="contact-icon-wrapper"
                        style="background:var(--ps-blue); color:white; width: 70px; height: 70px; font-size: 2rem; margin: 0 auto; border-radius: 20px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.2rem; margin-bottom: .5rem;">Email Us 📧</h4>
                        <p style="font-size: .95rem; line-height: 1.6;">contact@alamad.edu.tn<br>support@alamad.edu.tn
                        </p>
                    </div>
                </div>

                <!-- Call -->
                <div class="contact-info-card reveal"
                    style="padding: 2.5rem; flex-direction: column; text-align: center; gap: 1.5rem; transition-delay: .2s;">
                    <div class="contact-icon-wrapper"
                        style="background:var(--ps-green); color:white; width: 70px; height: 70px; font-size: 2rem; margin: 0 auto; border-radius: 20px;">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.2rem; margin-bottom: .5rem;">Call Us 📞</h4>
                        <p style="font-size: .95rem; line-height: 1.6;">+216 *** *** ***<br>Mon–Fri, 8:00 AM – 4:00 PM
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ REGISTRATION FORM ═══════════════════ -->
    <section id="register" class="contact-section" style="padding-top: 0;">
        <div class="container">
            <div class="section-header-block reveal"
                style="text-align:center;max-width:560px;margin:0 auto; margin-bottom: 3rem;">
                <span class="section-label">Enrollment</span>
                <h2 class="section-title">Pre-Register Your Child</h2>
                <p class="section-sub">Fill out the form below and our team will review your application and get in
                    touch with you shortly.</p>
            </div>

            <div class="contact-form-card reveal" style="max-width: 800px; margin: 0 auto;">
                <h3><i class="fas fa-user-plus" style="color:var(--ps-orange);margin-right:.5rem;"></i>Visitor
                    Registration Form</h3>
                <div id="formStatus" style="display:none;margin-bottom:1rem;"></div>
                <form id="registrationForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Parent First Name</label>
                            <input type="text" name="parent_first_name" placeholder="John" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Parent Last Name</label>
                            <input type="text" name="parent_last_name" placeholder="Doe" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Parent Email Address</label>
                        <input type="email" name="parent_email" placeholder="john.doe@example.com" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-child"></i> Child First Name</label>
                            <input type="text" name="child_first_name" placeholder="Jane" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-child"></i> Child Last Name</label>
                            <input type="text" name="child_last_name" placeholder="Doe" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-birthday-cake"></i> Child Age</label>
                            <input type="number" name="child_age" placeholder="7" min="3" max="15" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Child Gender</label>
                            <select name="child_gender" required>
                                <option value="">Select…</option>
                                <option value="male">Male 👦</option>
                                <option value="female">Female 👧</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-wallet"></i> Payment Plan</label>
                        <div style="display:flex;gap:1.5rem;margin-top:.6rem;">
                            <label
                                style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;font-size:.9rem;color:var(--ps-dark);">
                                <input type="radio" name="payment_plan" value="monthly" checked
                                    style="accent-color:var(--ps-orange);width:16px;height:16px;">
                                <i class="fas fa-calendar-week" style="color:var(--ps-orange)"></i> Monthly
                            </label>
                            <label
                                style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;font-size:.9rem;color:var(--ps-dark);">
                                <input type="radio" name="payment_plan" value="annual"
                                    style="accent-color:var(--ps-blue);width:16px;height:16px;">
                                <i class="fas fa-calendar" style="color:var(--ps-blue)"></i> Annual
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn-full" id="regSubmitBtn">
                        <span id="regBtnText"><i class="fas fa-paper-plane"></i> Submit Application</span>
                        <span id="regBtnLoad" style="display:none;"><i class="fas fa-circle-notch fa-spin"></i>
                            Submitting…</span>
                    </button>
                </form>
            </div>
        </div>
        <script>
            // Registration form submit
            document.getElementById('registrationForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                const status = document.getElementById('formStatus');
                const btnText = document.getElementById('regBtnText');
                const btnLoad = document.getElementById('regBtnLoad');

                btnText.style.display = 'none';
                btnLoad.style.display = 'inline-flex';
                status.style.display = 'none';

                fetch('api/register_visitor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(d => {
                        status.style.display = 'block';
                        if (d.success) {
                            status.innerHTML = '<div style="padding:1rem 1.25rem;background:rgba(6,214,160,.1);border:1.5px solid rgba(6,214,160,.3);border-radius:14px;color:#027a5e;font-weight:700;display:flex;align-items:center;gap:.6rem;"><i class=\'fas fa-check-circle\'></i> Registration submitted! Our team will review and contact you soon. 🎉</div>';
                            this.reset();
                        } else {
                            status.innerHTML = `<div style="padding:1rem 1.25rem;background:rgba(255,123,84,.1);border:1.5px solid rgba(255,123,84,.3);border-radius:14px;color:#c0392b;font-weight:700;display:flex;align-items:center;gap:.6rem;"><i class='fas fa-exclamation-circle'></i> ${d.message}</div>`;
                        }
                        btnText.style.display = 'inline-flex';
                        btnLoad.style.display = 'none';
                        if (d.success) status.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    })
                    .catch(() => {
                        status.innerHTML = '<div style="padding:1rem;background:rgba(255,123,84,.1);border:1.5px solid rgba(255,123,84,.3);border-radius:14px;color:#c0392b;font-weight:700;"><i class=\'fas fa-wifi\'></i> Network error. Please try again.</div>';
                        status.style.display = 'block';
                        btnText.style.display = 'inline-flex';
                        btnLoad.style.display = 'none';
                    });
            });

            // Scroll-reveal
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.reveal, .reveal-right').forEach(el => obs.observe(el));
        </script>
</main>

<?php include 'includes/footer.php'; ?>