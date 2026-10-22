<?php
require_once 'config/db.php'; session_start();
$page_title = 'Issue List';
$search = trim($_GET['search'] ?? ''); $category = $_GET['category'] ?? ''; $status = $_GET['status'] ?? '';
$categories = ['Electrical','Water and Plumbing','Furniture','Infrastructure','Others'];
$statuses = ['Reported','Assigned','In Progress','Resolved'];
$sql = "SELECT complaint_id,title,category,location,status,priority,created_at FROM issues WHERE (title LIKE CONCAT('%',?,'%') OR location LIKE CONCAT('%',?,'%') OR complaint_id LIKE CONCAT('%',?,'%'))";
$types = 'sss'; $params = [$search,$search,$search];
if (in_array($category, $categories, true)) { $sql .= ' AND category = ?'; $types .= 's'; $params[] = $category; }
if (in_array($status, $statuses, true)) { $sql .= ' AND status = ?'; $types .= 's'; $params[] = $status; }
$sql .= ' ORDER BY created_at DESC';
$stmt = $conn->prepare($sql); $stmt->bind_param($types, ...$params); $stmt->execute(); $result = $stmt->get_result();
include 'includes/header.php';
?>
<div class="container"><div class="panel">
    <div class="filters"><form class="filters" method="get"><div class="form-group"><label>Search</label><input name="search" placeholder="ID, title, or location" value="<?= e($search) ?>"></div><div class="form-group"><label>Category</label><select name="category"><option value="">All categories</option><?php foreach ($categories as $item): ?><option <?= $category === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Status</label><select name="status"><option value="">All statuses</option><?php foreach ($statuses as $item): ?><option <?= $status === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div><button class="button" type="submit">Filter</button></form></div>
    <div class="table-wrap"><table><thead><tr><th>Complaint ID</th><th>Title</th><th>Category</th><th>Location</th><th>Date</th><th>Status</th></tr></thead><tbody>
    <?php if ($result->num_rows): while ($issue = $result->fetch_assoc()): ?><tr><td><strong><?= e($issue['complaint_id']) ?></strong></td><td><?= e($issue['title']) ?></td><td><?= e($issue['category']) ?></td><td><?= e($issue['location']) ?></td><td><?= date('d M Y', strtotime($issue['created_at'])) ?></td><td><span class="badge <?= $issue['status'] === 'Resolved' ? 'green' : ($issue['priority'] === 'High' ? 'red' : 'blue') ?>"><?= e($issue['status']) ?></span><?php if ($issue['priority'] === 'High'): ?><br><small class="muted">High priority</small><?php endif; ?></td></tr><?php endwhile; else: ?><tr><td colspan="6" class="empty">No issues match your filters.</td></tr><?php endif; ?>
    </tbody></table></div>
</div></div>
<?php include 'includes/footer.php'; ?>
