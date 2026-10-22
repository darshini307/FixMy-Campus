<?php
require_once 'config/db.php';
session_start();
$page_title = 'Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT user_id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email); $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $name, $hash, $role); $stmt->fetch();
        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user_id; $_SESSION['name'] = $name; $_SESSION['role'] = $role;
            header('Location: ' . ($role === 'admin' ? 'admin_dashboard.php' : 'index.php')); exit;
        }
    }
    $error = 'Email or password is incorrect.'; $stmt->close();
}
include 'includes/header.php';
?>
<div class="container"><div class="panel auth-box">
    <h2>Welcome back</h2>
    <?php if (isset($_GET['registered'])): ?><div class="alert success">Account created. You can now log in.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" required></div>
        <div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" required></div>
        <button class="button" type="submit">Log in</button>
    </form>
    <p class="muted">New student? <a href="register.php">Create an account</a></p>
</div></div>
<?php include 'includes/footer.php'; ?>
