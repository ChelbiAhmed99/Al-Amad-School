/**
 * Al Amad School — Notification + Sidebar System
 * Handles: sidebar toggle, notification bell, and real-time polling
 */

// ─── Sidebar Toggle ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar   = document.querySelector('.sidebar');

    if (toggleBtn && sidebar) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });

        // Close button inside sidebar
        const closeBtn = document.querySelector('.sidebar-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    }

    // Animate reveals
    document.querySelectorAll('.reveal').forEach(el => {
        setTimeout(() => el.classList.add('visible'), 80);
    });

    // Boot notification system
    initNotifications();
});

// ─── Notification System ──────────────────────────────────────
let notifOpen = false;

function initNotifications() {
    const bell = document.getElementById('notif-bell');
    const panel = document.getElementById('notif-panel');
    if (!bell || !panel) return;

    bell.addEventListener('click', (e) => {
        e.stopPropagation();
        notifOpen = !notifOpen;
        panel.classList.toggle('open', notifOpen);
        if (notifOpen) {
            loadNotifications();
        }
    });

    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && e.target !== bell) {
            panel.classList.remove('open');
            notifOpen = false;
        }
    });

    // Initial load
    fetchUnreadCount();
    // Poll every 30 seconds
    setInterval(fetchUnreadCount, 30000);
}

function fetchUnreadCount() {
    fetch('../api/notifications.php?limit=5')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const badge = document.getElementById('notif-badge');
            if (badge) {
                badge.textContent = data.unread_count;
                badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
            }
        })
        .catch(() => {});
}

function loadNotifications() {
    const list = document.getElementById('notif-list');
    if (!list) return;
    list.innerHTML = '<div class="notif-loading"><i class="fas fa-circle-notch fa-spin"></i></div>';

    fetch('../api/notifications.php?limit=15')
        .then(r => r.json())
        .then(data => {
            if (!data.success) { list.innerHTML = '<p class="notif-empty">Could not load notifications.</p>'; return; }
            const notes = data.notifications;
            if (notes.length === 0) {
                list.innerHTML = '<div class="notif-empty"><i class="fas fa-check-circle"></i><p>You\'re all caught up!</p></div>';
                return;
            }
            list.innerHTML = notes.map(n => `
                <div class="notif-item ${n.is_read == 0 ? 'unread' : ''}" data-id="${n.id}" onclick="handleNotifClick(${n.id}, '${n.link || ''}')">
                    <div class="notif-icon notif-${n.type}">
                        <i class="${typeIcon(n.type)}"></i>
                    </div>
                    <div class="notif-body">
                        <div class="notif-title">${escHtml(n.title)}</div>
                        <div class="notif-msg">${escHtml(n.message)}</div>
                        <div class="notif-time"><i class="far fa-clock"></i> ${timeAgo(n.created_at)}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => { list.innerHTML = '<p class="notif-empty">Error loading notifications.</p>'; });
}

function handleNotifClick(id, link) {
    fetch('../api/notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_read', id })
    });
    document.querySelector(`.notif-item[data-id="${id}"]`)?.classList.remove('unread');
    fetchUnreadCount();
    if (link) window.location.href = link;
}

function markAllRead() {
    fetch('../api/notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_all_read' })
    }).then(() => { fetchUnreadCount(); loadNotifications(); });
}

function typeIcon(type) {
    const icons = { info: 'fas fa-info-circle', success: 'fas fa-check-circle', warning: 'fas fa-exclamation-triangle', message: 'fas fa-comment-dots', grade: 'fas fa-star', payment: 'fas fa-receipt' };
    return icons[type] || 'fas fa-bell';
}

function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}
