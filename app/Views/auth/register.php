<?php $title = 'Register'; include __DIR__ . '/../layouts/header.php'; ?>

<h1>Register</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/register" class="col-md-4">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" minlength="8" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<p class="mt-3"><a href="/login">Already have an account? Log in</a></p>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
