<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

// Handle POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $student_id   = $_POST['student_id'] ?? null;
        $amount       = trim($_POST['amount'] ?? '');
        $status       = trim($_POST['status'] ?? '');
        $payment_type = $_POST['payment_type'] ?? 'Monthly';
        $period       = trim($_POST['period'] ?? '');
        
        if ($student_id && $amount && $status) {
            try {
                $database->getReference('payments')->push([
                    'student_id'   => $student_id,
                    'amount'       => $amount,
                    'status'       => $status,
                    'payment_type' => $payment_type,
                    'period'       => $period,
                    'date'         => date('Y-m-d H:i:s')
                ]);
                $message = "<div class='alert success'>Payment logged successfully!</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Failed to log payment: " . $e->getMessage() . "</div>";
            }
        } else {
            $message = "<div class='alert error'>All required fields must be filled.</div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $delete_id = $_POST['payment_id'] ?? null;
        if ($delete_id) {
            try {
                $database->getReference('payments/' . $delete_id)->remove();
                $message = "<div class='alert success'>Payment record deleted.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Cannot delete payment.</div>";
            }
        }
    }
}

// Fetch DB
$paymentsDB = $database->getReference('payments')->getValue() ?: [];
$studentsDB = $database->getReference('students')->getValue() ?: [];

// Fetch Students for dropdown
$students = [];
foreach ($studentsDB as $s_id => $s) {
    if (!$s) continue;
    $s['id'] = $s_id;
    $students[] = $s;
}
usort($students, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

// Fetch Payments with Student Info
$payments = [];
foreach ($paymentsDB as $p_id => $p) {
    if (!$p) continue;
    $p['id'] = $p_id;
    
    $sid = $p['student_id'] ?? null;
    if ($sid && isset($studentsDB[$sid])) {
        $p['first_name'] = $studentsDB[$sid]['first_name'] ?? '';
        $p['last_name']  = $studentsDB[$sid]['last_name'] ?? '';
    } else {
        $p['first_name'] = 'Unknown';
        $p['last_name']  = 'Student';
    }
    
    $payments[] = $p;
}
usort($payments, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$payments = array_slice($payments, 0, 50);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance & Payments - Al Amad School</title>
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
$dash_title = 'Finance & Payments';
$dash_sub   = date('l, F j, Y');
$user_init  = 'AD';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?= $message ?>
        
        <div class="form-card reveal">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Log Payment</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Student</label>
                        <select name="student_id" required>
                            <option value="">-- Search Student --</option>
                            <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (#<?= strtoupper(substr($s['id'], -4)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (TND)</label>
                        <input type="number" step="0.5" name="amount" placeholder="E.g. 500.00" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="Paid">Paid Fully</option>
                            <option value="Pending">Pending / Unpaid</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Payment Type</label>
                        <select name="payment_type" required>
                            <option value="Monthly">Monthly Tuition</option>
                            <option value="Annual">Annual / Inscription</option>
                            <option value="Other">Other Fees</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Period (Month/Year)</label>
                        <input type="text" name="period" placeholder="E.g. Oct 2024 or 2024-2025" required>
                    </div>
                </div>
                <!-- Flex container to align buttons -->
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Save Record</button>
                    <button type="button" class="btn btn-secondary" onclick="alert('PDF generation implemented in final verification phase.')">Generate Receipt</button>
                </div>
            </form>
        </div>

        <div class="table-card reveal" style="animation-delay: 0.3s;">
            <div class="table-card-header">Payment History Logs</div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Receipt ID</th>
                            <th>Student Name</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($payments) > 0): ?>
                            <?php foreach($payments as $p): ?>
                            <tr>
                                <td><strong>REC-<?= strtoupper(substr($p['id'], -5)) ?></strong></td>
                                <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                                <td><span style="font-size:.75rem; font-weight:600; opacity:.8;"><?= htmlspecialchars($p['payment_type'] ?? 'Monthly') ?></span></td>
                                <td><span style="font-size:.75rem;"><?= htmlspecialchars($p['period'] ?? '—') ?></span></td>
                                <td><strong style="color:var(--text);"><?= number_format($p['amount'], 2) ?> TND</strong></td>
                                <td><span class="badge <?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this payment record?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: rgba(239,68,68,0.5); color: #ef4444;"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 3rem;">No payment records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
