<?php
require_once 'config/db.php'; session_start(); require_login();
$page_title = 'My Complaints'; $filter = $_GET['status'] ?? '';
$allowed = ['Reported','Assigned','In Progress','Resolved'];
$sql = 'SELECT complaint_id,title,category,location,status,priority,created_at FROM issues WHERE user_id = ?'; $types = 'i'; $params = [$_SESSION['user_id']];
if (in_array($filter, $allowed, true)) { $sql .= ' AND status = ?'; $types .= 's'; $params[] = $filter; }
$sql .= ' ORDER BY created_at DESC'; $stmt = $conn->prepare($sql); $stmt->bind_param($types, ...$params); $stmt->execute(); $result = $stmt->get_result();
include 'includes/header.php';
?>
<div class="container"><div class="panel"><div class="filters"><form class="filters" method="get"><div class="form-group"><label>Show</label><select name="status"><option value="">All complaints</option><?php foreach ($allowed as $item): ?><option <?= $filter === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div><button class="button" type="submit">Filter</button><a class="button secondary" href="report_issue.php">New complaint</a></form></div>
<div class="table-wrap"><table><thead><tr><th>Complaint ID</th><th>Title</th><th>Category</th><th>Location</th><th>Date</th><th>Status</th><th></th></tr></thead><tbody>
<?php if ($result->num_rows): while ($issue = $result->fetch_assoc()): ?><tr><td><?= e($issue['complaint_id']) ?></td><td><?= e($issue['title']) ?></td><td><?= e($issue['category']) ?></td><td><?= e($issue['location']) ?></td><td><?= date('d M Y', strtotime($issue['created_at'])) ?></td><td><span class="badge <?= $issue['status'] === 'Resolved' ? 'green' : 'blue' ?>"><?= e($issue['status']) ?></span></td><td><a href="track_complaint.php?complaint_id=<?= urlencode($issue['complaint_id']) ?>">View</a></td></tr><?php endwhile; else: ?><tr><td colspan="7" class="empty">You have not submitted any complaints.</td></tr><?php endif; ?>
</tbody></table></div></div></div>
<?php include 'includes/footer.php'; ?>
