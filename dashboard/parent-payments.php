<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('parent');
$current_role = 'parent';

// Fetch linked children
$user_id = $_SESSION['user_id'] ?? null;
$studentsAll = $database->getReference('students')->getValue() ?: [];
$studentsDB = [];
foreach ($studentsAll as $k => $v) {
    if (($v['parent_id'] ?? '') === $user_id) $studentsDB[$k] = $v;
}

$child_ids = [];
foreach ($studentsDB as $s_id => $s) {
    if (!$s) continue;
    $s['id'] = $s_id;
    $child_ids[$s_id] = $s;
}

$payments = [];
$total_pending = 0;

if (count($child_ids) > 0) {
    $paymentsDB = $database->getReference('payments')->getValue() ?: [];
    foreach ($paymentsDB as $p_id => $p) {
        if (!$p) continue;
        $sid = $p['student_id'] ?? null;
        if (isset($child_ids[$sid])) {
            $p['id'] = $p_id;
            $p['first_name'] = $child_ids[$sid]['first_name'] ?? '';
            $p['last_name']  = $child_ids[$sid]['last_name'] ?? '';
            $payments[] = $p;
            
            if (($p['status'] ?? '') === 'Pending') {
                $total_pending += (float)($p['amount'] ?? 0);
            }
        }
    }
    // Sort payments by date descending
    usort($payments, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuition Payments - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
    <?php
$dash_title = 'Tuition Payments';
$dash_sub   = date('l, F j, Y');
$user_init  = 'PA';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?php if (count($child_ids) == 0): ?>
            <div style="padding: 2rem; color: var(--text-muted);">No children linked to your account yet.</div>
        <?php else: ?>
            
            <?php if ($total_pending > 0): ?>
            <div class="alert-warning reveal">
                <div>
                    <div>⚠️ Outstanding Balance</div>
                    <div style="font-size: 0.85rem; font-weight: 400; opacity: 0.9; margin-top: 4px;">You have unpaid tuition fees.</div>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <span style="font-size: 1.5rem;"><?= number_format($total_pending, 2) ?> TND</span>
                    <button class="pay-btn" onclick="alert('Online payment gateway integration coming soon.')">Pay Now</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-card reveal" style="animation-delay: 0.3s;">
            <div class="table-card-header">Recent Payment Transactions</div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Receipt ID</th>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($payments) > 0): ?>
                            <?php foreach($payments as $p): ?>
                            <tr>
                                <td><strong>REC-<?= strtoupper(substr($p['id'], -5)) ?></strong></td>
                                <td><?= htmlspecialchars($p['first_name']) ?></td>
                                <td><span style="font-size:.75rem; font-weight:600; opacity:.8;"><?= htmlspecialchars($p['payment_type'] ?? 'Monthly') ?></span></td>
                                <td><span style="font-size:.72rem;"><?= htmlspecialchars($p['period'] ?? '—') ?></span></td>
                                <td><strong><?= number_format($p['amount'], 2) ?> TND</strong></td>
                                <td><span class="badge <?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                                <td>
                                    <?php if ($p['status'] === 'Paid'): ?>
                                    <button class="download-btn" onclick="alert('PDF Receipt Generation will be securely handled by TCPDF in production.')">📥 Receipt</button>
                                    <?php else: ?>
                                    <span style="opacity: 0.3; font-size: 0.85rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 3rem;">No payment history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
