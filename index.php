<?php
require_once 'config/db.php';
session_start();
$page_title = 'Campus Overview';
$counts = [];
$result = $conn->query("SELECT
    COUNT(*) total,
    SUM(status = 'Reported') open_count,
    SUM(status = 'In Progress') progress_count,
    SUM(status = 'Resolved') resolved_count
    FROM issues");
$counts = $result->fetch_assoc() ?: ['total'=>0,'open_count'=>0,'progress_count'=>0,'resolved_count'=>0];
$recent = $conn->query("SELECT issue_id, complaint_id, title, category, location, photo, status, priority, created_at FROM issues ORDER BY created_at DESC LIMIT 6");
include 'includes/header.php';
?>
<div class="container">
    <section class="hero">
        <h2>Make our campus better, one report at a time.</h2>
        <p>Report broken lights, water leaks, damaged furniture, and other campus problems. Follow every update until the issue is resolved.</p>
        <a class="button" href="report_issue.php">Report a new issue</a>
    </section>
    <div class="cards">
        <div class="card"><span class="muted">Total complaints</span><span class="stat-number"><?= (int)$counts['total'] ?></span></div>
        <div class="card"><span class="muted">Open complaints</span><span class="stat-number"><?= (int)$counts['open_count'] ?></span></div>
        <div class="card"><span class="muted">In progress</span><span class="stat-number"><?= (int)$counts['progress_count'] ?></span></div>
        <div class="card"><span class="muted">Resolved</span><span class="stat-number"><?= (int)$counts['resolved_count'] ?></span></div>
    </div>
    <div class="panel">
        <h2>Recent campus issues</h2>
        <div class="issue-grid">
        <?php if ($recent && $recent->num_rows): while ($issue = $recent->fetch_assoc()): ?>
            <article class="card issue-card">
                <?php if (!empty($issue['photo'])): ?><img class="issue-photo" src="uploads/<?= e($issue['photo']) ?>" alt="Issue photo"><?php else: ?><div class="issue-photo"></div><?php endif; ?>
                <span class="badge <?= $issue['status'] === 'Resolved' ? 'green' : ($issue['status'] === 'In Progress' ? 'orange' : 'blue') ?>"><?= e($issue['status']) ?></span>
                <h3><?= e($issue['title']) ?></h3>
                <p class="muted"><?= e($issue['category']) ?> · <?= e($issue['location']) ?></p>
                <small class="muted"><?= e($issue['complaint_id']) ?> · <?= date('d M Y', strtotime($issue['created_at'])) ?></small>
            </article>
        <?php endwhile; else: ?><div class="empty">No issues have been reported yet.</div><?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
