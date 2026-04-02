<?php
if (session_status() === PHP_SESSION_NONE)
  session_start();
if (isset($_SESSION['user_id'])) {
  $r = $_SESSION['role'] ?? '';
  header('Location: ../dashboard/' . ($r === 'teacher' ? 'teacher.php' : ($r === 'parent' ? 'parent.php' : 'index.php')));
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — Al Amad School</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    /* ── Primary School Color Palette ── */
    :root {
      --ps-orange: #FF7B54;
      --ps-blue: #4ECDC4;
      --ps-yellow: #FFD166;
      --ps-green: #06D6A0;
      --ps-purple: #a29bfe;
      --ps-red: #ef476f;
      --ps-dark: #2d3436;
      --ps-mid: #636e72;
      --ps-light: #fff8f5;
      --ps-white: #ffffff;
      --r: 20px;
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html,
    body {
      height: 100%;
      font-family: 'Outfit', sans-serif;
      overflow: hidden;
    }

    /* ── Background ── */
    .bg-wrap {
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse at 15% 20%, rgba(255, 123, 84, .15) 0%, transparent 50%),
        radial-gradient(ellipse at 85% 85%, rgba(78, 205, 196, .12) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(255, 209, 102, .07) 0%, transparent 60%),
        #fff8f5;
      z-index: 0;
    }

    /* Floating blobs */
    .blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(70px);
      opacity: .25;
      pointer-events: none;
      animation: blobFloat 12s ease-in-out infinite;
    }

    .blob1 {
      width: 420px;
      height: 420px;
      background: var(--ps-orange);
      top: -120px;
      left: -120px;
      animation-delay: 0s;
    }

    .blob2 {
      width: 350px;
      height: 350px;
      background: var(--ps-blue);
      bottom: -100px;
      right: -100px;
      animation-delay: -5s;
    }

    .blob3 {
      width: 260px;
      height: 260px;
      background: var(--ps-yellow);
      top: 40%;
      left: 35%;
      animation-delay: -9s;
    }

    @keyframes blobFloat {

      0%,
      100% {
        transform: translateY(0) scale(1);
      }

      50% {
        transform: translateY(-24px) scale(1.04);
      }
    }

    /* Dot grid */
    .dot-grid {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background-image: radial-gradient(rgba(255, 123, 84, .08) 1px, transparent 1px);
      background-size: 36px 36px;
    }

    /* ── Layout ── */
    .page {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
    }

    .login-wrap {
      display: flex;
      width: 100%;
      max-width: 1000px;
      min-height: 600px;
      border-radius: 30px;
      overflow: hidden;
      box-shadow: 0 30px 80px -15px rgba(255, 123, 84, .2), 0 0 0 1px rgba(255, 123, 84, .1);
      animation: cardIn .6s cubic-bezier(.175, .885, .32, 1.275) both;
    }

    @keyframes cardIn {
      from {
        opacity: 0;
        transform: translateY(28px) scale(.97);
      }

      to {
        opacity: 1;
        transform: none;
      }
    }

    /* ── LEFT PANEL ── */
    .v-panel {
      flex: 1;
      padding: 3rem;
      background: linear-gradient(150deg, var(--ps-orange) 0%, #ff9f7f 45%, var(--ps-yellow) 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
    }

    /* Decorative circles */
    .v-panel::before {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .08);
      top: -120px;
      right: -120px;
    }

    .v-panel::after {
      content: '';
      position: absolute;
      width: 280px;
      height: 280px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .06);
      bottom: -80px;
      left: -60px;
    }

    /* Wavy decoration */
    .v-wave {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 100px;
      background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 80'%3E%3Cpath fill='rgba(255,255,255,.08)' d='M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z'/%3E%3C/svg%3E") no-repeat bottom center / cover;
    }

    .v-content {
      position: relative;
      z-index: 1;
    }

    .v-logo {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2.5rem;
    }

    .v-logo-icon {
      width: 56px;
      height: 56px;
      border-radius: 18px;
      background: rgba(255, 255, 255, .25);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      font-weight: 900;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
      flex-shrink: 0;
      border: 2px solid rgba(255, 255, 255, .3);
    }

    .v-logo-icon img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 16px;
    }

    .v-logo-text strong {
      display: block;
      color: white;
      font-size: 1.15rem;
      font-weight: 900;
    }

    .v-logo-text span {
      font-size: .72rem;
      color: rgba(255, 255, 255, .75);
      text-transform: uppercase;
      letter-spacing: .08em;
    }

    .v-headline {
      font-size: clamp(1.8rem, 2.5vw, 2.5rem);
      font-weight: 900;
      color: white;
      line-height: 1.15;
      margin-bottom: 1rem;
    }

    .v-desc {
      color: rgba(255, 255, 255, .8);
      font-size: .92rem;
      line-height: 1.7;
      margin-bottom: 2rem;
      max-width: 310px;
    }

    .v-feats {
      display: flex;
      flex-direction: column;
      gap: .75rem;
    }

    .v-feat {
      display: flex;
      align-items: center;
      gap: .8rem;
      color: rgba(255, 255, 255, .9);
      font-size: .87rem;
      font-weight: 700;
    }

    .v-feat-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .2);
      color: white;
      font-size: .9rem;
    }

    /* Testimonial card */
    .v-testimonial {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, .2);
      border: 1px solid rgba(255, 255, 255, .3);
      border-radius: 22px;
      padding: 1.4rem;
      backdrop-filter: blur(10px);
    }

    .v-stars {
      font-size: .9rem;
      margin-bottom: .5rem;
    }

    .v-testimonial p {
      color: rgba(255, 255, 255, .9);
      font-size: .83rem;
      line-height: 1.6;
      font-style: italic;
      margin-bottom: .9rem;
    }

    .v-author {
      display: flex;
      align-items: center;
      gap: .65rem;
    }

    .v-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .3);
      border: 2px solid rgba(255, 255, 255, .4);
      color: white;
      font-weight: 900;
      font-size: .85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .v-author strong {
      display: block;
      color: white;
      font-size: .83rem;
    }

    .v-author small {
      color: rgba(255, 255, 255, .65);
      font-size: .72rem;
    }

    /* Emoji floaters */
    .emoji-float {
      position: absolute;
      font-size: 2rem;
      animation: emojiDrift 6s ease-in-out infinite;
      pointer-events: none;
      z-index: 2;
      opacity: .55;
    }

    .emoji-float.e1 {
      top: 10%;
      right: 15%;
      animation-delay: 0s;
    }

    .emoji-float.e2 {
      top: 45%;
      right: 8%;
      animation-delay: -2s;
      font-size: 1.6rem;
    }

    .emoji-float.e3 {
      bottom: 25%;
      right: 20%;
      animation-delay: -4s;
      font-size: 2.4rem;
    }

    @keyframes emojiDrift {

      0%,
      100% {
        transform: translateY(0) rotate(-5deg);
      }

      50% {
        transform: translateY(-14px) rotate(5deg);
      }
    }

    /* ── RIGHT PANEL ── */
    .f-panel {
      width: 430px;
      min-width: 430px;
      background: var(--ps-white);
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      position: relative;
      overflow-y: auto;
      border-left: 3px solid rgba(255, 123, 84, .12);
    }

    /* Utility icons */
    .f-utils {
      position: absolute;
      top: 1.25rem;
      right: 1.25rem;
      display: flex;
      gap: .5rem;
    }

    .f-util-btn {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: rgba(255, 123, 84, .08);
      border: 1.5px solid rgba(255, 123, 84, .15);
      color: var(--ps-mid);
      cursor: pointer;
      font-size: .9rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .2s;
      text-decoration: none;
    }

    .f-util-btn:hover {
      background: var(--ps-orange);
      color: white;
      border-color: transparent;
    }

    /* Role tabs */
    .role-tabs {
      display: flex;
      gap: .4rem;
      margin-bottom: 2rem;
      background: rgba(255, 123, 84, .06);
      border-radius: 16px;
      padding: 4px;
      border: 1.5px solid rgba(255, 123, 84, .1);
    }

    .role-tab {
      flex: 1;
      padding: .55rem;
      border: none;
      border-radius: 12px;
      font-family: 'Outfit', sans-serif;
      font-size: .8rem;
      font-weight: 700;
      cursor: pointer;
      background: transparent;
      color: var(--ps-mid);
      transition: all .25s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }

    .role-tab.active {
      background: var(--ps-white);
      color: var(--ps-orange);
      box-shadow: 0 2px 12px rgba(255, 123, 84, .15);
      border: 1.5px solid rgba(255, 123, 84, .2);
    }

    /* Heading */
    .f-heading {
      font-size: 1.65rem;
      font-weight: 900;
      color: var(--ps-dark);
      margin-bottom: .3rem;
    }

    .f-sub {
      color: var(--ps-mid);
      font-size: .88rem;
      margin-bottom: 1.75rem;
      line-height: 1.5;
    }

    /* Alert */
    .f-alert {
      padding: .85rem 1.1rem;
      border-radius: 16px;
      font-size: .85rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: .6rem;
      margin-bottom: 1.25rem;
      animation: alertIn .3s ease;
    }

    @keyframes alertIn {
      from {
        opacity: 0;
        transform: translateY(-8px);
      }

      to {
        opacity: 1;
        transform: none;
      }
    }

    .f-alert.ok {
      background: rgba(6, 214, 160, .1);
      color: #027a5e;
      border: 1.5px solid rgba(6, 214, 160, .3);
    }

    .f-alert.err {
      background: rgba(255, 123, 84, .1);
      color: #c0392b;
      border: 1.5px solid rgba(255, 123, 84, .3);
    }

    /* Fields */
    .field {
      margin-bottom: 1.2rem;
    }

    .field label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: .76rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .7px;
      color: var(--ps-mid);
      margin-bottom: .5rem;
    }

    .field label i {
      color: var(--ps-orange);
    }

    .inp-wrap {
      position: relative;
    }

    .inp-wrap input {
      width: 100%;
      padding: .9rem 1rem .9rem 2.8rem;
      border-radius: 14px;
      border: 2px solid rgba(0, 0, 0, .07);
      background: var(--ps-light);
      color: var(--ps-dark);
      font-family: 'Outfit', sans-serif;
      font-size: .93rem;
      outline: none;
      transition: all .25s;
    }

    .inp-wrap input:focus {
      border-color: var(--ps-orange);
      background: var(--ps-white);
      box-shadow: 0 0 0 4px rgba(255, 123, 84, .12);
      transform: translateY(-1px);
    }

    .inp-wrap .ico {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--ps-mid);
      font-size: .85rem;
      pointer-events: none;
      transition: color .25s;
    }

    .inp-wrap input:focus~.ico {
      color: var(--ps-orange);
    }

    .toggle-eye {
      position: absolute;
      right: .85rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--ps-mid);
      cursor: pointer;
      font-size: .9rem;
      padding: .25rem;
      transition: color .2s;
    }

    .toggle-eye:hover {
      color: var(--ps-orange);
    }

    .ferr {
      font-size: .73rem;
      color: var(--ps-orange);
      margin-top: .35rem;
      min-height: .95rem;
      display: block;
    }

    /* Options row */
    .f-opts {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      font-size: .83rem;
    }

    .f-check {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--ps-mid);
      cursor: pointer;
    }

    .f-check input {
      accent-color: var(--ps-orange);
      width: 15px;
      height: 15px;
    }

    .f-forgot {
      color: var(--ps-orange);
      text-decoration: none;
      font-weight: 700;
    }

    .f-forgot:hover {
      text-decoration: underline;
    }

    /* Submit button */
    .btn-go {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 16px;
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
      box-shadow: 0 6px 22px rgba(255, 123, 84, .35);
      transition: transform .2s, box-shadow .2s;
      position: relative;
      overflow: hidden;
    }

    .btn-go:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(255, 123, 84, .45);
    }

    .btn-go:disabled {
      opacity: .6;
      cursor: not-allowed;
    }

    /* Demo divider */
    .demo-divider {
      position: relative;
      text-align: center;
      margin: 1.5rem 0;
      font-size: .73rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: var(--ps-mid);
    }

    .demo-divider::before,
    .demo-divider::after {
      content: '';
      position: absolute;
      top: 50%;
      height: 1px;
      width: 28%;
      background: rgba(0, 0, 0, .06);
    }

    .demo-divider::before {
      left: 0;
    }

    .demo-divider::after {
      right: 0;
    }

    .demo-grid {
      display: flex;
      gap: .6rem;
    }

    .demo-chip {
      flex: 1;
      padding: .65rem .4rem;
      border: 2px solid rgba(255, 123, 84, .15);
      border-radius: 14px;
      background: rgba(255, 123, 84, .04);
      color: var(--ps-dark);
      font-family: 'Outfit', sans-serif;
      font-size: .8rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all .25s;
    }

    .demo-chip:hover {
      border-color: var(--ps-orange);
      background: rgba(255, 123, 84, .08);
      transform: translateY(-2px);
    }

    /* Home link */
    .f-home {
      text-align: center;
      margin-top: 1.25rem;
      font-size: .82rem;
      color: var(--ps-mid);
    }

    .f-home a {
      color: var(--ps-orange);
      font-weight: 700;
      text-decoration: none;
    }

    .f-home a:hover {
      text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 900px) {
      body {
        overflow: auto;
      }

      .login-wrap {
        flex-direction: column;
        max-width: 500px;
      }

      .v-panel {
        padding: 2rem;
        flex: none;
      }

      .v-headline {
        font-size: 1.5rem;
      }

      .v-desc,
      .v-testimonial {
        display: none;
      }

      .f-panel {
        width: 100%;
        min-width: 0;
        padding: 2rem 1.75rem;
      }
    }

    @media (max-width: 500px) {
      .page {
        padding: .75rem;
      }

      .login-wrap {
        border-radius: 22px;
      }

      .f-panel {
        padding: 1.5rem 1.25rem;
      }

      .demo-grid {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>
  <div class="bg-wrap">
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
  </div>
  <div class="dot-grid"></div>

  <div class="page">
    <div class="login-wrap">

      <!-- ██████ LEFT PANEL ██████ -->
      <div class="v-panel">
        <div class="emoji-float e1">⭐</div>
        <div class="emoji-float e2">✏️</div>
        <div class="emoji-float e3">🎒</div>
        <div class="v-wave"></div>

        <div class="v-content">
          <div class="v-logo">
            <div class="v-logo-icon">
              <img src="../assets/img/logo.png" alt="logo"
                onerror="this.style.display='none';this.parentNode.textContent='🏫'">
            </div>
            <div class="v-logo-text">
              <strong>Al Amad School</strong>
              <span>Tunisia · Est. 2008</span>
            </div>
          </div>

          <h1 class="v-headline" style="text-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            Learning is<br>
            the <span style="color: var(--ps-yellow); text-shadow: 0 2px 5px rgba(0,0,0,0.2);">greatest</span><br>
            adventure! 🌟
          </h1>
          <p class="v-desc" style="font-weight: 500; font-size: 1rem; opacity: 1; color: #fff;">
            <i class="fas fa-rocket" style="margin-right: 5px; color: var(--ps-yellow);"></i>
            Your complete school portal connecting parents, teachers, and administrators.
          </p>

          <div class="v-feats">
            <div class="v-feat">
              <div class="v-feat-icon"
                style="background: white; color: var(--ps-orange); font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-chart-bar"></i>
              </div>
              <span><strong style="display:block; font-size: 1rem;">Live Grades</strong> Tracking progress in
                real-time</span>
            </div>
            <div class="v-feat">
              <div class="v-feat-icon"
                style="background: white; color: var(--ps-blue); font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-bell"></i>
              </div>
              <span><strong style="display:block; font-size: 1rem;">Instant Alerts</strong> Stay updated with
                notifications</span>
            </div>
            <div class="v-feat">
              <div class="v-feat-icon"
                style="background: white; color: var(--ps-green); font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-comments"></i>
              </div>
              <span><strong style="display:block; font-size: 1rem;">Messaging</strong> Chat with teachers & staff</span>
            </div>
          </div>
        </div>

        <div class="v-testimonial">
          <div class="v-stars">⭐⭐⭐⭐⭐</div>
          <p>"An absolute game-changer for our school. Every parent loves how easy it is to track their child's
            progress."</p>
          <div class="v-author">
            <div class="v-avatar">S</div>
            <div>
              <strong>Sarah M.</strong>
              <small>Parent · Grade 5</small>
            </div>
          </div>
        </div>
      </div>

      <!-- ██████ RIGHT PANEL ██████ -->
      <div class="f-panel">

        <!-- Utility strip -->
        <div class="f-utils">
          <a href="../index.php" class="f-util-btn" title="Back to Home">
            <i class="fas fa-house"></i>
          </a>
        </div>

        <!-- Role tabs -->
        <div class="role-tabs" id="roleTabs">
          <button class="role-tab active" data-e="olfagammoudi5@gmail.com" data-p="password123">
            🛡️ Admin
          </button>
          <button class="role-tab" data-e="teacher@alamad.edu" data-p="password123">
            👩‍🏫 Teacher
          </button>
          <button class="role-tab" data-e="parent@alamad.edu" data-p="password123">
            👨‍👩‍👧 Parent
          </button>
        </div>

        <h2 class="f-heading">Welcome back! 👋</h2>
        <p class="f-sub">Sign in to your account and stay connected with your school community.</p>

        <!-- Alert -->
        <div id="alertBox" class="f-alert" style="display:none;"></div>

        <form id="loginForm" novalidate>

          <div class="field">
            <label><i class="fas fa-envelope"></i> Email Address</label>
            <div class="inp-wrap">
              <input type="email" id="email" placeholder="yourname@alamad.edu.tn" autocomplete="email" required>
              <span class="ico"><i class="fas fa-envelope"></i></span>
            </div>
            <span class="ferr" id="emailErr"></span>
          </div>

          <div class="field">
            <label><i class="fas fa-lock"></i> Password</label>
            <div class="inp-wrap">
              <input type="password" id="password" placeholder="••••••••" autocomplete="current-password" required>
              <span class="ico"><i class="fas fa-lock"></i></span>
              <button type="button" class="toggle-eye" id="eyeBtn">
                <i class="fas fa-eye" id="eyeIco"></i>
              </button>
            </div>
            <span class="ferr" id="pwErr"></span>
          </div>

          <div class="f-opts">
            <label class="f-check">
              <input type="checkbox" id="remember"> Keep me signed in
            </label>
            <a href="forgot-password.php" class="f-forgot">Forgot password?</a>
          </div>

          <button type="submit" class="btn-go" id="submitBtn">
            <span id="btnLabel"><i class="fas fa-sign-in-alt"></i> Sign In</span>
            <span id="btnSpinner" style="display:none;"><i class="fas fa-circle-notch fa-spin"></i> Signing in…</span>
          </button>
        </form>

        <div class="demo-divider">Quick demo access</div>
        <div class="demo-grid">
          <button class="demo-chip" data-e="olfagammoudi5@gmail.com" data-p="password123">
            🛡️ Admin
          </button>
          <button class="demo-chip" data-e="teacher@alamad.edu" data-p="password123">
            👩‍🏫 Teacher
          </button>
          <button class="demo-chip" data-e="parent@alamad.edu" data-p="password123">
            👨‍👩‍👧 Parent
          </button>
        </div>

        <div class="f-home">
          Don't have an account? <a href="../index.php#register">Enroll your child →</a>
        </div>

      </div><!-- /f-panel -->
    </div><!-- /login-wrap -->
  </div><!-- /page -->

  <script>
    // ── Role tabs → auto-fill ──
    document.getElementById('roleTabs').addEventListener('click', e => {
      const tab = e.target.closest('.role-tab');
      if (!tab) return;
      document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById('email').value = tab.dataset.e;
      document.getElementById('password').value = tab.dataset.p;
      clearErrors();
    });

    // ── Demo chips ──
    document.querySelectorAll('.demo-chip').forEach(c => {
      c.addEventListener('click', () => {
        document.getElementById('email').value = c.dataset.e;
        document.getElementById('password').value = c.dataset.p;
        clearErrors();
        document.querySelectorAll('.role-tab').forEach(t => {
          t.classList.toggle('active', t.dataset.e === c.dataset.e);
        });
      });
    });

    // ── Password toggle ──
    document.getElementById('eyeBtn').addEventListener('click', function () {
      const pw = document.getElementById('password');
      const ico = document.getElementById('eyeIco');
      const show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      ico.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    });

    // ── Helpers ──
    function clearErrors() {
      document.getElementById('emailErr').textContent = '';
      document.getElementById('pwErr').textContent = '';
    }
    function showAlert(msg, type) {
      const a = document.getElementById('alertBox');
      a.className = 'f-alert ' + type;
      a.innerHTML = (type === 'err'
        ? '<i class="fas fa-circle-exclamation"></i>'
        : '<i class="fas fa-circle-check"></i>'
      ) + ' ' + msg;
      a.style.display = 'flex';
    }

    // ── Form submit ──
    document.getElementById('loginForm').addEventListener('submit', async e => {
      e.preventDefault();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const btnLabel = document.getElementById('btnLabel');
      const btnSpin = document.getElementById('btnSpinner');
      const btn = document.getElementById('submitBtn');

      clearErrors();
      let ok = true;
      if (!email) { document.getElementById('emailErr').textContent = 'Email is required.'; ok = false; }
      if (!password) { document.getElementById('pwErr').textContent = 'Password is required.'; ok = false; }
      if (!ok) return;

      btnLabel.style.display = 'none';
      btnSpin.style.display = 'flex';
      btn.disabled = true;
      document.getElementById('alertBox').style.display = 'none';

      try {
        const res = await fetch('../api/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const data = await res.json();
        if (data.success) {
          showAlert('Login successful! Redirecting…', 'ok');
          setTimeout(() => window.location.href = data.redirect, 800);
        } else {
          showAlert(data.message || 'Invalid credentials. Please try again.', 'err');
          document.getElementById('password').value = '';
          btnLabel.style.display = 'flex'; btnSpin.style.display = 'none'; btn.disabled = false;
        }
      } catch {
        showAlert('Network error. Check your connection and try again.', 'err');
        btnLabel.style.display = 'flex'; btnSpin.style.display = 'none'; btn.disabled = false;
      }
    });
  </script>
</body>

</html>