<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Replace with your actual NodeMCU IP address
$esp_ip = "http://192.168.1.123";

$showToast = false;
if (isset($_SESSION['login_success'])) {
    $showToast = true;
    unset($_SESSION['login_success']);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Dashboard</a>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container mt-5 text-center">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>You have successfully logged in.</p>

    <div class="mt-4">
        <h3>LED Control</h3>
        <a href="<?= $esp_ip ?>/led?state=on" class="btn btn-success btn-lg me-3">Turn ON</a>
        <a href="<?= $esp_ip ?>/led?state=off" class="btn btn-danger btn-lg">Turn OFF</a>
    </div>
</div>

<?php if ($showToast): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Login successful! Welcome back <?= htmlspecialchars($_SESSION['username']) ?>.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
