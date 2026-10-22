<?php
require_once 'config/db.php';
session_start(); require_login();
$page_title = 'Report an Issue';
$categories = ['Electrical','Water and Plumbing','Furniture','Infrastructure','Others'];
$message = ''; $error = ''; $similar_count = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? ''); $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? ''; $location = trim($_POST['location'] ?? '');
    if ($title === '' || $description === '' || !in_array($category, $categories, true) || $location === '') {
        $error = 'Please complete all fields with a valid category.';
    } else {
        $similar_count = issue_report_count($conn, $location, $category);
        $photo_name = null;
        if (!empty($_FILES['photo']['name'])) {
            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK || $_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $error = 'Photo must be an image smaller than 5 MB.';
            } else {
                $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['photo']['tmp_name']);
                if (!isset($allowed[$mime])) $error = 'Only JPG, PNG, or GIF images are allowed.';
                else { $photo_name = uniqid('issue_', true) . '.' . $allowed[$mime]; move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $photo_name); }
            }
        }
        if ($error === '') {
            $complaint_id = 'FMC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $priority = $similar_count >= 2 ? 'High' : 'Normal';
            $stmt = $conn->prepare('INSERT INTO issues (complaint_id,user_id,title,description,category,location,photo,priority) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->bind_param('sissssss', $complaint_id, $_SESSION['user_id'], $title, $description, $category, $location, $photo_name, $priority);
            if ($stmt->execute()) {
                if ($similar_count >= 2) {
                    $priority_stmt = $conn->prepare("UPDATE issues SET priority = 'High' WHERE location = ? AND category = ?");
                    $priority_stmt->bind_param('ss', $location, $category);
                    $priority_stmt->execute();
                    $priority_stmt->close();
                }
                $message = 'Issue submitted successfully. Your complaint ID is ' . $complaint_id . '.';
                if ($similar_count > 0) $message .= ' Similar issues have already been reported at this location (' . $similar_count . ' existing report' . ($similar_count === 1 ? '' : 's') . ').';
            } else { $error = 'Could not save the issue. Please try again.'; }
            $stmt->close();
        }
    }
}
include 'includes/header.php';
?>
<div class="container"><div class="panel">
    <h2>Tell us what needs attention</h2>
    <?php if ($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="grid-2"><div class="form-group"><label for="title">Issue title</label><input id="title" name="title" required value="<?= e($_POST['title'] ?? '') ?>"></div>
        <div class="form-group"><label for="category">Category</label><select id="category" name="category" required><option value="">Select category</option><?php foreach ($categories as $item): ?><option <?= ($_POST['category'] ?? '') === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div></div>
        <div class="form-group"><label for="location">Location</label><input id="location" name="location" placeholder="Example: Library, Ground Floor" required value="<?= e($_POST['location'] ?? '') ?>"></div>
        <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" required><?= e($_POST['description'] ?? '') ?></textarea></div>
        <div class="form-group"><label for="photo">Photo (optional, JPG/PNG/GIF up to 5 MB)</label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/gif"></div>
        <button class="button" type="submit">Submit complaint</button>
    </form>
</div></div>
<?php include 'includes/footer.php'; ?>
