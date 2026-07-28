<?php $title = 'Login'; include __DIR__ . '/../layouts/header.php'; ?>

<h1>Login</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/login" class="col-md-4">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Log In</button>
</form>

<p class="mt-3">
    <a href="/books">Continue browsing as guest</a> |
    <a href="/register">Need an account? Register</a>
</p>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
