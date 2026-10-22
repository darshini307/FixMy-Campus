<?php
require_once 'config/db.php';
session_start();
$page_title = 'Create Student Account';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $errors[] = 'Enter a name, valid email, and password of at least 6 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            header('Location: login.php?registered=1');
            exit;
        }
        $errors[] = $stmt->errno === 1062 ? 'That email is already registered.' : 'Could not create account.';
        $stmt->close();
    }
}
include 'includes/header.php';
?>
<div class="container"><div class="panel auth-box">
    <h2>Join FixMy Campus</h2><p class="muted">Create a student account to report and track issues.</p>
    <?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
    <form method="post">
        <div class="form-group"><label for="name">Full name</label><input id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>"></div>
        <div class="form-group"><label for="email">Email</label><input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>"></div>
        <div class="form-group"><label for="password">Password</label><input id="password" name="password" type="password" minlength="6" required></div>
        <button class="button" type="submit">Create account</button>
    </form>
    <p class="muted">Already registered? <a href="login.php">Log in</a></p>
</div></div>
<?php include 'includes/footer.php'; ?>
