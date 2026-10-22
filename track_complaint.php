<?php
require_once 'config/db.php'; session_start();
$page_title = 'Track Complaint'; $issue = null; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['complaint_id'])) {
    $complaint_id = strtoupper(trim($_POST['complaint_id'] ?? $_GET['complaint_id'] ?? ''));
    $stmt = $conn->prepare('SELECT i.*, s.staff_name FROM issues i LEFT JOIN staff s ON i.assigned_staff=s.staff_id WHERE i.complaint_id = ?');
    $stmt->bind_param('s', $complaint_id); $stmt->execute(); $issue = $stmt->get_result()->fetch_assoc();
    if (!$issue) $error = 'No complaint was found with that ID.'; $stmt->close();
}
$steps = ['Reported','Assigned','In Progress','Resolved'];
include 'includes/header.php';
?>
<div class="container"><div class="panel"><h2>Find your complaint</h2><form class="inline-form" method="post"><input name="complaint_id" placeholder="Example: FMC-20260101-A1B2C3" required><button class="button" type="submit">Track</button></form></div>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($issue): $current = array_search($issue['status'], $steps, true); ?>
<div class="panel" style="margin-top:20px"><div class="grid-2"><div><p class="eyebrow"><?= e($issue['complaint_id']) ?></p><h2><?= e($issue['title']) ?></h2><p><?= nl2br(e($issue['description'])) ?></p></div><div><p><strong>Category:</strong> <?= e($issue['category']) ?></p><p><strong>Location:</strong> <?= e($issue['location']) ?></p><p><strong>Reported:</strong> <?= date('d M Y, h:i A', strtotime($issue['created_at'])) ?></p><p><strong>Assigned staff:</strong> <?= e($issue['staff_name'] ?? 'Not assigned yet') ?></p></div></div>
    <h3>Progress</h3><div class="timeline"><?php foreach ($steps as $index => $step): ?><div class="step <?= $index <= $current ? 'done' : '' ?>"><div class="step-dot"><?= $index <= $current ? '✓' : ($index + 1) ?></div><span><?= e($step) ?></span></div><?php endforeach; ?></div>
</div><?php endif; ?></div>
<?php include 'includes/footer.php'; ?>
