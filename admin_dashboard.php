<?php
require_once 'config/db.php'; session_start(); require_admin();
$page_title = 'Admin Dashboard'; $notice = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issue_id = (int)($_POST['issue_id'] ?? 0); $new_status = $_POST['status'] ?? ''; $staff_id = (int)($_POST['assigned_staff'] ?? 0);
    $allowed = ['Reported','Assigned','In Progress','Resolved'];
    if ($issue_id > 0 && in_array($new_status, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE issues SET status = ?, assigned_staff = NULLIF(?, 0) WHERE issue_id = ?");
        $stmt->bind_param('sii', $new_status, $staff_id, $issue_id);
        if ($stmt->execute()) $notice = 'Complaint updated successfully.'; else $error = 'Could not update complaint.'; $stmt->close();
    } else $error = 'Choose a valid status.';
}
$counts = $conn->query("SELECT COUNT(*) total, SUM(status IN ('Reported','Assigned')) open_count, SUM(status='In Progress') progress_count, SUM(status='Resolved') resolved_count FROM issues")->fetch_assoc();
$staff = []; $staff_result = $conn->query('SELECT staff_id,staff_name,department FROM staff ORDER BY staff_name'); while ($row = $staff_result->fetch_assoc()) $staff[] = $row;
$issues = $conn->query("SELECT i.*, u.name student_name, s.staff_name, (SELECT COUNT(*) FROM issues x WHERE x.location=i.location AND x.category=i.category) report_count FROM issues i JOIN users u ON i.user_id=u.user_id LEFT JOIN staff s ON i.assigned_staff=s.staff_id ORDER BY i.created_at DESC");
include 'includes/header.php';
?>
<div class="container">
<?php if ($notice): ?><div class="alert success"><?= e($notice) ?></div><?php endif; ?><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="cards"><div class="card"><span class="muted">Total complaints</span><span class="stat-number"><?= (int)$counts['total'] ?></span></div><div class="card"><span class="muted">Open</span><span class="stat-number"><?= (int)$counts['open_count'] ?></span></div><div class="card"><span class="muted">In progress</span><span class="stat-number"><?= (int)$counts['progress_count'] ?></span></div><div class="card"><span class="muted">Resolved</span><span class="stat-number"><?= (int)$counts['resolved_count'] ?></span></div></div>
<div class="panel"><h2>Manage complaints</h2><p class="muted">Recurring locations with three or more reports are marked high priority.</p><div class="table-wrap"><table><thead><tr><th>Complaint</th><th>Student / issue</th><th>Location</th><th>Reports</th><th>Manage</th></tr></thead><tbody>
<?php while ($issue = $issues->fetch_assoc()): ?><tr class="<?= $issue['report_count'] >= 3 ? 'recurring' : '' ?>"><td><strong><?= e($issue['complaint_id']) ?></strong><br><span class="badge <?= $issue['priority'] === 'High' ? 'red' : 'blue' ?>"><?= e($issue['priority']) ?></span></td><td><?= e($issue['student_name']) ?><br><?= e($issue['title']) ?></td><td><?= e($issue['location']) ?><br><small><?= e($issue['category']) ?></small></td><td><strong><?= (int)$issue['report_count'] ?></strong><?= $issue['report_count'] >= 3 ? ' <span class="badge red">Recurring</span>' : '' ?></td><td><form method="post" class="inline-form"><input type="hidden" name="issue_id" value="<?= (int)$issue['issue_id'] ?>"><select name="status"><?php foreach (['Reported','Assigned','In Progress','Resolved'] as $item): ?><option <?= $issue['status'] === $item ? 'selected' : '' ?>><?= $item ?></option><?php endforeach; ?></select><select name="assigned_staff"><option value="0">No staff</option><?php foreach ($staff as $person): ?><option value="<?= (int)$person['staff_id'] ?>" <?= (int)$issue['assigned_staff'] === (int)$person['staff_id'] ? 'selected' : '' ?>><?= e($person['staff_name']) ?></option><?php endforeach; ?></select><button class="button small" type="submit">Save</button></form></td></tr><?php endwhile; ?>
</tbody></table></div></div></div>
<?php include 'includes/footer.php'; ?>
