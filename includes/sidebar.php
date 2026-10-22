<aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark">F</span><span>FixMy <strong>Campus</strong></span></a>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="issues.php">Issue List</a>
        <a href="report_issue.php">Report Issue</a>
        <a href="track_complaint.php">Track Complaint</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="my_complaints.php">My Complaints</a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?><a href="admin_dashboard.php">Admin Dashboard</a><?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-note"><strong>See it. Report it.</strong><br>Help keep our campus safe and comfortable.</div>
</aside>
