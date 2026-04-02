<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('any');
$current_role = $_SESSION['role'];
$user_id      = $_SESSION['user_id'];

// Resolve user_init from email or role
$raw_email = $_SESSION['email'] ?? '';
$user_init = strtoupper(substr($raw_email ?: $current_role, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme','light');</script>
<style>
/* ── Messages Layout ── */
.chat-wrapper {
    display: flex;
    height: calc(100vh - 70px);  /* full below sticky header */
    overflow: hidden;
}

/* Contacts sidebar */
.contacts-pane {
    width: 300px;
    min-width: 300px;
    border-right: 1px solid var(--glass-border);
    display: flex;
    flex-direction: column;
    background: var(--card-bg);
    transition: transform .3s ease;
}
.contacts-head {
    padding: 1.25rem 1.5rem;
    font-weight: 800;
    font-size: .95rem;
    border-bottom: 1px solid var(--glass-border);
    background: var(--bg);
    display: flex;
    align-items: center;
    gap: .6rem;
}
.contacts-search {
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--glass-border);
}
.contacts-search input {
    width: 100%;
    padding: .55rem .9rem;
    border-radius: 10px;
    border: 1px solid var(--glass-border);
    background: var(--bg);
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    font-size: .85rem;
    outline: none;
}
.contact-list { flex: 1; overflow-y: auto; }
.contact-item {
    padding: .9rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    border-bottom: 1px solid var(--glass-border);
    transition: background .18s;
}
.contact-item:hover  { background: var(--bg); }
.contact-item.active { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
.contact-item.active .contact-role { color: rgba(255,255,255,.75); }
.contact-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .9rem; color: white;
    flex-shrink: 0;
}
.contact-name  { font-weight: 700; font-size: .88rem; }
.contact-role  { font-size: .72rem; color: var(--text-muted); text-transform: capitalize; }
.contact-unread {
    margin-left: auto;
    background: var(--primary);
    color: white;
    width: 20px; height: 20px;
    border-radius: 50%;
    font-size: .65rem;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}

/* Chat area */
.chat-pane {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--bg);
}
.chat-header-bar {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--glass-border);
    background: var(--card-bg);
    display: flex;
    align-items: center;
    gap: .9rem;
}
.chat-header-bar .back-btn {
    display: none;
    background: none; border: none;
    color: var(--primary); font-size: 1.15rem; cursor: pointer;
}
.chat-partner-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 800; font-size: .82rem;
}
.chat-partner-name { font-weight: 700; font-size: .95rem; }
.chat-partner-role { font-size: .72rem; color: var(--text-muted); text-transform: capitalize; }
.chat-status       { margin-left: auto; font-size: .75rem; color: #10b981; font-weight: 600; }

.messages-feed {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.msg-wrap {
    display: flex;
    align-items: flex-end;
    gap: .5rem;
}
.msg-wrap.sent  { flex-direction: row-reverse; }
.msg-bubble {
    max-width: 72%;
    padding: .75rem 1.1rem;
    border-radius: 18px;
    font-size: .88rem;
    line-height: 1.5;
    word-break: break-word;
}
.msg-bubble.sent     { background: linear-gradient(135deg, var(--primary), #ff8e53); color: white; border-bottom-right-radius: 4px; }
.msg-bubble.received { background: var(--card-bg); border: 1px solid var(--glass-border); color: var(--text); border-bottom-left-radius: 4px; }
.msg-meta { font-size: .68rem; color: var(--text-muted); white-space: nowrap; }
.msg-avatar-sm {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .7rem; color: white; flex-shrink: 0;
}

/* Input bar */
.chat-input-bar {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--glass-border);
    background: var(--card-bg);
    display: flex;
    align-items: center;
    gap: .75rem;
}
.msg-input {
    flex: 1;
    padding: .75rem 1.25rem;
    border-radius: 25px;
    border: 1.5px solid var(--glass-border);
    background: var(--bg);
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    font-size: .9rem;
    outline: none;
    transition: border-color .2s;
}
.msg-input:focus { border-color: var(--primary); }
.send-btn {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 4px 12px rgba(0,0,0,.2);
}
.send-btn:hover { transform: scale(1.08); }
.send-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; }

/* Empty / placeholder */
.chat-placeholder {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    gap: 1rem;
}
.chat-placeholder i {
    font-size: 3.5rem;
    opacity: .15;
}

/* Mobile responsive */
@media (max-width: 900px) {
    .contacts-pane {
        position: absolute;
        inset: 0;
        width: 100%;
        z-index: 10;
        transition: transform .3s ease;
    }
    .chat-pane {
        position: absolute;
        inset: 0;
        width: 100%;
        z-index: 9;
        transform: translateX(100%);
        transition: transform .3s ease;
    }
    .chat-wrapper { position: relative; }
    .chat-wrapper.chat-active .contacts-pane { transform: translateX(-100%); }
    .chat-wrapper.chat-active .chat-pane { transform: translateX(0); }
    .chat-header-bar .back-btn { display: flex; }
}
</style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
<?php
$dash_title = 'Messages';
$dash_sub   = 'Direct messaging with staff and administration';
$user_init  = strtoupper(substr($_SESSION['email'] ?? $_SESSION['role'] ?? 'U', 0, 2));
include '../includes/dash-header.php';

?>

    <div class="chat-wrapper" id="chatWrapper">

        <!-- Contacts Pane -->
        <div class="contacts-pane" id="contactsPane">
            <div class="contacts-head">
                <i class="fas fa-users" style="color:var(--primary);"></i> Contacts
            </div>
            <div class="contacts-search">
                <input type="search" id="contactSearch" placeholder="🔍 Search contacts…">
            </div>
            <div class="contact-list" id="contactList">
                <div style="padding:2rem; text-align:center; color:var(--text-muted);">
                    <i class="fas fa-circle-notch fa-spin" style="font-size:1.5rem; opacity:.4;"></i>
                </div>
            </div>
        </div>

        <!-- Chat Pane -->
        <div class="chat-pane" id="chatPane">
            <!-- Header -->
            <div class="chat-header-bar" id="chatHeaderBar">
                <button class="back-btn" onclick="closeChat()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="chat-partner-avatar" id="chatPartnerAvatar" style="background:var(--primary);">?</div>
                <div>
                    <div class="chat-partner-name" id="chatPartnerName">Select a contact</div>
                    <div class="chat-partner-role" id="chatPartnerRole">to start chatting</div>
                </div>
                <div class="chat-status" id="chatStatus"></div>
            </div>

            <!-- Messages Feed -->
            <div class="messages-feed" id="messagesFeed">
                <div class="chat-placeholder">
                    <i class="fas fa-comments"></i>
                    <p style="font-weight:600;">No conversation selected</p>
                    <p style="font-size:.82rem; opacity:.6;">Choose a contact from the left to start chatting</p>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="chat-input-bar">
                <input type="text" class="msg-input" id="msgInput" placeholder="Type a message…" disabled>
                <button class="send-btn" id="sendBtn" disabled>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div><!-- /chat-wrapper -->
</div>

<script>
const ME = '<?= $user_id ?>';
let currentContactId   = null;
let currentContactName = '';
let allContacts        = [];
let pollTimer          = null;

// ── Load Contacts ──────────────────────────────────────────
function loadContacts() {
    fetch('../api/messages.php?contacts=1')
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            allContacts = d.contacts;
            renderContacts(allContacts);
        });
}

function renderContacts(contacts) {
    const list = document.getElementById('contactList');
    if (!contacts.length) {
        list.innerHTML = '<p style="padding:2rem; text-align:center; opacity:.5; font-size:.85rem;">No contacts found.</p>';
        return;
    }
    const roleColors = { admin:'#6366f1', teacher:'#1dd1a1', parent:'#f9ca24' };
    list.innerHTML = contacts.map(c => {
        const initial = c.email.charAt(0).toUpperCase();
        const nameStr = c.email.split('@')[0];
        const color   = roleColors[c.role] || '#94a3b8';
        const badge   = c.unread_count > 0 ? `<span class="contact-unread">${c.unread_count}</span>` : '';
        return `<div class="contact-item ${c.id === currentContactId ? 'active' : ''}"
                     data-id="${c.id}" data-name="${nameStr}" data-role="${c.role}" data-color="${color}"
                     onclick="selectContact(this, '${c.id}', '${nameStr}', '${c.role}', '${color}')">
                    <div class="contact-avatar" style="background:${color}">${initial}</div>
                    <div>
                        <div class="contact-name">${nameStr}</div>
                        <div class="contact-role"><i class="fas fa-${c.role === 'admin' ? 'shield-halved' : c.role === 'teacher' ? 'chalkboard-teacher' : 'user-friends'}"></i> ${c.role}</div>
                    </div>
                    ${badge}
                </div>`;
    }).join('');
}

// Live search
document.getElementById('contactSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    renderContacts(allContacts.filter(c => c.email.toLowerCase().includes(q) || c.role.includes(q)));
});

// ── Select Contact ─────────────────────────────────────────
function selectContact(el, id, name, role, color) {
    currentContactId   = id;
    currentContactName = name;

    // Update header
    document.getElementById('chatPartnerAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('chatPartnerAvatar').style.background = color;
    document.getElementById('chatPartnerName').textContent = name;
    document.getElementById('chatPartnerRole').textContent = role;
    document.getElementById('chatStatus').textContent = '● Online';

    // Enable input
    document.getElementById('msgInput').disabled = false;
    document.getElementById('sendBtn').disabled  = false;
    document.getElementById('msgInput').focus();

    // Mobile: switch view
    document.getElementById('chatWrapper').classList.add('chat-active');

    loadMessages();
    clearInterval(pollTimer);
    pollTimer = setInterval(loadMessages, 5000);
}

function closeChat() {
    document.getElementById('chatWrapper').classList.remove('chat-active');
    clearInterval(pollTimer);
    currentContactId = null;
}

// ── Load Messages ──────────────────────────────────────────
function loadMessages() {
    if (!currentContactId) return;
    fetch(`../api/messages.php?with_id=${currentContactId}`)
        .then(r => r.json())
        .then(d => {
            const feed = document.getElementById('messagesFeed');
            if (!d.success) return;
            const msgs = d.messages;
            const atBottom = feed.scrollTop + feed.clientHeight >= feed.scrollHeight - 40;

            if (!msgs.length) {
                feed.innerHTML = `<div class="chat-placeholder">
                    <i class="fas fa-comment-dots"></i>
                    <p style="font-weight:600;">No messages yet</p>
                    <p style="font-size:.82rem; opacity:.6;">Be the first to say hello!</p>
                </div>`;
                return;
            }

            feed.innerHTML = msgs.map(m => {
                const isMine  = m.sender_id == ME;
                const initial = m.sender_email ? m.sender_email.charAt(0).toUpperCase() : '?';
                const time    = new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                return `<div class="msg-wrap ${isMine ? 'sent' : 'received'}">
                    ${!isMine ? `<div class="msg-avatar-sm" style="background:var(--primary)">${initial}</div>` : ''}
                    <div>
                        <div class="msg-bubble ${isMine ? 'sent' : 'received'}">${escHtml(m.content)}</div>
                        <div class="msg-meta" style="text-align:${isMine ? 'right' : 'left'}; margin-top:.25rem;">${time}</div>
                    </div>
                    ${isMine ? `<div class="msg-avatar-sm" style="background:var(--secondary);color:white;"><?= strtoupper(substr($raw_email ?: 'ME', 0, 1)) ?></div>` : ''}
                </div>`;
            }).join('');

            if (atBottom) feed.scrollTop = feed.scrollHeight;
            // Refresh unread counts in contact list
            loadContacts();
        });
}

// ── Send Message ───────────────────────────────────────────
function sendMessage() {
    const input   = document.getElementById('msgInput');
    const content = input.value.trim();
    if (!content || !currentContactId) return;

    input.disabled = true;
    fetch('../api/messages.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ receiver_id: currentContactId, content })
    })
    .then(r => r.json())
    .then(d => {
        input.disabled = false;
        if (d.success) { input.value = ''; loadMessages(); }
    })
    .catch(() => { input.disabled = false; });
    input.focus();
}

document.getElementById('sendBtn').onclick = sendMessage;
document.getElementById('msgInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', loadContacts);
</script>
</body>
</html>
