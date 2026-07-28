<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Library System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<nav class="d-flex justify-content-end mb-3">
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/logout" class="btn btn-outline-secondary btn-sm">Logout</a>
    <?php else: ?>
        <a href="/login" class="btn btn-outline-primary btn-sm me-2">Login</a>
        <a href="/register" class="btn btn-outline-secondary btn-sm">Register</a>
    <?php endif; ?>
</nav>
<body>
<div class="container mt-4">