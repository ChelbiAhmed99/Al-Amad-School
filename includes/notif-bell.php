<?php
/**
 * Reusable notification bell + dropdown panel
 * Include this inside any dashboard-header's header-right section
 */
?>
<div class="notif-wrap">
    <button class="notif-bell-btn" id="notif-bell" title="Notifications">
        <i class="fas fa-bell"></i>
        <span class="notif-badge" id="notif-badge" style="display:none">0</span>
    </button>
    <div class="notif-panel" id="notif-panel">
        <div class="notif-panel-header">
            <h4><i class="fas fa-bell" style="color:var(--primary); margin-right:.4rem;"></i>Notifications</h4>
            <button class="notif-mark-all" onclick="markAllRead()">Mark all read</button>
        </div>
        <div class="notif-list" id="notif-list">
            <div class="notif-loading"><i class="fas fa-circle-notch fa-spin"></i></div>
        </div>
        <div class="notif-panel-footer">
            <a href="messages.php"><i class="fas fa-comment-dots"></i> Open Messages</a>
        </div>
    </div>
</div>
